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

    public function createSentence(mixed $object, array $data)
    {
        return $object->sentences()->create($data);
    }

    public function updateSentence(Sentence $sentence, array $data)
    {
        return $sentence->update($data);
    }

    public function deleteSentence(Sentence $sentence)
    {
        return $sentence->delete();
    }

    public function modifyObjectSentences(mixed $object, array $sentences)
    {
        DB::transaction(function () use ($object, $sentences) {
            foreach ($sentences as $sentence) {
                $sentenceId = $sentence["id"] ?? null;
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
