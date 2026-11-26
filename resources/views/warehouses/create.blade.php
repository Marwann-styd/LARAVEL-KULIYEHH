@extends('layouts.app')

@section('title', 'Tambah Data Gudang')
@section('page-heading', 'Tambah Gudang Baru')

@section('content')
    <div class="wms-page-header">
        <div>
            <h1 class="wms-page-title">Tambah Data Gudang</h1>
            <p class="wms-page-subtitle">Daftarkan unit gudang Finished Goods baru ke dalam sistem monitoring.</p>
        </div>
        <a href="{{ route('warehouses.index') }}" class="btn btn-indo-outline">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger mb-4" style="border-radius: 8px; font-size: 13px;">
            <div class="fw-bold mb-1"><i class="bi bi-exclamation-circle-fill me-2"></i>Periksa kembali isian formulir:</div>
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="wms-panel">
                <div class="wms-panel-header">
                    <h2 class="wms-panel-title"><i class="bi bi-building-add"></i> Identitas Unit Gudang</h2>
                    <span class="wms-pill wms-pill-blue">Master Gudang</span>
                </div>
                <div class="wms-panel-body">
                    <form action="{{ route('warehouses.store') }}" method="POST">
                        @csrf

                        <div class="wms-form-section-title">Informasi Dasar Gudang</div>
                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <label for="warehouse_code" class="wms-form-label">Kode Gudang <span
                                        class="text-danger">*</span></label>
                                <input type="text" id="warehouse_code" name="warehouse_code"
                                    class="form-control wms-control" placeholder="Contoh: FG-01"
                                    value="{{ old('warehouse_code') }}" required autofocus>
                                <span class="wms-help-text">Gunakan kode unik singkat (contoh: FG-01).</span>
                            </div>

                            <div class="col-md-8">
                                <label for="warehouse_name" class="wms-form-label">Nama Gudang <span
                                        class="text-danger">*</span></label>
                                <input type="text" id="warehouse_name" name="warehouse_name"
                                    class="form-control wms-control" placeholder="Contoh: Gudang Finished Goods Utama"
                                    value="{{ old('warehouse_name') }}" required>
                            </div>

                            <div class="col-12">
                                <label for="location" class="wms-form-label">Lokasi / Plant</label>
                                <input type="text" id="location" name="location" class="form-control wms-control"
                                    maxlength="150" placeholder="Contoh: Plant Bandung — Divisi Noodle (Padalarang)"
                                    value="{{ old('location') }}">
                            </div>

                            <div class="col-12">
                                <label for="location" class="wms-form-label">Lokasi / Plant Pabrik</label>
                                <input type="text" id="location" name="location" class="form-control wms-control"
                                    placeholder="Contoh: Plant Bandung &mdash; Area Logistik Timur"
                                    value="{{ old('location') }}">
                            </div>

                            <div class="col-12">
                                <label for="description" class="wms-form-label">Keterangan (Opsional)</label>
                                <textarea id="description" name="description" rows="3" class="form-control"
                                    placeholder="Catatan kapasitas umum atau jenis produk yang disimpan...">{{ old('description') }}</textarea>
                            </div>

                            <div class="col-12">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" role="switch" id="is_active"
                                        name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                    <label class="form-check-label fw-semibold" for="is_active">Aktifkan unit gudang untuk
                                        operasional</label>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 pt-3 border-top"
                            style="border-color: var(--border-light) !important;">
                            <a href="{{ route('warehouses.index') }}" class="btn btn-indo-outline">Batal</a>
                            <button type="submit" class="btn btn-indo-primary">
                                <i class="bi bi-check2-circle"></i> Simpan Gudang
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
                    <div class="fw-bold mb-1">Hirarki Struktur Gudang</div>
                    <p class="mb-2 small">Setelah unit gudang dibuat, lanjutkan konfigurasi struktur penyimpanan secara
                        berurutan:</p>
                    <ol class="mb-0 ps-3 small">
                        <li><strong>Data Gudang</strong> (Unit bangunan/plant)</li>
                        <li><strong>Data Area</strong> (Zona penyimpanan di dalam gudang)</li>
                        <li><strong>Data Rak & Level</strong> (Slot kapasitas pallet)</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
@endsection
