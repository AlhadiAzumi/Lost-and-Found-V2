@php
    $campus = $currentCampus ?? request()->route('campus');
    $backUrl = $campus ? route('campus.home', ['campus' => $campus->slug]) : route('catalog');

    $statusLabels = [
        'pending' => ['Menunggu Verifikasi', 'bg-amber-100/90 text-amber-900'],
        'approved' => ['Aktif di Feed', 'bg-emerald-100/90 text-emerald-900'],
        'claim_in_progress' => ['Proses Klaim', 'bg-blue-100/90 text-blue-900'],
        'returned' => ['Sudah Dikembalikan', 'bg-slate-800 text-white'],
        'rejected' => ['Ditolak', 'bg-rose-100/90 text-rose-800'],
        'closed' => ['Ditutup', 'bg-slate-200/90 text-slate-700'],
    ];
    [$statusText, $statusBadge] = $statusLabels[$item->status] ?? [ucfirst($item->status), 'bg-white/70 text-slate-800'];
@endphp

<x-app-layout>
    <div class="max-w-4xl mx-auto space-y-6 pb-8">
        {{-- Top Header --}}
        <div class="flex flex-wrap items-center justify-between gap-4 pt-1">
            <div>
                <a href="{{ $backUrl }}"
                   class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-700 hover:text-[#141923] mb-1.5 transition">
                    &larr; Kembali ke Feed Publik
                </a>
                <h1 class="text-2xl sm:text-[28px] font-extrabold tracking-tight text-[#141923]">
                    Detail Laporan Barang
                </h1>
            </div>
        </div>

        @if (session('success'))
            <div class="rounded-[22px] bg-emerald-500/15 backdrop-blur-xl border border-emerald-400/40 px-4 py-3.5 text-sm font-medium text-emerald-950" role="status">
                {{ session('success') }}
            </div>
        @endif

        {{-- Main Glass Card --}}
        <article class="rounded-[28px] bg-white/40 backdrop-blur-xl border border-white/65 shadow-[0_12px_36px_rgba(20,25,35,0.06)] overflow-hidden">
            {{-- Foto Barang --}}
            @if ($item->image && ! $item->is_sensitive)
                <div class="relative bg-gradient-to-br from-[#B8D1BA]/50 to-[#7FA985]/50 border-b border-white/50">
                    <img src="{{ $item->photo_url }}" alt="Foto {{ $item->title }}"
                         onerror="this.parentElement.style.display='none';"
                         class="max-h-[420px] w-full object-contain mx-auto">
                </div>
            @elseif ($item->is_sensitive)
                <div class="bg-amber-500/10 border-b border-amber-400/30 px-6 py-4 flex items-center gap-3 text-xs sm:text-sm font-medium text-amber-950">
                    <svg class="w-5 h-5 shrink-0 text-amber-700" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                    <span>Laporan ini ditandai sensitif. Foto disembunyikan untuk melindungi privasi pemilik.</span>
                </div>
            @endif

            <div class="p-6 sm:p-8 space-y-6">
                {{-- Badges & Title --}}
                <div class="space-y-3">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="rounded-full px-3.5 py-1 text-xs font-bold shadow-sm {{ $item->type === 'lost' ? 'bg-rose-100/90 text-rose-700' : 'bg-emerald-100/90 text-emerald-800' }}">
                            {{ $item->type === 'lost' ? 'Barang Hilang' : 'Barang Ditemukan' }}
                        </span>

                        <span class="rounded-full px-3.5 py-1 text-xs font-semibold {{ $statusBadge }}">
                            {{ $statusText }}
                        </span>

                        @if ($item->category)
                            <span class="rounded-full bg-white/60 border border-white/75 px-3.5 py-1 text-xs font-semibold text-slate-700">
                                {{ $item->category->name }}
                            </span>
                        @endif
                    </div>

                    <h2 class="text-2xl sm:text-3xl font-extrabold text-[#141923] tracking-tight">
                        {{ $item->title }}
                    </h2>
                </div>

                {{-- Info Grid --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 rounded-2xl bg-white/45 border border-white/65 p-4 text-sm">
                    <div>
                        <span class="block text-[11px] font-bold uppercase tracking-wider text-slate-500">Tanggal Kejadian</span>
                        <span class="mt-0.5 block font-semibold text-slate-900">
                            {{ $item->date_event?->translatedFormat('d F Y') ?? '-' }}
                        </span>
                    </div>

                    <div>
                        <span class="block text-[11px] font-bold uppercase tracking-wider text-slate-500">Lokasi Kejadian</span>
                        <span class="mt-0.5 block font-semibold text-slate-900">
                            {{ $item->location }}
                        </span>
                    </div>

                    @if ($item->type === 'found' && $item->storage_location)
                        <div class="sm:col-span-2 pt-2 border-t border-white/60">
                            <span class="block text-[11px] font-bold uppercase tracking-wider text-slate-500">Lokasi Penitipan Barang</span>
                            <span class="mt-0.5 block font-semibold text-[#1B4332]">
                                {{ $item->storage_location }}
                            </span>
                        </div>
                    @endif
                </div>

                {{-- Deskripsi --}}
                <div class="space-y-2">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-600">Deskripsi &amp; Keterangan</h3>
                    <p class="whitespace-pre-line text-sm sm:text-base leading-relaxed text-slate-800">{{ $item->description }}</p>
                </div>

                {{-- Pertanyaan Verifikasi untuk Barang Temuan --}}
                @if ($item->type === 'found' && $item->security_question)
                    <div class="rounded-2xl bg-amber-500/10 border border-amber-400/35 p-5 space-y-1.5">
                        <p class="text-xs font-bold uppercase tracking-wider text-amber-900">
                            Pertanyaan Verifikasi Kepemilikan
                        </p>
                        <p class="text-sm sm:text-base font-medium text-slate-900">
                            {{ $item->security_question }}
                        </p>
                    </div>
                @endif

                {{-- Alasan Penolakan (Jika Ditolak Admin) --}}
                @if (auth()->id() === $item->user_id && $item->rejection_reason)
                    <div class="rounded-2xl bg-rose-500/15 border border-rose-400/40 p-4 text-sm text-rose-950">
                        <p class="font-bold">Alasan Penolakan Laporan:</p>
                        <p class="mt-1">{{ $item->rejection_reason }}</p>
                    </div>
                @endif

                {{-- Action Buttons --}}
                <div class="pt-4 border-t border-white/55 flex flex-wrap items-center justify-between gap-3">
                    <a href="{{ $backUrl }}"
                       class="rounded-full bg-white/55 hover:bg-white/80 border border-white/70 px-5 py-2.5 text-xs sm:text-sm font-semibold text-slate-800 transition">
                        &larr; Kembali ke Katalog
                    </a>

                    <div class="flex flex-wrap items-center gap-2.5">
                        @auth
                            @if ($item->type === 'found' && $item->status === 'approved' && auth()->id() !== $item->user_id)
                                <a href="{{ route('campus.claims.create', ['campus' => $campus->slug, 'item' => $item]) }}"
                                   class="rounded-full bg-[#141923] hover:bg-slate-800 px-6 py-2.5 text-xs sm:text-sm font-semibold text-white shadow-sm transition">
                                    Ajukan Klaim Barang Ini
                                </a>
                            @endif

                            @can('update', $item)
                                <a href="{{ route('campus.items.edit', ['campus' => $campus->slug, 'item' => $item]) }}"
                                   class="rounded-full bg-white/70 hover:bg-white border border-white/80 px-5 py-2.5 text-xs sm:text-sm font-semibold text-slate-800 transition">
                                    Ubah Laporan
                                </a>
                            @endcan

                            @can('close', $item)
                                <form method="POST" action="{{ route('campus.items.close', ['campus' => $campus->slug, 'item' => $item]) }}">
                                    @csrf
                                    <button type="submit"
                                            class="rounded-full bg-rose-500/15 hover:bg-rose-500/25 border border-rose-400/40 px-5 py-2.5 text-xs sm:text-sm font-semibold text-rose-900 transition">
                                        Tutup Laporan
                                    </button>
                                </form>
                            @endcan
                        @else
                            @if ($item->type === 'found' && $item->status === 'approved' && $campus)
                                <a href="{{ route('campus.login', ['campus' => $campus->slug]) }}"
                                   class="rounded-full bg-[#141923] hover:bg-slate-800 px-6 py-2.5 text-xs sm:text-sm font-semibold text-white shadow-sm transition">
                                    Masuk untuk Mengklaim
                                </a>
                            @endif
                        @endauth
                    </div>
                </div>
            </div>
        </article>
    </div>
</x-app-layout>