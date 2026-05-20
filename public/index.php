<?php

require_once __DIR__ . '/../autoload.php';
require_once __DIR__ . '/../src/Utility/helpers.php';
require_once __DIR__ . '/../src/Utility/auth.php';

use App\Db\DatabaseConnection;
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
];

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sanitizer = new UserSanitizer();
    $validator = new UserValidator($userRepository);

    $data = $sanitizer->sanitizeLogin($_POST);
    $errors = $validator->validateLogin($data);

    if (empty($errors)) {
        $user = $userRepository->findByEmail($data['email']);

        if ($user === null || !password_verify($data['password'], $user->getPasswordHash())) {
            $errors['credentials'] = 'Credenziali non valide.';
        } else {
            loginUser($user);
            redirectAfterLogin();
        }
    }
}

?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

    <main class="container">
        <h1>Accedi</h1>

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

        <form method="post" action="index.php" novalidate>
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
                >

                <?php if (isset($errors['password'])): ?>
                    <p class="field-error"><?= escape($errors['password']) ?></p>
                <?php endif; ?>
            </div>

            <button type="submit">Accedi</button>
        </form>

        <p>
            Non hai un account? <a href="register.php">Registrati</a>
        </p>
    </main>

</body>
</html>