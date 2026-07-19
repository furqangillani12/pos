@extends('shop.layouts.app')
@section('title', 'Withdraw credit')
@section('content')
<section class="py-10 sm:py-14">
    <div class="max-w-xl mx-auto px-4 sm:px-6 lg:px-8">
        <a href="{{ route('shop.account') }}" class="text-sm text-gray-500 hover:text-gray-700"><i class="fas fa-arrow-left"></i> Back to account</a>

        <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-6 sm:p-8 mt-4">
            <h1 class="display text-2xl font-bold">Withdraw your credit</h1>
            <div class="mt-3 rounded-xl bg-emerald-50 border border-emerald-200 px-4 py-3 text-emerald-700">
                <span class="text-xs uppercase font-bold">Available credit</span>
                <div class="text-2xl font-extrabold">{{ shop_price($credit) }}</div>
            </div>

            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 text-xs rounded-lg px-3 py-2 mt-4">{{ $errors->first() }}</div>
            @endif

            @if ($credit <= 0)
                <p class="text-sm text-gray-500 mt-5">Abhi aap ke paas koi withdrawable credit nahi hai.</p>
            @else
                <form method="POST" action="{{ route('shop.account.withdraw.submit') }}" class="space-y-4 mt-5">
                    @csrf
                    <div>
                        <label class="text-xs font-semibold text-gray-700 mb-1 block">Amount to withdraw (Rs) *</label>
                        <input type="number" step="0.01" min="1" max="{{ $credit }}" name="amount" required value="{{ old('amount', $credit) }}"
                               class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm">
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-700 mb-1 block">Account title *</label>
                        <input type="text" name="account_title" required value="{{ old('account_title', $customer->name) }}" class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm">
                    </div>
                    <div class="grid sm:grid-cols-2 gap-4">
                        <div>
                            <label class="text-xs font-semibold text-gray-700 mb-1 block">Account / IBAN number *</label>
                            <input type="text" name="account_number" required value="{{ old('account_number') }}" class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm">
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-gray-700 mb-1 block">Bank name *</label>
                            <input type="text" name="bank_name" required value="{{ old('bank_name') }}" placeholder="e.g. HBL / Easypaisa" class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm">
                        </div>
                    </div>
                    <button class="btn btn-primary btn-block" style="background:#059669;"><i class="fas fa-money-bill-wave"></i> Request withdrawal</button>
                    <p class="text-[11px] text-gray-400 text-center">Admin approve karke aap ke account me raqam bhej dega.</p>
                </form>
            @endif
        </div>

        @if ($recent->isNotEmpty())
            <div class="bg-white rounded-2xl border border-gray-100 p-5 mt-4">
                <h2 class="font-bold text-gray-800 mb-3 text-sm">Recent withdrawals</h2>
                <div class="divide-y divide-gray-100">
                    @foreach ($recent as $r)
                        <div class="py-2 flex items-center justify-between text-sm">
                            <div>
                                <span class="font-semibold">{{ shop_price($r->amount) }}</span>
                                <span class="text-xs text-gray-400">· {{ $r->bank_name }} · {{ $r->created_at->format('d M Y') }}</span>
                            </div>
                            <span class="chip capitalize"
                                  style="background:{{ $r->status === 'approved' ? '#d1fae5' : ($r->status === 'rejected' ? '#fee2e2' : '#fef3c7') }};color:{{ $r->status === 'approved' ? '#047857' : ($r->status === 'rejected' ? '#991b1b' : '#92400e') }};">{{ $r->status }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</section>
@endsection
