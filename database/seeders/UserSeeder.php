<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Default password for all users: 123456
        $defaultPassword = \Illuminate\Support\Facades\Hash::make('123456');
        
        // Get roles and departments
        $adminRole = \App\Models\Role::where('name', 'Admin')->first();
        $managerRole = \App\Models\Role::where('name', 'Manager')->first();
        $budgetHeadRole = \App\Models\Role::where('name', 'Budget Officer Head')->first();
        $budgetOfficerRole = \App\Models\Role::where('name', 'Budget Officer')->first();
        $userRole = \App\Models\Role::where('name', 'User')->first();
        
        $department1 = \App\Models\Department::first();
        $department2 = \App\Models\Department::skip(1)->first();
        $financeDept = \App\Models\Department::where('code', 'FIN')->first();

        // Create sample users - All users have default password: 123456
        \App\Models\User::create([
            'name' => 'System Administrator',
            'email' => 'admin@example.com',
            'password' => $defaultPassword,
            'role_id' => $adminRole?->id,
            'department_id' => $department1?->id,
        ]);

        \App\Models\User::create([
            'name' => 'Department Manager',
            'email' => 'manager@example.com',
            'password' => $defaultPassword,
            'role_id' => $managerRole?->id,
            'department_id' => $department1?->id,
        ]);

        \App\Models\User::create([
            'name' => 'Regular User',
            'email' => 'user@example.com',
            'password' => $defaultPassword,
            'role_id' => $userRole?->id,
            'department_id' => $department2?->id,
        ]);

        \App\Models\User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => $defaultPassword,
            'role_id' => $userRole?->id,
            'department_id' => $department1?->id,
        ]);

        \App\Models\User::create([
            'name' => 'Budget Head Manager',
            'email' => 'budgethead@example.com',
            'password' => $defaultPassword,
            'role_id' => $budgetHeadRole?->id,
            'department_id' => $financeDept?->id,
        ]);

        \App\Models\User::create([
            'name' => 'Senior Budget Officer',
            'email' => 'budgetofficer@example.com',
            'password' => $defaultPassword,
            'role_id' => $budgetOfficerRole?->id,
            'department_id' => $financeDept?->id,
        ]);
    }
}
