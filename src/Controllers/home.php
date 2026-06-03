<?php
$pageTitle = 'Главная';
$hotProducts = Product::all(['is_hot' => 1, 'limit' => 8]);
$popularProducts = Product::all(['sort' => 'popular', 'limit' => 8]);
$discountProducts = Product::all(['has_discount' => 1, 'limit' => 4]);
$newProducts = Product::all(['is_new' => 1, 'limit' => 4]);
$categories = Category::all();
$lang = get_setting('language', 'ru');
$contentFile = __DIR__ . '/../../views/home.php';
require __DIR__ . '/../../views/layout.php';
