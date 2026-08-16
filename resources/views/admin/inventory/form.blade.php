@extends('admin.layouts.master')
@section('title', isset($inventory) ? 'Edit Product' : 'Add Product')
@section('content')
<div class="page-header">
    <h1>{{ isset($inventory) ? 'Edit Product' : 'Add Product' }}</h1>
    <a href="{{ route('admin.inventory.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back</a>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ isset($inventory) ? route('admin.inventory.update', $inventory) : route('admin.inventory.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @if(isset($inventory)) @method('PUT') @endif

            <div class="form-row">
                <div class="form-group">
                    <label>Brand <span style="color:var(--red)">*</span></label>
                    <select name="brand_id" class="form-control" required>
                        <option value="">Select Brand</option>
                        @foreach($brands as $b)
                        <option value="{{ $b->id }}" {{ isset($inventory) && $inventory->brand_id == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Model Number <span style="color:var(--red)">*</span></label>
                    <input type="text" name="model_number" class="form-control" value="{{ $inventory->model_number ?? '' }}" required>
                </div>
                <div class="form-group">
                    <label>BQ Number</label>
                    <input type="text" name="bq_number" class="form-control" value="{{ $inventory->bq_number ?? '' }}" placeholder="Billing quantity code">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Category <span style="color:var(--red)">*</span></label>
                    <select name="category" class="form-control" required>
                        @foreach($categories as $c)
                        <option value="{{ $c }}" {{ isset($inventory) && $inventory->category == $c ? 'selected' : '' }}>{{ ucfirst(str_replace('_',' ',$c)) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Gender</label>
                    <select name="gender" class="form-control">
                        <option value="">Unisex</option>
                        <option value="male" {{ isset($inventory) && $inventory->gender=='male'?'selected':'' }}>Male</option>
                        <option value="female" {{ isset($inventory) && $inventory->gender=='female'?'selected':'' }}>Female</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Frame Material</label>
                    <select name="frame_material" class="form-control">
                        <option value="">Select</option>
                        @foreach($materials as $m)
                        <option value="{{ $m->name }}" {{ isset($inventory) && $inventory->frame_material == $m->name ? 'selected' : '' }}>{{ $m->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Frame Shape</label>
                    <select name="frame_shape" class="form-control">
                        <option value="">Select</option>
                        @foreach($shapes as $s)
                        <option value="{{ $s->name }}" {{ isset($inventory) && $inventory->frame_shape == $s->name ? 'selected' : '' }}>{{ $s->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Frame Color</label>
                    <select name="frame_color" class="form-control">
                        <option value="">Select</option>
                        @foreach($colors as $c)
                        <option value="{{ $c->name }}" {{ isset($inventory) && $inventory->frame_color == $c->name ? 'selected' : '' }}>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Frame Size</label>
                    <input type="text" name="frame_size" class="form-control" value="{{ $inventory->frame_size ?? '' }}" placeholder="e.g. 54-18-145">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Price (INR) <span style="color:var(--red)">*</span></label>
                    <input type="number" name="price" class="form-control" value="{{ $inventory->price ?? '' }}" step="0.01" min="0" required>
                </div>
                <div class="form-group">
                    <label>Sale Price (optional)</label>
                    <input type="number" name="sale_price" class="form-control" value="{{ $inventory->sale_price ?? '' }}" step="0.01" min="0">
                </div>
                <div class="form-group">
                    <label>Stock Quantity</label>
                    <input type="number" name="stock_quantity" class="form-control" value="{{ $inventory->stock_quantity ?? 1 }}" min="0">
                </div>
            </div>

            <div class="form-group">
                <label>Product Thumbnail (Main Image)</label>
                <input type="file" name="image" id="productImageInput" class="form-control" accept="image/jpeg,image/png,image/jpg,image/gif,image/webp">
                <small style="color:var(--text-light)">Max 5MB. JPEG, PNG, JPG, GIF, WebP.</small>
                @if(isset($inventory) && $inventory->image_url)
                <div style="margin-top:8px"><img src="{{ $inventory->image_url }}" alt="Current thumbnail" style="max-height:120px;border-radius:8px"></div>
                @endif
            </div>
            <div class="form-group">
                <label>OR Image URL</label>
                <input type="url" name="image_url" class="form-control" value="{{ $inventory->image_url ?? '' }}" placeholder="https://example.com/image.jpg">
            </div>
            <div class="form-group">
                <label>Product Gallery</label>
                <input type="file" name="gallery_images[]" class="form-control" accept="image/jpeg,image/png,image/jpg,image/gif,image/webp" multiple>
                <small style="color:var(--text-light)">Select multiple images. Max 5MB each. Uploading new gallery replaces the old one.</small>
                @if(isset($inventory) && !empty($inventory->additional_images))
                <div style="display:flex;gap:8px;flex-wrap:wrap;margin-top:8px">
                    @foreach($inventory->additional_images as $img)
                    <img src="{{ $img }}" alt="Gallery image" style="max-height:80px;border-radius:6px">
                    @endforeach
                </div>
                @endif
            </div>
            <div class="form-group">
                <label>Description</label>
                <textarea name="description" class="form-control">{{ $inventory->description ?? '' }}</textarea>
            </div>
            <div class="form-group">
                <label>About Brand</label>
                <textarea name="about_brand" class="form-control" placeholder="Brand story, highlights...">{{ $inventory->about_brand ?? '' }}</textarea>
            </div>

            <div class="form-row" style="grid-template-columns:repeat(3,auto)">
                <div class="form-check">
                    <input type="checkbox" name="is_active" id="is_active" value="1" {{ !isset($inventory) || $inventory->is_active ? 'checked' : '' }}>
                    <label for="is_active">Active</label>
                </div>
                <div class="form-check">
                    <input type="checkbox" name="is_new_arrival" id="is_new_arrival" value="1" {{ isset($inventory) && $inventory->is_new_arrival ? 'checked' : '' }}>
                    <label for="is_new_arrival">New Arrival</label>
                </div>
                <div class="form-check">
                    <input type="checkbox" name="is_on_sale" id="is_on_sale" value="1" {{ isset($inventory) && $inventory->is_on_sale ? 'checked' : '' }}>
                    <label for="is_on_sale">On Sale</label>
                </div>
            </div>

            <div style="margin-top:20px">
                <button type="submit" class="btn btn-primary">{{ isset($inventory) ? 'Update Product' : 'Create Product' }}</button>
                <a href="{{ route('admin.inventory.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
