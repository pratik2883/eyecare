@extends('admin.layouts.master')
@section('title', 'Sync & Network')
@section('content')
<div class="page-header">
    <h1>Sync & Network Settings</h1>
    <form action="{{ route('admin.sync.trigger') }}" method="POST" style="display:inline">
        @csrf
        <button type="submit" class="btn btn-primary"><i class="fas fa-sync-alt"></i> Trigger Force Sync</button>
    </form>
</div>

<div class="sync-status-grid">
    <div class="card">
        <div class="card-header"><h3>Server Status</h3></div>
        <div class="card-body">
            <div style="display:flex;flex-direction:column;gap:12px">
                <div style="display:flex;justify-content:space-between;align-items:center;padding:10px 0;border-bottom:1px solid var(--border)">
                    <span style="color:var(--text-secondary)">WiFi / LAN IP</span>
                    <span style="font-family:monospace;font-weight:700;color:var(--plum);font-size:1rem">{{ $lanIp }}</span>
                </div>
                <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid var(--border)">
                    <span style="color:var(--text-secondary)">Port</span>
                    <span style="font-family:monospace;font-weight:600">{{ $port }}</span>
                </div>
                <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid var(--border)">
                    <span style="color:var(--text-secondary)">Store URL (tablet/mobile)</span>
                    <span style="font-family:monospace;font-size:.72rem;color:var(--plum)">http://{{ $lanIp }}:{{ $port }}/</span>
                </div>
                <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid var(--border)">
                    <span style="color:var(--text-secondary)">Admin Panel URL</span>
                    <span style="font-family:monospace;font-size:.72rem;color:var(--plum)">http://{{ $lanIp }}:{{ $port }}/admin</span>
                </div>
                <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid var(--border)">
                    <span style="color:var(--text-secondary)">API Base URL</span>
                    <span style="font-family:monospace;font-size:.72rem;color:var(--plum)">http://{{ $lanIp }}:{{ $port }}/api/v1</span>
                </div>
                <div style="display:flex;justify-content:space-between;padding:8px 0">
                    <span style="color:var(--text-secondary)">Server Status</span>
                    <span class="badge badge-success">Running</span>
                </div>
            </div>
        </div>
    </div>

    <div class="card" style="grid-column:1/-1">
        <div class="card-header"><h3>How to Connect Tablets / Mobiles</h3></div>
        <div class="card-body" style="font-size:.8rem;line-height:1.7;color:var(--text-secondary)">
            <ol style="padding-left:18px;margin:0">
                <li>Make sure the server is running on the <strong>same WiFi network</strong> as the tablets.</li>
                <li>Make sure the server is running on <strong>your LAN IP</strong> (0.0.0.0) — otherwise it will only be accessible on localhost.</li>
                <li>Open <code style="font-family:monospace;color:var(--plum)">http://{{ $lanIp }}:{{ $port }}/</code> in the tablet/mobile browser.</li>
                <li>Once the page loads, <strong>service worker + auto-sync</strong> activate automatically — product updates will arrive every 30 seconds.</li>
                <li>Allow inbound connections on port <strong>{{ $port }}</strong> in Windows Firewall (otherwise the page won't open on other devices).</li>
            </ol>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><h3>Quick Sync Info</h3></div>
        <div class="card-body">
            <div style="display:flex;flex-direction:column;gap:12px">
                <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid var(--border)">
                    <span style="color:var(--text-secondary)">Total Products</span>
                    <span style="font-weight:600">{{ \App\Models\Inventory::count() }}</span>
                </div>
                <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid var(--border)">
                    <span style="color:var(--text-secondary)">Pending Changes (7d)</span>
                    <span style="font-weight:600">{{ \App\Models\Inventory::where('updated_at','>',now()->subDays(7))->count() }}</span>
                </div>
                <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid var(--border)">
                    <span style="color:var(--text-secondary)">Sync Logs</span>
                    <span style="font-weight:600">{{ \App\Models\SyncLog::count() }}</span>
                </div>
                <div style="display:flex;justify-content:space-between;padding:8px 0">
                    <span style="color:var(--text-secondary)">Last Sync</span>
                    <span>{{ \App\Models\SyncLog::latest()->first()?->created_at?->diffForHumans() ?? 'Never' }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card" style="margin-bottom:20px">
    <div class="card-header">
        <h3>Pending Sync Changes (Delta)</h3>
    </div>
    <div class="card-body" style="padding:0">
        <div class="table-wrapper">
            <table>
                <thead><tr><th>Brand</th><th>Model</th><th>Updated</th><th>Changes (Delta)</th></tr></thead>
                <tbody>
                    @forelse($pendingChanges as $p)
                    <tr>
                        <td><strong>{{ $p->brand->name ?? '—' }}</strong></td>
                        <td>{{ $p->model_number }}</td>
                        <td>{{ $p->updated_at->diffForHumans() }}</td>
                        <td style="max-width:420px">
                            @if($p->changes->isEmpty())
                            <span class="badge badge-warning">Updated</span>
                            @else
                            <div style="display:flex;flex-wrap:wrap;gap:6px">
                                @foreach($p->changes->take(6) as $chg)
                                <span class="delta-chip">
                                    <strong>{{ \App\Models\InventoryChange::label($chg->field) }}</strong>
                                    @if($chg->field === 'created')
                                    <span class="delta-new">+ New</span>
                                    @else
                                    <span class="delta-diff">{{ $chg->old_value ?? '—' }} → {{ $chg->new_value ?? '—' }}</span>
                                    @endif
                                </span>
                                @endforeach
                                @if($p->changes->count() > 6)
                                <span class="delta-more">+{{ $p->changes->count() - 6 }} more</span>
                                @endif
                            </div>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="empty-state"><p>No pending changes</p></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3>Sync History Log</h3>
    </div>
    <div class="card-body" style="padding:0">
        <div class="table-wrapper">
            <table>
                <thead><tr><th>Tablet IP</th><th>Name</th><th>Synced At</th><th>Products</th><th>Status</th><th>Error</th></tr></thead>
                <tbody>
                    @forelse($syncLogs as $log)
                    <tr>
                        <td style="font-family:monospace">{{ $log->tablet_ip ?? '—' }}</td>
                        <td>{{ $log->tablet_name ?? '—' }}</td>
                        <td>{{ $log->last_synced_at ? $log->last_synced_at->format('d M Y H:i') : '—' }}</td>
                        <td>{{ $log->products_count }}</td>
                        <td><span class="badge badge-{{ $log->status==='success'?'success':($log->status==='failed'?'danger':'warning') }}">{{ $log->status }}</span></td>
                        <td style="max-width:150px;overflow:hidden;text-overflow:ellipsis;font-size:.72rem;color:var(--text-secondary)">{{ $log->error_message ?? '—' }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="empty-state"><p>No sync history yet</p></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div style="padding:16px 20px">{{ $syncLogs->links() }}</div>
    </div>
</div>
@endsection
