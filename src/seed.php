<?php
require __DIR__ . '/db.php';
$pdo = db();

// Очистка (только товары/категории — пользователей и заказы НЕ трогаем)
// SQLite не принимает несколько запросов в одном exec() единым стейтментом, но exec() их разбирает
foreach (['DELETE FROM reviews','DELETE FROM products','DELETE FROM categories','DELETE FROM cart_items'] as $q) {
    try { $pdo->exec($q); } catch (Exception $e) {}
}

// Админ (создаём если нет)
$check = $pdo->prepare('SELECT id FROM users WHERE login = ?');
$check->execute(['admin']);
if (!$check->fetch()) {
    $hash = password_hash('admin123', PASSWORD_BCRYPT);
    $ins = $pdo->prepare('INSERT INTO users (name, login, email, password_hash, is_admin) VALUES (?, ?, ?, ?, 1)');
    $ins->execute(['Администратор', 'admin', 'admin@kolendula.local', $hash]);
} else {
    // гарантируем флаг админа
    $pdo->prepare('UPDATE users SET is_admin = 1 WHERE login = ?')->execute(['admin']);
}

// Категории
$categories = [
    ['laptops',    'Ноутбуки',      'Laptops',       '💻'],
    ['phones',     'Телефоны',      'Phones',        '📱'],
    ['watches',    'Смарт-часы',    'Smart Watches', '⌚'],
    ['mice',       'Мышки',         'Mice',          '🖱'],
    ['computers',  'Компьютеры',    'Computers',     '🖥'],
    ['microphones','Микрофоны',     'Microphones',   '🎙'],
    ['headphones', 'Наушники',      'Headphones',    '🎧'],
    ['vacuums',    'Пылесосы',      'Vacuums',       '🌀'],
    ['monitors',   'Мониторы',      'Monitors',      '🖥'],
    ['keyboards',  'Клавиатуры',    'Keyboards',     '⌨'],
];
$stmt = $pdo->prepare('INSERT INTO categories (slug, name_ru, name_en, icon) VALUES (?, ?, ?, ?)');
foreach ($categories as $c) $stmt->execute($c);

$catIds = [];
foreach ($pdo->query('SELECT id, slug FROM categories') as $row) {
    $catIds[$row['slug']] = $row['id'];
}

// Товары: [category_slug, name, brand, price, old_price, description, specs(json), image_id, rating, reviews_count, is_hot, is_new, sales]
$products = [
    // Ноутбуки
    ['laptops', 'MacBook Pro 14" M4 Pro', 'Apple', 219990, 249990, 'Профессиональный ноутбук Apple с чипом M4 Pro. Экран Liquid Retina XDR, до 22 часов работы.', ['Процессор'=>'Apple M4 Pro','ОЗУ'=>'24 ГБ','Накопитель'=>'1 ТБ SSD','Дисплей'=>'14.2" Liquid Retina XDR','Вес'=>'1.55 кг'], 'mbp14', 4.9, 312, 1, 1, 540],
    ['laptops', 'ASUS ROG Zephyrus G16', 'ASUS', 189990, 215000, 'Игровой ультрабук с RTX 4070 и OLED-дисплеем 240Гц.', ['Процессор'=>'Intel Core Ultra 9','GPU'=>'RTX 4070','ОЗУ'=>'32 ГБ','Дисплей'=>'16" OLED 240Hz'], 'rog', 4.7, 156, 1, 0, 220],
    ['laptops', 'Lenovo ThinkPad X1 Carbon Gen 12', 'Lenovo', 159000, null, 'Бизнес-ноутбук премиум-класса для работы и поездок.', ['Процессор'=>'Intel Core Ultra 7','ОЗУ'=>'16 ГБ','SSD'=>'1 ТБ','Вес'=>'1.09 кг'], 'thinkpad', 4.6, 98, 0, 0, 140],
    ['laptops', 'HUAWEI MateBook X Pro 2024', 'HUAWEI', 129990, 149990, 'Лёгкий ультрабук с OLED-дисплеем и стильным дизайном.', ['Процессор'=>'Intel Core Ultra 7','ОЗУ'=>'16 ГБ','SSD'=>'1 ТБ','Дисплей'=>'14.2" OLED'], 'matebook', 4.5, 76, 0, 1, 88],
    ['laptops', 'MSI Stealth 16 AI Studio', 'MSI', 235000, null, 'Игровой и креативный ноутбук с RTX 4080 и 16" OLED.', ['Процессор'=>'Intel Core Ultra 9','GPU'=>'RTX 4080','ОЗУ'=>'32 ГБ'], 'msi', 4.7, 64, 0, 1, 45],

    // Телефоны
    ['phones', 'iPhone 16 Pro Max', 'Apple', 134990, 149990, 'Флагман Apple с Apple Intelligence и титановым корпусом.', ['Чип'=>'A18 Pro','Память'=>'256 ГБ','Экран'=>'6.9" Super Retina XDR','Камера'=>'48 МП'], 'iphone', 4.9, 845, 1, 1, 1200],
    ['phones', 'Samsung Galaxy S25 Ultra', 'Samsung', 119990, 139990, 'Топовый Android-флагман с S Pen и камерой 200 МП.', ['Чип'=>'Snapdragon 8 Elite','Память'=>'512 ГБ','Экран'=>'6.9" Dynamic AMOLED 2X','Камера'=>'200 МП'], 'galaxy', 4.8, 612, 1, 1, 980],
    ['phones', 'Xiaomi 15 Pro', 'Xiaomi', 79990, 94990, 'Флагман с оптикой Leica и быстрой зарядкой 90Вт.', ['Чип'=>'Snapdragon 8 Elite','Память'=>'256 ГБ','Экран'=>'6.73" AMOLED 120Hz'], 'xiaomi', 4.7, 432, 1, 0, 720],
    ['phones', 'Google Pixel 9 Pro', 'Google', 89990, null, 'Чистый Android и лучшая вычислительная фотография.', ['Чип'=>'Tensor G4','Память'=>'256 ГБ','Экран'=>'6.3" LTPO OLED'], 'pixel', 4.6, 287, 0, 1, 310],
    ['phones', 'OnePlus 13', 'OnePlus', 64990, 74990, 'Быстрый и сбалансированный смартфон.', ['Чип'=>'Snapdragon 8 Elite','Память'=>'256 ГБ'], 'oneplus', 4.5, 198, 0, 0, 240],

    // Смарт-часы
    ['watches', 'Apple Watch Ultra 2', 'Apple', 89990, null, 'Часы для экстремальных условий с титановым корпусом.', ['Дисплей'=>'49 мм','Защита'=>'WR100','Автономность'=>'до 72 ч'], 'aw_ultra', 4.9, 234, 1, 0, 180],
    ['watches', 'Apple Watch Series 10', 'Apple', 44990, 49990, 'Самые тонкие Apple Watch с большим дисплеем.', ['Дисплей'=>'46 мм OLED','Чип'=>'S10'], 'aw_s10', 4.8, 412, 1, 1, 530],
    ['watches', 'Samsung Galaxy Watch 7', 'Samsung', 28990, 34990, 'Смарт-часы на Wear OS с премиальным дизайном.', ['Дисплей'=>'44 мм AMOLED'], 'gw7', 4.5, 156, 0, 0, 210],
    ['watches', 'Garmin Fenix 8', 'Garmin', 79990, null, 'Премиум мультиспортивные часы с картами.', ['Дисплей'=>'AMOLED','Автономность'=>'до 16 дней'], 'fenix', 4.8, 88, 0, 1, 65],
    ['watches', 'Xiaomi Watch S4', 'Xiaomi', 14990, 17990, 'Стильные часы по доступной цене.', ['Дисплей'=>'1.43" AMOLED'], 'xwatch', 4.4, 312, 0, 0, 450],

    // Мышки
    ['mice', 'Logitech MX Master 3S', 'Logitech', 9990, 11990, 'Лучшая мышь для продуктивности с тихими кнопками.', ['Сенсор'=>'8000 DPI','Подключение'=>'Bluetooth/USB-C'], 'mxmaster', 4.9, 1024, 1, 0, 1500],
    ['mice', 'Razer DeathAdder V3 Pro', 'Razer', 14990, 16990, 'Топовая беспроводная игровая мышь.', ['Сенсор'=>'Focus Pro 30K','Вес'=>'63 г'], 'deathadder', 4.8, 456, 1, 1, 380],
    ['mice', 'Logitech G Pro X Superlight 2', 'Logitech', 17990, null, 'Сверхлёгкая беспроводная игровая мышь.', ['Сенсор'=>'HERO 2','Вес'=>'60 г'], 'gpro', 4.9, 332, 0, 1, 270],
    ['mice', 'Apple Magic Mouse', 'Apple', 8990, 9990, 'Беспроводная мышь Apple с поверхностью Multi-Touch.', ['Подключение'=>'Bluetooth'], 'magicmouse', 4.2, 287, 0, 0, 410],

    // Компьютеры (готовые сборки)
    ['computers', 'Сборка Kolendula Gaming RTX 4070', 'Kolendula', 145990, 165990, 'Игровой ПК на Ryzen 7 и RTX 4070 для QHD-гейминга.', ['CPU'=>'AMD Ryzen 7 7800X3D','GPU'=>'RTX 4070','ОЗУ'=>'32 ГБ DDR5','SSD'=>'1 ТБ'], 'pc_gaming', 4.8, 87, 1, 1, 95],
    ['computers', 'Сборка Kolendula Pro Workstation', 'Kolendula', 245000, null, 'Профессиональная рабочая станция для рендеринга и монтажа.', ['CPU'=>'Intel Core i9-14900K','GPU'=>'RTX 4080 Super','ОЗУ'=>'64 ГБ DDR5'], 'pc_pro', 4.9, 42, 1, 0, 30],
    ['computers', 'Apple Mac mini M4', 'Apple', 79990, 89990, 'Компактный мощный десктоп с чипом M4.', ['CPU'=>'Apple M4','ОЗУ'=>'16 ГБ','SSD'=>'512 ГБ'], 'macmini', 4.7, 195, 0, 1, 240],
    ['computers', 'Сборка Kolendula Office', 'Kolendula', 49990, 59990, 'Тихая офисная сборка для работы и учёбы.', ['CPU'=>'Intel Core i5-13400','ОЗУ'=>'16 ГБ','SSD'=>'512 ГБ'], 'pc_office', 4.5, 132, 0, 0, 180],

    // Микрофоны
    ['microphones', 'Shure SM7B', 'Shure', 39990, 44990, 'Легендарный студийный микрофон для подкастов и стримов.', ['Тип'=>'Динамический','Подключение'=>'XLR'], 'sm7b', 4.9, 234, 1, 0, 120],
    ['microphones', 'Rode NT-USB+', 'Rode', 14990, null, 'Качественный USB-микрофон для стриминга.', ['Тип'=>'Конденсаторный','Подключение'=>'USB-C'], 'rode', 4.7, 187, 0, 1, 220],
    ['microphones', 'HyperX QuadCast S', 'HyperX', 12990, 15990, 'Игровой USB-микрофон с RGB-подсветкой.', ['Тип'=>'Конденсаторный','Подключение'=>'USB'], 'quadcast', 4.6, 412, 0, 0, 380],

    // Наушники
    ['headphones', 'Sony WH-1000XM5', 'Sony', 29990, 39990, 'Лучшее активное шумоподавление на рынке.', ['Тип'=>'Накладные','Bluetooth'=>'5.2','ANC'=>'Да','Автономность'=>'30 ч'], 'sony_xm5', 4.9, 1245, 1, 0, 890],
    ['headphones', 'AirPods Pro 2 USB-C', 'Apple', 24990, 27990, 'Беспроводные наушники Apple с шумоподавлением.', ['Тип'=>'TWS','ANC'=>'Да'], 'airpods', 4.8, 2034, 1, 0, 1500],
    ['headphones', 'Bose QuietComfort Ultra', 'Bose', 34990, null, 'Премиум-комфорт и пространственный звук.', ['ANC'=>'Да','Автономность'=>'24 ч'], 'bose', 4.7, 432, 0, 1, 280],
    ['headphones', 'Sennheiser Momentum 4', 'Sennheiser', 27990, 32990, 'Аудиофильское звучание и стильный дизайн.', ['ANC'=>'Да','Автономность'=>'60 ч'], 'momentum', 4.7, 286, 0, 0, 195],
    ['headphones', 'Marshall Major V', 'Marshall', 12990, 14990, 'Иконичный стиль и до 80 часов работы.', ['Автономность'=>'80 ч'], 'marshall', 4.5, 198, 0, 1, 320],

    // Пылесосы
    ['vacuums', 'Dyson V15 Detect', 'Dyson', 64990, 79990, 'Беспроводной пылесос с лазерной подсветкой.', ['Мощность всасывания'=>'230 AW','Автономность'=>'до 60 мин'], 'dyson', 4.8, 312, 1, 0, 145],
    ['vacuums', 'Xiaomi Robot Vacuum X20+', 'Xiaomi', 39990, 49990, 'Робот-пылесос с самоочисткой и LiDAR.', ['Тип'=>'Робот','Резервуар'=>'4 л'], 'robot_xm', 4.6, 287, 1, 1, 220],
    ['vacuums', 'iRobot Roomba j7+', 'iRobot', 54990, null, 'Умный робот-пылесос с компьютерным зрением.', ['Тип'=>'Робот'], 'roomba', 4.5, 156, 0, 0, 90],
    ['vacuums', 'Samsung Bespoke Jet AI', 'Samsung', 79990, 94990, 'Премиум-пылесос со станцией самоочистки.', ['Мощность'=>'280 Вт'], 'bespoke', 4.7, 88, 0, 1, 55],

    // Мониторы
    ['monitors', 'LG UltraGear 27GR95QE OLED', 'LG', 89990, 109990, '27" QHD OLED-монитор 240Гц для игр.', ['Диагональ'=>'27"','Разрешение'=>'QHD','Частота'=>'240 Hz','Тип'=>'OLED'], 'lg_oled', 4.8, 145, 1, 0, 85],
    ['monitors', 'Dell UltraSharp U2723QE', 'Dell', 64990, null, '4K-монитор для работы с цветом.', ['Диагональ'=>'27"','Разрешение'=>'4K','Тип'=>'IPS'], 'dell', 4.7, 210, 0, 0, 130],
    ['monitors', 'Samsung Odyssey G9 OLED', 'Samsung', 159990, 179990, 'Изогнутый 49" супер-ультра OLED для гейминга.', ['Диагональ'=>'49"','Тип'=>'OLED','Частота'=>'240 Hz'], 'odyssey', 4.8, 67, 1, 1, 35],

    // Клавиатуры
    ['keyboards', 'Keychron Q1 Pro', 'Keychron', 22990, 26990, 'Беспроводная механическая клавиатура с QMK/VIA.', ['Формат'=>'75%','Свитчи'=>'Gateron Pro','Подключение'=>'BT/USB-C'], 'keychron', 4.8, 187, 1, 0, 140],
    ['keyboards', 'Logitech MX Keys S', 'Logitech', 11990, 13990, 'Тонкая беспроводная клавиатура для продуктивности.', ['Формат'=>'Full','Подключение'=>'BT/USB'], 'mxkeys', 4.7, 412, 0, 0, 380],
    ['keyboards', 'Razer Huntsman V3 Pro', 'Razer', 24990, null, 'Аналоговая оптическая клавиатура для геймеров.', ['Свитчи'=>'Analog Optical'], 'huntsman', 4.6, 156, 0, 1, 95],
    ['keyboards', 'Apple Magic Keyboard', 'Apple', 14990, null, 'Беспроводная клавиатура Apple с Touch ID.', ['Подключение'=>'Bluetooth'], 'magickb', 4.4, 287, 0, 0, 210],
];

$stmt = $pdo->prepare('INSERT INTO products (category_id, name, brand, slug, price, old_price, description, specs, image, rating, reviews_count, is_hot, is_new, sales_count) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');

$reviewTexts = [
    'Отличный товар, полностью оправдал ожидания. Доставка быстрая, упаковка надёжная.',
    'Пользуюсь уже месяц — впечатления только положительные. Рекомендую к покупке.',
    'Качество на высоте. За эти деньги это лучший выбор на рынке.',
    'Брал в подарок — все в восторге. Хороший магазин, спасибо!',
    'Работает идеально. Сборка качественная, материалы премиальные.',
    'Не ожидал такого качества за такую цену. Однозначно рекомендую.',
    'Долго выбирал между моделями — не пожалел о выборе. Всё супер.',
    'Один из лучших в своём классе. Сравнивал с конкурентами — этот лучше.',
    'Доволен покупкой. Магазин Kolendula радует ассортиментом и сервисом.',
    'Очень тихий и эффективный. Прекрасное соотношение цена/качество.',
];
$authors = ['Алексей М.', 'Мария К.', 'Дмитрий С.', 'Анна П.', 'Иван Л.', 'Ольга Н.', 'Сергей В.', 'Екатерина Р.', 'Павел И.', 'Юлия Д.'];

$reviewStmt = $pdo->prepare('INSERT INTO reviews (product_id, author_name, rating, text) VALUES (?, ?, ?, ?)');

foreach ($products as $p) {
    $slug = strtolower(preg_replace('/[^a-z0-9]+/i', '-', $p[1] . '-' . $p[2]));
    $slug = trim($slug, '-');
    $stmt->execute([
        $catIds[$p[0]], $p[1], $p[2], $slug,
        $p[3], $p[4], $p[5], json_encode($p[6], JSON_UNESCAPED_UNICODE), $p[7],
        $p[8], $p[9], $p[10], $p[11], $p[12]
    ]);
    $productId = $pdo->lastInsertId();
    // Добавим 3-5 отзывов
    $reviewCount = rand(3, 5);
    for ($i = 0; $i < $reviewCount; $i++) {
        $reviewStmt->execute([
            $productId,
            $authors[array_rand($authors)],
            rand(4, 5),
            $reviewTexts[array_rand($reviewTexts)],
        ]);
    }
}

echo "✓ Сид завершён. Товаров: " . count($products) . "\n";
