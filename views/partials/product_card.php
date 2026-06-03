<?php
// $p — товар
$hasDiscount = !empty($p['old_price']) && $p['old_price'] > $p['price'];
$discountPct = $hasDiscount ? round((1 - $p['price'] / $p['old_price']) * 100) : 0;
?>
<article class="product-card reveal">
    <a href="/product/<?= (int)$p['id'] ?>" class="product-card-link" aria-label="<?= escape($p['name']) ?>"></a>
    <div class="product-badges">
        <?php if ($p['is_hot']): ?><span class="badge badge-hot"><?= t('hot') ?></span><?php endif; ?>
        <?php if ($p['is_new']): ?><span class="badge badge-new"><?= t('new') ?></span><?php endif; ?>
        <?php if ($hasDiscount): ?><span class="badge badge-discount">−<?= $discountPct ?>%</span><?php endif; ?>
    </div>
    <a href="/product/<?= (int)$p['id'] ?>" class="product-img-wrap" aria-hidden="true" tabindex="-1">
        <img src="/img/product/<?= escape($p['image']) ?>.svg" alt="" loading="lazy">
    </a>
    <div class="product-body">
        <span class="product-brand"><?= escape($p['brand']) ?></span>
        <a href="/product/<?= (int)$p['id'] ?>" class="product-name"><?= escape($p['name']) ?></a>
        <?php if ($p['rating'] > 0): ?>
            <span class="product-rating">
                <svg class="icon-star" viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                <span class="product-rating-value"><?= number_format($p['rating'], 1) ?></span>
                <span>(<?= $p['reviews_count'] ?>)</span>
            </span>
        <?php endif; ?>
        <div class="product-meta">
            <div class="product-prices">
                <span class="product-price"><?= format_price($p['price']) ?></span>
                <?php if ($hasDiscount): ?>
                    <span class="product-old-price"><?= format_price($p['old_price']) ?></span>
                <?php endif; ?>
            </div>
            <button class="product-add-btn" data-add-cart="<?= (int)$p['id'] ?>" aria-label="<?= t('add_to_cart') ?>">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            </button>
        </div>
    </div>
</article>
