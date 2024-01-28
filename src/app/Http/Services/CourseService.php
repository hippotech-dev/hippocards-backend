<?php

namespace App\Http\Services;

use App\Models\Course\Course;
use App\Models\Utility\Asset;

class CourseService
{
    private array $filterModel = [

    ];

    public function __construct(private AssetService $assetService) {}

    public function getCourses(array $filter = [], array $with = [])
    {

        return filter_query_with_model(Course::latest(), $this->filterModel, $filter)->get();
    }

    public function getCourseWithPage(array $filter = [], array $with = [], int $size = 20)
    {

        return filter_query_with_model(Course::latest(), $this->filterModel, $filter)->paginate($size);
    }

    public function getCourseById(int $id)
    {
        return Course::find($id);
    }

    public function createCourse(array $validatedData)
    {
        array_key_exists("thumbnail_asset_id", $validatedData)
            && $validatedData["thumbnail"] = $this->assetService->getAssetPath($validatedData["thumbnail_asset_id"]);

        return Course::create($validatedData);
    }

    public function updateCourse(Course $course, array $validatedData)
    {
        array_key_exists("thumbnail_asset_id", $validatedData)
            && $validatedData["thumbnail_asset_id"] !== $course->thumbnail_asset_id
            && $validatedData["thumbnail"] = $this->assetService->getAssetPath($validatedData["thumbnail_asset_id"]);

        $course->update($validatedData);

        return $course;
    }

    public function deleteCourse(Course $course)
    {
        !is_null($course->thumbnail_asset_id)
            && $this->assetService->deleteAssetById($course->thumbnail_asset_id);

        return $course->delete();
    }
}
