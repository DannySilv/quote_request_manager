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

$quoteRequests = $quoteRequestRepository->findAll();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Richieste Preventivo</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <main class="container container-wide">
        <header class="page-header">
            <div>
                <h1>Gestione richieste preventivo</h1>
                <p>Lista delle richieste ricevute dal form pubblico.</p>
            </div>

            <nav>
                <a href="../logout.php">Logout</a>
            </nav>
        </header>

        <?php if (empty($quoteRequests)): ?>
            <div class="alert">
                Nessuna richiesta presente.
            </div>
        <?php else: ?>
            <input
                type="search"
                data-table-search
                placeholder="Cerca per azienda, email, settore, stato..."
            >
            <div class="table-wrapper">
                <table class="admin-table" data-searchable-table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Azienda</th>
                            <th>Email</th>
                            <th>Settore</th>
                            <th>Quantità</th>
                            <th>Stato</th>
                            <th>Data</th>
                            <th>Azioni</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($quoteRequests as $quoteRequest): ?>
                            <tr>
                                <td><?= escape((string) $quoteRequest->getId()) ?></td>

                                <td>
                                    <strong><?= escape($quoteRequest->getCompany()) ?></strong>
                                </td>

                                <td><?= escape($quoteRequest->getEmail()) ?></td>

                                <td><?= escape($quoteRequest->getSector()) ?></td>

                                <td><?= escape((string) $quoteRequest->getQuantity()) ?></td>

                                <td>
                                    <span class="status status-<?= escape($quoteRequest->getStatus()) ?>">
                                        <?= escape($quoteRequest->getStatusLabel()) ?>
                                    </span>
                                </td>

                                <td><?= escape($quoteRequest->getCreatedAt() ?? '') ?></td>

                                <td>
                                    <div class="actions">
                                        <a
                                            href="admin-show.php?id=<?= escape((string) $quoteRequest->getId()) ?>"
                                            class="button-link secondary"
                                        >
                                            Dettaglio
                                        </a>
                                        <?php if ($quoteRequest->getStatus() !== 'archived'): ?>
                                            <form method="post" action="admin-status.php" class="inline-form">
                                                <input type="hidden" name="id" value="<?= escape((string) $quoteRequest->getId()) ?>">
                                                <input type="hidden" name="status" value="in_progress">
                                                <button type="submit">In lavorazione</button>
                                            </form>

                                            <form method="post" action="admin-status.php" class="inline-form">
                                                <input type="hidden" name="id" value="<?= escape((string) $quoteRequest->getId()) ?>">
                                                <input type="hidden" name="status" value="completed">
                                                <button type="submit">Evadi</button>
                                            </form>

                                            <form method="post" action="admin-status.php" class="inline-form">
                                                <input type="hidden" name="id" value="<?= escape((string) $quoteRequest->getId()) ?>">
                                                <input type="hidden" name="status" value="archived">
                                                <button type="submit">Archivia</button>
                                            </form>

                                            <form
                                                method="post"
                                                action="admin-delete.php"
                                                class="inline-form"
                                                data-confirm="Vuoi eliminare definitivamente questa richiesta?"
                                            >
                                                <input type="hidden" name="id" value="<?= escape((string) $quoteRequest->getId()) ?>">
                                                <input type="hidden" name="status" value="cancelled">
                                                <button type="submit" class="danger-button">Elimina</button>
                                            </form>
                                        <?php else: ?>
                                            <form method="post" action="admin-status.php" class="inline-form">
                                                <input type="hidden" name="id" value="<?= escape((string) $quoteRequest->getId()) ?>">
                                                <input type="hidden" name="status" value="to_restore">
                                                <button type="submit" class="restore-button">Ripristina</button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
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