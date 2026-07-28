<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Client update 24/7 #6: the general ledger must be per-branch so each
        // branch only sees its own accounts. ledger_entries had no branch column,
        // so every branch saw everyone's entries. Add one and backfill from the
        // source records that already carry a branch_id.
        if (! Schema::hasColumn('ledger_entries', 'branch_id')) {
            Schema::table('ledger_entries', function (Blueprint $table) {
                $table->unsignedBigInteger('branch_id')->nullable()->after('user_id')->index();
            });
        }

        // Backfill from the reference record's branch where it exists. Legacy
        // expense/refund entries have no derivable branch and stay null (they
        // surface only in the "All branches" view).
        $this->backfillFrom('orders', \App\Models\Order::class);
        $this->backfillFrom('purchases', \App\Models\Purchase::class);
        $this->backfillFrom('payrolls', \App\Models\Payroll::class);
    }

    private function backfillFrom(string $table, string $refClass): void
    {
        if (! Schema::hasTable($table) || ! Schema::hasColumn($table, 'branch_id')) {
            return;
        }

        DB::table('ledger_entries')
            ->join($table, 'ledger_entries.reference_id', '=', $table . '.id')
            ->where('ledger_entries.reference_type', $refClass)
            ->whereNull('ledger_entries.branch_id')
            ->update(['ledger_entries.branch_id' => DB::raw($table . '.branch_id')]);
    }

    public function down(): void
    {
        if (Schema::hasColumn('ledger_entries', 'branch_id')) {
            Schema::table('ledger_entries', function (Blueprint $table) {
                $table->dropColumn('branch_id');
            });
        }
    }
};
