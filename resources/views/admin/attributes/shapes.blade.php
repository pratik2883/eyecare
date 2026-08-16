@extends('admin.layouts.master')
@section('title', 'Shapes')
@section('content')
<div class="page-header">
    <h1>Frame Shapes</h1>
    <button class="btn btn-primary" onclick="openModal('shapeModal')"><i class="fas fa-plus"></i> Add Shape</button>
</div>
<div class="card">
    <div class="card-body" style="padding:0">
        <div class="table-wrapper">
            <table>
                <thead><tr><th>Name</th><th>Slug</th><th>Status</th><th>Actions</th></tr></thead>
                <tbody>
                    @foreach($shapes as $s)
                    <tr>
                        <td><strong>{{ $s->name }}</strong></td>
                        <td style="font-family:monospace;font-size:.75rem;color:var(--text-secondary)">{{ $s->slug }}</td>
                        <td><span class="badge badge-{{ $s->is_active ? 'success' : 'default' }}">{{ $s->is_active ? 'Active' : 'Inactive' }}</span></td>
                        <td>
                            <button class="btn btn-sm btn-secondary" onclick="editShape({{ $s->id }},{{ json_encode($s->name) }})"><i class="fas fa-edit"></i></button>
                            <form action="{{ route('admin.attributes.shapes.destroy', $s) }}" method="POST" onsubmit="return confirmDelete()" style="display:inline">@csrf @method('DELETE')<button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button></form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
<div id="shapeModal" class="modal">
    <div class="modal-header"><h2 id="shapeModalTitle">Add Shape</h2><button class="modal-close" onclick="closeAllModals()">&times;</button></div>
    <form id="shapeForm" method="POST" action="{{ route('admin.attributes.shapes.store') }}">@csrf<div id="shapeMethod"></div>
    <div class="modal-body"><div class="form-group"><label>Shape Name</label><input type="text" name="name" class="form-control" required></div></div>
    <div class="modal-footer"><button type="button" class="btn btn-secondary" onclick="closeAllModals()">Cancel</button><button type="submit" class="btn btn-primary">Save</button></div>
    </form>
</div>
@push('scripts')
<script>
function editShape(id,name){
    document.getElementById('shapeModalTitle').textContent = 'Edit Shape';
    const f = document.getElementById('shapeForm');
    f.action = '/admin/attributes/shapes/' + id;
    document.getElementById('shapeMethod').innerHTML = '<input type="hidden" name="_method" value="PUT">';
    f.querySelector('[name=name]').value = name;
    openModal('shapeModal');
}
document.querySelector('[onclick="openModal(\'shapeModal\')"]')?.addEventListener('click',function(){
    document.getElementById('shapeModalTitle').textContent = 'Add Shape';
    document.getElementById('shapeForm').action = '{{ route('admin.attributes.shapes.store') }}';
    document.getElementById('shapeMethod').innerHTML = '';
    document.getElementById('shapeForm').reset();
});
</script>
@endpush
@endsection
