<?php

namespace App\Http\Controllers\Web\Academy;

use App\Enums\EStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\Web\Academy\CourseCompletionResource;
use App\Http\Services\CourseService;
use App\Models\Course\Course;
use App\Models\Course\CourseExamResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CourseCompletionController extends Controller
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
        $requestUser =  auth()->user();
        $completion = $this->service->getCourseCompletion($course, $requestUser);
        $finalExamResult = $this->service->getCourseFinalExamResult($course, $requestUser);
        return response()->success([
            "completion" => new CourseCompletionResource($completion),
            "final_exam_result" => is_null($finalExamResult) ? null : new CourseExamResult($finalExamResult)
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Course $course)
    {
        $validatedData = Validator::make(
            $request->only(
                "block_id",
                "status"
            ),
            [
                "block_id" => "required|integer",
                "status" => [ "required", Rule::in(EStatus::cases()) ]
            ]
        )
            ->validate();

        $requestUser = auth()->user();
        $completionItem = $this->service->submitCourseCompletionProgress($course, $requestUser, $validatedData);

        return response()->success($completionItem);
    }
}
