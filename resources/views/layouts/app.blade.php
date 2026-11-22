<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? config('app.name', 'Lost & Found') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-['Plus_Jakarta_Sans',sans-serif] text-slate-900 antialiased relative">
    {{-- Siluet Daun SVG Ringan di Pojok Kanan Bawah (Persis katalog_2.jpg, 0 GPU lag) --}}
    <svg class="fixed bottom-0 right-0 w-[420px] h-[420px] pointer-events-none -z-10 opacity-45" viewBox="0 0 400 400" fill="none">
        <path d="M160 400 C180 290, 270 210, 400 180 L400 400 Z" fill="#4f8361"/>
        <path d="M250 400 C260 320, 320 270, 400 260 L400 400 Z" fill="#23593a"/>
        <path d="M90 400 C130 330, 190 310, 240 400 Z" fill="#76a384"/>
    </svg>

    <div class="min-h-screen">
        @include('layouts.navigation')

        <div class="lg:pl-[256px] min-h-screen">
            <main class="w-full px-5 sm:px-8 lg:px-9 py-6 lg:py-7 space-y-6">
                @if(session('success'))
                    <div class="glass-card bg-emerald-50/95 border-emerald-200 px-5 py-3 text-sm font-semibold text-[#23593a]">
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="glass-card bg-rose-50/95 border-rose-200 px-5 py-3 text-sm font-semibold text-rose-700">
                        {{ session('error') }}
                    </div>
                @endif

                {{ $slot }}
            </main>
        </div>
    </div>
</body>
</html>