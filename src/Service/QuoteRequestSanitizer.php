<?php

namespace App\Service;

class QuoteRequestSanitizer 
{
    public function sanitize(array $input): array
    {
        return [
            'company' => $this->sanitizeString($input['company'] ?? ''),
            'email' => $this->sanitizeEmail($input['email'] ?? ''),
            'sector' => $this->sanitizeString($input['sector'] ?? ''),
            'quantity' => $this->sanitizeInteger($input['quantity'] ?? 0),
            'message' => $this->sanitizeString($input['message'] ?? ''),
        ];
    }

    private function sanitizeString(string $value): string
    {
        return trim(strip_tags($value));
    }

    private function sanitizeEmail(string $value): string
    {
        return (string) filter_var(trim($value), FILTER_SANITIZE_EMAIL);
    }

    private function sanitizeInteger(string $value): int
    {
        return (int) $value;
    }
}   