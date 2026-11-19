@extends('layouts.app')

@section('title', 'Monitoring Lokasi Produk')
@section('page-heading', 'Lokasi Produk')

@section('content')
@php
    $pagePallet = $placements->sum('pallet_quantity');
    $pageKarton = $placements->sum(fn($p) => $p->pallet_quantity * ($p->product->cartons_per_pallet ?? 120));
@endphp

{{-- PAGE HEADER --}}
<div class="wms-page-header">
    <div>
        <h1 class="wms-page-title">Pemetaan Lokasi Produk</h1>
        <p class="wms-page-subtitle">Daftar posisi SKU Finished Goods yang tersimpan pada rak dan level gudang.</p>
    </div>
    <span class="wms-pill wms-pill-blue">
        <i class="bi bi-geo-alt-fill"></i> {{ number_format($placements->total()) }} Titik Lokasi
    </span>
</div>

{{-- RINGKASAN LOKASI --}}
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="wms-kpi-card" style="--kpi-accent: var(--indo-navy); --kpi-tint: #E8EEF5;">
            <div class="wms-kpi-top">
                <span class="wms-kpi-label">Total Lokasi Terisi</span>
                <span class="wms-kpi-icon"><i class="bi bi-geo-alt"></i></span>
            </div>
            <div class="wms-kpi-value">{{ number_format($placements->total()) }}</div>
            <div class="wms-kpi-note">Level rak terisi sesuai filter</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="wms-kpi-card" style="--kpi-accent: var(--indo-blue); --kpi-tint: var(--indo-blue-tint);">
            <div class="wms-kpi-top">
                <span class="wms-kpi-label">Pallet (Halaman Ini)</span>
                <span class="wms-kpi-icon"><i class="bi bi-box-seam"></i></span>
            </div>
            <div class="wms-kpi-value" style="color: var(--indo-blue);">{{ number_format($pagePallet) }}</div>
            <div class="wms-kpi-note">Total unit pallet pada tabel aktif</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="wms-kpi-card" style="--kpi-accent: var(--indo-green); --kpi-tint: var(--indo-green-tint);">
            <div class="wms-kpi-top">
                <span class="wms-kpi-label">Estimasi Karton (Halaman Ini)</span>
                <span class="wms-kpi-icon"><i class="bi bi-boxes"></i></span>
            </div>
            <div class="wms-kpi-value" style="color: var(--indo-green);">{{ number_format($pageKarton) }}</div>
            <div class="wms-kpi-note">Konversi berdasarkan kapasitas per pallet</div>
        </div>
    </div>
</div>

{{-- FILTER PENCARIAN --}}
<div class="wms-panel mb-4">
    <div class="wms-panel-header">
        <h2 class="wms-panel-title"><i class="bi bi-funnel"></i> Filter Lokasi Penyimpanan</h2>
    </div>
    <div class="wms-panel-body">
        <form action="{{ route('storage-locations.index') }}" method="GET">
            <div class="row g-3 align-items-end">
                <div class="col-lg-5">
                    <label for="location-search" class="wms-form-label">Cari Kode / Nama Produk atau Kode Rak</label>
                    <input id="location-search" type="text" name="search" class="form-control wms-control"
                           placeholder="Contoh: FG-IDM-01, Indomie Goreng, atau R-A01..."
                           value="{{ request('search') }}">
                </div>
                <div class="col-lg-4">
                    <label for="warehouse-filter" class="wms-form-label">Unit Gudang</label>
                    <select id="warehouse-filter" name="warehouse_id" class="form-select wms-control">
                        @if(auth()->user()->role === 'super_admin')
                            <option value="">Semua Gudang Aktif</option>
                        @endif
                        @foreach($warehouses as $warehouse)
                            <option value="{{ $warehouse->id }}"
                                {{ (string) request('warehouse_id') === (string) $warehouse->id ? 'selected' : '' }}>
                                {{ $warehouse->warehouse_code }} — {{ $warehouse->warehouse_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-3 d-flex gap-2">
                    <button type="submit" class="btn btn-indo-primary flex-fill justify-content-center">
                        <i class="bi bi-search"></i> Tampilkan
                    </button>
                    <a href="{{ route('storage-locations.index') }}" class="btn btn-indo-outline">
                        Reset
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- TABEL PENEMPATAN PRODUK --}}
<div class="wms-panel">
    <div class="wms-panel-header">
        <div>
            <h2 class="wms-panel-title"><i class="bi bi-table"></i> Daftar Penempatan SKU</h2>
            <p class="wms-panel-subtitle">Rincian stok produk jadi pada masing-masing koordinat gudang.</p>
        </div>
        <span class="wms-pill wms-pill-slate tabular-nums">{{ number_format($placements->total()) }} baris data</span>
    </div>

    <div class="table-responsive">
        <table class="wms-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kode SKU</th>
                    <th>Nama Produk</th>
                    <th>Gudang</th>
                    <th>Area</th>
                    <th>Rak</th>
                    <th>Level</th>
                    <th class="text-end">Jumlah Pallet</th>
                    <th class="text-end">Jumlah Karton</th>
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
                        <td class="text-muted tabular-nums">{{ $placements->firstItem() + $loop->index }}</td>
                        <td><span class="wms-code-badge">{{ $placement->product->product_code }}</span></td>
                        <td class="fw-bold">{{ $placement->product->product_name }}</td>
                        <td>{{ $warehouse->warehouse_name }}</td>
                        <td>{{ $area->area_name }}</td>
                        <td><span class="wms-pill wms-pill-blue">{{ $rack->rack_code }}</span></td>
                        <td><span class="wms-pill wms-pill-slate tabular-nums">Lv. {{ $level->level_number }}</span></td>
                        <td class="text-end tabular-nums fw-bold" style="color: var(--indo-blue);">{{ number_format($placement->pallet_quantity) }} Pallet</td>
                        <td class="text-end tabular-nums fw-semibold">{{ number_format($cartonQty) }} Karton</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                            <div class="fw-bold text-dark">Tidak ada data penempatan produk</div>
                            <div class="small">Ubah kata kunci pencarian atau pilih filter gudang lain.</div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($placements->hasPages())
        <div class="wms-pagination-bar">
            <span>Menampilkan {{ $placements->firstItem() }}–{{ $placements->lastItem() }} dari {{ $placements->total() }} lokasi</span>
            {{ $placements->withQueryString()->links() }}
        </div>
    @endif
</div>
@endsection