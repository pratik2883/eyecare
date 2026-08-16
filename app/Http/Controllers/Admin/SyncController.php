<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inventory;
use App\Models\SyncLog;
use Illuminate\Http\Request;

class SyncController extends Controller
{
    public function index()
    {
        $syncLogs = SyncLog::latest()->paginate(20);
        $pendingChanges = Inventory::where('updated_at', '>', now()->subDays(7))
            ->with('brand')
            ->with(['changes' => fn ($q) => $q->take(12)])
            ->latest('updated_at')
            ->take(50)
            ->get();

        $lanIp = $this->lanIp();
        $port = $_SERVER['SERVER_PORT'] ?? '8000';

        return view('admin.sync.index', compact('syncLogs', 'pendingChanges', 'lanIp', 'port'));
    }

    private function lanIp(): string
    {
        $sock = @fsockopen('udp://8.8.8.8', 53, $errno, $errstr, 1);
        if ($sock) {
            $name = @stream_socket_get_name($sock, false);
            @fclose($sock);
            if ($name && preg_match('/^(\d{1,3}(?:\.\d{1,3}){3}):/', $name, $m)) {
                return $m[1];
            }
        }

        $host = @gethostbyname(gethostname());
        if (is_string($host) && filter_var($host, FILTER_VALIDATE_IP) && !str_starts_with($host, '127.')) {
            return $host;
        }

        return $_SERVER['SERVER_ADDR'] ?? '127.0.0.1';
    }

    public function triggerSync()
    {
        SyncLog::create([
            'tablet_ip' => request()->ip(),
            'tablet_name' => 'Admin (manual trigger)',
            'status' => 'pending',
            'products_count' => Inventory::where('updated_at', '>', now()->subDay())->count(),
        ]);

        return redirect()->route('admin.sync.index')->with('success', 'Sync triggered. Tablets will pull updates on next check.');
    }
}
