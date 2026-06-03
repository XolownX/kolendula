<?php
$hasDiscount = !empty($product['old_price']) && $product['old_price'] > $product['price'];
$discountPct = $hasDiscount ? round((1 - $product['price'] / $product['old_price']) * 100) : 0;
$rating = (float)$product['rating'];
?>
<section class="container" style="padding-top: var(--space-6);">
    <div class="breadcrumbs">
        <a href="/"><?= t('home') ?></a>
        <span>›</span>
        <a href="/category/<?= escape($product['category_slug']) ?>"><?= escape($product['category_name']) ?></a>
        <span>›</span>
        <span><?= escape($product['name']) ?></span>
    </div>

    <div class="product-page">
        <div class="product-gallery">
            <img src="/img/product/<?= escape($product['image']) ?>.svg" alt="<?= escape($product['name']) ?>">
        </div>

        <div class="product-info">
            <div class="product-page-brand"><?= escape($product['brand']) ?></div>
            <h1 class="product-page-title"><?= escape($product['name']) ?></h1>

            <div class="rating-row">
                <span class="stars">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <svg class="icon-star <?= $i > round($rating) ? 'star-empty' : '' ?>" viewBox="0 0 24 24">
                            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                        </svg>
                    <?php endfor; ?>
                </span>
                <span><strong style="color: var(--text)"><?= number_format($rating, 1) ?></strong> · <?= $product['reviews_count'] ?> <?= t('reviews') ?></span>
            </div>

            <div class="price-block">
                <span class="price-current"><?= format_price($product['price']) ?></span>
                <?php if ($hasDiscount): ?>
                    <span class="price-old"><?= format_price($product['old_price']) ?></span>
                    <span class="price-discount">−<?= $discountPct ?>%</span>
                <?php endif; ?>
            </div>

            <div class="product-buy-row">
                <button class="btn btn-primary btn-lg" data-add-cart="<?= (int)$product['id'] ?>" style="flex: 1;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 3h2l2.4 12.2a2 2 0 0 0 2 1.6h9.7a2 2 0 0 0 2-1.5L23 7H6"/><circle cx="9" cy="21" r="1.5"/><circle cx="18" cy="21" r="1.5"/></svg>
                    <?= t('add_to_cart') ?>
                </button>
            </div>

            <?php if (!empty($product['description'])): ?>
                <div class="product-section">
                    <h3><?= t('description') ?></h3>
                    <p style="color: var(--text-muted); line-height: 1.7;"><?= escape($product['description']) ?></p>
                </div>
            <?php endif; ?>

            <?php if (!empty($specs)): ?>
                <div class="product-section">
                    <h3><?= t('specs') ?></h3>
                    <div class="spec-list">
                        <?php foreach ($specs as $k => $v): ?>
                            <div class="spec-item">
                                <span class="spec-key"><?= escape((string)$k) ?></span>
                                <span class="spec-val"><?= escape((string)$v) ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <div class="product-section" id="reviews">
                <h3><?= t('reviews') ?> (<?= count($reviews) ?>)</h3>

                <?php if (!empty($reviewAdded)): ?>
                    <div class="success-msg" style="margin-bottom: var(--space-3);">Спасибо за отзыв!</div>
                <?php endif; ?>
                <?php if (!empty($reviewError)): ?>
                    <div class="error-msg" style="margin-bottom: var(--space-3);"><?= escape($reviewError) ?></div>
                <?php endif; ?>

                <?php if ($canReview): ?>
                    <details class="review-form-wrap" style="margin-bottom: var(--space-4);">
                        <summary class="btn btn-ghost btn-sm" style="display: inline-block; cursor: pointer;">✨ Оставить отзыв</summary>
                        <form method="post" action="/product/<?= (int)$product['id'] ?>" class="review-form" style="margin-top: var(--space-3); padding: var(--space-3); background: var(--bg-alt); border-radius: 12px;">
                            <input type="hidden" name="action" value="review">
                            <div class="field">
                                <label>Оценка</label>
                                <div class="rating-input" id="rating-input" data-value="5">
                                    <?php for ($i=1;$i<=5;$i++): ?>
                                        <button type="button" class="rating-star" data-val="<?= $i ?>" aria-label="<?= $i ?> звёзд">★</button>
                                    <?php endfor; ?>
                                </div>
                                <input type="hidden" name="rating" value="5" id="rating-value">
                            </div>
                            <div class="field">
                                <label for="review-text">Ваш отзыв</label>
                                <textarea class="input" name="text" id="review-text" rows="3" required></textarea>
                            </div>
                            <button class="btn btn-primary btn-sm" type="submit">Отправить отзыв</button>
                        </form>
                    </details>
                    <script>
                    (function(){
                        const stars = document.querySelectorAll('#rating-input .rating-star');
                        const out = document.getElementById('rating-value');
                        const wrap = document.getElementById('rating-input');
                        function paint(v){ stars.forEach(s => s.classList.toggle('active', parseInt(s.dataset.val,10) <= v)); }
                        stars.forEach(s => s.addEventListener('click', () => { const v=parseInt(s.dataset.val,10); out.value=v; wrap.dataset.value=v; paint(v); }));
                        paint(5);
                    })();
                    </script>
                <?php elseif ($currentUserId): ?>
                    <p class="muted" style="font-size: 0.9rem;">Для отзыва нужно купить этот товар.</p>
                <?php else: ?>
                    <p class="muted" style="font-size: 0.9rem;"><a href="/login" class="link-muted">Войдите</a>, чтобы оставить отзыв.</p>
                <?php endif; ?>

                <?php if (empty($reviews)): ?>
                    <p style="color: var(--text-muted)">Пока нет отзывов</p>
                <?php else: ?>
                    <div class="reviews-list">
                        <?php foreach ($reviews as $r): ?>
                            <div class="review">
                                <div class="avatar avatar-md"><span><?= mb_strtoupper(mb_substr($r['author_name'], 0, 1)) ?></span></div>
                                <div class="review-body">
                                    <div class="review-head">
                                        <span class="review-author"><?= escape($r['author_name']) ?></span>
                                        <span class="review-date"><?= date('d.m.Y', strtotime($r['created_at'])) ?></span>
                                    </div>
                                    <div class="review-stars">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <svg class="icon-star <?= $i > $r['rating'] ? 'star-empty' : '' ?>" viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                                        <?php endfor; ?>
                                    </div>
                                    <p class="review-text"><?= escape($r['text']) ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php if (!empty($related)): ?>
        <section class="section">
            <div class="section-head">
                <h2 class="section-title"><?= t('related_products') ?></h2>
            </div>
            <div class="grid-products">
                <?php foreach ($related as $p): ?>
                    <?php require __DIR__ . '/partials/product_card.php'; ?>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endif; ?>
</section>
