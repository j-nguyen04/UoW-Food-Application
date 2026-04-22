<?php

namespace App\Models;

use App\Services\OrderService;
use PDO;

class OrderModel
{
    private OrderService $orderService;

    public function __construct(private PDO $pdo)
    {
        // Keep payment and order rules in OrderService so controllers stay thin.
        $this->orderService = new OrderService($pdo);
    }

    public function cartItems(): array
    {
        return $this->orderService->cartItemsFromSession();
    }

    public function cartTotal(array $items): float
    {
        return $this->orderService->calculateCartTotal($items);
    }

    public function history(int $userId): array
    {
        return $this->orderService->userOrderHistory($userId);
    }

    public function findByStripeSessionId(string $sessionId): ?array
    {
        return $this->orderService->findOrderByStripeSessionId($sessionId);
    }

    public function summary(string $orderId, int $userId): array
    {
        return $this->orderService->orderSummary($orderId, $userId);
    }

    public function createPaidOrder(int $userId, array $items, array $paymentDetails): string
    {
        return $this->orderService->createPaidOrder($userId, $items, $paymentDetails);
    }

    public function storePendingStripeCheckout(string $sessionId, int $userId, array $items, float $total, string $currency): void
    {
        $this->orderService->storePendingStripeCheckout($sessionId, $userId, $items, $total, $currency);
    }

    public function processPaidStripeCheckoutSession(string $sessionId): string
    {
        return $this->orderService->processPaidStripeCheckoutSession($sessionId);
    }
}
