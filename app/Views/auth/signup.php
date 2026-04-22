<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="frontend/css/sign-up.css?v=<?php echo time(); ?>">
    <title>Sign Up</title>
</head>
<body>
    <div class="signup-container">
        <h2 class="form-heading">Create an Account</h2>
        <form action="" method="POST">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
            <input type="text" name="first_name" placeholder="First name" value="<?= htmlspecialchars((string) ($_POST['first_name'] ?? '')) ?>" required>
            <input type="text" name="last_name" placeholder="Last name" value="<?= htmlspecialchars((string) ($_POST['last_name'] ?? '')) ?>" required>
            <input type="email" name="email" placeholder="Email address" value="<?= htmlspecialchars((string) ($_POST['email'] ?? '')) ?>" required>
            <input type="text" name="phone" placeholder="Phone number" value="<?= htmlspecialchars((string) ($_POST['phone'] ?? '')) ?>" required>
            <input type="password" name="password" placeholder="Password" minlength="8" required>
            <button type="submit" name="signup">Sign Up</button>
        </form>
        <?php if ($error !== ''): ?>
            <p class="error-message"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>
        <p class="login-link">Already have an account? <a href="login.php">Login</a></p>
    </div>
</body>
</html>
