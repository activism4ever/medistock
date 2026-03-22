<?php
use App\Http\Controllers\HodReportController;
use App\Http\Controllers\InsuranceSchemeController;
use App\Http\Controllers\AllocationController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\BatchController;
use App\Http\Controllers\CashierController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\MedicineController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// ── Public ────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login',  [LoginController::class, 'showLogin'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});
Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth')->name('logout');

// ── Authenticated ─────────────────────────────────────────
Route::middleware(['auth', 'active'])->group(function () {

    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // ── Admin only ────────────────────────────────────────
    Route::middleware('role:admin')->group(function () {
        Route::resource('medicines',   MedicineController::class);
        Route::resource('batches',     BatchController::class)->except(['destroy']);
        Route::resource('allocations', AllocationController::class)->only(['index', 'create', 'store']);
        Route::resource('users',       UserController::class);
        Route::resource('departments', DepartmentController::class)->except(['show', 'destroy']);

        // Insurance Schemes
        Route::get('/insurance-schemes',                      [InsuranceSchemeController::class, 'index'])->name('insurance.index');
        Route::post('/insurance-schemes',                     [InsuranceSchemeController::class, 'store'])->name('insurance.store');
        Route::patch('/insurance-schemes/{scheme}/toggle',    [InsuranceSchemeController::class, 'toggle'])->name('insurance.toggle');
        Route::delete('/insurance-schemes/{scheme}',          [InsuranceSchemeController::class, 'destroy'])->name('insurance.destroy');

        // Reports
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/',               [ReportController::class, 'index'])->name('index');
            Route::get('/download-pdf',   [ReportController::class, 'downloadPdf'])->name('download-pdf');
            Route::get('/download-excel', [ReportController::class, 'downloadExcel'])->name('download-excel');
            Route::get('/stock-value',    [ReportController::class, 'stockValue'])->name('stock-value');
            Route::get('/expiry',         [ReportController::class, 'expiry'])->name('expiry');
            Route::get('/low-stock',      [ReportController::class, 'lowStock'])->name('low-stock');
        });
    });

    // ── Pharmacist / Drawer users — Invoice workflow ──────
    Route::middleware('role:pharmacist')->group(function () {
    Route::get('/invoices',                      [InvoiceController::class, 'index'])->name('invoices.index');
    Route::get('/invoices/create',               [InvoiceController::class, 'create'])->name('invoices.create');
    Route::post('/invoices',                     [InvoiceController::class, 'store'])->name('invoices.store');
    Route::get('/invoices/{invoice}',            [InvoiceController::class, 'show'])->name('invoices.show');
    Route::patch('/invoices/{invoice}/dispense', [InvoiceController::class, 'dispense'])->name('invoices.dispense');
    Route::get('/my-sales',                      [InvoiceController::class, 'mySales'])->name('invoices.my-sales');
    Route::get('/my-sales/pdf',                  [InvoiceController::class, 'mySalesPdf'])->name('invoices.my-sales.pdf');
});

    // ── Cashier ───────────────────────────────────────────
   Route::middleware('role:cashier')->group(function () {
    Route::get('/cashier',                          [CashierController::class, 'dashboard'])->name('cashier.dashboard');
    Route::patch('/cashier/{invoice}/pay',          [CashierController::class, 'pay'])->name('cashier.pay');
    Route::get('/cashier/{invoice}/receipt',        [CashierController::class, 'receipt'])->name('cashier.receipt');
    Route::get('/cashier/collections',              [CashierController::class, 'collections'])->name('cashier.collections');
    Route::get('/cashier/collections/pdf',          [CashierController::class, 'collectionsPdf'])->name('cashier.collections.pdf');
});
 
    // ── HOD Pharmacy ──────────────────────────────────────────
Route::middleware('role:hod_pharmacy')->group(function () {
    Route::get('/hod', [DashboardController::class, 'hodPharmacy'])->name('hod.dashboard');
    Route::get('/hod/reports', [HodReportController::class, 'index'])->name('hod.reports');
    Route::get('/hod/reports/sales-pdf', [HodReportController::class, 'salesPdf'])->name('hod.reports.sales-pdf');
    Route::get('/hod/reports/stock-pdf', [HodReportController::class, 'stockPdf'])->name('hod.reports.stock-pdf');
});

    // ── Department users (old POS — kept for lab/theatre/ward) ───
    Route::middleware('role:lab,theatre,ward')->group(function () {
        Route::get('/pos',  [SaleController::class, 'create'])->name('sales.create');
        Route::post('/pos', [SaleController::class, 'store'])->name('sales.store');
    });

    // ── Shared ────────────────────────────────────────────
    Route::get('/sales',                [SaleController::class, 'index'])->name('sales.index');
    Route::get('/sales/{sale}',         [SaleController::class, 'show'])->name('sales.show');
    Route::get('/sales/{sale}/receipt', [SaleController::class, 'receipt'])->name('sales.receipt');
});