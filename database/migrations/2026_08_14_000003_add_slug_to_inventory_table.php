<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inventory', function (Blueprint $table) {
            $table->string('slug')->nullable()->unique()->after('id');
        });

        $used = [];
        DB::table('inventory')->orderBy('id')->get()->each(function ($row) use (&$used) {
            $base = Str::slug($row->name ?: $row->model_number) ?: Str::slug($row->model_number);
            $slug = $base;
            $n = 2;
            while (isset($used[$slug]) || DB::table('inventory')->where('slug', $slug)->where('id', '!=', $row->id)->exists()) {
                $slug = $base . '-' . $n++;
            }
            $used[$slug] = true;
            DB::table('inventory')->where('id', $row->id)->update(['slug' => $slug]);
        });
    }

    public function down(): void
    {
        Schema::table('inventory', function (Blueprint $table) {
            $table->dropUnique(['slug']);
            $table->dropColumn('slug');
        });
    }
};
