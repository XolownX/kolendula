<?php
header('Content-Type: application/json; charset=utf-8');

$action = $params[0] ?? '';

// FAQ-бот: простой матчинг ключевых слов
function bot_reply(string $text): ?string {
    $t = mb_strtolower($text);
    $rules = [
        ['доставк|курьер|пвз|самовывоз', 'Мы доставляем по всей России. Самовывоз бесплатно, ПВЗ — 299 ₽, курьер — 499 ₽. Сроки: 1–3 дня по Хабаровску, 3–7 по РФ.'],
        ['возврат|вернуть|обмен',         'Возврат возможен в течение 14 дней с момента покупки. Технически исправный товар в полной комплектации можно вернуть без объяснения причин.'],
        ['гарантия|сломал',               'Гарантия от производителя — обычно 12–24 месяца. На ноутбуки и смартфоны — от 12 мес., на крупную бытовую технику — до 36 мес.'],
        ['оплат|карт|нал',                'Принимаем оплату картами Visa / Mastercard / Mir. Также можно оплатить при получении (только для самовывоза).'],
        ['цен|стоим|скидк',               'Текущие цены и скидки указаны на странице товара. Подпишитесь на рассылку, чтобы получать промокоды.'],
        ['телефон|контакт|связ',          'Наша поддержка: +7 (4212) 99-99-99, ежедневно с 9:00 до 21:00. Или просто напишите здесь.'],
        ['работ|часы|режим',              'Магазины работают с 10:00 до 21:00 без выходных. Поддержка — с 9:00 до 21:00.'],
        ['привет|здравств|добр|hi|hello', 'Здравствуйте! Я бот-помощник Kolendula. Чем могу помочь?'],
        ['спасиб|благодар',               'Пожалуйста! Если возникнут ещё вопросы — пишите.'],
    ];
    foreach ($rules as [$re, $reply]) {
        if (preg_match('/' . $re . '/u', $t)) return $reply;
    }
    return null;
}

if ($action === 'messages' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    json_response(['messages' => Chat::messages()]);
}

if ($action === 'send' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $body = json_decode(file_get_contents('php://input'), true) ?: $_POST;
    $text = trim((string)($body['text'] ?? ''));
    if (!$text) json_response(['ok' => false, 'error' => 'empty'], 400);
    Chat::send('user', $text);

    // Бот пытается ответить, если оператор не подключен
    $reply = bot_reply($text);
    if ($reply !== null) {
        Chat::send('bot', $reply);
    }
    json_response(['ok' => true, 'messages' => Chat::messages()]);
}

if ($action === 'operator' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    // Пользователь хочет живого оператора
    Chat::send('user', '[Запрос на оператора]');
    Chat::send('bot', 'Оператор присоединится к чату в ближайшее время. Опишите вопрос — мы передадим его специалисту.');
    json_response(['ok' => true, 'messages' => Chat::messages()]);
}

// Админ-API
if ($action === 'admin_threads' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    require_admin();
    json_response(['threads' => Chat::threadsForAdmin(), 'unread' => Chat::unreadTotal()]);
}

if ($action === 'admin_thread' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    require_admin();
    $uid = !empty($_GET['user_id']) ? (int)$_GET['user_id'] : null;
    $sid = !empty($_GET['session_id']) ? (string)$_GET['session_id'] : null;
    $msgs = Chat::threadMessages($uid, $sid);
    Chat::markRead($uid, $sid);
    json_response(['messages' => $msgs]);
}

if ($action === 'admin_send' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    require_admin();
    $body = json_decode(file_get_contents('php://input'), true) ?: $_POST;
    $uid = !empty($body['user_id']) ? (int)$body['user_id'] : null;
    $sid = !empty($body['session_id']) ? (string)$body['session_id'] : null;
    $text = trim((string)($body['text'] ?? ''));
    if (!$text) json_response(['ok'=>false,'error'=>'empty'], 400);
    if ($uid) Chat::adminSendTo('user_id', $uid, $text);
    elseif ($sid) Chat::adminSendTo('session_id', $sid, $text);
    json_response(['ok' => true]);
}

http_response_code(404);
json_response(['error' => 'not_found'], 404);
