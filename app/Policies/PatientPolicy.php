<?php

namespace App\Policies;

use App\Models\Patient;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PatientPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'doctor', 'receptionist']);
    }

    public function view(User $user, Patient $patient): bool
    {
        return $user->hasAnyRole(['admin', 'doctor', 'receptionist']);
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'receptionist']);
    }

    public function update(User $user, Patient $patient): bool
    {
        return $user->hasAnyRole(['admin', 'receptionist']);
    }

    public function delete(User $user, Patient $patient): bool
    {
        return $user->hasRole('admin');
    }
}
