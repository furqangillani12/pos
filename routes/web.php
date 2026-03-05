<?php

use App\Http\Controllers\Admin\AttendanceController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\InventoryController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\PosController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ProductVariantController;
use App\Http\Controllers\Admin\PurchaseController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SupplierController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\UnitController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PublicReceiptController;
use App\Http\Controllers\Admin\CreditController;
use App\Http\Controllers\Admin\LedgerController;
use App\Http\Controllers\Admin\LedgerAccountController;
use App\Models\Customer;
use Illuminate\Http\Request;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('admin')->middleware(['auth'])->group(function () {


    Route::get('/dashboard',[DashboardController::class,'index'])->name('admin.dashboard');

});
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/logout', function () {
        Auth::logout();
        return redirect('/');
    })->name('logout');
});
// routes/web.php
Route::middleware(['auth', 'permission:manage employees'])->group(function () {
    Route::resource('employees', EmployeeController::class);
});
Route::middleware(['auth', 'permission:manage attendance'])->group(function () {
    Route::get('attendance', [AttendanceController::class, 'index'])->name('admin.attendance.index');
    Route::get('attendance/create', [AttendanceController::class, 'create'])->name('admin.attendance.create');
    Route::post('attendance', [AttendanceController::class, 'store'])->name('admin.attendance.store');
    // routes/web.php
    Route::get('attendance/bulk', [AttendanceController::class, 'bulkCreate'])
        ->name('admin.attendance.bulk-create');
    Route::post('attendance/bulk', [AttendanceController::class, 'bulkStore'])
        ->name('admin.attendance.bulk-store');

    Route::post('attendance/{attendance}/checkout', [AttendanceController::class, 'checkOut'])
        ->name('admin.attendance.checkout');
    Route::get('attendance/report', [AttendanceController::class, 'dailyReport'])
        ->name('admin.attendance.report');

    Route::get('attendance/reports/monthly', [AttendanceController::class, 'monthlyReport'])
        ->name('admin.attendance.monthly-report');
    Route::get('attendance/reports/yearly', [AttendanceController::class, 'yearlyReport'])
        ->name('admin.attendance.yearly-report');
});

Route::middleware(['auth', 'permission:manage products'])->group(function () {
    Route::resource('products', ProductController::class)->except(['show']);
    Route::get('products/import', [ProductController::class, 'showImportForm'])->name('products.import.show');
    Route::post('products/import', [ProductController::class, 'import'])->name('products.import');
    Route::get('products/export', [ProductController::class, 'export'])->name('products.export');

    // Product Variant Routes
    Route::post('products/{product}/variants', [ProductVariantController::class, 'store'])->name('variants.store');
    Route::delete('variants/{variant}', [ProductVariantController::class, 'destroy'])->name('variants.destroy');
    Route::resource('units', UnitController::class);
});

Route::middleware(['auth', 'permission:manage categories'])->group(function () {
    Route::resource('categories', CategoryController::class)->except(['show']);
    Route::get('categories/create', [CategoryController::class, 'create'])->name('categories.create');

});

Route::middleware(['auth', 'permission:manage inventory'])->group(function () {
    Route::get('inventory', [InventoryController::class, 'index'])->name('inventory.index');
    Route::post('inventory/adjust', [InventoryController::class, 'adjust'])->name('inventory.adjust');
    Route::get('inventory/logs', [InventoryController::class, 'logs'])->name('inventory.logs');
    Route::get('inventory/low-stock', [InventoryController::class, 'lowStock'])->name('inventory.low-stock');
});

Route::middleware(['auth', 'permission:manage purchases'])->group(function () {

    Route::resource('purchases', PurchaseController::class)->except(['edit', 'update']);
});

Route::middleware(['auth', 'permission:manage suppliers'])->group(function () {
    Route::resource('suppliers', SupplierController::class);
});

// Fixed POS routes with admin prefix
Route::middleware(['auth', 'permission:access pos'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/pos', [PosController::class, 'index'])->name('pos.index');
    Route::post('/pos', [PosController::class, 'storeOrder'])->name('pos.store');
    Route::get('/pos/receipt/{order}', [PosController::class, 'showReceipt'])->name('pos.receipt');
    Route::post('/pos/refund/{order}', [PosController::class, 'processRefund'])->name('pos.refund');
    Route::get('/pos/receipt/{order}/download', [PosController::class, 'downloadReceipt'])->name('pos.receipt.download');
});
Route::middleware(['auth', 'permission:manage reports'])->group(function () {
    Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/sales', [ReportController::class, 'sales'])->name('sales');
            Route::get('/top-products', [ReportController::class, 'topProducts'])->name('top-products');
            Route::get('/profit-loss', [ReportController::class, 'profitLoss'])->name('profit-loss');
            Route::get('/category-sales', [ReportController::class, 'categorySales'])->name('category-sales');
            Route::get('/customer-sales', [ReportController::class, 'customerSales'])->name('customer-sales');
        });
    });
});


Route::middleware(['auth', 'permission:manage roles'])->group(function () {
    Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
    Route::get('/roles/create', [RoleController::class, 'create'])->name('roles.create');
    Route::post('/roles', [RoleController::class, 'store'])->name('roles.store');
    Route::get('/roles/{role}/edit', [RoleController::class, 'edit'])->name('roles.edit');
    Route::put('/roles/{role}', [RoleController::class, 'update'])->name('roles.update');
    Route::delete('/roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');
});

Route::middleware(['auth', 'permission:manage permissions'])->group(function () {
    Route::resource('permissions', PermissionController::class)->except(['destroy']);

});

Route::middleware(['auth', 'permission:assign roles'])->group(function () {
    Route::get('/assign-role', [UserController::class, 'assignRoleForm'])->name('users.assign_role.form');
    Route::post('/assign-role', [UserController::class, 'assignRole'])->name('users.assign_role');
});



// routes/web.php
Route::middleware(['auth', 'permission:manage payroll'])->group(function () {
Route::prefix('admin')->middleware(['auth'])->group(function () {
    Route::get('/payroll', [PayrollController::class, 'index'])->name('admin.payroll.index');
    Route::get('/payroll/generate', [PayrollController::class, 'generate'])->name('admin.payroll.generate');
    Route::post('/payroll/{payroll}/mark-paid', [PayrollController::class, 'markPaid'])->name('admin.payroll.markPaid');
    Route::get('/payroll/{payroll}/payslip', [PayrollController::class, 'payslip'])->name('admin.payroll.payslip');
});
});
Route::get('/admin/reports/customer-orders/{customer}', [ReportController::class, 'getCustomerOrders']);
// routes/web.php
Route::get('/admin/orders/{order}', [ReportController::class, 'show'])->name('admin.orders.show');


Route::get('/orders/{order}/receipt-pdf', [PosController::class, 'downloadReceiptPdf'])->name('admin.pos.receipt.pdf');

Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {
    Route::resource('customers', CustomerController::class);
    Route::get('customers/{customer}/khata',
        [CustomerController::class, 'khata'])->name('customers.khata');

    Route::post('customers/{customer}/khata/payment',
        [CustomerController::class, 'storeKhataPayment'])->name('customers.khata.payment');

    Route::delete('customers/{customer}/khata/payment/{payment}',
    [CustomerController::class, 'deleteKhataPayment'])->name('customers.khata.payment.delete');

     Route::get('customers/generate-barcode', function() {
        return response()->json(['barcode' => \App\Models\Customer::generateBarcode()]);
    })->name('customers.generate-barcode');
    
    // Search route for AJAX
    Route::get('customers/search', [CustomerController::class, 'search'])->name('customers.search');
});


// In routes/web.php

// Public receipt routes (for customers)
Route::get('/receipt/{token}', [PublicReceiptController::class, 'show'])
    ->name('public.receipt.show');

Route::get('/receipt/{token}/download', [PublicReceiptController::class, 'download'])
    ->name('public.receipt.download');

Route::get('/receipt/{token}/print', [PublicReceiptController::class, 'print'])
    ->name('public.receipt.print');

Route::get('/receipt/{token}/json', [PublicReceiptController::class, 'json'])
    ->name('public.receipt.json');

Route::post('/receipt/{token}/send', [PublicReceiptController::class, 'send'])
    ->name('public.receipt.send');


Route::middleware(['auth', 'permission:manage credit'])->prefix('admin')->name('admin.')->group(function () {
    
    // Credit Dashboard
    Route::get('/credit', [CreditController::class, 'index'])->name('credit.index');
    
    // Customer Credit Statement
    Route::get('/credit/customer/{customer}', [CreditController::class, 'customerStatement'])->name('credit.statement');
    Route::get('/credit/customer/{customer}/export', [CreditController::class, 'exportStatement'])->name('credit.statement.export');
    
    // Credit Enable/Disable
    Route::post('/credit/customer/{customer}/enable', [CreditController::class, 'enableCredit'])->name('credit.enable');
    Route::delete('/credit/customer/{customer}/disable', [CreditController::class, 'disableCredit'])->name('credit.disable');
    
    // Credit Payments
    Route::get('/credit/payments', [CreditController::class, 'paymentForm'])->name('credit.payment');
    Route::post('/credit/payments/process', [CreditController::class, 'processPayment'])->name('credit.payment.process');
    
    // Overdue Report
    Route::get('/credit/overdue', [CreditController::class, 'overdueReport'])->name('credit.overdue');
    
    // POS Integration (AJAX)
    Route::post('/credit/process-sale/{order}', [CreditController::class, 'processCreditSale'])->name('credit.process-sale');
});

Route::get('/admin/customers/{customer}/credit-status', function(Customer $customer) {
    return response()->json([
        'credit_enabled' => $customer->credit_enabled,
        'credit_limit' => $customer->credit_limit,
        'current_balance' => $customer->current_balance,
        'available_credit' => $customer->available_credit
    ]);
})->middleware('auth');

Route::post('/admin/customers/{customer}/check-credit', function(Request $request, Customer $customer) {
    $amount = $request->amount;
    
    if (!$customer->credit_enabled) {
        return response()->json([
            'success' => false,
            'message' => 'Credit is not enabled for this customer'
        ]);
    }
    
    if (!$customer->hasSufficientCredit($amount)) {
        return response()->json([
            'success' => false,
            'message' => 'Insufficient credit limit',
            'available_credit' => $customer->available_credit
        ]);
    }
    
    return response()->json([
        'success' => true,
        'available_credit' => $customer->available_credit
    ]);
})->middleware('auth');

Route::middleware(['auth', 'permission:manage ledger'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/ledger',          [LedgerController::class, 'index'])->name('ledger.index');
    Route::get('/ledger/accounts', [LedgerController::class, 'accounts'])->name('ledger.accounts');
    Route::get('/ledger/export',   [LedgerController::class, 'export'])->name('ledger.export');
    Route::get('/ledger/{ledger}', [LedgerController::class, 'show'])->name('ledger.show');


    // Ledger Accounts
    Route::resource('ledger-accounts', LedgerAccountController::class);
    Route::patch('ledger-accounts/{ledgerAccount}/toggle', [LedgerAccountController::class, 'toggleActive'])->name('ledger-accounts.toggle');
    Route::post('ledger-accounts/{ledgerAccount}/add-entry', [LedgerAccountController::class, 'addEntry'])->name('ledger-accounts.add-entry');
});


require __DIR__.'/auth.php';

