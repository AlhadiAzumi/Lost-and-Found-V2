@php
    $currentCampus = request()->route('campus');
    $loginAction = $currentCampus ? route('campus.login', ['campus' => $currentCampus]) : route('login');
    $registerLink = $currentCampus ? route('campus.register', ['campus' => $currentCampus]) : route('register');
@endphp
<x-guest-layout :auth="true" title="Masuk | Lost & Found">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-[#141923] tracking-tight">Masuk ke Akun</h2>
        <p class="mt-1 text-sm text-slate-600">
            @if ($currentCampus)
                Masuk ke portal <span class="font-semibold text-slate-800">{{ $currentCampus->name }}</span>.
            @else
                Gunakan akun terdaftar untuk melanjutkan.
            @endif
        </p>
    </div>

    @if (session('status'))
        <div class="mb-4 rounded-2xl bg-emerald-50 border border-emerald-200 px-4 py-3 text-sm text-emerald-700">
            {{ session('status') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-4 rounded-2xl bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ $loginAction }}" class="space-y-4">
        @csrf

        <div>
            <label for="email" class="block text-xs font-semibold text-slate-700 mb-1.5">
                Alamat Email <span class="text-red-500">*</span>
            </label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                   placeholder="nama@campus.ac.id"
                   class="w-full rounded-xl border-slate-200 bg-white/95 px-3.5 py-2.5 text-sm text-slate-900 focus:border-[#1B4332] focus:ring-[#1B4332]">
        </div>

        <div>
            <label for="password" class="block text-xs font-semibold text-slate-700 mb-1.5">
                Kata Sandi <span class="text-red-500">*</span>
            </label>
            <input id="password" type="password" name="password" required
                   placeholder="Minimal 8 karakter"
                   class="w-full rounded-xl border-slate-200 bg-white/95 px-3.5 py-2.5 text-sm text-slate-900 focus:border-[#1B4332] focus:ring-[#1B4332]">
        </div>

        <div class="flex items-center justify-between pt-1">
            <label for="remember" class="inline-flex items-center gap-2 cursor-pointer text-xs font-medium text-slate-600">
                <input id="remember" type="checkbox" name="remember"
                       class="rounded border-slate-300 text-[#1B4332] focus:ring-[#1B4332]">
                Ingat saya
            </label>

            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="text-xs font-semibold text-[#1B4332] hover:underline">
                    Lupa sandi?
                </a>
            @endif
        </div>

        <button type="submit"
                class="w-full rounded-full bg-[#141923] py-3 px-4 text-sm font-semibold text-white shadow-sm hover:bg-slate-800 transition">
            Masuk Sekarang
        </button>
    </form>

    <div class="mt-6 pt-5 border-t border-slate-200/70 text-center space-y-2">
        <p class="text-xs text-slate-600">
            Belum punya akun?
            <a href="{{ $registerLink }}" class="font-bold text-[#1B4332] hover:underline">Daftar di sini</a>
        </p>
        <p>
            <a href="{{ route('campuses.index') }}" class="text-xs font-medium text-slate-500 hover:text-slate-800">
                &larr; Ganti portal kampus
            </a>
        </p>
    </div>
</x-guest-layout>