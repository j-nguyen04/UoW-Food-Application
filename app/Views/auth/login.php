<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="frontend/css/login.css?v=<?php echo time(); ?>">
    <title>Login</title>
</head>
<body>
    <section class="login-container">
        <div class="login-box">
            <h2>Login</h2>
            <form action="login.php" method="POST">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                <input type="email" name="email" placeholder="Email address" value="<?= htmlspecialchars((string) ($_POST['email'] ?? '')) ?>" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit">Login</button>
            </form>
            <?php if ($error !== ''): ?>
                <p class="error"><?= htmlspecialchars($error) ?></p>
            <?php endif; ?>
            <p class="signup-link">Don't have an account? <a href="sign-in.php">Sign Up</a></p>
        </div>
    </section>
</body>
</html>
