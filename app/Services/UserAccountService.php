<?php

namespace App\Services;

use InvalidArgumentException;
use PDO;

class UserAccountService
{
    private AuthService $authService;

    public function __construct(private PDO $pdo)
    {
        $this->authService = new AuthService($pdo);
    }

    public function profile(int $userId): ?array
    {
        $this->authService->ensureSchema();

        $stmt = $this->pdo->prepare("SELECT user_id, last_name, first_name, email, phone, password_hash FROM users WHERE user_id = :user_id LIMIT 1");
        $stmt->execute(['user_id' => $userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        return $user ?: null;
    }

    public function updateProfile(
        int $userId,
        string $lastName,
        string $firstName,
        string $email,
        string $phone,
        string $currentPassword = '',
        string $newPassword = '',
        string $confirmPassword = ''
    ): void {
        $this->authService->ensureSchema();

        $lastName = trim($lastName);
        $firstName = trim($firstName);
        $email = $this->authService->normalizeEmail($email);
        $phone = trim($phone);
        $currentPassword = trim($currentPassword);
        $newPassword = trim($newPassword);
        $confirmPassword = trim($confirmPassword);

        if ($lastName === '' || $firstName === '' || $email === '' || $phone === '') {
            throw new InvalidArgumentException('First name, last name, email, and phone number are required.');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Please enter a valid email address.');
        }

        $this->authService->validatePhoneNumber($phone);

        // Email and phone stay unique across the customer table so accounts can be
        // reliably identified during login and customer support checks.
        $phoneCheck = $this->pdo->prepare(
            "SELECT user_id FROM users WHERE phone = :phone AND user_id <> :user_id LIMIT 1"
        );
        $phoneCheck->execute([
            'phone' => $phone,
            'user_id' => $userId,
        ]);

        if ($phoneCheck->fetch(PDO::FETCH_ASSOC)) {
            throw new InvalidArgumentException('That phone number is already being used by another account.');
        }

        $emailCheck = $this->pdo->prepare(
            "SELECT user_id FROM users WHERE email = :email AND user_id <> :user_id LIMIT 1"
        );
        $emailCheck->execute([
            'email' => $email,
            'user_id' => $userId,
        ]);

        if ($emailCheck->fetch(PDO::FETCH_ASSOC)) {
            throw new InvalidArgumentException('That email address is already being used by another account.');
        }

        $currentUser = $this->profile($userId);
        $existingPasswordHash = $currentUser['password_hash'] ?? null;
        $passwordHash = null;

        if ($newPassword !== '' || $confirmPassword !== '' || $currentPassword !== '') {
            if ($newPassword === '' || $confirmPassword === '') {
                throw new InvalidArgumentException('Please enter and confirm your new password.');
            }

            if ($newPassword !== $confirmPassword) {
                throw new InvalidArgumentException('New password and confirmation do not match.');
            }

            if (strlen($newPassword) < 8) {
                throw new InvalidArgumentException('New password must be at least 8 characters long.');
            }

            if (!empty($existingPasswordHash)) {
                $passwordUser = [
                    'user_id' => $userId,
                    'password_hash' => $existingPasswordHash,
                ];

                // Existing accounts must prove knowledge of the current password
                // before changing it, but older password-less accounts can set one.
                if ($currentPassword === '' || !$this->authService->verifyUserPassword($passwordUser, $currentPassword)) {
                    throw new InvalidArgumentException('Your current password is incorrect.');
                }
            }

            $passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);
        }

        if ($passwordHash !== null) {
            $stmt = $this->pdo->prepare(
                "UPDATE users
                 SET last_name = :last_name,
                     first_name = :first_name,
                     email = :email,
                     phone = :phone,
                     password_hash = :password_hash
                 WHERE user_id = :user_id"
            );
            $stmt->execute([
                'last_name' => $lastName,
                'first_name' => $firstName,
                'email' => $email,
                'phone' => $phone,
                'password_hash' => $passwordHash,
                'user_id' => $userId,
            ]);
            return;
        }

        $stmt = $this->pdo->prepare(
            "UPDATE users
             SET last_name = :last_name,
                 first_name = :first_name,
                 email = :email,
                 phone = :phone
             WHERE user_id = :user_id"
        );
        $stmt->execute([
            'last_name' => $lastName,
            'first_name' => $firstName,
            'email' => $email,
            'phone' => $phone,
            'user_id' => $userId,
        ]);
    }
}
