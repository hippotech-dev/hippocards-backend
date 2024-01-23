<?php

namespace App\Jobs;

use App\Http\Services\MessageService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SMSJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(private string $phone, private string $message)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(MessageService $messageService): void
    {
        $messageService->sendMessage($this->phone, $this->message);
    }
}
