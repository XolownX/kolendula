<section class="auth-page">
    <div class="auth-card">
        <div class="auth-head">
            <h1 class="auth-title"><?= t('register') ?></h1>
            <p class="auth-sub">Создайте аккаунт за минуту</p>
        </div>
        <?php if (!empty($error)): ?>
            <div class="error-msg" style="margin-bottom: var(--space-4);"><?= escape($error) ?></div>
        <?php endif; ?>
        <form class="form" method="post" action="/register">
            <div class="field">
                <label for="name"><?= t('name') ?></label>
                <input class="input" type="text" id="name" name="name" required value="<?= escape($values['name']) ?>">
            </div>
            <div class="field">
                <label for="login"><?= t('login_field') ?></label>
                <input class="input" type="text" id="login" name="login" required value="<?= escape($values['login']) ?>">
            </div>
            <div class="field">
                <label for="phone"><?= t('phone') ?></label>
                <input class="input" type="tel" id="phone" name="phone" placeholder="+7 ___ ___-__-__" value="<?= escape($values['phone']) ?>">
            </div>
            <div class="field">
                <label for="email"><?= t('email') ?></label>
                <input class="input" type="email" id="email" name="email" required value="<?= escape($values['email']) ?>">
            </div>
            <div class="field">
                <label for="password"><?= t('password') ?></label>
                <input class="input" type="password" id="password" name="password" required minlength="6">
            </div>
            <div class="field">
                <label for="password_confirm"><?= t('password_confirm') ?></label>
                <input class="input" type="password" id="password_confirm" name="password_confirm" required minlength="6">
            </div>
            <button type="submit" class="btn btn-primary btn-block btn-lg"><?= t('register') ?></button>
        </form>
        <div class="auth-foot">
            <?= t('have_account') ?> <a href="/login"><?= t('login') ?></a>
        </div>
    </div>
</section>
