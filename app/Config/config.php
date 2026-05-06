<?php

$envPath = __DIR__ . '/../../.env';

if (file_exists($envPath)) {
    // Local: carga desde archivo .env
    $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    foreach ($lines as $line) {
        if (str_starts_with(trim($line), '#')) {
            continue;
        }

        [$key, $value] = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value);

        // Solo setear si no viene ya del entorno (Railway tiene prioridad)
        if (!isset($_ENV[$key]) && getenv($key) === false) {
            $_ENV[$key] = $value;
            putenv("$key=$value");
        }
    }
} else {
    // Producción (Railway): leer desde variables de entorno del sistema
    $keys = ['APP_NAME', 'APP_ENV', 'APP_DEBUG', 'APP_URL',
             'DB_HOST', 'DB_NAME', 'DB_USER', 'DB_PASS'];

    foreach ($keys as $key) {
        $value = getenv($key);
        if ($value !== false) {
            $_ENV[$key] = $value;
        }
    }
}