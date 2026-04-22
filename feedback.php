<?php
require_once __DIR__ . '/app/router.php';
app_dispatch($pdo, basename(__FILE__));
