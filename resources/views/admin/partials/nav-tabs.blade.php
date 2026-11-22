@php
    use Illuminate\Support\Facades\Route;

    $campus = $currentCampus ?? request()->route('campus');
    $user = auth()->user();
    $isCampusAdmin = $user && in_array($user->role, ['admin', 'platform_admin'], true);

    $dashboardRoute = Route::has('campus.admin.dashboard') ? 'campus.admin.dashboard' : 'dashboard';

    $tabs = [
        [
            'label' => 'Dashboard',
            'url' => route($dashboardRoute, ['campus' => $campus->slug]),
            'active' => request()->routeIs('campus.admin.dashboard', 'admin.dashboard', 'dashboard'),
        ],
        [
            'label' => 'Moderasi Laporan',
            'url' => route('campus.admin.reports', ['campus' => $campus->slug]),
            'active' => request()->routeIs('campus.admin.reports'),
        ],
        [
            'label' => 'Verifikasi Klaim',
            'url' => route('campus.admin.claims', ['campus' => $campus->slug]),
            'active' => request()->routeIs('campus.admin.claims'),
        ],
    ];

    if ($isCampusAdmin && Route::has('campus.admin.categories.index')) {
        $tabs[] = [
            'label' => 'Kategori',
            'url' => route('campus.admin.categories.index', ['campus' => $campus->slug]),
            'active' => request()->routeIs('campus.admin.categories*'),
        ];
    }
@endphp

<div class="flex flex-wrap items-center gap-1.5 p-1.5 rounded-full bg-white/40 backdrop-blur-xl border border-white/65 shadow-[0_8px_24px_rgba(20,25,35,0.04)] shrink-0">
    @foreach ($tabs as $tab)
        <a href="{{ $tab['url'] }}"
           class="px-3.5 py-2 rounded-full text-xs font-bold tracking-tight whitespace-nowrap transition {{ $tab['active'] ? 'bg-[#141923] text-white shadow-sm' : 'text-slate-800 hover:bg-white/65' }}">
            {{ $tab['label'] }}
        </a>
    @endforeach
</div>