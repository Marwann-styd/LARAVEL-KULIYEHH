@extends('layouts.app')

@section('title', 'Tambah Data Rak')
@section('page-heading', 'Tambah Rak Baru')

@section('content')
<div class="wms-page-header">
    <div>
        <h1 class="wms-page-title">Tambah Unit Rak Penyimpanan</h1>
        <p class="wms-page-subtitle">Konfigurasikan area penempatan, kode rak unik, dan jumlah tingkatan level rak.</p>
    </div>
    <a href="{{ route('racks.index') }}" class="btn btn-indo-outline">
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
                <h2 class="wms-panel-title"><i class="bi bi-bookshelf"></i> Konfigurasi Unit Rak</h2>
                <span class="wms-pill wms-pill-blue">Master Rak</span>
            </div>
            <div class="wms-panel-body">
                <form action="{{ route('racks.store') }}" method="POST">
                    @csrf

                    <div class="wms-form-section-title">1. Penempatan & Identitas Rak</div>
                    <div class="row g-3 mb-4">
                        <div class="col-12">
                            <label for="area_id" class="wms-form-label">Area Penyimpanan <span class="text-danger">*</span></label>
                            <select id="area_id" name="area_id" class="form-select wms-control" required>
                                <option value="">-- Pilih Gudang & Area --</option>
                                @foreach($areas as $area)
                                    <option value="{{ $area->id }}" {{ (string) old('area_id') === (string) $area->id ? 'selected' : '' }}>
                                        {{ $area->warehouse->warehouse_code ?? '' }} — {{ $area->warehouse->warehouse_name ?? '' }} | {{ $area->area_code }} ({{ $area->area_name }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label for="rack_code" class="wms-form-label">Kode Rak <span class="text-danger">*</span></label>
                            <input type="text" id="rack_code" name="rack_code" class="form-control wms-control"
                                   placeholder="Contoh: R-A01" maxlength="20" value="{{ old('rack_code') }}" required>
                            <span class="wms-help-text">Kode unik maksimal 20 karakter dalam satu area.</span>
                        </div>

                        <div class="col-md-6">
                            <label for="level_count" class="wms-form-label">Jumlah Tingkatan Level <span class="text-danger">*</span></label>
                            <select id="level_count" name="level_count" class="form-select wms-control" required>
                                <option value="3" {{ (string) old('level_count', '3') === '3' ? 'selected' : '' }}>3 Level (Kapasitas 9 Pallet)</option>
                                <option value="4" {{ (string) old('level_count') === '4' ? 'selected' : '' }}>4 Level (Kapasitas 12 Pallet)</option>
                            </select>
                            <span class="wms-help-text">Standar PT Indofood: 3 atau 4 level (@ 3 pallet/level).</span>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 pt-3 border-top" style="border-color: var(--border-light) !important;">
                        <a href="{{ route('racks.index') }}" class="btn btn-indo-outline">Batal</a>
                        <button type="submit" class="btn btn-indo-primary">
                            <i class="bi bi-check2-circle"></i> Simpan Rak
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="wms-panel mb-3">
            <div class="wms-panel-header">
                <h2 class="wms-panel-title"><i class="bi bi-calculator"></i> Simulasi Kapasitas Rak</h2>
            </div>
            <div class="wms-panel-body">
                <div class="p-3 rounded-3" style="background: var(--indo-blue-tint); border: 1px solid #C5DDF3;">
                    <div class="small fw-bold" style="color: var(--indo-blue);">Total Kapasitas Unit Rak</div>
                    <div class="fw-bold fs-3 tabular-nums mt-1" style="color: var(--indo-navy);" id="totalRackCapacityPreview">9 Pallet</div>
                    <div class="small text-muted mt-1" id="rackBreakdownPreview">3 Level &times; 3 Pallet / Level</div>
                </div>
            </div>
        </div>

        <div class="wms-info-box">
            <i class="bi bi-info-circle-fill fs-5 text-primary flex-shrink-0"></i>
            <div>
                <div class="fw-bold mb-1">Pembuatan Level Otomatis</div>
                <p class="mb-0 small">Saat unit rak disimpan, sistem akan otomatis membuatkan <strong>Level 1 hingga Level N</strong> dengan standar kapasitas <strong>3 pallet</strong> di setiap levelnya.</p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(() => {
    const levelSelect = document.getElementById('level_count');
    const totalOut = document.getElementById('totalRackCapacityPreview');
    const breakdownOut = document.getElementById('rackBreakdownPreview');

    function updateCapacity() {
        const lvl = parseInt(levelSelect.value || '3', 10);
        const cap = 3;
        totalOut.textContent = `${lvl * cap} Pallet`;
        breakdownOut.textContent = `${lvl} Level × ${cap} Pallet / Level`;
    }

    levelSelect.addEventListener('change', updateCapacity);
    updateCapacity();
})();
</script>
@endpush