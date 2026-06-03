<?php
// Увеличим лимит POST для аватара (на случай длинного dataURL)
@ini_set('post_max_size', '8M');
@ini_set('upload_max_filesize', '8M');

$user = current_user();
if (!$user) redirect('/login?return=' . urlencode('/profile'));

$method = $_SERVER['REQUEST_METHOD'];
$error = null;

if ($method === 'POST') {
    $data = [
        'name' => trim($_POST['name'] ?? $user['name']),
        'phone' => trim($_POST['phone'] ?? ''),
        'email' => trim($_POST['email'] ?? $user['email']),
    ];
    // Avatar — поддержим как dataURL для простоты. Только если поле НЕ пустое.
    if (!empty($_POST['avatar_data'])) {
        $av = $_POST['avatar_data'];
        if (strlen($av) > 6_000_000) {
            $error = 'Файл слишком большой (макс. 6 МБ)';
        } elseif (strpos($av, 'data:image/') !== 0) {
            $error = 'Некорректный формат файла';
        } else {
            $data['avatar'] = $av;
        }
    }

    // Смена пароля — обязательно подтверждение старым паролем
    if (!$error && !empty($_POST['password'])) {
        $oldPwd = $_POST['password_old'] ?? '';
        if (!$oldPwd) {
            $error = 'Введите старый пароль для смены';
        } elseif (!password_verify($oldPwd, $user['password_hash'])) {
            $error = 'Старый пароль неверный';
        } elseif ($_POST['password'] !== ($_POST['password_confirm'] ?? '')) {
            $error = 'Новые пароли не совпадают';
        } elseif (strlen($_POST['password']) < 6) {
            $error = 'Минимум 6 символов в пароле';
        } else {
            $data['password'] = $_POST['password'];
        }
    }

    if (!$error) {
        User::update($user['id'], $data);
        flash_set('profile_saved', true);
        redirect('/profile');
    }
}

$pageTitle = t('profile');
$contentFile = __DIR__ . '/../../views/profile.php';
require __DIR__ . '/../../views/layout.php';
