<?php

use App\Http\Controllers\Admin\AttendanceController;
use App\Http\Controllers\Admin\CategoryController;
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
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

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

Route::middleware(['auth', 'permission:access pos'])->group(function () {
    Route::get('pos', [PosController::class, 'index'])->name('admin.pos.index');
    Route::post('pos', [PosController::class, 'storeOrder'])->name('admin.pos.store');
    Route::get('pos/receipt/{order}', [PosController::class, 'showReceipt'])->name('admin.pos.receipt');
    Route::post('pos/refund/{order}', [PosController::class, 'processRefund'])->name('admin.pos.refund');
    Route::get('pos/receipt/{order}/download', [PosController::class, 'downloadReceipt'])
        ->name('admin.pos.receipt.download');
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



require __DIR__.'/auth.php';
