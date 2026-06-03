<?php
// Старт сессии
function start_session(): void {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

function current_user(): ?array {
    start_session();
    if (!isset($_SESSION['user_id'])) return null;
    static $cache = null;
    if ($cache !== null) return $cache;
    $stmt = db()->prepare('SELECT * FROM users WHERE id = ?');
    $stmt->execute([$_SESSION['user_id']]);
    $cache = $stmt->fetch() ?: null;
    return $cache;
}

function cart_key(): array {
    start_session();
    $user = current_user();
    if ($user) return ['user_id', $user['id']];
    if (!isset($_SESSION['cart_sid'])) {
        $_SESSION['cart_sid'] = bin2hex(random_bytes(16));
    }
    return ['session_id', $_SESSION['cart_sid']];
}

function cart_count(): int {
    [$col, $val] = cart_key();
    $stmt = db()->prepare("SELECT COALESCE(SUM(quantity), 0) AS n FROM cart_items WHERE $col = ?");
    $stmt->execute([$val]);
    return (int)$stmt->fetch()['n'];
}

function get_setting(string $key, string $default = ''): string {
    start_session();
    $user = current_user();
    if ($user && in_array($key, ['theme','language','region'])) {
        return $user[$key] ?: $default;
    }
    return $_SESSION['settings'][$key] ?? $default;
}

function set_setting(string $key, string $value): void {
    start_session();
    $_SESSION['settings'][$key] = $value;
    $user = current_user();
    if ($user && in_array($key, ['theme','language','region'])) {
        $stmt = db()->prepare("UPDATE users SET $key = ? WHERE id = ?");
        $stmt->execute([$value, $user['id']]);
    }
}

function format_price(int $price): string {
    return number_format($price, 0, ',', ' ') . ' ₽';
}

function escape(string $s): string {
    return htmlspecialchars($s, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

if (!function_exists('e')) {
    function e($s): string {
        return htmlspecialchars((string)$s, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }
}

function json_response($data, int $status = 200): void {
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

function redirect(string $url): void {
    header('Location: ' . $url);
    exit;
}

// i18n
function t(string $key): string {
    static $dict = null;
    if ($dict === null) {
        $lang = get_setting('language', 'ru');
        $file = __DIR__ . '/../lang/' . $lang . '.php';
        $dict = file_exists($file) ? require $file : [];
    }
    return $dict[$key] ?? $key;
}

function asset_url(string $path): string {
    return '/assets/' . ltrim($path, '/');
}

// Flash-сообщения
function flash_set(string $key, $value): void {
    start_session();
    $_SESSION['_flash'][$key] = $value;
}
function flash_get(string $key) {
    start_session();
    if (!isset($_SESSION['_flash'][$key])) return null;
    $v = $_SESSION['_flash'][$key];
    unset($_SESSION['_flash'][$key]);
    return $v;
}

function is_admin(): bool {
    $u = current_user();
    return $u && (int)($u['is_admin'] ?? 0) === 1;
}
function require_admin(): void {
    if (!is_admin()) {
        http_response_code(403);
        echo '<h1>403 — доступ только для администратора</h1>';
        exit;
    }
}

// Алгоритм Луна для проверки номера карты
function luhn_check(string $number): bool {
    $number = preg_replace('/\D/', '', $number);
    if (strlen($number) < 12 || strlen($number) > 19) return false;
    $sum = 0;
    $alt = false;
    for ($i = strlen($number) - 1; $i >= 0; $i--) {
        $d = (int)$number[$i];
        if ($alt) { $d *= 2; if ($d > 9) $d -= 9; }
        $sum += $d;
        $alt = !$alt;
    }
    return $sum % 10 === 0;
}

function card_brand(string $number): string {
    $n = preg_replace('/\D/', '', $number);
    if (!$n) return 'Card';
    $f = $n[0];
    if ($f === '4') return 'Visa';
    if ($f === '5') return 'Mastercard';
    if ($f === '2') return 'Mir';
    if ($f === '3') return 'Amex';
    return 'Card';
}

function chat_key(): array {
    start_session();
    $user = current_user();
    if ($user) return ['user_id', (int)$user['id']];
    if (!isset($_SESSION['chat_sid'])) {
        $_SESSION['chat_sid'] = bin2hex(random_bytes(12));
    }
    return ['session_id', $_SESSION['chat_sid']];
}
