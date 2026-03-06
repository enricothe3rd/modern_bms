<?php

namespace App\Services;

class PdfTemplateBuilder
{
    protected $templates = [];

    /**
     * Register a new PDF template
     */
    public static function register(string $name, array $config): void
    {
        config(['pdf.templates.' . $name => $config]);
    }

    /**
     * Get template configuration
     */
    public static function getTemplate(string $name): array
    {
        return config('pdf.templates.' . $name, []);
    }

    /**
     * Create PDF service with template
     */
    public static function create(string $templateName, array $data = []): PdfService
    {
        $template = static::getTemplate($templateName);
        
        if (empty($template)) {
            throw new \InvalidArgumentException("Template '{$templateName}' not found");
        }

        $service = PdfService::make();

        // Apply template configuration
        if (isset($template['config'])) {
            $service->configure($template['config']);
        }

        // Add template view
        if (isset($template['view'])) {
            $service->template($template['view'], $data);
        }

        // Add custom CSS
        if (isset($template['css'])) {
            $service->css($template['css']);
        }

        // Add watermark
        if (isset($template['watermark'])) {
            $service->watermark($template['watermark']['text'], $template['watermark']);
        }

        return $service;
    }

    /**
     * List all available templates
     */
    public static function available(): array
    {
        return array_keys(config('pdf.templates', []));
    }
}