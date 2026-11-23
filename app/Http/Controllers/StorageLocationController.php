<?php

namespace App\Http\Controllers;

use App\Models\StoragePlacement;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StorageLocationController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        // Query utama lokasi penyimpanan
        $query = StoragePlacement::with([
            'product.category',
            'rackLevel.rack.area.warehouse',
        ]);

        // ==========================================
        // 1. PEMBATASAN HAK AKSES BERDASARKAN GUDANG
        // ==========================================

        $query->whereHas(
            'rackLevel.rack.area.warehouse',
            function ($warehouseQuery) use ($user) {

                // Hanya gudang aktif yang ditampilkan
                $warehouseQuery->where('is_active', true);

                // Selain Super Admin hanya boleh melihat
                // gudang yang ditugaskan kepadanya
                if ($user->role !== 'super_admin') {

                    if ($user->warehouse_id === null) {
                        $warehouseQuery->whereRaw('1 = 0');
                    } else {
                        $warehouseQuery->where(
                            'id',
                            $user->warehouse_id
                        );
                    }
                }
            }
        );

        // ==========================================
        // 2. FILTER GUDANG KHUSUS SUPER ADMIN
        // ==========================================

        if (
            $user->role === 'super_admin' &&
            $request->filled('warehouse_id')
        ) {
            $query->whereHas(
                'rackLevel.rack.area',
                function ($areaQuery) use ($request) {
                    $areaQuery->where(
                        'warehouse_id',
                        $request->warehouse_id
                    );
                }
            );
        }

        // ==========================================
        // 3. PENCARIAN PRODUK ATAU KODE RAK
        // ==========================================

        if ($request->filled('search')) {

            $search = $request->search;

            $query->where(function ($q) use ($search) {

                $q->whereHas(
                    'product',
                    function ($productQuery) use ($search) {
                        $productQuery
                            ->where('product_code', 'ILIKE', "%{$search}%")
                            ->orWhere('product_name', 'ILIKE', "%{$search}%");
                    }
                );

                $q->orWhereHas(
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

        // ==========================================
        // 4. RINGKASAN DATA
        // ==========================================

        $summaryPlacements = (clone $query)
            ->with('product')
            ->get();

        $totalLokasi = $summaryPlacements->count();

        $totalPallet = $summaryPlacements->sum('pallet_quantity');

        $totalKarton = $summaryPlacements->sum(
            function ($placement) {
                return $placement->pallet_quantity *
                    $placement->product->cartons_per_pallet;
            }
        );

        // ==========================================
        // 5. DAFTAR LOKASI DAN PAGINATION
        // ==========================================

        $placements = $query
            ->orderBy('product_id')
            ->paginate(15)
            ->withQueryString();

        // ==========================================
        // 6. DAFTAR GUDANG PADA DROPDOWN
        // ==========================================

        $warehouseQuery = Warehouse::where('is_active', true)
            ->orderBy('warehouse_name');

        if ($user->role !== 'super_admin') {

            if ($user->warehouse_id === null) {
                $warehouseQuery->whereRaw('1 = 0');
            } else {
                $warehouseQuery->where(
                    'id',
                    $user->warehouse_id
                );
            }
        }

        $warehouses = $warehouseQuery->get();

        return view('storage-locations.index', compact(
            'placements',
            'warehouses',
            'totalLokasi',
            'totalPallet',
            'totalKarton'
        ));
    }
}
