<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Define permissions per module
        $permissions = [
            // Users
            'users.view', 'users.create', 'users.update', 'users.delete', 'users.assign_role',
            // Patients
            'patients.view', 'patients.create', 'patients.update', 'patients.delete',
            // Medical Records
            'medical_records.view', 'medical_records.update',
            // Consultations
            'consultations.view', 'consultations.create', 'consultations.update', 'consultations.delete',
            // Prescriptions
            'prescriptions.view', 'prescriptions.create', 'prescriptions.update', 'prescriptions.delete',
            // Appointments
            'appointments.view', 'appointments.create', 'appointments.update', 'appointments.delete',
            // Files
            'files.view', 'files.upload', 'files.delete',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'api']);
        }

        // Admin — full access (bypassed by Gate::before in AppServiceProvider)
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'api']);
        $adminRole->syncPermissions(Permission::all());

        // Doctor
        $doctorRole = Role::firstOrCreate(['name' => 'doctor', 'guard_name' => 'api']);
        $doctorRole->syncPermissions([
            'patients.view',
            'medical_records.view', 'medical_records.update',
            'consultations.view', 'consultations.create', 'consultations.update',
            'prescriptions.view', 'prescriptions.create', 'prescriptions.update',
            'appointments.view', 'appointments.update',
            'files.view', 'files.upload',
        ]);

        // Receptionist
        $receptionistRole = Role::firstOrCreate(['name' => 'receptionist', 'guard_name' => 'api']);
        $receptionistRole->syncPermissions([
            'patients.view', 'patients.create', 'patients.update',
            'medical_records.view',
            'consultations.view', 'consultations.create',
            'appointments.view', 'appointments.create', 'appointments.update',
            'files.view', 'files.upload',
        ]);

        $this->command->info('Roles and permissions seeded successfully.');
    }
}
