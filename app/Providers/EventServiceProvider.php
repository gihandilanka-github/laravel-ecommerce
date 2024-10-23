<?php

namespace App\Providers;

use App\Events\OrderCreated;
use App\Events\OrderStatusUpdated;
use App\Listeners\SendOrderConfirmation;
use App\Listeners\NotifyCustomerAboutStatusUpdate;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        OrderCreated::class => [
            SendOrderConfirmation::class,
        ],
        OrderStatusUpdated::class => [
            NotifyCustomerAboutStatusUpdate::class,
        ],
    ];
}
