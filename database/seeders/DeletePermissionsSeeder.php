<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\RolePermission;

class DeletePermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get Super Admin role
        $superAdminRole = Role::where('name', 'Super Admin')->first();
        
        if ($superAdminRole) {
            // Define new delete permissions
            $deletePermissions = [
                'delete_departments' => 'Delete Departments and Sectors',
                'delete_accounts' => 'Delete Chart of Accounts',
                'delete_sub_accounts' => 'Delete Sub-Account Classifications',
                'delete_roles' => 'Delete User Roles',
                'delete_users' => 'Delete System Users',
                'delete_fund_types' => 'Delete Fund Type Classifications',
                'delete_forms' => 'Delete Form Templates',
                'delete_form_signatories' => 'Delete Form Signatories',
                'delete_user_assignments' => 'Delete User Department Assignments',
            ];

            // Add delete permissions to Super Admin
            foreach ($deletePermissions as $permission => $description) {
                // Check if permission already exists
                $existingPermission = RolePermission::where('role_id', $superAdminRole->id)
                    ->where('permission_name', $permission)
                    ->first();

                if (!$existingPermission) {
                    RolePermission::create([
                        'role_id' => $superAdminRole->id,
                        'permission_name' => $permission,
                        'permission_description' => $description
                    ]);
                }
            }

            $this->command->info('Delete permissions added to Super Admin role successfully!');
        } else {
            $this->command->error('Super Admin role not found. Please create it first.');
        }

        // Optionally add delete permissions to other roles
        $adminRole = Role::where('name', 'Admin')->first();
        
        if ($adminRole) {
            // Add some delete permissions to Admin role (you can customize this)
            $adminDeletePermissions = [
                'delete_accounts' => 'Delete Chart of Accounts',
                'delete_sub_accounts' => 'Delete Sub-Account Classifications',
                'delete_fund_types' => 'Delete Fund Type Classifications',
                'delete_forms' => 'Delete Form Templates',
                'delete_form_signatories' => 'Delete Form Signatories',
            ];

            foreach ($adminDeletePermissions as $permission => $description) {
                $existingPermission = RolePermission::where('role_id', $adminRole->id)
                    ->where('permission_name', $permission)
                    ->first();

                if (!$existingPermission) {
                    RolePermission::create([
                        'role_id' => $adminRole->id,
                        'permission_name' => $permission,
                        'permission_description' => $description
                    ]);
                }
            }

            $this->command->info('Delete permissions added to Admin role successfully!');
        }
    }
}