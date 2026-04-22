<?php

namespace App\Models;

use App\Services\FeedbackService;
use PDO;

class FeedbackModel
{
    private FeedbackService $feedbackService;

    public function __construct(private PDO $pdo)
    {
        // The model delegates to the service layer but ensures the feedback feature
        // is ready before any controller tries to read or write it.
        $this->feedbackService = new FeedbackService($pdo);
        $this->feedbackService->ensureTableExists();
    }

    public function create(int $userId, int $rating, string $message): void
    {
        $this->feedbackService->create($userId, $rating, $message);
    }

    public function forUser(int $userId): array
    {
        return $this->feedbackService->forUser($userId);
    }

    public function recent(int $limit = 6): array
    {
        return $this->feedbackService->recent($limit);
    }

    public function all(): array
    {
        return $this->feedbackService->all();
    }
}
