@extends('layouts.app')

@section('title', 'Data Rak')
@section('page-heading', 'Data Rak')

@section('content')
@php
    $activeCount = $racks->getCollection()->where('is_active', true)->count();
    $totalLevelsPage = $racks->getCollection()->sum(fn($r) => $r->rack_levels_count ?? $r->level_count ?? 0);
    $totalCapPage = $totalLevelsPage * 3;
@endphp

{{-- PAGE HEADER --}}
<div class="wms-page-header">
    <div>
        <h1 class="wms-page-title">Master Data Rak Penyimpanan</h1>
        <p class="wms-page-subtitle">Pengelolaan struktur rak, jumlah level, dan kapasitas pallet per unit rak.</p>
    </div>
    <a href="{{ route('racks.create') }}" class="btn btn-indo-primary">
        <i class="bi bi-plus-lg"></i> Tambah Rak
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert" style="border-radius: 8px; font-size: 13px;">
        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-close="alert" aria-label="Tutup"></button>
    </div>
@endif

@if(session('error') || $errors->any())
    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert" style="border-radius: 8px; font-size: 13px;">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') ?? $errors->first() }}
        <button type="button" class="btn-close" data-bs-close="alert" aria-label="Tutup"></button>
    </div>
@endif

{{-- RINGKASAN DATA RAK --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-xl-3">
        <div class="wms-kpi-card" style="--kpi-accent: var(--indo-navy); --kpi-tint: #E8EEF5;">
            <div class="wms-kpi-top">
                <span class="wms-kpi-label">Total Unit Rak</span>
                <span class="wms-kpi-icon"><i class="bi bi-bookshelf"></i></span>
            </div>
            <div class="wms-kpi-value">{{ number_format($racks->total()) }}</div>
            <div class="wms-kpi-note">Unit rak terdaftar</div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="wms-kpi-card" style="--kpi-accent: var(--indo-green); --kpi-tint: var(--indo-green-tint);">
            <div class="wms-kpi-top">
                <span class="wms-kpi-label">Rak Aktif (Halaman)</span>
                <span class="wms-kpi-icon"><i class="bi bi-check-circle"></i></span>
            </div>
            <div class="wms-kpi-value" style="color: var(--indo-green);">{{ number_format($activeCount) }}</div>
            <div class="wms-kpi-note">Siap menerima penempatan</div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="wms-kpi-card" style="--kpi-accent: var(--indo-blue); --kpi-tint: var(--indo-blue-tint);">
            <div class="wms-kpi-top">
                <span class="wms-kpi-label">Total Level (Halaman)</span>
                <span class="wms-kpi-icon"><i class="bi bi-layers"></i></span>
            </div>
            <div class="wms-kpi-value" style="color: var(--indo-blue);">{{ number_format($totalLevelsPage) }}</div>
            <div class="wms-kpi-note">Tingkat rak terkonfigurasi</div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="wms-kpi-card" style="--kpi-accent: var(--indo-gold); --kpi-tint: var(--indo-gold-tint);">
            <div class="wms-kpi-top">
                <span class="wms-kpi-label">Kapasitas (Halaman)</span>
                <span class="wms-kpi-icon"><i class="bi bi-grid-3x3"></i></span>
            </div>
            <div class="wms-kpi-value">{{ number_format($totalCapPage) }} <span class="fs-6 fw-bold text-muted">Plt</span></div>
            <div class="wms-kpi-note">Total slot pallet halaman ini</div>
        </div>
    </div>
</div>

{{-- FILTER PENCARIAN --}}
<div class="wms-panel mb-4">
    <div class="wms-panel-header">
        <h2 class="wms-panel-title"><i class="bi bi-funnel"></i> Filter Area Penyimpanan</h2>
    </div>
    <div class="wms-panel-body">
        <form action="{{ route('racks.index') }}" method="GET">
            <div class="row g-3 align-items-end">
                <div class="col-lg-9">
                    <label for="area-filter" class="wms-form-label">Area Penyimpanan</label>
                    <select id="area-filter" name="area_id" class="form-select wms-control">
                        <option value="">Semua Area Penyimpanan</option>
                        @foreach($areas as $area)
                            <option value="{{ $area->id }}" {{ (string) request('area_id') === (string) $area->id ? 'selected' : '' }}>
                                {{ $area->warehouse->warehouse_code ?? '' }} | {{ $area->area_code }} — {{ $area->area_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-3 d-flex gap-2">
                    <button type="submit" class="btn btn-indo-primary flex-fill justify-content-center">
                        <i class="bi bi-search"></i> Tampilkan
                    </button>
                    <a href="{{ route('racks.index') }}" class="btn btn-indo-outline">Reset</a>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- TABEL DATA RAK --}}
<div class="wms-panel">
    <div class="wms-panel-header">
        <div>
            <h2 class="wms-panel-title"><i class="bi bi-table"></i> Daftar Unit Rak</h2>
            <p class="wms-panel-subtitle">Konfigurasi rak, jumlah tingkatan level, dan kapasitas maksimal pallet.</p>
        </div>
        <span class="wms-pill wms-pill-slate tabular-nums">{{ number_format($racks->total()) }} data</span>
    </div>

    <div class="table-responsive">
        <table class="wms-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kode Rak</th>
                    <th>Gudang & Area</th>
                    <th class="text-center">Jumlah Level</th>
                    <th class="text-end">Total Kapasitas</th>
                    <th>Status</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($racks as $rack)
                    @php
                        $levelCount = $rack->rack_levels_count ?? $rack->level_count ?? 0;
                        $rackCapacity = $levelCount * 3;
                    @endphp
                    <tr>
                        <td class="text-muted tabular-nums">{{ $racks->firstItem() + $loop->index }}</td>
                        <td><span class="wms-code-badge">{{ $rack->rack_code }}</span></td>
                        <td>
                            <div class="fw-bold">{{ $rack->area->area_name ?? '-' }}</div>
                            <div class="text-muted small">{{ $rack->area->warehouse->warehouse_code ?? '-' }} — {{ $rack->area->warehouse->warehouse_name ?? '-' }}</div>
                        </td>
                        <td class="text-center">
                            <span class="wms-pill wms-pill-blue tabular-nums">{{ $levelCount }} Level</span>
                        </td>
                        <td class="text-end tabular-nums fw-bold">{{ number_format($rackCapacity) }} Pallet</td>
                        <td>
                            @if($rack->is_active)
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
                                <a href="{{ route('racks.levels', $rack) }}" class="btn btn-sm btn-outline-secondary wms-action-btn" title="Lihat Detail Level">
                                    <i class="bi bi-layers"></i>
                                </a>
                                <a href="{{ route('racks.edit', $rack) }}" class="btn btn-sm btn-outline-primary wms-action-btn" title="Edit Kode Rak">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <form action="{{ route('racks.toggle-status', $rack) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-sm btn-outline-warning wms-action-btn" title="{{ $rack->is_active ? 'Nonaktifkan Rak' : 'Aktifkan Rak' }}">
                                        <i class="bi bi-power"></i>
                                    </button>
                                </form>
                                <form action="{{ route('racks.destroy', $rack) }}" method="POST" onsubmit="return confirm('Hapus unit rak {{ $rack->rack_code }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger wms-action-btn" title="Hapus Rak">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="bi bi-bookshelf fs-2 d-block mb-2"></i>
                            <div class="fw-bold text-dark">Belum ada data unit rak</div>
                            <div class="small">Klik tombol <strong>Tambah Rak</strong> untuk mengonfigurasi rak baru.</div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($racks->hasPages())
        <div class="wms-pagination-bar">
            <span>Menampilkan {{ $racks->firstItem() }}–{{ $racks->lastItem() }} dari {{ $racks->total() }} rak</span>
            {{ $racks->withQueryString()->links() }}
        </div>
    @endif
</div>
@endsection