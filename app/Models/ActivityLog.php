<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCampus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    use BelongsToCampus;

    protected $fillable = ['user_id', 'action', 'description', 'ip_address'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
