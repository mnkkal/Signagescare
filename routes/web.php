<?php

use App\Http\Controllers\AmcController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DownloadController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SessionManagerController;
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

    // 3. Asset Register & Health
    Route::resource('assets', AssetController::class)->only(['index', 'create', 'store']);
    Route::post('/assets/{asset}/health', [AssetController::class, 'updateHealth'])->name('assets.update-health');

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

    // User Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
