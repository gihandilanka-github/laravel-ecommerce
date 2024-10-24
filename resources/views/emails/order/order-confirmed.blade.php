@component('mail::message')
# Your order has been confirmed!

Here are the details of your order:

| Item Name | Unit Price | Quantity | Subtotal |
|------------------|--------------|--------------|--------------|
@foreach ($order->items as $item)
| {{ $item->product->name }} | ${{ number_format($item->unit_price, 2) }} | {{ $item->quantity }} | ${{ number_format($item->unit_price * $item->quantity, 2) }} |
@endforeach

**Total**: ${{ number_format($order->items->sum(fn($item) => $item->unit_price * $item->quantity), 2) }}

@component('mail::button', ['url' => url('/orders/' . $order->id)])
View Order
@endcomponent

Thanks for shopping with us!

@endcomponent