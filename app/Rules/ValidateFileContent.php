<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\Rule;

class ValidateFileContent implements Rule
{
    private ?string $errorMessage = null;
    private array $allowedMimes = [
        'application/pdf',
        'image/jpeg',
        'image/png',
    ];

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        if (!$this->isValidFileContent($value)) {
            $this->errorMessage = 'The :attribute appears to be invalid or corrupted.';
            return false;
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return $this->errorMessage ?? 'The :attribute is invalid.';
    }

    private function isValidFileContent(mixed $file): bool
    {
        try {
            $mimeType = $this->detectMimeType($file);
            if (!in_array($mimeType, $this->allowedMimes)) {
                return false;
            }
            return match (true) {
                str_starts_with($mimeType, 'image/') => $this->isValidImage($file),
                $mimeType === 'application/pdf' => $this->isValidPdf($file),
                default => true,
            };
        } catch (\Exception $e) {
            return false;
        }
    }

    private function detectMimeType(mixed $file): string
    {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file->getPathname());
        finfo_close($finfo);

        return $mimeType;
    }

    private function isValidImage(mixed $file): bool
    {
        return getimagesize($file->getPathname()) !== false;
    }

    private function isValidPdf(mixed $file): bool
    {
        $content = file_get_contents($file->getPathname());

        if ($content === false || !str_starts_with($content, '%PDF')) {
            return false;
        }

        // 1. Scan raw (uncompressed) content first
        if ($this->containsMaliciousPatterns($content)) {
            return false;
        }

        // 2. Decompress and scan ALL FlateDecode streams
        preg_match_all('/stream\r?\n(.*?)endstream/s', $content, $matches);

        foreach ($matches[1] as $stream) {
            // Suppress errors for non-compressed or partial streams
            $decompressed = @gzuncompress($stream);

            if ($decompressed === false) {
                // Try with zlib header stripped (some PDFs omit it)
                $decompressed = @gzinflate(substr($stream, 2));
            }

            if ($decompressed !== false && $this->containsMaliciousPatterns($decompressed)) {
                return false;
            }
        }

        // 3. Check for dangerous PDF-level action triggers
        $dangerousKeys = [
            '/JavaScript',   // JS action dictionary
            '/JS',           // Shorthand JS key
            '/OpenAction',   // Runs on document open
            '/AA',           // Additional actions (page open/close etc.)
            '/Launch',       // Can launch external executables
            '/SubmitForm',   // Exfiltrates data
            '/ImportData',   // Imports external data
            '/RichMedia',    // Embeds Flash/media
            '/EmbeddedFile', // Embedded file attachment
        ];

        foreach ($dangerousKeys as $key) {
            if (str_contains($content, $key)) {
                return false;
            }
        }

        return true;
    }

    private function containsMaliciousPatterns(string $content): bool
    {
        $pattern = '/\b(?:eval|exec|alert|app\.alert|unescape|document\.write|setTimeout|setInterval|exportDataObject|getURL|submitForm|launchURL)\b/i';
        return (bool) preg_match($pattern, $content);
    }
}
