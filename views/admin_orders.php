<?php
$orders = Order::all();
$statusLabels = [
    'processing' => 'В обработке',
    'shipping'   => 'Доставляется',
    'completed'  => 'Завершён',
    'cancelled'  => 'Отменён',
];
?>
<h1 class="admin-h1">Заказы</h1>
<div class="admin-block">
    <div class="admin-table-wrap">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>№</th>
                    <th>Дата</th>
                    <th>Пользователь</th>
                    <th>Сумма</th>
                    <th>Доставка</th>
                    <th>Статус</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $o): ?>
                    <tr>
                        <td>#<?= (int)$o['id'] ?></td>
                        <td><?= e($o['created_at'] ?? '') ?></td>
                        <td><?= e($o['user_name'] ?? '') ?: ('user#' . (int)$o['user_id']) ?></td>
                        <td><?= number_format($o['total'], 0, '.', ' ') ?> ₽</td>
                        <td><?= e($o['delivery_type'] ?? '') ?></td>
                        <td>
                            <form method="post" action="/admin/orders" class="admin-inline-form">
                                <input type="hidden" name="id" value="<?= (int)$o['id'] ?>">
                                <select name="status" onchange="this.form.submit()">
                                    <?php foreach ($statusLabels as $k => $v): ?>
                                        <option value="<?= e($k) ?>" <?= ($o['status'] ?? '') === $k ? 'selected' : '' ?>>
                                            <?= e($v) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </form>
                        </td>
                        <td>
                            <a href="/orders/<?= (int)$o['id'] ?>" class="admin-link" target="_blank">Открыть</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
