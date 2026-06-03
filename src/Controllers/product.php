<?php
$id = (int)($params[0] ?? 0);
$product = Product::find($id);
if (!$product) {
    http_response_code(404);
    $pageTitle = t('product_not_found');
    $contentFile = __DIR__ . '/../../views/404.php';
    require __DIR__ . '/../../views/layout.php';
    return;
}

// POST: отправка отзыва (только для купивших)
$reviewError = null;
$reviewSuccess = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'review') {
    $user = current_user();
    if (!$user) {
        $reviewError = 'Войдите, чтобы оставить отзыв';
    } elseif (!Order::userBoughtProduct((int)$user['id'], $id)) {
        $reviewError = 'Отзыв можно оставить только после покупки этого товара';
    } else {
        $rating = max(1, min(5, (int)($_POST['rating'] ?? 5)));
        $text = trim($_POST['text'] ?? '');
        if (strlen($text) < 5) {
            $reviewError = 'Отзыв слишком короткий (мин. 5 символов)';
        } else {
            $ins = db()->prepare('INSERT INTO reviews (product_id, user_id, author_name, rating, text) VALUES (?, ?, ?, ?, ?)');
            $ins->execute([$id, (int)$user['id'], $user['name'], $rating, $text]);
            // Обновим rating / reviews_count в товаре
            $agg = db()->prepare('SELECT AVG(rating) AS avg_r, COUNT(*) AS cnt FROM reviews WHERE product_id = ?');
            $agg->execute([$id]);
            $a = $agg->fetch();
            $upd = db()->prepare('UPDATE products SET rating = ?, reviews_count = ? WHERE id = ?');
            $upd->execute([round((float)$a['avg_r'], 2), (int)$a['cnt'], $id]);
            flash_set('review_added', true);
            redirect('/product/' . $id . '#reviews');
        }
    }
}

$reviews = Product::reviews($id);
$canReview = false;
$currentUserId = null;
$u = current_user();
if ($u) {
    $currentUserId = (int)$u['id'];
    $canReview = Order::userBoughtProduct($currentUserId, $id);
}
$reviewAdded = flash_get('review_added');
$related = Product::all(['category' => $product['category_slug'], 'limit' => 4]);
$related = array_values(array_filter($related, fn($p) => $p['id'] != $id));
$related = array_slice($related, 0, 4);

$lang = get_setting('language', 'ru');
$pageTitle = $product['name'];
$specs = $product['specs'] ? json_decode($product['specs'], true) : [];

$contentFile = __DIR__ . '/../../views/product.php';
require __DIR__ . '/../../views/layout.php';
