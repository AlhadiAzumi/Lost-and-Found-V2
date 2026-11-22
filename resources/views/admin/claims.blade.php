@php
    $activeCampus = request()->route('campus') ?? ($campus ?? (auth()->user()?->campus ?? null));
    $firstClaimId = $claims->first()?->id ?? 'null';
@endphp

<x-app-layout title="Verifikasi Klaim Masuk">
    <div x-data="{ selectedId: {{ $firstClaimId }} }" class="space-y-6">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-900">
                Verifikasi Klaim Masuk
            </h1>
            <a href="{{ $activeCampus ? route('campus.admin.handovers', $activeCampus) : route('admin.handovers') }}"
               class="btn-primary text-xs py-2.5 px-5 rounded-full">
                🤝 Ke Halaman Serah Terima →
            </a>
        </div>

        @if($claims->isEmpty())
            <div class="glass-card p-12 text-center space-y-2">
                <p class="text-3xl">📭</p>
                <p class="font-bold text-slate-900">Belum ada pengajuan klaim masuk.</p>
            </div>
        @else
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-start">
                {{-- Kolom 1: Daftar Pengklaim --}}
                <div class="lg:col-span-4 glass-card p-5 space-y-3">
                    <h2 class="text-sm font-extrabold text-slate-900 px-1">
                        Klaim ({{ $claims->total() }})
                    </h2>

                    <div class="space-y-2.5">
                        @foreach($claims as $claim)
                            <button type="button"
                                    @click="selectedId = {{ $claim->id }}"
                                    :class="selectedId === {{ $claim->id }} ? 'bg-[#161b26] text-white shadow-sm' : 'bg-white/75 hover:bg-white text-slate-800'"
                                    class="w-full text-left px-4 py-3 rounded-2xl transition-all flex items-center justify-between gap-2 cursor-pointer">
                                <div class="min-w-0">
                                    <p class="text-xs font-bold truncate">{{ $claim->user->name ?? 'Pengguna' }}</p>
                                    <p :class="selectedId === {{ $claim->id }} ? 'text-white/70' : 'text-slate-500'"
                                       class="text-[11px] truncate">
                                        {{ $claim->item->title ?? 'Barang' }}
                                    </p>
                                </div>
                                @if($claim->status === 'pending')
                                    <span class="px-2 py-0.5 rounded-full bg-amber-300 text-slate-900 text-[10px] font-bold shrink-0">Baru</span>
                                @elseif($claim->status === 'approved')
                                    <span class="px-2 py-0.5 rounded-full bg-emerald-400 text-slate-900 text-[10px] font-bold shrink-0">OK</span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full bg-rose-300 text-slate-900 text-[10px] font-bold shrink-0">Tolak</span>
                                @endif
                            </button>
                        @endforeach
                    </div>

                    <div class="pt-2">
                        {{ $claims->links() }}
                    </div>
                </div>

                {{-- Kolom 2 & 3: Detail Perbandingan & Keputusan --}}
                <div class="lg:col-span-8">
                    @foreach($claims as $claim)
                        @php
                            $verifyAction = $activeCampus
                                ? route('campus.admin.claims.verify', ['campus' => $activeCampus, 'claim' => $claim])
                                : route('admin.claims.verify', $claim);
                            $proofUrl = $activeCampus
                                ? route('campus.claims.proof', ['campus' => $activeCampus, 'claim' => $claim])
                                : route('claims.proof', $claim);
                        @endphp

                        <div x-show="selectedId === {{ $claim->id }}" class="grid grid-cols-1 md:grid-cols-12 gap-5">
                            {{-- Kolom 2: Perbandingan Jawaban --}}
                            <div class="md:col-span-7 glass-card p-6 flex flex-col justify-between gap-6">
                                <div class="space-y-4">
                                    <h3 class="text-sm font-extrabold text-slate-900">Perbandingan Jawaban</h3>

                                    <div class="p-3.5 rounded-2xl bg-white/70 text-xs space-y-1">
                                        <p class="font-bold text-slate-500">Pertanyaan Keamanan:</p>
                                        <p class="font-bold text-slate-900">{{ $claim->item->security_question ?? '-' }}</p>
                                    </div>

                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                        <div class="space-y-1">
                                            <p class="text-[11px] font-bold text-slate-600">Jawaban Benar (Pelapor)</p>
                                            <div class="p-3.5 rounded-2xl bg-emerald-100/90 text-xs font-semibold text-[#1f5136] min-h-[70px]">
                                                {{ $claim->item->security_answer ?? 'Tidak ada jawaban rahasia' }}
                                            </div>
                                        </div>

                                        <div class="space-y-1">
                                            <p class="text-[11px] font-bold text-slate-600">Jawaban Pengklaim</p>
                                            <div class="p-3.5 rounded-2xl bg-teal-50/95 border border-teal-200 text-xs font-semibold text-slate-800 min-h-[70px]">
                                                {{ $claim->security_answer }}
                                            </div>
                                        </div>
                                    </div>

                                    @if($claim->proof_image)
                                        <div class="space-y-1.5 pt-1">
                                            <p class="text-[11px] font-bold text-slate-600">Lampiran Bukti Kepemilikan</p>
                                            <a href="{{ $proofUrl }}" target="_blank" class="block overflow-hidden rounded-2xl border border-white">
                                                <img src="{{ $proofUrl }}" alt="Bukti klaim" class="max-h-44 w-full object-cover hover:scale-105 transition-transform">
                                            </a>
                                        </div>
                                    @endif
                                </div>

                                {{-- Form Setujui / Tolak --}}
                                @if($claim->status === 'pending')
                                    <form method="POST" action="{{ $verifyAction }}" class="space-y-3 pt-4 border-t border-slate-200/70">
                                        @csrf
                                        <input type="text" name="admin_note" placeholder="Catatan petugas (opsional)..."
                                               class="w-full px-4 py-2.5 rounded-full bg-white/90 border border-white text-xs text-slate-800 focus:outline-none">

                                        <div class="grid grid-cols-2 gap-3">
                                            <button type="submit" name="action" value="approve"
                                                    class="py-3 rounded-full bg-[#0d8a50] hover:bg-[#0a6e3f] text-white text-xs font-bold shadow-xs transition-colors cursor-pointer">
                                                Setujui
                                            </button>
                                            <button type="submit" name="action" value="reject"
                                                    class="py-3 rounded-full bg-white hover:bg-rose-50 text-rose-600 text-xs font-bold shadow-xs transition-colors cursor-pointer">
                                                Tolak
                                            </button>
                                        </div>
                                    </form>
                                @else
                                    <div class="p-4 rounded-2xl {{ $claim->status === 'approved' ? 'bg-emerald-100/90 text-[#1f5136]' : 'bg-rose-100/90 text-rose-700' }} text-xs font-bold">
                                        Status: {{ strtoupper($claim->status) }}
                                        @if($claim->handover_code)
                                            <span class="block mt-1 font-mono text-sm">Kode OTP Serah Terima: {{ $claim->handover_code }}</span>
                                        @endif
                                    </div>
                                @endif
                            </div>

                            {{-- Kolom 3: Info Pengklaim & Penitipan --}}
                            <div class="md:col-span-5 glass-card p-6 space-y-4">
                                <h3 class="text-sm font-extrabold text-slate-900">Detail Pengklaim & Barang</h3>

                                <div class="space-y-2.5 text-xs text-slate-700">
                                    <div class="p-3 rounded-2xl bg-white/70">
                                        <p class="text-[10px] font-bold uppercase text-slate-400">Nama Pengklaim</p>
                                        <p class="font-bold text-slate-900 mt-0.5">{{ $claim->user->name ?? '-' }}</p>
                                        <p class="text-slate-500">{{ $claim->user->nim_nip ?? '' }} &bull; {{ $claim->user->phone_number ?? '' }}</p>
                                    </div>

                                    <div class="p-3 rounded-2xl bg-white/70">
                                        <p class="text-[10px] font-bold uppercase text-slate-400">Barang Diklaim</p>
                                        <p class="font-bold text-slate-900 mt-0.5">{{ $claim->item->title ?? '-' }}</p>
                                        <p class="text-slate-500">Titip di: {{ $claim->item->storage_location ?? '-' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</x-app-layout>