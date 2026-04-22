<?php

require_once __DIR__ . '/bootstrap.php';

// Keep the public root files as thin entrypoints by mapping them to MVC controllers here.
// This preserves the original URLs used around the project while centralizing the
// actual request handling inside controller classes.
function app_dispatch(PDO $pdo, string $entryFile): void
{
    switch ($entryFile) {
        case 'login.php':
            (new \App\Controllers\AuthController($pdo))->login();
            return;
        case 'sign-in.php':
            (new \App\Controllers\AuthController($pdo))->signup();
            return;
        case 'profile.php':
            (new \App\Controllers\ProfileController($pdo))->show();
            return;
        case 'feedback.php':
            (new \App\Controllers\FeedbackController($pdo))->show();
            return;
        case 'admin.php':
            (new \App\Controllers\AdminController($pdo))->dashboard();
            return;
        case 'client-admin.php':
            (new \App\Controllers\AdminUserController($pdo))->index();
            return;
        case 'login-admin.php':
            (new \App\Controllers\AdminAuthController($pdo))->login();
            return;
        case 'feedback-admin.php':
            (new \App\Controllers\AdminFeedbackController($pdo))->index();
            return;
        case 'admin-security.php':
            (new \App\Controllers\AdminSecurityController($pdo))->index();
            return;
        case 'index.php':
            (new \App\Controllers\HomeController($pdo))->index();
            return;
        case 'checkout.php':
            (new \App\Controllers\CheckoutController($pdo))->show();
            return;
        case 'order-history.php':
            (new \App\Controllers\OrderHistoryController($pdo))->index();
            return;
        case 'order.php':
            (new \App\Controllers\CartController($pdo))->handle();
            return;
        case 'stripe_create_checkout_session.php':
            (new \App\Controllers\StripeCheckoutSessionController($pdo))->create();
            return;
    }

    throw new \RuntimeException('No MVC route registered for ' . $entryFile);
}
