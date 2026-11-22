<x-guest-layout title="Aktivasi Admin Kampus | Lost & Found">
    <div class="mx-auto max-w-xl">
        <div class="rounded-[28px] bg-white/80 backdrop-blur-xl border border-white/80 shadow-[0_20px_50px_rgba(20,25,35,0.08)] p-7 sm:p-8">
            <div class="mb-6">
                <span class="inline-block text-xs font-bold uppercase tracking-wider text-[#1B4332] bg-emerald-100/80 px-3 py-1 rounded-full mb-2">
                    Portal Disetujui
                </span>
                <h1 class="text-2xl sm:text-[26px] font-bold text-[#141923] tracking-tight">
                    Buat akun admin kampus
                </h1>
                <p class="mt-1.5 text-sm text-slate-600">
                    Portal <strong class="font-semibold text-[#141923]">{{ $application->name }}</strong> telah disetujui. Lengkapi data berikut untuk mengaktifkan akun admin utama.
                </p>
            </div>

            @if ($errors->any())
                <div class="mb-5 rounded-2xl bg-red-50 border border-red-200 px-4 py-3.5 text-sm text-red-700" role="alert">
                    <ul class="list-inside list-disc space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('campus-applications.setup.complete', ['application' => $application, 'token' => $token]) }}" class="space-y-4">
                @csrf

                <div>
                    <label for="admin-name" class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Nama admin
                    </label>
                    <input id="admin-name"
                           name="name"
                           type="text"
                           value="{{ old('name') }}"
                           required
                           maxlength="255"
                           autofocus
                           placeholder="Masukkan nama lengkap admin"
                           class="w-full rounded-xl border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-900 focus:border-[#1B4332] focus:ring-[#1B4332]">
                </div>

                <div>
                    <label for="admin-email" class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Email terdaftar
                    </label>
                    <input id="admin-email"
                           type="email"
                           value="{{ $application->email }}"
                           readonly
                           class="w-full rounded-xl border-slate-200 bg-slate-100/80 px-3.5 py-2.5 text-sm text-slate-500 cursor-not-allowed">
                </div>

                <div>
                    <label for="admin-password" class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Kata sandi
                    </label>
                    <input id="admin-password"
                           type="password"
                           name="password"
                           required
                           autocomplete="new-password"
                           placeholder="Minimal 8 karakter"
                           class="w-full rounded-xl border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-900 focus:border-[#1B4332] focus:ring-[#1B4332]">
                </div>

                <div>
                    <label for="admin-password-confirmation" class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Konfirmasi kata sandi
                    </label>
                    <input id="admin-password-confirmation"
                           type="password"
                           name="password_confirmation"
                           required
                           autocomplete="new-password"
                           placeholder="Ulangi kata sandi"
                           class="w-full rounded-xl border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-900 focus:border-[#1B4332] focus:ring-[#1B4332]">
                </div>

                <div class="pt-2">
                    <button type="submit"
                            class="w-full rounded-full bg-[#141923] px-6 py-3 text-sm font-semibold text-white hover:bg-slate-800 shadow-sm transition">
                        Aktifkan akun admin
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>