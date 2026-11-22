<x-app-layout>
    <div class="space-y-6 pb-8">
        {{-- Header --}}
        <div class="pt-1">
            <h1 class="text-2xl sm:text-[28px] font-extrabold tracking-tight text-[#141923]">
                Kelola Portal Kampus
            </h1>
            <p class="mt-1 text-xs sm:text-sm text-slate-600">
                Kelola pendaftaran instansi baru, undangan admin kampus, dan status aktif portal multitenant.
            </p>
        </div>

        @foreach (['success' => 'bg-emerald-500/15 border-emerald-400/40 text-emerald-950', 'error' => 'bg-rose-500/15 border-rose-400/40 text-rose-950'] as $key => $styles)
            @if (session($key))
                <div class="rounded-[22px] backdrop-blur-xl border px-4 py-3.5 text-sm font-medium {{ $styles }}" role="status">
                    {{ session($key) }}
                </div>
            @endif
        @endforeach

        @if ($errors->any())
            <div class="rounded-[22px] bg-rose-500/15 backdrop-blur-xl border border-rose-400/40 p-4 text-sm text-rose-950" role="alert">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        {{-- Buat Portal Secara Langsung --}}
        <section class="rounded-[26px] bg-white/40 backdrop-blur-xl border border-white/65 shadow-[0_12px_36px_rgba(20,25,35,0.06)] p-6 space-y-4">
            <h2 class="text-base font-extrabold text-[#141923]">Buat Portal Secara Langsung</h2>
            <form method="POST" action="{{ route('platform.campuses.store') }}" class="grid gap-3 sm:grid-cols-3">
                @csrf
                <input name="name" required maxlength="150" placeholder="Nama kampus (mis. Politeknik Negeri Lhokseumawe)"
                       class="rounded-full border border-white/75 bg-white/65 px-5 py-2.5 text-xs sm:text-sm text-slate-900 placeholder-slate-400 focus:bg-white focus:border-[#141923] focus:ring-[#141923]">
                <input name="slug" required maxlength="100" placeholder="slug-kampus (mis. pnl)"
                       class="rounded-full border border-white/75 bg-white/65 px-5 py-2.5 text-xs sm:text-sm text-slate-900 placeholder-slate-400 focus:bg-white focus:border-[#141923] focus:ring-[#141923]">
                <button type="submit"
                        class="rounded-full bg-[#141923] hover:bg-slate-800 px-6 py-2.5 text-xs sm:text-sm font-semibold text-white shadow-sm transition">
                    + Buat Portal
                </button>
            </form>
        </section>

        {{-- Permohonan Portal Menunggu Tinjauan --}}
        <section class="rounded-[26px] bg-white/40 backdrop-blur-xl border border-white/65 shadow-[0_12px_36px_rgba(20,25,35,0.06)] p-6 space-y-4">
            <h2 class="text-base font-extrabold text-[#141923]">Permohonan Portal Menunggu Tinjauan</h2>
            <div class="space-y-3">
                @forelse ($pendingApplications as $application)
                    <div class="flex flex-wrap items-center justify-between gap-3 rounded-2xl bg-white/55 border border-white/75 p-4">
                        <div>
                            <p class="font-bold text-slate-900">{{ $application->name }}</p>
                            <p class="text-xs text-slate-600">{{ $application->email }} &bull; /kampus/{{ $application->slug }}</p>
                        </div>
                        <div class="flex gap-2">
                            <form method="POST" action="{{ route('platform.campuses.applications.approve', ['application' => $application]) }}">
                                @csrf
                                <button type="submit"
                                        class="rounded-full bg-[#141923] hover:bg-slate-800 px-4 py-2 text-xs font-semibold text-white transition">
                                    Setujui
                                </button>
                            </form>
                            <form method="POST" action="{{ route('platform.campuses.applications.reject', ['application' => $application]) }}">
                                @csrf
                                <button type="submit"
                                        class="rounded-full bg-rose-600 hover:bg-rose-700 px-4 py-2 text-xs font-semibold text-white transition">
                                    Tolak
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <p class="text-xs sm:text-sm text-slate-600">Tidak ada permohonan tertunda.</p>
                @endforelse
            </div>
        </section>

        {{-- Undangan Admin Belum Digunakan --}}
        <section class="rounded-[26px] bg-white/40 backdrop-blur-xl border border-white/65 shadow-[0_12px_36px_rgba(20,25,35,0.06)] p-6 space-y-4">
            <h2 class="text-base font-extrabold text-[#141923]">Undangan Admin Belum Digunakan</h2>
            <div class="space-y-3">
                @forelse ($awaitingAdminSetup as $application)
                    <div class="flex flex-wrap items-center justify-between gap-3 rounded-2xl bg-white/55 border border-white/75 p-4">
                        <div>
                            <p class="font-bold text-slate-900">{{ $application->name }}</p>
                            <p class="text-xs text-slate-600">{{ $application->email }}</p>
                        </div>
                        <form method="POST" action="{{ route('platform.campuses.applications.resend', ['application' => $application]) }}">
                            @csrf
                            <button type="submit"
                                    class="rounded-full bg-white/80 hover:bg-white border border-white px-4 py-2 text-xs font-semibold text-slate-900 transition">
                                Kirim Ulang Undangan
                            </button>
                        </form>
                    </div>
                @empty
                    <p class="text-xs sm:text-sm text-slate-600">Tidak ada undangan tertunda.</p>
                @endforelse
            </div>
        </section>

        {{-- Portal Aktif & Nonaktif --}}
        <section class="rounded-[26px] bg-white/40 backdrop-blur-xl border border-white/65 shadow-[0_12px_36px_rgba(20,25,35,0.06)] p-6 space-y-4">
            <h2 class="text-base font-extrabold text-[#141923]">Portal Aktif dan Nonaktif</h2>
            <div class="space-y-3">
                @foreach ($campuses as $campus)
                    <div class="flex flex-wrap items-center justify-between gap-3 rounded-2xl bg-white/55 border border-white/75 p-4">
                        <div>
                            <div class="flex items-center gap-2">
                                <p class="font-bold text-slate-900">{{ $campus->name }}</p>
                                <span class="rounded-full px-2.5 py-0.5 text-[10px] font-bold {{ $campus->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-200 text-slate-700' }}">
                                    {{ $campus->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </div>
                            <p class="text-xs text-slate-600 mt-0.5">/kampus/{{ $campus->slug }} &bull; {{ $campus->users_count }} akun terdaftar</p>
                        </div>
                        <form method="POST" action="{{ route('platform.campuses.status', ['campus' => $campus]) }}">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="is_active" value="{{ $campus->is_active ? 0 : 1 }}">
                            <button type="submit"
                                    class="rounded-full px-4 py-2 text-xs font-semibold transition {{ $campus->is_active ? 'bg-rose-500/15 hover:bg-rose-500/25 text-rose-900 border border-rose-300/40' : 'bg-[#141923] hover:bg-slate-800 text-white' }}">
                                {{ $campus->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                            </button>
                        </form>
                    </div>
                @endforeach
            </div>

            <div>{{ $campuses->links() }}</div>
        </section>
    </div>
</x-app-layout>