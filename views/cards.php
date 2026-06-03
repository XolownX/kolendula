<section class="container" style="padding-top: var(--space-6);">
    <a href="/profile" class="link-back">← Назад в профиль</a>
    <h1 class="catalog-title" style="margin-bottom: var(--space-5);">Мои карты</h1>

    <?php if (empty($cards)): ?>
        <div class="empty-state">
            <div class="empty-icon">💳</div>
            <h2 class="empty-title">Нет сохранённых карт</h2>
            <p class="empty-text">Карта сохранится автоматически при первом успешном заказе</p>
            <a href="/catalog" class="btn btn-primary">В каталог</a>
        </div>
    <?php else: ?>
        <div class="cards-list">
            <?php foreach ($cards as $c): ?>
                <div class="saved-card-row">
                    <div class="card-visual card-visual-<?= strtolower(escape($c['brand'])) ?>">
                        <div class="card-brand"><?= escape($c['brand']) ?></div>
                        <div class="card-num">•••• •••• •••• <?= escape($c['last4']) ?></div>
                        <div class="card-meta">
                            <span><?= escape($c['holder'] ?? 'CARDHOLDER') ?></span>
                            <?php if ($c['exp_month'] && $c['exp_year']): ?>
                                <span><?= str_pad((string)$c['exp_month'], 2, '0', STR_PAD_LEFT) ?>/<?= $c['exp_year'] ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <form method="post" action="/profile/cards" onsubmit="return confirm('Удалить карту?')">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" value="<?= (int)$c['id'] ?>">
                        <button class="btn btn-ghost btn-sm" type="submit">Удалить</button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>
