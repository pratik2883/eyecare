<header class="admin-header">
    <button class="sidebar-toggle" onclick="toggleSidebar()" aria-label="Toggle Sidebar">
        <i class="fas fa-bars"></i>
    </button>
    <div class="header-search">
        <i class="fas fa-search"></i>
        <input type="text" placeholder="Search products, brands..." id="globalSearch" data-search-url="{{ route('admin.inventory.index') }}" onkeydown="triggerGlobalSearch(event)">
    </div>
    <div class="header-actions">
        <a href="/" class="header-btn" target="_blank" title="View Store">
            <i class="fas fa-external-link-alt"></i>
        </a>
        <div class="header-user">
            <div class="user-avatar">{{ substr(Auth::user()->name, 0, 1) }}</div>
            <div class="user-info">
                <span class="user-name">{{ Auth::user()->name }}</span>
                <span class="user-role">Store Manager</span>
            </div>
        </div>
    </div>
</header>
