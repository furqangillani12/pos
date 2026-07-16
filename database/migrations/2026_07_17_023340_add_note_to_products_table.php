<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * A short, important note about the product (client feedback #2). Shown to
     * staff on the product form and to customers on the storefront product page,
     * and included in product search (#3).
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (! Schema::hasColumn('products', 'note')) {
                $table->text('note')->nullable()->after('summary');
            }
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'note')) {
                $table->dropColumn('note');
            }
        });
    }
};
