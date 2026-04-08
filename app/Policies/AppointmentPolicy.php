<?php

namespace App\Policies;

use App\Models\Appointment;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AppointmentPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'doctor', 'receptionist']);
    }

    public function view(User $user, Appointment $appointment): bool
    {
        return $user->hasAnyRole(['admin', 'doctor', 'receptionist']);
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'receptionist', 'doctor']);
    }

    public function update(User $user, Appointment $appointment): bool
    {
        return $user->hasAnyRole(['admin', 'receptionist'])
            || $user->id === $appointment->doctor_id;
    }

    public function delete(User $user, Appointment $appointment): bool
    {
        return $user->hasAnyRole(['admin', 'receptionist']);
    }
}
