<?php

require_once __DIR__ . '/../../autoload.php';
require_once __DIR__ . '/../../src/Utility/helpers.php';
require_once __DIR__ . '/../../src/Utility/auth.php';

use App\Db\DatabaseConnection;
use App\Repository\UserRepository;
use App\Service\UserSanitizer;
use App\Service\UserValidator;

requireCustomer();

$config = require __DIR__ . '/../../config.php';

$pdo = (new DatabaseConnection($config))->connect();

$userRepository = new UserRepository($pdo);

$currentUserId = currentUserId();

$currentUser = $currentUserId !== null
    ? $userRepository->findById($currentUserId)
    : null;

if ($currentUser === null) {
    logoutUser();
    header('Location: ../index.php');
    exit;
}

$emailData = [
    'email' => $currentUser->getEmail(),
    'current_password' => '',
];

$passwordData = [
    'current_password' => '',
    'new_password' => '',
    'new_password_confirm' => '',
];

$emailErrors = [];
$passwordErrors = [];

$emailSuccessMessage = '';
$passwordSuccessMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sanitizer = new UserSanitizer();
    $validator = new UserValidator($userRepository);

    $formType = $_POST['form_type'] ?? '';

    if ($formType === 'email') {
        $emailData = $sanitizer->sanitizeEmailUpdate($_POST);
        $emailErrors = $validator->validateEmailUpdate($emailData, $currentUser->getId());

        if (
            empty($emailErrors)
            && !password_verify($emailData['current_password'], $currentUser->getPasswordHash())
        ) {
            $emailErrors['current_password'] = 'La password attuale non è corretta.';
        }

        if (empty($emailErrors)) {
            $userRepository->updateEmail($currentUser->getId(), $emailData['email']);

            $emailSuccessMessage = 'Email aggiornata correttamente.';

            $currentUser = $userRepository->findById($currentUser->getId());

            if ($currentUser !== null) {
                $emailData['email'] = $currentUser->getEmail();
            }

            $emailData['current_password'] = '';
        }
    }

    if ($formType === 'password') {
        $passwordData = $sanitizer->sanitizePasswordUpdate($_POST);
        $passwordErrors = $validator->validatePasswordUpdate($passwordData);

        if (
            empty($passwordErrors)
            && !password_verify($passwordData['current_password'], $currentUser->getPasswordHash())
        ) {
            $passwordErrors['current_password'] = 'La password attuale non è corretta.';
        }

        if (empty($passwordErrors)) {
            $newPasswordHash = password_hash($passwordData['new_password'], PASSWORD_DEFAULT);

            $userRepository->updatePassword($currentUser->getId(), $newPasswordHash);

            $passwordSuccessMessage = 'Password aggiornata correttamente.';

            $passwordData = [
                'current_password' => '',
                'new_password' => '',
                'new_password_confirm' => '',
            ];
        }
    }
}

?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Profilo utente</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<main class="container">
    <p>
        <a href="dashboard.php">← Torna alla dashboard</a>
    </p>

    <h1>Profilo utente</h1>

    <section class="detail-card">
        <h2>Dati account</h2>

        <dl class="detail-list">
            <dt>Email attuale</dt>
            <dd><?= escape($currentUser->getEmail()) ?></dd>

            <dt>Tipo account</dt>
            <dd><?= $currentUser->isAdmin() ? 'Admin' : 'Cliente' ?></dd>

            <dt>Creato il</dt>
            <dd><?= escape($currentUser->getCreatedAt() ?? '-') ?></dd>
        </dl>
    </section>

    <section class="detail-card">
        <h2>Cambia email</h2>

        <?php if ($emailSuccessMessage !== ''): ?>
            <div class="alert">
                <?= escape($emailSuccessMessage) ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($emailErrors)): ?>
            <div class="alert alert-error">
                <ul>
                    <?php foreach ($emailErrors as $error): ?>
                        <li><?= escape($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="post" action="profile.php" novalidate>
            <input type="hidden" name="form_type" value="email">

            <div class="field">
                <label for="email">Nuova email</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    value="<?= escape($emailData['email']) ?>"
                >

                <?php if (isset($emailErrors['email'])): ?>
                    <p class="field-error"><?= escape($emailErrors['email']) ?></p>
                <?php endif; ?>
            </div>

            <div class="field">
                <label for="email_current_password">Password attuale</label>
                <input
                    type="password"
                    id="email_current_password"
                    name="current_password"
                >

                <?php if (isset($emailErrors['current_password'])): ?>
                    <p class="field-error"><?= escape($emailErrors['current_password']) ?></p>
                <?php endif; ?>
            </div>

            <button type="submit">Aggiorna email</button>
        </form>
    </section>

    <section class="detail-card">
        <h2>Cambia password</h2>

        <?php if ($passwordSuccessMessage !== ''): ?>
            <div class="alert">
                <?= escape($passwordSuccessMessage) ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($passwordErrors)): ?>
            <div class="alert alert-error">
                <ul>
                    <?php foreach ($passwordErrors as $error): ?>
                        <li><?= escape($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="post" action="profile.php" novalidate>
            <input type="hidden" name="form_type" value="password">

            <div class="field">
                <label for="password_current_password">Password attuale</label>
                <input
                    type="password"
                    id="password_current_password"
                    name="current_password"
                >

                <?php if (isset($passwordErrors['current_password'])): ?>
                    <p class="field-error"><?= escape($passwordErrors['current_password']) ?></p>
                <?php endif; ?>
            </div>

            <div class="field">
                <label for="new_password">Nuova password</label>
                <input
                    type="password"
                    id="new_password"
                    name="new_password"
                >

                <?php if (isset($passwordErrors['new_password'])): ?>
                    <p class="field-error"><?= escape($passwordErrors['new_password']) ?></p>
                <?php endif; ?>
            </div>

            <div class="field">
                <label for="new_password_confirm">Conferma nuova password</label>
                <input
                    type="password"
                    id="new_password_confirm"
                    name="new_password_confirm"
                >

                <?php if (isset($passwordErrors['new_password_confirm'])): ?>
                    <p class="field-error"><?= escape($passwordErrors['new_password_confirm']) ?></p>
                <?php endif; ?>
            </div>

            <button type="submit">Aggiorna password</button>
        </form>
    </section>

    <p>
        <a href="../logout.php">Logout</a>
    </p>
</main>

</body>
</html>