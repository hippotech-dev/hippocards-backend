<?php

namespace App\Http\Controllers\System\Academy;

use App\Enums\ELanguageLevel;
use App\Http\Controllers\Controller;
use App\Http\Resources\System\Academy\CourseResource;
use App\Http\Services\CourseService;
use App\Models\Course\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use function Aws\boolean_value;

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
        $courses = $this->service->getCourseWithPage([], [ "language", "detail" ], [ "field" => "id", "value" => "desc" ]);

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
                "description" => "required|string|max:1024",
                "v3_thumbnail_asset_id" => "required|integer|exists:v3_assets,id",
                "language_id" => "required|integer|exists:language,id",
                "level" => [
                    "required",
                    Rule::in(
                        ELanguageLevel::BEGINNER->value,
                        ELanguageLevel::UPPER_BEGINNER->value,
                        ELanguageLevel::INTERMIDIATE->value,
                        ELanguageLevel::UPPER_INTERMIDIATE->value,
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
    public function show(int $id)
    {
        $course = $this->service->getCourseByIdLoaded($id);

        if (is_null($course)) {
            throw new NotFoundHttpException();
        }

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
                "level",
                "status"
            ),
            [
                "status" => "sometimes|integer",
                "name" => "sometimes|string|max:64",
                "description" => "sometimes|string|max:1024",
                "v3_thumbnail_asset_id" => "sometimes|integer|exists:v3_assets,id",
                "language_id" => "sometimes|integer|exists:language,id",
                "level" => [
                    "required",
                    Rule::in(
                        ELanguageLevel::BEGINNER->value,
                        ELanguageLevel::UPPER_BEGINNER->value,
                        ELanguageLevel::INTERMIDIATE->value,
                        ELanguageLevel::UPPER_INTERMIDIATE->value,
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
    public function automatedGroupsAndBlockCreate(Request $request, Course $course)
    {
        $validatedData = Validator::make(
            $request->only(
                "exam"
            ),
            [
                "exam" => [ "required", Rule::in("exam", "non-exam") ]
            ]
        )
            ->validate();

        DB::transaction(function () use ($course, $validatedData) {
            $this->service->createGroupsWithBlocks($course, $validatedData);
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

    public function test()
    {
        return response()->success();
    }
}
