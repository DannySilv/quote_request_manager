<?php

function getJsonInput(): array
{
    $rawInput = file_get_contents('php://input');
    
    if ($rawInput === false || trim($rawInput) === '') {
        return [];
    }
    
    $data = json_decode($rawInput, true);

    return is_array($data) ? $data : [];
}

function jsonResponse(array $data, int $statusCode = 200): void
{
    http_response_code($statusCode);
    header('Content-Type: application/json; charset=utf-8');

    echo json_encode($data);
    exit;
}

function requireAdminApi(): void
{
    if (!isLoggedIn()) {
        jsonResponse([
            'success' => false,
            'message' => 'Non autenticato.',
        ], 401);
    }

    if (!currentUserIsAdmin()) {
        jsonResponse([
            'success' => false,
            'message' => 'Accesso non autorizzato.',
        ], 403);
    }
}