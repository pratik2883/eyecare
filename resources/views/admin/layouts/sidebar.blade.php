<aside class="admin-sidebar" id="adminSidebar">
    <div class="sidebar-brand">
        <h2><span class="brand-gem">EyeCare</span> <span class="brand-opt">Studio</span></h2>
        <small>Admin Panel</small>
    </div>
    <nav class="sidebar-nav">
        <a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <i class="fas fa-chart-pie"></i><span>Dashboard</span>
        </a>
        <a href="{{ route('admin.banners.index') }}" class="nav-item {{ request()->routeIs('admin.banners.*') ? 'active' : '' }}">
            <i class="fas fa-images"></i><span>Hero Banners</span>
        </a>
        <a href="{{ route('admin.promos.index') }}" class="nav-item {{ request()->routeIs('admin.promos.*') ? 'active' : '' }}">
            <i class="fas fa-ad"></i><span>Promo Grid</span>
        </a>
        <div class="nav-divider">Storefront</div>
        <a href="{{ route('admin.categories.index') }}" class="nav-item {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
            <i class="fas fa-th-large"></i><span>Categories</span>
        </a>
        <a href="{{ route('admin.menu.index') }}" class="nav-item {{ request()->routeIs('admin.menu.*') ? 'active' : '' }}">
            <i class="fas fa-bars"></i><span>Menu Management</span>
        </a>
        <div class="nav-divider">Inventory</div>
        <a href="{{ route('admin.inventory.index') }}" class="nav-item {{ request()->routeIs('admin.inventory.*') ? 'active' : '' }}">
            <i class="fas fa-glasses"></i><span>All Products</span>
        </a>
        <a href="{{ route('admin.inventory.create') }}" class="nav-item {{ request()->routeIs('admin.inventory.create') ? 'active' : '' }}">
            <i class="fas fa-plus-circle"></i><span>Add Product</span>
        </a>
        <a href="{{ route('admin.bulk-import.index') }}" class="nav-item {{ request()->routeIs('admin.bulk-import.*') ? 'active' : '' }}">
            <i class="fas fa-file-upload"></i><span>Bulk Import</span>
        </a>
        <div class="nav-divider">Attributes</div>
        <a href="{{ route('admin.attributes.brands') }}" class="nav-item {{ request()->routeIs('admin.attributes.brands*') ? 'active' : '' }}">
            <i class="fas fa-tag"></i><span>Brands</span>
        </a>
        <a href="{{ route('admin.attributes.colors') }}" class="nav-item {{ request()->routeIs('admin.attributes.colors*') ? 'active' : '' }}">
            <i class="fas fa-palette"></i><span>Colors</span>
        </a>
        <a href="{{ route('admin.attributes.shapes') }}" class="nav-item {{ request()->routeIs('admin.attributes.shapes*') ? 'active' : '' }}">
            <i class="fas fa-shapes"></i><span>Shapes</span>
        </a>
        <a href="{{ route('admin.attributes.materials') }}" class="nav-item {{ request()->routeIs('admin.attributes.materials*') ? 'active' : '' }}">
            <i class="fas fa-cube"></i><span>Materials</span>
        </a>
        <a href="{{ route('admin.attributes.normalizer') }}" class="nav-item {{ request()->routeIs('admin.attributes.normalizer*') ? 'active' : '' }}">
            <i class="fas fa-magic"></i><span>Normalizer</span>
        </a>
        <div class="nav-divider">System</div>
        <a href="{{ route('admin.sync.index') }}" class="nav-item {{ request()->routeIs('admin.sync.*') ? 'active' : '' }}">
            <i class="fas fa-sync-alt"></i><span>Sync & Network</span>
        </a>
        <a href="{{ route('admin.settings.index') }}" class="nav-item {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
            <i class="fas fa-cog"></i><span>Storefront Settings</span>
        </a>
    </nav>
    <div class="sidebar-footer">
        <form action="{{ route('admin.logout') }}" method="POST" style="margin-bottom:8px">
            @csrf
            <button type="submit" style="background:none;border:none;color:rgba(255,255,255,0.5);cursor:pointer;font-size:.75rem;font-family:var(--font-sans);display:flex;align-items:center;gap:8px;width:100%;padding:6px 0;transition:color .2s" onmouseover="this.style.color='rgba(255,255,255,0.9)'" onmouseout="this.style.color='rgba(255,255,255,0.5)'">
                <i class="fas fa-sign-out-alt"></i> Sign Out
            </button>
        </form>
        <span class="sidebar-version">v1.0.0</span>
    </div>
</aside>
