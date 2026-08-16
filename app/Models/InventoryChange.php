<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryChange extends Model
{
    protected $fillable = ['inventory_id', 'field', 'old_value', 'new_value'];

    public static function label(string $field): string
    {
        return [
            'created' => 'Added',
            'model_number' => 'Model',
            'bq_number' => 'BQ Number',
            'name' => 'Name',
            'description' => 'Description',
            'about_brand' => 'About Brand',
            'category' => 'Category',
            'gender' => 'Gender',
            'frame_shape' => 'Shape',
            'frame_material' => 'Material',
            'frame_color' => 'Color',
            'frame_size' => 'Size',
            'lens_type' => 'Lens',
            'price' => 'Price',
            'sale_price' => 'Sale Price',
            'currency' => 'Currency',
            'image_url' => 'Image',
            'additional_images' => 'Gallery',
            'stock_quantity' => 'Stock',
            'is_active' => 'Active',
            'is_new_arrival' => 'New Arrival',
            'is_on_sale' => 'On Sale',
            'brand_id' => 'Brand',
        ][$field] ?? ucfirst(str_replace('_', ' ', $field));
    }

    public function inventory(): BelongsTo
    {
        return $this->belongsTo(Inventory::class);
    }
}