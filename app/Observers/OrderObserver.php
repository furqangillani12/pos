<?php

namespace App\Observers;

use App\Models\Order;
use App\Services\LedgerService;

class OrderObserver
{
    /**
     * When an order is created, record it in the ledger.
     * Only record completed orders.
     */
    public function created(Order $order): void
    {
        if ($order->status === Order::STATUS_COMPLETED) {
            $this->recordOrder($order);
        }
    }

    /**
     * When an order is updated (e.g., status changed to completed).
     */
    public function updated(Order $order): void
    {
        // Only fire when status changes TO completed
        if ($order->isDirty('status') && $order->status === Order::STATUS_COMPLETED) {
            $this->recordOrder($order);
        }
    }

    private function recordOrder(Order $order): void
    {
        if ($order->payment_method === 'credit' || $order->credit_status === 'pending') {
            LedgerService::recordCreditSale($order);
        } else {
            LedgerService::recordSale($order);
        }
    }
}