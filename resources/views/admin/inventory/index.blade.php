@extends('admin.layouts.master')
@section('title', 'Products')
@section('content')
<div class="page-header">
    <h1>All Products <small style="font-weight:400;font-size:.7rem;color:var(--text-light)">({{ $products->total() }} total)</small></h1>
    <div class="page-actions">
        <a href="{{ route('admin.inventory.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Add Product</a>
        <a href="{{ route('admin.bulk-import.index') }}" class="btn btn-gold"><i class="fas fa-file-upload"></i> Bulk Import</a>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3>Product Inventory</h3>
        @if(request()->except('page'))
        <a href="{{ route('admin.inventory.index') }}" class="btn btn-sm btn-outline"><i class="fas fa-times"></i> Clear Filters</a>
        @endif
    </div>
    <div class="card-body" style="border-bottom:1px solid var(--border)">
        <form method="GET" action="{{ route('admin.inventory.index') }}">
            <div class="form-row">
                <div class="form-group" style="margin-bottom:0">
                    <label>Search</label>
                    <input type="text" name="search" class="form-control" placeholder="Model, BQ, name..." value="{{ request('search') }}">
                </div>
                <div class="form-group" style="margin-bottom:0">
                    <label>Brand</label>
                    <select name="brand_id" class="form-control" onchange="this.form.submit()">
                        <option value="">All Brands</option>
                        @foreach($brands as $b)<option value="{{ $b->id }}" {{ request('brand_id')==$b->id?'selected':'' }}>{{ $b->name }}</option>@endforeach
                    </select>
                </div>
                <div class="form-group" style="margin-bottom:0">
                    <label>Category</label>
                    <select name="category" class="form-control" onchange="this.form.submit()">
                        <option value="">All Categories</option>
                        @foreach($categories as $c)<option value="{{ $c }}" {{ request('category')==$c?'selected':'' }}>{{ str_replace('_',' ',ucfirst($c)) }}</option>@endforeach
                    </select>
                </div>
                <div class="form-group" style="margin-bottom:0">
                    <label>Gender</label>
                    <select name="gender" class="form-control" onchange="this.form.submit()">
                        <option value="">All</option>
                        @foreach($genders as $g)<option value="{{ $g }}" {{ request('gender')==$g?'selected':'' }}>{{ ucfirst($g) }}</option>@endforeach
                    </select>
                </div>
                <div class="form-group" style="margin-bottom:0">
                    <label>Shape</label>
                    <select name="frame_shape" class="form-control" onchange="this.form.submit()">
                        <option value="">All</option>
                        @foreach($shapes as $s)<option value="{{ $s }}" {{ request('frame_shape')==$s?'selected':'' }}>{{ $s }}</option>@endforeach
                    </select>
                </div>
                <div class="form-group" style="margin-bottom:0">
                    <label>Material</label>
                    <select name="frame_material" class="form-control" onchange="this.form.submit()">
                        <option value="">All</option>
                        @foreach($materials as $m)<option value="{{ $m }}" {{ request('frame_material')==$m?'selected':'' }}>{{ $m }}</option>@endforeach
                    </select>
                </div>
                <div class="form-group" style="margin-bottom:0">
                    <label>Color</label>
                    <select name="frame_color" class="form-control" onchange="this.form.submit()">
                        <option value="">All</option>
                        @foreach($colors as $c)<option value="{{ $c }}" {{ request('frame_color')==$c?'selected':'' }}>{{ $c }}</option>@endforeach
                    </select>
                </div>
                <div class="form-group" style="margin-bottom:0">
                    <label>Size</label>
                    <select name="frame_size" class="form-control" onchange="this.form.submit()">
                        <option value="">All</option>
                        @foreach($sizes as $s)<option value="{{ $s }}" {{ request('frame_size')==$s?'selected':'' }}>{{ $s }}</option>@endforeach
                    </select>
                </div>
                <div class="form-group" style="margin-bottom:0">
                    <label>Price (₹)</label>
                    <div style="display:flex;gap:6px;align-items:center">
                        <input type="number" name="min_price" class="form-control" placeholder="Min" value="{{ request('min_price') }}" min="0" style="width:70px">
                        <span style="color:var(--text-light)">—</span>
                        <input type="number" name="max_price" class="form-control" placeholder="Max" value="{{ request('max_price') }}" min="0" style="width:70px">
                    </div>
                </div>
                <div class="form-group" style="margin-bottom:0">
                    <label>Status</label>
                    <select name="status" class="form-control" onchange="this.form.submit()">
                        <option value="">All</option>
                        <option value="active" {{ request('status')=='active'?'selected':'' }}>Active</option>
                        <option value="inactive" {{ request('status')=='inactive'?'selected':'' }}>Inactive</option>
                    </select>
                </div>
                <div class="form-group" style="margin-bottom:0">
                    <label>Flags</label>
                    <div style="display:flex;gap:14px;align-items:center;height:36px">
                        <label style="display:flex;gap:5px;align-items:center;font-size:.78rem;cursor:pointer">
                            <input type="checkbox" name="is_new_arrival" value="1" {{ request()->boolean('is_new_arrival') ? 'checked' : '' }} onchange="this.form.submit()">
                            New
                        </label>
                        <label style="display:flex;gap:5px;align-items:center;font-size:.78rem;cursor:pointer">
                            <input type="checkbox" name="is_on_sale" value="1" {{ request()->boolean('is_on_sale') ? 'checked' : '' }} onchange="this.form.submit()">
                            Sale
                        </label>
                    </div>
                </div>
            </div>
            <div style="margin-top:14px;display:flex;gap:8px">
                <button type="submit" class="btn btn-sm btn-secondary"><i class="fas fa-search"></i> Filter</button>
                <a href="{{ route('admin.inventory.index') }}" class="btn btn-sm btn-outline"><i class="fas fa-times"></i> Reset</a>
            </div>
        </form>
    </div>
    <div class="card-body" style="padding:0">
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Brand</th>
                        <th>Model</th>
                        <th>BQ#</th>
                        <th>Category</th>
                        <th>Shape</th>
                        <th>Size</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $p)
                    <tr>
                        <td>
                            @if($p->image_url)
                            <img src="{{ $p->image_url }}" alt="" style="width:40px;height:30px;object-fit:cover;border-radius:4px">
                            @else
                            <span style="color:var(--text-light)">—</span>
                            @endif
                        </td>
                        <td><strong>{{ $p->brand->name ?? '—' }}</strong></td>
                        <td>{{ $p->model_number }}</td>
                        <td style="font-family:monospace;font-size:.75rem">{{ $p->bq_number ?? '—' }}</td>
                        <td><span class="badge badge-plum">{{ str_replace('_',' ',ucfirst($p->category)) }}</span></td>
                        <td style="text-transform:capitalize">{{ $p->frame_shape ?? '—' }}</td>
                        <td>{{ $p->frame_size ?? '—' }}</td>
                        <td>₹{{ number_format($p->price) }}@if($p->sale_price)<br><small style="color:var(--red);font-size:.68rem">₹{{ number_format($p->sale_price) }}</small>@endif</td>
                        <td>{{ $p->stock_quantity }}</td>
                        <td>
                            <span class="badge badge-{{ $p->is_active ? 'success' : 'default' }}">
                                {{ $p->is_active ? 'Active' : 'Inactive' }}
                            </span>
                            @if($p->is_new_arrival)<span class="badge badge-info">New</span>@endif
                            @if($p->is_on_sale)<span class="badge badge-danger">Sale</span>@endif
                        </td>
                        <td>
                            <div class="table-actions">
                                <a href="{{ route('admin.inventory.edit', $p) }}" class="btn btn-sm btn-secondary"><i class="fas fa-edit"></i></a>
                                <form action="{{ route('admin.inventory.destroy', $p) }}" method="POST" onsubmit="return confirmDelete()" style="display:inline">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="11"><div class="empty-state"><i class="fas fa-glasses"></i><p>No products found</p></div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div style="padding:16px 20px">
            {{ $products->links() }}
        </div>
    </div>
</div>
@endsection
