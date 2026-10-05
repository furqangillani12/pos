<?php

namespace App\Console\Commands;

use App\Models\Customer;
use App\Models\Payment;
use App\Services\KhataService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * For customers whose balance does not match their statement (changes made in the
 * past without any record), add one "Balance adjustment" khata entry for the
 * difference. Balances are NOT changed — only the statement is made to explain
 * them, with a note so each adjustment can be reviewed and corrected later.
 */
class KhataAdjustMismatches extends Command
{
    protected $signature = 'khata:adjust-mismatches {--dry-run : List the adjustments without saving}';
    protected $description = 'Add a balance-adjustment entry where a customer balance differs from the statement';

    public function handle()
    {
        $dry  = $this->option('dry-run');
        $rows = [];

        DB::transaction(function () use ($dry, &$rows) {
            foreach (Customer::orderBy('id')->get() as $c) {
                $statement = round(KhataService::entries($c)->sum('effect'), 2);
                $diff      = round((float) $c->current_balance - $statement, 2);
                if (abs($diff) < 1) continue;

                $rows[] = [$c->id, mb_substr($c->name, 0, 32), number_format((float) $c->current_balance), number_format($statement), number_format($diff)];
                if ($dry) continue;

                Payment::create([
                    'payment_number'   => Payment::generatePaymentNumber(),
                    'payment_type'     => 'khata_adjust',
                    'customer_id'      => $c->id,
                    'amount'           => $diff,
                    'payment_date'     => now()->toDateString(),
                    'payment_method'   => 'adjustment',
                    'reference_number' => 'ADJ-REVIEW-' . now()->format('Ymd'),
                    'notes'            => 'Accounts review ' . now()->format('M Y') . ': balance differed from bills/payments by Rs. '
                                          . number_format($diff) . ' with no matching record. Please verify.',
                    'status'           => 'completed',
                    'created_by'       => auth()->id(),
                ]);
            }
        });

        $this->table(['ID', 'Customer', 'Balance', 'Statement', 'Adjustment'], $rows);
        $this->info(($dry ? '[DRY RUN] Would add ' : 'Added ') . count($rows) . ' adjustment(s). Balances unchanged.');
    }
}
