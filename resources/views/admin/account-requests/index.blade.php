@extends('layouts.admin')

@section('content')
<div class="p-6">
    <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
        <div>
            <h1 class="text-2xl font-semibold text-gray-800">Payment & Withdrawal Requests</h1>
            <p class="text-sm text-gray-600 mt-1">Customers ki payment (khata) aur withdrawal (credit) requests — approve karne par balance update ho jayega.</p>
        </div>
        <div class="flex items-center gap-2 text-sm">
            @foreach (['' => 'All', 'payment' => 'Payments', 'withdrawal' => 'Withdrawals'] as $val => $label)
                <a href="{{ route('admin.account-requests.index', array_filter(['type' => $val])) }}"
                   class="px-3 py-1.5 rounded-lg border {{ request('type') === $val || (request('type') === null && $val === '') ? 'bg-gray-900 text-white border-gray-900' : 'border-gray-300 text-gray-600 hover:bg-gray-50' }}">{{ $label }}</a>
            @endforeach
        </div>
    </div>

    @if (session('success'))
        <div class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm rounded-lg px-4 py-2">{{ session('success') }}</div>
    @endif

    <div class="space-y-3">
        @forelse ($requests as $r)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div class="flex-1 min-w-[240px]">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="px-2 py-0.5 rounded-full text-xs font-bold {{ $r->type === 'payment' ? 'bg-blue-100 text-blue-700' : 'bg-emerald-100 text-emerald-700' }}">
                                {{ $r->type === 'payment' ? 'PAYMENT' : 'WITHDRAWAL' }}
                            </span>
                            <span class="text-lg font-extrabold text-gray-900">Rs {{ number_format($r->amount, 0) }}</span>
                            <span class="px-2 py-0.5 rounded-full text-xs font-semibold capitalize
                                {{ $r->status === 'approved' ? 'bg-emerald-100 text-emerald-700' : ($r->status === 'rejected' ? 'bg-red-100 text-red-600' : 'bg-amber-100 text-amber-700') }}">{{ $r->status }}</span>
                        </div>
                        <div class="text-sm text-gray-700">
                            <strong>{{ $r->customer->name ?? 'Customer' }}</strong>
                            @if ($r->customer?->phone) · {{ $r->customer->phone }} @endif
                            · <span class="text-gray-400">{{ $r->created_at->format('d M Y, h:i A') }}</span>
                        </div>
                        @if ($r->type === 'payment')
                            <div class="text-xs text-gray-500 mt-1">
                                Sender: {{ $r->sender_name ?: '—' }} @if($r->sender_bank) · {{ $r->sender_bank }} @endif @if($r->reference) · Ref: {{ $r->reference }} @endif
                            </div>
                            @if ($r->proof_path)
                                <a href="{{ asset('storage/' . $r->proof_path) }}" target="_blank" class="inline-block mt-2">
                                    <img src="{{ asset('storage/' . $r->proof_path) }}" class="h-24 rounded-lg border border-gray-200" alt="proof">
                                </a>
                            @endif
                        @else
                            <div class="text-xs text-gray-500 mt-1">
                                Payout to: <strong>{{ $r->account_title }}</strong> · {{ $r->account_number }} · {{ $r->bank_name }}
                            </div>
                            @if ($r->admin_proof_path)
                                <a href="{{ asset('storage/' . $r->admin_proof_path) }}" target="_blank" class="inline-block mt-2">
                                    <img src="{{ asset('storage/' . $r->admin_proof_path) }}" class="h-24 rounded-lg border border-gray-200" alt="payout proof">
                                </a>
                            @endif
                        @endif
                        @if ($r->admin_note)<div class="text-xs text-gray-400 mt-1">Note: {{ $r->admin_note }}</div>@endif
                    </div>

                    @if ($r->status === 'new')
                        <div class="flex flex-col gap-2 w-full sm:w-64">
                            <form method="POST" action="{{ route('admin.account-requests.approve', $r) }}" enctype="multipart/form-data" class="space-y-2 border border-gray-100 rounded-lg p-3 bg-gray-50">
                                @csrf
                                <input type="text" name="admin_note" placeholder="Note (optional)" class="w-full px-2 py-1.5 border border-gray-300 rounded text-xs">
                                @if ($r->type === 'withdrawal')
                                    <label class="block text-[11px] text-gray-500">Payout screenshot (optional)</label>
                                    <input type="file" name="admin_proof" accept="image/*" class="w-full text-xs">
                                @endif
                                <button class="w-full px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded"
                                        onclick="return confirm('Approve? Customer balance update ho jayega.')">
                                    <i class="fas fa-check"></i> Approve & update balance
                                </button>
                            </form>
                            <form method="POST" action="{{ route('admin.account-requests.reject', $r) }}">
                                @csrf
                                <button class="w-full px-3 py-1.5 border border-red-300 text-red-600 text-xs font-semibold rounded hover:bg-red-50">
                                    <i class="fas fa-times"></i> Reject
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="bg-white rounded-xl border border-gray-200 p-10 text-center text-gray-400">No requests yet.</div>
        @endforelse
    </div>

    <div class="mt-4">{{ $requests->links() }}</div>
</div>
@endsection
