<?php

namespace App\Http\Services;

use App\Models\Utility\Sentence;
use Illuminate\Support\Facades\DB;

class SentenceService
{
    public function createSentences(mixed $object, array $sentences)
    {
        return $object->sentences()->createMany($sentences);
    }

    public function modifyObjectSentences(mixed $object, array $sentences)
    {
        DB::transaction(function () use ($object, $sentences) {
            foreach ($sentences as $sentence) {
                $sentenceId = $sentence["id"];
                if (!is_null($sentenceId) && ($sentence["is_deleted"] ?? false)) {
                    $object->sentences()->where("id", $sentenceId)->delete();
                    continue;
                }

                unset($sentence["is_deleted"]);
                unset($sentence["id"]);

                if (!is_null($sentenceId)) {
                    $object->sentences()->where("id", $sentenceId)->update($sentence);
                    continue;
                }

                $object->sentences()->create($sentence);
            };
        });
    }
}