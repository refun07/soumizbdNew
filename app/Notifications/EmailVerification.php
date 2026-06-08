<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class EmailVerification extends Notification
{
    use Queueable;

    public function __construct()
    {
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $notifiable->verification_code = rand(100000, 999999);
        $notifiable->save();

        $array['view'] = 'emails.verification';
        $array['subject'] = translate('Email Verification') . ' - ' . config('app.name');
        $array['content'] = translate('You verification code is') . ' ' . $notifiable->verification_code;

        return (new MailMessage)
            ->from(config('app.mail_from_address'))
            ->view('emails.verification', ['array' => $array])
            ->subject($array['subject']);
    }

    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
