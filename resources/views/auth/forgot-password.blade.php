@php
    $campus = $currentCampus ?? request()->route('campus');
    $loginUrl = $campus ? route('campus.login', ['campus' => $campus->slug]) : route('login');
@endphp

<x-guest-layout title="Lupa Kata Sandi | Lost & Found">
    <div class="mx-auto max-w-md">
        <div class="rounded-[28px] bg-white/80 backdrop-blur-xl border border-white/80 shadow-[0_20px_50px_rgba(20,25,35,0.08)] p-7 sm:p-8">
            <div class="mb-6">
                <a href="{{ $loginUrl }}"
                   class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-600 hover:text-[#141923] mb-3 transition">
                    &larr; Kembali ke halaman masuk
                </a>
                <h1 class="text-2xl font-bold text-[#141923] tracking-tight">
                    Lupa kata sandi?
                </h1>
                <p class="mt-1.5 text-sm text-slate-600 leading-relaxed">
                    Masukkan alamat email akun Anda, dan kami akan mengirimkan tautan untuk mengatur ulang kata sandi[cite: 8].
                </p>
            </div>

            @if (session('status'))
                <div class="mb-5 rounded-2xl bg-emerald-50 border border-emerald-200 px-4 py-3.5 text-sm font-medium text-emerald-800" role="status">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
                @csrf

                <div>
                    <label for="email" class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Alamat Email
                    </label>
                    <input id="email"
                           type="email"
                           name="email"
                           value="{{ old('email') }}"
                           required
                           autofocus
                           placeholder="nama@kampus.ac.id"
                           class="w-full rounded-xl border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-900 focus:border-[#1B4332] focus:ring-[#1B4332]">
                    @error('email')
                        <p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="pt-2">
                    <button type="submit"
                            class="w-full rounded-full bg-[#141923] px-6 py-3 text-sm font-semibold text-white hover:bg-slate-800 shadow-sm transition">
                        Kirim Tautan Reset Kata Sandi
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>