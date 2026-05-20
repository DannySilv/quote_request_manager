<?php

require_once __DIR__ . '/../autoload.php';
require_once __DIR__ . '/../src/Utility/helpers.php';
require_once __DIR__ . '/../src/Utility/auth.php';

use App\Db\DatabaseConnection;
use App\Model\User;
use App\Repository\UserRepository;
use App\Service\UserSanitizer;
use App\Service\UserValidator;

if (isLoggedIn()) {
    redirectAfterLogin();
}

$config = require __DIR__ . '/../config.php';

$pdo = (new DatabaseConnection($config))->connect();
$userRepository = new UserRepository($pdo);

$data = [
    'email' => '',
    'password' => '',
    'password_confirm' => '',
];

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sanitizer = new UserSanitizer();
    $validator = new UserValidator($userRepository);

    $data = $sanitizer->sanitizeRegistration($_POST);
    $errors = $validator->validateRegistration($data);

    if (empty($errors)) {
        $user = new User(
            email: $data['email'],
            passwordHash: password_hash($data['password'], PASSWORD_DEFAULT)
        );

        $userId = $userRepository->save($user);
        $createdUser = $userRepository->findById($userId);

        if ($createdUser !== null) {
            loginUser($createdUser);
            header('Location: user/dashboard.php');
            exit;
        }

        $errors['general'] = 'Errore durante la creazione dell\'account. Riprova.';
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrazione</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <main class="container">
        <h1>Crea un account</h1>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-error">
                <h2>Correggi i seguenti errori:</h2>
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?= escape($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="POST" action="register.php" novalidate>
            <div class="field">
                <label for="email">Email</label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    value="<?= escape($data['email']) ?>"
                >

                <?php if (isset($errors['email'])): ?>
                    <p class="field-error"><?= escape($errors['email']) ?></p>
                <?php endif; ?>
            </div>

            <div class="field">
                <label for="password">Password</label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    value="<?= escape($data['password']) ?>"
                >

                <?php if (isset($errors['password'])): ?>
                    <p class="field-error"><?= escape($errors['password']) ?></p>
                <?php endif; ?>
            </div>

            <div class="field">
                <label for="password_confirm">Conferma Password</label>
                <input 
                    type="password" 
                    id="password_confirm" 
                    name="password_confirm" 
                    value="<?= escape($data['password_confirm']) ?>"
                >

                <?php if (isset($errors['password_confirm'])): ?>
                    <p class="field-error"><?= escape($errors['password_confirm']) ?></p>
                <?php endif; ?>
            </div>

            <button type="submit">Registrati</button>
        </form>

        <p>
            Hai già un account? <a href="index.php">Accedi</a>
        </p>
    </main>
</body>
</html>