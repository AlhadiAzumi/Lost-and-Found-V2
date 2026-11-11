<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\Claim;
use App\Models\Item;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use RuntimeException;
use Throwable;

class ClaimService
{
    public function __construct(private NotificationService $notifications)
    {
    }

    public function submit(Item $item, User $claimant, string $answer, ?UploadedFile $proof = null): Claim
    {
        $proofPath = $proof?->store('claims', 'local');

        if ($proof !== null && $proofPath === false) {
            throw new RuntimeException('Bukti klaim gagal disimpan. Silakan coba kembali.');
        }

        try {
            $claim = DB::transaction(function () use ($item, $claimant, $answer, $proofPath): Claim {
                $lockedItem = Item::query()->lockForUpdate()->findOrFail($item->id);

                if ($lockedItem->type !== 'found'
                    || $lockedItem->status !== 'approved'
                    || $lockedItem->user_id === $claimant->id
                    || $lockedItem->claims()->whereIn('status', ['pending', 'approved'])->exists()) {
                    throw ValidationException::withMessages([
                        'item' => 'Barang ini tidak lagi tersedia untuk diklaim.',
                    ]);
                }

                $claim = Claim::create([
                    'item_id' => $lockedItem->id,
                    'user_id' => $claimant->id,
                    'security_answer' => $answer,
                    'proof_image' => $proofPath,
                    'status' => 'pending',
                ]);

                $lockedItem->update(['status' => 'claim_in_progress']);

                ActivityLog::create([
                    'user_id' => $claimant->id,
                    'action' => 'claim_submitted',
                    'description' => "Mengajukan klaim untuk barang {$lockedItem->title}.",
                ]);

                return $claim;
            });
        } catch (Throwable $exception) {
            if (is_string($proofPath)) {
                Storage::disk('local')->delete($proofPath);
            }

            throw $exception;
        }

        $this->notifications->claimSubmitted($claim);

        return $claim;
    }

    public function decide(Claim $claim, User $verifier, string $action, ?string $note): Claim
    {
        $updated = DB::transaction(function () use ($claim, $verifier, $action, $note): Claim {
            $lockedClaim = Claim::query()->lockForUpdate()->with('item')->findOrFail($claim->id);

            if ($lockedClaim->status !== 'pending') {
                throw ValidationException::withMessages([
                    'claim' => 'Klaim ini sudah diproses.',
                ]);
            }

            if ($action === 'approve' && $lockedClaim->securityAnswerMatches() !== true) {
                throw ValidationException::withMessages([
                    'security_answer' => 'Jawaban keamanan tidak cocok. Klaim tidak dapat disetujui.',
                ]);
            }

            $lockedClaim->update([
                'status' => $action === 'approve' ? 'approved' : 'rejected',
                'handover_code' => $action === 'approve' ? $this->newHandoverCode() : null,
                'verified_by' => $verifier->id,
                'admin_note' => $action === 'reject' ? $note : null,
            ]);

            if ($action === 'reject'
                && ! $lockedClaim->item->claims()->whereIn('status', ['pending', 'approved'])->exists()) {
                $lockedClaim->item->update(['status' => 'approved']);
            }

            ActivityLog::create([
                'user_id' => $verifier->id,
                'action' => 'claim_'.$action,
                'description' => "Memproses klaim barang {$lockedClaim->item->title}: {$action}.",
            ]);

            return $lockedClaim;
        });

        $this->notifications->claimDecided($updated);

        return $updated;
    }

    private function newHandoverCode(): string
    {
        do {
            $code = 'LFP-'.strtoupper(Str::random(4)).'-'.strtoupper(Str::random(4));
        } while (Claim::withoutGlobalScope('campus')->where('handover_code', $code)->exists());

        return $code;
    }
}
