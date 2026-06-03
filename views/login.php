<section class="auth-page">
    <div class="auth-card">
        <div class="auth-head">
            <h1 class="auth-title"><?= t('login') ?></h1>
            <p class="auth-sub">Добро пожаловать в Kolendula</p>
        </div>
        <?php if (!empty($error)): ?>
            <div class="error-msg" style="margin-bottom: var(--space-4);"><?= escape($error) ?></div>
        <?php endif; ?>
        <?php if (flash_get('forgot_done')): ?>
            <div class="success-msg" style="margin-bottom: var(--space-4);">Пароль успешно изменён. Войдите с новым паролем.</div>
        <?php endif; ?>
        <form class="form" method="post" action="/login">
            <div class="field">
                <label for="login"><?= t('login_field') ?> / Email</label>
                <input class="input" type="text" id="login" name="login" required autofocus>
            </div>
            <div class="field">
                <label for="password"><?= t('password') ?></label>
                <input class="input" type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary btn-block btn-lg"><?= t('login') ?></button>
            <p style="text-align:center; margin-top: var(--space-3);"><a href="/forgot" class="link-muted">Забыли пароль?</a></p>
        </form>
        <div class="auth-foot">
            <?= t('no_account') ?> <a href="/register"><?= t('register') ?></a>
        </div>
    </div>
</section>
