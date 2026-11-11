<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCampus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Handover extends Model
{
    use BelongsToCampus;

    protected $fillable = [
        'claim_id',
        'item_id',
        'claimed_by',
        'handed_over_by',
        'handover_date',
        'proof_photo',
        'notes',
    ];

    protected $casts = [
        'handover_date' => 'datetime',
    ];

    public function claim(): BelongsTo
    {
        return $this->belongsTo(Claim::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function recipient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'claimed_by');
    }

    public function officer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'handed_over_by');
    }
}
