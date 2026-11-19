<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AreaController extends Controller
{
    // Menampilkan daftar area
    public function index(Request $request)
    {
        $warehouses = Warehouse::orderBy('warehouse_code')->get();

        $query = Area::with('warehouse')
            ->withCount('racks');

        // Filter berdasarkan gudang
        if ($request->filled('warehouse_id')) {
            $query->where('warehouse_id', $request->warehouse_id);
        }

        $areas = $query
            ->latest()
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
        $warehouses = Warehouse::where('is_active', true)
            ->orderBy('warehouse_code')
            ->get();

        return view('areas.create', compact('warehouses'));
    }

    // Menyimpan area baru
    public function store(Request $request)
    {
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
        $warehouses = Warehouse::where('is_active', true)
            ->orWhere('id', $area->warehouse_id)
            ->orderBy('warehouse_code')
            ->get();

        return view('areas.edit', compact(
            'area',
            'warehouses'
        ));
    }

    // Memperbarui data area
    public function update(Request $request, Area $area)
    {
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
}
