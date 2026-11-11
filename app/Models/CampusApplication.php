<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CampusApplication extends Model
{
    protected $fillable = [
        'name',
        'email',
        'slug',
        'status',
        'campus_id',
        'reviewed_by',
        'reviewed_at',
        'invitation_token_hash',
        'invitation_expires_at',
        'admin_created_at',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
        'invitation_expires_at' => 'datetime',
        'admin_created_at' => 'datetime',
    ];

    protected $hidden = ['invitation_token_hash'];

    public function campus(): BelongsTo
    {
        return $this->belongsTo(Campus::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
