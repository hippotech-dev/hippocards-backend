<?php

namespace App\Http\Services;

use App\Enums\ELocale;
use App\Enums\ESentenceType;
use App\Models\Utility\Language;
use App\Models\Utility\Sentence;
use App\Util\AudioConfig;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SentenceService
{
    public function __construct(private AudioService $audioService)
    {
    }

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

    public function generateAudioForAllSentences(Language $language, int $limit = 500)
    {
        Log::channel("custom")->info(date("Y-m-d H:i:s") . " Sentence AUDIO Generate START");
        $sentences = Sentence::where("language_id", $language->id)->where("type", ESentenceType::DEFINITION)->whereNull("v3_audio_asset_id")->limit($limit)->get();
        foreach ($sentences as $sentence) {
            $asset = $this->audioService->generateAudio(
                $sentence->value,
                new AudioConfig($language->azure ?? ELocale::ENGLISH)
            );

            $sentence->update([
                "v3_audio_asset_id" => $asset->id
            ]);
        }
        Log::channel("custom")->info(date("Y-m-d H:i:s") . " Sentence AUDIO Generate FINISH " . " TOTAL: " . count($sentences));
    }
}
