<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inventory', function (Blueprint $table) {
            $table->index('gender', 'idx_inventory_gender');
            $table->index('frame_shape', 'idx_inventory_frame_shape');
            $table->index('frame_material', 'idx_inventory_frame_material');
            $table->index('frame_color', 'idx_inventory_frame_color');
            $table->index('frame_size', 'idx_inventory_frame_size');
        });

        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE inventory ADD FULLTEXT INDEX ft_inventory_search (model_number, bq_number)');
        }
    }

    public function down(): void
    {
        Schema::table('inventory', function (Blueprint $table) {
            $table->dropIndex('idx_inventory_gender');
            $table->dropIndex('idx_inventory_frame_shape');
            $table->dropIndex('idx_inventory_frame_material');
            $table->dropIndex('idx_inventory_frame_color');
            $table->dropIndex('idx_inventory_frame_size');
        });

        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE inventory DROP INDEX ft_inventory_search');
        }
    }
};
