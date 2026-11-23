@extends('layouts.app')

@section('title', 'Input Transaksi Penyimpanan')
@section('page-heading', 'Input Transaksi Baru')

@section('content')
{{-- PAGE HEADER --}}
<div class="wms-page-header">
    <div>
        <h1 class="wms-page-title">Input Transaksi Penyimpanan</h1>
        <p class="wms-page-subtitle">Catat pergerakan barang masuk (IN), keluar (OUT), atau perpindahan lokasi rak (MOVE).</p>
    </div>
    <a href="{{ route('transactions.index') }}" class="btn btn-indo-outline">
        <i class="bi bi-arrow-left"></i> Kembali ke Daftar
    </a>
</div>

@if($errors->any())
    <div class="alert alert-danger mb-4" style="border-radius: 8px; font-size: 13px;">
        <div class="fw-bold mb-1"><i class="bi bi-exclamation-circle-fill me-2"></i>Terjadi kesalahan input:</div>
        <ul class="mb-0 ps-3">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="row g-4">
    {{-- FORM UTAMA --}}
    <div class="col-lg-8">
        <div class="wms-panel">
            <div class="wms-panel-header">
                <h2 class="wms-panel-title"><i class="bi bi-pencil-square"></i> Formulir Mutasi Pallet</h2>
                <span class="wms-pill wms-pill-blue">Wajib Diisi Lengkap</span>
            </div>
            <div class="wms-panel-body">
                <form action="{{ route('transactions.store') }}" method="POST" id="transactionForm">
                    @csrf

                    <div class="wms-form-section-title">1. Jenis Transaksi & SKU Produk</div>
                    <div class="row g-3 mb-4">
                        <div class="col-md-5">
                            <label for="transaction_type" class="wms-form-label">Tipe Transaksi <span class="text-danger">*</span></label>
                            <select name="transaction_type" id="transaction_type" class="form-select wms-control" required>
                                <option value="IN" {{ old('transaction_type', 'IN') === 'IN' ? 'selected' : '' }}>IN — Barang Masuk ke Rak</option>
                                <option value="OUT" {{ old('transaction_type') === 'OUT' ? 'selected' : '' }}>OUT — Barang Keluar dari Rak</option>
                                <option value="MOVE" {{ old('transaction_type') === 'MOVE' ? 'selected' : '' }}>MOVE — Pindah Lokasi Rak</option>
                            </select>
                            <span class="wms-help-text" id="typeHelpText">Penempatan produk jadi baru ke dalam level rak gudang.</span>
                        </div>

                        <div class="col-md-7">
                            <label for="product_id" class="wms-form-label">Produk Finished Goods <span class="text-danger">*</span></label>
                            <select name="product_id" id="product_id" class="form-select wms-control" required>
                                <option value="">-- Pilih SKU Produk --</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}"
                                            data-cartons="{{ $product->cartons_per_pallet ?? 120 }}"
                                            {{ (string) old('product_id') === (string) $product->id ? 'selected' : '' }}>
                                        {{ $product->product_code }} — {{ $product->product_name }} ({{ $product->cartons_per_pallet ?? 120 }} ktn/plt)
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="wms-form-section-title">2. Koordinat Rak & Jumlah Pallet</div>
                    <div class="row g-3 mb-4">
                        {{-- LOKASI ASAL (OUT & MOVE) --}}
                        <div class="col-md-6" id="sourceWrapper">
                            <label for="from_rack_level_id" class="wms-form-label">Lokasi Asal (Rak / Level) <span class="text-danger">*</span></label>
                            <select name="from_rack_level_id" id="from_rack_level_id" class="form-select wms-control">
                                <option value="">-- Pilih Lokasi Asal --</option>
                                @foreach($rackLevels as $level)
                                    @php
                                        $occupied = $level->storagePlacement->pallet_quantity ?? 0;
                                        $skuLabel = $level->storagePlacement?->product?->product_code ?? 'Kosong';
                                    @endphp
                                    <option value="{{ $level->id }}" {{ (string) old('from_rack_level_id') === (string) $level->id ? 'selected' : '' }}>
                                        {{ $level->rack->area->warehouse->warehouse_code ?? '' }} | {{ $level->rack->rack_code }} - Lv.{{ $level->level_number }} (Isi: {{ $occupied }} Plt - {{ $skuLabel }})
                                    </option>
                                @endforeach
                            </select>
                            <span class="wms-help-text">Wajib diisi untuk transaksi OUT dan MOVE.</span>
                        </div>

                        {{-- LOKASI TUJUAN (IN & MOVE) --}}
                        <div class="col-md-6" id="destinationWrapper">
                            <label for="to_rack_level_id" class="wms-form-label">Lokasi Tujuan (Rak / Level) <span class="text-danger">*</span></label>
                            <select name="to_rack_level_id" id="to_rack_level_id" class="form-select wms-control">
                                <option value="">-- Pilih Lokasi Tujuan --</option>
                                @foreach($rackLevels as $level)
                                    @php
                                        $occupied = $level->storagePlacement->pallet_quantity ?? 0;
                                        $remaining = max(0, $level->capacity_pallets - $occupied);
                                    @endphp
                                    <option value="{{ $level->id }}" {{ (string) old('to_rack_level_id') === (string) $level->id ? 'selected' : '' }}>
                                        {{ $level->rack->area->warehouse->warehouse_code ?? '' }} | {{ $level->rack->rack_code }} - Lv.{{ $level->level_number }} (Sisa: {{ $remaining }}/{{ $level->capacity_pallets }} Plt)
                                    </option>
                                @endforeach
                            </select>
                            <span class="wms-help-text">Wajib diisi untuk transaksi IN dan MOVE.</span>
                        </div>

                        <div class="col-md-6">
                            <label for="pallet_quantity" class="wms-form-label">Jumlah Pallet <span class="text-danger">*</span></label>
                            <input type="number" name="pallet_quantity" id="pallet_quantity" class="form-control wms-control tabular-nums"
                                   min="1" value="{{ old('pallet_quantity', 1) }}" required>
                        </div>

                        <div class="col-md-6">
                            <label for="transaction_date" class="wms-form-label">Tanggal & Waktu Transaksi</label>
                            <input type="datetime-local" name="transaction_date" id="transaction_date" class="form-control wms-control tabular-nums"
                                   value="{{ old('transaction_date', now()->format('Y-m-d\TH:i')) }}">
                        </div>

                        <div class="col-12">
                            <label for="notes" class="wms-form-label">Catatan Operasional (Opsional)</label>
                            <textarea name="description" id="notes" rows="2" class="form-control"
                                      placeholder="Nomor surat jalan, batch produksi, atau keterangan shift...">{{ old('notes') }}</textarea>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 pt-3 border-top" style="border-color: var(--border-light) !important;">
                        <a href="{{ route('transactions.index') }}" class="btn btn-indo-outline">Batal</a>
                        <button type="submit" class="btn btn-indo-primary">
                            <i class="bi bi-check2-circle"></i> Simpan Transaksi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- PANEL RINGKASAN KALKULASI --}}
    <div class="col-lg-4">
        <div class="wms-panel mb-3">
            <div class="wms-panel-header">
                <h2 class="wms-panel-title"><i class="bi bi-calculator"></i> Estimasi Muatan</h2>
            </div>
            <div class="wms-panel-body">
                <div class="p-3 rounded-3 mb-3" style="background: var(--bg-subtle); border: 1px solid var(--border-light);">
                    <div class="text-muted small fw-semibold">Standar Konversi SKU</div>
                    <div class="fw-bold fs-5 tabular-nums mt-1" style="color: var(--indo-navy);" id="previewRatio">- Karton / Pallet</div>
                </div>
                <div class="p-3 rounded-3" style="background: var(--indo-blue-tint); border: 1px solid #C5DDF3;">
                    <div class="small fw-bold" style="color: var(--indo-blue);">Total Estimasi Karton</div>
                    <div class="fw-bold fs-3 tabular-nums mt-1" style="color: var(--indo-navy);" id="previewTotalCartons">0 Karton</div>
                </div>
            </div>
        </div>

        <div class="wms-info-box">
            <i class="bi bi-info-circle-fill fs-5 text-primary flex-shrink-0"></i>
            <div>
                <div class="fw-bold mb-1">Panduan Validasi Sistem</div>
                <ul class="mb-0 ps-3 small">
                    <li><strong>IN:</strong> Kapasitas tujuan tidak boleh melebihi batas level rak.</li>
                    <li><strong>OUT:</strong> Stok pallet di lokasi asal harus mencukupi.</li>
                    <li><strong>MOVE:</strong> Lokasi asal dan tujuan harus berbeda.</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(() => {
    const typeSelect = document.getElementById('transaction_type');
    const sourceWrap = document.getElementById('sourceWrapper');
    const destWrap = document.getElementById('destinationWrapper');
    const sourceSelect = document.getElementById('from_rack_level_id');
    const destSelect = document.getElementById('to_rack_level_id');
    const typeHelp = document.getElementById('typeHelpText');

    const productSelect = document.getElementById('product_id');
    const palletInput = document.getElementById('pallet_quantity');
    const previewRatio = document.getElementById('previewRatio');
    const previewTotal = document.getElementById('previewTotalCartons');

    const helpMap = {
        IN: 'Penempatan produk jadi baru ke dalam level rak gudang.',
        OUT: 'Pengeluaran stok produk jadi dari level rak untuk pengiriman.',
        MOVE: 'Pemindahan stok antar level rak di dalam area/gudang.'
    };

    function syncFields() {
        const val = typeSelect.value;
        typeHelp.textContent = helpMap[val] || '';

        if (val === 'IN') {
            sourceWrap.style.display = 'none';
            sourceSelect.required = false;
            destWrap.style.display = '';
            destSelect.required = true;
        } else if (val === 'OUT') {
            sourceWrap.style.display = '';
            sourceSelect.required = true;
            destWrap.style.display = 'none';
            destSelect.required = false;
        } else {
            sourceWrap.style.display = '';
            sourceSelect.required = true;
            destWrap.style.display = '';
            destSelect.required = true;
        }
    }

    function updateEstimate() {
        const opt = productSelect.options[productSelect.selectedIndex];
        const ratio = opt && opt.dataset.cartons ? parseInt(opt.dataset.cartons, 10) : 0;
        const pallets = Math.max(0, parseInt(palletInput.value || '0', 10));

        previewRatio.textContent = ratio > 0 ? `${ratio.toLocaleString('id-ID')} Karton / Pallet` : '- Karton / Pallet';
        previewTotal.textContent = `${(ratio * pallets).toLocaleString('id-ID')} Karton`;
    }

    typeSelect.addEventListener('change', syncFields);
    productSelect.addEventListener('change', updateEstimate);
    palletInput.addEventListener('input', updateEstimate);

    syncFields();
    updateEstimate();
})();
</script>
@endpush
