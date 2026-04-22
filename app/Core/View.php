<?php

namespace App\Core;

use App\Services\SecurityService;

final class View
{
    // Resolve a view file and inject shared data before handing off to the template.
    public static function render(string $view, array $data = []): void
    {
        $file = __DIR__ . '/../Views/' . $view . '.php';

        if (!is_file($file)) {
            throw new \RuntimeException('View not found: ' . $view);
        }

        // Every view receives a CSRF token by default so forms can opt in without
        // repeating token generation logic in every controller action.
        $data['csrfToken'] ??= SecurityService::csrfToken();
        extract($data, EXTR_SKIP);
        require $file;
    }
}
