<?php

namespace App\Http\Controllers;

use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class WarehouseController extends Controller
{
    // Menampilkan daftar gudang
    public function index()
    {
        $warehouses = Warehouse::withCount('areas')
            ->latest()
            ->paginate(10);

        return view('warehouses.index', compact('warehouses'));
    }

    // Menampilkan form tambah gudang
    public function create()
    {
        return view('warehouses.create');
    }

    // Menyimpan gudang baru
    public function store(Request $request)
    {
        $validated = $request->validate([
            'warehouse_code' => [
                'required',
                'string',
                'max:20',
                'unique:warehouses,warehouse_code',
            ],
            'warehouse_name' => [
                'required',
                'string',
                'max:100',
            ],
            'description' => [
                'nullable',
                'string',
            ],
        ]);

        Warehouse::create([
            ...$validated,
            'is_active' => true,
        ]);

        return redirect()
            ->route('warehouses.index')
            ->with('success', 'Data gudang berhasil ditambahkan.');
    }

    // Menampilkan form edit gudang
    public function edit(Warehouse $warehouse)
    {
        return view('warehouses.edit', compact('warehouse'));
    }

    // Memperbarui informasi gudang
    public function update(Request $request, Warehouse $warehouse)
    {
        $validated = $request->validate([
            'warehouse_code' => [
                'required',
                'string',
                'max:20',
                Rule::unique('warehouses', 'warehouse_code')
                    ->ignore($warehouse->id),
            ],
            'warehouse_name' => [
                'required',
                'string',
                'max:100',
            ],
            'description' => [
                'nullable',
                'string',
            ],
        ]);

        $warehouse->update($validated);

        return redirect()
            ->route('warehouses.index')
            ->with('success', 'Data gudang berhasil diperbarui.');
    }

    // Mengaktifkan atau menonaktifkan gudang
    public function toggleStatus(Warehouse $warehouse)
    {
        $warehouse->update([
            'is_active' => !$warehouse->is_active,
        ]);

        return redirect()
            ->route('warehouses.index')
            ->with(
                'success',
                'Status gudang berhasil diperbarui.'
            );
    }

    // Menghapus gudang yang belum memiliki area
    public function destroy(Warehouse $warehouse)
    {
        if ($warehouse->areas()->exists()) {
            return redirect()
                ->route('warehouses.index')
                ->with('error', 'Gudang tidak dapat dihapus karena masih memiliki area.');
        }

        $warehouse->delete();

        return redirect()
            ->route('warehouses.index')
            ->with('success', 'Data gudang berhasil dihapus.');
    }

}
