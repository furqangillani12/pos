<?php

use App\Http\Controllers\Admin\AttendanceController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\InventoryController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\PosController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\PurchaseController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SupplierController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\BranchController;
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
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\CashTransactionController;
use App\Http\Controllers\Admin\LinkedPartyController;
use App\Models\Customer;
use Illuminate\Http\Request;

Route::get('/', function () {
    return view('welcome');
});

// ── Branch Selection (NO branch middleware — must be accessible before selection) ──
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/branch/select', [BranchController::class, 'select'])->name('branch.select');
    Route::post('/branch/select', [BranchController::class, 'storeBranchSelection'])->name('branch.store-selection');
});

// ── Profile & Auth (no branch middleware needed) ──
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/logout', function () {
        Auth::logout();
        session()->forget('branch_id');
        return redirect('/');
    })->name('logout');
});

// ══════════════════════════════════════════════════════════════════
// All admin routes below require branch middleware
// ══════════════════════════════════════════════════════════════════

// ── Dashboard ──
Route::prefix('admin')->middleware(['auth', 'branch'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/receivables', [DashboardController::class, 'receivables'])->name('admin.receivables');
    Route::get('/advances', [DashboardController::class, 'advances'])->name('admin.advances');
});

// ── Branch Management (CRUD) ──
Route::middleware(['auth', 'branch', 'permission:manage branches'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('branches', BranchController::class);
    Route::patch('branches/{branch}/toggle', [BranchController::class, 'toggleActive'])->name('branches.toggle');
});

// ── Employees ──
Route::middleware(['auth', 'branch', 'permission:manage employees'])->group(function () {
    Route::resource('employees', EmployeeController::class);
});

// ── Attendance ──
Route::middleware(['auth', 'branch', 'permission:manage attendance'])->group(function () {
    Route::get('attendance', [AttendanceController::class, 'index'])->name('admin.attendance.index');
    Route::get('attendance/create', [AttendanceController::class, 'create'])->name('admin.attendance.create');
    Route::post('attendance', [AttendanceController::class, 'store'])->name('admin.attendance.store');
    Route::get('attendance/bulk', [AttendanceController::class, 'bulkCreate'])->name('admin.attendance.bulk-create');
    Route::post('attendance/bulk', [AttendanceController::class, 'bulkStore'])->name('admin.attendance.bulk-store');
    Route::post('attendance/{attendance}/checkout', [AttendanceController::class, 'checkOut'])->name('admin.attendance.checkout');
    Route::post('attendance/quick-checkin', [AttendanceController::class, 'quickCheckIn'])->name('admin.attendance.quick-checkin');
    Route::delete('attendance/{attendance}', [AttendanceController::class, 'destroy'])->name('admin.attendance.destroy');
    Route::get('attendance/report', [AttendanceController::class, 'dailyReport'])->name('admin.attendance.report');
    Route::get('attendance/reports/monthly', [AttendanceController::class, 'monthlyReport'])->name('admin.attendance.monthly-report');
    Route::get('attendance/reports/yearly', [AttendanceController::class, 'yearlyReport'])->name('admin.attendance.yearly-report');
});

// ── Products, Units, Categories ──
Route::middleware(['auth', 'branch', 'permission:manage products'])->group(function () {
    Route::resource('products', ProductController::class)->except(['show']);
    Route::get('products/import', [ProductController::class, 'showImportForm'])->name('products.import.show');
    Route::post('products/import', [ProductController::class, 'import'])->name('products.import');
    Route::get('products/export', [ProductController::class, 'export'])->name('products.export');
    Route::resource('units', UnitController::class);
});

Route::middleware(['auth', 'branch', 'permission:manage categories'])->group(function () {
    Route::resource('categories', CategoryController::class)->except(['show']);
    Route::get('categories/create', [CategoryController::class, 'create'])->name('categories.create');
});

// ── Inventory ──
Route::middleware(['auth', 'branch', 'permission:manage inventory'])->group(function () {
    Route::get('inventory', [InventoryController::class, 'index'])->name('inventory.index');
    Route::post('inventory/adjust', [InventoryController::class, 'adjust'])->name('inventory.adjust');
    Route::get('inventory/logs', [InventoryController::class, 'logs'])->name('inventory.logs');
    Route::get('inventory/low-stock', [InventoryController::class, 'lowStock'])->name('inventory.low-stock');
});

// ── Purchases ──
Route::middleware(['auth', 'branch', 'permission:manage purchases'])->group(function () {
    Route::resource('purchases', PurchaseController::class);
    Route::get('purchases/{purchase}/invoice', [PurchaseController::class, 'invoice'])->name('purchases.invoice');
});

// ── Suppliers ──
Route::middleware(['auth', 'branch', 'permission:manage suppliers'])->group(function () {
    Route::resource('suppliers', SupplierController::class);
    Route::get('suppliers/{supplier}/ledger', [SupplierController::class, 'ledger'])->name('suppliers.ledger');
    Route::post('suppliers/{supplier}/payment', [SupplierController::class, 'storePayment'])->name('suppliers.payment.store');
    Route::delete('suppliers/{supplier}/payment/{payment}', [SupplierController::class, 'deletePayment'])->name('suppliers.payment.delete');
    Route::get('suppliers/{supplier}/payment/{payment}/voucher', [SupplierController::class, 'paymentVoucher'])->name('suppliers.payment.voucher');
});

// ── POS ──
Route::middleware(['auth', 'branch', 'permission:access pos'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/pos', [PosController::class, 'index'])->name('pos.index');
    Route::get('/pos/products', [PosController::class, 'searchProducts'])->name('pos.products');
    Route::post('/pos', [PosController::class, 'storeOrder'])->name('pos.store');
    Route::get('/pos/receipt/{order}', [PosController::class, 'showReceipt'])->name('pos.receipt');
    Route::post('/pos/refund/{order}', [PosController::class, 'processRefund'])->name('pos.refund');
    Route::get('/pos/receipt/{order}/download', [PosController::class, 'downloadReceipt'])->name('pos.receipt.download');
    Route::get('/pos/receipt/{order}/thermal', [PosController::class, 'thermalReceipt'])->name('pos.receipt.thermal');
    Route::get('/pos/edit/{order}', [PosController::class, 'editOrder'])->name('pos.edit');
    Route::put('/pos/edit/{order}', [PosController::class, 'updateOrder'])->name('pos.update');
    Route::post('/pos/cancel/{order}', [PosController::class, 'cancelOrder'])->name('pos.cancel');
    Route::delete('/pos/delete/{order}', [PosController::class, 'deleteOrder'])->name('pos.delete');
});

// ── Reports ──
Route::middleware(['auth', 'branch', 'permission:manage reports'])->prefix('admin')->name('admin.')->group(function () {
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/sales', [ReportController::class, 'sales'])->name('sales');
        Route::get('/top-products', [ReportController::class, 'topProducts'])->name('top-products');
        Route::get('/profit-loss', [ReportController::class, 'profitLoss'])->name('profit-loss');
        Route::get('/category-sales', [ReportController::class, 'categorySales'])->name('category-sales');
        Route::get('/customer-sales', [ReportController::class, 'customerSales'])->name('customer-sales');
        Route::get('/product-statement', [ReportController::class, 'productStatement'])->name('product-statement');
    });
});

// ── Roles & Permissions ──
Route::middleware(['auth', 'branch', 'permission:manage roles'])->group(function () {
    Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
    Route::get('/roles/create', [RoleController::class, 'create'])->name('roles.create');
    Route::post('/roles', [RoleController::class, 'store'])->name('roles.store');
    Route::get('/roles/{role}/edit', [RoleController::class, 'edit'])->name('roles.edit');
    Route::put('/roles/{role}', [RoleController::class, 'update'])->name('roles.update');
    Route::delete('/roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');
});

Route::middleware(['auth', 'branch', 'permission:manage permissions'])->group(function () {
    Route::resource('permissions', PermissionController::class)->except(['destroy']);
});

Route::middleware(['auth', 'branch', 'role:super_admin'])->group(function () {
    Route::get('/assign-role', [UserController::class, 'assignRoleForm'])->name('users.assign_role.form');
    Route::post('/assign-role', [UserController::class, 'assignRole'])->name('users.assign_role');
});

// ── Payroll ──
Route::middleware(['auth', 'branch', 'permission:manage payroll'])->prefix('admin')->group(function () {
    Route::get('/payroll', [PayrollController::class, 'index'])->name('admin.payroll.index');
    Route::post('/payroll/generate', [PayrollController::class, 'generate'])->name('admin.payroll.generate');
    Route::post('/payroll/mark-all-paid', [PayrollController::class, 'markAllPaid'])->name('admin.payroll.markAllPaid');
    Route::post('/payroll/{payroll}/mark-paid', [PayrollController::class, 'markPaid'])->name('admin.payroll.markPaid');
    Route::get('/payroll/{payroll}/payslip', [PayrollController::class, 'payslip'])->name('admin.payroll.payslip');
});

// ── Orders (auth only, no specific permission) ──
Route::middleware(['auth', 'branch'])->group(function () {
    Route::get('/admin/reports/customer-orders/{customer}', [ReportController::class, 'getCustomerOrders']);
    Route::get('/admin/orders/{order}', [ReportController::class, 'show'])->name('admin.orders.show');
});

Route::get('/orders/{order}/receipt-pdf', [PosController::class, 'downloadReceiptPdf'])->name('admin.pos.receipt.pdf');

// ── Customers ──
Route::prefix('admin')->name('admin.')->middleware(['auth', 'branch'])->group(function () {
    Route::resource('customers', CustomerController::class);
    Route::get('customers/{customer}/khata', [CustomerController::class, 'khata'])->name('customers.khata');
    Route::post('customers/{customer}/khata/payment', [CustomerController::class, 'storeKhataPayment'])->name('customers.khata.payment');
    Route::delete('customers/{customer}/khata/payment/{payment}', [CustomerController::class, 'deleteKhataPayment'])->name('customers.khata.payment.delete');
    Route::get('customers/{customer}/khata/payment/{payment}/voucher', [CustomerController::class, 'paymentVoucher'])->name('customers.khata.payment.voucher');
    Route::get('customers/generate-barcode', function () {
        return response()->json(['barcode' => \App\Models\Customer::generateBarcode()]);
    })->name('customers.generate-barcode');
    Route::get('customers/search', [CustomerController::class, 'search'])->name('customers.search');
});

// ── Public Receipt Routes (no auth needed) ──
Route::get('/receipt/{token}', [PublicReceiptController::class, 'show'])->name('public.receipt.show');
Route::get('/receipt/{token}/download', [PublicReceiptController::class, 'download'])->name('public.receipt.download');
Route::get('/receipt/{token}/print', [PublicReceiptController::class, 'print'])->name('public.receipt.print');
Route::get('/receipt/{token}/json', [PublicReceiptController::class, 'json'])->name('public.receipt.json');
Route::post('/receipt/{token}/send', [PublicReceiptController::class, 'send'])->name('public.receipt.send');

// ── Credit System ──
Route::middleware(['auth', 'branch', 'permission:manage credit'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/credit', [CreditController::class, 'index'])->name('credit.index');
    Route::get('/credit/customer/{customer}', [CreditController::class, 'customerStatement'])->name('credit.statement');
    Route::get('/credit/customer/{customer}/export', [CreditController::class, 'exportStatement'])->name('credit.statement.export');
    Route::post('/credit/customer/{customer}/enable', [CreditController::class, 'enableCredit'])->name('credit.enable');
    Route::delete('/credit/customer/{customer}/disable', [CreditController::class, 'disableCredit'])->name('credit.disable');
    Route::get('/credit/payments', [CreditController::class, 'paymentForm'])->name('credit.payment');
    Route::post('/credit/payments/process', [CreditController::class, 'processPayment'])->name('credit.payment.process');
    Route::get('/credit/overdue', [CreditController::class, 'overdueReport'])->name('credit.overdue');
    Route::post('/credit/process-sale/{order}', [CreditController::class, 'processCreditSale'])->name('credit.process-sale');
});

Route::get('/admin/customers/{customer}/credit-status', function (Customer $customer) {
    return response()->json([
        'credit_enabled' => $customer->credit_enabled,
        'credit_limit' => $customer->credit_limit,
        'current_balance' => $customer->current_balance,
        'available_credit' => $customer->available_credit,
    ]);
})->middleware('auth');

Route::post('/admin/customers/{customer}/check-credit', function (Request $request, Customer $customer) {
    $amount = $request->amount;
    if (!$customer->credit_enabled) {
        return response()->json(['success' => false, 'message' => 'Credit is not enabled for this customer']);
    }
    if (!$customer->hasSufficientCredit($amount)) {
        return response()->json(['success' => false, 'message' => 'Insufficient credit limit', 'available_credit' => $customer->available_credit]);
    }
    return response()->json(['success' => true, 'available_credit' => $customer->available_credit]);
})->middleware('auth');

// ── Settings (Payment & Dispatch Methods) ──
Route::middleware(['auth', 'branch'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');

    Route::post('/settings/payment-methods', [SettingsController::class, 'storePaymentMethod'])->name('settings.payment-methods.store');
    Route::put('/settings/payment-methods/{paymentMethod}', [SettingsController::class, 'updatePaymentMethod'])->name('settings.payment-methods.update');
    Route::patch('/settings/payment-methods/{paymentMethod}/toggle', [SettingsController::class, 'togglePaymentMethod'])->name('settings.payment-methods.toggle');
    Route::delete('/settings/payment-methods/{paymentMethod}', [SettingsController::class, 'destroyPaymentMethod'])->name('settings.payment-methods.destroy');

    Route::post('/settings/dispatch-methods', [SettingsController::class, 'storeDispatchMethod'])->name('settings.dispatch-methods.store');
    Route::put('/settings/dispatch-methods/{dispatchMethod}', [SettingsController::class, 'updateDispatchMethod'])->name('settings.dispatch-methods.update');
    Route::patch('/settings/dispatch-methods/{dispatchMethod}/toggle', [SettingsController::class, 'toggleDispatchMethod'])->name('settings.dispatch-methods.toggle');
    Route::delete('/settings/dispatch-methods/{dispatchMethod}', [SettingsController::class, 'destroyDispatchMethod'])->name('settings.dispatch-methods.destroy');

    // Delivery Charge Slabs
    Route::post('/settings/delivery-slabs', [SettingsController::class, 'storeDeliverySlab'])->name('settings.delivery-slabs.store');
    Route::put('/settings/delivery-slabs/{slab}', [SettingsController::class, 'updateDeliverySlab'])->name('settings.delivery-slabs.update');
    Route::patch('/settings/delivery-slabs/{slab}/toggle', [SettingsController::class, 'toggleDeliverySlab'])->name('settings.delivery-slabs.toggle');
    Route::delete('/settings/delivery-slabs/{slab}', [SettingsController::class, 'destroyDeliverySlab'])->name('settings.delivery-slabs.destroy');
});

// ── Cash In / Out ──
Route::middleware(['auth', 'branch'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/cash',         [CashTransactionController::class, 'index'])->name('cash.index');
    Route::post('/cash',        [CashTransactionController::class, 'store'])->name('cash.store');
    Route::get('/cash/history', [CashTransactionController::class, 'history'])->name('cash.history');
});

// ── Manage Website (Brands + Banners + Online Orders) ──
Route::middleware(['auth', 'branch'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('brands',  \App\Http\Controllers\Admin\BrandController::class)->except(['show']);
    Route::patch('/brands/{brand}/toggle',   [\App\Http\Controllers\Admin\BrandController::class,  'toggle'])->name('brands.toggle');
    Route::resource('banners', \App\Http\Controllers\Admin\BannerController::class)->except(['show']);
    Route::patch('/banners/{banner}/toggle', [\App\Http\Controllers\Admin\BannerController::class, 'toggle'])->name('banners.toggle');

    Route::get('/online-orders',                     [\App\Http\Controllers\Admin\OnlineOrderController::class, 'index'])->name('online-orders.index');
    Route::get('/online-orders/{order}',             [\App\Http\Controllers\Admin\OnlineOrderController::class, 'show'])->name('online-orders.show');
    Route::patch('/online-orders/{order}/status',    [\App\Http\Controllers\Admin\OnlineOrderController::class, 'updateStatus'])->name('online-orders.status');
    Route::patch('/online-orders/{order}/mark-paid', [\App\Http\Controllers\Admin\OnlineOrderController::class, 'markPaid'])->name('online-orders.mark-paid');
});

// ── Linked customer ↔ supplier (offset feature) ──
Route::middleware(['auth', 'branch'])->prefix('admin')->name('admin.')->group(function () {
    Route::post('/linked-party/link',   [LinkedPartyController::class, 'link'])->name('linked-party.link');
    Route::post('/linked-party/unlink', [LinkedPartyController::class, 'unlink'])->name('linked-party.unlink');
    Route::post('/linked-party/offset', [LinkedPartyController::class, 'offset'])->name('linked-party.offset');
    Route::get('/customers/{customer}/combined-statement',
        [LinkedPartyController::class, 'combinedStatement'])->name('customers.combined-statement');
});

// ── Ledger ──
Route::middleware(['auth', 'branch', 'permission:manage ledger'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/ledger', [LedgerController::class, 'index'])->name('ledger.index');
    Route::get('/ledger/accounts', [LedgerController::class, 'accounts'])->name('ledger.accounts');
    Route::get('/ledger/export', [LedgerController::class, 'export'])->name('ledger.export');
    Route::get('/ledger/{ledger}', [LedgerController::class, 'show'])->name('ledger.show');
    Route::resource('ledger-accounts', LedgerAccountController::class);
    Route::patch('ledger-accounts/{ledgerAccount}/toggle', [LedgerAccountController::class, 'toggleActive'])->name('ledger-accounts.toggle');
    Route::post('ledger-accounts/{ledgerAccount}/add-entry', [LedgerAccountController::class, 'addEntry'])->name('ledger-accounts.add-entry');
});

require __DIR__ . '/auth.php';
