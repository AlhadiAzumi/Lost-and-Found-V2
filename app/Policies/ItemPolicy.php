<?php

namespace App\Policies;

use App\Models\Item;
use App\Models\User;

class ItemPolicy
{
    public function view(User $user, Item $item): bool
    {
        return $item->status !== 'pending'
            && $item->status !== 'rejected'
            || $user->id === $item->user_id
            || in_array($user->role, ['admin', 'satpam'], true);
    }

    public function update(User $user, Item $item): bool
    {
        return $item->user_id === $user->id
            && in_array($item->status, ['pending', 'rejected'], true);
    }

    public function close(User $user, Item $item): bool
    {
        return $item->user_id === $user->id
            && $item->type === 'lost'
            && in_array($item->status, ['pending', 'approved'], true);
    }

    public function moderate(User $user): bool
    {
        return in_array($user->role, ['admin', 'satpam'], true);
    }
}
