<?php

namespace App\Http\Controllers;

use App\Models\NotificationLog;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class SettingController extends Controller
{
    public function index()
    {
        $settings = [
            'company_name' => Setting::getValue('company_name', 'Signage Care Services Pvt. Ltd.'),
            'company_gstin' => Setting::getValue('company_gstin', '27AABCS1234F1Z5'),
            'company_address' => Setting::getValue('company_address', 'Plot 42, MIDC Hingna, Nagpur, Maharashtra 440016'),
            'support_phone' => Setting::getValue('support_phone', '8208900802'),
            'support_email' => Setting::getValue('support_email', 'care@signagecare.in'),
            'support_website' => Setting::getValue('support_website', 'Signagecare.in'),
            'quotation_approval_threshold' => Setting::getValue('quotation_approval_threshold', '5000'),
            'whatsapp_webhook_url' => Setting::getValue('whatsapp_webhook_url', ''),
            'sms_webhook_url' => Setting::getValue('sms_webhook_url', ''),
            'sms_alerts_enabled' => Setting::getValue('sms_alerts_enabled', '1'),
            'email_alerts_enabled' => Setting::getValue('email_alerts_enabled', '0'),
        ];

        $notificationLogs = NotificationLog::with('ticket')->latest()->take(25)->get();

        $backupDir = storage_path('app/backups');
        $backups = File::exists($backupDir)
            ? collect(File::files($backupDir))->map(fn ($f) => [
                'name' => $f->getFilename(),
                'size' => round($f->getSize() / 1024, 1) . ' KB',
                'date' => date('d M Y, h:i A', $f->getMTime()),
            ])->sortByDesc('name')->values()
            : collect();

        return view('settings.index', compact('settings', 'notificationLogs', 'backups'));
    }

    public function update(Request $request)
    {
        $keys = [
            'company_name', 'company_gstin', 'company_address',
            'support_phone', 'support_email', 'support_website',
            'quotation_approval_threshold', 'whatsapp_webhook_url', 'sms_webhook_url',
        ];

        foreach ($keys as $key) {
            Setting::setValue($key, $request->input($key, ''), 'general');
        }

        Setting::setValue('sms_alerts_enabled', $request->boolean('sms_alerts_enabled') ? '1' : '0', 'notifications');
        Setting::setValue('email_alerts_enabled', $request->boolean('email_alerts_enabled') ? '1' : '0', 'notifications');

        return back()->with('status', 'System settings updated successfully (no code change needed).');
    }

    public function runBackup()
    {
        Artisan::call('crm:backup');
        return back()->with('status', 'Database & compliance backup snapshot created in storage/app/backups.');
    }

    public function acceptConsent(Request $request)
    {
        $user = Auth::user();
        if ($user) {
            $user->update([
                'consent_accepted_at' => now(),
                'consent_ip' => $request->ip(),
            ]);
        }

        return back()->with('status', 'DPDP Act 2023 Data & Privacy Consent recorded.');
    }

    public function privacyPolicy()
    {
        return view('settings.privacy');
    }
}
