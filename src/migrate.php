<?php
require __DIR__ . '/db.php';

$pdo = db();
$isMysql = db_is_mysql();
$PK = db_pk();
$DT = db_dt();
$LT = db_longtext();

// Базовый набор таблиц
$pdo->exec("
CREATE TABLE IF NOT EXISTS users (
    id $PK,
    name " . ($isMysql ? "VARCHAR(255)" : "TEXT") . " NOT NULL,
    login " . ($isMysql ? "VARCHAR(100)" : "TEXT") . " NOT NULL UNIQUE,
    email " . ($isMysql ? "VARCHAR(190)" : "TEXT") . " NOT NULL UNIQUE,
    phone " . ($isMysql ? "VARCHAR(50)" : "TEXT") . ",
    password_hash " . ($isMysql ? "VARCHAR(255)" : "TEXT") . " NOT NULL,
    avatar $LT,
    theme " . ($isMysql ? "VARCHAR(20)" : "TEXT") . " DEFAULT 'auto',
    language " . ($isMysql ? "VARCHAR(5)" : "TEXT") . " DEFAULT 'ru',
    region " . ($isMysql ? "VARCHAR(5)" : "TEXT") . " DEFAULT 'RU',
    is_admin INT DEFAULT 0,
    created_at $DT
)" . ($isMysql ? " CHARSET=utf8mb4" : "") . ";
");

$pdo->exec("
CREATE TABLE IF NOT EXISTS categories (
    id $PK,
    slug " . ($isMysql ? "VARCHAR(100)" : "TEXT") . " NOT NULL UNIQUE,
    name_ru " . ($isMysql ? "VARCHAR(255)" : "TEXT") . " NOT NULL,
    name_en " . ($isMysql ? "VARCHAR(255)" : "TEXT") . " NOT NULL,
    icon " . ($isMysql ? "VARCHAR(20)" : "TEXT") . "
)" . ($isMysql ? " CHARSET=utf8mb4" : "") . ";
");

$pdo->exec("
CREATE TABLE IF NOT EXISTS products (
    id $PK,
    category_id INT NOT NULL,
    name " . ($isMysql ? "VARCHAR(255)" : "TEXT") . " NOT NULL,
    brand " . ($isMysql ? "VARCHAR(100)" : "TEXT") . " NOT NULL,
    slug " . ($isMysql ? "VARCHAR(255)" : "TEXT") . " NOT NULL UNIQUE,
    price INT NOT NULL,
    old_price INT,
    description $LT,
    specs $LT,
    image " . ($isMysql ? "VARCHAR(100)" : "TEXT") . ",
    rating " . ($isMysql ? "DECIMAL(3,2)" : "REAL") . " DEFAULT 0,
    reviews_count INT DEFAULT 0,
    is_hot INT DEFAULT 0,
    is_new INT DEFAULT 0,
    sales_count INT DEFAULT 0,
    created_at $DT
)" . ($isMysql ? " CHARSET=utf8mb4" : "") . ";
");

$pdo->exec("
CREATE TABLE IF NOT EXISTS reviews (
    id $PK,
    product_id INT NOT NULL,
    user_id INT,
    author_name " . ($isMysql ? "VARCHAR(255)" : "TEXT") . " NOT NULL,
    rating INT NOT NULL,
    text $LT NOT NULL,
    created_at $DT
)" . ($isMysql ? " CHARSET=utf8mb4" : "") . ";
");

$pdo->exec("
CREATE TABLE IF NOT EXISTS cart_items (
    id $PK,
    user_id INT,
    session_id " . ($isMysql ? "VARCHAR(100)" : "TEXT") . ",
    product_id INT NOT NULL,
    quantity INT DEFAULT 1,
    created_at $DT
)" . ($isMysql ? " CHARSET=utf8mb4" : "") . ";
");

// Новые таблицы v2
$pdo->exec("
CREATE TABLE IF NOT EXISTS orders (
    id $PK,
    user_id INT NOT NULL,
    total INT NOT NULL,
    status " . ($isMysql ? "VARCHAR(30)" : "TEXT") . " DEFAULT 'processing',
    payment_status " . ($isMysql ? "VARCHAR(30)" : "TEXT") . " DEFAULT 'paid',
    delivery_type " . ($isMysql ? "VARCHAR(20)" : "TEXT") . " DEFAULT 'pickup',
    delivery_address $LT,
    delivery_fee INT DEFAULT 0,
    card_last4 " . ($isMysql ? "VARCHAR(4)" : "TEXT") . ",
    card_brand " . ($isMysql ? "VARCHAR(20)" : "TEXT") . ",
    created_at $DT
)" . ($isMysql ? " CHARSET=utf8mb4" : "") . ";
");

$pdo->exec("
CREATE TABLE IF NOT EXISTS order_items (
    id $PK,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price INT NOT NULL
)" . ($isMysql ? " CHARSET=utf8mb4" : "") . ";
");

$pdo->exec("
CREATE TABLE IF NOT EXISTS saved_cards (
    id $PK,
    user_id INT NOT NULL,
    last4 " . ($isMysql ? "VARCHAR(4)" : "TEXT") . " NOT NULL,
    brand " . ($isMysql ? "VARCHAR(20)" : "TEXT") . ",
    exp_month INT,
    exp_year INT,
    holder " . ($isMysql ? "VARCHAR(255)" : "TEXT") . ",
    created_at $DT
)" . ($isMysql ? " CHARSET=utf8mb4" : "") . ";
");

$pdo->exec("
CREATE TABLE IF NOT EXISTS password_resets (
    id $PK,
    user_id INT NOT NULL,
    code " . ($isMysql ? "VARCHAR(10)" : "TEXT") . " NOT NULL,
    expires_at " . ($isMysql ? "DATETIME" : "TEXT") . " NOT NULL,
    used INT DEFAULT 0,
    created_at $DT
)" . ($isMysql ? " CHARSET=utf8mb4" : "") . ";
");

$pdo->exec("
CREATE TABLE IF NOT EXISTS chat_messages (
    id $PK,
    user_id INT,
    session_id " . ($isMysql ? "VARCHAR(100)" : "TEXT") . ",
    author " . ($isMysql ? "VARCHAR(20)" : "TEXT") . " NOT NULL,
    text $LT NOT NULL,
    is_read_by_admin INT DEFAULT 0,
    created_at $DT
)" . ($isMysql ? " CHARSET=utf8mb4" : "") . ";
");

// Идемпотентное добавление is_admin для уже существующих БД (SQLite)
if (!$isMysql) {
    try {
        $cols = $pdo->query("PRAGMA table_info(users)")->fetchAll();
        $hasAdmin = false;
        foreach ($cols as $c) if ($c['name'] === 'is_admin') $hasAdmin = true;
        if (!$hasAdmin) $pdo->exec("ALTER TABLE users ADD COLUMN is_admin INTEGER DEFAULT 0");
    } catch (Exception $e) {}
}

echo "✓ Миграции выполнены (драйвер: " . db_driver() . ")\n";
