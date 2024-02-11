<?php

namespace App\Http\Controllers\System\Academy;

use App\Enums\ELanguageLevel;
use App\Http\Controllers\Controller;
use App\Http\Resources\System\Academy\CourseResource;
use App\Http\Services\CourseService;
use App\Http\Services\PackageService;
use App\Models\Course\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

use function Aws\boolean_value;

class CourseController extends Controller
{
    public function __construct(private CourseService $service) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $courses = $this->service->getCourseWithPage();

        return CourseResource::collection($courses);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = Validator::make(
            $request->only(
                "name",
                "description",
                "v3_thumbnail_asset_id",
                "language_id",
                "level"
            ),
            [
                "name" => "required|string|max:64",
                "description" => "required|string|max:256",
                "v3_thumbnail_asset_id" => "required|integer|exists:v3_assets,id",
                "language_id" => "required|integer|exists:language,id",
                "level" => [
                    "required",
                    Rule::in(
                        ELanguageLevel::BEGINNER->value,
                        ELanguageLevel::INTERMIDIATE->value,
                        ELanguageLevel::ADVANCED->value
                    )
                ]
            ]
        )
            ->validate();

        $course = $this->service->createCourse($validatedData);

        return response()->success(new CourseResource($course));
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, int $id)
    {
        $short = boolean_value($request->get("short", false)) ?? true;
        $course = $this->service->getCourseById($id, $short);
        return new CourseResource($course);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Course $course)
    {
        $validatedData = Validator::make(
            $request->only(
                "name",
                "description",
                "v3_thumbnail_asset_id",
                "language_id",
                "level"
            ),
            [
                "name" => "sometimes|string|max:64",
                "description" => "sometimes|string|max:256",
                "v3_thumbnail_asset_id" => "sometimes|integer|exists:v3_assets,id",
                "language_id" => "sometimes|integer|exists:language,id",
                "level" => [
                    "required",
                    Rule::in(
                        ELanguageLevel::BEGINNER->value,
                        ELanguageLevel::INTERMIDIATE->value,
                        ELanguageLevel::ADVANCED->value
                    )
                ]
            ]
        )
            ->validate();


        $course = $this->service->updateCourse($course, $validatedData);

        return response()->success(new CourseResource($course));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Course $course)
    {
        $this->service->deleteCourse($course);
        return response()->success();
    }

    /**
     * Automated groups and block creation
     */
    public function automatedGroupsAndBlockCreate(Course $course)
    {
        DB::transaction(function () use ($course) {
            $this->service->createGroupsWithBlocks($course);
        });

        return response()->success();
    }

    /**
     * Get course kanban data
     */
    public function getCourseKanbanData(Course $course)
    {
        $kanbanData = $this->service->getCourseKanbanData($course);

        return response()->success($kanbanData);
    }
}
