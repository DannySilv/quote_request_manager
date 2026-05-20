<?php

namespace App\Service;

use App\Repository\UserRepository;

class UserValidator 
{
    public function __construct(
        private UserRepository $userRepository
    ) {}
    
    public function validateRegistration(array $data): array
    {
        $errors = [];

        if (empty($data['email']) || $data['email'] === '') {
            $errors['email'] = 'L\'email è obbligatoria.';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Formato email non valido.';
        } elseif ($this->userRepository->emailExists($data['email'])) {
            $errors['email'] = 'Esiste già un account con questa email.';
        }

        if (empty($data['password']) || $data['password'] === '') {
            $errors['password'] = 'La password è obbligatoria.';
        } elseif (strlen($data['password']) < 8) {
            $errors['password'] = 'La password deve contenere almeno 8 caratteri.';
        }

        if (empty($data['password_confirm']) || $data['password_confirm'] === '') {
            $errors['password_confirm'] = 'La conferma della password è obbligatoria.';
        } elseif ($data['password'] !== $data['password_confirm']) {
            $errors['password_confirm'] = 'Le password non coincidono.';
        }

        return $errors;
    }

    public function validateLogin(array $data): array
    {
        $errors = [];

        if (empty($data['email']) || $data['email'] === '') {
            $errors['email'] = 'L\'email è obbligatoria.';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'L\'email non è valida.';
        }

        if (empty($data['password']) || $data['password'] === '') {
            $errors['password'] = 'La password è obbligatoria.';
        }

        return $errors;
    }

    public function validateEmailUpdate(array $data, int $currentUserId): array
    {
        $errors = [];

        if (empty($data['email']) ||$data['email'] === '') {
            $errors['email'] = "L'email è obbligatoria.";
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = "L'email non è valida.";
        } elseif ($this->userRepository->emailExistsForOtherUser($data['email'], $currentUserId)) {
            $errors['email'] = "Questa email è già utilizzata da un altro account.";
        }

        if (empty($data['current_password']) || $data['current_password'] === '') {
            $errors['current_password'] = 'La password attuale è obbligatoria.';
        }

        return $errors;
    }

    public function validatePasswordUpdate(array $data): array
    {
        $errors = [];

        if (empty($data['current_password']) || $data['current_password'] === '') {
            $errors['current_password'] = 'La password attuale è obbligatoria.';
        }

        if (empty($data['new_password']) || $data['new_password'] === '') {
            $errors['new_password'] = 'La nuova password è obbligatoria.';
        } elseif (strlen($data['new_password']) < 8) {
            $errors['new_password'] = 'La nuova password deve contenere almeno 8 caratteri.';
        }

        if (empty($data['new_password_confirm']) || $data['new_password_confirm'] === '') {
            $errors['new_password_confirm'] = 'La conferma della nuova password è obbligatoria.';
        } elseif ($data['new_password'] !== $data['new_password_confirm']) {
            $errors['new_password_confirm'] = 'Le nuove password non coincidono.';
        }

        return $errors;
    }
}