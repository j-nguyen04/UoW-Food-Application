<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Security</title>
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
        <header><h1>Admin Security</h1></header>

        <?php if ($successMessage !== ''): ?>
            <p style="margin: 1.5rem 0; color: #0f7b0f; font-size: 1.5rem; text-transform:none;"><?= htmlspecialchars($successMessage) ?></p>
        <?php endif; ?>

        <?php if ($errorMessage !== ''): ?>
            <p style="margin: 1.5rem 0; color: #b42318; font-size: 1.5rem; text-transform:none;"><?= htmlspecialchars($errorMessage) ?></p>
        <?php endif; ?>

        <h2>Legacy Password Migration</h2>
        <form method="post" action="" style="margin-bottom: 2rem;">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
            <button type="submit" name="hash_legacy_passwords" class="btn">Hash Legacy Passwords</button>
        </form>

        <h2>Admin Accounts</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Password Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($admins as $admin): ?>
                    <tr>
                        <td><?= (int) $admin['id'] ?></td>
                        <td><?= htmlspecialchars($admin['username']) ?></td>
                        <td><?= htmlspecialchars($admin['password_status']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <h2>Reset Admin Password</h2>
        <form method="post" action="" style="max-width: 42rem;">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">

            <label for="admin_id" style="display:block; margin-bottom:.8rem; font-size:1.5rem; color:#555;">Admin Account</label>
            <select id="admin_id" name="admin_id" style="width:100%; margin-bottom:1.6rem; padding:1.2rem; border:1px solid #d9d9d9; border-radius:.8rem;">
                <?php foreach ($admins as $admin): ?>
                    <option value="<?= (int) $admin['id'] ?>"><?= htmlspecialchars($admin['username']) ?></option>
                <?php endforeach; ?>
            </select>

            <label for="new_password" style="display:block; margin-bottom:.8rem; font-size:1.5rem; color:#555;">New Password</label>
            <input type="password" id="new_password" name="new_password" minlength="8" required style="width:100%; margin-bottom:1.6rem; padding:1.2rem; border:1px solid #d9d9d9; border-radius:.8rem;">

            <label for="confirm_password" style="display:block; margin-bottom:.8rem; font-size:1.5rem; color:#555;">Confirm Password</label>
            <input type="password" id="confirm_password" name="confirm_password" minlength="8" required style="width:100%; margin-bottom:1.6rem; padding:1.2rem; border:1px solid #d9d9d9; border-radius:.8rem;">

            <button type="submit" name="reset_admin_password" class="btn">Reset Password</button>
        </form>
    </div>
</body>
</html>
