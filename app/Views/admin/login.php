<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="stylesheet" href="frontend/css/admin.css?v=<?php echo time(); ?>">
    <style>
        body {
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 2rem;
        }

        .admin-login-card {
            width: min(100%, 46rem);
            background: #fff;
            border-radius: 1.2rem;
            padding: 3.2rem;
            box-shadow: 0 1.2rem 3rem rgba(0, 0, 0, 0.08);
        }

        .admin-login-card h1 {
            margin: 0 0 2rem;
            color: var(--blue);
        }

        .admin-login-card label {
            display: block;
            font-size: 1.5rem;
            color: #555;
            margin-bottom: 0.8rem;
        }

        .admin-login-card input {
            width: 100%;
            border: 1px solid #d9d9d9;
            border-radius: 0.8rem;
            padding: 1.2rem 1.4rem;
            font-size: 1.5rem;
            margin-bottom: 1.6rem;
        }

        .admin-login-card button {
            width: 100%;
            background: var(--blue);
            color: #fff;
            border-radius: 0.8rem;
            padding: 1.3rem 1.6rem;
            font-size: 1.7rem;
            cursor: pointer;
        }

        .admin-login-card button:hover {
            background: var(--blue-hover);
        }

        .admin-login-error {
            margin-bottom: 1.6rem;
            color: #b42318;
            font-size: 1.4rem;
            text-transform: none;
        }
    </style>
</head>
<body>
    <main class="admin-login-card">
        <h1>Admin Login</h1>

        <?php if ($error !== ''): ?>
            <p class="admin-login-error"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>

        <form method="POST" action="">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken); ?>">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" required>

            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>

            <button type="submit">Log In</button>
        </form>
    </main>
</body>
</html>
