<x-app-layout>
    <x-slot name="header">
        <h1 class="text-xl font-semibold text-gray-800">Riwayat Saya</h1>
    </x-slot>

    <main class="mx-auto max-w-6xl space-y-8 px-4 py-8 sm:px-6 lg:px-8">
        @if (session('success'))
            <div class="rounded-md bg-green-50 p-4 text-sm text-green-800" role="status">{{ session('success') }}</div>
        @endif
        <section>
            <h2 class="mb-4 text-lg font-semibold">Laporan saya</h2>
            <div class="divide-y rounded-lg bg-white shadow-sm">
                @forelse ($reports as $report)
                    <a href="{{ route('campus.items.show', ['campus' => $currentCampus->slug, 'item' => $report]) }}" class="block p-4 hover:bg-gray-50">
                        <span class="font-medium">{{ $report->title }}</span>
                        <span class="ms-2 text-sm text-gray-600">{{ $report->status }}</span>
                        @if ($report->rejection_reason)
                            <p class="mt-1 text-sm text-red-700">{{ $report->rejection_reason }}</p>
                        @endif
                    </a>
                @empty
                    <p class="p-4 text-gray-600">Belum ada laporan.</p>
                @endforelse
            </div>
            <div class="mt-3">{{ $reports->links() }}</div>
        </section>

        <section>
            <h2 class="mb-4 text-lg font-semibold">Klaim saya</h2>
            <div class="divide-y rounded-lg bg-white shadow-sm">
                @forelse ($claims as $claim)
                    <article class="space-y-1 p-4">
                        <h3 class="font-medium">{{ $claim->item?->title ?? 'Barang tidak tersedia' }}</h3>
                        <p class="text-sm text-gray-600">Status: {{ $claim->status }}</p>
                        @if ($claim->admin_note)
                            <p class="text-sm text-red-700">Catatan petugas: {{ $claim->admin_note }}</p>
                        @endif
                        @if ($claim->status === 'approved' && $claim->handover_code)
                            <p class="font-mono font-semibold">Kode serah terima: {{ $claim->handover_code }}</p>
                        @endif
                    </article>
                @empty
                    <p class="p-4 text-gray-600">Belum ada klaim.</p>
                @endforelse
            </div>
            <div class="mt-3">{{ $claims->links() }}</div>
        </section>
    </main>
</x-app-layout>
