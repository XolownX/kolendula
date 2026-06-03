<?php
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

if ($path === '/logout') {
    User::logout();
    redirect('/');
}

if ($path === '/login') {
    $error = null;
    if ($method === 'POST') {
        $result = User::login(trim($_POST['login'] ?? ''), $_POST['password'] ?? '');
        if ($result['ok']) {
            redirect($_GET['return'] ?? '/');
        } else {
            $error = $result['error'];
        }
    }
    $pageTitle = t('login');
    $contentFile = __DIR__ . '/../../views/login.php';
    require __DIR__ . '/../../views/layout.php';
    return;
}

if ($path === '/register') {
    $error = null;
    $values = ['name'=>'','login'=>'','email'=>'','phone'=>''];
    if ($method === 'POST') {
        $values = array_intersect_key($_POST, $values) + $values;
        $result = User::register($_POST);
        if ($result['ok']) {
            redirect('/?registered=1');
        } else {
            $error = $result['error'];
        }
    }
    $pageTitle = t('register');
    $contentFile = __DIR__ . '/../../views/register.php';
    require __DIR__ . '/../../views/layout.php';
    return;
}
