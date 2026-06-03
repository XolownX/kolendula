<?php
$q = trim($_GET['q'] ?? '');
$limit = min((int)($_GET['limit'] ?? 8), 20);
if (mb_strlen($q) < 2) {
    json_response(['products' => []]);
}
$products = Product::all(['q' => $q, 'limit' => $limit, 'sort' => 'popular']);
$out = array_map(fn($p) => [
    'id' => (int)$p['id'],
    'name' => $p['name'],
    'brand' => $p['brand'],
    'price' => (int)$p['price'],
    'image' => $p['image'],
], $products);
json_response(['products' => $out]);
