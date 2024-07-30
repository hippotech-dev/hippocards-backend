<?php

use App\Http\Services\SentenceService;
use App\Models\Utility\Language;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('custom:generate-sentence-audio', function (SentenceService $service) {
    $service->generateAudioForAllSentences(Language::find(5), 1000);
})->purpose('Display an inspiring quote');

Artisan::command('custom:generate-sentence-audio-3', function (SentenceService $service) {
    sleep(10);
    $service->generateAudioForAllSentences(Language::find(6), 1000);
})->purpose('Display an inspiring quote');


Artisan::command('custom:generate-sentence-audio-4', function (SentenceService $service) {
    sleep(10);
    $service->generateAudioForAllSentences(Language::find(9), 1000);
})->purpose('Display an inspiring quote');
