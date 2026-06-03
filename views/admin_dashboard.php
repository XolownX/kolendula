<h1 class="admin-h1">Панель управления</h1>
<div class="admin-stats">
    <div class="admin-stat-card">
        <div class="admin-stat-label">Товаров</div>
        <div class="admin-stat-value"><?= (int)$stats['products'] ?></div>
    </div>
    <div class="admin-stat-card">
        <div class="admin-stat-label">Заказов</div>
        <div class="admin-stat-value"><?= (int)$stats['orders'] ?></div>
    </div>
    <div class="admin-stat-card">
        <div class="admin-stat-label">Пользователей</div>
        <div class="admin-stat-value"><?= (int)$stats['users'] ?></div>
    </div>
    <div class="admin-stat-card">
        <div class="admin-stat-label">Выручка</div>
        <div class="admin-stat-value"><?= number_format($stats['revenue'], 0, '.', ' ') ?> ₽</div>
    </div>
    <div class="admin-stat-card">
        <div class="admin-stat-label">Новых чатов</div>
        <div class="admin-stat-value"><?= (int)$stats['chat_unread'] ?></div>
    </div>
</div>

<div class="admin-block">
    <div class="admin-block-title">Быстрый доступ</div>
    <div class="admin-quick">
        <a class="admin-quick-link" href="/admin/products">Управление товарами</a>
        <a class="admin-quick-link" href="/admin/orders">Все заказы</a>
        <a class="admin-quick-link" href="/admin/chats">Чаты поддержки</a>
        <a class="admin-quick-link" href="/admin/categories">Категории</a>
    </div>
</div>
