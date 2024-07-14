<?php

namespace App\Http\Services;

use App\Enums\EStatus;
use App\Exceptions\AppException;
use App\Models\Utility\Asset;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Interfaces\EncodedImageInterface;
use Intervention\Image\Interfaces\ImageInterface;

class AssetService
{
    public function __construct(private VDOCipherService $vdoService)
    {

    }

    public function getAssetById(int $id)
    {
        return Asset::find($id);
    }

    public function getAsset(array $filter, array $with = [])
    {
        $filterModel = [
            "vdo_drm_video_id" => [ "where", "vdo_drm_video_id" ],
            "transcoder_job_id" => [ "where", "transcoder_job_id" ]
        ];

        return  filter_query_with_model(Asset::query(), $filterModel, $filter)->with($with)->first();
    }

    public function getAssetPath(int $id)
    {
        $asset = $this->getAssetById($id);
        if (is_null($asset)) {
            return null;
        }
        return $asset->path;
    }

    public function createAsset(UploadedFile $file, $folder = null)
    {
        $folder = !is_null($folder) ? "v3/assets/" . $folder : "v3/assets/" . date("Y-m");
        $filename = $file->getClientOriginalName();
        $filename = $this->generateRandomFilename($filename . "." . $file->extension());
        $path = $folder . "/" . $filename;

        Storage::disk("s3-tokyo")->putFileAs($folder, $file, $filename);

        return Asset::create([
            "path" => $path,
            "name" => $filename,
            "size" => $file->getSize(),
            "mime_type" => $file->getMimeType(),
        ]);
    }

    public function createAudioAsset(mixed $contents)
    {
        $folder = "v3/audio/" . date("Y-m");
        $filename = $this->generateRandomFilename("audio.mp3");
        $path = $folder . "/" . $filename;

        Storage::disk("s3-tokyo")->put($path, $contents);

        return Asset::create([
            "path" => $path,
            "name" => $filename,
            "size" => 0,
            "mime_type" => "unknown",
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
        $path = "v3/upload/" . $objectType . "/" . $this->generateRandomFilename(
            $filename
        );

        return Asset::create([
            "path" => $path,
            "name" => $filename,
            "size" => 0,
            "mime_type" => "unknown",
        ]);
    }

    public function createAssetByUrl(string $url, string $name = null)
    {
        if (is_null($name)) {
            $urlSplit = explode("/", $url);
            $name = $urlSplit[count($urlSplit) - 1];
        }
        return Asset::create([
            "path" => $url,
            "name" => $name,
            "size" => 0,
            "mime_type" => "unknown",
        ]);
    }

    public function createAssetFromURL(string $url, string $filename)
    {
        $file = file_get_contents($url);
        $folder = "v3/unsplash/" . date("Y-m");
        $filename = $this->generateRandomFilename($filename);
        $path = $folder . "/" . $filename;

        Storage::disk("s3-tokyo")->put($path, (string) $file);

        return Asset::create([
            "path" => $path,
            "name" => $filename,
            "size" => strlen($file),
            "mime_type" => "image/jpeg",
        ]);
    }

    public function deleteAssetById(int $id)
    {
        $asset = $this->getAssetById($id);

        return $this->deleteAsset($asset);
    }

    public function deleteAsset(Asset $asset)
    {
        $this->deleteAssetFile($asset);

        $this->deleteAssetDRMVideo($asset);

        return $asset->delete();
    }

    public function deleteAssetFile(Asset $asset)
    {
        return Storage::disk("s3-tokyo")->delete($asset->path);
    }

    public function deleteAssetDRMVideo(Asset $asset)
    {
        if (is_null($asset->vdo_drm_video_id)) {
            return false;
        }

        $videoId = $asset->vdo_drm_video_id;

        if (is_null($videoId)) {
            return false;
        }

        return $this->vdoService->deleteVideo([
            $videoId
        ]);
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

    public function generateRandomFilename(string $filename)
    {
        return bin2hex(random_bytes(16)) . (($filename[0] ?? "") === "." ? $filename : '-' . $filename);
    }

    public function setTranscoderJob(Asset $asset, string $jobId)
    {
        return $asset->update([
            "transcoder_job_id" => $jobId
        ]);
    }

    public function uploadToDRMProvider(Asset $asset)
    {
        $path = $asset->path;
        $response = $this->vdoService->importByUrl(append_s3_path($path));

        return $asset->update([
            "vdo_drm_video_id" => $response["id"] ?? null,
            "vdo_drm_video_status" => EStatus::PENDING
        ]);
    }

    public function completeTranscoderJob(Asset $asset)
    {
        $metadata = $asset->metadata ?? [];
        $pathSplit = explode("/", $asset->path);
        $filename = $pathSplit[count($pathSplit) - 1];
        $filenameSplit = explode(".", $filename);
        $filenameSplit[count($filenameSplit) - 1] = "m3u8";
        $metadata["transcoded_url"] = "v3/transcoded-video/" . implode(".", $filenameSplit);

        $asset->metadata = $metadata;
        return $asset->save();
    }

    public function getVideoPlaybackAndOTPInfo(Asset $asset)
    {

        if (is_null($asset->vdo_drm_video_id)) {
            throw new AppException("Video is not protected!");
        }

        $videoId = $asset->vdo_drm_video_id;

        $otp =  Cache::remember(cache_key("vdo-video-otp-playback-v1", [ $videoId ]), 55 * 60, function () use ($videoId) {
            return $this->vdoService->getVideoOTP($videoId);
        });

        if (is_null($otp)) {
            throw new AppException("Video is not protected!");
        }

        return $otp;
    }

    public function setVDOVideoStatus(Asset $asset, string $event)
    {
        switch ($event) {
            case "video:ready":
                $status = EStatus::SUCCESS;
                break;
            case "video:error":
                $status = EStatus::FAILURE;
                break;
            default:
                $status = EStatus::PENDING;
                break;
        }

        return $asset->update([
            "vdo_drm_video_status" => $status
        ]);
    }
}
