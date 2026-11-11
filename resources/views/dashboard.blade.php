<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-5">
                @foreach ([
                    'Total laporan' => $stats['total_items'] ?? 0,
                    'Barang hilang' => $stats['lost_items'] ?? 0,
                    'Barang temuan' => $stats['found_items'] ?? 0,
                    'Klaim menunggu' => $stats['pending_claims'] ?? 0,
                    'Barang kembali' => ($stats['returned_percentage'] ?? 0).'%'
                ] as $label => $value)
                    <div class="rounded-lg bg-white p-5 shadow-sm">
                        <p class="text-sm text-gray-600">{{ $label }}</p>
                        <p class="mt-2 text-2xl font-bold text-gray-900">{{ $value }}</p>
                    </div>
                @endforeach
            </div>

            <nav class="mt-6 flex flex-wrap gap-3">
                <a class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white" href="{{ route('campus.admin.reports', ['campus' => $currentCampus->slug]) }}">Moderasi laporan</a>
                <a class="rounded-md border px-4 py-2 text-sm" href="{{ route('campus.admin.claims', ['campus' => $currentCampus->slug]) }}">Verifikasi klaim</a>
                <a class="rounded-md border px-4 py-2 text-sm" href="{{ route('campus.admin.handovers', ['campus' => $currentCampus->slug]) }}">Serah terima</a>
                <a class="rounded-md border px-4 py-2 text-sm" href="{{ route('campus.admin.categories.index', ['campus' => $currentCampus->slug]) }}">Kategori</a>
                <a class="rounded-md border px-4 py-2 text-sm" href="{{ route('campus.admin.locations.index', ['campus' => $currentCampus->slug]) }}">Lokasi</a>
                @if (auth()->user()->role === 'admin' && filled(config('campus.platform_admin_email')) && strcasecmp(auth()->user()->email, config('campus.platform_admin_email')) === 0)
                    <a class="rounded-md border px-4 py-2 text-sm" href="{{ route('platform.campuses.index') }}">Kelola portal kampus</a>
                @endif
            </nav>

            <div class="mt-8 grid gap-6 lg:grid-cols-2">
                <section class="rounded-lg bg-white p-5 shadow-sm">
                    <h3 class="font-semibold">Laporan terbaru</h3>
                    <div class="mt-3 divide-y">
                        @forelse ($recentReports ?? [] as $item)
                            <p class="py-2 text-sm">{{ $item->title }} <span class="text-gray-500">· {{ $item->status }}</span></p>
                        @empty
                            <p class="py-2 text-sm text-gray-500">Belum ada laporan.</p>
                        @endforelse
                    </div>
                </section>
                <section class="rounded-lg bg-white p-5 shadow-sm">
                    <h3 class="font-semibold">Klaim menunggu</h3>
                    <div class="mt-3 divide-y">
                        @forelse ($recentClaims ?? [] as $claim)
                            <p class="py-2 text-sm">{{ $claim->item?->title }} <span class="text-gray-500">· {{ $claim->user?->name }}</span></p>
                        @empty
                            <p class="py-2 text-sm text-gray-500">Belum ada klaim.</p>
                        @endforelse
                    </div>
                </section>
            </div>
        </div>
    </div>
</x-app-layout>
