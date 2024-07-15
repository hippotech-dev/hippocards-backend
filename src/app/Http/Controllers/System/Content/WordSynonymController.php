<?php

namespace App\Http\Controllers\System\Content;

use App\Enums\EWordSimilarType;
use App\Http\Controllers\Controller;
use App\Http\Services\WordSortService;
use App\Models\Package\Word\WordSynonym;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class WordSynonymController extends Controller
{
    public function __construct(private WordSortService $service)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = Validator::make(
            $request->only(
                "word_id",
                "synonym",
                "translation",
                "type",
            ),
            [
                "word_id" => "required|integer|exists:word,id",
                "synonym" => "required|string|max:512",
                "translation" => "required|string|max:512",
                "type" => [
                    "required",
                    Rule::in([ EWordSimilarType::SYNONYM->value, EWordSimilarType::SIMILAR->value, EWordSimilarType::ANTONYM->value, ])
                ],
            ]
        )
            ->validate();

        $word = $this->service->getWordById($validatedData["word_id"]);

        if (is_null($word)) {
            throw new NotFoundHttpException("Word does not exist!");
        }

        $this->service->createWordSynonym($word, $validatedData);

        return response()->success();
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, WordSynonym $synonym)
    {
        $validatedData = Validator::make(
            $request->only(
                "synonym",
                "translation",
            ),
            [
                "synonym" => "sometimes|string|max:512",
                "translation" => "sometimes|string|max:512",
            ]
        )
            ->validate();

        $this->service->updateWordSynonym($synonym, $validatedData);

        return response()->success();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $word, WordSynonym $synonym)
    {
        $this->service->deleteWordSynonym($synonym);

        return response()->success();
    }
}
