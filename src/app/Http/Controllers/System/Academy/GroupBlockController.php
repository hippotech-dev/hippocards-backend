<?php

namespace App\Http\Controllers\System\Academy;

use App\Http\Controllers\Controller;
use App\Http\Resources\System\Academy\GroupBlockResource;
use App\Http\Services\CourseService;
use App\Models\Course\CourseGroup;
use App\Models\Course\CourseGroupBlock;
use Google\Service\SearchConsole\BlockedResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GroupBlockController extends Controller
{
    public function __construct(private CourseService $service)
    {
        $this->var = $var;
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
                "sort_id" => "required|exists:sort,id",
                "type"
            ]
        )
            ->validate();

        $block = $this->service->createGroupBlock($group, $validatedData);

        return new BlockedResource($block);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id) {}

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $course, CourseGroupBlock $block)
    {
        $validatedData = Validator::make(
            $request->only(
                "sort_id",
                "type",
            ),
            [
                "sort_id" => "required|exists:sort,id",
                "type"
            ]
        )
            ->validate();

        $block = $this->service->updateGroupBlock($block, $validatedData);

        return new BlockedResource($block);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
