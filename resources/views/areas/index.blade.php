@extends('layouts.app')

@section('title', 'Data Area')
@section('page-heading', 'Data Area')

@section('content')
    @php
        $activeCount = $areas->getCollection()->where('is_active', true)->count();
        $inactiveCount = $areas->getCollection()->where('is_active', false)->count();
    @endphp

    {{-- PAGE HEADER --}}
    <div class="wms-page-header">
        <div>
            <h1 class="wms-page-title">Master Data Area Penyimpanan</h1>
            <p class="wms-page-subtitle">Pengelolaan zona atau blok area pada masing-masing unit gudang Finished Goods.</p>
        </div>
        <a href="{{ route('areas.create') }}" class="btn btn-indo-primary">
            <i class="bi bi-plus-lg"></i> Tambah Area
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert"
            style="border-radius: 8px; font-size: 13px;">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-close="alert" aria-label="Tutup"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert"
            style="border-radius: 8px; font-size: 13px;">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ $errors->first() }}
            <button type="button" class="btn-close" data-bs-close="alert" aria-label="Tutup"></button>
        </div>
    @endif

    {{-- RINGKASAN DATA AREA --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="wms-kpi-card" style="--kpi-accent: var(--indo-navy); --kpi-tint: #E8EEF5;">
                <div class="wms-kpi-top">
                    <span class="wms-kpi-label">Total Area Terdaftar</span>
                    <span class="wms-kpi-icon"><i class="bi bi-map"></i></span>
                </div>
                <div class="wms-kpi-value">{{ number_format($areas->total()) }}</div>
                <div class="wms-kpi-note">Zona penyimpanan lintas gudang</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="wms-kpi-card" style="--kpi-accent: var(--indo-green); --kpi-tint: var(--indo-green-tint);">
                <div class="wms-kpi-top">
                    <span class="wms-kpi-label">Area Aktif (Halaman)</span>
                    <span class="wms-kpi-icon"><i class="bi bi-check-circle"></i></span>
                </div>
                <div class="wms-kpi-value" style="color: var(--indo-green);">{{ number_format($activeCount) }}</div>
                <div class="wms-kpi-note">Siap digunakan untuk penempatan rak</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="wms-kpi-card" style="--kpi-accent: var(--indo-red); --kpi-tint: var(--indo-red-tint);">
                <div class="wms-kpi-top">
                    <span class="wms-kpi-label">Nonaktif (Halaman)</span>
                    <span class="wms-kpi-icon"><i class="bi bi-slash-circle"></i></span>
                </div>
                <div class="wms-kpi-value" style="color: var(--indo-red);">{{ number_format($inactiveCount) }}</div>
                <div class="wms-kpi-note">Zona penyimpanan tidak aktif</div>
            </div>
        </div>
    </div>

    {{-- FILTER PENCARIAN --}}
    <div class="wms-panel mb-4">
        <div class="wms-panel-header">
            <h2 class="wms-panel-title"><i class="bi bi-funnel"></i> Filter & Pencarian Area</h2>
        </div>
        <div class="wms-panel-body">
            <form action="{{ route('areas.index') }}" method="GET">
                <div class="row g-3 align-items-end">
                    <div class="col-lg-5">
                        <label for="area-search" class="wms-form-label">Kode Area atau Nama Area</label>
                        <input type="text" id="area-search" name="search" class="form-control wms-control"
                            placeholder="Contoh: AREA-A, Zona Mie Instan, atau Cold Storage..."
                            value="{{ request('search') }}">
                    </div>
                    <div class="col-lg-4">
                        <label for="warehouse-filter" class="wms-form-label">Unit Gudang</label>
                        <select id="warehouse-filter" name="warehouse_id" class="form-select wms-control">
                            <option value="">Semua Unit Gudang</option>
                            @foreach ($warehouses as $warehouse)
                                <option value="{{ $warehouse->id }}"
                                    {{ (string) request('warehouse_id') === (string) $warehouse->id ? 'selected' : '' }}>
                                    {{ $warehouse->warehouse_code }} &mdash; {{ $warehouse->warehouse_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-3 d-flex gap-2">
                        <button type="submit" class="btn btn-indo-primary flex-fill justify-content-center">
                            <i class="bi bi-search"></i> Tampilkan
                        </button>
                        <a href="{{ route('areas.index') }}" class="btn btn-indo-outline">Reset</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- TABEL DATA AREA --}}
    <div class="wms-panel">
        <div class="wms-panel-header">
            <div>
                <h2 class="wms-panel-title"><i class="bi bi-table"></i> Daftar Area Penyimpanan</h2>
                <p class="wms-panel-subtitle">Pemetaan kode area terhadap unit gudang induknya.</p>
            </div>
            <span class="wms-pill wms-pill-slate tabular-nums">{{ number_format($areas->total()) }} data</span>
        </div>

        <div class="table-responsive">
            <table class="wms-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Kode Area</th>
                        <th>Nama Area</th>
                        <th>Unit Gudang</th>
                        <th>Keterangan</th>
                        <th>Status</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($areas as $area)
                        <tr>
                            <td class="text-muted tabular-nums">{{ $areas->firstItem() + $loop->index }}</td>
                            <td><span class="wms-code-badge">{{ $area->area_code }}</span></td>
                            <td class="fw-bold">{{ $area->area_name }}</td>
                            <td>
                                @if ($area->warehouse)
                                    <span class="wms-pill wms-pill-blue">{{ $area->warehouse->warehouse_code }}</span>
                                    <span class="ms-1">{{ $area->warehouse->warehouse_name }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-muted small">{{ $area->description ?: '-' }}</td>
                            <td>
                                @if ($area->is_active)
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
                                    <a href="{{ route('areas.edit', $area) }}"
                                        class="btn btn-sm btn-outline-primary wms-action-btn" title="Edit Area">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <form action="{{ route('areas.destroy', $area) }}" method="POST"
                                        onsubmit="return confirm('Hapus data area {{ $area->area_name }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger wms-action-btn"
                                            title="Hapus Area">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="bi bi-map fs-2 d-block mb-2"></i>
                                <div class="fw-bold text-dark">Belum ada data area penyimpanan</div>
                                <div class="small">Klik tombol <strong>Tambah Area</strong> untuk membuat zona baru di
                                    gudang.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($areas->hasPages())
            <div class="wms-pagination-bar">
                <span>Menampilkan {{ $areas->firstItem() }}â€“{{ $areas->lastItem() }} dari {{ $areas->total() }}
                    area</span>
                {{ $areas->withQueryString()->links() }}
            </div>
        @endif
    </div>
@endsection
