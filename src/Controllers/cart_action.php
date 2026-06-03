<?php
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if ($path === '/cart/add') {
    $pid = (int)($_POST['product_id'] ?? 0);
    $qty = max(1, (int)($_POST['qty'] ?? 1));
    if ($pid > 0) {
        Cart::add($pid, $qty);
    }
    json_response(['ok' => true, 'cart_count' => cart_count()]);
}

if ($path === '/cart/update') {
    $itemId = (int)($_POST['item_id'] ?? 0);
    $qty = (int)($_POST['qty'] ?? 0);
    Cart::update($itemId, $qty);
    $subtotal = Cart::total();
    $delivery = $subtotal >= 5000 ? 0 : 490;
    json_response([
        'ok' => true,
        'cart_count' => cart_count(),
        'cart_subtotal' => $subtotal,
        'cart_total' => $subtotal + $delivery,
    ]);
}

if ($path === '/cart/remove') {
    $itemId = (int)($_POST['item_id'] ?? 0);
    Cart::remove($itemId);
    $subtotal = Cart::total();
    $delivery = $subtotal >= 5000 ? 0 : 490;
    json_response([
        'ok' => true,
        'cart_count' => cart_count(),
        'cart_subtotal' => $subtotal,
        'cart_total' => $subtotal + $delivery,
    ]);
}

if ($path === '/cart/checkout') {
    Cart::clear();
    $_SESSION['flash'] = t('order_placed');
    redirect('/?ordered=1');
}
