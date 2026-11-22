@php
    $campus = $currentCampus ?? request()->route('campus');
    $catalogAction = $campus ? route('campus.home', ['campus' => $campus->slug]) : route('catalog');
    $activeType = $filters['type'] ?? '';
@endphp

<x-app-layout>
    <div class="space-y-6">
        {{-- Top Header: Judul Feed Publik + Search Pill --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pt-1">
            <div>
                <h1 class="text-2xl sm:text-[28px] font-extrabold tracking-tight text-[#141923]">
                    Feed Publik
                </h1>
            </div>

            <form method="GET" action="{{ $catalogAction }}" class="w-full sm:w-80">
                @if ($activeType !== '')
                    <input type="hidden" name="type" value="{{ $activeType }}">
                @endif
                @if (!empty($filters['category_id']))
                    <input type="hidden" name="category_id" value="{{ $filters['category_id'] }}">
                @endif
                @if (!empty($filters['location']))
                    <input type="hidden" name="location" value="{{ $filters['location'] }}">
                @endif
                @if (!empty($filters['from']))
                    <input type="hidden" name="from" value="{{ $filters['from'] }}">
                @endif
                @if (!empty($filters['to']))
                    <input type="hidden" name="to" value="{{ $filters['to'] }}">
                @endif

                <div class="relative">
                    <input type="search" name="search"
                           value="{{ $filters['search'] ?? $filters['q'] ?? '' }}" maxlength="100"
                           placeholder="Cari barang..."
                           class="w-full rounded-full border border-white/65 bg-white/45 backdrop-blur-xl pl-5 pr-11 py-2.5 text-sm text-slate-900 placeholder-slate-500 shadow-[0_8px_24px_rgba(20,25,35,0.04)] focus:bg-white/70 focus:border-[#141923] focus:ring-[#141923] transition">
                    <button type="submit" aria-label="Cari"
                            class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-500 hover:text-slate-800">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z"/>
                        </svg>
                    </button>
                </div>
            </form>
        </div>

        @if (session('status') || session('success'))
            <div class="rounded-2xl bg-emerald-500/15 backdrop-blur-xl border border-emerald-400/40 px-4 py-3 text-sm font-medium text-emerald-950" role="status">
                {{ session('status') ?? session('success') }}
            </div>
        @endif

        {{-- Grid Utama: Panel Filter Kiri + Grid Kartu Barang Kanan --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

            {{-- Kartu Filter Glass --}}
            <aside class="lg:col-span-3 rounded-[26px] bg-white/40 backdrop-blur-xl border border-white/65 shadow-[0_12px_36px_rgba(20,25,35,0.06)] p-5">
                <h2 class="text-base font-bold text-[#141923] mb-4">Filter</h2>

                <form method="GET" action="{{ $catalogAction }}" class="space-y-4">
                    @if (!empty($filters['search'] ?? $filters['q']))
                        <input type="hidden" name="search" value="{{ $filters['search'] ?? $filters['q'] }}">
                    @endif

                    {{-- Status Pill Toggle (Hilang / Ditemukan) --}}
                    <div>
                        <span class="block text-xs font-semibold text-slate-700 mb-2">Status</span>
                        <div class="flex flex-wrap gap-2">
                            <label class="cursor-pointer">
                                <input type="radio" name="type" value="lost" class="peer sr-only" @checked($activeType === 'lost')>
                                <span class="inline-block rounded-full px-3.5 py-1 text-xs font-semibold transition bg-rose-100/85 text-rose-700 backdrop-blur-sm peer-checked:ring-2 peer-checked:ring-rose-500">
                                    Hilang
                                </span>
                            </label>

                            <label class="cursor-pointer">
                                <input type="radio" name="type" value="found" class="peer sr-only" @checked($activeType === 'found')>
                                <span class="inline-block rounded-full px-3.5 py-1 text-xs font-semibold transition bg-emerald-100/85 text-emerald-800 backdrop-blur-sm peer-checked:ring-2 peer-checked:ring-emerald-600">
                                    Ditemukan
                                </span>
                            </label>

                            @if ($activeType !== '')
                                <label class="cursor-pointer">
                                    <input type="radio" name="type" value="" class="peer sr-only">
                                    <span class="inline-block rounded-full px-3 py-1 text-xs font-medium bg-white/60 text-slate-700 hover:bg-white/80">
                                        Semua
                                    </span>
                                </label>
                            @endif
                        </div>
                    </div>

                    {{-- Kategori --}}
                    <div>
                        <label for="filter-category" class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Kategori
                        </label>
                        <select id="filter-category" name="category_id"
                                class="w-full rounded-full border border-white/70 bg-white/55 backdrop-blur-md px-4 py-2 text-xs font-medium text-slate-800 focus:bg-white/80 focus:border-[#141923] focus:ring-[#141923]">
                            <option value="">Semua kategori</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" @selected((string) ($filters['category_id'] ?? '') === (string) $category->id)>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Rentang Tanggal --}}
                    <div>
                        <span class="block text-xs font-semibold text-slate-700 mb-1.5">Rentang tanggal</span>
                        <div class="space-y-2">
                            <input type="date" name="from" value="{{ $filters['from'] ?? '' }}" aria-label="Dari tanggal"
                                   class="w-full rounded-full border border-white/70 bg-white/55 backdrop-blur-md px-4 py-2 text-xs font-medium text-slate-800 focus:bg-white/80 focus:border-[#141923] focus:ring-[#141923]">
                            <input type="date" name="to" value="{{ $filters['to'] ?? '' }}" aria-label="Sampai tanggal"
                                   class="w-full rounded-full border border-white/70 bg-white/55 backdrop-blur-md px-4 py-2 text-xs font-medium text-slate-800 focus:bg-white/80 focus:border-[#141923] focus:ring-[#141923]">
                        </div>
                    </div>

                    {{-- Lokasi --}}
                    <div>
                        <label for="filter-location" class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Lokasi
                        </label>
                        <select id="filter-location" name="location"
                                class="w-full rounded-full border border-white/70 bg-white/55 backdrop-blur-md px-4 py-2 text-xs font-medium text-slate-800 focus:bg-white/80 focus:border-[#141923] focus:ring-[#141923]">
                            <option value="">Semua lokasi</option>
                            @foreach ($locations as $location)
                                <option value="{{ $location->name }}" @selected(($filters['location'] ?? '') === $location->name)>
                                    {{ $location->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="pt-1 space-y-2">
                        <button type="submit"
                                class="w-full rounded-full bg-[#141923] py-2.5 px-4 text-xs font-semibold text-white shadow-sm hover:bg-slate-800 transition">
                            Terapkan
                        </button>

                        @if (request()->hasAny(['search', 'q', 'type', 'category_id', 'location', 'from', 'to']))
                            <a href="{{ $catalogAction }}"
                               class="block w-full text-center rounded-full bg-white/50 hover:bg-white/75 border border-white/60 py-2 px-4 text-xs font-semibold text-slate-700 transition">
                                Reset Filter
                            </a>
                        @endif
                    </div>
                </form>
            </aside>

            {{-- Grid Kartu Barang Glass (3 Kolom) --}}
            <section class="lg:col-span-9">
                @if ($items->isEmpty())
                    <div class="rounded-[26px] bg-white/40 backdrop-blur-xl border border-white/65 p-12 text-center shadow-[0_12px_36px_rgba(20,25,35,0.06)]">
                        <p class="text-base font-bold text-slate-900">Belum ada laporan barang</p>
                        <p class="mt-1 text-xs text-slate-600">
                            Coba ubah filter atau kata kunci pencarian.
                        </p>
                    </div>
                @else
                    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-5">
                        @foreach ($items as $item)
                            @php
                                $itemUrl = $campus
                                    ? route('campus.items.show', ['campus' => $campus->slug, 'item' => $item])
                                    : route('items.show', $item);
                            @endphp
                            <a href="{{ $itemUrl }}"
                               class="group block rounded-[24px] bg-white/35 hover:bg-white/55 backdrop-blur-xl border border-white/65 hover:border-white/90 p-3.5 shadow-[0_12px_32px_rgba(20,25,35,0.06)] hover:shadow-[0_16px_38px_rgba(20,25,35,0.1)] hover:-translate-y-0.5 transition duration-200">

                                {{-- Thumbnail Gambar + Badge Status --}}
                                <div class="relative h-36 w-full rounded-[18px] overflow-hidden bg-gradient-to-br from-[#B8D1BA]/70 to-[#7FA985]/70 border border-white/40">
                                    @if ($item->image && ! $item->is_sensitive)
                                        <img src="{{ asset('storage/'.$item->image) }}" alt=""
                                             onerror="this.style.display='none'; this.nextElementSibling.classList.remove('hidden');"
                                             class="h-full w-full object-cover group-hover:scale-105 transition duration-300">
                                    @endif

                                    <div class="{{ ($item->image && ! $item->is_sensitive) ? 'hidden' : '' }} flex h-full w-full items-center justify-center text-slate-800/50">
                                        <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24">
                                            <rect x="3" y="5" width="18" height="14" rx="2"/>
                                            <circle cx="8.5" cy="10.5" r="1.5"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 15l-5-5L5 19"/>
                                        </svg>
                                    </div>

                                    <span class="absolute top-2.5 left-2.5 rounded-full px-3 py-0.5 text-[11px] font-bold shadow-sm backdrop-blur-md {{ $item->type === 'lost' ? 'bg-rose-100/90 text-rose-700' : 'bg-emerald-100/90 text-emerald-800' }}">
                                        {{ $item->type === 'lost' ? 'Hilang' : 'Ditemukan' }}
                                    </span>
                                </div>

                                {{-- Info Barang --}}
                                <div class="pt-3 px-1 space-y-1">
                                    <h3 class="text-sm font-bold text-[#141923] group-hover:text-[#1B4332] truncate">
                                        {{ $item->title }}
                                    </h3>
                                    <p class="text-xs font-medium text-slate-700">
                                        {{ $item->date_event->translatedFormat('d M Y') }}
                                    </p>
                                    <p class="text-xs text-slate-600 truncate">
                                        {{ $item->location }}
                                    </p>
                                </div>
                            </a>
                        @endforeach
                    </div>

                    <div class="mt-6">
                        {{ $items->links() }}
                    </div>
                @endif
            </section>
        </div>
    </div>
</x-app-layout>