<?php

namespace App\Jobs;

use App\Http\Services\AssetService;
use App\Http\Services\CourseService;
use App\Models\Course\Course;
use App\Models\User\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Intervention\Image\ImageManager;
use Intervention\Image\Typography\FontFactory;

class CourseCertificateJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    private $certificates = [
        3 => "certificate-1.jpg",
        4 => "certificate-3.jpg",
        5 => "certificate-3.jpg",
    ];

    /**
     * Create a new job instance.
     */
    public function __construct(private Course $course, private User $user)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(AssetService $assetService, CourseService $courseService): void
    {
        if (array_key_exists($this->course->id, $this->certificates)) {
            $certificateFilename = $this->certificates[$this->course->id];
        } else {
            Log::channel("custom")->info("Certificate does not exist!");
            $certificateFilename = 'certificate.png';
        }

        $image = ImageManager::gd()->read(public_path($certificateFilename));

        $image->text($this->user->name, $image->width() / 2, $image->height() / 2, function (FontFactory $font) {
            $font->color('#333333');
            $font->filename(public_path("fonts/Montserrat-Regular.ttf"));
            $font->size(64);
            $font->align('center');
            $font->valign('center');
        });

        $image->text("Date: " . date("Y/m/d"), $image->width() / 2, $image->height() / 2 + 510, function (FontFactory $font) {
            $font->color('#333333');
            $font->filename(public_path("fonts/Montserrat-Regular.ttf"));
            $font->size(18);
            $font->align('center');
            $font->valign('center');
        });

        $asset = $assetService->createImageAsset($image->toJpeg());

        $courseService->createCourseCertificate($this->course, $this->user, $asset);
    }
}
