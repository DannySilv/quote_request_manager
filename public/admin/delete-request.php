<?php

require_once __DIR__ . '../../../autoload.php';

use App\Db\DatabaseConnection;
use App\Repository\QuoteRequestRepository;

$config = require __DIR__ . '/../config.php';

$pdo = (new DatabaseConnection($config))->connect();
$quoteRequestRepository = new QuoteRequestRepository($pdo);

$id = (int) ($_POST['id'] ?? 0);

if ($id > 0) {
    $quoteRequestRepository->delete($id);
}

header('Location: admin.php');
exit;