<?php

namespace App\Http\Controllers\Web\Academy;

use App\Http\Controllers\Controller;
use App\Http\Resources\System\Academy\CourseResource;
use App\Http\Services\CourseService;
use App\Models\Course\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CourseController extends Controller
{
    public function __construct(private CourseService $service)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $courses = Cache::remember(
            cache_key("list-course"),
            3600,
            fn () => $this->service->getCourseWithPage()
        );

        return CourseResource::collection($courses);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        $course = Cache::remember(
            cache_key("show-course", [ $id ]),
            3600,
            fn () => $this->service->getCourseById($id)
        );
        return new CourseResource($course);
    }

    /**
     * Get learn data
     */
    public function getLearnData(Course $course)
    {
        $kanbanData = $this->service->getCourseLearnData($course);

        return response()->success($kanbanData);
    }
}
