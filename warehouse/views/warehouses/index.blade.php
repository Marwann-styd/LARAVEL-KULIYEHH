@extends('layouts.app')

@section('title', 'Data Gudang')
@section('page-heading', 'Data Gudang')

@section('content')
@php
    $activeCount = $warehouses->getCollection()->where('is_active', true)->count();
    $inactiveCount = $warehouses->getCollection()->where('is_active', false)->count();
@endphp

{{-- PAGE HEADER --}}
<div class="wms-page-header">
    <div>
        <h1 class="wms-page-title">Master Data Gudang</h1>
        <p class="wms-page-subtitle">Pengelolaan unit gudang Finished Goods PT Indofood beserta status operasionalnya.</p>
    </div>
    <a href="{{ route('warehouses.create') }}" class="btn btn-indo-primary">
        <i class="bi bi-plus-lg"></i> Tambah Gudang
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert" style="border-radius: 8px; font-size: 13px;">
        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-close="alert" aria-label="Tutup"></button>
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert" style="border-radius: 8px; font-size: 13px;">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ $errors->first() }}
        <button type="button" class="btn-close" data-bs-close="alert" aria-label="Tutup"></button>
    </div>
@endif

{{-- RINGKASAN DATA GUDANG --}}
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="wms-kpi-card" style="--kpi-accent: var(--indo-navy); --kpi-tint: #E8EEF5;">
            <div class="wms-kpi-top">
                <span class="wms-kpi-label">Total Unit Gudang</span>
                <span class="wms-kpi-icon"><i class="bi bi-buildings"></i></span>
            </div>
            <div class="wms-kpi-value">{{ number_format($warehouses->total()) }}</div>
            <div class="wms-kpi-note">Seluruh gudang terdaftar di sistem</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="wms-kpi-card" style="--kpi-accent: var(--indo-green); --kpi-tint: var(--indo-green-tint);">
            <div class="wms-kpi-top">
                <span class="wms-kpi-label">Gudang Aktif (Halaman)</span>
                <span class="wms-kpi-icon"><i class="bi bi-check-circle"></i></span>
            </div>
            <div class="wms-kpi-value" style="color: var(--indo-green);">{{ number_format($activeCount) }}</div>
            <div class="wms-kpi-note">Beroperasi & menerima transaksi</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="wms-kpi-card" style="--kpi-accent: var(--indo-red); --kpi-tint: var(--indo-red-tint);">
            <div class="wms-kpi-top">
                <span class="wms-kpi-label">Nonaktif (Halaman)</span>
                <span class="wms-kpi-icon"><i class="bi bi-slash-circle"></i></span>
            </div>
            <div class="wms-kpi-value" style="color: var(--indo-red);">{{ number_format($inactiveCount) }}</div>
            <div class="wms-kpi-note">Sementara tidak digunakan</div>
        </div>
    </div>
</div>

{{-- FILTER PENCARIAN --}}
<div class="wms-panel mb-4">
    <div class="wms-panel-header">
        <h2 class="wms-panel-title"><i class="bi bi-funnel"></i> Pencarian Data Gudang</h2>
    </div>
    <div class="wms-panel-body">
        <form action="{{ route('warehouses.index') }}" method="GET">
            <div class="row g-3 align-items-end">
                <div class="col-lg-9">
                    <label for="warehouse-search" class="wms-form-label">Kode Gudang, Nama Gudang, atau Lokasi</label>
                    <input type="text" id="warehouse-search" name="search" class="form-control wms-control"
                           placeholder="Contoh: FG-01, Gudang Finished Goods, atau Plant Bandung..."
                           value="{{ request('search') }}">
                </div>
                <div class="col-lg-3 d-flex gap-2">
                    <button type="submit" class="btn btn-indo-primary flex-fill justify-content-center">
                        <i class="bi bi-search"></i> Cari
                    </button>
                    <a href="{{ route('warehouses.index') }}" class="btn btn-indo-outline">Reset</a>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- TABEL DATA GUDANG --}}
<div class="wms-panel">
    <div class="wms-panel-header">
        <div>
            <h2 class="wms-panel-title"><i class="bi bi-table"></i> Daftar Unit Gudang</h2>
            <p class="wms-panel-subtitle">Informasi kode, nama unit, lokasi pabrik, serta status aktif gudang.</p>
        </div>
        <span class="wms-pill wms-pill-slate tabular-nums">{{ number_format($warehouses->total()) }} data</span>
    </div>

    <div class="table-responsive">
        <table class="wms-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kode Gudang</th>
                    <th>Nama Gudang</th>
                    <th>Lokasi / Plant</th>
                    <th>Keterangan</th>
                    <th>Status</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($warehouses as $warehouse)
                    <tr>
                        <td class="text-muted tabular-nums">{{ $warehouses->firstItem() + $loop->index }}</td>
                        <td><span class="wms-code-badge">{{ $warehouse->warehouse_code }}</span></td>
                        <td class="fw-bold">{{ $warehouse->warehouse_name }}</td>
                        <td>{{ $warehouse->location ?: '-' }}</td>
                        <td class="text-muted small">{{ $warehouse->description ?: '-' }}</td>
                        <td>
                            @if($warehouse->is_active)
                                <span class="wms-pill wms-pill-green">
                                    <i class="bi bi-circle-fill" style="font-size: 6px;"></i> Aktif
                                </span>
                            @else
                                <span class="wms-pill wms-pill-red">
                                    <i class="bi bi-circle-fill" style="font-size: 6px;"></i> Nonaktif
                                </span>
                            @endif
                        </td>
                        <td class="text-end">
                            <div class="d-inline-flex gap-1">
                                <a href="{{ route('warehouses.edit', $warehouse) }}" class="btn btn-sm btn-outline-primary wms-action-btn" title="Edit Gudang">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <form action="{{ route('warehouses.destroy', $warehouse) }}" method="POST" onsubmit="return confirm('Hapus data gudang {{ $warehouse->warehouse_name }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger wms-action-btn" title="Hapus Gudang">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="bi bi-building-x fs-2 d-block mb-2"></i>
                            <div class="fw-bold text-dark">Belum ada data gudang</div>
                            <div class="small">Klik tombol <strong>Tambah Gudang</strong> untuk menambahkan unit gudang baru.</div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($warehouses->hasPages())
        <div class="wms-pagination-bar">
            <span>Menampilkan {{ $warehouses->firstItem() }}â€“{{ $warehouses->lastItem() }} dari {{ $warehouses->total() }} gudang</span>
            {{ $warehouses->withQueryString()->links() }}
        </div>
    @endif
</div>
@endsection
