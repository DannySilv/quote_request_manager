<?php

require_once __DIR__ . '/../../autoload.php';
require_once __DIR__ . '/../../src/Utility/helpers.php';
require_once __DIR__ . '/../../src/Utility/auth.php';

use App\Db\DatabaseConnection;
use App\Repository\QuoteRequestRepository;

requireCustomer();

$config = require __DIR__ . '/../../config.php';

$pdo = (new DatabaseConnection($config))->connect();
$quoteRequestRepository = new QuoteRequestRepository($pdo);

$currentUserId = currentUserId();
$id = (int) ($_POST['id'] ?? 0);

if ($currentUserId !== null && $id > 0) {
    $quoteRequestRepository->cancelFromUser($id, $currentUserId);
}

header('Location: dashboard.php');
exit;