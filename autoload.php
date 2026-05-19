<?php

spl_autoload_register(function (string $className): void {
    $prefix = 'App\\';
    $baseDir = __DIR__ . '/src/';

    $prefixLength = strlen($prefix);

    if (strncmp($prefix, $className, $prefixLength) !== 0) {
        return;
    }

    $relativeClass = substr($className, $prefixLength);

    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

    if (file_exists($file)) {
        require_once $file;
    }
});