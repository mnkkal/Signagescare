<?php

use App\Http\Controllers\AmcController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DispatchController;
use App\Http\Controllers\DownloadController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\MassIncidentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SafetyController;
use App\Http\Controllers\SessionManagerController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\SopProjectController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\UserRoleController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware(['auth', 'verified'])->group(function () {
    // 1. Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // 2. Service Requests & Tickets
    Route::resource('tickets', TicketController::class)->only(['index', 'create', 'store', 'show']);
    Route::post('/tickets/{ticket}/status', [TicketController::class, 'updateStatus'])->name('tickets.update-status');
    Route::post('/tickets/{ticket}/evidence', [TicketController::class, 'uploadEvidence'])->name('tickets.upload-evidence');
    Route::post('/tickets/{ticket}/check-in', [TicketController::class, 'checkIn'])->name('tickets.check-in');
    Route::post('/tickets/{ticket}/check-out', [TicketController::class, 'checkOut'])->name('tickets.check-out');
    Route::post('/tickets/{ticket}/safety', [TicketController::class, 'submitSafetyChecklist'])->name('tickets.safety');
    Route::post('/tickets/{ticket}/reopen', [TicketController::class, 'reopen'])->name('tickets.reopen');
    Route::post('/tickets/{ticket}/rate', [TicketController::class, 'rate'])->name('tickets.rate');
    Route::post('/tickets/{ticket}/parts', [TicketController::class, 'attachPart'])->name('tickets.attach-part');

    // 3. Asset Register & Health
    Route::resource('assets', AssetController::class)->only(['index', 'create', 'store', 'show']);
    Route::post('/assets/{asset}/health', [AssetController::class, 'updateHealth'])->name('assets.update-health');
    Route::post('/assets/{asset}/documents', [AssetController::class, 'uploadDocument'])->name('assets.upload-document');

    // 4. Locations
    Route::resource('locations', LocationController::class)->only(['index', 'show', 'store']);

    // 5. Preventive Maintenance
    Route::resource('maintenance', MaintenanceController::class)->only(['index', 'store']);
    Route::post('/maintenance/{schedule}/status', [MaintenanceController::class, 'updateStatus'])->name('maintenance.update-status');

    // 6. AMC Details
    Route::resource('amc', AmcController::class)->only(['index', 'show']);

    // 7. Operations SOP Engine
    Route::resource('sop', SopProjectController::class)->parameters(['sop' => 'project'])->only(['index', 'show']);
    Route::post('/sop/checklists/{checklist}', [SopProjectController::class, 'updateChecklist'])->name('sop.update-checklist');
    Route::post('/sop/{project}/stage', [SopProjectController::class, 'advanceStage'])->name('sop.advance-stage');

    // 8. Session Management & Security Audit
    Route::get('/sessions', [SessionManagerController::class, 'index'])->name('sessions.index');
    Route::post('/sessions/destroy-others', [SessionManagerController::class, 'destroyOtherSessions'])->name('sessions.destroy-others');
    Route::delete('/sessions/{id}', [SessionManagerController::class, 'destroy'])->name('sessions.destroy');

    // 9. User & Role Management (Standard RBAC)
    Route::resource('users', UserRoleController::class)->only(['index', 'store', 'destroy']);
    Route::post('/users/{user}/role', [UserRoleController::class, 'updateRole'])->name('users.update-role');

    // 10. Reports & Downloads
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export-tickets', [ReportController::class, 'exportTicketsCsv'])->name('reports.export-tickets');
    Route::get('/downloads', [DownloadController::class, 'index'])->name('downloads.index');
    Route::get('/downloads/assets', [DownloadController::class, 'exportAssetsCsv'])->name('downloads.export-assets');
    Route::get('/downloads/sop', [DownloadController::class, 'downloadSop'])->name('downloads.download-sop');
    Route::get('/downloads/proposal', [DownloadController::class, 'downloadProposal'])->name('downloads.download-proposal');

    // 11. Support Desk
    Route::get('/support', function () {
        return view('support');
    })->name('support');

    // 12. Billing, Quotations & GST Invoices
    Route::get('/billing', [BillingController::class, 'index'])->name('billing.index');
    Route::post('/billing/quotations', [BillingController::class, 'storeQuotation'])->name('billing.quotations.store');
    Route::post('/billing/quotations/{quotation}/approve', [BillingController::class, 'approveQuotation'])->name('billing.quotations.approve');
    Route::post('/billing/quotations/{quotation}/invoice', [BillingController::class, 'generateInvoiceFromQuotation'])->name('billing.quotations.invoice');
    Route::post('/billing/invoices', [BillingController::class, 'storeDirectInvoice'])->name('billing.invoices.store');
    Route::get('/billing/invoices/{invoice}', [BillingController::class, 'showInvoice'])->name('billing.invoices.show');
    Route::post('/billing/invoices/{invoice}/payments', [BillingController::class, 'recordPayment'])->name('billing.invoices.payments');

    // 13. Technician Dispatch Board
    Route::get('/dispatch', [DispatchController::class, 'index'])->name('dispatch.index');
    Route::post('/dispatch/{ticket}/assign', [DispatchController::class, 'assign'])->name('dispatch.assign');

    // 14. Spare Parts & Suppliers Inventory
    Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory.index');
    Route::post('/inventory/parts', [InventoryController::class, 'storePart'])->name('inventory.parts.store');
    Route::post('/inventory/parts/{part}/adjust', [InventoryController::class, 'adjustStock'])->name('inventory.parts.adjust');
    Route::post('/inventory/suppliers', [InventoryController::class, 'storeSupplier'])->name('inventory.suppliers.store');

    // 15. Safety Incidents & Near-Miss Log
    Route::get('/safety', [SafetyController::class, 'index'])->name('safety.index');
    Route::post('/safety', [SafetyController::class, 'store'])->name('safety.store');
    Route::post('/safety/{incident}/status', [SafetyController::class, 'updateStatus'])->name('safety.update-status');

    // 16. Storm & Mass Incident Bulk Ticketing
    Route::get('/incidents', [MassIncidentController::class, 'index'])->name('incidents.index');
    Route::post('/incidents', [MassIncidentController::class, 'store'])->name('incidents.store');

    // 17. System Settings, Notifications Log, Backups & DPDP Privacy
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');
    Route::post('/settings/backup', [SettingController::class, 'runBackup'])->name('settings.backup');
    Route::post('/consent/accept', [SettingController::class, 'acceptConsent'])->name('consent.accept');
    Route::get('/privacy-policy', [SettingController::class, 'privacyPolicy'])->name('privacy.policy');

    // User Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
