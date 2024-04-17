<?php

namespace App\Http\Controllers\Web\Academy;

use App\Http\Controllers\Controller;
use App\Http\Resources\Web\Academy\CertificateResource;
use App\Http\Services\AssetService;
use App\Http\Services\CourseService;
use App\Jobs\CourseCertificateJob;
use App\Models\Course\Course;
use App\Models\User\User;
use Illuminate\Http\Request;
use Intervention\Image\ImageManager;
use Intervention\Image\Typography\FontFactory;

class CertificateController extends Controller
{
    public function __construct(private AssetService $assetService, private CourseService $service)
    {
        $this->middleware("jwt.auth", [
            "only" => [ "index" ]
        ]);
    }

    public function store()
    {
        dispatch(new CourseCertificateJob(Course::first(), User::find(1114988)));
    }

    public function index()
    {
        $requestUser = auth()->user();

        $certificates = $this->service->getUserCertificates($requestUser);

        return CertificateResource::collection($certificates);
    }
}
