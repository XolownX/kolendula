<?php
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    foreach (['theme', 'language', 'region'] as $k) {
        if (isset($_POST[$k])) {
            set_setting($k, $_POST[$k]);
        }
    }
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
        json_response(['ok' => true]);
    }
    redirect('/settings?saved=1');
}

$pageTitle = t('settings');
$contentFile = __DIR__ . '/../../views/settings.php';
require __DIR__ . '/../../views/layout.php';
