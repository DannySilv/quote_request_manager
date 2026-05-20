<?php

function escape(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function getSectors(): array
{
    return [
        'informatico' => 'Informatico',
        'alimentare' => 'Alimentare',
        'industriale' => 'Industriale',
        'sanitario' => 'Sanitario',
        'cosmetico' => 'Cosmetico',
        'enologico' => 'Enologico',
    ];
}

function generatePublicToken(): string
{
    return bin2hex(random_bytes(16));
}

function requireLogin(): void
{
    if (!isLoggedIn()) {
        header('Location: /index.php');
        exit;
    }
}

function requireCustomer(): void
{
    requireLogin();

    if (currentUserIsAdmin()) {
        header('Location: /admin/admin.php');
        exit;
    }
}