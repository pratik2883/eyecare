<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ColorMaterialMapping extends Model
{
    protected $table = 'color_material_mappings';

    protected $fillable = ['raw_value', 'type', 'mapped_value'];
}
