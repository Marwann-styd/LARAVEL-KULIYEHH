<?php

namespace App\Http\Controllers;

use App\Models\DamagedStock;
use App\Models\StoragePlacement;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DamagedStockController extends Controller
{
    private function getAllowedWarehouses(User $user)
    {
        $query = Warehouse::where('is_active', true);

        if ($user->role !== 'super_admin') {
            $query->where('id', $user->warehouse_id);
        }

        return $query->orderBy('warehouse_name')->get();
    }

    public function index(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();
        $warehouses = $this->getAllowedWarehouses($user);
        $allowedWarehouseIds = $warehouses->pluck('id')->toArray();

        $query = DamagedStock::with([
            'warehouse',
            'rackLevel.rack.area',
            'product',
            'reporter',
        ])->whereIn('warehouse_id', $allowedWarehouseIds);

        if ($request->filled('warehouse_id') && in_array((int) $request->warehouse_id, $allowedWarehouseIds)) {
            $query->where('warehouse_id', $request->warehouse_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('product', function ($q) use ($search) {
                $q->where('product_code', 'ilike', "%{$search}%")
                    ->orWhere('product_name', 'ilike', "%{$search}%");
            });
        }

        $damagedStocks = $query->orderByDesc('reported_at')->paginate(15)->withQueryString();
        $totalDamagedCartons = (clone $query)->sum('damaged_cartons');

        return view('damaged-stocks.index', compact(
            'damagedStocks',
            'warehouses',
            'totalDamagedCartons'
        ));
    }

    public function create()
    {
        /** @var User $user */
        $user = Auth::user();
        $warehouses = $this->getAllowedWarehouses($user);
        $allowedWarehouseIds = $warehouses->pluck('id')->toArray();

        $placements = StoragePlacement::with([
            'product',
            'rackLevel.rack.area.warehouse',
        ])
            ->where('pallet_quantity', '>', 0)
            ->whereHas('rackLevel.rack.area', function ($q) use ($allowedWarehouseIds) {
                $q->whereIn('warehouse_id', $allowedWarehouseIds);
            })
            ->get();

        return view('damaged-stocks.create', compact('placements'));
    }

    public function store(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        $request->validate([
            'storage_placement_id' => 'required|exists:storage_placements,id',
            'damaged_cartons'      => 'required|integer|min:1',
            'damage_category'      => 'required|string|max:80',
            'notes'                => 'nullable|string|max:500',
            'photo_file'           => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'camera_image_base64'  => 'nullable|string',
        ], [
            'storage_placement_id.required' => 'Pilih lokasi rak dan produk terlebih dahulu.',
            'damaged_cartons.min'           => 'Jumlah dus rusak minimal 1 dus/karton.',
        ]);

        if (!$request->hasFile('photo_file') && !$request->filled('camera_image_base64')) {
            return back()
                ->withErrors(['photo_file' => 'Foto bukti kerusakan wajib diambil menggunakan kamera atau diunggah!'])
                ->withInput();
        }

        try {
            DB::transaction(function () use ($request, $user) {
                $placement = StoragePlacement::with(['product', 'rackLevel.rack.area'])
                    ->where('id', $request->storage_placement_id)
                    ->lockForUpdate()
                    ->firstOrFail();

                $warehouseId = $placement->rackLevel->rack->area->warehouse_id;

                if ($user->role !== 'super_admin' && (int) $user->warehouse_id !== (int) $warehouseId) {
                    throw new \Exception('Anda tidak memiliki akses ke gudang tersebut.');
                }

                $availableCartons = ($placement->pallet_quantity * $placement->product->cartons_per_pallet) - $placement->damaged_cartons;

                if ($request->damaged_cartons > $availableCartons) {
                    throw new \Exception("Jumlah dus rusak ({$request->damaged_cartons} dus) melebihi sisa stok bersih di rak ({$availableCartons} dus).");
                }

                // Simpan foto bukti (prioritas dari kamera langsung jika ada, atau file upload)
                $photoPath = null;
                if ($request->filled('camera_image_base64')) {
                    $imageData = $request->camera_image_base64;
                    $imageData = preg_replace('/^data:image\/\w+;base64,/', '', $imageData);
                    $decodedImage = base64_decode($imageData);
                    $fileName = 'damage-proofs/' . now()->format('Ymd_His') . '_' . Str::random(8) . '.jpg';
                    Storage::disk('public')->put($fileName, $decodedImage);
                    $photoPath = $fileName;
                } elseif ($request->hasFile('photo_file')) {
                    $photoPath = $request->file('photo_file')->store('damage-proofs', 'public');
                }

                // Simpan catatan kerusakan
                DamagedStock::create([
                    'warehouse_id'    => $warehouseId,
                    'rack_level_id'   => $placement->rack_level_id,
                    'product_id'      => $placement->product_id,
                    'reported_by'     => $user->id,
                    'damaged_cartons' => $request->damaged_cartons,
                    'damage_category' => $request->damage_category,
                    'photo_path'      => $photoPath,
                    'notes'           => $request->notes,
                    'reported_at'     => now(),
                ]);

                // Potong stok bersih di rak (Opsi A)
                $newDamagedTotal = $placement->damaged_cartons + $request->damaged_cartons;
                $totalGrossCartons = $placement->pallet_quantity * $placement->product->cartons_per_pallet;

                if ($newDamagedTotal >= $totalGrossCartons) {
                    // Jika seluruh karton di pallet tersebut habis rusak, kosongkan slot rak
                    $placement->delete();
                } else {
                    $placement->update([
                        'damaged_cartons' => $newDamagedTotal,
                    ]);
                }
            });

            return redirect()
                ->route('damaged-stocks.index')
                ->with('success', 'Laporan barang rusak & foto bukti berhasil disimpan. Stok rak telah otomatis diperbarui.');
        } catch (\Exception $e) {
            return back()
                ->withErrors(['error' => $e->getMessage()])
                ->withInput();
        }
    }
}
