<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Create branch_product_stock pivot table
        Schema::create('branch_product_stock', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained('branches')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->decimal('stock_quantity', 10, 2)->default(0);
            $table->decimal('reorder_level', 10, 2)->default(10);
            $table->unique(['branch_id', 'product_id']);
            $table->timestamps();
        });

        // 2. Add branch_id to orders
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('branch_id')->nullable()->after('id')->constrained('branches')->nullOnDelete();
            $table->index('branch_id');
        });

        // 3. Add branch_id to users
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('branch_id')->nullable()->after('id')->constrained('branches')->nullOnDelete();
        });

        // 4. Add branch_id to employees
        Schema::table('employees', function (Blueprint $table) {
            $table->foreignId('branch_id')->nullable()->after('id')->constrained('branches')->nullOnDelete();
        });

        // 5. Add branch_id to attendances
        Schema::table('attendances', function (Blueprint $table) {
            $table->foreignId('branch_id')->nullable()->after('id')->constrained('branches')->nullOnDelete();
        });

        // 6. Add branch_id to payrolls
        Schema::table('payrolls', function (Blueprint $table) {
            $table->foreignId('branch_id')->nullable()->after('id')->constrained('branches')->nullOnDelete();
        });

        // 7. Add branch_id to purchases
        Schema::table('purchases', function (Blueprint $table) {
            $table->foreignId('branch_id')->nullable()->after('id')->constrained('branches')->nullOnDelete();
        });

        // 8. Add branch_id to inventory_logs
        Schema::table('inventory_logs', function (Blueprint $table) {
            $table->foreignId('branch_id')->nullable()->after('id')->constrained('branches')->nullOnDelete();
        });

        // 9. Create default branch and migrate existing data
        $defaultBranch = DB::table('branches')->insertGetId([
            'name' => 'Almufeed Saqafti Markaz',
            'code' => 'ASM',
            'address' => 'Main Branch',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Migrate product stock to branch_product_stock for the default branch
        $products = DB::table('products')->select('id', 'stock_quantity', 'reorder_level')->get();
        foreach ($products as $product) {
            DB::table('branch_product_stock')->insert([
                'branch_id' => $defaultBranch,
                'product_id' => $product->id,
                'stock_quantity' => $product->stock_quantity ?? 0,
                'reorder_level' => $product->reorder_level ?? 10,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Assign all existing records to the default branch
        DB::table('orders')->whereNull('branch_id')->update(['branch_id' => $defaultBranch]);
        DB::table('users')->whereNull('branch_id')->update(['branch_id' => $defaultBranch]);
        DB::table('employees')->whereNull('branch_id')->update(['branch_id' => $defaultBranch]);
        DB::table('attendances')->whereNull('branch_id')->update(['branch_id' => $defaultBranch]);
        DB::table('payrolls')->whereNull('branch_id')->update(['branch_id' => $defaultBranch]);
        DB::table('purchases')->whereNull('branch_id')->update(['branch_id' => $defaultBranch]);
        DB::table('inventory_logs')->whereNull('branch_id')->update(['branch_id' => $defaultBranch]);

        // 10. Add manage branches permission
        $permissionExists = DB::table('permissions')->where('name', 'manage branches')->exists();
        if (!$permissionExists) {
            DB::table('permissions')->insert([
                ['name' => 'manage branches', 'guard_name' => 'web', 'created_at' => now(), 'updated_at' => now()],
                ['name' => 'view all branches', 'guard_name' => 'web', 'created_at' => now(), 'updated_at' => now()],
            ]);

            // Assign to admin role
            $adminRole = DB::table('roles')->where('name', 'admin')->first();
            if ($adminRole) {
                $perms = DB::table('permissions')->whereIn('name', ['manage branches', 'view all branches'])->pluck('id');
                foreach ($perms as $permId) {
                    DB::table('role_has_permissions')->insertOrIgnore([
                        'permission_id' => $permId,
                        'role_id' => $adminRole->id,
                    ]);
                }
            }
        }
    }

    public function down(): void
    {
        Schema::table('inventory_logs', function (Blueprint $table) {
            $table->dropConstrainedForeignId('branch_id');
        });
        Schema::table('purchases', function (Blueprint $table) {
            $table->dropConstrainedForeignId('branch_id');
        });
        Schema::table('payrolls', function (Blueprint $table) {
            $table->dropConstrainedForeignId('branch_id');
        });
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropConstrainedForeignId('branch_id');
        });
        Schema::table('employees', function (Blueprint $table) {
            $table->dropConstrainedForeignId('branch_id');
        });
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('branch_id');
        });
        Schema::table('orders', function (Blueprint $table) {
            $table->dropConstrainedForeignId('branch_id');
        });
        Schema::dropIfExists('branch_product_stock');

        DB::table('permissions')->whereIn('name', ['manage branches', 'view all branches'])->delete();
    }
};
