@extends('admin.layouts.master')
@section('title', 'Categories')
@section('content')
<div class="page-header">
    <h1>Categories</h1>
    <button class="btn btn-primary" onclick="resetCategoryForm()"><i class="fas fa-plus"></i> Add Category</button>
</div>

<div class="card">
    <div class="card-body" style="padding:0">
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr><th>Order</th><th>Name</th><th>Slug</th><th>Icon</th><th>Products</th><th>Status</th><th>Actions</th></tr>
                </thead>
                <tbody>
                    @forelse($categories as $i => $c)
                    <tr>
                        <td>
                            <div style="display:flex;gap:4px">
                                <form action="{{ route('admin.categories.reorder') }}" method="POST">@csrf<input type="hidden" name="id" value="{{ $c->id }}"><input type="hidden" name="direction" value="up"><button class="btn btn-sm btn-secondary" title="Move up" {{ $loop->first ? 'disabled' : '' }}><i class="fas fa-chevron-up"></i></button></form>
                                <form action="{{ route('admin.categories.reorder') }}" method="POST">@csrf<input type="hidden" name="id" value="{{ $c->id }}"><input type="hidden" name="direction" value="down"><button class="btn btn-sm btn-secondary" title="Move down" {{ $loop->last ? 'disabled' : '' }}><i class="fas fa-chevron-down"></i></button></form>
                            </div>
                        </td>
                        <td><strong>{{ $c->name }}</strong></td>
                        <td style="font-family:monospace;font-size:.75rem;color:var(--text-secondary)">{{ $c->slug }}</td>
                        <td><i class="fas fa-{{ $c->icon }}"></i> <span style="font-size:.75rem;color:var(--text-light)">{{ $c->icon }}</span></td>
                        <td>{{ $counts[$c->slug] ?? 0 }}</td>
                        <td><span class="badge badge-{{ $c->is_active ? 'success' : 'default' }}">{{ $c->is_active ? 'Active' : 'Inactive' }}</span></td>
                        <td>
                            <button class="btn btn-sm btn-secondary" onclick="editCategory({{ $c->id }},{{ json_encode($c->name) }},{{ json_encode($c->slug) }},{{ json_encode($c->icon) }},{{ json_encode($c->description) }},{{ $c->is_active ? 'true' : 'false' }},{{ $c->sort_order }})"><i class="fas fa-edit"></i></button>
                            <form action="{{ route('admin.categories.destroy', $c) }}" method="POST" onsubmit="return confirmDelete('Delete this category? Products will not be removed.')" style="display:inline">@csrf @method('DELETE')<button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button></form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" style="text-align:center;color:var(--text-light);padding:24px">No categories yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div id="categoryModal" class="modal">
    <div class="modal-header"><h2 id="categoryModalTitle">Add Category</h2><button class="modal-close" onclick="closeAllModals()">&times;</button></div>
    <form id="categoryForm" method="POST" action="{{ route('admin.categories.store') }}">@csrf<div id="categoryMethod"></div>
    <div class="modal-body">
        <div class="form-group"><label>Name</label><input type="text" name="name" class="form-control" required></div>
        <div class="form-group"><label>Slug (auto-generated if empty)</label><input type="text" name="slug" class="form-control" placeholder="e.g. reading-glasses"></div>
        <div class="form-group">
            <label>Icon</label>
            <div class="icon-picker" id="categoryIconPicker">
                @foreach(['glasses','sun','eye','clock','child','gem','heart','fire'] as $icon)
                <span class="icon-option" data-icon="{{ $icon }}" title="{{ $icon }}"><i class="fas fa-{{ $icon }}"></i></span>
                @endforeach
            </div>
            <input type="hidden" name="icon" id="categoryIcon" value="">
        </div>
        <div class="form-group"><label>Description</label><textarea name="description" class="form-control" rows="2"></textarea></div>
        <div class="form-row">
            <div class="form-group"><label>Sort Order</label><input type="number" name="sort_order" class="form-control" value="0"></div>
        </div>
        <div class="form-check"><input type="checkbox" name="is_active" id="ca" value="1" checked><label for="ca">Active</label></div>
    </div>
    <div class="modal-footer"><button type="button" class="btn btn-secondary" onclick="closeAllModals()">Cancel</button><button type="submit" class="btn btn-primary">Save</button></div>
    </form>
</div>

@push('scripts')
<style>
.icon-picker { display:flex; gap:8px; flex-wrap:wrap; }
.icon-option { width:40px; height:40px; display:flex; align-items:center; justify-content:center; border:1px solid #ddd; border-radius:8px; cursor:pointer; color:var(--text-secondary); transition:all .15s; }
.icon-option.selected, .icon-option:hover { border-color:var(--plum); color:var(--plum); background:rgba(200,16,46,0.06); }
</style>
<script>
function selectIcon(icon){
    document.getElementById('categoryIcon').value = icon;
    document.querySelectorAll('#categoryIconPicker .icon-option').forEach(o => o.classList.toggle('selected', o.dataset.icon === icon));
}
function editCategory(id,name,slug,icon,desc,active,order){
    document.getElementById('categoryModalTitle').textContent = 'Edit Category';
    const f = document.getElementById('categoryForm');
    f.action = '/admin/categories/' + id;
    document.getElementById('categoryMethod').innerHTML = '<input type="hidden" name="_method" value="PUT">';
    f.querySelector('[name=name]').value = name;
    f.querySelector('[name=slug]').value = slug || '';
    f.querySelector('[name=description]').value = desc || '';
    f.querySelector('[name=sort_order]').value = order;
    f.querySelector('[name=is_active]').checked = active;
    selectIcon(icon || '');
    openModal('categoryModal');
}
function resetCategoryForm(){
    document.getElementById('categoryModalTitle').textContent = 'Add Category';
    document.getElementById('categoryForm').action = '{{ route('admin.categories.store') }}';
    document.getElementById('categoryMethod').innerHTML = '';
    document.getElementById('categoryForm').reset();
    document.getElementById('ca').checked = true;
    selectIcon('');
    openModal('categoryModal');
}
</script>
@endpush
@endsection