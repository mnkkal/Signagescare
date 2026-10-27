<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="mb-6">
        <h2 class="text-xl font-bold text-slate-900">Sign in to your account</h2>
        <p class="text-xs text-slate-500 mt-1">Access breakdown support, asset health and service operations</p>
    </div>

    <form method="POST" action="{{ route('login') }}" class="space-y-4 text-xs">
        @csrf

        <!-- Email Address -->
        <div>
            <label for="email" class="block font-bold text-slate-700 mb-1">Email Address</label>
            <input id="email" class="w-full text-xs rounded-xl border-slate-300 focus:ring-blue-500" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-1" />
        </div>

        <!-- Password -->
        <div>
            <div class="flex items-center justify-between mb-1">
                <label for="password" class="font-bold text-slate-700">Password</label>
                @if (Route::has('password.request'))
                    <a class="text-[11px] text-blue-600 hover:underline" href="{{ route('password.request') }}">
                        Forgot password?
                    </a>
                @endif
            </div>
            <input id="password" class="w-full text-xs rounded-xl border-slate-300 focus:ring-blue-500" type="password" name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-1" />
        </div>

        <!-- Remember Me -->
        <div class="flex items-center justify-between pt-1">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-slate-300 text-blue-600 shadow-sm focus:ring-blue-500" name="remember">
                <span class="ms-2 text-xs text-slate-600">Remember this device</span>
            </label>
        </div>

        <button type="submit" class="w-full py-3 px-4 rounded-xl font-bold text-xs text-white bg-blue-600 hover:bg-blue-700 shadow-lg shadow-blue-500/30 transition-all">
            Sign In to Dashboard
        </button>
    </form>

    <!-- Quick Role Credentials Switcher for Easy Testing -->
    <div class="mt-8 pt-6 border-t border-slate-100 text-left text-xs">
        <div class="font-bold text-slate-800 text-[11px] uppercase tracking-wider mb-2">Quick Access Demo Logins:</div>
        <div class="grid grid-cols-2 gap-2 text-[11px]">
            <button type="button" onclick="fillLogin('store.nagpur@lenskart.com')" class="p-2 rounded-xl bg-blue-50 hover:bg-blue-100 text-blue-900 border border-blue-200 text-left font-medium">
                <span class="block font-bold">Store Manager</span>
                <span class="text-[10px] text-blue-600">Lenskart Nagpur</span>
            </button>
            <button type="button" onclick="fillLogin('admin@signagecare.in')" class="p-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-900 border border-slate-200 text-left font-medium">
                <span class="block font-bold">Super Admin</span>
                <span class="text-[10px] text-slate-500">Full System Access</span>
            </button>
            <button type="button" onclick="fillLogin('ops@signagecare.in')" class="p-2 rounded-xl bg-cyan-50 hover:bg-cyan-100 text-cyan-900 border border-cyan-200 text-left font-medium">
                <span class="block font-bold">Operations Mgr</span>
                <span class="text-[10px] text-cyan-600">SOP & Dispatch</span>
            </button>
            <button type="button" onclick="fillLogin('rohit.tech@signagecare.in')" class="p-2 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-900 border border-emerald-200 text-left font-medium">
                <span class="block font-bold">Field Tech (Rohit)</span>
                <span class="text-[10px] text-emerald-600">Assigned Tickets</span>
            </button>
        </div>
        <p class="text-[10px] text-slate-400 mt-2 text-center">Password for all demo accounts: <code class="font-mono bg-slate-100 px-1 py-0.5 rounded text-slate-700">password</code></p>
    </div>

    <script>
        function fillLogin(email) {
            document.getElementById('email').value = email;
            document.getElementById('password').value = 'password';
        }
    </script>
</x-guest-layout>
