<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Client update 24/7 #11: let the operator print the weight in grams or kg.
        // orders.weight stays canonical in KG (delivery slabs depend on it); this
        // column only records which unit to show/print. Null = auto (grams < 1 kg).
        if (! Schema::hasColumn('orders', 'weight_unit')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->string('weight_unit', 4)->nullable()->after('weight');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('orders', 'weight_unit')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropColumn('weight_unit');
            });
        }
    }
};
