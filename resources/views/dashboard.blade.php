@extends('layouts.app')

@php
    $tabRoleMap = [
        'super_admin' => 'Super Admin',
        'admin_gudang' => 'Admin Gudang',
        'supervisor' => 'Supervisor',
        'petugas_fg' => 'Petugas FG',
    ];
    $tabRoleName = $tabRoleMap[Auth::user()->role ?? ''] ?? 'Operasional';
@endphp

@section('title', "Dashboard ({$tabRoleName})")
@section('page-heading', 'Dashboard')

@push('styles')
    <style>
        @keyframes sectionCascadeUp {
            from {
                opacity: 0;
                transform: translate3d(0, 18px, 0);
            }

            to {
                opacity: 1;
                transform: translate3d(0, 0, 0);
            }
        }

        @keyframes curtainSlideUp {
            0% {
                transform: translate3d(0, 0%, 0);
                opacity: 1;
            }

            100% {
                transform: translate3d(0, -100%, 0);
                opacity: 0;
            }
        }

        /* Tirai Full-Screen Versi ke-1: Tahan 0.55s biar 'Akses Terverifikasi' terbaca jelas, lalu angkat smooth 0.65s */
        .wms-entry-curtain {
            position: fixed;
            inset: 0;
            background: linear-gradient(135deg, #0E2340 0%, #00529B 100%);
            z-index: 9999;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: #FFFFFF;
            pointer-events: none;
            will-change: transform, opacity;
            animation: curtainSlideUp 0.65s cubic-bezier(0.77, 0, 0.175, 1) 0.55s forwards;
        }

        .dash-reveal {
            opacity: 0;
            will-change: transform, opacity;
            animation: sectionCascadeUp 0.55s cubic-bezier(0.22, 1, 0.36, 1) forwards;
        }

        .dash-delay-1 {
            animation-delay: 0.06s;
        }

        .dash-delay-2 {
            animation-delay: 0.14s;
        }

        .dash-delay-3 {
            animation-delay: 0.22s;
        }

        .dash-delay-4 {
            animation-delay: 0.30s;
        }

        .dash-delay-5 {
            animation-delay: 0.38s;
        }

        .dash-delay-6 {
            animation-delay: 0.46s;
        }

        /* Sinkronkan cascade dashboard tepat saat tirai mulai terangkat di detik 0.72s */
        html.wms-curtain-active .dash-delay-1 {
            animation-delay: 0.72s;
        }

        html.wms-curtain-active .dash-delay-2 {
            animation-delay: 0.80s;
        }

        html.wms-curtain-active .dash-delay-3 {
            animation-delay: 0.88s;
        }

        html.wms-curtain-active .dash-delay-4 {
            animation-delay: 0.96s;
        }

        html.wms-curtain-active .dash-delay-5 {
            animation-delay: 1.04s;
        }

        html.wms-curtain-active .dash-delay-6 {
            animation-delay: 1.12s;
        }

        .indo-section-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
            margin: 24px 0 12px;
        }

        .indo-section-title {
            margin: 0;
            color: var(--indo-navy);
            font-size: 14.5px;
            font-weight: 800;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .indo-section-title::before {
            content: "";
            width: 4px;
            height: 16px;
            border-radius: 2px;
            background: var(--indo-blue);
        }

        /* Quick Links */
        .indo-quick-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 13px 15px;
            background: #FFFFFF;
            border: 1px solid var(--border-crisp);
            border-radius: var(--radius-md);
            text-decoration: none;
            color: var(--text-primary);
            box-shadow: var(--shadow-card);
            transition: transform 0.24s cubic-bezier(0.22, 1, 0.36, 1),
                box-shadow 0.24s ease,
                border-color 0.22s ease,
                background-color 0.22s ease;
        }

        .indo-quick-link:hover {
            transform: translate3d(0, -3px, 0);
            border-color: var(--indo-blue);
            background: #F8FBFF;
            color: var(--indo-navy);
            box-shadow: 0 10px 24px rgba(14, 35, 64, 0.08);
        }

        .indo-quick-link:active {
            transform: scale(0.985);
        }

        .indo-quick-icon {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            background: var(--indo-blue-tint);
            border: 1px solid #C8DFF4;
            color: var(--indo-blue);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            flex-shrink: 0;
            transition: transform 0.24s cubic-bezier(0.22, 1, 0.36, 1);
        }

        .indo-quick-link:hover .indo-quick-icon {
            transform: scale(1.07);
        }

        .indo-quick-icon-danger {
            background: var(--indo-red-tint);
            border: 1px solid #F7C6C8;
            color: var(--indo-red);
        }

        .indo-quick-title {
            font-size: 13.5px;
            font-weight: 700;
            color: var(--indo-navy);
        }

        .indo-quick-link .bi-chevron-right {
            transition: transform 0.24s cubic-bezier(0.22, 1, 0.36, 1), color 0.2s ease;
        }

        .indo-quick-link:hover .bi-chevron-right {
            transform: translate3d(4px, 0, 0);
            color: var(--indo-blue) !important;
        }

        /* KPI & Command Cards */
        .wms-kpi-card,
        .wh-command-card,
        .chart-card {
            transition: transform 0.24s cubic-bezier(0.22, 1, 0.36, 1),
                box-shadow 0.24s ease,
                border-color 0.22s ease;
        }

        .wms-kpi-card:hover,
        .wh-command-card:hover,
        .chart-card:hover {
            transform: translate3d(0, -3px, 0);
            box-shadow: 0 10px 24px rgba(14, 35, 64, 0.08);
        }

        .chart-card {
            background: #FFFFFF;
            border: 1px solid var(--border-crisp);
            border-radius: 12px;
            padding: 18px 20px;
            box-shadow: var(--shadow-card);
            height: 100%;
        }

        .chart-canvas-wrap {
            position: relative;
            height: 235px;
            width: 100%;
        }

        /* Visual Denah Rak 2D Interaktif */
        .rack-legend {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 14px;
            font-size: 11.5px;
            font-weight: 600;
            color: #475569;
        }

        .rack-legend-item {
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .rack-legend-dot {
            width: 10px;
            height: 10px;
            border-radius: 3px;
            display: inline-block;
        }

        .rack-unit-card {
            background: #FFFFFF;
            border: 1px solid var(--border-crisp);
            border-top: 3px solid var(--indo-navy);
            border-radius: 12px;
            padding: 15px;
            box-shadow: var(--shadow-card);
            height: 100%;
            display: flex;
            flex-direction: column;
            transition: transform 0.24s cubic-bezier(0.22, 1, 0.36, 1), box-shadow 0.24s ease;
        }

        .rack-unit-card:hover {
            transform: translate3d(0, -3px, 0);
            box-shadow: 0 12px 26px rgba(14, 35, 64, 0.08);
        }

        .rack-unit-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 8px;
            padding-bottom: 10px;
            margin-bottom: 10px;
            border-bottom: 1px dashed #CBD5E1;
        }

        .rack-unit-code {
            font-size: 14.5px;
            font-weight: 800;
            color: var(--indo-navy);
            letter-spacing: -0.01em;
        }

        .rack-unit-meta {
            font-size: 11px;
            color: #64748B;
            font-weight: 600;
        }

        .rack-levels-stack {
            display: flex;
            flex-direction: column;
            gap: 7px;
            flex: 1;
            justify-content: flex-end;
        }

        .rack-slot-btn {
            width: 100%;
            text-align: left;
            border-radius: 8px;
            padding: 8px 11px;
            border: 1px solid #CBD5E1;
            background: #F8FAFC;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            cursor: pointer;
            transition: transform 0.2s cubic-bezier(0.22, 1, 0.36, 1),
                box-shadow 0.2s ease,
                border-color 0.2s ease,
                background-color 0.2s ease;
        }

        .rack-slot-btn:hover {
            transform: translate3d(4px, 0, 0);
            box-shadow: 0 5px 14px rgba(14, 35, 64, 0.08);
        }

        .rack-slot-btn:active {
            transform: scale(0.985);
        }

        .rack-slot-empty {
            background: #F8FAFC;
            border-color: #DCE5EF;
            color: #64748B;
        }

        .rack-slot-empty:hover {
            border-color: #94A3B8;
            background: #F1F5F9;
        }

        .rack-slot-partial {
            background: #EFF6FF;
            border-color: #93C5FD;
            border-left: 4px solid var(--indo-blue);
        }

        .rack-slot-partial:hover {
            background: #DBEAFE;
            border-color: var(--indo-blue);
        }

        .rack-slot-full {
            background: #E0F2FE;
            border-color: #38BDF8;
            border-left: 4px solid var(--indo-navy);
        }

        .rack-slot-full:hover {
            background: #BAE6FD;
            border-color: var(--indo-navy);
        }

        .rack-slot-damaged {
            border-right: 4px solid var(--indo-red) !important;
        }

        .rack-lv-tag {
            font-size: 11px;
            font-weight: 800;
            padding: 2px 7px;
            border-radius: 5px;
            background: rgba(14, 35, 64, 0.08);
            color: var(--indo-navy);
            flex-shrink: 0;
            font-variant-numeric: tabular-nums;
        }

        .rack-slot-info {
            min-width: 0;
            flex: 1;
        }

        .rack-slot-sku {
            font-size: 12px;
            font-weight: 700;
            color: var(--indo-navy);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .rack-slot-empty .rack-slot-sku {
            color: #94A3B8;
            font-weight: 600;
        }

        .rack-slot-cap {
            font-size: 11px;
            font-weight: 700;
            color: #334155;
            flex-shrink: 0;
            font-variant-numeric: tabular-nums;
            text-align: right;
        }

        .rack-base-foot {
            height: 6px;
            background: repeating-linear-gradient(-45deg,
                    #CBD5E1,
                    #CBD5E1 6px,
                    #E2E8F0 6px,
                    #E2E8F0 12px);
            border-radius: 4px;
            margin-top: 10px;
        }

        /* Command Card Gudang */
        .wh-command-card {
            background: #FFFFFF;
            border: 1px solid var(--border-crisp);
            border-top: 3px solid var(--indo-navy);
            border-radius: var(--radius-lg);
            padding: 18px 20px;
            box-shadow: var(--shadow-card);
            height: 100%;
        }

        .wh-metric-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 10px;
            margin: 14px 0;
        }

        .wh-metric-box {
            padding: 9px 11px;
            background: var(--bg-subtle);
            border: 1px solid var(--border-light);
            border-radius: 8px;
            transition: background-color 0.2s ease;
        }

        .wh-command-card:hover .wh-metric-box {
            background: #F4F8FC;
        }

        .wh-metric-label {
            display: block;
            font-size: 11px;
            font-weight: 700;
            color: var(--text-muted);
            margin-bottom: 2px;
        }

        .wh-metric-val {
            font-size: 17px;
            font-weight: 800;
            color: var(--indo-navy);
            font-variant-numeric: tabular-nums;
        }

        .wms-progress-bar {
            transition: width 1s cubic-bezier(0.22, 1, 0.36, 1);
        }

        @media (max-width: 575.98px) {
            .wh-metric-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }
    </style>
@endpush

@section('content')
    @php
        $currentUser = Auth::user();
        $capacity = (int) ($totalKapasitasPallet ?? 0);
        $occupied = (int) ($totalPalletTerisi ?? 0);
        $remaining = (int) ($sisaKapasitasPallet ?? 0);
        $capacityPercentage = $capacity > 0 ? min(100, max(0, ($occupied / $capacity) * 100)) : 0;

        $barColor =
            $capacityPercentage >= 90
                ? 'var(--indo-red)'
                : ($capacityPercentage >= 70
                    ? 'var(--indo-gold)'
                    : 'var(--indo-blue)');

        $roleLabels = [
            'super_admin' => 'Super Admin',
            'admin_gudang' => 'Admin Gudang',
            'supervisor' => 'Supervisor',
            'petugas_fg' => 'Petugas FG',
        ];
        $roleLabel =
            $roleLabels[$currentUser->role ?? ''] ?? ucfirst(str_replace('_', ' ', $currentUser->role ?? 'Pengguna'));
    @endphp

    <div class="dashboard-animate">
        {{-- HEADER RINGKAS --}}
        <div class="wms-page-header mb-3 dash-reveal dash-delay-1">
            <div>
                <h1 class="wms-page-title">Halo, {{ $currentUser->name }}</h1>
                <p class="wms-page-subtitle mb-0">
                    @if ($currentUser->role !== 'super_admin' && $currentUser->warehouse)
                        {{ $currentUser->warehouse->warehouse_code }} &mdash; {{ $currentUser->warehouse->warehouse_name }}
                    @else
                        Seluruh Unit Gudang Aktif
                    @endif
                </p>
            </div>
            <span class="wms-pill wms-pill-blue">
                <i class="bi bi-person-badge"></i> {{ $roleLabel }}
            </span>
        </div>

        {{-- AKSES CEPAT BERDASARKAN ROLE --}}
        <div class="row g-3 mb-4 dash-reveal dash-delay-2">
            @if (($currentUser->role ?? null) === 'super_admin')
                <div class="col-xl-3 col-md-6">
                    <a href="{{ route('warehouses.index') }}" class="indo-quick-link">
                        <span class="indo-quick-icon"><i class="bi bi-buildings"></i></span>
                        <span class="indo-quick-title">Master Gudang</span>
                        <i class="bi bi-chevron-right ms-auto text-muted"></i>
                    </a>
                </div>
                <div class="col-xl-3 col-md-6">
                    <a href="{{ route('users.index') }}" class="indo-quick-link">
                        <span class="indo-quick-icon"><i class="bi bi-people"></i></span>
                        <span class="indo-quick-title">Manajemen Pengguna</span>
                        <i class="bi bi-chevron-right ms-auto text-muted"></i>
                    </a>
                </div>
                <div class="col-xl-3 col-md-6">
                    <a href="{{ route('damaged-stocks.index') }}" class="indo-quick-link">
                        <span class="indo-quick-icon indo-quick-icon-danger"><i
                                class="bi bi-exclamation-triangle"></i></span>
                        <span class="indo-quick-title">Barang Rusak</span>
                        <i class="bi bi-chevron-right ms-auto text-muted"></i>
                    </a>
                </div>
                <div class="col-xl-3 col-md-6">
                    <a href="{{ route('reports.transactions') }}" class="indo-quick-link">
                        <span class="indo-quick-icon"><i class="bi bi-clock-history"></i></span>
                        <span class="indo-quick-title">Riwayat Transaksi</span>
                        <i class="bi bi-chevron-right ms-auto text-muted"></i>
                    </a>
                </div>
            @elseif(($currentUser->role ?? null) === 'admin_gudang')
                <div class="col-xl-3 col-md-6">
                    <a href="{{ route('transactions.create') }}" class="indo-quick-link">
                        <span class="indo-quick-icon"><i class="bi bi-box-arrow-in-down"></i></span>
                        <span class="indo-quick-title">Input Penyimpanan</span>
                        <i class="bi bi-chevron-right ms-auto text-muted"></i>
                    </a>
                </div>
                <div class="col-xl-3 col-md-6">
                    <a href="{{ route('storage-locations.index') }}" class="indo-quick-link">
                        <span class="indo-quick-icon"><i class="bi bi-geo-alt"></i></span>
                        <span class="indo-quick-title">Lokasi Produk</span>
                        <i class="bi bi-chevron-right ms-auto text-muted"></i>
                    </a>
                </div>
                <div class="col-xl-3 col-md-6">
                    <a href="{{ route('damaged-stocks.index') }}" class="indo-quick-link">
                        <span class="indo-quick-icon indo-quick-icon-danger"><i
                                class="bi bi-exclamation-triangle"></i></span>
                        <span class="indo-quick-title">Barang Rusak</span>
                        <i class="bi bi-chevron-right ms-auto text-muted"></i>
                    </a>
                </div>
                <div class="col-xl-3 col-md-6">
                    <a href="{{ route('reports.transactions') }}" class="indo-quick-link">
                        <span class="indo-quick-icon"><i class="bi bi-clock-history"></i></span>
                        <span class="indo-quick-title">Riwayat Transaksi</span>
                        <i class="bi bi-chevron-right ms-auto text-muted"></i>
                    </a>
                </div>
            @elseif(($currentUser->role ?? null) === 'supervisor')
                <div class="col-xl-3 col-md-6">
                    <a href="{{ route('monitoring.index') }}" class="indo-quick-link">
                        <span class="indo-quick-icon"><i class="bi bi-speedometer2"></i></span>
                        <span class="indo-quick-title">Monitoring Kapasitas</span>
                        <i class="bi bi-chevron-right ms-auto text-muted"></i>
                    </a>
                </div>
                <div class="col-xl-3 col-md-6">
                    <a href="{{ route('damaged-stocks.index') }}" class="indo-quick-link">
                        <span class="indo-quick-icon indo-quick-icon-danger"><i class="bi bi-camera"></i></span>
                        <span class="indo-quick-title">Barang Rusak</span>
                        <i class="bi bi-chevron-right ms-auto text-muted"></i>
                    </a>
                </div>
                <div class="col-xl-3 col-md-6">
                    <a href="{{ route('reports.stock-location') }}" class="indo-quick-link">
                        <span class="indo-quick-icon"><i class="bi bi-clipboard-data"></i></span>
                        <span class="indo-quick-title">Laporan Stok Produk</span>
                        <i class="bi bi-chevron-right ms-auto text-muted"></i>
                    </a>
                </div>
                <div class="col-xl-3 col-md-6">
                    <a href="{{ route('reports.transactions') }}" class="indo-quick-link">
                        <span class="indo-quick-icon"><i class="bi bi-clock-history"></i></span>
                        <span class="indo-quick-title">Riwayat Transaksi</span>
                        <i class="bi bi-chevron-right ms-auto text-muted"></i>
                    </a>
                </div>
            @elseif(($currentUser->role ?? null) === 'petugas_fg')
                <div class="col-xl-3 col-md-6">
                    <a href="{{ route('transactions.create') }}" class="indo-quick-link">
                        <span class="indo-quick-icon"><i class="bi bi-box-arrow-in-down"></i></span>
                        <span class="indo-quick-title">Input Penyimpanan</span>
                        <i class="bi bi-chevron-right ms-auto text-muted"></i>
                    </a>
                </div>
                <div class="col-xl-3 col-md-6">
                    <a href="{{ route('damaged-stocks.create') }}" class="indo-quick-link">
                        <span class="indo-quick-icon indo-quick-icon-danger"><i class="bi bi-camera-fill"></i></span>
                        <span class="indo-quick-title">Catat Barang Rusak</span>
                        <i class="bi bi-chevron-right ms-auto text-muted"></i>
                    </a>
                </div>
                <div class="col-xl-3 col-md-6">
                    <a href="{{ route('storage-locations.index') }}" class="indo-quick-link">
                        <span class="indo-quick-icon"><i class="bi bi-geo-alt"></i></span>
                        <span class="indo-quick-title">Lokasi Produk</span>
                        <i class="bi bi-chevron-right ms-auto text-muted"></i>
                    </a>
                </div>
                <div class="col-xl-3 col-md-6">
                    <a href="{{ route('monitoring.index') }}" class="indo-quick-link">
                        <span class="indo-quick-icon"><i class="bi bi-speedometer2"></i></span>
                        <span class="indo-quick-title">Monitoring Kapasitas</span>
                        <i class="bi bi-chevron-right ms-auto text-muted"></i>
                    </a>
                </div>
            @endif
        </div>

        {{-- RINGKASAN KAPASITAS & STOK --}}
        <div class="dash-reveal dash-delay-3">
            <div class="indo-section-bar">
                <h2 class="indo-section-title">Ringkasan Kapasitas & Stok</h2>
            </div>
            <div class="row g-3 mb-3">
                <div class="col-xl-3 col-md-6">
                    <div class="wms-kpi-card" style="--kpi-accent: var(--indo-navy); --kpi-tint: #E8EEF5;">
                        <div class="wms-kpi-top">
                            <span class="wms-kpi-label">Kapasitas Maksimum</span>
                            <span class="wms-kpi-icon"><i class="bi bi-grid-3x3"></i></span>
                        </div>
                        <div class="wms-kpi-value"><span class="js-kpi-count"
                                data-target="{{ $totalKapasitasPallet }}">{{ number_format($totalKapasitasPallet) }}</span>
                            <span class="fs-6 fw-bold text-muted">Plt</span>
                        </div>
                        <div class="wms-kpi-note">&plusmn; {{ number_format($totalKapasitasKarton ?? 0) }} karton</div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="wms-kpi-card" style="--kpi-accent: var(--indo-blue); --kpi-tint: var(--indo-blue-tint);">
                        <div class="wms-kpi-top">
                            <span class="wms-kpi-label">Pallet Terisi</span>
                            <span class="wms-kpi-icon"><i class="bi bi-box-seam-fill"></i></span>
                        </div>
                        <div class="wms-kpi-value" style="color: var(--indo-blue);"><span class="js-kpi-count"
                                data-target="{{ $totalPalletTerisi }}">{{ number_format($totalPalletTerisi) }}</span>
                            <span class="fs-6 fw-bold text-muted">Plt</span>
                        </div>
                        <div class="wms-kpi-note">{{ number_format($totalKartonTerisi ?? 0) }} karton bersih</div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="wms-kpi-card"
                        style="--kpi-accent: var(--indo-green); --kpi-tint: var(--indo-green-tint);">
                        <div class="wms-kpi-top">
                            <span class="wms-kpi-label">Sisa Kapasitas</span>
                            <span class="wms-kpi-icon"><i class="bi bi-check2-square"></i></span>
                        </div>
                        <div class="wms-kpi-value" style="color: var(--indo-green);"><span class="js-kpi-count"
                                data-target="{{ $sisaKapasitasPallet }}">{{ number_format($sisaKapasitasPallet) }}</span>
                            <span class="fs-6 fw-bold text-muted">Plt</span>
                        </div>
                        <div class="wms-kpi-note">{{ number_format($totalRak) }} rak &bull;
                            {{ number_format($totalArea) }} area</div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="wms-kpi-card" style="--kpi-accent: var(--indo-red); --kpi-tint: var(--indo-red-tint);">
                        <div class="wms-kpi-top">
                            <span class="wms-kpi-label">Barang Rusak</span>
                            <span class="wms-kpi-icon"><i class="bi bi-exclamation-triangle-fill"></i></span>
                        </div>
                        <div class="wms-kpi-value" style="color: var(--indo-red);"><span class="js-kpi-count"
                                data-target="{{ $totalBarangRusak ?? 0 }}">{{ number_format($totalBarangRusak ?? 0) }}</span>
                            <span class="fs-6 fw-bold text-muted">Dus</span>
                        </div>
                        <div class="wms-kpi-note">
                            <a href="{{ route('damaged-stocks.index') }}" class="text-decoration-none fw-semibold"
                                style="color: var(--indo-red);">
                                Lihat bukti &rarr;
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="wms-panel mb-4">
                <div class="wms-panel-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="fw-bold" style="color: var(--indo-navy); font-size: 13.5px;">Okupansi
                            Penyimpanan</span>
                        <span class="fw-bold tabular-nums"
                            style="color: {{ $barColor }}; font-size: 15px;">{{ number_format($capacityPercentage, 1) }}%</span>
                    </div>
                    <div class="wms-progress" role="progressbar" aria-valuenow="{{ round($capacityPercentage) }}"
                        aria-valuemin="0" aria-valuemax="100">
                        <div class="wms-progress-bar" id="mainOccupancyBar" data-target="{{ $capacityPercentage }}"
                            style="width: 0%; background-color: {{ $barColor }};"></div>
                    </div>
                    <div class="d-flex justify-content-between text-muted mt-2" style="font-size: 12px;">
                        <span>Terisi: <strong>{{ number_format($occupied) }}</strong> pallet</span>
                        <span>Tersedia: <strong>{{ number_format($remaining) }}</strong> pallet</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- GRAFIK ANALITIK PERGERAKAN & KOMPOSISI STOK (CHART.JS) --}}
        <div class="dash-reveal dash-delay-4">
            <div class="indo-section-bar">
                <h2 class="indo-section-title">Analitik Pergerakan & Komposisi Stok</h2>
                <span class="badge bg-light text-secondary border" style="font-size: 11px;">7 Hari Terakhir</span>
            </div>
            <div class="row g-3 mb-4">
                <div class="col-lg-8">
                    <div class="chart-card">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <div class="fw-bold" style="color: var(--indo-navy); font-size: 13.5px;">Tren Aktivitas
                                    Pallet (IN / OUT / MOVE)</div>
                                <div class="text-muted" style="font-size: 11.5px;">Volume pergerakan pallet harian dalam 7
                                    hari terakhir</div>
                            </div>
                        </div>
                        <div class="chart-canvas-wrap">
                            <canvas id="txTrendChart"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="chart-card">
                        <div class="mb-2">
                            <div class="fw-bold" style="color: var(--indo-navy); font-size: 13.5px;">Komposisi Stok Aktif
                            </div>
                            <div class="text-muted" style="font-size: 11.5px;">Distribusi pallet tersimpan saat ini</div>
                        </div>
                        <div class="chart-canvas-wrap d-flex align-items-center justify-content-center">
                            @if (count($categoryValues ?? []) > 0)
                                <canvas id="categoryDonutChart"></canvas>
                            @else
                                <div class="text-center text-muted small">
                                    <i class="bi bi-pie-chart fs-3 d-block mb-1 opacity-50"></i>
                                    Belum ada stok tersimpan di rak.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- VISUAL DENAH RAK 2D INTERAKTIF --}}
        <div class="dash-reveal dash-delay-5">
            <div class="indo-section-bar">
                <h2 class="indo-section-title">Visual Denah Rak Gudang (Klik Slot untuk Detail)</h2>
                <div class="rack-legend">
                    <span class="rack-legend-item"><span class="rack-legend-dot"
                            style="background:#DCE5EF; border:1px solid #94A3B8;"></span> Kosong</span>
                    <span class="rack-legend-item"><span class="rack-legend-dot" style="background:#00529B;"></span>
                        Terisi Sebagian</span>
                    <span class="rack-legend-item"><span class="rack-legend-dot" style="background:#0E2340;"></span>
                        Penuh</span>
                    <span class="rack-legend-item"><span class="rack-legend-dot" style="background:#D71920;"></span> Ada
                        Bad Stock</span>
                </div>
            </div>

            <div class="row g-3 mb-4">
                @forelse($visualRacks ?? [] as $rack)
                    @php
                        $rackCap = (int) $rack->levels->sum('capacity_pallets');
                        $rackOcc = (int) $rack->levels->sum(fn($lv) => $lv->storagePlacement->pallet_quantity ?? 0);
                    @endphp
                    <div class="col-xl-4 col-md-6">
                        <div class="rack-unit-card">
                            <div class="rack-unit-header">
                                <div>
                                    <div class="d-flex align-items-center gap-2">
                                        <span
                                            class="wms-code-badge">{{ $rack->area->warehouse->warehouse_code ?? 'WH' }}</span>
                                        <span class="rack-unit-code">{{ $rack->rack_code }}</span>
                                    </div>
                                    <div class="rack-unit-meta mt-1">
                                        <i class="bi bi-geo-alt-fill text-primary"></i>
                                        {{ $rack->area->area_name ?? 'Area Gudang' }}
                                    </div>
                                </div>
                                <span class="badge bg-light text-dark border tabular-nums" style="font-size: 11px;">
                                    {{ $rackOcc }}/{{ $rackCap }} Plt
                                </span>
                            </div>

                            <div class="rack-levels-stack">
                                @foreach ($rack->levels as $lv)
                                    @php
                                        $placement = $lv->storagePlacement;
                                        $usedPlt = (int) ($placement->pallet_quantity ?? 0);
                                        $maxPlt = (int) $lv->capacity_pallets;
                                        $dmgKey = $lv->id . '_' . ($placement->product_id ?? 0);
                                        $dmgDus = (int) ($damagedByLevelProduct[$dmgKey] ?? 0);
                                        $prodName =
                                            $placement && $placement->product
                                                ? $placement->product->product_name
                                                : 'Slot Kosong';
                                        $prodCode =
                                            $placement && $placement->product ? $placement->product->product_code : '-';
                                        $grossDus =
                                            $placement && $placement->product
                                                ? $usedPlt * ($placement->product->cartons_per_pallet ?? 120)
                                                : 0;
                                        $netDus = max(0, $grossDus - $dmgDus);

                                        $slotClass = 'rack-slot-empty';
                                        if ($usedPlt > 0 && $usedPlt >= $maxPlt) {
                                            $slotClass = 'rack-slot-full';
                                        } elseif ($usedPlt > 0) {
                                            $slotClass = 'rack-slot-partial';
                                        }
                                        if ($dmgDus > 0) {
                                            $slotClass .= ' rack-slot-damaged';
                                        }
                                    @endphp
                                    <button type="button" class="rack-slot-btn {{ $slotClass }} js-inspect-slot"
                                        data-bs-toggle="modal" data-bs-target="#rackSlotInspectModal"
                                        data-rack="{{ $rack->rack_code }}" data-level="Level {{ $lv->level_number }}"
                                        data-area="{{ ($rack->area->warehouse->warehouse_code ?? '') . ' - ' . ($rack->area->area_name ?? '') }}"
                                        data-product="{{ $prodName }}" data-sku="{{ $prodCode }}"
                                        data-pallet="{{ $usedPlt }} / {{ $maxPlt }} Pallet"
                                        data-carton="{{ number_format($netDus) }} Karton Bersih"
                                        data-damaged="{{ $dmgDus }} Dus Rusak">
                                        <span class="rack-lv-tag">Lv {{ $lv->level_number }}</span>
                                        <div class="rack-slot-info">
                                            <div class="rack-slot-sku">{{ $prodName }}</div>
                                            @if ($dmgDus > 0)
                                                <span class="text-danger fw-bold" style="font-size: 10.5px;">
                                                    <i class="bi bi-exclamation-circle-fill"></i> {{ $dmgDus }} Dus
                                                    Rusak
                                                </span>
                                            @endif
                                        </div>
                                        <span class="rack-slot-cap">{{ $usedPlt }}/{{ $maxPlt }} Plt</span>
                                    </button>
                                @endforeach
                            </div>

                            <div class="rack-base-foot" title="Fondasi Rak {{ $rack->rack_code }}"></div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="wms-panel">
                            <div class="wms-panel-body text-center text-muted small py-4">
                                Belum ada unit rak aktif untuk ditampilkan pada denah visual.
                            </div>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- BARANG RUSAK TERBARU & RINGKASAN UNIT GUDANG --}}
        <div class="dash-reveal dash-delay-6">
            <div class="indo-section-bar">
                <h2 class="indo-section-title">Barang Rusak Terbaru</h2>
                <a href="{{ route('damaged-stocks.index') }}" class="btn btn-sm btn-outline-primary">Semua &rarr;</a>
            </div>
            <div class="wms-panel mb-4">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Waktu (WIB)</th>
                                <th>Lokasi</th>
                                <th>Produk</th>
                                <th class="text-center">Rusak</th>
                                <th>Kategori</th>
                                <th>Petugas</th>
                                <th class="text-center">Foto</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentDamagedStocks ?? [] as $dmg)
                                <tr>
                                    <td class="small">{{ $dmg->reported_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <span class="wms-code-badge">{{ $dmg->warehouse->warehouse_code }}</span>
                                        <span class="small fw-semibold ms-1">{{ $dmg->rackLevel->rack->rack_code }} - Lv
                                            {{ $dmg->rackLevel->level_number }}</span>
                                    </td>
                                    <td>
                                        <div class="fw-semibold" style="font-size: 13px;">
                                            {{ $dmg->product->product_name }}</div>
                                        <div class="text-muted" style="font-size: 11px;">
                                            {{ $dmg->product->product_code }}</div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-danger">{{ $dmg->damaged_cartons }} Dus</span>
                                    </td>
                                    <td class="small">{{ $dmg->damage_category }}</td>
                                    <td class="small">{{ $dmg->reporter->name }}</td>
                                    <td class="text-center">
                                        <a href="{{ asset('storage/' . $dmg->photo_path) }}" target="_blank"
                                            class="btn btn-sm btn-outline-primary py-0 px-2" style="font-size: 12px;">
                                            <i class="bi bi-image"></i> Lihat
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-3 text-muted small">Belum ada data barang
                                        rusak.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if (isset($warehouseSummaries) && count($warehouseSummaries) > 0)
                <div class="indo-section-bar">
                    <h2 class="indo-section-title">Status Unit Gudang</h2>
                </div>

                <div class="row g-3">
                    @foreach ($warehouseSummaries as $warehouse)
                        @php
                            $whCap = (int) ($warehouse['kapasitas_pallet'] ?? 0);
                            $whOcc = (int) ($warehouse['pallet_terisi'] ?? 0);
                            $whRem = (int) ($warehouse['pallet_tersedia'] ?? 0);
                            $whPct = min(100, max(0, (float) ($warehouse['persentase_terisi'] ?? 0)));
                            $whBar =
                                $whPct >= 90
                                    ? 'var(--indo-red)'
                                    : ($whPct >= 70
                                        ? 'var(--indo-gold)'
                                        : 'var(--indo-blue)');
                        @endphp
                        <div class="col-xl-6">
                            <article class="wh-command-card">
                                <div class="d-flex justify-content-between align-items-center gap-2">
                                    <div>
                                        <span class="wms-code-badge">{{ $warehouse['warehouse_code'] ?? 'GUDANG' }}</span>
                                        <h5 class="fw-bold mt-2 mb-0" style="color: var(--indo-navy); font-size: 15.5px;">
                                            {{ $warehouse['warehouse_name'] ?? 'Nama gudang' }}
                                        </h5>
                                    </div>
                                    <span class="wms-pill wms-pill-green">Aktif</span>
                                </div>

                                <div class="wh-metric-grid">
                                    <div class="wh-metric-box">
                                        <span class="wh-metric-label">Area</span>
                                        <span
                                            class="wh-metric-val">{{ number_format((int) ($warehouse['total_area'] ?? 0)) }}</span>
                                    </div>
                                    <div class="wh-metric-box">
                                        <span class="wh-metric-label">Rak</span>
                                        <span
                                            class="wh-metric-val">{{ number_format((int) ($warehouse['total_rak'] ?? 0)) }}</span>
                                    </div>
                                    <div class="wh-metric-box">
                                        <span class="wh-metric-label">Kapasitas</span>
                                        <span class="wh-metric-val">{{ number_format($whCap) }}</span>
                                    </div>
                                    <div class="wh-metric-box">
                                        <span class="wh-metric-label">Rusak</span>
                                        <span class="wh-metric-val"
                                            style="color: var(--indo-red);">{{ number_format((int) ($warehouse['barang_rusak'] ?? 0)) }}</span>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between align-items-center mb-1"
                                    style="font-size: 12.5px;">
                                    <span class="fw-semibold text-secondary">Okupansi</span>
                                    <span class="fw-bold tabular-nums"
                                        style="color: {{ $whBar }};">{{ number_format($whPct, 1) }}%</span>
                                </div>
                                <div class="wms-progress">
                                    <div class="wms-progress-bar wh-occupancy-bar" data-target="{{ $whPct }}"
                                        style="width: 0%; background-color: {{ $whBar }};"></div>
                                </div>
                                <div class="d-flex justify-content-between text-muted mt-2" style="font-size: 11.5px;">
                                    <span>{{ number_format($whOcc) }} pallet
                                        ({{ number_format((int) ($warehouse['karton_terisi'] ?? 0)) }} karton)
                                    </span>
                                    <span>Sisa {{ number_format($whRem) }} pallet</span>
                                </div>
                            </article>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- Modal Inspeksi Slot Rak --}}
    <div class="modal fade" id="rackSlotInspectModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content border-0 shadow" style="border-radius: 14px;">
                <div class="modal-header border-bottom-0 pb-1">
                    <div>
                        <span class="badge bg-primary mb-1" id="inspectRackBadge">RAK</span>
                        <h6 class="modal-title fw-bold text-dark" id="inspectLevelTitle">Detail Slot</h6>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body pt-2">
                    <div class="rounded mb-2" style="background: #F8FAFC; border: 1px solid #E2E8F0; padding: 10px 12px;">
                        <div class="small text-muted">Lokasi Gudang & Area</div>
                        <div class="fw-semibold small text-dark" id="inspectAreaText">-</div>
                    </div>
                    <div class="rounded mb-2" style="background: #F8FAFC; border: 1px solid #E2E8F0; padding: 10px 12px;">
                        <div class="small text-muted">Produk Tersimpan</div>
                        <div class="fw-bold text-primary" style="font-size: 13.5px;" id="inspectProductText">-</div>
                        <div class="small text-muted" id="inspectSkuText">-</div>
                    </div>
                    <div class="row g-2 text-center">
                        <div class="col-6">
                            <div class="p-2 rounded border bg-white">
                                <div class="text-muted" style="font-size: 11px;">Okupansi Pallet</div>
                                <div class="fw-bold text-dark small" id="inspectPalletText">-</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2 rounded border bg-white">
                                <div class="text-muted" style="font-size: 11px;">Stok Bersih</div>
                                <div class="fw-bold text-success small" id="inspectCartonText">-</div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-2 text-center">
                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle w-100 py-1.5"
                            id="inspectDamagedBadge">0 Dus Rusak</span>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0">
                    <a href="{{ route('storage-locations.index') }}" class="btn btn-primary btn-sm w-100 fw-semibold">
                        Buka Lokasi Produk &rarr;
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // 1. Tirai Pembuka Full-Screen jika user baru saja masuk dari halaman /login
            if (sessionStorage.getItem('wms_just_logged_in') === '1') {
                sessionStorage.removeItem('wms_just_logged_in');
                const curtain = document.createElement('div');
                curtain.className = 'wms-entry-curtain';
                curtain.innerHTML = `
                                    <div style="background:#FFFFFF; padding:10px 18px; border-radius:10px; box-shadow:0 10px 28px rgba(0,0,0,0.25); margin-bottom:16px;">
                                    <img src="{{ asset('images/indofood-logo.svg') }}" alt="PT Indofood" style="width:130px; height:auto;">
                            </div>
                        <div style="display:inline-flex; align-items:center; gap:7px; padding:6px 14px; border-radius:999px; background:rgba(16,185,129,0.18); border:1px solid rgba(52,211,153,0.45); font-size:13px; font-weight:700; letter-spacing:0.03em; color:#6EE7B7;">
                        <i class="bi bi-check-circle-fill"></i> Akses Terverifikasi
                    </div>
            `;
                document.body.appendChild(curtain);
                setTimeout(() => curtain.remove(), 1350);
            }

            // 2. Animasi Progress Bar Okupansi
            requestAnimationFrame(() => {
                const mainBar = document.getElementById('mainOccupancyBar');
                if (mainBar) {
                    mainBar.style.width = mainBar.getAttribute('data-target') + '%';
                }
                document.querySelectorAll('.wh-occupancy-bar').forEach(bar => {
                    bar.style.width = bar.getAttribute('data-target') + '%';
                });
            });

            // 3. Animasi Angka KPI Count-Up
            document.querySelectorAll('.js-kpi-count').forEach(el => {
                const target = parseInt(el.dataset.target || '0', 10);
                if (target <= 0) return;
                const duration = 650;
                const startTime = performance.now();

                function step(now) {
                    const progress = Math.min(1, (now - startTime) / duration);
                    const easeOut = 1 - Math.pow(1 - progress, 3);
                    el.textContent = Math.round(target * easeOut).toLocaleString('id-ID');
                    if (progress < 1) requestAnimationFrame(step);
                }
                requestAnimationFrame(step);
            });

            // 4. Grafik 1: Tren Aktivitas Pallet 7 Hari Terakhir
            const txCtx = document.getElementById('txTrendChart');
            if (txCtx) {
                new Chart(txCtx, {
                    type: 'bar',
                    data: {
                        labels: @json($chartDates ?? []),
                        datasets: [{
                                label: 'Barang Masuk (IN)',
                                data: @json($chartIn ?? []),
                                backgroundColor: '#00529B',
                                borderRadius: 5,
                                barPercentage: 0.72
                            },
                            {
                                label: 'Barang Keluar (OUT)',
                                data: @json($chartOut ?? []),
                                backgroundColor: '#D71920',
                                borderRadius: 5,
                                barPercentage: 0.72
                            },
                            {
                                label: 'Pindah Rak (MOVE)',
                                data: @json($chartMove ?? []),
                                backgroundColor: '#F59E0B',
                                borderRadius: 5,
                                barPercentage: 0.72
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    usePointStyle: true,
                                    boxWidth: 8,
                                    font: {
                                        size: 11,
                                        weight: '600'
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                grid: {
                                    display: false
                                },
                                ticks: {
                                    font: {
                                        size: 11
                                    }
                                }
                            },
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1,
                                    precision: 0,
                                    font: {
                                        size: 11
                                    }
                                },
                                grid: {
                                    color: '#F1F5F9'
                                }
                            }
                        }
                    }
                });
            }

            // 5. Grafik 2: Komposisi Stok Aktif (Doughnut)
            const catCtx = document.getElementById('categoryDonutChart');
            if (catCtx) {
                new Chart(catCtx, {
                    type: 'doughnut',
                    data: {
                        labels: @json($categoryLabels ?? []),
                        datasets: [{
                            data: @json($categoryValues ?? []),
                            backgroundColor: ['#00529B', '#0E2340', '#16A34A', '#F59E0B', '#D71920',
                                '#0284C7'
                            ],
                            borderWidth: 2,
                            borderColor: '#FFFFFF'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '66%',
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    usePointStyle: true,
                                    boxWidth: 8,
                                    font: {
                                        size: 11,
                                        weight: '600'
                                    }
                                }
                            }
                        }
                    }
                });
            }

            // 6. Modal Inspeksi Slot Rak
            document.querySelectorAll('.js-inspect-slot').forEach(btn => {
                btn.addEventListener('click', function() {
                    document.getElementById('inspectRackBadge').textContent = this.dataset.rack;
                    document.getElementById('inspectLevelTitle').textContent = this.dataset.level;
                    document.getElementById('inspectAreaText').textContent = this.dataset.area;
                    document.getElementById('inspectProductText').textContent = this.dataset
                        .product;
                    document.getElementById('inspectSkuText').textContent = 'SKU: ' + this.dataset
                        .sku;
                    document.getElementById('inspectPalletText').textContent = this.dataset.pallet;
                    document.getElementById('inspectCartonText').textContent = this.dataset.carton;
                    document.getElementById('inspectDamagedBadge').textContent = this.dataset
                        .damaged;
                });
            });
        });
    </script>
@endsection
