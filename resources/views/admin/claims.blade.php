<x-app-layout>
    <x-slot name="header"><h1 class="text-xl font-semibold text-gray-800">Verifikasi Klaim</h1></x-slot>
    <main class="mx-auto max-w-7xl space-y-4 px-4 py-8 sm:px-6 lg:px-8">
        @if (session('success'))
            <div class="rounded-md bg-green-50 p-4 text-sm text-green-800" role="status">{{ session('success') }}</div>
        @endif
        @if ($errors->any())
            <div class="rounded-md bg-red-50 p-4 text-sm text-red-800" role="alert">@foreach ($errors->all() as $error)<p>{{ $error }}</p>@endforeach</div>
        @endif
        @forelse ($claims as $claim)
            <article class="space-y-3 rounded-lg bg-white p-5 shadow-sm">
                <h2 class="font-semibold">{{ $claim->item?->title }} — {{ $claim->user?->name }}</h2>
                <p class="text-sm">Status: {{ $claim->status }}</p>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="rounded-md bg-gray-50 p-4">
                        <p class="text-sm font-semibold">Jawaban pengklaim</p>
                        <p>{{ $claim->security_answer }}</p>
                    </div>
                    <div class="rounded-md bg-gray-50 p-4">
                        <p class="text-sm font-semibold">Hasil pencocokan pertanyaan rahasia</p>
                        <p>{{ $claim->securityAnswerMatches() === true ? 'Cocok' : ($claim->securityAnswerMatches() === false ? 'Tidak cocok' : 'Belum ada jawaban referensi') }}</p>
                    </div>
                </div>
                @if ($claim->proof_image)
                    <a class="text-sm text-indigo-700 underline" href="{{ route('campus.claims.proof', ['campus' => $currentCampus->slug, 'claim' => $claim]) }}">Lihat bukti kepemilikan (privat)</a>
                @endif
                @if ($claim->status === 'pending')
                    <div class="flex flex-wrap gap-3">
                        <form method="POST" action="{{ route('campus.admin.claims.verify', ['campus' => $currentCampus->slug, 'claim' => $claim]) }}">
                            @csrf
                            <input type="hidden" name="action" value="approve">
                            <button class="rounded-md bg-green-700 px-4 py-2 text-sm font-semibold text-white">Setujui dan terbitkan kode</button>
                        </form>
                        <form method="POST" action="{{ route('campus.admin.claims.verify', ['campus' => $currentCampus->slug, 'claim' => $claim]) }}" class="flex flex-wrap gap-2">
                            @csrf
                            <input type="hidden" name="action" value="reject">
                            <input name="admin_note" required maxlength="2000" placeholder="Alasan penolakan" class="rounded-md border-gray-300 text-sm">
                            <button class="rounded-md bg-red-700 px-4 py-2 text-sm font-semibold text-white">Tolak</button>
                        </form>
                    </div>
                @elseif ($claim->handover_code)
                    <p class="font-mono font-semibold">Kode serah terima: {{ $claim->handover_code }}</p>
                @endif
            </article>
        @empty
            <p class="rounded-lg bg-white p-6 text-gray-600">Belum ada klaim.</p>
        @endforelse
        {{ $claims->links() }}
    </main>
</x-app-layout>
