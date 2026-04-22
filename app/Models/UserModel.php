<?php

namespace App\Models;

use App\Services\AuthService;
use App\Services\UserAccountService;
use PDO;
use PDOException;

class UserModel
{
    private AuthService $authService;
    private UserAccountService $userAccountService;

    public function __construct(private PDO $pdo)
    {
        $this->authService = new AuthService($pdo);
        $this->userAccountService = new UserAccountService($pdo);
        $this->authService->ensureSchema();
    }

    public function findByEmail(string $email): ?array
    {
        return $this->authService->findUserByEmail($email);
    }

    public function findProfile(int $userId): ?array
    {
        return $this->userAccountService->profile($userId);
    }

    public function create(array $data): int
    {
        $firstName = trim((string) ($data['first_name'] ?? ''));
        $lastName = trim((string) ($data['last_name'] ?? ''));
        $email = $this->authService->normalizeEmail((string) ($data['email'] ?? ''));
        $phone = trim((string) ($data['phone'] ?? ''));
        $password = (string) ($data['password'] ?? '');

        $this->authService->validateSignupInput($firstName, $lastName, $email, $phone, $password);

        // Registration blocks duplicate phone numbers and emails so login, profile
        // updates, and order ownership stay tied to a single account.
        $stmt = $this->pdo->prepare("SELECT user_id FROM users WHERE phone = :phone OR email = :email LIMIT 1");
        $stmt->execute([
            'phone' => $phone,
            'email' => $email,
        ]);

        if ($stmt->fetch(PDO::FETCH_ASSOC)) {
            throw new \InvalidArgumentException('That email or phone number is already registered. Please log in.');
        }

        // The legacy schema does not use AUTO_INCREMENT for user_id, so the next
        // numeric id is derived manually before insert.
        $stmt = $this->pdo->prepare("SELECT MAX(user_id) AS last_id FROM users");
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $newId = (int) (($row['last_id'] ?? 0) + 1);

        $insert = $this->pdo->prepare(
            "INSERT INTO users (user_id, last_name, first_name, email, phone, password_hash)
             VALUES (:user_id, :last_name, :first_name, :email, :phone, :password_hash)"
        );
        $insert->execute([
            'user_id' => $newId,
            'last_name' => $lastName,
            'first_name' => $firstName,
            'email' => $email,
            'phone' => $phone,
            'password_hash' => password_hash($password, PASSWORD_DEFAULT),
        ]);

        return $newId;
    }

    public function updateProfile(int $userId, array $data): void
    {
        $this->userAccountService->updateProfile(
            $userId,
            (string) ($data['last_name'] ?? ''),
            (string) ($data['first_name'] ?? ''),
            (string) ($data['email'] ?? ''),
            (string) ($data['phone'] ?? ''),
            (string) ($data['current_password'] ?? ''),
            (string) ($data['new_password'] ?? ''),
            (string) ($data['confirm_password'] ?? '')
        );
    }

    public function all(): array
    {
        $stmt = $this->pdo->query("SELECT user_id, last_name, first_name, email, phone FROM users ORDER BY user_id ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function delete(int $userId): void
    {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM users WHERE user_id = :user_id");
            $stmt->execute(['user_id' => $userId]);
        } catch (PDOException $exception) {
            if ($exception->getCode() === '23000') {
                // Users with related orders or feedback remain protected by foreign keys.
                throw new \RuntimeException('This client cannot be deleted because they still have related orders or feedback.');
            }

            throw $exception;
        }
    }
}
