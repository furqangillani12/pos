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

        return view('admin.account-requests.index', compact('requests'));
    }

    public function approve(Request $request, AccountRequest $accountRequest)
    {
        abort_if($accountRequest->status === 'approved', 400, 'Already approved.');

        $data = $request->validate([
            'admin_note'  => 'nullable|string|max:255',
            'admin_proof' => 'nullable|image|mimes:png,jpg,jpeg,webp|max:5120',
        ]);

        $proof = $accountRequest->admin_proof_path;
        if ($request->hasFile('admin_proof')) {
            $proof = $request->file('admin_proof')->store('account-requests', 'public');
        }

        DB::transaction(function () use ($accountRequest, $data, $proof) {
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

            $accountRequest->update([
                'status'           => 'approved',
                'admin_note'       => $data['admin_note'] ?? null,
                'admin_proof_path' => $proof,
            ]);
        });

        return back()->with('success', 'Request approved and balance updated.');
    }

    public function reject(Request $request, AccountRequest $accountRequest)
    {
        $data = $request->validate(['admin_note' => 'nullable|string|max:255']);
        $accountRequest->update(['status' => 'rejected', 'admin_note' => $data['admin_note'] ?? null]);

        return back()->with('success', 'Request rejected.');
    }
}
