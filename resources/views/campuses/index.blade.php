<x-guest-layout>
    <main class="mx-auto max-w-5xl px-4 py-12">
        <h1 class="text-3xl font-bold text-gray-900">Pilih portal kampus</h1>
        <p class="mt-2 text-gray-600">Buka katalog Lost &amp; Found di kampus yang sesuai.</p>

        <div class="mt-8 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
            @forelse ($campuses as $campus)
                <a href="{{ route('campus.home', ['campus' => $campus->slug]) }}" class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm hover:border-indigo-500">
                    <h2 class="text-lg font-semibold text-gray-900">{{ $campus->name }}</h2>
                    <p class="mt-2 text-sm text-indigo-700">Buka katalog kampus</p>
                </a>
            @empty
                <p class="rounded-lg bg-white p-6 text-gray-600">Belum ada portal kampus yang aktif.</p>
            @endforelse
        </div>

        @if (session('application_submitted'))
            <p class="mt-8 rounded-md bg-green-50 p-4 text-sm text-green-800" role="status">{{ session('application_submitted') }}</p>
        @endif
        @if (session('error'))
            <p class="mt-8 rounded-md bg-red-50 p-4 text-sm text-red-800" role="alert">{{ session('error') }}</p>
        @endif

        <section class="mt-12 max-w-xl rounded-xl border border-gray-200 bg-white p-6">
            <h2 class="text-xl font-semibold">Ajukan portal kampus</h2>
            <p class="mt-2 text-sm text-gray-600">Permohonan ditinjau admin platform. Jika disetujui, tautan pembuatan akun admin dikirim ke email.</p>
            <form method="POST" action="{{ route('campus-applications.store') }}" class="mt-5 space-y-4">
                @csrf
                @if ($errors->any())
                    <div class="rounded-md bg-red-50 p-4 text-sm text-red-800" role="alert">
                        @foreach ($errors->all() as $error)<p>{{ $error }}</p>@endforeach
                    </div>
                @endif
                <label class="block text-sm font-medium">Nama kampus
                    <input name="name" value="{{ old('name') }}" required maxlength="150" class="mt-1 block w-full rounded-md border-gray-300">
                </label>
                <label class="block text-sm font-medium">Email pemohon
                    <input name="email" type="email" value="{{ old('email') }}" required maxlength="255" class="mt-1 block w-full rounded-md border-gray-300">
                </label>
                <button class="rounded-md bg-indigo-600 px-4 py-2 font-semibold text-white">Kirim permohonan</button>
            </form>
        </section>
    </main>
</x-guest-layout>
