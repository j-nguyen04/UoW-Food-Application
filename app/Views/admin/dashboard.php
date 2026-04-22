<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="frontend/css/admin.css?v=<?php echo time(); ?>">
</head>
<body>
    <div class="sidebar">
        <h2>Admin</h2>
        <nav>
            <a href="admin.php">Home Page</a>
            <a href="client-admin.php">Users</a>
            <a href="feedback-admin.php">Feedback</a>
            <a href="admin-security.php">Security</a>
            <a href="logout-admin.php">Sign Out</a>
        </nav>
    </div>
    <div class="main-content">
        <h1>Admin Dashboard Overview</h1>
        <?php if (!empty($errorMessage)): ?>
            <p style="margin: 1.5rem 0; color: #b42318; font-size: 1.5rem; text-transform:none;"><?= htmlspecialchars($errorMessage) ?></p>
        <?php endif; ?>
        <div class="stats-cards">
            <div class="card"><h3>Orders Today</h3><p><?= $totalOrders ?></p></div>
            <div class="card"><h3>Total Users</h3><p><?= $totalClients ?></p></div>
            <div class="card"><h3>Canceled Orders Today</h3><p><?= $canceledOrders ?></p></div>
            <div class="card"><h3>Total Dishes</h3><p><?= $totalDishes ?></p></div>
        </div>

        <h2>Today's Orders (<?= date('d-m-Y', strtotime($today)) ?>)</h2>
        <form method="POST" action="">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
            <table class="responsive-table">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Date</th>
                        <th>Current Status</th>
                        <th>Client Name</th>
                        <th>Order Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td data-label="Order ID"><?= htmlspecialchars($order['order_id']) ?></td>
                            <td data-label="Date"><?= htmlspecialchars($order['order_date']) ?></td>
                            <td data-label="Current Status"><?= htmlspecialchars($order['status']) ?></td>
                            <td data-label="Client Name"><?= htmlspecialchars($order['last_name'] . ' ' . $order['first_name']) ?></td>
                            <td data-label="Order Status">
                                <select name="status[<?= htmlspecialchars($order['order_id']) ?>]" class="status-select">
                                    <option value="pending" <?= $order['status'] === 'pending' ? 'selected' : '' ?>>pending</option>
                                    <option value="in progress" <?= $order['status'] === 'in progress' ? 'selected' : '' ?>>in progress</option>
                                    <option value="shipped" <?= $order['status'] === 'shipped' ? 'selected' : '' ?>>shipped</option>
                                    <option value="delivered" <?= $order['status'] === 'delivered' ? 'selected' : '' ?>>delivered</option>
                                    <option value="cancelled" <?= $order['status'] === 'cancelled' ? 'selected' : '' ?>>cancelled</option>
                                </select>
                                <button type="submit" name="updateStatus" class="btn"><img src="frontend/icons/check.png" alt=""></button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </form>

        <h2>Items ordered</h2>
        <table class="responsive-table compact-table">
            <thead><tr><th>Dish Name</th><th>Quantity Ordered</th></tr></thead>
            <tbody>
                <?php foreach ($topDishes as $dish): ?>
                    <tr>
                        <td data-label="Dish Name"><?= htmlspecialchars($dish['dish_name']) ?></td>
                        <td data-label="Quantity Ordered"><?= htmlspecialchars((string) $dish['totalOrdered']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <div class="footer"><div class="credit">Created by <span>w1977770</span></div></div>
    </div>
</body>
</html>
