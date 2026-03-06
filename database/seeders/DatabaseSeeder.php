<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed sectors first
        $this->call(SectorSeeder::class);
        
        // Seed departments (66 departments)
        $this->call(DepartmentSeeder::class);
        
        // Seed roles
        $this->call(RoleSeeder::class);

        // Seed super admin role and user
        $this->call(SuperAdminSeeder::class);
        
        // Seed users with roles and departments
        $this->call(UserSeeder::class);

        // Seed payee categories
        $this->call(PayeeCategorySeeder::class);

        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => \Illuminate\Support\Facades\Hash::make('123456'), // Default password: 123456
        ]);
    }
}
