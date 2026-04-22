<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\OrderModel;
use App\Services\OrderService;

class StripeCheckoutSessionController extends Controller
{
    // Create a Stripe Checkout session for the current cart after validating the session and CSRF token.
    public function create(): void
    {
        require_once __DIR__ . '/../../stripe_config.php';
        $orderService = new OrderService($this->pdo);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $orderService->jsonResponse(['message' => 'Method not allowed.'], 405);
        }

        try {
            $this->validateCsrf();
        } catch (\Throwable $exception) {
            $orderService->jsonResponse(['message' => $exception->getMessage()], 419);
        }

        if (!isset($_SESSION['client_id'])) {
            $orderService->jsonResponse(['message' => 'Please log in before checking out.'], 401);
        }

        if (!stripeIsConfigured()) {
            $orderService->jsonResponse(['message' => 'Stripe keys are not configured yet.'], 500);
        }

        $orderModel = new OrderModel($this->pdo);
        $items = $orderModel->cartItems();

        if (empty($items)) {
            $orderService->jsonResponse(['message' => 'Your cart is empty.'], 400);
        }

        $total = $orderModel->cartTotal($items);
        $baseUrl = appBaseUrl();
        $payload = [
            'mode' => 'payment',
            'success_url' => $baseUrl . '/checkout.php?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => $baseUrl . '/checkout.php?canceled=1',
            // Store the current customer id in Stripe metadata for easier auditing
            // and to make payment records easier to reconcile during testing.
            'client_reference_id' => (string) $_SESSION['client_id'],
            'metadata[user_id]' => (string) $_SESSION['client_id'],
        ];

        foreach ($items as $index => $item) {
            $payload["line_items[$index][price_data][currency]"] = STRIPE_CURRENCY;
            $payload["line_items[$index][price_data][product_data][name]"] = $item['dish_name'];
            $payload["line_items[$index][price_data][unit_amount]"] = (int) round(((float) $item['price']) * 100);
            $payload["line_items[$index][quantity]"] = (int) $item['quantity'];
        }

        try {
            $session = $orderService->stripeApiRequest('POST', '/checkout/sessions', $payload);
            // Persist the expected items and amount locally so the return flow can
            // verify that the paid Stripe session still matches what the customer saw.
            $orderModel->storePendingStripeCheckout(
                (string) $session['id'],
                (int) $_SESSION['client_id'],
                $items,
                $total,
                STRIPE_CURRENCY
            );

            $orderService->jsonResponse([
                'id' => $session['id'] ?? null,
                'url' => $session['url'] ?? null,
            ]);
        } catch (\Throwable $exception) {
            $orderService->jsonResponse(['message' => $exception->getMessage()], 500);
        }
    }
}
