
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\AreaController;
use App\Http\Controllers\RackController;
use App\Http\Controllers\MonitoringCapacityController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\StorageTransactionController;
use App\Http\Controllers\StorageLocationController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
use App\Models\StorageTransaction;


// Halaman utama
Route::get('/', function () {
    return redirect()->route('login');
});

// Pengguna yang belum login
Route::middleware('guest')->group(function () {

    Route::get('/login', [AuthController::class, 'showLogin'])
        ->name('login');

    Route::post('/login', [AuthController::class, 'login'])
        ->name('login.process');
});

// Pengguna yang sudah login
Route::middleware(['auth', 'active'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->middleware('role:super_admin,admin_gudang,petugas_fg,supervisor')
        ->name('dashboard');

    // Monitoring Kapasitas
    Route::get('/monitoring-capacity', [MonitoringCapacityController::class, 'index'])
        ->middleware('role:super_admin,admin_gudang,petugas_fg,supervisor')
        ->name('monitoring.index');

    // Monitoring Lokasi Produk
    Route::get('/storage-locations', [StorageLocationController::class, 'index'])
        ->middleware('role:super_admin,admin_gudang,petugas_fg,supervisor')
        ->name('storage-locations.index');

    // Laporan Stok Produk berdasarkan Lokasi
    Route::get('/reports/stock-location', [ReportController::class, 'stockLocation'])
        ->middleware('role:super_admin,admin_gudang,petugas_fg,supervisor')
        ->name('reports.stock-location');

    // Laporan Riwayat Transaksi
    Route::get('/reports/transactions', [ReportController::class, 'transactionHistory'])
        ->middleware('role:super_admin,admin_gudang,petugas_fg,supervisor')
        ->name('reports.transactions');

    // Riwayat Transaksi Penyimpanan (Semua Role)
    Route::get('/transactions', [StorageTransactionController::class, 'index'])
        ->middleware('role:super_admin,admin_gudang,petugas_fg,supervisor')
        ->name('transactions.index');

    // Input Transaksi Penyimpanan (Khusus Pelaksana & Super Admin, Supervisor Read-Only)
    Route::middleware(['role:super_admin,admin_gudang,petugas_fg'])
        ->group(function () {
            Route::get('/transactions/create', [StorageTransactionController::class, 'create'])
                ->name('transactions.create');

            Route::post('/transactions', [StorageTransactionController::class, 'store'])
                ->name('transactions.store');
        });

    // Daftar & Audit Barang Rusak / Bad Stock (Semua Role)
    Route::get('/damaged-stocks', [App\Http\Controllers\DamagedStockController::class, 'index'])
        ->middleware('role:super_admin,admin_gudang,petugas_fg,supervisor')
        ->name('damaged-stocks.index');

    // Input Barang Rusak + Kamera Bukti (Khusus Pelaksana & Super Admin)
    Route::middleware(['role:petugas_fg'])
        ->group(function () {
            Route::get('/damaged-stocks/create', [App\Http\Controllers\DamagedStockController::class, 'create'])
                ->name('damaged-stocks.create');

            Route::post('/damaged-stocks', [App\Http\Controllers\DamagedStockController::class, 'store'])
                ->name('damaged-stocks.store');
        });

    // Pengelolaan Master Gudang
    Route::middleware('role:super_admin')->group(function () {

        Route::get('/warehouses', [WarehouseController::class, 'index'])
            ->name('warehouses.index');

        Route::get('/warehouses/create', [WarehouseController::class, 'create'])
            ->name('warehouses.create');

        Route::post('/warehouses', [WarehouseController::class, 'store'])
            ->name('warehouses.store');

        Route::get('/warehouses/{warehouse}/edit', [WarehouseController::class, 'edit'])
            ->name('warehouses.edit');

        Route::put('/warehouses/{warehouse}', [WarehouseController::class, 'update'])
            ->name('warehouses.update');

        Route::patch('/warehouses/{warehouse}/status', [WarehouseController::class, 'toggleStatus'])
            ->name('warehouses.toggle-status');

        Route::delete('/warehouses/{warehouse}', [WarehouseController::class, 'destroy'])
            ->name('warehouses.destroy');

        Route::patch('/users/{user}/status', [UserController::class, 'toggleStatus'])
            ->name('users.toggle-status');


        // Pengelolaan Master Area
        Route::get('/areas', [AreaController::class, 'index'])
            ->name('areas.index');

        Route::get('/areas/create', [AreaController::class, 'create'])
            ->name('areas.create');

        Route::post('/areas', [AreaController::class, 'store'])
            ->name('areas.store');

        Route::get('/areas/{area}/edit', [AreaController::class, 'edit'])
            ->name('areas.edit');

        Route::put('/areas/{area}', [AreaController::class, 'update'])
            ->name('areas.update');

        Route::patch('/areas/{area}/status', [AreaController::class, 'toggleStatus'])
            ->name('areas.toggle-status');

        Route::delete('/areas/{area}', [AreaController::class, 'destroy'])
            ->name('areas.destroy');


        // Pengelolaan Master Rak dan Level
        Route::get('/racks', [RackController::class, 'index'])
            ->name('racks.index');

        Route::get('/racks/create', [RackController::class, 'create'])
            ->name('racks.create');

        Route::post('/racks', [RackController::class, 'store'])
            ->name('racks.store');

        Route::get('/racks/{rack}/levels', [RackController::class, 'levels'])
            ->name('racks.levels');

        Route::get('/racks/{rack}/edit', [RackController::class, 'edit'])
            ->name('racks.edit');

        Route::put('/racks/{rack}', [RackController::class, 'update'])
            ->name('racks.update');

        Route::patch('/racks/{rack}/status', [RackController::class, 'toggleStatus'])
            ->name('racks.toggle-status');

        Route::delete('/racks/{rack}', [RackController::class, 'destroy'])
            ->name('racks.destroy');


        // Pengelolaan Master Kategori Produk
        Route::get('/categories', [CategoryController::class, 'index'])
            ->name('categories.index');

        Route::get('/categories/create', [CategoryController::class, 'create'])
            ->name('categories.create');

        Route::post('/categories', [CategoryController::class, 'store'])
            ->name('categories.store');

        Route::get('/categories/{category}/edit', [CategoryController::class, 'edit'])
            ->name('categories.edit');

        Route::put('/categories/{category}', [CategoryController::class, 'update'])
            ->name('categories.update');

        Route::patch('/categories/{category}/status', [CategoryController::class, 'toggleStatus'])
            ->name('categories.toggle-status');

        Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])
            ->name('categories.destroy');



        // Pengelolaan Master Produk
        Route::get('/products', [ProductController::class, 'index'])
            ->name('products.index');

        Route::get('/products/create', [ProductController::class, 'create'])
            ->name('products.create');

        Route::post('/products', [ProductController::class, 'store'])
            ->name('products.store');

        Route::get('/products/{product}/edit', [ProductController::class, 'edit'])
            ->name('products.edit');

        Route::put('/products/{product}', [ProductController::class, 'update'])
            ->name('products.update');

        Route::patch('/products/{product}/status', [ProductController::class, 'toggleStatus'])
            ->name('products.toggle-status');

        Route::delete('/products/{product}', [ProductController::class, 'destroy'])
            ->name('products.destroy');

        // Manajemen Pengguna
        Route::get('/users', [UserController::class, 'index'])
            ->name('users.index');

        Route::get('/users/create', [UserController::class, 'create'])
            ->name('users.create');

        Route::post('/users', [UserController::class, 'store'])
            ->name('users.store');

        Route::get('/users/{user}/edit', [UserController::class, 'edit'])
            ->name('users.edit');

        Route::put('/users/{user}', [UserController::class, 'update'])
            ->name('users.update');

        Route::delete('/users/{user}', [UserController::class, 'destroy'])
            ->name('users.destroy');
    });

    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])
        ->name('logout');
});
