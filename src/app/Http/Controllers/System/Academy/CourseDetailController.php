<?php

namespace App\Http\Controllers\System\Academy;

use App\Http\Controllers\Controller;
use App\Http\Resources\System\Academy\CourseDetailResource;
use App\Http\Services\CourseService;
use App\Models\Course\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CourseDetailController extends Controller
{
    public function __construct(private CourseService $service) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Course $course)
    {
        $detail = $this->service->getCourseDetail($course);
        return new CourseDetailResource($detail);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Course $course)
    {
        $validatadData = Validator::make(
            $request->only(
                "contents",
                "price",
                "price_string",
                "duration_days",
            ),
            [
                "contents" => "required|array",
                "contents.*.title" => "required|string|max:128",
                "contents.*.body" => "required|string|max:4096",
                "price" => "required|integer",
                "duration_days" => "required|integer",
            ]
        )
            ->validate();

        $detail = $this->service->createOrUpdateDetail($course, $validatadData);

        return new CourseDetailResource($detail);
    }
}
