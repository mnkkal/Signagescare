<x-app-layout>
<div class="max-w-4xl mx-auto space-y-6">
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-6 sm:p-8 space-y-5">
        <div class="border-b border-slate-200 pb-4">
            <h1 class="text-2xl font-bold text-slate-900">Privacy &amp; Personal Data Protection Notice</h1>
            <p class="text-xs text-slate-500 mt-1">In compliance with India's Digital Personal Data Protection (DPDP) Act, 2023</p>
        </div>

        <div class="space-y-4 text-xs sm:text-sm text-slate-700 leading-relaxed">
            <section>
                <h2 class="font-bold text-slate-900 text-sm sm:text-base">1. Data We Collect</h2>
                <p>To deliver signage maintenance, AMC, billing, and field safety operations, {{ \App\Models\Setting::getValue('company_name', 'Signages Care') }} collects client contact details (name, phone, email, branch address), site photos/videos, technician GPS check-in coordinates, and GST billing records.</p>
            </section>

            <section>
                <h2 class="font-bold text-slate-900 text-sm sm:text-base">2. Purpose Limitation</h2>
                <p>Personal data is used strictly for service ticket dispatch, SLA tracking, safety compliance audits, GST invoicing, and service status notifications (WhatsApp/SMS/Email). We do not sell or share personal data with unauthorized third parties.</p>
            </section>

            <section>
                <h2 class="font-bold text-slate-900 text-sm sm:text-base">3. Access Control &amp; Backups</h2>
                <p>Access is restricted via Role-Based Access Control (RBAC). Clients only see their own organization's branches, assets, tickets, and invoices. Regular JSON backups are maintained for operational continuity.</p>
            </section>

            <section>
                <h2 class="font-bold text-slate-900 text-sm sm:text-base">4. Data Principal Rights &amp; Grievance Contact</h2>
                <p>You may request correction or erasure of personal contact records once statutory GST/safety retention obligations expire. Contact our Support Desk at <strong>{{ \App\Models\Setting::getValue('support_phone', '8208900802') }}</strong> or <strong>{{ \App\Models\Setting::getValue('support_email', 'care@signagescare.com') }}</strong>.</p>
            </section>
        </div>

        @auth
            <div class="pt-4 border-t border-slate-200 flex flex-wrap items-center justify-between gap-4">
                <div class="text-xs text-slate-600">
                    @if(auth()->user()->consent_accepted_at)
                        Consent recorded on <strong>{{ auth()->user()->consent_accepted_at->format('d M Y, H:i') }}</strong>.
                    @else
                        Please record your consent to continue using field &amp; portal features.
                    @endif
                </div>
                @if(!auth()->user()->consent_accepted_at)
                    <form method="POST" action="{{ route('consent.accept') }}">
                        @csrf
                        <button type="submit" class="px-5 py-2 rounded-xl bg-blue-600 text-white text-xs font-semibold hover:bg-blue-700">
                            Accept DPDP Consent Terms
                        </button>
                    </form>
                @endif
            </div>
        @endauth
    </div>
</div>
</x-app-layout>
