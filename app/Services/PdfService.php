<?php

namespace App\Services;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Storage;

class PdfService
{
    protected $config = [
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
    ];

    protected $templates = [];
    protected $data = [];
    protected $customCss = '';
    protected $watermark = null;

    /**
     * Set PDF configuration
     */
    public function configure(array $config): self
    {
        $this->config = array_merge($this->config, $config);
        return $this;
    }

    /**
     * Set template for PDF generation
     */
    public function template(string $template, array $data = []): self
    {
        $this->templates[] = [
            'view' => $template,
            'data' => $data
        ];
        return $this;
    }

    /**
     * Add data to be passed to all templates
     */
    public function with(array $data): self
    {
        $this->data = array_merge($this->data, $data);
        return $this;
    }

    /**
     * Add custom CSS
     */
    public function css(string $css): self
    {
        $this->customCss .= $css;
        return $this;
    }

    /**
     * Add watermark
     */
    public function watermark(string $text, array $options = []): self
    {
        $this->watermark = array_merge([
            'text' => $text,
            'opacity' => 0.1,
            'angle' => 45,
            'font_size' => 50,
            'color' => '#cccccc'
        ], $options);
        return $this;
    }

    /**
     * Generate PDF and return as response
     */
    public function generate(): \Illuminate\Http\Response
    {
        $html = $this->buildHtml();
        
        $pdf = Pdf::loadHTML($html);
        
        // Apply configuration
        $pdf->setPaper($this->config['paper'], $this->config['orientation']);
        
        // Set options
        $pdf->setOptions([
            'isPhpEnabled' => $this->config['enable_php'],
            'isJavascriptEnabled' => $this->config['enable_javascript'],
            'isRemoteEnabled' => $this->config['enable_remote'],
            'isHtml5ParserEnabled' => $this->config['enable_html5_parser'],
            'defaultFont' => $this->config['font_family'],
            'fontSize' => $this->config['font_size']
        ]);

        return $pdf->stream();
    }

    /**
     * Generate PDF and save to storage
     */
    public function save(string $filename, string $disk = 'local'): string
    {
        $html = $this->buildHtml();
        
        $pdf = Pdf::loadHTML($html);
        $pdf->setPaper($this->config['paper'], $this->config['orientation']);
        
        $pdfContent = $pdf->output();
        
        $path = 'pdfs/' . $filename;
        Storage::disk($disk)->put($path, $pdfContent);
        
        return $path;
    }

    /**
     * Generate PDF and return as download
     */
    public function download(string $filename = 'document.pdf'): \Illuminate\Http\Response
    {
        $html = $this->buildHtml();
        
        $pdf = Pdf::loadHTML($html);
        $pdf->setPaper($this->config['paper'], $this->config['orientation']);
        
        // Set options
        $pdf->setOptions([
            'isPhpEnabled' => $this->config['enable_php'],
            'isJavascriptEnabled' => $this->config['enable_javascript'],
            'isRemoteEnabled' => $this->config['enable_remote'],
            'isHtml5ParserEnabled' => $this->config['enable_html5_parser'],
            'defaultFont' => $this->config['font_family'],
            'fontSize' => $this->config['font_size']
        ]);
        
        return $pdf->download($filename);
    }

    /**
     * Generate HTML preview (for viewing before download)
     */
    public function preview(): string
    {
        return $this->buildHtml();
    }

    /**
     * Generate PDF and return inline (for browser viewing)
     */
    public function inline(string $filename = 'document.pdf'): \Illuminate\Http\Response
    {
        $html = $this->buildHtml();
        
        $pdf = Pdf::loadHTML($html);
        $pdf->setPaper($this->config['paper'], $this->config['orientation']);
        
        // Set options
        $pdf->setOptions([
            'isPhpEnabled' => $this->config['enable_php'],
            'isJavascriptEnabled' => $this->config['enable_javascript'],
            'isRemoteEnabled' => $this->config['enable_remote'],
            'isHtml5ParserEnabled' => $this->config['enable_html5_parser'],
            'defaultFont' => $this->config['font_family'],
            'fontSize' => $this->config['font_size']
        ]);
        
        return response($pdf->output(), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="' . $filename . '"');
    }

    /**
     * Build HTML from templates
     */
    protected function buildHtml(): string
    {
        $html = $this->getBaseHtml();
        
        $content = '';
        foreach ($this->templates as $template) {
            $templateData = array_merge($this->data, $template['data']);
            $content .= View::make($template['view'], $templateData)->render();
        }
        
        // Add watermark if set
        if ($this->watermark) {
            $content = $this->addWatermark($content);
        }
        
        return str_replace('{{CONTENT}}', $content, $html);
    }

    /**
     * Get base HTML structure
     */
    protected function getBaseHtml(): string
    {
        return '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8">
            <title>PDF Document</title>
            <style>
                ' . $this->getBaseCss() . '
                ' . $this->customCss . '
            </style>
        </head>
        <body>
            {{CONTENT}}
        </body>
        </html>';
    }

    /**
     * Get base CSS styles
     */
    protected function getBaseCss(): string
    {
        return '
        @page {
            margin: ' . $this->config['margin']['top'] . 'mm ' . 
                      $this->config['margin']['right'] . 'mm ' . 
                      $this->config['margin']['bottom'] . 'mm ' . 
                      $this->config['margin']['left'] . 'mm;
        }
        
        body {
            font-family: ' . $this->config['font_family'] . ', sans-serif;
            font-size: ' . $this->config['font_size'] . 'px;
            line-height: 1.4;
            color: #333;
        }
        
        .page-break {
            page-break-before: always;
        }
        
        .no-break {
            page-break-inside: avoid;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        th, td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        
        .font-bold { font-weight: bold; }
        .font-normal { font-weight: normal; }
        
        .text-sm { font-size: 0.875em; }
        .text-lg { font-size: 1.125em; }
        .text-xl { font-size: 1.25em; }
        .text-2xl { font-size: 1.5em; }
        
        .mb-1 { margin-bottom: 0.25rem; }
        .mb-2 { margin-bottom: 0.5rem; }
        .mb-3 { margin-bottom: 0.75rem; }
        .mb-4 { margin-bottom: 1rem; }
        .mb-5 { margin-bottom: 1.25rem; }
        
        .mt-1 { margin-top: 0.25rem; }
        .mt-2 { margin-top: 0.5rem; }
        .mt-3 { margin-top: 0.75rem; }
        .mt-4 { margin-top: 1rem; }
        .mt-5 { margin-top: 1.25rem; }
        
        .p-1 { padding: 0.25rem; }
        .p-2 { padding: 0.5rem; }
        .p-3 { padding: 0.75rem; }
        .p-4 { padding: 1rem; }
        
        .border { border: 1px solid #ddd; }
        .border-t { border-top: 1px solid #ddd; }
        .border-b { border-bottom: 1px solid #ddd; }
        .border-l { border-left: 1px solid #ddd; }
        .border-r { border-right: 1px solid #ddd; }
        
        .bg-gray-100 { background-color: #f7f7f7; }
        .bg-gray-200 { background-color: #e5e5e5; }
        
        /* Currency styling */
        .currency {
            font-weight: bold;
        }
        .currency:before {
            content: "₱ ";
        }
        
        /* Logo styling */
        .logo img {
            max-height: 80px;
            max-width: 200px;
            margin: 0 auto;
            display: block;
        }
        
        /* Signature box styling */
        .signature-box {
            height: 50px;
            border-bottom: 1px solid #333;
            margin-bottom: 10px;
        }
        ';
    }

    /**
     * Add watermark to content
     */
    protected function addWatermark(string $content): string
    {
        $watermarkStyle = '
        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(' . $this->watermark['angle'] . 'deg);
            font-size: ' . $this->watermark['font_size'] . 'px;
            color: ' . $this->watermark['color'] . ';
            opacity: ' . $this->watermark['opacity'] . ';
            z-index: -1;
            pointer-events: none;
            font-weight: bold;
            white-space: nowrap;
        }';
        
        $this->customCss .= $watermarkStyle;
        
        $watermarkHtml = '<div class="watermark">' . $this->watermark['text'] . '</div>';
        
        return $watermarkHtml . $content;
    }

    /**
     * Create a new instance for method chaining
     */
    public static function make(): self
    {
        return new static();
    }
}