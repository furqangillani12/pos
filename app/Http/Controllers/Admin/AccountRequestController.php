<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AccountRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AccountRequestController extends Controller
{
    public function index(Request $request)
    {
        $requests = AccountRequest::with('customer:id,name,phone,current_balance')
            ->when($request->input('type'), fn ($q, $t) => $q->where('type', $t))
            ->when($request->input('status'), fn ($q, $s) => $q->where('status', $s))
            ->latest()
            ->paginate(30)
            ->withQueryString();

        // Date each approved request was paid = its khata entry's payment date.
        $paidOn = \App\Models\Payment::whereIn('reference_number', $requests->getCollection()->map(fn ($r) => 'REQ-' . $r->id))
            ->pluck('payment_date', 'reference_number');

        return view('admin.account-requests.index', compact('requests', 'paidOn'));
    }

    public function approve(Request $request, AccountRequest $accountRequest)
    {
        abort_if($accountRequest->status === 'approved', 400, 'Already approved.');

        $data = $request->validate([
            'admin_note'  => 'nullable|string|max:255',
            'admin_proof' => 'nullable|image|mimes:png,jpg,jpeg,webp|max:5120',
            'paid_on'     => 'nullable|date|before_or_equal:today',
        ]);
        $paidOn = !empty($data['paid_on']) ? \Carbon\Carbon::parse($data['paid_on']) : now();

        $proof = $accountRequest->admin_proof_path;
        if ($request->hasFile('admin_proof')) {
            $proof = $request->file('admin_proof')->store('account-requests', 'public');
        }

        DB::transaction(function () use ($accountRequest, $data, $proof, $paidOn) {
            $customer = $accountRequest->customer;
            $amount   = (float) $accountRequest->amount;

            if ($customer) {
                if ($accountRequest->type === 'payment') {
                    // Customer paid down their khata → balance decreases.
                    $customer->update(['current_balance' => round((float) $customer->current_balance - $amount, 2)]);
                } else {
                    // Withdrawal released → their credit (negative balance) moves toward 0.
                    $customer->update(['current_balance' => round((float) $customer->current_balance + $amount, 2)]);
                }
            }

            // Leave a khata entry so the statement shows this movement (and the
            // payment is allocated to the customer's oldest unpaid bills).
            if ($customer) {
                \App\Services\KhataService::recordAccountRequest($accountRequest, $paidOn);
            }

            $accountRequest->update([
                'status'           => 'approved',
                'admin_note'       => $data['admin_note'] ?? null,
                'admin_proof_path' => $proof,
            ]);
        });

        return back()->with('success', 'Request approved and balance updated (paid on ' . $paidOn->format('d M Y') . ').');
    }

    /** Change the date an approved request was paid (its khata entry's date). */
    public function updateDate(Request $request, AccountRequest $accountRequest)
    {
        abort_unless($accountRequest->status === 'approved', 400, 'Only approved requests have a paid date.');
        $data = $request->validate(['paid_on' => 'required|date|before_or_equal:today']);

        $payment = \App\Models\Payment::where('reference_number', 'REQ-' . $accountRequest->id)->first();
        if (!$payment) {
            return back()->with('error', 'No khata entry found for this request.');
        }
        $payment->update(['payment_date' => $data['paid_on']]);

        return back()->with('success', 'Paid date changed to ' . \Carbon\Carbon::parse($data['paid_on'])->format('d M Y') . '.');
    }

    public function reject(Request $request, AccountRequest $accountRequest)
    {
        $data = $request->validate(['admin_note' => 'nullable|string|max:255']);
        $accountRequest->update(['status' => 'rejected', 'admin_note' => $data['admin_note'] ?? null]);

        return back()->with('success', 'Request rejected.');
    }
}
