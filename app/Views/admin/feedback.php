<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback Management</title>
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
        <header>
            <h1>Customer Feedback</h1>
        </header>

        <table class="responsive-table">
            <thead>
                <tr>
                    <th>Feedback ID</th>
                    <th>Customer</th>
                    <th>Phone</th>
                    <th>Rating</th>
                    <th>Message</th>
                    <th>Submitted</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($feedbackEntries)): ?>
                    <tr>
                        <td colspan="6" data-label="Feedback">No feedback has been submitted yet.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($feedbackEntries as $entry): ?>
                        <tr>
                            <td data-label="Feedback ID"><?php echo (int) $entry['feedback_id']; ?></td>
                            <td data-label="Customer"><?php echo htmlspecialchars($entry['last_name'] . ' ' . $entry['first_name']); ?></td>
                            <td data-label="Phone"><?php echo htmlspecialchars($entry['phone']); ?></td>
                            <td data-label="Rating"><?php echo str_repeat('*', (int) $entry['rating']) . str_repeat('-', 5 - (int) $entry['rating']); ?></td>
                            <td data-label="Message"><?php echo htmlspecialchars($entry['message']); ?></td>
                            <td data-label="Submitted"><?php echo htmlspecialchars($entry['created_at']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
