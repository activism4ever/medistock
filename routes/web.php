<?php
use App\Http\Controllers\AllocationController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\BatchController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartmentController;
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

        Route::prefix('reports')->name('reports.')->group(function () {
    Route::get('/',             [ReportController::class, 'index'])->name('index');
    Route::get('/download-pdf', [ReportController::class, 'downloadPdf'])->name('download-pdf');
    Route::get('/download-excel', [ReportController::class, 'downloadExcel'])->name('download-excel');
    Route::get('/stock-value',  [ReportController::class, 'stockValue'])->name('stock-value');
    Route::get('/expiry',       [ReportController::class, 'expiry'])->name('expiry');
    Route::get('/low-stock',    [ReportController::class, 'lowStock'])->name('low-stock');
});
    });

    // ── Department users ──────────────────────────────────
    Route::middleware('role:pharmacist,lab,theatre,ward')->group(function () {
        Route::get('/pos',  [SaleController::class, 'create'])->name('sales.create');
        Route::post('/pos', [SaleController::class, 'store'])->name('sales.store');
    });

    // ── Shared (admin + dept) ─────────────────────────────
    Route::get('/sales',                   [SaleController::class, 'index'])->name('sales.index');
    Route::get('/sales/{sale}',            [SaleController::class, 'show'])->name('sales.show');
    Route::get('/sales/{sale}/receipt',    [SaleController::class, 'receipt'])->name('sales.receipt');
});
