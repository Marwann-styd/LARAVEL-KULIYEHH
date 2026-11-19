@extends('layouts.app')

@section('title', 'Laporan Stok Produk')
@section('page-heading', 'Laporan Stok Produk')

@section('content')
@php
    $totalRows = $placements->count();
    $totalPallets = $placements->sum('pallet_quantity');
    $totalCartons = $placements->sum(fn($p) => $p->pallet_quantity * ($p->product->cartons_per_pallet ?? 120));
@endphp

{{-- HEADER CETAK KHUSUS SAAT PRINT --}}
<div class="d-none d-print-block mb-4 pb-3 border-bottom border-dark">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <div class="fw-bold text-uppercase" style="font-size: 16px; color: #0E2340;">PT INDOFOOD — WAREHOUSE FINISHED GOODS</div>
            <div class="fw-bold" style="font-size: 14px;">LAPORAN POSISI STOK & LOKASI PENYIMPANAN PRODUK</div>
        </div>
        <div class="text-end small">
            <div>Tanggal Cetak: {{ now()->format('d/m/Y H:i') }} WIB</div>
            <div>Dicetak oleh: {{ auth()->user()->name }}</div>
        </div>
    </div>
</div>

{{-- PAGE HEADER --}}
<div class="wms-page-header no-print">
    <div>
        <h1 class="wms-page-title">Laporan Stok & Lokasi Produk</h1>
        <p class="wms-page-subtitle">Rekapitulasi stok produk jadi beserta koordinat rak dan konversi karton siap cetak.</p>
    </div>
    <button type="button" onclick="window.print()" class="btn btn-indo-primary">
        <i class="bi bi-printer"></i> Cetak Laporan
    </button>
</div>

{{-- RINGKASAN LAPORAN --}}
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="wms-kpi-card" style="--kpi-accent: var(--indo-navy); --kpi-tint: #E8EEF5;">
            <div class="wms-kpi-top">
                <span class="wms-kpi-label">Total Baris Lokasi</span>
                <span class="wms-kpi-icon"><i class="bi bi-geo-alt"></i></span>
            </div>
            <div class="wms-kpi-value">{{ number_format($totalRows) }}</div>
            <div class="wms-kpi-note">Titik level rak terisi sesuai filter</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="wms-kpi-card" style="--kpi-accent: var(--indo-blue); --kpi-tint: var(--indo-blue-tint);">
            <div class="wms-kpi-top">
                <span class="wms-kpi-label">Akumulasi Pallet</span>
                <span class="wms-kpi-icon"><i class="bi bi-box-seam-fill"></i></span>
            </div>
            <div class="wms-kpi-value" style="color: var(--indo-blue);">{{ number_format($totalPallets) }}</div>
            <div class="wms-kpi-note">Total unit pallet pada laporan</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="wms-kpi-card" style="--kpi-accent: var(--indo-green); --kpi-tint: var(--indo-green-tint);">
            <div class="wms-kpi-top">
                <span class="wms-kpi-label">Akumulasi Karton</span>
                <span class="wms-kpi-icon"><i class="bi bi-boxes"></i></span>
            </div>
            <div class="wms-kpi-value" style="color: var(--indo-green);">{{ number_format($totalCartons) }}</div>
            <div class="wms-kpi-note">Total estimasi karton tersimpan</div>
        </div>
    </div>
</div>

{{-- FILTER LAPORAN --}}
<div class="wms-panel mb-4 no-print">
    <div class="wms-panel-header">
        <h2 class="wms-panel-title"><i class="bi bi-funnel"></i> Parameter Laporan Stok</h2>
    </div>
    <div class="wms-panel-body">
        <form action="{{ route('reports.stock-location') }}" method="GET">
            <div class="row g-3 align-items-end">
                <div class="col-lg-4">
                    <label for="report-warehouse" class="wms-form-label">Unit Gudang</label>
                    <select id="report-warehouse" name="warehouse_id" class="form-select wms-control">
                        @if(auth()->user()->role === 'super_admin')
                            <option value="">Semua Gudang Aktif</option>
                        @endif
                        @foreach($warehouses as $warehouse)
                            <option value="{{ $warehouse->id }}" {{ (string) request('warehouse_id') === (string) $warehouse->id ? 'selected' : '' }}>
                                {{ $warehouse->warehouse_code }} — {{ $warehouse->warehouse_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-5">
                    <label for="report-search" class="wms-form-label">Filter SKU / Nama Produk / Kode Rak</label>
                    <input id="report-search" type="text" name="search" class="form-control wms-control"
                           placeholder="Masukkan kode SKU, nama produk, atau kode rak..."
                           value="{{ request('search') }}">
                </div>
                <div class="col-lg-3 d-flex gap-2">
                    <button type="submit" class="btn btn-indo-primary flex-fill justify-content-center">
                        <i class="bi bi-search"></i> Filter Data
                    </button>
                    <a href="{{ route('reports.stock-location') }}" class="btn btn-indo-outline">Reset</a>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- TABEL LAPORAN STOK --}}
<div class="wms-panel">
    <div class="wms-panel-header no-print">
        <div>
            <h2 class="wms-panel-title"><i class="bi bi-clipboard-data"></i> Rincian Stok Produk per Lokasi</h2>
            <p class="wms-panel-subtitle">Data aktual penempatan Finished Goods pada rak penyimpanan.</p>
        </div>
        <span class="wms-pill wms-pill-slate tabular-nums">{{ number_format($totalRows) }} baris</span>
    </div>

    <div class="table-responsive">
        <table class="wms-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kode SKU</th>
                    <th>Nama Produk</th>
                    <th>Kategori</th>
                    <th>Gudang</th>
                    <th>Area</th>
                    <th>Rak / Level</th>
                    <th class="text-end">Pallet</th>
                    <th class="text-end">Karton</th>
                </tr>
            </thead>
            <tbody>
                @forelse($placements as $placement)
                    @php
                        $level = $placement->rackLevel;
                        $rack = $level->rack;
                        $area = $rack->area;
                        $warehouse = $area->warehouse;
                        $cartonQty = $placement->pallet_quantity * ($placement->product->cartons_per_pallet ?? 120);
                    @endphp
                    <tr>
                        <td class="text-muted tabular-nums">{{ $loop->iteration }}</td>
                        <td><span class="wms-code-badge">{{ $placement->product->product_code }}</span></td>
                        <td class="fw-bold">{{ $placement->product->product_name }}</td>
                        <td>{{ $placement->product->category->category_name ?? '-' }}</td>
                        <td>{{ $warehouse->warehouse_name }}</td>
                        <td>{{ $area->area_name }}</td>
                        <td>
                            <span class="fw-semibold">{{ $rack->rack_code }}</span>
                            <span class="text-muted small">Lv.{{ $level->level_number }}</span>
                        </td>
                        <td class="text-end tabular-nums fw-bold" style="color: var(--indo-blue);">{{ number_format($placement->pallet_quantity) }}</td>
                        <td class="text-end tabular-nums fw-semibold">{{ number_format($cartonQty) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center py-5 text-muted">
                            <i class="bi bi-clipboard-x fs-2 d-block mb-2"></i>
                            <div class="fw-bold text-dark">Tidak ada data stok sesuai filter laporan</div>
                            <div class="small">Silakan sesuaikan parameter gudang atau kata kunci pencarian.</div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
            @if($totalRows > 0)
                <tfoot>
                    <tr style="background: #F4F7FA; border-top: 2px solid var(--border-crisp);">
                        <th colspan="7" class="text-end text-uppercase small fw-bold py-3">Total Keseluruhan</th>
                        <th class="text-end tabular-nums fw-bold py-3" style="color: var(--indo-blue);">{{ number_format($totalPallets) }} Pallet</th>
                        <th class="text-end tabular-nums fw-bold py-3">{{ number_format($totalCartons) }} Karton</th>
                    </tr>
                </tfoot>
            @endif
        </table>
    </div>
</div>
@endsection