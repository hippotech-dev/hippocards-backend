<?php

namespace App\Http\Controllers\System\Academy;

use App\Enums\ECourseBlockVideoTimestampType;
use App\Http\Controllers\Controller;
use App\Http\Resources\System\Academy\VideoTimestampResource;
use App\Http\Services\CourseService;
use App\Models\Course\CourseBlockVideo;
use App\Models\Course\CourseBlockVideoTimestamp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class VideoTimestampController extends Controller
{
    public function __construct(private CourseService $service)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, CourseBlockVideo $video)
    {
        $validatedData = Validator::make(
            $request->only(
                "start",
                "end",
                "type",
                "content"
            ),
            [
                "start" => "required|integer",
                "end" => "required|integer",
                "type" => ["required", Rule::in(ECourseBlockVideoTimestampType::EXAM->value, ECourseBlockVideoTimestampType::IMAGE->value, ECourseBlockVideoTimestampType::TEXT->value)],
                "content" => "required"
            ]
        )
            ->validate();

        $timestamp = $this->service->createVideoTimestamp($video, $validatedData);

        return new VideoTimestampResource($timestamp);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $video, CourseBlockVideoTimestamp $timestamp)
    {
        $validatedData = Validator::make(
            $request->only(
                "start",
                "end",
                "type",
                "content"
            ),
            [
                "start" => "required|integer",
                "end" => "required|integer",
                "type" => ["required", Rule::in(ECourseBlockVideoTimestampType::EXAM->value, ECourseBlockVideoTimestampType::INPUT->value, ECourseBlockVideoTimestampType::TEXT->value)],
                "content" => "required"
            ]
        )
            ->validate();

        $timestamp = $this->service->updateVideoTimestamp($timestamp, $validatedData);

        return new VideoTimestampResource($timestamp);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $video, CourseBlockVideoTimestamp $timestamp)
    {
        $this->service->deleteVideoTimestamp($timestamp);
        return response()->success();
    }
}
