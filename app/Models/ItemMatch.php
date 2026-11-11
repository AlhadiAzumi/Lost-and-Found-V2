<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCampus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ItemMatch extends Model
{
    use BelongsToCampus;

    protected $fillable = ['lost_item_id', 'found_item_id', 'match_score', 'status'];

    protected $casts = [
        'match_score' => 'float',
    ];

    public function lostItem(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'lost_item_id');
    }

    public function foundItem(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'found_item_id');
    }
}
