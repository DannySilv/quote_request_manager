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
$id = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);

$quoteRequest = $currentUserId !== null && $id > 0
    ? $quoteRequestRepository->findByIdAndUserId($id, $currentUserId)
    : null;

if ($quoteRequest === null) {
    header('Location: dashboard.php');
    exit;
}

$isEditable = $quoteRequest->getStatus() === 'new';

if (!$isEditable) {
    header('Location: dashboard.php');
    exit;
}

$data = [
    'quantity' => $quoteRequest->getQuantity(),
    'message' => $quoteRequest->getMessage(),
];

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $quantity = (int) ($_POST['quantity'] ?? 0);
    $message = trim(strip_tags($_POST['message'] ?? ''));

    $data = [
        'quantity' => $quantity,
        'message' => $message,
    ];

    if ($quantity <= 0) {
        $errors['quantity'] = 'La quantità deve essere maggiore di zero.';
    }

    if ($message === '') {
        $errors['message'] = 'Il messaggio è obbligatorio.';
    } elseif (strlen($message) < 10) {
        $errors['message'] = 'Il messaggio deve contenere almeno 10 caratteri.';
    }

    if (empty($errors)) {
        $quoteRequestRepository->updateFromUser(
            id: $quoteRequest->getId(),
            userId: $currentUserId,
            quantity: $quantity,
            message: $message
        );

        header('Location: dashboard.php');
        exit;
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Modifica richiesta</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

    <main class="container">
        <p>
            <a href="dashboard.php">← Torna alla dashboard</a>
        </p>

        <h1>Modifica richiesta #<?= escape((string) $quoteRequest->getId()) ?></h1>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-error">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?= escape($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="post" action="edit-request.php" data-validate= "quote-request" novalidate>
            <input type="hidden" name="id" value="<?= escape((string) $quoteRequest->getId()) ?>">

            <div class="field">
                <label for="quantity">Quantità</label>
                <input
                    type="number"
                    id="quantity"
                    name="quantity"
                    min="1"
                    value="<?= escape((string) $data['quantity']) ?>"
                >
            </div>

            <div class="field">
                <label for="message">Messaggio</label>
                <textarea id="message" name="message"><?= escape((string) $data['message']) ?></textarea>
            </div>

            <button type="submit">Salva modifiche</button>
        </form>
    </main>

    <script src="../assets/js/app.js" defer></script>
</body>
</html>