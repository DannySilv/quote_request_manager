<?php

require_once __DIR__ . '../../../autoload.php';

use App\Db\DatabaseConnection;
use App\Repository\QuoteRequestRepository;

$config = require __DIR__ . '../../../config.php';

$pdo = (new DatabaseConnection($config))->connect();
$quoteRequestRepository = new QuoteRequestRepository($pdo);

$allowedStatuses = [
    'new',
    'in_progress',
    'completed',
    'archived',
    'cancelled',
    'to_restore'
];

$id = (int) ($_POST['id'] ?? 0);
$status = $_POST['status'] ?? '';

if ($id <= 0 || !in_array($status, $allowedStatuses, true)) {
    header('Location: admin.php');
    exit;
}

if ($status === 'to_restore') {
    $quoteRequestRepository->restore($id);
} elseif ($status === 'archived') {
    $quoteRequestRepository->softDelete($id);
} else {
    $quoteRequestRepository->updateStatus($id, $status);
}

header('Location: admin.php');
exit;