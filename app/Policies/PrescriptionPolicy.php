<?php

namespace App\Policies;

use App\Models\Prescription;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PrescriptionPolicy
{
    use HandlesAuthorization;

    public function view(User $user, Prescription $prescription): bool
    {
        return $user->hasAnyRole(['admin', 'doctor', 'receptionist']);
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'doctor']);
    }

    public function update(User $user, Prescription $prescription): bool
    {
        return $user->hasRole('admin') || $user->id === $prescription->issued_by;
    }

    public function delete(User $user, Prescription $prescription): bool
    {
        return $user->hasRole('admin');
    }
}
