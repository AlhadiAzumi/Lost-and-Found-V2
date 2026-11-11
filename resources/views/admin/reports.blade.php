<x-app-layout>
    <x-slot name="header"><h1 class="text-xl font-semibold text-gray-800">Moderasi Laporan</h1></x-slot>
    <main class="mx-auto max-w-7xl space-y-4 px-4 py-8 sm:px-6 lg:px-8">
        @if (session('success'))
            <div class="rounded-md bg-green-50 p-4 text-sm text-green-800" role="status">{{ session('success') }}</div>
        @endif
        <div class="space-y-4">
            @forelse ($items as $item)
                <article class="rounded-lg bg-white p-5 shadow-sm">
                    <div class="flex flex-wrap justify-between gap-3">
                        <div>
                            <h2 class="font-semibold">{{ $item->title }}</h2>
                            <p class="text-sm text-gray-600">{{ $item->type }} · {{ $item->category?->name }} · {{ $item->location }}</p>
                            <p class="mt-2 text-sm">{{ $item->description }}</p>
                            @if ($item->type === 'found')
                                <p class="mt-2 text-sm">Pertanyaan: {{ $item->security_question }}</p>
                            @endif
                        </div>
                        <span class="text-sm text-gray-600">Status: {{ $item->status }} · Pelapor: {{ $item->user?->name }}</span>
                    </div>
                    @if ($item->status === 'pending')
                        <div class="mt-4 flex flex-wrap gap-3">
                            <form method="POST" action="{{ route('campus.admin.reports.status', ['campus' => $currentCampus->slug, 'item' => $item]) }}">
                                @csrf
                                <input type="hidden" name="status" value="approved">
                                <button class="rounded-md bg-green-700 px-4 py-2 text-sm font-semibold text-white">Setujui</button>
                            </form>
                            <form method="POST" action="{{ route('campus.admin.reports.status', ['campus' => $currentCampus->slug, 'item' => $item]) }}" class="flex flex-wrap gap-2">
                                @csrf
                                <input type="hidden" name="status" value="rejected">
                                <input name="rejection_reason" required maxlength="2000" placeholder="Alasan penolakan" class="rounded-md border-gray-300 text-sm">
                                <button class="rounded-md bg-red-700 px-4 py-2 text-sm font-semibold text-white">Tolak</button>
                            </form>
                        </div>
                    @endif
                </article>
            @empty
                <p class="rounded-lg bg-white p-6 text-gray-600">Belum ada laporan.</p>
            @endforelse
        </div>
        {{ $items->links() }}
    </main>
</x-app-layout>
