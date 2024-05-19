<?php

namespace App\Http\Controllers\System\Academy;

use App\Enums\ECourseBlockImageType;
use App\Http\Controllers\Controller;
use App\Http\Resources\System\Academy\BlockImageResource;
use App\Http\Services\CourseService;
use App\Models\Course\CourseBlockImage;
use App\Models\Course\CourseGroupBlock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class BlockImageController extends Controller
{
    public function __construct(private CourseService $service)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(CourseGroupBlock $block)
    {
        $videos = $this->service->getBlockImages($block, [], ["asset"]);


        return BlockImageResource::collection($videos);
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
            ),
            [
                "v3_asset_id" => "required|exists:v3_assets,id",
                "type" => ["required", Rule::in(ECourseBlockImageType::DEFAULT->value)]
            ]
        )
            ->validate();

        $image = $this->service->createBlockImage($block, $validatedData);

        return new BlockImageResource($image);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $block, int $id)
    {
        $image = $this->service->getBlockVideoById($id);
        return new BlockImageResource($image);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $course, CourseBlockImage $image)
    {
        $this->service->deleteBlockImage($image);
        return response()->success();
    }
}
