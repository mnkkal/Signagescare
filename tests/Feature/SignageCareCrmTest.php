<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\ClientLocation;
use App\Models\Session;
use App\Models\SignageAsset;
use App\Models\SopChecklist;
use App\Models\SopProject;
use App\Models\Ticket;
use App\Models\User;
use Database\Seeders\SignageCareSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SignageCareCrmTest extends TestCase
{
    use RefreshDatabase;

    protected User $storeManager;
    protected User $admin;
    protected ClientLocation $location;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(SignageCareSeeder::class);

        $this->admin = User::where('email', 'admin@signagecare.in')->first();
        $this->storeManager = User::where('email', 'store.nagpur@lenskart.com')->first();
        $this->location = ClientLocation::first();
    }

    public function test_authenticated_user_can_access_dashboard(): void
    {
        $response = $this->actingAs($this->storeManager)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('SIGNAGE');
        $response->assertSee('Open Tickets');
        $response->assertSee('Asset Health Overview');
        $response->assertSee('Recent Service Tickets');
    }

    public function test_user_can_create_service_ticket(): void
    {
        Storage::fake('public');

        $response = $this->actingAs($this->storeManager)->post('/tickets', [
            'client_location_id' => $this->location->id,
            'problem_title' => 'Test Facade Glowsign Failure',
            'category' => 'Main Sign OFF',
            'priority' => 'P1',
            'problem_description' => 'Facade lights did not turn on at scheduled sunset timer.',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect();

        $this->assertDatabaseHas('tickets', [
            'problem_title' => 'Test Facade Glowsign Failure',
            'category' => 'Main Sign OFF',
            'priority' => 'P1',
            'status' => 'Open',
        ]);
    }

    public function test_session_manager_tracks_database_sessions(): void
    {
        $response = $this->actingAs($this->admin)->get('/sessions');

        $response->assertStatus(200);
        $response->assertSee('Active Sessions');
        $response->assertSee('Security Audit Logs');
    }

    public function test_sop_checklist_item_can_be_verified(): void
    {
        $checklist = SopChecklist::first();

        if ($checklist) {
            $response = $this->actingAs($this->admin)->post("/sop/checklists/{$checklist->id}", [
                'status' => 'yes',
                'remarks' => 'Verified per SOP standards in automated test.',
            ]);

            $response->assertSessionHas('status');
            $this->assertDatabaseHas('sop_checklists', [
                'id' => $checklist->id,
                'status' => 'yes',
            ]);
        }
    }

    public function test_user_can_view_sop_project_details(): void
    {
        $project = SopProject::first();
        $this->assertNotNull($project);

        $response = $this->actingAs($this->admin)->get("/sop/{$project->id}");

        $response->assertStatus(200);
        $response->assertSee($project->project_code);
        $response->assertSee($project->client->name);
    }
}
