@extends('layouts.app')

@section('title', 'Edit Data Area')
@section('page-heading', 'Edit Data Area')

@section('content')
<div class="wms-page-header">
    <div>
        <h1 class="wms-page-title">Edit Data Area Penyimpanan</h1>
        <p class="wms-page-subtitle">Perbarui unit gudang induk, kode zona, nama area, atau status operasionalnya.</p>
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
                <div class="d-flex align-items-center gap-2">
                    <h2 class="wms-panel-title"><i class="bi bi-pencil-square"></i> Edit Area Penyimpanan</h2>
                    <span class="wms-code-badge">{{ $area->area_code }}</span>
                </div>
                <span class="wms-pill wms-pill-slate">ID #{{ $area->id }}</span>
            </div>
            <div class="wms-panel-body">
                <form action="{{ route('areas.update', $area) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="wms-form-section-title">Informasi Zona Penyimpanan</div>
                    <div class="row g-3 mb-4">
                        <div class="col-12">
                            <label for="warehouse_id" class="wms-form-label">Unit Gudang Induk <span class="text-danger">*</span></label>
                            <select id="warehouse_id" name="warehouse_id" class="form-select wms-control" required>
                                <option value="">-- Pilih Unit Gudang --</option>
                                @foreach($warehouses as $warehouse)
                                    <option value="{{ $warehouse->id }}" {{ (string) old('warehouse_id', $area->warehouse_id) === (string) $warehouse->id ? 'selected' : '' }}>
                                        {{ $warehouse->warehouse_code }} â€” {{ $warehouse->warehouse_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label for="area_code" class="wms-form-label">Kode Area <span class="text-danger">*</span></label>
                            <input type="text" id="area_code" name="area_code" class="form-control wms-control"
                                   value="{{ old('area_code', $area->area_code) }}" required>
                        </div>

                        <div class="col-md-8">
                            <label for="area_name" class="wms-form-label">Nama Area <span class="text-danger">*</span></label>
                            <input type="text" id="area_name" name="area_name" class="form-control wms-control"
                                   value="{{ old('area_name', $area->area_name) }}" required>
                        </div>

                        <div class="col-12">
                            <label for="description" class="wms-form-label">Keterangan (Opsional)</label>
                            <textarea id="description" name="description" rows="3" class="form-control">{{ old('description', $area->description) }}</textarea>
                        </div>

                        <div class="col-12">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" id="is_active" name="is_active" value="1" {{ old('is_active', $area->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="is_active">Status Area Aktif</label>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 pt-3 border-top" style="border-color: var(--border-light) !important;">
                        <a href="{{ route('areas.index') }}" class="btn btn-indo-outline">Batal</a>
                        <button type="submit" class="btn btn-indo-primary">
                            <i class="bi bi-check2-circle"></i> Perbarui Area
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
                <div class="fw-bold mb-1">Catatan Pembaruan Area</div>
                <p class="mb-0 small">Perubahan status aktif area akan memengaruhi ketersediaan seluruh unit rak yang berada di bawah zona penyimpanan ini.</p>
            </div>
        </div>
    </div>
</div>
@endsection
