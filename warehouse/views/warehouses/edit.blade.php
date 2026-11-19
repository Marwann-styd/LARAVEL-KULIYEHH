@extends('layouts.app')

@section('title', 'Edit Data Gudang')
@section('page-heading', 'Edit Data Gudang')

@section('content')
<div class="wms-page-header">
    <div>
        <h1 class="wms-page-title">Edit Data Gudang</h1>
        <p class="wms-page-subtitle">Perbarui identitas, lokasi pabrik, atau status operasional unit gudang.</p>
    </div>
    <a href="{{ route('warehouses.index') }}" class="btn btn-indo-outline">
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
                    <h2 class="wms-panel-title"><i class="bi bi-pencil-square"></i> Edit Unit Gudang</h2>
                    <span class="wms-code-badge">{{ $warehouse->warehouse_code }}</span>
                </div>
                <span class="wms-pill wms-pill-slate">ID #{{ $warehouse->id }}</span>
            </div>
            <div class="wms-panel-body">
                <form action="{{ route('warehouses.update', $warehouse) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="wms-form-section-title">Informasi Dasar Gudang</div>
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label for="warehouse_code" class="wms-form-label">Kode Gudang <span class="text-danger">*</span></label>
                            <input type="text" id="warehouse_code" name="warehouse_code" class="form-control wms-control"
                                   value="{{ old('warehouse_code', $warehouse->warehouse_code) }}" required>
                        </div>

                        <div class="col-md-8">
                            <label for="warehouse_name" class="wms-form-label">Nama Gudang <span class="text-danger">*</span></label>
                            <input type="text" id="warehouse_name" name="warehouse_name" class="form-control wms-control"
                                   value="{{ old('warehouse_name', $warehouse->warehouse_name) }}" required>
                        </div>

                        <div class="col-12">
                            <label for="location" class="wms-form-label">Lokasi / Plant Pabrik</label>
                            <input type="text" id="location" name="location" class="form-control wms-control"
                                   value="{{ old('location', $warehouse->location) }}">
                        </div>

                        <div class="col-12">
                            <label for="description" class="wms-form-label">Keterangan (Opsional)</label>
                            <textarea id="description" name="description" rows="3" class="form-control">{{ old('description', $warehouse->description) }}</textarea>
                        </div>

                        <div class="col-12">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" id="is_active" name="is_active" value="1" {{ old('is_active', $warehouse->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="is_active">Status Gudang Aktif</label>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 pt-3 border-top" style="border-color: var(--border-light) !important;">
                        <a href="{{ route('warehouses.index') }}" class="btn btn-indo-outline">Batal</a>
                        <button type="submit" class="btn btn-indo-primary">
                            <i class="bi bi-check2-circle"></i> Perbarui Gudang
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
                <div class="fw-bold mb-1">Catatan Perubahan Status</div>
                <p class="mb-0 small">Menonaktifkan gudang akan menyembunyikannya dari opsi pemilihan operasional baru, namun riwayat transaksi dan penempatan lama tetap tersimpan aman.</p>
            </div>
        </div>
    </div>
</div>
@endsection
