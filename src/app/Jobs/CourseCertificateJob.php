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
        $image = ImageManager::gd()->read(public_path('certificate.jpg'));

        $image->text($this->user->name, 100, 550, function (FontFactory $font) {
            $font->color('#333333');
            $font->filename(public_path("fonts/Roboto-Regular.ttf"));
            $font->size(48);
        });

        $asset = $assetService->createImageAsset($image->toJpeg());

        $courseService->createCourseCertificate($this->course, $this->user, $asset);
    }
}
