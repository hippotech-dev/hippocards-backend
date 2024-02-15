<?php

namespace App\Http\Controllers\System\Academy;

use App\Enums\ECourseBlockVideoType;
use App\Http\Controllers\Controller;
use App\Http\Resources\System\Academy\BlockVideoResource;
use App\Http\Services\CourseService;
use App\Models\Course\CourseBlockVideo;
use App\Models\Course\CourseGroupBlock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class BlockVideoController extends Controller
{
    public function __construct(private CourseService $service) {}

    /**
     * Display a listing of the resource.
     */
    public function index(CourseGroupBlock $block)
    {
        $videos = $this->service->getBlockVideos($block);

        return BlockVideoResource::collection($videos);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, CourseGroupBlock $block)
    {
        $validatedData = Validator::make(
            $request->only(
                "type",
                "v3_asset_id",
                "duration"
            ),
            [
                "duration" => "sometimes|integer",
                "v3_asset_id" => "required|exists:v3_assets,id",
                "type" => ["required", Rule::in(ECourseBlockVideoType::IMAGINATION->value, ECourseBlockVideoType::TRANSLATION->value)]
            ]
        )
            ->validate();

        $video = $this->service->createBlockVideo($block, $validatedData);

        return new BlockVideoResource($video);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id) {}

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CourseGroupBlock $block, CourseBlockVideo $video)
    {
        $validatedData = Validator::make(
            $request->only(
                "type",
                "v3_asset_id",
                "duration"
            ),
            [
                "duration" => "sometimes|integer",
                "v3_asset_id" => "sometimes|exists:v3_assets,id",
                "type" => ["sometimes", Rule::in(ECourseBlockVideoType::IMAGINATION->value, ECourseBlockVideoType::TRANSLATION->value)]
            ]
        )
            ->validate();

        $video = $this->service->updateBlockVideo($block, $video, $validatedData);

        return new BlockVideoResource($video);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $course, CourseBlockVideo $video)
    {
        $this->service->deleteBlockVideo($video);
        return response()->success();
    }
}
