<?php

namespace App\Services;

use InvalidArgumentException;
use PDO;

class AuthService
{
    public function __construct(private PDO $pdo)
    {
    }

    // Keep the migrated auth schema in place even on databases imported from older versions.
    public function ensureSchema(): void
    {
        static $schemaReady = false;

        if ($schemaReady) {
            return;
        }

        $emailColumn = $this->pdo->query("SHOW COLUMNS FROM users LIKE 'email'");
        if ($emailColumn->fetch(PDO::FETCH_ASSOC) === false) {
            $this->pdo->exec("ALTER TABLE users ADD COLUMN email VARCHAR(150) DEFAULT NULL AFTER first_name");
        }

        $passwordColumn = $this->pdo->query("SHOW COLUMNS FROM users LIKE 'password_hash'");
        if ($passwordColumn->fetch(PDO::FETCH_ASSOC) === false) {
            $this->pdo->exec("ALTER TABLE users ADD COLUMN password_hash VARCHAR(255) DEFAULT NULL AFTER phone");
        }

        $emailIndex = $this->pdo->query("SHOW INDEX FROM users WHERE Key_name = 'email'");
        if ($emailIndex->fetch(PDO::FETCH_ASSOC) === false) {
            $this->pdo->exec("ALTER TABLE users ADD UNIQUE KEY email (email)");
        }

        $schemaReady = true;
    }

    // Normalize emails once so uniqueness checks and login lookups are case-insensitive.
    public function normalizeEmail(string $email): string
    {
        return strtolower(trim($email));
    }

    // Validate the customer registration rules enforced by the business layer.
    public function validateSignupInput(string $firstName, string $lastName, string $email, string $phone, string $password): void
    {
        if ($firstName === '' || $lastName === '' || $email === '' || $phone === '' || $password === '') {
            throw new InvalidArgumentException('Please complete all sign up fields.');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Please enter a valid email address.');
        }

        $this->validatePhoneNumber($phone);

        if (strlen($password) < 8) {
            throw new InvalidArgumentException('Password must be at least 8 characters long.');
        }
    }

    public function validatePhoneNumber(string $phone): void
    {
        // Validation is based on digit count rather than a country-specific format
        // so the application still accepts international numbers.
        $normalizedPhone = preg_replace('/\D+/', '', $phone) ?? '';

        if (strlen($normalizedPhone) < 7 || strlen($normalizedPhone) > 15) {
            throw new InvalidArgumentException('Please enter a valid phone number.');
        }
    }

    public function findUserByEmail(string $email): ?array
    {
        $this->ensureSchema();

        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
        $stmt->execute(['email' => $this->normalizeEmail($email)]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        return $user ?: null;
    }

    public function isPasswordHashValue(string $storedPassword): bool
    {
        $info = password_get_info($storedPassword);
        return !empty($info['algo']);
    }

    public function verifyUserPassword(array $user, string $password): bool
    {
        $storedPassword = (string) ($user['password_hash'] ?? '');

        if ($storedPassword === '') {
            return false;
        }

        if ($this->isPasswordHashValue($storedPassword)) {
            if (!password_verify($password, $storedPassword)) {
                return false;
            }

            // Rehashing keeps stored passwords aligned with newer PHP defaults over time.
            if (password_needs_rehash($storedPassword, PASSWORD_DEFAULT)) {
                $this->upgradeUserPasswordHash((int) $user['user_id'], $password);
            }

            return true;
        }

        // Older accounts may still have a legacy plain-text password in password_hash.
        // A successful login upgrades it immediately to a secure hash.
        if (!hash_equals($storedPassword, $password)) {
            return false;
        }

        $this->upgradeUserPasswordHash((int) $user['user_id'], $password);
        return true;
    }

    public function upgradeUserPasswordHash(int $userId, string $password): void
    {
        $stmt = $this->pdo->prepare(
            "UPDATE users
             SET password_hash = :password_hash
             WHERE user_id = :user_id"
        );
        $stmt->execute([
            'password_hash' => password_hash($password, PASSWORD_DEFAULT),
            'user_id' => $userId,
        ]);
    }
}
