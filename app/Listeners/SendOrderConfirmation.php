<?php

namespace App\Listeners;

use App\Events\OrderCreated;
use App\Notifications\OrderConfirmation;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendOrderConfirmation implements ShouldQueue
{
    public $tries = 3;

    public function handle(OrderCreated $event): void
    {
        $event->order->user->notify(
            new OrderConfirmation($event->order)
        );
    }
}
