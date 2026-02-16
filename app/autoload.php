<?php

spl_autoload_register(function ($class) {

    // Solo manejar clases del namespace App\
    $prefix = 'App\\';
    $baseDir = __DIR__ . '/';

    // Si la clase no usa el namespace App, no hacemos nada
    if (strncmp($prefix, $class, strlen($prefix)) !== 0) {
        return;
    }

    // Quitar "App\" del nombre de la clase
    $relativeClass = substr($class, strlen($prefix));

    // Convertir namespace a ruta
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

    if (file_exists($file)) {
        require_once $file;
    }
});
