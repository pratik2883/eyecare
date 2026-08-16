<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory', function (Blueprint $table) {
            $table->id();
            $table->foreignId('brand_id')->constrained()->onDelete('cascade');
            $table->string('model_number');
            $table->string('name')->nullable();
            $table->text('description')->nullable();
            $table->string('category'); // eyeglasses, sunglasses, contact_lenses, accessories, kids
            $table->string('gender')->nullable(); // male, female, unisex
            $table->string('frame_shape')->nullable(); // round, square, rectangular, cat-eye, aviator, etc.
            $table->string('frame_material')->nullable(); // metal, acetate, titanium, etc.
            $table->string('frame_color')->nullable();
            $table->string('lens_type')->nullable();
            $table->decimal('price', 10, 2);
            $table->decimal('sale_price', 10, 2)->nullable();
            $table->string('currency', 3)->default('INR');
            $table->string('image_url')->nullable();
            $table->json('additional_images')->nullable();
            $table->integer('stock_quantity')->default(0);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_new_arrival')->default(false);
            $table->boolean('is_on_sale')->default(false);
            $table->timestamp('last_synced_at')->nullable();
            $table->timestamps();

            $table->index('model_number');
            $table->index('category');
            $table->index('is_active');
            $table->index('updated_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory');
    }
};
