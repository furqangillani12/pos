<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->foreignId('branch_id')->nullable()->after('id')->constrained('branches')->nullOnDelete();
        });

        Schema::table('suppliers', function (Blueprint $table) {
            $table->foreignId('branch_id')->nullable()->after('id')->constrained('branches')->nullOnDelete();
        });

        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('branch_id')->nullable()->after('id')->constrained('branches')->nullOnDelete();
        });

        // Assign all existing records to default branch
        $defaultBranch = DB::table('branches')->first();
        if ($defaultBranch) {
            DB::table('customers')->whereNull('branch_id')->update(['branch_id' => $defaultBranch->id]);
            DB::table('suppliers')->whereNull('branch_id')->update(['branch_id' => $defaultBranch->id]);
            DB::table('products')->whereNull('branch_id')->update(['branch_id' => $defaultBranch->id]);
        }
    }

    public function down(): void
    {
        foreach (['customers', 'suppliers', 'products'] as $table) {
            Schema::table($table, function (Blueprint $t) {
                $t->dropForeign(['branch_id']);
                $t->dropColumn('branch_id');
            });
        }
    }
};
