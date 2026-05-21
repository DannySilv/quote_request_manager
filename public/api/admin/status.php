<?php

require_once __DIR__ . '/../../../autoload.php';
require_once __DIR__ . '/../../../src/Utility/auth.php';
require_once __DIR__ . '/../api.php';

use App\Db\DatabaseConnection;
use App\Repository\QuoteRequestRepository;

requireAdminApi();

$config = require __DIR__ . '/../../../config.php';

$pdo = (new DatabaseConnection($config))->connect();
$quoteRequestRepository = new QuoteRequestRepository($pdo);

$input = getJsonInput();

$id = (int) ($input['id'] ?? 0);
$status = $input['status'] ?? '';

$allowedStatuses = [
    'new',
    'in_progress',
    'completed',
    'archived',
    'cancelled',
    'to_restore',
];

if ($id <= 0) {
    jsonResponse([
        'success' => false,
        'message' => 'ID richiesta non valido.',
    ], 422);
}

if (!in_array($status, $allowedStatuses, true)) {
    jsonResponse([
        'success' => false,
        'message' => 'Stato non valido.',
    ], 422);
}

$quoteRequest = $quoteRequestRepository->findById($id);

if ($quoteRequest === null) {
    jsonResponse([
        'success' => false,
        'message' => 'Richiesta non trovata.',
    ], 404);
}

if ($status === 'to_restore') {
    $quoteRequestRepository->restore($id);

    jsonResponse([
        'success' => true,
        'message' => 'Richiesta ripristinata correttamente.',
    ]);
}

if ($status === 'archived') {
    $quoteRequestRepository->softDelete($id);

    jsonResponse([
        'success' => true,
        'message' => 'Richiesta archiviata correttamente.',
    ]);
}

$quoteRequestRepository->updateStatus($id, $status);

jsonResponse([
    'success' => true,
    'message' => 'Stato aggiornato correttamente.',
]);