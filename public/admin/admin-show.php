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

$id = (int) ($_GET['id'] ?? 0);

$quoteRequest = $id > 0
    ? $quoteRequestRepository->findById($id)
    : null;

?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dettaglio richiesta</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

    <main class="container">
        <p>
            <a href="admin.php">← Torna alla lista</a>
        </p>

        <?php if ($quoteRequest === null): ?>
            <div class="alert alert-error">
                <h1>Richiesta non trovata</h1>
                <p>La richiesta selezionata non esiste oppure non è più disponibile.</p>
            </div>
        <?php else: ?>
            <header class="page-header">
                <div>
                    <h1>Richiesta #<?= escape((string) $quoteRequest->getId()) ?></h1>
                    <p>Dettaglio completo della richiesta preventivo.</p>
                </div>

                <span class="status status-<?= escape($quoteRequest->getStatus()) ?>">
                    <?= escape($quoteRequest->getStatusLabel()) ?>
                </span>
            </header>

            <section class="detail-card">
                <h2>Dati azienda</h2>

                <dl class="detail-list">
                    <dt>Azienda</dt>
                    <dd><?= escape($quoteRequest->getCompany()) ?></dd>

                    <dt>Email</dt>
                    <dd>
                        <a href="mailto:<?= escape($quoteRequest->getEmail()) ?>">
                            <?= escape($quoteRequest->getEmail()) ?>
                        </a>
                    </dd>

                    <dt>Settore</dt>
                    <dd><?= escape($quoteRequest->getSector()) ?></dd>

                    <dt>Quantità</dt>
                    <dd><?= escape((string) $quoteRequest->getQuantity()) ?></dd>

                    <dt>Dimensione richiesta</dt>
                    <dd><?= escape($quoteRequest->getSizeLabel()) ?></dd>
                </dl>
            </section>

            <section class="detail-card">
                <h2>Messaggio</h2>

                <p class="message-box">
                    <?= nl2br(escape($quoteRequest->getMessage())) ?>
                </p>
            </section>

            <section class="detail-card">
                <h2>Informazioni interne</h2>

                <dl class="detail-list">
                    <dt>Stato tecnico</dt>
                    <dd><?= escape($quoteRequest->getStatus()) ?></dd>

                    <dt>Creata il</dt>
                    <dd><?= escape($quoteRequest->getCreatedAt() ?? '-') ?></dd>

                    <dt>Aggiornata il</dt>
                    <dd><?= escape($quoteRequest->getUpdatedAt() ?? '-') ?></dd>

                    <dt>Archiviata il</dt>
                    <dd><?= escape($quoteRequest->getDeletedAt() ?? '-') ?></dd>
                </dl>
            </section>

            <section class="detail-card">
                <h2>Azioni</h2>

                <div class="actions">
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
                    <?php else: ?>
                        <form method="post" action="admin-status.php" class="inline-form">
                            <input type="hidden" name="id" value="<?= escape((string) $quoteRequest->getId()) ?>">
                            <input type="hidden" name="status" value="to_restore">
                            <button type="submit" class="restore-button">Ripristina</button>
                        </form>
                    <?php endif; ?>

                    <form
                        method="post"
                        action="admin-delete.php"
                        class="inline-form"
                        data-confirm="Vuoi eliminare definitivamente questa richiesta?"
                    >
                        <input type="hidden" name="id" value="<?= escape((string) $quoteRequest->getId()) ?>">
                        <button type="submit" class="danger-button">Elimina definitivamente</button>
                    </form>
                </div>
            </section>
        <?php endif; ?>
    </main>

    <script src="../assets/js/app.js" defer></script>

</body>
</html>