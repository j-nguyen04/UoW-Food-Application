<?php

// Set these through environment variables in local/prod configuration.
define('STRIPE_SECRET_KEY', getenv('STRIPE_SECRET_KEY') ?: 'STRIPE_SECRET_KEY');
define('STRIPE_PUBLISHABLE_KEY', getenv('STRIPE_PUBLISHABLE_KEY') ?: 'STRIPE_PUBLISHABLE_KEY');
define('APP_URL', rtrim((string) (getenv('APP_URL') ?: ''), '/'));
define('STRIPE_CURRENCY', strtolower(getenv('STRIPE_CURRENCY') ?: 'gbp'));
define('STRIPE_API_BASE_URL', 'https://api.stripe.com/v1');

function stripeIsConfigured(): bool
{
    return STRIPE_SECRET_KEY !== ''
        && STRIPE_PUBLISHABLE_KEY !== ''
        && STRIPE_SECRET_KEY !== 'REPLACE_WITH_STRIPE_SECRET_KEY'
        && STRIPE_PUBLISHABLE_KEY !== 'REPLACE_WITH_STRIPE_PUBLISHABLE_KEY';
}

function appBaseUrl(): string
{
    if (APP_URL !== '') {
        return APP_URL;
    }

    $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (isset($_SERVER['SERVER_PORT']) && (int)$_SERVER['SERVER_PORT'] === 443);
    $scheme = $https ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $basePath = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/'));
    $segments = array_filter(explode('/', trim($basePath, '/')), static fn(string $segment): bool => $segment !== '');
    $encodedPath = '';

    if (!empty($segments)) {
        $encodedPath = '/' . implode('/', array_map('rawurlencode', $segments));
    }

    return $scheme . '://' . $host . $encodedPath;
}