<?php

namespace App\Http\Services;

use App\Models\Package\Baseklass;
use App\Models\Package\Sort;
use Illuminate\Support\Collection;

class WordService
{
    public function searchSorts(array $filter, array $with = [])
    {
        $filterModel = [
            "package" => [ "where", "baseklass_id" ],
            "language" => [ "where", "language_id" ],
            "word_id" => [ "where", "word_id" ],
        ];

        return filter_query_with_model(Sort::with($with), $filterModel, $filter)->paginate($_GET["limit"] ?? null)->withQueryString();
    }
}
