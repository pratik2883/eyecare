@extends('admin.layouts.master')
@section('title', 'Dashboard')
@section('content')
<div class="page-header"><h1>Dashboard</h1></div>

<div class="metric-grid">
    <div class="metric-card">
        <div class="metric-label">Total Products</div>
        <div class="metric-value">{{ number_format($totalProducts) }}</div>
        <div class="metric-sub">Across all brands & categories</div>
    </div>
    <div class="metric-card">
        <div class="metric-label">Active Brands</div>
        <div class="metric-value">{{ $activeBrands }}</div>
        <div class="metric-sub">Featured & active</div>
    </div>
    <div class="metric-card">
        <div class="metric-label">Unsynced Tablets</div>
        <div class="metric-value">{{ $unsyncedTablets }}</div>
        <div class="metric-sub">Pending sync pull</div>
    </div>
    <div class="metric-card">
        <div class="metric-label">Pending Changes</div>
        <div class="metric-value">{{ $pendingChanges }}</div>
        <div class="metric-sub">Updated in last 24h</div>
    </div>
</div>

<div class="quick-actions">
    <a href="{{ route('admin.inventory.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Add New Frame</a>
    <a href="{{ route('admin.bulk-import.index') }}" class="btn btn-gold"><i class="fas fa-file-upload"></i> Bulk Upload Excel</a>
    <form action="{{ route('admin.sync.trigger') }}" method="POST" style="display:inline">
        @csrf
        <button type="submit" class="btn btn-outline"><i class="fas fa-sync-alt"></i> Trigger Force Wi-Fi Sync</button>
    </form>
</div>

<div class="card">
    <div class="card-header">
        <h3>Recent Sync Activity</h3>
        <a href="{{ route('admin.sync.index') }}" class="btn btn-sm btn-secondary">View All</a>
    </div>
    <div class="card-body" style="padding:0">
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Tablet IP</th>
                        <th>Name</th>
                        <th>Last Synced</th>
                        <th>Products</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentSyncs as $log)
                    <tr>
                        <td>{{ $log->tablet_ip ?? '—' }}</td>
                        <td>{{ $log->tablet_name ?? '—' }}</td>
                        <td>{{ $log->last_synced_at ? $log->last_synced_at->diffForHumans() : 'Never' }}</td>
                        <td>{{ $log->products_count }}</td>
                        <td><span class="badge badge-{{ $log->status === 'success' ? 'success' : ($log->status === 'failed' ? 'danger' : 'warning') }}">{{ $log->status }}</span></td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="empty-state"><p>No sync records yet</p></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
