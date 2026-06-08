<?php

namespace App\Jobs;

use App\Mail\EmailManager;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use GuzzleHttp\Client;
use Log;
use Mail;

class SendEmailMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    protected $email;
    protected $message;

    /**
     * Create a new job instance.
     */
    public function __construct($email, $message)
    {
        $this->email = $email;
        $this->message = $message;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $array['view']    = 'emails.newsletter';
        $array['subject'] = env('APP_NAME') . ' - Offer Notification';
        $array['from']    = env('MAIL_FROM_ADDRESS');
        $array['content'] = $this->message;
        try {
            Mail::to($this->email)->queue(new EmailManager($array));
        } catch (\Exception $e) {
            // Log the error
            Log::error("Failed to send message to {$this->email}: " . $e->getMessage());
        }
    }
}
