<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use App\Models\Department;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserProfileSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Create a sample user with profile information
        $role = Role::first();
        $department = Department::first();

        if ($role && $department) {
            User::updateOrCreate(
                ['email' => 'admin@example.com'],
                [
                    'name' => 'System Administrator',
                    'email' => 'admin@example.com',
                    'password' => Hash::make('password'),
                    'role_id' => $role->id,
                    'department_id' => $department->id,
                    'phone' => '+1 (555) 123-4567',
                    'bio' => 'System administrator responsible for managing the budget application and user accounts.',
                    'email_verified_at' => now(),
                ]
            );
        }

        // Create additional sample users
        User::updateOrCreate(
            ['email' => 'user@example.com'],
            [
                'name' => 'John Doe',
                'email' => 'user@example.com',
                'password' => Hash::make('password'),
                'role_id' => $role?->id,
                'department_id' => $department?->id,
                'phone' => '+1 (555) 987-6543',
                'bio' => 'Budget analyst working on financial planning and expense tracking.',
                'email_verified_at' => now(),
            ]
        );
    }
}