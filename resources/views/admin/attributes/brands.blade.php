@extends('admin.layouts.master')
@section('title', 'Brands')
@section('content')
<div class="page-header">
    <h1>Brands</h1>
    <button class="btn btn-primary" onclick="openModal('brandModal')"><i class="fas fa-plus"></i> Add Brand</button>
</div>

<div class="card">
    <div class="card-body" style="padding:0">
        <div class="table-wrapper">
            <table>
                <thead><tr><th>Name</th><th>Slug</th><th>Featured</th><th>Order</th><th>Status</th><th>Actions</th></tr></thead>
                <tbody>
                    @foreach($brands as $b)
                    <tr>
                        <td><strong>{{ $b->name }}</strong></td>
                        <td style="font-family:monospace;font-size:.75rem;color:var(--text-secondary)">{{ $b->slug }}</td>
                        <td>@if($b->is_featured)<span class="badge badge-plum">Featured</span>@else<span class="badge badge-default">No</span>@endif</td>
                        <td>{{ $b->sort_order }}</td>
                        <td><span class="badge badge-{{ $b->is_active ? 'success' : 'default' }}">{{ $b->is_active ? 'Active' : 'Inactive' }}</span></td>
                        <td>
                            <button class="btn btn-sm btn-secondary" onclick="editBrand({{ $b->id }},{{ json_encode($b->name) }},{{ json_encode($b->description) }},{{ json_encode($b->logo_url) }},{{ $b->is_featured?'true':'false' }},{{ $b->is_active?'true':'false' }},{{ $b->sort_order }})"><i class="fas fa-edit"></i></button>
                            <form action="{{ route('admin.attributes.brands.destroy', $b) }}" method="POST" onsubmit="return confirmDelete()" style="display:inline">@csrf @method('DELETE')<button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button></form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<div id="brandModal" class="modal">
    <div class="modal-header"><h2 id="brandModalTitle">Add Brand</h2><button class="modal-close" onclick="closeAllModals()">&times;</button></div>
    <form id="brandForm" method="POST" action="{{ route('admin.attributes.brands.store') }}" enctype="multipart/form-data">@csrf<div id="brandMethod"></div>
    <div class="modal-body">
        <div class="form-group"><label>Brand Name</label><input type="text" name="name" class="form-control" required></div>
        <div class="form-group"><label>Description</label><textarea name="description" class="form-control"></textarea></div>
        <div class="form-group">
            <label>Upload Logo</label>
            <input type="file" name="logo" id="brandLogoInput" class="form-control" accept="image/jpeg,image/png,image/jpg,image/gif,image/svg+xml,image/webp">
            <small style="color:var(--text-light)">Max 5MB. JPEG, PNG, JPG, GIF, SVG, WebP.</small>
        </div>
        <div class="form-group"><label>OR Logo URL</label><input type="url" name="logo_url" id="brandLogoUrl" class="form-control" placeholder="https://..."></div>
        <div class="form-group" id="currentBrandLogoPreview" style="display:none">
            <label>Current Logo</label>
            <div><img id="currentBrandLogoTag" src="" style="max-height:60px;border-radius:6px"></div>
        </div>
        <div class="form-row">
            <div class="form-group"><label>Sort Order</label><input type="number" name="sort_order" class="form-control" value="0"></div>
        </div>
        <div class="form-check"><input type="checkbox" name="is_featured" id="bf" value="1"><label for="bf">Featured (show in ticker)</label></div>
        <div class="form-check"><input type="checkbox" name="is_active" id="ba" value="1" checked><label for="ba">Active</label></div>
    </div>
    <div class="modal-footer"><button type="button" class="btn btn-secondary" onclick="closeAllModals()">Cancel</button><button type="submit" class="btn btn-primary">Save</button></div>
    </form>
</div>

@push('scripts')
<script>
function editBrand(id,name,desc,logo,featured,active,order){
    document.getElementById('brandModalTitle').textContent = 'Edit Brand';
    const f = document.getElementById('brandForm');
    f.action = '/admin/attributes/brands/' + id;
    document.getElementById('brandMethod').innerHTML = '<input type="hidden" name="_method" value="PUT">';
    f.querySelector('[name=name]').value = name;
    f.querySelector('[name=description]').value = desc || '';
    f.querySelector('[name=logo_url]').value = logo || '';
    f.querySelector('[name=sort_order]').value = order;
    f.querySelector('[name=is_featured]').checked = featured;
    f.querySelector('[name=is_active]').checked = active;
    document.getElementById('brandLogoInput').value = '';
    if (logo) {
        document.getElementById('currentBrandLogoPreview').style.display = 'block';
        document.getElementById('currentBrandLogoTag').src = logo;
    } else {
        document.getElementById('currentBrandLogoPreview').style.display = 'none';
    }
    openModal('brandModal');
}
document.querySelector('[onclick="openModal(\'brandModal\')"]')?.addEventListener('click',function(){
    document.getElementById('brandModalTitle').textContent = 'Add Brand';
    document.getElementById('brandForm').action = '{{ route('admin.attributes.brands.store') }}';
    document.getElementById('brandMethod').innerHTML = '';
    document.getElementById('brandForm').reset();
    document.getElementById('ba').checked = true;
    document.getElementById('currentBrandLogoPreview').style.display = 'none';
});
</script>
@endpush
@endsection
