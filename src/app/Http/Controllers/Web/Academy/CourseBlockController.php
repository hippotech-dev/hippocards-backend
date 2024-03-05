<?php

namespace App\Http\Controllers\Web\Academy;

use App\Enums\ECourseExamType;
use App\Exceptions\AppException;
use App\Http\Controllers\Controller;
use App\Http\Resources\System\Academy\GroupBlockResource;
use App\Http\Services\CourseService;
use App\Models\Course\Course;
use App\Models\Course\CourseGroupBlock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CourseBlockController extends Controller
{
    public function __construct(private CourseService $service)
    {
        $this->middleware("jwt.auth", [
            "only" => [ "show" ]
        ]);
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
        $block = Cache::remember(
            cache_key("show-block-detail", [ $id ]),
            3600,
            fn () => $this->service->getCourseBlockByIdLoaded($course, $id)
        );

        $requestUser = auth()->user();
        if (is_null($block)) {
            throw new AppException("Not found!");
        }
        $this->service->setCurrentBlockCompletion($course, $requestUser, $block);

        return new GroupBlockResource($block);
    }

    /**
     * Get exam questions
     */
    public function getCourseExamData(Request $request, CourseGroupBlock $block)
    {
        $validatedData = Validator::make(
            $request->only(
                "type"
            ),
            [
                "type" => ["required", Rule::in(ECourseExamType::CHOOSE->value)]
            ]
        )
            ->validate();

        $random = rand(15, 20);

        $questions = Cache::remember(
            cache_key("course-exam-questions", [ $validatedData["type"], $block->id, $random ]),
            60,
            fn () => $this->service->getCourseExamQuestions($block)
        );

        shuffle($questions);

        return response()->success([
            "type" => ECourseExamType::CHOOSE,
            "start_date" => date("Y-m-d H:i:s"),
            "questions" => $questions,
        ]);
    }

    /**
     * Submit exam answers
     */
    public function submitExamAnswers(Request $request, CourseGroupBlock $block)
    {
        $validatedData = Validator::make(
            $request->only(
                "answers"
            ),
            [
                "answers" => "required|array",
                "answers.*.question_id" => "required|integer",
                "answers.*.answer_id" => "required|integer",
            ]
        )
            ->validate();

        $result = $this->service->submitExamAnswers($block, $validatedData["answers"]);

        return response()->success($result);
    }
}
