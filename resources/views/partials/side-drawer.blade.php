<nav id="sideDrawer" class="side-drawer">
    <div class="drawer-header">
        <h2 class="drawer-title">Menu</h2>
        <button class="drawer-close" onclick="closeDrawer()">&times;</button>
    </div>

    <ul class="drawer-nav">
        @forelse($menuItems as $item)
            @if($item->type === 'category')
                @php $shapes = array_values(array_filter(explode('|', $menuShapesByCategory[$item->ref] ?? ''))); @endphp
                <li class="drawer-item {{ count($shapes) ? 'has-submenu' : '' }}">
                    <a href="#" class="drawer-link {{ count($shapes) ? 'submenu-toggle' : '' }}" data-filter='{"category":"{{ $item->ref }}"}'>
                        <span>{{ $item->label }}</span>
                        <span class="expand-icon">+</span>
                    </a>
                    @if(count($shapes))
                    <ul class="submenu">
                        @foreach($shapes as $shape)
                        <li><a href="#" data-shape="{{ $shape }}" data-category="{{ $item->ref }}">{{ $shape }}</a></li>
                        @endforeach
                    </ul>
                    @endif
                </li>
            @elseif($item->type === 'brand')
                <li class="drawer-item">
                    <a href="#" class="drawer-link" data-filter='{"brand_id":{{ $item->ref }}}'>
                        <span>{{ $item->label }}</span>
                        <span class="expand-icon">+</span>
                    </a>
                </li>
            @elseif($item->type === 'brands')
                <li class="drawer-item has-submenu">
                    <a href="#" class="drawer-link submenu-toggle">
                        <span>{{ $item->label }}</span>
                        <span class="expand-icon">+</span>
                    </a>
                    <ul class="submenu" id="brandSubmenu"></ul>
                </li>
            @elseif($item->type === 'collection')
                <li class="drawer-item">
                    <a href="#" class="drawer-link" data-filter='{{ $item->ref }}'>
                        <span>{{ $item->label }}</span>
                        <span class="expand-icon">+</span>
                    </a>
                </li>
            @elseif($item->type === 'custom')
                <li class="drawer-item">
                    <a href="{{ $item->link_url ?? '#' }}" class="drawer-link">
                        <span>{{ $item->label }}</span>
                        <span class="expand-icon">+</span>
                    </a>
                </li>
            @elseif($item->type === 'all')
                <li class="drawer-item">
                    <a href="#" class="drawer-link" data-filter='{"reset":1}'>
                        <span>{{ $item->label }}</span>
                        <span class="expand-icon">+</span>
                    </a>
                </li>
            @endif
        @empty
            <li class="drawer-item">
                <a href="#" class="drawer-link" data-filter='{}'>
                    <span>ALL PRODUCTS</span>
                    <span class="expand-icon">+</span>
                </a>
            </li>
        @endforelse
    </ul>

    <div class="drawer-footer">
        <div class="social-icons">
            <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
            <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
            <a href="#" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
        </div>
        <p class="drawer-footer-text">Your Vision, Our Passion</p>
    </div>
</nav>