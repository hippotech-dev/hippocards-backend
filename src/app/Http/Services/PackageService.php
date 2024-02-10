<?php

namespace App\Http\Services;

use App\Models\Package\Baseklass;
use App\Models\Package\Sort;
use Illuminate\Support\Collection;

class PackageService
{
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

    public function getSortById(int $id)
    {
        return Sort::with("word")->find($id);
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
}
