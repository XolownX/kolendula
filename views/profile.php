<section class="container" style="padding-top: var(--space-6);">
    <h1 class="catalog-title" style="margin-bottom: var(--space-6);"><?= t('profile') ?></h1>

    <?php if (flash_get('profile_saved')): ?>
        <div class="success-msg" style="margin-bottom: var(--space-4);">Изменения сохранены</div>
    <?php endif; ?>
    <?php if (!empty($error)): ?>
        <div class="error-msg" style="margin-bottom: var(--space-4);"><?= escape($error) ?></div>
    <?php endif; ?>

    <div class="profile-layout">
        <aside class="profile-side">
            <a href="/profile" class="active">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                <?= t('profile') ?>
            </a>
            <a href="/orders">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
                Мои заказы
            </a>
            <a href="/profile/cards">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
                Мои карты
            </a>
            <a href="/settings">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 1 1-4 0v-.09a1.65 1.65 0 0 0-1-1.51 1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 1 1 0-4h.09a1.65 1.65 0 0 0 1.51-1 1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33h.01a1.65 1.65 0 0 0 1-1.51V3a2 2 0 1 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82v.01a1.65 1.65 0 0 0 1.51 1H21a2 2 0 1 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
                <?= t('settings') ?>
            </a>
            <a href="/cart">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 3h2l2.4 12.2a2 2 0 0 0 2 1.6h9.7a2 2 0 0 0 2-1.5L23 7H6"/><circle cx="9" cy="21" r="1.5"/><circle cx="18" cy="21" r="1.5"/></svg>
                <?= t('cart') ?>
            </a>
            <?php if (is_admin()): ?>
            <a href="/admin">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2 4 5v6c0 5 3.4 9.4 8 11 4.6-1.6 8-6 8-11V5z"/></svg>
                Админ-панель
            </a>
            <?php endif; ?>
            <a href="/logout">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                <?= t('logout') ?>
            </a>
        </aside>

        <div class="profile-card">
            <form class="form" method="post" action="/profile" id="profile-form" enctype="application/x-www-form-urlencoded">
                <input type="hidden" name="avatar_data" id="avatar-data">

                <div class="profile-head">
                    <div class="avatar avatar-lg" id="avatar-display">
                        <?php if (!empty($user['avatar'])): ?>
                            <img src="<?= escape($user['avatar']) ?>" alt="" id="avatar-img">
                        <?php else: ?>
                            <span><?= mb_strtoupper(mb_substr($user['name'], 0, 1)) ?></span>
                        <?php endif; ?>
                    </div>
                    <div>
                        <div class="profile-name"><?= escape($user['name']) ?></div>
                        <div class="profile-email"><?= escape($user['email']) ?></div>
                        <label for="avatar-input" class="btn btn-ghost btn-sm" style="margin-top: var(--space-2); cursor: pointer;">Сменить фото</label>
                        <input type="file" id="avatar-input" accept="image/*" hidden>
                    </div>
                </div>

                <div class="field">
                    <label for="name"><?= t('name') ?></label>
                    <input class="input" type="text" id="name" name="name" required value="<?= escape($user['name']) ?>">
                </div>
                <div class="field">
                    <label for="phone"><?= t('phone') ?></label>
                    <input class="input" type="tel" id="phone" name="phone" value="<?= escape($user['phone'] ?? '') ?>">
                </div>
                <div class="field">
                    <label for="email"><?= t('email') ?></label>
                    <input class="input" type="email" id="email" name="email" required value="<?= escape($user['email']) ?>">
                </div>
                <h3 style="margin-top: var(--space-6); margin-bottom: var(--space-2); font-size: 1rem;">Смена пароля</h3>
                <div class="field">
                    <label for="password_old">Старый пароль</label>
                    <input class="input" type="password" id="password_old" name="password_old" autocomplete="current-password">
                </div>
                <div class="field">
                    <label for="password">Новый пароль</label>
                    <input class="input" type="password" id="password" name="password" minlength="6" autocomplete="new-password">
                </div>
                <div class="field">
                    <label for="password_confirm">Повторите новый пароль</label>
                    <input class="input" type="password" id="password_confirm" name="password_confirm" minlength="6" autocomplete="new-password">
                </div>
                <div style="display:flex; gap: var(--space-3); align-items: center;">
                    <button type="submit" class="btn btn-primary"><?= t('save') ?></button>
                    <a href="/forgot" class="link-muted" style="font-size: 0.9rem;">Забыли пароль?</a>
                </div>
            </form>
        </div>
    </div>
</section>

<script>
(function(){
    const input = document.getElementById('avatar-input');
    const display = document.getElementById('avatar-display');
    const hidden = document.getElementById('avatar-data');
    if (!input) return;
    input.addEventListener('change', function(){
        const file = this.files && this.files[0];
        if (!file) return;
        // Сжимаем картинку до 400x400 jpeg ~80% — чтобы dataURL точно влез в POST
        const reader = new FileReader();
        reader.onload = function(e){
            const img = new Image();
            img.onload = function(){
                const max = 400;
                const ratio = Math.min(max / img.width, max / img.height, 1);
                const w = Math.round(img.width * ratio);
                const h = Math.round(img.height * ratio);
                const canvas = document.createElement('canvas');
                canvas.width = w; canvas.height = h;
                const ctx = canvas.getContext('2d');
                ctx.fillStyle = '#fff';
                ctx.fillRect(0, 0, w, h);
                ctx.drawImage(img, 0, 0, w, h);
                const dataUrl = canvas.toDataURL('image/jpeg', 0.85);
                display.innerHTML = '<img src="'+dataUrl+'" alt="">';
                hidden.value = dataUrl;
            };
            img.src = e.target.result;
        };
        reader.readAsDataURL(file);
    });
})();
</script>
