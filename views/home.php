<?php
// $hotProducts, $popularProducts, $discountProducts, $newProducts, $categories, $lang
?>
<section class="hero">
    <div class="container">
        <div class="hero-inner">
            <div>
                <div class="hero-eyebrow reveal">
                    <span class="hero-eyebrow-dot"></span>
                    <?= t('hot_deals') ?> · Май 2026
                </div>
                <h1 class="hero-title reveal">
                    <?= t('home_hero_title') ?><br>
                    <span class="gradient">Kolendula.</span>
                </h1>
                <p class="hero-sub reveal"><?= t('home_hero_subtitle') ?></p>
                <div class="hero-actions reveal">
                    <a href="/catalog" class="btn btn-primary btn-lg">
                        <?= t('go_to_catalog') ?>
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                    </a>
                    <a href="/catalog?hot=1" class="btn btn-outline btn-lg">
                        <?= t('hot_deals') ?>
                    </a>
                </div>
                <div class="hero-stats reveal">
                    <div>
                        <div class="hero-stat-num">10K+</div>
                        <div class="hero-stat-label">Товаров</div>
                    </div>
                    <div>
                        <div class="hero-stat-num">98%</div>
                        <div class="hero-stat-label">Довольных клиентов</div>
                    </div>
                    <div>
                        <div class="hero-stat-num">24/7</div>
                        <div class="hero-stat-label">Поддержка</div>
                    </div>
                </div>
            </div>
            <div class="hero-visual reveal">
                <div class="hero-orb hero-orb-1"></div>
                <div class="hero-orb hero-orb-2"></div>
                <div class="hero-card hero-card-1">
                    <div class="hero-card-icon">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                    </div>
                    <div class="hero-card-info">
                        <div class="hero-card-title"><?= t('free_shipping') ?></div>
                        <div class="hero-card-sub">По всей России</div>
                    </div>
                </div>
                <div class="hero-card hero-card-2">
                    <div class="hero-card-icon">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2L3 7v6c0 5 4 9 9 11 5-2 9-6 9-11V7l-9-5z"/></svg>
                    </div>
                    <div class="hero-card-info">
                        <div class="hero-card-title"><?= t('warranty') ?></div>
                        <div class="hero-card-sub">Официальная</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="features reveal">
            <div class="feature">
                <div class="feature-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg></div>
                <div class="feature-text"><?= t('free_shipping') ?></div>
            </div>
            <div class="feature">
                <div class="feature-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2L3 7v6c0 5 4 9 9 11 5-2 9-6 9-11V7l-9-5z"/></svg></div>
                <div class="feature-text"><?= t('warranty') ?></div>
            </div>
            <div class="feature">
                <div class="feature-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/></svg></div>
                <div class="feature-text"><?= t('support_247') ?></div>
            </div>
        </div>
    </div>
</section>

<!-- Categories strip -->
<section class="section">
    <div class="container">
        <div class="section-head reveal">
            <h2 class="section-title"><?= t('categories') ?></h2>
            <a href="/catalog" class="section-link"><?= t('view_all') ?> →</a>
        </div>
        <div class="cat-strip reveal">
            <?php foreach ($categories as $cat): ?>
                <a href="/category/<?= escape($cat['slug']) ?>" class="cat-tile">
                    <span class="cat-tile-icon"><?= $cat['icon'] ?></span>
                    <span class="cat-tile-name"><?= escape($lang === 'en' ? $cat['name_en'] : $cat['name_ru']) ?></span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Ad banners -->
<section class="section" style="padding-top: 0">
    <div class="container">
        <div class="ad-grid">
            <a href="/category/laptops" class="ad-card ad-card-primary reveal">
                <div>
                    <div class="ad-eyebrow">Новинка месяца</div>
                    <h3 class="ad-title">MacBook Pro M4 Pro</h3>
                    <ul class="ad-features-list">
                        <li>✓ Чип Apple M4 Pro</li>
                        <li>✓ Дисплей Liquid Retina XDR</li>
                        <li>✓ До 22 часов работы</li>
                    </ul>
                </div>
                <span class="ad-cta">Подробнее →</span>
            </a>
            <a href="/category/headphones" class="ad-card ad-card-accent reveal">
                <div>
                    <div class="ad-eyebrow"><?= t('hot') ?></div>
                    <h3 class="ad-title">Sony WH-1000XM5</h3>
                    <ul class="ad-features-list">
                        <li>✓ Лучшее шумоподавление</li>
                        <li>✓ 30 часов автономности</li>
                    </ul>
                </div>
                <span class="ad-cta">Выбрать →</span>
            </a>
        </div>
    </div>
</section>

<!-- Hot deals -->
<section class="section">
    <div class="container">
        <div class="section-head reveal">
            <h2 class="section-title">🔥 <?= t('hot_deals') ?></h2>
            <a href="/catalog?hot=1" class="section-link"><?= t('view_all') ?> →</a>
        </div>
        <div class="grid-products">
            <?php foreach ($hotProducts as $p): ?>
                <?php require __DIR__ . '/partials/product_card.php'; ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Discounts -->
<?php if (!empty($discountProducts)): ?>
<section class="section">
    <div class="container">
        <div class="section-head reveal">
            <h2 class="section-title">💸 <?= t('discount') ?></h2>
            <a href="/catalog?discount=1" class="section-link"><?= t('view_all') ?> →</a>
        </div>
        <div class="grid-products">
            <?php foreach ($discountProducts as $p): ?>
                <?php require __DIR__ . '/partials/product_card.php'; ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Popular -->
<section class="section">
    <div class="container">
        <div class="section-head reveal">
            <h2 class="section-title">⭐ <?= t('popular') ?></h2>
            <a href="/catalog" class="section-link"><?= t('view_all') ?> →</a>
        </div>
        <div class="grid-products">
            <?php foreach ($popularProducts as $p): ?>
                <?php require __DIR__ . '/partials/product_card.php'; ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- New arrivals -->
<?php if (!empty($newProducts)): ?>
<section class="section">
    <div class="container">
        <div class="section-head reveal">
            <h2 class="section-title">✨ <?= t('new_arrivals') ?></h2>
            <a href="/catalog?new=1" class="section-link"><?= t('view_all') ?> →</a>
        </div>
        <div class="grid-products">
            <?php foreach ($newProducts as $p): ?>
                <?php require __DIR__ . '/partials/product_card.php'; ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>
