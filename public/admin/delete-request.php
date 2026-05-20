<?php

require_once __DIR__ . '/../../autoload.php';
require_once __DIR__ . '/../../src/Utility/helpers.php';
require_once __DIR__ . '/../../src/Utility/auth.php';

use App\Db\DatabaseConnection;
use App\Repository\QuoteRequestRepository;

requireAdmin();

$config = require __DIR__ . '/../../config.php';

$pdo = (new DatabaseConnection($config))->connect();
$quoteRequestRepository = new QuoteRequestRepository($pdo);

$id = (int) ($_POST['id'] ?? 0);

if ($id > 0) {
    $quoteRequestRepository->delete($id);
}

header('Location: admin.php');
exit;