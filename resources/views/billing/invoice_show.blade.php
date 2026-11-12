<x-app-layout>
    <div class="max-w-5xl mx-auto space-y-6">

        <div class="flex flex-wrap items-center justify-between gap-3 print:hidden">
            <a href="{{ route('billing.index') }}" class="text-xs font-semibold text-blue-600 hover:underline">&larr; Back to Billing</a>
            <button onclick="window.print()" class="px-4 py-2 rounded-xl bg-slate-900 text-white text-xs font-semibold hover:bg-slate-800 shadow-sm">
                Print / Save as PDF
            </button>
        </div>

        <!-- Printable GST Tax Invoice -->
        <div class="bg-white rounded-2xl p-6 sm:p-8 shadow-sm border border-slate-200 text-xs">
            <div class="flex flex-col sm:flex-row justify-between gap-4 pb-6 border-b border-slate-200">
                <div>
                    <div class="text-xl font-black text-slate-900 tracking-tight">{{ $company['name'] }}</div>
                    <div class="text-slate-500 mt-1">{{ $company['address'] }}</div>
                    <div class="text-slate-700 font-semibold mt-1">GSTIN: {{ $company['gstin'] }}</div>
                    <div class="text-slate-500">Helpline: {{ $company['phone'] }} &middot; {{ $company['email'] }}</div>
                </div>
                <div class="sm:text-right">
                    <div class="inline-block px-3 py-1 rounded-lg bg-slate-900 text-white font-bold uppercase tracking-wider text-[11px]">TAX INVOICE</div>
                    <div class="text-lg font-black text-blue-600 mt-2">{{ $invoice->invoice_number }}</div>
                    <div class="text-slate-600">Invoice Date: <span class="font-bold">{{ $invoice->invoice_date->format('d M Y') }}</span></div>
                    <div class="text-slate-600">Due Date: <span class="font-bold">{{ $invoice->due_date?->format('d M Y') ?? '-' }}</span></div>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 py-5 border-b border-slate-100">
                <div>
                    <div class="text-[10px] font-bold uppercase text-slate-400">Billed To</div>
                    <div class="text-sm font-bold text-slate-900 mt-1">{{ $invoice->client?->name }}</div>
                    <div class="text-slate-600">{{ $invoice->location?->name ?? 'Head Office' }}</div>
                    <div class="text-slate-500">{{ $invoice->location?->address ?? $invoice->client?->address }}</div>
                    @if ($invoice->gstin_client)
                        <div class="font-semibold text-slate-700 mt-1">Client GSTIN: {{ $invoice->gstin_client }}</div>
                    @endif
                </div>
                <div class="sm:text-right">
                    <div class="text-[10px] font-bold uppercase text-slate-400">Payment Status</div>
                    <div class="mt-1">
                        <span class="px-3 py-1 rounded-full text-xs font-bold border {{ $invoice->payment_status === 'Paid' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-amber-50 text-amber-700 border-amber-200' }}">
                            {{ $invoice->payment_status }}
                        </span>
                    </div>
                    <div class="text-slate-600 mt-2">Balance Due: <span class="font-black text-rose-600">&#8377;{{ number_format($invoice->balance_due, 2) }}</span></div>
                </div>
            </div>

            <div class="overflow-x-auto my-5">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-500 font-semibold uppercase text-[10px] border-y border-slate-200">
                        <tr>
                            <th class="py-2.5 px-3">Description</th>
                            <th class="py-2.5 px-3">HSN/SAC</th>
                            <th class="py-2.5 px-3 text-right">Qty</th>
                            <th class="py-2.5 px-3 text-right">Rate (&#8377;)</th>
                            <th class="py-2.5 px-3 text-right">GST %</th>
                            <th class="py-2.5 px-3 text-right">Total (&#8377;)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($invoice->items as $item)
                            <tr>
                                <td class="py-3 px-3 font-semibold text-slate-800">{{ $item->description }}</td>
                                <td class="py-3 px-3 text-slate-500">{{ $item->hsn_sac }}</td>
                                <td class="py-3 px-3 text-right">{{ $item->quantity }}</td>
                                <td class="py-3 px-3 text-right">&#8377;{{ number_format($item->unit_price, 2) }}</td>
                                <td class="py-3 px-3 text-right">{{ $item->gst_rate }}%</td>
                                <td class="py-3 px-3 text-right font-bold text-slate-900">&#8377;{{ number_format($item->line_total, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="flex justify-end pt-4 border-t border-slate-200">
                <div class="w-full sm:w-72 space-y-1.5">
                    <div class="flex justify-between text-slate-600"><span>Subtotal:</span><span>&#8377;{{ number_format($invoice->subtotal, 2) }}</span></div>
                    <div class="flex justify-between text-slate-600"><span>CGST (9%):</span><span>&#8377;{{ number_format($invoice->cgst_amount, 2) }}</span></div>
                    <div class="flex justify-between text-slate-600"><span>SGST (9%):</span><span>&#8377;{{ number_format($invoice->sgst_amount, 2) }}</span></div>
                    <div class="flex justify-between text-sm font-black text-slate-900 pt-2 border-t border-slate-200">
                        <span>Grand Total:</span><span>&#8377;{{ number_format($invoice->total_amount, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-emerald-700 font-bold"><span>Paid:</span><span>&#8377;{{ number_format($invoice->paid_amount, 2) }}</span></div>
                    <div class="flex justify-between text-rose-600 font-black"><span>Balance Due:</span><span>&#8377;{{ number_format($invoice->balance_due, 2) }}</span></div>
                </div>
            </div>
        </div>

        <!-- Record Payment & Payment Receipts (Hidden on Print) -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 print:hidden">
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200/80">
                <h3 class="text-sm font-bold text-slate-900 mb-3">Record Payment Receipt</h3>
                <form action="{{ route('billing.invoices.payments', $invoice) }}" method="POST" class="space-y-3 text-xs">
                    @csrf
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="font-semibold text-slate-700">Amount (&#8377;)</label>
                            <input type="number" step="0.01" name="amount" value="{{ $invoice->balance_due }}" required class="w-full mt-1 text-xs rounded-xl border-slate-200" />
                        </div>
                        <div>
                            <label class="font-semibold text-slate-700">Payment Type</label>
                            <select name="payment_type" class="w-full mt-1 text-xs rounded-xl border-slate-200">
                                <option value="Advance">Advance</option>
                                <option value="Partial">Partial</option>
                                <option value="Final" selected>Final</option>
                            </select>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="font-semibold text-slate-700">Mode</label>
                            <select name="payment_mode" class="w-full mt-1 text-xs rounded-xl border-slate-200">
                                <option value="UPI">UPI</option>
                                <option value="NEFT/RTGS">NEFT/RTGS</option>
                                <option value="Cheque">Cheque</option>
                                <option value="Cash">Cash</option>
                            </select>
                        </div>
                        <div>
                            <label class="font-semibold text-slate-700">Payment Date</label>
                            <input type="date" name="payment_date" value="{{ date('Y-m-d') }}" required class="w-full mt-1 text-xs rounded-xl border-slate-200" />
                        </div>
                    </div>
                    <div>
                        <label class="font-semibold text-slate-700">UTR / Reference Number</label>
                        <input type="text" name="reference_number" placeholder="e.g. UTR123456789" class="w-full mt-1 text-xs rounded-xl border-slate-200" />
                    </div>
                    <button type="submit" class="w-full py-2.5 rounded-xl bg-emerald-600 text-white font-semibold hover:bg-emerald-700">
                        Record Payment &amp; Generate Receipt
                    </button>
                </form>
            </div>

            <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200/80">
                <h3 class="text-sm font-bold text-slate-900 mb-3">Payment Receipts History</h3>
                <div class="space-y-2 text-xs">
                    @forelse ($invoice->payments as $pmt)
                        <div class="p-3 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-between">
                            <div>
                                <div class="font-bold text-slate-800">{{ $pmt->receipt_number }} ({{ $pmt->payment_type }})</div>
                                <div class="text-[11px] text-slate-500">{{ $pmt->payment_date->format('d M Y') }} &middot; {{ $pmt->payment_mode }} {{ $pmt->reference_number ? "({$pmt->reference_number})" : '' }}</div>
                            </div>
                            <div class="font-black text-emerald-600">&#8377;{{ number_format($pmt->amount, 2) }}</div>
                        </div>
                    @empty
                        <div class="text-slate-400 text-center py-6">No payments recorded yet.</div>
                    @endforelse
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
