<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="frontend/css/style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="frontend/css/order-history.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css">
    <title>Order Tracking</title>
</head>
<body>
    <?php include __DIR__ . '/../partials/site-header.php'; ?>
    <section class="tracking-section">
        <div class="tracking-shell">
            <p class="eyebrow">Order Tracking</p>
            <h1 class="heading">Your Orders</h1>
            <p class="tracking-intro">Check your latest order status, payment details, and everything you have ordered in one place.</p>
            <?php if (empty($orders)): ?>
                <div class="tracking-empty">
                    <h2>No orders yet</h2>
                    <p>You have not placed an order yet. Once you check out, your history and current status will appear here.</p>
                    <a href="index.php" class="btn">Start Ordering</a>
                </div>
            <?php else: ?>
                <div class="tracking-grid">
                    <?php foreach ($orders as $order): ?>
                        <article class="order-card">
                            <div class="order-card-top">
                                <div>
                                    <p class="order-label">Order ID</p>
                                    <h2>#<?= htmlspecialchars($order['order_id']) ?></h2>
                                </div>
                                <span class="status-pill <?= htmlspecialchars($order['status_meta']['class']) ?>"><?= htmlspecialchars($order['status_meta']['label']) ?></span>
                            </div>
                            <p class="status-description"><?= htmlspecialchars($order['status_meta']['description']) ?></p>
                            <div class="order-meta">
                                <div><span class="meta-label">Placed</span><strong><?= htmlspecialchars(date('d M Y, H:i', strtotime($order['order_date']))) ?></strong></div>
                                <div><span class="meta-label">Items</span><strong><?= (int) $order['item_count'] ?></strong></div>
                                <div><span class="meta-label">Payment</span><strong><?= htmlspecialchars(ucwords((string) $order['payment_status'])) ?></strong></div>
                                <div><span class="meta-label">Method</span><strong><?= htmlspecialchars(ucwords((string) $order['payment_method'])) ?></strong></div>
                            </div>
                            <div class="order-items-list">
                                <?php foreach ($order['items'] as $item): ?>
                                    <div class="history-item">
                                        <img src="<?= htmlspecialchars($item['image_url']) ?>" alt="<?= htmlspecialchars($item['dish_name']) ?>">
                                        <div class="history-item-copy">
                                            <h3><?= htmlspecialchars($item['dish_name']) ?></h3>
                                            <p>Quantity: <?= (int) $item['quantity'] ?></p>
                                        </div>
                                        <strong>GBP <?= number_format((float) $item['line_total'], 2) ?></strong>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <div class="history-total">
                                <span>Total</span>
                                <strong>GBP <?= number_format((float) $order['total_price'], 2) ?></strong>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>
    <?php include __DIR__ . '/../partials/site-footer.php'; ?>
</body>
</html>
