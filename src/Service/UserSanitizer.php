<?php

namespace App\Service;

class UserSanitizer 
{
    public function sanitizeRegistration(array $input): array
    {
        return [
            'email' => $this->sanitizeEmail($input['email'] ?? ''),
            'password' => (string)$input['password'] ?? '',
            'password_confirm' => (string) $input['password_confirm'] ?? '',
        ];
    }

    public function sanitizeLogin(array $input): array
    {
        return [
            'email' => $this->sanitizeEmail($input['email'] ?? ''),
            'password' => (string) $input['password'] ?? '',
        ];
    }

    private function sanitizeEmail(string $email): string
    {
        return strtolower((string) filter_var(trim($email), FILTER_SANITIZE_EMAIL));
    }

    public function sanitizeEmailUpdate(array $input): array
    {
        return [
            'email' => $this->sanitizeEmail($input['email'] ?? ''),
            'current_password' => (string) ($input['current_password'] ?? ''),
        ];
    }

public function sanitizePasswordUpdate(array $input): array
    {
        return [
            'current_password' => (string) ($input['current_password'] ?? ''),
            'new_password' => (string) ($input['new_password'] ?? ''),
            'new_password_confirm' => (string) ($input['new_password_confirm'] ?? ''),
        ];
    }
}