<?php

namespace App\Http\Services;

use App\Models\Utility\Language;

class LanguageService
{
    protected function getFilterModel($filters)
    {
        return [
            "name" => [ "where", "name" ]
        ];
    }

    public function getLanguageById(int $id)
    {
        return Language::find($id);
    }

    public function getLanguages(array $filters = [], array $with = [])
    {
        return filter_query_with_model(Language::query(), $this->getFilterModel($filters), $filters)->with($with)->get();
    }
}
