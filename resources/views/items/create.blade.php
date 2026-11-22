@php
    $campus = $currentCampus ?? request()->route('campus');
    $formAction = $item
        ? route('campus.items.update', ['campus' => $campus->slug, 'item' => $item])
        : route('campus.reports.store', ['campus' => $campus->slug]);
    $cancelUrl = route('campus.home', ['campus' => $campus->slug]);
    $selectedType = old('type', $defaultType ?? 'lost');
    $isFound = $selectedType === 'found';
@endphp

<x-app-layout>
    <div class="max-w-4xl mx-auto space-y-6 pb-8">
        {{-- Top Header --}}
        <div class="flex flex-wrap items-center justify-between gap-4 pt-1">
            <div>
                <a href="{{ $cancelUrl }}"
                   class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-700 hover:text-[#141923] mb-1.5 transition">
                    &larr; Kembali ke Feed Publik
                </a>
                <h1 class="text-2xl sm:text-[28px] font-extrabold tracking-tight text-[#141923]">
                    @if ($item)
                        Edit Laporan {{ $isFound ? 'Barang Temuan' : 'Barang Hilang' }}
                    @else
                        {{ $isFound ? 'Lapor Barang Ditemukan' : 'Lapor Barang Hilang' }}
                    @endif
                </h1>
            </div>
        </div>

        {{-- Error Alert --}}
        @if ($errors->any())
            <div class="rounded-[22px] bg-rose-500/15 backdrop-blur-xl border border-rose-400/40 p-4 text-sm text-rose-950" role="alert">
                <p class="font-bold">Periksa kembali data laporan:</p>
                <ul class="mt-1.5 list-inside list-disc space-y-0.5 text-xs sm:text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Form Glass Card --}}
        <form method="POST" action="{{ $formAction }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @if ($item)
                @method('PUT')
            @endif

            {{-- Hidden Type (otomatis mengikuti menu navigasi atau data item saat edit) --}}
            <input type="hidden" id="type" name="type" value="{{ $selectedType }}">

            {{-- Section 1: Informasi Utama --}}
            <div class="rounded-[28px] bg-white/40 backdrop-blur-xl border border-white/65 shadow-[0_12px_36px_rgba(20,25,35,0.06)] p-6 sm:p-8 space-y-5">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label for="title" class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Nama Barang <span class="text-rose-600">*</span>
                        </label>
                        <input id="title" name="title" type="text"
                               value="{{ old('title', $item?->title) }}" maxlength="150" required
                               placeholder="Contoh: Dompet Kulit Hitam / Kunci Motor Honda"
                               class="w-full rounded-2xl border border-white/70 bg-white/60 backdrop-blur-md px-4 py-2.5 text-sm text-slate-900 placeholder-slate-400 focus:bg-white/85 focus:border-[#141923] focus:ring-[#141923]">
                    </div>

                    <div>
                        <label for="category_id" class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Kategori <span class="text-rose-600">*</span>
                        </label>
                        <select id="category_id" name="category_id" required
                                class="w-full rounded-2xl border border-white/70 bg-white/60 backdrop-blur-md px-4 py-2.5 text-sm text-slate-900 focus:bg-white/85 focus:border-[#141923] focus:ring-[#141923]">
                            <option value="">Pilih kategori</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" @selected((string) old('category_id', $item?->category_id) === (string) $category->id)>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label for="description" class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Deskripsi &amp; Ciri-ciri Barang <span class="text-rose-600">*</span>
                    </label>
                    <textarea id="description" name="description" rows="4" maxlength="3000" required
                              placeholder="Jelaskan merek, warna, stiker, atau tanda khusus lainnya..."
                              class="w-full rounded-2xl border border-white/70 bg-white/60 backdrop-blur-md px-4 py-3 text-sm text-slate-900 placeholder-slate-400 focus:bg-white/85 focus:border-[#141923] focus:ring-[#141923]">{{ old('description', $item?->description) }}</textarea>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label for="location" class="block text-xs font-semibold text-slate-700 mb-1.5">
                            {{ $isFound ? 'Lokasi Ditemukan' : 'Lokasi Hilang' }} <span class="text-rose-600">*</span>
                        </label>
                        <input id="location" name="location" type="text"
                               value="{{ old('location', $item?->location) }}" maxlength="150" required
                               placeholder="Contoh: Gedung Teknik Lantai 2 / Kantin"
                               class="w-full rounded-2xl border border-white/70 bg-white/60 backdrop-blur-md px-4 py-2.5 text-sm text-slate-900 placeholder-slate-400 focus:bg-white/85 focus:border-[#141923] focus:ring-[#141923]">
                    </div>

                    <div>
                        <label for="date_event" class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Tanggal Kejadian <span class="text-rose-600">*</span>
                        </label>
                        <input id="date_event" name="date_event" type="date"
                               value="{{ old('date_event', $item?->date_event?->toDateString()) }}"
                               max="{{ now()->toDateString() }}" required
                               class="w-full rounded-2xl border border-white/70 bg-white/60 backdrop-blur-md px-4 py-2.5 text-sm text-slate-900 focus:bg-white/85 focus:border-[#141923] focus:ring-[#141923]">
                    </div>
                </div>
            </div>

            {{-- Section 2: Upload Foto Storage & Sensitivitas --}}
            <div class="rounded-[28px] bg-white/40 backdrop-blur-xl border border-white/65 shadow-[0_12px_36px_rgba(20,25,35,0.06)] p-6 sm:p-8 space-y-5">
                <div>
                    <h2 class="text-base font-bold text-[#141923]">Foto Barang</h2>
                    <p class="text-xs text-slate-600 mt-0.5">
                        Format JPG, JPEG, PNG, atau WEBP dengan ukuran maksimal 2 MB.
                    </p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-12 gap-5 items-center">
                    {{-- Dropzone Upload --}}
                    <div class="sm:col-span-7">
                        <label for="image" id="upload-dropzone"
                               class="flex flex-col items-center justify-center rounded-2xl border-2 border-dashed border-slate-400/50 bg-white/45 hover:bg-white/65 px-5 py-7 text-center cursor-pointer transition">
                            <svg class="w-8 h-8 text-slate-600 mb-2" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                            </svg>
                            <span class="text-xs sm:text-sm font-bold text-[#141923]">
                                Klik untuk pilih foto atau seret ke sini
                            </span>
                            <span id="image-filename" class="mt-1 text-xs text-slate-600 truncate max-w-full">
                                Belum ada berkas dipilih
                            </span>
                            <input id="image" name="image" type="file" accept=".jpg,.jpeg,.png,.webp" class="sr-only">
                        </label>
                    </div>

                    {{-- Live Preview Box --}}
                    <div class="sm:col-span-5">
                        <div class="relative h-40 w-full rounded-2xl overflow-hidden bg-gradient-to-br from-[#B8D1BA]/70 to-[#7FA985]/70 border border-white/70 flex items-center justify-center">
                            <img id="image-preview"
                                 src="{{ $item?->image ? asset('storage/'.$item->image) : '' }}"
                                 alt="Preview foto barang"
                                 class="{{ $item?->image ? '' : 'hidden' }} h-full w-full object-cover">

                            <div id="image-placeholder" class="{{ $item?->image ? 'hidden' : '' }} text-center px-4 text-slate-700/70">
                                <svg class="w-7 h-7 mx-auto mb-1" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24">
                                    <rect x="3" y="5" width="18" height="14" rx="2"/>
                                    <circle cx="8.5" cy="10.5" r="1.5"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 15l-5-5L5 19"/>
                                </svg>
                                <span class="text-[11px] font-semibold">Pratinjau Foto</span>
                            </div>

                            <button type="button" id="remove-image-btn"
                                    class="hidden absolute top-2 right-2 rounded-full bg-[#141923]/85 text-white px-2.5 py-1 text-[11px] font-semibold hover:bg-[#141923]">
                                Hapus
                            </button>
                        </div>
                    </div>
                </div>

                <label class="flex items-start gap-3 rounded-2xl bg-white/45 border border-white/65 p-3.5 text-xs sm:text-sm text-slate-800 cursor-pointer">
                    <input type="checkbox" name="is_sensitive" value="1"
                           @checked(old('is_sensitive', $item?->is_sensitive))
                           class="mt-0.5 rounded border-slate-400 text-[#141923] focus:ring-[#141923]">
                    <span>
                        <strong class="font-semibold text-[#141923]">Tandai sebagai informasi sensitif</strong> — foto dan detail rahasia (misalnya KTM, KTP, kartu ATM) tidak akan ditampilkan terbuka di feed publik.
                    </span>
                </label>
            </div>

            {{-- Section 3: Khusus Barang Ditemukan (Penitipan & Pertanyaan Keamanan) --}}
            <div id="found-only-section"
                 class="rounded-[28px] bg-white/40 backdrop-blur-xl border border-white/65 shadow-[0_12px_36px_rgba(20,25,35,0.06)] p-6 sm:p-8 space-y-5">
                <div>
                    <span class="inline-block rounded-full bg-emerald-100/90 text-emerald-900 px-3 py-0.5 text-[11px] font-bold mb-1.5">
                        Khusus Barang Temuan
                    </span>
                    <h2 class="text-base font-bold text-[#141923]">Penitipan &amp; Verifikasi Pemilik</h2>
                    <p class="text-xs text-slate-600 mt-0.5">
                        Digunakan petugas dan sistem untuk mencocokkan klaim dari pemilik asli.
                    </p>
                </div>

                <div id="storage-location-field">
                    <label for="storage_location" class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Lokasi Penitipan Barang Temuan <span class="text-rose-600">*</span>
                    </label>
                    <input id="storage_location" name="storage_location" type="text"
                           value="{{ old('storage_location', $item?->storage_location) }}" maxlength="150"
                           placeholder="Contoh: Pos Satpam Gerbang Utama / Dibawa Pelapor"
                           class="w-full rounded-2xl border border-white/70 bg-white/60 backdrop-blur-md px-4 py-2.5 text-sm text-slate-900 placeholder-slate-400 focus:bg-white/85 focus:border-[#141923] focus:ring-[#141923]">
                </div>

                <div id="security-question-field">
                    <label for="security_question" class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Pertanyaan Verifikasi untuk Pemilik <span class="text-rose-600">*</span>
                    </label>
                    <textarea id="security_question" name="security_question" rows="2" maxlength="500"
                              placeholder="Contoh: Apa warna wallpaper lockscreen HP ini atau isi di dalam saku depan tas?"
                              class="w-full rounded-2xl border border-white/70 bg-white/60 backdrop-blur-md px-4 py-2.5 text-sm text-slate-900 placeholder-slate-400 focus:bg-white/85 focus:border-[#141923] focus:ring-[#141923]">{{ old('security_question', $item?->security_question) }}</textarea>
                    <p class="mt-1 text-xs text-slate-600">
                        Pastikan pertanyaan tidak membocorkan jawabannya secara langsung.
                    </p>
                </div>

                <div id="security-answer-field">
                    <label for="security_answer" class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Jawaban Keamanan (Rahasia) <span class="text-rose-600">*</span>
                    </label>
                    <input id="security_answer" name="security_answer" type="password" maxlength="500" autocomplete="new-password"
                           placeholder="Masukkan jawaban yang hanya diketahui pemilik asli"
                           class="w-full rounded-2xl border border-white/70 bg-white/60 backdrop-blur-md px-4 py-2.5 text-sm text-slate-900 placeholder-slate-400 focus:bg-white/85 focus:border-[#141923] focus:ring-[#141923]">
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="flex items-center justify-end gap-3 pt-1">
                <a href="{{ $cancelUrl }}"
                   class="rounded-full bg-white/55 hover:bg-white/80 border border-white/70 px-6 py-3 text-xs sm:text-sm font-semibold text-slate-800 transition">
                    Batal
                </a>
                <button type="submit"
                        class="rounded-full bg-[#141923] hover:bg-slate-800 px-7 py-3 text-xs sm:text-sm font-semibold text-white shadow-sm transition">
                    {{ $item ? 'Simpan Perubahan' : 'Kirim Laporan' }}
                </button>
            </div>
        </form>
    </div>

    <script>
        const typeField = document.getElementById('type');
        const foundSection = document.getElementById('found-only-section');
        const questionField = document.getElementById('security_question');
        const answerField = document.getElementById('security_answer');
        const storageField = document.getElementById('storage_location');

        function updateFormMode() {
            const isFoundReport = typeField.value === 'found';

            foundSection.hidden = !isFoundReport;
            questionField.required = isFoundReport;
            questionField.disabled = !isFoundReport;
            answerField.required = isFoundReport;
            answerField.disabled = !isFoundReport;
            storageField.required = isFoundReport;
            storageField.disabled = !isFoundReport;
        }

        updateFormMode();

        // Storage Image Preview & Validation (Max 2MB)
        const imageInput = document.getElementById('image');
        const imagePreview = document.getElementById('image-preview');
        const imagePlaceholder = document.getElementById('image-placeholder');
        const imageFilename = document.getElementById('image-filename');
        const removeImageBtn = document.getElementById('remove-image-btn');
        const dropzone = document.getElementById('upload-dropzone');

        function handleFileSelection(file) {
            if (!file) return;

            if (file.size > 2 * 1024 * 1024) {
                alert('Ukuran foto melebihi batas maksimal 2 MB.');
                imageInput.value = '';
                return;
            }

            imageFilename.textContent = file.name;
            const reader = new FileReader();
            reader.onload = (e) => {
                imagePreview.src = e.target.result;
                imagePreview.classList.remove('hidden');
                imagePlaceholder.classList.add('hidden');
                removeImageBtn.classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        }

        imageInput.addEventListener('change', (e) => handleFileSelection(e.target.files[0]));

        removeImageBtn.addEventListener('click', () => {
            imageInput.value = '';
            imagePreview.src = '';
            imagePreview.classList.add('hidden');
            imagePlaceholder.classList.remove('hidden');
            removeImageBtn.classList.add('hidden');
            imageFilename.textContent = 'Belum ada berkas dipilih';
        });

        ['dragover', 'dragenter'].forEach((evt) => {
            dropzone.addEventListener(evt, (e) => {
                e.preventDefault();
                dropzone.classList.add('bg-white/75', 'border-[#141923]');
            });
        });

        ['dragleave', 'drop'].forEach((evt) => {
            dropzone.addEventListener(evt, (e) => {
                e.preventDefault();
                dropzone.classList.remove('bg-white/75', 'border-[#141923]');
            });
        });

        dropzone.addEventListener('drop', (e) => {
            const file = e.dataTransfer.files[0];
            if (file) {
                const dt = new DataTransfer();
                dt.items.add(file);
                imageInput.files = dt.files;
                handleFileSelection(file);
            }
        });
    </script>
</x-app-layout>