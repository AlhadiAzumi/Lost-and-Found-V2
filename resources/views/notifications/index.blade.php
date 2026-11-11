<x-app-layout>
    <x-slot name="header">
        <h1 class="text-xl font-semibold text-gray-800">Notifikasi</h1>
    </x-slot>

    <main class="mx-auto max-w-4xl space-y-4 px-4 py-8 sm:px-6 lg:px-8">
        <form method="POST" action="{{ route('campus.notifications.read-all', ['campus' => $currentCampus->slug]) }}">
            @csrf
            <button class="rounded-md border px-4 py-2 text-sm">Tandai semua sudah dibaca</button>
        </form>
        <div class="divide-y rounded-lg bg-white shadow-sm">
            @forelse ($notifications as $notification)
                <article class="flex items-start justify-between gap-4 p-5">
                    <div>
                        <h2 class="font-semibold">{{ $notification->title }}</h2>
                        <p class="mt-1 text-sm text-gray-700">{{ $notification->message }}</p>
                    </div>
                    @if (! $notification->read_at)
                        <form method="POST" action="{{ route('campus.notifications.read', ['campus' => $currentCampus->slug, 'notification' => $notification]) }}">
                            @csrf
                            <button class="text-sm text-indigo-700">Tandai dibaca</button>
                        </form>
                    @endif
                </article>
            @empty
                <p class="p-5 text-gray-600">Belum ada notifikasi.</p>
            @endforelse
        </div>
        {{ $notifications->links() }}
    </main>
</x-app-layout>
