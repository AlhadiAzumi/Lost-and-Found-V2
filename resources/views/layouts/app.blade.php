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
    <body class="font-['Plus_Jakarta_Sans',sans-serif] antialiased text-slate-900 bg-[#D6E1D6] selection:bg-[#141923] selection:text-white">
        {{-- Background Gradient & Siluet Daun Halus --}}
        <div class="fixed inset-0 -z-10 overflow-hidden pointer-events-none bg-gradient-to-br from-[#E3ECE3] via-[#D2DFD3] to-[#AFC4B1]">
            <div class="absolute -top-24 left-1/3 w-[500px] h-[500px] rounded-full bg-white/45 blur-3xl"></div>
            <div class="absolute -bottom-32 -right-20 w-[620px] h-[620px] rounded-full bg-[#3A6B4C]/20 blur-3xl"></div>
            <svg class="absolute bottom-0 right-0 w-[560px] h-[560px] text-[#2D5A3F]/15" viewBox="0 0 500 500" fill="currentColor">
                <path d="M480 500C470 360 390 250 260 210C320 310 340 400 320 500H480Z"/>
                <path d="M500 380C410 310 310 290 190 320C280 370 360 420 430 500H500V380Z"/>
                <path d="M360 500C330 390 250 320 130 310C210 380 260 440 280 500H360Z"/>
            </svg>
        </div>

        <div class="min-h-screen flex flex-col lg:flex-row gap-6 p-4 sm:p-6 max-w-[1440px] mx-auto">
            {{-- Floating Left Sidebar --}}
            @include('layouts.navigation')

            {{-- Main Content Area --}}
            <div class="flex-1 min-w-0 flex flex-col">
                @isset($header)
                    <header class="mb-6">
                        {{ $header }}
                    </header>
                @endisset

                <main class="flex-1">
                    {{ $slot }}
                </main>
            </div>
        </div>
    </body>
</html>