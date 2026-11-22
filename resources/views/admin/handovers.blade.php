@php
    $campus = $currentCampus ?? request()->route('campus');
@endphp

<x-app-layout>
    <div class="max-w-5xl mx-auto space-y-6 pb-8">
        {{-- Top Header --}}
        <div class="pt-1">
            <h1 class="text-2xl sm:text-[28px] font-extrabold tracking-tight text-[#141923]">
                Serah Terima Barang
            </h1>
            <p class="mt-1 text-xs sm:text-sm text-slate-600">
                Verifikasi kode serah terima milik pengklaim sebelum menyerahkan barang fisik.
            </p>
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

        {{-- Form Cari Kode Serah Terima --}}
        <div class="rounded-[28px] bg-white/40 backdrop-blur-xl border border-white/65 shadow-[0_12px_36px_rgba(20,25,35,0.06)] p-6">
            <form method="GET" action="{{ route('campus.admin.handovers', ['campus' => $campus->slug]) }}"
                  class="flex flex-col sm:flex-row gap-3">
                <input name="code" value="{{ $searchCode }}" maxlength="20" required
                       placeholder="Masukkan kode serah terima (Contoh: HND-XXXXXX)"
                       class="flex-1 rounded-full border border-white/70 bg-white/65 backdrop-blur-md px-5 py-3 font-mono text-sm font-semibold uppercase tracking-wider text-slate-900 placeholder-slate-400 focus:bg-white/90 focus:border-[#141923] focus:ring-[#141923]">
                <button type="submit"
                        class="rounded-full bg-[#141923] hover:bg-slate-800 px-7 py-3 text-sm font-semibold text-white shadow-sm transition">
                    Cari Kode
                </button>
            </form>
        </div>

        {{-- Hasil Pencarian Kode --}}
        @if ($claimToHandover)
            <article class="rounded-[28px] bg-white/55 backdrop-blur-xl border border-white/80 shadow-[0_16px_40px_rgba(20,25,35,0.08)] p-6 sm:p-7 space-y-5">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Data Klaim Ditemukan</span>
                        <h2 class="text-xl font-extrabold text-[#141923] mt-0.5">
                            {{ $claimToHandover->item?->title }}
                        </h2>
                        <p class="text-sm text-slate-700 mt-1">
                            Penerima: <strong class="font-bold text-slate-900">{{ $claimToHandover->user?->name }}</strong>
                            @if ($claimToHandover->user?->nim_nip)
                                ({{ $claimToHandover->user->nim_nip }})
                            @endif
                        </p>
                    </div>

                    <span class="rounded-full px-3.5 py-1 text-xs font-bold {{ $claimToHandover->status === 'approved' ? 'bg-emerald-100 text-emerald-900' : 'bg-amber-100 text-amber-900' }}">
                        Status: {{ ucfirst($claimToHandover->status) }}
                    </span>
                </div>

                @if ($claimToHandover->status === 'approved' && ! $claimToHandover->handover)
                    <form method="POST"
                          action="{{ route('campus.admin.handovers.process', ['campus' => $campus->slug, 'claim' => $claimToHandover]) }}"
                          class="space-y-4 pt-3 border-t border-white/65">
                        @csrf
                        <input type="hidden" name="code" value="{{ $searchCode }}">

                        <div>
                            <label for="notes" class="block text-xs font-semibold text-slate-700 mb-1.5">
                                Catatan Serah Terima (Opsional)
                            </label>
                            <textarea id="notes" name="notes" rows="2" maxlength="2000"
                                      placeholder="Contoh: KTM penerima sudah dicek sesuai nama akun..."
                                      class="w-full rounded-2xl border border-white/75 bg-white/70 px-4 py-2.5 text-sm text-slate-900 focus:bg-white focus:border-[#141923] focus:ring-[#141923]"></textarea>
                        </div>

                        <button type="submit"
                                class="rounded-full bg-[#141923] hover:bg-slate-800 px-6 py-3 text-xs sm:text-sm font-semibold text-white shadow-sm transition">
                            Konfirmasi Barang Diserahkan
                        </button>
                    </form>
                @else
                    <div class="rounded-2xl bg-amber-500/15 border border-amber-400/40 px-4 py-3 text-xs sm:text-sm font-medium text-amber-950">
                        Kode tidak valid, klaim belum disetujui, atau barang sudah diserahterimakan sebelumnya.
                    </div>
                @endif
            </article>
        @elseif ($searchCode !== '')
            <div class="rounded-[22px] bg-amber-500/15 backdrop-blur-xl border border-amber-400/40 px-5 py-4 text-sm font-medium text-amber-950">
                Kode <span class="font-mono font-bold">{{ $searchCode }}</span> tidak ditemukan di sistem.
            </div>
        @endif

        {{-- Riwayat Serah Terima --}}
        <section class="rounded-[28px] bg-white/40 backdrop-blur-xl border border-white/65 shadow-[0_12px_36px_rgba(20,25,35,0.06)] p-6 space-y-4">
            <h2 class="text-lg font-extrabold text-[#141923]">Riwayat Serah Terima</h2>

            <div class="space-y-2.5">
                @forelse ($handovers as $handover)
                    <div class="rounded-2xl bg-white/55 border border-white/70 px-4 py-3.5 flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-xs sm:text-sm">
                        <div>
                            <strong class="font-bold text-[#141923]">{{ $handover->item?->title }}</strong>
                            <span class="text-slate-700">diserahkan kepada</span>
                            <strong class="font-semibold text-slate-900">{{ $handover->recipient?->name }}</strong>
                        </div>
                        <div class="text-xs text-slate-600 shrink-0">
                            Petugas: <span class="font-semibold text-slate-800">{{ $handover->officer?->name }}</span>
                            &bull; {{ $handover->handover_date?->translatedFormat('d M Y, H:i') }}
                        </div>
                    </div>
                @empty
                    <div class="rounded-2xl bg-white/40 border border-white/60 p-8 text-center text-xs sm:text-sm text-slate-600">
                        Belum ada riwayat serah terima.
                    </div>
                @endforelse
            </div>

            <div>{{ $handovers->links() }}</div>
        </section>
    </div>
</x-app-layout>