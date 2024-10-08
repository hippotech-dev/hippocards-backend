<?php

namespace App\Http\Controllers\Web\Academy;

use App\Http\Controllers\Controller;
use App\Http\Resources\Web\Academy\CourseGroupResource;
use App\Http\Services\CourseService;
use App\Models\Course\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CourseGroupController extends Controller
{
    public function __construct(private CourseService $service)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Course $course)
    {
        $groups = Cache::remember(
            cache_key("list-group-v2", [ $course->id ]),
            3600,
            fn () => $this->service->getCourseGroups($course, [ "blocks" => fn ($query) => $query->orderBy("order") ])
        );

        return CourseGroupResource::collection($groups);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }
}
