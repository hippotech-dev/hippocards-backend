<?php

namespace App\Http\Controllers\Web\Academy;

use App\Http\Controllers\Controller;
use App\Http\Resources\System\Academy\CourseResource;
use App\Http\Resources\Web\Academy\CourseExamInstanceResource;
use App\Http\Services\CourseService;
use App\Models\Course\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

class CourseController extends Controller
{
    public function __construct(private CourseService $service)
    {
        $this->middleware("jwt.auth", [
            "except" => [
                "index",
                "show",
            ]
        ]);
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
        $kanbanData = Cache::remember(
            cache_key("get-learn-data", [$course->id]),
            3600,
            fn () => $this->service->getCourseLearnData($course)
        );

        $requestUser = auth()->user();
        $finalExamResult = $this->service->getCourseFinalExamResult($course, $requestUser);

        $kanbanData["final_exam_result"] = $finalExamResult;

        return response()->success($kanbanData);
    }

    /**
     * Get final exam data
     */
    public function getFinalExamData(Course $course)
    {
        $requestUser = auth()->user();
        $examInstance = $this->service->getCourseFinalExamQuestions($course, $requestUser);

        return new CourseExamInstanceResource($examInstance);
    }

    /**
     * Submit final exam data
     */
    public function submitFinalExamData(Request $request, Course $course)
    {
        $validatedData = Validator::make(
            $request->only(
                "questions_id",
                "answer_id"
            ),
            [
                "questions_id",
                "answer_id"
            ]
        )
            ->validate();

        $requestUser = auth()->user();
        $this->service->submitCourseFinalExamQuestions($course, $requestUser, $validatedData);

        return response()->success();
    }
}
