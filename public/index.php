<?php

require_once __DIR__ . '/../autoload.php';
require_once __DIR__ . '/../src/Utility/helpers.php';

use App\Db\DatabaseConnection;
use App\Model\QuoteRequest;
use App\Repository\QuoteRequestRepository;
use App\Service\QuoteRequestSanitizer;
use App\Service\QuoteRequestValidator;

$config = require __DIR__ . '/../config.php';

$pdo = (new DatabaseConnection($config))->connect();
$quoteRequestRepository = new QuoteRequestRepository($pdo);

$data = [
    'company' => '',
    'email' => '',
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
    $errors = $validator->validate($data);

    if (empty($errors)) {
        $quoteRequest = new QuoteRequest(
            company: $data['company'],
            email: $data['email'],
            sector: $data['sector'],
            quantity: $data['quantity'],
            message: $data['message']
        );

        $quoteRequestRepository->save($quoteRequest);

        header('Location: thank_you.php');
        exit;
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Richiesta Preventivo</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <main class="container">
        <h1>Richiedi un preventivo</h1>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-error">
                <h2>Si sono verificati i seguenti errori:</h2>
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?= escape($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="index.php" method="POST" novalidate>
            <div class="field">
                <label for="company">Azienda</label>
                <input 
                    type="text" 
                    id="company" 
                    name="company" 
                    value="<?= escape((string) $data['company']) ?>" 
                >
                <?php if (isset($errors['company'])): ?>
                    <p class="field-error"><?= escape($errors['company']) ?></p>
                <?php endif; ?>
            </div>

            <div class="field">
                <label for="email">Email</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    value="<?= escape((string) $data['email']) ?>"
                >

                <?php if (isset($errors['email'])): ?>
                    <p class="field-error"><?= escape($errors['email']) ?></p>
                <?php endif; ?>
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

                <?php if (isset($errors['sector'])): ?>
                    <p class="field-error"><?= escape($errors['sector']) ?></p>
                <?php endif; ?>
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

                <?php if (isset($errors['quantity'])): ?>
                    <p class="field-error"><?= escape($errors['quantity']) ?></p>
                <?php endif; ?>
            </div>

            <div class="field">
                <label for="message">Messaggio</label>
                <textarea id="message" name="message"><?= escape((string) $data['message']) ?></textarea>

                <?php if (isset($errors['message'])): ?>
                    <p class="field-error"><?= escape($errors['message']) ?></p>
                <?php endif; ?>
            </div>

            <button type="submit">Invia richiesta</button>
        </form>
    </main>

</body>
</html>