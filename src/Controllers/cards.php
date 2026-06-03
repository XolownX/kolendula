<?php
$user = current_user();
if (!$user) redirect('/login?return=' . urlencode('/profile/cards'));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id) Card::delete($id, (int)$user['id']);
        redirect('/profile/cards');
    }
}

$cards = Card::forUser((int)$user['id']);
$pageTitle = 'Мои карты';
$contentFile = __DIR__ . '/../../views/cards.php';
require __DIR__ . '/../../views/layout.php';
