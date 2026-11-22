@php
    $campus = $currentCampus ?? request()->route('campus');

    $reportStatusMap = [
        'pending' => ['Menunggu Verifikasi', 'bg-amber-100/90 text-amber-900'],
        'approved' => ['Disetujui', 'bg-emerald-100/90 text-emerald-900'],
        'claim_in_progress' => ['Proses Klaim', 'bg-blue-100/90 text-blue-900'],
        'returned' => ['Selesai Dikembalikan', 'bg-slate-800 text-white'],
        'rejected' => ['Ditolak', 'bg-rose-100/90 text-rose-800'],
        'closed' => ['Ditutup', 'bg-slate-200/90 text-slate-700'],
    ];

    $claimStatusMap = [
        'pending' => ['Menunggu Verifikasi', 'bg-amber-100/90 text-amber-900'],
        'approved' => ['Disetujui (Siap Diambil)', 'bg-emerald-100/90 text-emerald-900'],
        'rejected' => ['Ditolak', 'bg-rose-100/90 text-rose-800'],
        'completed' => ['Sudah Diserahterimakan', 'bg-slate-800 text-white'],
    ];
@endphp

<x-app-layout>
    <div class="space-y-6 pb-8">
        {{-- Top Header --}}
        <div class="pt-1">
            <h1 class="text-2xl sm:text-[28px] font-extrabold tracking-tight text-[#141923]">
                Riwayat Laporan &amp; Klaim
            </h1>
            <p class="mt-1 text-xs sm:text-sm text-slate-600">
                Pantau status moderasi laporanmu dan ambil kode serah terima jika klaim disetujui petugas.
            </p>
        </div>

        @if (session('success'))
            <div class="rounded-[22px] bg-emerald-500/15 backdrop-blur-xl border border-emerald-400/40 px-4 py-3.5 text-sm font-medium text-emerald-950" role="status">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-start">
            {{-- Kolom 1: Klaim Saya --}}
            <section class="rounded-[28px] bg-white/40 backdrop-blur-xl border border-white/65 shadow-[0_12px_36px_rgba(20,25,35,0.06)] p-6 space-y-4">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-extrabold text-[#141923]">Klaim Saya</h2>
                    <span class="rounded-full bg-white/60 border border-white/75 px-3 py-0.5 text-xs font-bold text-slate-700">
                        {{ $claims->total() }}
                    </span>
                </div>

                <div class="space-y-3">
                    @forelse ($claims as $claim)
                        @php
                            [$cText, $cBadge] = $claimStatusMap[$claim->status] ?? [ucfirst($claim->status), 'bg-white/70 text-slate-800'];
                        @endphp
                        <article class="rounded-2xl bg-white/55 hover:bg-white/75 border border-white/75 p-4 space-y-2.5 transition">
                            <div class="flex items-start justify-between gap-3">
                                <h3 class="text-sm sm:text-base font-bold text-[#141923]">
                                    {{ $claim->item?->title ?? 'Barang tidak tersedia' }}
                                </h3>
                                <span class="shrink-0 rounded-full px-3 py-0.5 text-[11px] font-bold {{ $cBadge }}">
                                    {{ $cText }}
                                </span>
                            </div>

                            @if ($claim->status === 'approved' && $claim->handover_code)
                                <div class="rounded-xl bg-[#141923] text-white px-4 py-3 flex items-center justify-between gap-3">
                                    <div>
                                        <span class="block text-[10px] uppercase tracking-wider text-slate-300">
                                            Kode Serah Terima (Tunjukkan ke Petugas)
                                        </span>
                                        <span class="font-mono text-base sm:text-lg font-extrabold tracking-widest text-emerald-300">
                                            {{ $claim->handover_code }}
                                        </span>
                                    </div>
                                </div>
                            @endif

                            @if ($claim->admin_note)
                                <p class="rounded-xl bg-rose-500/10 border border-rose-300/40 px-3 py-2 text-xs text-rose-950">
                                    <span class="font-bold">Catatan petugas:</span> {{ $claim->admin_note }}
                                </p>
                            @endif
                        </article>
                    @empty
                        <div class="rounded-2xl bg-white/40 border border-white/60 p-8 text-center text-xs sm:text-sm text-slate-600">
                            Belum ada pengajuan klaim.
                        </div>
                    @endforelse
                </div>

                <div>{{ $claims->links() }}</div>
            </section>

            {{-- Kolom 2: Laporan Saya --}}
            <section class="rounded-[28px] bg-white/40 backdrop-blur-xl border border-white/65 shadow-[0_12px_36px_rgba(20,25,35,0.06)] p-6 space-y-4">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-extrabold text-[#141923]">Laporan Saya</h2>
                    <span class="rounded-full bg-white/60 border border-white/75 px-3 py-0.5 text-xs font-bold text-slate-700">
                        {{ $reports->total() }}
                    </span>
                </div>

                <div class="space-y-3">
                    @forelse ($reports as $report)
                        @php
                            [$rText, $rBadge] = $reportStatusMap[$report->status] ?? [ucfirst($report->status), 'bg-white/70 text-slate-800'];
                        @endphp
                        <a href="{{ route('campus.items.show', ['campus' => $campus->slug, 'item' => $report]) }}"
                           class="block rounded-2xl bg-white/55 hover:bg-white/80 border border-white/75 p-4 space-y-2 transition">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <span class="text-[11px] font-bold uppercase tracking-wider {{ $report->type === 'lost' ? 'text-rose-700' : 'text-emerald-800' }}">
                                        {{ $report->type === 'lost' ? 'Hilang' : 'Ditemukan' }}
                                    </span>
                                    <h3 class="text-sm sm:text-base font-bold text-[#141923]">
                                        {{ $report->title }}
                                    </h3>
                                </div>
                                <span class="shrink-0 rounded-full px-3 py-0.5 text-[11px] font-bold {{ $rBadge }}">
                                    {{ $rText }}
                                </span>
                            </div>

                            @if ($report->rejection_reason)
                                <p class="rounded-xl bg-rose-500/10 border border-rose-300/40 px-3 py-2 text-xs text-rose-950">
                                    <span class="font-bold">Alasan ditolak:</span> {{ $report->rejection_reason }}
                                </p>
                            @endif
                        </a>
                    @empty
                        <div class="rounded-2xl bg-white/40 border border-white/60 p-8 text-center text-xs sm:text-sm text-slate-600">
                            Belum ada laporan yang dibuat.
                        </div>
                    @endforelse
                </div>

                <div>{{ $reports->links() }}</div>
            </section>
        </div>
    </div>
</x-app-layout>