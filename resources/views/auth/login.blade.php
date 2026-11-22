@php
    $activeCampus = request()->route('campus') ?? ($campus ?? null);
    $loginAction = $activeCampus ? route('campus.login', $activeCampus) : route('login');
@endphp

<x-auth-split mode="login">
    <div class="space-y-1">
        <h2 class="text-2xl font-extrabold tracking-tight text-slate-900">
            Selamat datang kembali
        </h2>
        @if($activeCampus)
            <p class="text-xs font-medium text-slate-500">Portal {{ $activeCampus->name }}</p>
        @endif
    </div>

    @if(session('status'))
        <div class="p-3 rounded-2xl bg-emerald-50 text-xs font-semibold text-[#1f5136]">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ $loginAction }}" class="space-y-4">
        @csrf

        <div class="space-y-1.5">
            <label for="email" class="block text-xs font-semibold text-slate-700">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                   placeholder="nama@email.com"
                   class="w-full px-4 py-3 rounded-full bg-white/90 border border-white text-sm text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-[#1f5136]/30">
            @error('email') <p class="text-xs text-rose-600 pl-2">{{ $message }}</p> @enderror
        </div>

        <div class="space-y-1.5">
            <div class="flex items-center justify-between">
                <label for="password" class="block text-xs font-semibold text-slate-700">Kata sandi</label>
                @if(Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-[11px] font-semibold text-slate-500 hover:text-slate-900">
                        Lupa sandi?
                    </a>
                @endif
            </div>
            <input id="password" type="password" name="password" required
                   placeholder="••••••••••"
                   class="w-full px-4 py-3 rounded-full bg-white/90 border border-white text-sm text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-[#1f5136]/30">
            @error('password') <p class="text-xs text-rose-600 pl-2">{{ $message }}</p> @enderror
        </div>

        <div class="flex items-center justify-between pt-1 px-1">
            <label for="remember_me" class="inline-flex items-center gap-2 cursor-pointer">
                <input id="remember_me" type="checkbox" name="remember" class="rounded border-slate-300 text-[#161b26] focus:ring-[#161b26]">
                <span class="text-xs font-medium text-slate-600">Ingat saya</span>
            </label>
            <a href="{{ route('campuses.index') }}" class="text-xs font-semibold text-[#1f5136] hover:underline">
                Ganti kampus
            </a>
        </div>

        <button type="submit"
                class="w-full py-3.5 rounded-full bg-[#161b26] hover:bg-[#1f5136] text-white text-sm font-bold shadow-sm transition-colors cursor-pointer">
            Masuk
        </button>
    </form>
</x-auth-split>