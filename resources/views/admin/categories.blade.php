@php
    $campus = $currentCampus ?? request()->route('campus');
@endphp

<x-app-layout>
    <div class="space-y-6 pb-8">
        {{-- Header & Tab Navigasi Admin --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pt-1">
            <div>
                <h1 class="text-2xl sm:text-[28px] font-extrabold tracking-tight text-[#141923]">
                    Kelola Kategori
                </h1>
                <p class="mt-1 text-xs sm:text-sm text-slate-600">
                    Atur kategori barang untuk memudahkan pencarian di feed publik.
                </p>
            </div>

            @include('admin.partials.nav-tabs')
        </div>

        @if (session('success'))
            <div class="rounded-[22px] bg-emerald-500/15 backdrop-blur-xl border border-emerald-400/40 px-4 py-3.5 text-sm font-medium text-emerald-950" role="status">
                {{ session('success') }}
            </div>
        @endif

        {{-- Form Tambah Kategori --}}
        <div class="rounded-[26px] bg-white/40 backdrop-blur-xl border border-white/65 shadow-[0_12px_36px_rgba(20,25,35,0.06)] p-6">
            <h2 class="text-sm font-bold text-[#141923] mb-3">Tambah Kategori Baru</h2>
            <form method="POST" action="{{ route('campus.admin.categories.store', ['campus' => $campus->slug]) }}"
                  class="grid gap-3 sm:grid-cols-4">
                @csrf
                <input name="name" required maxlength="100" placeholder="Nama kategori (mis. Elektronik)"
                       class="rounded-full border border-white/75 bg-white/65 px-4 py-2.5 text-xs sm:text-sm text-slate-900 placeholder-slate-400 focus:bg-white focus:border-[#141923] focus:ring-[#141923]">
                <input name="slug" required maxlength="120" placeholder="slug-kategori"
                       class="rounded-full border border-white/75 bg-white/65 px-4 py-2.5 text-xs sm:text-sm text-slate-900 placeholder-slate-400 focus:bg-white focus:border-[#141923] focus:ring-[#141923]">
                <input name="icon" maxlength="50" placeholder="Ikon (opsional)"
                       class="rounded-full border border-white/75 bg-white/65 px-4 py-2.5 text-xs sm:text-sm text-slate-900 placeholder-slate-400 focus:bg-white focus:border-[#141923] focus:ring-[#141923]">
                <button type="submit"
                        class="rounded-full bg-[#141923] hover:bg-slate-800 px-6 py-2.5 text-xs sm:text-sm font-semibold text-white shadow-sm transition">
                    + Tambah
                </button>
            </form>
        </div>

        {{-- Daftar Kategori --}}
        <div class="rounded-[26px] bg-white/40 backdrop-blur-xl border border-white/65 shadow-[0_12px_36px_rgba(20,25,35,0.06)] p-6 space-y-3">
            @foreach ($categories as $category)
                <form method="POST" action="{{ route('campus.admin.categories.update', ['campus' => $campus->slug, 'category' => $category]) }}"
                      class="grid gap-3 rounded-2xl bg-white/50 border border-white/70 p-3.5 sm:grid-cols-4 items-center">
                    @csrf
                    @method('PUT')
                    <input name="name" value="{{ $category->name }}" required maxlength="100"
                           class="rounded-full border border-white/80 bg-white/80 px-4 py-2 text-xs sm:text-sm font-semibold text-slate-900 focus:border-[#141923] focus:ring-[#141923]">
                    <input name="slug" value="{{ $category->slug }}" required maxlength="120"
                           class="rounded-full border border-white/80 bg-white/80 px-4 py-2 text-xs sm:text-sm text-slate-700 focus:border-[#141923] focus:ring-[#141923]">
                    <input name="icon" value="{{ $category->icon }}" maxlength="50" placeholder="Ikon"
                           class="rounded-full border border-white/80 bg-white/80 px-4 py-2 text-xs sm:text-sm text-slate-700 focus:border-[#141923] focus:ring-[#141923]">
                    <button type="submit"
                            class="rounded-full bg-white/85 hover:bg-[#141923] hover:text-white border border-white px-4 py-2 text-xs font-semibold text-slate-900 transition">
                        Simpan &bull; {{ $category->items_count }} laporan
                    </button>
                </form>
            @endforeach
        </div>

        <div>{{ $categories->links() }}</div>
    </div>
</x-app-layout>