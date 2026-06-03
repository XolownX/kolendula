<?php
$user = current_user();
if (!$user) redirect('/login?return=' . urlencode('/orders'));

$orderId = isset($params[0]) ? (int)$params[0] : null;

if ($orderId) {
    $order = Order::find($orderId);
    if (!$order || (int)$order['user_id'] !== (int)$user['id']) {
        http_response_code(404);
        $pageTitle = 'Заказ не найден';
        $contentFile = __DIR__ . '/../../views/404.php';
        require __DIR__ . '/../../views/layout.php';
        return;
    }
    $orderItems = Order::items($orderId);
    $showSuccess = (bool)flash_get('order_success');
    $pageTitle = 'Заказ №' . $orderId;
    $contentFile = __DIR__ . '/../../views/order_show.php';
} else {
    $orders = Order::forUser((int)$user['id']);
    $pageTitle = 'Мои заказы';
    $contentFile = __DIR__ . '/../../views/orders.php';
}

require __DIR__ . '/../../views/layout.php';
