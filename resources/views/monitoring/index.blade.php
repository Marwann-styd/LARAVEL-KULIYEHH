@extends('layouts.app')

@section('title', 'Monitoring Kapasitas Gudang')
@section('page-heading', 'Monitoring Kapasitas Gudang')

@section('content')
    @php
        $globalBarColor =
            $usagePercentage >= 90
                ? 'var(--indo-red)'
                : ($usagePercentage >= 70
                    ? 'var(--indo-gold)'
                    : 'var(--indo-blue)');
    @endphp

    {{-- PAGE HEADER --}}
    <div class="wms-page-header">
        <div>
            <h1 class="wms-page-title">Monitoring Kapasitas Gudang</h1>
            <p class="wms-page-subtitle">Pemantauan okupansi pallet per gudang, area penyimpanan, dan level rak aktif.</p>
        </div>
        <span class="wms-pill wms-pill-blue">
            <i class="bi bi-activity"></i> Okupansi Global: {{ number_format($usagePercentage, 1) }}%
        </span>
    </div>

    {{-- RINGKASAN KAPASITAS --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-xl-3">
            <div class="wms-kpi-card" style="--kpi-accent: var(--indo-navy); --kpi-tint: #E8EEF5;">
                <div class="wms-kpi-top">
                    <span class="wms-kpi-label">Total Kapasitas</span>
                    <span class="wms-kpi-icon"><i class="bi bi-grid-3x3"></i></span>
                </div>
                <div class="wms-kpi-value">{{ number_format($totalCapacity) }}</div>
                <div class="wms-kpi-note">Kapasitas aktif (Pallet)</div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="wms-kpi-card" style="--kpi-accent: var(--indo-blue); --kpi-tint: var(--indo-blue-tint);">
                <div class="wms-kpi-top">
                    <span class="wms-kpi-label">Pallet Terisi</span>
                    <span class="wms-kpi-icon"><i class="bi bi-box-seam-fill"></i></span>
                </div>
                <div class="wms-kpi-value" style="color: var(--indo-blue);">{{ number_format($occupiedPallets) }}</div>
                <div class="wms-kpi-note">Pallet ditempatkan pada rak</div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="wms-kpi-card" style="--kpi-accent: var(--indo-green); --kpi-tint: var(--indo-green-tint);">
                <div class="wms-kpi-top">
                    <span class="wms-kpi-label">Sisa Kapasitas</span>
                    <span class="wms-kpi-icon"><i class="bi bi-check2-circle"></i></span>
                </div>
                <div class="wms-kpi-value" style="color: var(--indo-green);">{{ number_format($remainingCapacity) }}</div>
                <div class="wms-kpi-note">Slot kosong tersedia</div>
            </div>
        </div>
        <div class="col-6 col-xl-3">
            <div class="wms-kpi-card" style="--kpi-accent: {{ $globalBarColor }}; --kpi-tint: #F1F5F9;">
                <div class="wms-kpi-top">
                    <span class="wms-kpi-label">Tingkat Pemakaian</span>
                    <span class="wms-kpi-icon"><i class="bi bi-speedometer2"></i></span>
                </div>
                <div class="wms-kpi-value" style="color: {{ $globalBarColor }};">{{ number_format($usagePercentage, 1) }}%
                </div>
                <div class="wms-kpi-note">Rasio terisi terhadap kapasitas</div>
            </div>
        </div>
    </div>

    {{-- PROGRESS BAR KESELURUHAN --}}
    <div class="wms-panel mb-4">
        <div class="wms-panel-body">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="fw-bold" style="color: var(--indo-navy); font-size: 13.5px;">Indikator Okupansi
                    Keseluruhan</span>
                <span class="fw-bold tabular-nums"
                    style="color: {{ $globalBarColor }};">{{ number_format($usagePercentage, 2) }}%</span>
            </div>
            <div class="wms-progress" role="progressbar" aria-valuenow="{{ round($usagePercentage) }}" aria-valuemin="0"
                aria-valuemax="100">
                <div class="wms-progress-bar"
                    style="width: {{ min(100, $usagePercentage) }}%; background-color: {{ $globalBarColor }};"></div>
            </div>
            <div class="d-flex justify-content-between text-muted mt-2" style="font-size: 12px;">
                <span>Terisi: <strong>{{ number_format($occupiedPallets) }}</strong> pallet</span>
                <span>Tersedia: <strong>{{ number_format($remainingCapacity) }}</strong> pallet</span>
            </div>
        </div>
    </div>

    {{-- RINCIAN PER GUDANG --}}
    @forelse($warehouses as $warehouse)
        @php
            $warehouseCapacity = $warehouse->areas->sum(
                fn($area) => $area->racks->sum(fn($rack) => $rack->rackLevels->sum('capacity_pallets')),
            );
            $warehouseOccupied = $warehouse->areas->sum(
                fn($area) => $area->racks->sum(
                    fn($rack) => $rack->rackLevels->sum(
                        fn($level) => $level->storagePlacement ? $level->storagePlacement->pallet_quantity : 0,
                    ),
                ),
            );
            $warehouseRemaining = max(0, $warehouseCapacity - $warehouseOccupied);
            $warehousePercentage =
                $warehouseCapacity > 0 ? round(($warehouseOccupied / $warehouseCapacity) * 100, 2) : 0;
            $whBarColor =
                $warehousePercentage >= 90
                    ? 'var(--indo-red)'
                    : ($warehousePercentage >= 70
                        ? 'var(--indo-gold)'
                        : 'var(--indo-blue)');
        @endphp

        <section class="wms-panel mb-4">
            <div class="wms-panel-header">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span class="wms-code-badge">{{ $warehouse->warehouse_code }}</span>
                    <h2 class="wms-panel-title">{{ $warehouse->warehouse_name }}</h2>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="wms-pill wms-pill-slate tabular-nums">{{ number_format($warehouseOccupied) }} /
                        {{ number_format($warehouseCapacity) }} Pallet</span>
                    <span class="wms-pill wms-pill-green"><i class="bi bi-circle-fill" style="font-size: 6px;"></i>
                        Aktif</span>
                </div>
            </div>

            <div class="wms-panel-body">
                {{-- Bar Kapasitas Gudang --}}
                <div class="p-3 mb-4 rounded-3"
                    style="background: var(--bg-subtle); border: 1px solid var(--border-light);">
                    <div class="d-flex justify-content-between align-items-center mb-1" style="font-size: 12.5px;">
                        <span class="fw-bold text-secondary">Utilisasi Kapasitas {{ $warehouse->warehouse_code }}</span>
                        <span class="fw-bold tabular-nums"
                            style="color: {{ $whBarColor }};">{{ number_format($warehousePercentage, 2) }}% (Sisa
                            {{ number_format($warehouseRemaining) }} Pallet)</span>
                    </div>
                    <div class="wms-progress">
                        <div class="wms-progress-bar"
                            style="width: {{ min(100, $warehousePercentage) }}%; background-color: {{ $whBarColor }};">
                        </div>
                    </div>
                </div>

                {{-- Daftar Area & Tabel Rak --}}
                @forelse($warehouse->areas as $area)
                    <div class="mb-4 last:mb-0">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <h3 class="fw-bold mb-0" style="font-size: 13.5px; color: var(--indo-navy);">
                                <i class="bi bi-map text-primary me-1"></i>
                                Area {{ $area->area_code }} &mdash; {{ $area->area_name }}
                            </h3>
                            <span class="text-muted small">{{ $area->racks->count() }} Rak Aktif</span>
                        </div>

                        <div class="table-responsive border rounded-3"
                            style="border-color: var(--border-crisp) !important;">
                            <table class="wms-table">
                                <thead>
                                    <tr>
                                        <th>Kode Rak</th>
                                        <th>Jumlah Level</th>
                                        <th>Kapasitas</th>
                                        <th>Terisi</th>
                                        <th>Sisa Slot</th>
                                        <th style="min-width: 190px;">Tingkat Pemakaian</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($area->racks as $rack)
                                        @php
                                            $rackCapacity = $rack->rackLevels->sum('capacity_pallets');
                                            $rackOccupied = $rack->rackLevels->sum(
                                                fn($level) => $level->storagePlacement
                                                    ? $level->storagePlacement->pallet_quantity
                                                    : 0,
                                            );
                                            $rackRemaining = max(0, $rackCapacity - $rackOccupied);
                                            $rackPercentage =
                                                $rackCapacity > 0 ? round(($rackOccupied / $rackCapacity) * 100, 1) : 0;
                                            $rackBar =
                                                $rackPercentage >= 90
                                                    ? 'var(--indo-red)'
                                                    : ($rackPercentage >= 70
                                                        ? 'var(--indo-gold)'
                                                        : 'var(--indo-blue)');
                                        @endphp
                                        <tr>
                                            <td><span class="wms-code-badge">{{ $rack->rack_code }}</span></td>
                                            <td class="tabular-nums">{{ $rack->rackLevels->count() }} Level</td>
                                            <td class="tabular-nums fw-semibold">{{ number_format($rackCapacity) }} Pallet
                                            </td>
                                            <td class="tabular-nums fw-bold" style="color: var(--indo-blue);">
                                                {{ number_format($rackOccupied) }} Pallet</td>
                                            <td class="tabular-nums">{{ number_format($rackRemaining) }} Pallet</td>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="wms-progress flex-grow-1" style="height: 7px;">
                                                        <div class="wms-progress-bar"
                                                            style="width: {{ min(100, $rackPercentage) }}%; background-color: {{ $rackBar }};">
                                                        </div>
                                                    </div>
                                                    <span class="fw-bold tabular-nums"
                                                        style="font-size: 12px; color: {{ $rackBar }}; min-width: 44px; text-align: right;">
                                                        {{ number_format($rackPercentage, 1) }}%
                                                    </span>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-muted py-4">
                                                Belum ada unit rak aktif pada area ini.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-muted py-4">Belum ada area penyimpanan aktif pada gudang ini.</div>
                @endforelse
            </div>
        </section>
    @empty
        <div class="wms-panel">
            <div class="wms-panel-body text-center py-5 text-muted">
                <i class="bi bi-building-exclamation fs-2 d-block mb-2"></i>
                Belum terdapat data gudang aktif untuk ditampilkan.
            </div>
        </div>
    @endforelse
@endsection
