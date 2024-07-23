<?php

namespace App\Http\Services;

use App\Enums\ELocale;
use App\Enums\EPackageType;
use App\Enums\EUserActivityAction;
use App\Enums\EUserActivityType;
use App\Exceptions\AppException;
use App\Jobs\GenerateWordAudioJob;
use App\Models\Package\Baseklass;
use App\Models\Package\ExamResult;
use App\Models\Package\Sort;
use App\Models\Package\Word\Word;
use App\Models\Package\Word\WordSynonym;
use App\Models\User\User;
use App\Util\AudioConfig;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class WordSortService
{
    public function __construct(
        private UserActivityService $userActivityService,
        private PackageService $packageService,
        private AssetService $assetService,
        private AudioService $audioService,
        private LanguageService $languageService
    ) {
    }

    protected function getFilterModel(array $filters)
    {
        return [
            "search" => [
                [ "whereHas" ],
                [
                    [
                        "name" => "word",
                        "value" => fn ($query) => $query->whereLike("word", $filters["search"])
                    ]
                ],
            ],
            "language" => [ "where", "language_id" ],
            "package" => [ "where", "baseklass_id" ],
            "id_in" => [ "whereIn",  ]
        ];
    }

    protected function createWordSortActivity(User $user, mixed $object, EUserActivityAction $action)
    {
        return $this->userActivityService->createObjectActivity($user, $object, EUserActivityType::SYSTEM_WORD, $action);
    }

    public function getSortById(int $id, $with = [ "word" ])
    {
        return Sort::with($with)->find($id);
    }

    public function getSortByIdLoaded(int $id)
    {
        $sort = $this->getSortById($id);

        if (is_null($sort)) {
            return null;
        }

        $word = Word::with([
            "mainDetail",
            "images",
            "synonyms",
            "definitionSentences",
            "imaginationSentences"
        ])
            ->find($sort->word_id);

        $sort->setRelation("word", $word);

        return $sort;
    }

    public function getSortByIdInclude(int $id, array $include = [])
    {
        $sort = $this->getSortById($id);

        if (is_null($sort)) {
            return null;
        }

        $with = [];

        if (in_array("images", $include)) {
            array_push($with, "images");
        }

        if (in_array("mainDetail", $include)) {
            array_push($with, "mainDetail");
        }

        if (in_array("synonyms", $include)) {
            array_push($with, "synonyms");
        }

        if (in_array("imaginations", $include)) {
            array_push($with, "imaginationSentences");
        }

        if (in_array("examples", $include)) {
            array_push($with, "definitionSentences");
        }

        $word = Word::with($with)->find($sort->word_id);

        $sort->setRelation("word", $word);

        return $sort;
    }

    public function getPackagesSorts(Collection|array $packages)
    {
        if ($packages instanceof Collection) {
            $ids = $packages->pluck("id")->toArray();
        } else {
            $ids = $packages;
        }
        return Sort::with("word")->whereIn("baseklass_id", $ids)->get();
    }

    public function getSort(array $filters, array $with = [], $orderBy = [ "field" => "id", "value" => "desc" ])
    {
        $orderBy = get_sort_info($orderBy);
        return filter_query_with_model(Sort::with($with), $this->getFilterModel($filters), $filters)->orderBy($orderBy["field"], $orderBy["value"])->first();
    }

    public function getWordById(int $id, array $with = [])
    {
        return Word::with($with)->find($id);
    }

    public function getSortsWithSimplePage(array $filters, array $with = [], $orderBy = [ "field" => "id", "value" => "desc" ])
    {
        $orderBy = get_sort_info($orderBy);
        return filter_query_with_model(Sort::with($with), $this->getFilterModel($filters), $filters)->orderBy($orderBy["field"], $orderBy["value"])->simplePaginate(page_size())->withQueryString();
    }

    public function getSortsWithCursor(array $filters, array $with = [], $orderBy = [ "field" => "id", "value" => "desc" ])
    {
        $orderBy = get_sort_info($orderBy);
        return filter_query_with_model(Sort::with($with), $this->getFilterModel($filters), $filters)->orderBy($orderBy["field"], $orderBy["value"])->cursorPaginate(page_size())->withQueryString();
    }

    public function getPackageSorts(Baseklass $package, array $filters, array $with = [], $orderBy = [ "field" => "id", "value" => "desc" ])
    {
        $orderBy = get_sort_info($orderBy);
        return filter_query_with_model(Sort::with($with)->where("baseklass_id", $package->id), $this->getFilterModel($filters), $filters)->orderBy($orderBy["field"], $orderBy["value"])->get();
    }

    public function getSortsWithPage(array $filters, array $with = [], $orderBy = [ "field" => "id", "value" => "desc" ])
    {
        $orderBy = get_sort_info($orderBy);
        return filter_query_with_model(Sort::with($with), $this->getFilterModel($filters), $filters)->orderBy($orderBy["field"], $orderBy["value"])->paginate(page_size())->withQueryString();
    }

    public function createSort(User $user, Baseklass $package, array $data)
    {
        DB::transaction(function () use ($user, $package, $data) {
            $wordCheck = $this->getSort([ "search" => $data["word"], "package" => $package->id ]);

            if (!is_null($wordCheck)) {
                throw new AppException("Word already exists in the package!");
            }

            $word = Word::create($data);

            $sort =  $package->wordSorts()->create([
                "word_id" => $word->id,
                "sort_word" => $data["sort_word"],
                "language_id" => $package->language_id,
                "user_id" => $user->id,
            ]);

            $this->createWordAudio($sort);
            $this->createWordSortActivity($user, $sort, EUserActivityAction::CREATE);
            $this->packageService->resetPackageWordCount($package);

            return $sort;
        });
    }

    public function updateSort(User $user, Sort $sort, array $data)
    {
        DB::transaction(function () use ($user, $sort, $data) {

            $sort->update($data);

            $this->createWordSortActivity($user, $sort, EUserActivityAction::UPDATE);

            return $sort;
        });
    }

    public function deleteSort(User $user, Sort $sort)
    {
        DB::transaction(function () use ($user, $sort) {
            $package = $sort->package()->first();
            $this->createWordSortActivity($user, $sort, EUserActivityAction::DELETE);
            $this->packageService->resetPackageWordCount($package);

            return $sort->delete();
        });
    }

    public function updateWord(User $user, Word $word, array $data)
    {
        DB::transaction(function () use ($user, $word, $data) {

            $word->update($data);

            $this->createWordSortActivity($user, $word, EUserActivityAction::UPDATE);

            return $word;
        });
    }

    public function deleteWord(User $user, Word $word)
    {
        DB::transaction(function () use ($user, $word) {

            $this->createWordSortActivity($user, $word, EUserActivityAction::DELETE);

            return $word->delete();
        });
    }

    public function createUpdateWordDetail(Word $word, array $data)
    {
        return $word->mainDetail()->updateOrCreate([], $data);
    }

    public function createWordImage(Word $word, array $data)
    {
        $asset = $this->assetService->getAssetById($data["v3_asset_id"]);

        return $word->images()->updateOrCreate(
            [
                "type" => $data["type"],
            ],
            [
                "name" => $asset->name ?? "none",
                "path" => $asset->path,
                "v3_asset_id" => $asset->id
            ]
        );
    }

    public function createWordSynonym(Word $word, array $data)
    {
        $data["language_id"] = 0;
        return $word->synonyms()->create($data);
    }

    public function updateWordSynonym(WordSynonym $wordSynonym, array $data)
    {
        return $wordSynonym->update($data);
    }

    public function deleteWordSynonym(WordSynonym $wordSynonym)
    {
        return $wordSynonym->delete();
    }

    public function createWordAudio(Sort $sort)
    {
        return GenerateWordAudioJob::dispatch($sort);
    }

    public function generateSortAudio(Sort $sort)
    {
        $word = $this->getWordById($sort->word_id);
        $language = $this->languageService->getLanguageById($sort->language_id);
        if (is_null($word)) {
            throw new AppException("Sort word is empty!");
        }
        $asset = $this->audioService->generateAudio(
            $word->word,
            new AudioConfig($language->azure ?? ELocale::ENGLISH),
            "power-vocabs/sound"
        );

        $word->update([
            "mp3" => $asset->name
        ]);
    }

    public function getRecentLearningWords(User $user, array $filters = [])
    {
        $query = ExamResult::with("word.images")
            ->whereHas("package", fn ($query) => filter_query_with_model($query, [ "language" => [ "where", "language_id" ] ], $filters))
            ->where("user_id", $user->id)
            ->orderBy("id", "desc");

        return [
            "results" => $query->cursorPaginate(page_size()),
            "total" => $query->count(),
        ];
    }
}
