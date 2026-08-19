@extends('layouts.app')

@section('title', ($product->name ?: $product->model_number) . ' — ' . setting('store_name', 'EyeCare Studio'))

@section('content')
    <div class="product-page">
        {{-- Breadcrumb --}}
        <nav class="breadcrumb" aria-label="Breadcrumb">
            <a href="/">Home</a>
            <span class="breadcrumb-sep">/</span>
            @if($product->category)
            <a href="/category/{{ $product->category }}">{{ ucfirst(str_replace('_', ' ', $product->category)) }}</a>
            <span class="breadcrumb-sep">/</span>
            @endif
            @if($product->brand)
            <a href="#" onclick="setFilter({ brand_id: {{ $product->brand_id }} }); return false;">{{ $product->brand->name }}</a>
            <span class="breadcrumb-sep">/</span>
            @endif
            <span class="breadcrumb-current">{{ $product->model_number }}</span>
        </nav>

        <div class="ppd-layout">
            {{-- Gallery --}}
            <div class="ppd-gallery">
                @php $gallery = array_merge($product->image_url ? [$product->image_url] : [], $product->additional_images ?: []); @endphp
                <div class="ppd-stage">
                    @if($gallery)
                    <img src="{{ $gallery[0] }}" alt="{{ $product->name ?: $product->model_number }}" id="ppdMainImage">
                    @else
                    <div class="ppd-noimg"><i class="fas fa-glasses"></i></div>
                    @endif
                    <div class="ppd-badges">
                        @if($product->is_on_sale)<span class="ppd-badge sale">SALE</span>@endif
                        @if($product->is_new_arrival)<span class="ppd-badge new">NEW</span>@endif
                    </div>
                </div>
                @if(count($gallery) > 1)
                <div class="ppd-thumbs">
                    @foreach($gallery as $i => $img)
                    <button class="ppd-thumb {{ $i === 0 ? 'active' : '' }}" onclick="setPdpImage(this, '{{ $img }}')">
                        <img src="{{ $img }}" alt="View {{ $i + 1 }}">
                    </button>
                    @endforeach
                </div>
                @endif
            </div>

            {{-- Info --}}
            <div class="ppd-info">
                @if($product->brand)
                <p class="ppd-brand">{{ $product->brand->name }}</p>
                @endif
                <h1 class="ppd-name">{{ $product->name ?: $product->model_number }}</h1>
                <p class="ppd-model">{{ $product->model_number }}</p>

                <div class="ppd-price-row">
                    <p class="ppd-price">
                        @if($product->sale_price && $product->sale_price < $product->price)
                        <span class="original">₹{{ number_format((float) $product->price, 0) }}</span>
                        ₹{{ number_format((float) $product->sale_price, 0) }}
                        @else
                        ₹{{ number_format((float) $product->price, 0) }}
                        @endif
                    </p>
                    @if($product->sale_price && $product->sale_price < $product->price)
                    <span class="ppd-save">{{ round((1 - $product->sale_price / $product->price) * 100) }}% OFF</span>
                    @endif
                    @php $outOfStock = $product->stock_quantity !== null && (int) $product->stock_quantity === 0; @endphp
                    <span class="ppd-stock {{ $outOfStock ? 'out' : '' }}">
                        <i class="fas {{ $outOfStock ? 'fa-times-circle' : 'fa-check-circle' }}"></i>
                        {{ $outOfStock ? 'Out of Stock' : 'In Stock' }}
                    </span>
                </div>

                <hr class="ppd-divider">

                @if($product->description)
                <div class="ppd-description">
                    <h2 class="ppd-section-title">Description</h2>
                    <p>{{ $product->description }}</p>
                </div>
                @endif

                <div>
                    <h2 class="ppd-section-title">Specifications</h2>
                    <div class="ppd-specs">
                        @foreach([
                            'category' => ['fas fa-th-large', 'Category'],
                            'gender' => ['fas fa-user', 'Gender'],
                            'frame_shape' => ['fas fa-shapes', 'Shape'],
                            'frame_material' => ['fas fa-cog', 'Material'],
                            'frame_color' => ['fas fa-palette', 'Color'],
                            'frame_size' => ['fas fa-ruler', 'Size'],
                            'lens_type' => ['fas fa-eye', 'Lens Type'],
                            'bq_number' => ['fas fa-barcode', 'BQ Number'],
                        ] as $field => [$icon, $label])
                            @if($product->$field)
                            <div class="ppd-spec">
                                <i class="ppd-spec-icon {{ $icon }}"></i>
                                <span class="ppd-spec-label">{{ $label }}</span>
                                <span class="ppd-spec-value">{{ ucfirst($product->$field) }}</span>
                            </div>
                            @endif
                        @endforeach
                    </div>
                </div>

                <div class="ppd-actions">
                    <div class="ppd-qty">
                        <button type="button" onclick="changeQty(-1)" aria-label="Decrease quantity">&minus;</button>
                        <input type="number" id="ppdQty" value="1" min="1" max="99" readonly>
                        <button type="button" onclick="changeQty(1)" aria-label="Increase quantity">&plus;</button>
                    </div>
                    <button class="ppd-btn-cart" onclick="addToCart({{ $product->id }}, this)" data-id="{{ $product->id }}"
                            data-model="{{ $product->model_number }}" data-name="{{ $product->name ?: $product->model_number }}"
                            data-brand="{{ $product->brand->name ?? '' }}" data-price="{{ $product->price }}"
                            data-sale-price="{{ $product->sale_price ?? '' }}" data-image="{{ $product->image_url ?? '' }}"
                            data-stock="{{ $product->stock_quantity ?? 0 }}">
                        <i class="fas fa-shopping-bag"></i> Add to Cart
                    </button>
                    <button class="ppd-btn-compare" id="ppdCompareBtn" data-id="{{ $product->id }}"
                            onclick="toggleComparePdp({{ $product->id }}, this)">
                        <i class="fas fa-scale-balanced"></i> Compare
                    </button>
                </div>

                @if($product->about_brand)
                <div class="ppd-about">
                    <h3 class="ppd-section-title">About {{ $product->brand->name ?? 'the Brand' }}</h3>
                    <p>{{ $product->about_brand }}</p>
                </div>
                @endif

                <div class="ppd-trust">
                    <div class="ppd-trust-item">
                        <i class="fas fa-shield-alt"></i>
                        <div><strong>{{ setting('trust_1_title', '100% Authentic') }}</strong>{{ setting('trust_1_text', 'Authorized brand dealer') }}</div>
                    </div>
                    <div class="ppd-trust-item">
                        <i class="fas fa-truck"></i>
                        <div><strong>{{ setting('trust_2_title', 'Free Fitment') }}</strong>{{ setting('trust_2_text', 'Complimentary at store') }}</div>
                    </div>
                    <div class="ppd-trust-item">
                        <i class="fas fa-tags"></i>
                        <div><strong>{{ setting('trust_3_title', 'Best Prices') }}</strong>{{ setting('trust_3_text', 'Store-exclusive offers') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Mobile sticky bar --}}
    <div class="ppd-sticky">
        <div class="ppd-sticky-price">
            @if($product->sale_price && $product->sale_price < $product->price)
            <span class="price">₹{{ number_format((float) $product->sale_price, 0) }}</span>
            <span class="old">₹{{ number_format((float) $product->price, 0) }}</span>
            @else
            <span class="price">₹{{ number_format((float) $product->price, 0) }}</span>
            @endif
        </div>
        <button class="ppd-btn-cart" onclick="addToCart({{ $product->id }}, this)" data-id="{{ $product->id }}"
                data-model="{{ $product->model_number }}" data-name="{{ $product->name ?: $product->model_number }}"
                data-brand="{{ $product->brand->name ?? '' }}" data-price="{{ $product->price }}"
                data-sale-price="{{ $product->sale_price ?? '' }}" data-image="{{ $product->image_url ?? '' }}"
                data-stock="{{ $product->stock_quantity ?? 0 }}">
            <i class="fas fa-shopping-bag"></i> Add to Cart
        </button>
    </div>

    @if($related->isNotEmpty())
    <section class="products-section ppd-related">
        <div class="section-header">
            <h2 class="section-title">You May Also Like</h2>
        </div>
        <div class="product-grid">
            @foreach($related as $rp)
            <div class="product-card">
                <div class="product-image">
                    <a href="/product/{{ $rp->slug ?? $rp->id }}">
                        <img src="{{ $rp->image_url ?? 'https://via.placeholder.com/300x200?text=Frame' }}" alt="{{ $rp->name ?: $rp->model_number }}" loading="lazy">
                    </a>
                    @if($rp->is_on_sale)<span class="sale-badge">SALE</span>@endif
                    @if($rp->is_new_arrival)<span class="new-badge">NEW</span>@endif
                </div>
                <div class="product-info">
                    <h3 class="product-brand">{{ $rp->brand->name ?? 'Luxury Brand' }}</h3>
                    <p class="product-model">{{ $rp->model_number }}</p>
                    <p class="product-price">
                        @if($rp->sale_price && $rp->sale_price < $rp->price)
                        <span class="original">₹{{ number_format((float) $rp->price, 0) }}</span> ₹{{ number_format((float) $rp->sale_price, 0) }}
                        @else
                        ₹{{ number_format((float) $rp->price, 0) }}
                        @endif
                    </p>
                    <div class="card-actions">
                        <button class="btn-view-more" onclick="window.location='/product/{{ $rp->slug ?? $rp->id }}'">View More</button>
                        <button class="btn-cart" onclick="addToCart({{ $rp->id }}, this)" data-id="{{ $rp->id }}"
                                data-model="{{ $rp->model_number }}" data-name="{{ $rp->name ?: $rp->model_number }}"
                                data-brand="{{ $rp->brand->name ?? '' }}" data-price="{{ $rp->price }}"
                                data-sale-price="{{ $rp->sale_price ?? '' }}" data-image="{{ $rp->image_url ?? '' }}"
                                data-stock="{{ $rp->stock_quantity ?? 0 }}" title="Add to Cart" aria-label="Add to Cart">
                            <i class="fas fa-shopping-bag"></i>
                        </button>
                        <button class="btn-compare" data-id="{{ $rp->id }}" onclick="toggleCompare({{ $rp->id }}, this)" title="Add to compare" aria-label="Add to compare"><i class="fas fa-scale-balanced"></i></button>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </section>
    @endif
@endsection

@push('scripts')
<script>
    function setPdpImage(btn, url) {
        const main = document.getElementById('ppdMainImage');
        if (main) main.src = url;
        document.querySelectorAll('.ppd-thumb').forEach(t => t.classList.remove('active'));
        btn.classList.add('active');
    }
    function changeQty(delta) {
        const input = document.getElementById('ppdQty');
        if (!input) return;
        let v = parseInt(input.value) || 1;
        v = Math.max(1, Math.min(99, v + delta));
        input.value = v;
    }
    function toggleComparePdp(id, btn) {
        toggleCompare(id, btn);
        const active = btn.classList.contains('active');
        btn.innerHTML = `<i class="fas fa-scale-balanced"></i> ${active ? 'In Compare' : 'Compare'}`;
    }
    document.addEventListener('DOMContentLoaded', function() {
        const btn = document.getElementById('ppdCompareBtn');
        if (btn && typeof isInCompare === 'function' && isInCompare(btn.dataset.id)) {
            btn.classList.add('active');
            btn.innerHTML = '<i class="fas fa-scale-balanced"></i> In Compare';
        }
    });
</script>
@endpush