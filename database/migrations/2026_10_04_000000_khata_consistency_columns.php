<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Khata / accounts consistency (Oct 2026 review):
 *  - orders.khata_paid      : part of the bill settled by khata payments (Cash In,
 *                             khata page, website requests), allocated oldest-bill-first.
 *  - order_items.cost_price / retail_price : snapshot at sale time, for profit
 *                             and reseller earnings that don't drift when prices change.
 *  - payments.deleted_at / deleted_by : soft delete + audit for khata payment deletion.
 * All guarded, safe to re-run on the live schema.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('orders', 'khata_paid')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->decimal('khata_paid', 12, 2)->default(0)->after('paid_amount');
            });
        }

        Schema::table('order_items', function (Blueprint $table) {
            if (!Schema::hasColumn('order_items', 'cost_price')) {
                $table->decimal('cost_price', 10, 2)->nullable()->after('unit_price');
            }
            if (!Schema::hasColumn('order_items', 'retail_price')) {
                $table->decimal('retail_price', 10, 2)->nullable()->after('cost_price');
            }
        });

        Schema::table('payments', function (Blueprint $table) {
            if (!Schema::hasColumn('payments', 'deleted_at')) {
                $table->softDeletes();
            }
            if (!Schema::hasColumn('payments', 'deleted_by')) {
                $table->unsignedBigInteger('deleted_by')->nullable();
            }
        });
    }

    public function down(): void
    {
        if (Schema::hasColumn('orders', 'khata_paid')) {
            Schema::table('orders', fn (Blueprint $t) => $t->dropColumn('khata_paid'));
        }
        foreach (['cost_price', 'retail_price'] as $col) {
            if (Schema::hasColumn('order_items', $col)) {
                Schema::table('order_items', fn (Blueprint $t) => $t->dropColumn($col));
            }
        }
        foreach (['deleted_at', 'deleted_by'] as $col) {
            if (Schema::hasColumn('payments', $col)) {
                Schema::table('payments', fn (Blueprint $t) => $t->dropColumn($col));
            }
        }
    }
};
