<?php

namespace App\Http\Services;

use App\Enums\ECourseBlockType;
use App\Enums\ECourseBlockVideoType;
use App\Exceptions\AppException;
use App\Exceptions\UnauthorizedException;
use App\Http\Resources\System\Academy\BlockKanbanCardResource;
use App\Http\Resources\System\Academy\CourseGroupResource;
use App\Http\Resources\System\Academy\GroupBlockResource;
use App\Models\Course\Course;
use App\Models\Course\CourseBlockVideo;
use App\Models\Course\CourseBlockVideoTimestamp;
use App\Models\Course\CourseGroup;
use App\Models\Course\CourseGroupBlock;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class CourseService
{
    private array $filterModel = [

    ];

    public function __construct(private AssetService $assetService, private PackageService $packageService)
    {
    }

    /**
     * Course
     */

    public function getCourses(array $filter = [], array $with = [])
    {

        return filter_query_with_model(Course::with($with), $this->filterModel, $filter)->get();
    }

    public function getCourseWithPage(array $filter = [], array $with = [ "language", "detail" ], int $size = 20)
    {

        return filter_query_with_model(Course::with($with)->latest(), $this->filterModel, $filter)->paginate($size);
    }

    public function getCourseById(int $id, array $with = [ "groups", "detail", "packages", "groups" ])
    {
        return Course::with($with)->find($id);
    }

    public function createCourse(array $data)
    {
        array_key_exists("v3_thumbnail_asset_id", $data)
            && $data["thumbnail"] = $this->assetService->getAssetPath($data["v3_thumbnail_asset_id"]);

        return Course::create($data);
    }

    public function updateCourse(Course $course, array $data)
    {
        array_key_exists("v3_thumbnail_asset_id", $data)
            && $data["v3_thumbnail_asset_id"] !== $course->v3_thumbnail_asset_id
            && $data["thumbnail"] = $this->assetService->getAssetPath($data["v3_thumbnail_asset_id"]);

        $course->update($data);

        return $course;
    }

    public function deleteCourse(Course $course)
    {
        !is_null($course->v3_thumbnail_asset_id)
            && $this->assetService->deleteAssetById($course->v3_thumbnail_asset_id);

        return $course->delete();
    }

    public function getCoursePackages(Course $course)
    {
        if ($course->relationLoaded("packages")) {
            $packages = $course->packages;
        } else {
            $packages = $course->packages()->get();
        }
        return $packages;
    }

    public function getCourseKanbanData(Course $course)
    {
        $blocks = $this->getCourseBlocks($course);
        $columns = $this->getCourseGroups($course);

        foreach ($columns as $column) {
            $column->cardIds = $blocks->where("v3_course_group_id", $column->id)->sortBy("order")->pluck("id");
        }

        return [
            "cards" => BlockKanbanCardResource::collection($blocks),
            "columns" => CourseGroupResource::collection($columns),
            "columnOrder" => $columns->pluck("id")
        ];
    }

    /**
     * Detail
     */

    public function getCourseDetail(Course $course)
    {
        return $course->detail()->first();
    }

    public function createOrUpdateDetail(Course $course, array $data)
    {
        array_key_exists("price", $data)
            && $data["price_string"] = number_format($data["price"]);
        return $course->detail()->updateOrCreate([], $data);
    }

    public function attachPackagesToCourse(Course $course, array $data)
    {
        $generatedData = array();
        foreach ($data as &$item) {
            $generatedData[$item["package_id"] ?? 0] = [
                "order" => $item["order"] ?? 0
            ];
        }

        $course->packages()->detach();
        return $course->packages()->attach($generatedData);
    }

    /**
     * Course Group
     */

    public function getCourseGroups(Course $course)
    {
        return $course->groups()->orderBy("order", "asc")->get();
    }

    public function getCourseGroupById(Course $course, int $id, array $with = [])
    {
        return $course->groups()->with($with)->where("id", $id)->first();
    }

    public function createCourseGroup(Course $course, array $data)
    {
        $data["order"] = $course->groups()->count();
        return $course->groups()->create($data);
    }

    public function updateCourseGroup(CourseGroup &$group, array $data)
    {
        return $group->update($data);
    }

    public function deleteCourseGroup(CourseGroup &$group)
    {
        CourseGroup::where("v3_course_id", $group->v3_course_id)->where("order", ">", $group->order)->decrement("order");
        return $group->delete();
    }

    public function shiftCourseGroup(Course $course, CourseGroup $group, int $newPosition)
    {
        $count = $course->groups()->count();
        if ($newPosition > $count) {
            throw new AppException("New order is exceeding the current number of groups!");
        }
        if ($newPosition === $group->order) {
            throw new AppException("No change!");
        }
        return DB::transaction(function () use ($course, $group, $newPosition) {
            if ($group->order < $newPosition) {
                $course->groups()->where("order", "<=", $newPosition)->where("order", ">", $group->order)->decrement("order");
            } else {
                $course->groups()->where("order", ">=", $newPosition)->where("order", "<", $group->order)->increment("order");
            }
            return $this->updateCourseGroup($group, [ "order" => $newPosition ]);
        });
    }

    public function createManyCourseGroups(Course $course)
    {
        $detail = $this->getCourseDetail($course);
        if (is_null($detail)) {
            throw new AppException("Coursse detail is not added!");
        }
        $totalDays = $detail->duration_days;

        $courseGroupData = [];

        for ($day = 1; $day <= $totalDays; $day++) {
            array_push($courseGroupData, [
                "name" => "Day $day",
                "order" => $day - 1
            ]);
        }

        return $course->groups()->createMany($courseGroupData);
    }

    /**
     * Group Block
     */
    public function getGroupBlocks(CourseGroup $group)
    {
        return $group->blocks()->get();
    }

    public function getCourseBlocks(Course $course)
    {
        return $course->blocks()->get();
    }

    public function getGroupBlockById(CourseGroup $group, int $id, array $with = ["wordSort", "videos.asset"])
    {
        return $group->blocks()->with($with)->where("id", $id)->first();
    }

    public function createGroupBlock(CourseGroup $group, array $data)
    {
        $count = $group->blocks()->count();
        if (array_key_exists("sort_id", $data)) {
            $sort = $this->packageService->getSortById($data["sort_id"]);
            if (is_null($sort) || is_null($sort->word)) {
                throw new AppException("Invalid sort!");
            }
            $data["name"] = $sort->word->word;
        }

        $data["order"] = $count;
        $data["v3_course_id"] = $group->v3_course_id;
        return $group->blocks()->create($data);
    }

    public function updateGroupBlock(CourseGroupBlock $block, array $data)
    {
        if (array_key_exists("sort_id", $data)) {
            $sort = $this->packageService->getSortById($data["sort_id"]);
            if (is_null($sort) || is_null($sort->word)) {
                throw new AppException("Invalid sort!");
            }
            $data["name"] = $sort->word->word;
        }

        if (array_key_exists("metadata", $data)) {
            $metadata = $block->metadata ?? [];
            $data["metadata"] = array_merge(
                $metadata,
                $data["metadata"]
            );
        }

        return $block->update($data);
    }

    public function deleteGroupBlock(CourseGroupBlock $block)
    {
        CourseGroupBlock::where("v3_course_group_id", $block->v3_course_group_id)->where("order", ">", $block->order)->decrement("order");
        return $block->delete();
    }

    public function shiftGroupBlock(CourseGroup $group, CourseGroupBlock $block, int $newGroup, int $newPosition)
    {
        if ($group->id !== $block->v3_course_group_id) {
            throw new UnauthorizedException("Invalid object!");
        }
        $count = $group->blocks()->count();
        if ($newPosition > $count) {
            throw new AppException("New order is exceeding the current number of blocks!");
        }
        return DB::transaction(function () use ($group, $block, $newGroup, $newPosition) {
            if ($block->v3_course_group_id !== $newGroup) {
                $group->blocks()->where("order", ">", $block->order)->decrement("order");
                CourseGroupBlock::where("v3_course_group_id", $newGroup)->where("order", ">=", $newPosition)->increment("order");
            } elseif ($block->order < $newPosition) {
                $group->blocks()->where("order", "<=", $newPosition)->where("order", ">", $block->order)->decrement("order");
            } else {
                $group->blocks()->where("order", ">=", $newPosition)->where("order", "<", $block->order)->increment("order");
            }
            return $this->updateGroupBlock($block, [ "order" => $newPosition, "v3_course_group_id" => $newGroup ]);
        });
    }

    public function createManyGroupBlocks(CourseGroup $group, Collection $packageSorts)
    {
        $blockData = [];

        $sortIndex = 0;
        foreach ($packageSorts as &$sort) {
            if (is_null($sort->word)) {
                continue;
            }
            array_push($blockData, [
                "sort_id" => $sort->id,
                "name" => $sort->word->word,
                "type" => ECourseBlockType::LESSON,
                "order" => $sortIndex,
                "v3_course_id" => $group->v3_course_id
            ]);

            $sortIndex++;
        }

        $group->blocks()->createMany($blockData);
    }

    public function createBlocksMultipleGroup(Collection $groups, Collection $packageSorts)
    {
        $totalGroupCount = $groups->count();
        $totalPackageCount = $packageSorts->count();
        $totalSortPerGroup = $totalPackageCount / $totalGroupCount;

        $groupIndex = 0;
        foreach ($groups as &$group) {
            $slicedPackageSorts = $packageSorts->slice($groupIndex * $totalSortPerGroup, $totalSortPerGroup);
            $this->createManyGroupBlocks($group, $slicedPackageSorts);
            $groupIndex++;
        }
    }

    /**
     * Automated creation
     */

    public function createGroupsWithBlocks(Course $course)
    {
        $packages = $this->getCoursePackages($course);

        if (count($packages) === 0) {
            throw new AppException("Packages are not added!");
        }

        $packageSorts = $this->packageService->getPackagesSorts($packages);

        $courseGroups = $this->createManyCourseGroups($course);

        $this->createBlocksMultipleGroup($courseGroups, $packageSorts);
    }

    /**
     * Video
     */

    public function getBlockVideos(CourseGroupBlock $block, array $with = [])
    {
        return $block->videos()->with($with)->get();
    }

    public function getBlockVideoById(int $id, array $with = [
        "videoTimestamps",
        "asset"
    ])
    {
        return CourseBlockVideo::with($with)->find($id);
    }

    public function getBlockVideoByType(CourseGroupBlock $block, ECourseBlockVideoType $type, array $with = [])
    {
        return $block->videos()->with($with)->where("type", $type)->first();
    }

    public function createUpdateBlockVideo(CourseGroupBlock $block, array $data)
    {
        $type = ECourseBlockVideoType::from($data["type"]);
        $video = $this->getBlockVideoByType($block, $type);
        if (is_null($video)) {
            $video = $block->videos()->create($data);
        } else {
            $video->update($data);
        }

        $this->updateGroupBlock($block, [
            "metadata" => [
                ($type === ECourseBlockVideoType::IMAGINATION ? "upload_imagination" : "upload_definition") => true
            ]
        ]);

        return $video;
    }

    public function deleteBlockVideo(CourseBlockVideo $video)
    {
        return $video->delete();
    }

    public function createVideoTimestamp(CourseBlockVideo $video, array $data)
    {
        $checkTimestamp = $video->videoTimestamps()->where("end", ">", $data["start"])->where("start", "<", $data["end"])->first();

        if (!is_null($checkTimestamp)) {
            throw new AppException("Timestamp is overlapping with other timestamp");
        }

        return $video->videoTimestamps()->create($data);
    }

    public function updateVideoTimestamp(CourseBlockVideoTimestamp $timestamp, array $data)
    {
        return $timestamp->update($data);
    }

    public function deleteVideoTimestamp(CourseBlockVideoTimestamp $timestamp)
    {
        return $timestamp->delete();
    }
}
