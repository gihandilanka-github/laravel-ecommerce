<?php

namespace Tests\Unit\Listeners;

use App\Events\OrderCreated;
use App\Listeners\SendOrderConfirmation;
use App\Models\Order;
use App\Models\User;
use App\Notifications\OrderConfirmation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class SendOrderConfirmationTest extends TestCase
{
    use RefreshDatabase;

    public function test_sends_order_confirmation_notification(): void
    {
        Notification::fake();

        $user = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $user->id]);
        $event = new OrderCreated($order);

        $listener = new SendOrderConfirmation();
        $listener->handle($event);

        Notification::assertSentTo(
            $user,
            OrderConfirmation::class,
            function ($notification) use ($order) {
                return $notification->order->id === $order->id;
            }
        );
    }
}
