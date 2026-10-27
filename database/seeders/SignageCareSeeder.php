<?php

namespace Database\Seeders;

use App\Models\AmcContract;
use App\Models\AuditLog;
use App\Models\Client;
use App\Models\ClientLocation;
use App\Models\MaintenanceSchedule;
use App\Models\SignageAsset;
use App\Models\SopChecklist;
use App\Models\SopProject;
use App\Models\Ticket;
use App\Models\TicketEvidence;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class SignageCareSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Roles and Permissions
        $roles = [
            'Super Admin',
            'Operations Manager',
            'Store Manager',
            'Technician',
            'Production Supervisor',
        ];

        foreach ($roles as $roleName) {
            Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
        }

        $permissions = [
            'view dashboard',
            'view all tickets',
            'create tickets',
            'update ticket status',
            'assign technician',
            'manage assets',
            'manage locations',
            'manage amc',
            'upload evidence',
            'view reports',
            'manage sop projects',
            'verify sop checklists',
            'manage users',
            'view audit logs',
        ];

        foreach ($permissions as $permName) {
            Permission::firstOrCreate(['name' => $permName, 'guard_name' => 'web']);
        }

        // Assign all permissions to Super Admin & Ops Manager
        $superAdminRole = Role::findByName('Super Admin');
        $superAdminRole->syncPermissions(Permission::all());

        $opsManagerRole = Role::findByName('Operations Manager');
        $opsManagerRole->syncPermissions(Permission::all());

        $storeManagerRole = Role::findByName('Store Manager');
        $storeManagerRole->syncPermissions([
            'view dashboard',
            'create tickets',
            'view all tickets',
            'manage assets',
            'manage locations',
            'manage amc',
            'upload evidence',
            'view reports',
        ]);

        $technicianRole = Role::findByName('Technician');
        $technicianRole->syncPermissions([
            'view dashboard',
            'update ticket status',
            'upload evidence',
            'verify sop checklists',
        ]);

        // 2. Clients
        $lenskart = Client::firstOrCreate(
            ['code' => 'LKT'],
            [
                'name' => 'Lenskart Solutions',
                'contact_person' => 'Saurabh Sharma',
                'contact_email' => 'retail.ops@lenskart.com',
                'contact_phone' => '+91 9823012345',
                'is_active' => true,
                'address' => 'Vatika Mindscapes, 7th Floor, Mathura Road, Faridabad, Haryana',
                'notes' => 'Pan-India retail store network with annual signage maintenance contract.',
            ]
        );

        // 3. Key Users
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@signagecare.in'],
            [
                'name' => 'Director Operations',
                'password' => Hash::make('password'),
                'phone' => '+91 8208900802',
                'status' => 'active',
            ]
        );
        $adminUser->syncRoles(['Super Admin']);

        $opsUser = User::firstOrCreate(
            ['email' => 'ops@signagecare.in'],
            [
                'name' => 'Operations Controller',
                'password' => Hash::make('password'),
                'phone' => '+91 8208900803',
                'status' => 'active',
            ]
        );
        $opsUser->syncRoles(['Operations Manager']);

        // Store Manager SK (matches mockup avatar SK - Store Manager Lenskart Nagpur)
        $storeManager = User::firstOrCreate(
            ['email' => 'store.nagpur@lenskart.com'],
            [
                'name' => 'Store Manager',
                'password' => Hash::make('password'),
                'phone' => '+91 9975123456',
                'client_id' => $lenskart->id,
                'status' => 'active',
            ]
        );
        $storeManager->syncRoles(['Store Manager']);

        // Technicians
        $rohit = User::firstOrCreate(
            ['email' => 'rohit.tech@signagecare.in'],
            [
                'name' => 'Rohit S.',
                'password' => Hash::make('password'),
                'phone' => '+91 9822114455',
                'status' => 'active',
            ]
        );
        $rohit->syncRoles(['Technician']);

        $amit = User::firstOrCreate(
            ['email' => 'amit.tech@signagecare.in'],
            [
                'name' => 'Amit P.',
                'password' => Hash::make('password'),
                'phone' => '+91 9822114466',
                'status' => 'active',
            ]
        );
        $amit->syncRoles(['Technician']);

        $sagar = User::firstOrCreate(
            ['email' => 'sagar.tech@signagecare.in'],
            [
                'name' => 'Sagar M.',
                'password' => Hash::make('password'),
                'phone' => '+91 9822114477',
                'status' => 'active',
            ]
        );
        $sagar->syncRoles(['Technician']);

        // 4. Client Locations (matching dashboard)
        $locationsData = [
            [
                'name' => 'Lenskart - Dharampeth',
                'store_code' => 'LKT-NAG-001',
                'city' => 'Nagpur',
                'state' => 'Maharashtra',
                'address' => 'Plot No. 12, West High Court Road, Dharampeth',
                'pincode' => '440010',
                'primary_contact_number' => '+91 9823123401',
                'store_manager_user_id' => $storeManager->id,
            ],
            [
                'name' => 'Lenskart - Sadar',
                'store_code' => 'LKT-NAG-002',
                'city' => 'Nagpur',
                'state' => 'Maharashtra',
                'address' => 'Mount Road Extension, Sadar Bazar',
                'pincode' => '440001',
                'primary_contact_number' => '+91 9823123402',
                'store_manager_user_id' => $storeManager->id,
            ],
            [
                'name' => 'Lenskart - Hingna',
                'store_code' => 'LKT-NAG-003',
                'city' => 'Nagpur',
                'state' => 'Maharashtra',
                'address' => 'Near ICICI Bank, Hingna Road, T-Point',
                'pincode' => '440016',
                'primary_contact_number' => '+91 9823123403',
                'store_manager_user_id' => $storeManager->id,
            ],
            [
                'name' => 'Lenskart - Manish Nagar',
                'store_code' => 'LKT-NAG-004',
                'city' => 'Nagpur',
                'state' => 'Maharashtra',
                'address' => 'Beltarodi Road, Manish Nagar Main Square',
                'pincode' => '440037',
                'primary_contact_number' => '+91 9823123404',
                'store_manager_user_id' => $storeManager->id,
            ],
            [
                'name' => 'Lenskart - Besa',
                'store_code' => 'LKT-NAG-005',
                'city' => 'Nagpur',
                'state' => 'Maharashtra',
                'address' => 'Manewada-Besa Road, Near Pyramid City',
                'pincode' => '440034',
                'primary_contact_number' => '+91 9823123405',
                'store_manager_user_id' => $storeManager->id,
            ],
        ];

        $locations = [];
        foreach ($locationsData as $locData) {
            $loc = ClientLocation::firstOrCreate(
                ['store_code' => $locData['store_code']],
                array_merge($locData, ['client_id' => $lenskart->id, 'is_active' => true])
            );
            $locations[$locData['name']] = $loc;
        }

        // 5. AMC Contract
        $amc = AmcContract::firstOrCreate(
            ['contract_number' => 'AMC-2026-LKT-01'],
            [
                'client_id' => $lenskart->id,
                'title' => 'Comprehensive Signage AMC - Nagpur Region',
                'start_date' => Carbon::now()->subMonths(3)->toDateString(),
                'end_date' => Carbon::now()->addMonths(9)->toDateString(),
                'visit_frequency' => 'Quarterly',
                'sla_response_hours' => 24,
                'sla_resolution_hours' => 48,
                'status' => 'active',
                'contract_value' => 240000.00,
                'coverage_scope' => 'All facade LED 3D letters, ACP claddings, blade signs, and power supply driver replacements.',
            ]
        );
        $amc->locations()->sync(collect($locations)->pluck('id'));

        // 6. Signage Assets (Total 47: 33 Healthy, 8 Attention, 6 Critical)
        $assetTypes = [
            'Main Front Fascia (Acrylic 3D LED)',
            'Side Vertical Blade Sign',
            'Entrance Arch Glowsign',
            'Window Branding LED Lightbox',
            'Store Front Acrylic Logo Board',
        ];

        $locList = array_values($locations);
        $assetCounter = 1;

        // Create 33 Healthy assets
        for ($i = 0; $i < 33; $i++) {
            $loc = $locList[$i % count($locList)];
            $type = $assetTypes[$i % count($assetTypes)];
            $tag = sprintf('AST-LKT-%03d', $assetCounter++);
            SignageAsset::firstOrCreate(
                ['asset_tag' => $tag],
                [
                    'client_location_id' => $loc->id,
                    'asset_name' => $type,
                    'sign_type' => 'Acrylic 3D LED',
                    'dimensions' => '24ft x 4ft',
                    'illumination_type' => 'Backlit LED',
                    'installation_date' => Carbon::now()->subMonths(8)->toDateString(),
                    'warranty_expiry_date' => Carbon::now()->addMonths(16)->toDateString(),
                    'health_status' => 'healthy',
                    'notes' => 'Regular maintenance verified. All LED modules operating at optimal lumen output.',
                ]
            );
        }

        // Create 8 Attention assets
        for ($i = 0; $i < 8; $i++) {
            $loc = $locList[$i % count($locList)];
            $tag = sprintf('AST-LKT-%03d', $assetCounter++);
            SignageAsset::firstOrCreate(
                ['asset_tag' => $tag],
                [
                    'client_location_id' => $loc->id,
                    'asset_name' => 'Secondary Fascia & Blade Sign',
                    'sign_type' => 'ACP Backlit',
                    'dimensions' => '12ft x 3ft',
                    'illumination_type' => 'Edge-lit',
                    'installation_date' => Carbon::now()->subMonths(14)->toDateString(),
                    'warranty_expiry_date' => Carbon::now()->addMonths(6)->toDateString(),
                    'health_status' => 'attention',
                    'notes' => 'Minor dust ingress in diffuser panel. Scheduled for ultrasonic cleaning.',
                ]
            );
        }

        // Create 6 Critical assets
        for ($i = 0; $i < 6; $i++) {
            $loc = $locList[$i % count($locList)];
            $tag = sprintf('AST-LKT-%03d', $assetCounter++);
            SignageAsset::firstOrCreate(
                ['asset_tag' => $tag],
                [
                    'client_location_id' => $loc->id,
                    'asset_name' => 'Main Facade Illuminated Logo',
                    'sign_type' => 'Glowsign LED',
                    'dimensions' => '18ft x 4ft',
                    'illumination_type' => 'Backlit LED',
                    'installation_date' => Carbon::now()->subMonths(20)->toDateString(),
                    'warranty_expiry_date' => Carbon::now()->subMonths(2)->toDateString(),
                    'health_status' => 'critical',
                    'notes' => 'Power supply driver tripping intermittently or LED modules dark.',
                ]
            );
        }

        // 7. Recent Service Tickets (Directly matching dashboard screenshot)
        $ticketsData = [
            [
                'ticket_number' => 'SC-NAG-0048',
                'location_name' => 'Lenskart - Dharampeth',
                'problem_title' => 'Main Sign OFF',
                'category' => 'Lighting Failure',
                'priority' => 'P1',
                'status' => 'Assigned',
                'technician_id' => $rohit->id,
                'eta_display' => 'Today',
                'eta_date' => Carbon::today()->toDateString(),
                'problem_description' => 'Primary facade 3D LED logo completely dark since yesterday evening. Smps driver suspected faulty.',
            ],
            [
                'ticket_number' => 'SC-NAG-0047',
                'location_name' => 'Lenskart - Sadar',
                'problem_title' => '2 Letters OFF',
                'category' => 'Module Defect',
                'priority' => 'P2',
                'status' => 'In Progress',
                'technician_id' => $amit->id,
                'eta_display' => 'Today',
                'eta_date' => Carbon::today()->toDateString(),
                'problem_description' => 'Letters "K" and "A" illumination stopped. Technician on site troubleshooting wiring harness.',
            ],
            [
                'ticket_number' => 'SC-NAG-0044',
                'location_name' => 'Lenskart - Hingna',
                'problem_title' => 'Flickering',
                'category' => 'Power Fluctuations',
                'priority' => 'P3',
                'status' => 'Parts Required',
                'technician_id' => null,
                'eta_display' => 'Tomorrow',
                'eta_date' => Carbon::tomorrow()->toDateString(),
                'problem_description' => 'Right side blade sign is flickering during nighttime operations. Requires MeanWell 12V 150W driver replacement.',
            ],
            [
                'ticket_number' => 'SC-NAG-0042',
                'location_name' => 'Lenskart - Manish Nagar',
                'problem_title' => 'Cleaning',
                'category' => 'Maintenance',
                'priority' => 'P4',
                'status' => 'Scheduled',
                'technician_id' => $sagar->id,
                'eta_display' => '12 Dec',
                'eta_date' => Carbon::now()->addDays(7)->toDateString(),
                'problem_description' => 'Exterior acrylic panel accumulation of road dust. Facade wash and polish required.',
            ],
            [
                'ticket_number' => 'SC-NAG-0041',
                'location_name' => 'Lenskart - Besa',
                'problem_title' => 'Sign Loose',
                'category' => 'Structural Repair',
                'priority' => 'P3',
                'status' => 'Completed',
                'technician_id' => $rohit->id,
                'eta_display' => '-',
                'eta_date' => Carbon::yesterday()->toDateString(),
                'completed_at' => Carbon::yesterday()->setTime(17, 30),
                'resolution_notes' => 'Fasteners reinforced with high-grade M8 anchor bolts. Sign alignment leveled and certified secure.',
                'problem_description' => 'Bottom mounting anchor loose due to high wind. Re-anchored safely.',
            ],
        ];

        foreach ($ticketsData as $tData) {
            $loc = $locations[$tData['location_name']] ?? $locations['Lenskart - Dharampeth'];
            $asset = $loc->assets()->first();

            $ticket = Ticket::firstOrCreate(
                ['ticket_number' => $tData['ticket_number']],
                [
                    'client_location_id' => $loc->id,
                    'signage_asset_id' => $asset?->id,
                    'reported_by_user_id' => $storeManager->id,
                    'assigned_technician_id' => $tData['technician_id'],
                    'priority' => $tData['priority'],
                    'status' => $tData['status'],
                    'category' => $tData['category'],
                    'problem_title' => $tData['problem_title'],
                    'problem_description' => $tData['problem_description'],
                    'eta_display' => $tData['eta_display'],
                    'eta_date' => $tData['eta_date'],
                    'completed_at' => $tData['completed_at'] ?? null,
                    'resolution_notes' => $tData['resolution_notes'] ?? null,
                ]
            );

            // Add sample evidence for completed ticket
            if ($ticket->status === 'Completed') {
                TicketEvidence::firstOrCreate(
                    ['ticket_id' => $ticket->id, 'evidence_type' => 'before'],
                    [
                        'file_path' => 'evidence/sample_before.jpg',
                        'caption' => 'Signage before repair - Loose mounting bracket and dark letters',
                        'uploaded_by_user_id' => $rohit->id,
                        'captured_at' => Carbon::yesterday()->subHours(4),
                    ]
                );
                TicketEvidence::firstOrCreate(
                    ['ticket_id' => $ticket->id, 'evidence_type' => 'after'],
                    [
                        'file_path' => 'evidence/sample_after.jpg',
                        'caption' => 'Signage after service - Fully secured and 100% illuminated',
                        'uploaded_by_user_id' => $rohit->id,
                        'captured_at' => Carbon::yesterday()->subHours(1),
                    ]
                );
            }
        }

        // Additional tickets to align with KPIs:
        // Open (12), In Progress (8), Completed This Month (22), Overdue (3)
        for ($i = 6; $i <= 12; $i++) {
            $loc = $locList[$i % count($locList)];
            Ticket::firstOrCreate(
                ['ticket_number' => sprintf('SC-NAG-%04d', 50 + $i)],
                [
                    'client_location_id' => $loc->id,
                    'signage_asset_id' => $loc->assets()->first()?->id,
                    'reported_by_user_id' => $storeManager->id,
                    'assigned_technician_id' => null,
                    'priority' => ($i % 2 === 0) ? 'P2' : 'P3',
                    'status' => 'Open',
                    'category' => 'Inspection',
                    'problem_title' => 'Routine Glowsign Inspection',
                    'problem_description' => 'Scheduled inspection request logged by store operator.',
                    'eta_display' => 'Pending Assignment',
                ]
            );
        }

        for ($i = 1; $i <= 7; $i++) {
            $loc = $locList[$i % count($locList)];
            Ticket::firstOrCreate(
                ['ticket_number' => sprintf('SC-NAG-%04d', 65 + $i)],
                [
                    'client_location_id' => $loc->id,
                    'signage_asset_id' => $loc->assets()->first()?->id,
                    'reported_by_user_id' => $storeManager->id,
                    'assigned_technician_id' => $amit->id,
                    'priority' => 'P2',
                    'status' => 'In Progress',
                    'category' => 'LED Replacement',
                    'problem_title' => 'Driver Unit Refurbishment',
                    'problem_description' => 'Replacing 24V waterproof SMPS on rooftop parapet.',
                    'eta_display' => 'In Work',
                ]
            );
        }

        for ($i = 1; $i <= 3; $i++) {
            $loc = $locList[$i % count($locList)];
            Ticket::firstOrCreate(
                ['ticket_number' => sprintf('SC-NAG-%04d', 80 + $i)],
                [
                    'client_location_id' => $loc->id,
                    'signage_asset_id' => $loc->assets()->first()?->id,
                    'reported_by_user_id' => $storeManager->id,
                    'assigned_technician_id' => $sagar->id,
                    'priority' => 'P1',
                    'status' => 'Overdue',
                    'category' => 'Urgent Fix',
                    'problem_title' => 'SLA Breached - Main Sign Issue',
                    'problem_description' => 'Exceeded 48hr SLA deadline due to delayed custom acrylic letter shipment.',
                    'eta_display' => 'Overdue',
                    'eta_date' => Carbon::now()->subDays(2)->toDateString(),
                ]
            );
        }

        for ($i = 1; $i <= 21; $i++) {
            $loc = $locList[$i % count($locList)];
            Ticket::firstOrCreate(
                ['ticket_number' => sprintf('SC-NAG-%04d', 10 + $i)],
                [
                    'client_location_id' => $loc->id,
                    'signage_asset_id' => $loc->assets()->first()?->id,
                    'reported_by_user_id' => $storeManager->id,
                    'assigned_technician_id' => $rohit->id,
                    'priority' => 'P3',
                    'status' => 'Completed',
                    'category' => 'Periodic Service',
                    'problem_title' => 'Quarterly Signage Preventive Check',
                    'problem_description' => 'Thorough cleaning, current draw measurement, and wiring check done.',
                    'completed_at' => Carbon::now()->subDays($i % 25),
                    'eta_display' => 'Done',
                    'resolution_notes' => 'Sign verified in pristine operational condition.',
                ]
            );
        }

        // 8. Scheduled Preventive Maintenance (matching dashboard)
        $pmData = [
            [
                'location' => 'Lenskart - Dharampeth',
                'next_visit' => Carbon::parse('2026-12-12'),
                'last_visit' => Carbon::parse('2026-09-12'),
                'status' => 'Scheduled',
                'assigned_id' => $rohit->id,
            ],
            [
                'location' => 'Lenskart - Hingna',
                'next_visit' => Carbon::parse('2026-12-15'),
                'last_visit' => Carbon::parse('2026-09-15'),
                'status' => 'Scheduled',
                'assigned_id' => $amit->id,
            ],
            [
                'location' => 'Lenskart - Sadar',
                'next_visit' => Carbon::parse('2026-12-20'),
                'last_visit' => Carbon::parse('2026-09-20'),
                'status' => 'Scheduled',
                'assigned_id' => $sagar->id,
            ],
        ];

        foreach ($pmData as $item) {
            $loc = $locations[$item['location']];
            MaintenanceSchedule::firstOrCreate(
                [
                    'client_location_id' => $loc->id,
                    'scheduled_date' => $item['next_visit']->toDateString(),
                ],
                [
                    'amc_contract_id' => $amc->id,
                    'assigned_technician_id' => $item['assigned_id'],
                    'last_visit_date' => $item['last_visit']->toDateString(),
                    'next_visit_date' => $item['next_visit']->toDateString(),
                    'status' => $item['status'],
                    'checklist_summary' => 'Deep cleaning, LED driver voltage check, anchor bolt torque inspection.',
                ]
            );
        }

        // 9. Operations SOP Project (Directly from SOP checklist docx)
        $sopProject = SopProject::firstOrCreate(
            ['project_code' => 'SOP-2026-001'],
            [
                'client_id' => $lenskart->id,
                'client_location_id' => $locations['Lenskart - Dharampeth']->id,
                'title' => 'Lenskart Dharampeth - Full Facade Revamp & LED Signage',
                'current_stage' => 'site_survey_recce',
                'overall_status' => 'In Progress',
                'project_manager_id' => $opsUser->id,
                'supervisor_id' => $adminUser->id,
                'target_deadline' => Carbon::now()->addDays(20)->toDateString(),
                'notes' => 'New flagship store specification standard with 3D acrylic embossed brand logo.',
            ]
        );

        $sopChecklistTemplate = [
            '1. SALES / CLIENT REQUIREMENT' => [
                'stage' => 'sales_requirement',
                'items' => [
                    'Client details name/number verified',
                    'Client requirement received and clearly understood',
                    'Site location, dimensions and branding guidelines collected',
                    'Recce requirements confirmed',
                    'Artwork / logo / brand reference image collected',
                    'Quotation prepared and approved',
                    'Payments term confirmed',
                    'Delivery / installation deadline confirmed',
                ],
            ],
            '2. SITE SURVEY & MEASUREMENT (RECCE)' => [
                'stage' => 'site_survey_recce',
                'items' => [
                    'Site visit scheduled and completed',
                    'Actual dimensions measured and recorded',
                    'Wall / structure / mounting condition checked',
                    'Overall width height depth measurement checked',
                    'Access, height, ladder / scaffolding / lift requirement checked',
                    'Type of work discussed with store manager',
                    'Site photos and measurement taken',
                    'Final measurements approved before production',
                ],
            ],
            '3. DESIGN / CREATIVE' => [
                'stage' => 'design_creative',
                'items' => [
                    'Recce report received',
                    'Work briefing received',
                    'Artwork created (as per measurements)',
                    'Artwork approved by client',
                    'Jobcard created for production',
                    'Reference photos and design attached',
                ],
            ],
            '4. FABRICATION & PRODUCTION' => [
                'stage' => 'fabrication',
                'items' => [
                    'Jobcard / excel received',
                    'Measurements verified on workshop floor',
                    'Material availability verified (ACP, Acrylic, LEDs)',
                    'Correct material, thickness, colour and quantity taken',
                    'Cutting dimensions checked before cutting',
                    'Lights and electric components checked',
                ],
            ],
            '5. INSTALLATION & FINAL HANDOVER' => [
                'stage' => 'installation',
                'items' => [
                    'Installation team received site instructions',
                    'Tools, hardware and safety equipment checked',
                    'Site access and permissions reconfirmed',
                    'Signage installed level, aligned and securely',
                    'Final photographs taken (Before and After)',
                    'Installation certificate and feedback form signed',
                    'Warranty and maintenance info handed over to store manager',
                ],
            ],
        ];

        foreach ($sopChecklistTemplate as $section => $data) {
            foreach ($data['items'] as $item) {
                SopChecklist::firstOrCreate(
                    [
                        'sop_project_id' => $sopProject->id,
                        'checklist_item' => $item,
                    ],
                    [
                        'stage' => $data['stage'],
                        'section_title' => $section,
                        'status' => 'yes',
                        'remarks' => 'Verified per SOP standards.',
                        'verified_by_user_id' => $opsUser->id,
                        'verified_at' => Carbon::now()->subDays(1),
                    ]
                );
            }
        }

        // 10. Audit Log Seed
        AuditLog::log('system_initialized', 'System seed completed with standard roles, client, locations, assets, and SOP checklists.');
    }
}
