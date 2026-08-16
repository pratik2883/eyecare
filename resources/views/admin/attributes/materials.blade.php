@extends('admin.layouts.master')
@section('title', 'Materials')
@section('content')
<div class="page-header">
    <h1>Materials</h1>
    <button class="btn btn-primary" onclick="openModal('materialModal')"><i class="fas fa-plus"></i> Add Material</button>
</div>
<div class="card">
    <div class="card-body" style="padding:0">
        <div class="table-wrapper">
            <table>
                <thead><tr><th>Name</th><th>Slug</th><th>Status</th><th>Actions</th></tr></thead>
                <tbody>
                    @foreach($materials as $m)
                    <tr>
                        <td><strong>{{ $m->name }}</strong></td>
                        <td style="font-family:monospace;font-size:.75rem;color:var(--text-secondary)">{{ $m->slug }}</td>
                        <td><span class="badge badge-{{ $m->is_active ? 'success' : 'default' }}">{{ $m->is_active ? 'Active' : 'Inactive' }}</span></td>
                        <td>
                            <button class="btn btn-sm btn-secondary" onclick="editMaterial({{ $m->id }},{{ json_encode($m->name) }})"><i class="fas fa-edit"></i></button>
                            <form action="{{ route('admin.attributes.materials.destroy', $m) }}" method="POST" onsubmit="return confirmDelete()" style="display:inline">@csrf @method('DELETE')<button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button></form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
<div id="materialModal" class="modal">
    <div class="modal-header"><h2 id="materialModalTitle">Add Material</h2><button class="modal-close" onclick="closeAllModals()">&times;</button></div>
    <form id="materialForm" method="POST" action="{{ route('admin.attributes.materials.store') }}">@csrf<div id="materialMethod"></div>
    <div class="modal-body"><div class="form-group"><label>Material Name</label><input type="text" name="name" class="form-control" required></div></div>
    <div class="modal-footer"><button type="button" class="btn btn-secondary" onclick="closeAllModals()">Cancel</button><button type="submit" class="btn btn-primary">Save</button></div>
    </form>
</div>
@push('scripts')
<script>
function editMaterial(id,name){
    document.getElementById('materialModalTitle').textContent = 'Edit Material';
    const f = document.getElementById('materialForm');
    f.action = '/admin/attributes/materials/' + id;
    document.getElementById('materialMethod').innerHTML = '<input type="hidden" name="_method" value="PUT">';
    f.querySelector('[name=name]').value = name;
    openModal('materialModal');
}
document.querySelector('[onclick="openModal(\'materialModal\')"]')?.addEventListener('click',function(){
    document.getElementById('materialModalTitle').textContent = 'Add Material';
    document.getElementById('materialForm').action = '{{ route('admin.attributes.materials.store') }}';
    document.getElementById('materialMethod').innerHTML = '';
    document.getElementById('materialForm').reset();
});
</script>
@endpush
@endsection
