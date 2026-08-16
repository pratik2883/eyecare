@extends('admin.layouts.master')
@section('title', 'Hero Banners')
@section('content')
<div class="page-header">
    <h1>Hero Slider Manager</h1>
    <button class="btn btn-primary" onclick="openModal('bannerModal')"><i class="fas fa-plus"></i> Add Banner</button>
</div>

<div class="card">
    <div class="card-header"><h3>Slider Banners <small style="font-weight:400;font-size:.7rem;color:var(--text-light)">— Drag to reorder</small></h3></div>
    <div class="card-body" style="padding:0">
        <div class="sortable-grid" data-reorder-url="{{ route('admin.banners.reorder') }}">
            @forelse($banners as $banner)
            <div class="sortable-item" draggable="true" data-id="{{ $banner->id }}">
                <span class="drag-handle"><i class="fas fa-grip-vertical"></i></span>
                <div class="item-preview"><img src="{{ $banner->image_url }}" alt=""></div>
                <div class="item-info">
                    <h4>{{ $banner->title }}</h4>
                    <p>{{ $banner->subtitle ?? 'No subtitle' }} — Order: {{ $banner->sort_order }}</p>
                </div>
                <label class="toggle-switch" title="Active">
                    <input type="checkbox" {{ $banner->is_active ? 'checked' : '' }} onchange="fetch('{{ route('admin.banners.update', $banner) }}',{method:'PUT',headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Content-Type':'application/json'},body:JSON.stringify({is_active:this.checked,title:{{ json_encode($banner->title) }},image_url:{{ json_encode($banner->image_url) }},subtitle:{{ json_encode($banner->subtitle) }},sort_order:{{ $banner->sort_order }}})}).then(()=>location.reload())">
                    <span class="toggle-slider"></span>
                </label>
                <div class="item-actions">
                    <button class="btn btn-sm btn-secondary" onclick="editBanner({{ $banner->id }},{{ json_encode($banner->title) }},{{ json_encode($banner->subtitle) }},{{ json_encode($banner->image_url) }},{{ json_encode($banner->link_url) }},{{ json_encode($banner->background_color) }},{{ $banner->sort_order }},{{ $banner->is_active ? 'true' : 'false' }})"><i class="fas fa-edit"></i></button>
                    <form action="{{ route('admin.banners.destroy', $banner) }}" method="POST" onsubmit="return confirmDelete('Delete this banner?')" style="display:inline">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                    </form>
                </div>
            </div>
            @empty
            <div class="empty-state"><i class="fas fa-images"></i><p>No banners yet. Click "Add Banner" to create one.</p></div>
            @endforelse
        </div>
    </div>
</div>

{{-- Add/Edit Modal --}}
<div id="bannerModal" class="modal">
    <div class="modal-header"><h2 id="bannerModalTitle">Add Banner</h2><button class="modal-close" onclick="closeAllModals()">&times;</button></div>
    <form id="bannerForm" method="POST" action="{{ route('admin.banners.store') }}" enctype="multipart/form-data">
        @csrf
        <div id="bannerMethod"></div>
        <div class="modal-body">
            <div class="form-group">
                <label>Title <small style="color:var(--text-light)">(optional)</small></label>
                <input type="text" name="title" class="form-control">
            </div>
            <div class="form-group">
                <label>Subtitle <small style="color:var(--text-light)">(optional)</small></label>
                <input type="text" name="subtitle" class="form-control">
            </div>
            <div class="form-group">
                <label>Upload Image</label>
                <input type="file" name="image" id="bannerFileInput" class="form-control" accept="image/jpeg,image/png,image/jpg,image/gif,image/webp">
                <small style="color:var(--text-light)">Max 5MB. JPEG, PNG, JPG, GIF, WebP.</small>
            </div>
            <div class="form-group">
                <label>OR Image URL</label>
                <input type="url" name="image_url" id="bannerImageUrl" class="form-control" placeholder="https://...">
            </div>
            <div class="form-group" id="currentImagePreview" style="display:none">
                <label>Current Image</label>
                <div><img id="currentImageTag" src="" style="max-height:120px;border-radius:8px"></div>
            </div>
            <div class="form-group">
                <label>Link URL</label>
                <input type="url" name="link_url" class="form-control" placeholder="https://...">
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Background Color</label>
                    <input type="text" name="background_color" class="form-control" placeholder="#1A1A1A">
                </div>
                <div class="form-group">
                    <label>Sort Order <small style="color:var(--text-light)">(auto if empty)</small></label>
                    <input type="number" name="sort_order" class="form-control" min="0" placeholder="Auto">
                </div>
            </div>
            <div class="form-check">
                <input type="checkbox" name="is_active" id="bannerActive" value="1" checked>
                <label for="bannerActive">Active</label>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="closeAllModals()">Cancel</button>
            <button type="submit" class="btn btn-primary">Save</button>
        </div>
    </form>
</div>

@push('scripts')
<script>
function editBanner(id,title,subtitle,image,link,bg,order,active){
    document.getElementById('bannerModalTitle').textContent = 'Edit Banner';
    const form = document.getElementById('bannerForm');
    form.action = '/admin/banners/' + id;
    document.getElementById('bannerMethod').innerHTML = '<input type="hidden" name="_method" value="PUT">';
    form.querySelector('[name=title]').value = title;
    form.querySelector('[name=subtitle]').value = subtitle;
    form.querySelector('[name=image_url]').value = image;
    form.querySelector('[name=link_url]').value = link || '';
    form.querySelector('[name=background_color]').value = bg || '';
    form.querySelector('[name=sort_order]').value = order;
    form.querySelector('[name=is_active]').checked = active;
    document.getElementById('bannerFileInput').value = '';
    if (image) {
        document.getElementById('currentImagePreview').style.display = 'block';
        document.getElementById('currentImageTag').src = image;
    } else {
        document.getElementById('currentImagePreview').style.display = 'none';
    }
    openModal('bannerModal');
}
document.querySelector('[onclick="openModal(\'bannerModal\')"]')?.addEventListener('click',function(){
    document.getElementById('bannerModalTitle').textContent = 'Add Banner';
    document.getElementById('bannerForm').action = '{{ route('admin.banners.store') }}';
    document.getElementById('bannerMethod').innerHTML = '';
    document.getElementById('bannerForm').reset();
    document.getElementById('currentImagePreview').style.display = 'none';
});
</script>
@endpush
@endsection
