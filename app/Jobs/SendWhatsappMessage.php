<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use GuzzleHttp\Client;
use Log;

class SendWhatsappMessage implements ShouldQueue
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
        $body = [
            'message_type' => 'text',
            'number'       => $this->number,
            'message'      => $this->message,
        ];
        try {
            $client = new Client();

            $client->request('POST', env('WHATSAPP_BASE_URL'), [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . env('WHATSAPP_API_KEY'),
                ],
                'json' => $body,
            ]);
        } catch (\Exception $e) {
            // Log the error
            Log::error("Failed to send message to {$this->number}: " . $e->getMessage());
        }
    }
}
