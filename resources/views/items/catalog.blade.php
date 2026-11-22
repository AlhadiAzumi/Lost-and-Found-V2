@php
    $activeCampus = request()->route('campus') ?? ($campus ?? (auth()->user()?->campus ?? null));
    $catalogAction = $activeCampus ? route('campus.catalog', $activeCampus) : route('catalog');
    $user = auth()->user();
@endphp

<x-app-layout title="Feed Publik">
    <form method="GET" action="{{ $catalogAction }}" class="space-y-6">
        {{-- Top Row: Judul "Feed Publik" + Search Input Pill Kanan (Persis katalog_2.jpg) --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <h1 class="text-[26px] font-extrabold tracking-tight text-slate-900">
                Feed Publik
            </h1>

            <div class="w-full sm:w-[300px]">
                <input type="text"
                       name="search"
                       value="{{ $filters['search'] ?? '' }}"
                       placeholder="Cari barang..."
                       class="w-full px-5 py-2.5 rounded-full bg-[#f4f7f4]/90 border border-white text-sm text-slate-800 placeholder:text-slate-400 shadow-sm focus:bg-white focus:outline-none">
            </div>
        </div>

        {{-- Main Content: Panel Filter Kiri + Grid 3 Kolom Kartu Barang --}}
        <div class="flex flex-col lg:flex-row gap-6 items-start">
            {{-- Panel Filter Kiri --}}
            <aside class="w-full lg:w-[235px] shrink-0 glass-card p-5 space-y-4">
                <div class="flex items-center justify-between">
                    <h2 class="text-sm font-extrabold text-slate-900">Filter</h2>
                    @if(collect($filters)->filter()->isNotEmpty())
                        <a href="{{ $catalogAction }}" class="text-[11px] font-bold text-slate-500 hover:text-slate-900">
                            Reset
                        </a>
                    @endif
                </div>

                {{-- Status: Hanya Hilang & Ditemukan (Sesuai katalog_2.jpg) --}}
                <div class="space-y-2">
                    <label class="block text-[11px] font-semibold text-slate-600">Status</label>
                    <div class="flex items-center gap-2">
                        <label class="cursor-pointer">
                            <input type="radio" name="type" value="lost" class="sr-only peer" @checked(($filters['type'] ?? '') === 'lost')>
                            <span class="inline-block px-3 py-1 rounded-full text-xs font-bold bg-[#fde2e2] text-[#c83e3e] peer-checked:ring-2 peer-checked:ring-[#c83e3e]">Hilang</span>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="type" value="found" class="sr-only peer" @checked(($filters['type'] ?? '') === 'found')>
                            <span class="inline-block px-3 py-1 rounded-full text-xs font-bold bg-[#d8f3e5] text-[#1f6e4a] peer-checked:ring-2 peer-checked:ring-[#1f6e4a]">Ditemukan</span>
                        </label>
                    </div>
                </div>

                {{-- Kategori --}}
                <div class="space-y-1.5">
                    <label class="block text-[11px] font-semibold text-slate-600">Kategori</label>
                    <select name="category_id" class="w-full px-4 py-2.5 rounded-full bg-[#eef2ee] text-xs font-medium text-slate-700 focus:bg-white focus:outline-none">
                        <option value="">Semua kategori</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" @selected((string)($filters['category_id'] ?? '') === (string)$category->id)>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Dari & Sampai Tanggal --}}
                <div class="space-y-1.5">
                    <label class="block text-[11px] font-semibold text-slate-600">Dari tanggal</label>
                    <input type="date" name="from" value="{{ $filters['from'] ?? '' }}"
                        class="w-full px-4 py-2 rounded-full bg-[#eef2ee] text-xs text-slate-700 focus:bg-white focus:outline-none">
                </div>

                <div class="space-y-1.5">
                    <label class="block text-[11px] font-semibold text-slate-600">Sampai tanggal</label>
                    <input type="date" name="to" value="{{ $filters['to'] ?? '' }}"
                        class="w-full px-4 py-2 rounded-full bg-[#eef2ee] text-xs text-slate-700 focus:bg-white focus:outline-none">
                </div>

                {{-- Lokasi --}}
                <div class="space-y-1.5">
                    <label class="block text-[11px] font-semibold text-slate-600">Lokasi</label>
                    <select name="location" class="w-full px-4 py-2.5 rounded-full bg-[#eef2ee] text-xs font-medium text-slate-700 focus:bg-white focus:outline-none">
                        <option value="">Semua lokasi</option>
                        @foreach($locations as $loc)
                            <option value="{{ $loc->name }}" @selected(($filters['location'] ?? '') === $loc->name)>
                                {{ $loc->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <button type="submit"
                        class="w-full py-2.5 rounded-full bg-[#111111] hover:bg-[#23593a] text-white text-xs font-bold transition-colors cursor-pointer">
                    Terapkan
                </button>
            </aside>

            {{-- Grid 3 Kolom Kartu Barang (Sesuai katalog_2.jpg) --}}
            <div class="flex-1 w-full space-y-5">
                @if($items->isEmpty())
                    <div class="glass-card p-12 text-center space-y-1">
                        <p class="font-bold text-sm text-slate-800">Belum ada laporan barang.</p>
                        <p class="text-xs text-slate-500">Coba ubah filter pencarian di samping.</p>
                    </div>
                @else
                    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-5">
                        @foreach($items as $item)
                            @php
                                $itemCampus = $activeCampus ?? $item->campus;
                                $detailUrl = $itemCampus
                                    ? route('campus.items.show', ['campus' => $itemCampus, 'item' => $item])
                                    : route('items.show', $item);
                                $imagePath = $item->image_path ?? $item->image ?? null;
                                $canViewSensitive = $user && ($user->id === $item->user_id || in_array($user->role, ['admin', 'satpam'], true));
                                $hidePhoto = $item->is_sensitive && !$canViewSensitive;
                            @endphp

                            <a href="{{ $detailUrl }}" class="glass-card-hover p-4 flex flex-col gap-3.5 group">
                                {{-- Kotak Gambar Hijau Sage Gradien + Ikon Minimalis di Tengah --}}
                                <div x-data="{ broken: false }"
                                     class="relative h-36 rounded-2xl bg-gradient-to-br from-[#b8d0b9] to-[#87ad8b] overflow-hidden flex items-center justify-center">
                                    @if($hidePhoto)
                                        <span class="px-3 py-1 rounded-full bg-white/85 text-[11px] font-bold text-slate-800">
                                            Sensitif
                                        </span>
                                    @elseif($imagePath)
                                        <img x-show="!broken"
                                             src="{{ asset('storage/' . $imagePath) }}"
                                             alt="{{ $item->title }}"
                                             x-on:error="broken = true"
                                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                    @endif

                                    <div x-show="broken || {{ (!$hidePhoto && !$imagePath) ? 'true' : 'false' }}"
                                         class="text-[#23593a]/70">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>

                                    {{-- Status Badge Pill --}}
                                    <span class="absolute top-2.5 left-2.5 px-3 py-0.5 rounded-full text-[11px] font-bold {{ $item->type === 'lost' ? 'bg-[#fde2e2] text-[#c83e3e]' : 'bg-[#d8f3e5] text-[#1f6e4a]' }}">
                                        {{ $item->type === 'lost' ? 'Hilang' : 'Ditemukan' }}
                                    </span>
                                </div>

                                {{-- Teks Info Bersih Sesuai katalog_2.jpg --}}
                                <div class="space-y-0.5 px-0.5">
                                    <h3 class="font-extrabold text-[15px] text-slate-900 truncate">
                                        {{ $item->title }}
                                    </h3>
                                    <p class="text-xs font-medium text-slate-700">
                                        {{ \Carbon\Carbon::parse($item->date_event)->translatedFormat('d M Y') }}
                                    </p>
                                    <p class="text-xs text-slate-500 truncate">
                                        {{ $item->locationRecord->name ?? $item->location ?? 'Lokasi' }}
                                    </p>
                                </div>
                            </a>
                        @endforeach
                    </div>

                    <div class="pt-2">
                        {{ $items->links() }}
                    </div>
                @endif
            </div>
        </div>
    </form>
</x-app-layout>