<?php

session_start();

require_once __DIR__ . '/../config.php';

// Bootstrap the MVC application once so every entrypoint shares the same
// session handling, database config, and class loading behavior.
// Autoload every class inside the App\ namespace from the app/ directory.
spl_autoload_register(static function (string $class): void {
    $prefix = 'App\\';

    if (strncmp($class, $prefix, strlen($prefix)) !== 0) {
        return;
    }

    $relativeClass = substr($class, strlen($prefix));
    $file = __DIR__ . '/' . str_replace('\\', '/', $relativeClass) . '.php';

    if (is_file($file)) {
        require_once $file;
    }
});
