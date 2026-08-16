<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SyncLog extends Model
{
    protected $fillable = ['tablet_ip', 'tablet_name', 'last_synced_at', 'status', 'error_message', 'products_count'];

    protected function casts(): array
    {
        return [
            'last_synced_at' => 'datetime',
            'products_count' => 'integer',
        ];
    }
}
