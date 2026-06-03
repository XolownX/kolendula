<?php
$products = Product::all(['limit' => 500]);
$categories = Category::all();
$editId = isset($_GET['edit']) ? (int)$_GET['edit'] : 0;
$editProduct = null;
if ($editId) {
    $editProduct = Product::find($editId);
}
$isNew = isset($_GET['new']);
?>
<h1 class="admin-h1">Товары</h1>

<?php if ($editProduct || $isNew): ?>
    <div class="admin-block">
        <div class="admin-block-title"><?= $editProduct ? 'Редактировать товар' : 'Новый товар' ?></div>
        <form method="post" action="/admin/products" class="admin-form">
            <input type="hidden" name="action" value="save">
            <input type="hidden" name="id" value="<?= $editProduct['id'] ?? 0 ?>">
            <div class="admin-form-row">
                <label>Название
                    <input type="text" name="name" value="<?= e($editProduct['name'] ?? '') ?>" required>
                </label>
                <label>Бренд
                    <input type="text" name="brand" value="<?= e($editProduct['brand'] ?? '') ?>" required>
                </label>
            </div>
            <div class="admin-form-row">
                <label>Категория
                    <select name="category_id" required>
                        <?php foreach ($categories as $c): ?>
                            <option value="<?= (int)$c['id'] ?>" <?= isset($editProduct['category_id']) && (int)$editProduct['category_id'] === (int)$c['id'] ? 'selected' : '' ?>>
                                <?= e($c['name_ru']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <label>Иконка (slug)
                    <input type="text" name="image" value="<?= e($editProduct['image'] ?? 'pc_office') ?>">
                </label>
            </div>
            <div class="admin-form-row">
                <label>Цена, ₽
                    <input type="number" name="price" value="<?= (int)($editProduct['price'] ?? 0) ?>" min="0" required>
                </label>
                <label>Старая цена, ₽
                    <input type="number" name="old_price" value="<?= isset($editProduct['old_price']) && $editProduct['old_price'] !== null ? (int)$editProduct['old_price'] : '' ?>" min="0">
                </label>
            </div>
            <label>Описание
                <textarea name="description" rows="3"><?= e($editProduct['description'] ?? '') ?></textarea>
            </label>
            <div class="admin-form-row">
                <label class="admin-check">
                    <input type="checkbox" name="is_hot" value="1" <?= !empty($editProduct['is_hot']) ? 'checked' : '' ?>> Hot
                </label>
                <label class="admin-check">
                    <input type="checkbox" name="is_new" value="1" <?= !empty($editProduct['is_new']) ? 'checked' : '' ?>> New
                </label>
            </div>
            <div class="admin-form-actions">
                <button type="submit" class="btn btn-primary">Сохранить</button>
                <a href="/admin/products" class="btn btn-secondary">Отмена</a>
            </div>
        </form>
    </div>
<?php else: ?>
    <div class="admin-toolbar">
        <a href="/admin/products?new=1" class="btn btn-primary">+ Новый товар</a>
        <div class="admin-toolbar-info"><?= count($products) ?> позиций</div>
    </div>
<?php endif; ?>

<div class="admin-block">
    <div class="admin-table-wrap">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Название</th>
                    <th>Бренд</th>
                    <th>Цена</th>
                    <th>Рейтинг</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $p): ?>
                    <tr>
                        <td><?= (int)$p['id'] ?></td>
                        <td><?= e($p['name']) ?></td>
                        <td><?= e($p['brand']) ?></td>
                        <td><?= number_format($p['price'], 0, '.', ' ') ?> ₽</td>
                        <td><?= number_format((float)($p['rating'] ?? 0), 1) ?> (<?= (int)($p['reviews_count'] ?? 0) ?>)</td>
                        <td class="admin-table-actions">
                            <a href="/admin/products?edit=<?= (int)$p['id'] ?>" class="admin-link">Изменить</a>
                            <form method="post" action="/admin/products" style="display:inline" onsubmit="return confirm('Удалить товар?');">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
                                <button type="submit" class="admin-link admin-link-danger">Удалить</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
