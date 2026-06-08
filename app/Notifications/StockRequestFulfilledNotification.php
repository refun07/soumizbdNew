<?php

namespace App\Notifications;

use App\Models\StockRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class StockRequestFulfilledNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $stockRequest;

    public function __construct(StockRequest $stockRequest)
    {
        $this->stockRequest = $stockRequest;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $details = $this->stockRequest->product_details ?? [];
        $productUrl = route('product', $details['slug'] ?? '#');

        return (new MailMessage)
            ->view('emails.stock-request-fulfilled', [
                'request' => $this->stockRequest,
                'productUrl' => $productUrl
            ])
            ->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'))
            ->subject('Stock Request Fulfilled - ' . $this->stockRequest->product_name);
    }
}
