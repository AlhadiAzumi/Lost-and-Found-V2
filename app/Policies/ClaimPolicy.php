<?php

namespace App\Policies;

use App\Models\Claim;
use App\Models\Item;
use App\Models\User;

class ClaimPolicy
{
    public function create(User $user, Item $item): bool
    {
        return $item->type === 'found'
            && $item->status === 'approved'
            && $item->user_id !== $user->id
            && ! $item->claims()->whereIn('status', ['pending', 'approved'])->exists();
    }

    public function view(User $user, Claim $claim): bool
    {
        return $claim->user_id === $user->id
            || in_array($user->role, ['admin', 'satpam'], true);
    }

    public function verify(User $user, Claim $claim): bool
    {
        return in_array($user->role, ['admin', 'satpam'], true)
            && $claim->status === 'pending';
    }
}
