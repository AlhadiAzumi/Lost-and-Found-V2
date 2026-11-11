<x-app-layout>
    <x-slot name="header"><h1 class="text-xl font-semibold text-gray-800">Serah Terima</h1></x-slot>
    <main class="mx-auto max-w-6xl space-y-6 px-4 py-8 sm:px-6 lg:px-8">
        @if (session('success'))
            <div class="rounded-md bg-green-50 p-4 text-sm text-green-800" role="status">{{ session('success') }}</div>
        @endif
        @if ($errors->any())
            <div class="rounded-md bg-red-50 p-4 text-sm text-red-800" role="alert">@foreach ($errors->all() as $error)<p>{{ $error }}</p>@endforeach</div>
        @endif
        <form method="GET" action="{{ route('campus.admin.handovers', ['campus' => $currentCampus->slug]) }}" class="flex gap-2 rounded-lg bg-white p-5 shadow-sm">
            <input name="code" value="{{ $searchCode }}" maxlength="20" required placeholder="Masukkan kode serah terima" class="flex-1 rounded-md border-gray-300">
            <button class="rounded-md bg-indigo-600 px-4 py-2 font-semibold text-white">Cari kode</button>
        </form>

        @if ($claimToHandover)
            <article class="rounded-lg bg-white p-5 shadow-sm">
                <h2 class="font-semibold">{{ $claimToHandover->item?->title }} — {{ $claimToHandover->user?->name }}</h2>
                <p class="mt-1 text-sm text-gray-600">Status klaim: {{ $claimToHandover->status }}</p>
                @if ($claimToHandover->status === 'approved' && ! $claimToHandover->handover)
                    <form method="POST" action="{{ route('campus.admin.handovers.process', ['campus' => $currentCampus->slug, 'claim' => $claimToHandover]) }}" class="mt-4 space-y-3">
                        @csrf
                        <input type="hidden" name="code" value="{{ $searchCode }}">
                        <label class="block text-sm">Catatan (opsional)<textarea name="notes" maxlength="2000" class="mt-1 block w-full rounded-md border-gray-300"></textarea></label>
                        <button class="rounded-md bg-green-700 px-4 py-2 font-semibold text-white">Konfirmasi barang diserahkan</button>
                    </form>
                @else
                    <p class="mt-2 text-sm">Kode tidak valid, belum disetujui, atau sudah digunakan.</p>
                @endif
            </article>
        @elseif ($searchCode !== '')
            <p class="rounded-lg bg-amber-50 p-4 text-sm text-amber-800">Kode tidak ditemukan.</p>
        @endif

        <section class="rounded-lg bg-white p-5 shadow-sm">
            <h2 class="mb-4 font-semibold">Riwayat serah terima</h2>
            <div class="divide-y">
                @forelse ($handovers as $handover)
                    <div class="py-3 text-sm">
                        {{ $handover->item?->title }} diserahkan kepada {{ $handover->recipient?->name }}
                        oleh {{ $handover->officer?->name }} pada {{ $handover->handover_date?->format('d M Y H:i') }}
                    </div>
                @empty
                    <p class="text-sm text-gray-600">Belum ada serah terima.</p>
                @endforelse
            </div>
            <div class="mt-3">{{ $handovers->links() }}</div>
        </section>
    </main>
</x-app-layout>
