<?php

namespace App\Http\Controllers\Web\Academy;

use App\Http\Controllers\Controller;
use App\Http\Resources\System\Academy\GroupBlockResource;
use App\Http\Services\CourseService;
use App\Models\Course\Course;

class CourseBlockController extends Controller
{
    public function __construct(private CourseService $service)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Course $course)
    {
        $blocks = $this->service->getCourseBlocks($course);

        return GroupBlockResource::collection($blocks);
    }

    /**
     * Display the specified resource.
     */
    public function show(Course $course, int $id)
    {
        $block = $this->service->getCourseBlockById($course, $id);
        return new GroupBlockResource($block);
    }
}
