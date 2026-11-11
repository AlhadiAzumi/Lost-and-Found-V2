<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\Claim;
use App\Models\Handover;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class HandoverService
{
    public function __construct(private NotificationService $notifications)
    {
    }

    public function complete(Claim $claim, User $officer, string $code, ?string $notes = null): Handover
    {
        $handover = DB::transaction(function () use ($claim, $officer, $code, $notes): Handover {
            $lockedClaim = Claim::query()->lockForUpdate()->with('item')->findOrFail($claim->id);

            if ($lockedClaim->status !== 'approved'
                || $lockedClaim->handover()->exists()
                || ! hash_equals((string) $lockedClaim->handover_code, trim($code))) {
                throw ValidationException::withMessages([
                    'code' => 'Kode serah terima tidak valid atau sudah pernah digunakan.',
                ]);
            }

            $handover = Handover::create([
                'claim_id' => $lockedClaim->id,
                'item_id' => $lockedClaim->item_id,
                'claimed_by' => $lockedClaim->user_id,
                'handed_over_by' => $officer->id,
                'handover_date' => now(),
                'notes' => $notes,
            ]);

            $lockedClaim->update(['status' => 'completed']);
            $lockedClaim->item->update(['status' => 'returned']);

            ActivityLog::create([
                'user_id' => $officer->id,
                'action' => 'handover_completed',
                'description' => "Mencatat serah terima barang {$lockedClaim->item->title}.",
            ]);

            return $handover;
        });

        $this->notifications->handoverCompleted($claim->fresh());

        return $handover;
    }
}
