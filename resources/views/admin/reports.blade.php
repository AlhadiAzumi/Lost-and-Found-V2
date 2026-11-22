@php
    $activeCampus = request()->route('campus') ?? ($campus ?? (auth()->user()?->campus ?? null));
@endphp

<x-app-layout title="Moderasi Laporan - Admin">
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-900">
                Antrean Moderasi Laporan
            </h1>
            <a href="{{ $activeCampus ? route('campus.admin.dashboard', $activeCampus) : route('admin.dashboard') }}"
               class="btn-secondary text-xs py-2 px-4 rounded-full">
                ← Kembali ke Metrik
            </a>
        </div>

        <div class="glass-card p-6 space-y-4">
            @if($items->isEmpty())
                <p class="text-xs text-slate-500 text-center py-8">Tidak ada laporan untuk dimoderasi.</p>
            @else
                <div class="divide-y divide-slate-200/70">
                    @foreach($items as $report)
                        @php
                            $modAction = $activeCampus
                                ? route('campus.admin.reports.status', ['campus' => $activeCampus, 'item' => $report])
                                : route('admin.reports.status', $report);
                        @endphp
                        <div class="py-4 flex flex-wrap items-center justify-between gap-4 text-xs">
                            <div class="min-w-[200px] flex-1 space-y-0.5">
                                <a href="{{ $activeCampus ? route('campus.items.show', ['campus' => $activeCampus, 'item' => $report]) : route('items.show', $report) }}"
                                   class="font-bold text-sm text-slate-900 hover:text-[#23593a]">
                                    {{ $report->title }}
                                </a>
                                <p class="text-slate-500">{{ $report->category->name ?? '-' }} &bull; {{ $report->location }}</p>
                            </div>

                            <div>
                                <span class="px-3 py-1 rounded-full font-bold text-[11px] {{ $report->type === 'lost' ? 'bg-rose-100 text-rose-700' : 'bg-emerald-100 text-[#23593a]' }}">
                                    {{ $report->type === 'lost' ? 'Hilang' : 'Ditemukan' }}
                                </span>
                            </div>

                            <div class="text-slate-700 font-semibold min-w-[130px]">
                                {{ $report->user->name ?? 'Anonim' }}
                            </div>

                            <div class="text-slate-500 min-w-[100px]">
                                {{ \Carbon\Carbon::parse($report->created_at)->translatedFormat('d M Y') }}
                            </div>

                            <div class="flex items-center gap-2">
                                @if($report->status === 'pending')
                                    <form method="POST" action="{{ $modAction }}">
                                        @csrf
                                        <input type="hidden" name="status" value="approved">
                                        <button type="submit" class="px-4 py-1.5 rounded-full bg-[#111111] hover:bg-[#23593a] text-white font-bold text-[11px] transition-colors cursor-pointer">
                                            Setujui
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ $modAction }}">
                                        @csrf
                                        <input type="hidden" name="status" value="rejected">
                                        <input type="hidden" name="rejection_reason" value="Laporan ditolak oleh petugas moderasi.">
                                        <button type="submit" class="px-4 py-1.5 rounded-full bg-white hover:bg-rose-50 text-rose-600 font-bold text-[11px] transition-colors cursor-pointer">
                                            Tolak
                                        </button>
                                    </form>
                                @else
                                    <span class="px-3 py-1 rounded-full bg-white/85 text-slate-700 font-bold text-[11px] uppercase">
                                        {{ $report->status }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="pt-3">
                    {{ $items->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>