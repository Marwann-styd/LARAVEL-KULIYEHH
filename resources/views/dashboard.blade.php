@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-heading', 'Dashboard')

@push('styles')
    <style>
        @keyframes dashboardFadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .dashboard-animate {
            animation: dashboardFadeIn 0.35s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        .indo-section-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin: 22px 0 12px;
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
            transition: transform 0.18s cubic-bezier(0.16, 1, 0.3, 1),
                box-shadow 0.18s ease,
                border-color 0.18s ease,
                background-color 0.18s ease;
        }

        .indo-quick-link:hover {
            transform: translateY(-3px);
            border-color: var(--indo-blue);
            background: #F8FBFF;
            color: var(--indo-navy);
            box-shadow: 0 10px 22px rgba(14, 35, 64, 0.08);
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
            transition: transform 0.18s ease;
        }

        .indo-quick-link:hover .indo-quick-icon {
            transform: scale(1.06);
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
            transition: transform 0.18s ease, color 0.18s ease;
        }

        .indo-quick-link:hover .bi-chevron-right {
            transform: translateX(4px);
            color: var(--indo-blue) !important;
        }

        .wms-kpi-card,
        .wh-command-card {
            transition: transform 0.18s cubic-bezier(0.16, 1, 0.3, 1),
                box-shadow 0.18s ease,
                border-color 0.18s ease;
        }

        .wms-kpi-card:hover,
        .wh-command-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(14, 35, 64, 0.07);
        }

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
            transition: background-color 0.15s ease;
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
            transition: width 0.75s cubic-bezier(0.22, 1, 0.36, 1);
        }

        .btn {
            transition: transform 0.12s ease, box-shadow 0.15s ease, background-color 0.15s ease;
        }

        .btn:active {
            transform: scale(0.97);
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
        <div class="wms-page-header mb-3">
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
        <div class="row g-3 mb-4">
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
                    <div class="wms-kpi-value">{{ number_format($totalKapasitasPallet) }} <span
                            class="fs-6 fw-bold text-muted">Plt</span></div>
                    <div class="wms-kpi-note">&plusmn; {{ number_format($totalKapasitasKarton ?? 0) }} karton</div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="wms-kpi-card" style="--kpi-accent: var(--indo-blue); --kpi-tint: var(--indo-blue-tint);">
                    <div class="wms-kpi-top">
                        <span class="wms-kpi-label">Pallet Terisi</span>
                        <span class="wms-kpi-icon"><i class="bi bi-box-seam-fill"></i></span>
                    </div>
                    <div class="wms-kpi-value" style="color: var(--indo-blue);">{{ number_format($totalPalletTerisi) }}
                        <span class="fs-6 fw-bold text-muted">Plt</span></div>
                    <div class="wms-kpi-note">{{ number_format($totalKartonTerisi ?? 0) }} karton bersih</div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="wms-kpi-card" style="--kpi-accent: var(--indo-green); --kpi-tint: var(--indo-green-tint);">
                    <div class="wms-kpi-top">
                        <span class="wms-kpi-label">Sisa Kapasitas</span>
                        <span class="wms-kpi-icon"><i class="bi bi-check2-square"></i></span>
                    </div>
                    <div class="wms-kpi-value" style="color: var(--indo-green);">
                        {{ number_format($sisaKapasitasPallet) }} <span class="fs-6 fw-bold text-muted">Plt</span></div>
                    <div class="wms-kpi-note">{{ number_format($totalRak) }} rak &bull; {{ number_format($totalArea) }}
                        area</div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="wms-kpi-card" style="--kpi-accent: var(--indo-red); --kpi-tint: var(--indo-red-tint);">
                    <div class="wms-kpi-top">
                        <span class="wms-kpi-label">Barang Rusak</span>
                        <span class="wms-kpi-icon"><i class="bi bi-exclamation-triangle-fill"></i></span>
                    </div>
                    <div class="wms-kpi-value" style="color: var(--indo-red);">
                        {{ number_format($totalBarangRusak ?? 0) }} <span class="fs-6 fw-bold text-muted">Dus</span></div>
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
                    <span class="fw-bold" style="color: var(--indo-navy); font-size: 13.5px;">Okupansi Penyimpanan</span>
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

        {{-- BARANG RUSAK TERBARU --}}
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
                                    <div class="fw-semibold" style="font-size: 13px;">{{ $dmg->product->product_name }}
                                    </div>
                                    <div class="text-muted" style="font-size: 11px;">{{ $dmg->product->product_code }}
                                    </div>
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
                                <td colspan="7" class="text-center py-3 text-muted small">Belum ada data barang rusak.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- RINGKASAN PER GUDANG --}}
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
                            $whPct >= 90 ? 'var(--indo-red)' : ($whPct >= 70 ? 'var(--indo-gold)' : 'var(--indo-blue)');
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
                                    ({{ number_format((int) ($warehouse['karton_terisi'] ?? 0)) }} karton)</span>
                                <span>Sisa {{ number_format($whRem) }} pallet</span>
                            </div>
                        </article>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            requestAnimationFrame(() => {
                const mainBar = document.getElementById('mainOccupancyBar');
                if (mainBar) {
                    mainBar.style.width = mainBar.getAttribute('data-target') + '%';
                }
                document.querySelectorAll('.wh-occupancy-bar').forEach(bar => {
                    bar.style.width = bar.getAttribute('data-target') + '%';
                });
            });
        });
    </script>
@endsection
