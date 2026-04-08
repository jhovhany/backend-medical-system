<?php

namespace App\Providers;

use App\Models\Appointment;
use App\Models\Consultation;
use App\Models\File;
use App\Models\MedicalRecord;
use App\Models\Patient;
use App\Models\Prescription;
use App\Models\User;
use App\Policies\AppointmentPolicy;
use App\Policies\ConsultationPolicy;
use App\Policies\FilePolicy;
use App\Policies\MedicalRecordPolicy;
use App\Policies\PatientPolicy;
use App\Policies\PrescriptionPolicy;
use App\Policies\UserPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Register policies explicitly
        Gate::policy(User::class, UserPolicy::class);
        Gate::policy(Patient::class, PatientPolicy::class);
        Gate::policy(MedicalRecord::class, MedicalRecordPolicy::class);
        Gate::policy(Consultation::class, ConsultationPolicy::class);
        Gate::policy(Prescription::class, PrescriptionPolicy::class);
        Gate::policy(Appointment::class, AppointmentPolicy::class);
        Gate::policy(File::class, FilePolicy::class);

        // Admins bypass all policies
        Gate::before(function (User $user, string $ability) {
            if ($user->hasRole('admin')) {
                return true;
            }
        });
    }
}
