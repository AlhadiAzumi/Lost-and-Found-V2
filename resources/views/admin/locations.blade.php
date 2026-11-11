<x-app-layout>
    <x-slot name="header"><h1 class="text-xl font-semibold text-gray-800">Kelola Lokasi</h1></x-slot>
    <main class="mx-auto max-w-4xl space-y-5 px-4 py-8 sm:px-6 lg:px-8">
        @if (session('success'))<div class="rounded-md bg-green-50 p-4 text-sm text-green-800">{{ session('success') }}</div>@endif
        <form method="POST" action="{{ route('campus.admin.locations.store', ['campus' => $currentCampus->slug]) }}" class="flex gap-3 rounded-lg bg-white p-5">
            @csrf
            <input name="name" required maxlength="150" placeholder="Nama lokasi kampus" class="flex-1 rounded-md border-gray-300">
            <button class="rounded-md bg-indigo-600 px-4 py-2 font-semibold text-white">Tambah</button>
        </form>
        <div class="divide-y rounded-lg bg-white p-5">
            @foreach ($locations as $location)
                <form method="POST" action="{{ route('campus.admin.locations.update', ['campus' => $currentCampus->slug, 'location' => $location]) }}" class="flex gap-3 py-3">
                    @csrf
                    @method('PUT')
                    <input name="name" value="{{ $location->name }}" required maxlength="150" class="flex-1 rounded-md border-gray-300">
                    <button class="rounded-md border px-3 py-2 text-sm">Simpan · {{ $location->items_count }} laporan</button>
                </form>
            @endforeach
        </div>
        {{ $locations->links() }}
    </main>
</x-app-layout>
