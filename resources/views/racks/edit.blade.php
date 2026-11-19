@extends('layouts.app')

@section('title', 'Edit Data Rak')
@section('page-heading', 'Edit Data Rak')

@section('content')
<div class="wms-page-header">
    <div>
        <h1 class="wms-page-title">Edit Unit Rak Penyimpanan</h1>
        <p class="wms-page-subtitle">Perbarui kode unit rak pada area penyimpanan terkait.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('racks.levels', $rack) }}" class="btn btn-indo-outline">
            <i class="bi bi-layers"></i> Lihat Level
        </a>
        <a href="{{ route('racks.index') }}" class="btn btn-indo-outline">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>
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
                    <h2 class="wms-panel-title"><i class="bi bi-pencil-square"></i> Edit Kode Rak</h2>
                    <span class="wms-code-badge">{{ $rack->rack_code }}</span>
                </div>
                <span class="wms-pill wms-pill-blue tabular-nums">{{ $rack->level_count ?? $rack->rackLevels->count() }} Level</span>
            </div>
            <div class="wms-panel-body">
                <form action="{{ route('racks.update', $rack) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="wms-form-section-title">Lokasi & Identitas Rak</div>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="wms-form-label">Unit Gudang</label>
                            <input type="text" class="form-control wms-control bg-light"
                                   value="{{ $rack->area->warehouse->warehouse_code ?? '-' }} — {{ $rack->area->warehouse->warehouse_name ?? '-' }}" disabled>
                        </div>

                        <div class="col-md-6">
                            <label class="wms-form-label">Area Penyimpanan</label>
                            <input type="text" class="form-control wms-control bg-light"
                                   value="{{ $rack->area->area_code ?? '-' }} — {{ $rack->area->area_name ?? '-' }}" disabled>
                        </div>

                        <div class="col-md-6">
                            <label for="rack_code" class="wms-form-label">Kode Rak <span class="text-danger">*</span></label>
                            <input type="text" id="rack_code" name="rack_code" class="form-control wms-control"
                                   maxlength="20" value="{{ old('rack_code', $rack->rack_code) }}" required autofocus>
                            <span class="wms-help-text">Kode unik maksimal 20 karakter di area ini.</span>
                        </div>

                        <div class="col-md-6">
                            <label class="wms-form-label">Struktur Level</label>
                            <input type="text" class="form-control wms-control bg-light"
                                   value="{{ $rack->level_count ?? $rack->rackLevels->count() }} Level ({{ ($rack->level_count ?? $rack->rackLevels->count()) * 3 }} Pallet)" disabled>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 pt-3 border-top" style="border-color: var(--border-light) !important;">
                        <a href="{{ route('racks.index') }}" class="btn btn-indo-outline">Batal</a>
                        <button type="submit" class="btn btn-indo-primary">
                            <i class="bi bi-check2-circle"></i> Perbarui Rak
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
                <div class="fw-bold mb-1">Informasi Pengubahan</div>
                <p class="mb-2 small">Lokasi area dan jumlah tingkatan level dikunci demi menjaga integritas riwayat penempatan pallet.</p>
                <p class="mb-0 small text-muted">Untuk mengaktifkan atau menonaktifkan rak, gunakan tombol status pada halaman <strong>Data Rak</strong>.</p>
            </div>
        </div>
    </div>
</div>
@endsection