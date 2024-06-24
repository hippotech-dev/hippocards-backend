<?php

namespace App\Http\Controllers\Utility;

use App\Enums\ELocale;
use App\Http\Controllers\Controller;
use App\Http\Services\AudioService;
use App\Util\AudioConfig;
use Illuminate\Http\Request;

class AudioController extends Controller
{
    public function __construct(private AudioService $service)
    {
        $this->middleware("jwt.auth");
    }

    /**
     * Generate audio for given text
     */
    public function generateAudio(Request $request)
    {
        dd($this->service->generateAudio("Hello khulan", new AudioConfig(ELocale::ENGLISH)));
    }
}
