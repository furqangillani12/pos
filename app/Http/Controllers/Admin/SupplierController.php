<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use App\Models\SupplierPayment;
use App\Traits\BranchScoped;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    use BranchScoped;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = $this->scopeBranch(Supplier::query());

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('company_name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $suppliers = $query->latest()->paginate(10);
        return view('admin.suppliers.index', compact('suppliers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.suppliers.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string',
            'company_name' => 'nullable|string|max:255',
        ]);

        $branchId = $this->branchId();
        if ($branchId && $branchId !== 'all') {
            $validated['branch_id'] = $branchId;
        }

        Supplier::create($validated);

        return redirect()->route('suppliers.index')
            ->with('success', 'Supplier created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Supplier $supplier)
    {
        return view('admin.suppliers.show', compact('supplier'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Supplier $supplier)
    {
        return view('admin.suppliers.edit', compact('supplier'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Supplier $supplier)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string',
            'company_name' => 'nullable|string|max:255',
        ]);

        $supplier->update($validated);

        return redirect()->route('suppliers.index')
            ->with('success', 'Supplier updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Supplier $supplier)
    {
        $supplier->delete();

        return redirect()->route('suppliers.index')
            ->with('success', 'Supplier deleted successfully.');
    }

    /**
     * Show supplier ledger (khata) with all purchases and payments.
     */
    public function ledger(Supplier $supplier)
    {
        $supplier->load(['purchases.items.product', 'payments']);

        $purchases = $supplier->purchases()->orderBy('purchase_date')->orderBy('id')->get();
        $payments = $supplier->payments()->orderBy('payment_date')->orderBy('id')->get();

        // Build unified transaction list
        $transactions = [];

        foreach ($purchases as $p) {
            // Subtract any linked supplier payments to get original paid at purchase time
            // (storePayment adds to purchase.paid_amount, so we undo that to avoid double-counting)
            $linkedPayments = $payments->where('purchase_id', $p->id)->sum('amount');
            $originalPaid = $p->paid_amount - $linkedPayments;

            $transactions[] = [
                'type'      => 'purchase',
                'id'        => $p->id,
                'date'      => $p->purchase_date,
                'reference' => $p->invoice_number,
                'amount'    => $p->total_amount,
                'paid'      => $originalPaid,
                'items_count' => $p->items->count(),
                'notes'     => $p->notes,
                'created_at' => $p->created_at,
            ];
        }

        foreach ($payments as $pay) {
            $transactions[] = [
                'type'      => 'payment',
                'id'        => $pay->id,
                'date'      => $pay->payment_date,
                'reference' => $pay->payment_number,
                'amount'    => $pay->amount,
                'method'    => $pay->payment_method,
                'notes'     => $pay->notes,
                'purchase_id' => $pay->purchase_id,
                'created_at' => $pay->created_at,
            ];
        }

        // Sort by date, then by created_at
        usort($transactions, function ($a, $b) {
            $dateCompare = strtotime($a['date']) - strtotime($b['date']);
            if ($dateCompare !== 0) return $dateCompare;
            return strtotime($a['created_at']) - strtotime($b['created_at']);
        });

        // Calculate running balance
        $runningBalance = 0;
        foreach ($transactions as &$txn) {
            if ($txn['type'] === 'purchase') {
                // Debit: full amount, Credit: original paid at purchase time
                $runningBalance += ($txn['amount'] - $txn['paid']);
            } else {
                // Supplier payment reduces balance
                $runningBalance -= $txn['amount'];
            }
            $txn['running_balance'] = $runningBalance;
        }
        unset($txn);

        // Newest first
        $transactions = array_reverse($transactions);

        // Summary — no double-counting
        $totalPurchased = $purchases->sum('total_amount');
        $linkedPaymentsTotal = $payments->whereNotNull('purchase_id')->sum('amount');
        $originalPaidOnPurchases = $purchases->sum('paid_amount') - $linkedPaymentsTotal;
        $totalSupplierPayments = $payments->sum('amount');
        $totalPaid = $originalPaidOnPurchases + $totalSupplierPayments;
        $balance = $totalPurchased - $totalPaid; // positive = we owe, negative = advance

        $summary = [
            'total_purchased' => $totalPurchased,
            'total_paid'      => $totalPaid,
            'total_due'       => max(0, $balance),
            'advance'         => $balance < 0 ? abs($balance) : 0,
            'balance'         => $balance,
            'payments_count'  => $payments->count(),
        ];

        return view('admin.suppliers.ledger', compact('supplier', 'transactions', 'summary'));
    }

    /**
     * Record a payment to supplier.
     */
    public function storePayment(Request $request, Supplier $supplier)
    {
        $validated = $request->validate([
            'amount'         => 'required|numeric|min:1',
            'payment_method' => 'required|string',
            'payment_date'   => 'required|date',
            'purchase_id'    => 'nullable|exists:purchases,id',
            'notes'          => 'nullable|string|max:500',
        ]);

        $branchId = $this->branchId();

        $payment = SupplierPayment::create([
            'payment_number' => SupplierPayment::generatePaymentNumber(),
            'supplier_id'    => $supplier->id,
            'purchase_id'    => $validated['purchase_id'] ?? null,
            'branch_id'      => $branchId && $branchId !== 'all' ? $branchId : null,
            'amount'         => $validated['amount'],
            'payment_date'   => $validated['payment_date'],
            'payment_method' => $validated['payment_method'],
            'notes'          => $validated['notes'] ?? null,
            'created_by'     => auth()->id(),
        ]);

        // If payment is against a specific purchase, update its paid_amount
        if ($payment->purchase_id) {
            $purchase = $payment->purchase;
            $purchase->paid_amount += $payment->amount;
            if ($purchase->paid_amount >= $purchase->total_amount) {
                $purchase->payment_status = 'paid';
            } else {
                $purchase->payment_status = 'partial';
            }
            $purchase->save();
        }

        return redirect()->route('suppliers.payment.voucher', [$supplier, $payment])
            ->with('success', 'Payment of Rs. ' . number_format($validated['amount'], 0) . ' recorded successfully.');
    }

    /**
     * Delete a supplier payment.
     */
    public function deletePayment(Supplier $supplier, SupplierPayment $payment)
    {
        // Reverse purchase paid_amount if linked
        if ($payment->purchase_id) {
            $purchase = $payment->purchase;
            if ($purchase) {
                $purchase->paid_amount = max(0, $purchase->paid_amount - $payment->amount);
                if ($purchase->paid_amount <= 0) {
                    $purchase->payment_status = 'unpaid';
                } elseif ($purchase->paid_amount < $purchase->total_amount) {
                    $purchase->payment_status = 'partial';
                }
                $purchase->save();
            }
        }

        $payment->delete();

        return redirect()->route('suppliers.ledger', $supplier)
            ->with('success', 'Payment reversed successfully.');
    }

    /**
     * Show payment voucher.
     */
    public function paymentVoucher(Supplier $supplier, SupplierPayment $payment)
    {
        // Calculate balance before this payment
        $totalPurchased = $supplier->purchases()->sum('total_amount');
        $totalPaidOnPurchases = $supplier->purchases()->sum('paid_amount');
        $totalSupplierPayments = SupplierPayment::where('supplier_id', $supplier->id)
            ->where('id', '<=', $payment->id)
            ->sum('amount');
        $totalSupplierPaymentsBefore = SupplierPayment::where('supplier_id', $supplier->id)
            ->where('id', '<', $payment->id)
            ->sum('amount');

        // If this payment was linked to a purchase and updated its paid_amount,
        // we need to subtract it from totalPaidOnPurchases for "before" calculation
        $paidOnPurchasesAdjusted = $totalPaidOnPurchases;
        if ($payment->purchase_id) {
            $paidOnPurchasesAdjusted -= $payment->amount;
        }

        $balanceBefore = $totalPurchased - $paidOnPurchasesAdjusted - $totalSupplierPaymentsBefore;
        $balanceAfter = $balanceBefore - $payment->amount;

        return view('admin.suppliers.payment-voucher', compact('supplier', 'payment', 'balanceBefore', 'balanceAfter'));
    }
}
