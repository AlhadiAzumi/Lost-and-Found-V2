<x-app-layout>
    <x-slot name="header">
        <h1 class="text-xl font-semibold text-gray-800">{{ $item->title }}</h1>
    </x-slot>

    <main class="mx-auto max-w-3xl space-y-5 px-4 py-8 sm:px-6 lg:px-8">
        @if (session('success'))
            <div class="rounded-md bg-green-50 p-4 text-sm text-green-800" role="status">{{ session('success') }}</div>
        @endif

        <article class="space-y-4 rounded-lg bg-white p-6 shadow-sm">
            <div class="flex flex-wrap gap-3 text-sm text-gray-600">
                <span>{{ $item->type === 'lost' ? 'Barang hilang' : 'Barang ditemukan' }}</span>
                <span>{{ $item->category?->name }}</span>
                <span>{{ $item->date_event?->format('d M Y') }}</span>
                <span>{{ $item->location }}</span>
            </div>

            @if ($item->image && ! $item->is_sensitive)
                <img src="{{ $item->photo_url }}" alt="Foto {{ $item->title }}" class="max-h-96 w-full rounded-md object-contain">
            @endif

            <p class="whitespace-pre-line text-gray-700">{{ $item->description }}</p>
            @if ($item->type === 'found' && $item->security_question)
                <div class="rounded-md bg-amber-50 p-4">
                    <p class="text-sm font-semibold text-gray-900">Pertanyaan verifikasi</p>
                    <p class="mt-1 text-gray-700">{{ $item->security_question }}</p>
                </div>
            @endif

            @if (auth()->id() === $item->user_id && $item->rejection_reason)
                <p class="rounded-md bg-red-50 p-4 text-sm text-red-800">Alasan penolakan: {{ $item->rejection_reason }}</p>
            @endif

            <div class="flex flex-wrap gap-3">
                @auth
                    @if ($item->type === 'found' && $item->status === 'approved' && auth()->id() !== $item->user_id)
                        <a href="{{ route('campus.claims.create', ['campus' => $currentCampus->slug, 'item' => $item]) }}" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white">Ajukan klaim</a>
                    @endif
                    @can('update', $item)
                        <a href="{{ route('campus.items.edit', ['campus' => $currentCampus->slug, 'item' => $item]) }}" class="rounded-md border px-4 py-2 text-sm">Ubah laporan</a>
                    @endcan
                    @can('close', $item)
                        <form method="POST" action="{{ route('campus.items.close', ['campus' => $currentCampus->slug, 'item' => $item]) }}">
                            @csrf
                            <button class="rounded-md border border-red-300 px-4 py-2 text-sm text-red-700" type="submit">Tutup laporan</button>
                        </form>
                    @endcan
                @endauth
                <a href="{{ route('campus.catalog', ['campus' => $currentCampus->slug]) }}" class="rounded-md border px-4 py-2 text-sm">Kembali ke katalog</a>
            </div>
        </article>
    </main>
</x-app-layout>
