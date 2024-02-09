<?php

namespace App\Http\Services;

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

    public function createAsset(UploadedFile $file)
    {
        $folder = "assets/" . date("Y-m");
        $filename = $this->generateRandomFilename($file->extension());
        $path = $folder . "/" . $filename;

        Storage::putFileAs($folder, $file, $filename);

        return Asset::create([
            "path" => $path,
            "size" => $file->getSize(),
            "mime_type" => $file->getMimeType(),
        ]);
    }

    public function createNonuploadedAssetByObject(string $objectType, int $objectId, string $filename)
    {
        $path = $objectType . "/" . $this->generateRandomFilename($filename);

        return Asset::create([
            "path" => $path,
            "size" => 0,
            "mime_type" => "unknown",
        ]);
    }

    public function deleteAssetById(int $id)
    {
        // $asset = $this->getAssetById($id);
        return Asset::where("id", $id)->delete();
    }

    public function createVideoUploadUrl($path, array $metaData = [])
    {
        [ "url" => $url ] = Storage::disk("s3-tokyo")->temporaryUploadUrl(
            "video/" . $path,
            now()->addMinutes(2),
            [
                'Metadata' => $metaData,
                "ContentType" => "application/octet-stream"
            ]
        );

        return $url;
    }

    public function generateRandomFilename(string $ext)
    {
        return bin2hex(random_bytes(16)) . "." . $ext;
    }
}
