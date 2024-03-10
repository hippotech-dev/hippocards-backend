<?php

namespace App\Http\Controllers\Web\Academy;

use App\Http\Controllers\Controller;
use App\Http\Services\AssetService;
use App\Jobs\CourseCertificateJob;
use App\Models\Course\Course;
use App\Models\User\User;
use Illuminate\Http\Request;
use Intervention\Image\ImageManager;
use Intervention\Image\Typography\FontFactory;

class CertificateController extends Controller
{
    public function __construct(private AssetService $service)
    {
    }

    public function generate()
    {
        dispatch(new CourseCertificateJob(Course::first(), User::first()));
    }
}
