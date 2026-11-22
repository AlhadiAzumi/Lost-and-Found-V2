@php
    $activeCampus = request()->route('campus') ?? ($campus ?? (auth()->user()?->campus ?? null));
    $isEdit = $item !== null;
    $reportType = old('type', $defaultType ?? 'found');

    if ($isEdit) {
        $formAction = $activeCampus
            ? route('campus.items.update', ['campus' => $activeCampus, 'item' => $item])
            : route('items.update', $item);
    } else {
        $formAction = $activeCampus
            ? route('campus.reports.store', $activeCampus)
            : route('items.store');
    }
@endphp

<x-app-layout title="{{ $reportType === 'found' ? 'Laporkan Barang Ditemukan' : 'Laporkan Barang Hilang' }}">
    <div x-data="{
            type: '{{ $reportType }}',
            isSensitive: {{ old('is_sensitive', $item->is_sensitive ?? false) ? 'true' : 'false' }},
            photoPreview: '{{ ($item && ($item->image_path ?? $item->image)) ? asset('storage/' . ($item->image_path ?? $item->image)) : '' }}',
            selectedLocationId: '{{ old('location_id', $item->location_id ?? '') }}',
            locationText: '{{ old('location', $item->location ?? '') }}',
            syncLocation(e) {
                const opt = e.target.options[e.target.selectedIndex];
                if (opt && opt.value) {
                    this.locationText = opt.text.trim();
                }
            },
            handlePhoto(e) {
                const file = e.target.files[0];
                if (!file) return;
                const reader = new FileReader();
                reader.onload = (ev) => { this.photoPreview = ev.target.result; };
                reader.readAsDataURL(file);
            }
        }"
        class="space-y-6">

        {{-- Header Judul --}}
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-900">
                {{ $reportType === 'found'
                    ? ($isEdit ? 'Edit Laporan Barang Temuan' : 'Laporkan Barang Ditemukan')
                    : ($isEdit ? 'Edit Laporan Barang Hilang' : 'Laporkan Barang Hilang') }}
            </h1>
        </div>

        {{-- Status Stepper Bar (Khusus Mode Lapor Hilang sesuai lapor_hilang.jpg) --}}
        <div x-show="type === 'lost'" x-transition class="glass-card px-6 py-4">
            <div class="grid grid-cols-4 gap-2 items-center text-center">
                <div class="space-y-1.5">
                    <div class="w-4 h-4 mx-auto rounded-full bg-[#10b981] ring-4 ring-[#10b981]/20"></div>
                    <p class="text-[11px] font-bold text-slate-800">Dilaporkan</p>
                </div>
                <div class="space-y-1.5">
                    <div class="w-4 h-4 mx-auto rounded-full bg-slate-300"></div>
                    <p class="text-[11px] font-medium text-slate-500">Diverifikasi</p>
                </div>
                <div class="space-y-1.5">
                    <div class="w-4 h-4 mx-auto rounded-full bg-slate-300"></div>
                    <p class="text-[11px] font-medium text-slate-500">Dalam Pencarian</p>
                </div>
                <div class="space-y-1.5">
                    <div class="w-4 h-4 mx-auto rounded-full bg-slate-300"></div>
                    <p class="text-[11px] font-medium text-slate-500">Selesai</p>
                </div>
            </div>
        </div>

        {{-- Form Utama 2 Kolom --}}
        <form method="POST" action="{{ $formAction }}" enctype="multipart/form-data" class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            @csrf
            @if($isEdit)
                @method('PUT')
            @endif
            <input type="hidden" name="type" :value="type">

            {{-- Kolom Kiri: Detail Barang & Upload Foto --}}
            <div class="lg:col-span-7 glass-card p-6 sm:p-7 space-y-4">
                {{-- Nama Barang --}}
                <div class="space-y-1.5">
                    <label for="title" class="block text-xs font-bold text-slate-700">Nama barang</label>
                    <input id="title" type="text" name="title" value="{{ old('title', $item->title ?? '') }}" required
                           :placeholder="type === 'found' ? 'Contoh: Kunci motor' : 'Contoh: Dompet kulit hitam'"
                           class="w-full px-4 py-3 rounded-full bg-white/90 border border-white text-sm text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-[#1f5136]/30">
                    @error('title') <p class="text-xs text-rose-600 pl-2">{{ $message }}</p> @enderror
                </div>

                {{-- Grid Kategori & Tanggal --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label for="category_id" class="block text-xs font-bold text-slate-700">Kategori</label>
                        <select id="category_id" name="category_id" required
                                class="w-full px-4 py-3 rounded-full bg-white/90 border border-white text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#1f5136]/30">
                            <option value="">Pilih kategori</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" @selected((string)old('category_id', $item->category_id ?? '') === (string)$category->id)>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id') <p class="text-xs text-rose-600 pl-2">{{ $message }}</p> @enderror
                    </div>

                    <div class="space-y-1.5">
                        <label for="date_event" class="block text-xs font-bold text-slate-700">
                            <span x-text="type === 'found' ? 'Tanggal ditemukan' : 'Tanggal hilang'"></span>
                        </label>
                        <input id="date_event" type="date" name="date_event"
                               max="{{ now()->toDateString() }}"
                               value="{{ old('date_event', isset($item->date_event) ? \Carbon\Carbon::parse($item->date_event)->toDateString() : '') }}"
                               required
                               class="w-full px-4 py-3 rounded-full bg-white/90 border border-white text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#1f5136]/30">
                        @error('date_event') <p class="text-xs text-rose-600 pl-2">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Lokasi Area Kampus --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label for="location_id" class="block text-xs font-bold text-slate-700">Area / Gedung Kampus</label>
                        <select id="location_id" name="location_id" x-model="selectedLocationId" @change="syncLocation($event)"
                                class="w-full px-4 py-3 rounded-full bg-white/90 border border-white text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#1f5136]/30">
                            <option value="">Pilih area kampus</option>
                            @foreach($locations as $loc)
                                <option value="{{ $loc->id }}">{{ $loc->name }}</option>
                            @endforeach
                        </select>
                        @error('location_id') <p class="text-xs text-rose-600 pl-2">{{ $message }}</p> @enderror
                    </div>

                    <div class="space-y-1.5">
                        <label for="location" class="block text-xs font-bold text-slate-700">Detail titik lokasi *</label>
                        <input id="location" type="text" name="location" x-model="locationText" required
                               placeholder="Contoh: Parkiran Gedung TI Lt. 1"
                               class="w-full px-4 py-3 rounded-full bg-white/90 border border-white text-sm text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-[#1f5136]/30">
                        @error('location') <p class="text-xs text-rose-600 pl-2">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Deskripsi Umum --}}
                <div class="space-y-1.5">
                    <label for="description" class="block text-xs font-bold text-slate-700">
                        <span x-text="type === 'found' ? 'Deskripsi umum (aman dipublikasikan)' : 'Deskripsi & ciri khas barang'"></span>
                    </label>
                    <textarea id="description" name="description" rows="3" required
                              :placeholder="type === 'found' ? 'Deskripsi yang aman dipublikasikan tanpa membocorkan ciri rahasia...' : 'Ciri khas, warna, merek, isi di dalamnya...'"
                              class="w-full px-4 py-3 rounded-2xl bg-white/90 border border-white text-sm text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-[#1f5136]/30">{{ old('description', $item->description ?? '') }}</textarea>
                    @error('description') <p class="text-xs text-rose-600 pl-2">{{ $message }}</p> @enderror
                </div>

                {{-- Dropzone Unggah Foto (Kotak Hijau Gradien Dashed sesuai desain) --}}
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-700">Foto barang <span class="font-normal text-slate-500">(Maks. 2MB)</span></label>
                    <label for="image"
                           class="relative flex flex-col items-center justify-center min-h-[150px] rounded-2xl border-2 border-dashed border-[#1f5136]/45 bg-gradient-to-br from-[#b4ceb6]/75 to-[#8fb392]/75 hover:from-[#a8c6aa] hover:to-[#82a885] cursor-pointer overflow-hidden transition-all p-4 text-center">
                        <template x-if="photoPreview">
                            <div class="space-y-2">
                                <img :src="photoPreview" alt="Preview" class="max-h-40 mx-auto rounded-xl object-cover shadow-sm">
                                <span class="inline-block px-3 py-1 rounded-full bg-white/85 text-[11px] font-bold text-slate-800">
                                    Klik untuk ganti foto
                                </span>
                            </div>
                        </template>
                        <template x-if="!photoPreview">
                            <div class="flex items-center gap-2 text-slate-800 font-semibold text-xs sm:text-sm">
                                <svg class="w-5 h-5 text-[#1f5136]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <span x-text="type === 'found' ? 'Unggah foto barang temuan' : 'Seret foto atau klik untuk unggah'"></span>
                            </div>
                        </template>
                        <input id="image" type="file" name="image" accept="image/jpeg,image/png,image/webp" @change="handlePhoto($event)" class="hidden">
                    </label>
                    @error('image') <p class="text-xs text-rose-600 pl-2">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Kolom Kanan: Privasi & Verifikasi (Found) / Konfirmasi Kirim (Lost) --}}
            <div class="lg:col-span-5 glass-card p-6 sm:p-7 space-y-5">
                {{-- Toggle Sembunyikan Detail Sensitif --}}
                <div class="flex items-start justify-between gap-4 pb-4 border-b border-slate-200/70">
                    <div>
                        <h2 class="text-sm font-extrabold text-slate-900">Privasi & Verifikasi</h2>
                        <p class="text-xs font-semibold text-slate-700 mt-1">Sembunyikan foto & detail dari publik</p>
                        <p class="text-[11px] text-slate-500">Wajib dicentang untuk KTM, KTP, dompet, atau kartu ATM.</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer shrink-0 mt-1">
                        <input type="checkbox" name="is_sensitive" value="1" x-model="isSensitive" class="sr-only peer">
                        <div class="w-11 h-6 bg-slate-300 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#0d9458]"></div>
                    </label>
                </div>

                {{-- Field Khusus Barang Temuan (Sesuai StoreItemReportRequest) --}}
                <template x-if="type === 'found'">
                    <div class="space-y-4">
                        <div class="space-y-1.5">
                            <label for="storage_location" class="block text-xs font-bold text-slate-700">
                                Lokasi penitipan barang sekarang *
                            </label>
                            <input id="storage_location" type="text" name="storage_location"
                                   value="{{ old('storage_location', $item->storage_location ?? '') }}"
                                   required
                                   placeholder="Contoh: Pos Satpam Gerbang Utama / Tata Usaha"
                                   class="w-full px-4 py-3 rounded-full bg-white/90 border border-white text-sm text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-[#1f5136]/30">
                            @error('storage_location') <p class="text-xs text-rose-600 pl-2">{{ $message }}</p> @enderror
                        </div>

                        <div class="space-y-1.5">
                            <label for="security_question" class="block text-xs font-bold text-slate-700">
                                Pertanyaan keamanan untuk pengklaim *
                            </label>
                            <input id="security_question" type="text" name="security_question"
                                   value="{{ old('security_question', $item->security_question ?? '') }}"
                                   required
                                   placeholder="Contoh: Apa isi kantong depan tas / warna wallpaper HP?"
                                   class="w-full px-4 py-3 rounded-full bg-white/90 border border-white text-sm text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-[#1f5136]/30">
                            @error('security_question') <p class="text-xs text-rose-600 pl-2">{{ $message }}</p> @enderror
                        </div>

                        <div class="space-y-1.5">
                            <label for="security_answer" class="block text-xs font-bold text-slate-700">
                                Jawaban benar (hanya petugas yang melihat) *
                            </label>
                            <input id="security_answer" type="text" name="security_answer"
                                   value="{{ old('security_answer') }}"
                                   {{ $isEdit ? '' : 'required' }}
                                   placeholder="{{ $isEdit ? 'Kosongkan jika tidak mengubah jawaban' : 'Jawaban spesifik untuk verifikasi' }}"
                                   class="w-full px-4 py-3 rounded-full bg-white/90 border border-white text-sm text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-[#1f5136]/30">
                            @error('security_answer') <p class="text-xs text-rose-600 pl-2">{{ $message }}</p> @enderror
                        </div>

                        <button type="submit"
                                class="w-full py-3.5 rounded-full bg-[#0d8a50] hover:bg-[#0a6e3f] text-white text-sm font-bold shadow-sm transition-colors cursor-pointer">
                            {{ $isEdit ? 'Simpan Perubahan Temuan' : 'Publikasikan Temuan' }}
                        </button>
                    </div>
                </template>

                {{-- Field Khusus Barang Hilang --}}
                <template x-if="type === 'lost'">
                    <div class="space-y-4">
                        <div class="p-4 rounded-2xl bg-gradient-to-br from-[#b4ceb6]/55 to-[#8fb392]/55 border border-white/70 space-y-2">
                            <p class="text-xs font-extrabold text-slate-900">📌 Tips agar cepat ditemukan:</p>
                            <ul class="text-xs text-slate-700 space-y-1 list-disc list-inside">
                                <li>Sebutkan ciri khusus seperti stiker, goresan, atau gantungan kunci.</li>
                                <li>Tulis perkiraan jam dan ruangan terakhir barang terlihat.</li>
                                <li>Aktifkan opsi sensitif jika barang memuat data pribadi.</li>
                            </ul>
                        </div>

                        <button type="submit"
                                class="w-full py-3.5 rounded-full bg-[#161b26] hover:bg-[#1f5136] text-white text-sm font-bold shadow-sm transition-colors cursor-pointer">
                            {{ $isEdit ? 'Simpan Perubahan Laporan' : 'Kirim Laporan' }}
                        </button>
                    </div>
                </template>
            </div>
        </form>
    </div>
</x-app-layout>