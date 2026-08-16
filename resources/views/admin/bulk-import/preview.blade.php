@extends('admin.layouts.master')
@section('title', 'Preview Import')
@section('content')
<div class="page-header">
    <h1>Preview Import <small style="font-weight:400;font-size:.7rem;color:var(--text-light)">{{ count($sheet) }} rows</small></h1>
    <a href="{{ route('admin.bulk-import.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back</a>
</div>

<div class="card">
    <div class="card-header"><h3>Column Mapping & Data Preview</h3></div>
    <div class="card-body">
        <form action="{{ route('admin.bulk-import.import') }}" method="POST">
            @csrf
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            @foreach($headers as $h)
                            <th>{{ $h }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach(array_slice($sheet, 0, 10) as $idx => $row)
                        <tr>
                            <td>{{ $idx + 2 }}</td>
                            @foreach($headers as $hi => $h)
                            <td>{{ Str::limit($row[$hi] ?? '', 30) }}</td>
                            @endforeach
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if(count($sheet) > 10)
            <p style="font-size:.75rem;color:var(--text-light);margin-top:8px">Showing first 10 of {{ count($sheet) }} rows.</p>
            @endif

            <div style="margin-top:20px">
                <h4 style="font-size:.85rem;margin-bottom:12px">Map Columns</h4>
                <div class="form-row">
                    @foreach(['model_number'=>'Model Number *','brand'=>'Brand','bq_number'=>'BQ Number','name'=>'Name','category'=>'Category','gender'=>'Gender','price'=>'Price *','sale_price'=>'Sale Price','frame_color'=>'Color','frame_material'=>'Material','frame_shape'=>'Shape','frame_size'=>'Size','about_brand'=>'About Brand','stock_quantity'=>'Stock','image_url'=>'Image URL','gallery_images'=>'Gallery Images','description'=>'Description'] as $field => $label)
                    <div class="form-group">
                        <label>{{ $label }}</label>
                        <select name="mapping[{{ $field }}]" class="form-control">
                            <option value="">— Skip —</option>
                            @foreach($headers as $hi => $h)
                            <option value="{{ $hi }}" {{ Str::slug($h) == $field || strtolower($h) == strtolower($field) ? 'selected' : '' }}>{{ $h }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endforeach
                </div>
            </div>

            <input type="hidden" name="import_id" value="{{ $importId }}">

            <div style="margin-top:16px">
                <button type="submit" class="btn btn-success"><i class="fas fa-check"></i> Import {{ count($sheet) }} Products</button>
                <a href="{{ route('admin.bulk-import.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
