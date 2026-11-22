<section class="space-y-4">
    <header>
        <h2 class="text-lg font-extrabold text-rose-900">
            Hapus Akun
        </h2>
        <p class="mt-1 text-xs sm:text-sm text-slate-600">
            Setelah akun dihapus, seluruh data laporan dan riwayat klaim Anda akan dihapus secara permanen.
        </p>
    </header>

    <div>
        <button type="button"
                x-data=""
                x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
                class="rounded-full bg-rose-600 hover:bg-rose-700 px-6 py-2.5 text-xs sm:text-sm font-semibold text-white shadow-sm transition">
            Hapus Akun Permanen
        </button>
    </div>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('campus.profile.destroy', ['campus' => $currentCampus->slug]) }}"
              class="p-6 sm:p-7 bg-white/95 backdrop-blur-xl space-y-4">
            @csrf
            @method('delete')

            <h2 class="text-lg font-extrabold text-[#141923]">
                Yakin ingin menghapus akun Anda?
            </h2>

            <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                Tindakan ini tidak dapat dibatalkan. Masukkan kata sandi Anda untuk mengonfirmasi penghapusan akun secara permanen.
            </p>

            <div>
                <label for="delete_account_password" class="sr-only">Kata Sandi</label>
                <input id="delete_account_password"
                       name="password"
                       type="password"
                       placeholder="Masukkan kata sandi Anda"
                       class="w-full sm:w-3/4 rounded-2xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 focus:border-[#141923] focus:ring-[#141923]">
                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-1.5" />
            </div>

            <div class="pt-2 flex items-center justify-end gap-3">
                <button type="button"
                        x-on:click="$dispatch('close')"
                        class="rounded-full bg-slate-100 hover:bg-slate-200 px-5 py-2.5 text-xs sm:text-sm font-semibold text-slate-800 transition">
                    Batal
                </button>

                <button type="submit"
                        class="rounded-full bg-rose-600 hover:bg-rose-700 px-6 py-2.5 text-xs sm:text-sm font-semibold text-white shadow-sm transition">
                    Ya, Hapus Akun
                </button>
            </div>
        </form>
    </x-modal>
</section>