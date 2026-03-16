<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class TestJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::info('TestJob executed successfully!', [
            'timestamp' => now()->toDateTimeString(),
            'job_id' => $this->job->getJobId(),
        ]);

        // Simulasi proses yang membutuhkan waktu
        sleep(1);

        Log::info('TestJob completed!', [
            'timestamp' => now()->toDateTimeString(),
        ]);
    }
}
