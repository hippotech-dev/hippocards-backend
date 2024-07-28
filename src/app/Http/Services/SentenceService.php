<?php

namespace App\Http\Services;

use App\Enums\ELocale;
use App\Enums\ESentenceType;
use App\Jobs\GenerateSentenceAudioJob;
use App\Models\Utility\Language;
use App\Models\Utility\Sentence;
use App\Util\AudioConfig;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SentenceService
{
    public function __construct(private AudioService $audioService, private LanguageService $languageService, private AssetService $assetService)
    {
    }

    public function createSentences(mixed $object, array $sentences)
    {
        return $object->sentences()->createMany($sentences);
    }

    public function createSentence(mixed $object, array $data)
    {
        $sentence = $object->sentences()->create($data);
        $this->createSentenceAudio($sentence);

        return $sentence;
    }

    public function updateSentence(Sentence $sentence, array $data)
    {
        if (array_key_exists("value", $data) && $sentence->value !== $data["value"]) {
            $this->createSentenceAudio($sentence);
        }

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

                $sentence = $object->sentences()->create($sentence);
            };
        });
    }

    public function createSentenceAudio(Sentence $sentence)
    {
        return GenerateSentenceAudioJob::dispatch($sentence);
    }

    public function generateSentenceAudio(Sentence $sentence)
    {
        if (!is_null($sentence->v3_audio_asset_id)) {
            $this->assetService->deleteAssetById($sentence->v3_audio_asset_id);
        }

        $language = $this->languageService->getLanguageById($sentence->language_id);

        $asset = $this->audioService->generateAudio(
            $sentence->value,
            new AudioConfig($language->azure ?? ELocale::ENGLISH)
        );

        return $sentence->update([
            "v3_audio_asset_id" => $asset->id
        ]);
    }

    public function generateAudioForAllSentences(Language $language, int $limit = 500)
    {
        Log::channel("custom")->info(date("Y-m-d H:i:s") . " Sentence AUDIO Generate START");
        $sentences = Sentence::where("language_id", $language->id)->where("type", ESentenceType::DEFINITION)->whereNull("v3_audio_asset_id")->limit($limit)->get();
        foreach ($sentences as $sentence) {
            try {
                $asset = $this->audioService->generateAudio(
                    $sentence->value,
                    new AudioConfig($language->azure ?? ELocale::ENGLISH)
                );

                $sentence->update([
                    "v3_audio_asset_id" => $asset->id
                ]);
            } catch (Exception $err) {
                Log::channel("custom")->error(date("Y-m-d H:i:s") . " Sentence AUDIO Generate ERROR " . " Sentence ID: " . $sentence->id, [
                    "error" => $err
                ]);
            }
        }
        Log::channel("custom")->info(date("Y-m-d H:i:s") . " Sentence AUDIO Generate FINISH " . " TOTAL: " . count($sentences));
    }
}
