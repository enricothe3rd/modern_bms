<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use App\Models\Department;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Create Super Admin role
        $superAdminRole = Role::updateOrCreate(
            ['name' => 'Super Admin'],
            [
                'name' => 'Super Admin',
                'description' => 'Super Administrator with full system access. Has all permissions without explicit assignment.'
            ]
        );

        // Get or create a department for the super admin
        $department = Department::first();
        if (!$department) {
            $department = Department::create([
                'code' => 'ADMIN',
                'name' => 'Administration',
                'sector_id' => null
            ]);
        }

        // Create Super Admin user
        $superAdmin = User::updateOrCreate(
            ['email' => 'superadmin@example.com'],
            [
                'name' => 'Super Administrator',
                'email' => 'superadmin@example.com',
                'password' => Hash::make('password'),
                'role_id' => $superAdminRole->id,
                'department_id' => $department->id,
                'phone' => '+1 (555) 000-0001',
                'bio' => 'Super Administrator with full system access and all permissions.',
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('Super Admin role and user created successfully!');
        $this->command->info('Email: superadmin@example.com');
        $this->command->info('Password: password');
        $this->command->info('Role: Super Admin (Full Access)');
    }
}