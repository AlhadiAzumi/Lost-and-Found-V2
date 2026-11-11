<x-app-layout>
    <x-slot name="header"><h1 class="text-xl font-semibold text-gray-800">Kelola Portal Kampus</h1></x-slot>
    <main class="mx-auto max-w-6xl space-y-8 px-4 py-8 sm:px-6 lg:px-8">
        @foreach (['success' => 'bg-green-50 text-green-800', 'error' => 'bg-red-50 text-red-800'] as $key => $styles)
            @if (session($key))
                <div class="rounded-md p-4 text-sm {{ $styles }}" role="status">{{ session($key) }}</div>
            @endif
        @endforeach
        @if ($errors->any())
            <div class="rounded-md bg-red-50 p-4 text-sm text-red-800" role="alert">@foreach ($errors->all() as $error)<p>{{ $error }}</p>@endforeach</div>
        @endif

        <section class="rounded-lg bg-white p-5 shadow-sm">
            <h2 class="font-semibold">Buat portal secara langsung</h2>
            <form method="POST" action="{{ route('platform.campuses.store') }}" class="mt-4 grid gap-3 sm:grid-cols-3">
                @csrf
                <input name="name" required maxlength="150" placeholder="Nama kampus" class="rounded-md border-gray-300">
                <input name="slug" required maxlength="100" placeholder="slug-kampus" class="rounded-md border-gray-300">
                <button class="rounded-md bg-indigo-600 px-4 py-2 font-semibold text-white">Buat portal</button>
            </form>
        </section>

        <section class="rounded-lg bg-white p-5 shadow-sm">
            <h2 class="font-semibold">Permohonan portal menunggu tinjauan</h2>
            <div class="mt-3 divide-y">
                @forelse ($pendingApplications as $application)
                    <div class="flex flex-wrap items-center justify-between gap-3 py-3">
                        <div>
                            <p class="font-medium">{{ $application->name }}</p>
                            <p class="text-sm text-gray-600">{{ $application->email }} · /kampus/{{ $application->slug }}</p>
                        </div>
                        <div class="flex gap-2">
                            <form method="POST" action="{{ route('platform.campuses.applications.approve', ['application' => $application]) }}">
                                @csrf
                                <button class="rounded-md bg-green-700 px-3 py-2 text-sm font-semibold text-white">Setujui</button>
                            </form>
                            <form method="POST" action="{{ route('platform.campuses.applications.reject', ['application' => $application]) }}">
                                @csrf
                                <button class="rounded-md bg-red-700 px-3 py-2 text-sm font-semibold text-white">Tolak</button>
                            </form>
                        </div>
                    </div>
                @empty
                    <p class="py-3 text-sm text-gray-600">Tidak ada permohonan tertunda.</p>
                @endforelse
            </div>
        </section>

        <section class="rounded-lg bg-white p-5 shadow-sm">
            <h2 class="font-semibold">Undangan admin belum digunakan</h2>
            <div class="mt-3 divide-y">
                @forelse ($awaitingAdminSetup as $application)
                    <div class="flex flex-wrap items-center justify-between gap-3 py-3">
                        <p>{{ $application->name }} · {{ $application->email }}</p>
                        <form method="POST" action="{{ route('platform.campuses.applications.resend', ['application' => $application]) }}">
                            @csrf
                            <button class="rounded-md border px-3 py-2 text-sm">Kirim ulang undangan</button>
                        </form>
                    </div>
                @empty
                    <p class="py-3 text-sm text-gray-600">Tidak ada undangan tertunda.</p>
                @endforelse
            </div>
        </section>

        <section class="rounded-lg bg-white p-5 shadow-sm">
            <h2 class="font-semibold">Portal aktif dan nonaktif</h2>
            <div class="mt-3 divide-y">
                @foreach ($campuses as $campus)
                    <div class="flex flex-wrap items-center justify-between gap-3 py-3">
                        <div>
                            <p class="font-medium">{{ $campus->name }}</p>
                            <p class="text-sm text-gray-600">/kampus/{{ $campus->slug }} · {{ $campus->users_count }} akun</p>
                        </div>
                        <form method="POST" action="{{ route('platform.campuses.status', ['campus' => $campus]) }}">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="is_active" value="{{ $campus->is_active ? 0 : 1 }}">
                            <button class="rounded-md border px-3 py-2 text-sm">{{ $campus->is_active ? 'Nonaktifkan' : 'Aktifkan' }}</button>
                        </form>
                    </div>
                @endforeach
            </div>
            {{ $campuses->links() }}
        </section>
    </main>
</x-app-layout>
