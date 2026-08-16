<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('icon')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('materials', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Titanium, Acetate, Metal, etc.
            $table->string('slug')->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('shapes', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Aviator, Wayfarer, Round, etc.
            $table->string('slug')->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('colors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('hex_code')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('banners', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('subtitle')->nullable();
            $table->string('image_url');
            $table->string('link_url')->nullable();
            $table->string('background_color')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('promos', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('subtitle')->nullable();
            $table->string('image_url')->nullable();
            $table->string('background_gradient')->nullable();
            $table->string('tag_text')->nullable();
            $table->string('link_url')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('sync_logs', function (Blueprint $table) {
            $table->id();
            $table->string('tablet_ip')->nullable();
            $table->string('tablet_name')->nullable();
            $table->timestamp('last_synced_at')->nullable();
            $table->string('status')->default('pending'); // pending, syncing, success, failed
            $table->text('error_message')->nullable();
            $table->integer('products_count')->default(0);
            $table->timestamps();
        });

        Schema::create('color_material_mappings', function (Blueprint $table) {
            $table->id();
            $table->string('raw_value');
            $table->string('type'); // color or material
            $table->string('mapped_value');
            $table->timestamps();

            $table->unique(['raw_value', 'type']);
        });

        Schema::table('inventory', function (Blueprint $table) {
            if (!Schema::hasColumn('inventory', 'bq_number')) {
                $table->string('bq_number')->nullable()->after('model_number');
            }
            if (!Schema::hasColumn('inventory', 'frame_size')) {
                $table->string('frame_size')->nullable()->after('frame_color');
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('color_material_mappings');
        Schema::dropIfExists('sync_logs');
        Schema::dropIfExists('promos');
        Schema::dropIfExists('banners');
        Schema::dropIfExists('colors');
        Schema::dropIfExists('shapes');
        Schema::dropIfExists('materials');
        Schema::dropIfExists('categories');
    }
};
