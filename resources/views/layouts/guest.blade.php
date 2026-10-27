<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-900">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Signage Care CRM') }} - Sign In</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>
            body { font-family: 'Inter', sans-serif; }
        </style>
    </head>
    <body class="h-full antialiased text-slate-800 bg-gradient-to-br from-[#070e1e] via-[#0c1836] to-[#050a14] flex flex-col justify-center items-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-md text-center mb-6">
            <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-gradient-to-br from-blue-500 to-cyan-400 text-white shadow-xl shadow-blue-500/30 mb-3">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
            </div>
            <div class="text-2xl font-black tracking-wider text-white">
                SIGNAGE<span class="text-blue-400">CARE</span>
            </div>
            <div class="text-[10px] tracking-widest uppercase text-cyan-300/80 font-bold">
                YOUR SIGN &middot; OUR CARE
            </div>
        </div>

        <div class="w-full sm:max-w-md bg-white rounded-3xl p-8 shadow-2xl border border-slate-200/40">
            {{ $slot }}
        </div>

        <div class="mt-8 text-center text-xs text-slate-500">
            &copy; {{ date('Y') }} Signage Care / Backdrop Advertising &middot; Helpline: 8208900802
        </div>
    </body>
</html>
