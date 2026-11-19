<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\RackLevel;
use App\Models\StoragePlacement;
use App\Models\StorageTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class StorageTransactionController extends Controller
{
    // ==========================================
    // 1. DAFTAR RIWAYAT TRANSAKSI
    // ==========================================

    public function index()
    {
        $user = auth()->user();

        $query = StorageTransaction::with([
            'product',
            'fromRackLevel.rack.area.warehouse',
            'toRackLevel.rack.area.warehouse',
            'user',
        ]);

        // Super Admin dapat melihat semua transaksi.
        // Role lainnya hanya melihat transaksi gudangnya.
        if ($user->role !== 'super_admin') {

            if ($user->warehouse_id === null) {
                $query->whereRaw('1 = 0');
            } else {
                $warehouseId = $user->warehouse_id;

                $query->where(function ($q) use ($warehouseId) {

                    // Barang masuk: lokasi tujuan harus milik gudang pengguna.
                    $q->where(function ($in) use ($warehouseId) {
                        $in->where('transaction_type', 'IN')
                            ->whereHas(
                                'toRackLevel.rack.area',
                                function ($area) use ($warehouseId) {
                                    $area->where(
                                        'warehouse_id',
                                        $warehouseId
                                    );
                                }
                            );
                    })

                    // Barang keluar: lokasi asal harus milik gudang pengguna.
                    ->orWhere(function ($out) use ($warehouseId) {
                        $out->where('transaction_type', 'OUT')
                            ->whereHas(
                                'fromRackLevel.rack.area',
                                function ($area) use ($warehouseId) {
                                    $area->where(
                                        'warehouse_id',
                                        $warehouseId
                                    );
                                }
                            );
                    })

                    // Pindah lokasi: asal DAN tujuan harus
                    // berada di gudang pengguna yang sama.
                    ->orWhere(function ($move) use ($warehouseId) {
                        $move->where('transaction_type', 'MOVE')
                            ->whereHas(
                                'fromRackLevel.rack.area',
                                function ($area) use ($warehouseId) {
                                    $area->where(
                                        'warehouse_id',
                                        $warehouseId
                                    );
                                }
                            )
                            ->whereHas(
                                'toRackLevel.rack.area',
                                function ($area) use ($warehouseId) {
                                    $area->where(
                                        'warehouse_id',
                                        $warehouseId
                                    );
                                }
                            );
                    });
                });
            }
        }

        $transactions = $query
            ->latest('transaction_date')
            ->paginate(10);

        return view('transactions.index', compact('transactions'));
    }

    // ==========================================
    // 2. FORM TAMBAH TRANSAKSI
    // ==========================================

    public function create()
    {
        $user = auth()->user();

        // Master produk digunakan bersama oleh kedua gudang.
        $products = Product::where('is_active', true)
            ->orderBy('product_name')
            ->get();

        // Mengambil level rak yang aktif dan sesuai hak akses.
        $rackLevels = RackLevel::with(
            'rack.area.warehouse'
        )
            ->whereHas('rack', function ($rackQuery) use ($user) {

                $rackQuery->where('is_active', true)
                    ->whereHas('area', function ($areaQuery) use ($user) {

                        $areaQuery->where('is_active', true)
                            ->whereHas(
                                'warehouse',
                                function ($warehouseQuery) use ($user) {

                                    $warehouseQuery->where(
                                        'is_active',
                                        true
                                    );

                                    if ($user->role !== 'super_admin') {
                                        $warehouseQuery->where(
                                            'id',
                                            $user->warehouse_id ?? 0
                                        );
                                    }
                                }
                            );
                    });
            })
            ->orderBy('rack_id')
            ->orderBy('level_number')
            ->get();

        return view('transactions.create', compact(
            'products',
            'rackLevels'
        ));
    }

    // ==========================================
    // 3. MENYIMPAN TRANSAKSI
    // ==========================================

    public function store(Request $request)
    {
        $validated = $request->validate([
            'transaction_type' => [
                'required',
                Rule::in(['IN', 'OUT', 'MOVE']),
            ],

            'product_id' => [
                'required',
                'exists:products,id',
            ],

            'pallet_quantity' => [
                'required',
                'integer',
                'min:1',
            ],

            'from_rack_level_id' => [
                'nullable',
                'required_if:transaction_type,OUT,MOVE',
                'exists:rack_levels,id',
            ],

            'to_rack_level_id' => [
                'nullable',
                'required_if:transaction_type,IN,MOVE',
                'exists:rack_levels,id',
            ],

            'description' => [
                'nullable',
                'string',
                'max:500',
            ],
        ]);

        $user = auth()->user();

        try {

            DB::transaction(function () use ($validated, $user) {

                $type = $validated['transaction_type'];
                $quantity = (int) $validated['pallet_quantity'];

                // Kunci data produk.
                $product = Product::whereKey($validated['product_id'])
                    ->lockForUpdate()
                    ->firstOrFail();

                if (!$product->is_active) {
                    throw new \RuntimeException(
                        'Produk yang dipilih sedang nonaktif.'
                    );
                }

                $fromLevel = null;
                $toLevel = null;

                // Lokasi asal dan tujuan tidak boleh sama.
                if (
                    $type === 'MOVE' &&
                    $validated['from_rack_level_id'] ==
                    $validated['to_rack_level_id']
                ) {
                    throw new \RuntimeException(
                        'Lokasi asal dan tujuan tidak boleh sama.'
                    );
                }

                // Mengumpulkan ID level yang akan dikunci.
                $levelIds = [];

                if (in_array($type, ['OUT', 'MOVE'])) {
                    $levelIds[] = $validated['from_rack_level_id'];
                }

                if (in_array($type, ['IN', 'MOVE'])) {
                    $levelIds[] = $validated['to_rack_level_id'];
                }

                $lockedLevels = $this->getLockedLevels($levelIds);

                // Memeriksa lokasi asal.
                if (in_array($type, ['OUT', 'MOVE'])) {

                    $fromLevel = $lockedLevels->get(
                        (int) $validated['from_rack_level_id']
                    );

                    $this->ensureLevelAccessible($fromLevel, $user);
                }

                // Memeriksa lokasi tujuan.
                if (in_array($type, ['IN', 'MOVE'])) {

                    $toLevel = $lockedLevels->get(
                        (int) $validated['to_rack_level_id']
                    );

                    $this->ensureLevelAccessible($toLevel, $user);
                }

                // ==================================
                // TRANSAKSI IN
                // ==================================

                if ($type === 'IN') {

                    $placement = StoragePlacement::where(
                        'rack_level_id',
                        $toLevel->id
                    )->lockForUpdate()->first();

                    if (
                        $placement &&
                        $placement->product_id != $product->id
                    ) {
                        throw new \RuntimeException(
                            'Level tujuan sudah digunakan oleh produk lain.'
                        );
                    }

                    $occupied = $placement
                        ? $placement->pallet_quantity
                        : 0;

                    if (
                        $occupied + $quantity >
                        $toLevel->capacity_pallets
                    ) {
                        throw new \RuntimeException(
                            'Jumlah pallet melebihi kapasitas level rak.'
                        );
                    }

                    if ($placement) {

                        $placement->increment(
                            'pallet_quantity',
                            $quantity
                        );

                    } else {

                        StoragePlacement::create([
                            'rack_level_id' => $toLevel->id,
                            'product_id' => $product->id,
                            'pallet_quantity' => $quantity,
                        ]);
                    }
                }

                // ==================================
                // TRANSAKSI OUT
                // ==================================

                if ($type === 'OUT') {

                    $placement = StoragePlacement::where(
                        'rack_level_id',
                        $fromLevel->id
                    )->lockForUpdate()->first();

                    if (
                        !$placement ||
                        $placement->product_id != $product->id
                    ) {
                        throw new \RuntimeException(
                            'Produk tidak ditemukan pada level rak asal.'
                        );
                    }

                    if ($quantity > $placement->pallet_quantity) {
                        throw new \RuntimeException(
                            'Jumlah pallet keluar melebihi stok yang tersedia.'
                        );
                    }

                    if ($quantity == $placement->pallet_quantity) {

                        $placement->delete();

                    } else {

                        $placement->decrement(
                            'pallet_quantity',
                            $quantity
                        );
                    }
                }

                // ==================================
                // TRANSAKSI MOVE
                // ==================================

                if ($type === 'MOVE') {

                    $source = StoragePlacement::where(
                        'rack_level_id',
                        $fromLevel->id
                    )->lockForUpdate()->first();

                    if (
                        !$source ||
                        $source->product_id != $product->id
                    ) {
                        throw new \RuntimeException(
                            'Produk tidak ditemukan pada lokasi asal.'
                        );
                    }

                    if ($quantity > $source->pallet_quantity) {
                        throw new \RuntimeException(
                            'Jumlah pallet pindah melebihi stok di lokasi asal.'
                        );
                    }

                    $destination = StoragePlacement::where(
                        'rack_level_id',
                        $toLevel->id
                    )->lockForUpdate()->first();

                    if (
                        $destination &&
                        $destination->product_id != $product->id
                    ) {
                        throw new \RuntimeException(
                            'Lokasi tujuan sudah digunakan oleh produk lain.'
                        );
                    }

                    $destinationOccupied = $destination
                        ? $destination->pallet_quantity
                        : 0;

                    if (
                        $destinationOccupied + $quantity >
                        $toLevel->capacity_pallets
                    ) {
                        throw new \RuntimeException(
                            'Jumlah pallet melebihi kapasitas lokasi tujuan.'
                        );
                    }

                    // Mengurangi stok asal.
                    if ($quantity == $source->pallet_quantity) {

                        $source->delete();

                    } else {

                        $source->decrement(
                            'pallet_quantity',
                            $quantity
                        );
                    }

                    // Menambahkan stok tujuan.
                    if ($destination) {

                        $destination->increment(
                            'pallet_quantity',
                            $quantity
                        );

                    } else {

                        StoragePlacement::create([
                            'rack_level_id' => $toLevel->id,
                            'product_id' => $product->id,
                            'pallet_quantity' => $quantity,
                        ]);
                    }
                }

                // ==================================
                // MENYIMPAN RIWAYAT TRANSAKSI
                // ==================================

                StorageTransaction::create([
                    'transaction_code' => 'TRX-' .
                        strtoupper((string) Str::uuid()),

                    'transaction_type' => $type,
                    'product_id' => $product->id,
                    'from_rack_level_id' => $fromLevel?->id,
                    'to_rack_level_id' => $toLevel?->id,
                    'pallet_quantity' => $quantity,
                    'user_id' => $user->id,
                    'transaction_date' => now(),
                    'description' => $validated['description'] ?? null,
                ]);

            }, 3);

            return redirect()
                ->route('transactions.index')
                ->with(
                    'success',
                    'Transaksi penyimpanan berhasil disimpan.'
                );

        } catch (\RuntimeException $e) {

            return back()
                ->withInput()
                ->withErrors([
                    'transaction' => $e->getMessage(),
                ]);

        } catch (\Throwable $e) {

            report($e);

            return back()
                ->withInput()
                ->withErrors([
                    'transaction' =>
                        'Terjadi kesalahan saat menyimpan transaksi. Silakan periksa kembali data dan coba lagi.',
                ]);
        }
    }

    // ==========================================
    // 4. MENGUNCI LEVEL RAK
    // ==========================================

    private function getLockedLevels(array $ids)
    {
        $ids = array_values(array_unique(
            array_map('intval', $ids)
        ));

        sort($ids, SORT_NUMERIC);

        $levels = RackLevel::whereIn('id', $ids)
            ->orderBy('id')
            ->lockForUpdate()
            ->get()
            ->keyBy('id');

        if ($levels->count() !== count($ids)) {
            throw new \RuntimeException(
                'Data level rak tidak ditemukan.'
            );
        }

        $levels->load('rack.area.warehouse');

        return $levels;
    }

    // ==========================================
    // 5. MEMERIKSA HAK AKSES LOKASI
    // ==========================================

    private function ensureLevelAccessible(RackLevel $level, $user)
    {
        $this->ensureLevelActive($level);

        // Super Admin dapat mengakses semua gudang aktif.
        if ($user->role === 'super_admin') {
            return;
        }

        $warehouseId = $level->rack->area->warehouse->id;

        if (
            $user->warehouse_id === null ||
            (int) $user->warehouse_id !== (int) $warehouseId
        ) {
            throw new \RuntimeException(
                'Anda tidak memiliki akses ke lokasi gudang tersebut.'
            );
        }
    }

    // ==========================================
    // 6. MEMERIKSA STATUS LOKASI
    // ==========================================

    private function ensureLevelActive(RackLevel $level)
    {
        if (
            !$level->rack->is_active ||
            !$level->rack->area->is_active ||
            !$level->rack->area->warehouse->is_active
        ) {
            throw new \RuntimeException(
                'Lokasi rak, area, atau gudang sedang nonaktif.'
            );
        }
    }
}
