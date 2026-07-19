<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * A short reason/note the admin can attach to the current status so the
     * customer knows why an order is held (e.g. payment short, confirmation
     * pending) — client feedback #1f.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (! Schema::hasColumn('orders', 'status_note')) {
                $table->string('status_note', 500)->nullable()->after('status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'status_note')) {
                $table->dropColumn('status_note');
            }
        });
    }
};
