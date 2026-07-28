@extends('shop.layouts.app')
@section('title', 'Payment history')
@section('content')
<section class="py-10 sm:py-14">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <a href="{{ route('shop.account') }}" class="text-sm text-gray-500 hover:text-gray-700"><i class="fas fa-arrow-left"></i> Back to account</a>

        <div class="flex items-end justify-between mt-4 mb-6">
            <div>
                <h1 class="display text-2xl sm:text-3xl font-bold">Payment history</h1>
                <p class="text-gray-500 text-sm mt-1">Your payments & withdrawals with screenshots and status.</p>
            </div>
            @php $bal = (float) ($customer->current_balance ?? 0); @endphp
            <div class="text-right">
                <div class="text-[11px] uppercase font-bold text-gray-400">Balance</div>
                @if ($bal > 0)
                    <div class="text-lg font-extrabold text-red-600">{{ shop_price($bal) }} <span class="text-xs font-medium">due</span></div>
                @elseif ($bal < 0)
                    <div class="text-lg font-extrabold text-emerald-600">{{ shop_price(abs($bal)) }} <span class="text-xs font-medium">credit</span></div>
                @else
                    <div class="text-lg font-extrabold text-gray-700">{{ shop_price(0) }}</div>
                @endif
            </div>
        </div>

        @if ($requests->isEmpty())
            <div class="bg-white rounded-2xl border border-gray-100 p-12 text-center">
                <i class="fas fa-receipt text-4xl text-gray-300 mb-3 block"></i>
                <p class="text-gray-500">No payments or withdrawals yet.</p>
                <div class="mt-4 flex justify-center gap-3">
                    <a href="{{ route('shop.account.pay') }}" class="btn btn-primary !py-2 !px-4 !text-xs">Pay balance</a>
                    <a href="{{ route('shop.account.withdraw') }}" class="btn btn-ghost !py-2 !px-4 !text-xs">Withdraw credit</a>
                </div>
            </div>
        @else
            <div class="space-y-3">
                @foreach ($requests as $r)
                    @php
                        $isPayment = $r->type === 'payment';
                        $statusColor = $r->status === 'approved' ? ['#d1fae5','#047857'] : ($r->status === 'rejected' ? ['#fee2e2','#991b1b'] : ['#fef3c7','#92400e']);
                    @endphp
                    <div class="bg-white rounded-2xl border border-gray-100 p-4">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-center gap-3">
                                <span class="w-9 h-9 rounded-full flex items-center justify-center text-white flex-none"
                                      style="background:{{ $isPayment ? 'var(--brand-navy)' : '#0e7490' }};">
                                    <i class="fas {{ $isPayment ? 'fa-arrow-up' : 'fa-arrow-down' }} text-xs"></i>
                                </span>
                                <div>
                                    <div class="font-bold text-gray-900">{{ $isPayment ? 'Payment' : 'Withdrawal' }} · {{ shop_price($r->amount) }}</div>
                                    <div class="text-xs text-gray-400">{{ $r->created_at->format('d M Y, g:i A') }}</div>
                                </div>
                            </div>
                            <span class="chip capitalize" style="background:{{ $statusColor[0] }};color:{{ $statusColor[1] }};">{{ $r->status }}</span>
                        </div>

                        {{-- Extra detail --}}
                        <div class="mt-3 grid sm:grid-cols-2 gap-x-6 gap-y-1 text-xs text-gray-600">
                            @if ($r->sender_name)<div><span class="text-gray-400">Sent by:</span> {{ $r->sender_name }}</div>@endif
                            @if ($r->sender_bank)<div><span class="text-gray-400">From bank:</span> {{ $r->sender_bank }}</div>@endif
                            @if ($r->reference)<div><span class="text-gray-400">Ref:</span> {{ $r->reference }}</div>@endif
                            @if ($r->bank_name)<div><span class="text-gray-400">To bank:</span> {{ $r->bank_name }}</div>@endif
                            @if ($r->account_number)<div><span class="text-gray-400">Account:</span> <span class="font-mono">{{ $r->account_number }}</span></div>@endif
                            @if ($r->account_title)<div><span class="text-gray-400">Title:</span> {{ $r->account_title }}</div>@endif
                        </div>
                        @if ($r->admin_note)
                            <div class="mt-2 text-xs text-gray-500 bg-gray-50 rounded-lg px-3 py-2"><span class="font-semibold">Note:</span> {{ $r->admin_note }}</div>
                        @endif

                        {{-- Screenshots: customer's proof + admin's payout proof --}}
                        @if ($r->proof_path || $r->admin_proof_path)
                            <div class="mt-3 flex flex-wrap gap-3">
                                @if ($r->proof_path)
                                    <a href="{{ asset('storage/' . $r->proof_path) }}" target="_blank" class="block">
                                        <div class="text-[10px] text-gray-400 mb-1">Your receipt</div>
                                        <img src="{{ asset('storage/' . $r->proof_path) }}" alt="Your receipt" class="w-20 h-20 object-cover rounded-lg border border-gray-200 hover:opacity-90">
                                    </a>
                                @endif
                                @if ($r->admin_proof_path)
                                    <a href="{{ asset('storage/' . $r->admin_proof_path) }}" target="_blank" class="block">
                                        <div class="text-[10px] text-gray-400 mb-1">Our proof</div>
                                        <img src="{{ asset('storage/' . $r->admin_proof_path) }}" alt="Our proof" class="w-20 h-20 object-cover rounded-lg border border-gray-200 hover:opacity-90">
                                    </a>
                                @endif
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>

            <div class="mt-6">{{ $requests->links() }}</div>
        @endif
    </div>
</section>
@endsection
