@php
    $campus = $currentCampus ?? request()->route('campus');
    $statusMap = [
        'pending' => ['Menunggu Verifikasi', 'bg-amber-100/90 text-amber-900'],
        'approved' => ['Disetujui', 'bg-emerald-100/90 text-emerald-900'],
        'claim_in_progress' => ['Proses Klaim', 'bg-blue-100/90 text-blue-900'],
        'returned' => ['Dikembalikan', 'bg-slate-800 text-white'],
        'rejected' => ['Ditolak', 'bg-rose-100/90 text-rose-800'],
        'closed' => ['Ditutup', 'bg-slate-200/90 text-slate-700'],
    ];
@endphp

<x-app-layout>
    <div class="space-y-6 pb-8">
        {{-- Header & Tab Navigasi Admin --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pt-1">
            <div>
                <h1 class="text-2xl sm:text-[28px] font-extrabold tracking-tight text-[#141923]">
                    Moderasi Laporan
                </h1>
                <p class="mt-1 text-xs sm:text-sm text-slate-600">
                    Tinjau laporan barang hilang dan temuan sebelum tayang di feed publik kampus.
                </p>
            </div>

            @include('admin.partials.nav-tabs')
        </div>

        @if (session('success'))
            <div class="rounded-[22px] bg-emerald-500/15 backdrop-blur-xl border border-emerald-400/40 px-4 py-3.5 text-sm font-medium text-emerald-950" role="status">
                {{ session('success') }}
            </div>
        @endif

        <div class="space-y-4">
            @forelse ($items as $item)
                @php
                    [$stText, $stBadge] = $statusMap[$item->status] ?? [ucfirst($item->status), 'bg-white/70 text-slate-800'];
                @endphp
                <article class="rounded-[26px] bg-white/40 backdrop-blur-xl border border-white/65 shadow-[0_12px_36px_rgba(20,25,35,0.06)] p-6 space-y-4">
                    <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
                        <div class="space-y-2 flex-1 min-w-0">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="rounded-full px-3 py-0.5 text-[11px] font-bold {{ $item->type === 'lost' ? 'bg-rose-100/90 text-rose-700' : 'bg-emerald-100/90 text-emerald-800' }}">
                                    {{ $item->type === 'lost' ? 'Barang Hilang' : 'Barang Ditemukan' }}
                                </span>
                                <span class="rounded-full px-3 py-0.5 text-[11px] font-bold {{ $stBadge }}">
                                    {{ $stText }}
                                </span>
                                @if ($item->category)
                                    <span class="rounded-full bg-white/60 border border-white/75 px-3 py-0.5 text-[11px] font-semibold text-slate-700">
                                        {{ $item->category->name }}
                                    </span>
                                @endif
                            </div>

                            <h2 class="text-lg font-extrabold text-[#141923]">
                                <a href="{{ route('campus.items.show', ['campus' => $campus->slug, 'item' => $item]) }}" class="hover:underline">
                                    {{ $item->title }}
                                </a>
                            </h2>

                            <p class="text-xs font-medium text-slate-600">
                                Lokasi: <span class="text-slate-900 font-semibold">{{ $item->location }}</span>
                                &bull; Pelapor: <span class="text-slate-900 font-semibold">{{ $item->user?->name ?? '-' }}</span>
                            </p>

                            <p class="text-sm text-slate-800 leading-relaxed pt-1">
                                {{ $item->description }}
                            </p>

                            @if ($item->type === 'found' && $item->security_question)
                                <div class="rounded-2xl bg-amber-500/10 border border-amber-400/35 px-4 py-2.5 text-xs sm:text-sm text-slate-900">
                                    <span class="font-bold text-amber-950">Pertanyaan verifikasi:</span> {{ $item->security_question }}
                                </div>
                            @endif
                        </div>

                        @if ($item->image)
                            <img src="{{ $item->photo_url }}" alt="Foto {{ $item->title }}"
                                 onerror="this.style.display='none';"
                                 class="w-full sm:w-32 h-32 rounded-2xl object-cover border border-white/70 shrink-0">
                        @endif
                    </div>

                    @if ($item->status === 'pending')
                        <div class="pt-4 border-t border-white/55 flex flex-col lg:flex-row lg:items-center justify-between gap-3">
                            <form method="POST" action="{{ route('campus.admin.reports.status', ['campus' => $campus->slug, 'item' => $item]) }}">
                                @csrf
                                <input type="hidden" name="status" value="approved">
                                <button type="submit"
                                        class="rounded-full bg-[#141923] hover:bg-slate-800 px-6 py-2.5 text-xs sm:text-sm font-semibold text-white shadow-sm transition">
                                    Setujui &amp; Tayangkan
                                </button>
                            </form>

                            <form method="POST" action="{{ route('campus.admin.reports.status', ['campus' => $campus->slug, 'item' => $item]) }}"
                                  class="flex flex-1 max-w-xl items-center gap-2">
                                @csrf
                                <input type="hidden" name="status" value="rejected">
                                <input name="rejection_reason" required maxlength="2000"
                                       placeholder="Tulis alasan penolakan laporan..."
                                       class="flex-1 rounded-full border border-white/75 bg-white/65 px-4 py-2 text-xs sm:text-sm text-slate-900 placeholder-slate-400 focus:bg-white focus:border-[#141923] focus:ring-[#141923]">
                                <button type="submit"
                                        class="shrink-0 rounded-full bg-rose-600 hover:bg-rose-700 px-5 py-2 text-xs sm:text-sm font-semibold text-white transition">
                                    Tolak
                                </button>
                            </form>
                        </div>
                    @endif
                </article>
            @empty
                <div class="rounded-[26px] bg-white/40 backdrop-blur-xl border border-white/65 p-12 text-center shadow-[0_12px_36px_rgba(20,25,35,0.06)]">
                    <p class="text-base font-bold text-slate-900">Belum ada laporan masuk</p>
                    <p class="mt-1 text-xs text-slate-600">Semua laporan baru dari civitas kampus akan muncul di sini.</p>
                </div>
            @endforelse
        </div>

        <div>{{ $items->links() }}</div>
    </div>
</x-app-layout>