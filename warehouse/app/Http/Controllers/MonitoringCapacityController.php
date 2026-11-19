<?php

namespace App\Http\Controllers;

use App\Models\Warehouse;

class MonitoringCapacityController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $warehouseQuery = Warehouse::with([
            'areas' => function ($query) {
                $query->where('is_active', true);
            },
            'areas.racks' => function ($query) {
                $query->where('is_active', true);
            },
            'areas.racks.rackLevels.storagePlacement.product',
        ])->where('is_active', true);

        // Super Admin dapat melihat semua gudang aktif.
        // Role lainnya hanya dapat melihat gudang penugasannya.
        if ($user->role !== 'super_admin') {
            if ($user->warehouse_id === null) {
                $warehouseQuery->whereRaw('1 = 0');
            } else {
                $warehouseQuery->where('id', $user->warehouse_id);
            }
        }

        $warehouses = $warehouseQuery
            ->orderBy('warehouse_code')
            ->get();

        $totalCapacity = $warehouses->sum(function ($warehouse) {
            return $warehouse->areas->sum(function ($area) {
                return $area->racks->sum(function ($rack) {
                    return $rack->rackLevels->sum('capacity_pallets');
                });
            });
        });

        $occupiedPallets = $warehouses->sum(function ($warehouse) {
            return $warehouse->areas->sum(function ($area) {
                return $area->racks->sum(function ($rack) {
                    return $rack->rackLevels->sum(function ($level) {
                        return $level->storagePlacement
                            ? $level->storagePlacement->pallet_quantity
                            : 0;
                    });
                });
            });
        });

        $remainingCapacity = max(
            0,
            $totalCapacity - $occupiedPallets
        );

        $usagePercentage = $totalCapacity > 0
            ? round(($occupiedPallets / $totalCapacity) * 100, 2)
            : 0;

        return view('monitoring.index', compact(
            'warehouses',
            'totalCapacity',
            'occupiedPallets',
            'remainingCapacity',
            'usagePercentage'
        ));
    }
}
