<?php

namespace App\Console\Commands;

use App\Models\Customer;
use App\Services\KhataService;
use Illuminate\Console\Command;

/**
 * Rebuild customers.current_balance from their khata rows (bills, payments,
 * payouts, offsets, refunds — see KhataService). Overwrites balances, so always
 * run with --dry-run first and review; balances changed without any record
 * (see the accounts review) will be lost.
 */
class RecalculateCustomerBalances extends Command
{
    protected $signature = 'customers:recalculate-balances {--customer= : Recalculate for a specific customer ID} {--dry-run : Show changes without saving}';
    protected $description = 'Recalculate customer current_balance from the khata statement';

    public function handle()
    {
        $dryRun = $this->option('dry-run');
        $query  = Customer::query();
        if ($id = $this->option('customer')) $query->where('id', $id);

        $fixed = 0;
        foreach ($query->get() as $customer) {
            $statement = round(KhataService::entries($customer)->sum('effect'), 2);
            $old       = round((float) ($customer->current_balance ?? 0), 2);

            if (abs($statement - $old) > 0.01) {
                $this->warn(sprintf('  #%-5d %-30s | Old: %12s | Statement: %12s | Diff: %10s',
                    $customer->id, mb_substr($customer->name, 0, 30),
                    number_format($old, 2), number_format($statement, 2), number_format($statement - $old, 2)));
                if (!$dryRun) {
                    $customer->update(['current_balance' => $statement]);
                    KhataService::reallocate($customer->id);
                }
                $fixed++;
            }
        }

        $this->newLine();
        $this->info(($dryRun ? '[DRY RUN] Would fix' : 'Fixed') . " {$fixed} customer(s).");
    }
}
