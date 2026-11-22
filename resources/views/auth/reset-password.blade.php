<x-guest-layout title="Atur Ulang Kata Sandi | Lost & Found">
    <div class="mx-auto max-w-md">
        <div class="rounded-[28px] bg-white/80 backdrop-blur-xl border border-white/80 shadow-[0_20px_50px_rgba(20,25,35,0.08)] p-7 sm:p-8">
            <div class="mb-6">
                <span class="inline-block text-xs font-bold uppercase tracking-wider text-[#1B4332] bg-emerald-100/80 px-3 py-1 rounded-full mb-2">
                    Pemulihan Akun
                </span>
                <h1 class="text-2xl font-bold text-[#141923] tracking-tight">
                    Atur ulang kata sandi
                </h1>
                <p class="mt-1.5 text-sm text-slate-600">
                    Buat kata sandi baru yang kuat untuk mengamankan kembali akun Anda[cite: 9].
                </p>
            </div>

            <form method="POST" action="{{ route('password.store') }}" class="space-y-4">
                @csrf

                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                <div>
                    <label for="email" class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Alamat Email
                    </label>
                    <input id="email"
                           type="email"
                           name="email"
                           value="{{ old('email', $request->email) }}"
                           required
                           autofocus
                           autocomplete="username"
                           class="w-full rounded-xl border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-900 focus:border-[#1B4332] focus:ring-[#1B4332]">
                    @error('email')
                        <p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Kata Sandi Baru
                    </label>
                    <input id="password"
                           type="password"
                           name="password"
                           required
                           autocomplete="new-password"
                           placeholder="Minimal 8 karakter"
                           class="w-full rounded-xl border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-900 focus:border-[#1B4332] focus:ring-[#1B4332]">
                    @error('password')
                        <p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Konfirmasi Kata Sandi Baru
                    </label>
                    <input id="password_confirmation"
                           type="password"
                           name="password_confirmation"
                           required
                           autocomplete="new-password"
                           placeholder="Ulangi kata sandi baru"
                           class="w-full rounded-xl border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-900 focus:border-[#1B4332] focus:ring-[#1B4332]">
                    @error('password_confirmation')
                        <p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="pt-2">
                    <button type="submit"
                            class="w-full rounded-full bg-[#141923] px-6 py-3 text-sm font-semibold text-white hover:bg-slate-800 shadow-sm transition">
                        Simpan Kata Sandi Baru
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>