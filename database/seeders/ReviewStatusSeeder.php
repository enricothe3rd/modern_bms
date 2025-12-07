<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ReviewStatus;

class ReviewStatusSeeder extends Seeder
{
    public function run(): void
    {
        $statuses = [
            [
                'name' => 'Draft',
                'code' => 'draft',
                'description' => 'Draft - not yet submitted',
                'color' => '#6B7280',
                'order' => 0,
                'is_active' => true,
            ],
            [
                'name' => 'Submitted',
                'code' => 'submitted',
                'description' => 'User submitted the obligation request',
                'color' => '#3B82F6',
                'order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Budget Staff Review',
                'code' => 'budget_staff_review',
                'description' => 'Budget Staff reviews and approves/rejects',
                'color' => '#F59E0B',
                'order' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Budget Staff Approved',
                'code' => 'budget_staff_approved',
                'description' => 'Approved by Budget Staff, pending Budget Officer review',
                'color' => '#10B981',
                'order' => 3,
                'is_active' => true,
            ],
            [
                'name' => 'Budget Officer Final Review',
                'code' => 'budget_officer_review',
                'description' => 'Budget Officer performs final review',
                'color' => '#8B5CF6',
                'order' => 4,
                'is_active' => true,
            ],
            [
                'name' => 'Final Approved',
                'code' => 'final_approved',
                'description' => 'Final approval by Budget Officer - Complete',
                'color' => '#059669',
                'order' => 5,
                'is_active' => true,
            ],
            [
                'name' => 'Rejected',
                'code' => 'rejected',
                'description' => 'Rejected by Budget Staff or Budget Officer',
                'color' => '#EF4444',
                'order' => 6,
                'is_active' => true,
            ],
        ];

        foreach ($statuses as $status) {
            ReviewStatus::create($status);
        }
    }
}