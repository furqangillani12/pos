<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Customer-initiated account money movements (client #1b / #1c):
     *  - type=payment    → customer pays down a pending balance (with proof);
     *                      on admin approval the khata is reduced.
     *  - type=withdrawal → customer requests their credit/advance back to a bank
     *                      account; on admin approval the credit is released.
     */
    public function up(): void
    {
        Schema::create('account_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('order_id')->nullable()->constrained()->nullOnDelete();
            $table->string('type'); // payment | withdrawal
            $table->decimal('amount', 12, 2)->default(0);

            // Payment (customer → us)
            $table->string('sender_name')->nullable();
            $table->string('sender_bank')->nullable();
            $table->string('reference')->nullable();
            $table->string('proof_path')->nullable();       // customer's screenshot

            // Withdrawal (us → customer)
            $table->string('account_title')->nullable();
            $table->string('account_number')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('admin_proof_path')->nullable(); // admin's payout screenshot

            $table->string('status')->default('new');        // new | approved | rejected
            $table->string('admin_note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('account_requests');
    }
};
