<?php

namespace App\Http\Controllers;

use App\Models\StoragePlacement;
use App\Models\User;
use App\Models\Warehouse;
use App\Models\Category;
use App\Models\StorageTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    /**
     * Menentukan gudang yang boleh diakses pengguna.
     */
    private function getWarehouseQuery(User $user)
    {
        $query = Warehouse::where('is_active', true);

        if ($user->role !== 'super_admin') {
            if ($user->warehouse_id === null) {
                $query->whereRaw('1 = 0');
            } else {
                $query->where('id', $user->warehouse_id);
            }
        }

        return $query;
    }

    /**
     * Laporan stok produk berdasarkan lokasi penyimpanan.
     */
    public function stockLocation(Request $request)
    {
        $user = Auth::user();

        // Daftar gudang sesuai hak akses pengguna.
        $warehouses = $this->getWarehouseQuery($user)
            ->orderBy('warehouse_name')
            ->get();

        $categories = Category::orderBy('category_name')->get();

        // Query utama laporan stok.
        $placementsQuery = StoragePlacement::with([
            'product.category',
            'rackLevel.rack.area.warehouse',
        ]);

        // Batasi data berdasarkan gudang pengguna.
        $placementsQuery->whereHas(
            'rackLevel.rack.area.warehouse',
            function ($query) use ($user) {
                $query->where('warehouses.is_active', true);

                if ($user->role !== 'super_admin') {
                    $query->where(
                        'warehouses.id',
                        $user->warehouse_id ?? 0
                    );
                }
            }
        );

        // Filter gudang hanya berlaku untuk Super Admin.
        if (
            $user->role === 'super_admin' &&
            $request->filled('warehouse_id')
        ) {
            $placementsQuery->whereHas(
                'rackLevel.rack.area.warehouse',
                function ($query) use ($request) {
                    $query->where(
                        'warehouses.id',
                        $request->warehouse_id
                    );
                }
            );
        }

        // Filter kategori produk.
        if ($request->filled('category_id')) {
            $placementsQuery->whereHas(
                'product',
                function ($query) use ($request) {
                    $query->where(
                        'category_id',
                        $request->category_id
                    );
                }
            );
        }

        // Pencarian produk atau kode rak.
        if ($request->filled('search')) {
            $search = trim($request->search);

            $placementsQuery->where(function ($query) use ($search) {
                $query->whereHas(
                    'product',
                    function ($productQuery) use ($search) {
                        $productQuery
                            ->where('product_code', 'ILIKE', "%{$search}%")
                            ->orWhere('product_name', 'ILIKE', "%{$search}%");
                    }
                )->orWhereHas(
                    'rackLevel.rack',
                    function ($rackQuery) use ($search) {
                        $rackQuery->where(
                            'rack_code',
                            'ILIKE',
                            "%{$search}%"
                        );
                    }
                );
            });
        }

        // Ringkasan seluruh data sesuai filter.
        $summary = (clone $placementsQuery)
            ->join(
                'products',
                'products.id',
                '=',
                'storage_placements.product_id'
            )
            ->selectRaw(
                'COALESCE(SUM(storage_placements.pallet_quantity), 0) AS total_pallets'
            )
            ->selectRaw(
                'COALESCE(SUM(storage_placements.pallet_quantity * products.cartons_per_pallet), 0) AS total_cartons'
            )
            ->first();

        // Daftar lokasi dengan pagination.
        $placements = $placementsQuery
            ->join(
                'rack_levels',
                'rack_levels.id',
                '=',
                'storage_placements.rack_level_id'
            )
            ->join(
                'racks',
                'racks.id',
                '=',
                'rack_levels.rack_id'
            )
            ->orderBy('racks.rack_code')
            ->orderBy('rack_levels.level_number')
            ->select('storage_placements.*')
            ->paginate(10)
            ->withQueryString();

        // Nilai gudang terpilih.
        $selectedWarehouse = $user->role === 'super_admin'
            ? $request->warehouse_id
            : $user->warehouse_id;

        return view('reports.stock-location', [
            'placements' => $placements,
            'warehouses' => $warehouses,
            'categories' => $categories,
            'totalLocations' => (clone $placementsQuery)->count(),
            'totalPallets' => (int) $summary->total_pallets,
            'totalCartons' => (int) $summary->total_cartons,
            'selectedWarehouse' => $selectedWarehouse,
            'selectedCategory' => $request->category_id,
            'search' => $request->search,
        ]);
    }

    /**
     * Laporan riwayat transaksi penyimpanan.
     */
    public function transactionHistory(Request $request)
    {
        $request->validate([
            'start_date' => ['nullable', 'date'],
            'end_date' => [
                'nullable',
                'date',
                'after_or_equal:start_date',
            ],
            'warehouse_id' => ['nullable', 'exists:warehouses,id'],
            'transaction_type' => [
                'nullable',
                'in:IN,OUT,MOVE',
            ],
            'search' => ['nullable', 'string', 'max:100'],
        ]);

        $user = Auth::user();

        // Daftar gudang sesuai hak akses.
        $warehouses = $this->getWarehouseQuery($user)
            ->orderBy('warehouse_name')
            ->get();

        $query = StorageTransaction::with([
            'product',
            'fromRackLevel.rack.area.warehouse',
            'toRackLevel.rack.area.warehouse',
            'user',
        ]);

        // Batasi transaksi berdasarkan gudang pengguna.
        if ($user->role !== 'super_admin') {

            if ($user->warehouse_id === null) {
                $query->whereRaw('1 = 0');
            } else {
                $warehouseId = $user->warehouse_id;

                $query->where(function ($q) use ($warehouseId) {

                    // Barang masuk: gudang tujuan.
                    $q->where(function ($in) use ($warehouseId) {
                        $in->where('transaction_type', 'IN')
                            ->whereHas(
                                'toRackLevel.rack.area.warehouse',
                                function ($warehouseQuery) use ($warehouseId) {
                                    $warehouseQuery
                                        ->where('warehouses.id', $warehouseId)
                                        ->where('warehouses.is_active', true);
                                }
                            );
                    })

                        // Barang keluar: gudang asal.
                        ->orWhere(function ($out) use ($warehouseId) {
                            $out->where('transaction_type', 'OUT')
                                ->whereHas(
                                    'fromRackLevel.rack.area.warehouse',
                                    function ($warehouseQuery) use ($warehouseId) {
                                        $warehouseQuery
                                            ->where('warehouses.id', $warehouseId)
                                            ->where('warehouses.is_active', true);
                                    }
                                );
                        })

                        // Pindah lokasi: asal dan tujuan harus
                        // berada di gudang pengguna yang sama.
                        ->orWhere(function ($move) use ($warehouseId) {
                            $move->where('transaction_type', 'MOVE')
                                ->whereHas(
                                    'fromRackLevel.rack.area.warehouse',
                                    function ($warehouseQuery) use ($warehouseId) {
                                        $warehouseQuery
                                            ->where('warehouses.id', $warehouseId)
                                            ->where('warehouses.is_active', true);
                                    }
                                )
                                ->whereHas(
                                    'toRackLevel.rack.area.warehouse',
                                    function ($warehouseQuery) use ($warehouseId) {
                                        $warehouseQuery
                                            ->where('warehouses.id', $warehouseId)
                                            ->where('warehouses.is_active', true);
                                    }
                                );
                        });
                });
            }
        }

        // Filter tanggal awal.
        if ($request->filled('start_date')) {
            $query->whereDate(
                'transaction_date',
                '>=',
                $request->start_date
            );
        }

        // Filter tanggal akhir.
        if ($request->filled('end_date')) {
            $query->whereDate(
                'transaction_date',
                '<=',
                $request->end_date
            );
        }

        // Filter jenis transaksi.
        if ($request->filled('transaction_type')) {
            $query->where(
                'transaction_type',
                $request->transaction_type
            );
        }

        // Filter gudang tambahan hanya untuk Super Admin.
        if (
            $user->role === 'super_admin' &&
            $request->filled('warehouse_id')
        ) {
            $warehouseId = $request->warehouse_id;

            $query->where(function ($q) use ($warehouseId) {
                $q->whereHas(
                    'fromRackLevel.rack.area.warehouse',
                    function ($warehouseQuery) use ($warehouseId) {
                        $warehouseQuery->where(
                            'warehouses.id',
                            $warehouseId
                        );
                    }
                )->orWhereHas(
                    'toRackLevel.rack.area.warehouse',
                    function ($warehouseQuery) use ($warehouseId) {
                        $warehouseQuery->where(
                            'warehouses.id',
                            $warehouseId
                        );
                    }
                );
            });
        }

        // Pencarian kode atau nama produk.
        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->whereHas(
                'product',
                function ($productQuery) use ($search) {
                    $productQuery
                        ->where('product_code', 'ILIKE', "%{$search}%")
                        ->orWhere('product_name', 'ILIKE', "%{$search}%");
                }
            );
        }

        // Ringkasan berdasarkan seluruh data hasil filter.
        $summary = (clone $query)
            ->selectRaw('COUNT(*) AS total_transactions')
            ->selectRaw('COALESCE(SUM(pallet_quantity), 0) AS total_pallets')
            ->selectRaw(
                "SUM(CASE WHEN transaction_type = 'IN' THEN 1 ELSE 0 END) AS total_in"
            )
            ->selectRaw(
                "SUM(CASE WHEN transaction_type = 'OUT' THEN 1 ELSE 0 END) AS total_out"
            )
            ->selectRaw(
                "SUM(CASE WHEN transaction_type = 'MOVE' THEN 1 ELSE 0 END) AS total_move"
            )
            ->first();

        // Daftar transaksi.
        $transactions = $query
            ->oldest('id')
            ->paginate(10)
            ->withQueryString();

        $selectedWarehouse = $user->role === 'super_admin'
            ? $request->warehouse_id
            : $user->warehouse_id;

        return view('reports.transactions', [
            'transactions' => $transactions,
            'warehouses' => $warehouses,
            'totalTransactions' => (int) $summary->total_transactions,
            'totalPallets' => (int) $summary->total_pallets,
            'totalIn' => (int) $summary->total_in,
            'totalOut' => (int) $summary->total_out,
            'totalMove' => (int) $summary->total_move,
            'selectedWarehouse' => $selectedWarehouse,
            'selectedType' => $request->transaction_type,
            'startDate' => $request->start_date,
            'endDate' => $request->end_date,
            'search' => $request->search,
        ]);
    }
}
