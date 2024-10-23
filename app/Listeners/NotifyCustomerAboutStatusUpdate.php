<?php

namespace App\Listeners;

use App\Events\OrderStatusUpdated;
use App\Notifications\StatusUpdateNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotifyCustomerAboutStatusUpdate implements ShouldQueue
{
    public $tries = 3;

    public function handle(OrderStatusUpdated $event): void
    {
        $event->order->user->notify(
            new StatusUpdateNotification($event->order)
        );
    }
}
