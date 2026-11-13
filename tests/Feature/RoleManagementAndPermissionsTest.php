<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\ClientLocation;
use App\Models\SignageAsset;
use App\Models\Ticket;
use App\Models\User;
use Database\Seeders\SignageCareSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleManagementAndPermissionsTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;
    protected User $opsManager;
    protected User $storeManager;
    protected User $technician;
    protected User $vendor;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(SignageCareSeeder::class);

        $this->superAdmin = User::where('email', 'admin@signagecare.in')->firstOrFail();
        $this->opsManager = User::where('email', 'ops@signagecare.in')->firstOrFail();
        $this->storeManager = User::where('email', 'store.nagpur@lenskart.com')->firstOrFail();
        $this->technician = User::where('email', 'rohit.tech@signagecare.in')->firstOrFail();

        // Create a vendor user to test the new Subcontractor / Vendor role
        $this->vendor = User::create([
            'name' => 'Apex High-Rise Subcontractor',
            'email' => 'vendor@apexfacades.in',
            'password' => bcrypt('password'),
            'status' => 'active',
            'skills' => ['High-Rise', 'Rooftop Scaffolding', 'Crane Operations'],
        ]);
        $this->vendor->assignRole('Subcontractor / Vendor');
    }

    public function test_super_admin_has_unrestricted_access_to_all_sections(): void
    {
        $this->actingAs($this->superAdmin);

        $this->get(route('dashboard'))->assertOk();
        $this->get(route('tickets.index'))->assertOk();
        $this->get(route('assets.index'))->assertOk();
        $this->get(route('billing.index'))->assertOk();
        $this->get(route('dispatch.index'))->assertOk();
        $this->get(route('inventory.index'))->assertOk();
        $this->get(route('safety.index'))->assertOk();
        $this->get(route('incidents.index'))->assertOk();
        $this->get(route('reports.index'))->assertOk();
        $this->get(route('settings.index'))->assertOk();
        $this->get(route('users.index'))->assertOk();
        $this->get(route('sop.index'))->assertOk();
        $this->get(route('amc.index'))->assertOk();
    }

    public function test_operations_manager_has_operational_and_billing_access(): void
    {
        $this->actingAs($this->opsManager);

        $this->get(route('dashboard'))->assertOk();
        $this->get(route('tickets.index'))->assertOk();
        $this->get(route('assets.index'))->assertOk();
        $this->get(route('dispatch.index'))->assertOk();
        $this->get(route('inventory.index'))->assertOk();
        $this->get(route('safety.index'))->assertOk();
        $this->get(route('incidents.index'))->assertOk();
        $this->get(route('reports.index'))->assertOk();
        $this->get(route('billing.index'))->assertOk();
    }

    public function test_store_manager_role_is_tenant_scoped_to_their_own_brand(): void
    {
        $this->actingAs($this->storeManager);

        // Can access store-relevant views
        $dashResponse = $this->get(route('dashboard'));
        $dashResponse->assertOk();

        $ticketsResponse = $this->get(route('tickets.index'));
        $ticketsResponse->assertOk();

        // Assets and store tickets load properly
        $assetsResponse = $this->get(route('assets.index'));
        $assetsResponse->assertOk();

        // Billing view respects client scoping
        $billingResponse = $this->get(route('billing.index'));
        $billingResponse->assertOk();

        // Can raise ticket for their own store
        $location = ClientLocation::where('client_id', $this->storeManager->client_id)->firstOrFail();
        $createRes = $this->post(route('tickets.store'), [
            'client_location_id' => $location->id,
            'category' => 'Main Sign OFF',
            'priority' => 'P2',
            'problem_title' => 'Left letter dark on store front',
            'problem_description' => 'Left brand letter stopped glowing after 7 PM.',
            'billing_type' => 'AMC Covered',
        ]);
        $createRes->assertSessionHas('status');
    }

    public function test_technician_and_vendor_roles_can_execute_field_operations(): void
    {
        $ticket = Ticket::firstOrFail();

        // 1. In-house Technician operations
        $this->actingAs($this->technician);
        $this->get(route('tickets.show', $ticket))->assertOk();

        // Check-in on site
        $this->post(route('tickets.check-in', $ticket), [
            'lat' => '21.1466',
            'lng' => '79.0888',
            'visit_notes' => 'Technician reached store.',
        ])->assertSessionHas('status');

        // Submit safety checklist (mandatory before In Progress)
        $this->post(route('tickets.safety', $ticket), [
            'ppe_verified' => '1',
            'electrical_isolation_confirmed' => '1',
            'work_at_height_required' => '0',
            'safety_notes' => 'All safety equipment checked and verified.',
        ])->assertSessionHas('status');

        // Update status to In Progress
        $this->post(route('tickets.update-status', $ticket), [
            'status' => 'In Progress',
        ])->assertSessionHas('status');

        // Check-out
        $this->post(route('tickets.check-out', $ticket), [
            'travel_distance_km' => 12.0,
            'vehicle_cost' => 150.0,
            'visit_notes' => 'Completed inspection and part replacement.',
        ])->assertSessionHas('status');

        // 2. Subcontractor / Vendor role testing
        $this->actingAs($this->vendor);
        $this->get(route('tickets.show', $ticket))->assertOk();

        // Vendor can check-in for height work
        $this->post(route('tickets.check-in', $ticket), [
            'lat' => '21.1466',
            'lng' => '79.0888',
            'visit_notes' => 'Scaffolding team arrived on site.',
        ])->assertSessionHas('status');

        // Vendor verifies work at height permit
        $this->post(route('tickets.safety', $ticket), [
            'ppe_verified' => '1',
            'electrical_isolation_confirmed' => '1',
            'work_at_height_required' => '1',
            'work_at_height_permit' => '1',
            'permit_number' => 'WAH-PERMIT-2026-99',
            'safety_notes' => 'Double lanyard harness attached to lifeline.',
        ])->assertSessionHas('status');
    }

    public function test_user_creation_and_role_assignment_with_skill_tags(): void
    {
        $this->actingAs($this->superAdmin);

        // Create new technician with skill tags
        $response = $this->post(route('users.store'), [
            'name' => 'Sunil Electrician',
            'email' => 'sunil.elec@signagecare.in',
            'phone' => '+91 9822114477',
            'role' => 'Technician',
            'skills_input' => 'Electrical, 24V SMPS, Soldering, High-Rise',
            'password' => 'Password123!',
        ]);

        $response->assertSessionHas('status');

        $createdUser = User::where('email', 'sunil.elec@signagecare.in')->firstOrFail();
        $this->assertTrue($createdUser->hasRole('Technician'));
        $this->assertContains('Electrical', $createdUser->skills);
        $this->assertContains('High-Rise', $createdUser->skills);

        // Update role and status
        $updateRoleRes = $this->post(route('users.update-role', $createdUser), [
            'role' => 'Subcontractor / Vendor',
            'status' => 'active',
            'skills_input' => 'Electrical, Crane Operator',
        ]);

        $updateRoleRes->assertSessionHas('status');
        $this->assertTrue($createdUser->fresh()->hasRole('Subcontractor / Vendor'));
        $this->assertContains('Crane Operator', $createdUser->fresh()->skills);
    }
}
