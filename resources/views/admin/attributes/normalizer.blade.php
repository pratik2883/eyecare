@extends('admin.layouts.master')
@section('title', 'Color & Material Normalizer')
@section('content')
<div class="page-header">
    <h1>Color & Material Normalizer</h1>
    <form action="{{ route('admin.attributes.normalizer.apply') }}" method="POST" style="display:inline" onsubmit="return confirm('Apply all mappings to existing products? This will update {{ \App\Models\Inventory::count() }} products.')">
        @csrf
        <button type="submit" class="btn btn-success"><i class="fas fa-magic"></i> Apply All to Products</button>
    </form>
</div>

<div class="metric-grid" style="margin-bottom:20px">
    <div class="metric-card">
        <div class="metric-label">Raw Colors in DB</div>
        <div class="metric-value">{{ $distinctColors->count() }}</div>
    </div>
    <div class="metric-card">
        <div class="metric-label">Raw Materials in DB</div>
        <div class="metric-value">{{ $distinctMaterials->count() }}</div>
    </div>
    <div class="metric-card">
        <div class="metric-label">Mappings Created</div>
        <div class="metric-value">{{ $mappings->count() }}</div>
    </div>
    <div class="metric-card">
        <div class="metric-label">Master Colors</div>
        <div class="metric-value">{{ $masterColors->count() }}</div>
    </div>
</div>

<div class="tabs">
    <button class="tab active" onclick="switchTab('colorsTab',this)">Unmapped Colors</button>
    <button class="tab" onclick="switchTab('materialsTab',this)">Unmapped Materials</button>
    <button class="tab" onclick="switchTab('mappingsTab',this)">Existing Mappings</button>
</div>

<div id="colorsTab" class="tab-content">
    <div class="card">
        <div class="card-header"><h3>Unmapped Raw Colors</h3></div>
        <div class="card-body" style="padding:0">
            <div class="table-wrapper">
                <table>
                    <thead><tr><th>Raw Value</th><th>Map To</th><th>Action</th></tr></thead>
                    <tbody>
                        @php $mappedColors = $mappings->where('type','color')->pluck('raw_value')->toArray(); @endphp
                        @forelse($distinctColors as $color)
                        @if(!in_array($color, $mappedColors))
                        <tr>
                            <td class="normalizer-table">{{ $color }}</td>
                            <td>
                                <form action="{{ route('admin.attributes.normalizer.store') }}" method="POST" style="display:flex;gap:6px;align-items:center">
                                    @csrf
                                    <input type="hidden" name="raw_value" value="{{ $color }}">
                                    <input type="hidden" name="type" value="color">
                                    <select name="mapped_value" class="form-control" style="width:auto;padding:4px 8px;font-size:.75rem" required>
                                        <option value="">Select master color</option>
                                        @foreach($masterColors as $mc)
                                        <option value="{{ $mc }}">{{ $mc }}</option>
                                        @endforeach
                                    </select>
                                    <button class="btn btn-sm btn-primary">Map</button>
                                </form>
                            </td>
                            <td><span class="badge badge-warning">Unmapped</span></td>
                        </tr>
                        @endif
                        @empty
                        <tr><td colspan="3" class="empty-state"><p>All colors are mapped!</p></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div id="materialsTab" class="tab-content" style="display:none">
    <div class="card">
        <div class="card-header"><h3>Unmapped Raw Materials</h3></div>
        <div class="card-body" style="padding:0">
            <div class="table-wrapper">
                <table>
                    <thead><tr><th>Raw Value</th><th>Map To</th><th>Action</th></tr></thead>
                    <tbody>
                        @php $mappedMats = $mappings->where('type','material')->pluck('raw_value')->toArray(); @endphp
                        @forelse($distinctMaterials as $mat)
                        @if(!in_array($mat, $mappedMats))
                        <tr>
                            <td class="normalizer-table">{{ $mat }}</td>
                            <td>
                                <form action="{{ route('admin.attributes.normalizer.store') }}" method="POST" style="display:flex;gap:6px;align-items:center">
                                    @csrf
                                    <input type="hidden" name="raw_value" value="{{ $mat }}">
                                    <input type="hidden" name="type" value="material">
                                    <select name="mapped_value" class="form-control" style="width:auto;padding:4px 8px;font-size:.75rem" required>
                                        <option value="">Select master material</option>
                                        @foreach($masterMaterials as $mm)
                                        <option value="{{ $mm }}">{{ $mm }}</option>
                                        @endforeach
                                    </select>
                                    <button class="btn btn-sm btn-primary">Map</button>
                                </form>
                            </td>
                            <td><span class="badge badge-warning">Unmapped</span></td>
                        </tr>
                        @endif
                        @empty
                        <tr><td colspan="3" class="empty-state"><p>All materials are mapped!</p></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div id="mappingsTab" class="tab-content" style="display:none">
    <div class="card">
        <div class="card-header"><h3>Existing Mappings</h3></div>
        <div class="card-body" style="padding:0">
            <div class="table-wrapper">
                <table>
                    <thead><tr><th>Type</th><th>Raw Value</th><th>→ Mapped Value</th><th>Actions</th></tr></thead>
                    <tbody>
                        @forelse($mappings as $m)
                        <tr>
                            <td><span class="badge badge-{{ $m->type=='color'?'plum':'info' }}">{{ $m->type }}</span></td>
                            <td class="normalizer-table">{{ $m->raw_value }}</td>
                            <td><strong>{{ $m->mapped_value }}</strong></td>
                            <td>
                                <form action="{{ route('admin.attributes.normalizer.destroy', $m) }}" method="POST" onsubmit="return confirmDelete()" style="display:inline">@csrf @method('DELETE')<button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button></form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="empty-state"><p>No mappings created yet.</p></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function switchTab(tabId, btn) {
    document.querySelectorAll('.tab-content').forEach(t => t.style.display = 'none');
    document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
    document.getElementById(tabId).style.display = '';
    btn.classList.add('active');
}
</script>
@endpush
@endsection
