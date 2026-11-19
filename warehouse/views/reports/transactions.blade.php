@extends('layouts.app')

@section('title', 'Riwayat Transaksi Gudang')
@section('page-heading', 'Riwayat Transaksi')

@section('content')
@php
    $totalTrx = $transactions->count();
    $totalIn = $transactions->where('transaction_type', 'IN')->sum('pallet_quantity');
    $totalOut = $transactions->where('transaction_type', 'OUT')->sum('pallet_quantity');
    $totalMove = $transactions->where('transaction_type', 'MOVE')->sum('pallet_quantity');
@endphp

{{-- HEADER CETAK KHUSUS SAAT PRINT --}}
<div class="d-none d-print-block mb-4 pb-3 border-bottom border-dark">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <div class="fw-bold text-uppercase" style="font-size: 16px; color: #0E2340;">PT INDOFOOD — WAREHOUSE FINISHED GOODS</div>
            <div class="fw-bold" style="font-size: 14px;">LAPORAN RIWAYAT TRANSAKSI PENYIMPANAN BARANG</div>
            @if(request('start_date') || request('end_date'))
                <div class="small text-muted">
                    Periode: {{ request('start_date') ? \Carbon\Carbon::parse(request('start_date'))->format('d/m/Y') : 'Awal' }}
                    s/d {{ request('end_date') ? \Carbon\Carbon::parse(request('end_date'))->format('d/m/Y') : 'Sekarang' }}
                </div>
            @endif
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
        <h1 class="wms-page-title">Laporan Riwayat Transaksi</h1>
        <p class="wms-page-subtitle">Audit log mutasi barang masuk (IN), keluar (OUT), dan perpindahan lokasi rak (MOVE).</p>
    </div>
    <button type="button" onclick="window.print()" class="btn btn-indo-primary">
        <i class="bi bi-printer"></i> Cetak Laporan
    </button>
</div>

{{-- RINGKASAN MUTASI --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-xl-3">
        <div class="wms-kpi-card" style="--kpi-accent: var(--indo-navy); --kpi-tint: #E8EEF5;">
            <div class="wms-kpi-top">
                <span class="wms-kpi-label">Total Transaksi</span>
                <span class="wms-kpi-icon"><i class="bi bi-receipt"></i></span>
            </div>
            <div class="wms-kpi-value">{{ number_format($totalTrx) }}</div>
            <div class="wms-kpi-note">Jumlah dokumen/aktivitas</div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="wms-kpi-card" style="--kpi-accent: var(--indo-green); --kpi-tint: var(--indo-green-tint);">
            <div class="wms-kpi-top">
                <span class="wms-kpi-label">Total Masuk (IN)</span>
                <span class="wms-kpi-icon"><i class="bi bi-box-arrow-in-down"></i></span>
            </div>
            <div class="wms-kpi-value" style="color: var(--indo-green);">{{ number_format($totalIn) }} <span class="fs-6 fw-bold text-muted">Plt</span></div>
            <div class="wms-kpi-note">Akumulasi pallet masuk</div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="wms-kpi-card" style="--kpi-accent: var(--indo-red); --kpi-tint: var(--indo-red-tint);">
            <div class="wms-kpi-top">
                <span class="wms-kpi-label">Total Keluar (OUT)</span>
                <span class="wms-kpi-icon"><i class="bi bi-box-arrow-up"></i></span>
            </div>
            <div class="wms-kpi-value" style="color: var(--indo-red);">{{ number_format($totalOut) }} <span class="fs-6 fw-bold text-muted">Plt</span></div>
            <div class="wms-kpi-note">Akumulasi pallet keluar</div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="wms-kpi-card" style="--kpi-accent: var(--indo-blue); --kpi-tint: var(--indo-blue-tint);">
            <div class="wms-kpi-top">
                <span class="wms-kpi-label">Total Pindah (MOVE)</span>
                <span class="wms-kpi-icon"><i class="bi bi-arrow-left-right"></i></span>
            </div>
            <div class="wms-kpi-value" style="color: var(--indo-blue);">{{ number_format($totalMove) }} <span class="fs-6 fw-bold text-muted">Plt</span></div>
            <div class="wms-kpi-note">Akumulasi relokasi antar rak</div>
        </div>
    </div>
</div>

{{-- FILTER LAPORAN TRANSAKSI --}}
<div class="wms-panel mb-4 no-print">
    <div class="wms-panel-header">
        <h2 class="wms-panel-title"><i class="bi bi-funnel"></i> Parameter Laporan Transaksi</h2>
    </div>
    <div class="wms-panel-body">
        <form action="{{ route('reports.transactions') }}" method="GET">
            <div class="row g-3 align-items-end">
                <div class="col-lg-3 col-md-6">
                    <label for="trx-warehouse" class="wms-form-label">Unit Gudang</label>
                    <select id="trx-warehouse" name="warehouse_id" class="form-select wms-control">
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
                <div class="col-lg-2 col-md-6">
                    <label for="trx-type" class="wms-form-label">Tipe Transaksi</label>
                    <select id="trx-type" name="transaction_type" class="form-select wms-control">
                        <option value="">Semua Tipe</option>
                        <option value="IN" {{ request('transaction_type') === 'IN' ? 'selected' : '' }}>IN (Masuk)</option>
                        <option value="OUT" {{ request('transaction_type') === 'OUT' ? 'selected' : '' }}>OUT (Keluar)</option>
                        <option value="MOVE" {{ request('transaction_type') === 'MOVE' ? 'selected' : '' }}>MOVE (Pindah)</option>
                    </select>
                </div>
                <div class="col-lg-2 col-md-6">
                    <label for="start_date" class="wms-form-label">Dari Tanggal</label>
                    <input type="date" id="start_date" name="start_date" class="form-control wms-control tabular-nums" value="{{ request('start_date') }}">
                </div>
                <div class="col-lg-2 col-md-6">
                    <label for="end_date" class="wms-form-label">Sampai Tanggal</label>
                    <input type="date" id="end_date" name="end_date" class="form-control wms-control tabular-nums" value="{{ request('end_date') }}">
                </div>
                <div class="col-lg-3 d-flex gap-2">
                    <button type="submit" class="btn btn-indo-primary flex-fill justify-content-center">
                        <i class="bi bi-search"></i> Tampilkan
                    </button>
                    <a href="{{ route('reports.transactions') }}" class="btn btn-indo-outline">Reset</a>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- TABEL RIWAYAT TRANSAKSI --}}
<div class="wms-panel">
    <div class="wms-panel-header no-print">
        <div>
            <h2 class="wms-panel-title"><i class="bi bi-table"></i> Log Mutasi Penyimpanan</h2>
            <p class="wms-panel-subtitle">Histori detail pergerakan stok beserta operator penanggung jawab.</p>
        </div>
        <span class="wms-pill wms-pill-slate tabular-nums">{{ number_format($totalTrx) }} baris</span>
    </div>

    <div class="table-responsive">
        <table class="wms-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal & Waktu</th>
                    <th>Tipe</th>
                    <th>SKU & Nama Produk</th>
                    <th>Lokasi Asal</th>
                    <th>Lokasi Tujuan</th>
                    <th class="text-end">Pallet</th>
                    <th class="text-end">Karton</th>
                    <th>Petugas</th>
                    <th>Catatan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transactions as $trx)
                    @php
                        $typeBadge = match($trx->transaction_type) {
                            'IN' => ['wms-pill-green', 'bi-box-arrow-in-down', 'IN'],
                            'OUT' => ['wms-pill-red', 'bi-box-arrow-up', 'OUT'],
                            'MOVE' => ['wms-pill-blue', 'bi-arrow-left-right', 'MOVE'],
                            default => ['wms-pill-slate', 'bi-dash', $trx->transaction_type],
                        };
                    @endphp
                    <tr>
                        <td class="text-muted tabular-nums">{{ $loop->iteration }}</td>
                        <td class="tabular-nums small">{{ $trx->transaction_date ? \Carbon\Carbon::parse($trx->transaction_date)->format('d/m/Y H:i') : ($trx->created_at?->format('d/m/Y H:i') ?? '-') }}</td>
                        <td>
                            <span class="wms-pill {{ $typeBadge[0] }}">
                                <i class="bi {{ $typeBadge[1] }}"></i> {{ $typeBadge[2] }}
                            </span>
                        </td>
                        <td>
                            <div class="fw-bold">{{ $trx->product->product_name ?? '-' }}</div>
                            <span class="wms-code-badge">{{ $trx->product->product_code ?? '-' }}</span>
                        </td>
                        <td>
                            @if($trx->sourceLevel)
                                <span class="fw-semibold">{{ $trx->sourceLevel->rack->rack_code ?? '-' }}</span>
                                <span class="text-muted small">Lv.{{ $trx->sourceLevel->level_number ?? '-' }}</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            @if($trx->destinationLevel)
                                <span class="fw-semibold">{{ $trx->destinationLevel->rack->rack_code ?? '-' }}</span>
                                <span class="text-muted small">Lv.{{ $trx->destinationLevel->level_number ?? '-' }}</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="text-end tabular-nums fw-bold">{{ number_format($trx->pallet_quantity) }}</td>
                        <td class="text-end tabular-nums">{{ number_format($trx->carton_quantity ?? ($trx->pallet_quantity * ($trx->product->cartons_per_pallet ?? 120))) }}</td>
                        <td>{{ $trx->user->name ?? '-' }}</td>
                        <td class="text-muted small">{{ $trx->notes ?: '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="text-center py-5 text-muted">
                            <i class="bi bi-journal-x fs-2 d-block mb-2"></i>
                            <div class="fw-bold text-dark">Tidak ada riwayat transaksi pada filter ini</div>
                            <div class="small">Ubah rentang tanggal, tipe transaksi, atau gudang untuk melihat data lain.</div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection