<?php

namespace App\Listeners;

use App\Events\OrderPlaced;
use App\Notifications\NewOrder;

class OrderPlacedNotification
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(OrderPlaced $event): void
    {
        foreach ($event->orderGroup->orders as $order) {
            // $order->user->notify(new NewOrder($order));
        }
    }
}
