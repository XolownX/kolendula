<section class="container auth-wrap" style="padding-top: var(--space-6);">
    <div class="auth-card">
        <h1 class="auth-title">Восстановление пароля</h1>

        <?php if (!empty($error)): ?>
            <div class="error-msg" style="margin-bottom: var(--space-3);"><?= escape($error) ?></div>
        <?php endif; ?>

        <?php if ($stage === 'request'): ?>
            <p class="muted" style="margin-bottom: var(--space-4);">Введите логин или email, мы пришлём 6-значный код.</p>
            <form method="post" class="form">
                <input type="hidden" name="action" value="request">
                <div class="field">
                    <label for="login_or_email">Логин или email</label>
                    <input class="input" type="text" name="login_or_email" id="login_or_email" required autofocus>
                </div>
                <button type="submit" class="btn btn-primary btn-block">Получить код</button>
                <p style="text-align:center; margin-top: var(--space-3);"><a href="/login" class="link-muted">← Назад ко входу</a></p>
            </form>

        <?php elseif ($stage === 'verify'): ?>
            <p class="muted" style="margin-bottom: var(--space-4);">Введите 6-значный код.</p>
            <form method="post" class="form">
                <input type="hidden" name="action" value="verify">
                <div class="field">
                    <label for="code">Код подтверждения</label>
                    <input class="input" type="text" name="code" id="code" inputmode="numeric" pattern="[0-9]{6}" maxlength="6" required autofocus style="letter-spacing: 0.3em; text-align: center; font-size: 1.2rem;">
                </div>
                <button type="submit" class="btn btn-primary btn-block">Подтвердить</button>
                <p style="text-align:center; margin-top: var(--space-3);"><a href="/forgot" class="link-muted">Отправить ещё раз</a></p>
            </form>

        <?php elseif ($stage === 'reset'): ?>
            <p class="muted" style="margin-bottom: var(--space-4);">Придумайте новый пароль.</p>
            <form method="post" class="form">
                <input type="hidden" name="action" value="reset">
                <div class="field">
                    <label for="password">Новый пароль</label>
                    <input class="input" type="password" name="password" id="password" minlength="6" required>
                </div>
                <div class="field">
                    <label for="password_confirm">Повторите пароль</label>
                    <input class="input" type="password" name="password_confirm" id="password_confirm" minlength="6" required>
                </div>
                <button type="submit" class="btn btn-primary btn-block">Сменить пароль</button>
            </form>
        <?php endif; ?>
    </div>
</section>

<?php if (!empty($demoCode)): ?>
<script>
(function(){
    // Демо-режим: показываем код в модалке
    setTimeout(function(){
        const overlay = document.createElement('div');
        overlay.className = 'demo-code-modal';
        overlay.innerHTML = '<div class="demo-code-inner">' +
            '<div class="demo-code-title">🔐 Демо-режим</div>' +
            '<div class="demo-code-text">Ваш код подтверждения:</div>' +
            '<div class="demo-code-value"><?= escape($demoCode) ?></div>' +
            '<div class="demo-code-hint">В реальном проекте он придёт на email/SMS</div>' +
            '<button class="btn btn-primary btn-block" type="button">OK</button>' +
            '</div>';
        document.body.appendChild(overlay);
        overlay.addEventListener('click', function(e){
            if (e.target === overlay || e.target.tagName === 'BUTTON') {
                overlay.remove();
            }
        });
    }, 80);
})();
</script>
<?php endif; ?>
