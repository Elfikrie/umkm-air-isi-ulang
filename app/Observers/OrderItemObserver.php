<?php

namespace App\Observers;

use App\Models\OrderItem;

class OrderItemObserver
{
    /**
     * Handle the OrderItem "created" event.
     */
    public function saving(OrderItem $orderItem): void
    {
        $orderItem->price_at_order = $orderItem->product->price;
        $orderItem->subtotal = $orderItem->quantity * $orderItem->price_at_order;
    }

    /**
     * Handle the OrderItem "updated" event.
     */
    public function saved(OrderItem $orderItem): void
    {
        $order = $orderItem->order()->first();
        $total = $order->items()->sum('subtotal');
        $order->updateQuietly([
            'total_amount' => $total,
        ]);
    }

    /**
     * Handle the OrderItem "deleted" event.
     */
    public function deleted(OrderItem $orderItem): void
    {
        $order = $orderItem->order;
        $order->updateQuietly([
            'total_amount' => $order->items()->sum('subtotal'),
        ]);
    }

}
