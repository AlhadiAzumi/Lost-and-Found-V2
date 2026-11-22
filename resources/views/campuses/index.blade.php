<x-guest-layout title="Pilih Portal Kampus | Lost & Found">
    <div class="space-y-6">
        @if (session('application_submitted'))
            <div class="rounded-2xl bg-emerald-50 border border-emerald-200 px-4 py-3.5 text-sm text-emerald-800">
                {{ session('application_submitted') }}
            </div>
        @endif

        @if (session('error'))
            <div class="rounded-2xl bg-red-50 border border-red-200 px-4 py-3.5 text-sm text-red-700">
                {{ session('error') }}
            </div>
        @endif

        {{-- Kartu Pilih Portal Kampus --}}
        <div class="rounded-[28px] bg-white/80 backdrop-blur-xl border border-white/80 shadow-[0_20px_50px_rgba(20,25,35,0.08)] p-7 sm:p-8">
            <div class="mb-6">
                <span class="inline-block text-xs font-bold uppercase tracking-wider text-[#1B4332] bg-emerald-100/80 px-3 py-1 rounded-full mb-2">
                    Direktori Portal
                </span>
                <h2 class="text-2xl sm:text-[26px] font-bold text-[#141923] tracking-tight">
                    Pilih portal kampus
                </h2>
                <p class="mt-1 text-sm text-slate-600">
                    Buka katalog Lost &amp; Found di kampus yang sesuai tanpa perlu login terlebih dahulu.
                </p>
            </div>

            @if ($campuses->isEmpty())
                <div class="rounded-2xl bg-slate-50 border border-slate-200/80 p-5 text-sm text-slate-600">
                    Belum ada portal kampus yang aktif. Silakan ajukan portal kampus baru di bawah.
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    @foreach ($campuses as $campus)
                        <a href="{{ route('campus.home', ['campus' => $campus->slug]) }}"
                           class="group flex items-center justify-between gap-4 rounded-2xl bg-white border border-slate-200/90 p-4 shadow-sm hover:border-[#1B4332] hover:shadow-md transition">
                            <div class="min-w-0">
                                <h3 class="font-bold text-slate-900 group-hover:text-[#1B4332] truncate">
                                    {{ $campus->name }}
                                </h3>
                                <p class="text-xs font-medium text-emerald-700 mt-0.5">
                                    Buka portal kampus
                                </p>
                            </div>
                            <span class="shrink-0 w-9 h-9 rounded-full bg-slate-100 group-hover:bg-[#1B4332] group-hover:text-white inline-flex items-center justify-center text-slate-600 transition">
                                <svg class="w-4 h-4 block" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
                                </svg>
                            </span>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Kartu Pengajuan Portal Kampus Baru --}}
        <div class="rounded-[28px] bg-white/75 backdrop-blur-xl border border-white/80 shadow-[0_20px_50px_rgba(20,25,35,0.06)] p-7 sm:p-8">
            <div class="mb-5">
                <h3 class="text-lg font-bold text-[#141923]">Ajukan portal kampus</h3>
                <p class="mt-1 text-xs sm:text-sm text-slate-600">
                    Permohonan ditinjau admin platform. Jika disetujui, tautan pembuatan akun admin dikirim ke email.
                </p>
            </div>

            @if ($errors->any())
                <div class="mb-4 rounded-xl bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('campus-applications.store') }}" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                @csrf
                <div>
                    <label for="application-campus-name" class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Nama kampus
                    </label>
                    <input id="application-campus-name" name="name" type="text" value="{{ old('name') }}" required
                           placeholder="Contoh: Politeknik Negeri Lhokseumawe"
                           class="w-full rounded-xl border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-900 focus:border-[#1B4332] focus:ring-[#1B4332]">
                </div>

                <div>
                    <label for="application-email" class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Email pemohon
                    </label>
                    <input id="application-email" name="email" type="email" value="{{ old('email') }}" required
                           placeholder="admin@kampus.ac.id"
                           class="w-full rounded-xl border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-900 focus:border-[#1B4332] focus:ring-[#1B4332]">
                </div>

                <div class="sm:col-span-2 pt-1">
                    <button type="submit"
                            class="w-full sm:w-auto rounded-full bg-[#141923] px-6 py-2.5 text-sm font-semibold text-white hover:bg-slate-800 transition">
                        Kirim Pengajuan
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>