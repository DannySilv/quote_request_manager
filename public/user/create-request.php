<?php

require_once __DIR__ . '/../../autoload.php';
require_once __DIR__ . '/../../src/Utility/helpers.php';
require_once __DIR__ . '/../../src/Utility/auth.php';

use App\Db\DatabaseConnection;
use App\Model\QuoteRequest;
use App\Repository\QuoteRequestRepository;
use App\Repository\UserRepository;
use App\Service\QuoteRequestSanitizer;
use App\Service\QuoteRequestValidator;

requireCustomer();

$config = require __DIR__ . '/../../config.php';

$pdo = (new DatabaseConnection($config))->connect();

$userRepository = new UserRepository($pdo);
$quoteRequestRepository = new QuoteRequestRepository($pdo);

$currentUserId = currentUserId();
$currentUser = $currentUserId !== null
    ? $userRepository->findById($currentUserId)
    : null;

if ($currentUser === null) {
    logoutUser();
    header('Location: ../index.php');
    exit;
}

$data = [
    'company' => '',
    'email' => $currentUser->getEmail(),
    'sector' => '',
    'quantity' => '',
    'message' => '',
];

$errors = [];
$sectors = getSectors();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sanitizer = new QuoteRequestSanitizer();
    $validator = new QuoteRequestValidator();

    $data = $sanitizer->sanitize($_POST);
    $data['email'] = $currentUser->getEmail();

    $errors = $validator->validate($data);

    if (empty($errors)) {
        $quoteRequest = new QuoteRequest(
            userId: $currentUser->getId(),
            company: $data['company'],
            email: $data['email'],
            sector: $data['sector'],
            quantity: $data['quantity'],
            status: 'new',
            message: $data['message']
        );

        $quoteRequestRepository->save($quoteRequest);

        header('Location: thank-you.php');
        exit;
    }
}

?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Nuova richiesta</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

    <main class="container">
        <p>
            <a href="dashboard.php">← Torna alla dashboard</a>
        </p>
        <section class="detail-card">
            <h2>Nuova richiesta preventivo</h2>
        
            <?php if (!empty($errors)): ?>
                <div class="alert alert-error">
                    <h2>Correggi questi errori:</h2>

                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?= escape($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form method="post" action="create-request.php" data-validate= "quote-request" novalidate>
                <div class="field">
                    <label for="company">Azienda</label>
                    <input
                        type="text"
                        id="company"
                        name="company"
                        value="<?= escape((string) $data['company']) ?>"
                    >
                </div>

                <div class="field">
                    <label>Email account</label>
                    <input
                        type="email"
                        value="<?= escape($currentUser->getEmail()) ?>"
                        disabled
                    >
                </div>

                <div class="field">
                    <label for="sector">Settore</label>
                    <select id="sector" name="sector">
                        <option value="">Seleziona un settore</option>

                        <?php foreach ($sectors as $sectorValue => $sectorLabel): ?>
                            <option
                                value="<?= escape($sectorValue) ?>"
                                <?= $data['sector'] === $sectorValue ? 'selected' : '' ?>
                            >
                                <?= escape($sectorLabel) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

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

                <button type="submit">Invia richiesta</button>
            </form>
        </section>
    </main>

    <script src="../assets/js/app.js" defer></script>
</body>
</html>