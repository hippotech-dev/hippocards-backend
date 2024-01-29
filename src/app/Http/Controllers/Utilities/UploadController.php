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
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
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
                "folder" => "required|string|max:64"
            ]
        )
            ->validate();

        $asset = $this->service->createAsset($validatedData["folder"], $validatedData["file"]);

        return new AssetResource($asset);
    }
}
