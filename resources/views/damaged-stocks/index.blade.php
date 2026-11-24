@extends('layouts.app')
@section('title', 'Monitoring Barang Rusak')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-1">Monitoring Barang Rusak / Bad Stock</h4>
                <p class="text-muted small mb-0">Rekapitulasi dus mie instan rusak/bocor beserta bukti dokumentasi kamera
                    lapangan.</p>
            </div>
            @if (auth()->user()->role === 'petugas_fg')
                <a href="{{ route('damaged-stocks.create') }}" class="btn btn-danger btn-sm">
                    <i class="bi bi-camera-fill me-1"></i> Catat Barang Rusak
                </a>
            @endif
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- Filter & KPI Ringkas --}}
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <form method="GET" class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold mb-1">Unit Gudang</label>
                        <select name="warehouse_id" class="form-select">
                            @if (auth()->user()->role === 'super_admin')
                                <option value="">Semua Gudang Aktif</option>
                            @endif
                            @foreach ($warehouses as $wh)
                                <option value="{{ $wh->id }}"
                                    {{ request('warehouse_id') == $wh->id ? 'selected' : '' }}>
                                    {{ $wh->warehouse_code }} - {{ $wh->warehouse_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold mb-1">Cari SKU / Nama Produk</label>
                        <input type="text" name="search" value="{{ request('search') }}" class="form-control"
                            placeholder="Contoh: Indomie Goreng...">
                    </div>
                    <div class="col-md-4 d-flex align-items-center justify-content-between gap-2">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary px-3">
                                <i class="bi bi-search me-1"></i> Filter
                            </button>
                            <a href="{{ route('damaged-stocks.index') }}" class="btn btn-outline-secondary px-3">
                                Reset
                            </a>
                        </div>
                        <div class="text-end ps-2 border-start">
                            <span class="small text-muted d-block" style="font-size: 0.75rem;">Total Bad Stock</span>
                            <span class="fw-bold text-danger fs-5">{{ number_format($totalDamagedCartons) }} Dus</span>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Tabel Riwayat Barang Rusak --}}
        <div class="card shadow-sm border-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Waktu (WIB)</th>
                            <th>Gudang & Lokasi Rak</th>
                            <th>Produk (SKU)</th>
                            <th class="text-center">Jumlah Rusak</th>
                            <th>Kategori & Catatan</th>
                            <th class="text-center">Foto Bukti</th>
                            <th>Petugas</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($damagedStocks as $row)
                            <tr>
                                <td class="small">{{ $row->reported_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <span class="badge bg-secondary">{{ $row->warehouse->warehouse_code }}</span>
                                    <div class="small fw-semibold mt-1">
                                        {{ $row->rackLevel->rack->rack_code }} - Lv {{ $row->rackLevel->level_number }}
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-semibold">{{ $row->product->product_name }}</div>
                                    <small class="text-muted">{{ $row->product->product_code }}</small>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-danger fs-6">{{ $row->damaged_cartons }} Dus</span>
                                </td>
                                <td>
                                    <span class="fw-semibold text-dark d-block">{{ $row->damage_category }}</span>
                                    <small class="text-muted">{{ $row->notes ?? '-' }}</small>
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#photoModal{{ $row->id }}">
                                        <i class="bi bi-image"></i> Lihat Bukti
                                    </button>
                                </td>
                                <td class="small">{{ $row->reporter->name }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">Belum ada catatan barang rusak.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($damagedStocks->hasPages())
                <div class="card-footer bg-white">
                    {{ $damagedStocks->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- Modal Preview Foto (Di luar tabel agar z-index & tombol X selalu berfungsi normal) --}}
    @foreach ($damagedStocks as $row)
        <div class="modal fade" id="photoModal{{ $row->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h6 class="modal-title fw-bold">Bukti Kerusakan — {{ $row->product->product_name }}</h6>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body text-center">
                        <img src="{{ asset('storage/' . $row->photo_path) }}" alt="Foto Bukti"
                            class="img-fluid rounded mb-2">
                        <p class="small text-muted mb-0">
                            Dilaporkan oleh <strong>{{ $row->reporter->name }}</strong> pada
                            {{ $row->reported_at->format('d/m/Y H:i') }} WIB
                        </p>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
@endsection
