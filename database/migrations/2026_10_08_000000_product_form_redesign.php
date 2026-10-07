<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Add/Edit Product redesign (client design, Oct 2026):
 *  - products: Urdu name / note / description, sub-category, price grid breakdown
 *    (per column: list price, discount, charges → final), billed other charges
 *    (title + amount rows), how-to video (upload or link)
 *  - product_variants: list price, weight, reorder level and images per row
 *  - product_sizes / product_colors: managed lists for the size / colour dropdowns
 * All guarded, safe to re-run.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $t) {
            if (!Schema::hasColumn('products', 'name_ur'))         $t->string('name_ur')->nullable()->after('name');
            if (!Schema::hasColumn('products', 'description_ur'))  $t->text('description_ur')->nullable()->after('description');
            if (!Schema::hasColumn('products', 'note_ur'))         $t->text('note_ur')->nullable()->after('note');
            if (!Schema::hasColumn('products', 'subcategory_id'))  $t->unsignedBigInteger('subcategory_id')->nullable()->index()->after('category_id');
            if (!Schema::hasColumn('products', 'price_breakdown')) $t->json('price_breakdown')->nullable();
            if (!Schema::hasColumn('products', 'other_charges'))   $t->json('other_charges')->nullable();
            if (!Schema::hasColumn('products', 'video'))           $t->string('video')->nullable();
            if (!Schema::hasColumn('products', 'video_url'))       $t->string('video_url', 500)->nullable();
        });

        Schema::table('product_variants', function (Blueprint $t) {
            if (!Schema::hasColumn('product_variants', 'base_price'))    $t->decimal('base_price', 10, 2)->nullable()->after('barcode');
            if (!Schema::hasColumn('product_variants', 'weight'))        $t->decimal('weight', 10, 4)->nullable()->after('stock');
            if (!Schema::hasColumn('product_variants', 'reorder_level')) $t->decimal('reorder_level', 10, 2)->nullable()->after('weight');
            if (!Schema::hasColumn('product_variants', 'images'))        $t->json('images')->nullable()->after('reorder_level');
        });

        foreach (['product_sizes', 'product_colors'] as $table) {
            if (!Schema::hasTable($table)) {
                Schema::create($table, function (Blueprint $t) {
                    $t->id();
                    $t->string('name', 100)->unique();
                    $t->unsignedInteger('sort_order')->default(0);
                    $t->boolean('is_active')->default(true);
                    $t->timestamps();
                });
            }
        }
    }

    public function down(): void
    {
        foreach (['name_ur', 'description_ur', 'note_ur', 'subcategory_id', 'price_breakdown', 'other_charges', 'video', 'video_url'] as $c) {
            if (Schema::hasColumn('products', $c)) Schema::table('products', fn (Blueprint $t) => $t->dropColumn($c));
        }
        foreach (['base_price', 'weight', 'reorder_level', 'images'] as $c) {
            if (Schema::hasColumn('product_variants', $c)) Schema::table('product_variants', fn (Blueprint $t) => $t->dropColumn($c));
        }
        Schema::dropIfExists('product_sizes');
        Schema::dropIfExists('product_colors');
    }
};
