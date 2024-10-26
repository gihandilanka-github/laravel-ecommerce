<?php

namespace Tests\Unit\Services;

use App\Events\OrderCreated;
use App\Models\Product;
use App\Models\User;
use App\Services\Order\OrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class OrderServiceTest extends TestCase
{
    use RefreshDatabase;

    private OrderService $orderService;
    private User $user;
    private Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        $this->orderService = app(OrderService::class);
        $this->user = User::factory()->create();
        $this->product = Product::factory()->create([
            'price' => 100,
            'stock_quantity' => 10,
        ]);
    }

    public function test_creates_order_with_correct_total(): void
    {
        Event::fake();

        $order = $this->orderService->createOrder($this->user, [
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'quantity' => 2,
                ],
            ],
            'shipping_address' => '123 Ship St',
            'billing_address' => '123 Bill St',
            'payment_method' => 'credit_card',
        ]);

        $this->assertEquals(200, $order->total_amount);
        $this->assertEquals('pending', $order->status);
        $this->assertCount(1, $order->items);
        Event::assertDispatched(OrderCreated::class);
    }
}
