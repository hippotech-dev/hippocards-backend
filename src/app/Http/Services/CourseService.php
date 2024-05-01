<?php

namespace App\Http\Services;

use App\Enums\ECourseBlockType;
use App\Enums\ECourseBlockVideoType;
use App\Enums\ECourseExamType;
use App\Enums\EStatus;
use App\Exceptions\AppException;
use App\Exceptions\UnauthorizedException;
use App\Http\Resources\System\Academy\BlockKanbanCardResource;
use App\Http\Resources\System\Academy\CourseGroupResource;
use App\Http\Resources\System\Academy\WordResource;
use App\Jobs\CourseCertificateJob;
use App\Models\Course\Course;
use App\Models\Course\CourseBlockDetail;
use App\Models\Course\CourseBlockResponse;
use App\Models\Course\CourseBlockVideo;
use App\Models\Course\CourseBlockVideoTimestamp;
use App\Models\Course\CourseCertificate;
use App\Models\Course\CourseCompletion;
use App\Models\Course\CourseExamInstance;
use App\Models\Course\CourseExamResult;
use App\Models\Course\CourseGroup;
use App\Models\Course\CourseGroupBlock;
use App\Models\Course\UserCourse;
use App\Models\User\User;
use App\Models\Utility\Asset;
use Exception;
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
        return Course::with($with)->withCount("blocks")->find($id);
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

    public function getCourseLearnData(Course $course)
    {
        $blocks = $this->getCourseBlocks($course);
        $columns = $this->getCourseGroups($course);

        foreach ($columns as $column) {
            $column->cardIds = $blocks->where("v3_course_group_id", $column->id)->sortBy("order")->pluck("id");
        }

        return [
            "blocks" => BlockKanbanCardResource::collection($blocks),
            "groups" => CourseGroupResource::collection($columns),
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

    public function getCourseGroups(Course $course, array $with = [])
    {
        return $course->groups()->with($with)->orderBy("order", "asc")->get();
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

    public function getGroupBlockById(CourseGroup $group, int $id, array $with = ["wordSort", "videos.asset", "detail"])
    {
        return $group->blocks()->with($with)->where("id", $id)->first();
    }

    public function getCourseBlockById(Course $course, int $id, array $with = ["wordSort", "videos.asset", "detail"])
    {
        return $course->blocks()->with($with)->where("id", $id)->first();
    }

    public function getCourseBlockByIdLoaded(Course $course, int $id)
    {
        $block = $this->getCourseBlockById($course, $id, [ "videos.asset", "videos.videoTimestamps", "detail" ]);

        if ($block->type === ECourseBlockType::EXAM) {
            return $block;
        }

        $block->setRelation(
            "wordSort",
            $this->packageService->getSortByIdLoaded($block->sort_id)
        );

        return $block;
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
            $data["word_id"] = $sort->word_id;
            $data["package_id"] = $sort->baseklass_id;
        }

        if (array_key_exists("type", $data) && $data["type"] === ECourseBlockType::EXAM->value) {
            $data["name"] = "Exam";
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
            $data["word_id"] = $sort->word_id;
            $data["package_id"] = $sort->baseklass_id;
        }

        if (array_key_exists("metadata", $data)) {
            $metadata = $block->metadata ?? [];
            $data["metadata"] = array_merge(
                $metadata,
                $data["metadata"]
            );
        }

        if (array_key_exists("type", $data) && $data["type"] === ECourseBlockType::EXAM->value) {
            $data["name"] = "Exam";
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

    public function createManyGroupBlocks(CourseGroup $group, Collection $packageSorts, array &$config = [])
    {
        $blockData = [];

        $sortIndex = 0;
        $examIndex = 0;
        foreach ($packageSorts as &$sort) {
            if (is_null($sort->word)) {
                continue;
            }

            array_push($blockData, [
                "sort_id" => $sort->id,
                "name" => $sort->word->word,
                "type" => ECourseBlockType::LESSON,
                "order" => $sortIndex,
                "v3_course_id" => $group->v3_course_id,
                "word_id" => $sort->word_id,
                "package_id" => $sort->baseklass_id,
            ]);

            $sortIndex++;
            $examIndex++;

            if (array_key_exists("exam", $config) && $config["exam"] === "exam" && $examIndex === 10) {
                array_push($blockData, [
                    "sort_id" => null,
                    "name" => "Exam",
                    "type" => ECourseBlockType::EXAM,
                    "order" => $sortIndex,
                    "v3_course_id" => $group->v3_course_id
                ]);
                $sortIndex++;
                $examIndex = 0;
            }
        }

        $group->blocks()->createMany($blockData);

        return $sortIndex;
    }

    public function createBlocksMultipleGroup(Collection $groups, Collection $packageSorts, array &$config = [])
    {
        $totalGroupCount = $groups->count();
        $totalPackageCount = $packageSorts->count();
        $totalSortPerGroup = $totalPackageCount / $totalGroupCount;

        $groupIndex = 0;
        $totalBlocks = 0;
        foreach ($groups as &$group) {
            $slicedPackageSorts = $packageSorts->slice($groupIndex * $totalSortPerGroup, $totalSortPerGroup);
            $totalBlocks += $this->createManyGroupBlocks($group, $slicedPackageSorts, $config);
            $groupIndex++;
        }

        return $totalBlocks;
    }

    public function createUpdateBlockDetail(CourseGroupBlock $block, array $data)
    {
        [ "sentences" => $sentences, "keywords" => $keywords ] = $data;

        return $block->detail()->updateOrCreate(
            [
                "v3_course_id" => $block->v3_course_id,
                "v3_course_block_id" => $block->id
            ],
            [
                "sentences" => $sentences,
                "keywords" => $keywords
            ]
        );
    }

    public function submitSentenceKeywordResponse(CourseGroupBlock $block, CourseCompletion $completion, User $user, array $data)
    {
        $type = array_key_exists("sentences", $data) ? "sentence" : "keyword";
        $sentences = $data["sentences"] ?? [];
        $keyword = $data["keyword"] ?? [];

        $responses = $this->getSentenceKeywordResponse($block, $completion, $type);
        if (count($responses) > 10) {
            throw new AppException("Нийт 10 аас дээш хариулт илгээх боломжгүй!");
        }

        switch ($type) {
            case "sentence":
                return $block->sentenceKeywordResponses()->createMany(array_map(function ($item) use ($type, $block, $completion, $user) {
                    return array_merge($item, [
                        "type" => $type,
                        "v3_course_id" => $block->v3_course_id,
                        "v3_course_completion_id" => $completion->id,
                        "v3_course_block_id" => $block->id,
                        "user_id" => $user->id
                    ]);
                }, $sentences));
                return;
            case "keyword":
                $check = $responses->filter(function ($item) use ($keyword) {
                    return $keyword === $item->keyword;
                });
                if (count($check) > 0) {
                    return null;
                }
                return $block->sentenceKeywordResponses()->create([
                    "keyword" => $keyword,
                    "v3_course_id" => $block->v3_course_id,
                    "v3_course_completion_id" => $completion->id,
                    "v3_course_block_id" => $block->id,
                    "user_id" => $user->id,
                    "type" => $type,
                ]);
            default:
                return null;
        }
    }

    public function getSentenceKeywordResponse(CourseGroupBlock $block, CourseCompletion $completion, $type)
    {
        return $block
            ->sentenceKeywordResponses()
            ->where("v3_course_completion_id", $completion->id)
            ->where("type", $type)
            ->get();
    }

    /**
     * Automated creation
     */

    public function createGroupsWithBlocks(Course $course, array $config = [])
    {
        $packages = $this->getCoursePackages($course);

        if (count($packages) === 0) {
            throw new AppException("Packages are not added!");
        }

        $packageSorts = $this->packageService->getPackagesSorts($packages);

        $courseGroups = $this->createManyCourseGroups($course);

        $totalBlocks = $this->createBlocksMultipleGroup($courseGroups, $packageSorts, $config);

        $this->createOrUpdateDetail($course, [
            "total_blocks" => $totalBlocks
        ]);
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

    /**
     * Exam
     */

    public function generateCourseExamQuestions(Course $course, int $total)
    {
        $blocks = $course->blocks()
            ->with([ "wordSort", "wordSort.word.translation" ])
            ->where("type", ECourseBlockType::LESSON)
            ->inRandomOrder()
            ->limit($total)
            ->get();
        $generatedData = array();
        $totalMongolianWords = floor(($total * 75) / 100);
        $isShort = count($blocks) <= 3;
        foreach ($blocks as $block) {
            $randomElements = $isShort ? $blocks->random() : $blocks->where("id", "!=", $block->id)->random(3)->push($block)->shuffle();
            $answers = $randomElements->pluck("wordSort.word");
            $word = $block->wordSort->word ?? null;
            if (is_null($word)) {
                continue;
            }
            $isWord = $totalMongolianWords <= 0 ? false : rand(0, 1);
            $isWord && $totalMongolianWords > 0 && $totalMongolianWords--;
            array_push(
                $generatedData,
                [
                    "id" => $block->id,
                    "question" => $isWord ? $word->translation->name ?? "NONE" : $word->word,
                    "correct_answer" => $word->id,
                    "answers" => $answers->map(fn ($item) => ["id" => $item->id ?? 0, "answer" => $isWord ? $item->word ?? "NONE" : $item->translation->name ?? "NONE"]),
                    "is_word" => $isWord,
                ]
            );
        }

        return $generatedData;
    }

    public function getCourseExamInstance(UserCourse $userCourse, ECourseBlockType $type)
    {
        return CourseExamInstance::where("v3_user_course_id", $userCourse->id)
            ->where("type", $type)
            ->first();
    }

    public function getCourseExamResult(Course $course, User $user, ECourseBlockType $type)
    {
        return CourseExamResult::where("v3_course_id", $course->id)
            ->where("user_id", $user->id)
            ->where("type", $type)
            ->first();
    }

    public function getCourseExamInstaceResult(CourseExamInstance $examInstance)
    {
        $currentDate = date("Y-m-d H:i:s");
        $examResult = CourseExamResult::where("v3_course_exam_instance_id", $examInstance->id)->first();

        if (!is_null($examResult) && $examResult->status === EStatus::PENDING && $examInstance->end_time < $currentDate) {
            $this->setCourseExamResultStatus($examResult, EStatus::FAILURE);
        }

        return $examResult;
    }

    public function submitCourseExamAnswer(CourseExamInstance $instance, array $answer)
    {
        $answers = $instance->answers ?? [];
        $questionId = $answer["question_id"] ?? 0;
        $answerId = $answer["answer_id"] ?? 0;
        $answers[$questionId] = $answerId;
        $index = $answer["index"] + 1;
        return $instance->update([
            "answers" => $answers,
            "current_question_number" => $index >= $instance->total_questions
                ? $instance->total_questions - 1
                : ($index < $instance->current_question_number
                    ? $instance->current_question_number
                    : $index)
        ]);
    }

    public function finishCourseExam(CourseExamInstance $instance)
    {
        $result = $this->getCourseExamInstaceResult($instance);
        $totalReceivedPoints = 0;
        $questions = $instance->questions ?? [];
        $answers = $instance->answers ?? [];
        foreach ($questions as $question) {
            if (array_key_exists("correct_answer", $question)
                && array_key_exists($question["id"], $answers)
                && $question["correct_answer"] === $answers[$question["id"]]) {
                $totalReceivedPoints++;
            }
        }

        $this->updateCourseExamResultInstance(
            $result,
            [
                "status" => EStatus::SUCCESS,
                "total_received_points" => $totalReceivedPoints
            ]
        );

        return [
            "total_points" => $result->total_points,
            "total_received_points" => $totalReceivedPoints
        ];
    }

    public function createCourseExamInstance(UserCourse $userCourse, Course $course, User $user, ECourseBlockType $type, int $totalQuestions)
    {
        $questions = $this->generateCourseExamQuestions($course, $totalQuestions);
        $totalQuestions = count($questions);
        $examInstance = CourseExamInstance::create([
            "v3_course_id" => $course->id,
            "user_id" => $user->id,
            "questions" => $questions,
            "v3_user_course_id" => $userCourse->id,
            "total_questions" => $totalQuestions,
            "start_time" => date("Y-m-d H:i:s"),
            "end_time" => date("Y-m-d H:i:s", strtotime("+$totalQuestions minutes")),
            "type" => $type
        ]);

        $this->createCourseExamResultInstance($examInstance);

        return $examInstance;
    }

    public function createCourseExamResultInstance(CourseExamInstance $instance)
    {
        return CourseExamResult::create([
            "v3_course_id" => $instance->v3_course_id,
            "v3_course_group_id" => $instance->v3_course_group_id,
            "v3_course_block_id" => $instance->v3_course_block_id,
            "user_id" => $instance->user_id,
            "type" => $instance->type,
            "total_points" => $instance->total_questions,
            "v3_course_exam_instance_id" => $instance->id,
        ]);
    }

    public function updateCourseExamResultInstance(CourseExamResult $result, $data)
    {
        return $result->update($data);
    }

    public function setCourseExamResultStatus(CourseExamResult $result, EStatus $status)
    {
        $result->status = $status;
        return $result->save();
    }

    public function getCourseExamQuestions(CourseGroupBlock $examBlock)
    {
        $previousBlocks = CourseGroupBlock::with("wordSort.word", "wordSort.word.translation")
            ->where("v3_course_group_id", $examBlock->v3_course_group_id)
            ->where("order", "<", $examBlock->order)
            ->orderBy("order", "asc")
            ->limit(10)
            ->get();

        $generatedData = array();

        foreach ($previousBlocks as $block) {
            $randomElements = count($previousBlocks) <= 3
                ? $previousBlocks->where("id", "!=", $block->id)
                : $previousBlocks->where("id", "!=", $block->id)->random(3)->push($block)->shuffle();
            $answers = $randomElements->pluck("wordSort.word");
            $word = $block->wordSort->word ?? null;
            if (is_null($word)) {
                continue;
            }
            $isWord = rand(0, 1);
            array_push(
                $generatedData,
                [
                    "id" => $block->id,
                    "question" => $isWord ? $word->translation->name ?? "NONE" : $word->word,
                    "answers" => $answers->map(fn ($item) => ["id" => $item->id ?? 0, "answer" => $isWord ? $item->word ?? "NONE" : $item->translation->name ?? "NONE",]),
                    "is_word" => $isWord,
                ]
            );
        }

        return $generatedData;
    }

    public function submitExamAnswers(CourseGroupBlock $examBlock, array $answers)
    {
        $answerConvertedArr = array_column($answers, "answer_id", "question_id");

        $blocks = CourseGroupBlock::with("wordSort.word.translation")->whereIn("id", array_keys($answerConvertedArr))->get();

        $generatedData = array();

        foreach ($blocks as $block) {
            if (!is_null($block->wordSort) && !is_null($block->wordSort->word) && array_key_exists($block->id, $answerConvertedArr)) {
                $answer = $answerConvertedArr[$block->id];
                $word = $block->wordSort->word;
                array_push($generatedData, [
                    "is_correct" => $answer === $word->id,
                    "word" => new WordResource($block->wordSort->word),
                ]);
            }
        }

        return $generatedData;
    }

    /**
     * Final exam
     */

    public function getCourseFinalExamQuestions(Course $course, User $user)
    {
        $userCourse = $this->getActiveUserCourse($course, $user);
        if (is_null($userCourse)) {
            throw new Exception("User is not enrolled to the course!");
        }
        $examInstance = $this->getCourseExamInstance($userCourse, ECourseBlockType::FINAL_EXAM);

        if (is_null($examInstance)) {
            $examInstance = $this->createCourseExamInstance($userCourse, $course, $user, ECourseBlockType::FINAL_EXAM, 100);
        }

        return $examInstance;
    }

    public function submitCourseFinalExamQuestions(Course $course, User $user, CourseExamInstance $examInstance, array $answer)
    {
        $currentDate = date("Y-m-d H:i:s");
        if ($examInstance->user_id !== $user->id && $examInstance->v3_course_id !== $course->id) {
            throw new AppException("Invalid request!");
        }
        if (is_null($examInstance) || ($examInstance->status === EStatus::PENDING && $examInstance->end_time < $currentDate)) {
            throw new AppException("Invalid request!");
        }

        $this->submitCourseExamAnswer($examInstance, $answer);
    }

    public function finishCourseFinalExamQuestions(Course $course, User $user, CourseExamInstance $examInstance)
    {
        $currentDate = date("Y-m-d H:i:s");
        if ($examInstance->user_id !== $user->id && $examInstance->v3_course_id !== $course->id) {
            throw new AppException("Invalid request!");
        }

        if (is_null($examInstance) || ($examInstance->status === EStatus::PENDING && $examInstance->end_time < $currentDate)) {
            throw new AppException("Invalid request!");
        }

        [ "total_points" => $totalPoints, "total_received_points" => $totalReceivedPoints ] = $this->finishCourseExam($examInstance);

        if ($totalReceivedPoints >= floor(($totalPoints * 90) / 100)) {
            dispatch(new CourseCertificateJob($course, $user));
        }
    }

    public function getCourseFinalExamResult(Course $course, User $user)
    {
        $userCourse = $this->getActiveUserCourse($course, $user);
        if (is_null($userCourse)) {
            throw new Exception("User is not enrolled to the course!");
        }
        $examInstance = $this->getCourseExamInstance($userCourse, ECourseBlockType::FINAL_EXAM);

        return is_null($examInstance) ? null : $this->getCourseExamInstaceResult($examInstance);
    }

    public function getCourseFinalExamAnswers(Course $course, User $user)
    {
        $userCourse = $this->getActiveUserCourse($course, $user);
        $examInstance = $this->getCourseExamInstance($userCourse, ECourseBlockType::FINAL_EXAM);

        if (is_null($examInstance)) {
            throw new AppException("Final exam is not submitted!");
        }

        $answers = $examInstance->answers;
        $questions = $examInstance->questions;
        foreach ($questions as &$question) {
            if (array_key_exists("correct_answer", $question)
                && array_key_exists($question["id"], $answers)) {
                $question["user_answer"] = $answers[$question["id"]];
            }
        }

        return $questions;
    }

    /**
     * User course
     */

    public function getUserActiveCourses(User $user)
    {
        $userCourses =  UserCourse::with("course.detail")
            ->where("user_id", $user->id)
            ->where("end", ">=", date("Y-m-d"))
            ->get();

        return $userCourses->pluck("course");
    }

    public function getActiveUserCourse(Course $course, User $user)
    {
        return UserCourse::where("v3_course_id", $course->id)
            ->where("user_id", $user->id)
            ->where("end", ">=", date("Y-m-d"))
            ->first();
    }

    /**
     * Completion
     */

    public function getUserCourseCompletion(UserCourse $userCourse, array $with = [])
    {
        $completion = CourseCompletion::with($with)->where("v3_user_course_id", $userCourse->id)->first();

        if (is_null($completion)) {
            $completion = CourseCompletion::create([
                "v3_user_course_id" => $userCourse->id,
                "v3_course_id" => $userCourse->v3_course_id,
            ]);

            $completion->setRelation("items", []);
        }

        return $completion;
    }

    public function getCourseCompletion(Course $course, User $user)
    {
        $userCourse = $this->getActiveUserCourse($course, $user);

        if (is_null($userCourse)) {
            throw new AppException("Course is not available!");
        }

        $userCompletion = $this->getUserCourseCompletion($userCourse, [ "items" ]);

        if (is_null($userCompletion)) {
            throw new AppException("Course is not available!");
        }

        return $userCompletion;
    }

    public function submitCourseCompletionProgress(Course $course, User $user, array $data)
    {
        $userCourse = $this->getActiveUserCourse($course, $user);
        $block = $this->getCourseBlockById($course, $data["block_id"]);

        if (is_null($userCourse) || is_null($block)) {
            throw new AppException("Course is not avaialble!");
        }

        $userCompletion = $this->getUserCourseCompletion($userCourse);

        if (is_null($userCompletion)) {
            throw new AppException("Course is not available!");
        }

        $blockCompletionItem = $userCompletion->items()->where("v3_course_block_id", $block->id)->first();

        if (is_null($blockCompletionItem)) {
            $blockCompletionItem = $userCompletion->items()->create([
                "v3_user_course_id" => $userCourse->id,
                "v3_course_group_id" => $block->v3_course_group_id,
                "v3_course_block_id" => $block->id,
                "status" => $data["status"]
            ]);
            $userCompletion->update([
                "progress" => $userCompletion->progress + 1
            ]);
        } else {
            $blockCompletionItem->status = $data["status"];
            $blockCompletionItem->save();
        }



        return $blockCompletionItem;
    }

    public function setCurrentBlockCompletion(Course $course, User $user, CourseGroupBlock $block)
    {
        $userCourse = $this->getActiveUserCourse($course, $user);

        if (is_null($userCourse)) {
            throw new AppException("Course is not avaialble!");
        }

        $userCompletion = $this->getUserCourseCompletion($userCourse);

        if (is_null($userCompletion)) {
            throw new AppException("Course is not available!");
        }

        $userCompletion->update([
            "current_group_id" => $block->v3_course_group_id,
            "current_block_id" => $block->id
        ]);
    }

    /**
     * Course certificate
     */

    public function createCourseCertificate(Course $course, User $user, Asset $asset)
    {
        return CourseCertificate::create([
            "issue_date" => date("Y-m-d"),
            "user_id" => $user->id,
            "v3_asset_id" => $asset->id,
            "v3_course_id" => $course->id
        ]);
    }

    public function getUserCertificates(User $user)
    {
        return $user
            ->courseCertificates()
            ->with([ "course", "asset" ])
            ->get();
    }
}
