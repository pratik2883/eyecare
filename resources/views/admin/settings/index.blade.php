@extends('admin.layouts.master')
@section('title', 'Storefront Settings')
@section('content')
<div class="page-header">
    <h1>Storefront Settings</h1>
    <a href="/" class="btn btn-secondary" target="_blank"><i class="fas fa-external-link-alt"></i> View Store</a>
</div>

<form action="{{ route('admin.settings.update') }}" method="POST">
    @csrf
    @method('PUT')

    <div class="card">
        <div class="card-header"><h3>Brand & Identity</h3></div>
        <div class="card-body">
            <p style="color:var(--text-light);font-size:.82rem;margin-top:0">Shown in the store header and browser title.</p>
            <div class="form-row">
                <div class="form-group">
                    <label>Store Name</label>
                    <input type="text" name="store_name" class="form-control" value="{{ setting('store_name', 'EyeCare Studio') }}" placeholder="EyeCare Studio">
                </div>
                <div class="form-group">
                    <label>Store Tagline</label>
                    <input type="text" name="store_tagline" class="form-control" value="{{ setting('store_tagline', 'Est. 1969') }}" placeholder="Est. 1969">
                </div>
                <div class="form-group">
                    <label>App / Browser Title</label>
                    <input type="text" name="app_name" class="form-control" value="{{ setting('app_name', 'EyeCare Studio') }}" placeholder="EyeCare Studio">
                </div>
            </div>
        </div>
    </div>

    <div class="card" style="margin-top:16px">
        <div class="card-header"><h3>Homepage Section Headings</h3></div>
        <div class="card-body">
            <div class="form-row">
                <div class="form-group">
                    <label>Categories Section Title</label>
                    <input type="text" name="section_categories_title" class="form-control" value="{{ setting('section_categories_title', 'Categories') }}">
                </div>
                <div class="form-group">
                    <label>Offers & Highlights Title</label>
                    <input type="text" name="section_offers_title" class="form-control" value="{{ setting('section_offers_title', 'Offers & Highlights') }}">
                </div>
                <div class="form-group">
                    <label>Collection Section Title</label>
                    <input type="text" name="section_collection_title" class="form-control" value="{{ setting('section_collection_title', 'Our Collection') }}">
                </div>
            </div>
            <p style="color:var(--text-light);font-size:.78rem;margin-bottom:0">Leave a field blank to keep the current value.</p>
        </div>
    </div>

    <div class="card" style="margin-top:16px">
        <div class="card-header"><h3>Product Page Trust Section</h3></div>
        <div class="card-body">
            <p style="color:var(--text-light);font-size:.82rem;margin-top:0">Shown under the Add to Cart button on every product page.</p>
            <div class="form-row">
                <div class="form-group">
                    <label>Item 1 — Heading</label>
                    <input type="text" name="trust_1_title" class="form-control" value="{{ setting('trust_1_title', '100% Authentic') }}">
                </div>
                <div class="form-group">
                    <label>Item 1 — Paragraph</label>
                    <input type="text" name="trust_1_text" class="form-control" value="{{ setting('trust_1_text', 'Authorized brand dealer') }}">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Item 2 — Heading</label>
                    <input type="text" name="trust_2_title" class="form-control" value="{{ setting('trust_2_title', 'Free Fitment') }}">
                </div>
                <div class="form-group">
                    <label>Item 2 — Paragraph</label>
                    <input type="text" name="trust_2_text" class="form-control" value="{{ setting('trust_2_text', 'Complimentary at store') }}">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Item 3 — Heading</label>
                    <input type="text" name="trust_3_title" class="form-control" value="{{ setting('trust_3_title', 'Best Prices') }}">
                </div>
                <div class="form-group">
                    <label>Item 3 — Paragraph</label>
                    <input type="text" name="trust_3_text" class="form-control" value="{{ setting('trust_3_text', 'Store-exclusive offers') }}">
                </div>
            </div>
        </div>
    </div>

    <div style="margin-top:20px">
        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Settings</button>
    </div>
</form>
@endsection