<?php

namespace App\Jobs;

use App\Http\Services\SentenceService;
use App\Models\Utility\Sentence;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateSentenceAudioJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(private Sentence $sentence)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(SentenceService $service): void
    {
        $service->generateSentenceAudio($this->sentence);
    }
}
