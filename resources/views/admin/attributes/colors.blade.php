@extends('admin.layouts.master')
@section('title', 'Colors')
@section('content')
<div class="page-header">
    <h1>Colors</h1>
    <button class="btn btn-primary" onclick="openModal('colorModal')"><i class="fas fa-plus"></i> Add Color</button>
</div>
<div class="card">
    <div class="card-body" style="padding:0">
        <div class="table-wrapper">
            <table>
                <thead><tr><th>Name</th><th>Slug</th><th>Hex</th><th>Swatch</th><th>Status</th><th>Actions</th></tr></thead>
                <tbody>
                    @foreach($colors as $c)
                    <tr>
                        <td><strong>{{ $c->name }}</strong></td>
                        <td style="font-family:monospace;font-size:.75rem;color:var(--text-secondary)">{{ $c->slug }}</td>
                        <td>{{ $c->hex_code ?? '—' }}</td>
                        <td>@if($c->hex_code)<span style="display:inline-block;width:24px;height:24px;border-radius:50%;background:{{ $c->hex_code }};border:1px solid var(--border)"></span>@else—@endif</td>
                        <td><span class="badge badge-{{ $c->is_active ? 'success' : 'default' }}">{{ $c->is_active ? 'Active' : 'Inactive' }}</span></td>
                        <td>
                            <button class="btn btn-sm btn-secondary" onclick="editColor({{ $c->id }},{{ json_encode($c->name) }},{{ json_encode($c->hex_code) }})"><i class="fas fa-edit"></i></button>
                            <form action="{{ route('admin.attributes.colors.destroy', $c) }}" method="POST" onsubmit="return confirmDelete()" style="display:inline">@csrf @method('DELETE')<button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button></form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
<div id="colorModal" class="modal">
    <div class="modal-header"><h2 id="colorModalTitle">Add Color</h2><button class="modal-close" onclick="closeAllModals()">&times;</button></div>
    <form id="colorForm" method="POST" action="{{ route('admin.attributes.colors.store') }}">@csrf<div id="colorMethod"></div>
    <div class="modal-body">
        <div class="form-group"><label>Color Name</label><input type="text" name="name" class="form-control" required></div>
        <div class="form-group"><label>Hex Code</label><input type="text" name="hex_code" class="form-control" placeholder="#FF5733"></div>
    </div>
    <div class="modal-footer"><button type="button" class="btn btn-secondary" onclick="closeAllModals()">Cancel</button><button type="submit" class="btn btn-primary">Save</button></div>
    </form>
</div>
@push('scripts')
<script>
function editColor(id,name,hex){
    document.getElementById('colorModalTitle').textContent = 'Edit Color';
    const f = document.getElementById('colorForm');
    f.action = '/admin/attributes/colors/' + id;
    document.getElementById('colorMethod').innerHTML = '<input type="hidden" name="_method" value="PUT">';
    f.querySelector('[name=name]').value = name;
    f.querySelector('[name=hex_code]').value = hex || '';
    openModal('colorModal');
}
document.querySelector('[onclick="openModal(\'colorModal\')"]')?.addEventListener('click',function(){
    document.getElementById('colorModalTitle').textContent = 'Add Color';
    document.getElementById('colorForm').action = '{{ route('admin.attributes.colors.store') }}';
    document.getElementById('colorMethod').innerHTML = '';
    document.getElementById('colorForm').reset();
});
</script>
@endpush
@endsection
