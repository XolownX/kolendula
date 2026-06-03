<?php
/**
 * Подключение к БД (SQLite или MySQL — управляется через config.php).
 */
if (!function_exists('db_config')) {
    function db_config(): array {
        static $cfg = null;
        if ($cfg === null) {
            $cfg = require __DIR__ . '/../config.php';
        }
        return $cfg;
    }
}

if (!function_exists('db_driver')) {
    function db_driver(): string {
        return db_config()['db_driver'] ?? 'sqlite';
    }
}

if (!function_exists('db')) {
    function db(): PDO {
        static $pdo = null;
        if ($pdo === null) {
            $cfg = db_config();
            $driver = $cfg['db_driver'];

            if ($driver === 'mysql') {
                $m = $cfg['mysql'];
                $dsn = "mysql:host={$m['host']};port={$m['port']};dbname={$m['database']};charset={$m['charset']}";
                $pdo = new PDO($dsn, $m['username'], $m['password']);
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            } else {
                $path = $cfg['sqlite']['path'];
                $pdo = new PDO('sqlite:' . $path);
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
                $pdo->exec('PRAGMA foreign_keys = ON');
            }
        }
        return $pdo;
    }
}

if (!function_exists('db_is_mysql')) {
    function db_is_mysql(): bool { return db_driver() === 'mysql'; }
}

/**
 * Возвращает SQL для автоинкремента первичного ключа в зависимости от драйвера.
 */
if (!function_exists('db_pk')) {
    function db_pk(): string {
        return db_is_mysql() ? 'INT AUTO_INCREMENT PRIMARY KEY' : 'INTEGER PRIMARY KEY AUTOINCREMENT';
    }
}

/**
 * Возвращает SQL для DATETIME с дефолтом.
 */
if (!function_exists('db_dt')) {
    function db_dt(): string {
        return 'DATETIME DEFAULT CURRENT_TIMESTAMP';
    }
}

/**
 * Безопасный TEXT/LONGTEXT.
 */
if (!function_exists('db_longtext')) {
    function db_longtext(): string {
        return db_is_mysql() ? 'LONGTEXT' : 'TEXT';
    }
}
