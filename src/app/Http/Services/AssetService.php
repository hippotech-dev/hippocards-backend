<?php

namespace App\Http\Services;

use App\Exceptions\AppException;
use App\Models\Utility\Asset;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Interfaces\EncodedImageInterface;
use Intervention\Image\Interfaces\ImageInterface;

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
        $folder = "v3/assets/" . date("Y-m");
        $filename = $this->generateRandomFilename($file->extension());
        $path = $folder . "/" . $filename;

        Storage::disk("s3-tokyo")->putFileAs($folder, $file, $filename);

        return Asset::create([
            "path" => $path,
            "size" => $file->getSize(),
            "mime_type" => $file->getMimeType(),
        ]);
    }

    public function createImageAsset(EncodedImageInterface $file)
    {
        $folder = "v3/assets/" . date("Y-m");
        $filename = $this->generateRandomFilename("image.jpg");
        $path = $folder . "/" . $filename;

        Storage::disk("s3-tokyo")->put($path, (string) $file);

        return Asset::create([
            "path" => $path,
            "size" => $file->size(),
            "mime_type" => "image/jpeg",
        ]);
    }

    public function createNonuploadedAssetByObject(string $objectType, string $filename)
    {
        $path = "v3/upload/" . $objectType . "/" . $this->generateRandomFilename($filename);

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

    public function createVideoUploadUrl(Asset $asset, array $metaData = [])
    {
        [ "url" => $url ] = Storage::disk("s3-tokyo")->temporaryUploadUrl(
            $asset->path,
            now()->addMinutes(2),
            [
                'Metadata' => $metaData,
                "ContentType" => "application/octet-stream",
                "ACL" => "public-read",
            ]
        );

        return $url;
    }

    public function generateRandomFilename(string $ext)
    {
        return bin2hex(random_bytes(16)) . "-" . $ext;
    }

    public function setTranscoderJob(Asset $asset, string $jobId)
    {
        $asset->update([
            "transcoder_job_id" => $jobId
        ]);
    }

    public function completeTranscoderJob(string $jobId)
    {
        $asset = Asset::where("transcoder_job_id", $jobId)->first();
        if (is_null($asset)) {
            throw new AppException("Asset is invalid!");
        }
        $metadata = $asset->metadata ?? [];
        $pathSplit = explode("/", $asset->path);
        $filename = $pathSplit[count($pathSplit) - 1];
        $filenameSplit = explode(".", $filename);
        $metadata["transcoded_url"] = "v3/transcoded-video/" . ($filenameSplit[0] ?? "none") . ".m3u8";
        return $asset->update([
            "metadata" => $metadata
        ]);
    }
}
