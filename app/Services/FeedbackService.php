<?php

namespace App\Services;

use InvalidArgumentException;
use PDO;

class FeedbackService
{
    public function __construct(private PDO $pdo)
    {
    }

    public function ensureTableExists(): void
    {
        // The feedback table is provisioned lazily so the feature can run even if the
        // migration was not executed manually before first use.
        $this->pdo->exec(
            "CREATE TABLE IF NOT EXISTS feedback (
                feedback_id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                rating TINYINT NOT NULL,
                message TEXT NOT NULL,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                CONSTRAINT feedback_user_fk FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci"
        );
    }

    public function create(int $userId, int $rating, string $message): void
    {
        $this->ensureTableExists();

        $message = trim($message);

        if ($rating < 1 || $rating > 5) {
            throw new InvalidArgumentException('Please choose a rating between 1 and 5.');
        }

        if ($message === '') {
            throw new InvalidArgumentException('Please write a short feedback message.');
        }

        $stmt = $this->pdo->prepare(
            "INSERT INTO feedback (user_id, rating, message)
             VALUES (:user_id, :rating, :message)"
        );
        $stmt->execute([
            'user_id' => $userId,
            'rating' => $rating,
            'message' => $message,
        ]);
    }

    public function forUser(int $userId): array
    {
        $this->ensureTableExists();

        $stmt = $this->pdo->prepare(
            "SELECT feedback_id, rating, message, created_at
             FROM feedback
             WHERE user_id = :user_id
             ORDER BY created_at DESC"
        );
        $stmt->execute(['user_id' => $userId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function recent(int $limit = 6): array
    {
        $this->ensureTableExists();

        // Join customer names so the homepage and feedback page can show recent
        // community comments without making an extra lookup per row.
        $stmt = $this->pdo->prepare(
            "SELECT f.feedback_id, f.rating, f.message, f.created_at, u.first_name, u.last_name
             FROM feedback f
             JOIN users u ON f.user_id = u.user_id
             ORDER BY f.created_at DESC
             LIMIT :limit"
        );
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function all(): array
    {
        $this->ensureTableExists();

        $stmt = $this->pdo->query(
            "SELECT f.feedback_id, f.rating, f.message, f.created_at, u.user_id, u.first_name, u.last_name, u.phone
             FROM feedback f
             JOIN users u ON f.user_id = u.user_id
             ORDER BY f.created_at DESC"
        );

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
