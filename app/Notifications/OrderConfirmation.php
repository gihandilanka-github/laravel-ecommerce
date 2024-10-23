<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderConfirmation extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly Order $order
    ) {}

    public function via(): array
    {
        return ['mail'];
    }

    public function toMail(): MailMessage
    {
        return (new MailMessage)
            ->subject('Order Confirmation #' . $this->order->id)
            ->line('Thank you for your order!')
            ->line('Order Total: $' . $this->order->total_amount)
            ->action('View Order', url('/orders/' . $this->order->id))
            ->line('We will notify you when your order ships.');
    }
}
