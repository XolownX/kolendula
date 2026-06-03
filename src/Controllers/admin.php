<?php
require_admin();

$section = $params[0] ?? 'dashboard';
$lang = get_setting('language', 'ru');

// CRUD: товары
if ($section === 'products' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        db()->prepare('DELETE FROM reviews WHERE product_id = ?')->execute([$id]);
        db()->prepare('DELETE FROM cart_items WHERE product_id = ?')->execute([$id]);
        db()->prepare('DELETE FROM products WHERE id = ?')->execute([$id]);
        flash_set('admin_msg', 'Товар удалён');
        redirect('/admin/products');
    } elseif ($action === 'save') {
        $id = (int)($_POST['id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $brand = trim($_POST['brand'] ?? '');
        $catId = (int)($_POST['category_id'] ?? 0);
        $price = (int)($_POST['price'] ?? 0);
        $oldPrice = $_POST['old_price'] !== '' ? (int)$_POST['old_price'] : null;
        $description = trim($_POST['description'] ?? '');
        $image = trim($_POST['image'] ?? 'pc_office');
        $isHot = !empty($_POST['is_hot']) ? 1 : 0;
        $isNew = !empty($_POST['is_new']) ? 1 : 0;

        if ($id) {
            $stmt = db()->prepare('UPDATE products SET name=?, brand=?, category_id=?, price=?, old_price=?, description=?, image=?, is_hot=?, is_new=? WHERE id=?');
            $stmt->execute([$name, $brand, $catId, $price, $oldPrice, $description, $image, $isHot, $isNew, $id]);
        } else {
            $slug = strtolower(preg_replace('/[^a-z0-9]+/i', '-', $name . '-' . $brand . '-' . random_int(100, 999)));
            $slug = trim($slug, '-');
            $stmt = db()->prepare('INSERT INTO products (name, brand, slug, category_id, price, old_price, description, image, is_hot, is_new, specs) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
            $stmt->execute([$name, $brand, $slug, $catId, $price, $oldPrice, $description, $image, $isHot, $isNew, '{}']);
        }
        flash_set('admin_msg', 'Сохранено');
        redirect('/admin/products');
    }
}

// CRUD: категории
if ($section === 'categories' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        db()->prepare('DELETE FROM categories WHERE id = ?')->execute([$id]);
        flash_set('admin_msg', 'Категория удалена');
        redirect('/admin/categories');
    } elseif ($action === 'save') {
        $id = (int)($_POST['id'] ?? 0);
        $slug = trim($_POST['slug'] ?? '');
        $nameRu = trim($_POST['name_ru'] ?? '');
        $nameEn = trim($_POST['name_en'] ?? '');
        $icon = trim($_POST['icon'] ?? '📦');
        if ($id) {
            db()->prepare('UPDATE categories SET slug=?, name_ru=?, name_en=?, icon=? WHERE id=?')
               ->execute([$slug, $nameRu, $nameEn, $icon, $id]);
        } else {
            db()->prepare('INSERT INTO categories (slug, name_ru, name_en, icon) VALUES (?, ?, ?, ?)')
               ->execute([$slug, $nameRu, $nameEn, $icon]);
        }
        flash_set('admin_msg', 'Сохранено');
        redirect('/admin/categories');
    }
}

// Изменение статуса заказа
if ($section === 'orders' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    $status = $_POST['status'] ?? 'processing';
    Order::updateStatus($id, $status);
    flash_set('admin_msg', 'Статус обновлён');
    redirect('/admin/orders');
}

// Ответ оператора в чате
if ($section === 'chats' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $uid = !empty($_POST['user_id']) ? (int)$_POST['user_id'] : null;
    $sid = !empty($_POST['session_id']) ? (string)$_POST['session_id'] : null;
    $text = trim($_POST['text'] ?? '');
    if ($text) {
        if ($uid) Chat::adminSendTo('user_id', $uid, $text);
        elseif ($sid) Chat::adminSendTo('session_id', $sid, $text);
    }
    $back = '/admin/chats';
    if ($uid) $back .= '?user_id=' . $uid;
    elseif ($sid) $back .= '?session_id=' . urlencode($sid);
    redirect($back);
}

// Подготовка данных
$adminMsg = flash_get('admin_msg');
$stats = [
    'products' => (int)db()->query('SELECT COUNT(*) AS n FROM products')->fetch()['n'],
    'orders'   => (int)db()->query('SELECT COUNT(*) AS n FROM orders')->fetch()['n'],
    'users'    => (int)db()->query('SELECT COUNT(*) AS n FROM users')->fetch()['n'],
    'revenue'  => (int)db()->query("SELECT COALESCE(SUM(total),0) AS n FROM orders WHERE status != 'cancelled'")->fetch()['n'],
    'chat_unread' => Chat::unreadTotal(),
];

$pageTitle = 'Админ';
$contentFile = __DIR__ . '/../../views/admin_' . $section . '.php';
if (!file_exists($contentFile)) $contentFile = __DIR__ . '/../../views/admin_dashboard.php';
require __DIR__ . '/../../views/admin_layout.php';
