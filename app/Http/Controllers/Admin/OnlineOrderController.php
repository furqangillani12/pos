<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Traits\BranchScoped;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Admin manager for storefront / online orders. Reads the same orders
 * table as the POS but filters by order_source='online'.
 */
class OnlineOrderController extends Controller
{
    use BranchScoped;

    public function index(Request $request)
    {
        // Each online order belongs to the branch that owns its products (the
        // storefront splits multi-branch carts into one order per branch), so a
        // branch only sees its own orders; "All branches" shows everything.
        $query = $this->scopeBranch(Order::query())
            ->where('order_source', 'online')
            ->with('branch')
            ->with('customer')
            ->withCount('items');

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }
        if ($payment = $request->input('online_payment_status')) {
            $this->applyPaymentFilter($query, $payment);
        }
        if ($search = trim((string) $request->input('search'))) {
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhere('tracking_id', 'like', "%{$search}%")
                  ->orWhere('customer_email', 'like', "%{$search}%")
                  ->orWhere('shipping_first_name', 'like', "%{$search}%")
                  ->orWhere('shipping_last_name', 'like', "%{$search}%")
                  ->orWhere('shipping_phone', 'like', "%{$search}%")
                  ->orWhereHas('customer', function ($c) use ($search) {
                      $c->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                  });
            });
        }
        if ($from = $request->input('from')) $query->whereDate('created_at', '>=', $from);
        if ($to   = $request->input('to'))   $query->whereDate('created_at', '<=', $to);

        $orders = $query->latest()->paginate(20)->withQueryString();

        // Stats for the same branch scope as the list.
        $statBase = $this->scopeBranch(Order::query())->where('order_source', 'online');
        $stats = [
            'all'       => (clone $statBase)->count(),
            'pending'   => (clone $statBase)->where('status', 'pending')->count(),
            'confirmed' => (clone $statBase)->where('status', 'confirmed')->count(),
            'shipped'   => (clone $statBase)->where('status', 'shipped')->count(),
            'delivered' => (clone $statBase)->where('status', 'delivered')->count(),
            'cancelled' => (clone $statBase)->where('status', 'cancelled')->count(),
            'unpaid_bank' => (clone $statBase)->where('online_payment_status', 'bank_pending')->count(),
            'revenue'   => (clone $statBase)->where('status', '!=', 'cancelled')->sum('total'),
        ];

        // Order count per payment filter (same branch scope), shown in the dropdown.
        $paymentCounts = [];
        foreach (array_keys(self::PAYMENT_FILTERS) as $key) {
            $q = $this->scopeBranch(Order::query())->where('order_source', 'online');
            $this->applyPaymentFilter($q, $key);
            $paymentCounts[$key] = $q->count();
        }
        $paymentFilters = self::PAYMENT_FILTERS;

        return view('admin.online-orders.index', compact('orders', 'stats', 'paymentCounts', 'paymentFilters'));
    }

    public function show(Order $order)
    {
        $this->guardBranch($order);
        $order->load('items.product', 'customer', 'branch');

        // Payments received against this order (each "Mark as Paid", full or partial).
        $orderPayments  = \App\Models\Payment::where('order_id', $order->id)->where('payment_type', 'order')->latest('id')->get();
        $paymentMethods = \App\Models\PaymentMethod::where('is_active', true)->orderBy('sort_order')->get();

        return view('admin.online-orders.show', compact('order', 'orderPayments', 'paymentMethods'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $this->guardBranch($order);
        abort_unless($order->order_source === 'online', 404);

        $allowed = array_keys(config('order_flow.statuses', []));
        $data = $request->validate([
            'status'      => ['required', 'string', \Illuminate\Validation\Rule::in($allowed)],
            'tracking_id' => 'nullable|string|max:191',
            'status_note' => 'nullable|string|max:500',
        ]);

        // NOTE: the earlier "must Mark as Paid / attach receipt before dispatch"
        // block was REMOVED at the client's request — staff must be able to
        // dispatch/print unpaid orders (COD and pay-later are normal). The payment
        // receipt is already enforced up-front at checkout for non-COD (P0), so no
        // admin-side gate is needed here.

        $restockStatuses = config('order_flow.restock', ['cancelled']);

        DB::transaction(function () use ($order, $data, $restockStatuses) {
            $wasOff = in_array($order->status, $restockStatuses, true);
            $isOff  = in_array($data['status'], $restockStatuses, true);

            // Returned / cancelled: put stock back and take the bill off the khata (once).
            // Moving back to an active status re-applies both. The khata effect is
            // the bill's total less what was paid on it at the counter — the same
            // amount the statement drops for a returned/cancelled order.
            if ($isOff !== $wasOff) {
                $sign = $isOff ? 1 : -1;
                foreach ($order->items as $item) {
                    if ($item->product && $item->product->track_inventory && $order->branch_id) {
                        $isOff
                            ? $item->product->incrementBranchStock($order->branch_id, (float) $item->quantity, $item->variant_id)
                            : $item->product->decrementBranchStock($order->branch_id, (float) $item->quantity, $item->variant_id);
                    }
                }
                if ($order->customer) {
                    $order->customer->update([
                        'current_balance' => round((float) ($order->customer->current_balance ?? 0) - $sign * $order->khataNet(), 2),
                    ]);
                }
            }
            $changed = $order->status !== $data['status'];
            $order->update([
                'status'      => $data['status'],
                'status_note' => $data['status_note'] ?? null,
                'tracking_id' => $data['tracking_id'] ?? $order->tracking_id,
            ]);

            // Tracking timeline event (#20) — prefer the admin reason note (#1f),
            // else the tracking line on dispatch.
            if ($changed) {
                $note = $data['status_note'] ?: (
                    $data['status'] === 'dispatched' && ($data['tracking_id'] ?? $order->tracking_id)
                        ? trim(($order->dispatch_method ? $order->dispatch_method . ' · ' : '') . 'Tracking ' . ($data['tracking_id'] ?? $order->tracking_id))
                        : null
                );
                $order->recordStatus($data['status'], $note);
            }

            // Reward points on delivery (#22) — once per order, scheme-gated.
            if ($data['status'] === 'delivered' && $order->customer) {
                $already = \App\Models\PointTransaction::where('order_id', $order->id)
                    ->where('type', 'earn_order')->exists();
                $points = shop_order_points($order->total);
                if (!$already && $points > 0) {
                    $order->customer->awardPoints($points, 'earn_order', "Order {$order->order_number}", $order->id);
                }
            }
        });

        // Notify the customer of the new status (safe no-op without an email).
        \App\Mail\OrderStatusMail::dispatchFor($order->fresh(), $data['status']);

        return back()->with('success', 'Order status updated to ' . ucfirst($data['status']) . '.');
    }

    /**
     * Edit the shipping address of an online order (#9). Customers sometimes
     * enter the wrong address; staff fix it at confirmation so the dispatch slip
     * prints correctly.
     */
    public function updateAddress(Request $request, Order $order)
    {
        $this->guardBranch($order);
        abort_unless($order->order_source === 'online', 404);

        $data = $request->validate([
            'shipping_first_name' => 'nullable|string|max:191',
            'shipping_last_name'  => 'nullable|string|max:191',
            'shipping_phone'      => 'nullable|string|max:40',
            'shipping_address1'   => 'required|string|max:255',
            'shipping_address2'   => 'nullable|string|max:255',
            'shipping_tehsil'     => 'nullable|string|max:191',
            'shipping_district'   => 'nullable|string|max:191',
            'shipping_city'       => 'nullable|string|max:191',
            'shipping_province'   => 'nullable|string|max:191',
            'shipping_post_code'  => 'nullable|string|max:40',
            'shipping_country'    => 'nullable|string|max:191',
        ]);

        $order->update($data);

        return back()->with('success', 'Shipping address updated.');
    }

    /** Printable bilingual dispatch slip (#15/#16/#17). */
    public function slip(Request $request, Order $order)
    {
        $this->guardBranch($order);
        abort_unless($order->order_source === 'online', 404);
        $order->load('items.product', 'customer', 'branch');

        // Default language comes from settings; a ?lang= URL param overrides per-print.
        $defaultLang = in_array(setting('dispatch_slip_lang'), ['ur', 'en'], true) ? setting('dispatch_slip_lang') : 'en';
        $lang        = in_array($request->input('lang'), ['ur', 'en'], true) ? $request->input('lang') : $defaultLang;
        // Sender ("From") block default:
        //   • regular customer                    → show the company as sender
        //   • reseller/wholesaler + own address   → show the reseller's address
        //   • reseller/wholesaler, no own address → hide From (white-label)
        $resellerType = in_array($order->customer_type ?: optional($order->customer)->customer_type, ['reseller', 'wholesale'], true);
        $fromDefault  = $resellerType ? ($order->from_name ? 'reseller' : 'hide') : 'company';
        $from = in_array($request->input('from'), ['company', 'reseller', 'hide'], true) ? $request->input('from') : $fromDefault;
        if ($from === 'reseller' && ! $order->from_name) {
            $from = 'hide';
        }
        $withLogo    = $request->boolean('logo', true);
        $withDetails = $request->boolean('details', true);
        $dispatchMethod = \App\Models\DispatchMethod::where('name', $order->dispatch_method)->first();

        // Dispatch date = when the order first hit dispatched/shipped (legacy).
        $dispatchedAt = $order->statusHistory()
            ->whereIn('status', ['dispatched', 'shipped'])
            ->orderBy('id')
            ->value('created_at');

        // Print scale so a slip fits smaller courier labels: 100 / 85 / 70 %.
        $scale = in_array((int) $request->input('scale'), [100, 85, 70], true) ? (int) $request->input('scale') : 100;

        return view('admin.online-orders.slip', compact('order', 'lang', 'from', 'withLogo', 'withDetails', 'dispatchMethod', 'dispatchedAt', 'scale'));
    }

    /** Printable picking checklist (#18): image, name, barcode, price, qty. */
    public function checklist(Order $order)
    {
        $this->guardBranch($order);
        abort_unless($order->order_source === 'online', 404);
        $order->load('items.product');
        return view('admin.online-orders.checklist', compact('order'));
    }

    /** Save the picking-checklist piece count so the dispatch slip shows it (#11). */
    public function savePieces(Request $request, Order $order)
    {
        $this->guardBranch($order);
        abort_unless($order->order_source === 'online', 404);
        $data = $request->validate(['dispatch_pieces' => 'nullable|integer|min:0']);
        $order->update(['dispatch_pieces' => $data['dispatch_pieces']]);

        return back()->with('success', 'Dispatch pieces set to ' . (int) $data['dispatch_pieces'] . ' for the slip.');
    }

    /** Attach a dispatch photo / short video to the order. */
    public function uploadDispatchMedia(Request $request, Order $order)
    {
        $this->guardBranch($order);
        abort_unless($order->order_source === 'online', 404);
        $request->validate([
            'dispatch_media' => 'required|file|mimes:png,jpg,jpeg,webp,mp4,webm,mov|max:20480',
        ]);

        if ($order->dispatch_media_path && \Storage::disk('public')->exists($order->dispatch_media_path)) {
            \Storage::disk('public')->delete($order->dispatch_media_path);
        }
        $order->update([
            'dispatch_media_path' => $request->file('dispatch_media')->store('dispatch-media', 'public'),
        ]);

        return back()->with('success', 'Dispatch photo/video attached.');
    }

    /**
     * Edit weight + delivery charge on an online order and recompute the tax and
     * total the same way checkout did. Keeps the customer's khata (balance) in
     * sync by the difference. POS orders are unaffected (online-only action).
     */
    public function adjust(Request $request, Order $order)
    {
        $this->guardBranch($order);
        abort_unless($order->order_source === 'online', 404);

        $data = $request->validate([
            'weight'              => 'nullable|numeric|min:0|max:9999999',
            'weight_unit'         => 'nullable|in:kg,g',
            'delivery_charges'    => 'nullable|numeric|min:0|max:9999999',
            // Editable COD amount for the dispatch slip (blank = auto: paid ? 0 : balance).
            'dispatch_cod_amount' => 'nullable|numeric|min:0|max:9999999',
            // Remarks printed on the dispatch slip (blank = empty box for handwriting).
            'dispatch_remarks'    => 'nullable|string|max:500',
        ]);

        DB::transaction(function () use ($request, $order, $data) {
            $oldTotal = (float) $order->total;
            $delivery = (float) ($data['delivery_charges'] ?? 0);

            // Same formula as checkout: tax on (subtotal − discounts + delivery).
            $afterDiscount = max(0, (float) $order->subtotal - (float) $order->coupon_discount - (float) $order->points_discount);
            $tax           = shop_tax_amount($afterDiscount + $delivery, $order->online_payment_status === 'cod');
            $newTotal      = round(max(0, $afterDiscount + $tax + $delivery + (float) ($order->packing_total ?? 0)), 2);
            $delta         = round($newTotal - $oldTotal, 2);

            // Blank COD field clears the override (back to auto); a number stores it.
            $codRaw = $request->input('dispatch_cod_amount');
            $codOverride = ($codRaw === null || $codRaw === '') ? null : round((float) $codRaw, 2);

            // Weight is entered in the chosen unit (#11) but always STORED in kg so
            // delivery-charge slabs keep working. Grams → kg on the way in.
            $weightUnit  = in_array($data['weight_unit'] ?? null, ['kg', 'g'], true) ? $data['weight_unit'] : 'kg';
            $weightInput = (float) ($data['weight'] ?? 0);
            $weightKg    = $weightUnit === 'g' ? round($weightInput / 1000, 3) : $weightInput;

            $order->update([
                'weight'              => $weightKg,
                'weight_unit'         => $weightUnit,
                'delivery_charges'    => $delivery,
                'tax'                 => $tax,
                'total'               => $newTotal,
                'balance_amount'      => round($newTotal - (float) $order->paid_amount, 2),
                'dispatch_cod_amount' => $codOverride,
                'dispatch_remarks'    => $data['dispatch_remarks'] ?? null,
            ]);

            // Reflect the change on the customer's running balance.
            if ($order->customer && abs($delta) > 0) {
                $order->customer->increment('current_balance', $delta);
            }
        });

        return back()->with('success', 'Weight & delivery updated — total recalculated.');
    }

    /** Manually (re)send the current-status email to the customer. */
    public function notify(Order $order)
    {
        $this->guardBranch($order);
        abort_unless($order->order_source === 'online', 404);
        \App\Mail\OrderStatusMail::dispatchFor($order, $order->status);
        return back()->with('success', 'Status email sent to the customer.');
    }

    /**
     * Record a payment received for an online order — full or partial (e.g. half
     * now, or only the delivery charges in advance). Reduces the order balance and
     * the customer's khata by exactly the amount received, and keeps a Payment row
     * per receipt so the order shows its payment history.
     */
    public function markPaid(Request $request, Order $order)
    {
        $this->guardBranch($order);
        abort_unless($order->order_source === 'online', 404);

        $due = round((float) $order->balance_amount, 2);
        if ($due <= 0) {
            return back()->with('error', 'This order has nothing left to pay.');
        }

        $data = $request->validate([
            'amount'         => 'required|numeric|min:1',
            'payment_method' => 'nullable|string|max:100',
            'note'           => 'nullable|string|max:255',
            'payment_ref'    => 'nullable|string|max:191',
            'payment_proof'  => 'nullable|image|max:5120',
        ]);

        $amount = round((float) $data['amount'], 2);
        // More than the balance (e.g. a reseller sends extra): the order is settled
        // and the extra goes to the customer's khata as an advance / credit, which
        // they can later withdraw. Guest orders have no khata to hold it.
        $extra = round(max(0, $amount - $due), 2);
        if ($extra > 0 && !$order->customer) {
            return back()->with('error', 'Amount Rs. ' . number_format($amount, 0) . ' is more than the balance due (Rs. ' . number_format($due, 0) . '). This is a guest order with no customer khata, so the extra cannot be kept as advance.');
        }
        $onOrder = round($amount - $extra, 2);
        $full    = abs($onOrder - $due) < 0.01;
        $method = ($data['payment_method'] ?? '') !== '' ? $data['payment_method'] : $order->payment_method;

        // Admin can attach a payment receipt here (#7).
        $proofPath = $order->payment_proof_path;
        if ($request->hasFile('payment_proof')) {
            $proofPath = $request->file('payment_proof')->store('payment-proofs', 'public');
        }

        DB::transaction(function () use ($order, $data, $proofPath, $amount, $onOrder, $extra, $due, $full, $method) {
            \App\Models\Payment::create([
                'payment_number'   => \App\Models\Payment::generatePaymentNumber(),
                'payment_type'     => 'order',
                'order_id'         => $order->id,
                'customer_id'      => $order->customer_id,
                'amount'           => $onOrder,
                'payment_date'     => now()->toDateString(),
                'payment_method'   => $method,
                'reference_number' => $data['payment_ref'] ?? null,
                'notes'            => $data['note'] ?? null,
                'status'           => 'completed',
                'created_by'       => auth()->id(),
            ]);

            // The extra becomes a khata payment (advance) on the customer's account.
            if ($extra > 0) {
                \App\Models\Payment::create([
                    'payment_number'   => \App\Models\Payment::generatePaymentNumber(),
                    'payment_type'     => 'khata',
                    'order_id'         => null,
                    'customer_id'      => $order->customer_id,
                    'amount'           => $extra,
                    'payment_date'     => now()->toDateString(),
                    'payment_method'   => $method,
                    'reference_number' => $data['payment_ref'] ?? null,
                    'notes'            => "Extra received with order {$order->order_number} payment (Rs. " . number_format($amount, 0) . ' total) — kept as advance',
                    'status'           => 'completed',
                    'created_by'       => auth()->id(),
                ]);
            }

            $order->update([
                'paid_amount'           => round((float) $order->paid_amount + $onOrder, 2),
                'balance_amount'        => round($due - $onOrder, 2),
                'payment_status'        => $full ? 'paid' : 'partial',
                'online_payment_status' => $full
                    ? ($this->isCodOrder($order) ? 'paid' : 'bank_paid')
                    : 'partial',
                'online_payment_ref'    => $data['payment_ref'] ?? $order->online_payment_ref,
                'payment_proof_path'    => $proofPath,
            ]);

            // Reduce customer khata by what was received (if logged-in customer)
            if ($order->customer) {
                $order->customer->update([
                    'current_balance' => round((float) ($order->customer->current_balance ?? 0) - $amount, 2),
                ]);
            }
        });

        return back()->with('success', $extra > 0
            ? 'Payment of Rs. ' . number_format($amount, 0) . ' received — order fully paid, Rs. ' . number_format($extra, 0) . ' extra added to the customer\'s khata as advance.'
            : ($full
                ? 'Payment of Rs. ' . number_format($amount, 0) . ' received — order fully paid.'
                : 'Payment of Rs. ' . number_format($amount, 0) . ' received — Rs. ' . number_format($due - $amount, 0) . ' still due.'));
    }

    /** Payment filter options on the Online Orders list: key => label. */
    private const PAYMENT_FILTERS = [
        'not_received'    => '⏳ Payment not received yet',
        'proof_submitted' => 'Proof submitted — confirm',
        'partial'         => 'Partially paid',
        'fully_paid'      => '✓ Fully paid',
        'cod'             => 'COD',
        'bank_pending'    => 'Bank pending (no proof)',
        'bank_paid'       => 'Bank paid',
    ];

    /**
     * "not_received" = still has a balance to collect (unpaid or part-paid), and
     * "fully_paid" = nothing left; both ignore cancelled/returned orders. Other
     * keys match the stored online payment status.
     */
    private function applyPaymentFilter($query, string $key): void
    {
        match ($key) {
            'not_received' => $query->where('balance_amount', '>', 0)->whereNotIn('status', ['cancelled', 'returned']),
            'fully_paid'   => $query->where('balance_amount', '<=', 0)->whereNotIn('status', ['cancelled', 'returned']),
            default        => $query->where('online_payment_status', $key),
        };
    }

    /** COD order? (status may already read 'partial' after an earlier part-payment.) */
    private function isCodOrder(Order $order): bool
    {
        if ($order->online_payment_status === 'cod') return true;
        $m = strtolower(trim((string) $order->payment_method));
        return $m === 'cod' || str_contains($m, 'cash on delivery');
    }

    /**
     * An online order is only reachable from its own branch (or "All branches"),
     * so staff can't open another branch's order by editing the URL.
     */
    private function guardBranch(Order $order): void
    {
        abort_unless($order->order_source === 'online', 404);
        if (! $this->isAllBranches()) {
            abort_unless((int) $order->branch_id === (int) $this->branchId(), 404);
        }
    }
}
