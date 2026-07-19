<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Operator-set piece count for the dispatch slip (client #11). When set from
     * the picking checklist it overrides the auto sum of item quantities.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (! Schema::hasColumn('orders', 'dispatch_pieces')) {
                $table->unsignedInteger('dispatch_pieces')->nullable()->after('dispatch_remarks');
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'dispatch_pieces')) {
                $table->dropColumn('dispatch_pieces');
            }
        });
    }
};
