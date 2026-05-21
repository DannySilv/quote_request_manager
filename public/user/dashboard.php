<?php

require_once __DIR__ . '/../../autoload.php';
require_once __DIR__ . '/../../src/Utility/helpers.php';
require_once __DIR__ . '/../../src/Utility/auth.php';

use App\Db\DatabaseConnection;
use App\Repository\QuoteRequestRepository;
use App\Repository\UserRepository;

requireCustomer();

$config = require __DIR__ . '/../../config.php';

$pdo = (new DatabaseConnection($config))->connect();

$userRepository = new UserRepository($pdo);
$quoteRequestRepository = new QuoteRequestRepository($pdo);

$currentUserId = currentUserId();
$currentUser = $currentUserId !== null ? $userRepository->findById($currentUserId) : null;
if ($currentUser === null) {
    logoutUser();
    header('Location: ../index.php');
    exit;
}

$quoteRequests = $quoteRequestRepository->findByUserId($currentUser->getId());

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard utente</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

    <main class="container container-wide">
        <header class="page-header">
            <div>
                <h1>Dashboard azienda</h1>
                <p>Benvenuto, <?= escape($currentUser->getEmail()) ?></p>
            </div>

            <nav>
                <a href="create-request.php">Nuova richiesta</a>
                <a href="profile.php">Profilo</a>
                <a href="../logout.php">Logout</a>
            </nav>
        </header>

        <?php if (empty($quoteRequests)): ?>
            <div class="alert">
                Non hai ancora inviato richieste di preventivo.
            </div>
        <?php else: ?>
            <div class="field">
                <label for="request-search">Cerca richieste</label>
                <input
                    type="search"
                    id="request-search"
                    data-table-search
                    placeholder="Cerca per azienda, settore, stato..."
                >
            </div>
            <div class="table-wrapper">
                <table class="admin-table" data-searchable-table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Azienda</th>
                            <th>Settore</th>
                            <th>Quantità</th>
                            <th>Stato</th>
                            <th>Creata il</th>
                            <th>Azioni</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($quoteRequests as $quoteRequest): ?>
                            <tr>
                                <td><?= escape((string) $quoteRequest->getId()) ?></td>
                                <td><?= escape($quoteRequest->getCompany()) ?></td>
                                <td><?= escape($quoteRequest->getSector()) ?></td>
                                <td><?= escape((string) $quoteRequest->getQuantity()) ?></td>
                                <td><?= escape($quoteRequest->getStatusLabel()) ?></td>
                                <td><?= escape($quoteRequest->getCreatedAt() ?? '-') ?></td>
                                <td>
                                    <?php if ($quoteRequest->getStatus() === 'new'): ?>
                                        <a href="edit-request.php?id=<?= escape((string) $quoteRequest->getId()) ?>"
                                            class="button-link secondary"
                                        >
                                            Modifica
                                        </a>

                                        <form
                                            method="post"
                                            action="cancel-request.php"
                                            class="inline-form"
                                            onsubmit="return confirm('Vuoi annullare questa richiesta?');"
                                        >
                                            <input type="hidden" name="id" value="<?= escape((string) $quoteRequest->getId()) ?>">
                                            <button type="submit" class="danger-button">Annulla</button>
                                        </form>
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </main>

    <script src="../assets/js/app.js" defer></script>

</body>
</html>