<?php

namespace App\Service;

class QuoteRequestValidator
{
    private array $allowedSectors = [
        'informatico',
        'alimentare',
        'industriale',
        'sanitario',
        'cosmetico',
        'enologico'
    ];

    public function validate(array $data): array
    {
        $errors = [];

        if (empty($data['company']) || $data['company'] === '') {
            $errors['company'] = 'Il nome dell\'azienda è obbligatorio.';
        }

        if (empty($data['email']) || $data['email'] === '') {
            $errors['email'] = 'L\'email è obbligatoria.';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Formato email non valido.';
        }

        if (empty($data['sector']) || $data['sector'] === '') {
            $errors['sector'] = 'Il settore è obbligatorio.';
        } elseif (!in_array($data['sector'], $this->allowedSectors, true)) {
            $errors['sector'] = 'Il settore selezionato non è valido.';
        }

        if ($data['quantity'] <= 0) {
            $errors['quantity'] = 'La quantità è deve essere maggiore di zero.';
        }

        if (empty($data['message']) || $data['message'] === '') {
            $errors['message'] = 'Il messaggio è obbligatorio.';
        } elseif (strlen($data['message']) < 10 || strlen($data['message']) > 1000) {
            $errors['message'] = 'Il messaggio deve contenere tra i 10 e i 1000 caratteri.';
        }

        return $errors;
    }
}