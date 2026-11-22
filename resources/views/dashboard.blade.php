@php
    $activeCampus = request()->route('campus') ?? ($campus ?? (auth()->user()?->campus ?? null));
    $allReportsUrl = $activeCampus ? route('campus.admin.reports', $activeCampus) : route('admin.reports');
    $claimsUrl = $activeCampus ? route('campus.admin.claims', $activeCampus) : route('admin.claims');
@endphp

<x-app-layout title="Moderasi & Metrik - Admin">
    <div class="space-y-6">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-900">
                Moderasi & Metrik
            </h1>
            <div class="flex items-center gap-2.5">
                <a href="{{ $claimsUrl }}" class="btn-secondary text-xs py-2.5 px-4 rounded-full">
                    Verifikasi Klaim ({{ $stats['pending_claims'] ?? 0 }})
                </a>
                <a href="{{ $allReportsUrl }}" class="btn-primary text-xs py-2.5 px-4 rounded-full">
                    Semua Laporan →
                </a>
            </div>
        </div>

        {{-- 3 KPI Stat Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
            <div class="glass-card p-6">
                <p class="text-xs font-semibold text-slate-500">Total Laporan</p>
                <p class="text-3xl font-extrabold text-slate-900 mt-1">
                    {{ str_pad((string)($stats['total_items'] ?? 0), 4, '0', STR_PAD_LEFT) }}
                </p>
            </div>

            <div class="glass-card p-6">
                <p class="text-xs font-semibold text-slate-500">Laporan Selesai</p>
                <p class="text-3xl font-extrabold text-[#23593a] mt-1">
                    {{ str_pad((string)($stats['returned_items'] ?? 0), 4, '0', STR_PAD_LEFT) }}
                </p>
            </div>

            <div class="glass-card p-6">
                <p class="text-xs font-semibold text-slate-500">Klaim Aktif</p>
                <p class="text-3xl font-extrabold text-[#111111] mt-1">
                    {{ str_pad((string)($stats['pending_claims'] ?? 0), 3, '0', STR_PAD_LEFT) }}
                </p>
            </div>
        </div>

        {{-- Ringkasan Distribusi Laporan --}}
        <div class="glass-card p-6 space-y-4">
            <div class="flex items-center justify-between">
                <h2 class="text-sm font-extrabold text-slate-900">Ringkasan Distribusi Laporan</h2>
                <span class="text-xs font-semibold text-slate-500">Tingkat Pengembalian: {{ $stats['returned_percentage'] ?? 0 }}%</span>
            </div>

            @php
                $maxVal = max(1, $stats['total_items'] ?? 1);
                $bars = [
                    ['label' => 'Total', 'val' => $stats['total_items'] ?? 0, 'color' => 'bg-[#111111]'],
                    ['label' => 'Hilang', 'val' => $stats['lost_items'] ?? 0, 'color' => 'bg-rose-500'],
                    ['label' => 'Temuan', 'val' => $stats['found_items'] ?? 0, 'color' => 'bg-[#23593a]'],
                    ['label' => 'Klaim', 'val' => $stats['pending_claims'] ?? 0, 'color' => 'bg-amber-500'],
                    ['label' => 'Selesai', 'val' => $stats['returned_items'] ?? 0, 'color' => 'bg-emerald-600'],
                ];
            @endphp

            <div class="h-36 flex items-end justify-around gap-4 pt-4 px-4">
                @foreach($bars as $bar)
                    @php $height = max(16, round(($bar['val'] / $maxVal) * 100)); @endphp
                    <div class="flex flex-col items-center gap-2 flex-1 max-w-[64px]">
                        <span class="text-[11px] font-bold text-slate-700">{{ $bar['val'] }}</span>
                        <div class="w-8 sm:w-10 rounded-t-xl {{ $bar['color'] }} transition-all" style="height: {{ $height }}px"></div>
                        <span class="text-[11px] font-semibold text-slate-500">{{ $bar['label'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Antrean Moderasi Terbaru --}}
        <div class="glass-card p-6 space-y-4">
            <div class="flex items-center justify-between">
                <h2 class="text-sm font-extrabold text-slate-900">Antrean Moderasi Terbaru</h2>
                <a href="{{ $allReportsUrl }}" class="text-xs font-bold text-[#23593a] hover:underline">Lihat semua</a>
            </div>

            @if($recentReports->isEmpty())
                <p class="text-xs text-slate-500 py-4 text-center">Belum ada laporan masuk.</p>
            @else
                <div class="divide-y divide-slate-200/70">
                    @foreach($recentReports as $report)
                        @php
                            $modAction = $activeCampus
                                ? route('campus.admin.reports.status', ['campus' => $activeCampus, 'item' => $report])
                                : route('admin.reports.status', $report);
                        @endphp
                        <div class="py-3.5 flex flex-wrap items-center justify-between gap-4 text-xs">
                            <div class="min-w-[180px] flex-1">
                                <a href="{{ $activeCampus ? route('campus.items.show', ['campus' => $activeCampus, 'item' => $report]) : route('items.show', $report) }}"
                                   class="font-bold text-slate-900 hover:text-[#23593a]">
                                    {{ $report->title }}
                                </a>
                            </div>

                            <div>
                                @if($report->type === 'lost')
                                    <span class="px-3 py-1 rounded-full bg-rose-100 text-rose-700 font-bold text-[11px]">Hilang</span>
                                @else
                                    <span class="px-3 py-1 rounded-full bg-emerald-100 text-[#23593a] font-bold text-[11px]">Ditemukan</span>
                                @endif
                            </div>

                            <div class="text-slate-600 font-medium min-w-[120px]">
                                {{ $report->user->name ?? 'Anonim' }}
                            </div>

                            <div class="text-slate-500 min-w-[100px]">
                                {{ \Carbon\Carbon::parse($report->created_at)->translatedFormat('d M Y') }}
                            </div>

                            <div class="flex items-center gap-2">
                                @if($report->status === 'pending')
                                    <form method="POST" action="{{ $modAction }}" class="inline">
                                        @csrf
                                        <input type="hidden" name="status" value="approved">
                                        <button type="submit" class="px-3.5 py-1.5 rounded-full bg-[#111111] hover:bg-[#23593a] text-white font-bold text-[11px] transition-colors cursor-pointer">
                                            Setujui
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ $modAction }}" class="inline">
                                        @csrf
                                        <input type="hidden" name="status" value="rejected">
                                        <input type="hidden" name="rejection_reason" value="Informasi kurang lengkap / tidak sesuai ketentuan.">
                                        <button type="submit" class="px-3.5 py-1.5 rounded-full bg-white hover:bg-rose-50 text-rose-600 font-bold text-[11px] transition-colors cursor-pointer">
                                            Tolak
                                        </button>
                                    </form>
                                @else
                                    <span class="px-3 py-1 rounded-full bg-white/80 text-slate-600 font-bold text-[11px] uppercase">
                                        {{ $report->status }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>