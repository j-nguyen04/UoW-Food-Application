<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css">
    <link rel="stylesheet" href="frontend/css/style.css?v=<?php echo time(); ?>">
    <title>UoWFTA</title>
</head>
<body>
    <?php include __DIR__ . '/../partials/site-header.php'; ?>

    <section class="home" id="home">
        <div class="content">
            <h3>Final Year Project Prototype</h3>
            <p>Here is my working prototype in developing a food takeaway application for students to purchase items from a menu.</p>
        </div>
    </section>

    <section class="Plats" id="PLats">
        <h1 class="heading">Menu </h1>
        <div class="form-filter">
            <form class="form-select" method="get" action="">
                <select name="category">
                    <option value="ALL" <?= $filters['category'] === 'ALL' ? 'selected' : '' ?>>Categories</option>
                    <option value="main course" <?= $filters['category'] === 'main course' ? 'selected' : '' ?>>Main Course</option>
                    <option value="starter" <?= $filters['category'] === 'starter' ? 'selected' : '' ?>>Starter</option>
                    <option value="dessert" <?= $filters['category'] === 'dessert' ? 'selected' : '' ?>>Dessert</option>
                </select>

                <select name="cuisine_type">
                    <option value="ALL" <?= $filters['cuisine_type'] === 'ALL' ? 'selected' : '' ?>>Cuisines</option>
                    <option value="Moroccan" <?= $filters['cuisine_type'] === 'Moroccan' ? 'selected' : '' ?>>Moroccan</option>
                    <option value="Chinese" <?= $filters['cuisine_type'] === 'Chinese' ? 'selected' : '' ?>>Chinese</option>
                    <option value="Spanish" <?= $filters['cuisine_type'] === 'Spanish' ? 'selected' : '' ?>>Spanish</option>
                    <option value="French" <?= $filters['cuisine_type'] === 'French' ? 'selected' : '' ?>>French</option>
                    <option value="Italian" <?= $filters['cuisine_type'] === 'Italian' ? 'selected' : '' ?>>Italian</option>
                </select>

                <select name="dietary">
                    <option value="ALL" <?= strtolower((string) $filters['dietary']) === 'all' ? 'selected' : '' ?>>Dietary</option>
                    <?php foreach ($dietaryOptions as $value => $label): ?>
                        <option value="<?= htmlspecialchars($value) ?>" <?= strtolower((string) $filters['dietary']) === $value ? 'selected' : '' ?>>
                            <?= htmlspecialchars($label) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <button class="button-filter" type="submit">Filter</button>
            </form>
        </div>

        <?php if (empty($dishesByCuisine)): ?>
            <p class="empty-filter-results">No dishes match the selected dietary and menu filters yet.</p>
        <?php else: ?>
            <?php foreach ($dishesByCuisine as $cuisineType => $cuisineDishes): ?>
                <h2 class="cuisine-heading"><?= htmlspecialchars($cuisineType) ?> Foods</h2>
                <div class="box-container">
                    <?php foreach ($cuisineDishes as $dish): ?>
                        <?php $dishLabels = $menuModel->dishLabels($dish['dietary_labels'] ?? null); ?>
                        <div class="box">
                            <span class="price"><?= htmlspecialchars($dish['price']) ?> GBP</span>
                            <img src="<?= htmlspecialchars($dish['image_url']) ?>" alt="<?= htmlspecialchars($dish['dish_name']) ?>">
                            <h3><?= htmlspecialchars($dish['dish_name']) ?></h3>
                            <div class="stars"><p>Category : <?= htmlspecialchars($dish['category']) ?></p></div>
                            <?php if (!empty($dishLabels)): ?>
                                <div class="dietary-badges">
                                    <?php foreach ($dishLabels as $label): ?>
                                        <span class="dietary-badge"><?= htmlspecialchars($label) ?></span>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                            <form method="post" action="">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                                <input type="hidden" name="add_to_order" value="<?= htmlspecialchars((string) $dish['dish_id']) ?>">
                                <button type="submit" class="btn">Add to Cart</button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </section>

    <section class="feedback-showcase" id="feedback">
        <h1 class="heading">Student Feedback</h1>
        <?php if (empty($recentFeedback)): ?>
            <p class="account-copy" style="text-align:center;">Customer feedback will appear here once the first reviews are submitted.</p>
        <?php else: ?>
            <div class="feedback-grid">
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
    </section>

    <?php include __DIR__ . '/../partials/site-footer.php'; ?>
    <a href="#home" class="fas fa-angle-up" id="scroll-top"></a>
</body>
<script src="frontend/js/script.js" defer></script>
</html>
