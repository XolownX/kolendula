<section class="container" style="padding-top: var(--space-6);">
    <?php if (!empty($showSuccess)): ?>
        <div class="success-banner">
            <div class="success-banner-icon">✓</div>
            <div>
                <div class="success-banner-title">Успешно! Спасибо за покупку!</div>
                <div class="success-banner-text">Заказ оформлен и передан в обработку. Мы свяжемся с вами по указанным контактам.</div>
            </div>
        </div>
    <?php endif; ?>

    <a href="/orders" class="link-back">← К списку заказов</a>
    <h1 class="catalog-title" style="margin-bottom: var(--space-3);">Заказ №<?= (int)$order['id'] ?></h1>
    <div class="muted" style="margin-bottom: var(--space-5);"><?= escape($order['created_at']) ?></div>

    <div class="order-grid">
        <div>
            <div class="checkout-block">
                <h2 class="checkout-block-title">Товары</h2>
                <?php foreach ($orderItems as $i): ?>
                    <div class="order-item-row">
                        <a href="/product/<?= (int)$i['product_id'] ?>" class="order-item-info">
                            <img src="/img/product/<?= escape($i['image']) ?>.svg" alt="" class="order-item-img">
                            <div>
                                <div class="order-item-name"><?= escape($i['name']) ?></div>
                                <div class="muted" style="font-size: 0.85rem;"><?= escape($i['brand']) ?></div>
                            </div>
                        </a>
                        <div class="order-item-qty">× <?= (int)$i['quantity'] ?></div>
                        <div class="order-item-price"><?= format_price((int)$i['price'] * (int)$i['quantity']) ?></div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="checkout-block">
                <h2 class="checkout-block-title">Доставка</h2>
                <div><?= escape($order['delivery_address'] ?: '—') ?></div>
            </div>
        </div>

        <aside>
            <div class="checkout-summary">
                <div class="summary-row"><span>Статус</span><strong><?php
                    $stMap = ['processing'=>'В обработке','shipping'=>'В пути','completed'=>'Доставлен','cancelled'=>'Отменён'];
                    echo escape($stMap[$order['status']] ?? $order['status']);
                ?></strong></div>
                <div class="summary-row"><span>Оплата</span><strong><?= escape(($order['card_brand'] ?? 'Карта')) ?> •••• <?= escape($order['card_last4'] ?? '0000') ?></strong></div>
                <div class="summary-divider"></div>
                <div class="summary-row"><span>Товаров</span><span><?= format_price((int)$order['total'] - (int)$order['delivery_fee']) ?></span></div>
                <div class="summary-row"><span>Доставка</span><span><?= (int)$order['delivery_fee'] ? format_price((int)$order['delivery_fee']) : 'Бесплатно' ?></span></div>
                <div class="summary-divider"></div>
                <div class="summary-row summary-total"><span>Итого</span><span><?= format_price((int)$order['total']) ?></span></div>
                <a href="/catalog" class="btn btn-ghost btn-block" style="margin-top: var(--space-4);">Продолжить покупки</a>
            </div>
        </aside>
    </div>
</section>
