<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="frontend/css/checkout.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css">
    <link rel="stylesheet" href="frontend/css/style.css?v=<?php echo time(); ?>">
    <title>Checkout - Order Confirmation</title>
</head>
<body>
    <?php include __DIR__ . '/../partials/site-header.php'; ?>

    <section class="checkout-section">
        <?php if ($isPaidView && $orderPlaced): ?>
            <h1 class="heading">Thank You for Your Order!</h1>
            <div class="order-confirmation">
                <p class="order-greeting">
                    Dear <span class="client-name"><?= htmlspecialchars($_SESSION['client_name']) ?></span>,
                    your payment was successful and your order has been placed.
                </p>
                <div class="order-details">
                    <p class="order-date"><span class="label">Order Date:</span> <?= htmlspecialchars($orderDetails['order_date']) ?></p>
                    <p class="order-status"><span class="label">Order Status:</span> <span class="status"><?= htmlspecialchars($orderDetails['status']) ?></span></p>
                    <p class="order-status"><span class="label">Payment:</span> <span class="status"><?= htmlspecialchars($orderDetails['payment_status'] ?? 'paid') ?></span></p>
                    <p class="order-status"><span class="label">Method:</span> <span class="status"><?= htmlspecialchars($orderDetails['payment_method'] ?? 'stripe') ?></span></p>
                </div>
                <h3 class="sub-heading">Ordered Items</h3>
                <ul class="order-items">
                    <?php foreach ($orderItems as $item): ?>
                        <li class="order-item">
                            <img src="<?= htmlspecialchars($item['image_url']) ?>" alt="<?= htmlspecialchars($item['dish_name']) ?>" class="item-image">
                            <div class="item-details">
                                <span class="item-name"><?= htmlspecialchars($item['dish_name']) ?></span>
                                <span class="item-quantity">(<?= (int) $item['quantity'] ?>)</span>
                                <span class="item-price"><?= htmlspecialchars((string) $item['price']) ?> GBP</span>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <div class="order-total"><h3>Total: <span class="total-amount"><?= htmlspecialchars((string) $totalPrice) ?> GBP</span></h3></div>
                <p class="order-thanks">Thank for the order! Please wait for your order whilist its processing. We will let you know once it's ready for collection.</p>
                <a href="order-history.php" class="btn">Track This Order</a>
                <a href="index.php" class="btn">Return to Home</a>
            </div>
        <?php else: ?>
            <h1 class="heading">Checkout</h1>
            <div class="order-confirmation">
                <p class="order-greeting">
                    Dear <span class="client-name"><?= htmlspecialchars($_SESSION['client_name']) ?></span>,
                    please review your order and complete payment with Stripe.
                </p>
                <?php if ($checkoutError !== ''): ?><div class="order-error"><p class="error-message"><?= htmlspecialchars($checkoutError) ?></p></div><?php endif; ?>
                <?php if ($checkoutNotice !== ''): ?><div class="order-error"><p class="error-message"><?= htmlspecialchars($checkoutNotice) ?></p></div><?php endif; ?>

                <h3 class="sub-heading">Items in Your Cart</h3>
                <ul class="order-items">
                    <?php foreach ($cartItems as $item): ?>
                        <li class="order-item">
                            <img src="<?= htmlspecialchars($item['image_url']) ?>" alt="<?= htmlspecialchars($item['dish_name']) ?>" class="item-image">
                            <div class="item-details">
                                <span class="item-name"><?= htmlspecialchars($item['dish_name']) ?></span>
                                <span class="item-quantity">(<?= (int) $item['quantity'] ?>)</span>
                                <span class="item-price"><?= number_format((float) $item['price'], 2) ?> GBP</span>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <div class="order-total"><h3>Total: <span class="total-amount"><?= number_format($totalPrice, 2) ?> GBP</span></h3></div>

                <div class="checkout-actions">
                    <?php if (stripeIsConfigured()): ?>
                        <p class="payment-note">Pay securely with Stripe Checkout. Your order will only be created after your app verifies Stripe marked the payment as paid.</p>
                        <button type="button" id="stripe-checkout-button" class="btn" data-csrf-token="<?= htmlspecialchars($csrfToken) ?>">Pay with Stripe</button>
                        <p id="stripe-message" class="payment-warning" hidden></p>
                    <?php else: ?>
                        <div class="order-error"><p class="error-message">Stripe is not configured yet. Set `STRIPE_SECRET_KEY`, `STRIPE_PUBLISHABLE_KEY`, and `APP_URL` in your environment first.</p></div>
                    <?php endif; ?>
                </div>
                <a href="order.php" class="btn">Back to Cart</a>
            </div>
        <?php endif; ?>
    </section>

    <?php include __DIR__ . '/../partials/site-footer.php'; ?>
    <?php if (!$isPaidView && stripeIsConfigured()): ?>
        <script>
            const stripeButton = document.getElementById('stripe-checkout-button');
            const stripeMessage = document.getElementById('stripe-message');
            const csrfToken = stripeButton?.dataset.csrfToken || '';
            function setStripeMessage(message) {
                if (!stripeMessage) return;
                stripeMessage.hidden = false;
                stripeMessage.textContent = message;
            }
            stripeButton?.addEventListener('click', () => {
                stripeButton.disabled = true;
                fetch('stripe_create_checkout_session.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-Token': csrfToken
                    }
                })
                .then((response) => response.json())
                .then((data) => {
                    if (!data.url) throw new Error(data.message || 'Unable to start Stripe checkout.');
                    window.location.href = data.url;
                })
                .catch((error) => {
                    stripeButton.disabled = false;
                    console.error(error);
                    setStripeMessage(error.message || 'A Stripe error occurred. Please try again.');
                });
            });
        </script>
    <?php endif; ?>
</body>
</html>
