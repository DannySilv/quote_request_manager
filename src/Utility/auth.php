<?php

use App\Model\User;

function startSession(): void
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
}

function loginUser(User $user): void
{
    startSession();

    session_regenerate_id(true);


    $_SESSION['user_id'] = $user->getId();
    $_SESSION['is_admin'] = $user->isAdmin();
}

function logoutUser(): void
{
    startSession();

    $_SESSION = [];

    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();

        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params['path'],
            $params['domain'],
            $params['secure'],
            $params['httponly']
        );
    }

    session_destroy();
}

function isLoggedIn(): bool
{
    startSession();

    return isset($_SESSION['user_id']);
}

function currentUserId(): ?int
{
    startSession();

    return isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : null;
}

function currentUserIsAdmin(): bool
{
    startSession();

    return isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true;
}

function redirectAfterLogin(): void
{
    if (currentUserIsAdmin()) {
        header('Location: admin/admin.php');
        exit;
    }

    header('Location: user/dashboard.php');
    exit;
}