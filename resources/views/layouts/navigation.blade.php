@php
    $routeCampus = request()->route('campus');
    $activeCampus = is_object($routeCampus)
        ? $routeCampus
        : ($campus ?? (auth()->user()?->campus ?? \App\Models\Campus::where('slug', (string) $routeCampus)->first()));

    $user = auth()->user();
    $unreadCount = $user ? $user->unreadNotifications()->count() : 0;

    $homeUrl = $activeCampus ? route('campus.home', $activeCampus) : route('campuses.index');
    $catalogUrl = $activeCampus ? route('campus.catalog', $activeCampus) : route('catalog');
    $lostCreateUrl = $activeCampus ? route('campus.reports.lost.create', $activeCampus) : route('items.create', ['type' => 'lost']);
    $foundCreateUrl = $activeCampus ? route('campus.reports.found.create', $activeCampus) : route('items.create', ['type' => 'found']);
    $historyUrl = $activeCampus ? route('campus.my.history', $activeCampus) : route('my.history');
    $notifUrl = $activeCampus ? route('campus.notifications.index', $activeCampus) : route('notifications.index');
    $adminUrl = $activeCampus ? route('campus.admin.dashboard', $activeCampus) : route('admin.dashboard');
    $handoversUrl = $activeCampus ? route('campus.admin.handovers', $activeCampus) : route('admin.handovers');
    $profileUrl = $activeCampus ? route('campus.profile.edit', $activeCampus) : route('profile.edit');
    $logoutUrl = $activeCampus ? route('campus.logout', $activeCampus) : route('logout');

    $loginUrl = $activeCampus ? route('campus.login', $activeCampus) : route('login');
    $registerUrl = $activeCampus ? route('campus.register', $activeCampus) : route('register');
@endphp

<div x-data="{ mobileOpen: false }">
    {{-- Mobile Bar --}}
    <div class="lg:hidden sticky top-0 z-40 px-4 pt-3">
        <div class="glass-card px-4 py-3 flex items-center justify-between">
            <a href="{{ $homeUrl }}" class="font-extrabold text-base text-slate-900">Lost & Found</a>
            <button @click="mobileOpen = !mobileOpen" class="p-2 rounded-xl bg-white text-slate-800">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
        </div>
    </div>

    <div x-show="mobileOpen" @click="mobileOpen = false" class="lg:hidden fixed inset-0 bg-slate-900/25 z-40"></div>

    {{-- Sidebar Vertikal Rapi (Sesuai katalog_2.jpg) --}}
    <aside :class="mobileOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
           class="fixed top-2.5 bottom-2.5 left-2.5 w-[232px] rounded-[26px] bg-gradient-to-b from-[#9bb79d] via-[#dde7dd] to-[#eef3ee] border border-white/75 shadow-[0_8px_24px_rgba(35,89,58,0.08)] px-4 py-6 flex flex-col justify-between z-50 transition-transform duration-200">
        <div class="space-y-6">
            {{-- Brand Title --}}
            <div class="px-3">
                <a href="{{ $homeUrl }}" class="block font-extrabold text-[21px] tracking-tight text-white">
                    Lost & Found
                </a>
            </div>

            {{-- Menu Utama --}}
            <nav class="space-y-1">
                @auth
                    <a href="{{ $profileUrl }}"
                       class="flex items-center gap-3 px-4 py-2.5 rounded-full text-[13px] font-semibold transition-colors {{ request()->routeIs('*.profile.*') || request()->routeIs('profile.*') ? 'bg-[#111111] text-white' : 'text-slate-700 hover:bg-white/50' }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        <span>Profil</span>
                    </a>
                @endauth

                <a href="{{ $lostCreateUrl }}"
                   class="flex items-center gap-3 px-4 py-2.5 rounded-full text-[13px] font-semibold transition-colors {{ request()->routeIs('*.reports.lost.create') || (request()->routeIs('items.create') && request('type') === 'lost') ? 'bg-[#111111] text-white' : 'text-slate-700 hover:bg-white/50' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 4v16m8-8H4"/></svg>
                    <span>Lapor Hilang</span>
                </a>

                <a href="{{ $foundCreateUrl }}"
                   class="flex items-center gap-3 px-4 py-2.5 rounded-full text-[13px] font-semibold transition-colors {{ request()->routeIs('*.reports.found.create') || (request()->routeIs('items.create') && request('type') === 'found') ? 'bg-[#111111] text-white' : 'text-slate-700 hover:bg-white/50' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5 13l4 4L19 7"/></svg>
                    <span>Lapor Temuan</span>
                </a>

                <a href="{{ $catalogUrl }}"
                   class="flex items-center gap-3 px-4 py-2.5 rounded-full text-[13px] font-semibold transition-colors {{ request()->routeIs('*.catalog') || request()->routeIs('*.home') || request()->routeIs('catalog') ? 'bg-[#111111] text-white' : 'text-slate-700 hover:bg-white/50' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <span>Feed Publik</span>
                </a>

                @auth
                    <a href="{{ $historyUrl }}"
                       class="flex items-center gap-3 px-4 py-2.5 rounded-full text-[13px] font-semibold transition-colors {{ request()->routeIs('*.my.history') || request()->routeIs('my.history') ? 'bg-[#111111] text-white' : 'text-slate-700 hover:bg-white/50' }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="8" stroke-width="1.8"/></svg>
                        <span>Klaim</span>
                    </a>

                    @if(in_array($user->role ?? '', ['admin', 'satpam']))
                        <a href="{{ $handoversUrl }}"
                           class="flex items-center gap-3 px-4 py-2.5 rounded-full text-[13px] font-semibold transition-colors {{ request()->routeIs('*.admin.handovers') || request()->routeIs('admin.handovers') ? 'bg-[#111111] text-white' : 'text-slate-700 hover:bg-white/50' }}">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 4h6v6H4V4zm10 0h6v6h-6V4zM4 14h6v6H4v-6zm10 3h6m-3-3v6"/></svg>
                            <span>Serah Terima</span>
                        </a>

                        <a href="{{ $adminUrl }}"
                           class="flex items-center gap-3 px-4 py-2.5 rounded-full text-[13px] font-semibold transition-colors {{ request()->routeIs('*.admin.dashboard') || request()->routeIs('*.admin.reports') || request()->routeIs('*.admin.claims') || (request()->routeIs('admin.*') && !request()->routeIs('*.handovers')) ? 'bg-[#111111] text-white' : 'text-slate-700 hover:bg-white/50' }}">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                            <span>Moderasi Admin</span>
                        </a>
                    @endif
                @endauth
            </nav>
        </div>

        {{-- Footer Sidebar dengan Background Pill Tipis --}}
        <div class="space-y-2 pt-3 border-t border-slate-300/50">
            @auth
                <a href="{{ $notifUrl }}"
                    class="flex items-center justify-between px-4 py-2.5 rounded-full bg-white/65 hover:bg-white/95 border border-white/80 text-xs font-semibold text-slate-700 shadow-2xs transition-all">
                    <span>Notifikasi</span>
                    @if($unreadCount > 0)
                        <span class="px-2 py-0.5 bg-[#111111] text-white text-[10px] font-bold rounded-full">{{ $unreadCount }}</span>
                    @endif
                </a>
                <form method="POST" action="{{ $logoutUrl }}">
                    @csrf
                    <button type="submit"
                        class="w-full px-4 py-2.5 rounded-full bg-rose-500/10 hover:bg-rose-500/20 border border-rose-200/60 text-xs font-semibold text-rose-700 transition-all text-left cursor-pointer">
                        Keluar
                    </button>
                </form>
            @else
                <a href="{{ $loginUrl }}"
                    class="block w-full py-2.5 text-center rounded-full bg-[#111111] hover:bg-[#23593a] text-white text-xs font-bold transition-colors">
                    Masuk
                </a>
                <a href="{{ $registerUrl }}"
                    class="block w-full py-2.5 text-center rounded-full bg-white/80 hover:bg-white text-slate-800 text-xs font-bold transition-colors">
                    Daftar
                </a>
            @endauth
</div>
    </aside>
</div>