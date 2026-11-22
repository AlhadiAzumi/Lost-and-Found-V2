@php
    $isAuth = filter_var($attributes->get('auth', false), FILTER_VALIDATE_BOOLEAN);
    $pageTitle = $attributes->get('title') ?? config('app.name', 'Lost & Found');
    $currentCampus = request()->route('campus');
    $loginUrl = $currentCampus ? route('campus.login', ['campus' => $currentCampus]) : route('login');
    $registerUrl = $currentCampus ? route('campus.register', ['campus' => $currentCampus]) : route('register');
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $pageTitle }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-['Plus_Jakarta_Sans',sans-serif] antialiased text-slate-900 bg-[#DCE5DC]">
    <div class="min-h-screen grid grid-cols-1 {{ $isAuth ? 'lg:grid-cols-2' : 'lg:grid-cols-12' }}">

        {{-- Panel Kiri: Hero Branding --}}
        <div class="relative hidden lg:flex flex-col justify-between p-12 xl:p-16 bg-[#1B4332] text-white overflow-hidden {{ $isAuth ? '' : 'lg:col-span-5' }}">
            <svg class="absolute inset-0 w-full h-full pointer-events-none opacity-30" viewBox="0 0 800 900" fill="none" preserveAspectRatio="none">
                <path d="M520 -50C430 140 640 280 510 470C380 660 620 780 480 960H850V-50H520Z" fill="#2D6A4F"/>
                <path d="M640 -50C570 160 730 310 610 510C490 710 710 810 590 960H850V-50H640Z" fill="#40916C"/>
                <path d="M350 740C420 620 540 560 660 510C580 630 510 730 470 860L680 820L650 890L360 930L350 740Z" fill="#2D6A4F"/>
            </svg>

            <div class="relative z-10">
                <a href="{{ route('campuses.index') }}" class="inline-flex items-center gap-2.5 text-xl font-bold tracking-tight text-white">
                    <span class="w-3 h-3 rounded-full bg-emerald-400"></span>
                    Lost &amp; Found
                </a>
            </div>

            <div class="relative z-10 max-w-md space-y-4">
                <h1 class="text-4xl xl:text-[42px] font-extrabold leading-[1.15] tracking-tight">
                    Barang hilang, kembali ke pemiliknya.
                </h1>
                <p class="text-base text-emerald-100/85 leading-relaxed">
                    Lapor, cari, dan verifikasi kepemilikan barang di lingkungan kampus dengan aman.
                </p>
            </div>

            <div class="relative z-10 flex items-center gap-3">
                <span class="px-4 py-1.5 rounded-full text-xs font-semibold bg-white text-[#1B4332] shadow-sm">
                    Hilang
                </span>
                <span class="px-4 py-1.5 rounded-full text-xs font-semibold bg-[#10B981] text-white shadow-sm">
                    Ditemukan
                </span>
            </div>
        </div>

        {{-- Panel Kanan --}}
        <div class="relative flex flex-col justify-center items-center p-6 sm:p-10 bg-gradient-to-br from-[#E2EAE2] via-[#D4DFD4] to-[#B7C9B8] overflow-y-auto {{ $isAuth ? '' : 'lg:col-span-7' }}">
            <div class="absolute -bottom-24 -right-24 w-96 h-96 rounded-full bg-[#2D6A4F]/20 blur-3xl pointer-events-none"></div>
            <div class="absolute -top-24 -left-24 w-80 h-80 rounded-full bg-white/40 blur-3xl pointer-events-none"></div>

            @if ($isAuth)
                {{-- Mode Login / Register --}}
                <div class="relative z-10 w-full max-w-[440px] rounded-[28px] bg-white/75 backdrop-blur-xl border border-white/80 shadow-[0_20px_50px_rgba(20,25,35,0.08)] p-7 sm:p-9">
                    <div class="grid grid-cols-2 gap-2.5 mb-7">
                        <a href="{{ $loginUrl }}"
                           class="py-2.5 px-4 rounded-full text-center text-sm font-semibold transition {{ request()->routeIs('login', 'campus.login') ? 'bg-[#141923] text-white shadow-sm' : 'bg-white/90 text-slate-700 hover:bg-white' }}">
                            Masuk
                        </a>
                        <a href="{{ $registerUrl }}"
                           class="py-2.5 px-4 rounded-full text-center text-sm font-semibold transition {{ request()->routeIs('register', 'campus.register') ? 'bg-[#141923] text-white shadow-sm' : 'bg-white/90 text-slate-700 hover:bg-white' }}">
                            Daftar
                        </a>
                    </div>

                    {{ $slot }}
                </div>
            @else
                {{-- Mode Halaman Pilih Kampus --}}
                <div class="relative z-10 w-full max-w-2xl">
                    {{ $slot }}
                </div>
            @endif
        </div>
    </div>
</body>
</html>