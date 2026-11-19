@extends('layouts.app')

@section('title', 'Tambah Data Area')
@section('page-heading', 'Tambah Area Baru')

@section('content')
<div class="wms-page-header">
    <div>
        <h1 class="wms-page-title">Tambah Data Area Penyimpanan</h1>
        <p class="wms-page-subtitle">Buat zona penyimpanan baru dan hubungkan ke unit gudang induknya.</p>
    </div>
    <a href="{{ route('areas.index') }}" class="btn btn-indo-outline">
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
                <h2 class="wms-panel-title"><i class="bi bi-map"></i> Identitas Area Penyimpanan</h2>
                <span class="wms-pill wms-pill-blue">Master Area</span>
            </div>
            <div class="wms-panel-body">
                <form action="{{ route('areas.store') }}" method="POST">
                    @csrf

                    <div class="wms-form-section-title">Informasi Zona Penyimpanan</div>
                    <div class="row g-3 mb-4">
                        <div class="col-12">
                            <label for="warehouse_id" class="wms-form-label">Unit Gudang Induk <span class="text-danger">*</span></label>
                            <select id="warehouse_id" name="warehouse_id" class="form-select wms-control" required>
                                <option value="">-- Pilih Unit Gudang --</option>
                                @foreach($warehouses as $warehouse)
                                    <option value="{{ $warehouse->id }}" {{ (string) old('warehouse_id') === (string) $warehouse->id ? 'selected' : '' }}>
                                        {{ $warehouse->warehouse_code }} â€” {{ $warehouse->warehouse_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label for="area_code" class="wms-form-label">Kode Area <span class="text-danger">*</span></label>
                            <input type="text" id="area_code" name="area_code" class="form-control wms-control"
                                   placeholder="Contoh: AREA-A" value="{{ old('area_code') }}" required>
                            <span class="wms-help-text">Kode unik zona (contoh: AREA-A, ZONA-01).</span>
                        </div>

                        <div class="col-md-8">
                            <label for="area_name" class="wms-form-label">Nama Area <span class="text-danger">*</span></label>
                            <input type="text" id="area_name" name="area_name" class="form-control wms-control"
                                   placeholder="Contoh: Zona Penyimpanan Mie Instan" value="{{ old('area_name') }}" required>
                        </div>

                        <div class="col-12">
                            <label for="description" class="wms-form-label">Keterangan (Opsional)</label>
                            <textarea id="description" name="description" rows="3" class="form-control"
                                      placeholder="Catatan karakteristik zona, suhu ruangan, atau akses forklift...">{{ old('description') }}</textarea>
                        </div>

                        <div class="col-12">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="is_active">Aktifkan area untuk penempatan rak</label>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 pt-3 border-top" style="border-color: var(--border-light) !important;">
                        <a href="{{ route('areas.index') }}" class="btn btn-indo-outline">Batal</a>
                        <button type="submit" class="btn btn-indo-primary">
                            <i class="bi bi-check2-circle"></i> Simpan Area
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="wms-info-box">
            <i class="bi bi-info-circle-fill fs-5 text-primary flex-shrink-0"></i>
            <div>
                <div class="fw-bold mb-1">Panduan Pembuatan Area</div>
                <p class="mb-0 small">Setiap area wajib terhubung ke salah satu unit gudang aktif. Setelah area dibuat, Anda dapat menambahkan unit rak beserta jumlah levelnya pada menu <strong>Data Rak</strong>.</p>
            </div>
        </div>
    </div>
</div>
@endsection
