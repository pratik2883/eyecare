@extends('admin.layouts.master')
@section('title', 'Products')

@push('styles')
<style>
.selected-row {
    background-color: rgba(200, 16, 46, 0.05) !important;
}
.bulk-action-bar {
    display: none;
    align-items: center;
    gap: 12px;
    margin-left: auto;
    animation: bulkFadeIn 0.2s ease-in-out;
}
@keyframes bulkFadeIn {
    from { opacity: 0; transform: translateY(-2px); }
    to { opacity: 1; transform: translateY(0); }
}
.checkbox-cell {
    width: 38px;
    text-align: center;
    vertical-align: middle;
}
.custom-checkbox-input {
    width: 16px;
    height: 16px;
    cursor: pointer;
    accent-color: var(--plum);
    vertical-align: middle;
}
</style>
@endpush

@section('content')
<div class="page-header">
    <h1>All Products <small style="font-weight:400;font-size:.7rem;color:var(--text-light)">({{ $products->total() }} total)</small></h1>
    <div class="page-actions">
        <a href="{{ route('admin.inventory.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Add Product</a>
        <a href="{{ route('admin.bulk-import.index') }}" class="btn btn-gold"><i class="fas fa-file-upload"></i> Bulk Import</a>
    </div>
</div>

<div class="card">
    <div class="card-header" style="display:flex;align-items:center;justify-content:space-between">
        <div style="display:flex;align-items:center;gap:16px">
            <h3>Product Inventory</h3>
            @if(request()->except('page'))
            <a href="{{ route('admin.inventory.index') }}" class="btn btn-sm btn-outline"><i class="fas fa-times"></i> Clear Filters</a>
            @endif
        </div>
        <div id="bulkActionBar" class="bulk-action-bar">
            <span id="selectedCountBadge" class="badge badge-plum">0 selected</span>
            <button type="submit" form="bulkDeleteForm" class="btn btn-sm btn-danger" onclick="return confirmBulkDelete()"><i class="fas fa-trash"></i> Delete Selected</button>
        </div>
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
        <form id="bulkDeleteForm" method="POST" action="{{ route('admin.inventory.bulk-destroy') }}">
            @csrf
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th class="checkbox-cell">
                                <input type="checkbox" id="selectAllCheckbox" class="custom-checkbox-input" onchange="toggleSelectAllProducts(this)" title="Select All">
                            </th>
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
                            <td class="checkbox-cell">
                                <input type="checkbox" name="ids[]" value="{{ $p->id }}" class="product-select-checkbox custom-checkbox-input" onchange="updateBulkActionState()">
                            </td>
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
                                    <button type="button" class="btn btn-sm btn-danger" onclick="deleteSingleProduct({{ $p->id }}, '{{ addslashes($p->model_number) }}')"><i class="fas fa-trash"></i></button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="12"><div class="empty-state"><i class="fas fa-glasses"></i><p>No products found</p></div></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </form>
        <!-- Single Delete Form -->
        <form id="singleDeleteForm" method="POST" action="" style="display:none">
            @csrf
            @method('DELETE')
        </form>
        <div style="padding:16px 20px">
            {{ $products->links() }}
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function toggleSelectAllProducts(master) {
    const checkboxes = document.querySelectorAll('.product-select-checkbox');
    checkboxes.forEach(cb => {
        cb.checked = master.checked;
        const tr = cb.closest('tr');
        if (tr) {
            if (cb.checked) tr.classList.add('selected-row');
            else tr.classList.remove('selected-row');
        }
    });
    updateBulkActionState();
}

function updateBulkActionState() {
    const checkboxes = document.querySelectorAll('.product-select-checkbox');
    const checked = document.querySelectorAll('.product-select-checkbox:checked');
    const master = document.getElementById('selectAllCheckbox');
    const bulkBar = document.getElementById('bulkActionBar');
    const countBadge = document.getElementById('selectedCountBadge');

    checkboxes.forEach(cb => {
        const tr = cb.closest('tr');
        if (tr) {
            if (cb.checked) tr.classList.add('selected-row');
            else tr.classList.remove('selected-row');
        }
    });

    if (master) {
        master.checked = checkboxes.length > 0 && checked.length === checkboxes.length;
        master.indeterminate = checked.length > 0 && checked.length < checkboxes.length;
    }

    if (bulkBar) {
        if (checked.length > 0) {
            bulkBar.style.display = 'flex';
            if (countBadge) countBadge.textContent = checked.length + ' selected';
        } else {
            bulkBar.style.display = 'none';
        }
    }
}

function confirmBulkDelete() {
    const checked = document.querySelectorAll('.product-select-checkbox:checked');
    if (checked.length === 0) {
        alert('Please select at least one product to delete.');
        return false;
    }
    return confirm('Are you sure you want to delete ' + checked.length + ' selected product(s)? This action cannot be undone.');
}

function deleteSingleProduct(id, model) {
    if (confirm('Are you sure you want to delete product "' + model + '"?')) {
        const form = document.getElementById('singleDeleteForm');
        form.action = '/admin/inventory/' + id;
        form.submit();
    }
}
</script>
@endpush
