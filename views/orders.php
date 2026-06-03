<section class="container" style="padding-top: var(--space-6);">
    <h1 class="catalog-title" style="margin-bottom: var(--space-5);">Мои заказы</h1>

    <?php if (empty($orders)): ?>
        <div class="empty-state">
            <div class="empty-icon">📦</div>
            <h2 class="empty-title">У вас пока нет заказов</h2>
            <p class="empty-text">Выберите товары в каталоге</p>
            <a href="/catalog" class="btn btn-primary">В каталог</a>
        </div>
    <?php else: ?>
        <div class="orders-list">
            <?php foreach ($orders as $o):
                $statusMap = [
                    'processing' => ['label' => 'В обработке', 'cls' => 'status-processing'],
                    'shipping'   => ['label' => 'В пути',      'cls' => 'status-shipping'],
                    'completed'  => ['label' => 'Доставлен',   'cls' => 'status-completed'],
                    'cancelled'  => ['label' => 'Отменён',     'cls' => 'status-cancelled'],
                ];
                $st = $statusMap[$o['status']] ?? ['label' => $o['status'], 'cls' => ''];
            ?>
                <a href="/orders/<?= (int)$o['id'] ?>" class="order-row">
                    <div>
                        <div class="order-row-id">Заказ №<?= (int)$o['id'] ?></div>
                        <div class="order-row-date"><?= escape($o['created_at']) ?></div>
                    </div>
                    <div class="order-row-meta">
                        <span class="order-status <?= $st['cls'] ?>"><?= escape($st['label']) ?></span>
                        <strong><?= format_price((int)$o['total']) ?></strong>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>
