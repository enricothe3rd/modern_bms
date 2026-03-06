<?php

return [

    'links' => [
        [
            'label' => 'Dashboard',
            'icon' => 'home',
            'route' => 'dashboard',
        ],
        [
            'label' => 'Reports',
            'icon' => 'document-chart-bar',
            'children' => [
                [
                    'label' => 'LBP Form 1',
                    'route' => 'reports.lbpf1',
                ],
                [
                    'label' => 'LBP Form 2',
                    // fallback
                ],
            ],
        ],
        [
            'label' => 'Obligation Requests',
            'icon' => 'document-text',
            'route' => 'obligation-requests.index',
        ],
        [
            'label' => 'Supplemental Budgets',
            'icon' => 'currency-dollar',
            'route' => 'supplemental-budgets.index',
        ],
        [
            'label' => 'Budget Realignments',
            'icon' => 'arrows-right-left',
            'route' => 'budget-realignments.index',
        ],
        [
            'label' => 'Statements of Indebtedness',
            'icon' => 'document-text',
            'route' => 'statements-of-indebtedness.index',
        ],
        [
            'label' => 'Funding Sources',
            'icon' => 'document-text',
            'route' => 'statements-of-funding-sources.index',
        ],
        [
            'label' => 'Statutory Obligations',
            'icon' => 'document-text',
            'route' => 'statements-of-statutory-obligations.index',
        ],
        [
            'label' => 'Management',
            'icon' => 'cog-6-tooth',
            'route' => 'management.index',
        ],
        [
            'label' => 'Settings',
            'icon' => 'adjustments-horizontal',
            'children' => [
                [
                    'label' => 'Profile Settings',
                    'route' => 'profile.edit',
                ],
                [
                    'label' => 'Role Permissions',
                    'route' => 'role-permissions.index',
                ],
            ],
        ],
        // [
        //     'label' => 'Departments',
        //     'icon' => 'icons/24/outline/academic-cap.svg',
        //     'route' => 'departments.index',
        // ],
        // [
        //     'label' => 'Accounts',
        //     'icon' => 'icons/24/outline/academic-cap.svg',
        //     'route' => 'accounts.index',
        // ],
        // [
        //     'label' => 'Sub Accounts',
        //     'icon' => 'icons/24/outline/academic-cap.svg',
        //     'route' => 'sub-accounts.index',
        // ],
        // [
        //     'label' => 'Expense Types',
        //     'icon' => 'icons/24/outline/academic-cap.svg',
        //     'route' => 'expense-types.index',
        // ],
        // [
        //     'label' => 'Roles',
        //     'icon' => 'icons/24/outline/academic-cap.svg',
        //     'route' => 'roles.index',
        // ],
        // [
        //     'label' => 'Users',
        //     'icon' => 'icons/24/outline/academic-cap.svg',
        //     'route' => 'users.index',
        // ],
        // [
        //     'label' => 'Fund Types',
        //     'icon' => 'icons/24/outline/academic-cap.svg',
        //     'route' => 'fund-types.index',
        // ],
        // [
        //     'label' => 'Forms',
        //     'icon' => 'icons/24/outline/academic-cap.svg',
        //     'route' => 'forms.index',
        // ],
        // [
        //     'label' => 'Form Signatories',
        //     'icon' => 'icons/24/outline/academic-cap.svg',
        //     'route' => 'form-signatories.index',
        // ],
        // [
        //     'label' => 'User Department Assignments',
        //     'icon' => 'icons/24/outline/academic-cap.svg',
        //     'route' => 'user-department-assignments.index',
        // ],
        // [
        //     'label' => 'Goals',
        //     'icon' => 'icons/24/outline/academic-cap.svg',
        //     'route' => 'obligationRequests.index',
        // ],
        // [
        //     'label' => 'Setup',
        //     'icon' => 'icons/24/outline/academic-cap.svg',
        //     'route' => 'setUp.index',
        // ],
    ],

    'footer_links' => [
        [
            'label' => 'Profile',
            'icon' => 'user-circle',
            'route' => 'profile.edit',
        ],
        [
            'label' => 'Help & Support',
            'icon' => 'question-mark-circle',
            'href' => '#',
        ],
        [
            'label' => 'Logout',
            'icon' => 'arrow-right-on-rectangle',
            'action' => 'logout',
        ],
    ],

];
