<?php

class Product
{
    public static function all(array $opts = []): array
    {
        $pdo = db();
        $where = [];
        $params = [];
        $sql = 'SELECT p.*, c.slug AS category_slug, c.name_ru AS category_name FROM products p JOIN categories c ON c.id = p.category_id';

        // Категории: поддержка одной или множественного выбора
        $cats = [];
        if (!empty($opts['categories']) && is_array($opts['categories'])) {
            $cats = array_filter(array_map('strval', $opts['categories']));
        } elseif (!empty($opts['category'])) {
            $cats = [(string)$opts['category']];
        }
        if ($cats) {
            $ph = implode(',', array_fill(0, count($cats), '?'));
            $where[] = "c.slug IN ($ph)";
            foreach ($cats as $c) $params[] = $c;
        }

        $brs = [];
        if (!empty($opts['brands']) && is_array($opts['brands'])) {
            $brs = array_filter(array_map('strval', $opts['brands']));
        } elseif (!empty($opts['brand'])) {
            $brs = [(string)$opts['brand']];
        }
        if ($brs) {
            $ph = implode(',', array_fill(0, count($brs), '?'));
            $where[] = "p.brand IN ($ph)";
            foreach ($brs as $b) $params[] = $b;
        }
        if (!empty($opts['q'])) {
            $where[] = '(LOWER(p.name) LIKE ? OR LOWER(p.brand) LIKE ?)';
            $q = '%' . mb_strtolower($opts['q']) . '%';
            $params[] = $q;
            $params[] = $q;
        }
        if (!empty($opts['min_price'])) {
            $where[] = 'p.price >= ?';
            $params[] = (int)$opts['min_price'];
        }
        if (!empty($opts['max_price'])) {
            $where[] = 'p.price <= ?';
            $params[] = (int)$opts['max_price'];
        }
        if (!empty($opts['is_hot'])) $where[] = 'p.is_hot = 1';
        if (!empty($opts['is_new'])) $where[] = 'p.is_new = 1';
        if (!empty($opts['has_discount'])) $where[] = 'p.old_price IS NOT NULL';

        if ($where) $sql .= ' WHERE ' . implode(' AND ', $where);

        $sort = $opts['sort'] ?? 'popular';
        $sortMap = [
            'popular' => 'p.sales_count DESC',
            'price_asc' => 'p.price ASC',
            'price_desc' => 'p.price DESC',
            'rating' => 'p.rating DESC',
            'new' => 'p.created_at DESC, p.is_new DESC',
        ];
        $sql .= ' ORDER BY ' . ($sortMap[$sort] ?? $sortMap['popular']);

        if (!empty($opts['limit'])) $sql .= ' LIMIT ' . (int)$opts['limit'];

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $stmt = db()->prepare('SELECT p.*, c.slug AS category_slug, c.name_ru AS category_name FROM products p JOIN categories c ON c.id = p.category_id WHERE p.id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public static function reviews(int $productId): array
    {
        $stmt = db()->prepare('SELECT * FROM reviews WHERE product_id = ? ORDER BY created_at DESC');
        $stmt->execute([$productId]);
        return $stmt->fetchAll();
    }

    public static function brands(?string $categorySlug = null): array
    {
        $sql = 'SELECT DISTINCT p.brand FROM products p';
        $params = [];
        if ($categorySlug) {
            $sql .= ' JOIN categories c ON c.id = p.category_id WHERE c.slug = ?';
            $params[] = $categorySlug;
        }
        $sql .= ' ORDER BY p.brand';
        $stmt = db()->prepare($sql);
        $stmt->execute($params);
        return array_column($stmt->fetchAll(), 'brand');
    }
}

class Category
{
    public static function all(): array
    {
        return db()->query('SELECT * FROM categories ORDER BY id')->fetchAll();
    }

    public static function findBySlug(string $slug): ?array
    {
        $stmt = db()->prepare('SELECT * FROM categories WHERE slug = ?');
        $stmt->execute([$slug]);
        return $stmt->fetch() ?: null;
    }
}

class Cart
{
    public static function items(): array
    {
        [$col, $val] = cart_key();
        $stmt = db()->prepare("
            SELECT ci.id AS cart_item_id, ci.quantity, p.*
            FROM cart_items ci
            JOIN products p ON p.id = ci.product_id
            WHERE ci.$col = ?
            ORDER BY ci.created_at DESC
        ");
        $stmt->execute([$val]);
        return $stmt->fetchAll();
    }

    public static function add(int $productId, int $qty = 1): void
    {
        [$col, $val] = cart_key();
        $stmt = db()->prepare("SELECT id, quantity FROM cart_items WHERE $col = ? AND product_id = ?");
        $stmt->execute([$val, $productId]);
        $existing = $stmt->fetch();
        if ($existing) {
            $upd = db()->prepare('UPDATE cart_items SET quantity = ? WHERE id = ?');
            $upd->execute([$existing['quantity'] + $qty, $existing['id']]);
        } else {
            $ins = db()->prepare("INSERT INTO cart_items ($col, product_id, quantity) VALUES (?, ?, ?)");
            $ins->execute([$val, $productId, $qty]);
        }
    }

    public static function update(int $itemId, int $qty): void
    {
        if ($qty <= 0) {
            self::remove($itemId);
            return;
        }
        [$col, $val] = cart_key();
        $stmt = db()->prepare("UPDATE cart_items SET quantity = ? WHERE id = ? AND $col = ?");
        $stmt->execute([$qty, $itemId, $val]);
    }

    public static function remove(int $itemId): void
    {
        [$col, $val] = cart_key();
        $stmt = db()->prepare("DELETE FROM cart_items WHERE id = ? AND $col = ?");
        $stmt->execute([$itemId, $val]);
    }

    public static function clear(): void
    {
        [$col, $val] = cart_key();
        $stmt = db()->prepare("DELETE FROM cart_items WHERE $col = ?");
        $stmt->execute([$val]);
    }

    public static function total(): int
    {
        $sum = 0;
        foreach (self::items() as $item) $sum += $item['price'] * $item['quantity'];
        return $sum;
    }
}

class User
{
    public static function register(array $data): array
    {
        $name = trim($data['name'] ?? '');
        $login = trim($data['login'] ?? '');
        $email = trim($data['email'] ?? '');
        $phone = trim($data['phone'] ?? '');
        $password = $data['password'] ?? '';
        $passwordConfirm = $data['password_confirm'] ?? '';

        if (!$name || !$login || !$email || !$password) {
            return ['ok' => false, 'error' => t('all_fields_required')];
        }
        if ($password !== $passwordConfirm) {
            return ['ok' => false, 'error' => t('passwords_mismatch')];
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['ok' => false, 'error' => 'Email'];
        }

        $check = db()->prepare('SELECT id FROM users WHERE email = ? OR login = ?');
        $check->execute([$email, $login]);
        $existing = $check->fetch();
        if ($existing) {
            // Determine which
            $check2 = db()->prepare('SELECT id FROM users WHERE email = ?');
            $check2->execute([$email]);
            if ($check2->fetch()) return ['ok' => false, 'error' => t('email_taken')];
            return ['ok' => false, 'error' => t('login_taken')];
        }

        $hash = password_hash($password, PASSWORD_BCRYPT);
        $ins = db()->prepare('INSERT INTO users (name, login, email, phone, password_hash) VALUES (?, ?, ?, ?, ?)');
        $ins->execute([$name, $login, $email, $phone, $hash]);
        $userId = (int)db()->lastInsertId();
        start_session();
        $_SESSION['user_id'] = $userId;
        // Перенесём корзину гостя
        if (!empty($_SESSION['cart_sid'])) {
            $upd = db()->prepare('UPDATE cart_items SET user_id = ?, session_id = NULL WHERE session_id = ?');
            $upd->execute([$userId, $_SESSION['cart_sid']]);
        }
        return ['ok' => true];
    }

    public static function login(string $loginOrEmail, string $password): array
    {
        $stmt = db()->prepare('SELECT * FROM users WHERE login = ? OR email = ?');
        $stmt->execute([$loginOrEmail, $loginOrEmail]);
        $user = $stmt->fetch();
        if (!$user || !password_verify($password, $user['password_hash'])) {
            return ['ok' => false, 'error' => t('invalid_credentials')];
        }
        start_session();
        $_SESSION['user_id'] = $user['id'];
        if (!empty($_SESSION['cart_sid'])) {
            $upd = db()->prepare('UPDATE cart_items SET user_id = ?, session_id = NULL WHERE session_id = ?');
            $upd->execute([$user['id'], $_SESSION['cart_sid']]);
        }
        return ['ok' => true];
    }

    public static function logout(): void
    {
        start_session();
        unset($_SESSION['user_id']);
    }

    public static function update(int $userId, array $data): void
    {
        $fields = [];
        $params = [];
        foreach (['name', 'phone', 'email', 'avatar'] as $f) {
            if (array_key_exists($f, $data)) {
                $fields[] = "$f = ?";
                $params[] = $data[$f];
            }
        }
        if (!empty($data['password'])) {
            $fields[] = 'password_hash = ?';
            $params[] = password_hash($data['password'], PASSWORD_BCRYPT);
        }
        if (!$fields) return;
        $params[] = $userId;
        $stmt = db()->prepare('UPDATE users SET ' . implode(', ', $fields) . ' WHERE id = ?');
        $stmt->execute($params);
    }

    public static function findById(int $id): ?array
    {
        $stmt = db()->prepare('SELECT * FROM users WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public static function findByLoginOrEmail(string $loginOrEmail): ?array
    {
        $stmt = db()->prepare('SELECT * FROM users WHERE login = ? OR email = ?');
        $stmt->execute([$loginOrEmail, $loginOrEmail]);
        return $stmt->fetch() ?: null;
    }

    public static function all(): array
    {
        return db()->query('SELECT * FROM users ORDER BY id DESC')->fetchAll();
    }
}

class Order
{
    public static function create(int $userId, array $items, array $info): int
    {
        $pdo = db();
        $total = 0;
        foreach ($items as $i) $total += $i['price'] * $i['quantity'];
        $total += (int)($info['delivery_fee'] ?? 0);

        $ins = $pdo->prepare('INSERT INTO orders (user_id, total, status, payment_status, delivery_type, delivery_address, delivery_fee, card_last4, card_brand) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $ins->execute([
            $userId,
            $total,
            $info['status'] ?? 'processing',
            $info['payment_status'] ?? 'paid',
            $info['delivery_type'] ?? 'pickup',
            $info['delivery_address'] ?? '',
            (int)($info['delivery_fee'] ?? 0),
            $info['card_last4'] ?? null,
            $info['card_brand'] ?? null,
        ]);
        $orderId = (int)$pdo->lastInsertId();
        $itemIns = $pdo->prepare('INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)');
        foreach ($items as $i) {
            $itemIns->execute([$orderId, (int)$i['id'] ?? (int)$i['product_id'], (int)$i['quantity'], (int)$i['price']]);
        }
        return $orderId;
    }

    public static function forUser(int $userId): array
    {
        $stmt = db()->prepare('SELECT * FROM orders WHERE user_id = ? ORDER BY id DESC');
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $stmt = db()->prepare('SELECT * FROM orders WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public static function items(int $orderId): array
    {
        $stmt = db()->prepare('
            SELECT oi.*, p.name, p.brand, p.image, p.slug
            FROM order_items oi
            JOIN products p ON p.id = oi.product_id
            WHERE oi.order_id = ?
        ');
        $stmt->execute([$orderId]);
        return $stmt->fetchAll();
    }

    public static function all(): array
    {
        return db()->query('
            SELECT o.*, u.name AS user_name, u.email AS user_email
            FROM orders o JOIN users u ON u.id = o.user_id
            ORDER BY o.id DESC
        ')->fetchAll();
    }

    public static function updateStatus(int $orderId, string $status): void
    {
        $stmt = db()->prepare('UPDATE orders SET status = ? WHERE id = ?');
        $stmt->execute([$status, $orderId]);
    }

    public static function userBoughtProduct(int $userId, int $productId): bool
    {
        $stmt = db()->prepare('
            SELECT COUNT(*) AS n FROM order_items oi
            JOIN orders o ON o.id = oi.order_id
            WHERE o.user_id = ? AND oi.product_id = ?
        ');
        $stmt->execute([$userId, $productId]);
        return (int)$stmt->fetch()['n'] > 0;
    }
}

class Card
{
    public static function forUser(int $userId): array
    {
        $stmt = db()->prepare('SELECT * FROM saved_cards WHERE user_id = ? ORDER BY id DESC');
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public static function save(int $userId, string $last4, string $brand, ?int $expM, ?int $expY, string $holder): int
    {
        $stmt = db()->prepare('SELECT id FROM saved_cards WHERE user_id = ? AND last4 = ? LIMIT 1');
        $stmt->execute([$userId, $last4]);
        $existing = $stmt->fetch();
        if ($existing) return (int)$existing['id'];
        $ins = db()->prepare('INSERT INTO saved_cards (user_id, last4, brand, exp_month, exp_year, holder) VALUES (?, ?, ?, ?, ?, ?)');
        $ins->execute([$userId, $last4, $brand, $expM, $expY, $holder]);
        return (int)db()->lastInsertId();
    }

    public static function find(int $id, int $userId): ?array
    {
        $stmt = db()->prepare('SELECT * FROM saved_cards WHERE id = ? AND user_id = ?');
        $stmt->execute([$id, $userId]);
        return $stmt->fetch() ?: null;
    }

    public static function delete(int $id, int $userId): void
    {
        $stmt = db()->prepare('DELETE FROM saved_cards WHERE id = ? AND user_id = ?');
        $stmt->execute([$id, $userId]);
    }
}

class Chat
{
    public static function send(string $author, string $text): int
    {
        [$col, $val] = chat_key();
        $stmt = db()->prepare("INSERT INTO chat_messages ($col, author, text, is_read_by_admin) VALUES (?, ?, ?, ?)");
        $stmt->execute([$val, $author, $text, $author === 'user' ? 0 : 1]);
        return (int)db()->lastInsertId();
    }

    public static function messages(): array
    {
        [$col, $val] = chat_key();
        $stmt = db()->prepare("SELECT * FROM chat_messages WHERE $col = ? ORDER BY id ASC");
        $stmt->execute([$val]);
        return $stmt->fetchAll();
    }

    public static function adminSendTo(string $col, $val, string $text): void
    {
        $stmt = db()->prepare("INSERT INTO chat_messages ($col, author, text, is_read_by_admin) VALUES (?, 'operator', ?, 1)");
        $stmt->execute([$val, $text]);
    }

    public static function threadsForAdmin(): array
    {
        // Группируем диалоги по пользователю или session_id
        $rows = db()->query("
            SELECT
                COALESCE(CAST(user_id AS CHAR), session_id) AS thread_key,
                user_id, session_id,
                MAX(id) AS last_id,
                MAX(created_at) AS last_at,
                SUM(CASE WHEN is_read_by_admin = 0 THEN 1 ELSE 0 END) AS unread
            FROM chat_messages
            GROUP BY user_id, session_id
            ORDER BY last_id DESC
        ")->fetchAll();
        // Дополним именами пользователей
        $out = [];
        foreach ($rows as $r) {
            $name = 'Гость';
            if ($r['user_id']) {
                $u = User::findById((int)$r['user_id']);
                if ($u) $name = $u['name'];
            } else {
                $name = 'Гость ' . substr((string)$r['session_id'], 0, 6);
            }
            // Последнее сообщение
            $st = db()->prepare('SELECT text, author FROM chat_messages WHERE id = ?');
            $st->execute([$r['last_id']]);
            $last = $st->fetch();
            $out[] = [
                'user_id'    => $r['user_id'],
                'session_id' => $r['session_id'],
                'name'       => $name,
                'unread'     => (int)$r['unread'],
                'last_text'  => $last['text'] ?? '',
                'last_author' => $last['author'] ?? '',
                'last_at'    => $r['last_at'],
            ];
        }
        return $out;
    }

    public static function threadMessages(?int $userId, ?string $sessionId): array
    {
        if ($userId) {
            $stmt = db()->prepare('SELECT * FROM chat_messages WHERE user_id = ? ORDER BY id ASC');
            $stmt->execute([$userId]);
        } else {
            $stmt = db()->prepare('SELECT * FROM chat_messages WHERE session_id = ? ORDER BY id ASC');
            $stmt->execute([$sessionId]);
        }
        return $stmt->fetchAll();
    }

    public static function markRead(?int $userId, ?string $sessionId): void
    {
        if ($userId) {
            db()->prepare('UPDATE chat_messages SET is_read_by_admin = 1 WHERE user_id = ?')->execute([$userId]);
        } else {
            db()->prepare('UPDATE chat_messages SET is_read_by_admin = 1 WHERE session_id = ?')->execute([$sessionId]);
        }
    }

    public static function unreadTotal(): int
    {
        return (int)db()->query('SELECT COUNT(*) AS n FROM chat_messages WHERE is_read_by_admin = 0')->fetch()['n'];
    }
}
