<?php
$theme = get_setting('theme', 'auto');
$lang = get_setting('language', 'ru');
$region = get_setting('region', 'RU');
$user = current_user();
?>
<section class="container" style="padding-top: var(--space-6); max-width: 800px;">
    <h1 class="catalog-title" style="margin-bottom: var(--space-6);"><?= t('settings') ?></h1>

    <?php if (!empty($_GET['saved'])): ?>
        <div class="success-msg" style="margin-bottom: var(--space-4);">Изменения сохранены</div>
    <?php endif; ?>

    <form method="post" action="/settings" id="settings-form">
        <div class="settings-section">
            <h3>Внешний вид</h3>
            <div class="settings-row">
                <div>
                    <div class="settings-row-label"><?= t('theme') ?></div>
                    <div class="settings-row-desc">Выберите оформление сайта</div>
                </div>
                <div class="segmented" data-segmented="theme">
                    <button type="button" data-val="light" class="<?= $theme==='light'?'active':'' ?>"><?= t('theme_light') ?></button>
                    <button type="button" data-val="dark" class="<?= $theme==='dark'?'active':'' ?>"><?= t('theme_dark') ?></button>
                    <button type="button" data-val="auto" class="<?= $theme==='auto'?'active':'' ?>"><?= t('theme_auto') ?></button>
                </div>
                <input type="hidden" name="theme" value="<?= escape($theme) ?>" id="theme-input">
            </div>
        </div>

        <div class="settings-section">
            <h3>Локализация</h3>
            <div class="settings-row">
                <div>
                    <div class="settings-row-label"><?= t('language') ?></div>
                    <div class="settings-row-desc">Язык интерфейса</div>
                </div>
                <div class="segmented" data-segmented="language">
                    <button type="button" data-val="ru" class="<?= $lang==='ru'?'active':'' ?>">Русский</button>
                    <button type="button" data-val="en" class="<?= $lang==='en'?'active':'' ?>">English</button>
                </div>
                <input type="hidden" name="language" value="<?= escape($lang) ?>" id="language-input">
            </div>
            <div class="settings-row">
                <div>
                    <div class="settings-row-label"><?= t('region') ?></div>
                    <div class="settings-row-desc">Регион доставки</div>
                </div>
                <select name="region" class="sort-select" style="background-color: var(--surface-2);">
                    <option value="RU" <?= $region==='RU'?'selected':'' ?>>Россия</option>
                    <option value="BY" <?= $region==='BY'?'selected':'' ?>>Беларусь</option>
                    <option value="KZ" <?= $region==='KZ'?'selected':'' ?>>Казахстан</option>
                    <option value="AM" <?= $region==='AM'?'selected':'' ?>>Армения</option>
                    <option value="KG" <?= $region==='KG'?'selected':'' ?>>Кыргызстан</option>
                </select>
            </div>
        </div>

        <?php if ($user): ?>
            <div class="settings-section">
                <h3><?= t('account_settings') ?></h3>
                <div class="settings-row">
                    <div>
                        <div class="settings-row-label"><?= escape($user['name']) ?></div>
                        <div class="settings-row-desc"><?= escape($user['email']) ?></div>
                    </div>
                    <a href="/profile" class="btn btn-ghost btn-sm">Редактировать</a>
                </div>
                <div class="settings-row">
                    <div>
                        <div class="settings-row-label">Выйти из аккаунта</div>
                    </div>
                    <a href="/logout" class="btn btn-danger btn-sm"><?= t('logout') ?></a>
                </div>
            </div>
        <?php else: ?>
            <div class="settings-section">
                <h3><?= t('account_settings') ?></h3>
                <div class="settings-row">
                    <div>
                        <div class="settings-row-label">Войдите в аккаунт</div>
                        <div class="settings-row-desc">Чтобы сохранить корзину и настройки</div>
                    </div>
                    <a href="/login" class="btn btn-primary btn-sm"><?= t('login') ?></a>
                </div>
            </div>
        <?php endif; ?>

        <button type="submit" class="btn btn-primary btn-lg"><?= t('save') ?></button>
    </form>
</section>

<script>
document.querySelectorAll('[data-segmented]').forEach(group => {
    const key = group.dataset.segmented;
    const input = document.getElementById(key + '-input');
    group.querySelectorAll('button').forEach(b => {
        b.addEventListener('click', () => {
            group.querySelectorAll('button').forEach(x => x.classList.remove('active'));
            b.classList.add('active');
            input.value = b.dataset.val;
            if (key === 'theme') {
                document.documentElement.setAttribute('data-theme', b.dataset.val);
            }
        });
    });
});
</script>
