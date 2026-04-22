<?php
$cartCount = isset($_SESSION['cart']) && is_array($_SESSION['cart']) ? count($_SESSION['cart']) : 0;
$cartTotal = 0.0;
?>
<header>
    <a href="index.php" class="logo"><i class="fas fa-university"></i>UoW Food Takeaway Application</a>
    <nav class="navbar">
        <div class="card-shopping" id="cartDropdown">
            <button class="cart-btn">
                <img src="frontend/icons/shopping-cart.png" alt="Shopping Cart" id="cart-icon">
                <span class="cart-count"><?= $cartCount ?></span>
            </button>
            <div class="cart-dropdown">
                <?php if ($cartCount === 0): ?>
                    <p class="empty-cart">Your cart is empty.</p>
                <?php else: ?>
                    <ul>
                        <?php foreach ($_SESSION['cart'] as $item): ?>
                            <?php
                            $quantity = (int) ($item['quantity'] ?? 0);
                            $price = (float) ($item['price'] ?? 0);
                            $subtotal = $price * $quantity;
                            $cartTotal += $subtotal;
                            ?>
                            <li>
                                <img src="<?= htmlspecialchars((string) ($item['image_url'] ?? '')) ?>" alt="<?= htmlspecialchars((string) ($item['name'] ?? 'Item')) ?>">
                                <div class="order-details">
                                    <h4><?= htmlspecialchars((string) ($item['name'] ?? 'Item')) ?></h4>
                                    <p><?= number_format($price, 2) ?> GBP</p>
                                </div>
                                <div class="order-controls">
                                    <form method="post" action="order.php">
                                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(\App\Services\SecurityService::csrfToken()) ?>">
                                        <input type="hidden" name="update" value="decrease">
                                        <input type="hidden" name="id" value="<?= htmlspecialchars((string) ($item['id'] ?? '')) ?>">
                                        <button type="submit">-</button>
                                    </form>
                                    <span><?= $quantity ?></span>
                                    <form method="post" action="order.php">
                                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(\App\Services\SecurityService::csrfToken()) ?>">
                                        <input type="hidden" name="update" value="increase">
                                        <input type="hidden" name="id" value="<?= htmlspecialchars((string) ($item['id'] ?? '')) ?>">
                                        <button type="submit">+</button>
                                    </form>
                                </div>
                                <form method="post" action="order.php" class="delete-item-form">
                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(\App\Services\SecurityService::csrfToken()) ?>">
                                    <input type="hidden" name="remove" value="<?= htmlspecialchars((string) ($item['id'] ?? '')) ?>">
                                    <button type="submit" class="delete-item">X</button>
                                </form>
                            </li>
                            <p>Subtotal: <span class="subtotal"><?= number_format($subtotal, 2) ?></span> GBP</p>
                        <?php endforeach; ?>
                    </ul>
                    <div class="cart-total">
                        <h3>Total: <span id="grand-total"><?= number_format($cartTotal, 2) ?></span> GBP</h3>
                    </div>
                    <a href="checkout.php" class="checkout-btn">Proceed to Checkout</a>
                <?php endif; ?>
            </div>
        </div>

        <?php if (isset($_SESSION['client_name'])): ?>
            <div class="profile-dropdown">
                <button id="profileButton" class="profile-btn">
                    <span class="client-name"><?= htmlspecialchars((string) $_SESSION['client_name']) ?></span>
                    <i class="fas fa-chevron-down"></i>
                </button>
                <div id="dropdownMenu" class="dropdown-menu">
                    <a href="profile.php">Profile</a>
                    <a href="order-history.php">Track Orders</a>
                    <a href="feedback.php">Feedback</a>
                    <a href="logout.php">Log Out</a>
                </div>
            </div>
        <?php else: ?>
            <a href="login.php"><button class="btn0">Sign Up</button></a>
        <?php endif; ?>
    </nav>
</header>
