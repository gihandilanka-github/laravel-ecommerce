<?php

namespace App\Repositories\Order;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Events\OrderCreated;
use App\Events\OrderStatusUpdated;
use App\Exceptions\Order\InsufficientStockException;
use App\Exceptions\Order\InvalidOrderStatusTransitionException;
use App\Exceptions\Payment\PaymentFailedException;
use App\Repositories\BaseRepository;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Throwable;

class OrderRepository extends BaseRepository
{
    private const ALLOWED_STATUS_TRANSITIONS = [
        'pending' => ['processed', 'cancelled'],
        'processed' => ['shipped', 'cancelled'],
        'shipped' => ['delivered', 'returned'],
        'delivered' => ['returned'],
        'returned' => [],
        'cancelled' => [],
    ];

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
                'status' => OrderStatus::PENDING,
                'payment_status' => PaymentStatus::PENDING,
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

            logger()->info('Order created', [
                'order_id'   => $order->id
            ]);

            return $order;
        } catch (Throwable $ex) {
            DB::rollback();
            report($ex);
            throw $ex;
        }
    }

    private function processPayment(Order $order, string $paymentMethod): void
    {
        try {
            $payment = Payment::create([
                'order_id' => $order->id,
                'payment_method' => $paymentMethod,
                'amount' => $order->total_price,
                'transaction_id' => Str::uuid(),
                'status' => PaymentStatus::PENDING
            ]);

            logger()->info('Payment created', [
                'payment_id' => $payment->id,
                'order_id'   => $order->id
            ]);
        } catch (Throwable $ex) {
            report($ex);
            throw $ex;
        }
    }

    private function isValidStatusTransition(string $currentStatus, string $newStatus): bool
    {
        return in_array($newStatus, self::ALLOWED_STATUS_TRANSITIONS[$currentStatus] ?? []);
    }

    public function updateOrderStatus(int $orderId, string $status): Order
    {
        try {
            $order = Order::find($orderId);
            if (!$order) {
                throw new ModelNotFoundException("Order not found for orderId:" . $orderId);
            }
            $currentStatus = $order->status;

            if (!$this->isValidStatusTransition($currentStatus, $status)) {
                throw new InvalidOrderStatusTransitionException(
                    $currentStatus,
                    $status
                );
            }

            $order->update(['status' => $status]);
            event(new OrderStatusUpdated($order));

            return $order;
        } catch (Throwable $ex) {
            report($ex);
            throw $ex;
        }
    }

    public function show(int $id): Order
    {
        return $this->order->find($id);
    }
}
