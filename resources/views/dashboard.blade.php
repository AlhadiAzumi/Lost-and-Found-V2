@php
    $campus = $currentCampus ?? request()->route('campus');
    $user = auth()->user();
    $isPlatformAdmin = $user && $user->role === 'admin'
        && filled(config('campus.platform_admin_email'))
        && strcasecmp($user->email, config('campus.platform_admin_email')) === 0;

    $kpiCards = [
        [
            'label' => 'Total laporan',
            'value' => $stats['total_items'] ?? 0,
            'color' => 'text-[#141923]',
            'tint' => 'bg-white/45',
        ],
        [
            'label' => 'Barang hilang',
            'value' => $stats['lost_items'] ?? 0,
            'color' => 'text-rose-600',
            'tint' => 'bg-white/45',
        ],
        [
            'label' => 'Barang ditemukan',
            'value' => $stats['found_items'] ?? 0,
            'color' => 'text-emerald-700',
            'tint' => 'bg-white/45',
        ],
        [
            'label' => 'Klaim tertunda',
            'value' => $stats['pending_claims'] ?? 0,
            'color' => 'text-amber-700',
            'tint' => 'bg-amber-500/10',
        ],
        [
            'label' => 'Barang kembali',
            'value' => ($stats['returned_percentage'] ?? 0) . '%',
            'color' => 'text-[#1B4332]',
            'tint' => 'bg-emerald-500/10',
        ],
    ];

    $statusBadgeMap = [
        'pending' => ['Pending', 'bg-amber-100/90 text-amber-900 border-amber-300/60'],
        'approved' => ['Approved', 'bg-emerald-100/90 text-emerald-900 border-emerald-300/60'],
        'claim_in_progress' => ['Claiming', 'bg-blue-100/90 text-blue-900 border-blue-300/60'],
        'returned' => ['Returned', 'bg-emerald-100/90 text-emerald-950 border-emerald-400/60'],
        'rejected' => ['Rejected', 'bg-rose-100/90 text-rose-900 border-rose-300/60'],
        'closed' => ['Closed', 'bg-slate-200/90 text-slate-800 border-slate-300/60'],
    ];
@endphp

<x-app-layout>
    <div class="space-y-6 pb-8">
        {{-- Header: Pill Badge + Judul + Subtitle --}}
        <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 pt-1">
            <div>
                <span class="inline-flex items-center gap-1.5 rounded-full bg-white/55 backdrop-blur-md border border-white/80 px-3.5 py-1 text-xs font-bold text-[#141923] shadow-sm mb-2.5">
                    <svg class="w-3.5 h-3.5 text-[#1B4332]" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                    Panel Satpam / Admin
                </span>

                <h1 class="text-2xl sm:text-[30px] font-extrabold tracking-tight text-[#141923]">
                    Dashboard Pengawasan
                </h1>
                <p class="mt-1 text-xs sm:text-sm text-slate-600">
                    Pantau laporan, klaim, dan barang yang telah dikembalikan di {{ $campus->name }}.
                </p>
            </div>

            @if ($isPlatformAdmin)
                <a href="{{ route('platform.campuses.index') }}"
                   class="self-start sm:self-auto rounded-full bg-white/60 hover:bg-white border border-white/80 px-4 py-2 text-xs font-bold text-[#141923] shadow-sm transition">
                    Kelola Portal Kampus &rarr;
                </a>
            @endif
        </div>

        {{-- Floating Pill Tab Navigation --}}
        <div>
            @include('admin.partials.nav-tabs')
        </div>

        {{-- 5 Kartu Statistik KPI (Glass Cards) --}}
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
            @foreach ($kpiCards as $card)
                <div class="rounded-[24px] {{ $card['tint'] }} backdrop-blur-xl border border-white/70 shadow-[0_12px_32px_rgba(20,25,35,0.05)] p-5">
                    <p class="text-xs font-semibold text-slate-600">{{ $card['label'] }}</p>
                    <p class="mt-2.5 text-2xl sm:text-3xl font-extrabold tracking-tight {{ $card['color'] }}">
                        {{ $card['value'] }}
                    </p>
                </div>
            @endforeach
        </div>

        {{-- 2 Kolom Bawah: Klaim Menunggu & Laporan Terbaru --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-start">
            {{-- Panel Kiri: Klaim Menunggu --}}
            <section class="rounded-[28px] bg-white/40 backdrop-blur-xl border border-white/65 shadow-[0_12px_36px_rgba(20,25,35,0.06)] p-6 space-y-4">
                <div class="flex items-center justify-between">
                    <h2 class="text-base sm:text-lg font-extrabold text-[#141923]">Klaim menunggu</h2>
                    <a href="{{ route('campus.admin.claims', ['campus' => $campus->slug]) }}"
                       class="text-xs font-bold text-slate-700 hover:text-[#141923] transition">
                        Lihat semua &rarr;
                    </a>
                </div>

                <div class="space-y-2.5">
                    @forelse ($recentClaims ?? [] as $claim)
                        <a href="{{ route('campus.admin.claims', ['campus' => $campus->slug]) }}"
                           class="flex items-center justify-between gap-3 rounded-2xl bg-white/55 hover:bg-white/80 border border-white/75 px-4 py-3.5 transition">
                            <div class="min-w-0">
                                <p class="text-sm font-bold text-[#141923] truncate">
                                    {{ $claim->item?->title ?? 'Barang tidak tersedia' }}
                                </p>
                                <p class="text-xs text-slate-600 truncate mt-0.5">
                                    Pengklaim: {{ $claim->user?->name ?? '-' }}
                                </p>
                            </div>
                            <span class="shrink-0 inline-flex items-center gap-1 rounded-full bg-amber-100/90 border border-amber-300/60 px-3 py-1 text-[11px] font-bold text-amber-900">
                                Pending
                            </span>
                        </a>
                    @empty
                        <div class="rounded-2xl bg-white/45 border border-white/65 px-4 py-6 text-center text-xs sm:text-sm text-slate-600">
                            Tidak ada klaim yang perlu ditinjau saat ini.
                        </div>
                    @endforelse
                </div>
            </section>

            {{-- Panel Kanan: Laporan Terbaru --}}
            <section class="rounded-[28px] bg-white/40 backdrop-blur-xl border border-white/65 shadow-[0_12px_36px_rgba(20,25,35,0.06)] p-6 space-y-4">
                <div class="flex items-center justify-between">
                    <h2 class="text-base sm:text-lg font-extrabold text-[#141923]">Laporan terbaru</h2>
                    <a href="{{ route('campus.admin.reports', ['campus' => $campus->slug]) }}"
                       class="text-xs font-bold text-slate-700 hover:text-[#141923] transition">
                        Lihat semua &rarr;
                    </a>
                </div>

                <div class="space-y-2.5">
                    @forelse ($recentReports ?? [] as $item)
                        @php
                            [$bLabel, $bClass] = $statusBadgeMap[$item->status] ?? [ucfirst($item->status), 'bg-white/70 text-slate-800 border-white'];
                        @endphp
                        <a href="{{ route('campus.items.show', ['campus' => $campus->slug, 'item' => $item]) }}"
                           class="flex items-center justify-between gap-3 rounded-2xl bg-white/55 hover:bg-white/80 border border-white/75 px-4 py-3.5 transition">
                            <div class="min-w-0">
                                <p class="text-sm font-bold text-[#141923] truncate">
                                    {{ $item->title }}
                                </p>
                                <p class="text-xs text-slate-600 truncate mt-0.5">
                                    {{ $item->location ?? '-' }}
                                </p>
                            </div>
                            <span class="shrink-0 inline-flex items-center gap-1.5 rounded-full border px-3 py-1 text-[11px] font-bold {{ $bClass }}">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                {{ $bLabel }}
                            </span>
                        </a>
                    @empty
                        <div class="rounded-2xl bg-white/45 border border-white/65 px-4 py-6 text-center text-xs sm:text-sm text-slate-600">
                            Belum ada laporan terbaru.
                        </div>
                    @endforelse
                </div>
            </section>
        </div>
    </div>
</x-app-layout>