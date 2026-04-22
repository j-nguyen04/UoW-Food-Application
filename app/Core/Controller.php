<?php

namespace App\Core;

use App\Services\SecurityService;
use PDO;

abstract class Controller
{
    public function __construct(protected PDO $pdo)
    {
    }

    // Render a named view with its prepared data so controllers stay focused on
    // request orchestration rather than raw PHP templates.
    protected function render(string $view, array $data = []): void
    {
        View::render($view, $data);
    }

    // Central redirect helper used by controllers after successful POST actions
    // to enforce the common POST/Redirect/GET pattern.
    protected function redirect(string $location): void
    {
        header('Location: ' . $location);
        exit();
    }

    // CSRF validation is shared so state-changing actions stay consistent across controllers.
    protected function validateCsrf(): void
    {
        SecurityService::validateRequestOrFail();
    }
}
