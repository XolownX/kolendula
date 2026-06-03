<?php
// $products, $opts, $brands, $allCategories, $selectedCats, $selectedBrands, $lang
$selectedCats   = $selectedCats   ?? [];
$selectedBrands = $selectedBrands ?? [];
$currentSort = $opts['sort'] ?? 'popular';
$qParams = array_filter([
    'sort'     => $currentSort,
    'hot'      => $opts['is_hot'] ? 1 : null,
    'new'      => $opts['is_new'] ? 1 : null,
    'discount' => $opts['has_discount'] ? 1 : null,
    'q'        => $opts['q'] ?? null,
    'min_price'=> $_GET['min_price'] ?? null,
    'max_price'=> $_GET['max_price'] ?? null,
]);
?>
<section class="container" style="padding-top: var(--space-6);">

    <?php if (!empty($opts['q'])): ?>
        <!-- Mobile search bar visible in mobile -->
    <?php endif; ?>

    <div class="catalog-header">
        <div>
            <h1 class="catalog-title"><?= escape($pageTitle) ?></h1>
            <div class="catalog-count"><?= t('showing_results') ?>: <?= count($products) ?></div>
        </div>
        <div class="toolbar">
            <button class="mobile-filter-btn" id="mobile-filter-btn">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="4" y1="21" x2="4" y2="14"/><line x1="4" y1="10" x2="4" y2="3"/><line x1="12" y1="21" x2="12" y2="12"/><line x1="12" y1="8" x2="12" y2="3"/><line x1="20" y1="21" x2="20" y2="16"/><line x1="20" y1="12" x2="20" y2="3"/><line x1="1" y1="14" x2="7" y2="14"/><line x1="9" y1="8" x2="15" y2="8"/><line x1="17" y1="16" x2="23" y2="16"/></svg>
                Фильтры
            </button>
            <select class="sort-select" id="sort-select" aria-label="<?= t('sort_by') ?>">
                <option value="popular" <?= $currentSort==='popular'?'selected':'' ?>><?= t('sort_popular') ?></option>
                <option value="price_asc" <?= $currentSort==='price_asc'?'selected':'' ?>><?= t('sort_price_asc') ?></option>
                <option value="price_desc" <?= $currentSort==='price_desc'?'selected':'' ?>><?= t('sort_price_desc') ?></option>
                <option value="rating" <?= $currentSort==='rating'?'selected':'' ?>><?= t('sort_rating') ?></option>
                <option value="new" <?= $currentSort==='new'?'selected':'' ?>><?= t('sort_new') ?></option>
            </select>
        </div>
    </div>

    <div class="catalog-layout">
        <aside class="filters" id="filters">
            <div class="filters-mobile-head">
                <h2>Фильтры</h2>
                <button class="icon-btn" id="filters-close" aria-label="<?= t('close') ?>">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </div>

            <form method="get" action="/catalog" id="filters-form">
                <input type="hidden" name="sort" value="<?= escape($currentSort) ?>">
                <?php if (!empty($opts['q'])): ?><input type="hidden" name="q" value="<?= escape($opts['q']) ?>"><?php endif; ?>
                <?php if (!empty($opts['is_hot'])): ?><input type="hidden" name="hot" value="1"><?php endif; ?>
                <?php if (!empty($opts['is_new'])): ?><input type="hidden" name="new" value="1"><?php endif; ?>
                <?php if (!empty($opts['has_discount'])): ?><input type="hidden" name="discount" value="1"><?php endif; ?>

                <div class="filter-group">
                    <div class="filter-title"><?= t('categories') ?></div>
                    <div class="filter-list">
                        <?php foreach ($allCategories as $cat): ?>
                            <?php $on = in_array($cat['slug'], $selectedCats, true); ?>
                            <label class="filter-check">
                                <input type="checkbox" name="categories_arr[]" value="<?= escape($cat['slug']) ?>" <?= $on?'checked':'' ?>>
                                <span><?= $cat['icon'] ?></span>
                                <span><?= escape($lang === 'en' ? $cat['name_en'] : $cat['name_ru']) ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <?php if (!empty($brands)): ?>
                <div class="filter-group">
                    <div class="filter-title"><?= t('brand') ?></div>
                    <div class="filter-list">
                        <?php foreach ($brands as $brand): ?>
                            <?php $on = in_array($brand, $selectedBrands, true); ?>
                            <label class="filter-check">
                                <input type="checkbox" name="brands_arr[]" value="<?= escape($brand) ?>" <?= $on?'checked':'' ?>>
                                <span><?= escape($brand) ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <div class="filter-group">
                    <div class="filter-title"><?= t('price_range') ?></div>
                    <div class="filter-range">
                        <input type="number" name="min_price" placeholder="от" class="filter-input" value="<?= escape($_GET['min_price'] ?? '') ?>">
                        <input type="number" name="max_price" placeholder="до" class="filter-input" value="<?= escape($_GET['max_price'] ?? '') ?>">
                    </div>
                </div>

                <div style="display: flex; gap: var(--space-2); margin-top: var(--space-2);">
                    <button class="btn btn-primary btn-sm" type="submit" style="flex: 1;"><?= t('apply') ?></button>
                    <a class="btn btn-ghost btn-sm" href="/catalog" style="flex: 1;"><?= t('reset') ?></a>
                </div>
            </form>
        </aside>

        <div>
            <?php if (empty($products)): ?>
                <div class="empty-state">
                    <div class="empty-icon">
                        <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3"/></svg>
                    </div>
                    <h2 class="empty-title"><?= t('no_results') ?></h2>
                    <p class="empty-text">Попробуйте изменить фильтры или поисковый запрос</p>
                    <a href="/catalog" class="btn btn-primary"><?= t('reset') ?></a>
                </div>
            <?php else: ?>
                <div class="grid-products">
                    <?php foreach ($products as $p): ?>
                        <?php require __DIR__ . '/partials/product_card.php'; ?>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>
