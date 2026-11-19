@extends('layouts.app')

@section('title', 'Kategori Produk')
@section('page-heading', 'Kategori Produk')

@section('content')
@php
    $withProducts = $categories->getCollection()->filter(fn($c) => ($c->products_count ?? $c->products->count()) > 0)->count();
@endphp

{{-- PAGE HEADER --}}
<div class="wms-page-header">
    <div>
        <h1 class="wms-page-title">Master Kategori Produk</h1>
        <p class="wms-page-subtitle">Pengelompokan lini produk Finished Goods PT Indofood (Mie Instan, Snack, Bumbu, Minuman, dll).</p>
    </div>
    <a href="{{ route('categories.create') }}" class="btn btn-indo-primary">
        <i class="bi bi-plus-lg"></i> Tambah Kategori
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

{{-- RINGKASAN DATA KATEGORI --}}
<div class="row g-3 mb-4">
    <div class="col-md-6">
        <div class="wms-kpi-card" style="--kpi-accent: var(--indo-navy); --kpi-tint: #E8EEF5;">
            <div class="wms-kpi-top">
                <span class="wms-kpi-label">Total Kategori Terdaftar</span>
                <span class="wms-kpi-icon"><i class="bi bi-tags"></i></span>
            </div>
            <div class="wms-kpi-value">{{ number_format($categories->total()) }}</div>
            <div class="wms-kpi-note">Klasifikasi lini produk gudang</div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="wms-kpi-card" style="--kpi-accent: var(--indo-blue); --kpi-tint: var(--indo-blue-tint);">
            <div class="wms-kpi-top">
                <span class="wms-kpi-label">Kategori Memiliki SKU (Halaman)</span>
                <span class="wms-kpi-icon"><i class="bi bi-box-seam"></i></span>
            </div>
            <div class="wms-kpi-value" style="color: var(--indo-blue);">{{ number_format($withProducts) }}</div>
            <div class="wms-kpi-note">Kategori aktif terhubung ke master produk</div>
        </div>
    </div>
</div>

{{-- FILTER PENCARIAN --}}
<div class="wms-panel mb-4">
    <div class="wms-panel-header">
        <h2 class="wms-panel-title"><i class="bi bi-funnel"></i> Pencarian Kategori</h2>
    </div>
    <div class="wms-panel-body">
        <form action="{{ route('categories.index') }}" method="GET">
            <div class="row g-3 align-items-end">
                <div class="col-lg-9">
                    <label for="category-search" class="wms-form-label">Kode atau Nama Kategori</label>
                    <input type="text" id="category-search" name="search" class="form-control wms-control"
                           placeholder="Contoh: CAT-01, Mie Instan, Snack..."
                           value="{{ request('search') }}">
                </div>
                <div class="col-lg-3 d-flex gap-2">
                    <button type="submit" class="btn btn-indo-primary flex-fill justify-content-center">
                        <i class="bi bi-search"></i> Cari
                    </button>
                    <a href="{{ route('categories.index') }}" class="btn btn-indo-outline">Reset</a>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- TABEL DATA KATEGORI --}}
<div class="wms-panel">
    <div class="wms-panel-header">
        <div>
            <h2 class="wms-panel-title"><i class="bi bi-table"></i> Daftar Kategori Produk</h2>
            <p class="wms-panel-subtitle">Klasifikasi SKU produk jadi beserta jumlah item yang terdaftar.</p>
        </div>
        <span class="wms-pill wms-pill-slate tabular-nums">{{ number_format($categories->total()) }} data</span>
    </div>

    <div class="table-responsive">
        <table class="wms-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kode</th>
                    <th>Nama Kategori</th>
                    <th>Keterangan</th>
                    <th class="text-center">Jumlah SKU</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($categories as $category)
                    @php
                        $skuCount = $category->products_count ?? $category->products->count();
                    @endphp
                    <tr>
                        <td class="text-muted tabular-nums">{{ $categories->firstItem() + $loop->index }}</td>
                        <td><span class="wms-code-badge">{{ $category->category_code ?? '-' }}</span></td>
                        <td class="fw-bold">{{ $category->category_name }}</td>
                        <td class="text-muted small">{{ $category->description ?: '-' }}</td>
                        <td class="text-center">
                            <span class="wms-pill wms-pill-blue tabular-nums">{{ number_format($skuCount) }} Produk</span>
                        </td>
                        <td class="text-end">
                            <div class="d-inline-flex gap-1">
                                <a href="{{ route('categories.edit', $category) }}" class="btn btn-sm btn-outline-primary wms-action-btn" title="Edit Kategori">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <form action="{{ route('categories.destroy', $category) }}" method="POST" onsubmit="return confirm('Hapus kategori {{ $category->category_name }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger wms-action-btn" title="Hapus Kategori">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="bi bi-tags fs-2 d-block mb-2"></i>
                            <div class="fw-bold text-dark">Belum ada data kategori produk</div>
                            <div class="small">Klik tombol <strong>Tambah Kategori</strong> untuk membuat kategori baru.</div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($categories->hasPages())
        <div class="wms-pagination-bar">
            <span>Menampilkan {{ $categories->firstItem() }}–{{ $categories->lastItem() }} dari {{ $categories->total() }} kategori</span>
            {{ $categories->withQueryString()->links() }}
        </div>
    @endif
</div>
@endsection