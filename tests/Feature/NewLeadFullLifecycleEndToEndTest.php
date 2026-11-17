<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\ClientLocation;
use App\Models\Invoice;
use App\Models\Quotation;
use App\Models\SignageAsset;
use App\Models\SparePart;
use App\Models\Ticket;
use App\Models\User;
use Database\Seeders\SignageCareSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NewLeadFullLifecycleEndToEndTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;
    protected User $opsManager;
    protected User $technician;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(SignageCareSeeder::class);

        $this->superAdmin = User::where('email', 'admin@signagecare.in')->firstOrFail();
        $this->opsManager = User::where('email', 'ops@signagecare.in')->firstOrFail();
        $this->technician = User::where('email', 'rohit.tech@signagecare.in')->firstOrFail();
    }

    public function test_complete_new_lead_to_ticket_to_invoice_lifecycle(): void
    {
        // -------------------------------------------------------------
        // STEP 1: NEW LEAD ONBOARDING (Super Admin / Sales)
        // A new retail brand onboarded: "Titan Eyeplus"
        // -------------------------------------------------------------
        $client = Client::create([
            'code' => 'TEP',
            'name' => 'Titan Eyeplus Retail Ltd',
            'contact_person' => 'Vikram Malhotra',
            'contact_email' => 'store.ops@titan.co.in',
            'contact_phone' => '+91 9820011223',
            'gst_number' => '27AAACT2727Q1ZB',
            'address' => 'Plot 18, Commercial Area, Ramdaspeth, Nagpur',
            'is_active' => true,
        ]);

        $this->assertDatabaseHas('clients', ['code' => 'TEP']);

        // -------------------------------------------------------------
        // STEP 2: STORE BRANCH & SIGNAGE ASSET REGISTRATION
        // -------------------------------------------------------------
        $location = ClientLocation::create([
            'client_id' => $client->id,
            'name' => 'Titan Eyeplus - Ramdaspeth',
            'store_code' => 'TEP-NGP-01',
            'city' => 'Nagpur',
            'state' => 'Maharashtra',
            'address' => 'Shop 4, Central Bazar Road, Ramdaspeth, Nagpur',
            'primary_contact_name' => 'Kunal Joshi (Store Lead)',
            'primary_contact_number' => '+91 9820099887',
            'is_active' => true,
        ]);

        $asset = SignageAsset::create([
            'client_location_id' => $location->id,
            'asset_tag' => 'AST-TEP-001',
            'asset_name' => 'Main Facade Acrylic 3D LED Logo',
            'sign_type' => 'Acrylic 3D LED',
            'dimensions' => '20ft x 3.5ft',
            'illumination_type' => 'Backlit LED',
            'power_rating_watts' => 450,
            'manufacturer_model' => 'Samsung LEDs / MeanWell Driver',
            'health_status' => 'healthy',
        ]);

        $this->assertDatabaseHas('signage_assets', ['asset_tag' => 'AST-TEP-001']);

        // -------------------------------------------------------------
        // STEP 3: CREATE STORE MANAGER USER & VERIFY DASHBOARD SCOPING
        // -------------------------------------------------------------
        $storeManager = User::create([
            'name' => 'Kunal Joshi',
            'email' => 'kunal.titan@retail.com',
            'password' => bcrypt('password'),
            'client_id' => $client->id,
            'status' => 'active',
        ]);
        $storeManager->assignRole('Store Manager');

        // Store manager visits dashboard: must see role pill and their brand
        $dashResponse = $this->actingAs($storeManager)->get(route('dashboard'));
        $dashResponse->assertOk();
        $dashResponse->assertSee('Store Manager');
        $dashResponse->assertSee('Titan Eyeplus Retail Ltd');

        // -------------------------------------------------------------
        // STEP 4: STORE MANAGER RAISES BREAKDOWN TICKET (Chargeable)
        // -------------------------------------------------------------
        $ticketRes = $this->actingAs($storeManager)->post(route('tickets.store'), [
            'client_location_id' => $location->id,
            'signage_asset_id' => $asset->id,
            'category' => 'Main Sign OFF',
            'priority' => 'P1',
            'billing_type' => 'Chargeable',
            'problem_title' => 'Titan Facade completely dark after power surge',
            'problem_description' => 'Main glowsign letters not turning on. Smell of burnt SMPS driver in ceiling.',
        ]);
        $ticketRes->assertSessionHas('status');

        $ticket = Ticket::where('problem_title', 'Titan Facade completely dark after power surge')->firstOrFail();
        $this->assertEquals('Open', $ticket->status);
        $this->assertEquals('Chargeable', $ticket->billing_type);

        // -------------------------------------------------------------
        // STEP 5: OPERATIONS MANAGER DISPATCHES TECHNICIAN
        // -------------------------------------------------------------
        $this->actingAs($this->opsManager)->post(route('dispatch.assign', $ticket), [
            'assigned_technician_id' => $this->technician->id,
            'eta_display' => 'Within 2 Hours',
        ])->assertSessionHas('status');

        $this->assertEquals($this->technician->id, $ticket->fresh()->assigned_technician_id);
        $this->assertEquals('Assigned', $ticket->fresh()->status);

        // -------------------------------------------------------------
        // STEP 6: TECHNICIAN ARRIVES ON SITE, PERFORMS GPS CHECK-IN & SAFETY
        // -------------------------------------------------------------
        $this->actingAs($this->technician)->post(route('tickets.check-in', $ticket), [
            'lat' => '21.1399',
            'lng' => '79.0766',
            'visit_notes' => 'Reached Titan store with diagnostic kit and replacement SMPS.',
        ])->assertSessionHas('status');

        // Verify Safety Gate: cannot go to In Progress without safety checklist
        $blockedStatusRes = $this->actingAs($this->technician)->post(route('tickets.update-status', $ticket), [
            'status' => 'In Progress',
        ]);
        $blockedStatusRes->assertSessionHas('error');
        $this->assertNotEquals('In Progress', $ticket->fresh()->status);

        // Submit safety checklist (PPE & Lockout)
        $this->actingAs($this->technician)->post(route('tickets.safety', $ticket), [
            'ppe_verified' => '1',
            'electrical_isolation_confirmed' => '1',
            'work_at_height_required' => '0',
            'safety_notes' => 'MCB switched OFF, circuit tested dead with multimeter, safety gloves worn.',
        ])->assertSessionHas('status');

        // Now move status to In Progress
        $this->actingAs($this->technician)->post(route('tickets.update-status', $ticket), [
            'status' => 'In Progress',
        ])->assertSessionHas('status');
        $this->assertEquals('In Progress', $ticket->fresh()->status);

        // -------------------------------------------------------------
        // STEP 7: CHARGEABLE ESTIMATE & CLIENT APPROVAL FLOW
        // Repair exceeds threshold (2x 400W SMPS @ ₹3,000 = ₹6,000 + 18% GST = ₹7,080 > ₹5,000)
        // -------------------------------------------------------------
        $this->actingAs($this->opsManager)->post(route('billing.quotations.store'), [
            'client_id' => $client->id,
            'client_location_id' => $location->id,
            'ticket_id' => $ticket->id,
            'notes' => 'Titan Eyeplus burnt driver replacement quote',
            'items' => [
                [
                    'description' => '400W Waterproof MeanWell SMPS Driver',
                    'quantity' => 2,
                    'unit_price' => 3000,
                    'gst_rate' => 18,
                ],
            ],
        ])->assertSessionHas('status');

        $quotation = Quotation::where('ticket_id', $ticket->id)->firstOrFail();
        $this->assertEquals('Pending Approval', $quotation->status);
        $this->assertTrue($quotation->total_amount > 5000);

        // Client approves quotation
        $this->actingAs($storeManager)->post(route('billing.quotations.approve', $quotation))
            ->assertSessionHas('status');
        $this->assertEquals('approved', strtolower($quotation->fresh()->status));

        // -------------------------------------------------------------
        // STEP 8: SPARE PART CONSUMPTION & TICKET COMPLETION
        // -------------------------------------------------------------
        $part = SparePart::firstOrFail();
        $initialStock = $part->current_stock;

        $this->actingAs($this->technician)->post(route('tickets.attach-part', $ticket), [
            'spare_part_id' => $part->id,
            'quantity' => 2,
        ])->assertSessionHas('status');

        $this->assertEquals($initialStock - 2, $part->fresh()->current_stock);

        // Technician logs check-out travel
        $this->actingAs($this->technician)->post(route('tickets.check-out', $ticket), [
            'travel_distance_km' => 11.5,
            'vehicle_cost' => 140,
            'visit_notes' => 'New drivers wired, tested 24V steady load, letters bright and uniform.',
        ])->assertSessionHas('status');

        // Mark ticket completed with mandatory root cause
        $this->actingAs($this->technician)->post(route('tickets.update-status', $ticket), [
            'status' => 'Completed',
            'root_cause' => 'SMPS / Driver Failure',
            'resolution_notes' => 'Replaced burnt SMPS drivers with new waterproof units and reset MCB.',
        ])->assertSessionHas('status');

        $this->assertEquals('Completed', $ticket->fresh()->status);
        $this->assertNotNull($ticket->fresh()->completed_at);

        // Client rates the ticket
        $this->actingAs($storeManager)->post(route('tickets.rate', $ticket), [
            'client_rating' => 5,
            'client_feedback' => 'Store front illuminated perfectly before evening rush. Great job!',
        ])->assertSessionHas('status');
        $this->assertEquals(5, $ticket->fresh()->client_rating);

        // -------------------------------------------------------------
        // STEP 9: ACCOUNTS GENERATES GST TAX INVOICE & RECEIVES PAYMENT
        // -------------------------------------------------------------
        $this->actingAs($this->superAdmin)->post(route('billing.quotations.invoice', $quotation))
            ->assertSessionHas('status');

        $invoice = Invoice::where('ticket_id', $ticket->id)->firstOrFail();
        $this->assertEquals('Unpaid', $invoice->payment_status);

        // Record payment
        $this->actingAs($this->superAdmin)->post(route('billing.invoices.payments', $invoice), [
            'amount' => $invoice->total_amount,
            'payment_type' => 'Final',
            'payment_mode' => 'UPI',
            'payment_date' => now()->toDateString(),
            'reference_number' => 'UPI-TITAN-992211',
            'notes' => 'Full payment received via corporate UPI',
        ])->assertSessionHas('status');

        $this->assertEquals('paid', strtolower($invoice->fresh()->payment_status));
        $this->assertEquals(0, $invoice->fresh()->balance_due);
    }
}
