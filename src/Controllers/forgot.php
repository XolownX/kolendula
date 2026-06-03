<?php
// Восстановление пароля. Demo: код показываем во всплывающем окне.

$stage = $_GET['stage'] ?? 'request'; // request | verify | reset
$error = null;
$success = null;
$demoCode = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'request') {
        $loginOrEmail = trim($_POST['login_or_email'] ?? '');
        if (!$loginOrEmail) {
            $error = 'Введите логин или email';
        } else {
            $user = User::findByLoginOrEmail($loginOrEmail);
            if (!$user) {
                $error = 'Пользователь не найден';
            } else {
                $code = str_pad((string)random_int(0, 999999), 6, '0', STR_PAD_LEFT);
                $expires = date('Y-m-d H:i:s', time() + 15 * 60);
                // Инвалидируем старые коды
                $del = db()->prepare('UPDATE password_resets SET used = 1 WHERE user_id = ? AND used = 0');
                $del->execute([$user['id']]);
                $ins = db()->prepare('INSERT INTO password_resets (user_id, code, expires_at) VALUES (?, ?, ?)');
                $ins->execute([$user['id'], $code, $expires]);
                $_SESSION['reset_user_id'] = (int)$user['id'];

                $cfg = db_config();
                if (!empty($cfg['demo_password_reset'])) {
                    flash_set('demo_code', $code);
                }
                redirect('/forgot?stage=verify');
            }
        }
    } elseif ($action === 'verify') {
        $code = trim($_POST['code'] ?? '');
        $userId = $_SESSION['reset_user_id'] ?? null;
        if (!$userId) {
            $error = 'Сессия истекла. Начните заново.';
            $stage = 'request';
        } else {
            $stmt = db()->prepare('SELECT * FROM password_resets WHERE user_id = ? AND code = ? AND used = 0 ORDER BY id DESC LIMIT 1');
            $stmt->execute([$userId, $code]);
            $row = $stmt->fetch();
            if (!$row || strtotime($row['expires_at']) < time()) {
                $error = 'Неверный или просроченный код';
                $stage = 'verify';
            } else {
                $_SESSION['reset_verified'] = (int)$row['id'];
                redirect('/forgot?stage=reset');
            }
        }
    } elseif ($action === 'reset') {
        $resetId = $_SESSION['reset_verified'] ?? null;
        $userId = $_SESSION['reset_user_id'] ?? null;
        if (!$resetId || !$userId) {
            $error = 'Сессия истекла. Начните заново.';
            $stage = 'request';
        } else {
            $pwd = $_POST['password'] ?? '';
            $pwd2 = $_POST['password_confirm'] ?? '';
            if (strlen($pwd) < 6) {
                $error = 'Минимум 6 символов';
                $stage = 'reset';
            } elseif ($pwd !== $pwd2) {
                $error = 'Пароли не совпадают';
                $stage = 'reset';
            } else {
                User::update($userId, ['password' => $pwd]);
                db()->prepare('UPDATE password_resets SET used = 1 WHERE id = ?')->execute([$resetId]);
                unset($_SESSION['reset_user_id'], $_SESSION['reset_verified']);
                flash_set('forgot_done', true);
                redirect('/login');
            }
        }
    }
}

$demoCode = flash_get('demo_code');

$pageTitle = 'Восстановление пароля';
$contentFile = __DIR__ . '/../../views/forgot.php';
require __DIR__ . '/../../views/layout.php';
