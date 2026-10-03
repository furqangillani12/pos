<?php

namespace App\Console\Commands;

use App\Models\AccountRequest;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Payment;
use App\Services\KhataService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * One-time (re-runnable) khata clean-up after the accounts fix. Never changes a
 * customer's balance:
 *   1. Approved website payment/withdraw requests get their missing khata entry
 *      (their balance effect already happened when they were approved).
 *   2. Khata payments are allocated to bills oldest-first, so bills paid through
 *      Cash In / the khata page show as paid and Wasooli matches the khata.
 */
class KhataSync extends Command
{
    protected $signature = 'khata:sync {--dry-run : Show what would change, then roll back}';
    protected $description = 'Backfill missing khata entries and allocate khata payments to bills';

    public function handle()
    {
        $dry = $this->option('dry-run');
        $before = $this->snapshot();

        DB::beginTransaction();
        try {
            // 1. Missing entries for approved website requests.
            $created = 0;
            AccountRequest::where('status', 'approved')->orderBy('id')->each(function ($req) use (&$created) {
                if (KhataService::recordAccountRequest($req, $req->updated_at)) $created++;
            });
            $this->info("Website requests: {$created} khata entr" . ($created === 1 ? 'y' : 'ies') . ' added.');

            // 2. Allocate khata payments to bills.
            KhataService::reallocateAll();

            $after = $this->snapshot();
            $this->table(['', 'Before', 'After'], [
                ['Customers whose statement ≠ balance', $before['mismatch'], $after['mismatch']],
                ['Unpaid on bills of named customers (Rs)', number_format($before['billDue']), number_format($after['billDue'])],
                ['Khata due — customers.current_balance > 0 (Rs)', number_format($before['khataDue']), number_format($after['khataDue'])],
                ['Bills marked fully paid', $before['paidBills'], $after['paidBills']],
            ]);

            if ($dry) {
                DB::rollBack();
                $this->warn('[DRY RUN] Nothing saved.');
            } else {
                DB::commit();
                $this->info('Saved.');
            }
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    private function snapshot(): array
    {
        $mismatch = 0;
        foreach (Customer::all() as $c) {
            if (abs(KhataService::entries($c)->sum('effect') - (float) $c->current_balance) > 1) $mismatch++;
        }
        $named = Order::whereNotNull('customer_id')->whereNotIn('status', Order::KHATA_EXCLUDED_STATUSES);
        return [
            'mismatch'  => $mismatch,
            'billDue'   => (float) (clone $named)->where('balance_amount', '>', 0)->sum('balance_amount'),
            'khataDue'  => (float) Customer::where('current_balance', '>', 0)->sum('current_balance'),
            'paidBills' => (clone $named)->where('balance_amount', '<=', 0)->count(),
        ];
    }
}
