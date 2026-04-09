<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@medicalsystem.com'],
            [
                'name'      => 'System Administrator',
                'password'  => Hash::make('Admin@123456'),
                'specialty' => 'System Administration',
                'is_active' => true,
            ]
        );

        // Keep this user as full-access account on every seed execution.
        $admin->syncRoles(['admin']);
        $admin->syncPermissions(Permission::all());

        // Demo doctor
        $doctor = User::firstOrCreate(
            ['email' => 'doctor@medicalsystem.com'],
            [
                'name'      => 'Dr. John Smith',
                'password'  => Hash::make('Doctor@123456'),
                'phone'     => '+1-555-0001',
                'specialty' => 'General Medicine',
                'is_active' => true,
            ]
        );

        $doctor->assignRole('doctor');

        // Demo receptionist
        $receptionist = User::firstOrCreate(
            ['email' => 'receptionist@medicalsystem.com'],
            [
                'name'      => 'Jane Doe',
                'password'  => Hash::make('Recep@123456'),
                'phone'     => '+1-555-0002',
                'is_active' => true,
            ]
        );

        $receptionist->assignRole('receptionist');

        $this->command->info('Demo users seeded:');
        $this->command->line('  Admin:        admin@medicalsystem.com / Admin@123456');
        $this->command->line('  Doctor:       doctor@medicalsystem.com / Doctor@123456');
        $this->command->line('  Receptionist: receptionist@medicalsystem.com / Recep@123456');
    }
}
