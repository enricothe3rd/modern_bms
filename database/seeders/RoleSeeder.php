<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'Admin',
                'description' => 'Full system access with all administrative privileges'
            ],
            [
                'name' => 'Manager',
                'description' => 'Department management and budget oversight responsibilities'
            ],
            [
                'name' => 'Budget Officer',
                'description' => 'Budget preparation, monitoring, and financial reporting'
            ],
            [
                'name' => 'Budget Officer Head',
                'description' => 'Senior budget officer with supervisory responsibilities and advanced budget management authority'
            ],
            [
                'name' => 'Accountant',
                'description' => 'Financial record keeping and accounting operations'
            ],
            [
                'name' => 'User',
                'description' => 'Basic user access with limited permissions'
            ],
            [
                'name' => 'Viewer',
                'description' => 'Read-only access to reports and data'
            ],
            [
                'name' => 'Auditor',
                'description' => 'Audit and compliance review access'
            ]
        ];

        foreach ($roles as $role) {
            \App\Models\Role::create($role);
        }
    }
}
