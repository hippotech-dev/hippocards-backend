<?php

namespace App\Http\Controllers\System\Academy;

use App\Enums\ECourseBlockType;
use App\Http\Controllers\Controller;
use App\Http\Resources\System\Academy\GroupBlockResource;
use App\Http\Services\CourseService;
use App\Models\Course\CourseGroup;
use App\Models\Course\CourseGroupBlock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class GroupBlockController extends Controller
{
    public function __construct(private CourseService $service)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(CourseGroup $group)
    {
        $blocks = $this->service->getGroupBlocks($group);

        return GroupBlockResource::collection($blocks);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, CourseGroup $group)
    {
        $validatedData = Validator::make(
            $request->only(
                "sort_id",
                "type",
            ),
            [
                "sort_id" => "sometimes|exists:sort,id",
                "type" => ["required", Rule::in([ ECourseBlockType::EXAM->value, ECourseBlockType::FINAL_EXAM->value, ECourseBlockType::LESSON->value ])]
            ]
        )
            ->validate();

        $block = $this->service->createGroupBlock($group, $validatedData);

        return new GroupBlockResource($block);
    }

    /**
     * Display the specified resource.
     */
    public function show(CourseGroup $group, int $id)
    {
        $block = $this->service->getGroupBlockById($group, $id);
        return new GroupBlockResource($block);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $group, CourseGroupBlock $block)
    {
        $validatedData = Validator::make(
            $request->only(
                "sort_id",
                "type",
            ),
            [
                "sort_id" => "sometimes|exists:sort,id",
                "type" => ["sometimes", Rule::in([ ECourseBlockType::EXAM->value, ECourseBlockType::FINAL_EXAM->value, ECourseBlockType::LESSON->value ])]
            ]
        )
            ->validate();

        $this->service->updateGroupBlock($block, $validatedData);

        return new GroupBlockResource($block);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $group, CourseGroupBlock $block)
    {
        $this->service->deleteGroupBlock($block);

        return response()->success();
    }

    /**
     * Shift group blocks
     */
    public function shiftBlock(Request $request, CourseGroup $group, CourseGroupBlock $block)
    {
        $validatedData = Validator::make(
            $request->only(
                "v3_course_group_id",
                "order"
            ),
            [
                "v3_course_group_id" => "required|integer",
                "order" => "required|integer"
            ]
        )
            ->validate();

        $this->service->shiftGroupBlock($group, $block, $validatedData["v3_course_group_id"], $validatedData["order"]);

        return response()->success();
    }

    /**
     * Create or update block details
     */
    public function createUpdateBlockDetail(Request $request, CourseGroupBlock $block)
    {
        $validatedData = Validator::make(
            $request->only(
                "sentences",
                "keywords"
            ),
            [
                "sentences" => "present|array",
                "sentences.*.value" => "required|string",
                "sentences.*.translation" => "nullable|string",
                "sentences.*.is_english" => "required|boolean",
                "keywords" => "present|array",
                "keywords.*" => "required|string"
            ],
        )
            ->validate();

        $this->service->createUpdateBlockDetail($block, $validatedData);

        return response()->success();
    }

    /**
     * Import word images to block
     */
    public function importWordImagesToBlock(CourseGroupBlock $block)
    {
        $this->service->importWordImagesToBlock($block);

        return response()->success();
    }

    /**
     * Import word sentences to block
     */
    public function importWordSentencesToBlock(CourseGroupBlock $block)
    {
        $this->service->importWordSentencesToBlock($block);

        return response()->success();
    }
}
