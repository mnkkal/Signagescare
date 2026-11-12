<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\ClientLocation;
use App\Models\Invoice;
use App\Models\Quotation;
use App\Models\Setting;
use App\Models\SignageAsset;
use App\Models\SparePart;
use App\Models\Ticket;
use App\Models\User;
use Database\Seeders\SignageCareSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PilotModulesTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(SignageCareSeeder::class);
        $this->admin = User::where('email', 'admin@signagecare.in')->firstOrFail();
    }

    public function test_safety_checklist_and_root_cause_are_enforced_on_ticket_lifecycle(): void
    {
        $ticket = Ticket::firstOrFail();

        // Moving to In Progress without safety checklist should be blocked
        $res = $this->actingAs($this->admin)->post(route('tickets.update-status', $ticket), [
            'status' => 'In Progress',
        ]);
        $res->assertSessionHas('error');
        $this->assertNotEquals('In Progress', $ticket->fresh()->status);

        // Submit safety checklist
        $this->actingAs($this->admin)->post(route('tickets.safety', $ticket), [
            'ppe_verified' => '1',
            'electrical_isolation_confirmed' => '1',
            'work_at_height_required' => '0',
        ])->assertSessionHas('status');

        // Now moving to In Progress succeeds
        $this->actingAs($this->admin)->post(route('tickets.update-status', $ticket), [
            'status' => 'In Progress',
            'billing_type' => 'Chargeable',
        ])->assertSessionHas('status');
        $this->assertEquals('In Progress', $ticket->fresh()->status);

        // Completing without root_cause is blocked
        $this->actingAs($this->admin)->post(route('tickets.update-status', $ticket), [
            'status' => 'Completed',
        ])->assertSessionHas('error');

        // Completing with root_cause succeeds
        $this->actingAs($this->admin)->post(route('tickets.update-status', $ticket), [
            'status' => 'Completed',
            'root_cause' => 'SMPS / Driver Failure',
            'resolution_notes' => 'Replaced 400W waterproof SMPS.',
        ])->assertSessionHas('status');
        $this->assertEquals('Completed', $ticket->fresh()->status);

        // Client can rate & reopen
        $this->actingAs($this->admin)->post(route('tickets.rate', $ticket), [
            'client_rating' => 5,
            'client_feedback' => 'Quick turnaround.',
        ])->assertSessionHas('status');
        $this->assertEquals(5, $ticket->fresh()->client_rating);

        $this->actingAs($this->admin)->post(route('tickets.reopen', $ticket), [
            'reopen_reason' => 'Flickering returned after rain.',
        ])->assertSessionHas('status');
        $this->assertEquals('Open', $ticket->fresh()->status);
        $this->assertEquals(1, $ticket->fresh()->reopen_count);
    }

    public function test_technician_checkin_checkout_and_spare_part_consumption(): void
    {
        $ticket = Ticket::firstOrFail();
        $part = SparePart::firstOrFail();
        $initialStock = $part->current_stock;

        $this->actingAs($this->admin)->post(route('tickets.check-in', $ticket), [
            'lat' => '21.1458',
            'lng' => '79.0882',
            'visit_notes' => 'Arrived at store',
        ])->assertSessionHas('status');

        $this->actingAs($this->admin)->post(route('tickets.check-out', $ticket), [
            'travel_distance_km' => 14.5,
            'vehicle_cost' => 180,
        ])->assertSessionHas('status');

        $this->actingAs($this->admin)->post(route('tickets.attach-part', $ticket), [
            'spare_part_id' => $part->id,
            'quantity' => 2,
        ])->assertSessionHas('status');

        $this->assertEquals($initialStock - 2, $part->fresh()->current_stock);
    }

    public function test_billing_quotation_invoice_and_payment_flow(): void
    {
        $client = Client::firstOrFail();
        $ticket = Ticket::firstOrFail();

        $this->actingAs($this->admin)->get(route('billing.index'))->assertOk();

        $this->actingAs($this->admin)->post(route('billing.quotations.store'), [
            'client_id' => $client->id,
            'ticket_id' => $ticket->id,
            'tax_percent' => 18,
            'notes' => 'SMPS replacement estimate',
            'items' => [
                ['description' => '400W Waterproof SMPS', 'quantity' => 2, 'unit_price' => 3000, 'gst_rate' => 18],
            ],
        ])->assertSessionHas('status');

        $quotation = Quotation::latest('id')->firstOrFail();
        $this->assertEquals('Pending Approval', $quotation->status);

        $this->actingAs($this->admin)->post(route('billing.quotations.approve', $quotation))
            ->assertSessionHas('status');
        $this->assertEquals('approved', strtolower($quotation->fresh()->status));

        $this->actingAs($this->admin)->post(route('billing.quotations.invoice', $quotation))
            ->assertSessionHas('status');

        $invoice = Invoice::latest('id')->firstOrFail();
        $this->actingAs($this->admin)->get(route('billing.invoices.show', $invoice))->assertOk();

        $this->actingAs($this->admin)->post(route('billing.invoices.payments', $invoice), [
            'amount' => $invoice->total_amount,
            'payment_type' => 'Final',
            'payment_mode' => 'UPI',
            'payment_date' => now()->toDateString(),
            'reference_number' => 'UPI123456',
        ])->assertSessionHas('status');

        $this->assertEquals('paid', strtolower($invoice->fresh()->payment_status));
    }

    public function test_dispatch_inventory_safety_incidents_reports_and_settings_pages_load(): void
    {
        $this->actingAs($this->admin)->get(route('dispatch.index'))->assertOk();
        $this->actingAs($this->admin)->get(route('inventory.index'))->assertOk();
        $this->actingAs($this->admin)->get(route('safety.index'))->assertOk();
        $this->actingAs($this->admin)->get(route('incidents.index'))->assertOk();
        $this->actingAs($this->admin)->get(route('reports.index'))->assertOk();
        $this->actingAs($this->admin)->get(route('settings.index'))->assertOk();
        $this->actingAs($this->admin)->get(route('privacy.policy'))->assertOk();

        $asset = SignageAsset::firstOrFail();
        $this->actingAs($this->admin)->get(route('assets.show', $asset))->assertOk();
        $this->actingAs($this->admin)->get(route('tickets.show', Ticket::firstOrFail()))->assertOk();

        // Storm / Mass incident bulk ticket creation
        $locationIds = ClientLocation::pluck('id')->take(2)->all();
        $this->actingAs($this->admin)->post(route('incidents.store'), [
            'title' => 'Monsoon Squall Alert',
            'incident_type' => 'Storm / High Wind',
            'priority' => 'P1',
            'category' => 'Physical Damage',
            'location_ids' => $locationIds,
            'description' => 'Check fascia brackets and electrical isolation.',
        ])->assertSessionHas('status');

        // Settings update & DPDP consent
        $this->actingAs($this->admin)->post(route('settings.update'), [
            'company_name' => 'Signages Care',
            'support_phone' => '9999988888',
            'support_email' => 'help@signagescare.com',
            'quotation_approval_threshold' => 7500,
        ])->assertSessionHas('status');
        $this->assertEquals('9999988888', Setting::getValue('support_phone'));

        $this->actingAs($this->admin)->post(route('consent.accept'))->assertSessionHas('status');
        $this->assertNotNull($this->admin->fresh()->consent_accepted_at);
    }
}
