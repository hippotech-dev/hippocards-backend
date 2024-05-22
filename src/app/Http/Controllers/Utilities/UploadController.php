<?php

namespace App\Http\Controllers\Utilities;

use App\Exceptions\AppException;
use App\Http\Controllers\Controller;
use App\Http\Resources\Utility\AssetResource;
use App\Http\Services\AssetService;
use App\Http\Services\VDOCipherService;
use App\Models\Utility\Asset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UploadController extends Controller
{
    public function __construct(private AssetService $service)
    {
    }

    /**
     * Upload file
     */
    public function uploadFile(Request $request)
    {
        $validatedData = Validator::make(
            $request->only(
                "file",
                "folder"
            ),
            [
                "file" => "required|file|max:131072",
                "folder" => "sometimes|string|max:64"
            ]
        )
                ->validate();

        $asset = $this->service->createAsset($validatedData["file"], $validatedData["folder"] ?? null);

        return new AssetResource($asset);
    }

    /**
     * Get signed url
     */
    public function getVideoSignedUrl(Request $request)
    {
        $validatedData = Validator::make(
            $request->only(
                "filename",
                "object_type",
            ),
            [
                "filename" => "required|string|max:32",
                "object_type" => "required|string|max:128",
            ]
        )
            ->validate();


        $asset = $this->service->createNonuploadedAssetByObject($validatedData["object_type"], $validatedData["filename"]);
        $url = $this->service->createVideoUploadUrl($asset, [
            "asset_id" => $asset->id
        ]);

        return response()->success([
            "url" => $url,
            "asset" => new AssetResource($asset)
        ]);
    }

    /**
     * Set transcoder job
     */
    public function setTranscoderJob(Request $request)
    {
        $validatedData = Validator::make(
            $request->only([
                "job_id",
                "asset_id"
            ]),
            [
                "job_id" => "required|string",
                "asset_id" => "required|integer"
            ]
        )
            ->validate();

        $asset = $this->service->getAssetById($validatedData["asset_id"]);
        if (is_null($asset)) {
            throw new AppException("Invalid asset id!");
        }

        $this->service->setTranscoderJob($asset, $validatedData["job_id"]);

        $this->service->uploadToDRMProvider($asset);

        return response()->success();
    }

    /**
     * Complete transcoder job
     */
    public function completeTranscoderJob(Request $request)
    {
        $validatedData = Validator::make(
            $request->only([
                "job_id",
            ]),
            [
                "job_id" => "required|string",
            ]
        )
            ->validate();

        $asset = $this->service->getAsset([ "transcoder_job_id" => $validatedData["job_id"] ]);

        if (is_null($asset)) {
            throw new AppException("Asset is invalid!");
        }

        $this->service->completeTranscoderJob($asset);

        return response()->success();
    }

    /**
     * Get video playback and otp info
     */
    public function getVideoPlaybackAndOTPInfo(Asset $asset)
    {
        $result = $this->service->getVideoPlaybackAndOTPInfo($asset);

        return response()->successAppend([
            "data" => $result
        ]);
    }
}
