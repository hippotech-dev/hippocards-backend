<?php

namespace App\Http\Services;

use App\Enums\ESentenceType;
use App\Enums\EUserActivityType;
use App\Models\Package\Baseklass;
use App\Models\Package\Sort;
use App\Models\Package\Word\Word;
use App\Models\Package\Word\WordExample;
use App\Models\User\User;
use App\Models\Utility\Sentence;
use App\Models\Utility\UserActivity;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PackageService
{
    public function __construct(private UserActivityService $userActivityService)
    {
    }

    public function getPackages(array $filter)
    {
        $filterModel = [
            "id_in" => [ "whereIn", "id" ]
        ];

        return filter_query_with_model(Baseklass::query(), $filterModel, $filter)->get();
    }

    public function searchPackages(array $filter)
    {
        $filterModel = [
            "name_like" => [ "whereLike", "name" ],
            "language_id" => [ "where", "language_id" ]
        ];

        return filter_query_with_model(Baseklass::query(), $filterModel, $filter)->paginate($_GET["limit"] ?? null)->withQueryString();
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
            "translation",
            "pronunciation",
            "wordKeyword" => function ($query) use ($sort) {
                $query->where("baseklass_id", $sort->baseklass_id)
                    ->where("language_id", $sort->language_id);
            },
            "wordKeyword.keyword",
            "wordImages" => function ($query) use ($sort) {
                $query->where("baseklass_id", $sort->baseklass_id)
                    ->where("language_id", $sort->language_id);
            },
            "wordImages.image",
            "pos",
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
            array_merge($with, [
                "wordImages" => function ($query) use ($sort) {
                    $query->where("baseklass_id", $sort->baseklass_id)
                        ->where("language_id", $sort->language_id);
                },
            ]);
            array_push($with, "wordImages.image");
        }

        if (in_array("translation", $include)) {
            array_push($with, "translation");
        }

        if (in_array("pronunciation", $include)) {
            array_push($with, "pronunciation");
        }

        if (in_array("pos", $include)) {
            array_push($with, "pos");
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

        if (in_array("keywords", $include)) {
            array_merge($with, [
                "word.wordKeyword" => function ($query) use ($sort) {
                    $query->where("baseklass_id", $sort->baseklass_id)
                        ->where("language_id", $sort->language_id);
                },
            ]);
            array_push($with, "word.wordKeyword.keyword");
        }
        $word = Word::with($with)
            ->find($sort->word_id);

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

    public function searchWords(array $filters)
    {
        $filterModel = [
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
            "id_in" => [ "whereIn",  ]
        ];

        return filter_query_with_model(Sort::with("word.translation", "package")->active(), $filterModel, $filters)->orderBy("id", "desc")->simplePaginate(page_size());
    }

    public function getMemorizedWords(User $user)
    {
        $activitiesWithSorts = $this->userActivityService->getUserActivitiesByTypeWithPage($user, EUserActivityType::WORD, [ "object.word.translation", "object.package" ]);

        $activitiesWithSortsCollection = $activitiesWithSorts->getCollection();

        $activitiesWithSorts->setCollection($activitiesWithSortsCollection->pluck("object"));

        return $activitiesWithSorts;
    }

    public function convertOldSentencesToNewSentence()
    {
        DB::transaction(function () {
            $sorts = Sort::with("word.exampleSentences.example")->limit(41000)->offset(40000)->get();

            foreach ($sorts as $sort) {
                if (is_null($sort->word) || count($sort->word->exampleSentences) === 0) {
                    continue;
                }

                $examplesSentences = $sort->word->exampleSentences;

                $value = $examplesSentences->where("type", 1)->where("baseklass_id", $sort->baseklass_id)->first();

                if (is_null($value) || is_null($value->example) || $value->example->name === "") {
                    continue;
                }

                $translation = $examplesSentences->where("type", 2)->where("baseklass_id", $sort->baseklass_id)->first();
                $latin = $examplesSentences->where("type", 3)->where("baseklass_id", $sort->baseklass_id)->first();

                Sentence::create([
                    "object_id" => $sort->word_id,
                    "object_type" => Word::class,
                    "language_id" => $sort->language_id,
                    "type" => ESentenceType::DEFINITION,
                    "value" => $value->example->name ?? "",
                    "translation" => $translation->example->name ?? "",
                    "latin" => $latin->example->name ?? "",
                ]);

                $value2 = $examplesSentences->where("type", 4)->where("baseklass_id", $sort->baseklass_id)->first();

                if (is_null($value2) || is_null($value2->example) || $value2->example->name === "") {
                    continue;
                }

                $translation2 = $examplesSentences->where("type", 5)->where("baseklass_id", $sort->baseklass_id)->first();
                $latin2 = $examplesSentences->where("type", 6)->where("baseklass_id", $sort->baseklass_id)->first();

                Sentence::create([
                    "object_id" => $sort->word_id,
                    "object_type" => Word::class,
                    "language_id" => $sort->language_id,
                    "value" => $value2->example->name ?? "",
                    "translation" => $translation2->example->name ?? "",
                    "latin" => $latin2->example->name ?? "",
                    "order" => 1,
                    "type" => ESentenceType::DEFINITION,
                ]);
            }
        });
    }

    public function convertOldImaginationToNewSentence()
    {
        DB::transaction(function () {
            $sorts = Sort::with("word.wordImaginations.imagination")->limit(41000)->offset(40000)->get();

            foreach ($sorts as $sort) {
                if (is_null($sort->word) || count($sort->word->wordImaginations) === 0) {
                    continue;
                }

                $wordImaginations = $sort->word->wordImaginations;

                $value = $wordImaginations->where("baseklass_id", $sort->baseklass_id)->first();

                if (is_null($value) || is_null($value->imagination) || $value->imagination->name === "") {
                    continue;
                }

                Sentence::create([
                    "object_id" => $sort->word_id,
                    "object_type" => Word::class,
                    "language_id" => $sort->language_id,
                    "type" => ESentenceType::IMAGINATION,
                    "value" => $value->imagination->name ?? "",
                ]);
            }
        });
    }
}
