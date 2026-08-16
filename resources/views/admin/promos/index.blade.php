@extends('admin.layouts.master')
@section('title', 'Promo Grid')
@section('content')
<div class="page-header">
    <h1>Promo Grid Manager</h1>
    <button class="btn btn-primary" onclick="openModal('promoModal')"><i class="fas fa-plus"></i> Add Promo</button>
</div>

<div class="promo-grid-admin">
    @forelse($promos as $promo)
    <div class="promo-card-admin">
        <div class="promo-preview" style="background: {{ $promo->background_gradient ?? 'linear-gradient(135deg, #C8102E, #E2364B)' }}">
            {{ $promo->title }}
            @if($promo->tag_text)<span style="position:absolute;bottom:8px;right:10px;font-size:.55rem;background:rgba(255,255,255,0.2);padding:2px 8px;border-radius:10px">{{ $promo->tag_text }}</span>@endif
        </div>
        <div class="promo-body">
            <h4>{{ $promo->title }}</h4>
            <p>{{ $promo->subtitle ?? '' }}</p>
            <div class="promo-actions">
                <button class="btn btn-sm btn-secondary" onclick="editPromo({{ $promo->id }},{{ json_encode($promo->title) }},{{ json_encode($promo->subtitle) }},{{ json_encode($promo->image_url) }},{{ json_encode($promo->background_gradient) }},{{ json_encode($promo->tag_text) }},{{ json_encode($promo->link_url) }},{{ $promo->sort_order }},{{ $promo->is_active ? 'true' : 'false' }})"><i class="fas fa-edit"></i></button>
                <form action="{{ route('admin.promos.destroy', $promo) }}" method="POST" onsubmit="return confirmDelete()" style="display:inline">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                </form>
                <label class="toggle-switch" style="margin-left:auto">
                    <input type="checkbox" {{ $promo->is_active ? 'checked' : '' }} onchange="fetch('{{ route('admin.promos.update', $promo) }}',{method:'PUT',headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Content-Type':'application/json'},body:JSON.stringify({is_active:this.checked,title:{{ json_encode($promo->title) }},sort_order:{{ $promo->sort_order ?? 'null' }}})}).then(()=>location.reload())">
                    <span class="toggle-slider"></span>
                </label>
            </div>
        </div>
    </div>
    @empty
    <div class="empty-state" style="grid-column:1/-1"><i class="fas fa-ad"></i><p>No promos yet.</p></div>
    @endforelse
</div>

<div id="promoModal" class="modal">
    <div class="modal-header"><h2 id="promoModalTitle">Add Promo</h2><button class="modal-close" onclick="closeAllModals()">&times;</button></div>
    <form id="promoForm" method="POST" action="{{ route('admin.promos.store') }}" enctype="multipart/form-data">
        @csrf
        <div id="promoMethod"></div>
        <div class="modal-body">
            <div class="form-group"><label>Title</label><input type="text" name="title" class="form-control" required></div>
            <div class="form-group"><label>Subtitle</label><input type="text" name="subtitle" class="form-control"></div>
            <div class="form-group"><label>Upload Image</label><input type="file" name="image" id="promoFileInput" class="form-control" accept="image/jpeg,image/png,image/jpg,image/gif,image/webp"><small style="color:var(--text-light)">Max 5MB. JPEG, PNG, JPG, GIF, WebP.</small></div>
            <div class="form-group"><label>OR Image URL</label><input type="url" name="image_url" id="promoImageUrl" class="form-control" placeholder="https://..."></div>
            <div class="form-group" id="currentPromoImagePreview" style="display:none">
                <label>Current Image</label>
                <div><img id="currentPromoImageTag" src="" style="max-height:120px;border-radius:8px"></div>
            </div>
            <div class="form-group"><label>Background Gradient</label><input type="text" name="background_gradient" class="form-control" placeholder="linear-gradient(135deg, #C8102E, #E2364B)"></div>
            <div class="form-row">
                <div class="form-group"><label>Tag Text</label><input type="text" name="tag_text" class="form-control"></div>
                <div class="form-group"><label>Sort Order</label><input type="number" name="sort_order" class="form-control" value="0"></div>
            </div>
            <div class="form-group"><label>Link URL</label><input type="url" name="link_url" class="form-control"></div>
            <div class="form-check"><input type="checkbox" name="is_active" id="promoActive" value="1" checked><label for="promoActive">Active</label></div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="closeAllModals()">Cancel</button>
            <button type="submit" class="btn btn-primary">Save</button>
        </div>
    </form>
</div>

@push('scripts')
<script>
function editPromo(id,title,subtitle,image,bg,tag,link,order,active){
    document.getElementById('promoModalTitle').textContent = 'Edit Promo';
    const form = document.getElementById('promoForm');
    form.action = '/admin/promos/' + id;
    document.getElementById('promoMethod').innerHTML = '<input type="hidden" name="_method" value="PUT">';
    form.querySelector('[name=title]').value = title;
    form.querySelector('[name=subtitle]').value = subtitle;
    form.querySelector('[name=image_url]').value = image;
    form.querySelector('[name=background_gradient]').value = bg;
    form.querySelector('[name=tag_text]').value = tag;
    form.querySelector('[name=link_url]').value = link || '';
    form.querySelector('[name=sort_order]').value = order;
    form.querySelector('[name=is_active]').checked = active;
    document.getElementById('promoFileInput').value = '';
    if (image) {
        document.getElementById('currentPromoImagePreview').style.display = 'block';
        document.getElementById('currentPromoImageTag').src = image;
    } else {
        document.getElementById('currentPromoImagePreview').style.display = 'none';
    }
    openModal('promoModal');
}
document.querySelector('[onclick="openModal(\'promoModal\')"]')?.addEventListener('click',function(){
    document.getElementById('promoModalTitle').textContent = 'Add Promo';
    document.getElementById('promoForm').action = '{{ route('admin.promos.store') }}';
    document.getElementById('promoMethod').innerHTML = '';
    document.getElementById('promoForm').reset();
    document.getElementById('currentPromoImagePreview').style.display = 'none';
});
</script>
@endpush
@endsection
