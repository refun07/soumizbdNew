<?php

namespace App\Notifications;

use App\Models\OrderGroup;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class OrderPlacedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $orderGroup;

    public function __construct(OrderGroup $orderGroup)
    {
        $this->orderGroup = $orderGroup;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $array['subject'] = translate('Your order has been placed') . ' - ' . $this->orderGroup->code;
        $array['order'] = $this->orderGroup;

        return (new MailMessage)
            ->view('emails.invoice', ['array' => $array, 'orderGroup' => $this->orderGroup])
            ->from(env('MAIL_FROM_ADDRESS'))
            ->subject(translate('Order Placed') . ' - ' . getSetting('systemName'));
    }

    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
