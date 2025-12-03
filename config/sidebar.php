<?php

return [

    'links' => [
        [
            'label' => 'Dashboard',
            'icon' => 'icons/24/outline/academic-cap.svg',
            'route' => 'dashboard',
        ],
        [
            'label' => 'Reports',
            'icon' => 'icons/24/outline/academic-cap.svg',
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
            'label' => 'Settings',
            'icon' => 'icons/24/outline/academic-cap.svg',
            'route' => 'reports',
        ],
        [
            'label' => 'Departments',
            'icon' => 'icons/24/outline/academic-cap.svg',
            'route' => 'departments.index',
        ],
        [
            'label' => 'Goals',
            'icon' => 'icons/24/outline/academic-cap.svg',
            'route' => 'obligationRequests',
        ],
        [
            'label' => 'Setup',
            'icon' => 'icons/24/outline/academic-cap.svg',
            'route' => 'setpUp',
        ],
    ],

    'footer_links' => [
        [
            'label' => 'Help',
            'icon' => 'icons/24/outline/academic-cap.svg',
            // fallback
        ],
        [
            'label' => 'Logout',
            'icon' => 'icons/24/outline/academic-cap.svg',
            'route' => 'logout',
        ],
    ],

];
