@php
    use Illuminate\Support\Facades\Route;

    $campus = $currentCampus ?? request()->route('campus');
    $user = auth()->user();
    $isStaff = $user && in_array($user->role, ['admin', 'satpam', 'platform_admin'], true);

    $feedUrl = $campus ? route('campus.home', ['campus' => $campus->slug]) : route('catalog');
    $profileUrl = $campus ? route('campus.profile.edit', ['campus' => $campus->slug]) : route('profile.edit');
    $lostUrl = $campus ? route('campus.reports.lost.create', ['campus' => $campus->slug]) : route('items.create');
    $foundUrl = $campus ? route('campus.reports.found.create', ['campus' => $campus->slug]) : route('items.create');
    $historyUrl = $campus ? route('campus.my.history', ['campus' => $campus->slug]) : route('my.history');
    $handoverUrl = $campus ? route('campus.admin.handovers', ['campus' => $campus->slug]) : route('admin.handovers');
    $adminUrl = $campus ? route('campus.admin.dashboard', ['campus' => $campus->slug]) : route('admin.dashboard');
    $logoutUrl = $campus ? route('campus.logout', ['campus' => $campus->slug]) : route('logout');
    $loginUrl = $campus ? route('campus.login', ['campus' => $campus->slug]) : route('login');

    $notificationsUrl = null;
    if ($campus && Route::has('campus.notifications.index')) {
        $notificationsUrl = route('campus.notifications.index', ['campus' => $campus->slug]);
    } elseif (Route::has('notifications.index')) {
        $notificationsUrl = route('notifications.index');
    }

    $unreadNotificationsCount = 0;
    if ($user && method_exists($user, 'notifications')) {
        $unreadNotificationsCount = $user->notifications()->whereNull('read_at')->count();
    }
@endphp

<aside x-data="{ open: false }" class="w-full lg:w-[250px] shrink-0">
    <div class="lg:sticky lg:top-6 rounded-[28px] bg-white/40 backdrop-blur-xl border border-white/65 shadow-[0_12px_36px_rgba(20,25,35,0.06)] p-5 flex flex-col justify-between lg:min-h-[calc(100vh-3rem)]">
        <div>
            {{-- Brand & Mobile Toggle --}}
            <div class="flex items-center justify-between px-2 pt-1 pb-6">
                <a href="{{ $feedUrl }}" class="block">
                    <span class="text-xl font-extrabold tracking-tight text-[#141923]">Lost &amp; Found</span>
                    @if ($campus)
                        <span class="block text-xs font-semibold text-[#1B4332] truncate max-w-[175px] mt-0.5">
                            {{ $campus->name }}
                        </span>
                    @endif
                </a>

                <button @click="open = !open" type="button"
                        class="lg:hidden rounded-xl p-2 text-slate-800 hover:bg-white/60 focus:outline-none">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path x-show="!open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        <path x-show="open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Menu Navigasi --}}
            <nav :class="{ 'block': open, 'hidden': !open }" class="hidden lg:block space-y-1.5">
                @auth
                    <a href="{{ $profileUrl }}"
                       class="flex items-center gap-3 px-4 py-2.5 rounded-full text-sm font-semibold transition {{ request()->routeIs('profile.edit', 'campus.profile.edit') ? 'bg-[#141923] text-white shadow-sm' : 'text-slate-800 hover:bg-white/55' }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        <span>Profil</span>
                    </a>

                    @if ($notificationsUrl)
                        <a href="{{ $notificationsUrl }}"
                           class="flex items-center justify-between gap-2 px-4 py-2.5 rounded-full text-sm font-semibold transition {{ request()->routeIs('notifications.*', 'campus.notifications.*') ? 'bg-[#141923] text-white shadow-sm' : 'text-slate-800 hover:bg-white/55' }}">
                            <span class="flex items-center gap-3">
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                </svg>
                                <span>Notifikasi</span>
                            </span>

                            @if ($unreadNotificationsCount > 0)
                                <span class="inline-flex items-center justify-center min-w-[20px] h-5 px-1.5 rounded-full text-[11px] font-extrabold {{ request()->routeIs('notifications.*', 'campus.notifications.*') ? 'bg-emerald-400 text-[#141923]' : 'bg-[#141923] text-white' }}">
                                    {{ $unreadNotificationsCount > 99 ? '99+' : $unreadNotificationsCount }}
                                </span>
                            @endif
                        </a>
                    @endif

                    <a href="{{ $lostUrl }}"
                       class="flex items-center gap-3 px-4 py-2.5 rounded-full text-sm font-semibold transition {{ request()->routeIs('campus.reports.lost.create') ? 'bg-[#141923] text-white shadow-sm' : 'text-slate-800 hover:bg-white/55' }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                        </svg>
                        <span>Lapor Hilang</span>
                    </a>

                    <a href="{{ $foundUrl }}"
                       class="flex items-center gap-3 px-4 py-2.5 rounded-full text-sm font-semibold transition {{ request()->routeIs('campus.reports.found.create') ? 'bg-[#141923] text-white shadow-sm' : 'text-slate-800 hover:bg-white/55' }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span>Lapor Temuan</span>
                    </a>
                @endauth

                <a href="{{ $feedUrl }}"
                   class="flex items-center gap-3 px-4 py-2.5 rounded-full text-sm font-semibold transition {{ request()->routeIs('campus.home', 'campus.catalog', 'catalog') ? 'bg-[#141923] text-white shadow-sm' : 'text-slate-800 hover:bg-white/55' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z"/>
                    </svg>
                    <span>Feed Publik</span>
                </a>

                @auth
                    <a href="{{ $historyUrl }}"
                       class="flex items-center gap-3 px-4 py-2.5 rounded-full text-sm font-semibold transition {{ request()->routeIs('my.history', 'campus.my.history') ? 'bg-[#141923] text-white shadow-sm' : 'text-slate-800 hover:bg-white/55' }}">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="8"/>
                        </svg>
                        <span>Klaim</span>
                    </a>

                    @if ($isStaff)
                        <a href="{{ $handoverUrl }}"
                           class="flex items-center gap-3 px-4 py-2.5 rounded-full text-sm font-semibold transition {{ request()->routeIs('admin.handovers', 'campus.admin.handovers') ? 'bg-[#141923] text-white shadow-sm' : 'text-slate-800 hover:bg-white/55' }}">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h6v6H4V6zm10 0h6v6h-6V6zM4 16h6v4H4v-4zm10 0h6v4h-6v-4z"/>
                            </svg>
                            <span>Serah Terima</span>
                        </a>

                        <a href="{{ $adminUrl }}"
                           class="flex items-center gap-3 px-4 py-2.5 rounded-full text-sm font-semibold transition {{ request()->routeIs('admin.dashboard', 'campus.admin.dashboard', 'campus.admin.reports', 'campus.admin.claims', 'campus.admin.categories*') ? 'bg-[#141923] text-white shadow-sm' : 'text-slate-800 hover:bg-white/55' }}">
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                            <span>Moderasi Admin</span>
                        </a>
                    @endif
                @endauth
            </nav>
        </div>

        {{-- Footer Sidebar --}}
        <div :class="{ 'block': open, 'hidden': !open }" class="hidden lg:block pt-6 mt-6 border-t border-white/50 space-y-2">
            <a href="{{ route('campuses.index') }}"
               class="flex items-center justify-center w-full px-4 py-2.5 rounded-full text-xs font-bold text-slate-800 bg-white/55 hover:bg-white/80 border border-white/70 transition">
                &larr; Ganti Portal Kampus
            </a>

            @auth
                <form method="POST" action="{{ $logoutUrl }}">
                    @csrf
                    <button type="submit"
                            class="w-full px-4 py-2.5 rounded-full text-xs font-bold text-slate-900 bg-white/40 hover:bg-rose-600 hover:text-white border border-white/70 transition">
                        Keluar
                    </button>
                </form>
            @else
                <a href="{{ $loginUrl }}"
                   class="flex items-center justify-center w-full px-4 py-2.5 rounded-full text-xs font-bold text-white bg-[#141923] hover:bg-slate-800 transition">
                    Masuk / Daftar
                </a>
            @endauth
        </div>
    </div>
</aside>