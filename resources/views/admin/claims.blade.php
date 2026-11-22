@php
    $campus = $currentCampus ?? request()->route('campus');
    $claimStatusMap = [
        'pending' => ['Menunggu Verifikasi', 'bg-amber-100/90 text-amber-900'],
        'approved' => ['Disetujui', 'bg-emerald-100/90 text-emerald-900'],
        'rejected' => ['Ditolak', 'bg-rose-100/90 text-rose-800'],
        'completed' => ['Selesai Diserahkan', 'bg-slate-800 text-white'],
    ];
@endphp

<x-app-layout>
    <div class="space-y-6 pb-8">
        {{-- Header & Tab Navigasi Admin --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pt-1">
            <div>
                <h1 class="text-2xl sm:text-[28px] font-extrabold tracking-tight text-[#141923]">
                    Verifikasi Klaim
                </h1>
                <p class="mt-1 text-xs sm:text-sm text-slate-600">
                    Cocokkan jawaban verifikasi dan bukti kepemilikan sebelum menerbitkan kode serah terima.
                </p>
            </div>

            @include('admin.partials.nav-tabs')
        </div>

        @if (session('success'))
            <div class="rounded-[22px] bg-emerald-500/15 backdrop-blur-xl border border-emerald-400/40 px-4 py-3.5 text-sm font-medium text-emerald-950" role="status">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="rounded-[22px] bg-rose-500/15 backdrop-blur-xl border border-rose-400/40 p-4 text-sm text-rose-950" role="alert">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <div class="space-y-4">
            @forelse ($claims as $claim)
                @php
                    [$cText, $cBadge] = $claimStatusMap[$claim->status] ?? [ucfirst($claim->status), 'bg-white/70 text-slate-800'];
                    $isMatch = $claim->securityAnswerMatches();
                @endphp
                <article class="rounded-[26px] bg-white/40 backdrop-blur-xl border border-white/65 shadow-[0_12px_36px_rgba(20,25,35,0.06)] p-6 space-y-4">
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div>
                            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Pengajuan Klaim Barang</span>
                            <h2 class="text-lg font-extrabold text-[#141923]">
                                {{ $claim->item?->title }}
                                <span class="font-normal text-slate-600">&mdash; oleh</span>
                                <span class="text-[#1B4332]">{{ $claim->user?->name }}</span>
                            </h2>
                        </div>

                        <span class="rounded-full px-3.5 py-1 text-xs font-bold {{ $cBadge }}">
                            {{ $cText }}
                        </span>
                    </div>

                    {{-- Perbandingan Jawaban --}}
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="rounded-2xl bg-white/55 border border-white/75 p-4">
                            <p class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Jawaban Pengklaim</p>
                            <p class="mt-1 text-sm font-semibold text-slate-900">{{ $claim->security_answer }}</p>
                        </div>

                        <div class="rounded-2xl bg-white/55 border border-white/75 p-4">
                            <p class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Pencocokan Jawaban Rahasia</p>
                            <p class="mt-1 text-sm font-bold {{ $isMatch === true ? 'text-emerald-700' : ($isMatch === false ? 'text-rose-700' : 'text-slate-700') }}">
                                {{ $isMatch === true ? 'Cocok dengan referensi' : ($isMatch === false ? 'Tidak cocok dengan referensi' : 'Belum ada jawaban referensi') }}
                            </p>
                        </div>
                    </div>

                    @if ($claim->proof_image)
                        <div>
                            <a href="{{ route('campus.claims.proof', ['campus' => $campus->slug, 'claim' => $claim]) }}"
                               target="_blank"
                               class="inline-flex items-center gap-2 rounded-full bg-white/65 hover:bg-white border border-white/80 px-4 py-2 text-xs font-semibold text-[#141923] transition">
                                Lihat Bukti Kepemilikan (Privat) &nearr;
                            </a>
                        </div>
                    @endif

                    @if ($claim->status === 'pending')
                        <div class="pt-4 border-t border-white/55 flex flex-col lg:flex-row lg:items-center justify-between gap-3">
                            <form method="POST" action="{{ route('campus.admin.claims.verify', ['campus' => $campus->slug, 'claim' => $claim]) }}">
                                @csrf
                                <input type="hidden" name="action" value="approve">
                                <button type="submit"
                                        class="rounded-full bg-[#141923] hover:bg-slate-800 px-6 py-2.5 text-xs sm:text-sm font-semibold text-white shadow-sm transition">
                                    Setujui &amp; Terbitkan Kode
                                </button>
                            </form>

                            <form method="POST" action="{{ route('campus.admin.claims.verify', ['campus' => $campus->slug, 'claim' => $claim]) }}"
                                  class="flex flex-1 max-w-xl items-center gap-2">
                                @csrf
                                <input type="hidden" name="action" value="reject">
                                <input name="admin_note" required maxlength="2000"
                                       placeholder="Tulis alasan penolakan klaim..."
                                       class="flex-1 rounded-full border border-white/75 bg-white/65 px-4 py-2 text-xs sm:text-sm text-slate-900 placeholder-slate-400 focus:bg-white focus:border-[#141923] focus:ring-[#141923]">
                                <button type="submit"
                                        class="shrink-0 rounded-full bg-rose-600 hover:bg-rose-700 px-5 py-2 text-xs sm:text-sm font-semibold text-white transition">
                                    Tolak
                                </button>
                            </form>
                        </div>
                    @elseif ($claim->handover_code)
                        <div class="rounded-2xl bg-[#141923] text-white px-4 py-3 inline-flex items-center gap-3">
                            <span class="text-xs text-slate-300">Kode serah terima:</span>
                            <span class="font-mono text-sm font-extrabold tracking-widest text-emerald-300">{{ $claim->handover_code }}</span>
                        </div>
                    @endif
                </article>
            @empty
                <div class="rounded-[26px] bg-white/40 backdrop-blur-xl border border-white/65 p-12 text-center shadow-[0_12px_36px_rgba(20,25,35,0.06)]">
                    <p class="text-base font-bold text-slate-900">Belum ada pengajuan klaim</p>
                    <p class="mt-1 text-xs text-slate-600">Klaim barang dari mahasiswa atau staf akan muncul di halaman ini.</p>
                </div>
            @endforelse
        </div>

        <div>{{ $claims->links() }}</div>
    </div>
</x-app-layout>