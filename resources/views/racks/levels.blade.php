@extends('layouts.app')

@section('title', 'Detail Level Rak')
@section('page-heading', 'Detail Level Rak')

@section('content')
@php
    $levels = $rack->rackLevels->sortBy('level_number');
    $totalCap = $levels->sum('capacity_pallets');
    $totalOcc = $levels->sum(fn($l) => $l->storagePlacement->pallet_quantity ?? 0);
    $utilPct = $totalCap > 0 ? round(($totalOcc / $totalCap) * 100, 1) : 0;
@endphp

<div class="wms-page-header">
    <div>
        <h1 class="wms-page-title">Detail Tingkatan Level — {{ $rack->rack_code }}</h1>
        <p class="wms-page-subtitle">
            {{ $rack->area->warehouse->warehouse_code ?? '' }} ({{ $rack->area->warehouse->warehouse_name ?? '' }}) &bull;
            Area {{ $rack->area->area_code ?? '' }} ({{ $rack->area->area_name ?? '' }})
        </p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('racks.edit', $rack) }}" class="btn btn-indo-primary">
            <i class="bi bi-pencil-square"></i> Edit Konfigurasi Rak
        </a>
        <a href="{{ route('racks.index') }}" class="btn btn-indo-outline">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>
</div>

{{-- RINGKASAN KAPASITAS RAK --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-xl-3">
        <div class="wms-kpi-card" style="--kpi-accent: var(--indo-navy); --kpi-tint: #E8EEF5;">
            <div class="wms-kpi-top">
                <span class="wms-kpi-label">Jumlah Tingkat Level</span>
                <span class="wms-kpi-icon"><i class="bi bi-layers"></i></span>
            </div>
            <div class="wms-kpi-value">{{ number_format($levels->count()) }} <span class="fs-6 fw-bold text-muted">Level</span></div>
            <div class="wms-kpi-note">Susunan vertikal unit rak</div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="wms-kpi-card" style="--kpi-accent: var(--indo-blue); --kpi-tint: var(--indo-blue-tint);">
            <div class="wms-kpi-top">
                <span class="wms-kpi-label">Total Kapasitas</span>
                <span class="wms-kpi-icon"><i class="bi bi-grid-3x3"></i></span>
            </div>
            <div class="wms-kpi-value" style="color: var(--indo-blue);">{{ number_format($totalCap) }} <span class="fs-6 fw-bold text-muted">Plt</span></div>
            <div class="wms-kpi-note">Kapasitas maksimal seluruh level</div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="wms-kpi-card" style="--kpi-accent: var(--indo-green); --kpi-tint: var(--indo-green-tint);">
            <div class="wms-kpi-top">
                <span class="wms-kpi-label">Pallet Terisi</span>
                <span class="wms-kpi-icon"><i class="bi bi-box-seam"></i></span>
            </div>
            <div class="wms-kpi-value" style="color: var(--indo-green);">{{ number_format($totalOcc) }} <span class="fs-6 fw-bold text-muted">Plt</span></div>
            <div class="wms-kpi-note">Sisa ruang: {{ number_format(max(0, $totalCap - $totalOcc)) }} Pallet</div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="wms-kpi-card" style="--kpi-accent: var(--indo-gold); --kpi-tint: var(--indo-gold-tint);">
            <div class="wms-kpi-top">
                <span class="wms-kpi-label">Utilisasi Rak</span>
                <span class="wms-kpi-icon"><i class="bi bi-pie-chart"></i></span>
            </div>
            <div class="wms-kpi-value">{{ $utilPct }}%</div>
            <div class="wms-kpi-note">Tingkat keterisian unit rak</div>
        </div>
    </div>
</div>

{{-- TABEL DAFTAR LEVEL RAK --}}
<div class="wms-panel">
    <div class="wms-panel-header">
        <div>
            <h2 class="wms-panel-title"><i class="bi bi-table"></i> Matriks Slot & Okupansi Level Rak</h2>
            <p class="wms-panel-subtitle">Rincian produk yang tersimpan, jumlah pallet terisi, dan sisa kapasitas tiap level.</p>
        </div>
        <span class="wms-code-badge">{{ $rack->rack_code }}</span>
    </div>
    <div class="table-responsive">
        <table class="wms-table">
            <thead>
                <tr>
                    <th>Tingkat Level</th>
                    <th>Produk Tersimpan (SKU)</th>
                    <th class="text-end">Terisi</th>
                    <th class="text-end">Kapasitas Maks</th>
                    <th class="text-end">Sisa Slot</th>
                    <th>Status Slot</th>
                </tr>
            </thead>
            <tbody>
                @forelse($levels as $level)
                    @php
                        $occ = $level->storagePlacement->pallet_quantity ?? 0;
                        $cap = $level->capacity_pallets ?? 0;
                        $rem = max(0, $cap - $occ);
                        $sku = $level->storagePlacement?->product;
                    @endphp
                    <tr>
                        <td><span class="wms-pill wms-pill-blue tabular-nums">Level {{ $level->level_number }}</span></td>
                        <td>
                            @if($sku && $occ > 0)
                                <div class="fw-bold">{{ $sku->product_name }}</div>
                                <span class="wms-code-badge">{{ $sku->product_code }}</span>
                            @else
                                <span class="text-muted small">Slot Kosong (Tersedia)</span>
                            @endif
                        </td>
                        <td class="text-end tabular-nums fw-bold">{{ number_format($occ) }} Plt</td>
                        <td class="text-end tabular-nums">{{ number_format($cap) }} Plt</td>
                        <td class="text-end tabular-nums fw-bold" style="color: {{ $rem > 0 ? 'var(--indo-green)' : 'var(--indo-red)' }};">
                            {{ number_format($rem) }} Plt
                        </td>
                        <td>
                            @if($occ <= 0)
                                <span class="wms-pill wms-pill-slate">Kosong</span>
                            @elseif($occ >= $cap)
                                <span class="wms-pill wms-pill-red">Penuh</span>
                            @else
                                <span class="wms-pill wms-pill-green">Terisi Sebagian</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">Belum ada level terkonfigurasi pada rak ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection