@extends('admin.layouts.master')
@section('title', 'Bulk Import')
@section('content')
<div class="page-header">
    <h1>Bulk Excel Import</h1>
</div>

@if(session('import_errors') && count(session('import_errors')))
<div class="card" style="margin-bottom:16px;border-left:4px solid var(--red)">
    <div class="card-header"><h3 style="color:var(--red)">Import Errors</h3></div>
    <div class="card-body" style="max-height:200px;overflow-y:auto">
        <ul style="font-size:.78rem;color:var(--red)">
            @foreach(session('import_errors') as $e)
            <li>{{ $e }}</li>
            @endforeach
        </ul>
    </div>
</div>
@endif

<div class="card">
    <div class="card-header"><h3>Upload Excel File</h3></div>
    <div class="card-body">
        <form action="{{ route('admin.bulk-import.preview') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="drop-zone" onclick="document.getElementById('fileInput').click()">
                <i class="fas fa-file-excel"></i>
                <h3>Drop your frames.xlsx here</h3>
                <p>or click to browse — Supports .xlsx, .xls, .csv</p>
                <input type="file" name="file" id="fileInput" accept=".xlsx,.xls,.csv" style="display:none" required onchange="this.closest('form').submit()">
            </div>
        </form>
    </div>
</div>

<div class="card" style="margin-top:16px">
    <div class="card-header"><h3>Expected Columns</h3></div>
    <div class="card-body">
        <table>
            <thead><tr><th>Column</th><th>Required</th><th>Description</th></tr></thead>
            <tbody>
                <tr><td>model_number</td><td><span class="badge badge-danger">Required</span></td><td>Unique product identifier</td></tr>
                <tr><td>brand</td><td><span class="badge badge-warning">Recommended</span></td><td>Brand name (auto-created if new)</td></tr>
                <tr><td>category</td><td><span class="badge badge-warning">Recommended</span></td><td>eyeglasses, sunglasses, contact_lenses, accessories, kids</td></tr>
                <tr><td>price</td><td><span class="badge badge-danger">Required</span></td><td>Selling price in INR</td></tr>
                <tr><td>name</td><td><span class="badge badge-default">Optional</span></td><td>Product display name</td></tr>
                <tr><td>description</td><td><span class="badge badge-default">Optional</span></td><td>Product description (shown on product page)</td></tr>
                <tr><td>frame_color</td><td><span class="badge badge-default">Optional</span></td><td>e.g. BLACK, TORTOISE, GOLD (or color)</td></tr>
                <tr><td>frame_material</td><td><span class="badge badge-default">Optional</span></td><td>e.g. PLASTIC, METAL, RIMLESS (or material)</td></tr>
                <tr><td>frame_shape</td><td><span class="badge badge-default">Optional</span></td><td>e.g. RECTANGLE, SQUARE, AVIATOR (or shape)</td></tr>
                <tr><td>frame_size</td><td><span class="badge badge-default">Optional</span></td><td>e.g. 49, 52, 54, 56 (or size)</td></tr>
                <tr><td>bq_number</td><td><span class="badge badge-default">Optional</span></td><td>Stock / Barcode code</td></tr>
                <tr><td>gender</td><td><span class="badge badge-default">Optional</span></td><td>MALE, FEMALE, UNISEX</td></tr>
                <tr><td>about_brand</td><td><span class="badge badge-default">Optional</span></td><td>Brand story / highlights</td></tr>
                <tr><td>sale_price</td><td><span class="badge badge-default">Optional</span></td><td>Discounted price</td></tr>
                <tr><td>stock_quantity</td><td><span class="badge badge-default">Optional</span></td><td>Default: 0</td></tr>
                <tr><td>image_url</td><td><span class="badge badge-default">Optional</span></td><td>Product main image URL</td></tr>
                <tr><td>gallery_images</td><td><span class="badge badge-default">Optional</span></td><td>Extra gallery image URLs separated by <code>|</code> (pipe)</td></tr>
            </tbody>
        </table>
    </div>
</div>
@endsection
