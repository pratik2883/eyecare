@extends('admin.layouts.master')
@section('title', 'Menu Management')
@section('content')
<div class="page-header">
    <h1>Menu Management</h1>
    <p style="color:var(--text-light);font-size:.85rem">Controls the hamburger menu on the storefront.</p>
    <button class="btn btn-primary" onclick="resetMenuForm()"><i class="fas fa-plus"></i> Add Menu Item</button>
</div>

<div class="card">
    <div class="card-body" style="padding:0">
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr><th>Order</th><th>Label</th><th>Type</th><th>Target</th><th>Status</th><th>Actions</th></tr>
                </thead>
                <tbody>
                    @forelse($items as $item)
                    <tr>
                        <td>
                            <div style="display:flex;gap:4px">
                                <form action="{{ route('admin.menu.reorder') }}" method="POST">@csrf<input type="hidden" name="id" value="{{ $item->id }}"><input type="hidden" name="direction" value="up"><button class="btn btn-sm btn-secondary" title="Move up" {{ $loop->first ? 'disabled' : '' }}><i class="fas fa-chevron-up"></i></button></form>
                                <form action="{{ route('admin.menu.reorder') }}" method="POST">@csrf<input type="hidden" name="id" value="{{ $item->id }}"><input type="hidden" name="direction" value="down"><button class="btn btn-sm btn-secondary" title="Move down" {{ $loop->last ? 'disabled' : '' }}><i class="fas fa-chevron-down"></i></button></form>
                            </div>
                        </td>
                        <td><strong>{{ $item->label }}</strong></td>
                        <td><span class="badge badge-plum">{{ ucwords(str_replace('_', ' ', $item->type)) }}</span></td>
                        <td style="font-family:monospace;font-size:.75rem;color:var(--text-secondary)">{{ $item->type === 'custom' ? $item->link_url : ($item->ref ?: '—') }}</td>
                        <td><span class="badge badge-{{ $item->is_active ? 'success' : 'default' }}">{{ $item->is_active ? 'Active' : 'Inactive' }}</span></td>
                        <td>
                            <button class="btn btn-sm btn-secondary" onclick="editMenuItem({{ $item->id }},{{ json_encode($item->label) }},{{ json_encode($item->type) }},{{ json_encode($item->ref) }},{{ json_encode($item->link_url) }},{{ $item->is_active ? 'true' : 'false' }},{{ $item->sort_order }})"><i class="fas fa-edit"></i></button>
                            <form action="{{ route('admin.menu.destroy', $item) }}" method="POST" onsubmit="return confirmDelete('Remove this menu item from the storefront?')" style="display:inline">@csrf @method('DELETE')<button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button></form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" style="text-align:center;color:var(--text-light);padding:24px">No menu items.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div id="menuModal" class="modal">
    <div class="modal-header"><h2 id="menuModalTitle">Add Menu Item</h2><button class="modal-close" onclick="closeAllModals()">&times;</button></div>
    <form id="menuForm" method="POST" action="{{ route('admin.menu.store') }}">@csrf<div id="menuMethod"></div>
    <div class="modal-body">
        <div class="form-group"><label>Label</label><input type="text" name="label" class="form-control" placeholder="e.g. SUNGLASSES" required></div>
        <div class="form-group">
            <label>Type</label>
            <select name="type" id="menuType" class="form-control" onchange="syncMenuType()">
                <option value="category">Category (with shape submenu)</option>
                <option value="brand">Brand (single)</option>
                <option value="brands">Brands (all brands group)</option>
                <option value="collection">Collection (filter)</option>
                <option value="custom">Custom Link</option>
            </select>
        </div>

        <div class="form-group" id="menuRefCategory">
            <label>Category</label>
            <select name="ref" class="form-control">
                @foreach($categories as $c)<option value="{{ $c->slug }}">{{ $c->name }}</option>@endforeach
            </select>
        </div>

        <div class="form-group" id="menuRefBrand" style="display:none">
            <label>Brand</label>
            <select name="ref" class="form-control">
                @foreach($brands as $b)<option value="{{ $b->id }}">{{ $b->name }}</option>@endforeach
            </select>
        </div>

        <div class="form-group" id="menuRefCollection" style="display:none">
            <label>Collection Filter (JSON)</label>
            <input type="text" name="ref" class="form-control" placeholder='e.g. {"is_new_arrival":1}'>
            <small style="color:var(--text-light)">New arrivals: {"is_new_arrival":1} &middot; On sale: {"is_on_sale":1}</small>
        </div>

        <div class="form-group" id="menuRefUrl" style="display:none">
            <label>Link URL</label>
            <input type="url" name="link_url" class="form-control" placeholder="https://...">
        </div>

        <div class="form-row">
            <div class="form-group"><label>Sort Order</label><input type="number" name="sort_order" class="form-control" value="0"></div>
        </div>
        <div class="form-check"><input type="checkbox" name="is_active" id="ma" value="1" checked><label for="ma">Active</label></div>
    </div>
    <div class="modal-footer"><button type="button" class="btn btn-secondary" onclick="closeAllModals()">Cancel</button><button type="submit" class="btn btn-primary">Save</button></div>
    </form>
</div>

@push('scripts')
<script>
function syncMenuType(){
    const t = document.getElementById('menuType').value;
    const show = (id, yes) => document.getElementById(id).style.display = yes ? '' : 'none';
    document.getElementById('menuRefCategory').querySelector('[name=ref]').required = t === 'category';
    show('menuRefCategory', t === 'category');
    show('menuRefBrand', t === 'brand');
    show('menuRefCollection', t === 'collection');
    show('menuRefUrl', t === 'custom');
}
function editMenuItem(id,label,type,ref,link,active,order){
    document.getElementById('menuModalTitle').textContent = 'Edit Menu Item';
    const f = document.getElementById('menuForm');
    f.action = '/admin/menu/' + id;
    document.getElementById('menuMethod').innerHTML = '<input type="hidden" name="_method" value="PUT">';
    f.querySelector('[name=label]').value = label;
    f.querySelector('[name=type]').value = type;
    f.querySelector('[name=sort_order]').value = order;
    f.querySelector('[name=is_active]').checked = active;
    syncMenuType();
    if (type === 'category' || type === 'brand') {
        f.querySelector('#menuRefCategory select').value = type === 'category' ? (ref || '') : '';
        f.querySelector('#menuRefBrand select').value = type === 'brand' ? (ref || '') : '';
    } else if (type === 'collection') {
        f.querySelector('#menuRefCollection [name=ref]').value = ref || '';
    } else if (type === 'custom') {
        f.querySelector('#menuRefUrl [name=link_url]').value = link || '';
    }
    openModal('menuModal');
}
function resetMenuForm(){
    document.getElementById('menuModalTitle').textContent = 'Add Menu Item';
    document.getElementById('menuForm').action = '{{ route('admin.menu.store') }}';
    document.getElementById('menuMethod').innerHTML = '';
    document.getElementById('menuForm').reset();
    document.getElementById('ma').checked = true;
    syncMenuType();
    openModal('menuModal');
}
</script>
@endpush
@endsection