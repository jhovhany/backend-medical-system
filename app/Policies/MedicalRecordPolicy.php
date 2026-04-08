<?php

namespace App\Policies;

use App\Models\MedicalRecord;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class MedicalRecordPolicy
{
    use HandlesAuthorization;

    public function view(User $user, MedicalRecord $medicalRecord): bool
    {
        return $user->hasAnyRole(['admin', 'doctor', 'receptionist']);
    }

    public function update(User $user, MedicalRecord $medicalRecord): bool
    {
        return $user->hasAnyRole(['admin', 'doctor']);
    }
}
