<section class="container" style="padding-top: var(--space-6);">
    <h1 class="catalog-title" style="margin-bottom: var(--space-6);"><?= t('cart') ?></h1>

    <?php if (empty($items)): ?>
        <div class="empty-state">
            <div class="empty-icon">
                <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M3 3h2l2.4 12.2a2 2 0 0 0 2 1.6h9.7a2 2 0 0 0 2-1.5L23 7H6" />
                    <circle cx="9" cy="21" r="1.5" />
                    <circle cx="18" cy="21" r="1.5" />
                </svg>
            </div>
            <h2 class="empty-title"><?= t('empty_cart') ?></h2>
            <p class="empty-text">Добавьте товары из каталога, чтобы оформить заказ</p>
            <a href="/catalog" class="btn btn-primary"><?= t('go_shopping') ?></a>
        </div>
    <?php else: ?>
        <?php $subtotal = $total;
        $delivery = $total >= 5000 ? 0 : 490;
        $grand = $subtotal + $delivery; ?>
        <div class="cart-layout">
            <div class="cart-list">
                <?php foreach ($items as $it): ?>
                    <article class="cart-item">
                        <a href="/product/<?= (int)$it['id'] ?>" class="cart-item-img">
                            <img src="/img/product/<?= escape($it['image']) ?>.svg" alt="">
                        </a>
                        <div class="cart-item-info">
                            <span class="cart-item-brand"><?= escape($it['brand']) ?></span>
                            <a href="/product/<?= (int)$it['id'] ?>" class="cart-item-name"><?= escape($it['name']) ?></a>
                            <div class="cart-item-controls">
                                <div class="qty-stepper" data-item-id="<?= (int)$it['cart_item_id'] ?>">
                                    <button class="qty-btn" data-qty-step="-1" aria-label="−">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                            <line x1="5" y1="12" x2="19" y2="12" />
                                        </svg>
                                    </button>
                                    <span class="qty-val"><?= (int)$it['quantity'] ?></span>
                                    <button class="qty-btn" data-qty-step="1" aria-label="+">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                            <line x1="12" y1="5" x2="12" y2="19" />
                                            <line x1="5" y1="12" x2="19" y2="12" />
                                        </svg>
                                    </button>
                                </div>
                                <span class="cart-item-price"><?= format_price($it['price'] * $it['quantity']) ?></span>
                                <button class="cart-item-remove" data-remove-item="<?= (int)$it['cart_item_id'] ?>"><?= t('remove') ?></button>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>

            <aside class="cart-summary">
                <h2 class="summary-title">Итоги заказа</h2>
                <div class="summary-row">
                    <span>Товары</span>
                    <span data-cart-subtotal><?= format_price($subtotal) ?></span>
                </div>
                <div class="summary-row">
                    <span>Доставка</span>
                    <span><?= $delivery === 0 ? '<span style="color: var(--success)">Бесплатно</span>' : format_price($delivery) ?></span>
                </div>
                <div class="summary-row total">
                    <span><?= t('total') ?></span>
                    <span class="summary-val" data-cart-total><?= format_price($grand) ?></span>
                </div>
                <a href="/checkout" class="btn btn-primary btn-block btn-lg" style="margin-top: var(--space-4);">
                    <?= t('checkout') ?>
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="5" y1="12" x2="19" y2="12" />
                        <polyline points="12 5 19 12 12 19" />
                    </svg>
                </a>
            </aside>
        </div>
    <?php endif; ?>
</section>