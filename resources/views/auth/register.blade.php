@php
    $activeCampus = request()->route('campus') ?? ($campus ?? null);
    $registerAction = $activeCampus ? route('campus.register.store', $activeCampus) : route('register');
@endphp

<x-auth-split mode="register">
    <div class="space-y-1">
        <h2 class="text-2xl font-extrabold tracking-tight text-slate-900">
            Buat akun baru
        </h2>
        @if($activeCampus)
            <p class="text-xs font-medium text-slate-500">Warga {{ $activeCampus->name }}</p>
        @endif
    </div>

    <form method="POST" action="{{ $registerAction }}" class="space-y-3.5">
        @csrf

        <div class="space-y-1">
            <label for="name" class="block text-xs font-semibold text-slate-700">Nama lengkap</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus
                   placeholder="Nama Anda"
                   class="w-full px-4 py-2.5 rounded-full bg-white/90 border border-white text-sm text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-[#1f5136]/30">
            @error('name') <p class="text-xs text-rose-600 pl-2">{{ $message }}</p> @enderror
        </div>

        <div class="grid grid-cols-2 gap-2.5">
            <div class="space-y-1">
                <label for="nim_nip" class="block text-xs font-semibold text-slate-700">NIM / NIP</label>
                <input id="nim_nip" type="text" name="nim_nip" value="{{ old('nim_nip') }}"
                       placeholder="Opsional"
                       class="w-full px-4 py-2.5 rounded-full bg-white/90 border border-white text-sm text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-[#1f5136]/30">
                @error('nim_nip') <p class="text-xs text-rose-600 pl-2">{{ $message }}</p> @enderror
            </div>

            <div class="space-y-1">
                <label for="phone_number" class="block text-xs font-semibold text-slate-700">No. WhatsApp</label>
                <input id="phone_number" type="text" name="phone_number" value="{{ old('phone_number') }}" required
                       placeholder="0812xxxx"
                       class="w-full px-4 py-2.5 rounded-full bg-white/90 border border-white text-sm text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-[#1f5136]/30">
                @error('phone_number') <p class="text-xs text-rose-600 pl-2">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="space-y-1">
            <label for="email" class="block text-xs font-semibold text-slate-700">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required
                   placeholder="nama@email.com"
                   class="w-full px-4 py-2.5 rounded-full bg-white/90 border border-white text-sm text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-[#1f5136]/30">
            @error('email') <p class="text-xs text-rose-600 pl-2">{{ $message }}</p> @enderror
        </div>

        <div class="grid grid-cols-2 gap-2.5">
            <div class="space-y-1">
                <label for="password" class="block text-xs font-semibold text-slate-700">Kata sandi</label>
                <input id="password" type="password" name="password" required
                       placeholder="Min. 8 karakter"
                       class="w-full px-4 py-2.5 rounded-full bg-white/90 border border-white text-sm text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-[#1f5136]/30">
                @error('password') <p class="text-xs text-rose-600 pl-2">{{ $message }}</p> @enderror
            </div>

            <div class="space-y-1">
                <label for="password_confirmation" class="block text-xs font-semibold text-slate-700">Ulangi sandi</label>
                <input id="password_confirmation" type="password" name="password_confirmation" required
                       placeholder="Konfirmasi"
                       class="w-full px-4 py-2.5 rounded-full bg-white/90 border border-white text-sm text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-[#1f5136]/30">
            </div>
        </div>

        <button type="submit"
                class="w-full py-3.5 mt-1 rounded-full bg-[#161b26] hover:bg-[#1f5136] text-white text-sm font-bold shadow-sm transition-colors cursor-pointer">
            Daftar
        </button>
    </form>
</x-auth-split>