/* Kolendula — frontend interactions */
(function () {
  'use strict';

  const $ = (sel, root = document) => root.querySelector(sel);
  const $$ = (sel, root = document) => Array.from(root.querySelectorAll(sel));

  /* ====== THEME ====== */
  const root = document.documentElement;
  const currentTheme = () => root.getAttribute('data-theme') || 'auto';
  function setTheme(theme) {
    root.setAttribute('data-theme', theme);
    fetch('/settings', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'fetch' },
      body: 'theme=' + encodeURIComponent(theme),
    }).catch(() => {});
    updateThemeButtons();
  }
  function updateThemeButtons() {
    const cur = currentTheme();
    $$('[data-theme-set]').forEach((b) => b.classList.toggle('active', b.dataset.themeSet === cur));
  }
  $$('[data-theme-set]').forEach((b) =>
    b.addEventListener('click', () => setTheme(b.dataset.themeSet))
  );
  updateThemeButtons();

  /* ====== BURGER PANEL ====== */
  const panel = $('#burger-panel');
  const overlay = $('#burger-overlay');
  const burgerBtn = $('#burger-btn');
  const burgerClose = $('#burger-close');
  const bottomMenuBtn = $('#bottom-menu-btn');

  function openPanel() {
    if (!panel) return;
    panel.hidden = false;
    overlay.hidden = false;
    requestAnimationFrame(() => {
      panel.classList.add('visible');
      overlay.classList.add('visible');
    });
    document.body.style.overflow = 'hidden';
    burgerBtn?.setAttribute('aria-expanded', 'true');
  }
  function closePanel() {
    if (!panel) return;
    panel.classList.remove('visible');
    panel.hidden = true;
    overlay.hidden = true;
    document.body.style.overflow = '';
    burgerBtn?.setAttribute('aria-expanded', 'false');
  }
  burgerBtn?.addEventListener('click', openPanel);
  burgerClose?.addEventListener('click', closePanel);
  overlay?.addEventListener('click', closePanel);
  bottomMenuBtn?.addEventListener('click', openPanel);

  /* ====== CATEGORIES DROPDOWN ====== */
  let activeDropdown = null;
  $$('[data-dropdown]').forEach((trigger) => {
    const menu = $(`[data-dropdown-menu="${trigger.dataset.dropdown}"]`);
    if (!menu) return;
    function toggle(e) {
      e.stopPropagation();
      const willOpen = !menu.classList.contains('open');
      if (activeDropdown && activeDropdown !== menu) activeDropdown.classList.remove('open');
      menu.classList.toggle('open', willOpen);
      activeDropdown = willOpen ? menu : null;
    }
    trigger.addEventListener('click', toggle);
  });
  document.addEventListener('click', (e) => {
    if (activeDropdown && !e.target.closest('[data-dropdown-menu]') && !e.target.closest('[data-dropdown]')) {
      activeDropdown.classList.remove('open');
      activeDropdown = null;
    }
  });

  /* ====== SEARCH SUGGEST ====== */
  const searchForms = $$('.search-form');
  searchForms.forEach((form) => {
    const input = form.querySelector('.search-input');
    const suggest = form.querySelector('.search-suggest');
    if (!input || !suggest) return;
    let timer = null;
    let abort = null;

    async function runSearch(q) {
      if (!q || q.length < 2) {
        suggest.hidden = true;
        return;
      }
      if (abort) abort.abort();
      abort = new AbortController();
      try {
        const r = await fetch('/api/search?q=' + encodeURIComponent(q) + '&limit=6', { signal: abort.signal });
        const data = await r.json();
        if (!data.products || data.products.length === 0) {
          suggest.innerHTML = `<div class="suggest-empty">${KOL.t.no_results}</div>`;
        } else {
          suggest.innerHTML = data.products
            .map(
              (p) => `
            <a class="suggest-item" href="/product/${p.id}">
              <img class="suggest-img" src="/img/product/${p.image}.svg" alt="" loading="lazy">
              <div class="suggest-info">
                <div class="suggest-name">${escapeHtml(p.name)}</div>
                <div class="suggest-brand">${escapeHtml(p.brand)}</div>
              </div>
              <div class="suggest-price">${formatPrice(p.price)}</div>
            </a>`
            )
            .join('');
        }
        suggest.hidden = false;
      } catch (e) {
        if (e.name !== 'AbortError') console.error(e);
      }
    }

    input.addEventListener('input', () => {
      const q = input.value.trim();
      clearTimeout(timer);
      timer = setTimeout(() => runSearch(q), 200);
    });
    input.addEventListener('focus', () => {
      if (input.value.trim().length >= 2) runSearch(input.value.trim());
    });
    document.addEventListener('click', (e) => {
      if (!form.contains(e.target)) suggest.hidden = true;
    });
  });

  /* ====== ADD TO CART ====== */
  document.addEventListener('click', async (e) => {
    const btn = e.target.closest('[data-add-cart]');
    if (!btn) return;
    e.preventDefault();
    if (btn.disabled) return;
    btn.disabled = true;
    const productId = btn.dataset.addCart;
    const qty = parseInt(btn.dataset.qty || '1', 10);
    try {
      const r = await fetch('/cart/add', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `product_id=${encodeURIComponent(productId)}&qty=${qty}`,
      });
      const data = await r.json();
      if (data.ok) {
        updateCartBadge(data.cart_count);
        btn.classList.add('in-cart');
        showToast(KOL.t.added_to_cart, 'success');
        // Tiny bounce
        btn.animate(
          [
            { transform: 'scale(1)' },
            { transform: 'scale(1.18)' },
            { transform: 'scale(1)' },
          ],
          { duration: 360, easing: 'cubic-bezier(0.34, 1.56, 0.64, 1)' }
        );
      } else {
        showToast(data.error || 'Error', 'error');
      }
    } catch (err) {
      showToast('Error', 'error');
    } finally {
      setTimeout(() => (btn.disabled = false), 200);
    }
  });

  function updateCartBadge(n) {
    $$('[data-cart-count]').forEach((el) => {
      el.textContent = n;
      el.hidden = n === 0;
    });
  }

  /* ====== TOAST ====== */
  const toastContainer = $('#toast-container');
  function showToast(message, kind = '') {
    if (!toastContainer) return;
    const toast = document.createElement('div');
    toast.className = 'toast ' + kind;
    toast.textContent = message;
    toastContainer.appendChild(toast);
    setTimeout(() => {
      toast.style.transition = 'opacity 0.3s, transform 0.3s';
      toast.style.opacity = '0';
      toast.style.transform = 'translateY(20px)';
      setTimeout(() => toast.remove(), 320);
    }, 2400);
  }
  window.kolendulaToast = showToast;

  /* ====== REVEAL ANIMATIONS ====== */
  const reveals = $$('.reveal');
  const observer = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          entry.target.classList.add('in');
          observer.unobserve(entry.target);
        }
      });
    },
    { threshold: 0.01, rootMargin: '0px 0px 200px 0px' }
  );
  reveals.forEach((el, i) => {
    el.style.transitionDelay = Math.min(i * 40, 240) + 'ms';
    observer.observe(el);
  });
  /* Fallback: показать всё через 3 секунды если не доскроллили */
  setTimeout(() => {
    reveals.forEach((el) => el.classList.add('in'));
  }, 3000);

  /* ====== BOTTOM NAV ACTIVE STATE ====== */
  const path = window.location.pathname;
  $$('.bottom-nav-item[data-route]').forEach((item) => {
    const route = item.dataset.route;
    const map = { home: '/', catalog: '/catalog', search: '/search', cart: '/cart' };
    const target = map[route];
    if (target && (path === target || (target !== '/' && path.startsWith(target)))) {
      item.classList.add('active');
    } else if (target === '/' && path === '/') {
      item.classList.add('active');
    }
  });

  /* ====== MOBILE FILTERS DRAWER ====== */
  const filterBtn = $('#mobile-filter-btn');
  const filterPanel = $('#filters');
  filterBtn?.addEventListener('click', () => {
    filterPanel.classList.add('open');
    document.body.style.overflow = 'hidden';
  });
  $('#filters-close')?.addEventListener('click', () => {
    filterPanel.classList.remove('open');
    document.body.style.overflow = '';
  });

  /* ====== QUANTITY STEPPERS ====== */
  document.addEventListener('click', async (e) => {
    const stepBtn = e.target.closest('[data-qty-step]');
    if (!stepBtn) return;
    const stepper = stepBtn.closest('.qty-stepper');
    const valEl = stepper.querySelector('.qty-val');
    const itemId = stepper.dataset.itemId;
    let val = parseInt(valEl.textContent, 10);
    val = Math.max(0, val + parseInt(stepBtn.dataset.qtyStep, 10));
    valEl.textContent = val;
    if (itemId) {
      const r = await fetch('/cart/update', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `item_id=${itemId}&qty=${val}`,
      });
      const data = await r.json();
      if (data.ok) {
        updateCartBadge(data.cart_count);
        if (val === 0) stepper.closest('.cart-item')?.remove();
        if (data.cart_total !== undefined) updateCartTotal(data.cart_total, data.cart_subtotal);
      }
    }
  });

  document.addEventListener('click', async (e) => {
    const rm = e.target.closest('[data-remove-item]');
    if (!rm) return;
    e.preventDefault();
    const itemId = rm.dataset.removeItem;
    const r = await fetch('/cart/remove', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: `item_id=${itemId}`,
    });
    const data = await r.json();
    if (data.ok) {
      const card = rm.closest('.cart-item');
      if (card) {
        card.style.transition = 'opacity 0.3s, transform 0.3s';
        card.style.opacity = '0';
        card.style.transform = 'translateX(20px)';
        setTimeout(() => card.remove(), 320);
      }
      updateCartBadge(data.cart_count);
      if (data.cart_total !== undefined) updateCartTotal(data.cart_total, data.cart_subtotal);
      if (data.cart_count === 0) setTimeout(() => location.reload(), 400);
    }
  });

  function updateCartTotal(total, subtotal) {
    const tEl = $('[data-cart-total]');
    const sEl = $('[data-cart-subtotal]');
    if (tEl) tEl.textContent = formatPrice(total);
    if (sEl) sEl.textContent = formatPrice(subtotal);
  }

  /* ====== UTILS ====== */
  function escapeHtml(s) {
    return String(s).replace(/[&<>"']/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
  }
  function formatPrice(n) {
    return new Intl.NumberFormat('ru-RU').format(n) + ' ₽';
  }

  /* ====== SORT — submit on change ====== */
  $('#sort-select')?.addEventListener('change', (e) => {
    const url = new URL(window.location);
    url.searchParams.set('sort', e.target.value);
    window.location = url.toString();
  });

  /* ====== HEADER SHADOW ON SCROLL ====== */
  const header = $('#header');
  if (header) {
    const onScroll = () => header.classList.toggle('scrolled', window.scrollY > 8);
    onScroll();
    window.addEventListener('scroll', onScroll, { passive: true });
  }
})();

/* burger panel visibility helper styles via JS-added class */
const style = document.createElement('style');
style.textContent = `
.burger-overlay { transition: opacity 240ms cubic-bezier(0.16, 1, 0.3, 1); opacity: 0; }
.burger-overlay.visible { opacity: 1; }
.burger-panel { transform: translateX(100%); transition: transform 360ms cubic-bezier(0.16, 1, 0.3, 1); }
.burger-panel.visible { transform: translateX(0); }
.header.scrolled { box-shadow: 0 4px 24px rgba(0,0,0,0.04); }
[data-theme='dark'] .header.scrolled { box-shadow: 0 4px 24px rgba(0,0,0,0.3); }
`;
document.head.appendChild(style);
