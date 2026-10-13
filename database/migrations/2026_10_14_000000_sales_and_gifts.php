<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Sales (% off chosen products for chosen customer types) and gift products. Guarded. */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('sales')) {
            Schema::create('sales', function (Blueprint $t) {
                $t->id();
                $t->unsignedBigInteger('branch_id')->nullable()->index();
                $t->string('name');
                $t->decimal('discount_percent', 5, 2);
                $t->json('applies_to');               // ["customer","reseller","wholesale"]
                $t->dateTime('starts_at')->nullable();
                $t->dateTime('ends_at')->nullable();
                $t->boolean('is_active')->default(true);
                $t->timestamps();
            });
        }
        if (!Schema::hasTable('sale_product')) {
            Schema::create('sale_product', function (Blueprint $t) {
                $t->unsignedBigInteger('sale_id');
                $t->unsignedBigInteger('product_id')->index();
                $t->primary(['sale_id', 'product_id']);
            });
        }
        if (!Schema::hasTable('gifts')) {
            Schema::create('gifts', function (Blueprint $t) {
                $t->id();
                $t->unsignedBigInteger('branch_id')->nullable()->index();
                $t->unsignedBigInteger('product_id')->index();
                $t->decimal('price', 10, 2)->default(0);
                $t->boolean('is_active')->default(true);
                $t->unsignedInteger('sort_order')->default(0);
                $t->timestamps();
            });
        }
        if (!Schema::hasColumn('cart_items', 'is_gift')) {
            Schema::table('cart_items', fn (Blueprint $t) => $t->boolean('is_gift')->default(false)->after('selected_color'));
        }
        if (!Schema::hasColumn('order_items', 'is_gift')) {
            Schema::table('order_items', fn (Blueprint $t) => $t->boolean('is_gift')->default(false)->after('packing_label'));
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('sale_product');
        Schema::dropIfExists('sales');
        Schema::dropIfExists('gifts');
        foreach (['cart_items', 'order_items'] as $table) {
            if (Schema::hasColumn($table, 'is_gift')) {
                Schema::table($table, fn (Blueprint $t) => $t->dropColumn('is_gift'));
            }
        }
    }
};
