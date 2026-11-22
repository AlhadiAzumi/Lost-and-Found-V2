@php
    $campus = $currentCampus ?? request()->route('campus');
@endphp

<x-app-layout>
    <div class="space-y-6 pb-8">
        {{-- Header & Tab Navigasi Admin --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pt-1">
            <div>
                <h1 class="text-2xl sm:text-[28px] font-extrabold tracking-tight text-[#141923]">
                    Kelola Lokasi Kampus
                </h1>
                <p class="mt-1 text-xs sm:text-sm text-slate-600">
                    Daftar gedung dan area di {{ $campus->name }} untuk pilihan lokasi laporan.
                </p>
            </div>

            @include('admin.partials.nav-tabs')
        </div>

        @if (session('success'))
            <div class="rounded-[22px] bg-emerald-500/15 backdrop-blur-xl border border-emerald-400/40 px-4 py-3.5 text-sm font-medium text-emerald-950" role="status">
                {{ session('success') }}
            </div>
        @endif

        {{-- Form Tambah Lokasi --}}
        <div class="rounded-[26px] bg-white/40 backdrop-blur-xl border border-white/65 shadow-[0_12px_36px_rgba(20,25,35,0.06)] p-6">
            <h2 class="text-sm font-bold text-[#141923] mb-3">Tambah Lokasi Baru</h2>
            <form method="POST" action="{{ route('campus.admin.locations.store', ['campus' => $campus->slug]) }}"
                  class="flex flex-col sm:flex-row gap-3">
                @csrf
                <input name="name" required maxlength="150" placeholder="Nama lokasi kampus (mis. Gedung Utama Lt. 1)"
                       class="flex-1 rounded-full border border-white/75 bg-white/65 px-5 py-2.5 text-xs sm:text-sm text-slate-900 placeholder-slate-400 focus:bg-white focus:border-[#141923] focus:ring-[#141923]">
                <button type="submit"
                        class="rounded-full bg-[#141923] hover:bg-slate-800 px-7 py-2.5 text-xs sm:text-sm font-semibold text-white shadow-sm transition">
                    + Tambah
                </button>
            </form>
        </div>

        {{-- Daftar Lokasi --}}
        <div class="rounded-[26px] bg-white/40 backdrop-blur-xl border border-white/65 shadow-[0_12px_36px_rgba(20,25,35,0.06)] p-6 space-y-3">
            @foreach ($locations as $location)
                <form method="POST" action="{{ route('campus.admin.locations.update', ['campus' => $campus->slug, 'location' => $location]) }}"
                      class="flex flex-col sm:flex-row gap-3 rounded-2xl bg-white/50 border border-white/70 p-3.5 items-center">
                    @csrf
                    @method('PUT')
                    <input name="name" value="{{ $location->name }}" required maxlength="150"
                           class="w-full sm:flex-1 rounded-full border border-white/80 bg-white/80 px-4 py-2 text-xs sm:text-sm font-semibold text-slate-900 focus:border-[#141923] focus:ring-[#141923]">
                    <button type="submit"
                            class="w-full sm:w-auto rounded-full bg-white/85 hover:bg-[#141923] hover:text-white border border-white px-5 py-2 text-xs font-semibold text-slate-900 transition">
                        Simpan &bull; {{ $location->items_count }} laporan
                    </button>
                </form>
            @endforeach
        </div>

        <div>{{ $locations->links() }}</div>
    </div>
</x-app-layout>