<?php

namespace App\Http\Controllers\Utilities;

use App\Http\Controllers\Controller;
use App\Http\Resources\Utility\AssetResource;
use App\Http\Services\AssetService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UploadController extends Controller
{
    public function __construct(private AssetService $service) {}

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
                "object_id"
            ),
            [
                "filename" => "required|string|max:32",
                "object_type" => "required|string|max:128",
                "object_id" => "required|integer",
            ]
        )
            ->validate();


        $asset = $this->service->createNonuploadedAssetByObject($validatedData["object_type"], $validatedData["object_id"], $validatedData["filename"]);
        $url = $this->service->createVideoUploadUrl($validatedData["filename"], [

            "asset_id" => $asset->id
        ]);

        return response()->success($url);
    }
}
