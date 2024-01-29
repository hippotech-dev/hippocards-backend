<?php

namespace App\Http\Services;

use App\Enums\EAssetType;
use App\Models\Utility\Asset;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class AssetService
{
    public function getAssetById(int $id)
    {
        return Asset::find($id);
    }

    public function getAssetPath(int $id)
    {
        $asset = $this->getAssetById($id);
        if (is_null($asset)) {
            return null;
        }
        return $asset->path;
    }

    public function createAsset(string $folder, UploadedFile $file)
    {
        $fileExtension = $file->extension();
        $filename = bin2hex(random_bytes(16)) . "." . $fileExtension;

        $path = $folder . "/" . $filename;

        Storage::putFileAs($folder, $file, $filename);

        return Asset::create([
            "path" => $path,
            "size" => $file->getSize(),
            "mime_type" => $file->getMimeType(),
        ]);
    }

    public function deleteAssetById(int $id)
    {
        // $asset = $this->getAssetById($id);
        return Asset::where("id", $id)->delete();
    }
}
