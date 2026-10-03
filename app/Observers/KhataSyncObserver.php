<?php

namespace App\Observers;

use App\Models\Order;
use App\Models\Payment;
use App\Models\Refund;
use App\Services\KhataService;
use Illuminate\Database\Eloquent\Model;

/**
 * Keeps bill-level balances (orders.khata_paid / balance_amount) in step with the
 * customer's khata whenever a bill, khata payment or refund changes. See
 * KhataService::reallocate(). Registered for Order, Payment and Refund.
 */
class KhataSyncObserver
{
    public function saved(Model $model): void   { $this->sync($model); }
    public function deleted(Model $model): void { $this->sync($model); }
    public function restored(Model $model): void { $this->sync($model); }

    private function sync(Model $model): void
    {
        foreach ($this->customerIds($model) as $id) {
            KhataService::reallocate($id);
        }
    }

    private function customerIds(Model $model): array
    {
        if ($model instanceof Order) {
            return array_unique(array_filter([
                $model->customer_id,
                $model->getOriginal('customer_id'),
            ]));
        }
        if ($model instanceof Payment) {
            return in_array($model->payment_type, KhataService::PAYMENT_TYPES, true)
                ? array_filter([$model->customer_id]) : [];
        }
        if ($model instanceof Refund) {
            return array_filter([$model->order?->customer_id]);
        }
        return [];
    }
}
