<?php

namespace App\Http\Controllers\System\Academy;

use App\Http\Controllers\Controller;
use App\Http\Resources\System\Academy\CourseIntroductionResource;
use App\Http\Services\CourseService;
use App\Models\Course\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CourseIntroductionController extends Controller
{
    public function __construct(private CourseService $service)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Course $course)
    {
        $detail = $this->service->getCourseIntroduction($course);
        return new CourseIntroductionResource($detail);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Course $course)
    {
        $validatadData = Validator::make(
            $request->only(
                "content",
                "v3_video_asset_id"
            ),
            [
                "content" => "required|array",
                "content.*.title" => "required|string|max:128",
                "content.*.body" => "required|string|max:4096",
                "content.*.index" => "required|integer",
                "v3_video_asset_id" => "nullable|integer"
            ]
        )
            ->validate();

        $detail = $this->service->createOrUpdateIntroduction($course, $validatadData);

        return new CourseIntroductionResource($detail);
    }
}
