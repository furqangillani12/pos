@extends('shop.layouts.app')
@section('title', 'Pay balance')
@section('content')
<section class="py-10 sm:py-14">
    <div class="max-w-xl mx-auto px-4 sm:px-6 lg:px-8">
        <a href="{{ route('shop.account') }}" class="text-sm text-gray-500 hover:text-gray-700"><i class="fas fa-arrow-left"></i> Back to account</a>

        <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-6 sm:p-8 mt-4">
            <h1 class="display text-2xl font-bold">Pay your balance</h1>
            @php $bal = (float) ($customer->current_balance ?? 0); @endphp
            @if ($bal > 0)
                <div class="mt-3 rounded-xl bg-red-50 border border-red-200 px-4 py-3 text-red-700">
                    <span class="text-xs uppercase font-bold">Pending</span>
                    <div class="text-2xl font-extrabold">{{ shop_price($bal) }}</div>
                </div>
            @endif

            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 text-xs rounded-lg px-3 py-2 mt-4">{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('shop.account.pay.submit') }}" enctype="multipart/form-data" class="space-y-4 mt-5">
                @csrf
                <div>
                    <label class="text-xs font-semibold text-gray-700 mb-1 block">Amount paid (Rs) *</label>
                    <input type="number" step="0.01" min="1" name="amount" required value="{{ old('amount', $bal > 0 ? $bal : '') }}"
                           class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm">
                </div>
                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs font-semibold text-gray-700 mb-1 block">Sender name</label>
                        <input type="text" name="sender_name" value="{{ old('sender_name', $customer->name) }}" class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm">
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-700 mb-1 block">Bank / method</label>
                        <input type="text" name="sender_bank" value="{{ old('sender_bank') }}" placeholder="e.g. JazzCash / HBL" class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm">
                    </div>
                </div>
                <div>
                    <label class="text-xs font-semibold text-gray-700 mb-1 block">Transaction / reference #</label>
                    <input type="text" name="reference" value="{{ old('reference') }}" class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm">
                </div>
                <div>
                    <label class="text-xs font-semibold text-gray-700 mb-1 block">Payment screenshot / receipt *</label>
                    <input type="file" name="proof" accept="image/*" required class="w-full text-sm">
                </div>
                <button class="btn btn-primary btn-block"><i class="fas fa-paper-plane"></i> Submit payment</button>
                <p class="text-[11px] text-gray-400 text-center">Admin approve karne ke baad aap ke khate me kam ho jayega.</p>
            </form>
        </div>

        @if ($recent->isNotEmpty())
            <div class="bg-white rounded-2xl border border-gray-100 p-5 mt-4">
                <h2 class="font-bold text-gray-800 mb-3 text-sm">Recent payments</h2>
                <div class="divide-y divide-gray-100">
                    @foreach ($recent as $r)
                        <div class="py-2 flex items-center justify-between text-sm">
                            <div>
                                <span class="font-semibold">{{ shop_price($r->amount) }}</span>
                                <span class="text-xs text-gray-400">· {{ $r->created_at->format('d M Y') }}</span>
                            </div>
                            <span class="chip capitalize {{ $r->status === 'approved' ? '' : '' }}"
                                  style="background:{{ $r->status === 'approved' ? '#d1fae5' : ($r->status === 'rejected' ? '#fee2e2' : '#fef3c7') }};color:{{ $r->status === 'approved' ? '#047857' : ($r->status === 'rejected' ? '#991b1b' : '#92400e') }};">{{ $r->status }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</section>
@endsection
