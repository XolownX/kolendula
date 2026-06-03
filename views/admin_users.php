<?php
$users = User::all();
?>
<h1 class="admin-h1">Пользователи</h1>
<div class="admin-block">
    <div class="admin-table-wrap">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Логин</th>
                    <th>Email</th>
                    <th>Имя</th>
                    <th>Админ</th>
                    <th>Создан</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $u): ?>
                    <tr>
                        <td><?= (int)$u['id'] ?></td>
                        <td><?= e($u['login']) ?></td>
                        <td><?= e($u['email']) ?></td>
                        <td><?= e($u['name'] ?? '') ?></td>
                        <td><?= !empty($u['is_admin']) ? '<span class="admin-badge">админ</span>' : '—' ?></td>
                        <td><?= e($u['created_at'] ?? '') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
