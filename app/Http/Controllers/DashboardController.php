<?php

namespace App\Http\Controllers;

use App\Models\Warehouse;
use Illuminate\Support\Facades\Auth;
use App\Models\Area;
use App\Models\Rack;
use App\Models\Product;
use App\Models\RackLevel;
use App\Models\StoragePlacement;
use App\Models\StorageTransaction;
use App\Models\DamagedStock;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->role !== 'super_admin' && $user->warehouse_id === null) {
            abort(403, 'Akun Anda belum ditetapkan ke gudang.');
        }

        $warehouseId = $user->role === 'super_admin'
            ? null
            : $user->warehouse_id;

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

        $totalGudang = $warehouses->count();

        $areaQuery = Area::where('is_active', true);
        if ($warehouseId !== null) {
            $areaQuery->where('warehouse_id', $warehouseId);
        }
        $totalArea = $areaQuery->count();

        $rackQuery = Rack::where('is_active', true)
            ->whereHas('area', function ($query) use ($warehouseId) {
                $query->where('is_active', true);
                if ($warehouseId !== null) {
                    $query->where('warehouse_id', $warehouseId);
                }
            });

        $totalRak = $rackQuery->count();
        $totalProduk = Product::where('is_active', true)->count();

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

        $placementQuery = StoragePlacement::with('product.category')
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
        $totalPalletTerisi = (int) $placements->sum('pallet_quantity');
        $sisaKapasitasPallet = max(0, $totalKapasitasPallet - $totalPalletTerisi);

        $totalKartonTerisi = (int) $placements->sum(function ($placement) {
            $gross = $placement->pallet_quantity * ($placement->product->cartons_per_pallet ?? 120);
            return max(0, $gross - ($placement->damaged_cartons ?? 0));
        });

        $totalKapasitasKarton = $totalKapasitasPallet * 120;

        $damagedQuery = DamagedStock::when($warehouseId !== null, function ($query) use ($warehouseId) {
            $query->where('warehouse_id', $warehouseId);
        });

        $totalBarangRusak = (int) (clone $damagedQuery)->sum('damaged_cartons');

        $recentDamagedStocks = (clone $damagedQuery)
            ->with(['warehouse', 'rackLevel.rack', 'product', 'reporter'])
            ->orderByDesc('reported_at')
            ->limit(4)
            ->get();

        // Data Grafik 1: Tren Pergerakan Barang 7 Hari Terakhir (IN, OUT, MOVE)
        $startDate = Carbon::today()->subDays(6);
        $txQuery = StorageTransaction::whereDate('transaction_date', '>=', $startDate)
            ->when($warehouseId !== null, function ($query) use ($warehouseId) {
                $query->where(function ($sub) use ($warehouseId) {
                    $sub->whereHas('toRackLevel.rack.area', fn($q) => $q->where('warehouse_id', $warehouseId))
                        ->orWhereHas('fromRackLevel.rack.area', fn($q) => $q->where('warehouse_id', $warehouseId));
                });
            })
            ->get();

        $chartDates = [];
        $chartIn = [];
        $chartOut = [];
        $chartMove = [];

        for ($i = 6; $i >= 0; $i--) {
            $day = Carbon::today()->subDays($i);
            $dayStr = $day->format('Y-m-d');
            $chartDates[] = $day->format('d M');

            $dayTxs = $txQuery->filter(fn($tx) => $tx->transaction_date && $tx->transaction_date->format('Y-m-d') === $dayStr);

            $chartIn[] = (int) $dayTxs->where('transaction_type', 'IN')->sum('pallet_quantity');
            $chartOut[] = (int) $dayTxs->where('transaction_type', 'OUT')->sum('pallet_quantity');
            $chartMove[] = (int) $dayTxs->where('transaction_type', 'MOVE')->sum('pallet_quantity');
        }

        // Data Grafik 2: Komposisi Stok Aktif per Kategori Produk
        $categoryGrouped = $placements
            ->filter(fn($p) => $p->pallet_quantity > 0)
            ->groupBy(fn($p) => $p->product->category->category_name ?? ($p->product->product_name ?? 'Lainnya'))
            ->map(fn($group) => (int) $group->sum('pallet_quantity'));

        $categoryLabels = $categoryGrouped->keys()->values()->toArray();
        $categoryValues = $categoryGrouped->values()->toArray();

        // Hitung total dus rusak per rack_level_id & product_id dari tabel damaged_stocks
        $damagedByLevelProduct = DamagedStock::when($warehouseId !== null, function ($query) use ($warehouseId) {
            $query->where('warehouse_id', $warehouseId);
        })
            ->selectRaw('rack_level_id, product_id, SUM(damaged_cartons) as total_damaged')
            ->groupBy('rack_level_id', 'product_id')
            ->get()
            ->mapWithKeys(fn($row) => [$row->rack_level_id . '_' . $row->product_id => (int) $row->total_damaged]);

        // Data Visual Denah Rak 2D Interaktif
        $visualRacks = Rack::with([
            'area.warehouse',
            'levels' => function ($q) {
                $q->orderByDesc('level_number');
            },
            'levels.storagePlacement.product',
        ])
            ->where('is_active', true)
            ->whereHas('area', function ($query) use ($warehouseId) {
                $query->where('is_active', true);
                if ($warehouseId !== null) {
                    $query->where('warehouse_id', $warehouseId);
                }
            })
            ->orderBy('rack_code')
            ->get();

        $warehouseSummaries = $warehouses->map(function ($warehouse) {
            $totalAreaGudang = Area::where('is_active', true)
                ->where('warehouse_id', $warehouse->id)
                ->count();

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
                $gross = $placement->pallet_quantity * ($placement->product->cartons_per_pallet ?? 120);
                return max(0, $gross - ($placement->damaged_cartons ?? 0));
            });

            $kapasitasKarton = $kapasitasPallet * 120;
            $barangRusakGudang = (int) DamagedStock::where('warehouse_id', $warehouse->id)->sum('damaged_cartons');

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
                'barang_rusak' => $barangRusakGudang,
            ];
        });

        return view('dashboard', compact(
            'damagedByLevelProduct',
            'totalGudang',
            'totalArea',
            'totalRak',
            'totalProduk',
            'totalKapasitasPallet',
            'totalPalletTerisi',
            'sisaKapasitasPallet',
            'totalKapasitasKarton',
            'totalKartonTerisi',
            'totalBarangRusak',
            'recentDamagedStocks',
            'chartDates',
            'chartIn',
            'chartOut',
            'chartMove',
            'categoryLabels',
            'categoryValues',
            'visualRacks',
            'warehouseSummaries'
        ));
    }
}
