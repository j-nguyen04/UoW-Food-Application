<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\OrderModel;

class CheckoutController extends Controller
{
    // Show either the live cart checkout or the paid-order confirmation screen.
    public function show(): void
    {
        if (!isset($_SESSION['client_id'], $_SESSION['client_name'])) {
            $this->redirect('login.php');
        }

        require_once __DIR__ . '/../../stripe_config.php';

        $orderModel = new OrderModel($this->pdo);
        $isPaidView = isset($_GET['session_id']) && $_GET['session_id'] !== '';
        $orderPlaced = false;
        $orderDetails = [];
        $orderItems = [];
        $totalPrice = 0.0;
        $cartItems = [];
        $checkoutError = '';
        $checkoutNotice = '';

        if ($isPaidView) {
            $stripeSessionId = (string) $_GET['session_id'];
            $existingOrder = $orderModel->findByStripeSessionId($stripeSessionId);

            if ($existingOrder) {
                // If the order was already created for this Stripe session, just show the saved summary.
                [$orderDetails, $orderItems, $totalPrice] = $orderModel->summary(
                    $existingOrder['order_id'],
                    (int) $_SESSION['client_id']
                );
                $orderPlaced = $orderDetails !== null;
            } else {
                try {
                    // For the simplified prototype flow, payment is verified with Stripe
                    // when the user returns from Checkout rather than via webhooks.
                    $localOrderId = $orderModel->processPaidStripeCheckoutSession($stripeSessionId);
                    [$orderDetails, $orderItems, $totalPrice] = $orderModel->summary(
                        $localOrderId,
                        (int) $_SESSION['client_id']
                    );

                    $orderPlaced = $orderDetails !== null;
                    unset($_SESSION['cart']);
                } catch (\Throwable $exception) {
                    $checkoutError = $exception->getMessage();
                }
            }

            if (!$orderPlaced && $checkoutError === '') {
                $checkoutError = 'We could not find that paid order for your account.';
            }
        } else {
            // Before payment, this page acts as a final cart review screen.
            $cartItems = $orderModel->cartItems();
            $totalPrice = $orderModel->cartTotal($cartItems);

            if (empty($cartItems)) {
                $this->redirect('index.php');
            }

            if (isset($_GET['canceled']) && $_GET['canceled'] === '1') {
                $checkoutNotice = 'Stripe checkout was cancelled before payment was completed.';
            }
        }

        $this->render('checkout/show', compact(
            'isPaidView',
            'orderPlaced',
            'orderDetails',
            'orderItems',
            'totalPrice',
            'cartItems',
            'checkoutError',
            'checkoutNotice'
        ));
    }
}
