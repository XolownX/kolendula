<?php
$pageTitle = t('cart');
$items = Cart::items();
$total = Cart::total();
$contentFile = __DIR__ . '/../../views/cart.php';
require __DIR__ . '/../../views/layout.php';
