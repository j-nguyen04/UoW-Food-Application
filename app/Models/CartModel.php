<?php

namespace App\Models;

class CartModel
{
    public function ensureCart(): void
    {
        // Cart actions always normalize the session shape before mutating it.
        if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
    }

    public function remove(int $dishId): void
    {
        $this->ensureCart();

        foreach ($_SESSION['cart'] as $key => $item) {
            if ((int) ($item['id'] ?? 0) === $dishId) {
                unset($_SESSION['cart'][$key]);
                break;
            }
        }

        $_SESSION['cart'] = array_values($_SESSION['cart']);
    }

    public function updateQuantity(int $dishId, string $direction): void
    {
        $this->ensureCart();

        foreach ($_SESSION['cart'] as &$item) {
            if ((int) ($item['id'] ?? 0) !== $dishId) {
                continue;
            }

            if ($direction === 'increase') {
                $item['quantity'] = ((int) ($item['quantity'] ?? 0)) + 1;
            } elseif ($direction === 'decrease' && (int) ($item['quantity'] ?? 0) > 1) {
                // The quantity never drops below 1 here; full removal uses the separate remove action.
                $item['quantity'] = ((int) ($item['quantity'] ?? 0)) - 1;
            }

            break;
        }
        unset($item);
    }
}
