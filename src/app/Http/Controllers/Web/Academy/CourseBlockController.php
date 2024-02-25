<?php

namespace App\Http\Controllers\Web\Academy;

use App\Http\Controllers\Controller;
use App\Http\Resources\System\Academy\GroupBlockResource;
use App\Http\Services\CourseService;
use App\Models\Course\Course;
use Illuminate\Support\Facades\Cache;

class CourseBlockController extends Controller
{
    public function __construct(private CourseService $service)
    {
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
            cache_key("show-block", [ $id ]),
            3600,
            fn () => $this->service->getCourseBlockById($course, $id, [
                "wordSort",
                "wordSort.word.translation",
                "wordSort.word.pronunciation",
                "wordSort.word.wordImaginations.imagination",
                "wordSort.word.exampleSentences.example",
                "wordSort.word.pos",
                "wordSort.word.synonyms",
                "videos.asset",
                "videos.videoTimestamps"
            ])
        );

        return new GroupBlockResource($block);
    }
}
