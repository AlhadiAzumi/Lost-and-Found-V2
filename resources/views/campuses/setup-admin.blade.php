<x-guest-layout>
    <main class="mx-auto max-w-xl px-4 py-12">
        <div class="rounded-xl border border-gray-200 bg-white p-6">
            <h1 class="text-2xl font-bold">Buat akun admin kampus</h1>
            <p class="mt-2 text-gray-600">Portal {{ $application->name }} telah disetujui.</p>
            <form method="POST" action="{{ route('campus-applications.setup.complete', ['application' => $application, 'token' => $token]) }}" class="mt-6 space-y-4">
                @csrf
                @if ($errors->any())
                    <div class="rounded-md bg-red-50 p-4 text-sm text-red-800" role="alert">@foreach ($errors->all() as $error)<p>{{ $error }}</p>@endforeach</div>
                @endif
                <label class="block text-sm font-medium">Nama admin
                    <input name="name" value="{{ old('name') }}" required maxlength="255" class="mt-1 block w-full rounded-md border-gray-300">
                </label>
                <label class="block text-sm font-medium">Email
                    <input type="email" value="{{ $application->email }}" readonly class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50">
                </label>
                <label class="block text-sm font-medium">Kata sandi
                    <input type="password" name="password" required autocomplete="new-password" class="mt-1 block w-full rounded-md border-gray-300">
                </label>
                <label class="block text-sm font-medium">Konfirmasi kata sandi
                    <input type="password" name="password_confirmation" required autocomplete="new-password" class="mt-1 block w-full rounded-md border-gray-300">
                </label>
                <button class="rounded-md bg-indigo-600 px-4 py-2 font-semibold text-white">Aktifkan akun admin</button>
            </form>
        </div>
    </main>
</x-guest-layout>
