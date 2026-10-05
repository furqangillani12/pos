<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Colour × size variants (Daraz-style). Each variant row is one combination with
 * its own prices, stock (at the product's branch), barcode and status.
 *  - product_variants : + color, size, sku/barcode, prices, stock (decimal), active, sort
 *                       (old variant_name / variant_value / price_adjustment kept, nullable)
 *  - products         : has_variants flag, color_images JSON {colour: image path}
 *  - cart_items       : variant_id
 *  - order_items      : variant_label snapshot ("Black / 54")
 * All guarded, safe to re-run.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_variants', function (Blueprint $table) {
            if (!Schema::hasColumn('product_variants', 'color'))           $table->string('color', 100)->nullable()->after('product_id');
            if (!Schema::hasColumn('product_variants', 'size'))            $table->string('size', 100)->nullable()->after('color');
            if (!Schema::hasColumn('product_variants', 'barcode'))         $table->string('barcode', 100)->nullable()->index()->after('size');
            if (!Schema::hasColumn('product_variants', 'sale_price'))      $table->decimal('sale_price', 10, 2)->nullable()->after('barcode');
            if (!Schema::hasColumn('product_variants', 'resale_price'))    $table->decimal('resale_price', 10, 2)->nullable()->after('sale_price');
            if (!Schema::hasColumn('product_variants', 'wholesale_price')) $table->decimal('wholesale_price', 10, 2)->nullable()->after('resale_price');
            if (!Schema::hasColumn('product_variants', 'is_active'))       $table->boolean('is_active')->default(true)->after('stock');
            if (!Schema::hasColumn('product_variants', 'sort_order'))      $table->unsignedInteger('sort_order')->default(0)->after('is_active');
        });
        // Legacy columns become optional; stock allows fractions like the rest of the app.
        DB::statement('ALTER TABLE product_variants MODIFY variant_name VARCHAR(255) NULL, MODIFY variant_value VARCHAR(255) NULL, MODIFY stock DECIMAL(10,2) NOT NULL DEFAULT 0');

        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'has_variants')) $table->boolean('has_variants')->default(false)->after('track_inventory');
            if (!Schema::hasColumn('products', 'color_images')) $table->json('color_images')->nullable()->after('gallery');
        });

        if (!Schema::hasColumn('cart_items', 'variant_id')) {
            Schema::table('cart_items', fn (Blueprint $t) => $t->unsignedBigInteger('variant_id')->nullable()->index()->after('product_id'));
        }
        if (!Schema::hasColumn('order_items', 'variant_label')) {
            Schema::table('order_items', fn (Blueprint $t) => $t->string('variant_label', 191)->nullable()->after('variant_id'));
        }
    }

    public function down(): void
    {
        foreach (['color', 'size', 'barcode', 'sale_price', 'resale_price', 'wholesale_price', 'is_active', 'sort_order'] as $c) {
            if (Schema::hasColumn('product_variants', $c)) Schema::table('product_variants', fn (Blueprint $t) => $t->dropColumn($c));
        }
        foreach (['has_variants', 'color_images'] as $c) {
            if (Schema::hasColumn('products', $c)) Schema::table('products', fn (Blueprint $t) => $t->dropColumn($c));
        }
        if (Schema::hasColumn('cart_items', 'variant_id')) Schema::table('cart_items', fn (Blueprint $t) => $t->dropColumn('variant_id'));
        if (Schema::hasColumn('order_items', 'variant_label')) Schema::table('order_items', fn (Blueprint $t) => $t->dropColumn('variant_label'));
    }
};
