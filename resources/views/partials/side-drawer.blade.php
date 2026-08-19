<nav id="sideDrawer" class="side-drawer">
    <div class="drawer-header">
        <h2 class="drawer-title">Menu</h2>
        <button class="drawer-close" onclick="closeDrawer()">&times;</button>
    </div>

    <ul class="drawer-nav">
        @forelse($menuItems as $item)
            @php
                $iconTag = $item->icon
                    ? '<img class="menu-item-icon" src="' . e($item->icon) . '" alt="' . e($item->label) . '">'
                    : '';
            @endphp
            @if($item->type === 'category')
                @php $shapes = array_values(array_filter(explode('|', $menuShapesByCategory[$item->ref] ?? ''))); @endphp
                <li class="drawer-item {{ count($shapes) ? 'has-submenu' : '' }}">
                    <a href="#" class="drawer-link {{ count($shapes) ? 'submenu-toggle' : '' }}" data-filter='{"category":"{{ $item->ref }}"}'>
                        <div class="drawer-link-content">
                            {!! $iconTag !!}
                            <span>{{ $item->label }}</span>
                        </div>
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
                        <div class="drawer-link-content">
                            {!! $iconTag !!}
                            <span>{{ $item->label }}</span>
                        </div>
                        <span class="expand-icon">+</span>
                    </a>
                </li>
            @elseif($item->type === 'brands')
                <li class="drawer-item has-submenu">
                    <a href="#" class="drawer-link submenu-toggle">
                        <div class="drawer-link-content">
                            {!! $iconTag !!}
                            <span>{{ $item->label }}</span>
                        </div>
                        <span class="expand-icon">+</span>
                    </a>
                    <ul class="submenu" id="brandSubmenu"></ul>
                </li>
            @elseif($item->type === 'collection')
                <li class="drawer-item">
                    <a href="#" class="drawer-link" data-filter='{{ $item->ref }}'>
                        <div class="drawer-link-content">
                            {!! $iconTag !!}
                            <span>{{ $item->label }}</span>
                        </div>
                        <span class="expand-icon">+</span>
                    </a>
                </li>
            @elseif($item->type === 'custom')
                <li class="drawer-item">
                    <a href="{{ $item->link_url ?? '#' }}" class="drawer-link">
                        <div class="drawer-link-content">
                            {!! $iconTag !!}
                            <span>{{ $item->label }}</span>
                        </div>
                        <span class="expand-icon">+</span>
                    </a>
                </li>
            @elseif($item->type === 'all')
                <li class="drawer-item">
                    <a href="#" class="drawer-link" data-filter='{"reset":1}'>
                        <div class="drawer-link-content">
                            {!! $iconTag !!}
                            <span>{{ $item->label }}</span>
                        </div>
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
            @if(setting('social_facebook'))
            <a href="{{ setting('social_facebook') }}" target="_blank" rel="noopener" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
            @endif
            @if(setting('social_instagram'))
            <a href="{{ setting('social_instagram') }}" target="_blank" rel="noopener" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
            @endif
            @if(setting('social_youtube'))
            <a href="{{ setting('social_youtube') }}" target="_blank" rel="noopener" aria-label="YouTube"><i class="fab fa-youtube"></i></a>
            @endif
            @if(setting('social_linkedin'))
            <a href="{{ setting('social_linkedin') }}" target="_blank" rel="noopener" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
            @endif
            @if(setting('social_whatsapp'))
            <a href="{{ setting('social_whatsapp') }}" target="_blank" rel="noopener" aria-label="WhatsApp"><i class="fab fa-whatsapp"></i></a>
            @endif
            @if(setting('social_twitter'))
            <a href="{{ setting('social_twitter') }}" target="_blank" rel="noopener" aria-label="X (Twitter)"><i class="fab fa-x-twitter"></i></a>
            @endif
        </div>
        <p class="drawer-footer-text">Your Vision, Our Passion</p>
    </div>
</nav>