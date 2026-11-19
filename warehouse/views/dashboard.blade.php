@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-heading', 'Dashboard Operasional')

@push('styles')
<style>
    .indo-welcome-card {
        background: #FFFFFF;
        border: 1px solid var(--border-crisp);
        border-left: 5px solid var(--indo-blue);
        border-radius: var(--radius-lg);
        padding: 20px 24px;
        box-shadow: var(--shadow-card);
        position: relative;
        overflow: hidden;
    }
    .indo-welcome-card::after {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        width: 5px;
        height: 36%;
        background: var(--indo-red);
    }
    .indo-welcome-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 10px;
        border-radius: 5px;
        background: var(--indo-navy);
        color: #FFFFFF;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0.04em;
        text-transform: uppercase;
    }
    .indo-section-bar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin: 26px 0 14px;
    }
    .indo-section-title {
        margin: 0;
        color: var(--indo-navy);
        font-size: 15.5px;
        font-weight: 800;
        display: flex;
        align-items: center;
        gap: 9px;
    }
    .indo-section-title::before {
        content: "";
        width: 4px;
        height: 18px;
        border-radius: 2px;
        background: var(--indo-blue);
    }
    .indo-quick-link {
        display: flex;
        align-items: center;
        gap: 13px;
        height: 100%;
        padding: 15px 16px;
        background: #FFFFFF;
        border: 1px solid var(--border-crisp);
        border-radius: var(--radius-md);
        text-decoration: none;
        color: var(--text-primary);
        box-shadow: var(--shadow-card);
        transition: border-color 0.15s ease, background-color 0.15s ease;
    }
    .indo-quick-link:hover {
        border-color: var(--indo-blue);
        background: #F8FBFF;
        color: var(--indo-navy);
    }
    .indo-quick-icon {
        width: 40px;
        height: 40px;
        border-radius: 8px;
        background: var(--indo-blue-tint);
        border: 1px solid #C8DFF4;
        color: var(--indo-blue);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        flex-shrink: 0;
    }
    .indo-quick-title {
        display: block;
        font-size: 13.5px;
        font-weight: 800;
        color: var(--indo-navy);
        line-height: 1.25;
    }
    .indo-quick-desc {
        display: block;
        font-size: 11.5px;
        color: var(--text-muted);
        margin-top: 2px;
    }
    .wh-command-card {
        background: #FFFFFF;
        border: 1px solid var(--border-crisp);
        border-top: 3px solid var(--indo-navy);
        border-radius: var(--radius-lg);
        padding: 20px;
        box-shadow: var(--shadow-card);
        height: 100%;
    }
    .wh-metric-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 10px;
        margin: 16px 0;
    }
    .wh-metric-box {
        padding: 10px 12px;
        background: var(--bg-subtle);
        border: 1px solid var(--border-light);
        border-radius: 8px;
    }
    .wh-metric-label {
        display: block;
        font-size: 11px;
        font-weight: 700;
        color: var(--text-muted);
        margin-bottom: 3px;
    }
    .wh-metric-val {
        font-size: 18px;
        font-weight: 800;
        color: var(--indo-navy);
        font-variant-numeric: tabular-nums;
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

    $barColor = $capacityPercentage >= 90
        ? 'var(--indo-red)'
        : ($capacityPercentage >= 70 ? 'var(--indo-gold)' : 'var(--indo-blue)');

    $roleLabels = [
        'super_admin' => 'Super Admin',
        'admin_gudang' => 'Admin Gudang',
        'supervisor' => 'Supervisor',
        'petugas_fg' => 'Petugas FG',
    ];
    $roleLabel = $roleLabels[$currentUser->role ?? ''] ?? ucfirst(str_replace('_', ' ', $currentUser->role ?? 'Pengguna'));
@endphp

{{-- PAGE HEADER --}}
<div class="wms-page-header">
    <div>
        <h1 class="wms-page-title">Dashboard Warehouse Finished Goods</h1>
        <p class="wms-page-subtitle">Ringkasan kapasitas penyimpanan, inventori rak, dan kendali operasional gudang.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <span class="wms-pill wms-pill-blue">
            <i class="bi bi-person-badge"></i> {{ $roleLabel }}
        </span>
    </div>
</div>

{{-- SIGNATURE INDOFOOD WELCOME PANEL --}}
<section class="indo-welcome-card mb-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div>
            <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                <span class="indo-welcome-badge">PT Indofood &bull; Finished Goods</span>
                @if($currentUser->role !== 'super_admin' && $currentUser->warehouse)
                    <span class="wms-code-badge">{{ $currentUser->warehouse->warehouse_code }} â€” {{ $currentUser->warehouse->warehouse_name }}</span>
                @else
                    <span class="wms-pill wms-pill-slate">Akses Lintas Seluruh Gudang Aktif</span>
                @endif
            </div>
            <h4 class="fw-bold mb-1" style="color: var(--indo-navy); font-size: 18px;">
                Selamat Datang, {{ $currentUser->name }}
            </h4>
            <p class="text-muted mb-0" style="font-size: 13px;">
                Data kapasitas rak, jumlah pallet, dan histori mutasi barang ditampilkan secara aktual sesuai otoritas akun Anda.
            </p>
        </div>
    </div>
</section>

{{-- AKSES CEPAT BERDASARKAN ROLE --}}
@if(($currentUser->role ?? null) === 'admin_gudang')
    <div class="indo-section-bar">
        <h2 class="indo-section-title">Pusat Kendali Admin Gudang</h2>
        <span class="text-muted small">Operasional Harian</span>
    </div>
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-md-6">
            <a href="{{ route('monitoring.index') }}" class="indo-quick-link">
                <span class="indo-quick-icon"><i class="bi bi-speedometer2"></i></span>
                <span>
                    <span class="indo-quick-title">Monitoring Kapasitas</span>
                    <span class="indo-quick-desc">Cek sisa slot pallet tiap area & rak</span>
                </span>
                <i class="bi bi-chevron-right ms-auto text-muted"></i>
            </a>
        </div>
        <div class="col-xl-3 col-md-6">
            <a href="{{ route('storage-locations.index') }}" class="indo-quick-link">
                <span class="indo-quick-icon"><i class="bi bi-geo-alt"></i></span>
                <span>
                    <span class="indo-quick-title">Lokasi Produk</span>
                    <span class="indo-quick-desc">Lacak posisi SKU pada rak & level</span>
                </span>
                <i class="bi bi-chevron-right ms-auto text-muted"></i>
            </a>
        </div>
        <div class="col-xl-3 col-md-6">
            <a href="{{ route('transactions.create') }}" class="indo-quick-link">
                <span class="indo-quick-icon" style="background: var(--indo-red-tint); border-color: #F7C6C8; color: var(--indo-red);"><i class="bi bi-box-arrow-in-down"></i></span>
                <span>
                    <span class="indo-quick-title">Input Penyimpanan</span>
                    <span class="indo-quick-desc">Catat transaksi IN, OUT, atau MOVE</span>
                </span>
                <i class="bi bi-chevron-right ms-auto text-muted"></i>
            </a>
        </div>
        <div class="col-xl-3 col-md-6">
            <a href="{{ route('reports.transactions') }}" class="indo-quick-link">
                <span class="indo-quick-icon"><i class="bi bi-clock-history"></i></span>
                <span>
                    <span class="indo-quick-title">Riwayat Transaksi</span>
                    <span class="indo-quick-desc">Tinjau & cetak log mutasi barang</span>
                </span>
                <i class="bi bi-chevron-right ms-auto text-muted"></i>
            </a>
        </div>
    </div>
@endif

@if(($currentUser->role ?? null) === 'supervisor')
    <div class="indo-section-bar">
        <h2 class="indo-section-title">Pusat Pemantauan Supervisor</h2>
        <span class="wms-pill wms-pill-gold"><i class="bi bi-eye"></i> Mode Pemantauan & Audit</span>
    </div>
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-md-6">
            <a href="{{ route('monitoring.index') }}" class="indo-quick-link">
                <span class="indo-quick-icon"><i class="bi bi-speedometer2"></i></span>
                <span>
                    <span class="indo-quick-title">Monitoring Kapasitas</span>
                    <span class="indo-quick-desc">Pantau tingkat okupansi rak gudang</span>
                </span>
                <i class="bi bi-chevron-right ms-auto text-muted"></i>
            </a>
        </div>
        <div class="col-xl-3 col-md-6">
            <a href="{{ route('storage-locations.index') }}" class="indo-quick-link">
                <span class="indo-quick-icon"><i class="bi bi-geo-alt"></i></span>
                <span>
                    <span class="indo-quick-title">Lokasi Produk</span>
                    <span class="indo-quick-desc">Periksa distribusi stok di area & rak</span>
                </span>
                <i class="bi bi-chevron-right ms-auto text-muted"></i>
            </a>
        </div>
        <div class="col-xl-3 col-md-6">
            <a href="{{ route('reports.stock-location') }}" class="indo-quick-link">
                <span class="indo-quick-icon"><i class="bi bi-clipboard-data"></i></span>
                <span>
                    <span class="indo-quick-title">Laporan Stok Produk</span>
                    <span class="indo-quick-desc">Rekap & cetak posisi stok gudang</span>
                </span>
                <i class="bi bi-chevron-right ms-auto text-muted"></i>
            </a>
        </div>
        <div class="col-xl-3 col-md-6">
            <a href="{{ route('reports.transactions') }}" class="indo-quick-link">
                <span class="indo-quick-icon"><i class="bi bi-clock-history"></i></span>
                <span>
                    <span class="indo-quick-title">Riwayat Transaksi</span>
                    <span class="indo-quick-desc">Audit aktivitas keluar masuk barang</span>
                </span>
                <i class="bi bi-chevron-right ms-auto text-muted"></i>
            </a>
        </div>
    </div>
@endif

@if(($currentUser->role ?? null) === 'petugas_fg')
    <div class="indo-section-bar">
        <h2 class="indo-section-title">Akses Cepat Petugas Finished Goods</h2>
        <span class="text-muted small">Aktivitas Lapangan</span>
    </div>
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-md-6">
            <a href="{{ route('transactions.create') }}" class="indo-quick-link">
                <span class="indo-quick-icon" style="background: var(--indo-red-tint); border-color: #F7C6C8; color: var(--indo-red);"><i class="bi bi-box-arrow-in-down"></i></span>
                <span>
                    <span class="indo-quick-title">Input Penyimpanan</span>
                    <span class="indo-quick-desc">Catat barang masuk, keluar, & pindah</span>
                </span>
                <i class="bi bi-chevron-right ms-auto text-muted"></i>
            </a>
        </div>
        <div class="col-xl-3 col-md-6">
            <a href="{{ route('transactions.index') }}" class="indo-quick-link">
                <span class="indo-quick-icon"><i class="bi bi-arrow-left-right"></i></span>
                <span>
                    <span class="indo-quick-title">Transaksi Penyimpanan</span>
                    <span class="indo-quick-desc">Lihat daftar riwayat mutasi pallet</span>
                </span>
                <i class="bi bi-chevron-right ms-auto text-muted"></i>
            </a>
        </div>
        <div class="col-xl-3 col-md-6">
            <a href="{{ route('storage-locations.index') }}" class="indo-quick-link">
                <span class="indo-quick-icon"><i class="bi bi-geo-alt"></i></span>
                <span>
                    <span class="indo-quick-title">Lokasi Produk</span>
                    <span class="indo-quick-desc">Cek posisi rak sebelum penempatan</span>
                </span>
                <i class="bi bi-chevron-right ms-auto text-muted"></i>
            </a>
        </div>
        <div class="col-xl-3 col-md-6">
            <a href="{{ route('monitoring.index') }}" class="indo-quick-link">
                <span class="indo-quick-icon"><i class="bi bi-speedometer2"></i></span>
                <span>
                    <span class="indo-quick-title">Monitoring Kapasitas</span>
                    <span class="indo-quick-desc">Pantau ketersediaan level rak kosong</span>
                </span>
                <i class="bi bi-chevron-right ms-auto text-muted"></i>
            </a>
        </div>
    </div>
@endif

{{-- RINGKASAN INFRASTRUKTUR & MASTER DATA --}}
<div class="indo-section-bar">
    <h2 class="indo-section-title">Ringkasan Infrastruktur & Produk</h2>
</div>
<div class="row g-3">
    <div class="col-xl-3 col-md-6">
        <div class="wms-kpi-card" style="--kpi-accent: var(--indo-blue); --kpi-tint: var(--indo-blue-tint);">
            <div class="wms-kpi-top">
                <span class="wms-kpi-label">Total Gudang</span>
                <span class="wms-kpi-icon"><i class="bi bi-buildings"></i></span>
            </div>
            <div class="wms-kpi-value">{{ number_format($totalGudang) }}</div>
            <div class="wms-kpi-note">Unit gudang aktif dalam cakupan</div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="wms-kpi-card" style="--kpi-accent: var(--indo-green); --kpi-tint: var(--indo-green-tint);">
            <div class="wms-kpi-top">
                <span class="wms-kpi-label">Total Area</span>
                <span class="wms-kpi-icon"><i class="bi bi-map"></i></span>
            </div>
            <div class="wms-kpi-value">{{ number_format($totalArea) }}</div>
            <div class="wms-kpi-note">Zona penyimpanan terkonfigurasi</div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="wms-kpi-card" style="--kpi-accent: var(--indo-gold); --kpi-tint: var(--indo-gold-tint);">
            <div class="wms-kpi-top">
                <span class="wms-kpi-label">Total Rak</span>
                <span class="wms-kpi-icon"><i class="bi bi-bookshelf"></i></span>
            </div>
            <div class="wms-kpi-value">{{ number_format($totalRak) }}</div>
            <div class="wms-kpi-note">Unit rak aktif siap penyimpanan</div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="wms-kpi-card" style="--kpi-accent: var(--indo-navy); --kpi-tint: #E8EEF5;">
            <div class="wms-kpi-top">
                <span class="wms-kpi-label">SKU Produk Aktif</span>
                <span class="wms-kpi-icon"><i class="bi bi-box-seam"></i></span>
            </div>
            <div class="wms-kpi-value">{{ number_format($totalProduk) }}</div>
            <div class="wms-kpi-note">Varian produk jadi terdaftar</div>
        </div>
    </div>
</div>

{{-- RINGKASAN KAPASITAS PENYIMPANAN --}}
<div class="indo-section-bar">
    <h2 class="indo-section-title">Utilisasi Kapasitas Penyimpanan</h2>
</div>
<div class="row g-3 mb-3">
    <div class="col-lg-4 col-md-6">
        <div class="wms-kpi-card" style="--kpi-accent: var(--indo-navy); --kpi-tint: #E8EEF5;">
            <div class="wms-kpi-top">
                <span class="wms-kpi-label">Kapasitas Maksimum</span>
                <span class="wms-kpi-icon"><i class="bi bi-grid-3x3"></i></span>
            </div>
            <div class="wms-kpi-value">{{ number_format($totalKapasitasPallet) }} <span class="fs-6 fw-bold text-muted">Pallet</span></div>
            <div class="wms-kpi-note">Estimasi &plusmn; {{ number_format($totalKapasitasKarton ?? 0) }} karton</div>
        </div>
    </div>
    <div class="col-lg-4 col-md-6">
        <div class="wms-kpi-card" style="--kpi-accent: var(--indo-blue); --kpi-tint: var(--indo-blue-tint);">
            <div class="wms-kpi-top">
                <span class="wms-kpi-label">Pallet Terisi</span>
                <span class="wms-kpi-icon"><i class="bi bi-box-seam-fill"></i></span>
            </div>
            <div class="wms-kpi-value" style="color: var(--indo-blue);">{{ number_format($totalPalletTerisi) }} <span class="fs-6 fw-bold text-muted">Pallet</span></div>
            <div class="wms-kpi-note">Setara {{ number_format($totalKartonTerisi ?? 0) }} karton tersimpan</div>
        </div>
    </div>
    <div class="col-lg-4 col-md-12">
        <div class="wms-kpi-card" style="--kpi-accent: var(--indo-green); --kpi-tint: var(--indo-green-tint);">
            <div class="wms-kpi-top">
                <span class="wms-kpi-label">Sisa Kapasitas</span>
                <span class="wms-kpi-icon"><i class="bi bi-check2-square"></i></span>
            </div>
            <div class="wms-kpi-value" style="color: var(--indo-green);">{{ number_format($sisaKapasitasPallet) }} <span class="fs-6 fw-bold text-muted">Pallet</span></div>
            <div class="wms-kpi-note">Slot level rak yang masih kosong</div>
        </div>
    </div>
</div>

<div class="wms-panel mb-4">
    <div class="wms-panel-body">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <span class="fw-bold" style="color: var(--indo-navy); font-size: 13.5px;">Tingkat Okupansi Penyimpanan</span>
            <span class="fw-bold tabular-nums" style="color: {{ $barColor }}; font-size: 15px;">{{ number_format($capacityPercentage, 1) }}%</span>
        </div>
        <div class="wms-progress" role="progressbar" aria-valuenow="{{ round($capacityPercentage) }}" aria-valuemin="0" aria-valuemax="100">
            <div class="wms-progress-bar" style="width: {{ $capacityPercentage }}%; background-color: {{ $barColor }};"></div>
        </div>
        <div class="d-flex justify-content-between text-muted mt-2" style="font-size: 12px;">
            <span>Terpakai: <strong>{{ number_format($occupied) }}</strong> pallet</span>
            <span>Tersedia: <strong>{{ number_format($remaining) }}</strong> pallet</span>
        </div>
    </div>
</div>

{{-- WAREHOUSE COMMAND CENTER PER GUDANG --}}
@if(isset($warehouseSummaries) && count($warehouseSummaries) > 0)
    <div class="indo-section-bar">
        <h2 class="indo-section-title">Warehouse Command Center</h2>
        <span class="text-muted small">Status per Unit Gudang</span>
    </div>

    <div class="row g-3 mb-4">
        @foreach($warehouseSummaries as $warehouse)
            @php
                $whCap = (int) ($warehouse['kapasitas_pallet'] ?? 0);
                $whOcc = (int) ($warehouse['pallet_terisi'] ?? 0);
                $whRem = (int) ($warehouse['pallet_tersedia'] ?? 0);
                $whPct = min(100, max(0, (float) ($warehouse['persentase_terisi'] ?? 0)));
                $whBar = $whPct >= 90 ? 'var(--indo-red)' : ($whPct >= 70 ? 'var(--indo-gold)' : 'var(--indo-blue)');
            @endphp
            <div class="col-xl-6">
                <article class="wh-command-card">
                    <div class="d-flex justify-content-between align-items-start gap-2">
                        <div>
                            <span class="wms-code-badge">{{ $warehouse['warehouse_code'] ?? 'GUDANG' }}</span>
                            <h5 class="fw-bold mt-2 mb-0" style="color: var(--indo-navy); font-size: 16px;">
                                {{ $warehouse['warehouse_name'] ?? 'Nama gudang' }}
                            </h5>
                        </div>
                        <span class="wms-pill wms-pill-green">
                            <i class="bi bi-circle-fill" style="font-size: 6px;"></i> Aktif
                        </span>
                    </div>

                    <div class="wh-metric-grid">
                        <div class="wh-metric-box">
                            <span class="wh-metric-label">Total Area</span>
                            <span class="wh-metric-val">{{ number_format((int) ($warehouse['total_area'] ?? 0)) }}</span>
                        </div>
                        <div class="wh-metric-box">
                            <span class="wh-metric-label">Total Rak</span>
                            <span class="wh-metric-val">{{ number_format((int) ($warehouse['total_rak'] ?? 0)) }}</span>
                        </div>
                        <div class="wh-metric-box">
                            <span class="wh-metric-label">Kapasitas</span>
                            <span class="wh-metric-val">{{ number_format($whCap) }}</span>
                        </div>
                        <div class="wh-metric-box">
                            <span class="wh-metric-label">Pallet Terisi</span>
                            <span class="wh-metric-val" style="color: var(--indo-blue);">{{ number_format($whOcc) }}</span>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-1" style="font-size: 12.5px;">
                        <span class="fw-semibold text-secondary">Utilisasi Kapasitas</span>
                        <span class="fw-bold tabular-nums" style="color: {{ $whBar }};">{{ number_format($whPct, 1) }}%</span>
                    </div>
                    <div class="wms-progress">
                        <div class="wms-progress-bar" style="width: {{ $whPct }}%; background-color: {{ $whBar }};"></div>
                    </div>
                    <div class="d-flex justify-content-between text-muted mt-2" style="font-size: 11.5px;">
                        <span>{{ number_format($whOcc) }} pallet terisi ({{ number_format((int) ($warehouse['karton_terisi'] ?? 0)) }} karton)</span>
                        <span>Sisa {{ number_format($whRem) }} pallet</span>
                    </div>
                </article>
            </div>
        @endforeach
    </div>
@endif

{{-- INFORMASI AKUN PENGGUNA --}}
<div class="wms-panel">
    <div class="wms-panel-header">
        <h3 class="wms-panel-title"><i class="bi bi-person-vcard"></i> Informasi Sesi Pengguna</h3>
        <span class="wms-pill wms-pill-slate">Terautentikasi</span>
    </div>
    <div class="wms-panel-body">
        <div class="row g-3">
            <div class="col-md-4">
                <div class="text-muted small fw-semibold">Nama Lengkap</div>
                <div class="fw-bold text-dark mt-1">{{ $currentUser->name }}</div>
            </div>
            <div class="col-md-4">
                <div class="text-muted small fw-semibold">Email Korporat</div>
                <div class="fw-bold text-dark mt-1">{{ $currentUser->email }}</div>
            </div>
            <div class="col-md-4">
                <div class="text-muted small fw-semibold">Otoritas & Penugasan</div>
                <div class="mt-1 d-flex flex-wrap gap-2">
                    <span class="wms-pill wms-pill-blue">{{ $roleLabel }}</span>
                    @if($currentUser->warehouse)
                        <span class="wms-code-badge">{{ $currentUser->warehouse->warehouse_code }}</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
