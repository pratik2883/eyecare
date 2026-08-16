@extends('layouts.app')

@section('title', $categoryTitle)

@section('content')
    {{-- Category Hero / Breadcrumb --}}
    <section class="category-hero">
        <div class="category-hero-inner">
            <nav class="breadcrumb" aria-label="Breadcrumb">
                <a href="/">Home</a>
                <span class="breadcrumb-sep">/</span>
                <span class="breadcrumb-current">{{ $categoryTitle }}</span>
            </nav>
            <div class="category-hero-heading">
                <div class="category-hero-icon"><i class="fas fa-{{ $categoryIcon }}"></i></div>
                <div>
                    <h1 class="category-hero-title">{{ $categoryTitle }}</h1>
                    <p class="category-hero-subtitle">{{ $categorySubtitle }}</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Product Grid --}}
    <section class="products-section" id="productsSection">
        <div class="section-header">
            <h2 class="section-title">{{ $categoryTitle }}</h2>
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
<script>window.categoryFilter = {!! json_encode($categorySlug) !!};</script>
@endpush