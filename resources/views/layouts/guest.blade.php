<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? config('app.name', 'Campus Lost & Found') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-['Plus_Jakarta_Sans',sans-serif] text-slate-800 antialiased">
    @php
        $activeCampus = request()->route('campus') ?? ($campus ?? null);
    @endphp

    <div class="min-h-screen flex flex-col justify-between">
        {{-- Top Floating Glass Header --}}
        <header class="w-full max-w-6xl mx-auto px-4 sm:px-6 pt-6">
            <div class="glass-card px-5 py-3.5 flex items-center justify-between">
                <a href="{{ $activeCampus ? route('campus.home', $activeCampus) : route('campuses.index') }}"
                   class="inline-flex items-center gap-3 group">
                    <div class="w-10 h-10 rounded-2xl bg-[#23593a] text-white flex items-center justify-center shadow-sm group-hover:scale-105 transition-transform">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z" />
                        </svg>
                    </div>
                    <div>
                        <span class="block font-bold text-sm sm:text-base text-slate-900 leading-tight">
                            {{ $activeCampus->name ?? 'Campus Lost & Found' }}
                        </span>
                        <span class="text-xs text-slate-500 font-medium">
                            {{ $activeCampus ? 'Portal Barang Hilang & Temuan' : 'Jaringan Lost & Found Kampus' }}
                        </span>
                    </div>
                </a>

                @if($activeCampus)
                    <a href="{{ route('campuses.index') }}" class="btn-secondary text-xs py-2 px-4">
                        ← Ganti Kampus
                    </a>
                @endif
            </div>
        </header>

        {{-- Main Content --}}
        <main class="flex-1 flex items-center justify-center px-4 sm:px-6 py-10">
            {{ $slot }}
        </main>

        {{-- Footer --}}
        <footer class="py-6 text-center text-xs font-medium text-slate-600/80">
            &copy; {{ date('Y') }} Campus Lost & Found Platform &bull; Aman, Terverifikasi & Transparan
        </footer>
    </div>
</body>
</html>