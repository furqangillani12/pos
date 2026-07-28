<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Client update 24/7 #1: fragile products can carry a packing charge with a
        // custom title. Set per product; snapshotted onto each order line so a later
        // price change never rewrites old bills; summed into orders.packing_total.
        if (! Schema::hasColumn('products', 'packing_charge')) {
            Schema::table('products', function (Blueprint $table) {
                $table->decimal('packing_charge', 10, 2)->default(0)->after('weight');
                $table->string('packing_label')->nullable()->after('packing_charge');
            });
        }

        if (! Schema::hasColumn('order_items', 'packing_charge')) {
            Schema::table('order_items', function (Blueprint $table) {
                $table->decimal('packing_charge', 10, 2)->default(0)->after('total_price');
                $table->string('packing_label')->nullable()->after('packing_charge');
            });
        }

        if (! Schema::hasColumn('orders', 'packing_total')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->decimal('packing_total', 10, 2)->default(0)->after('delivery_charges');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('products', 'packing_charge')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropColumn(['packing_charge', 'packing_label']);
            });
        }
        if (Schema::hasColumn('order_items', 'packing_charge')) {
            Schema::table('order_items', function (Blueprint $table) {
                $table->dropColumn(['packing_charge', 'packing_label']);
            });
        }
        if (Schema::hasColumn('orders', 'packing_total')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropColumn('packing_total');
            });
        }
    }
};
