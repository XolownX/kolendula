<?php
// PHP built-in server router
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$file = __DIR__ . '/public' . $uri;
if ($uri !== '/' && file_exists($file) && is_file($file)) {
    // Let PHP server serve the static file with proper MIME
    return false;
}
require __DIR__ . '/public/index.php';
