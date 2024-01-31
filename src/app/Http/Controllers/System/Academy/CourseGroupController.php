<?php

namespace App\Http\Controllers\System\Academy;

use App\Http\Controllers\Controller;
use App\Http\Resources\System\Academy\CourseGroupResource;
use App\Http\Services\CourseService;
use App\Http\Services\PackageService;
use App\Models\Course\Course;
use App\Models\Course\CourseGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CourseGroupController extends Controller
{
    public function __construct(private CourseService $service) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Course $course)
    {
        $groups = $this->service->getCourseGroups($course);

        return CourseGroupResource::collection($groups);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Course $course)
    {
        $validatedData = Validator::make(
            $request->only(
                "name",
                "order",
            ),
            [
                "name" => "sometimes|string",
                "order" => "sometimes|integer",
            ]
        )
            ->validate();

        $group = $this->service->createCourseGroup($course, $validatedData);

        return new CourseGroupResource($group);
    }

    /**
     * Display the specified resource.
     */
    public function show(Course $course, string $id)
    {
        $group = $this->service->getCourseGroupById($course, $id);
        return new CourseGroupResource($group);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $course, CourseGroup $group)
    {
        $validatedData = Validator::make(
            $request->only(
                "name",
            ),
            [
                "name" => "sometimes|string",
            ]
        )
            ->validate();

        $this->service->updateCourseGroup($group, $validatedData);

        return new CourseGroupResource($group);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $course, CourseGroup $group)
    {
        $this->service->deleteCourseGroup($group);
        return response()->success();
    }

    /**
     * Shift course gorups
     */
    public function shiftGroups(Request $request, Course $course, CourseGroup $group)
    {
        $validatedData = Validator::make(
            $request->only(
                "order"
            ),
            [
                "order" => "required|integer"
            ]
        )
            ->validate();

        $this->service->shiftCourseGroup($course, $group, $validatedData["order"]);

        return response()->success();
    }
}
