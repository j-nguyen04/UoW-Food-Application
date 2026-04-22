<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management</title>
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
        <header><h1>User Management</h1></header>
        <?php if ($errorMessage !== ''): ?>
            <p style="margin: 1.5rem 0; color: #b42318; font-size: 1.5rem; text-transform:none;"><?= htmlspecialchars($errorMessage) ?></p>
        <?php endif; ?>
        <table class="responsive-table">
            <thead>
                <tr>
                    <th>User ID</th>
                    <th>Last Name</th>
                    <th>First Name</th>
                    <th>Email</th>
                    <th>Phone Number</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td data-label="User ID"><?= htmlspecialchars((string) $user['user_id']) ?></td>
                        <td data-label="Last Name"><?= htmlspecialchars($user['last_name']) ?></td>
                        <td data-label="First Name"><?= htmlspecialchars($user['first_name']) ?></td>
                        <td data-label="Email"><?= htmlspecialchars((string) ($user['email'] ?? '')) ?></td>
                        <td data-label="Phone Number"><?= htmlspecialchars($user['phone']) ?></td>
                        <td data-label="Action">
                            <form method="post" action="" onsubmit="return confirm('Are you sure you want to delete this client?')">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                                <input type="hidden" name="delete" value="<?= htmlspecialchars((string) $user['user_id']) ?>">
                                <button type="submit">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
