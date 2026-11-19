@extends('layouts.app')

@section('title', 'Edit Kategori Produk')
@section('page-heading', 'Edit Kategori Produk')

@section('content')
<div class="wms-page-header">
    <div>
        <h1 class="wms-page-title">Edit Kategori Produk</h1>
        <p class="wms-page-subtitle">Perbarui kode, nama klasifikasi kategori, atau keterangan lini produk.</p>
    </div>
    <a href="{{ route('categories.index') }}" class="btn btn-indo-outline">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

@if($errors->any())
    <div class="alert alert-danger mb-4" style="border-radius: 8px; font-size: 13px;">
        <div class="fw-bold mb-1"><i class="bi bi-exclamation-circle-fill me-2"></i>Periksa kembali isian formulir:</div>
        <ul class="mb-0 ps-3">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="row g-4">
    <div class="col-lg-8">
        <div class="wms-panel">
            <div class="wms-panel-header">
                <div class="d-flex align-items-center gap-2">
                    <h2 class="wms-panel-title"><i class="bi bi-pencil-square"></i> Edit Kategori</h2>
                    @if($category->category_code)
                        <span class="wms-code-badge">{{ $category->category_code }}</span>
                    @endif
                </div>
                <span class="wms-pill wms-pill-slate">ID #{{ $category->id }}</span>
            </div>
            <div class="wms-panel-body">
                <form action="{{ route('categories.update', $category) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="wms-form-section-title">Informasi Kategori</div>
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label for="category_code" class="wms-form-label">Kode Kategori <span class="text-danger">*</span></label>
                            <input type="text" id="category_code" name="category_code" class="form-control wms-control"
                                   value="{{ old('category_code', $category->category_code) }}" required>
                        </div>

                        <div class="col-md-8">
                            <label for="category_name" class="wms-form-label">Nama Kategori <span class="text-danger">*</span></label>
                            <input type="text" id="category_name" name="category_name" class="form-control wms-control"
                                   value="{{ old('category_name', $category->category_name) }}" required>
                        </div>

                        <div class="col-12">
                            <label for="description" class="wms-form-label">Keterangan (Opsional)</label>
                            <textarea id="description" name="description" rows="3" class="form-control">{{ old('description', $category->description) }}</textarea>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 pt-3 border-top" style="border-color: var(--border-light) !important;">
                        <a href="{{ route('categories.index') }}" class="btn btn-indo-outline">Batal</a>
                        <button type="submit" class="btn btn-indo-primary">
                            <i class="bi bi-check2-circle"></i> Perbarui Kategori
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="wms-info-box">
            <i class="bi bi-shield-check fs-5 text-primary flex-shrink-0"></i>
            <div>
                <div class="fw-bold mb-1">Catatan Pembaruan</div>
                <p class="mb-0 small">Perubahan nama atau kode kategori akan otomatis tercermin pada seluruh SKU produk yang terhubung dengan kategori ini.</p>
            </div>
        </div>
    </div>
</div>
@endsection