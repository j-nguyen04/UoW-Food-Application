<?php

namespace App\Models;

use App\Services\DietaryService;
use PDO;

class MenuModel
{
    private DietaryService $dietaryService;

    public function __construct(private PDO $pdo)
    {
        $this->dietaryService = new DietaryService($pdo);
        $this->dietaryService->ensureSchema();
    }

    public function dietaryOptions(): array
    {
        return $this->dietaryService->options();
    }

    public function dishes(array $filters): array
    {
        $cuisineType = (string) ($filters['cuisine_type'] ?? 'ALL');
        $category = (string) ($filters['category'] ?? 'ALL');
        $dietary = strtolower(trim((string) ($filters['dietary'] ?? 'ALL')));
        $dietaryOptions = $this->dietaryOptions();

        $sql = "SELECT * FROM dishes";
        $params = [];
        $conditions = [];

        if ($cuisineType !== 'ALL') {
            $conditions[] = "cuisine_type = :cuisine_type";
            $params['cuisine_type'] = $cuisineType;
        }

        if ($category !== 'ALL') {
            $conditions[] = "category = :category";
            $params['category'] = $category;
        }

        if ($dietary !== 'ALL' && isset($dietaryOptions[$dietary])) {
            // Dietary labels are stored as a comma-separated column, so the lookup
            // normalizes the value and searches for a whole-label match.
            $conditions[] = "CONCAT(',', REPLACE(LOWER(COALESCE(dietary_labels, '')), ' ', ''), ',') LIKE :dietary";
            $params['dietary'] = '%,' . $dietary . ',%';
        }

        if (!empty($conditions)) {
            $sql .= ' WHERE ' . implode(' AND ', $conditions);
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $dishes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $grouped = [];
        // The homepage groups dishes by cuisine to preserve the original menu sections.
        foreach ($dishes as $dish) {
            $grouped[$dish['cuisine_type']][] = $dish;
        }

        return $grouped;
    }

    public function addToCart(int $dishId): void
    {
        $stmt = $this->pdo->prepare("SELECT * FROM dishes WHERE dish_id = ?");
        $stmt->execute([$dishId]);
        $dish = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$dish) {
            return;
        }

        $item = [
            'id' => $dish['dish_id'],
            'name' => $dish['dish_name'],
            'price' => $dish['price'],
            'image_url' => $dish['image_url'],
            'quantity' => 1,
        ];

        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        $found = false;
        // Cart rows are merged by dish id so the same dish increments quantity instead
        // of producing duplicate cart entries.
        foreach ($_SESSION['cart'] as &$cartItem) {
            if ($cartItem['id'] == $dishId) {
                $cartItem['quantity'] += 1;
                $found = true;
                break;
            }
        }
        unset($cartItem);

        if (!$found) {
            $_SESSION['cart'][] = $item;
        }
    }

    public function dishLabels(?string $dietaryLabels): array
    {
        return $this->dietaryService->labels($dietaryLabels);
    }
}
