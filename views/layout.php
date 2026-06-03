<?php
$user = current_user();
$theme = get_setting('theme', 'auto');
$lang = get_setting('language', 'ru');
$region = get_setting('region', 'RU');
$cartCount = cart_count();
$categories = Category::all();
?><!DOCTYPE html>
<html lang="<?= escape($lang) ?>" data-theme="<?= escape($theme) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="theme-color" content="#0a0a0b">
    <title><?= isset($pageTitle) ? escape($pageTitle) . ' — ' : '' ?>Kolendula</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/app.css?v=10">
    <link rel="icon" type="image/svg+xml" href="/assets/img/logo.svg">
    <script>document.documentElement.classList.add('js');</script>
</head>
<body>

<!-- Desktop header -->
<header class="header" id="header">
    <div class="container header-inner">
        <a href="/" class="logo" aria-label="Kolendula">
            <svg class="logo-mark" viewBox="0 0 40 40" fill="none" aria-hidden="true">
                <path d="M20 4 L34 12 V28 L20 36 L6 28 V12 Z" stroke="currentColor" stroke-width="2.2" stroke-linejoin="round"/>
                <circle cx="20" cy="20" r="5.5" fill="currentColor"/>
            </svg>
            <span class="logo-text">Kolendula</span>
        </a>

        <nav class="nav-desktop">
            <button class="nav-link nav-dropdown-trigger" data-dropdown="categories">
                <?= t('catalog') ?>
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
            </button>
            <div class="nav-dropdown" data-dropdown-menu="categories">
                <div class="dropdown-grid">
                    <?php foreach ($categories as $cat): ?>
                        <a href="/category/<?= escape($cat['slug']) ?>" class="dropdown-cat">
                            <span class="dropdown-cat-icon"><?= $cat['icon'] ?></span>
                            <span><?= escape($lang === 'en' ? $cat['name_en'] : $cat['name_ru']) ?></span>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
            <a href="/catalog?hot=1" class="nav-link"><?= t('hot_deals') ?></a>
            <a href="/catalog?new=1" class="nav-link"><?= t('new_arrivals') ?></a>
        </nav>

        <form class="search-form" action="/search" method="get" role="search">
            <svg class="search-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3"/></svg>
            <input type="search" name="q" class="search-input" placeholder="<?= t('search') ?>" value="<?= escape($_GET['q'] ?? '') ?>" autocomplete="off">
            <div class="search-suggest" id="search-suggest" hidden></div>
        </form>

        <div class="header-actions">
            <a href="/cart" class="icon-btn cart-btn" aria-label="<?= t('cart') ?>">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 3h2l2.4 12.2a2 2 0 0 0 2 1.6h9.7a2 2 0 0 0 2-1.5L23 7H6"/><circle cx="9" cy="21" r="1.5"/><circle cx="18" cy="21" r="1.5"/></svg>
                <span class="cart-badge" data-cart-count<?= $cartCount ? '' : ' hidden' ?>><?= $cartCount ?></span>
            </a>
            <button class="icon-btn burger-btn" id="burger-btn" aria-label="<?= t('menu') ?>" aria-expanded="false">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
            </button>
        </div>
    </div>
    <!-- Mobile search row (visible only on mobile) -->
    <div class="mobile-search-row">
        <form class="search-form search-form-mobile" action="/search" method="get" role="search">
            <svg class="search-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3"/></svg>
            <input type="search" name="q" class="search-input" placeholder="<?= t('search') ?>" value="<?= escape($_GET['q'] ?? '') ?>" autocomplete="off">
        </form>
    </div>
</header>

<!-- Burger panel (slides from right) -->
<div class="burger-overlay" id="burger-overlay" hidden></div>
<aside class="burger-panel" id="burger-panel" hidden aria-hidden="true">
    <div class="burger-header">
        <span class="burger-title"><?= t('menu') ?></span>
        <button class="icon-btn" id="burger-close" aria-label="<?= t('close') ?>">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        </button>
    </div>

    <?php if ($user): ?>
        <a href="/profile" class="burger-user">
            <div class="avatar avatar-md">
                <?php if (!empty($user['avatar'])): ?>
                    <img src="<?= escape($user['avatar']) ?>" alt="">
                <?php else: ?>
                    <span><?= mb_strtoupper(mb_substr($user['name'], 0, 1)) ?></span>
                <?php endif; ?>
            </div>
            <div class="burger-user-info">
                <div class="burger-user-name"><?= escape($user['name']) ?></div>
                <div class="burger-user-email"><?= escape($user['email']) ?></div>
            </div>
        </a>
    <?php else: ?>
        <div class="burger-auth">
            <a href="/login" class="btn btn-primary btn-block"><?= t('login') ?></a>
            <a href="/register" class="btn btn-ghost btn-block"><?= t('register') ?></a>
        </div>
    <?php endif; ?>

    <nav class="burger-nav">
        <a href="/profile" class="burger-item">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            <span><?= t('profile') ?></span>
        </a>
        <a href="/orders" class="burger-item">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
            <span>Мои заказы</span>
        </a>
        <?php if (is_admin()): ?>
        <a href="/admin" class="burger-item">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2 4 5v6c0 5 3.4 9.4 8 11 4.6-1.6 8-6 8-11V5z"/></svg>
            <span>Админ-панель</span>
        </a>
        <?php endif; ?>
        <a href="/settings" class="burger-item">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 1 1-4 0v-.09a1.65 1.65 0 0 0-1-1.51 1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 1 1 0-4h.09a1.65 1.65 0 0 0 1.51-1 1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33h.01a1.65 1.65 0 0 0 1-1.51V3a2 2 0 1 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82v.01a1.65 1.65 0 0 0 1.51 1H21a2 2 0 1 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
            <span><?= t('settings') ?></span>
        </a>

        <div class="burger-divider"></div>

        <div class="theme-toggle-row">
            <span class="theme-toggle-label">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="5"/><path d="M12 1v2M12 21v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42M1 12h2M21 12h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42"/></svg>
                <?= t('theme') ?>
            </span>
            <div class="theme-switch" role="group" aria-label="<?= t('theme') ?>">
                <button class="theme-opt" data-theme-set="light" title="<?= t('theme_light') ?>">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="5"/><path d="M12 1v2M12 21v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42M1 12h2M21 12h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42"/></svg>
                </button>
                <button class="theme-opt" data-theme-set="dark" title="<?= t('theme_dark') ?>">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
                </button>
                <button class="theme-opt" data-theme-set="auto" title="<?= t('theme_auto') ?>">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/></svg>
                </button>
            </div>
        </div>

        <?php if ($user): ?>
            <a href="/logout" class="burger-item burger-item-danger">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                <span><?= t('logout') ?></span>
            </a>
        <?php endif; ?>
    </nav>
</aside>

<main class="main">
    <?php
    if (isset($contentFile) && file_exists($contentFile)) {
        require $contentFile;
    }
    ?>
</main>

<footer class="footer">
    <div class="container footer-inner">
        <div class="footer-brand">
            <a href="/" class="logo logo-footer">
                <svg class="logo-mark" viewBox="0 0 40 40" fill="none"><path d="M20 4 L34 12 V28 L20 36 L6 28 V12 Z" stroke="currentColor" stroke-width="2.2" stroke-linejoin="round"/><circle cx="20" cy="20" r="5.5" fill="currentColor"/></svg>
                <span class="logo-text">Kolendula</span>
            </a>
            <p class="footer-tagline"><?= t('site_tagline') ?></p>
        </div>
        <div class="footer-cols">
            <div>
                <h4><?= t('categories') ?></h4>
                <ul>
                    <?php foreach (array_slice($categories, 0, 5) as $cat): ?>
                        <li><a href="/category/<?= escape($cat['slug']) ?>"><?= escape($lang === 'en' ? $cat['name_en'] : $cat['name_ru']) ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <div>
                <h4>Kolendula</h4>
                <ul>
                    <li><a href="/catalog"><?= t('catalog') ?></a></li>
                    <li><a href="/profile"><?= t('profile') ?></a></li>
                    <li><a href="/settings"><?= t('settings') ?></a></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            © <?= date('Y') ?> Kolendula. Все права защищены.
        </div>
    </div>
</footer>

<!-- Mobile bottom navigation -->
<nav class="bottom-nav" id="bottom-nav">
    <a href="/" class="bottom-nav-item" data-route="home">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 12l9-9 9 9"/><path d="M5 10v10a1 1 0 0 0 1 1h3v-6h6v6h3a1 1 0 0 0 1-1V10"/></svg>
        <span><?= t('home') ?></span>
    </a>
    <a href="/catalog" class="bottom-nav-item" data-route="catalog">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
        <span><?= t('catalog') ?></span>
    </a>
    <a href="/search" class="bottom-nav-item" data-route="search">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3"/></svg>
        <span><?= t('search') ?></span>
    </a>
    <a href="/cart" class="bottom-nav-item" data-route="cart">
        <span class="bottom-nav-icon-wrap">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 3h2l2.4 12.2a2 2 0 0 0 2 1.6h9.7a2 2 0 0 0 2-1.5L23 7H6"/><circle cx="9" cy="21" r="1.5"/><circle cx="18" cy="21" r="1.5"/></svg>
            <span class="bottom-nav-badge" data-cart-count<?= $cartCount ? '' : ' hidden' ?>><?= $cartCount ?></span>
        </span>
        <span><?= t('cart') ?></span>
    </a>
    <button class="bottom-nav-item" id="bottom-menu-btn">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><circle cx="12" cy="10" r="3"/><path d="M6.5 18.5a6 6 0 0 1 11 0"/></svg>
        <span><?= $user ? t('profile') : t('menu') ?></span>
    </button>
</nav>

<div class="toast-container" id="toast-container"></div>

<!-- Чат с поддержкой -->
<button class="chat-fab" id="chat-fab" aria-label="Чат с поддержкой">
    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/></svg>
</button>
<aside class="chat-panel" id="chat-panel" hidden>
    <div class="chat-head">
        <div>
            <div class="chat-title">Чат с поддержкой</div>
            <div class="chat-sub">Обычно отвечаем в течение 5 минут</div>
        </div>
        <button class="icon-btn" id="chat-close" aria-label="Закрыть">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        </button>
    </div>
    <div class="chat-body" id="chat-body"></div>
    <div class="chat-actions">
        <button class="btn btn-ghost btn-sm" id="chat-operator-btn">Связаться с живым оператором</button>
    </div>
    <form class="chat-input-row" id="chat-form">
        <input type="text" class="input" id="chat-input" placeholder="Сообщение..." autocomplete="off">
        <button type="submit" class="btn btn-primary btn-sm" aria-label="Отправить">➜</button>
    </form>
</aside>

<script>
    window.KOL = {
        lang: <?= json_encode($lang) ?>,
        cartCount: <?= (int)$cartCount ?>,
        loggedIn: <?= $user ? 'true' : 'false' ?>,
        t: {
            no_results: <?= json_encode(t('no_results')) ?>,
            added_to_cart: <?= json_encode($lang === 'en' ? 'Added to cart' : 'Добавлено в корзину') ?>,
        }
    };
</script>
<script src="/assets/js/app.js?v=10"></script>
<script src="/assets/js/chat.js?v=2"></script>
</body>
</html>
