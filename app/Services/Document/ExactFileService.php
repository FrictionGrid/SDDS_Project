<?php

namespace App\Services\Document;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class ExactFileService
{
    /**
     * Extract text from stored file path (DocumentAI->disk + DocumentAI->path)
     */
    public function extractFromStorage(string $disk, string $path, ?string $mimeType = null): string
    {
        if (!Storage::disk($disk)->exists($path)) {
            throw new RuntimeException("File not found on disk={$disk}, path={$path}");
        }

        $fullPath = Storage::disk($disk)->path($path);

        $mime = $mimeType ?: (function () use ($fullPath) {
            $detected = @mime_content_type($fullPath);
            return is_string($detected) ? $detected : null;
        })();

        return $this->extractFromLocalPath($fullPath, $mime);
    }

    /**
     * Extract from uploaded file (not saved yet)
     */
    public function extractFromUploadedFile(UploadedFile $file): string
    {
        $mime = $file->getMimeType();
        return $this->extractFromLocalPath($file->getRealPath(), $mime);
    }

    private function extractFromLocalPath(string $fullPath, ?string $mimeType): string
    {
        $mime = strtolower((string) $mimeType);
        $ext = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));

        // PDF
        if ($mime === 'application/pdf' || $ext === 'pdf') {
            return $this->cleanText($this->extractPdf($fullPath));
        }

        // DOCX
        if (
            $mime === 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
            || $ext === 'docx'
        ) {
            return $this->cleanText($this->extractDocx($fullPath));
        }

        // TXT / MD / JSON (or fallback text)
        if ($ext === 'txt' || $ext === 'md' || str_contains($mime, 'text/')) {
            return $this->cleanText($this->extractPlainText($fullPath));
        }

        if ($mime === 'application/json' || $ext === 'json') {
            return $this->cleanText($this->extractJsonAsText($fullPath));
        }

        // Fallback: try read as text
        return $this->cleanText($this->extractPlainText($fullPath));
    }

    private function extractPdf(string $fullPath): string
    {
        // requires: composer require smalot/pdfparser
        if (!class_exists(\Smalot\PdfParser\Parser::class)) {
            throw new RuntimeException("Missing dependency: smalot/pdfparser");
        }

        $parser = new \Smalot\PdfParser\Parser();
        $pdf = $parser->parseFile($fullPath);
        $text = $pdf->getText();

        return is_string($text) ? $text : '';
    }

    private function extractDocx(string $fullPath): string
    {
        // requires: composer require phpoffice/phpword
        if (!class_exists(\PhpOffice\PhpWord\IOFactory::class)) {
            throw new RuntimeException("Missing dependency: phpoffice/phpword");
        }

        $phpWord = \PhpOffice\PhpWord\IOFactory::load($fullPath);
        $text = '';

        foreach ($phpWord->getSections() as $section) {
            $elements = $section->getElements();
            foreach ($elements as $el) {
                // best-effort text extraction
                if (method_exists($el, 'getText')) {
                    $text .= (string) $el->getText() . "\n";
                } elseif (method_exists($el, 'getElements')) {
                    foreach ($el->getElements() as $child) {
                        if (method_exists($child, 'getText')) {
                            $text .= (string) $child->getText() . "\n";
                        }
                    }
                }
            }
        }

        return $text;
    }

    private function extractPlainText(string $fullPath): string
    {
        $raw = @file_get_contents($fullPath);
        if ($raw === false) {
            throw new RuntimeException("Unable to read file: {$fullPath}");
        }

        // Force UTF-8 best effort
        if (!mb_check_encoding($raw, 'UTF-8')) {
            $raw = mb_convert_encoding($raw, 'UTF-8', 'auto');
        }

        return (string) $raw;
    }

    private function extractJsonAsText(string $fullPath): string
    {
        $raw = $this->extractPlainText($fullPath);

        $data = json_decode($raw, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            // If JSON invalid, still return raw
            return $raw;
        }

        $lines = [];
        $this->flattenJson($data, '', $lines);

        // Each line "path: value" helps retrieval
        return implode("\n", $lines);
    }

    private function flattenJson(mixed $data, string $prefix, array &$lines): void
    {
        if (is_array($data)) {
            $isAssoc = array_keys($data) !== range(0, count($data) - 1);

            foreach ($data as $k => $v) {
                $key = $isAssoc ? (string) $k : '[' . $k . ']';
                $newPrefix = $prefix === '' ? $key : ($prefix . '.' . $key);
                $this->flattenJson($v, $newPrefix, $lines);
            }
            return;
        }

        if (is_object($data)) {
            $this->flattenJson((array) $data, $prefix, $lines);
            return;
        }

        // scalar
        $value = is_bool($data) ? ($data ? 'true' : 'false') : (string) $data;
        $value = trim($value);
        if ($value === '') return;

        $lines[] = ($prefix !== '' ? $prefix . ': ' : '') . $value;
    }

    public function cleanText(string $text): string
    {
        // Remove null bytes
        $text = str_replace("\0", '', $text);

        // Normalize line endings
        $text = str_replace(["\r\n", "\r"], "\n", $text);

        // Collapse excessive spaces
        $text = preg_replace("/[ \t]+/u", " ", $text) ?? $text;

        // Collapse too many newlines
        $text = preg_replace("/\n{3,}/u", "\n\n", $text) ?? $text;

        return trim($text);
    }
}
