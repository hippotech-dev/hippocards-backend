<?php

namespace App\Http\Controllers\Utility;

use App\Enums\EStatus;
use App\Exceptions\AppException;
use App\Http\Controllers\Controller;
use App\Http\Resources\Utility\AssetResource;
use App\Http\Services\AssetService;
use App\Http\Services\VDOCipherService;
use App\Models\Utility\Asset;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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
        $validatedData = Validator::make($request->only("file", "folder"), [
            "file" => "required|file|max:131072",
            "folder" => "sometimes|string|max:64",
        ])->validate();

        $asset = $this->service->createAsset(
            $validatedData["file"],
            $validatedData["folder"] ?? null
        );

        return new AssetResource($asset);
    }

    /**
     * Upload file
     */
    public function uploadFileWithURL(Request $request)
    {
        $validatedData = Validator::make(
            $request->only(
                "url",
                "filename"
            ),
            [
                "url" => "required|string|max:512",
                "filename" => "required|string|max:512"
            ]
        )->validate();

        $asset = $this->service->createAssetFromURL(
            $validatedData["url"],
            $validatedData["filename"]
        );

        return new AssetResource($asset);
    }

    /**
     * Upload unsplash urls
     */
    public function uploadUnsplashUrls(Request $request)
    {
        $validatedData = Validator::make(
            $request->only(
                "urls",
                "filename"
            ),
            [
                "urls" => "required",
                "urls.small" => "required|string",
                "urls.regular" => "required|string",
                "urls.full" => "required|string",
                "filename" => "required|string|max:512"
            ]
        )->validate();

        $asset = $this->service->createUnsplashAssetFromUrls(
            $validatedData["urls"],
            $validatedData["filename"]
        );

        return new AssetResource($asset);
    }

    /**
     * Get signed url
     */
    public function getVideoSignedUrl(Request $request)
    {
        $validatedData = Validator::make(
            $request->only("filename", "object_type"),
            [
                "filename" => "required|string|max:32",
                "object_type" => "required|string|max:128",
                "is_drm_protected" => "sometimes|boolean"
            ]
        )->validate();

        $asset = $this->service->createNonuploadedAssetByObject(
            $validatedData["object_type"],
            $validatedData["filename"],
            $validatedData["is_drm_protected"] ?? true
        );
        $url = $this->service->createVideoUploadUrl($asset, [
            "asset_id" => $asset->id,
        ]);

        return response()->success([
            "url" => $url,
            "asset" => new AssetResource($asset),
        ]);
    }

    /**
     * Set transcoder job
     */
    public function setTranscoderJob(Request $request)
    {
        $validatedData = Validator::make(
            $request->only(["job_id", "asset_id"]),
            [
                "job_id" => "required|string",
                "asset_id" => "required|integer",
            ]
        )->validate();

        $asset = $this->service->getAssetById($validatedData["asset_id"]);
        if (is_null($asset)) {
            throw new AppException("Invalid asset id!");
        }

        $this->service->setTranscoderJob($asset, $validatedData["job_id"]);

        if ($asset->metadata["is_drm_protected"] ?? true) {
            $this->service->uploadToDRMProvider($asset);
        }

        return response()->success();
    }

    /**
     * Complete transcoder job
     */
    public function completeTranscoderJob(Request $request)
    {
        $validatedData = Validator::make($request->only(["job_id"]), [
            "job_id" => "required|string",
        ])->validate();

        $asset = $this->service->getAsset([
            "transcoder_job_id" => $validatedData["job_id"],
        ]);

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
            "data" => $result,
        ]);
    }

    /**
     * Webhook when video is ready
     */
    public function webhookVDOVideoSuccess(Request $request)
    {
        $validatedData = Validator::make($request->only("event", "payload"), [
            "event" => "required|string",
            "payload" => "required|array",
            "payload.id" => "required|string",
        ])->validate();
        $payload = $validatedData["payload"];
        $event = $validatedData["event"];
        $videoId = $payload["id"];

        Log::channel("custom")->info(print_r($validatedData, true));

        $asset = $this->service->getAsset([
            "vdo_drm_video_id" => $videoId,
        ]);

        if (is_null($asset)) {
            throw new AppException("VDO Video not found!");
        }

        $this->service->setVDOVideoStatus($asset, $event);

        return response()->success();
    }
}
