<section class="space-y-5">
    <header>
        <h2 class="text-lg font-extrabold text-[#141923]">
            Informasi Profil
        </h2>
        <p class="mt-1 text-xs sm:text-sm text-slate-600">
            Perbarui nama tampilan dan alamat email akun Anda.
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('campus.verification.send', ['campus' => $currentCampus->slug]) }}">
        @csrf
    </form>

    <form method="post" action="{{ route('campus.profile.update', ['campus' => $currentCampus->slug]) }}" class="space-y-4">
        @csrf
        @method('patch')

        <div>
            <label for="name" class="block text-xs font-semibold text-slate-700 mb-1.5">
                Nama Lengkap
            </label>
            <input id="name" name="name" type="text"
                   value="{{ old('name', $user->name) }}" required autofocus autocomplete="name"
                   class="w-full rounded-2xl border border-white/70 bg-white/60 backdrop-blur-md px-4 py-2.5 text-sm text-slate-900 placeholder-slate-400 focus:bg-white/85 focus:border-[#141923] focus:ring-[#141923]">
            <x-input-error class="mt-1.5" :messages="$errors->get('name')" />
        </div>

        <div>
            <label for="email" class="block text-xs font-semibold text-slate-700 mb-1.5">
                Alamat Email
            </label>
            <input id="email" name="email" type="email"
                   value="{{ old('email', $user->email) }}" required autocomplete="username"
                   class="w-full rounded-2xl border border-white/70 bg-white/60 backdrop-blur-md px-4 py-2.5 text-sm text-slate-900 placeholder-slate-400 focus:bg-white/85 focus:border-[#141923] focus:ring-[#141923]">
            <x-input-error class="mt-1.5" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-3 rounded-2xl bg-amber-500/15 border border-amber-400/40 p-3.5 text-xs sm:text-sm text-amber-950">
                    <p>
                        Alamat email Anda belum terverifikasi.
                        <button form="send-verification" class="font-bold underline hover:text-[#141923] ml-1">
                            Klik di sini untuk mengirim ulang tautan verifikasi.
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-semibold text-emerald-800">
                            Tautan verifikasi baru telah dikirim ke alamat email Anda.
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="pt-2 flex items-center gap-4">
            <button type="submit"
                    class="rounded-full bg-[#141923] hover:bg-slate-800 px-6 py-2.5 text-xs sm:text-sm font-semibold text-white shadow-sm transition">
                Simpan Perubahan
            </button>

            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }"
                   x-show="show"
                   x-transition
                   x-init="setTimeout(() => show = false, 2500)"
                   class="text-xs sm:text-sm font-semibold text-emerald-800">
                    Profil berhasil disimpan.
                </p>
            @endif
        </div>
    </form>
</section>