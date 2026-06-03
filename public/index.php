<?php
// Front controller
require __DIR__ . '/../src/db.php';
require __DIR__ . '/../src/helpers.php';
require __DIR__ . '/../src/Models/Product.php';

start_session();

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

// Route map: pattern => controller file
$routes = [
    ['GET',  '#^/$#',                              'home.php'],
    ['GET',  '#^/catalog$#',                       'catalog.php'],
    ['GET',  '#^/category/([\w-]+)$#',             'catalog.php'],
    ['GET',  '#^/product/(\d+)$#',                 'product.php'],
    ['POST', '#^/product/(\d+)$#',                 'product.php'],
    ['GET',  '#^/search$#',                        'catalog.php'],
    ['GET',  '#^/cart$#',                          'cart.php'],
    ['POST', '#^/cart/add$#',                      'cart_action.php'],
    ['POST', '#^/cart/update$#',                   'cart_action.php'],
    ['POST', '#^/cart/remove$#',                   'cart_action.php'],
    ['POST', '#^/cart/checkout$#',                 'cart_action.php'],
    ['GET',  '#^/login$#',                         'auth.php'],
    ['POST', '#^/login$#',                         'auth.php'],
    ['GET',  '#^/register$#',                      'auth.php'],
    ['POST', '#^/register$#',                      'auth.php'],
    ['GET',  '#^/logout$#',                        'auth.php'],
    ['GET',  '#^/profile$#',                       'profile.php'],
    ['POST', '#^/profile$#',                       'profile.php'],
    ['GET',  '#^/settings$#',                      'settings.php'],
    ['POST', '#^/settings$#',                      'settings.php'],
    ['GET',  '#^/forgot$#',                        'forgot.php'],
    ['POST', '#^/forgot$#',                        'forgot.php'],
    ['GET',  '#^/checkout$#',                      'checkout.php'],
    ['POST', '#^/checkout$#',                      'checkout.php'],
    ['GET',  '#^/orders$#',                        'orders.php'],
    ['GET',  '#^/orders/(\d+)$#',                  'orders.php'],
    ['GET',  '#^/profile/cards$#',                 'cards.php'],
    ['POST', '#^/profile/cards$#',                 'cards.php'],
    ['GET',  '#^/api/chat/(messages|admin_threads|admin_thread)$#', 'chat.php'],
    ['POST', '#^/api/chat/(send|operator|admin_send)$#',            'chat.php'],
    ['GET',  '#^/admin$#',                         'admin.php'],
    ['GET',  '#^/admin/(\w+)$#',                   'admin.php'],
    ['POST', '#^/admin/(\w+)$#',                   'admin.php'],
    ['GET',  '#^/api/search$#',                    'api_search.php'],
    ['GET',  '#^/api/cart-count$#',                'api_cart_count.php'],
    ['GET',  '#^/img/product/(\w+)\.svg$#',        'product_image.php'],
];

$matched = false;
foreach ($routes as $r) {
    if ($r[0] === $method && preg_match($r[1], $uri, $m)) {
        array_shift($m);
        $params = $m;
        $controllerFile = __DIR__ . '/../src/Controllers/' . $r[2];
        if (file_exists($controllerFile)) {
            require $controllerFile;
            $matched = true;
            break;
        }
    }
}

if (!$matched) {
    http_response_code(404);
    $pageTitle = '404';
    $contentFile = __DIR__ . '/../views/404.php';
    require __DIR__ . '/../views/layout.php';
}
