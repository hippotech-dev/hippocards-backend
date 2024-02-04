<?php

namespace App\Http\Controllers\System\Academy;

use App\Enums\ECourseBlockType;
use App\Http\Controllers\Controller;
use App\Http\Resources\System\Academy\GroupBlockResource;
use App\Http\Services\CourseService;
use App\Models\Course\CourseGroup;
use App\Models\Course\CourseGroupBlock;
use Google\Service\SearchConsole\BlockedResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class GroupBlockController extends Controller
{
    public function __construct(private CourseService $service) {}

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
                "sort_id" => "required|exists:sort,id",
                "type" => ["required", Rule::in([ ECourseBlockType::EXAM->value, ECourseBlockType::FINAL_EXAM->value, ECourseBlockType::LESSON->value ])]
            ]
        )
            ->validate();

        $block = $this->service->createGroupBlock($group, $validatedData);

        return new BlockedResource($block);
    }

    /**
     * Display the specified resource.
     */
    public function show(CourseGroup $group, int $id)
    {
        $block = $this->service->getGroupBlockById($group, $id);
        return new BlockedResource($block);
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

        return new BlockedResource($block);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $group, CourseGroupBlock $block)
    {
        $this->service->deleteGroupBlock($block);

        return response()->success();
    }
}
