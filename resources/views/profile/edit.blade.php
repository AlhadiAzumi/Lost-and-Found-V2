@php
    $campus = $currentCampus ?? request()->route('campus');
    $user = auth()->user();
    $roleLabels = [
        'admin' => 'Admin Kampus',
        'satpam' => 'Petugas / Satpam',
        'platform_admin' => 'Platform Admin',
        'user' => 'Civitas Kampus',
    ];
    $roleText = $roleLabels[$user->role ?? 'user'] ?? ucfirst($user->role ?? 'Pengguna');
@endphp

<x-app-layout>
    <div class="max-w-4xl mx-auto space-y-6 pb-8">
        {{-- Header Profil --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pt-1">
            <div>
                <h1 class="text-2xl sm:text-[28px] font-extrabold tracking-tight text-[#141923]">
                    Pengaturan Profil
                </h1>
                <p class="mt-1 text-xs sm:text-sm text-slate-600">
                    Kelola informasi akun, alamat email, dan keamanan kata sandi Anda.
                </p>
            </div>
        </div>

        {{-- Ringkasan Identitas Akun --}}
        <div class="rounded-[28px] bg-white/40 backdrop-blur-xl border border-white/65 shadow-[0_12px_36px_rgba(20,25,35,0.06)] p-6 flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center gap-4 min-w-0">
                <div class="h-14 w-14 shrink-0 rounded-full bg-[#141923] text-white flex items-center justify-center text-lg font-extrabold tracking-wider shadow-sm">
                    {{ strtoupper(mb_substr($user->name, 0, 2)) }}
                </div>
                <div class="min-w-0">
                    <h2 class="text-lg font-extrabold text-[#141923] truncate">{{ $user->name }}</h2>
                    <p class="text-xs sm:text-sm text-slate-600 truncate">{{ $user->email }}</p>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <span class="rounded-full bg-emerald-100/90 border border-emerald-300/60 px-3.5 py-1 text-xs font-bold text-emerald-900">
                    {{ $roleText }}
                </span>
                @if ($campus)
                    <span class="rounded-full bg-white/65 border border-white/80 px-3.5 py-1 text-xs font-semibold text-slate-800">
                        {{ $campus->name }}
                    </span>
                @endif
            </div>
        </div>

        {{-- Kartu 1: Informasi Profil --}}
        <div class="rounded-[28px] bg-white/40 backdrop-blur-xl border border-white/65 shadow-[0_12px_36px_rgba(20,25,35,0.06)] p-6 sm:p-8">
            <div class="max-w-2xl">
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>

        {{-- Kartu 2: Ubah Kata Sandi --}}
        <div class="rounded-[28px] bg-white/40 backdrop-blur-xl border border-white/65 shadow-[0_12px_36px_rgba(20,25,35,0.06)] p-6 sm:p-8">
            <div class="max-w-2xl">
                @include('profile.partials.update-password-form')
            </div>
        </div>

        {{-- Kartu 3: Hapus Akun --}}
        <div class="rounded-[28px] bg-white/40 backdrop-blur-xl border border-rose-300/50 shadow-[0_12px_36px_rgba(20,25,35,0.06)] p-6 sm:p-8">
            <div class="max-w-2xl">
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>
</x-app-layout>