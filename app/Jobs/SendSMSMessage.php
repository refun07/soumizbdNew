<?php

namespace App\Jobs;

use App\Services\SmsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use GuzzleHttp\Client;
use Log;

class SendSMSMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    protected $number;
    protected $message;

    /**
     * Create a new job instance.
     */
    public function __construct($number, $message)
    {
        $this->number = $number;
        $this->message = $message;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {

        $sms = app(SmsService::class); // Dynamically resolve again
        try {
            $sms->send($this->number, $this->message);
        } catch (\Exception $e) {
            // Log the error
            Log::error("Failed to send message to {$this->number}: " . $e->getMessage());
        }
    }
}
