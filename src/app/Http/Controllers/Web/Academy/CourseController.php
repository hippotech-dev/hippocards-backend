<?php

namespace App\Http\Controllers\Web\Academy;

use App\Exceptions\AppException;
use App\Http\Controllers\Controller;
use App\Http\Resources\Web\Academy\CourseResource;
use App\Http\Resources\Web\Academy\CourseExamInstanceResource;
use App\Http\Resources\Web\Academy\UserCourseResource;
use App\Http\Services\CourseService;
use App\Models\Course\Course;
use App\Models\Course\CourseExamInstance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
            fn () => $this->service->getCourseWithPage([], [ "language", "detail" ], [ "field" => "status", "value" => "desc" ])
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
            fn () => $this->service->getCourseByIdLoaded($id)
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
     * Get final exam overview
     */
    public function getFinalExamOverview(int $examInstance)
    {
        $requestUser = auth()->user();

        $examInstance = $this->service->getCourseExamInstanceById($examInstance);

        if (is_null($examInstance) || $examInstance->user_id != $requestUser->id) {
            throw new NotFoundHttpException("Exam instance not found!");
        }

        unset($examInstance->questions);
        unset($examInstance->answers);

        return new CourseExamInstanceResource($examInstance);
    }

    /**
     * Submit final exam data
     */
    public function submitFinalExamData(Request $request, Course $course, CourseExamInstance $examInstance)
    {
        $validatedData = Validator::make(
            $request->only(
                "index",
                "question_id",
                "answer_id"
            ),
            [
                "index" => "required|integer",
                "question_id" => "required|integer",
                "answer_id" => "required|integer"
            ]
        )
            ->validate();

        $requestUser = auth()->user();
        $this->service->submitCourseFinalExamQuestions($course, $requestUser, $examInstance, $validatedData);

        return response()->success();
    }

    /**
     * Finish final exam data
     */
    public function finishFinalExamData(Course $course, CourseExamInstance $examInstance)
    {
        $requestUser = auth()->user();
        $userCourse = $this->service->getActiveUserCourse($course, $requestUser);

        if ($examInstance->v3_user_course_id !== $userCourse->id) {
            throw new AppException("Not authorized!");
        }

        $this->service->finishCourseFinalExamQuestions($course, $requestUser, $examInstance);

        return response()->success();
    }

    /**
     * Get final exam answer list with correct answer
     */
    public function getFinalExamCorrectAnswers(Course $course, CourseExamInstance $examInstance)
    {
        $requestUser = auth()->user();
        $userCourse = $this->service->getActiveUserCourse($course, $requestUser);

        if ($examInstance->v3_user_course_id !== $userCourse->id) {
            throw new AppException("Not authorized!");
        }

        $questions = Cache::remember(
            cache_key("final-exam-answers", [ $userCourse->id ]),
            24 * 60 * 60,
            fn () => $this->service->getCourseFinalExamAnswers($userCourse)
        );

        return response()->success($questions);
    }

    /**
     * Get user purchased courses with active duration
     */
    public function getUserPurchasedCourses()
    {
        $requestUser = auth()->user();

        return Cache::remember(cache_key("user-courses", [ $requestUser->id, 4 ]), 3600, function () use ($requestUser) {
            $userCourses = $this->service->getUserCourses($requestUser);

            return UserCourseResource::collection($userCourses);
        });
    }
}
