<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default PDF Configuration
    |--------------------------------------------------------------------------
    */
    'default' => [
        'paper' => 'a4',
        'orientation' => 'portrait',
        'margin' => [
            'top' => 20,
            'right' => 15,
            'bottom' => 20,
            'left' => 15
        ],
        'font_size' => 12,
        'font_family' => 'Arial',
        'enable_php' => true,
        'enable_javascript' => false,
        'enable_remote' => true,
        'enable_html5_parser' => true
    ],

    /*
    |--------------------------------------------------------------------------
    | PDF Templates
    |--------------------------------------------------------------------------
    | Define reusable PDF templates here
    */
    'templates' => [
        'obligation_request' => [
            'view' => 'pdf.obligation-request',
            'config' => [
                'paper' => 'a4',
                'orientation' => 'portrait',
                'margin' => ['top' => 25, 'right' => 20, 'bottom' => 25, 'left' => 20]
            ],
            'css' => '
                .header { border-bottom: 2px solid #333; padding-bottom: 10px; }
                .signature-section { margin-top: 30px; }
                .signature-box { 
                    border: 1px solid #333; 
                    height: 60px; 
                    margin-top: 20px;
                    padding: 5px;
                }
            '
        ],
        
        'financial_report' => [
            'view' => 'pdf.financial-report',
            'config' => [
                'paper' => 'a4',
                'orientation' => 'landscape',
                'margin' => ['top' => 15, 'right' => 15, 'bottom' => 15, 'left' => 15]
            ],
            'watermark' => [
                'text' => 'CONFIDENTIAL',
                'opacity' => 0.1,
                'angle' => 45
            ]
        ],

        'invoice' => [
            'view' => 'pdf.invoice',
            'config' => [
                'paper' => 'a4',
                'orientation' => 'portrait'
            ],
            'css' => '
                .invoice-header { background-color: #f8f9fa; padding: 15px; }
                .invoice-total { background-color: #e9ecef; font-weight: bold; }
            '
        ],

        'receipt' => [
            'view' => 'pdf.receipt',
            'config' => [
                'paper' => 'a5',
                'orientation' => 'portrait',
                'font_size' => 10
            ]
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Storage Configuration
    |--------------------------------------------------------------------------
    */
    'storage' => [
        'disk' => 'local',
        'path' => 'pdfs'
    ]
];