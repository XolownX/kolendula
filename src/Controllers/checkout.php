<?php
$user = current_user();
if (!$user) redirect('/login?return=' . urlencode('/checkout'));

$items = Cart::items();
if (empty($items) && $_SERVER['REQUEST_METHOD'] === 'GET') {
    redirect('/cart');
}

$cards = Card::forUser((int)$user['id']);
$subtotal = 0;
foreach ($items as $i) $subtotal += $i['price'] * $i['quantity'];

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $deliveryType = $_POST['delivery_type'] ?? 'pickup';
    $address = '';
    $deliveryFee = 0;

    if ($deliveryType === 'pickup') {
        $store = $_POST['pickup_store'] ?? '';
        if (!$store) { $error = 'Выберите магазин самовывоза'; }
        else $address = 'Самовывоз: ' . $store;
    } elseif ($deliveryType === 'pvz') {
        $pvz = $_POST['pvz_point'] ?? '';
        if (!$pvz) { $error = 'Выберите пункт выдачи'; }
        else { $address = 'ПВЗ: ' . $pvz; $deliveryFee = 299; }
    } elseif ($deliveryType === 'courier') {
        $addr = trim($_POST['courier_address'] ?? '');
        if (!$addr) { $error = 'Укажите адрес для курьера'; }
        else { $address = 'Курьером: ' . $addr; $deliveryFee = 499; }
    } else {
        $error = 'Неверный способ доставки';
    }

    $cardLast4 = null;
    $cardBrand = null;
    $paymentOk = true;
    $paymentError = null;

    if (!$error) {
        $useSaved = $_POST['use_saved_card'] ?? '';
        if ($useSaved) {
            $card = Card::find((int)$useSaved, (int)$user['id']);
            if (!$card) {
                $error = 'Сохранённая карта не найдена';
            } else {
                $cardLast4 = $card['last4'];
                $cardBrand = $card['brand'];
            }
        } else {
            $num = preg_replace('/\D/', '', $_POST['card_number'] ?? '');
            $expM = (int)($_POST['exp_month'] ?? 0);
            $expY = (int)($_POST['exp_year'] ?? 0);
            $cvv = preg_replace('/\D/', '', $_POST['cvv'] ?? '');
            $holder = trim($_POST['card_holder'] ?? '');
            if (!$num || !luhn_check($num)) {
                $paymentOk = false;
                $paymentError = 'Неверный номер карты';
            } elseif (strlen($cvv) < 3) {
                $paymentOk = false;
                $paymentError = 'Неверный CVV';
            } elseif (!$expM || $expM < 1 || $expM > 12) {
                $paymentOk = false;
                $paymentError = 'Неверный месяц истечения';
            } elseif (!$expY || $expY < (int)date('Y') % 100) {
                $paymentOk = false;
                $paymentError = 'Срок действия истёк';
            } else {
                // 10% случайных отказов банка для демо
                if (random_int(1, 100) <= 10) {
                    $paymentOk = false;
                    $paymentError = 'Платёж отклонён банком. Попробуйте другую карту.';
                } else {
                    $cardLast4 = substr($num, -4);
                    $cardBrand = card_brand($num);
                    if (!empty($_POST['save_card'])) {
                        Card::save((int)$user['id'], $cardLast4, $cardBrand, $expM, $expY, $holder);
                    }
                }
            }
        }
    }

    if (!$error && !$paymentOk) {
        $error = $paymentError;
    }

    if (!$error) {
        $orderId = Order::create((int)$user['id'], $items, [
            'status' => 'processing',
            'payment_status' => 'paid',
            'delivery_type' => $deliveryType,
            'delivery_address' => $address,
            'delivery_fee' => $deliveryFee,
            'card_last4' => $cardLast4,
            'card_brand' => $cardBrand,
        ]);
        Cart::clear();
        flash_set('order_success', true);
        redirect('/orders/' . $orderId);
    }
}

$pageTitle = 'Оформление заказа';
$contentFile = __DIR__ . '/../../views/checkout.php';
require __DIR__ . '/../../views/layout.php';
