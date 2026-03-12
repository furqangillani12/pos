@extends('layouts.admin')

@section('title', 'Supplier Ledger — ' . $supplier->name)

@section('content')
    <div class="container mx-auto px-4 py-6 max-w-5xl">

        {{-- Header --}}
        <div class="flex flex-wrap items-start justify-between gap-3 mb-6">
            <div>
                <a href="{{ route('suppliers.show', $supplier) }}"
                    class="text-sm text-blue-600 hover:underline mb-1 block">&larr; Back to Supplier</a>
                <h1 class="text-2xl font-bold text-gray-800">Supplier Ledger (سپلائر کھاتہ)</h1>
                <p class="text-sm text-gray-500 mt-1">Account statement for <strong>{{ $supplier->name }}</strong>
                    @if($supplier->company_name) · {{ $supplier->company_name }} @endif
                </p>
            </div>
            <button onclick="window.print()"
                class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg text-sm flex items-center gap-2">
                <i class="fas fa-print"></i> Print
            </button>
        </div>

        {{-- Flash --}}
        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-5 flex items-center gap-2">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
            </div>
        @endif

        {{-- Main Layout --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- LEFT: Supplier Info + Payment Form --}}
            <div class="lg:col-span-1 space-y-5">

                {{-- Supplier Info --}}
                <div class="bg-white rounded-lg shadow p-5 border border-gray-200">
                    <h3 class="font-semibold text-gray-700 mb-4 flex items-center gap-2">
                        <i class="fas fa-truck text-blue-400"></i> Supplier Info
                    </h3>
                    <div class="space-y-3 text-sm">
                        <div>
                            <p class="text-xs text-gray-400">Name</p>
                            <p class="font-bold text-gray-800">{{ $supplier->name }}</p>
                            @if($supplier->company_name)
                                <p class="text-xs text-gray-500">{{ $supplier->company_name }}</p>
                            @endif
                        </div>
                        @if ($supplier->phone)
                            <div>
                                <p class="text-xs text-gray-400">Phone</p>
                                <p class="font-medium text-gray-700">{{ $supplier->phone }}</p>
                            </div>
                        @endif
                        <div>
                            <p class="text-xs text-gray-400">Total Purchases</p>
                            <p class="text-xl font-bold text-blue-600">{{ $supplier->purchases->count() }}</p>
                        </div>
                    </div>

                    {{-- Balance Status --}}
                    @php $due = $summary['total_due']; @endphp
                    <div class="mt-4 rounded-lg p-4 text-center
                        {{ $due > 0 ? 'bg-red-50 border border-red-200' : 'bg-green-50 border border-green-200' }}">
                        <p class="text-xs font-medium {{ $due > 0 ? 'text-red-500' : 'text-green-500' }}">
                            {{ $due > 0 ? 'Balance Due (بقایا)' : 'All Clear (حساب برابر)' }}
                        </p>
                        <p class="text-2xl font-bold mt-1 {{ $due > 0 ? 'text-red-600' : 'text-green-600' }}">
                            Rs. {{ number_format($due, 0) }}
                        </p>
                    </div>
                </div>

                {{-- Payment Form --}}
                @if($due > 0)
                <div class="bg-white rounded-lg shadow border border-blue-200">
                    <div class="bg-blue-600 text-white px-5 py-3 rounded-t-lg">
                        <h3 class="font-semibold flex items-center gap-2">
                            <i class="fas fa-hand-holding-usd"></i> Record Payment (ادائیگی)
                        </h3>
                        <p class="text-xs text-blue-200 mt-0.5">Pay supplier for purchases</p>
                    </div>

                    <form method="POST" action="{{ route('suppliers.payment.store', $supplier) }}" class="p-5 space-y-4">
                        @csrf

                        <div class="bg-red-50 border border-red-200 rounded-lg p-3 text-sm">
                            <p class="text-red-600 font-medium">
                                Due Amount: <strong>Rs. {{ number_format($due, 0) }}</strong>
                            </p>
                            <button type="button"
                                onclick="document.getElementById('payAmount').value = {{ $due }}"
                                class="mt-1 text-xs text-red-500 underline hover:text-red-700">
                                &rarr; Click to fill full amount
                            </button>
                        </div>

                        {{-- Against specific purchase (optional) --}}
                        @php
                            $unpaidPurchases = $supplier->purchases->filter(fn($p) => $p->total_amount > $p->paid_amount);
                        @endphp
                        @if($unpaidPurchases->count() > 0)
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Against Purchase (optional)</label>
                            <select name="purchase_id" id="purchaseSelect"
                                class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300"
                                onchange="fillPurchaseBalance()">
                                <option value="">General Payment</option>
                                @foreach($unpaidPurchases as $p)
                                    <option value="{{ $p->id }}" data-due="{{ $p->total_amount - $p->paid_amount }}">
                                        {{ $p->invoice_number }} — Due: Rs. {{ number_format($p->total_amount - $p->paid_amount, 0) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @endif

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">
                                Amount (Rs.) <span class="text-red-500">*</span>
                            </label>
                            <input type="number" id="payAmount" name="amount" min="1" step="0.01" required
                                placeholder="Enter amount..."
                                class="w-full border-2 border-blue-300 rounded-lg px-3 py-2 text-lg font-bold focus:outline-none focus:border-blue-500">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">
                                Payment Method <span class="text-red-500">*</span>
                            </label>
                            <select name="payment_method" required
                                class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300">
                                <option value="cash">Cash</option>
                                <option value="jazzcash">JazzCash</option>
                                <option value="easypaisa">EasyPaisa</option>
                                <option value="bank">Bank Transfer</option>
                                <option value="cheque">Cheque</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">
                                Payment Date <span class="text-red-500">*</span>
                            </label>
                            <input type="date" name="payment_date" value="{{ date('Y-m-d') }}" required
                                class="w-full border rounded-lg px-3 py-2 text-sm">
                        </div>

                        <div>
                            <label class="block text-sm text-gray-600 mb-1">Notes (optional)</label>
                            <input type="text" name="notes" placeholder="e.g. Cheque #, reference..."
                                class="w-full border rounded-lg px-3 py-2 text-sm">
                        </div>

                        <button type="submit"
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-lg font-bold text-base transition">
                            Record Payment
                        </button>
                    </form>
                </div>
                @endif

            </div>

            {{-- RIGHT: Transaction History --}}
            <div class="lg:col-span-2">

                {{-- Summary Cards --}}
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-5">
                    <div class="bg-white rounded-lg shadow p-3 border-l-4 border-blue-500">
                        <p class="text-xs text-gray-500">Total Purchased</p>
                        <p class="text-lg font-bold text-blue-600">Rs. {{ number_format($summary['total_purchased'], 0) }}</p>
                    </div>
                    <div class="bg-white rounded-lg shadow p-3 border-l-4 border-green-500">
                        <p class="text-xs text-gray-500">Total Paid</p>
                        <p class="text-lg font-bold text-green-600">Rs. {{ number_format($summary['total_paid'], 0) }}</p>
                    </div>
                    <div class="bg-white rounded-lg shadow p-3 border-l-4 border-red-500">
                        <p class="text-xs text-gray-500">Balance Due</p>
                        <p class="text-lg font-bold text-red-600">Rs. {{ number_format($summary['total_due'], 0) }}</p>
                    </div>
                    <div class="bg-white rounded-lg shadow p-3 border-l-4 border-purple-500">
                        <p class="text-xs text-gray-500">Payments Made</p>
                        <p class="text-lg font-bold text-purple-600">{{ $summary['payments_count'] }}</p>
                    </div>
                </div>

                {{-- Transaction Table --}}
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="px-5 py-4 border-b bg-gray-50 flex flex-wrap items-center justify-between gap-3">
                        <h3 class="font-semibold text-gray-700">Transaction History (کھاتہ)</h3>
                        <div class="flex items-center gap-3 text-xs">
                            <span class="flex items-center gap-1">
                                <span class="w-3 h-3 rounded-full bg-red-200 inline-block"></span> Purchase
                            </span>
                            <span class="flex items-center gap-1">
                                <span class="w-3 h-3 rounded-full bg-green-200 inline-block"></span> Payment
                            </span>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-100 text-gray-600 uppercase text-xs">
                                <tr>
                                    <th class="px-3 py-3 text-left">Date</th>
                                    <th class="px-3 py-3 text-left">Details</th>
                                    <th class="px-3 py-3 text-right text-red-500">Debit (Purchase)</th>
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
                                        </td>

                                        <td class="px-3 py-3">
                                            @if ($isPayment)
                                                <div class="flex items-center gap-2">
                                                    <span class="text-green-500 text-base">💳</span>
                                                    <div>
                                                        <p class="font-semibold text-green-700">Payment Made</p>
                                                        <p class="text-xs text-gray-400">
                                                            {{ ucfirst(str_replace('_', ' ', $txn['method'])) }}
                                                            @if ($txn['notes']) · {{ $txn['notes'] }} @endif
                                                        </p>
                                                        <p class="text-xs font-mono text-gray-300">{{ $txn['reference'] }}</p>
                                                    </div>
                                                </div>
                                            @else
                                                <div>
                                                    <a href="{{ route('purchases.invoice', $txn['id']) }}"
                                                        class="font-semibold text-blue-600 hover:underline font-mono text-xs">
                                                        {{ $txn['reference'] }}
                                                    </a>
                                                    <p class="text-xs text-gray-500 mt-0.5">
                                                        {{ $txn['items_count'] }} item(s)
                                                        @if($txn['paid'] > 0)
                                                            · Paid at purchase: Rs. {{ number_format($txn['paid'], 0) }}
                                                        @endif
                                                    </p>
                                                </div>
                                            @endif
                                        </td>

                                        <td class="px-3 py-3 text-right {{ !$isPayment ? 'text-red-600 font-semibold' : 'text-gray-200' }}">
                                            @if (!$isPayment)
                                                Rs. {{ number_format($txn['amount'], 0) }}
                                            @else
                                                —
                                            @endif
                                        </td>

                                        <td class="px-3 py-3 text-right {{ ($isPayment || (!$isPayment && $txn['paid'] > 0)) ? 'text-green-600 font-bold' : 'text-gray-200' }}">
                                            @if ($isPayment)
                                                Rs. {{ number_format($txn['amount'], 0) }}
                                            @elseif($txn['paid'] > 0)
                                                Rs. {{ number_format($txn['paid'], 0) }}
                                            @else
                                                —
                                            @endif
                                        </td>

                                        <td class="px-3 py-3 text-right bg-yellow-50 font-bold
                                            {{ $txn['running_balance'] > 0 ? 'text-red-600' : 'text-green-600' }}">
                                            @if ($txn['running_balance'] > 0)
                                                Rs. {{ number_format($txn['running_balance'], 0) }}
                                            @elseif($txn['running_balance'] == 0)
                                                <span class="text-green-500 font-bold">✓ Clear</span>
                                            @else
                                                <span class="text-blue-500">Adv. Rs. {{ number_format(abs($txn['running_balance']), 0) }}</span>
                                            @endif
                                        </td>

                                        <td class="px-3 py-3 text-center">
                                            @if ($isPayment)
                                                <div class="flex items-center justify-center gap-1">
                                                    <a href="{{ route('suppliers.payment.voucher', [$supplier, $txn['id']]) }}"
                                                        class="text-xs text-blue-400 hover:text-blue-600 px-2 py-1 rounded hover:bg-blue-50"
                                                        title="View Voucher">
                                                        <i class="fas fa-file-invoice"></i>
                                                    </a>
                                                    <form method="POST"
                                                        action="{{ route('suppliers.payment.delete', [$supplier, $txn['id']]) }}"
                                                        onsubmit="return confirm('Delete this payment of Rs.{{ number_format($txn['amount'], 0) }}?')">
                                                        @csrf @method('DELETE')
                                                        <button type="submit"
                                                            class="text-xs text-red-400 hover:text-red-600 px-2 py-1 rounded hover:bg-red-50"
                                                            title="Delete Payment">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            @else
                                                <a href="{{ route('purchases.invoice', $txn['id']) }}"
                                                    class="text-blue-400 hover:text-blue-600 text-xs" title="View Invoice">
                                                    <i class="fas fa-file-invoice"></i>
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-4 py-10 text-center text-gray-400">
                                            No transactions found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>

                            @if (count($transactions))
                                <tfoot class="bg-gray-100 font-bold text-sm border-t-2">
                                    <tr>
                                        <td colspan="2" class="px-3 py-3 text-gray-600">Total</td>
                                        <td class="px-3 py-3 text-right text-red-600">Rs. {{ number_format($summary['total_purchased'], 0) }}</td>
                                        <td class="px-3 py-3 text-right text-green-600">Rs. {{ number_format($summary['total_paid'], 0) }}</td>
                                        <td class="px-3 py-3 text-right bg-yellow-100 {{ $summary['total_due'] > 0 ? 'text-red-700' : 'text-green-700' }}">
                                            Rs. {{ number_format($summary['total_due'], 0) }}
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
                .no-print, nav, aside, header, form, button { display: none !important; }
                body { background: white; }
                .shadow { box-shadow: none; }
                .lg\:col-span-1 { display: none; }
                .lg\:col-span-2 { grid-column: span 3; }
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            function fillPurchaseBalance() {
                const sel = document.getElementById('purchaseSelect');
                if (!sel) return;
                const opt = sel.options[sel.selectedIndex];
                const due = opt?.dataset?.due;
                if (due) {
                    document.getElementById('payAmount').value = due;
                }
            }
        </script>
    @endpush

@endsection
