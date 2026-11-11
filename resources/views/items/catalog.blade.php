<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Katalog Lost &amp; Found</h2>
            @auth
                <div class="flex gap-2">
                    <a href="{{ route('campus.reports.lost.create', ['campus' => $currentCampus->slug]) }}" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">
                        Lapor barang hilang
                    </a>
                    <a href="{{ route('campus.reports.found.create', ['campus' => $currentCampus->slug]) }}" class="rounded-md bg-green-700 px-4 py-2 text-sm font-semibold text-white hover:bg-green-800">
                        Lapor barang temuan
                    </a>
                </div>
            @endauth
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="rounded-md bg-green-50 p-4 text-sm text-green-800" role="status">
                    {{ session('status') }}
                </div>
            @endif

            <form method="GET" action="{{ route('catalog') }}" class="grid gap-4 rounded-lg bg-white p-6 shadow-sm sm:grid-cols-2 lg:grid-cols-5">
                <label class="block text-sm font-medium text-gray-700">
                    Kata kunci
                    <input type="search" name="search" value="{{ $filters['search'] ?? $filters['q'] ?? '' }}" maxlength="100" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </label>

                <label class="block text-sm font-medium text-gray-700">
                    Jenis laporan
                    <select name="type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        <option value="">Semua</option>
                        <option value="lost" @selected(($filters['type'] ?? '') === 'lost')>Hilang</option>
                        <option value="found" @selected(($filters['type'] ?? '') === 'found')>Ditemukan</option>
                    </select>
                </label>

                <label class="block text-sm font-medium text-gray-700">
                    Kategori
                    <select name="category_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        <option value="">Semua kategori</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @selected((string) ($filters['category_id'] ?? '') === (string) $category->id)>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </label>

                <label class="block text-sm font-medium text-gray-700">
                    Lokasi
                    <select name="location" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        <option value="">Semua lokasi</option>
                        @foreach ($locations as $location)
                            <option value="{{ $location->name }}" @selected(($filters['location'] ?? '') === $location->name)>{{ $location->name }}</option>
                        @endforeach
                    </select>
                </label>

                <label class="block text-sm font-medium text-gray-700">
                    Dari tanggal
                    <input type="date" name="from" value="{{ $filters['from'] ?? '' }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </label>

                <label class="block text-sm font-medium text-gray-700">
                    Sampai tanggal
                    <input type="date" name="to" value="{{ $filters['to'] ?? '' }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </label>

                <div class="flex items-end">
                    <button type="submit" class="w-full rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">
                        Cari
                    </button>
                </div>
            </form>

            @if ($items->isEmpty())
                <div class="rounded-lg bg-white p-8 text-center text-gray-600 shadow-sm">
                    Belum ada laporan yang cocok dengan pencarian.
                </div>
            @else
                <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($items as $item)
                        <article class="overflow-hidden rounded-lg bg-white shadow-sm">
                            @if ($item->image && ! $item->is_sensitive)
                                <img src="{{ asset('storage/'.$item->image) }}" alt="Foto {{ $item->title }}" class="h-48 w-full object-cover">
                            @endif
                            <div class="space-y-2 p-5">
                                <div class="flex items-center justify-between gap-3">
                                    <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $item->type === 'lost' ? 'bg-amber-100 text-amber-800' : 'bg-green-100 text-green-800' }}">
                                        {{ $item->type === 'lost' ? 'Hilang' : 'Ditemukan' }}
                                    </span>
                                    <span class="text-xs text-gray-500">{{ $item->date_event->format('d M Y') }}</span>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900">{{ $item->title }}</h3>
                                <p class="text-sm text-gray-600">{{ $item->category?->name ?? 'Tanpa kategori' }}</p>
                                <p class="text-sm text-gray-600">Lokasi: {{ $item->location }}</p>
                            </div>
                        </article>
                    @endforeach
                </div>

                <div>{{ $items->links() }}</div>
            @endif
        </div>
    </div>
</x-app-layout>
