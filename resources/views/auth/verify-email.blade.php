<x-guest-layout title="Verifikasi Email | Lost & Found">
    <div class="mx-auto max-w-md">
        <div class="rounded-[28px] bg-white/80 backdrop-blur-xl border border-white/80 shadow-[0_20px_50px_rgba(20,25,35,0.08)] p-7 sm:p-8">
            <div class="mb-5">
                <span class="inline-block text-xs font-bold uppercase tracking-wider text-[#1B4332] bg-emerald-100/80 px-3 py-1 rounded-full mb-2">
                    Aktivasi Akun
                </span>
                <h1 class="text-2xl font-bold text-[#141923] tracking-tight">
                    Verifikasi email Anda
                </h1>
                <p class="mt-2 text-sm text-slate-600 leading-relaxed">
                    Terima kasih telah mendaftar! Sebelum memulai, silakan verifikasi alamat email Anda dengan mengklik tautan yang baru saja kami kirimkan. Jika belum menerima email, kami dapat mengirimkannya ulang[cite: 10].
                </p>
            </div>

            @if (session('status') == 'verification-link-sent')
                <div class="mb-5 rounded-2xl bg-emerald-50 border border-emerald-200 px-4 py-3.5 text-sm font-medium text-emerald-800" role="status">
                    Tautan verifikasi baru telah dikirimkan ke alamat email yang Anda daftarkan[cite: 10].
                </div>
            @endif

            <div class="mt-6 flex flex-col sm:flex-row items-center justify-between gap-3">
                <form method="POST" action="{{ route('verification.send') }}" class="w-full sm:w-auto">
                    @csrf
                    <button type="submit"
                            class="w-full rounded-full bg-[#141923] px-5 py-2.5 text-sm font-semibold text-white hover:bg-slate-800 shadow-sm transition">
                        Kirim Ulang Email Verifikasi
                    </button>
                </form>

                <form method="POST" action="{{ route('logout') }}" class="w-full sm:w-auto">
                    @csrf
                    <button type="submit"
                            class="w-full rounded-full border border-slate-300 bg-white/70 px-5 py-2.5 text-sm font-semibold text-slate-700 hover:bg-white hover:text-[#141923] transition">
                        Keluar
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-guest-layout>