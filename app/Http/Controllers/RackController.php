<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\Rack;
use App\Models\RackLevel;
use App\Models\StorageTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class RackController extends Controller
{
    // Menampilkan daftar rak
    public function index(Request $request)
    {
        $areas = Area::with('warehouse')
            ->where('is_active', true)
            ->orderBy('area_code')
            ->get();

        $query = Rack::with('area.warehouse')
            ->withCount('rackLevels');

        // Filter berdasarkan area
        if ($request->filled('area_id')) {
            $query->where('area_id', $request->area_id);
        }

        $racks = $query
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('racks.index', compact(
            'racks',
            'areas'
        ));
    }

    // Menampilkan form tambah rak
    public function create()
    {
        $areas = Area::with('warehouse')
            ->where('is_active', true)
            ->orderBy('area_code')
            ->get();

        return view('racks.create', compact('areas'));
    }

    // Menyimpan rak beserta level otomatis
    public function store(Request $request)
    {
        $validated = $request->validate([
            'area_id' => [
                'required',
                'exists:areas,id',
            ],

            'rack_code' => [
                'required',
                'string',
                'max:20',
                Rule::unique('racks', 'rack_code')
                    ->where('area_id', $request->area_id),
            ],

            'level_count' => [
                'required',
                'integer',
                Rule::in([3, 4]),
            ],
        ]);

        $area = Area::findOrFail($validated['area_id']);

        if (!$area->is_active) {
            return back()
                ->withInput()
                ->withErrors([
                    'area_id' => 'Area yang dipilih sedang nonaktif.',
                ]);
        }

        DB::transaction(function () use ($validated) {

            $rack = Rack::create([
                'area_id' => $validated['area_id'],
                'rack_code' => $validated['rack_code'],
                'level_count' => $validated['level_count'],
                'is_active' => true,
            ]);

            // Membuat level secara otomatis
            for ($level = 1; $level <= $validated['level_count']; $level++) {

                RackLevel::create([
                    'rack_id' => $rack->id,
                    'level_number' => $level,
                    'capacity_pallets' => 3,
                ]);

            }
        });

        return redirect()
            ->route('racks.index')
            ->with('success', 'Data rak dan level berhasil ditambahkan.');
    }

    // Menampilkan form edit rak
    public function edit(Rack $rack)
    {
        $rack->load('area.warehouse');

        return view('racks.edit', compact('rack'));
    }

    // Memperbarui kode rak
    public function update(Request $request, Rack $rack)
    {
        $validated = $request->validate([
            'rack_code' => [
                'required',
                'string',
                'max:20',
                Rule::unique('racks', 'rack_code')
                    ->where('area_id', $rack->area_id)
                    ->ignore($rack->id),
            ],
        ]);

        $rack->update([
            'rack_code' => $validated['rack_code'],
        ]);

        return redirect()
            ->route('racks.index')
            ->with('success', 'Data rak berhasil diperbarui.');
    }

    // Mengaktifkan atau menonaktifkan rak
    public function toggleStatus(Rack $rack)
    {
        $rack->update([
            'is_active' => !$rack->is_active,
        ]);

        return redirect()
            ->route('racks.index')
            ->with('success', 'Status rak berhasil diperbarui.');
    }

    // Menghapus rak yang belum memiliki aktivitas penyimpanan
    public function destroy(Rack $rack)
    {
        $levelIds = $rack->rackLevels()->pluck('id');

        $hasPlacement = DB::table('storage_placements')
            ->whereIn('rack_level_id', $levelIds)
            ->exists();

        $hasTransaction = StorageTransaction::query()
            ->where(function ($query) use ($levelIds) {
                $query->whereIn('from_rack_level_id', $levelIds)
                    ->orWhereIn('to_rack_level_id', $levelIds);
            })
            ->exists();

        if ($hasPlacement || $hasTransaction) {
            return redirect()
                ->route('racks.index')
                ->with('error', 'Rak tidak dapat dihapus karena sudah memiliki data penyimpanan atau transaksi.');
        }

        DB::transaction(function () use ($rack) {

            $rack->rackLevels()->delete();

            $rack->delete();

        });

        return redirect()
            ->route('racks.index')
            ->with('success', 'Data rak dan level berhasil dihapus.');
    }
    public function levels(Rack $rack)
    {
        $rack->load([
        'area.warehouse',
        'rackLevels.storagePlacement.product',
    ]);

        return view('racks.levels', compact('rack'));
    }
}
