<?php

namespace App\Observers;

use App\Models\FinancialReport;
use App\Models\Order;

class OrderObserver
{
    /**
     * Handle the Order "created" event.
     */
    public function created(Order $order): void
    {
        //
    }

    /**
     * Handle the Order "updated" event.
     */
    public function updated(Order $order): void
    {
        if($order->wasChanged('status') && $order->status === 'diterima'){
            FinancialReport::create([
                'type' => 'otomatis',
                'category' => 'pemasukan',
                'amount' => $order->total_amount,
                'description' => "Pemasukan dari Order #{$order->id}",
                'report_date' => now()->toDateString(),
                'order_id' => $order->id,
                'created_by' => $order->processed_by,
            ]);
        }
    }

    /**
     * Handle the Order "deleted" event.
     */
    public function deleted(Order $order): void
    {
        //
    }

    /**
     * Handle the Order "restored" event.
     */
    public function restored(Order $order): void
    {
        //
    }

    /**
     * Handle the Order "force deleted" event.
     */
    public function forceDeleted(Order $order): void
    {
        //
    }
}
