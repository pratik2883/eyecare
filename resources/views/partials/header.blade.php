<header class="header">
    <div class="header-inner">
        <button class="hamburger" onclick="openDrawer()" aria-label="Menu">
            <span></span><span></span><span></span>
        </button>

        <div class="header-brand">
            <h1 class="shop-name">
                <span class="shop-name-main">{{ setting('store_name', 'EyeCare Studio') }}</span>
                <span class="shop-name-est">{{ setting('store_tagline', 'Est. 1969') }}</span>
            </h1>
        </div>

        <div class="header-actions">
            <button class="icon-btn" id="searchBtn" aria-label="Search">
                <i class="fas fa-search"></i>
            </button>
            <button class="icon-btn cart-btn" aria-label="Cart" onclick="openCart()">
                <i class="fas fa-shopping-bag"></i>
                <span class="cart-badge" id="cartCount">0</span>
            </button>
            <button class="icon-btn sync-status" id="syncStatusBtn" aria-label="Sync Status" title="Sync active">
                <i class="fas fa-sync-alt"></i>
                <span class="sync-dot"></span>
            </button>
        </div>
    </div>
</header>
