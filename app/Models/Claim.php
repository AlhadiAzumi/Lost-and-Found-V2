<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCampus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Claim extends Model
{
    use BelongsToCampus;

    private ?bool $securityAnswerMatch = null;

    private bool $securityAnswerMatchResolved = false;

    protected $fillable = [
        'item_id',
        'user_id',
        'security_answer',
        'proof_image',
        'status',
        'handover_code',
        'verified_by',
        'admin_note',
    ];

    protected $hidden = ['security_answer', 'proof_image', 'handover_code'];

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function handover(): HasOne
    {
        return $this->hasOne(Handover::class);
    }

    public function securityAnswerMatches(): ?bool
    {
        if (! $this->securityAnswerMatchResolved) {
            $this->securityAnswerMatch = $this->item?->matchesSecurityAnswer((string) $this->security_answer);
            $this->securityAnswerMatchResolved = true;
        }

        return $this->securityAnswerMatch;
    }
}
