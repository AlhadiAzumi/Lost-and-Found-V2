<?php

namespace App\Http\Controllers;

use App\Http\Requests\CompleteCampusAdminSetupRequest;
use App\Http\Requests\StoreCampusApplicationRequest;
use App\Models\Campus;
use App\Models\CampusApplication;
use App\Models\User;
use App\Support\CampusContext;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CampusApplicationController extends Controller
{
    public function store(StoreCampusApplicationRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $email = Str::lower(trim($validated['email']));

        if (User::withoutGlobalScopes()->whereRaw('LOWER(email) = ?', [$email])->exists()) {
            throw ValidationException::withMessages([
                'email' => 'Email ini sudah terhubung ke akun kampus lain. Gunakan email yang belum terdaftar.',
            ]);
        }

        if (CampusApplication::query()
            ->whereRaw('LOWER(email) = ?', [$email])
            ->where('status', 'pending')
            ->exists()) {
            throw ValidationException::withMessages([
                'email' => 'Email ini sudah memiliki pengajuan yang menunggu persetujuan.',
            ]);
        }

        $baseSlug = Str::substr(Str::slug(trim($validated['name'])) ?: 'kampus', 0, 70);
        $slug = $baseSlug;
        $suffix = 2;

        while (Campus::query()->where('slug', $slug)->exists()
            || CampusApplication::query()->where('slug', $slug)->exists()) {
            $slug = $baseSlug.'-'.$suffix++;
        }

        CampusApplication::create([
            'name' => trim($validated['name']),
            'email' => $email,
            'slug' => $slug,
        ]);

        return redirect()->route('campuses.index')
            ->with('application_submitted', 'Pengajuan portal berhasil dikirim dan menunggu persetujuan admin platform.');
    }

    public function setup(CampusApplication $application, string $token): View|RedirectResponse
    {
        if (! $this->invitationIsValid($application, $token)) {
            return redirect()->route('campuses.index')
                ->with('error', 'Tautan pengaturan admin tidak valid atau sudah kedaluwarsa.');
        }

        return view('campuses.setup-admin', compact('application', 'token'));
    }

    public function completeSetup(
        CompleteCampusAdminSetupRequest $request,
        CampusApplication $application,
        string $token
    ): RedirectResponse {
        if (! $this->invitationIsValid($application, $token)) {
            return redirect()->route('campuses.index')
                ->with('error', 'Tautan pengaturan admin tidak valid atau sudah kedaluwarsa.');
        }

        $user = DB::transaction(function () use ($request, $application, $token): User {
            $lockedApplication = CampusApplication::query()
                ->lockForUpdate()
                ->findOrFail($application->id);

            abort_unless($this->invitationIsValid($lockedApplication, $token), 410);

            $emailExists = User::withoutGlobalScopes()
                ->whereRaw('LOWER(email) = ?', [Str::lower($lockedApplication->email)])
                ->exists();

            if ($emailExists) {
                abort(409, 'Email pemohon sudah digunakan oleh akun lain.');
            }

            $campus = Campus::query()->findOrFail($lockedApplication->campus_id);
            app(CampusContext::class)->set($campus);

            $user = User::create([
                'name' => $request->validated('name'),
                'email' => $lockedApplication->email,
                'password' => Hash::make($request->validated('password')),
                'role' => 'admin',
            ]);

            $lockedApplication->update([
                'admin_created_at' => now(),
                'invitation_token_hash' => null,
                'invitation_expires_at' => null,
            ]);

            return $user;
        });

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('campus.home', ['campus' => $user->campus->slug])
            ->with('success', 'Akun admin kampus berhasil dibuat.');
    }

    private function invitationIsValid(CampusApplication $application, string $token): bool
    {
        return $application->status === 'approved'
            && $application->campus_id !== null
            && $application->admin_created_at === null
            && $application->invitation_expires_at?->isFuture()
            && $application->invitation_token_hash !== null
            && Hash::check($token, $application->invitation_token_hash);
    }
}
