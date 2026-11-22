@php
    $campus = $currentCampus ?? request()->route('campus');
@endphp

<x-app-layout>
    <div class="max-w-4xl mx-auto space-y-6 pb-8">
        {{-- Top Header --}}
        <div class="flex flex-wrap items-center justify-between gap-4 pt-1">
            <div>
                <h1 class="text-2xl sm:text-[28px] font-extrabold tracking-tight text-[#141923]">
                    Notifikasi
                </h1>
                <p class="mt-1 text-xs sm:text-sm text-slate-600">
                    Pembaruan status laporan, pengajuan klaim, dan serah terima barang Anda.
                </p>
            </div>

            @if ($notifications->isNotEmpty())
                <form method="POST" action="{{ route('campus.notifications.read-all', ['campus' => $campus->slug]) }}">
                    @csrf
                    <button type="submit"
                            class="rounded-full bg-white/60 hover:bg-[#141923] hover:text-white border border-white/75 px-5 py-2.5 text-xs font-bold text-[#141923] shadow-sm transition">
                        Tandai semua sudah dibaca
                    </button>
                </form>
            @endif
        </div>

        {{-- Daftar Notifikasi --}}
        <div class="space-y-3">
            @forelse ($notifications as $notification)
                @php
                    $isUnread = ! $notification->read_at;
                @endphp
                <article class="rounded-[24px] {{ $isUnread ? 'bg-white/65 border-white/90 shadow-[0_12px_32px_rgba(20,25,35,0.08)]' : 'bg-white/35 border-white/55' }} backdrop-blur-xl border p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4 transition">
                    <div class="space-y-1 min-w-0">
                        <div class="flex items-center gap-2.5">
                            @if ($isUnread)
                                <span class="h-2.5 w-2.5 shrink-0 rounded-full bg-emerald-600"></span>
                            @endif
                            <h2 class="text-sm sm:text-base font-extrabold text-[#141923]">
                                {{ $notification->title }}
                            </h2>
                        </div>
                        <p class="text-xs sm:text-sm text-slate-700 leading-relaxed">
                            {{ $notification->message }}
                        </p>
                        @if ($notification->created_at)
                            <p class="text-[11px] font-medium text-slate-500 pt-0.5">
                                {{ $notification->created_at->diffForHumans() }}
                            </p>
                        @endif
                    </div>

                    @if ($isUnread)
                        <form method="POST" action="{{ route('campus.notifications.read', ['campus' => $campus->slug, 'notification' => $notification]) }}" class="shrink-0">
                            @csrf
                            <button type="submit"
                                    class="rounded-full bg-[#141923] hover:bg-slate-800 px-4 py-2 text-xs font-semibold text-white transition">
                                Tandai dibaca
                            </button>
                        </form>
                    @endif
                </article>
            @empty
                <div class="rounded-[28px] bg-white/40 backdrop-blur-xl border border-white/65 p-12 text-center shadow-[0_12px_36px_rgba(20,25,35,0.06)]">
                    <p class="text-base font-bold text-slate-900">Belum ada notifikasi</p>
                    <p class="mt-1 text-xs text-slate-600">
                        Semua pemberitahuan aktivitas akun Anda akan muncul di sini.
                    </p>
                </div>
            @endforelse
        </div>

        <div>{{ $notifications->links() }}</div>
    </div>
</x-app-layout>