<?php

namespace Tests\Feature\Api;

use App\Events\OrderCreated;
use App\Models\Product;
use App\Models\User;
use App\Models\Order;
use Tests\TestCase;
use Illuminate\Support\Facades\Event;

use Illuminate\Foundation\Testing\RefreshDatabase;

class OrderControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->product = Product::factory()->create([
            'price' => 100,
            'stock_quantity' => 10,
        ]);
    }

    public function test_can_create_order(): void
    {
        Event::fake();

        $response = $this->actingAs($this->user)
            ->postJson('/api/orders', [
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

        $response->assertCreated()
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'status',
                    'total_price',
                    'items' => [
                        '*' => [
                            'product_id',
                            'quantity',
                            'unit_price',
                        ],
                    ],
                ],
            ]);

        Event::assertDispatched(OrderCreated::class);
    }

    public function test_can_update_order_status(): void
    {
        $order = $this->createOrder();

        $response = $this->actingAs($this->user)
            ->patchJson("/api/update-order-status/{$order->id}", [
                'status' => 'precessed',
            ]);

        $response->assertOk()
            ->assertJsonPath('data.status', 'processing');
    }

    public function test_can_list_orders_with_pagination(): void
    {
        $orders = Order::factory()
            ->count(25)
            ->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)
            ->getJson('/api/orders');

        $response->assertOk()
            ->assertJsonCount(20, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'status',
                        'total_amount',
                    ],
                ],
                'links',
                'meta',
            ]);
    }

    private function createOrder(): Order
    {
        return Order::factory()->create([
            'user_id' => $this->user->id,
        ]);
    }
}
