<?php

namespace App\Models;

use App\Models\Concerns\BelongsToCampus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use BelongsToCampus;
    use HasFactory;
    use Notifiable;

    protected $fillable = ['name', 'email', 'nim_nip', 'phone_number', 'password', 'role', 'avatar'];

    protected $hidden = ['password', 'remember_token'];

    public function items(): HasMany
    {
        return $this->hasMany(Item::class);
    }

    public function claims(): HasMany
    {
        return $this->hasMany(Claim::class);
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    public function unreadNotifications(): HasMany
    {
        return $this->notifications()->whereNull('read_at');
    }
}
