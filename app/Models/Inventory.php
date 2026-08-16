<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Inventory extends Model
{
    protected $table = 'inventory';

    protected $fillable = [
        'brand_id', 'model_number', 'bq_number', 'name', 'description',
        'about_brand', 'slug',
        'category', 'gender', 'frame_shape', 'frame_material',
        'frame_color', 'frame_size', 'lens_type', 'price', 'sale_price',
        'currency', 'image_url', 'additional_images',
        'stock_quantity', 'is_active', 'is_new_arrival',
        'is_on_sale', 'last_synced_at',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'sale_price' => 'decimal:2',
            'additional_images' => 'array',
            'is_active' => 'boolean',
            'is_new_arrival' => 'boolean',
            'is_on_sale' => 'boolean',
            'last_synced_at' => 'datetime',
        ];
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function changes(): HasMany
    {
        return $this->hasMany(InventoryChange::class)->latest();
    }
}
