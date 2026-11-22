@php
    $currentCampus = request()->route('campus');
    $registerAction = $currentCampus ? route('campus.register.store', ['campus' => $currentCampus]) : route('register');
    $loginLink = $currentCampus ? route('campus.login', ['campus' => $currentCampus]) : route('login');
@endphp
<x-guest-layout :auth="true" title="Daftar Akun | Lost & Found">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-[#141923] tracking-tight">Registrasi Akun</h2>
        <p class="mt-1 text-sm text-slate-600">
            @if ($currentCampus)
                Buat akun untuk portal <span class="font-semibold text-slate-800">{{ $currentCampus->name }}</span>.
            @else
                Daftarkan dirimu untuk melapor atau mengklaim barang di kampus.
            @endif
        </p>
    </div>

    @if ($errors->any())
        <div class="mb-4 rounded-2xl bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ $registerAction }}" class="space-y-3.5">
        @csrf

        <div>
            <label for="name" class="block text-xs font-semibold text-slate-700 mb-1.5">
                Nama Lengkap <span class="text-red-500">*</span>
            </label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus
                   placeholder="Mis. Ahmad Farhan"
                   class="w-full rounded-xl border-slate-200 bg-white/95 px-3.5 py-2.5 text-sm text-slate-900 focus:border-[#1B4332] focus:ring-[#1B4332]">
        </div>

        <div>
            <label for="email" class="block text-xs font-semibold text-slate-700 mb-1.5">
                Alamat Email Kampus <span class="text-red-500">*</span>
            </label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required
                   placeholder="nama@campus.ac.id"
                   class="w-full rounded-xl border-slate-200 bg-white/95 px-3.5 py-2.5 text-sm text-slate-900 focus:border-[#1B4332] focus:ring-[#1B4332]">
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div>
                <label for="nim_nip" class="block text-xs font-semibold text-slate-700 mb-1.5">
                    NIM / NIP
                </label>
                <input id="nim_nip" type="text" name="nim_nip" value="{{ old('nim_nip') }}"
                       placeholder="210100..."
                       class="w-full rounded-xl border-slate-200 bg-white/95 px-3.5 py-2.5 text-sm text-slate-900 focus:border-[#1B4332] focus:ring-[#1B4332]">
            </div>

            <div>
                <label for="phone_number" class="block text-xs font-semibold text-slate-700 mb-1.5">
                    No. WhatsApp
                </label>
                <input id="phone_number" type="text" name="phone_number" value="{{ old('phone_number') }}"
                       placeholder="0812..."
                       class="w-full rounded-xl border-slate-200 bg-white/95 px-3.5 py-2.5 text-sm text-slate-900 focus:border-[#1B4332] focus:ring-[#1B4332]">
            </div>
        </div>

        <div>
            <label for="password" class="block text-xs font-semibold text-slate-700 mb-1.5">
                Kata Sandi <span class="text-red-500">*</span>
            </label>
            <input id="password" type="password" name="password" required
                   placeholder="Minimal 8 karakter"
                   class="w-full rounded-xl border-slate-200 bg-white/95 px-3.5 py-2.5 text-sm text-slate-900 focus:border-[#1B4332] focus:ring-[#1B4332]">
        </div>

        <div>
            <label for="password_confirmation" class="block text-xs font-semibold text-slate-700 mb-1.5">
                Ulangi Kata Sandi <span class="text-red-500">*</span>
            </label>
            <input id="password_confirmation" type="password" name="password_confirmation" required
                   placeholder="Ulangi kata sandi"
                   class="w-full rounded-xl border-slate-200 bg-white/95 px-3.5 py-2.5 text-sm text-slate-900 focus:border-[#1B4332] focus:ring-[#1B4332]">
        </div>

        <div class="pt-1">
            <button type="submit"
                    class="w-full rounded-full bg-[#141923] py-3 px-4 text-sm font-semibold text-white shadow-sm hover:bg-slate-800 transition">
                Daftar Sekarang
            </button>
        </div>
    </form>

    <div class="mt-6 pt-5 border-t border-slate-200/70 text-center space-y-2">
        <p class="text-xs text-slate-600">
            Sudah punya akun?
            <a href="{{ $loginLink }}" class="font-bold text-[#1B4332] hover:underline">Masuk di sini</a>
        </p>
        <p>
            <a href="{{ route('campuses.index') }}" class="text-xs font-medium text-slate-500 hover:text-slate-800">
                &larr; Ganti portal kampus
            </a>
        </p>
    </div>
</x-guest-layout>