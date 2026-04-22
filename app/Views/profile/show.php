<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css">
    <link rel="stylesheet" href="frontend/css/style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="frontend/css/account.css?v=<?php echo time(); ?>">
    <title>My Profile</title>
</head>
<body>
    <?php include __DIR__ . '/../partials/site-header.php'; ?>
    <section class="account-section">
        <div class="account-shell">
            <div class="account-panel">
                <p class="account-kicker">Account</p>
                <h1 class="heading">Manage Profile</h1>
                <p class="account-copy">Keep your account details up to date so your orders stay linked to the right profile.</p>

                <?php if ($successMessage !== ''): ?>
                    <div class="account-alert success-alert"><?= htmlspecialchars($successMessage) ?></div>
                <?php endif; ?>

                <?php if ($errorMessage !== ''): ?>
                    <div class="account-alert error-alert"><?= htmlspecialchars($errorMessage) ?></div>
                <?php endif; ?>

                <form method="post" class="account-form">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                    <label for="first_name">First Name</label>
                    <input type="text" id="first_name" name="first_name" value="<?= htmlspecialchars($profile['first_name']) ?>" required>

                    <label for="last_name">Last Name</label>
                    <input type="text" id="last_name" name="last_name" value="<?= htmlspecialchars($profile['last_name']) ?>" required>

                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" value="<?= htmlspecialchars((string) ($profile['email'] ?? '')) ?>" required>

                    <label for="phone">Phone Number</label>
                    <input type="text" id="phone" name="phone" value="<?= htmlspecialchars($profile['phone']) ?>" required>

                    <label for="current_password">Current Password</label>
                    <input type="password" id="current_password" name="current_password" placeholder="Enter current password to change it">

                    <label for="new_password">New Password</label>
                    <input type="password" id="new_password" name="new_password" placeholder="Leave blank to keep your current password" minlength="8">

                    <label for="confirm_password">Confirm New Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" placeholder="Re-enter your new password" minlength="8">

                    <button type="submit" class="btn">Save Changes</button>
                </form>
            </div>
        </div>
    </section>
    <?php include __DIR__ . '/../partials/site-footer.php'; ?>
</body>
</html>
