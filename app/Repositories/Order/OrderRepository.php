<?php

namespace App\Repositories\Order;

use App\Events\OrderCreated;
use App\Events\OrderStatusUpdated;
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
            }

            $order->update(['total_price' => $total]);

            event(new OrderCreated($order));

            DB::commit();

            return $order;
        } catch (Throwable $ex) {
            DB::rollback();
            dd($ex->getMessage());
        }
    }

    public function updateStatus(Order $order, string $status): Order
    {
        $order->update(['status' => $status]);

        OrderStatusUpdated::dispatch($order);

        return $order;
    }
}
