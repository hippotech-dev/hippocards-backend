<?php

use App\Http\Services\PackageService;
use App\Http\Services\SentenceService;
use App\Models\Utility\Language;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

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
    $service->generateAudioForAllSentences(Language::find(1), 1000);
    // $service->generateAudioForAllSentences(Language::find(2));
    // $service->generateAudioForAllSentences(Language::fin3d(3));
    // $service->generateAudioForAllSentences(Language::find(4));
    // $service->generateAudioForAllSentences(Language::find(5));
    // $service->generateAudioForAllSentences(Language::find(9));
    // $service->generateAudioForAllSentences(Language::find(13));
})->purpose('Display an inspiring quote');
