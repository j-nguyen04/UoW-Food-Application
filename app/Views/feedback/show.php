<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css">
    <link rel="stylesheet" href="frontend/css/style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="frontend/css/account.css?v=<?php echo time(); ?>">
    <title>Feedback</title>
</head>
<body>
    <?php include __DIR__ . '/../partials/site-header.php'; ?>
    <section class="account-section">
        <div class="account-shell feedback-layout">
            <div class="account-panel">
                <p class="account-kicker">Feedback</p>
                <h1 class="heading">Share Your Experience</h1>
                <p class="account-copy">Tell us how your order went so we can improve the takeaway experience for students.</p>

                <?php if ($successMessage !== ''): ?>
                    <div class="account-alert success-alert"><?= htmlspecialchars($successMessage) ?></div>
                <?php endif; ?>

                <?php if ($errorMessage !== ''): ?>
                    <div class="account-alert error-alert"><?= htmlspecialchars($errorMessage) ?></div>
                <?php endif; ?>

                <form method="post" class="account-form">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                    <label for="rating">Rating</label>
                    <select id="rating" name="rating" required>
                        <option value="">Choose a rating</option>
                        <?php for ($rating = 5; $rating >= 1; $rating--): ?>
                            <option value="<?= $rating ?>" <?= (isset($_POST['rating']) && (int) $_POST['rating'] === $rating) ? 'selected' : '' ?>>
                                <?= $rating ?> Star<?= $rating === 1 ? '' : 's' ?>
                            </option>
                        <?php endfor; ?>
                    </select>

                    <label for="message">Comments</label>
                    <textarea id="message" name="message" rows="6" required><?= htmlspecialchars((string) ($_POST['message'] ?? '')) ?></textarea>

                    <button type="submit" class="btn">Submit Feedback</button>
                </form>
            </div>

            <div class="account-panel">
                <h2 class="account-subtitle">Your Recent Feedback</h2>
                <?php if (empty($userFeedback)): ?>
                    <p class="account-copy">You have not submitted any feedback yet.</p>
                <?php else: ?>
                    <div class="feedback-list">
                        <?php foreach ($userFeedback as $entry): ?>
                            <article class="feedback-card">
                                <div class="feedback-top">
                                    <strong><?= str_repeat('*', (int) $entry['rating']) . str_repeat('.', 5 - (int) $entry['rating']) ?></strong>
                                    <span><?= htmlspecialchars(date('d M Y, H:i', strtotime($entry['created_at']))) ?></span>
                                </div>
                                <p><?= htmlspecialchars($entry['message']) ?></p>
                            </article>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="account-shell">
            <div class="account-panel">
                <h2 class="account-subtitle">Latest Community Feedback</h2>
                <?php if (empty($recentFeedback)): ?>
                    <p class="account-copy">Feedback from customers will appear here once submissions start coming in.</p>
                <?php else: ?>
                    <div class="feedback-list">
                        <?php foreach ($recentFeedback as $entry): ?>
                            <article class="feedback-card">
                                <div class="feedback-top">
                                    <strong><?= htmlspecialchars($entry['first_name'] . ' ' . $entry['last_name']) ?></strong>
                                    <span><?= str_repeat('*', (int) $entry['rating']) . str_repeat('.', 5 - (int) $entry['rating']) ?></span>
                                </div>
                                <p><?= htmlspecialchars($entry['message']) ?></p>
                            </article>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
    <?php include __DIR__ . '/../partials/site-footer.php'; ?>
</body>
</html>
