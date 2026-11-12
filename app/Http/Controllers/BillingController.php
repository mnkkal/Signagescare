<?php

namespace App\Http\Controllers;

use App\Models\AmcContract;
use App\Models\AuditLog;
use App\Models\Client;
use App\Models\ClientLocation;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Payment;
use App\Models\Quotation;
use App\Models\QuotationItem;
use App\Models\Setting;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BillingController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $isClientScoped = $user && $user->hasRole('Store Manager') && $user->client_id;

        $quotationsQuery = Quotation::with(['client', 'location', 'ticket', 'items'])->latest();
        $invoicesQuery = Invoice::with(['client', 'location', 'ticket', 'payments'])->latest();

        if ($isClientScoped) {
            $quotationsQuery->where('client_id', $user->client_id);
            $invoicesQuery->where('client_id', $user->client_id);
        }

        $quotations = $quotationsQuery->paginate(10, ['*'], 'q_page');
        $invoices = $invoicesQuery->paginate(10, ['*'], 'i_page');

        $clients = Client::all();
        $locations = ClientLocation::all();
        $tickets = Ticket::with('location')->latest()->take(50)->get();
        $contracts = AmcContract::with('client')->get();
        $approvalThreshold = (float) Setting::getValue('quotation_approval_threshold', 5000);

        $stats = [
            'total_invoiced' => (clone $invoicesQuery)->sum('total_amount'),
            'total_collected' => (clone $invoicesQuery)->sum('paid_amount'),
            'total_outstanding' => (clone $invoicesQuery)->sum('balance_due'),
            'pending_approvals' => (clone $quotationsQuery)->where('status', 'Pending Approval')->count(),
        ];

        return view('billing.index', compact(
            'quotations', 'invoices', 'clients', 'locations', 'tickets', 'contracts', 'approvalThreshold', 'stats'
        ));
    }

    public function storeQuotation(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'client_location_id' => 'nullable|exists:client_locations,id',
            'ticket_id' => 'nullable|exists:tickets,id',
            'valid_until' => 'nullable|date',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string|max:255',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.gst_rate' => 'required|numeric|min:0|max:28',
        ]);

        DB::transaction(function () use ($validated) {
            $subtotal = 0;
            $gstTotal = 0;

            foreach ($validated['items'] as $item) {
                $base = (float) $item['quantity'] * (float) $item['unit_price'];
                $gst = $base * ((float) $item['gst_rate'] / 100);
                $subtotal += $base;
                $gstTotal += $gst;
            }

            $total = $subtotal + $gstTotal;
            $threshold = (float) Setting::getValue('quotation_approval_threshold', 5000);
            $needsApproval = $total > $threshold;

            $nextNum = (Quotation::max('id') ?? 0) + 1;
            $quotationNumber = 'QTN-' . date('Y') . '-' . str_pad($nextNum, 4, '0', STR_PAD_LEFT);

            $quotation = Quotation::create([
                'quotation_number' => $quotationNumber,
                'ticket_id' => $validated['ticket_id'] ?? null,
                'client_id' => $validated['client_id'],
                'client_location_id' => $validated['client_location_id'] ?? null,
                'created_by' => Auth::id(),
                'status' => $needsApproval ? 'Pending Approval' : 'Sent',
                'subtotal' => $subtotal,
                'gst_amount' => $gstTotal,
                'total_amount' => $total,
                'notes' => $validated['notes'] ?? null,
                'valid_until' => $validated['valid_until'] ?? now()->addDays(15),
            ]);

            foreach ($validated['items'] as $item) {
                $base = (float) $item['quantity'] * (float) $item['unit_price'];
                $lineTotal = $base + ($base * ((float) $item['gst_rate'] / 100));
                QuotationItem::create([
                    'quotation_id' => $quotation->id,
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'gst_rate' => $item['gst_rate'],
                    'line_total' => $lineTotal,
                ]);
            }

            if ($quotation->ticket_id) {
                Ticket::where('id', $quotation->ticket_id)->update([
                    'estimated_amount' => $total,
                    'requires_client_approval' => $needsApproval,
                ]);
            }

            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => 'Quotation Created',
                'model_type' => Quotation::class,
                'model_id' => $quotation->id,
                'description' => "Created quotation {$quotationNumber} for ₹" . number_format($total, 2),
                'ip_address' => request()->ip(),
            ]);
        });

        return back()->with('status', 'Quotation created successfully.');
    }

    public function approveQuotation(Request $request, Quotation $quotation)
    {
        $action = $request->input('decision', 'Approved');
        if ($action === 'Rejected') {
            $quotation->update([
                'status' => 'Rejected',
                'rejection_reason' => $request->input('rejection_reason', 'Rejected by client'),
            ]);
            return back()->with('status', "Quotation {$quotation->quotation_number} marked as Rejected.");
        }

        $quotation->update([
            'status' => 'Approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        if ($quotation->ticket_id) {
            Ticket::where('id', $quotation->ticket_id)->update([
                'client_approved_at' => now(),
                'client_approved_by' => Auth::id(),
            ]);
        }

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'Quotation Approved',
            'model_type' => Quotation::class,
            'model_id' => $quotation->id,
            'description' => "Approved quotation {$quotation->quotation_number}",
            'ip_address' => $request->ip(),
        ]);

        return back()->with('status', "Quotation {$quotation->quotation_number} approved.");
    }

    public function generateInvoiceFromQuotation(Quotation $quotation)
    {
        $existing = Invoice::where('quotation_id', $quotation->id)->first();
        if ($existing) {
            return redirect()->route('billing.invoices.show', $existing);
        }

        $invoice = DB::transaction(function () use ($quotation) {
            $nextNum = (Invoice::max('id') ?? 0) + 1;
            $invoiceNumber = 'INV-' . date('Y') . '-' . str_pad($nextNum, 4, '0', STR_PAD_LEFT);
            $halfGst = (float) $quotation->gst_amount / 2;

            $invoice = Invoice::create([
                'invoice_number' => $invoiceNumber,
                'ticket_id' => $quotation->ticket_id,
                'quotation_id' => $quotation->id,
                'client_id' => $quotation->client_id,
                'client_location_id' => $quotation->client_location_id,
                'invoice_date' => now()->toDateString(),
                'due_date' => now()->addDays(15)->toDateString(),
                'subtotal' => $quotation->subtotal,
                'cgst_amount' => $halfGst,
                'sgst_amount' => $halfGst,
                'igst_amount' => 0,
                'total_amount' => $quotation->total_amount,
                'paid_amount' => 0,
                'balance_due' => $quotation->total_amount,
                'payment_status' => 'Unpaid',
                'gstin_client' => $quotation->client->gst_number ?? null,
                'notes' => $quotation->notes,
                'created_by' => Auth::id(),
            ]);

            foreach ($quotation->items as $item) {
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'description' => $item->description,
                    'hsn_sac' => '998719',
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'gst_rate' => $item->gst_rate,
                    'line_total' => $item->line_total,
                ]);
            }

            return $invoice;
        });

        return redirect()->route('billing.invoices.show', $invoice)->with('status', "GST Invoice {$invoice->invoice_number} generated.");
    }

    public function storeDirectInvoice(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'client_location_id' => 'nullable|exists:client_locations,id',
            'ticket_id' => 'nullable|exists:tickets,id',
            'amc_contract_id' => 'nullable|exists:amc_contracts,id',
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:1',
            'gst_rate' => 'required|numeric|min:0|max:28',
            'due_date' => 'nullable|date',
        ]);

        $invoice = DB::transaction(function () use ($validated) {
            $subtotal = (float) $validated['amount'];
            $gst = $subtotal * ((float) $validated['gst_rate'] / 100);
            $total = $subtotal + $gst;
            $client = Client::find($validated['client_id']);
            $nextNum = (Invoice::max('id') ?? 0) + 1;
            $invoiceNumber = 'INV-' . date('Y') . '-' . str_pad($nextNum, 4, '0', STR_PAD_LEFT);

            $invoice = Invoice::create([
                'invoice_number' => $invoiceNumber,
                'ticket_id' => $validated['ticket_id'] ?? null,
                'amc_contract_id' => $validated['amc_contract_id'] ?? null,
                'client_id' => $validated['client_id'],
                'client_location_id' => $validated['client_location_id'] ?? null,
                'invoice_date' => now()->toDateString(),
                'due_date' => $validated['due_date'] ?? now()->addDays(15)->toDateString(),
                'subtotal' => $subtotal,
                'cgst_amount' => $gst / 2,
                'sgst_amount' => $gst / 2,
                'igst_amount' => 0,
                'total_amount' => $total,
                'paid_amount' => 0,
                'balance_due' => $total,
                'payment_status' => 'Unpaid',
                'gstin_client' => $client->gst_number ?? null,
                'created_by' => Auth::id(),
            ]);

            InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'description' => $validated['description'],
                'hsn_sac' => '998719',
                'quantity' => 1,
                'unit_price' => $subtotal,
                'gst_rate' => $validated['gst_rate'],
                'line_total' => $total,
            ]);

            return $invoice;
        });

        return redirect()->route('billing.invoices.show', $invoice)->with('status', "GST Invoice {$invoice->invoice_number} created.");
    }

    public function showInvoice(Invoice $invoice)
    {
        $invoice->load(['client', 'location', 'ticket', 'amcContract', 'items', 'payments.recorder']);
        $company = [
            'name' => Setting::getValue('company_name', 'Signage Care Services Pvt. Ltd.'),
            'gstin' => Setting::getValue('company_gstin', '27AABCS1234F1Z5'),
            'address' => Setting::getValue('company_address', 'Plot 42, MIDC Hingna, Nagpur, Maharashtra 440016'),
            'phone' => Setting::getValue('support_phone', '8208900802'),
            'email' => Setting::getValue('support_email', 'care@signagecare.in'),
        ];

        return view('billing.invoice_show', compact('invoice', 'company'));
    }

    public function recordPayment(Request $request, Invoice $invoice)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:1',
            'payment_type' => 'required|in:Advance,Partial,Final',
            'payment_mode' => 'required|string|max:50',
            'reference_number' => 'nullable|string|max:100',
            'payment_date' => 'required|date',
            'notes' => 'nullable|string|max:255',
        ]);

        $nextNum = (Payment::max('id') ?? 0) + 1;
        $receiptNumber = 'RCPT-' . date('Y') . '-' . str_pad($nextNum, 4, '0', STR_PAD_LEFT);

        Payment::create([
            'invoice_id' => $invoice->id,
            'receipt_number' => $receiptNumber,
            'amount' => $validated['amount'],
            'payment_type' => $validated['payment_type'],
            'payment_mode' => $validated['payment_mode'],
            'reference_number' => $validated['reference_number'] ?? null,
            'payment_date' => $validated['payment_date'],
            'notes' => $validated['notes'] ?? null,
            'recorded_by' => Auth::id(),
        ]);

        $invoice->recalculatePayments();

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'Payment Recorded',
            'model_type' => Invoice::class,
            'model_id' => $invoice->id,
            'description' => "Recorded {$validated['payment_type']} payment {$receiptNumber} of ₹" . number_format($validated['amount'], 2),
            'ip_address' => $request->ip(),
        ]);

        return back()->with('status', "Payment receipt {$receiptNumber} recorded. Status: {$invoice->fresh()->payment_status}");
    }
}
