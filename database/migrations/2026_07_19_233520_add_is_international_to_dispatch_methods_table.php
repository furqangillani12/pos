<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Marks a dispatch method as international-only (client #4). Local methods
     * show for Pakistan addresses; international methods show for other countries.
     */
    public function up(): void
    {
        Schema::table('dispatch_methods', function (Blueprint $table) {
            if (! Schema::hasColumn('dispatch_methods', 'is_international')) {
                $table->boolean('is_international')->default(false)->after('has_tracking');
            }
        });
    }

    public function down(): void
    {
        Schema::table('dispatch_methods', function (Blueprint $table) {
            if (Schema::hasColumn('dispatch_methods', 'is_international')) {
                $table->dropColumn('is_international');
            }
        });
    }
};
