<x-app-layout>
    <x-slot name="header"><h1 class="text-xl font-semibold text-gray-800">Kelola Kategori</h1></x-slot>
    <main class="mx-auto max-w-5xl space-y-5 px-4 py-8 sm:px-6 lg:px-8">
        @if (session('success'))<div class="rounded-md bg-green-50 p-4 text-sm text-green-800">{{ session('success') }}</div>@endif
        <form method="POST" action="{{ route('campus.admin.categories.store', ['campus' => $currentCampus->slug]) }}" class="grid gap-3 rounded-lg bg-white p-5 sm:grid-cols-4">
            @csrf
            <input name="name" required maxlength="100" placeholder="Nama kategori" class="rounded-md border-gray-300">
            <input name="slug" required maxlength="120" placeholder="slug-kategori" class="rounded-md border-gray-300">
            <input name="icon" maxlength="50" placeholder="Ikon (opsional)" class="rounded-md border-gray-300">
            <button class="rounded-md bg-indigo-600 px-4 py-2 font-semibold text-white">Tambah</button>
        </form>
        <div class="divide-y rounded-lg bg-white p-5">
            @foreach ($categories as $category)
                <form method="POST" action="{{ route('campus.admin.categories.update', ['campus' => $currentCampus->slug, 'category' => $category]) }}" class="grid gap-3 py-3 sm:grid-cols-4">
                    @csrf
                    @method('PUT')
                    <input name="name" value="{{ $category->name }}" required maxlength="100" class="rounded-md border-gray-300">
                    <input name="slug" value="{{ $category->slug }}" required maxlength="120" class="rounded-md border-gray-300">
                    <input name="icon" value="{{ $category->icon }}" maxlength="50" class="rounded-md border-gray-300">
                    <button class="rounded-md border px-3 py-2 text-sm">Simpan · {{ $category->items_count }} laporan</button>
                </form>
            @endforeach
        </div>
        {{ $categories->links() }}
    </main>
</x-app-layout>
