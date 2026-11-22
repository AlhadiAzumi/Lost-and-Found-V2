@php
    $campus = $currentCampus ?? request()->route('campus');
    $itemUrl = route('campus.items.show', ['campus' => $campus->slug, 'item' => $item]);
@endphp

<x-app-layout>
    <div class="max-w-3xl mx-auto space-y-6 pb-8">
        {{-- Top Header --}}
        <div class="pt-1">
            <a href="{{ $itemUrl }}"
               class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-700 hover:text-[#141923] mb-1.5 transition">
                &larr; Kembali ke Detail Barang
            </a>
            <h1 class="text-2xl sm:text-[28px] font-extrabold tracking-tight text-[#141923]">
                Ajukan Klaim Barang
            </h1>
        </div>

        {{-- Ringkasan Barang & Pertanyaan Verifikasi --}}
        <div class="rounded-[28px] bg-white/40 backdrop-blur-xl border border-white/65 shadow-[0_12px_36px_rgba(20,25,35,0.06)] p-6 sm:p-7 space-y-4">
            <div class="flex flex-wrap items-center justify-between gap-2">
                <span class="rounded-full bg-emerald-100/90 text-emerald-900 px-3.5 py-1 text-xs font-bold">
                    Barang Ditemukan
                </span>
                <span class="text-xs font-medium text-slate-600">
                    {{ $item->location }} &bull; {{ $item->date_event?->translatedFormat('d M Y') }}
                </span>
            </div>

            <h2 class="text-xl font-extrabold text-[#141923]">{{ $item->title }}</h2>

            <div class="rounded-2xl bg-amber-500/10 border border-amber-400/35 p-4 space-y-1">
                <span class="block text-[11px] font-bold uppercase tracking-wider text-amber-900">
                    Pertanyaan Verifikasi dari Pelapor / Petugas
                </span>
                <p class="text-sm sm:text-base font-semibold text-slate-900">
                    {{ $item->security_question }}
                </p>
            </div>
        </div>

        {{-- Form Pengajuan Klaim --}}
        <form method="POST"
              action="{{ route('campus.claims.store', ['campus' => $campus->slug, 'item' => $item]) }}"
              enctype="multipart/form-data"
              class="rounded-[28px] bg-white/40 backdrop-blur-xl border border-white/65 shadow-[0_12px_36px_rgba(20,25,35,0.06)] p-6 sm:p-8 space-y-5">
            @csrf

            @if ($errors->any())
                <div class="rounded-2xl bg-rose-500/15 border border-rose-400/40 p-4 text-sm text-rose-950" role="alert">
                    <ul class="list-inside list-disc space-y-0.5 text-xs sm:text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div>
                <label for="security_answer" class="block text-xs font-semibold text-slate-700 mb-1.5">
                    Jawaban Verifikasi Kepemilikan <span class="text-rose-600">*</span>
                </label>
                <textarea id="security_answer" name="security_answer" rows="3" maxlength="500" required
                          placeholder="Jawab pertanyaan di atas secara spesifik untuk membuktikan barang ini milikmu..."
                          class="w-full rounded-2xl border border-white/70 bg-white/60 backdrop-blur-md px-4 py-3 text-sm text-slate-900 placeholder-slate-400 focus:bg-white/85 focus:border-[#141923] focus:ring-[#141923]">{{ old('security_answer') }}</textarea>
            </div>

            <div>
                <label for="proof_image" class="block text-xs font-semibold text-slate-700 mb-1.5">
                    Bukti Kepemilikan (Opsional — JPG, PNG, WebP, PDF; maks. 2 MB)
                </label>
                <label for="proof_image"
                       class="flex items-center justify-between gap-4 rounded-2xl border-2 border-dashed border-slate-400/50 bg-white/45 hover:bg-white/65 px-4 py-4 cursor-pointer transition">
                    <div class="min-w-0">
                        <span class="block text-xs sm:text-sm font-bold text-[#141923]">
                            Pilih file bukti (foto barang sebelumnya, nota, atau identitas)
                        </span>
                        <span id="proof-filename" class="block text-xs text-slate-600 truncate mt-0.5">
                            Belum ada berkas dipilih
                        </span>
                    </div>
                    <span class="shrink-0 rounded-full bg-white/80 border border-white px-4 py-1.5 text-xs font-semibold text-slate-800">
                        Pilih File
                    </span>
                    <input id="proof_image" name="proof_image" type="file" accept=".jpg,.jpeg,.png,.webp,.pdf" class="sr-only">
                </label>
            </div>

            <div class="pt-2 flex items-center justify-end gap-3">
                <a href="{{ $itemUrl }}"
                   class="rounded-full bg-white/55 hover:bg-white/80 border border-white/70 px-6 py-2.5 text-xs sm:text-sm font-semibold text-slate-800 transition">
                    Batal
                </a>
                <button type="submit"
                        class="rounded-full bg-[#141923] hover:bg-slate-800 px-7 py-2.5 text-xs sm:text-sm font-semibold text-white shadow-sm transition">
                    Kirim Klaim
                </button>
            </div>
        </form>
    </div>

    <script>
        const proofInput = document.getElementById('proof_image');
        const proofFilename = document.getElementById('proof-filename');

        proofInput.addEventListener('change', (e) => {
            const file = e.target.files[0];
            if (!file) {
                proofFilename.textContent = 'Belum ada berkas dipilih';
                return;
            }
            if (file.size > 2 * 1024 * 1024) {
                alert('Ukuran file bukti maksimal 2 MB.');
                proofInput.value = '';
                proofFilename.textContent = 'Belum ada berkas dipilih';
                return;
            }
            proofFilename.textContent = file.name;
        });
    </script>
</x-app-layout>