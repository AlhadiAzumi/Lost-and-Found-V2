<section class="space-y-5">
    <header>
        <h2 class="text-lg font-extrabold text-[#141923]">
            Ubah Kata Sandi
        </h2>
        <p class="mt-1 text-xs sm:text-sm text-slate-600">
            Pastikan akun Anda menggunakan kata sandi yang panjang dan acak agar tetap aman.
        </p>
    </header>

    <form method="post" action="{{ route('campus.password.update', ['campus' => $currentCampus->slug]) }}" class="space-y-4">
        @csrf
        @method('put')

        <div>
            <label for="update_password_current_password" class="block text-xs font-semibold text-slate-700 mb-1.5">
                Kata Sandi Saat Ini
            </label>
            <input id="update_password_current_password" name="current_password" type="password"
                   autocomplete="current-password"
                   class="w-full rounded-2xl border border-white/70 bg-white/60 backdrop-blur-md px-4 py-2.5 text-sm text-slate-900 focus:bg-white/85 focus:border-[#141923] focus:ring-[#141923]">
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-1.5" />
        </div>

        <div>
            <label for="update_password_password" class="block text-xs font-semibold text-slate-700 mb-1.5">
                Kata Sandi Baru
            </label>
            <input id="update_password_password" name="password" type="password"
                   autocomplete="new-password"
                   class="w-full rounded-2xl border border-white/70 bg-white/60 backdrop-blur-md px-4 py-2.5 text-sm text-slate-900 focus:bg-white/85 focus:border-[#141923] focus:ring-[#141923]">
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-1.5" />
        </div>

        <div>
            <label for="update_password_password_confirmation" class="block text-xs font-semibold text-slate-700 mb-1.5">
                Konfirmasi Kata Sandi Baru
            </label>
            <input id="update_password_password_confirmation" name="password_confirmation" type="password"
                   autocomplete="new-password"
                   class="w-full rounded-2xl border border-white/70 bg-white/60 backdrop-blur-md px-4 py-2.5 text-sm text-slate-900 focus:bg-white/85 focus:border-[#141923] focus:ring-[#141923]">
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-1.5" />
        </div>

        <div class="pt-2 flex items-center gap-4">
            <button type="submit"
                    class="rounded-full bg-[#141923] hover:bg-slate-800 px-6 py-2.5 text-xs sm:text-sm font-semibold text-white shadow-sm transition">
                Perbarui Kata Sandi
            </button>

            @if (session('status') === 'password-updated')
                <p x-data="{ show: true }"
                   x-show="show"
                   x-transition
                   x-init="setTimeout(() => show = false, 2500)"
                   class="text-xs sm:text-sm font-semibold text-emerald-800">
                    Kata sandi berhasil diperbarui.
                </p>
            @endif
        </div>
    </form>
</section>