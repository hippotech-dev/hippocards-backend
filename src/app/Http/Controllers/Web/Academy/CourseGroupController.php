<?php

namespace App\Http\Controllers\Web\Academy;

use App\Http\Controllers\Controller;
use App\Http\Resources\System\Academy\CourseGroupResource;
use App\Http\Services\CourseService;
use App\Models\Course\Course;
use Illuminate\Http\Request;

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
        $groups = $this->service->getCourseGroups($course, [ "blocks" ]);

        return CourseGroupResource::collection($groups);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
