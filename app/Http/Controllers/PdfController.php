<?php

namespace App\Http\Controllers;

use App\Services\PdfService;
use App\Services\PdfTemplateBuilder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PdfController extends Controller
{
    /**
     * Generate PDF using template
     */
    public function generate(Request $request, string $template)
    {
        try {
            $data = $request->all();
            $action = $request->get('action', 'inline'); // preview, inline, download, save
            
            $pdf = PdfTemplateBuilder::create($template, $data);
            
            switch ($action) {
                case 'preview':
                    // Return HTML preview
                    return response($pdf->preview())
                        ->header('Content-Type', 'text/html');
                    
                case 'download':
                    $filename = $request->get('filename', $template . '.pdf');
                    return $pdf->download($filename);
                    
                case 'save':
                    $filename = $request->get('filename', $template . '_' . time() . '.pdf');
                    $path = $pdf->save($filename);
                    return response()->json(['path' => $path, 'message' => 'PDF saved successfully']);
                    
                case 'inline':
                default:
                    // Show PDF in browser (viewable)
                    $filename = $request->get('filename', $template . '.pdf');
                    return $pdf->inline($filename);
            }
            
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Generate custom PDF
     */
    public function custom(Request $request)
    {
        $request->validate([
            'template' => 'required|string',
            'data' => 'array'
        ]);

        try {
            $pdf = PdfService::make()
                ->template($request->template, $request->get('data', []))
                ->configure($request->get('config', []));

            if ($request->has('css')) {
                $pdf->css($request->css);
            }

            if ($request->has('watermark')) {
                $watermark = $request->watermark;
                $pdf->watermark($watermark['text'], $watermark);
            }

            $action = $request->get('action', 'inline');
            
            switch ($action) {
                case 'preview':
                    return response($pdf->preview())
                        ->header('Content-Type', 'text/html');
                case 'download':
                    return $pdf->download($request->get('filename', 'document.pdf'));
                case 'save':
                    $path = $pdf->save($request->get('filename', 'document_' . time() . '.pdf'));
                    return response()->json(['path' => $path]);
                case 'inline':
                default:
                    return $pdf->inline($request->get('filename', 'document.pdf'));
            }
            
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * List available templates
     */
    public function templates()
    {
        return response()->json([
            'templates' => PdfTemplateBuilder::available()
        ]);
    }

    /**
     * Preview template (HTML version)
     */
    public function preview(Request $request, string $template)
    {
        try {
            $data = $request->all();
            $templateConfig = PdfTemplateBuilder::getTemplate($template);
            
            if (empty($templateConfig) || !isset($templateConfig['view'])) {
                return response()->json(['error' => 'Template not found'], 404);
            }

            return view($templateConfig['view'], $data);
            
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}