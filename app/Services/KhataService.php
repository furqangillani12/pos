<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Refund;
use Illuminate\Support\Collection;

/**
 * Single source of truth for a customer's khata (running account).
 *
 * The khata is made of:
 *   - bills          : orders (not cancelled/returned), each adding total − paid-at-counter
 *   - khata payments : Payment rows khata / khata_offset (reduce) and khata_payout (increase)
 *   - refunds        : completed Refund rows on the customer's orders (reduce)
 *
 * customers.current_balance is kept in step by the controllers; every action that
 * moves it must also leave one of the rows above, so the statement always explains it.
 *
 * Khata payments are also allocated to bills oldest-first (orders.khata_paid), so a
 * bill paid through Cash In / khata page shows as paid and Wasooli matches the khata.
 */
class KhataService
{
    /** Payment types that live on the khata (not tied to the bill-time payment). */
    public const PAYMENT_TYPES = ['khata', 'khata_offset', 'khata_payout', 'khata_adjust'];

    /**
     * Re-spread the customer's khata payments over their bills, oldest bill first.
     * Only touches orders.khata_paid / balance_amount / payment_status (never the
     * customer's balance), writes quietly (no observers), and is idempotent.
     */
    public static function reallocate(?int $customerId): void
    {
        if (!$customerId) return;

        $pool = (float) Payment::where('customer_id', $customerId)->whereIn('payment_type', ['khata', 'khata_offset'])->sum('amount')
              - (float) Payment::where('customer_id', $customerId)->where('payment_type', 'khata_payout')->sum('amount')
              // Balance adjustments are signed: + adds to what is owed, − works like a payment.
              - (float) Payment::where('customer_id', $customerId)->where('payment_type', 'khata_adjust')->sum('amount');

        $orders = Order::where('customer_id', $customerId)
            ->whereNotIn('status', Order::KHATA_EXCLUDED_STATUSES)
            ->orderBy('created_at')->orderBy('id')
            ->get();

        $refunded = Refund::whereIn('order_id', $orders->pluck('id'))
            ->where('status', 'completed')
            ->selectRaw('order_id, SUM(amount) as amt')->groupBy('order_id')
            ->pluck('amt', 'order_id');

        foreach ($orders as $order) {
            $due   = max(0.0, round($order->khataNet() - (float) ($refunded[$order->id] ?? 0), 2));
            $alloc = round(min(max($pool, 0.0), $due), 2);
            $pool -= $alloc;
            $bal   = round($due - $alloc, 2);

            // Legacy "fully paid" orders (paid 0 / balance 0) have nothing due — leave untouched.
            if ($due == 0.0 && (float) $order->khata_paid == 0.0 && (float) $order->balance_amount == 0.0) continue;

            if (abs((float) $order->khata_paid - $alloc) > 0.009 || abs((float) $order->balance_amount - $bal) > 0.009) {
                $order->khata_paid     = $alloc;
                $order->balance_amount = $bal;
                if ($bal <= 0) {
                    $order->payment_status = 'paid';
                } elseif ($order->counterPaid() + $alloc > 0) {
                    $order->payment_status = 'partial';
                }
                $order->saveQuietly();
            }
        }
    }

    /** Reallocate every customer (used by the khata:sync command). Returns orders changed. */
    public static function reallocateAll(): void
    {
        Customer::query()->select('id')->chunkById(200, function ($customers) {
            foreach ($customers as $c) self::reallocate($c->id);
        });
    }

    /**
     * All khata rows of a customer in a period, oldest first. Each row:
     *   type (order|payment|offset|payout|refund), date, id, reference, amount,
     *   paid (counter-paid for bills), effect (signed change to the balance),
     *   balance_on_bill, method, notes, items_count, order, channel.
     */
    public static function entries(Customer $customer, ?string $from = null, ?string $to = null): Collection
    {
        $rows = collect();

        $orders = $customer->orders()
            ->with('items.product')
            ->whereNotIn('status', Order::KHATA_EXCLUDED_STATUSES)
            ->when($from, fn ($q) => $q->where('created_at', '>=', $from . ' 00:00:00'))
            ->when($to,   fn ($q) => $q->where('created_at', '<=', $to . ' 23:59:59'))
            ->get();

        foreach ($orders as $order) {
            $paid = $order->counterPaid();
            $rows->push([
                'type'            => 'order',
                'date'            => $order->created_at,
                'id'              => $order->id,
                'reference'       => $order->order_number,
                'amount'          => (float) $order->total,
                'paid'            => $paid,
                'effect'          => round((float) $order->total - $paid, 2),
                'balance_on_bill' => (float) ($order->balance_amount ?? 0),
                'method'          => $order->payment_method,
                'notes'           => null,
                'items_count'     => $order->items->count(),
                'order'           => $order,
                'channel'         => $order->order_source === 'online' ? 'Website' : 'In-store',
            ]);
        }

        $payments = Payment::where('customer_id', $customer->id)
            ->whereIn('payment_type', self::PAYMENT_TYPES)
            ->when($from, fn ($q) => $q->whereDate('payment_date', '>=', $from))
            ->when($to,   fn ($q) => $q->whereDate('payment_date', '<=', $to))
            ->get();

        foreach ($payments as $p) {
            $type = match ($p->payment_type) {
                'khata_payout' => 'payout',
                'khata_offset' => 'offset',
                'khata_adjust' => 'adjust',
                default        => 'payment',
            };
            // Adjustments are stored signed (+ owed more / − owed less); others positive.
            $signed = (float) $p->amount;
            $amount = abs($signed);
            $rows->push([
                'type'            => $type,
                // Payments carry a date only; order them by their real creation time
                // on that day so same-day bills and payments stay in sequence.
                'date'            => $p->created_at && $p->created_at->toDateString() === $p->payment_date->toDateString()
                                        ? $p->created_at : $p->payment_date,
                'id'              => $p->id,
                'reference'       => $p->payment_number ?? $p->reference_number,
                'amount'          => $amount,
                'paid'            => ($type === 'payout' || ($type === 'adjust' && $signed > 0)) ? 0 : $amount,
                'effect'          => match ($type) { 'payout' => $amount, 'adjust' => $signed, default => -$amount },
                'balance_on_bill' => 0,
                'method'          => $p->payment_method,
                'notes'           => $p->notes,
                'items_count'     => 0,
                'order'           => null,
                'channel'         => $p->payment_method,
            ]);
        }

        $refunds = Refund::with('order:id,order_number')
            ->where('status', 'completed')
            ->whereHas('order', fn ($q) => $q->where('customer_id', $customer->id)
                ->whereNotIn('status', Order::KHATA_EXCLUDED_STATUSES))
            ->when($from, fn ($q) => $q->where('created_at', '>=', $from . ' 00:00:00'))
            ->when($to,   fn ($q) => $q->where('created_at', '<=', $to . ' 23:59:59'))
            ->get();

        foreach ($refunds as $r) {
            $amount = (float) $r->amount;
            $rows->push([
                'type'            => 'refund',
                'date'            => $r->created_at,
                'id'              => $r->order_id,
                'reference'       => ($r->refund_number ?? 'Refund') . ($r->order ? ' · ' . $r->order->order_number : ''),
                'amount'          => $amount,
                'paid'            => $amount,
                'effect'          => -$amount,
                'balance_on_bill' => 0,
                'method'          => 'refund',
                'notes'           => $r->reason,
                'items_count'     => 0,
                'order'           => null,
                'channel'         => 'Refund',
            ]);
        }

        return $rows->sortBy(fn ($r) => \Carbon\Carbon::parse($r['date'])->timestamp)->values();
    }

    /**
     * Rows with a running balance, anchored to the customer's current balance so
     * the last row always equals what the POS shows. Returns [rows, openingBalance].
     * Pass $through = the period's end date (rows after it shift the anchor).
     */
    public static function withRunningBalance(Customer $customer, Collection $rows, ?string $to = null): array
    {
        $current = (float) ($customer->current_balance ?? 0);

        // Anything after the period end still moved the balance — rewind past it first.
        if ($to) {
            $after = self::entries($customer, \Carbon\Carbon::parse($to)->addDay()->toDateString(), null);
            $current -= $after->sum('effect');
        }

        $opening = round($current - $rows->sum('effect'), 2);
        $running = $opening;
        $out = $rows->map(function ($r) use (&$running) {
            $running = round($running + $r['effect'], 2);
            $r['running_balance'] = $running;
            return $r;
        });

        return [$out, $opening];
    }

    /**
     * Customer's khata balance as of just before $order (receipts' "previous balance").
     */
    public static function balanceBefore(Order $order): float
    {
        if (!$order->customer_id || !$order->customer) return 0.0;

        $rows = self::entries($order->customer)->filter(function ($r) use ($order) {
            if ($r['type'] === 'order') return $r['id'] !== $order->id && (
                $r['date'] < $order->created_at || ($r['date'] == $order->created_at && $r['id'] < $order->id)
            );
            return \Carbon\Carbon::parse($r['date'])->lt($order->created_at);
        });

        return round($rows->sum('effect'), 2);
    }

    /**
     * Reseller / wholesale earnings: (retail price − price they paid) × qty, over
     * bills that still stand (not cancelled / returned / fully refunded). Uses the
     * retail price saved at sale time, falling back to today's price for old bills.
     */
    public static function resellerEarnings(Customer $customer, ?string $from = null, ?string $to = null): float
    {
        $orders = $customer->orders()
            ->with('items.product:id,price,sale_price')
            ->whereNotIn('status', array_merge(Order::KHATA_EXCLUDED_STATUSES, ['refunded']))
            ->when($from, fn ($q) => $q->where('created_at', '>=', $from . ' 00:00:00'))
            ->when($to,   fn ($q) => $q->where('created_at', '<=', $to . ' 23:59:59'))
            ->get();

        $earnings = 0.0;
        foreach ($orders as $order) {
            foreach ($order->items as $item) {
                $retail = (float) ($item->retail_price ?? 0);
                if ($retail <= 0 && $item->product) {
                    $retail = self::retailPrice($item->product);
                }
                $diff = $retail - (float) $item->unit_price;
                if ($diff > 0) $earnings += $diff * (float) $item->quantity;
            }
        }
        return round($earnings, 2);
    }

    /**
     * Khata entry for an approved website payment / withdrawal request. The
     * caller moves the customer's balance; this only records it (idempotent —
     * keyed on the request id in reference_number).
     */
    public static function recordAccountRequest(\App\Models\AccountRequest $req, ?\Carbon\Carbon $date = null): ?Payment
    {
        if (!$req->customer_id) return null;
        $ref = 'REQ-' . $req->id;
        if (Payment::withTrashed()->where('reference_number', $ref)->exists()) return null;

        $isPayment = $req->type === 'payment';
        $method = $isPayment
            ? trim(($req->sender_bank ?: 'Website') . ($req->reference ? ' / ' . $req->reference : ''))
            : trim(($req->bank_name ?: 'Website') . ($req->account_number ? ' / ' . $req->account_number : ''));

        return Payment::create([
            'payment_number'   => Payment::generatePaymentNumber(),
            'payment_type'     => $isPayment ? 'khata' : 'khata_payout',
            'order_id'         => null,
            'customer_id'      => $req->customer_id,
            'amount'           => (float) $req->amount,
            'payment_date'     => ($date ?? now())->toDateString(),
            'payment_method'   => $method,
            'reference_number' => $ref,
            'notes'            => ($isPayment ? 'Website payment request #' : 'Website withdrawal request #') . $req->id
                                  . ($req->sender_name ? ', ' . $req->sender_name : ''),
            'status'           => 'completed',
            'created_by'       => auth()->id(),
        ]);
    }

    /**
     * When a Cash In/Out payment is deleted, post the opposite entry on the Cash
     * account (once) so the cash book matches the khata. $type is the original
     * entry's reference_type (cash_customer / cash_supplier).
     */
    public static function reverseCashEntry(string $type, int $referenceId, string $description): void
    {
        $entries = \App\Models\LedgerAccountEntry::where('reference_type', $type)
            ->where('reference_id', $referenceId)->get();

        foreach ($entries as $e) {
            $already = \App\Models\LedgerAccountEntry::where('reference_type', $type . '_reversal')
                ->where('reference_id', $e->id)->exists();
            if ($already) continue;

            \App\Models\LedgerAccountEntry::create([
                'entry_number'      => \App\Models\LedgerAccountEntry::generateEntryNumber(),
                'ledger_account_id' => $e->ledger_account_id,
                'entry_date'        => now()->toDateString(),
                'description'       => $description,
                'debit'             => (float) $e->credit,
                'credit'            => (float) $e->debit,
                'reference_type'    => $type . '_reversal',
                'reference_id'      => $e->id,
                'reference_number'  => $e->reference_number,
                'payment_method'    => $e->payment_method,
                'notes'             => 'Auto-reversal of ' . $e->entry_number,
                'created_by'        => auth()->id(),
            ]);
        }
    }

    /** Retail (walk-in customer) selling price of a product. */
    public static function retailPrice(Product $product): float
    {
        return (float) ($product->sale_price ?: $product->price ?: 0);
    }

    /** Sale-time snapshot stored on each order item (cost & retail price). */
    public static function itemSnapshot(?Product $product, ?\App\Models\ProductVariant $variant = null): array
    {
        if (!$product) return [];
        return [
            'cost_price'   => $product->cost_price !== null ? (float) $product->cost_price : null,
            'retail_price' => ($variant ? $variant->retailPrice() : self::retailPrice($product)) ?: null,
        ];
    }
}
