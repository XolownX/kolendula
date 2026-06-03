<?php
$categories = Category::all();
$editId = isset($_GET['edit']) ? (int)$_GET['edit'] : 0;
$editCat = null;
foreach ($categories as $c) {
    if ((int)$c['id'] === $editId) { $editCat = $c; break; }
}
$isNew = isset($_GET['new']);
?>
<h1 class="admin-h1">Категории</h1>

<?php if ($editCat || $isNew): ?>
    <div class="admin-block">
        <div class="admin-block-title"><?= $editCat ? 'Редактировать категорию' : 'Новая категория' ?></div>
        <form method="post" action="/admin/categories" class="admin-form">
            <input type="hidden" name="action" value="save">
            <input type="hidden" name="id" value="<?= (int)($editCat['id'] ?? 0) ?>">
            <div class="admin-form-row">
                <label>Slug (латиница)
                    <input type="text" name="slug" value="<?= e($editCat['slug'] ?? '') ?>" required>
                </label>
                <label>Иконка (emoji)
                    <input type="text" name="icon" value="<?= e($editCat['icon'] ?? '📦') ?>">
                </label>
            </div>
            <div class="admin-form-row">
                <label>Название RU
                    <input type="text" name="name_ru" value="<?= e($editCat['name_ru'] ?? '') ?>" required>
                </label>
                <label>Название EN
                    <input type="text" name="name_en" value="<?= e($editCat['name_en'] ?? '') ?>" required>
                </label>
            </div>
            <div class="admin-form-actions">
                <button type="submit" class="btn btn-primary">Сохранить</button>
                <a href="/admin/categories" class="btn btn-secondary">Отмена</a>
            </div>
        </form>
    </div>
<?php else: ?>
    <div class="admin-toolbar">
        <a href="/admin/categories?new=1" class="btn btn-primary">+ Новая категория</a>
    </div>
<?php endif; ?>

<div class="admin-block">
    <div class="admin-table-wrap">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th></th>
                    <th>Slug</th>
                    <th>RU</th>
                    <th>EN</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($categories as $c): ?>
                    <tr>
                        <td><?= (int)$c['id'] ?></td>
                        <td><span style="font-size:1.4em"><?= e($c['icon']) ?></span></td>
                        <td><?= e($c['slug']) ?></td>
                        <td><?= e($c['name_ru']) ?></td>
                        <td><?= e($c['name_en']) ?></td>
                        <td class="admin-table-actions">
                            <a href="/admin/categories?edit=<?= (int)$c['id'] ?>" class="admin-link">Изменить</a>
                            <form method="post" action="/admin/categories" style="display:inline" onsubmit="return confirm('Удалить категорию?');">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= (int)$c['id'] ?>">
                                <button type="submit" class="admin-link admin-link-danger">Удалить</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
