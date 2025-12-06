<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sectors = \App\Models\Sector::all();
        
        if ($sectors->isEmpty()) {
            // Create some sectors if none exist
            $sectors = collect([
                \App\Models\Sector::create(['code' => 'ADMIN', 'name' => 'Administration']),
                \App\Models\Sector::create(['code' => 'TECH', 'name' => 'Technology']),
                \App\Models\Sector::create(['code' => 'BUS', 'name' => 'Business']),
                \App\Models\Sector::create(['code' => 'OPS', 'name' => 'Operations']),
            ]);
        }

        $departments = [
            // Administration Departments
            ['code' => 'HR', 'name' => 'Human Resources'],
            ['code' => 'FIN', 'name' => 'Finance'],
            ['code' => 'ACC', 'name' => 'Accounting'],
            ['code' => 'LEG', 'name' => 'Legal'],
            ['code' => 'ADM', 'name' => 'Administration'],
            ['code' => 'EXE', 'name' => 'Executive'],
            ['code' => 'COM', 'name' => 'Compliance'],
            ['code' => 'AUD', 'name' => 'Internal Audit'],
            ['code' => 'PAY', 'name' => 'Payroll'],
            ['code' => 'BEN', 'name' => 'Benefits'],
            ['code' => 'REC', 'name' => 'Recruitment'],
            ['code' => 'TRA', 'name' => 'Training & Development'],
            ['code' => 'REL', 'name' => 'Employee Relations'],
            
            // Technology Departments
            ['code' => 'IT', 'name' => 'Information Technology'],
            ['code' => 'DEV', 'name' => 'Software Development'],
            ['code' => 'QA', 'name' => 'Quality Assurance'],
            ['code' => 'NET', 'name' => 'Network Administration'],
            ['code' => 'SEC', 'name' => 'Information Security'],
            ['code' => 'DBA', 'name' => 'Database Administration'],
            ['code' => 'SYS', 'name' => 'Systems Administration'],
            ['code' => 'TSUP', 'name' => 'Technical Support'],
            ['code' => 'WEB', 'name' => 'Web Development'],
            ['code' => 'MOB', 'name' => 'Mobile Development'],
            ['code' => 'UX', 'name' => 'User Experience'],
            ['code' => 'UI', 'name' => 'User Interface'],
            ['code' => 'DATA', 'name' => 'Data Analytics'],
            ['code' => 'AI', 'name' => 'Artificial Intelligence'],
            
            // Business Departments
            ['code' => 'SAL', 'name' => 'Sales'],
            ['code' => 'MKT', 'name' => 'Marketing'],
            ['code' => 'CUS', 'name' => 'Customer Service'],
            ['code' => 'BDEV', 'name' => 'Business Development'],
            ['code' => 'STR', 'name' => 'Strategic Planning'],
            ['code' => 'PRO', 'name' => 'Product Management'],
            ['code' => 'PUB', 'name' => 'Public Relations'],
            ['code' => 'ADV', 'name' => 'Advertising'],
            ['code' => 'DIG', 'name' => 'Digital Marketing'],
            ['code' => 'CON', 'name' => 'Content Marketing'],
            ['code' => 'SOC', 'name' => 'Social Media'],
            ['code' => 'EVE', 'name' => 'Events & Conferences'],
            ['code' => 'PAR', 'name' => 'Partnerships'],
            
            // Operations Departments
            ['code' => 'OPS', 'name' => 'Operations'],
            ['code' => 'LOG', 'name' => 'Logistics'],
            ['code' => 'SUP', 'name' => 'Supply Chain'],
            ['code' => 'WAR', 'name' => 'Warehouse'],
            ['code' => 'SHP', 'name' => 'Shipping'],
            ['code' => 'RECV', 'name' => 'Receiving'],
            ['code' => 'INV', 'name' => 'Inventory Management'],
            ['code' => 'QUA', 'name' => 'Quality Control'],
            ['code' => 'MAN', 'name' => 'Manufacturing'],
            ['code' => 'PRD', 'name' => 'Production'],
            ['code' => 'MAI', 'name' => 'Maintenance'],
            ['code' => 'FAC', 'name' => 'Facilities'],
            ['code' => 'ENV', 'name' => 'Environmental'],
            ['code' => 'SAF', 'name' => 'Safety & Health'],
            
            // Additional Specialized Departments
            ['code' => 'RND', 'name' => 'Research & Development'],
            ['code' => 'INN', 'name' => 'Innovation'],
            ['code' => 'PMO', 'name' => 'Project Management Office'],
            ['code' => 'CHG', 'name' => 'Change Management'],
            ['code' => 'RIS', 'name' => 'Risk Management'],
            ['code' => 'GOV', 'name' => 'Corporate Governance'],
            ['code' => 'CSR', 'name' => 'Corporate Social Responsibility'],
            ['code' => 'SUS', 'name' => 'Sustainability'],
            ['code' => 'DIV', 'name' => 'Diversity & Inclusion'],
            ['code' => 'INTL', 'name' => 'International Affairs'],
            ['code' => 'REG', 'name' => 'Regulatory Affairs'],
            ['code' => 'GREL', 'name' => 'Government Relations'],
        ];

        foreach ($departments as $index => $dept) {
            // Distribute departments across sectors
            $sectorIndex = $index % $sectors->count();
            $sector = $sectors->get($sectorIndex);
            
            \App\Models\Department::create([
                'code' => $dept['code'],
                'name' => $dept['name'],
                'sector_id' => $sector->id,
            ]);
        }
    }
}
