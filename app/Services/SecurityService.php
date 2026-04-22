<?php

namespace App\Services;

use RuntimeException;

final class SecurityService
{
    private const CSRF_SESSION_KEY = '_csrf_token';

    // Reuse one token per session so every rendered form can include it cheaply.
    public static function csrfToken(): string
    {
        if (empty($_SESSION[self::CSRF_SESSION_KEY]) || !is_string($_SESSION[self::CSRF_SESSION_KEY])) {
            $_SESSION[self::CSRF_SESSION_KEY] = bin2hex(random_bytes(32));
        }

        return $_SESSION[self::CSRF_SESSION_KEY];
    }

    public static function regenerateSession(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            // Regenerate the session id after login to reduce session fixation risk.
            session_regenerate_id(true);
        }
    }

    public static function validateCsrf(?string $token): bool
    {
        $sessionToken = $_SESSION[self::CSRF_SESSION_KEY] ?? '';

        return is_string($sessionToken)
            && is_string($token)
            && $sessionToken !== ''
            && hash_equals($sessionToken, $token);
    }

    public static function validateRequestOrFail(): void
    {
        // Accept the token from either a standard form field or an AJAX header.
        $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;

        if (!self::validateCsrf(is_string($token) ? $token : null)) {
            throw new RuntimeException('Your session security token is invalid. Please refresh the page and try again.');
        }
    }
}
