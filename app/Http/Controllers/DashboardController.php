<?php

namespace App\Http\Controllers;

use App\Models\Warehouse;
use Illuminate\Support\Facades\Auth;
use App\Models\Area;
use App\Models\Rack;
use App\Models\Product;
use App\Models\RackLevel;
use App\Models\StoragePlacement;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth::user();

        // Pengamanan akun non-Super Admin.
        // Pengguna wajib memiliki penugasan gudang.
        if ($user->role !== 'super_admin' && $user->warehouse_id === null) {
            abort(403, 'Akun Anda belum ditetapkan ke gudang.');
        }

        // Super Admin melihat seluruh gudang aktif.
        // Role lainnya hanya melihat gudang penugasannya.
        $warehouseId = $user->role === 'super_admin'
            ? null
            : $user->warehouse_id;

        // Ambil daftar gudang untuk ringkasan per gudang.
        // Nama kolom mengikuti struktur database: warehouse_name.
        $warehouses = Warehouse::where('is_active', true)
            ->when($warehouseId !== null, function ($query) use ($warehouseId) {
                $query->where('id', $warehouseId);
            })
            ->orderBy('warehouse_code')
            ->get([
                'id',
                'warehouse_code',
                'warehouse_name',
            ]);

        // Total gudang aktif dalam cakupan akses pengguna.
        $totalGudang = $warehouses->count();

        // Total area aktif.
        $areaQuery = Area::where('is_active', true);

        if ($warehouseId !== null) {
            $areaQuery->where('warehouse_id', $warehouseId);
        }

        $totalArea = $areaQuery->count();

        // Total rak aktif berdasarkan area aktif.
        $rackQuery = Rack::where('is_active', true)
            ->whereHas('area', function ($query) use ($warehouseId) {
                $query->where('is_active', true);

                if ($warehouseId !== null) {
                    $query->where('warehouse_id', $warehouseId);
                }
            });

        $totalRak = $rackQuery->count();

        // Master produk digunakan bersama oleh seluruh gudang.
        $totalProduk = Product::where('is_active', true)->count();

        // Kapasitas pallet berdasarkan level rak aktif.
        $rackLevelQuery = RackLevel::whereHas('rack', function ($query) use ($warehouseId) {
            $query->where('is_active', true)
                ->whereHas('area', function ($areaQuery) use ($warehouseId) {
                    $areaQuery->where('is_active', true);

                    if ($warehouseId !== null) {
                        $areaQuery->where('warehouse_id', $warehouseId);
                    }
                });
        });

        $totalKapasitasPallet = (int) $rackLevelQuery->sum('capacity_pallets');

        // Placement hanya pada level rak di gudang yang sesuai.
        $placementQuery = StoragePlacement::with('product')
            ->whereHas('rackLevel', function ($query) use ($warehouseId) {
                $query->whereHas('rack', function ($rackQuery) use ($warehouseId) {
                    $rackQuery->where('is_active', true)
                        ->whereHas('area', function ($areaQuery) use ($warehouseId) {
                            $areaQuery->where('is_active', true);

                            if ($warehouseId !== null) {
                                $areaQuery->where('warehouse_id', $warehouseId);
                            }
                        });
                });
            });

        $placements = $placementQuery->get();

        // Total pallet terisi.
        $totalPalletTerisi = (int) $placements->sum('pallet_quantity');

        // Sisa kapasitas pallet.
        $sisaKapasitasPallet = max(
            0,
            $totalKapasitasPallet - $totalPalletTerisi
        );

        // Total karton terisi berdasarkan kapasitas masing-masing produk.
        $totalKartonTerisi = (int) $placements->sum(function ($placement) {
            return $placement->pallet_quantity *
                ($placement->product->cartons_per_pallet ?? 120);
        });

        // Estimasi kapasitas karton: 120 karton per pallet.
        $totalKapasitasKarton = $totalKapasitasPallet * 120;

        // Ringkasan masing-masing gudang untuk dashboard.
        // Super Admin akan mendapatkan FG-01 dan FG-02.
        // Role lain hanya mendapatkan ringkasan gudang penugasannya.
        $warehouseSummaries = $warehouses->map(function ($warehouse) {
            $areaQuery = Area::where('is_active', true)
                ->where('warehouse_id', $warehouse->id);

            $totalAreaGudang = $areaQuery->count();

            $totalRakGudang = Rack::where('is_active', true)
                ->whereHas('area', function ($query) use ($warehouse) {
                    $query->where('is_active', true)
                        ->where('warehouse_id', $warehouse->id);
                })
                ->count();

            $rackLevelQuery = RackLevel::whereHas('rack', function ($query) use ($warehouse) {
                $query->where('is_active', true)
                    ->whereHas('area', function ($areaQuery) use ($warehouse) {
                        $areaQuery->where('is_active', true)
                            ->where('warehouse_id', $warehouse->id);
                    });
            });

            $kapasitasPallet = (int) $rackLevelQuery->sum('capacity_pallets');

            $placementsGudang = StoragePlacement::with('product')
                ->whereHas('rackLevel', function ($query) use ($warehouse) {
                    $query->whereHas('rack', function ($rackQuery) use ($warehouse) {
                        $rackQuery->where('is_active', true)
                            ->whereHas('area', function ($areaQuery) use ($warehouse) {
                                $areaQuery->where('is_active', true)
                                    ->where('warehouse_id', $warehouse->id);
                            });
                    });
                })
                ->get();

            $palletTerisi = (int) $placementsGudang->sum('pallet_quantity');

            $palletTersedia = max(0, $kapasitasPallet - $palletTerisi);

            $kartonTerisi = (int) $placementsGudang->sum(function ($placement) {
                return $placement->pallet_quantity *
                    ($placement->product->cartons_per_pallet ?? 120);
            });

            $kapasitasKarton = $kapasitasPallet * 120;

            $persentaseTerisi = $kapasitasPallet > 0
                ? min(100, ($palletTerisi / $kapasitasPallet) * 100)
                : 0;

            return [
                'id' => $warehouse->id,
                'warehouse_code' => $warehouse->warehouse_code,
                'warehouse_name' => $warehouse->warehouse_name,
                'total_area' => $totalAreaGudang,
                'total_rak' => $totalRakGudang,
                'kapasitas_pallet' => $kapasitasPallet,
                'pallet_terisi' => $palletTerisi,
                'pallet_tersedia' => $palletTersedia,
                'persentase_terisi' => round($persentaseTerisi, 1),
                'kapasitas_karton' => $kapasitasKarton,
                'karton_terisi' => $kartonTerisi,
            ];
        });

        return view('dashboard', compact(
            'totalGudang',
            'totalArea',
            'totalRak',
            'totalProduk',
            'totalKapasitasPallet',
            'totalPalletTerisi',
            'sisaKapasitasPallet',
            'totalKapasitasKarton',
            'totalKartonTerisi',
            'warehouseSummaries'
        ));
    }
}
