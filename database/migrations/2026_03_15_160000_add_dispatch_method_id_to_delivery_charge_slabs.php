<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        DB::table('delivery_charge_slabs')->truncate();

        if (!Schema::hasColumn('delivery_charge_slabs', 'dispatch_method_id')) {
            Schema::table('delivery_charge_slabs', function (Blueprint $table) {
                $table->foreignId('dispatch_method_id')->after('id')->constrained('dispatch_methods')->onDelete('cascade');
            });
        } else {
            Schema::table('delivery_charge_slabs', function (Blueprint $table) {
                $table->foreign('dispatch_method_id')->references('id')->on('dispatch_methods')->onDelete('cascade');
            });
        }
    }

    public function down()
    {
        Schema::table('delivery_charge_slabs', function (Blueprint $table) {
            $table->dropForeign(['dispatch_method_id']);
            $table->dropColumn('dispatch_method_id');
        });
    }
};
