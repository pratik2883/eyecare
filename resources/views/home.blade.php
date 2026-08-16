@extends('layouts.app')

@section('content')
    {{-- Hero Section --}}
    <section class="hero-section">
        <div class="hero-slider">
            @forelse($banners as $banner)
            <div class="hero-slide @if($loop->first) active @endif"
                 style="background: {{ $banner->image_url ? 'url(' . $banner->image_url . ') center/cover no-repeat' : ($banner->background_color ?? 'linear-gradient(135deg, #C8102E, #9A0C22)') }};">
                <div class="hero-overlay"></div>
                <div class="hero-content">
                    @if($banner->title)<h2 class="hero-title">{{ $banner->title }}</h2>@endif
                    @if($banner->subtitle)<p class="hero-subtitle">{{ $banner->subtitle }}</p>@endif
                    @if($banner->link_url)<a href="{{ $banner->link_url }}" class="hero-cta">Shop Now</a>@endif
                </div>
            </div>
            @empty
            <div class="hero-slide active" style="background: linear-gradient(135deg, #C8102E 0%, #9A0C22 50%, #1A1A1A 100%);">
                <div class="hero-content">
                    <h2 class="hero-title">Welcome to {{ setting('store_name', 'EyeCare Studio') }}</h2>
                    <p class="hero-subtitle">Discover the world's finest frames curated for the discerning</p>
                </div>
                <div class="hero-image">
                    <i class="fas fa-glasses" style="font-size: 12rem; opacity: 0.15; color: #fff;"></i>
                </div>
            </div>
            @endforelse
        </div>
        @if($banners->count() > 1)
        <div class="hero-indicators">
            @foreach($banners as $i => $banner)
            <span class="dot @if($i === 0) active @endif"></span>
            @endforeach
        </div>
        @endif
    </section>

    {{-- Categories Carousel --}}
    <section class="categories-section">
        <div class="section-header">
            <h2 class="section-title">{{ setting('section_categories_title', 'Categories') }}</h2>
        </div>
        <div class="categories-carousel">
            @forelse($categories as $c)
            <a class="category-card" href="/category/{{ $c->slug }}">
                <div class="category-icon"><i class="fas fa-{{ $c->icon }}"></i></div>
                <span>{{ $c->name }}</span>
                @if(($categoryCounts[$c->slug] ?? 0) > 0)
                <small class="category-count">{{ $categoryCounts[$c->slug] }} items</small>
                @endif
            </a>
            @empty
            <a class="category-card" href="#">
                <div class="category-icon"><i class="fas fa-glasses"></i></div>
                <span>Frames</span>
            </a>
            @endforelse
        </div>
    </section>

    {{-- Sales / Marketing Grid --}}
    @if($activeOffers->isNotEmpty())
    <section class="promo-section offers-section">
        <div class="section-header">
            <h2 class="section-title">{{ setting('section_offers_title', 'Offers & Highlights') }}</h2>
        </div>
        <div class="promo-grid">
            @foreach($activeOffers->take(2) as $offer)
            <a class="promo-card"
               style="background: {{ $offer->image_url ? 'url(' . $offer->image_url . ') center/cover no-repeat' : ($offer->background_gradient ?: 'linear-gradient(135deg, #C8102E, #E2364B)') }}; text-decoration: none;"
               @if($offer->link_url) href="{{ $offer->link_url }}" @else href="#" @endif>
                <h3>{{ $offer->title }}</h3>
                @if($offer->subtitle)<p>{{ $offer->subtitle }}</p>@endif
                @if($offer->tag_text)<span class="promo-tag">{{ $offer->tag_text }}</span>@endif
            </a>
            @endforeach
        </div>
    </section>
    @endif

    {{-- Product Grid --}}
    <section class="products-section" id="productsSection">
        <div class="section-header">
            <h2 class="section-title">{{ setting('section_collection_title', 'Our Collection') }}</h2>
            <button class="filter-toggle" onclick="openFilterDrawer()">
                <i class="fas fa-filter"></i> Filters
            </button>
        </div>
        <p id="productCount" class="product-count"></p>
        <div id="productGrid" class="product-grid">
            <div class="loading-spinner"><i class="fas fa-spinner fa-spin"></i></div>
        </div>
        <button id="loadMoreBtn" class="load-more-btn" onclick="loadMore()" style="display:none">Load More</button>
    </section>
@endsection

@push('scripts')
<script>
    let heroIndex = 0;
    const heroSlides = () => document.querySelectorAll('.hero-slide');
    const heroDots = () => document.querySelectorAll('.hero-indicators .dot');

    function goToSlide(index) {
        heroIndex = index;
        const slides = heroSlides();
        if (!slides.length) return;
        slides.forEach((s, i) => s.classList.toggle('active', i === index));
        heroDots().forEach((d, i) => d.classList.toggle('active', i === index));
    }

    function rotateHero() {
        const slides = heroSlides();
        if (!slides.length) return;
        goToSlide(heroIndex);
        heroIndex = (heroIndex + 1) % slides.length;
    }

    heroDots().forEach((dot, i) => {
        dot.addEventListener('click', function () {
            goToSlide(i);
            heroIndex = (i + 1) % heroSlides().length;
        });
    });

    if (heroSlides().length > 1) {
        setInterval(rotateHero, 5000);
    }
</script>
@endpush
