<x-app-layout>
    <div class="space-y-6 max-w-7xl mx-auto" x-data="{ showQuoteModal: false, showInvoiceModal: false }">

        <!-- Page Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pb-4 border-b border-slate-200 gap-3">
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-slate-900">Billing, Quotations &amp; GST Invoices</h1>
                <p class="text-xs text-slate-500 mt-0.5">Repair estimates, client approvals (above &#8377;{{ number_format($approvalThreshold, 0) }}), GST tax invoices, and payment receipts</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <button @click="showQuoteModal = true" class="px-4 py-2 rounded-xl text-xs font-semibold text-white bg-blue-600 hover:bg-blue-700 shadow-sm">
                    + New Quotation
                </button>
                <button @click="showInvoiceModal = true" class="px-4 py-2 rounded-xl text-xs font-semibold text-white bg-slate-900 hover:bg-slate-800 shadow-sm">
                    + Direct GST Invoice
                </button>
            </div>
        </div>

        <!-- 4 Billing KPIs -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
            <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-200/80">
                <div class="text-[11px] font-semibold text-slate-500">Total Billed (GST Incl.)</div>
                <div class="text-xl sm:text-2xl font-extrabold text-slate-900 mt-1">&#8377;{{ number_format($stats['total_invoiced'], 2) }}</div>
            </div>
            <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-200/80">
                <div class="text-[11px] font-semibold text-slate-500">Total Collected</div>
                <div class="text-xl sm:text-2xl font-extrabold text-emerald-600 mt-1">&#8377;{{ number_format($stats['total_collected'], 2) }}</div>
            </div>
            <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-200/80">
                <div class="text-[11px] font-semibold text-slate-500">Balance Due</div>
                <div class="text-xl sm:text-2xl font-extrabold text-rose-600 mt-1">&#8377;{{ number_format($stats['total_outstanding'], 2) }}</div>
            </div>
            <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-200/80">
                <div class="text-[11px] font-semibold text-slate-500">Pending Quote Approvals</div>
                <div class="text-xl sm:text-2xl font-extrabold text-amber-600 mt-1">{{ $stats['pending_approvals'] }}</div>
            </div>
        </div>

        <!-- Quotations Section -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100">
                <h2 class="text-sm sm:text-base font-bold text-slate-900">Repair Quotations &amp; Estimates</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs min-w-[680px]">
                    <thead class="bg-slate-50 text-slate-500 font-semibold uppercase text-[10px] border-b border-slate-100">
                        <tr>
                            <th class="py-3 px-4">Quote #</th>
                            <th class="py-3 px-4">Client / Location</th>
                            <th class="py-3 px-4">Ticket</th>
                            <th class="py-3 px-4">Total (Incl. GST)</th>
                            <th class="py-3 px-4">Status</th>
                            <th class="py-3 px-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($quotations as $q)
                            <tr class="hover:bg-slate-50/70">
                                <td class="py-3 px-4 font-bold text-blue-600">{{ $q->quotation_number }}</td>
                                <td class="py-3 px-4">
                                    <div class="font-semibold text-slate-800">{{ $q->client?->name }}</div>
                                    <div class="text-[11px] text-slate-400">{{ $q->location?->name ?? 'All Branches' }}</div>
                                </td>
                                <td class="py-3 px-4">
                                    @if ($q->ticket)
                                        <a href="{{ route('tickets.show', $q->ticket) }}" class="text-blue-600 font-semibold hover:underline">{{ $q->ticket->ticket_number }}</a>
                                    @else
                                        <span class="text-slate-400">-</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 font-bold text-slate-900">&#8377;{{ number_format($q->total_amount, 2) }}</td>
                                <td class="py-3 px-4">
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold border {{ $q->status === 'Approved' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : ($q->status === 'Rejected' ? 'bg-rose-50 text-rose-700 border-rose-200' : 'bg-amber-50 text-amber-700 border-amber-200') }}">
                                        {{ $q->status }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-right whitespace-nowrap space-x-1">
                                    @if (!in_array($q->status, ['Approved', 'Rejected']))
                                        <form action="{{ route('billing.quotations.approve', $q) }}" method="POST" class="inline">
                                            @csrf
                                            <input type="hidden" name="decision" value="Approved">
                                            <button type="submit" class="px-2.5 py-1 rounded-lg bg-emerald-600 text-white text-[11px] font-semibold hover:bg-emerald-700">Approve</button>
                                        </form>
                                        <form action="{{ route('billing.quotations.approve', $q) }}" method="POST" class="inline">
                                            @csrf
                                            <input type="hidden" name="decision" value="Rejected">
                                            <button type="submit" class="px-2.5 py-1 rounded-lg bg-rose-50 text-rose-700 border border-rose-200 text-[11px] font-semibold hover:bg-rose-100">Reject</button>
                                        </form>
                                    @endif
                                    @if ($q->status !== 'Rejected')
                                        <form action="{{ route('billing.quotations.invoice', $q) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="px-2.5 py-1 rounded-lg bg-slate-900 text-white text-[11px] font-semibold hover:bg-slate-800">Generate GST Invoice</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="py-6 text-center text-slate-400">No quotations created yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- GST Invoices Section -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100">
                <h2 class="text-sm sm:text-base font-bold text-slate-900">GST Tax Invoices &amp; Payment Status</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs min-w-[680px]">
                    <thead class="bg-slate-50 text-slate-500 font-semibold uppercase text-[10px] border-b border-slate-100">
                        <tr>
                            <th class="py-3 px-4">Invoice #</th>
                            <th class="py-3 px-4">Date</th>
                            <th class="py-3 px-4">Client / Branch</th>
                            <th class="py-3 px-4">Total</th>
                            <th class="py-3 px-4">Paid</th>
                            <th class="py-3 px-4">Balance Due</th>
                            <th class="py-3 px-4">Status</th>
                            <th class="py-3 px-4 text-right">PDF / Receipts</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($invoices as $inv)
                            <tr class="hover:bg-slate-50/70">
                                <td class="py-3 px-4 font-bold text-blue-600">{{ $inv->invoice_number }}</td>
                                <td class="py-3 px-4 text-slate-600">{{ $inv->invoice_date->format('d M Y') }}</td>
                                <td class="py-3 px-4">
                                    <div class="font-semibold text-slate-800">{{ $inv->client?->name }}</div>
                                    <div class="text-[11px] text-slate-400">{{ $inv->location?->name ?? 'AMC / Head Office' }}</div>
                                </td>
                                <td class="py-3 px-4 font-bold text-slate-900">&#8377;{{ number_format($inv->total_amount, 2) }}</td>
                                <td class="py-3 px-4 font-semibold text-emerald-600">&#8377;{{ number_format($inv->paid_amount, 2) }}</td>
                                <td class="py-3 px-4 font-semibold text-rose-600">&#8377;{{ number_format($inv->balance_due, 2) }}</td>
                                <td class="py-3 px-4">
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold border {{ $inv->payment_status === 'Paid' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : ($inv->payment_status === 'Unpaid' ? 'bg-rose-50 text-rose-700 border-rose-200' : 'bg-amber-50 text-amber-700 border-amber-200') }}">
                                        {{ $inv->payment_status }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-right">
                                    <a href="{{ route('billing.invoices.show', $inv) }}" class="px-3 py-1 rounded-lg bg-blue-50 text-blue-700 font-semibold hover:bg-blue-600 hover:text-white transition-colors">
                                        View / Print PDF &rarr;
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="py-6 text-center text-slate-400">No invoices generated yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- New Quotation Modal -->
        <div x-show="showQuoteModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-slate-200" @click.away="showQuoteModal = false">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
                    <h3 class="text-base font-bold text-slate-900">Create Repair Quotation</h3>
                    <button @click="showQuoteModal = false" class="text-slate-400 hover:text-slate-600">&times;</button>
                </div>
                <form action="{{ route('billing.quotations.store') }}" method="POST" class="space-y-3 text-xs">
                    @csrf
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="font-semibold text-slate-700">Client</label>
                            <select name="client_id" required class="w-full mt-1 text-xs rounded-xl border-slate-200">
                                @foreach ($clients as $c)
                                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="font-semibold text-slate-700">Store Location</label>
                            <select name="client_location_id" class="w-full mt-1 text-xs rounded-xl border-slate-200">
                                <option value="">-- Optional --</option>
                                @foreach ($locations as $loc)
                                    <option value="{{ $loc->id }}">{{ $loc->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="font-semibold text-slate-700">Link Service Ticket (Optional)</label>
                        <select name="ticket_id" class="w-full mt-1 text-xs rounded-xl border-slate-200">
                            <option value="">-- None --</option>
                            @foreach ($tickets as $t)
                                <option value="{{ $t->id }}">{{ $t->ticket_number }} - {{ $t->problem_title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 space-y-2">
                        <div class="font-bold text-slate-700">Line Item</div>
                        <input type="text" name="items[0][description]" required placeholder="e.g. 24V Waterproof SMPS & LED Module Replacement" class="w-full text-xs rounded-xl border-slate-200" />
                        <div class="grid grid-cols-3 gap-2">
                            <div>
                                <label class="text-[10px] text-slate-500">Qty</label>
                                <input type="number" step="0.01" name="items[0][quantity]" value="1" required class="w-full text-xs rounded-xl border-slate-200" />
                            </div>
                            <div>
                                <label class="text-[10px] text-slate-500">Unit Price (&#8377;)</label>
                                <input type="number" step="0.01" name="items[0][unit_price]" required placeholder="5500" class="w-full text-xs rounded-xl border-slate-200" />
                            </div>
                            <div>
                                <label class="text-[10px] text-slate-500">GST %</label>
                                <input type="number" step="0.01" name="items[0][gst_rate]" value="18" required class="w-full text-xs rounded-xl border-slate-200" />
                            </div>
                        </div>
                    </div>
                    <div>
                        <label class="font-semibold text-slate-700">Notes</label>
                        <textarea name="notes" rows="2" class="w-full mt-1 text-xs rounded-xl border-slate-200" placeholder="Valid for 15 days. Above ₹{{ number_format($approvalThreshold, 0) }} requires client approval."></textarea>
                    </div>
                    <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
                        <button type="button" @click="showQuoteModal = false" class="px-3 py-2 rounded-xl text-slate-600 hover:bg-slate-100">Cancel</button>
                        <button type="submit" class="px-4 py-2 rounded-xl bg-blue-600 text-white font-semibold hover:bg-blue-700">Create Quotation</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Direct Invoice Modal -->
        <div x-show="showInvoiceModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-200" @click.away="showInvoiceModal = false">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
                    <h3 class="text-base font-bold text-slate-900">Generate GST Invoice (Ticket / AMC Renewal)</h3>
                    <button @click="showInvoiceModal = false" class="text-slate-400 hover:text-slate-600">&times;</button>
                </div>
                <form action="{{ route('billing.invoices.store') }}" method="POST" class="space-y-3 text-xs">
                    @csrf
                    <div>
                        <label class="font-semibold text-slate-700">Client</label>
                        <select name="client_id" required class="w-full mt-1 text-xs rounded-xl border-slate-200">
                            @foreach ($clients as $c)
                                <option value="{{ $c->id }}">{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="font-semibold text-slate-700">AMC Contract (if AMC Renewal Invoice)</label>
                        <select name="amc_contract_id" class="w-full mt-1 text-xs rounded-xl border-slate-200">
                            <option value="">-- N/A (One-time Repair) --</option>
                            @foreach ($contracts as $amc)
                                <option value="{{ $amc->id }}">{{ $amc->contract_number }} - {{ $amc->title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="font-semibold text-slate-700">Description</label>
                        <input type="text" name="description" required placeholder="e.g. Quarterly AMC Fee / Chargeable Signage Repair" class="w-full mt-1 text-xs rounded-xl border-slate-200" />
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="font-semibold text-slate-700">Taxable Amount (&#8377;)</label>
                            <input type="number" step="0.01" name="amount" required class="w-full mt-1 text-xs rounded-xl border-slate-200" />
                        </div>
                        <div>
                            <label class="font-semibold text-slate-700">GST %</label>
                            <input type="number" step="0.01" name="gst_rate" value="18" required class="w-full mt-1 text-xs rounded-xl border-slate-200" />
                        </div>
                    </div>
                    <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
                        <button type="button" @click="showInvoiceModal = false" class="px-3 py-2 rounded-xl text-slate-600 hover:bg-slate-100">Cancel</button>
                        <button type="submit" class="px-4 py-2 rounded-xl bg-slate-900 text-white font-semibold hover:bg-slate-800">Generate Invoice</button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>
