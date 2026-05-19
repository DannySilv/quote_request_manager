<?php

function escape(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function getSectors(): array
{
    return [
        'informatico' => 'Informatico',
        'alimentare' => 'Alimentare',
        'industriale' => 'Industriale',
        'sanitario' => 'Sanitario',
        'cosmetico' => 'Cosmetico',
        'enologico' => 'Enologico',
    ];
}