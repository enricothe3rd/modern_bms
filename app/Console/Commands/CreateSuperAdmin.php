<?php

namespace App\Console\Commands;

use App\Models\Role;
use App\Models\User;
use App\Models\Department;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CreateSuperAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:superadmin {--email=} {--password=} {--name=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a Super Admin user with full system access';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Creating Super Admin User...');

        // Get or create Super Admin role
        $superAdminRole = Role::firstOrCreate(
            ['name' => 'Super Admin'],
            [
                'name' => 'Super Admin',
                'description' => 'Super Administrator with full system access. Has all permissions without explicit assignment.'
            ]
        );

        // Get user details
        $name = $this->option('name') ?: $this->ask('Enter the super admin name', 'Super Administrator');
        $email = $this->option('email') ?: $this->ask('Enter the super admin email');
        $password = $this->option('password') ?: $this->secret('Enter the super admin password');

        // Validate input
        $validator = Validator::make([
            'name' => $name,
            'email' => $email,
            'password' => $password,
        ], [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
        ]);

        if ($validator->fails()) {
            $this->error('Validation failed:');
            foreach ($validator->errors()->all() as $error) {
                $this->error('- ' . $error);
            }
            return 1;
        }

        // Get or create admin department
        $department = Department::first();
        if (!$department) {
            $department = Department::create([
                'code' => 'ADMIN',
                'name' => 'Administration',
                'sector_id' => null
            ]);
            $this->info('Created Administration department');
        }

        // Create super admin user
        $superAdmin = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'role_id' => $superAdminRole->id,
            'department_id' => $department->id,
            'email_verified_at' => now(),
        ]);

        $this->info('✅ Super Admin user created successfully!');
        $this->table(['Field', 'Value'], [
            ['Name', $superAdmin->name],
            ['Email', $superAdmin->email],
            ['Role', $superAdmin->role->name],
            ['Department', $superAdmin->department->name],
            ['Access Level', 'Full System Access'],
        ]);

        $this->warn('⚠️  Please keep these credentials secure!');
        
        return 0;
    }
}