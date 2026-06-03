<?php
$currentSection = $section ?? 'dashboard';
$tabs = [
    'dashboard'  => ['label' => 'Панель',       'href' => '/admin'],
    'products'   => ['label' => 'Товары',       'href' => '/admin/products'],
    'categories' => ['label' => 'Категории',    'href' => '/admin/categories'],
    'users'      => ['label' => 'Пользователи', 'href' => '/admin/users'],
    'orders'     => ['label' => 'Заказы',       'href' => '/admin/orders'],
    'chats'      => ['label' => 'Чаты',         'href' => '/admin/chats'],
];
ob_start();
?>
<div class="admin-layout">
    <aside class="admin-sidebar">
        <div class="admin-sidebar-title">Админ</div>
        <nav class="admin-nav">
            <?php foreach ($tabs as $key => $t): ?>
                <a class="admin-nav-link <?= $currentSection === $key ? 'active' : '' ?>" href="<?= e($t['href']) ?>">
                    <?= e($t['label']) ?>
                    <?php if ($key === 'chats' && !empty($stats['chat_unread'])): ?>
                        <span class="admin-badge"><?= (int)$stats['chat_unread'] ?></span>
                    <?php endif; ?>
                </a>
            <?php endforeach; ?>
            <a class="admin-nav-link admin-nav-back" href="/">← На сайт</a>
        </nav>
    </aside>
    <main class="admin-content">
        <?php if (!empty($adminMsg)): ?>
            <div class="admin-flash"><?= e($adminMsg) ?></div>
        <?php endif; ?>
        <?php require $contentFile; ?>
    </main>
</div>
<?php
$content = ob_get_clean();
$pageTitle = 'Админ — ' . ($tabs[$currentSection]['label'] ?? '');
$contentFile = null;
// inline rendering: substitute layout
$bodyClass = 'admin-body';
?><!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?> · Kolendula</title>
    <link rel="stylesheet" href="/assets/css/app.css?v=11">
</head>
<body class="<?= e($bodyClass) ?>">
    <header class="admin-topbar">
        <a href="/" class="admin-logo">Kolendula</a>
        <div class="admin-topbar-user">
            <?= e($_SESSION['user']['login'] ?? 'admin') ?> · <a href="/logout">Выйти</a>
        </div>
    </header>
    <?= $content ?>
</body>
</html>
