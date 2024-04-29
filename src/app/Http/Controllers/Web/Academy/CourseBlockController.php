<?php

namespace App\Http\Controllers\Web\Academy;

use App\Enums\ECourseExamType;
use App\Http\Controllers\Controller;
use App\Http\Resources\Web\Academy\GroupBlockResource;
use App\Http\Services\CourseService;
use App\Models\Course\Course;
use App\Models\Course\CourseGroupBlock;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class CourseBlockController extends Controller
{
    public function __construct(private CourseService $service)
    {
        $this->middleware("jwt.auth");
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
            cache_key("show-block-detail-v2", [ $id ]),
            3600,
            fn () => new GroupBlockResource($this->service->getCourseBlockByIdLoaded($course, $id))
        );

        return $block;
    }

    /**
     * Set course completion
     */
    public function setCourseCompletion(Course $course, CourseGroupBlock $block)
    {
        $requestUser = auth()->user();

        $this->service->setCurrentBlockCompletion($course, $requestUser, $block);

        return response()->success();
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
            600,
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

    /**
     * Submit sentence and keyword response
     */
    public function submitSentenceKeywordResponse(Request $request, CourseGroupBlock $block)
    {
        $validatedData = Validator::make(
            $request->only(
                "sentences",
                "keyword"
            ),
            [
                "sentences" => "sometimes|array",
                "sentences.*.sentence" => "required|string|max:512",
                "sentences.*.sentence_translation" => "required|string|max:512",
                "sentences.*.sentence_hint" => "required|string|max:512",
                "keyword" => "sometimes|string|max:128"
            ]
        )
            ->validate();

        if (count($validatedData) === 0) {
            throw new Exception("Empty data!");
        }
        $requestUser = auth()->user();
        $course = $this->service->getCourseById($block->v3_course_id);
        $completion = $this->service->getCourseCompletion($course, $requestUser);
        $this->service->submitSentenceKeywordResponse($block, $completion, $requestUser, $validatedData);

        return response()->success();
    }

    /**
     * Get sentence and keywords exam
     */
    public function getSentenceKeywordsResponses(Request $request, CourseGroupBlock $block)
    {
        $type = $request->get("type", "sentence");
        $requestUser = auth()->user();
        $course = $this->service->getCourseById($block->v3_course_id);
        $completion = $this->service->getCourseCompletion($course, $requestUser);
        $responses = $this->service->getSentenceKeywordResponse($block, $completion, $type);

        return response()->success($responses);
    }
}
