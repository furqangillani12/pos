<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Links a re-order back to the returned order it was placed from (client #1e),
     * so both orders can show "reordered as #X" / "reorder of #Y".
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (! Schema::hasColumn('orders', 'reorder_of_order_id')) {
                $table->unsignedBigInteger('reorder_of_order_id')->nullable()->after('order_source');
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'reorder_of_order_id')) {
                $table->dropColumn('reorder_of_order_id');
            }
        });
    }
};
