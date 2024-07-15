<?php

namespace App\Jobs;

use App\Http\Services\WordSortService;
use App\Models\Package\Sort;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateWordAudioJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(private Sort $sort)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(WordSortService $service): void
    {
        $service->generateSortAudio($this->sort);
    }
}
