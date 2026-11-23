@extends('layouts.app')

@section('title', 'Transaksi Penyimpanan')
@section('page-heading', 'Transaksi Penyimpanan')

@section('content')
    @php
        $canCreateTransaction = auth()->user()?->role !== 'supervisor';
        $pageIn = $transactions->getCollection()->where('transaction_type', 'IN')->sum('pallet_quantity');
        $pageOut = $transactions->getCollection()->where('transaction_type', 'OUT')->sum('pallet_quantity');
        $pageMove = $transactions->getCollection()->where('transaction_type', 'MOVE')->sum('pallet_quantity');
    @endphp

    {{-- PAGE HEADER --}}
    <div class="wms-page-header">
        <div>
            <h1 class="wms-page-title">Transaksi Penyimpanan Gudang</h1>
            <p class="wms-page-subtitle">Pencatatan dan pemantauan pergerakan barang masuk (IN), keluar (OUT), dan pindah rak
                (MOVE).</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            @if ($canCreateTransaction)
                <a href="{{ route('transactions.create') }}" class="btn btn-indo-primary">
                    <i class="bi bi-plus-lg"></i> Tambah Transaksi
                </a>
            @else
                <span class="wms-pill wms-pill-gold">
                    <i class="bi bi-eye"></i> Mode Pemantauan
                </span>
            @endif
        </div>
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

    {{-- RINGKASAN HALAMAN AKTIF --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="wms-kpi-card" style="--kpi-accent: var(--indo-navy); --kpi-tint: #E8EEF5;">
                <div class="wms-kpi-top">
                    <span class="wms-kpi-label">Total Log Tercatat</span>
                    <span class="wms-kpi-icon"><i class="bi bi-list-check"></i></span>
                </div>
                <div class="wms-kpi-value">{{ number_format($transactions->total()) }}</div>
                <div class="wms-kpi-note">Seluruh histori transaksi</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="wms-kpi-card" style="--kpi-accent: var(--indo-green); --kpi-tint: var(--indo-green-tint);">
                <div class="wms-kpi-top">
                    <span class="wms-kpi-label">Barang Masuk (Halaman)</span>
                    <span class="wms-kpi-icon"><i class="bi bi-box-arrow-in-down"></i></span>
                </div>
                <div class="wms-kpi-value" style="color: var(--indo-green);">{{ number_format($pageIn) }} <span
                        class="fs-6 fw-bold text-muted">Pallet</span></div>
                <div class="wms-kpi-note">Akumulasi tipe IN pada halaman ini</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="wms-kpi-card" style="--kpi-accent: var(--indo-red); --kpi-tint: var(--indo-red-tint);">
                <div class="wms-kpi-top">
                    <span class="wms-kpi-label">Barang Keluar (Halaman)</span>
                    <span class="wms-kpi-icon"><i class="bi bi-box-arrow-up"></i></span>
                </div>
                <div class="wms-kpi-value" style="color: var(--indo-red);">{{ number_format($pageOut) }} <span
                        class="fs-6 fw-bold text-muted">Pallet</span></div>
                <div class="wms-kpi-note">Akumulasi tipe OUT pada halaman ini</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="wms-kpi-card" style="--kpi-accent: var(--indo-blue); --kpi-tint: var(--indo-blue-tint);">
                <div class="wms-kpi-top">
                    <span class="wms-kpi-label">Pindah Lokasi (Halaman)</span>
                    <span class="wms-kpi-icon"><i class="bi bi-arrow-left-right"></i></span>
                </div>
                <div class="wms-kpi-value" style="color: var(--indo-blue);">{{ number_format($pageMove) }} <span
                        class="fs-6 fw-bold text-muted">Pallet</span></div>
                <div class="wms-kpi-note">Akumulasi tipe MOVE pada halaman ini</div>
            </div>
        </div>
    </div>

    {{-- TABEL TRANSAKSI --}}
    <div class="wms-panel">
        <div class="wms-panel-header">
            <div>
                <h2 class="wms-panel-title"><i class="bi bi-clock-history"></i> Daftar Transaksi Penyimpanan</h2>
                <p class="wms-panel-subtitle">Catatan mutasi pallet beserta lokasi asal dan tujuan rak.</p>
            </div>
            <span class="wms-pill wms-pill-slate tabular-nums">{{ number_format($transactions->total()) }} data</span>
        </div>

        <div class="table-responsive">
            <table class="wms-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Waktu</th>
                        <th>Tipe</th>
                        <th>Produk</th>
                        <th>Lokasi Asal</th>
                        <th>Lokasi Tujuan</th>
                        <th class="text-end">Pallet</th>
                        <th class="text-end">Karton</th>
                        <th>Petugas</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $trx)
                        @php
                            $typeBadge = match ($trx->transaction_type) {
                                'IN' => ['wms-pill-green', 'bi-box-arrow-in-down', 'IN'],
                                'OUT' => ['wms-pill-red', 'bi-box-arrow-up', 'OUT'],
                                'MOVE' => ['wms-pill-blue', 'bi-arrow-left-right', 'MOVE'],
                                default => ['wms-pill-slate', 'bi-dash', $trx->transaction_type],
                            };
                            $trxTime = $trx->transaction_date
                                ? \Carbon\Carbon::parse($trx->transaction_date)->timezone('Asia/Jakarta')
                                : $trx->created_at?->timezone('Asia/Jakarta');
                        @endphp
                        <tr>
                            <td class="text-muted tabular-nums">{{ $transactions->firstItem() + $loop->index }}</td>
                            <td class="tabular-nums small">
                                @if ($trxTime)
                                    <div class="fw-semibold text-dark">{{ $trxTime->format('d/m/Y') }}</div>
                                    <div class="text-muted">{{ $trxTime->format('H:i') }} WIB</div>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
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
                                @if ($trx->fromRackLevel)
                                    <span class="fw-semibold">{{ $trx->fromRackLevel->rack->rack_code ?? '-' }}</span>
                                    <span class="text-muted small">Lv.{{ $trx->fromRackLevel->level_number ?? '-' }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if ($trx->toRackLevel)
                                    <span class="fw-semibold">{{ $trx->toRackLevel->rack->rack_code ?? '-' }}</span>
                                    <span class="text-muted small">Lv.{{ $trx->toRackLevel->level_number ?? '-' }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-end tabular-nums fw-bold">{{ number_format($trx->pallet_quantity) }}</td>
                            <td class="text-end tabular-nums">
                                {{ number_format($trx->carton_quantity ?? $trx->pallet_quantity * ($trx->product->cartons_per_pallet ?? 120)) }}
                            </td>
                            <td>{{ $trx->user->name ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted">
                                <i class="bi bi-journal-x fs-2 d-block mb-2"></i>
                                <div class="fw-bold text-dark">Belum ada transaksi penyimpanan</div>
                                <div class="small">Transaksi baru akan muncul setelah pencatatan IN, OUT, atau MOVE.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($transactions->hasPages())
            <div class="wms-pagination-bar">
                <span>Menampilkan {{ $transactions->firstItem() }}–{{ $transactions->lastItem() }} dari
                    {{ $transactions->total() }} transaksi</span>
                {{ $transactions->withQueryString()->links() }}
            </div>
        @endif
    </div>
@endsection
