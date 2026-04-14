@extends('layouts.admin')

@section('title', 'Customer Khata — ' . $customer->name)

@section('content')
    <div class="container mx-auto px-4 py-6 max-w-5xl">

        {{-- ── Header ── --}}
        <div class="flex flex-wrap items-start justify-between gap-3 mb-6">
            <div>
                <a href="{{ route('admin.customers.show', $customer) }}"
                    class="text-sm text-blue-600 hover:underline mb-1 block">← Back to Customer</a>
                <h1 class="text-2xl font-bold text-gray-800">📒 Customer Khata</h1>
                <p class="text-sm text-gray-500 mt-1">Account statement for <strong>{{ $customer->name }}</strong>
                    <br><span class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($fromDate)->format('d M Y') }} — {{ \Carbon\Carbon::parse($toDate)->format('d M Y') }}</span>
                </p>
            </div>
            <div class="flex gap-2 flex-wrap">
                <a href="{{ route('admin.customers.khata', ['customer' => $customer->id, 'export' => 'csv'] + request()->query()) }}"
                    class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm flex items-center gap-2">
                    <i class="fas fa-file-csv"></i> Export CSV
                </a>
                <button onclick="window.print()"
                    class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg text-sm flex items-center gap-2">
                    <i class="fas fa-print"></i> Print
                </button>
            </div>
        </div>

        {{-- Flash Messages --}}
        @if (session('success'))
            <div
                class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-5 flex items-center gap-2">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-5">
                {{ session('error') }}
            </div>
        @endif

        {{-- ── MAIN LAYOUT: 2 columns on large screens ── --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- ════════════════════════════════════════
             LEFT COLUMN: Customer Info + Payment Form
             ════════════════════════════════════════ --}}
            <div class="lg:col-span-1 space-y-5">

                {{-- Customer Info Card --}}
                <div class="bg-white rounded-lg shadow p-5 border border-gray-200">
                    <h3 class="font-semibold text-gray-700 mb-4 flex items-center gap-2">
                        <i class="fas fa-user-circle text-blue-400"></i> Customer Info
                    </h3>
                    <div class="space-y-3 text-sm">
                        <div>
                            <p class="text-xs text-gray-400">Name</p>
                            <p class="font-bold text-gray-800">{{ $customer->name }}</p>
                            <p class="text-xs text-gray-400 capitalize">{{ $customer->customer_type }}</p>
                        </div>
                        @if ($customer->phone)
                            <div>
                                <p class="text-xs text-gray-400">Phone</p>
                                <p class="font-medium text-gray-700">{{ $customer->phone }}</p>
                            </div>
                        @endif
                        <div>
                            <p class="text-xs text-gray-400">Total Orders</p>
                            <p class="text-xl font-bold text-blue-600">{{ $orders->count() }}</p>
                        </div>
                    </div>

                    {{-- Balance Status --}}
                    @php $balance = $customer->current_balance ?? 0; @endphp
                    <div
                        class="mt-4 rounded-lg p-4 text-center
                    {{ $balance > 0 ? 'bg-red-50 border border-red-200' : ($balance < 0 ? 'bg-blue-50 border border-blue-200' : 'bg-green-50 border border-green-200') }}">
                        <p
                            class="text-xs font-medium
                        {{ $balance > 0 ? 'text-red-500' : ($balance < 0 ? 'text-blue-500' : 'text-green-500') }}">
                            {{ $balance > 0 ? '⚠️ Balance Due' : ($balance < 0 ? '✅ Advance Credit' : '✅ Account Clear') }}
                        </p>
                        <p
                            class="text-2xl font-bold mt-1
                        {{ $balance > 0 ? 'text-red-600' : ($balance < 0 ? 'text-blue-600' : 'text-green-600') }}">
                            Rs. {{ number_format(abs($balance), 0) }}
                        </p>
                    </div>
                </div>

                {{-- ══════════════════════════════════════
                 RECEIVE PAYMENT FORM
                 ══════════════════════════════════════ --}}
                <div class="bg-white rounded-lg shadow border border-blue-200">
                    <div class="bg-blue-600 text-white px-5 py-3 rounded-t-lg">
                        <h3 class="font-semibold flex items-center gap-2">
                            <i class="fas fa-hand-holding-usd"></i> Receive Payment (رقم وصول کریں)
                        </h3>
                        <p class="text-xs text-blue-200 mt-0.5">Record full or partial payment from customer</p>
                    </div>

                    <form method="POST" action="{{ route('admin.customers.khata.payment', $customer) }}"
                        class="p-5 space-y-4">
                        @csrf

                        {{-- Current Balance Reminder --}}
                        @if ($balance > 0)
                            <div class="bg-red-50 border border-red-200 rounded-lg p-3 text-sm">
                                <p class="text-red-600 font-medium">
                                    Customer owes: <strong>Rs. {{ number_format($balance, 0) }}</strong>
                                </p>
                                <button type="button"
                                    onclick="document.getElementById('payAmount').value = {{ $balance }}"
                                    class="mt-1 text-xs text-red-500 underline hover:text-red-700">
                                    → Click to fill full amount
                                </button>
                            </div>
                        @elseif($balance == 0)
                            <div class="bg-green-50 border border-green-200 rounded-lg p-3 text-sm text-green-700">
                                ✅ Account is currently settled. Any payment will create advance credit.
                            </div>
                        @endif

                        {{-- Amount --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">
                                Amount Received (Rs.) <span class="text-red-500">*</span>
                            </label>
                            <input type="number" id="payAmount" name="amount" min="1" step="0.01" required
                                placeholder="Enter amount..."
                                class="w-full border-2 border-blue-300 rounded-lg px-3 py-2 text-lg font-bold focus:outline-none focus:border-blue-500"
                                oninput="calcRemaining(this.value)">

                            {{-- Live remaining preview --}}
                            <div id="remainingPreview" class="mt-2 text-sm hidden">
                                <div class="bg-gray-50 rounded-lg p-2 space-y-1">
                                    <div class="flex justify-between">
                                        <span class="text-gray-500">Current Balance:</span>
                                        <span class="font-medium text-red-600">Rs. {{ number_format($balance, 0) }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-500">Paying:</span>
                                        <span class="font-medium text-green-600" id="payingDisplay">Rs. 0</span>
                                    </div>
                                    <div class="flex justify-between border-t pt-1">
                                        <span class="font-semibold">After Payment:</span>
                                        <span class="font-bold" id="afterDisplay">Rs. 0</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Payment Method --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">
                                Payment Method <span class="text-red-500">*</span>
                            </label>
                            <select name="payment_method" required
                                class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300">
                                <option value="">Select method...</option>
                                @foreach($paymentMethods as $pm)
                                    @if($pm->name !== 'pending')
                                        <option value="{{ $pm->name }}">{{ $pm->label }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>

                        {{-- Payment Date --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">
                                Payment Date <span class="text-red-500">*</span>
                            </label>
                            <input type="date" name="payment_date" value="{{ date('Y-m-d') }}" required
                                class="w-full border rounded-lg px-3 py-2 text-sm">
                        </div>

                        {{-- Notes --}}
                        <div>
                            <label class="block text-sm text-gray-600 mb-1">Notes (optional)</label>
                            <input type="text" name="notes" placeholder="e.g. Paid via JazzCash, cheque no..."
                                class="w-full border rounded-lg px-3 py-2 text-sm">
                        </div>

                        <button type="submit"
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-lg font-bold text-base transition">
                            ✅ Record Payment
                        </button>
                    </form>
                </div>

            </div>

            {{-- ════════════════════════════════════════
             RIGHT COLUMN: Transaction History
             ════════════════════════════════════════ --}}
            <div class="lg:col-span-2">

                {{-- Period Summary Cards --}}
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-5">
                    <div class="bg-white rounded-lg shadow p-3 border-l-4 border-blue-500">
                        <p class="text-xs text-gray-500">Total Billed</p>
                        <p class="text-lg font-bold text-blue-600">Rs. {{ number_format($summary['total_billed'], 0) }}</p>
                    </div>
                    <div class="bg-white rounded-lg shadow p-3 border-l-4 border-green-500">
                        <p class="text-xs text-gray-500">Total Paid</p>
                        <p class="text-lg font-bold text-green-600">Rs. {{ number_format($summary['total_paid'], 0) }}</p>
                    </div>
                    <div class="bg-white rounded-lg shadow p-3 border-l-4 border-red-500">
                        <p class="text-xs text-gray-500">Outstanding</p>
                        <p class="text-lg font-bold text-red-600">Rs. {{ number_format($summary['total_balance'], 0) }}
                        </p>
                    </div>
                    <div class="bg-white rounded-lg shadow p-3 border-l-4 border-purple-500">
                        <p class="text-xs text-gray-500">Payments Made</p>
                        <p class="text-lg font-bold text-purple-600">{{ $summary['payments_count'] }}</p>
                    </div>
                </div>

                {{-- Date Filter --}}
                <form method="GET" action="{{ route('admin.customers.khata', $customer) }}"
                    class="bg-white rounded-lg shadow p-3 mb-5 flex flex-wrap gap-3 items-end">
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">From Date</label>
                        <input type="date" name="from_date" value="{{ $fromDate }}"
                            class="border rounded-lg px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">To Date</label>
                        <input type="date" name="to_date" value="{{ $toDate }}"
                            class="border rounded-lg px-3 py-2 text-sm">
                    </div>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg text-sm">
                        Filter
                    </button>
                    <a href="{{ route('admin.customers.khata', $customer) }}"
                        class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg text-sm">
                        Reset
                    </a>
                </form>

                {{-- ── Unified Transaction History ── --}}
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="px-5 py-4 border-b bg-gray-50 flex flex-wrap items-center justify-between gap-3">
                        <h3 class="font-semibold text-gray-700">Transaction History (کھاتہ)</h3>
                        <div class="flex items-center gap-3 text-xs">
                            <span class="flex items-center gap-1">
                                <span class="w-3 h-3 rounded-full bg-blue-200 inline-block"></span> Sale Bill
                            </span>
                            <span class="flex items-center gap-1">
                                <span class="w-3 h-3 rounded-full bg-green-200 inline-block"></span> Payment Received
                            </span>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-100 text-gray-600 uppercase text-xs">
                                <tr>
                                    <th class="px-3 py-3 text-left">Date</th>
                                    <th class="px-3 py-3 text-left">Details</th>
                                    <th class="px-3 py-3 text-right text-red-500">Debit (Bill)</th>
                                    <th class="px-3 py-3 text-right text-green-600">Credit (Paid)</th>
                                    <th class="px-3 py-3 text-right bg-yellow-50">Balance</th>
                                    <th class="px-3 py-3 text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">

                                @forelse($transactions as $txn)
                                    @php
                                        $isPayment = $txn['type'] === 'payment';
                                        $rowClass = $isPayment ? 'bg-green-50/50' : '';
                                    @endphp
                                    <tr class="hover:bg-gray-50 transition {{ $rowClass }}">

                                        <td class="px-3 py-3 text-xs text-gray-500">
                                            {{ \Carbon\Carbon::parse($txn['date'])->format('d M Y') }}
                                            @if (!$isPayment)
                                                <br><span
                                                    class="text-gray-300">{{ \Carbon\Carbon::parse($txn['date'])->format('h:i A') }}</span>
                                            @endif
                                        </td>

                                        <td class="px-3 py-3">
                                            @if ($isPayment)
                                                {{-- Payment Row --}}
                                                <div class="flex items-center gap-2">
                                                    <div>
                                                        <p class="font-semibold text-green-700">Payment Received</p>
                                                        <p class="text-xs text-gray-500">
                                                            {{ ucfirst(str_replace('_', ' ', $txn['method'] ?? '')) }}
                                                            @if (!empty($txn['notes']))
                                                                — {{ $txn['notes'] }}
                                                            @endif
                                                        </p>
                                                    </div>
                                                </div>
                                            @else
                                                {{-- Order Row --}}
                                                <div>
                                                    <a href="{{ route('admin.pos.receipt', $txn['id']) }}"
                                                        target="_blank"
                                                        class="font-semibold text-blue-600 hover:underline font-mono text-xs">
                                                        {{ $txn['reference'] }}
                                                    </a>
                                                    <p class="text-xs text-gray-500 mt-0.5">
                                                        {{ $txn['items_count'] }} item(s)
                                                    </p>
                                                </div>
                                            @endif
                                        </td>

                                        <td
                                            class="px-3 py-3 text-right {{ !$isPayment ? 'text-red-600 font-semibold' : 'text-gray-200' }}">
                                            @if (!$isPayment)
                                                Rs. {{ number_format($txn['amount'], 0) }}
                                            @else
                                                —
                                            @endif
                                        </td>

                                        <td
                                            class="px-3 py-3 text-right {{ $isPayment ? 'text-green-600 font-bold' : ($txn['paid'] > 0 ? 'text-green-500' : 'text-gray-200') }}">
                                            @if ($isPayment)
                                                Rs. {{ number_format($txn['amount'], 0) }}
                                            @elseif($txn['paid'] > 0)
                                                Rs. {{ number_format($txn['paid'], 0) }}
                                            @else
                                                —
                                            @endif
                                        </td>

                                        <td
                                            class="px-3 py-3 text-right bg-yellow-50 font-bold
                                    {{ $txn['running_balance'] > 0 ? 'text-red-600' : ($txn['running_balance'] < 0 ? 'text-blue-600' : 'text-green-600') }}">
                                            @if ($txn['running_balance'] > 0)
                                                Rs. {{ number_format($txn['running_balance'], 0) }}
                                            @elseif($txn['running_balance'] < 0)
                                                <span class="text-xs text-blue-500">Adv.</span>
                                                Rs. {{ number_format(abs($txn['running_balance']), 0) }}
                                            @else
                                                <span class="text-green-500 font-bold">✓ Clear</span>
                                            @endif
                                        </td>

                                        <td class="px-3 py-3 text-center">
                                            @if ($isPayment)
                                                <div class="flex items-center justify-center gap-1">
                                                    {{-- View Voucher --}}
                                                    <a href="{{ route('admin.customers.khata.payment.voucher', [$customer, $txn['id']]) }}"
                                                        class="text-xs text-blue-400 hover:text-blue-600 px-2 py-1 rounded hover:bg-blue-50"
                                                        title="View Payment Voucher">
                                                        <i class="fas fa-file-invoice"></i>
                                                    </a>
                                                    {{-- Delete button --}}
                                                    <form method="POST"
                                                        action="{{ route('admin.customers.khata.payment.delete', [$customer, $txn['id']]) }}"
                                                        onsubmit="return confirm('Delete this payment of Rs.{{ number_format($txn['amount'], 0) }}? This will add the amount back to the balance.')">
                                                        @csrf @method('DELETE')
                                                        <button type="submit"
                                                            class="text-xs text-red-400 hover:text-red-600 px-2 py-1 rounded hover:bg-red-50"
                                                            title="Delete / Reverse Payment">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            @else
                                                <a href="{{ route('admin.pos.receipt', $txn['id']) }}" target="_blank"
                                                    class="text-blue-400 hover:text-blue-600 text-xs"
                                                    title="View Receipt">
                                                    <i class="fas fa-receipt"></i>
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-4 py-10 text-center text-gray-400">
                                            No transactions found for this period.
                                        </td>
                                    </tr>
                                @endforelse

                            </tbody>

                            @if (count($transactions))
                                <tfoot class="bg-gray-100 font-bold text-sm border-t-2">
                                    <tr>
                                        <td colspan="2" class="px-3 py-3 text-gray-600">Period Total</td>
                                        <td class="px-3 py-3 text-right text-red-600">Rs.
                                            {{ number_format($summary['total_billed'], 0) }}</td>
                                        <td class="px-3 py-3 text-right text-green-600">Rs.
                                            {{ number_format($summary['total_paid'], 0) }}
                                        </td>
                                        <td
                                            class="px-3 py-3 text-right bg-yellow-100 {{ ($customer->current_balance ?? 0) > 0 ? 'text-red-700' : 'text-green-700' }}">
                                            Rs. {{ number_format(abs($customer->current_balance ?? 0), 0) }}
                                        </td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            @endif
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>

    @push('styles')
        <style>
            @media print {
                .no-print,
                nav,
                aside,
                header,
                form,
                button,
                a[href],
                .flex.gap-2.flex-wrap {
                    display: none !important;
                }

                body {
                    background: white;
                    font-size: 11px;
                    -webkit-print-color-adjust: exact;
                    print-color-adjust: exact;
                }

                .shadow {
                    box-shadow: none !important;
                }

                .lg\:col-span-1 {
                    display: none !important;
                }

                .lg\:col-span-2 {
                    grid-column: span 3 !important;
                }

                .container {
                    max-width: 100% !important;
                    padding: 0 !important;
                }

                /* Print header */
                h1 { font-size: 16px !important; }
                p.text-sm { font-size: 11px !important; }

                /* Clean table for print */
                table {
                    width: 100% !important;
                    border-collapse: collapse !important;
                }
                table th, table td {
                    border: 1px solid #d1d5db !important;
                    padding: 4px 6px !important;
                    font-size: 10px !important;
                }
                table thead {
                    background: #f3f4f6 !important;
                }

                /* Summary cards for print */
                .grid.grid-cols-2 {
                    display: flex !important;
                    gap: 8px !important;
                }
                .grid.grid-cols-2 > div {
                    flex: 1 !important;
                    border: 1px solid #d1d5db !important;
                    padding: 6px !important;
                }

                /* Remove rounded corners and shadows */
                .rounded-lg { border-radius: 0 !important; }

                /* Footer totals */
                tfoot td { font-weight: bold !important; }
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            function calcRemaining(value) {
                const amount = parseFloat(value) || 0;
                const balance = {{ $customer->current_balance ?? 0 }};
                const after = balance - amount;
                const preview = document.getElementById('remainingPreview');
                const paying = document.getElementById('payingDisplay');
                const afterEl = document.getElementById('afterDisplay');

                if (amount > 0) {
                    preview.classList.remove('hidden');
                    paying.textContent = 'Rs. ' + amount.toLocaleString('en-PK');
                    afterEl.textContent = 'Rs. ' + Math.abs(after).toLocaleString('en-PK') + (after < 0 ? ' (Advance)' : (
                        after === 0 ? ' ✅ Settled' : ' remaining'));
                    afterEl.className = 'font-bold ' + (after > 0 ? 'text-red-600' : 'text-green-600');
                } else {
                    preview.classList.add('hidden');
                }
            }
        </script>
    @endpush

@endsection
