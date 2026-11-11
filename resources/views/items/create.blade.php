<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">Buat Laporan Barang</h2>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
            <form method="POST" action="{{ $item ? route('campus.items.update', ['campus' => $currentCampus->slug, 'item' => $item]) : route('campus.reports.store', ['campus' => $currentCampus->slug]) }}" enctype="multipart/form-data" class="space-y-6 rounded-lg bg-white p-6 shadow-sm">
                @csrf
                @if ($item)
                    @method('PUT')
                @endif

                @if ($errors->any())
                    <div class="rounded-md bg-red-50 p-4 text-sm text-red-800" role="alert">
                        <p class="font-semibold">Periksa kembali data laporan:</p>
                        <ul class="mt-2 list-inside list-disc">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700">Jenis laporan</label>
                    <select id="type" name="type" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        <option value="lost" @selected(old('type', $defaultType) === 'lost')>Barang hilang</option>
                        <option value="found" @selected(old('type', $defaultType) === 'found')>Barang ditemukan</option>
                    </select>
                </div>

                <div>
                    <label for="category_id" class="block text-sm font-medium text-gray-700">Kategori</label>
                    <select id="category_id" name="category_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        <option value="">Pilih kategori</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @selected((string) old('category_id') === (string) $category->id)>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700">Nama barang</label>
                    <input id="title" name="title" type="text" value="{{ old('title', $item?->title) }}" maxlength="150" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700">Deskripsi</label>
                    <textarea id="description" name="description" rows="4" maxlength="3000" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">{{ old('description', $item?->description) }}</textarea>
                </div>

                <div>
                    <label for="location" class="block text-sm font-medium text-gray-700">Lokasi hilang/ditemukan</label>
                    <input id="location" name="location" type="text" value="{{ old('location', $item?->location) }}" maxlength="150" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </div>

                <div id="storage-location-field">
                    <label for="storage_location" class="block text-sm font-medium text-gray-700">Lokasi penitipan barang temuan</label>
                    <input id="storage_location" name="storage_location" type="text" value="{{ old('storage_location', $item?->storage_location) }}" maxlength="150" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </div>

                <div>
                    <label for="date_event" class="block text-sm font-medium text-gray-700">Tanggal kejadian</label>
                    <input id="date_event" name="date_event" type="date" value="{{ old('date_event', $item?->date_event?->toDateString()) }}" max="{{ now()->toDateString() }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </div>

                <div>
                    <label for="image" class="block text-sm font-medium text-gray-700">Foto (opsional, maks. 2 MB)</label>
                    <input id="image" name="image" type="file" accept=".jpg,.jpeg,.png,.webp" class="mt-1 block w-full text-sm text-gray-700">
                </div>

                <label class="flex items-start gap-3 text-sm text-gray-700">
                    <input type="checkbox" name="is_sensitive" value="1" @checked(old('is_sensitive', $item?->is_sensitive)) class="mt-1 rounded border-gray-300 text-indigo-600 shadow-sm">
                    <span>Tandai foto/deskripsi sebagai informasi sensitif agar foto tidak ditampilkan di katalog publik.</span>
                </label>

                <div id="security-question-field">
                    <label for="security_question" class="block text-sm font-medium text-gray-700">Pertanyaan verifikasi untuk barang temuan</label>
                    <textarea id="security_question" name="security_question" rows="2" maxlength="500" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">{{ old('security_question', $item?->security_question) }}</textarea>
                    <p class="mt-1 text-sm text-gray-500">Wajib diisi untuk laporan barang ditemukan. Pertanyaan tidak mengungkap jawaban keamanan.</p>
                </div>

                <div id="security-answer-field">
                    <label for="security_answer" class="block text-sm font-medium text-gray-700">Jawaban keamanan (rahasia)</label>
                    <input id="security_answer" name="security_answer" type="password" maxlength="500" autocomplete="new-password" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </div>

                <div class="flex items-center justify-end gap-3">
                    <a href="{{ route('campus.catalog', ['campus' => $currentCampus->slug]) }}" class="rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Batal</a>
                    <button type="submit" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">Kirim laporan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const typeField = document.getElementById('type');
        const questionField = document.getElementById('security_question');
        const questionContainer = document.getElementById('security-question-field');
        const answerField = document.getElementById('security_answer');
        const answerContainer = document.getElementById('security-answer-field');
        const storageField = document.getElementById('storage_location');
        const storageContainer = document.getElementById('storage-location-field');

        function updateSecurityQuestion() {
            const isFoundReport = typeField.value === 'found';
            questionField.required = isFoundReport;
            questionField.disabled = !isFoundReport;
            questionContainer.hidden = !isFoundReport;
            answerField.required = isFoundReport;
            answerField.disabled = !isFoundReport;
            answerContainer.hidden = !isFoundReport;
            storageField.required = isFoundReport;
            storageField.disabled = !isFoundReport;
            storageContainer.hidden = !isFoundReport;
        }

        typeField.addEventListener('change', updateSecurityQuestion);
        updateSecurityQuestion();
    </script>
</x-app-layout>
