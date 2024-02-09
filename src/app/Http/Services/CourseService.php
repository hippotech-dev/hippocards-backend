<?php

namespace App\Http\Services;

use App\Enums\ECourseBlockType;
use App\Exceptions\AppException;
use App\Models\Course\Course;
use App\Models\Course\CourseGroup;
use App\Models\Course\CourseGroupBlock;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class CourseService
{
    private array $filterModel = [

    ];

    public function __construct(private AssetService $assetService, private PackageService $packageService) {}

    /**
     * Course
     */

    public function getCourses(array $filter = [], array $with = [])
    {

        return filter_query_with_model(Course::latest(), $this->filterModel, $filter)->get();
    }

    public function getCourseWithPage(array $filter = [], array $with = [], int $size = 20)
    {

        return filter_query_with_model(Course::with("language")->latest(), $this->filterModel, $filter)->paginate($size);
    }

    public function getCourseById(int $id, $short = true)
    {
        if ($short) {
            return Course::find($id);
        }
        return Course::with([
            "groups",
            "detail",
            "packages",
            "groups",
        ])
            ->find($id);
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

    /**
     * Detail
     */

    public function createOrUpdateDetail(Course $course, array $data)
    {
        array_key_exists("price", $data)
            && $data["price_string"] = number_format($data["price"]);
        return $course->detail()->updateOrCreate([], $data);
    }

    public function attachPackagesToCourse(Course $course, array $data)
    {
        return $course->packagePivots()->createMany($data);
    }

    /**
     * Course Group
     */

    public function getCourseDetail(Course $course)
    {
        return $course->detail()->first();
    }

    public function getCourseGroups(Course $course)
    {
        return $course->groups()->get();
    }

    public function getCourseGroupById(Course $course, int $id)
    {
        return $course->groups()->where("id", $id)->first();
    }

    public function createCourseGroup(Course $course, array $data)
    {
        return $course->groups()->create($data);
    }

    public function updateCourseGroup(CourseGroup &$group, array $data)
    {
        return $group->update($data);
    }

    public function deleteCourseGroup(CourseGroup &$group)
    {
        return $group->delete();
    }

    public function shiftCourseGroup(Course $course, CourseGroup $group, int $newPosition)
    {
        $count = $course->groups()->count();
        if ($newPosition > $count) {
            throw new AppException("New order is exceeding the current number of groups!");
        }
        return DB::transaction(function () use ($course, $group, $newPosition) {
            return $course->groups()->where("order", ">=", $newPosition)->increment("order");
            $this->updateCourseGroup($group, [ "order" => $newPosition ]);
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

    public function getGroupBlockById(CourseGroup $group, int $id)
    {
        return $group->blocks()->where("id", $id)->first();
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
            $data["sort"] = $sort->word->word;
        }

        return $block->update($data);
    }

    public function deleteGroupBlock(CourseGroupBlock $block)
    {
        return $block->delete();
    }

    public function shiftGroupBlock(CourseGroup $group, CourseGroupBlock $block, int $newPosition)
    {
        $count = $group->blocks()->count();
        if ($newPosition > $count) {
            throw new AppException("New order is exceeding the current number of blocks!");
        }
        return DB::transaction(function () use ($group, $block, $newPosition) {
            return $group->blocks()->where("order", ">=", $newPosition)->increment("order");
            $this->updateGroupBlock($block, [ "order" => $newPosition ]);
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
}
