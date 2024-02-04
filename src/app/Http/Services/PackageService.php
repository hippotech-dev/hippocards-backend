<?php

namespace App\Http\Services;

use App\Models\Package\Baseklass;
use App\Models\Package\Sort;

class PackageService
{
    public function getPackages(array $filter)
    {
        $filterModel = [
            "id_in" => [ "whereIn", "id" ]
        ];

        return filter_query_with_model(Baseklass::query(), $filterModel, $filter)->get();
    }

    public function getSortById(int $id)
    {
        return Sort::with("word")->find($id);
    }
}
