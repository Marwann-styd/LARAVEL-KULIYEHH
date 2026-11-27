<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class AreaController extends Controller
{
    // Menampilkan daftar area
    public function index(Request $request)
    {
        $user = Auth::user();

        $warehousesQuery = Warehouse::orderBy('warehouse_code');
        if ($user->role !== 'super_admin') {
            $warehousesQuery->where('id', $user->warehouse_id ?? 0);
        }
        $warehouses = $warehousesQuery->get();

        $query = Area::with('warehouse')
            ->withCount('racks');

        // Isolasi gudang untuk non-super_admin
        if ($user->role !== 'super_admin') {
            $query->where('warehouse_id', $user->warehouse_id ?? 0);
        } elseif ($request->filled('warehouse_id')) {
            $query->where('warehouse_id', $request->warehouse_id);
        }

        $areas = $query
            ->oldest()
            ->paginate(10)
            ->withQueryString();

        return view('areas.index', compact(
            'areas',
            'warehouses'
        ));
    }

    // Menampilkan form tambah area
    public function create()
    {
        $user = Auth::user();

        $warehousesQuery = Warehouse::where('is_active', true)
            ->orderBy('warehouse_code');

        if ($user->role !== 'super_admin') {
            $warehousesQuery->where('id', $user->warehouse_id ?? 0);
        }

        $warehouses = $warehousesQuery->get();

        return view('areas.create', compact('warehouses'));
    }

    // Menyimpan area baru
    public function store(Request $request)
    {
        $user = Auth::user();

        if ($user->role !== 'super_admin') {
            $request->merge(['warehouse_id' => $user->warehouse_id]);
        }

        $validated = $request->validate([
            'warehouse_id' => [
                'required',
                'exists:warehouses,id',
            ],

            'area_code' => [
                'required',
                'string',
                'max:20',
                Rule::unique('areas', 'area_code')
                    ->where('warehouse_id', $request->warehouse_id),
            ],

            'area_name' => [
                'required',
                'string',
                'max:100',
            ],

            'description' => [
                'nullable',
                'string',
            ],
        ]);

        Area::create([
            ...$validated,
            'is_active' => true,
        ]);

        return redirect()
            ->route('areas.index')
            ->with('success', 'Data area berhasil ditambahkan.');
    }

    // Menampilkan form edit area
    public function edit(Area $area)
    {
        $this->authorizeWarehouseAccess($area);

        $user = Auth::user();
        $warehousesQuery = Warehouse::where(function ($q) use ($area) {
            $q->where('is_active', true)
                ->orWhere('id', $area->warehouse_id);
        })->orderBy('warehouse_code');

        if ($user->role !== 'super_admin') {
            $warehousesQuery->where('id', $user->warehouse_id ?? 0);
        }

        $warehouses = $warehousesQuery->get();

        return view('areas.edit', compact(
            'area',
            'warehouses'
        ));
    }

    // Memperbarui data area
    public function update(Request $request, Area $area)
    {
        $this->authorizeWarehouseAccess($area);

        $user = Auth::user();
        if ($user->role !== 'super_admin') {
            $request->merge(['warehouse_id' => $user->warehouse_id]);
        }

        $validated = $request->validate([
            'warehouse_id' => [
                'required',
                'exists:warehouses,id',
            ],

            'area_code' => [
                'required',
                'string',
                'max:20',
                Rule::unique('areas', 'area_code')
                    ->where('warehouse_id', $request->warehouse_id)
                    ->ignore($area->id),
            ],

            'area_name' => [
                'required',
                'string',
                'max:100',
            ],

            'description' => [
                'nullable',
                'string',
            ],
        ]);

        $area->update($validated);

        return redirect()
            ->route('areas.index')
            ->with('success', 'Data area berhasil diperbarui.');
    }

    // Mengaktifkan atau menonaktifkan area
    public function toggleStatus(Area $area)
    {
        $this->authorizeWarehouseAccess($area);

        $area->update([
            'is_active' => !$area->is_active,
        ]);

        return redirect()
            ->route('areas.index')
            ->with('success', 'Status area berhasil diperbarui.');
    }

    // Menghapus area yang belum memiliki rak
    public function destroy(Area $area)
    {
        $this->authorizeWarehouseAccess($area);

        if ($area->racks()->exists()) {
            return redirect()
                ->route('areas.index')
                ->with('error', 'Area tidak dapat dihapus karena masih memiliki rak.');
        }

        $area->delete();

        return redirect()
            ->route('areas.index')
            ->with('success', 'Data area berhasil dihapus.');
    }

    private function authorizeWarehouseAccess(Area $area): void
    {
        $user = Auth::user();
        if ($user->role !== 'super_admin' && (int) $area->warehouse_id !== (int) $user->warehouse_id) {
            abort(403, 'Anda tidak memiliki akses ke area gudang lain.');
        }
    }
}
