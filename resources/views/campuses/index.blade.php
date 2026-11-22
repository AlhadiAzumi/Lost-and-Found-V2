<x-guest-layout title="Pilih Portal Kampus - Campus Lost & Found">
    <div x-data="{ search: '' }" class="w-full max-w-5xl space-y-8">
        {{-- Hero Glass Card --}}
        <div class="glass-card p-7 sm:p-10">
            <div class="max-w-2xl space-y-4">
                <span class="badge-pill bg-[#23593a]/10 text-[#23593a]">
                    🏫 Jaringan Lost & Found Kampus
                </span>
                <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-slate-900 leading-tight">
                    Barang Hilang di Kampus? <br class="hidden sm:block">
                    Temukan & Klaim Secara Aman.
                </h1>
                <p class="text-sm sm:text-base text-slate-600 leading-relaxed">
                    Pilih portal resmi kampus kamu di bawah ini untuk melihat katalog barang hilang & temuan, membuat laporan, atau memverifikasi kepemilikan barang.
                </p>

                <div class="pt-2">
                    <div class="relative">
                        <input type="text"
                               x-model="search"
                               placeholder="Cari nama kampus atau kota..."
                               class="input-glass pl-11 py-3.5 text-sm">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">🔎</span>
                    </div>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="glass-card bg-emerald-50/90 px-5 py-4 text-sm font-semibold text-[#23593a]">
                ✅ {{ session('success') }}
            </div>
        @endif

        {{-- Daftar Kampus --}}
        <div class="space-y-4">
            <h2 class="text-base font-bold text-slate-800 px-1">
                Portal Kampus Aktif ({{ $campuses->count() }})
            </h2>

            @if($campuses->isEmpty())
                <div class="glass-card p-10 text-center space-y-2">
                    <p class="text-3xl">🏛️</p>
                    <p class="font-bold text-base text-slate-900">Belum ada portal kampus yang aktif.</p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                    @foreach($campuses as $campus)
                        <a href="{{ route('campus.home', $campus) }}"
                           x-show="search === '' || '{{ strtolower($campus->name . ' ' . ($campus->city ?? '') . ' ' . $campus->slug) }}'.includes(search.toLowerCase())"
                           class="glass-card-hover p-6 flex flex-col justify-between gap-5 group">
                            <div class="space-y-3">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="w-12 h-12 rounded-2xl bg-[#23593a]/10 text-[#23593a] flex items-center justify-center text-xl shrink-0">
                                        🎓
                                    </div>
                                    <span class="badge-pill bg-emerald-100 text-[#23593a]">
                                        ● Aktif
                                    </span>
                                </div>

                                <div>
                                    <h3 class="font-bold text-base text-slate-900 group-hover:text-[#23593a] transition-colors">
                                        {{ $campus->name }}
                                    </h3>
                                    @if(!empty($campus->city) || !empty($campus->address))
                                        <p class="text-xs text-slate-500 mt-1">
                                            📍 {{ $campus->city ?? $campus->address }}
                                        </p>
                                    @endif
                                </div>
                            </div>

                            <div class="pt-3 border-t border-slate-200/60 flex items-center justify-between text-xs font-semibold text-slate-700">
                                <span>Masuk Portal</span>
                                <span class="px-3 py-1.5 rounded-xl bg-[#111111] group-hover:bg-[#23593a] text-white transition-colors">
                                    Buka →
                                </span>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-guest-layout>