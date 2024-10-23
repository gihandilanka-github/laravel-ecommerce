<?php

namespace App\Repositories\Order;

use App\Events\OrderCreated;
use App\Events\OrderStatusUpdated;
use App\Exceptions\InsufficientStockException;
use App\Exceptions\OrderException;
use App\Exceptions\PaymentFailedException;
use App\Repositories\BaseRepository;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Throwable;

class OrderRepository extends BaseRepository
{
    public function __construct(protected Order $order) {}

    public function index(array $request): Collection|LengthAwarePaginator
    {
        $orders = $this->order->query();

        if (request()->filled('name')) {
            $orders->where('name', 'like', '%' . $request['name'] . '%');
        }

        if (request()->filled('sortOrder')) {
            $orders->orderBy('created_at', $request['sortOrder']);
        }

        if (!request()->filled('sortOrder')) {
            $orders->orderBy('created_at', 'desc');
        }

        if (empty($request['limit'])) {
            return $orders->get();
        }

        return $orders->paginate($request['limit']);
    }

    public function createOrder(User $user, array $request)
    {
        DB::beginTransaction();
        try {

            foreach ($request['items'] as $item) {
                $product = Product::findOrFail($item['product_id']);
                if ($product->stock_quantity < $item['quantity']) {
                    throw new InsufficientStockException(
                        $product->name,
                        $item['quantity'],
                        $product->stock_quantity
                    );
                }
            }

            $order = Order::create([
                'user_id' => $user->id,
                'shipping_address' => $request['shipping_address'],
                'billing_address' => $request['billing_address'],
                'status' => 'pending',
                'payment_status' => 'pending',
                'total_price' => 0
            ]);

            $total = 0;
            foreach ($request['items'] as $item) {
                $product = Product::findOrFail($item['product_id']);
                $subtotal = $product->price * $item['quantity'];
                $total += $subtotal;

                $order->items()->create([
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $product->price,
                    'subtotal' => $subtotal,
                ]);

                $product->decrement('stock_quantity', $item['quantity']);
            }

            $order->update(['total_price' => $total]);

            try {
                $this->processPayment($order, $request['payment_method']);
            } catch (Throwable $e) {
                throw new PaymentFailedException(
                    $e->getMessage(),
                    $e->getCode()
                );
            }

            event(new OrderCreated($order));

            DB::commit();

            return $order;
        } catch (Throwable $ex) {
            DB::rollback();
            if ($ex instanceof OrderException) {
                throw $ex;
            }

            report($ex);
            throw $ex;
        }
    }

    private function processPayment(Order $order, string $paymentMethod): void
    {
        // Payment processing logic here
        // Throws PaymentFailedException if payment fails
    }

    public function updateStatus(Order $order, string $status): Order
    {
        $order->update(['status' => $status]);

        OrderStatusUpdated::dispatch($order);

        return $order;
    }
}
