<?php

namespace App\Models;

use PDO;

class AdminDashboardModel
{
    public function __construct(private PDO $pdo)
    {
    }

    public function overview(string $today): array
    {
        // Dashboard metrics are anchored to the selected calendar day so the summary
        // cards and the order table always describe the same reporting window.
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM orders WHERE DATE(order_date) = :today");
        $stmt->execute(['today' => $today]);
        $totalOrders = (int) $stmt->fetchColumn();

        $stmt = $this->pdo->query("SELECT COUNT(*) FROM users");
        $totalClients = (int) $stmt->fetchColumn();

        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM orders WHERE DATE(order_date) = :today AND status = 'cancelled'");
        $stmt->execute(['today' => $today]);
        $canceledOrders = (int) $stmt->fetchColumn();

        $stmt = $this->pdo->query("SELECT COUNT(*) FROM dishes");
        $totalDishes = (int) $stmt->fetchColumn();

        $ordersStmt = $this->pdo->prepare(
            "SELECT o.order_id, o.order_date, o.status, u.last_name, u.first_name
             FROM orders o
             JOIN users u ON o.user_id = u.user_id
             WHERE DATE(o.order_date) = :today"
        );
        $ordersStmt->execute(['today' => $today]);

        // Aggregate the most-ordered dishes for the same day to give admins a quick
        // operational view of what sold best.
        $topStmt = $this->pdo->prepare(
            "SELECT d.dish_name, SUM(oi.quantity) AS totalOrdered
             FROM order_items oi
             JOIN dishes d ON oi.dish_id = d.dish_id
             JOIN orders o ON oi.order_id = o.order_id
             WHERE DATE(o.order_date) = :today
             GROUP BY d.dish_name
             ORDER BY totalOrdered DESC
             LIMIT 5"
        );
        $topStmt->execute(['today' => $today]);

        return [
            'totalOrders' => $totalOrders,
            'totalClients' => $totalClients,
            'canceledOrders' => $canceledOrders,
            'totalDishes' => $totalDishes,
            'orders' => $ordersStmt->fetchAll(PDO::FETCH_ASSOC),
            'topDishes' => $topStmt->fetchAll(PDO::FETCH_ASSOC),
        ];
    }

    public function updateOrderStatuses(array $statusUpdates): void
    {
        $stmt = $this->pdo->prepare("UPDATE orders SET status = :status WHERE order_id = :order_id");

        foreach ($statusUpdates as $orderId => $status) {
            $stmt->execute([
                'status' => $status,
                'order_id' => $orderId,
            ]);
        }
    }
}
