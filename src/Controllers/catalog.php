<?php
$pageTitle = t('catalog');
$lang = get_setting('language', 'ru');

$categorySlug = $params[0] ?? null;

// Мульти-выбор: через GET 'categories' и 'brands' (comma-separated)
$cats = [];
if (!empty($_GET['categories_arr']) && is_array($_GET['categories_arr'])) {
    $cats = array_filter(array_map('strval', $_GET['categories_arr']));
} elseif (!empty($_GET['categories'])) {
    $cats = array_filter(explode(',', $_GET['categories']));
}
if ($categorySlug) $cats[] = $categorySlug;
$cats = array_values(array_unique($cats));

$brs = [];
if (!empty($_GET['brands_arr']) && is_array($_GET['brands_arr'])) {
    $brs = array_filter(array_map('strval', $_GET['brands_arr']));
} elseif (!empty($_GET['brands'])) {
    $brs = array_filter(explode(',', $_GET['brands']));
} elseif (!empty($_GET['brand'])) {
    $brs = [$_GET['brand']];
}

$opts = [
    'categories' => $cats,
    'brands' => $brs,
    'q' => $_GET['q'] ?? null,
    'min_price' => $_GET['min_price'] ?? null,
    'max_price' => $_GET['max_price'] ?? null,
    'sort' => $_GET['sort'] ?? 'popular',
    'is_hot' => !empty($_GET['hot']),
    'is_new' => !empty($_GET['new']),
    'has_discount' => !empty($_GET['discount']),
];

$products = Product::all($opts);
// Бренды: если выбрана одна категория — её бренды, иначе все
$brands = Product::brands(count($cats) === 1 ? $cats[0] : null);
$allCategories = Category::all();

// Текущая категория для заголовка (если ровно одна выбрана)
$category = null;
if (count($cats) === 1) {
    $category = Category::findBySlug($cats[0]);
}

if ($category) {
    $pageTitle = $lang === 'en' ? $category['name_en'] : $category['name_ru'];
} elseif (!empty($opts['q'])) {
    $pageTitle = t('search') . ': ' . $opts['q'];
}

// Передаём массивы для view
$selectedCats = $cats;
$selectedBrands = $brs;

$contentFile = __DIR__ . '/../../views/catalog.php';
require __DIR__ . '/../../views/layout.php';
