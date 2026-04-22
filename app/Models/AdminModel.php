<?php

namespace App\Models;

use InvalidArgumentException;
use PDO;

class AdminModel
{
    public function __construct(private PDO $pdo)
    {
    }

    public function findByUsername(string $username): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM admin WHERE username = :username LIMIT 1');
        $stmt->execute(['username' => trim($username)]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        return $admin ?: null;
    }

    public function verifyCredentials(string $username, string $password): ?array
    {
        $admin = $this->findByUsername($username);

        if (!$admin) {
            return null;
        }

        $storedPassword = (string) ($admin['password'] ?? '');
        $password = trim($password);

        if ($storedPassword === '') {
            return null;
        }

        $info = password_get_info($storedPassword);
        $isHash = !empty($info['algo']);

        if ($isHash) {
            if (!password_verify($password, $storedPassword)) {
                return null;
            }

            if (password_needs_rehash($storedPassword, PASSWORD_DEFAULT)) {
                $this->upgradePasswordHash((int) $admin['id'], $password);
            }

            return $admin;
        }

        // Legacy plain-text admin passwords are still accepted once, then upgraded
        // immediately so older records do not block access during migration.
        if (!hash_equals($storedPassword, $password)) {
            return null;
        }

        $this->upgradePasswordHash((int) $admin['id'], $password);
        return $admin;
    }

    public function all(): array
    {
        $stmt = $this->pdo->query('SELECT id, username, password FROM admin ORDER BY id ASC');
        $admins = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($admins as &$admin) {
            // Only expose the password storage state to the view, never the value itself.
            $admin['password_status'] = $this->isPasswordHash((string) ($admin['password'] ?? ''))
                ? 'Hashed'
                : 'Legacy Plain Text';
            unset($admin['password']);
        }
        unset($admin);

        return $admins;
    }

    public function hashLegacyPasswords(): int
    {
        $stmt = $this->pdo->query('SELECT id, password FROM admin');
        $admins = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $updatedCount = 0;

        foreach ($admins as $admin) {
            $storedPassword = (string) ($admin['password'] ?? '');

            // Already-hashed or blank records are skipped so the admin can run this
            // utility safely multiple times.
            if ($storedPassword === '' || $this->isPasswordHash($storedPassword)) {
                continue;
            }

            $this->upgradePasswordHash((int) $admin['id'], $storedPassword);
            $updatedCount++;
        }

        return $updatedCount;
    }

    public function resetPassword(int $adminId, string $newPassword, string $confirmPassword): void
    {
        $newPassword = trim($newPassword);
        $confirmPassword = trim($confirmPassword);

        if ($adminId <= 0) {
            throw new InvalidArgumentException('Please choose a valid admin account.');
        }

        if ($newPassword === '' || $confirmPassword === '') {
            throw new InvalidArgumentException('Please enter and confirm the new password.');
        }

        if ($newPassword !== $confirmPassword) {
            throw new InvalidArgumentException('The new password and confirmation do not match.');
        }

        if (strlen($newPassword) < 8) {
            throw new InvalidArgumentException('Admin passwords must be at least 8 characters long.');
        }

        $this->upgradePasswordHash($adminId, $newPassword);
    }

    private function isPasswordHash(string $password): bool
    {
        $info = password_get_info($password);
        return !empty($info['algo']);
    }

    private function upgradePasswordHash(int $adminId, string $password): void
    {
        $stmt = $this->pdo->prepare('UPDATE admin SET password = :password WHERE id = :id');
        $stmt->execute([
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'id' => $adminId,
        ]);
    }
}
