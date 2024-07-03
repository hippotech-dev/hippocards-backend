<?php

namespace App\Http\Services;

use App\Models\Utility\Language;
use App\Models\Utility\MainCategory;

class CategoryService
{
    protected function getFilterModel($filters)
    {
        return [
            "name" => [ "where", "name" ],
            "type" => [ "where", "object_type" ]
        ];
    }

    public function getCategories(array $filters = [], array $with = [])
    {
        return filter_query_with_model(MainCategory::query(), $this->getFilterModel($filters), $filters)->with($with)->get();
    }
}
