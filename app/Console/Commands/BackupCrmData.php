<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class BackupCrmData extends Command
{
    protected $signature = 'crm:backup';
    protected $description = 'Create a daily JSON snapshot backup of Signage Care CRM tables';

    public function handle(): int
    {
        $dir = storage_path('app/backups');
        File::ensureDirectoryExists($dir);

        $tables = [
            'users', 'clients', 'client_locations', 'signage_assets', 'amc_contracts',
            'tickets', 'ticket_visits', 'safety_checklists', 'safety_incidents',
            'quotations', 'quotation_items', 'invoices', 'invoice_items', 'payments',
            'spare_parts', 'suppliers', 'settings', 'audit_logs',
        ];

        $snapshot = [
            'generated_at' => now()->toIso8601String(),
            'tables' => [],
        ];

        foreach ($tables as $table) {
            try {
                $snapshot['tables'][$table] = DB::table($table)->get()->toArray();
            } catch (\Throwable) {
                // Skip if table not present
            }
        }

        $filename = 'crm_backup_' . date('Y-m-d_His') . '.json';
        File::put($dir . DIRECTORY_SEPARATOR . $filename, json_encode($snapshot, JSON_PRETTY_PRINT));

        $this->info("Backup saved: {$filename}");
        return self::SUCCESS;
    }
}
