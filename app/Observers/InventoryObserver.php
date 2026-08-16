<?php

namespace App\Observers;

use App\Models\Inventory;
use App\Models\InventoryChange;

class InventoryObserver
{
    private array $ignoreFields = ['updated_at', 'created_at', 'last_synced_at', 'slug'];

    public function created(Inventory $inventory): void
    {
        if (!$inventory->slug) {
            $inventory->slug = $this->generateSlug($inventory);
            $inventory->saveQuietly();
        }

        InventoryChange::create([
            'inventory_id' => $inventory->id,
            'field' => 'created',
            'old_value' => null,
            'new_value' => 'Product added',
        ]);
    }

    public function updating(Inventory $inventory): void
    {
        if (empty($inventory->slug)) {
            $inventory->slug = $this->generateSlug($inventory);
        }

        $dirty = $inventory->getDirty();

        foreach ($dirty as $field => $newValue) {
            if (in_array($field, $this->ignoreFields, true)) {
                continue;
            }

            $oldValue = $inventory->getOriginal($field);

            if ($oldValue === null && ($newValue === null || $newValue === '')) {
                continue;
            }

            if ((string) $oldValue === (string) $newValue) {
                continue;
            }

            InventoryChange::create([
                'inventory_id' => $inventory->id,
                'field' => $field,
                'old_value' => $this->normalize($oldValue),
                'new_value' => $this->normalize($newValue),
            ]);
        }
    }

    private function generateSlug(Inventory $inventory): string
    {
        $base = \Illuminate\Support\Str::slug($inventory->name ?: $inventory->model_number);
        if (!$base) {
            $base = 'product-' . $inventory->id;
        }

        $slug = $base;
        $n = 2;
        while (Inventory::where('slug', $slug)->where('id', '!=', $inventory->id)->exists()) {
            $slug = $base . '-' . $n++;
        }

        return $slug;
    }

    private function normalize($value): ?string
    {
        if ($value === null) {
            return null;
        }

        return is_array($value) ? json_encode($value) : (string) $value;
    }
}