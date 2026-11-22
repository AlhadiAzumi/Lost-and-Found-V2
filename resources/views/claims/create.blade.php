@php
    $activeCampus = request()->route('campus') ?? ($campus ?? $item->campus);
    $storeAction = $activeCampus
        ? route('campus.claims.store', ['campus' => $activeCampus, 'item' => $item])
        : route('claims.store', $item);
    $detailUrl = $activeCampus
        ? route('campus.items.show', ['campus' => $activeCampus, 'item' => $item])
        : route('items.show', $item);
@endphp

<x-app-layout title="Ajukan Klaim - {{ $item->title }}">
    <div class="max-w-4xl mx-auto space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-900">
                    Formulir Klaim Kepemilikan
                </h1>
                <p class="text-xs text-slate-600 mt-1">
                    Jawab pertanyaan verifikasi dan unggah bukti agar petugas dapat memvalidasi barangmu.
                </p>
            </div>
            <a href="{{ $detailUrl }}" class="btn-secondary text-xs py-2 px-4">← Batal</a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            {{-- Ringkasan Barang --}}
            <div class="lg:col-span-5 glass-card p-6 space-y-4">
                <span class="badge-pill {{ $item->type === 'found' ? 'bg-emerald-100 text-[#1f5136]' : 'bg-rose-100 text-rose-700' }}">
                    {{ $item->type === 'found' ? '✨ Barang Temuan' : '📢 Barang Hilang' }}
                </span>

                <h2 class="text-lg font-extrabold text-slate-900">{{ $item->title }}</h2>

                <div class="space-y-2 text-xs text-slate-600 pt-2 border-t border-slate-200/70">
                    <p><strong class="text-slate-800">Kategori:</strong> {{ $item->category->name ?? '-' }}</p>
                    <p><strong class="text-slate-800">Lokasi:</strong> {{ $item->locationRecord->name ?? $item->location }}</p>
                    <p><strong class="text-slate-800">Tanggal:</strong> {{ \Carbon\Carbon::parse($item->date_event)->translatedFormat('d F Y') }}</p>
                </div>

                <div class="p-3.5 rounded-2xl bg-white/60 text-xs text-slate-600 leading-relaxed">
                    <strong>Catatan:</strong> Jawabanmu akan dibandingkan langsung dengan data rahasia yang disimpan pelapor/petugas.
                </div>
            </div>

            {{-- Form Verifikasi Klaim (Sesuai panel kanan verifikasi.jpg) --}}
            <form method="POST" action="{{ $storeAction }}" enctype="multipart/form-data"
                  x-data="{ proofPreview: '' }"
                  class="lg:col-span-7 glass-card p-6 sm:p-7 space-y-5">
                @csrf

                <div class="space-y-2">
                    <label for="security_answer" class="block text-xs font-extrabold uppercase tracking-wide text-slate-500">
                        Pertanyaan Keamanan
                    </label>
                    <div class="p-3.5 rounded-2xl bg-[#1f5136]/10 text-sm font-bold text-slate-900">
                        {{ $item->security_question ?? 'Jelaskan ciri-ciri khusus atau isi di dalam barang ini untuk membuktikan kepemilikan:' }}
                    </div>

                    <textarea id="security_answer" name="security_answer" rows="3" required
                              placeholder="Tulis jawaban spesifik Anda di sini..."
                              class="w-full px-4 py-3 rounded-2xl bg-white/90 border border-white text-sm text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-[#1f5136]/30">{{ old('security_answer') }}</textarea>
                    @error('security_answer') <p class="text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>

                {{-- Dropzone Bukti Kepemilikan (Hijau Gradien Dashed) --}}
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-700">
                        Bukti kepemilikan <span class="font-normal text-slate-500">(Foto nota, foto bersama barang, atau KTM)</span>
                    </label>
                    <label for="proof_image"
                           class="flex flex-col items-center justify-center min-h-[130px] rounded-2xl border-2 border-dashed border-[#1f5136]/45 bg-gradient-to-br from-[#b4ceb6]/75 to-[#8fb392]/75 hover:from-[#a8c6aa] hover:to-[#82a885] cursor-pointer p-4 text-center transition-all">
                        <template x-if="proofPreview">
                            <img :src="proofPreview" class="max-h-36 rounded-xl object-cover shadow-xs">
                        </template>
                        <template x-if="!proofPreview">
                            <div class="flex items-center gap-2 text-xs sm:text-sm font-semibold text-slate-800">
                                <span>🖼️</span>
                                <span>Unggah bukti kepemilikan</span>
                            </div>
                        </template>
                        <input id="proof_image" type="file" name="proof_image" accept="image/*"
                               @change="const f = $event.target.files[0]; if(f){ const r = new FileReader(); r.onload = e => proofPreview = e.target.result; r.readAsDataURL(f); }"
                               class="hidden">
                    </label>
                    @error('proof_image') <p class="text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>

                <button type="submit"
                        class="w-full py-3.5 rounded-full bg-[#161b26] hover:bg-[#1f5136] text-white text-sm font-bold shadow-sm transition-colors cursor-pointer">
                    Kirim Klaim
                </button>
            </form>
        </div>
    </div>
</x-app-layout>