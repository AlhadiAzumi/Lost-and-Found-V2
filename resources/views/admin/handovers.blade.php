@php
    $activeCampus = request()->route('campus') ?? ($campus ?? (auth()->user()?->campus ?? null));
    $searchAction = $activeCampus ? route('campus.admin.handovers', $activeCampus) : route('admin.handovers');
@endphp

<x-app-layout title="Serah Terima Barang">
    <div class="max-w-4xl mx-auto space-y-6">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-900">
                Serah Terima Barang
            </h1>
            <p class="text-xs text-slate-600 mt-1">
                Masukkan kode serah terima dari penerima barang untuk menyelesaikan proses pengembalian.
            </p>
        </div>

        {{-- Kartu Tengah Input Kode OTP / Verifikasi --}}
        <div class="max-w-lg mx-auto glass-card p-7 sm:p-9 text-center space-y-6">
            <span class="badge-pill bg-emerald-100 text-[#23593a]">
                Verifikasi Serah Terima
            </span>

            <div class="space-y-1">
                <h2 class="text-xl font-extrabold text-slate-900">Serah Terima Barang</h2>
                <p class="text-xs text-slate-500">Cari & masukkan kode tiket klaim yang sudah disetujui</p>
            </div>

            {{-- Form Cari Kode --}}
            <form method="GET" action="{{ $searchAction }}" class="space-y-4">
                <input type="text"
                       name="code"
                       value="{{ $searchCode }}"
                       placeholder="Masukkan Kode Serah Terima (contoh: CLM-XXXXXX)"
                       required
                       class="w-full px-5 py-3.5 rounded-full bg-white border border-slate-200 text-center font-mono font-bold text-base tracking-widest uppercase text-slate-900 focus:outline-none focus:ring-2 focus:ring-[#23593a]/30">

                <button type="submit"
                        class="w-full py-3.5 rounded-full bg-[#111111] hover:bg-[#23593a] text-white text-sm font-bold shadow-sm transition-colors cursor-pointer">
                    Cek Kode Klaim
                </button>
            </form>

            {{-- Hasil Pencarian Kode & Konfirmasi Final --}}
            @if($searchCode !== '')
                @if(!$claimToHandover)
                    <div class="p-4 rounded-2xl bg-rose-50 text-xs font-bold text-rose-700">
                        ❌ Kode "{{ $searchCode }}" tidak ditemukan atau belum disetujui.
                    </div>
                @else
                    @php
                        $completeAction = $activeCampus
                            ? route('campus.admin.handovers.process', ['campus' => $activeCampus, 'claim' => $claimToHandover])
                            : route('admin.handovers.process', $claimToHandover);
                    @endphp

                    <div class="p-5 rounded-3xl bg-white/85 border border-white text-left space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="badge-pill bg-emerald-100 text-[#23593a]">✔ Klaim Terverifikasi</span>
                            <span class="font-mono text-xs font-bold text-slate-500">{{ $claimToHandover->handover_code }}</span>
                        </div>

                        <div class="text-xs space-y-1 text-slate-700">
                            <p><strong>Barang:</strong> {{ $claimToHandover->item->title ?? '-' }}</p>
                            <p><strong>Penerima:</strong> {{ $claimToHandover->user->name ?? '-' }} ({{ $claimToHandover->user->nim_nip ?? '-' }})</p>
                            <p><strong>Lokasi Penitipan:</strong> {{ $claimToHandover->item->storage_location ?? '-' }}</p>
                        </div>

                        @if($claimToHandover->handover)
                            <div class="p-3 rounded-2xl bg-slate-100 text-xs font-bold text-slate-700 text-center">
                                Barang ini sudah diserahkan pada {{ \Carbon\Carbon::parse($claimToHandover->handover->handover_date)->translatedFormat('d M Y H:i') }}.
                            </div>
                        @else
                            <form method="POST" action="{{ $completeAction }}" class="space-y-3 pt-2">
                                @csrf
                                <input type="hidden" name="code" value="{{ $claimToHandover->handover_code }}">
                                <input type="text" name="notes" placeholder="Catatan serah terima (opsional, misal: KTP dicek)..."
                                       class="w-full px-4 py-2.5 rounded-full bg-slate-50 border border-slate-200 text-xs">

                                <button type="submit"
                                        class="w-full py-3.5 rounded-full bg-[#23593a] hover:bg-[#19422b] text-white text-xs font-bold shadow-sm transition-colors cursor-pointer">
                                    Konfirmasi Serah Terima
                                </button>
                            </form>
                        @endif
                    </div>
                @endif
            @endif
        </div>

        {{-- Riwayat Serah Terima Terbaru --}}
        @if($handovers->isNotEmpty())
            <div class="glass-card p-6 space-y-4">
                <h3 class="text-sm font-extrabold text-slate-900">Riwayat Serah Terima Selesai</h3>
                <div class="divide-y divide-slate-200/70 text-xs">
                    @foreach($handovers as $ho)
                        <div class="py-3 flex flex-wrap items-center justify-between gap-2">
                            <div>
                                <p class="font-bold text-slate-900">{{ $ho->item->title ?? 'Barang' }}</p>
                                <p class="text-slate-500">Diterima oleh {{ $ho->recipient->name ?? '-' }} &bull; Petugas: {{ $ho->officer->name ?? '-' }}</p>
                            </div>
                            <span class="text-slate-500 font-medium">
                                {{ \Carbon\Carbon::parse($ho->handover_date)->translatedFormat('d M Y, H:i') }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</x-app-layout>