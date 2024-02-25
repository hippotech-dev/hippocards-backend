<?php

namespace App\Http\Controllers\Utilities;

use App\Exceptions\AppException;
use App\Http\Controllers\Controller;
use App\Http\Resources\Utility\AssetResource;
use App\Http\Services\AssetService;
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
            ),
            [
                    "file" => "required|file|max:131072",
                ]
        )
                ->validate();

        $asset = $this->service->createAsset($validatedData["file"]);

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

        $this->service->completeTranscoderJob($validatedData["job_id"]);

        return response()->success();
    }
}
