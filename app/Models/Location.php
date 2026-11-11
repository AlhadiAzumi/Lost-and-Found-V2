<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCampus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Location extends Model
{
    use BelongsToCampus;

    protected $fillable = ['name'];

    public function items(): HasMany
    {
        return $this->hasMany(Item::class);
    }
}
