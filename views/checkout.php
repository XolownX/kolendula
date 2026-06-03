<section class="container" style="padding-top: var(--space-6); padding-bottom: var(--space-8);">
    <h1 class="catalog-title" style="margin-bottom: var(--space-5);">Оформление заказа</h1>

    <?php if (!empty($error)): ?>
        <div class="error-msg" style="margin-bottom: var(--space-4);"><?= escape($error) ?></div>
    <?php endif; ?>

    <form method="post" action="/checkout" class="checkout-form">
        <div class="checkout-grid">
            <div class="checkout-main">

                <div class="checkout-block">
                    <h2 class="checkout-block-title">Способ получения</h2>
                    <div class="delivery-options">
                        <label class="delivery-opt">
                            <input type="radio" name="delivery_type" value="pickup" checked>
                            <div class="delivery-opt-body">
                                <div class="delivery-opt-name">Самовывоз из магазина</div>
                                <div class="delivery-opt-meta">Бесплатно</div>
                            </div>
                        </label>
                        <label class="delivery-opt">
                            <input type="radio" name="delivery_type" value="pvz">
                            <div class="delivery-opt-body">
                                <div class="delivery-opt-name">Пункт выдачи</div>
                                <div class="delivery-opt-meta">+ 299 ₽</div>
                            </div>
                        </label>
                        <label class="delivery-opt">
                            <input type="radio" name="delivery_type" value="courier">
                            <div class="delivery-opt-body">
                                <div class="delivery-opt-name">Курьером на адрес</div>
                                <div class="delivery-opt-meta">+ 499 ₽</div>
                            </div>
                        </label>
                    </div>

                    <div class="delivery-detail" data-for="pickup">
                        <label class="field-label">Магазин самовывоза</label>
                        <select class="input" name="pickup_store">
                            <option value="Хабаровск, ул. Муравьёва-Амурского, 21">Хабаровск, ул. Муравьёва-Амурского, 21</option>
                            <option value="Хабаровск, ТЦ Большая, Карла Маркса 91">Хабаровск, ТЦ Большая, Карла Маркса 91</option>
                            <option value="Владивосток, Светланская 29">Владивосток, Светланская 29</option>
                            <option value="Москва, Пресненская набережная 12">Москва, Пресненская набережная 12</option>
                        </select>
                    </div>
                    <div class="delivery-detail" data-for="pvz" hidden>
                        <label class="field-label">Пункт выдачи</label>
                        <select class="input" name="pvz_point">
                            <option value="">— Выберите пункт —</option>
                            <option value="СДЭК, Хабаровск, Ленина 45">СДЭК, Хабаровск, Ленина 45</option>
                            <option value="Боксберри, Хабаровск, Ким Ю Чена 5">Боксберри, Хабаровск, Ким Ю Чена 5</option>
                            <option value="ПЭК, Хабаровск, Уссурийский бул. 9">ПЭК, Хабаровск, Уссурийский бул. 9</option>
                            <option value="5Post, Хабаровск, Краснореченская 30">5Post, Хабаровск, Краснореченская 30</option>
                        </select>
                    </div>
                    <div class="delivery-detail" data-for="courier" hidden>
                        <label class="field-label">Адрес доставки</label>
                        <input class="input" type="text" name="courier_address" placeholder="Город, улица, дом, квартира">
                    </div>
                </div>

                <div class="checkout-block">
                    <h2 class="checkout-block-title">Оплата картой</h2>

                    <?php if (!empty($cards)): ?>
                        <div class="saved-cards">
                            <?php foreach ($cards as $c): ?>
                                <label class="saved-card">
                                    <input type="radio" name="use_saved_card" value="<?= (int)$c['id'] ?>">
                                    <span class="saved-card-body">
                                        <span class="saved-card-brand"><?= escape($c['brand']) ?></span>
                                        <span class="saved-card-num">•••• <?= escape($c['last4']) ?></span>
                                        <?php if ($c['exp_month'] && $c['exp_year']): ?>
                                            <span class="saved-card-exp"><?= str_pad((string)$c['exp_month'], 2, '0', STR_PAD_LEFT) ?>/<?= $c['exp_year'] ?></span>
                                        <?php endif; ?>
                                    </span>
                                </label>
                            <?php endforeach; ?>
                            <label class="saved-card">
                                <input type="radio" name="use_saved_card" value="" checked>
                                <span class="saved-card-body"><strong>+ Новая карта</strong></span>
                            </label>
                        </div>
                    <?php endif; ?>

                    <div class="new-card-fields">
                        <div class="field">
                            <label>Номер карты</label>
                            <input class="input" type="text" name="card_number" inputmode="numeric" maxlength="19" placeholder="0000 0000 0000 0000" id="card-number">
                        </div>
                        <div class="field">
                            <label>Имя владельца</label>
                            <input class="input" type="text" name="card_holder" placeholder="IVAN IVANOV">
                        </div>
                        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: var(--space-3);">
                            <div class="field">
                                <label>Месяц</label>
                                <input class="input" type="text" name="exp_month" inputmode="numeric" maxlength="2" placeholder="MM">
                            </div>
                            <div class="field">
                                <label>Год</label>
                                <input class="input" type="text" name="exp_year" inputmode="numeric" maxlength="2" placeholder="YY">
                            </div>
                            <div class="field">
                                <label>CVV</label>
                                <input class="input" type="password" name="cvv" inputmode="numeric" maxlength="4" placeholder="•••">
                            </div>
                        </div>
                        <label class="check-row">
                            <input type="checkbox" name="save_card" value="1" checked>
                            <span>Сохранить карту для будущих покупок</span>
                        </label>
                    </div>

                    <p class="muted" style="font-size: 0.85rem; margin-top: var(--space-3);">💡 Это демо. Для теста используйте номер <code>4242 4242 4242 4242</code> или любой с валидной контрольной суммой.</p>
                </div>
            </div>

            <aside class="checkout-side">
                <div class="checkout-summary">
                    <h2 class="checkout-block-title">Ваш заказ</h2>
                    <?php foreach ($items as $i): ?>
                        <div class="summary-item">
                            <div class="summary-item-name"><?= escape($i['name']) ?> × <?= (int)$i['quantity'] ?></div>
                            <div class="summary-item-price"><?= format_price($i['price'] * $i['quantity']) ?></div>
                        </div>
                    <?php endforeach; ?>
                    <div class="summary-divider"></div>
                    <div class="summary-row">
                        <span>Товаров на сумму</span>
                        <span><?= format_price($subtotal) ?></span>
                    </div>
                    <div class="summary-row" id="delivery-row">
                        <span>Доставка</span>
                        <span id="delivery-fee">Бесплатно</span>
                    </div>
                    <div class="summary-divider"></div>
                    <div class="summary-row summary-total">
                        <span>Итого</span>
                        <span id="total-price"><?= format_price($subtotal) ?></span>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block btn-lg" style="margin-top: var(--space-4);">Подтвердить и оплатить</button>
                    <p class="muted" style="font-size: 0.8rem; text-align: center; margin-top: var(--space-2);">Нажимая кнопку, вы соглашаетесь с условиями</p>
                </div>
            </aside>
        </div>
    </form>
</section>

<script>
(function(){
    const subtotal = <?= (int)$subtotal ?>;
    const radios = document.querySelectorAll('input[name="delivery_type"]');
    const details = document.querySelectorAll('.delivery-detail');
    const feeEl = document.getElementById('delivery-fee');
    const totalEl = document.getElementById('total-price');
    const fees = { pickup: 0, pvz: 299, courier: 499 };

    function fmt(n){ return n.toLocaleString('ru-RU').replace(/,/g,' ') + ' ₽'; }
    function sync(){
        const val = document.querySelector('input[name="delivery_type"]:checked').value;
        details.forEach(d => d.hidden = d.dataset.for !== val);
        const fee = fees[val] || 0;
        feeEl.textContent = fee ? '+ ' + fmt(fee) : 'Бесплатно';
        totalEl.textContent = fmt(subtotal + fee);
    }
    radios.forEach(r => r.addEventListener('change', sync));
    sync();

    // Форматирование номера карты
    const cn = document.getElementById('card-number');
    cn?.addEventListener('input', function(){
        let v = this.value.replace(/\D/g,'').slice(0,16);
        this.value = v.replace(/(\d{4})(?=\d)/g,'$1 ');
    });

    // Сохранённая карта vs новая
    document.querySelectorAll('input[name="use_saved_card"]').forEach(r => {
        r.addEventListener('change', function(){
            const useNew = this.value === '';
            document.querySelector('.new-card-fields').style.display = useNew ? '' : 'none';
        });
    });
})();
</script>
