<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use App\Models\Order;

return new class extends Migration
{
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('receipt_token')->nullable()->unique()->after('id');
        });
        
        // Generate tokens for existing orders
        $orders = Order::whereNull('receipt_token')->get();
        foreach ($orders as $order) {
            $order->receipt_token = Str::random(32);
            $order->save();
        }
    }

    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('receipt_token');
        });
    }
};