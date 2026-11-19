@extends('layouts.app')

@section('title', 'Data Produk')
@section('page-heading', 'Data Produk')

@section('content')
{{-- PAGE HEADER --}}
<div class="wms-page-header">
    <div>
        <h1 class="wms-page-title">Master Data Produk (SKU)</h1>
        <p class="wms-page-subtitle">Pengelolaan katalog produk Finished Goods PT Indofood beserta standar isi karton per pallet.</p>
    </div>
    <a href="{{ route('products.create') }}" class="btn btn-indo-primary">
        <i class="bi bi-plus-lg"></i> Tambah Produk
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

{{-- RINGKASAN DATA PRODUK --}}
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="wms-kpi-card" style="--kpi-accent: var(--indo-navy); --kpi-tint: #E8EEF5;">
            <div class="wms-kpi-top">
                <span class="wms-kpi-label">Total SKU Terdaftar</span>
                <span class="wms-kpi-icon"><i class="bi bi-upc-scan"></i></span>
            </div>
            <div class="wms-kpi-value">{{ number_format($products->total()) }}</div>
            <div class="wms-kpi-note">Seluruh item produk dalam katalog</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="wms-kpi-card" style="--kpi-accent: var(--indo-green); --kpi-tint: var(--indo-green-tint);">
            <div class="wms-kpi-top">
                <span class="wms-kpi-label">SKU Aktif</span>
                <span class="wms-kpi-icon"><i class="bi bi-check-circle"></i></span>
            </div>
            <div class="wms-kpi-value" style="color: var(--indo-green);">{{ number_format($totalAktif ?? 0) }}</div>
            <div class="wms-kpi-note">Tersedia untuk transaksi IN / OUT / MOVE</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="wms-kpi-card" style="--kpi-accent: var(--indo-red); --kpi-tint: var(--indo-red-tint);">
            <div class="wms-kpi-top">
                <span class="wms-kpi-label">SKU Nonaktif</span>
                <span class="wms-kpi-icon"><i class="bi bi-slash-circle"></i></span>
            </div>
            <div class="wms-kpi-value" style="color: var(--indo-red);">{{ number_format($totalNonaktif ?? 0) }}</div>
            <div class="wms-kpi-note">Discontinue atau sementara dihentikan</div>
        </div>
    </div>
</div>

{{-- TABEL DATA PRODUK --}}
<div class="wms-panel">
    <div class="wms-panel-header">
        <div>
            <h2 class="wms-panel-title"><i class="bi bi-table"></i> Katalog Produk Finished Goods</h2>
            <p class="wms-panel-subtitle">Daftar SKU lengkap dengan kategori serta standar isi karton per pallet.</p>
        </div>
        <span class="wms-pill wms-pill-slate tabular-nums">{{ number_format($products->total()) }} SKU</span>
    </div>

    <div class="table-responsive">
        <table class="wms-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kode SKU</th>
                    <th>Nama Produk</th>
                    <th>Kategori</th>
                    <th class="text-end">Isi / Pallet</th>
                    <th>Status</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                    <tr>
                        <td class="text-muted tabular-nums">{{ $products->firstItem() + $loop->index }}</td>
                        <td><span class="wms-code-badge">{{ $product->product_code }}</span></td>
                        <td>
                            <div class="fw-bold">{{ $product->product_name }}</div>
                            @if($product->description)
                                <div class="text-muted small">{{ \Illuminate\Support\Str::limit($product->description, 65) }}</div>
                            @endif
                        </td>
                        <td>
                            @if($product->category)
                                <span class="wms-pill wms-pill-blue">{{ $product->category->category_name }}</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="text-end tabular-nums fw-bold">
                            {{ number_format($product->cartons_per_pallet ?? 120) }} <span class="text-muted fw-normal small">Karton</span>
                        </td>
                        <td>
                            @if($product->is_active)
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
                                <a href="{{ route('products.edit', $product) }}" class="btn btn-sm btn-outline-primary wms-action-btn" title="Edit Produk">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <form action="{{ route('products.toggle-status', $product) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-sm btn-outline-warning wms-action-btn" title="{{ $product->is_active ? 'Nonaktifkan Produk' : 'Aktifkan Produk' }}">
                                        <i class="bi bi-power"></i>
                                    </button>
                                </form>
                                <form action="{{ route('products.destroy', $product) }}" method="POST" onsubmit="return confirm('Hapus produk {{ $product->product_name }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger wms-action-btn" title="Hapus Produk">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="bi bi-box-seam fs-2 d-block mb-2"></i>
                            <div class="fw-bold text-dark">Belum ada data produk (SKU)</div>
                            <div class="small">Klik tombol <strong>Tambah Produk</strong> untuk menambahkan item baru ke katalog.</div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($products->hasPages())
        <div class="wms-pagination-bar">
            <span>Menampilkan {{ $products->firstItem() }}–{{ $products->lastItem() }} dari {{ $products->total() }} produk</span>
            {{ $products->withQueryString()->links() }}
        </div>
    @endif
</div>
@endsection