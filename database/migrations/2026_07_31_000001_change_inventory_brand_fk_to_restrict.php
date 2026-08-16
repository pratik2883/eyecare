<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inventory', function (Blueprint $table) {
            $table->dropForeign(['brand_id']);
            $table->foreign('brand_id')->references('id')->on('brands')->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::table('inventory', function (Blueprint $table) {
            $table->dropForeign(['brand_id']);
            $table->foreign('brand_id')->references('id')->on('brands')->onDelete('cascade');
        });
    }
};
