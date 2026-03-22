<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PatientController;
use App\Http\Controllers\Admin\MedicalRecordController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\TransactionController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ImportExportController;
use App\Http\Controllers\Admin\SupplierController;
use App\Http\Controllers\Admin\PurchaseOrderController;
use App\Http\Controllers\Admin\ActivityLogController;

Route::get('/', fn() => redirect()->route('dashboard'));

require __DIR__.'/auth.php';

Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::post('/heartbeat', function (Request $request) {
        session(['last_activity' => now()->timestamp]);
        return response()->json(['ok' => true]);
    })->name('heartbeat')->withoutMiddleware([\App\Http\Middleware\TrackActivity::class]);

    // ── Pasien ────────────────────────────────────────────────────
    Route::middleware(['permission:patient.view'])->group(function () {
        Route::get('patients', [PatientController::class, 'index'])->name('patients.index');
        Route::get('patients/search/json', [PatientController::class, 'search'])->name('patients.search');
    });
    Route::middleware(['permission:patient.create'])->group(function () {
        Route::get('patients/create', [PatientController::class, 'create'])->name('patients.create');
        Route::post('patients', [PatientController::class, 'store'])->name('patients.store');
    });
    Route::middleware(['permission:patient.edit'])->group(function () {
        Route::get('patients/{patient}/edit', [PatientController::class, 'edit'])->name('patients.edit');
        Route::put('patients/{patient}', [PatientController::class, 'update'])->name('patients.update');
    });
    Route::middleware(['permission:patient.delete'])->group(function () {
        Route::delete('patients/{patient}', [PatientController::class, 'destroy'])->name('patients.destroy');
    });
    Route::middleware(['permission:patient.view'])->group(function () {
        Route::get('patients/{patient}/kartu', [PatientController::class, 'printCard'])->name('patients.card');
        Route::get('patients/{patient}', [PatientController::class, 'show'])->name('patients.show');
    });

    // ── Rekam Medis ───────────────────────────────────────────────
    Route::middleware(['permission:medical_record.view'])->group(function () {
        Route::get('medical-records', [MedicalRecordController::class, 'index'])->name('medical-records.index');
    });
    Route::middleware(['permission:medical_record.create'])->group(function () {
        Route::get('medical-records/create', [MedicalRecordController::class, 'create'])->name('medical-records.create');
        Route::post('medical-records', [MedicalRecordController::class, 'store'])->name('medical-records.store');
    });
    Route::middleware(['permission:medical_record.edit'])->group(function () {
        Route::get('medical-records/{medicalRecord}/edit', [MedicalRecordController::class, 'edit'])->name('medical-records.edit');
        Route::put('medical-records/{medicalRecord}', [MedicalRecordController::class, 'update'])->name('medical-records.update');
    });
    Route::middleware(['permission:medical_record.delete'])->group(function () {
        Route::delete('medical-records/{medicalRecord}', [MedicalRecordController::class, 'destroy'])->name('medical-records.destroy');
    });
    Route::middleware(['permission:medical_record.view'])->group(function () {
        Route::get('medical-records/{medicalRecord}', [MedicalRecordController::class, 'show'])->name('medical-records.show');
    });

    // ── Produk & Kategori ─────────────────────────────────────────
    Route::middleware(['permission:product.view'])->group(function () {
        Route::get('products', [ProductController::class, 'index'])->name('products.index');
        Route::get('categories', [CategoryController::class, 'index'])->name('categories.index');
    });
    Route::middleware(['permission:product.create'])->group(function () {
        Route::get('products/create', [ProductController::class, 'create'])->name('products.create');
        Route::post('products', [ProductController::class, 'store'])->name('products.store');
        Route::get('categories/create', [CategoryController::class, 'create'])->name('categories.create');
        Route::post('categories', [CategoryController::class, 'store'])->name('categories.store');
    });
    Route::middleware(['permission:product.edit'])->group(function () {
        Route::get('products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
        Route::put('products/{product}', [ProductController::class, 'update'])->name('products.update');
        Route::get('categories/{category}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
        Route::put('categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
    });
    Route::middleware(['permission:product.delete'])->group(function () {
        Route::delete('products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');
        Route::delete('categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');
    });
    Route::middleware(['permission:product.view'])->group(function () {
        Route::get('products/{product}', [ProductController::class, 'show'])->name('products.show');
    });

    // ── Transaksi ─────────────────────────────────────────────────
    // PENTING: Route statis (tanpa wildcard) HARUS di atas route {transaction}
    Route::middleware(['permission:transaction.view'])->group(function () {
        Route::get('transactions', [TransactionController::class, 'index'])->name('transactions.index');
        // AJAX endpoints — harus di atas {transaction} agar tidak tertangkap wildcard
        Route::get('transactions/product/search', [TransactionController::class, 'searchProduct'])->name('transactions.product.search');
        Route::get('transactions/get-medical-records', [TransactionController::class, 'getMedicalRecords'])->name('transactions.medical-records');
    });
    Route::middleware(['permission:transaction.create'])->group(function () {
        // /create juga harus di atas {transaction}
        Route::get('transactions/create', [TransactionController::class, 'create'])->name('transactions.create');
        Route::post('transactions', [TransactionController::class, 'store'])->name('transactions.store');
    });
    Route::middleware(['permission:transaction.edit'])->group(function () {
        Route::patch('transactions/{transaction}/cancel', [TransactionController::class, 'cancel'])->name('transactions.cancel');
    });
    Route::middleware(['permission:transaction.view'])->group(function () {
        // Wildcard routes TERAKHIR
        Route::get('transactions/{transaction}/invoice', [TransactionController::class, 'invoice'])->name('transactions.invoice');
        Route::get('transactions/{transaction}', [TransactionController::class, 'show'])->name('transactions.show');
    });

    // ── Laporan ───────────────────────────────────────────────────
    Route::middleware(['permission:report.view'])->prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/penjualan', [ReportController::class, 'penjualan'])->name('penjualan');
        Route::get('/penjualan/print', [ReportController::class, 'printPenjualan'])->name('penjualan.print');
        Route::get('/produk-terlaris', [ReportController::class, 'produkTerlaris'])->name('produk-terlaris');
        Route::get('/stok', [ReportController::class, 'stok'])->name('stok');
        Route::get('/stok/print', [ReportController::class, 'printStok'])->name('stok.print');
        Route::get('/mutasi-stok', [ReportController::class, 'mutasiStok'])->name('mutasi-stok');
        Route::get('/mutasi-stok/print', [ReportController::class, 'printMutasiStok'])->name('mutasi-stok.print');
    });

    // ── Import / Export ───────────────────────────────────────────
    Route::get('/import', [ImportExportController::class, 'index'])->name('import.index');
    Route::get('/import/template/{type}', [ImportExportController::class, 'downloadTemplate'])
        ->name('import.template')
        ->where('type', 'produk|pasien');
    Route::middleware(['permission:product.create'])->group(function () {
        Route::post('/import/produk', [ImportExportController::class, 'importProduk'])->name('import.produk');
    });
    Route::middleware(['permission:patient.create'])->group(function () {
        Route::post('/import/pasien', [ImportExportController::class, 'importPasien'])->name('import.pasien');
    });
    Route::middleware(['permission:product.view'])->group(function () {
        Route::get('/export/produk', [ImportExportController::class, 'exportProduk'])->name('export.produk');
    });
    Route::middleware(['permission:patient.view'])->group(function () {
        Route::get('/export/pasien', [\App\Http\Controllers\Admin\ReportController::class, 'exportPasienExcel'])->name('export.pasien');
    });

    // ── Supplier ──────────────────────────────────────────────────
    Route::middleware(['permission:supplier.view'])->group(function () {
        Route::get('suppliers', [SupplierController::class, 'index'])->name('suppliers.index');
    });
    Route::middleware(['permission:supplier.create'])->group(function () {
        Route::get('suppliers/create', [SupplierController::class, 'create'])->name('suppliers.create');
        Route::post('suppliers', [SupplierController::class, 'store'])->name('suppliers.store');
    });
    Route::middleware(['permission:supplier.edit'])->group(function () {
        Route::get('suppliers/{supplier}/edit', [SupplierController::class, 'edit'])->name('suppliers.edit');
        Route::put('suppliers/{supplier}', [SupplierController::class, 'update'])->name('suppliers.update');
    });
    Route::middleware(['permission:supplier.delete'])->group(function () {
        Route::delete('suppliers/{supplier}', [SupplierController::class, 'destroy'])->name('suppliers.destroy');
    });
    Route::middleware(['permission:supplier.view'])->group(function () {
        Route::get('suppliers/{supplier}', [SupplierController::class, 'show'])->name('suppliers.show');
    });

    // ── Purchase Order ────────────────────────────────────────────
    Route::middleware(['permission:purchase_order.view'])->group(function () {
        Route::get('purchase-orders', [PurchaseOrderController::class, 'index'])->name('purchase-orders.index');
    });
    Route::middleware(['permission:purchase_order.create'])->group(function () {
        Route::get('purchase-orders/create', [PurchaseOrderController::class, 'create'])->name('purchase-orders.create');
        Route::post('purchase-orders', [PurchaseOrderController::class, 'store'])->name('purchase-orders.store');
    });
    Route::middleware(['permission:purchase_order.edit'])->group(function () {
        Route::patch('purchase-orders/{purchaseOrder}/receive', [PurchaseOrderController::class, 'receive'])->name('purchase-orders.receive');
        Route::patch('purchase-orders/{purchaseOrder}/cancel', [PurchaseOrderController::class, 'cancel'])->name('purchase-orders.cancel');
    });
    Route::middleware(['permission:purchase_order.view'])->group(function () {
        Route::get('purchase-orders/{purchaseOrder}', [PurchaseOrderController::class, 'show'])->name('purchase-orders.show');
    });

    // ── Activity Log ──────────────────────────────────────────────
    Route::middleware(['role:super_admin'])->group(function () {
        Route::get('activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');
        Route::resource('users', UserController::class);
    });
});
