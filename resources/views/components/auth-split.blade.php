@props(['mode' => 'login'])

@php
    $activeCampus = request()->route('campus') ?? ($campus ?? null);
    $loginUrl = $activeCampus ? route('campus.login', $activeCampus) : route('login');
    $registerUrl = $activeCampus ? route('campus.register', $activeCampus) : route('register');
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $mode === 'login' ? 'Masuk' : 'Daftar' }} - {{ $activeCampus->name ?? 'Lost & Found' }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-['Plus_Jakarta_Sans',sans-serif] antialiased min-h-screen grid grid-cols-1 lg:grid-cols-2">
    {{-- Panel Kiri: Hijau Hutan Tua (#1f5136) dengan Wave Organik --}}
    <div class="relative hidden lg:flex flex-col justify-between bg-[#1f5136] text-white p-14 overflow-hidden">
        {{-- Dekorasi Gelombang SVG Organik --}}
        <svg class="absolute inset-0 w-full h-full object-cover pointer-events-none opacity-35" viewBox="0 0 800 900" fill="none">
            <path d="M520 -50 C430 150, 620 300, 480 500 C340 700, 580 820, 450 980 L850 980 L850 -50 Z" fill="#2d6a49"/>
            <path d="M640 -50 C560 180, 720 340, 590 540 C460 740, 690 840, 560 980 L850 980 L850 -50 Z" fill="#173e29"/>
        </svg>

        {{-- Brand Top Left --}}
        <div class="relative z-10 flex items-center justify-between">
            <a href="{{ $activeCampus ? route('campus.home', $activeCampus) : route('campuses.index') }}"
               class="text-xl font-extrabold tracking-tight text-white">
                Lost & Found
            </a>
            @if($activeCampus)
                <span class="px-3.5 py-1 rounded-full bg-white/15 backdrop-blur-xs text-xs font-semibold text-white">
                    {{ $activeCampus->name }}
                </span>
            @endif
        </div>

        {{-- Hero Copy Tengah --}}
        <div class="relative z-10 max-w-md space-y-4">
            <h1 class="text-4xl xl:text-[42px] font-extrabold leading-[1.15] tracking-tight">
                Barang hilang, kembali ke pemiliknya.
            </h1>
            <p class="text-sm text-white/80 font-medium leading-relaxed">
                Lapor, cari, dan verifikasi kepemilikan dengan aman.
            </p>
        </div>

        {{-- Bottom Status Pills --}}
        <div class="relative z-10 flex items-center gap-3">
            <span class="px-4 py-1.5 rounded-full bg-white text-slate-900 text-xs font-bold shadow-xs">
                Hilang
            </span>
            <span class="px-4 py-1.5 rounded-full bg-[#10b981] text-white text-xs font-bold shadow-xs">
                Ditemukan
            </span>
        </div>
    </div>

    {{-- Panel Kanan: Background Sage Blur + Kartu Kaca --}}
    <div class="relative flex items-center justify-center p-6 sm:p-10 bg-gradient-to-br from-[#d3e0d2] via-[#dce6db] to-[#c3d5c2] overflow-y-auto">
        {{-- Bentuk Organik Dekoratif di Belakang Card --}}
        <div class="absolute bottom-0 right-0 w-80 h-80 bg-[#1f5136]/20 rounded-tl-full blur-2xl pointer-events-none"></div>

        <div class="relative z-10 w-full max-w-[430px] rounded-[32px] bg-white/75 backdrop-blur-2xl border border-white/80 shadow-[0_20px_50px_rgba(31,81,54,0.12)] p-7 sm:p-9 space-y-6">
            {{-- Top Segmented Pill Tabs: Masuk | Daftar --}}
            <div class="grid grid-cols-2 gap-2 p-1 rounded-full bg-white/60 border border-white/80">
                <a href="{{ $loginUrl }}"
                   class="py-2.5 text-center rounded-full text-xs font-bold transition-all {{ $mode === 'login' ? 'bg-[#161b26] text-white shadow-xs' : 'text-slate-700 hover:text-slate-950' }}">
                    Masuk
                </a>
                <a href="{{ $registerUrl }}"
                   class="py-2.5 text-center rounded-full text-xs font-bold transition-all {{ $mode === 'register' ? 'bg-[#161b26] text-white shadow-xs' : 'text-slate-700 hover:text-slate-950' }}">
                    Daftar
                </a>
            </div>

            {{ $slot }}
        </div>
    </div>
</body>
</html>