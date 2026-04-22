<?php

namespace App\Services;

use PDO;
use PDOException;
use RuntimeException;
use Throwable;

class OrderService
{
    public function __construct(private PDO $pdo)
    {
    }

    public function ensurePaymentSessionTable(): void
    {
        // Store the expected checkout contents locally before redirecting to Stripe so
        // the return flow can verify the paid session against known server-side values.
        $this->pdo->exec(
            "CREATE TABLE IF NOT EXISTS payment_sessions (
                stripe_checkout_session_id VARCHAR(255) PRIMARY KEY,
                user_id INT NOT NULL,
                items_json LONGTEXT NOT NULL,
                total_amount DECIMAL(10,2) NOT NULL,
                currency CHAR(3) NOT NULL DEFAULT 'GBP',
                processed_order_id CHAR(4) DEFAULT NULL,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                processed_at DATETIME DEFAULT NULL,
                CONSTRAINT payment_sessions_user_fk FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci"
        );
    }

    public function cartItemsFromSession(): array
    {
        if (empty($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
            return [];
        }

        $cartRows = [];

        foreach ($_SESSION['cart'] as $item) {
            $dishId = isset($item['id']) ? (int) $item['id'] : 0;
            $quantity = isset($item['quantity']) ? (int) $item['quantity'] : 0;

            if ($dishId > 0 && $quantity > 0) {
                $cartRows[$dishId] = $quantity;
            }
        }

        if (empty($cartRows)) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($cartRows), '?'));
        // Pull the latest dish data from the database so totals are always based on
        // current menu records rather than trusting stale session values alone.
        $stmt = $this->pdo->prepare("SELECT dish_id, dish_name, price, image_url FROM dishes WHERE dish_id IN ($placeholders)");
        $stmt->execute(array_keys($cartRows));
        $dishes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $items = [];

        foreach ($dishes as $dish) {
            $dishId = (int) $dish['dish_id'];
            $quantity = $cartRows[$dishId] ?? 0;

            if ($quantity <= 0) {
                continue;
            }

            $price = (float) $dish['price'];
            $items[] = [
                'id' => $dishId,
                'dish_name' => $dish['dish_name'],
                'image_url' => $dish['image_url'],
                'price' => $price,
                'quantity' => $quantity,
                'line_total' => round($price * $quantity, 2),
            ];
        }

        usort($items, static function (array $left, array $right): int {
            return $left['id'] <=> $right['id'];
        });

        return $items;
    }

    public function calculateCartTotal(array $items): float
    {
        $total = 0.0;

        foreach ($items as $item) {
            $total += (float) $item['line_total'];
        }

        return round($total, 2);
    }

    public function generateNextOrderId(): string
    {
        // Order ids keep the legacy human-readable C001, C002 format used by the admin UI.
        $stmt = $this->pdo->query("SELECT MAX(order_id) AS lastId FROM orders");
        $lastId = $stmt->fetchColumn();

        if ($lastId && preg_match('/\d+$/', $lastId, $matches)) {
            $nextNumber = (int) $matches[0] + 1;
            return 'C' . str_pad((string) $nextNumber, 3, '0', STR_PAD_LEFT);
        }

        return 'C001';
    }

    public function createPaidOrder(int $userId, array $items, array $paymentDetails): string
    {
        $orderId = $this->generateNextOrderId();
        // Orders and order_items must be written together so a paid order never exists
        // without its line items, or vice versa.
        $this->pdo->beginTransaction();

        try {
            $stmt = $this->pdo->prepare(
                "INSERT INTO orders (
                    order_id,
                    order_date,
                    status,
                    user_id,
                    payment_status,
                    payment_method,
                    stripe_checkout_session_id,
                    stripe_payment_intent_id,
                    payment_amount,
                    payment_currency
                ) VALUES (
                    :order_id,
                    NOW(),
                    'pending',
                    :user_id,
                    'paid',
                    :payment_method,
                    :stripe_checkout_session_id,
                    :stripe_payment_intent_id,
                    :payment_amount,
                    :payment_currency
                )"
            );

            $stmt->execute([
                'order_id' => $orderId,
                'user_id' => $userId,
                'payment_method' => $paymentDetails['payment_method'],
                'stripe_checkout_session_id' => $paymentDetails['stripe_checkout_session_id'] ?? null,
                'stripe_payment_intent_id' => $paymentDetails['stripe_payment_intent_id'] ?? null,
                'payment_amount' => $paymentDetails['payment_amount'],
                'payment_currency' => strtoupper($paymentDetails['payment_currency']),
            ]);

            $itemStmt = $this->pdo->prepare(
                "INSERT INTO order_items (order_id, dish_id, quantity) VALUES (:order_id, :dish_id, :quantity)"
            );

            foreach ($items as $item) {
                $itemStmt->execute([
                    'order_id' => $orderId,
                    'dish_id' => $item['id'],
                    'quantity' => $item['quantity'],
                ]);
            }

            $this->pdo->commit();
        } catch (Throwable $exception) {
            $this->pdo->rollBack();
            throw $exception;
        }

        return $orderId;
    }

    public function orderSummary(string $orderId, int $userId): array
    {
        // Scope the lookup to the current customer so one customer cannot load another
        // customer's order confirmation by guessing an order id.
        $stmt = $this->pdo->prepare("SELECT * FROM orders WHERE order_id = :order_id AND user_id = :user_id");
        $stmt->execute([
            'order_id' => $orderId,
            'user_id' => $userId,
        ]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$order) {
            return [null, [], 0.0];
        }

        $itemStmt = $this->pdo->prepare(
            "SELECT oi.quantity, d.dish_name, d.price, d.image_url
             FROM order_items oi
             JOIN dishes d ON oi.dish_id = d.dish_id
             WHERE oi.order_id = :order_id
             ORDER BY d.dish_name"
        );
        $itemStmt->execute(['order_id' => $orderId]);
        $items = $itemStmt->fetchAll(PDO::FETCH_ASSOC);

        $total = 0.0;
        foreach ($items as $item) {
            $total += ((float) $item['price']) * ((int) $item['quantity']);
        }

        return [$order, $items, round($total, 2)];
    }

    public function statusMeta(string $status): array
    {
        // Centralize the display label/CSS/description mapping so every order status
        // looks consistent between checkout, history, and admin pages.
        $normalizedStatus = strtolower(trim($status));

        $statusMap = [
            'pending' => [
                'label' => 'Pending',
                'class' => 'status-pending',
                'description' => 'We have received your order and will start preparing it shortly.',
            ],
            'in progress' => [
                'label' => 'In Progress',
                'class' => 'status-in-progress',
                'description' => 'Your order is currently being prepared.',
            ],
            'shipped' => [
                'label' => 'Shipped',
                'class' => 'status-shipped',
                'description' => 'Your order is on the way or ready for collection soon.',
            ],
            'delivered' => [
                'label' => 'Delivered',
                'class' => 'status-delivered',
                'description' => 'Your order has been completed successfully.',
            ],
            'cancelled' => [
                'label' => 'Cancelled',
                'class' => 'status-cancelled',
                'description' => 'This order was cancelled. Please contact support if this looks wrong.',
            ],
        ];

        return $statusMap[$normalizedStatus] ?? [
            'label' => ucwords($status),
            'class' => 'status-generic',
            'description' => 'Your order status was updated.',
        ];
    }

    public function userOrderHistory(int $userId): array
    {
        // This denormalized query returns one row per order item so the customer's
        // history can be rebuilt into grouped orders in a single round trip.
        $stmt = $this->pdo->prepare(
            "SELECT
                o.order_id,
                o.order_date,
                o.status,
                o.payment_status,
                o.payment_method,
                oi.quantity,
                d.dish_name,
                d.price,
                d.image_url
             FROM orders o
             LEFT JOIN order_items oi ON o.order_id = oi.order_id
             LEFT JOIN dishes d ON oi.dish_id = d.dish_id
             WHERE o.user_id = :user_id
             ORDER BY o.order_date DESC, d.dish_name ASC"
        );
        $stmt->execute(['user_id' => $userId]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($rows)) {
            return [];
        }

        $orders = [];

        foreach ($rows as $row) {
            $orderId = $row['order_id'];

            if (!isset($orders[$orderId])) {
                $orders[$orderId] = [
                    'order_id' => $orderId,
                    'order_date' => $row['order_date'],
                    'status' => $row['status'],
                    'payment_status' => $row['payment_status'] ?? 'paid',
                    'payment_method' => $row['payment_method'] ?? 'card',
                    'items' => [],
                    'item_count' => 0,
                    'total_price' => 0.0,
                ];
            }

            if ($row['dish_name'] === null) {
                continue;
            }

            $quantity = (int) $row['quantity'];
            $price = (float) $row['price'];

            $orders[$orderId]['items'][] = [
                'dish_name' => $row['dish_name'],
                'quantity' => $quantity,
                'price' => $price,
                'image_url' => $row['image_url'],
                'line_total' => round($quantity * $price, 2),
            ];
            $orders[$orderId]['item_count'] += $quantity;
            $orders[$orderId]['total_price'] += $quantity * $price;
        }

        foreach ($orders as &$order) {
            $order['total_price'] = round($order['total_price'], 2);
            $order['status_meta'] = $this->statusMeta($order['status']);
        }
        unset($order);

        return array_values($orders);
    }

    public function findOrderByStripeSessionId(string $stripeSessionId): ?array
    {
        $stmt = $this->pdo->prepare(
            "SELECT order_id
             FROM orders
             WHERE stripe_checkout_session_id = :stripe_checkout_session_id
             LIMIT 1"
        );
        $stmt->execute(['stripe_checkout_session_id' => $stripeSessionId]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);

        return $order ?: null;
    }

    public function storePendingStripeCheckout(string $sessionId, int $userId, array $items, float $total, string $currency): void
    {
        $this->ensurePaymentSessionTable();

        // The latest expected cart replaces any earlier attempt for the same checkout session.
        $stmt = $this->pdo->prepare(
            "INSERT INTO payment_sessions (
                stripe_checkout_session_id,
                user_id,
                items_json,
                total_amount,
                currency,
                processed_order_id,
                processed_at
            ) VALUES (
                :stripe_checkout_session_id,
                :user_id,
                :items_json,
                :total_amount,
                :currency,
                NULL,
                NULL
            )
            ON DUPLICATE KEY UPDATE
                user_id = VALUES(user_id),
                items_json = VALUES(items_json),
                total_amount = VALUES(total_amount),
                currency = VALUES(currency)"
        );
        $stmt->execute([
            'stripe_checkout_session_id' => $sessionId,
            'user_id' => $userId,
            'items_json' => json_encode($items, JSON_THROW_ON_ERROR),
            'total_amount' => round($total, 2),
            'currency' => strtoupper($currency),
        ]);
    }

    public function getPendingStripeCheckout(string $sessionId): ?array
    {
        $this->ensurePaymentSessionTable();

        $stmt = $this->pdo->prepare(
            "SELECT stripe_checkout_session_id, user_id, items_json, total_amount, currency, processed_order_id
             FROM payment_sessions
             WHERE stripe_checkout_session_id = :stripe_checkout_session_id
             LIMIT 1"
        );
        $stmt->execute(['stripe_checkout_session_id' => $sessionId]);
        $pending = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$pending) {
            return null;
        }

        $pending['items'] = json_decode((string) $pending['items_json'], true, 512, JSON_THROW_ON_ERROR);
        unset($pending['items_json']);

        return $pending;
    }

    public function markStripeCheckoutProcessed(string $sessionId, string $orderId): void
    {
        $this->ensurePaymentSessionTable();

        $stmt = $this->pdo->prepare(
            "UPDATE payment_sessions
             SET processed_order_id = :processed_order_id,
                 processed_at = NOW()
             WHERE stripe_checkout_session_id = :stripe_checkout_session_id"
        );
        $stmt->execute([
            'processed_order_id' => $orderId,
            'stripe_checkout_session_id' => $sessionId,
        ]);
    }

    public function processPaidStripeCheckoutSession(string $sessionId): string
    {
        $existingOrder = $this->findOrderByStripeSessionId($sessionId);

        if ($existingOrder) {
            // The return flow is idempotent: once an order exists for this Stripe
            // session, the app simply reuses it instead of creating duplicates.
            return (string) $existingOrder['order_id'];
        }

        $pendingCheckout = $this->getPendingStripeCheckout($sessionId);

        if (!$pendingCheckout) {
            throw new RuntimeException('We could not find a matching pending Stripe checkout.');
        }

        $session = $this->stripeApiRequest('GET', '/checkout/sessions/' . rawurlencode($sessionId));

        // Business rule: the local order is only created after Stripe explicitly says
        // the checkout session is paid and the captured totals still match.
        if (($session['payment_status'] ?? '') !== 'paid') {
            throw new RuntimeException('Stripe has not marked this checkout session as paid.');
        }

        $capturedTotal = ((int) ($session['amount_total'] ?? 0)) / 100;
        $expectedTotal = (float) $pendingCheckout['total_amount'];

        if (abs($capturedTotal - $expectedTotal) > 0.01) {
            throw new RuntimeException('The Stripe payment total does not match the expected checkout total.');
        }

        $sessionCurrency = strtoupper((string) ($session['currency'] ?? ''));
        $expectedCurrency = strtoupper((string) ($pendingCheckout['currency'] ?? ''));

        if ($sessionCurrency !== '' && $expectedCurrency !== '' && $sessionCurrency !== $expectedCurrency) {
            throw new RuntimeException('The Stripe payment currency does not match the expected checkout currency.');
        }

        try {
            $orderId = $this->createPaidOrder(
                (int) $pendingCheckout['user_id'],
                (array) $pendingCheckout['items'],
                [
                    'payment_method' => 'stripe',
                    'stripe_checkout_session_id' => $session['id'],
                    'stripe_payment_intent_id' => $session['payment_intent'] ?? null,
                    'payment_amount' => $capturedTotal,
                    'payment_currency' => $sessionCurrency !== '' ? $sessionCurrency : $expectedCurrency,
                ]
            );
        } catch (PDOException $exception) {
            if ($exception->getCode() !== '23000') {
                throw $exception;
            }

            // If two return requests race each other, fall back to the order that was
            // already inserted rather than treating the duplicate key as a hard failure.
            $existingOrder = $this->findOrderByStripeSessionId($sessionId);

            if (!$existingOrder) {
                throw $exception;
            }

            $orderId = (string) $existingOrder['order_id'];
        }

        $this->markStripeCheckoutProcessed($sessionId, $orderId);

        return $orderId;
    }

    public function jsonResponse(array $payload, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($payload);
        exit();
    }

    public function stripeApiRequest(string $method, string $path, array $payload = []): array
    {
        // Stripe is called server-to-server so secret keys never reach the browser.
        $ch = curl_init(STRIPE_API_BASE_URL . $path);
        $headers = [
            'Authorization: Bearer ' . STRIPE_SECRET_KEY,
        ];
        $options = [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => $headers,
        ];

        if (!empty($payload)) {
            $headers[] = 'Content-Type: application/x-www-form-urlencoded';
            $options[CURLOPT_HTTPHEADER] = $headers;
            $options[CURLOPT_POSTFIELDS] = http_build_query($payload);
        }

        curl_setopt_array($ch, $options);

        $response = curl_exec($ch);
        $curlError = curl_error($ch);
        $statusCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($response === false) {
            throw new RuntimeException('Stripe request failed: ' . $curlError);
        }

        $decoded = json_decode($response, true);

        if ($statusCode >= 400) {
            $message = $decoded['error']['message'] ?? $decoded['message'] ?? 'Stripe API error';
            throw new RuntimeException($message);
        }

        if (!is_array($decoded)) {
            throw new RuntimeException('Unexpected Stripe response received.');
        }

        return $decoded;
    }

}
