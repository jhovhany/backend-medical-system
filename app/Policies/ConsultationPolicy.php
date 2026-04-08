<?php

namespace App\Policies;

use App\Models\Consultation;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ConsultationPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'doctor', 'receptionist']);
    }

    public function view(User $user, Consultation $consultation): bool
    {
        return $user->hasAnyRole(['admin', 'doctor', 'receptionist']);
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'doctor', 'receptionist']);
    }

    public function update(User $user, Consultation $consultation): bool
    {
        return $user->hasRole('admin') || $user->id === $consultation->doctor_id;
    }

    public function delete(User $user, Consultation $consultation): bool
    {
        return $user->hasRole('admin');
    }
}
