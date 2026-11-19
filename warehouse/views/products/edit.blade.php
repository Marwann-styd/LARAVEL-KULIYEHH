@extends('layouts.app')

@section('title', 'Edit Data Produk')
@section('page-heading', 'Edit Data Produk')

@section('content')
<div class="wms-page-header">
    <div>
        <h1 class="wms-page-title">Edit Data Produk (SKU)</h1>
        <p class="wms-page-subtitle">Perbarui identitas produk, kategori, atau standar konversi karton per pallet.</p>
    </div>
    <a href="{{ route('products.index') }}" class="btn btn-indo-outline">
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
                    <h2 class="wms-panel-title"><i class="bi bi-pencil-square"></i> Edit Spesifikasi Produk</h2>
                    <span class="wms-code-badge">{{ $product->product_code }}</span>
                </div>
                <span class="wms-pill wms-pill-slate">ID #{{ $product->id }}</span>
            </div>
            <div class="wms-panel-body">
                <form action="{{ route('products.update', $product) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="wms-form-section-title">1. Identitas & Klasifikasi SKU</div>
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label for="product_code" class="wms-form-label">Kode SKU <span class="text-danger">*</span></label>
                            <input type="text" id="product_code" name="product_code" class="form-control wms-control"
                                   maxlength="50" value="{{ old('product_code', $product->product_code) }}" required>
                        </div>

                        <div class="col-md-8">
                            <label for="product_name" class="wms-form-label">Nama Produk <span class="text-danger">*</span></label>
                            <input type="text" id="product_name" name="product_name" class="form-control wms-control"
                                   maxlength="150" value="{{ old('product_name', $product->product_name) }}" required>
                        </div>

                        <div class="col-md-6">
                            <label for="category_id" class="wms-form-label">Kategori Produk <span class="text-danger">*</span></label>
                            <select id="category_id" name="category_id" class="form-select wms-control" required>
                                <option value="">-- Pilih Kategori --</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ (string) old('category_id', $product->category_id) === (string) $category->id ? 'selected' : '' }}>
                                        {{ $category->category_code }} — {{ $category->category_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label for="cartons_per_pallet" class="wms-form-label">Standar Isi per Pallet (Karton) <span class="text-danger">*</span></label>
                            <input type="number" id="cartons_per_pallet" name="cartons_per_pallet" class="form-control wms-control tabular-nums"
                                   min="1" value="{{ old('cartons_per_pallet', $product->cartons_per_pallet ?? 120) }}" required>
                        </div>
                    </div>

                    <div class="wms-form-section-title">2. Keterangan Produk</div>
                    <div class="row g-3 mb-4">
                        <div class="col-12">
                            <label for="description" class="wms-form-label">Deskripsi / Catatan Produk (Opsional)</label>
                            <textarea id="description" name="description" rows="3" maxlength="500" class="form-control">{{ old('description', $product->description) }}</textarea>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 pt-3 border-top" style="border-color: var(--border-light) !important;">
                        <a href="{{ route('products.index') }}" class="btn btn-indo-outline">Batal</a>
                        <button type="submit" class="btn btn-indo-primary">
                            <i class="bi bi-check2-circle"></i> Perbarui Produk
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="wms-panel mb-3">
            <div class="wms-panel-header">
                <h2 class="wms-panel-title"><i class="bi bi-calculator"></i> Simulasi Konversi Stok</h2>
            </div>
            <div class="wms-panel-body">
                <div class="p-3 rounded-3" style="background: var(--indo-blue-tint); border: 1px solid #C5DDF3;">
                    <div class="small fw-bold" style="color: var(--indo-blue);">1 Pallet Penuh Setara</div>
                    <div class="fw-bold fs-3 tabular-nums mt-1" style="color: var(--indo-navy);" id="palletRatioPreview">
                        {{ number_format($product->cartons_per_pallet ?? 120) }} Karton
                    </div>
                    <div class="small text-muted mt-1" id="tenPalletPreview">
                        10 Pallet = {{ number_format(($product->cartons_per_pallet ?? 120) * 10) }} Karton
                    </div>
                </div>
            </div>
        </div>

        <div class="wms-info-box">
            <i class="bi bi-shield-check fs-5 text-primary flex-shrink-0"></i>
            <div>
                <div class="fw-bold mb-1">Catatan Pembaruan SKU</div>
                <p class="mb-0 small">Untuk mengaktifkan atau menonaktifkan SKU produk, gunakan tombol status pada tabel <strong>Data Produk</strong>.</p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(() => {
    const ratioInput = document.getElementById('cartons_per_pallet');
    const ratioOut = document.getElementById('palletRatioPreview');
    const tenOut = document.getElementById('tenPalletPreview');

    function updatePreview() {
        const qty = Math.max(0, parseInt(ratioInput.value || '0', 10));
        ratioOut.textContent = `${qty.toLocaleString('id-ID')} Karton`;
        tenOut.textContent = `10 Pallet = ${(qty * 10).toLocaleString('id-ID')} Karton`;
    }

    ratioInput.addEventListener('input', updatePreview);
    updatePreview();
})();
</script>
@endpush