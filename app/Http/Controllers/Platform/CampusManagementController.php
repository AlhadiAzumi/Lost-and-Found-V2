<?php

namespace App\Http\Controllers\Platform;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCampusRequest;
use App\Http\Requests\UpdateCampusStatusRequest;
use App\Mail\CampusAdminInvitation;
use App\Models\Campus;
use App\Models\CampusApplication;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class CampusManagementController extends Controller
{
    public function index(): View
    {
        return view('admin.campuses', [
            'campuses' => Campus::query()->withCount('users')->orderBy('name')->paginate(20),
            'pendingApplications' => CampusApplication::query()->where('status', 'pending')->oldest()->get(),
            'awaitingAdminSetup' => CampusApplication::query()
                ->where('status', 'approved')
                ->whereNull('admin_created_at')
                ->latest()
                ->get(),
        ]);
    }

    public function approve(CampusApplication $application): RedirectResponse
    {
        [$application, $token] = DB::transaction(function () use ($application): array {
            $lockedApplication = CampusApplication::query()
                ->lockForUpdate()
                ->findOrFail($application->id);

            if ($lockedApplication->status !== 'pending') {
                throw ValidationException::withMessages([
                    'application' => 'Pengajuan ini sudah pernah ditinjau.',
                ]);
            }

            if (User::withoutGlobalScopes()
                ->whereRaw('LOWER(email) = ?', [Str::lower($lockedApplication->email)])
                ->exists()) {
                throw ValidationException::withMessages([
                    'application' => 'Email pemohon sudah digunakan akun lain.',
                ]);
            }

            $baseSlug = $lockedApplication->slug;
            $slug = $baseSlug;
            $suffix = 2;

            while (Campus::query()->where('slug', $slug)->exists()) {
                $slug = $baseSlug.'-'.$suffix++;
            }

            $campus = Campus::create([
                'name' => $lockedApplication->name,
                'slug' => $slug,
                'is_active' => true,
            ]);
            $token = Str::random(64);

            $lockedApplication->update([
                'slug' => $slug,
                'status' => 'approved',
                'campus_id' => $campus->id,
                'reviewed_by' => request()->user()->id,
                'reviewed_at' => now(),
                'invitation_token_hash' => Hash::make($token),
                'invitation_expires_at' => now()->addDays(7),
            ]);

            return [$lockedApplication->fresh(), $token];
        });

        if (! $this->sendInvitation($application, $token)) {
            return back()->with('error', 'Portal aktif, tetapi email undangan gagal dikirim. Coba kirim ulang setelah layanan email tersedia.');
        }

        return back()->with('success', 'Portal aktif dan tautan pembuatan akun admin telah dikirim.');
    }

    public function reject(CampusApplication $application): RedirectResponse
    {
        DB::transaction(function () use ($application): void {
            $lockedApplication = CampusApplication::query()
                ->lockForUpdate()
                ->findOrFail($application->id);

            if ($lockedApplication->status !== 'pending') {
                throw ValidationException::withMessages([
                    'application' => 'Pengajuan ini sudah pernah ditinjau.',
                ]);
            }

            $lockedApplication->update([
                'status' => 'rejected',
                'reviewed_by' => request()->user()->id,
                'reviewed_at' => now(),
            ]);
        });

        return back()->with('success', 'Pengajuan portal ditolak.');
    }

    public function resendInvitation(CampusApplication $application): RedirectResponse
    {
        $token = Str::random(64);
        $application = DB::transaction(function () use ($application, $token): CampusApplication {
            $lockedApplication = CampusApplication::query()
                ->lockForUpdate()
                ->findOrFail($application->id);

            if ($lockedApplication->status !== 'approved' || $lockedApplication->admin_created_at !== null) {
                throw ValidationException::withMessages([
                    'application' => 'Undangan admin tidak tersedia untuk pengajuan ini.',
                ]);
            }

            $lockedApplication->update([
                'invitation_token_hash' => Hash::make($token),
                'invitation_expires_at' => now()->addDays(7),
            ]);

            return $lockedApplication;
        });

        if (! $this->sendInvitation($application, $token)) {
            return back()->with('error', 'Email undangan gagal dikirim. Coba lagi setelah layanan email tersedia.');
        }

        return back()->with('success', 'Tautan pembuatan akun admin dikirim ulang.');
    }

    public function store(StoreCampusRequest $request): RedirectResponse
    {
        Campus::create($request->validated() + ['is_active' => true]);

        return back()->with('success', 'Portal kampus berhasil dibuat.');
    }

    public function updateStatus(UpdateCampusStatusRequest $request, Campus $campus): RedirectResponse
    {
        abort_if(
            $campus->slug === config('campus.default_slug') && ! $request->boolean('is_active'),
            422,
            'Portal kampus utama tidak dapat dinonaktifkan.'
        );

        $campus->update(['is_active' => $request->boolean('is_active')]);

        return back()->with('success', 'Status portal kampus berhasil diperbarui.');
    }

    private function sendInvitation(CampusApplication $application, string $token): bool
    {
        try {
            Mail::to($application->email)->send(new CampusAdminInvitation($application, $token));
        } catch (TransportExceptionInterface $exception) {
            report($exception);

            return false;
        }

        return true;
    }
}
