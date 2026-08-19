<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#1A1A1A">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="{{ setting('app_name', 'EyeCare Studio') }}">
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/apple-touch-icon.png') }}">
    <title>@yield('title', setting('app_name', config('app.name', 'EyeCare Studio')))</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400;1,600&family=Cormorant+Garamond:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400&family=Dancing+Script:wght@400;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/app.css') . '?v=10' }}">
    @stack('styles')
</head>
<body>
    <div id="offlineIndicator" class="offline-indicator">
        <i class="fas fa-wifi-slash"></i> No internet connection. Showing cached data.
    </div>

    {{-- PWA install banner --}}
    <div id="installBanner" class="install-banner" style="display:none">
        <img src="{{ asset('images/icon-192.png') }}" alt="{{ setting('app_name', 'EyeCare Studio') }}" width="40" height="40">
        <div class="install-banner-info">
            <strong>Install {{ setting('app_name', 'EyeCare Studio') }}</strong>
            <span>Add to home screen for fast offline access</span>
        </div>
        <button class="install-banner-btn" id="installBtn">Install</button>
        <button class="install-banner-close" aria-label="Dismiss" onclick="this.closest('#installBanner').style.display='none'">&times;</button>
    </div>

    {{-- PWA update toast --}}
    <div id="updateToast" class="update-toast" style="display:none">
        <i class="fas fa-sync-alt"></i>
        <span>New version available</span>
        <button id="updateReloadBtn">Refresh</button>
        <button class="update-toast-close" aria-label="Dismiss" onclick="this.closest('#updateToast').style.display='none'">&times;</button>
    </div>
    <div id="app">
        @include('partials.side-drawer')
        @include('partials.header')
        @include('partials.brand-ticker')

        <main class="main-content">
            <div id="syncBanner" class="sync-banner" style="display:none">
                <i class="fas fa-sync-alt"></i>
                <span id="syncBannerText">New products available</span>
                <button onclick="applySyncUpdate()">Refresh</button>
                <button onclick="dismissSyncBanner()">&times;</button>
            </div>
            @yield('content')
        </main>

        @include('partials.filter-drawer')
        @include('partials.cart-drawer')
    </div>

    <div id="overlay" class="overlay" onclick="closeDrawer()"></div>

    {{-- Search Overlay (AJAX) --}}
    <div id="searchOverlay" class="search-overlay" onclick="if(event.target===this) closeSearch()">
        <div class="search-panel">
            <div class="search-panel-header">
                <i class="fas fa-search search-panel-icon"></i>
                <input type="search" id="searchOverlayInput" class="search-panel-input"
                       placeholder="Search by model or BQ number..." autocomplete="off" aria-label="Search products">
                <button class="modal-close" onclick="closeSearch()" aria-label="Close">&times;</button>
            </div>
            <div id="searchResults" class="search-results">
                <p class="search-hint">Type a model number or BQ number to search.</p>
            </div>
            <div class="search-panel-footer">
                <button id="searchViewAllBtn" class="btn-view-more" style="display:none">View All Results</button>
            </div>
        </div>
    </div>

    {{-- Compare Tray --}}
    <div id="compareTray" class="compare-tray" style="display:none">
        <div class="compare-tray-info">
            <i class="fas fa-scale-balanced"></i>
            <span class="compare-tray-label">Compare</span>
            <span class="compare-tray-count" id="compareCount">0</span>
        </div>
        <div class="compare-tray-actions">
            <button class="btn-compare-clear" onclick="clearCompare()">Clear</button>
            <button class="btn-compare-open" onclick="openCompare()">Compare Now</button>
        </div>
    </div>

    {{-- Compare Overlay --}}
    <div id="compareOverlay" class="compare-overlay" onclick="if(event.target===this) closeCompare()">
        <div class="compare-box">
            <div class="compare-box-header">
                <h3>Compare Products</h3>
                <button class="modal-close" onclick="closeCompare()" aria-label="Close">&times;</button>
            </div>
            <div class="compare-scroll" id="compareTable"></div>
        </div>
    </div>

    <script>
        const drawer = document.getElementById('sideDrawer');
        const filterDrawer = document.getElementById('filterDrawer');
        const cartDrawer = document.getElementById('cartDrawer');
        const overlay = document.getElementById('overlay');
        let lastSyncTime = localStorage.getItem('gem_last_sync') || '';
        let pendingUpdates = [];
        let syncInterval = null;
        let isSyncing = false;

        // ─── Service Worker Registration ───
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/sw.js').catch(() => {});
        }

        // ─── Install Prompt (Android/Chrome) ───
        let deferredPrompt = null;
        const isStandalone = window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true;
        if (!isStandalone) {
            window.addEventListener('beforeinstallprompt', function(e) {
                e.preventDefault();
                deferredPrompt = e;
                const banner = document.getElementById('installBanner');
                if (banner && !localStorage.getItem('gem_install_dismissed')) banner.style.display = 'flex';
            });
            const installBtn = document.getElementById('installBtn');
            if (installBtn) {
                installBtn.addEventListener('click', function() {
                    if (!deferredPrompt) return;
                    deferredPrompt.prompt();
                    deferredPrompt.userChoice.then(function() {
                        deferredPrompt = null;
                        const banner = document.getElementById('installBanner');
                        if (banner) banner.style.display = 'none';
                    });
                });
            }
            window.addEventListener('appinstalled', function() {
                localStorage.setItem('gem_install_dismissed', '1');
                const banner = document.getElementById('installBanner');
                if (banner) banner.style.display = 'none';
            });
        }

        // ─── New-version Update Toast ───
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.ready.then(function(reg) {
                reg.addEventListener('updatefound', function() {
                    const newWorker = reg.installing;
                    if (!newWorker) return;
                    newWorker.addEventListener('statechange', function() {
                        if (newWorker.state === 'installed' && navigator.serviceWorker.controller) {
                            const toast = document.getElementById('updateToast');
                            if (toast) toast.style.display = 'flex';
                        }
                    });
                });
            });
            const updateReloadBtn = document.getElementById('updateReloadBtn');
            if (updateReloadBtn) {
                updateReloadBtn.addEventListener('click', function() {
                    navigator.serviceWorker.ready.then(function(reg) {
                        if (reg.waiting) {
                            reg.waiting.postMessage('SKIP_WAITING');
                        } else {
                            window.location.reload();
                        }
                    });
                });
            }
        }

        // ─── Auto Sync Engine ───
        function startAutoSync() {
            if (syncInterval) clearInterval(syncInterval);
            syncInterval = setInterval(pollForUpdates, 30000);
            updateSyncStatus();
        }

        function pollForUpdates() {
            const syncBtn = document.getElementById('syncStatusBtn');
            if (syncBtn) syncBtn.classList.add('syncing');

            let url = '/api/v1/inventory/delta';
            if (lastSyncTime) url += '?last_synced=' + encodeURIComponent(lastSyncTime);

            const hadPreviousSync = !!lastSyncTime;

            fetch(url)
                .then(r => r.json())
                .then(data => {
                    if (data.data && data.data.length > 0) {
                        pendingUpdates = data.data;
                        lastSyncTime = new Date().toISOString();
                        localStorage.setItem('gem_last_sync', lastSyncTime);
                        if (data.meta.current_page < data.meta.last_page) {
                            fetchAllDeltaPages(data.meta.last_page);
                        } else if (hadPreviousSync) {
                            showSyncBanner(data.data.length + ' new product' + (data.data.length > 1 ? 's' : '') + ' available');
                        }
                    }
                    if (syncBtn) syncBtn.classList.remove('syncing');
                })
                .catch(() => {
                    if (syncBtn) syncBtn.classList.remove('syncing');
                });
        }

        function fetchAllDeltaPages(lastPage) {
            for (let page = 2; page <= lastPage; page++) {
                fetch('/api/v1/inventory/delta?last_synced=' + encodeURIComponent(lastSyncTime) + '&page=' + page)
                    .then(r => r.json())
                    .then(data => {
                        if (data.data) pendingUpdates = pendingUpdates.concat(data.data);
                    })
                    .catch(() => {});
            }
        }

        function showSyncBanner(message) {
            const banner = document.getElementById('syncBanner');
            const text = document.getElementById('syncBannerText');
            if (banner && text) {
                text.textContent = message;
                banner.style.display = 'flex';
                setTimeout(() => banner.classList.add('show'), 10);
            }
        }

        function dismissSyncBanner() {
            const banner = document.getElementById('syncBanner');
            if (banner) {
                banner.classList.remove('show');
                setTimeout(() => banner.style.display = 'none', 300);
            }
        }

        function applySyncUpdate() {
            dismissSyncBanner();
            if (pendingUpdates.length > 0) {
                const currentProducts = document.querySelectorAll('.product-card');
                if (currentProducts.length > 0) {
                    const existingIds = new Set();
                    currentProducts.forEach(card => {
                        const btn = card.querySelector('.btn-view-more');
                        if (btn && btn.getAttribute('onclick')) {
                            const match = btn.getAttribute('onclick').match(/(\d+)/);
                            if (match) existingIds.add(parseInt(match[1]));
                        }
                    });
                    const newProducts = pendingUpdates.filter(p => !existingIds.has(p.id));
                    if (newProducts.length > 0) {
                        const container = document.getElementById('productGrid');
                        const html = newProducts.map(p => {
                            comparePool[p.id] = buildCompareSnapshot(p);
                            return `
                            <div class="product-card sync-flash">
                                <div class="product-image">
                                    <img src="${escapeHtml(p.image_url || 'https://via.placeholder.com/300x200?text=Frame')}" alt="${escapeHtml(p.name || p.model_number)}" loading="lazy">
                                    ${p.is_on_sale ? '<span class="sale-badge">SALE</span>' : ''}
                                    ${p.is_new_arrival ? '<span class="new-badge">NEW</span>' : ''}
                                </div>
                                <div class="product-info">
                                    <h3 class="product-brand">${escapeHtml(p.brand?.name || 'Luxury Brand')}</h3>
                                    <p class="product-model">${escapeHtml(p.model_number)}</p>
                                    <p class="product-price">
${p.sale_price && Number(p.sale_price) < Number(p.price) ? `<span class="original">₹${Number(p.price).toLocaleString()}</span> ₹${Number(p.sale_price).toLocaleString()}` : `₹${Number(p.price).toLocaleString()}`}
                                    </p>
                                    <div class="card-actions">
                                        <button class="btn-view-more" onclick="window.location='/product/${p.slug || p.id}'">View More</button>
                                        <button class="btn-cart" onclick="addToCart(${p.id}, this)" data-id="${p.id}"
                                                data-model="${escapeHtml(p.model_number)}" data-name="${escapeHtml(p.name || p.model_number)}"
                                                data-brand="${escapeHtml(p.brand?.name || '')}" data-price="${p.price}"
                                                data-sale-price="${p.sale_price || ''}" data-image="${escapeHtml(p.image_url || '')}"
                                                data-stock="${p.stock_quantity ?? 0}" title="Add to Cart" aria-label="Add to Cart">
                                            <i class="fas fa-shopping-bag"></i>
                                        </button>
                                        <button class="btn-compare" data-id="${p.id}" onclick="toggleCompare(${p.id}, this)" title="Add to compare" aria-label="Add to compare"><i class="fas fa-scale-balanced"></i></button>
                                    </div>
                                </div>
                            </div>
                        `;
                        }).join('');
                        if (container) {
                            container.insertAdjacentHTML('afterbegin', html);
                            setTimeout(() => {
                                document.querySelectorAll('.sync-flash').forEach(el => el.classList.remove('sync-flash'));
                            }, 2000);
                        }
                    }
                } else {
                    filterProducts();
                }
                pendingUpdates = [];
            }
            updateSyncStatus();
        }

        function updateSyncStatus() {
            const syncBtn = document.getElementById('syncStatusBtn');
            if (syncBtn) {
                const lastSync = localStorage.getItem('gem_last_sync');
                if (lastSync) {
                    const diff = Date.now() - new Date(lastSync).getTime();
                    const mins = Math.floor(diff / 60000);
                    syncBtn.title = 'Last synced ' + (mins < 1 ? 'just now' : mins + 'm ago');
                }
            }
        }

        // ─── Compare Feature ───
        const COMPARE_KEY = 'gem_compare';
        const COMPARE_MAX = 2;
        let comparePool = {};

        function buildCompareSnapshot(p) {
            return {
                id: p.id,
                brand_name: p.brand?.name || '—',
                model_number: p.model_number,
                name: p.name || p.model_number,
                category: p.category || '—',
                gender: p.gender || '—',
                frame_shape: p.frame_shape || '—',
                frame_material: p.frame_material || '—',
                frame_color: p.frame_color || '—',
                frame_size: p.frame_size || '—',
                price: p.price,
                sale_price: p.sale_price || null,
                image_url: p.image_url || '',
                stock_quantity: p.stock_quantity ?? '—',
                is_on_sale: !!p.is_on_sale,
                is_new_arrival: !!p.is_new_arrival,
            };
        }

        function getCompareList() {
            try {
                const raw = localStorage.getItem(COMPARE_KEY);
                const list = raw ? JSON.parse(raw) : [];
                return Array.isArray(list) ? list : [];
            } catch (e) { return []; }
        }

        function saveCompareList(list) {
            localStorage.setItem(COMPARE_KEY, JSON.stringify(list));
        }

        function isInCompare(id) {
            return getCompareList().some(c => String(c.id) === String(id));
        }

        function toggleCompare(id, btn) {
            let list = getCompareList();
            const idx = list.findIndex(c => String(c.id) === String(id));
            if (idx > -1) {
                list.splice(idx, 1);
                saveCompareList(list);
                if (btn) btn.classList.remove('active');
            } else {
                if (list.length >= COMPARE_MAX) {
                    alert('You can compare up to ' + COMPARE_MAX + ' products. Remove one first.');
                    return;
                }
                let snap = comparePool[id];
                if (!snap) {
                    fetch(`/api/v1/inventory/${id}`)
                        .then(r => r.json())
                        .then(res => {
                            if (res.data) {
                                comparePool[id] = buildCompareSnapshot(res.data);
                                toggleCompare(id, btn);
                            }
                        })
                        .catch(() => {});
                    return;
                }
                list.push(snap);
                saveCompareList(list);
                if (btn) btn.classList.add('active');
            }
            updateCompareTray();
            updateCompareButtons();
        }

        function updateCompareTray() {
            const tray = document.getElementById('compareTray');
            const countEl = document.getElementById('compareCount');
            const n = getCompareList().length;
            if (tray) tray.style.display = n > 0 ? 'flex' : 'none';
            if (countEl) countEl.textContent = n;
        }

        function updateCompareButtons() {
            document.querySelectorAll('.btn-compare').forEach(btn => {
                btn.classList.toggle('active', isInCompare(btn.dataset.id));
            });
        }

        function clearCompare() {
            saveCompareList([]);
            updateCompareTray();
            updateCompareButtons();
            const body = document.getElementById('compareTable');
            if (body) body.innerHTML = '';
        }

        function openCompare() {
            const list = getCompareList();
            if (list.length === 0) return;
            const wrap = document.getElementById('compareOverlay');
            if (!wrap) return;
            renderCompareTable(list);
            wrap.classList.add('open');
            document.body.style.overflow = 'hidden';
        }

        function closeCompare() {
            const wrap = document.getElementById('compareOverlay');
            if (wrap) wrap.classList.remove('open');
            document.body.style.overflow = '';
        }

        function removeFromCompare(id) {
            const list = getCompareList().filter(c => String(c.id) !== String(id));
            saveCompareList(list);
            renderCompareTable(list);
            updateCompareTray();
            updateCompareButtons();
        }

        function renderCompareTable(list) {
            const body = document.getElementById('compareTable');
            if (!body) return;
            if (list.length === 0) {
                body.innerHTML = '<div class="compare-empty"><i class="fas fa-scale-balanced"></i><p>No products to compare.</p></div>';
                return;
            }
            const money = v => '₹' + Number(v).toLocaleString('en-IN');
            const img = c => c.image_url
                ? `<img src="${escapeHtml(c.image_url)}" alt="${escapeHtml(c.model_number)}" loading="lazy">`
                : '<div class="compare-noimg"><i class="fas fa-glasses"></i></div>';

            const headCells = '<div class="cg-corner"></div>' + list.map(c => `
                <div class="cg-head">
                    <button class="compare-remove" onclick="removeFromCompare(${c.id})" title="Remove" aria-label="Remove">&times;</button>
                    <div class="compare-col-img">${img(c)}</div>
                    <div class="compare-col-name">${escapeHtml(c.brand_name)}</div>
                    <div class="compare-col-model">${escapeHtml(c.model_number)}</div>
                    ${c.is_on_sale ? '<span class="cmp-badge cmp-sale">SALE</span>' : ''}
                    ${c.is_new_arrival ? '<span class="cmp-badge cmp-new">NEW</span>' : ''}
                </div>`).join('');

            const rows = [
                ['Brand', 'brand_name'],
                ['Model', 'model_number'],
                ['Category', 'category'],
                ['Gender', 'gender'],
                ['Shape', 'frame_shape'],
                ['Material', 'frame_material'],
                ['Color', 'frame_color'],
                ['Size', 'frame_size'],
                ['Price', null],
                ['Stock', 'stock_quantity'],
            ].map(([label, field]) => {
                const cells = list.map(c => {
                    const htmlFor = (inner) => `<div class="cg-cell">${inner}</div>`;
                    if (label === 'Price') {
                        const p = Number(c.price);
                        const s = c.sale_price ? Number(c.sale_price) : null;
                        return (s && s < p)
                            ? htmlFor(`<span class="compare-sale"><s>${money(p)}</s> ${money(s)}</span>`)
                            : htmlFor(money(p));
                    }
                    const v = c[field];
                    const clean = (v === '—' || v == null || v === '') ? '<span class="cmp-na">—</span>' : escapeHtml(String(v));
                    return htmlFor(clean);
                }).join('');
                return `<div class="cg-label">${label}</div>${cells}`;
            }).join('');

            body.innerHTML = `
                <div class="compare-grid" style="grid-template-columns:minmax(120px,160px) repeat(${list.length}, minmax(150px,1fr))">
                    ${headCells}
                    ${rows}
                </div>`;
        }

        // ─── AJAX Search Overlay ───
        let searchTimer = null;

        function openSearch() {
            const wrap = document.getElementById('searchOverlay');
            const input = document.getElementById('searchOverlayInput');
            const results = document.getElementById('searchResults');
            const viewAll = document.getElementById('searchViewAllBtn');
            if (!wrap) return;
            wrap.classList.add('open');
            document.body.style.overflow = 'hidden';
            if (input) input.value = '';
            if (results) results.innerHTML = '<p class="search-hint">Type a model number or BQ number to search.</p>';
            if (viewAll) viewAll.style.display = 'none';
            setTimeout(() => input && input.focus(), 100);
        }

        function closeSearch() {
            const wrap = document.getElementById('searchOverlay');
            if (wrap) wrap.classList.remove('open');
            document.body.style.overflow = '';
        }

        function runSearch(query) {
            clearTimeout(searchTimer);
            const results = document.getElementById('searchResults');
            const viewAll = document.getElementById('searchViewAllBtn');
            if (!results) return;
            const q = (query || '').trim();
            if (!q) {
                results.innerHTML = '<p class="search-hint">Type a model number or BQ number to search.</p>';
                if (viewAll) viewAll.style.display = 'none';
                return;
            }
            results.innerHTML = '<div class="loading-spinner"><i class="fas fa-spinner fa-spin"></i></div>';
            searchTimer = setTimeout(function() {
                fetch('/api/v1/inventory?search=' + encodeURIComponent(q) + '&per_page=8')
                    .then(function(r) { return r.json(); })
                    .then(function(data) { renderSearchResults(data.data || [], q); })
                    .catch(function() {
                        results.innerHTML = '<p class="error-msg">Search unavailable. Check your connection.</p>';
                        if (viewAll) viewAll.style.display = 'none';
                    });
            }, 300);
        }

        function renderSearchResults(items, query) {
            const results = document.getElementById('searchResults');
            const viewAll = document.getElementById('searchViewAllBtn');
            const input = document.getElementById('searchOverlayInput');
            if (!results) return;
            const q = String(query != null && query !== '' ? query : (input ? input.value : '')).trim();
            if (!items.length) {
                results.innerHTML = '<p class="search-empty"><i class="fas fa-search"></i> No products match "' + escapeHtml(q) + '"</p>';
                if (viewAll) viewAll.style.display = 'none';
                return;
            }
            results.innerHTML = items.map(function(p) {
                return `
                    <div class="search-result" onclick="openResult(${p.id}, '${escapeHtml(p.slug || '')}')">
                        <img src="${escapeHtml(p.image_url || 'https://via.placeholder.com/60x60?text=Frame')}" alt="${escapeHtml(p.name || p.model_number)}" loading="lazy">
                        <div class="search-result-info">
                            <span class="search-result-brand">${escapeHtml(p.brand?.name || 'Luxury Brand')}</span>
                            <span class="search-result-model">${escapeHtml(p.model_number)}</span>
                            <span class="search-result-price">₹${Number(p.price).toLocaleString()}</span>
                        </div>
                    </div>`;
            }).join('');
            if (viewAll) viewAll.style.display = 'block';
        }

        function openResult(id, slug) {
            closeSearch();
            showProductDetail(id, slug);
        }

        function viewAllSearchResults() {
            const input = document.getElementById('searchOverlayInput');
            const q = input ? input.value.trim() : '';
            if (!q) return;
            const gridSearch = document.getElementById('searchInput');
            if (gridSearch) gridSearch.value = q;
            closeSearch();
            setFilter({ search: q });
        }

        // ─── Cart Feature (localStorage) ───
        const CART_KEY = 'gem_cart';

        function getCart() {
            try {
                const raw = localStorage.getItem(CART_KEY);
                const list = raw ? JSON.parse(raw) : [];
                return Array.isArray(list) ? list : [];
            } catch (e) { return []; }
        }

        function saveCart(list) {
            localStorage.setItem(CART_KEY, JSON.stringify(list));
        }

        function addToCart(id, btn) {
            let snap = comparePool[id];
            if (snap) { pushToCart(snap); return; }
            if (btn) {
                const s = {
                    id: id,
                    brand_name: btn.dataset.brand || '—',
                    model_number: btn.dataset.model || '',
                    name: btn.dataset.name || btn.dataset.model || '',
                    price: btn.dataset.price,
                    sale_price: btn.dataset.salePrice || null,
                    image_url: btn.dataset.image || '',
                    stock_quantity: btn.dataset.stock
                };
                pushToCart(s);
                return;
            }
            fetch(`/api/v1/inventory/${id}`)
                .then(r => r.json())
                .then(res => { if (res.data) { comparePool[id] = buildCompareSnapshot(res.data); pushToCart(comparePool[id]); } })
                .catch(() => {});
        }

        function pushToCart(snap) {
            let list = getCart();
            const idx = list.findIndex(c => String(c.id) === String(snap.id));
            if (idx > -1) {
                list[idx].qty = (parseInt(list[idx].qty) || 1) + 1;
            } else {
                list.push(Object.assign({ qty: 1 }, snap));
            }
            saveCart(list);
            updateCartUI();
            showCartToast(snap.name || snap.model_number);
        }

        function setCartQty(id, qty) {
            let list = getCart();
            const idx = list.findIndex(c => String(c.id) === String(id));
            if (idx === -1) return;
            qty = Math.max(1, parseInt(qty) || 1);
            list[idx].qty = qty;
            saveCart(list);
            updateCartUI();
        }

        function removeCartItem(id) {
            saveCart(getCart().filter(c => String(c.id) !== String(id)));
            updateCartUI();
        }

        function resetCart() {
            saveCart([]);
            updateCartUI();
            closeCart();
            window.location.href = '/';
        }

        function cartCount() {
            return getCart().reduce((n, c) => n + (parseInt(c.qty) || 1), 0);
        }

        function cartSubtotal() {
            return getCart().reduce((n, c) => {
                const price = c.sale_price && Number(c.sale_price) > 0 ? c.sale_price : c.price;
                return n + (Number(price) || 0) * (parseInt(c.qty) || 1);
            }, 0);
        }

        function cartMoney(v) {
            return '₹' + Number(v).toLocaleString('en-IN');
        }

        function updateCartUI() {
            const n = cartCount();
            const badge = document.getElementById('cartCount');
            if (badge) {
                badge.textContent = n;
                badge.style.display = n > 0 ? 'flex' : 'none';
            }
            const hcount = document.getElementById('cartHeaderCount');
            if (hcount) hcount.textContent = n > 0 ? `${n} item${n !== 1 ? 's' : ''}` : '0 items';
            const body = document.getElementById('cartBody');
            const footer = document.getElementById('cartFooter');
            const list = getCart();
            if (!body) return;
            if (!list.length) {
                body.innerHTML = `
                    <div class="cart-empty">
                        <div class="cart-empty-icon"><i class="fas fa-shopping-bag"></i></div>
                        <h4>Your cart is empty</h4>
                        <p>Discover luxury frames crafted for you.</p>
                        <button class="cart-continue-btn" onclick="closeCart()">Continue Shopping</button>
                    </div>`;
                if (footer) footer.style.display = 'none';
                return;
            }
            body.innerHTML = list.map(c => {
                const unit = c.sale_price && Number(c.sale_price) > 0 ? c.sale_price : c.price;
                const qty = parseInt(c.qty) || 1;
                return `
                    <div class="cart-item">
                        <div class="cart-item-img">
                            <img src="${escapeHtml(c.image_url || 'https://via.placeholder.com/80x60?text=Frame')}" alt="${escapeHtml(c.model_number)}">
                        </div>
                        <div class="cart-item-info">
                            <span class="cart-item-brand">${escapeHtml(c.brand_name || '')}</span>
                            <span class="cart-item-model">${escapeHtml(c.model_number)}</span>
                            <span class="cart-item-price">${cartMoney(unit)}${(c.sale_price && Number(c.sale_price) > 0 && Number(c.sale_price) < Number(c.price)) ? `<small class="cart-item-old">${cartMoney(c.price)}</small>` : ''}</span>
                            <div class="cart-item-controls">
                                <div class="cart-qty">
                                    <button onclick="setCartQty(${c.id}, ${qty - 1})" aria-label="Decrease">&minus;</button>
                                    <span>${qty}</span>
                                    <button onclick="setCartQty(${c.id}, ${qty + 1})" aria-label="Increase">&plus;</button>
                                </div>
                                <button class="cart-remove" onclick="removeCartItem(${c.id})" title="Remove" aria-label="Remove"><i class="fas fa-trash-alt"></i></button>
                            </div>
                        </div>
                        <span class="cart-item-total">${cartMoney(Number(unit) * qty)}</span>
                    </div>`;
            }).join('');
            if (footer) {
                const sub = document.getElementById('cartSubtotal');
                if (sub) sub.textContent = cartMoney(cartSubtotal());
                const ic = document.getElementById('cartItemCount');
                if (ic) ic.textContent = `${n} item${n !== 1 ? 's' : ''}`;
                footer.style.display = 'block';
            }
        }

        let cartToastTimer = null;
        function showCartToast(name) {
            let toast = document.getElementById('cartToast');
            if (!toast) {
                toast = document.createElement('div');
                toast.id = 'cartToast';
                toast.className = 'cart-toast';
                document.body.appendChild(toast);
            }
            toast.innerHTML = `<i class="fas fa-check-circle"></i> <span>Added to cart</span>`;
            toast.classList.add('show');
            clearTimeout(cartToastTimer);
            cartToastTimer = setTimeout(() => toast.classList.remove('show'), 1800);
        }

        function openCart() {
            const drawer = document.getElementById('cartDrawer');
            const filter = document.getElementById('filterDrawer');
            if (drawer) drawer.classList.add('open');
            if (filter) filter.classList.remove('open');
            document.getElementById('overlay').classList.add('active');
            document.body.style.overflow = 'hidden';
            updateCartUI();
        }

        function closeCart() {
            const drawer = document.getElementById('cartDrawer');
            if (drawer) drawer.classList.remove('open');
            const ov = document.getElementById('overlay');
            if (ov && !drawer.classList.contains('open') && !document.getElementById('filterDrawer').classList.contains('open')) {
                ov.classList.remove('active');
                document.body.style.overflow = '';
            }
        }

        // ─── Original Functions ───
        function openDrawer() {
            drawer.classList.add('open');
            overlay.classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeDrawer() {
            drawer.classList.remove('open');
            filterDrawer.classList.remove('open');
            if (cartDrawer) cartDrawer.classList.remove('open');
            overlay.classList.remove('active');
            document.body.style.overflow = '';
        }

        function openFilterDrawer() {
            if (cartDrawer) cartDrawer.classList.remove('open');
            filterDrawer.classList.add('open');
            overlay.classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        document.querySelectorAll('.drawer-item.has-submenu').forEach(item => {
            const toggle = item.querySelector('.submenu-toggle');
            const submenu = item.querySelector('.submenu');
            if (toggle && submenu) {
                toggle.addEventListener('click', function(e) {
                    e.stopPropagation();
                    item.classList.toggle('expanded');
                    if (item.classList.contains('expanded')) {
                        submenu.style.maxHeight = submenu.scrollHeight + 'px';
                    } else {
                        submenu.style.maxHeight = '0';
                    }
                });
            }
        });

        function loadBrands() {
            fetch('/api/v1/brands')
                .then(r => r.json())
                .then(res => {
                    const brands = res.data || [];
                    const tickerList = document.getElementById('brandTickerList');
                    if (tickerList) {
                        tickerList.innerHTML = brands.map(b =>
                            `<span class="ticker-brand" data-brand-id="${b.id}">${escapeHtml(b.name)}</span>`
                        ).join('');
                        const ticker = document.querySelector('.brand-ticker');
if (ticker) {
                        ticker.querySelectorAll('.brand-ticker-inner:not(#brandTickerList)').forEach(n => n.remove());
                        const clone = tickerList.cloneNode(true);
                        clone.removeAttribute('id');
                        ticker.appendChild(clone);
                    }
                    }
                    const submenu = document.getElementById('brandSubmenu');
                    if (submenu) {
                        submenu.innerHTML = brands.map(b =>
                            `<li><a href="#" class="brand-filter-link" data-brand-id="${b.id}">${escapeHtml(b.name)}</a></li>`
                        ).join('');
                    }
                    document.querySelectorAll('[data-brand-id]').forEach(el => {
                        el.addEventListener('click', function(e) {
                            e.preventDefault();
                            setFilter({ brand_id: this.dataset.brandId });
                        });
                    });
                })
                .catch(() => {});
        }

        function escapeHtml(str) {
            return String(str == null ? '' : str)
                .replace(/&/g, '&amp;').replace(/</g, '&lt;')
                .replace(/>/g, '&gt;').replace(/"/g, '&quot;');
        }

        function updateFilterOptions(f) {
            if (!f || typeof f !== 'object') return;
            const setSelect = (id, entries, emptyLabel, fmt) => {
                const sel = document.getElementById(id);
                if (!sel) return;
                const current = sel.value;
                sel.innerHTML = '<option value="">' + emptyLabel + '</option>';
                (entries || []).forEach(entry => {
                    const pair = Array.isArray(entry) ? entry : [entry, entry];
                    const opt = document.createElement('option');
                    opt.value = pair[0];
                    opt.textContent = fmt ? fmt(pair[1]) : pair[1];
                    sel.appendChild(opt);
                });
                let found = false;
                sel.querySelectorAll('option').forEach(o => { if (o.value === current) found = true; });
                if (found) {
                    sel.value = current;
                } else if (current) {
                    const opt = document.createElement('option');
                    opt.value = current;
                    opt.textContent = current;
                    sel.appendChild(opt);
                    sel.value = current;
                }
            };
            setSelect('category', f.categories, 'All Categories', v => v.charAt(0).toUpperCase() + v.slice(1).replace(/_/g, ' '));
            setSelect('gender', f.genders, 'All', v => v.charAt(0) + v.slice(1).toLowerCase());
            setSelect('brand', Object.entries(f.brands || {}), 'All Brands');
            setSelect('shape', f.frame_shapes, 'All', v => v.charAt(0).toUpperCase() + v.slice(1).toLowerCase());
            setSelect('material', f.frame_materials, 'All', v => v.charAt(0).toUpperCase() + v.slice(1).toLowerCase());
            setSelect('color', f.frame_colors, 'All');
            setSelect('size', f.frame_sizes, 'All');
            if (f.price_range) {
                const minEl = document.getElementById('min_price');
                const maxEl = document.getElementById('max_price');
                if (minEl) minEl.placeholder = 'Min ₹' + f.price_range.min;
                if (maxEl) maxEl.placeholder = 'Max ₹' + f.price_range.max;
            }
        }

        let currentPage = 1;
        let lastPage = 1;
        let totalProducts = 0;

        function filterProducts() {
            const form = document.getElementById('filterForm');
            const params = new URLSearchParams(new FormData(form));
            if (window.categoryFilter && !params.get('category')) params.set('category', window.categoryFilter);
            params.set('page', 1);
            params.set('per_page', 24);
            currentPage = 1;
            const container = document.getElementById('productGrid');
            container.innerHTML = '<div class="loading-spinner"><i class="fas fa-spinner fa-spin"></i></div>';

            fetch(`/api/v1/inventory?${params.toString()}`)
                .then(r => r.json())
                .then(data => {
                    lastPage = data.meta?.last_page || 1;
                    totalProducts = data.meta?.total || 0;
                    renderProducts(data.data);
                    updateLoadMore();
                    updateProductCount();
                    updateFilterOptions(data.meta?.filters);
                })
                .catch(() => {
                    container.innerHTML = '<p class="error-msg">Failed to load products.</p>';
                });
        }

        function loadMore() {
            if (currentPage >= lastPage) return;
            const form = document.getElementById('filterForm');
            const params = new URLSearchParams(new FormData(form));
            if (window.categoryFilter && !params.get('category')) params.set('category', window.categoryFilter);
            params.set('page', currentPage + 1);
            params.set('per_page', 24);
            const btn = document.getElementById('loadMoreBtn');
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Loading...';
            }

            fetch(`/api/v1/inventory?${params.toString()}`)
                .then(r => r.json())
                .then(data => {
                    currentPage++;
                    lastPage = data.meta?.last_page || 1;
                    totalProducts = data.meta?.total || 0;
                    renderProducts(data.data, true);
                    updateLoadMore();
                    updateProductCount();
                })
                .catch(() => {})
                .finally(() => {
                    if (btn) {
                        btn.disabled = false;
                        btn.innerHTML = 'Load More';
                    }
                });
        }

        function updateLoadMore() {
            const btn = document.getElementById('loadMoreBtn');
            if (!btn) return;
            if (currentPage < lastPage) {
                btn.style.display = 'block';
            } else {
                btn.style.display = 'none';
            }
        }

        function updateProductCount() {
            const el = document.getElementById('productCount');
            if (el) el.textContent = `Showing ${Math.min(currentPage * 24, totalProducts)} of ${totalProducts} frames`;
        }

        function renderProducts(products, append) {
            const container = document.getElementById('productGrid');
            if (!products || !products.length) {
                if (!append) container.innerHTML = '<div class="empty-state"><i class="fas fa-glasses"></i><p>No frames found</p></div>';
                return;
            }
            const html = products.map(p => {
                comparePool[p.id] = buildCompareSnapshot(p);
                return `
                <div class="product-card">
                    <div class="product-image">
                        <img src="${escapeHtml(p.image_url || 'https://via.placeholder.com/300x200?text=Frame')}" alt="${escapeHtml(p.name || p.model_number)}" loading="lazy">
                        ${p.is_on_sale ? '<span class="sale-badge">SALE</span>' : ''}
                        ${p.is_new_arrival ? '<span class="new-badge">NEW</span>' : ''}
                    </div>
                    <div class="product-info">
                        <h3 class="product-brand">${escapeHtml(p.brand?.name || 'Luxury Brand')}</h3>
                        <p class="product-model">${escapeHtml(p.model_number)}</p>
                        <p class="product-price">
                            ${p.sale_price && Number(p.sale_price) < Number(p.price) ? `<span class="original">₹${Number(p.price).toLocaleString()}</span> ₹${Number(p.sale_price).toLocaleString()}` : `₹${Number(p.price).toLocaleString()}`}
                        </p>
                        <div class="card-actions">
                            <button class="btn-view-more" onclick="window.location='/product/${p.slug || p.id}'">View More</button>
                            <button class="btn-cart" onclick="addToCart(${p.id}, this)" data-id="${p.id}"
                                    data-model="${escapeHtml(p.model_number)}" data-name="${escapeHtml(p.name || p.model_number)}"
                                    data-brand="${escapeHtml(p.brand?.name || '')}" data-price="${p.price}"
                                    data-sale-price="${p.sale_price || ''}" data-image="${escapeHtml(p.image_url || '')}"
                                    data-stock="${p.stock_quantity ?? 0}" title="Add to Cart" aria-label="Add to Cart">
                                <i class="fas fa-shopping-bag"></i>
                            </button>
                            <button class="btn-compare" data-id="${p.id}" onclick="toggleCompare(${p.id}, this)" title="Add to compare" aria-label="Add to compare"><i class="fas fa-scale-balanced"></i></button>
                        </div>
                    </div>
                </div>
            `;
            }).join('');
            if (append) {
                container.insertAdjacentHTML('beforeend', html);
            } else {
                container.innerHTML = html;
            }
            updateCompareButtons();
        }

        function setFilter(overrides) {
            const form = document.getElementById('filterForm');
            if (overrides && typeof overrides === 'object') {
                if (overrides.reset) {
                    form.reset();
                    delete overrides.reset;
                }
                Object.entries(overrides).forEach(([key, value]) => {
                    const el = form.elements[key];
                    if (!el) return;
                    if (el.type === 'checkbox') {
                        el.checked = !!value;
                    } else {
                        el.value = value;
                    }
                });
            }
            closeDrawer();
            filterProducts();
            const section = document.getElementById('productsSection');
            if (section) section.scrollIntoView({ behavior: 'smooth' });
        }

        function showProductDetail(id, slug) {
            window.location.href = '/product/' + (slug || id);
        }

        document.addEventListener('DOMContentLoaded', function() {
            if (window.categoryFilter) {
                const categoryInput = document.getElementById('category');
                if (categoryInput) categoryInput.value = window.categoryFilter;
                const catSection = document.getElementById('categoryFilterSection');
                if (catSection) catSection.style.display = 'none';
            }
            filterProducts();
            startAutoSync();
            loadBrands();
            updateCompareTray();
            updateCompareButtons();
            updateCartUI();

            document.querySelectorAll('.search-overlay').forEach(ov => {
                ov.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape') closeSearch();
                });
            });
            const searchOverlayInput = document.getElementById('searchOverlayInput');
            if (searchOverlayInput) {
                searchOverlayInput.addEventListener('input', function() { runSearch(this.value); });
                searchOverlayInput.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter') { e.preventDefault(); viewAllSearchResults(); }
                });
            }
            const searchViewAllBtn = document.getElementById('searchViewAllBtn');
            if (searchViewAllBtn) searchViewAllBtn.addEventListener('click', viewAllSearchResults);

            document.querySelectorAll('.drawer-link[data-filter]').forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    setFilter(JSON.parse(this.dataset.filter));
                });
            });

            document.querySelectorAll('[data-shape]').forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const shape = this.dataset.shape;
                    const fallback = this.dataset.category || 'eyeglasses';
                    const shapeSel = document.getElementById('shape');
                    const norm = s => s.toLowerCase().replace(/[^a-z0-9]/g, '');
                    let matched = '';
                    if (shapeSel) {
                        const opt = Array.from(shapeSel.options).find(o => norm(o.value) === norm(shape) || norm(o.textContent) === norm(shape));
                        if (opt) matched = opt.value;
                    }
                    if (matched) {
                        setFilter({ frame_shape: matched });
                    } else {
                        setFilter({ category: fallback });
                    }
                });
            });

            const searchBtn = document.getElementById('searchBtn');
            if (searchBtn) {
                searchBtn.addEventListener('click', openSearch);
            }
        });

        // Re-sync when coming back online
        window.addEventListener('online', function() {
            document.getElementById('offlineIndicator')?.classList.remove('show');
            lastSyncTime = localStorage.getItem('gem_last_sync') || '';
            pollForUpdates();
        });

        window.addEventListener('offline', function() {
            document.getElementById('offlineIndicator')?.classList.add('show');
        });

        if (!navigator.onLine) {
            document.getElementById('offlineIndicator')?.classList.add('show');
        }
    </script>
    @stack('scripts')
</body>
</html>
