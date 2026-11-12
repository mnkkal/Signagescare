<x-app-layout>
<div class="space-y-6 max-w-7xl mx-auto">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-slate-200">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">System Settings &amp; Compliance</h1>
            <p class="text-xs text-slate-500 mt-1">Manage support contact details, billing thresholds, notification webhooks, and DPDP data backups.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('privacy.policy') }}" class="px-4 py-2 rounded-xl border border-slate-300 bg-white text-slate-700 text-xs font-semibold hover:bg-slate-50">
                View DPDP Privacy Notice
            </a>
            <form method="POST" action="{{ route('settings.backup') }}">
                @csrf
                <button type="submit" class="px-4 py-2 rounded-xl bg-emerald-600 text-white text-xs font-semibold hover:bg-emerald-700">
                    Run Manual Backup Now
                </button>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-slate-200/80 p-6">
            <h2 class="text-base font-bold text-slate-900 mb-4">Company, Support &amp; Alert Configuration</h2>
            <form method="POST" action="{{ route('settings.update') }}" class="space-y-4 text-xs">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Company Name</label>
                        <input type="text" name="company_name" value="{{ old('company_name', $settings['company_name'] ?? 'Signages Care') }}" required class="w-full rounded-xl border-slate-300 text-xs">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Company GSTIN</label>
                        <input type="text" name="company_gstin" value="{{ old('company_gstin', $settings['company_gstin'] ?? '') }}" placeholder="27AABCU9603R1ZM" class="w-full rounded-xl border-slate-300 text-xs">
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Company Registered Address</label>
                    <textarea name="company_address" rows="2" class="w-full rounded-xl border-slate-300 text-xs">{{ old('company_address', $settings['company_address'] ?? '') }}</textarea>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-2 border-t border-slate-100">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Support Phone (Dynamic)</label>
                        <input type="text" name="support_phone" value="{{ old('support_phone', $settings['support_phone'] ?? '8208900802') }}" required class="w-full rounded-xl border-slate-300 text-xs">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Support Email</label>
                        <input type="email" name="support_email" value="{{ old('support_email', $settings['support_email'] ?? 'care@signagescare.com') }}" required class="w-full rounded-xl border-slate-300 text-xs">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Support Website</label>
                        <input type="text" name="support_website" value="{{ old('support_website', $settings['support_website'] ?? 'https://signagescare.com') }}" class="w-full rounded-xl border-slate-300 text-xs">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2 border-t border-slate-100">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Quotation Approval Threshold (INR)</label>
                        <input type="number" step="0.01" min="0" name="quotation_approval_threshold" value="{{ old('quotation_approval_threshold', $settings['quotation_approval_threshold'] ?? '5000') }}" required class="w-full rounded-xl border-slate-300 text-xs">
                        <p class="text-[11px] text-slate-500 mt-1">Repairs above this amount require explicit client approval before work.</p>
                    </div>
                </div>

                <div class="space-y-3 pt-2 border-t border-slate-100">
                    <h3 class="text-xs font-bold text-slate-800">Customer Communication Channels (WhatsApp / SMS / Email)</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">WhatsApp Business Webhook URL</label>
                            <input type="url" name="whatsapp_webhook_url" value="{{ old('whatsapp_webhook_url', $settings['whatsapp_webhook_url'] ?? '') }}" placeholder="https://api.provider.com/whatsapp/send" class="w-full rounded-xl border-slate-300 text-xs">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">SMS Gateway Webhook URL</label>
                            <input type="url" name="sms_webhook_url" value="{{ old('sms_webhook_url', $settings['sms_webhook_url'] ?? '') }}" placeholder="https://api.provider.com/sms/send" class="w-full rounded-xl border-slate-300 text-xs">
                        </div>
                    </div>
                    <div class="flex flex-wrap gap-6 pt-1">
                        <label class="inline-flex items-center gap-2 text-xs text-slate-700">
                            <input type="checkbox" name="sms_alerts_enabled" value="1" {{ ($settings['sms_alerts_enabled'] ?? '0') === '1' ? 'checked' : '' }} class="rounded border-slate-300 text-blue-600">
                            Enable SMS Alerts
                        </label>
                        <label class="inline-flex items-center gap-2 text-xs text-slate-700">
                            <input type="checkbox" name="email_alerts_enabled" value="1" {{ ($settings['email_alerts_enabled'] ?? '1') === '1' ? 'checked' : '' }} class="rounded border-slate-300 text-blue-600">
                            Enable Email Alerts
                        </label>
                    </div>
                </div>

                <div class="pt-3">
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-blue-600 text-white text-xs font-semibold hover:bg-blue-700">
                        Save Settings
                    </button>
                </div>
            </form>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-6 space-y-4">
            <h2 class="text-base font-bold text-slate-900">Recent Backups (DPDP Act 2023)</h2>
            <p class="text-xs text-slate-500">JSON snapshots stored in <code class="bg-slate-100 px-1 py-0.5 rounded">storage/app/backups</code> via <code class="bg-slate-100 px-1 py-0.5 rounded">php artisan crm:backup</code>.</p>
            @if(empty($backups))
                <p class="text-xs text-slate-500 italic">No backups generated yet.</p>
            @else
                <ul class="divide-y divide-slate-100">
                    @foreach($backups as $backup)
                        <li class="py-2.5 flex items-center justify-between gap-2 text-xs">
                            <div class="min-w-0">
                                <p class="font-semibold text-slate-800 truncate">{{ $backup['name'] }}</p>
                                <p class="text-[11px] text-slate-500">{{ $backup['date'] }}</p>
                            </div>
                            <span class="text-[11px] font-mono text-slate-600 shrink-0">{{ number_format($backup['size'] / 1024, 1) }} KB</span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200">
            <h2 class="text-base font-bold text-slate-900">Recent Customer Notification Logs</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-xs">
                <thead class="bg-slate-50 text-[10px] uppercase text-slate-500">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold">Time</th>
                        <th class="px-4 py-3 text-left font-semibold">Channel</th>
                        <th class="px-4 py-3 text-left font-semibold">Event</th>
                        <th class="px-4 py-3 text-left font-semibold">Recipient</th>
                        <th class="px-4 py-3 text-left font-semibold">Message</th>
                        <th class="px-4 py-3 text-left font-semibold">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($notificationLogs as $log)
                        <tr>
                            <td class="px-4 py-3 text-slate-500 whitespace-nowrap">{{ $log->created_at?->format('d M Y, H:i') }}</td>
                            <td class="px-4 py-3 uppercase font-bold text-blue-700">{{ $log->channel }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ $log->event }}</td>
                            <td class="px-4 py-3 text-slate-700">{{ $log->recipient }}</td>
                            <td class="px-4 py-3 text-slate-600 max-w-md truncate">{{ $log->message }}</td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold {{ $log->status === 'sent' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                                    {{ ucfirst($log->status) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-6 text-center text-slate-500">No notifications logged yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
</x-app-layout>
