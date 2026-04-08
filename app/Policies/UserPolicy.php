<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $authUser): bool
    {
        return $authUser->hasAnyRole(['admin', 'receptionist']);
    }

    public function view(User $authUser, User $user): bool
    {
        return $authUser->hasRole('admin') || $authUser->id === $user->id;
    }

    public function create(User $authUser): bool
    {
        return $authUser->hasRole('admin');
    }

    public function update(User $authUser, User $user): bool
    {
        return $authUser->hasRole('admin') || $authUser->id === $user->id;
    }

    public function delete(User $authUser, User $user): bool
    {
        return $authUser->hasRole('admin') && $authUser->id !== $user->id;
    }

    public function assignRole(User $authUser): bool
    {
        return $authUser->hasRole('admin');
    }
}
