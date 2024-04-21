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
use Intervention\Image\ImageManager;
use Intervention\Image\Typography\FontFactory;

class CourseCertificateJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

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
        $image = ImageManager::gd()->read(public_path('certificate.png'));

        $image->text($this->user->name, $image->width() / 2, $image->height() / 2 + 50, function (FontFactory $font) {
            $font->color('#333333');
            $font->filename(public_path("fonts/Montserrat-Regular.ttf"));
            $font->size(64);
            $font->align('center');
            $font->valign('center');
        });

        $image->text("Огноо: " . date("Y/m/d"), $image->width() / 2, $image->height() / 2 + 520, function (FontFactory $font) {
            $font->color('#333333');
            $font->filename(public_path("fonts/Montserrat-Regular.ttf"));
            $font->size(36);
            $font->align('center');
            $font->valign('center');
        });

        $asset = $assetService->createImageAsset($image->toJpeg());

        $courseService->createCourseCertificate($this->course, $this->user, $asset);
    }
}
