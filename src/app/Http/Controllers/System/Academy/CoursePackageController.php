<?php

namespace App\Http\Controllers\System\Academy;

use App\Http\Controllers\Controller;
use App\Http\Resources\System\Academy\CourseDetailResource;
use App\Http\Resources\System\Academy\PackageResource;
use App\Http\Services\CourseService;
use App\Models\Course\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CoursePackageController extends Controller
{
    public function __construct(private CourseService $service) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Course $course)
    {
        $packages = $this->service->getCoursePackages($course);
        return PackageResource::collection($packages);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Course $course)
    {
        $validatedData = Validator::make(
            $request->only([
                "packages",
            ]),
            [
                "packages" => "present|array",
                "packages.*.package_id" => "required|integer",
                "packages.*.order" => "required|integer"
            ]
        )
            ->validate();

        $this->service->attachPackagesToCourse($course, $validatedData["packages"]);

        return response()->success();
    }
}
