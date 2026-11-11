<x-app-layout>
    <x-slot name="header">
        <h1 class="text-xl font-semibold text-gray-800">Ajukan Klaim Barang</h1>
    </x-slot>

    <main class="mx-auto max-w-2xl space-y-5 px-4 py-8 sm:px-6 lg:px-8">
        <div class="rounded-lg bg-white p-6 shadow-sm">
            <h2 class="text-lg font-semibold">{{ $item->title }}</h2>
            <p class="mt-3 text-gray-700">{{ $item->security_question }}</p>
        </div>

        <form method="POST" action="{{ route('campus.claims.store', ['campus' => $currentCampus->slug, 'item' => $item]) }}" enctype="multipart/form-data" class="space-y-5 rounded-lg bg-white p-6 shadow-sm">
            @csrf
            @if ($errors->any())
                <div class="rounded-md bg-red-50 p-4 text-sm text-red-800" role="alert">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif
            <div>
                <label for="security_answer" class="block text-sm font-medium text-gray-700">Jawaban verifikasi</label>
                <input id="security_answer" name="security_answer" type="text" maxlength="500" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
            </div>
            <div>
                <label for="proof_image" class="block text-sm font-medium text-gray-700">Bukti kepemilikan (JPG, PNG, WebP, PDF; maks. 2 MB)</label>
                <input id="proof_image" name="proof_image" type="file" accept=".jpg,.jpeg,.png,.webp,.pdf" class="mt-1 block w-full text-sm">
            </div>
            <button type="submit" class="rounded-md bg-indigo-600 px-4 py-2 font-semibold text-white">Kirim klaim</button>
        </form>
    </main>
</x-app-layout>
