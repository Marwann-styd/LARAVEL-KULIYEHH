@extends('layouts.app')

@section('title', 'Tambah Pengguna Baru')
@section('page-heading', 'Tambah Pengguna Baru')

@section('content')
<div class="wms-page-header">
    <div>
        <h1 class="wms-page-title">Tambah Akun Pengguna Baru</h1>
        <p class="wms-page-subtitle">Buat akun login baru untuk personel operasional gudang beserta penetapan hak aksesnya.</p>
    </div>
    <a href="{{ route('users.index') }}" class="btn btn-indo-outline">
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
                <h2 class="wms-panel-title"><i class="bi bi-person-plus-fill"></i> Identitas & Kredensial Pengguna</h2>
                <span class="wms-pill wms-pill-blue">Manajemen Akun</span>
            </div>
            <div class="wms-panel-body">
                <form action="{{ route('users.store') }}" method="POST">
                    @csrf

                    <div class="wms-form-section-title">1. Informasi Profil Akun</div>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="name" class="wms-form-label">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" id="name" name="name" class="form-control wms-control"
                                   placeholder="Contoh: Budi Santoso" value="{{ old('name') }}" required autofocus>
                        </div>

                        <div class="col-md-6">
                            <label for="email" class="wms-form-label">Alamat Email Login <span class="text-danger">*</span></label>
                            <input type="email" id="email" name="email" class="form-control wms-control"
                                   placeholder="Contoh: budi.santoso@indofood.co.id" value="{{ old('email') }}" required>
                        </div>
                    </div>

                    <div class="wms-form-section-title">2. Keamanan Kata Sandi</div>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="password" class="wms-form-label">Password <span class="text-danger">*</span></label>
                            <input type="password" id="password" name="password" class="form-control wms-control"
                                   placeholder="Minimal 8 karakter" required>
                            <span class="wms-help-text">Gunakan kombinasi huruf dan angka minimal 8 karakter.</span>
                        </div>

                        <div class="col-md-6">
                            <label for="password_confirmation" class="wms-form-label">Konfirmasi Password <span class="text-danger">*</span></label>
                            <input type="password" id="password_confirmation" name="password_confirmation" class="form-control wms-control"
                                   placeholder="Ulangi password yang sama" required>
                        </div>
                    </div>

                    <div class="wms-form-section-title">3. Hak Akses & Penugasan Gudang</div>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="role" class="wms-form-label">Role Akses Sistem <span class="text-danger">*</span></label>
                            <select id="role" name="role" class="form-select wms-control" required>
                                <option value="">-- Pilih Role Akses --</option>
                                <option value="super_admin" {{ old('role') === 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                                <option value="admin_gudang" {{ old('role') === 'admin_gudang' ? 'selected' : '' }}>Admin Gudang</option>
                                <option value="supervisor" {{ old('role') === 'supervisor' ? 'selected' : '' }}>Supervisor</option>
                                <option value="petugas_fg" {{ old('role') === 'petugas_fg' ? 'selected' : '' }}>Petugas FG</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label for="warehouse_id" class="wms-form-label">Penugasan Unit Gudang</label>
                            <select id="warehouse_id" name="warehouse_id" class="form-select wms-control">
                                <option value="">-- Semua Gudang / Belum Ditugaskan --</option>
                                @foreach($warehouses as $warehouse)
                                    <option value="{{ $warehouse->id }}" {{ (string) old('warehouse_id') === (string) $warehouse->id ? 'selected' : '' }}>
                                        {{ $warehouse->warehouse_code }} — {{ $warehouse->warehouse_name }}
                                    </option>
                                @endforeach
                            </select>
                            <span class="wms-help-text" id="warehouseHint">Wajib dipilih untuk Admin Gudang, Supervisor, dan Petugas FG.</span>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 pt-3 border-top" style="border-color: var(--border-light) !important;">
                        <a href="{{ route('users.index') }}" class="btn btn-indo-outline">Batal</a>
                        <button type="submit" class="btn btn-indo-primary">
                            <i class="bi bi-check2-circle"></i> Simpan Pengguna
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="wms-panel mb-3">
            <div class="wms-panel-header">
                <h2 class="wms-panel-title"><i class="bi bi-shield-lock"></i> Matriks Hak Akses Role</h2>
            </div>
            <div class="wms-panel-body d-flex flex-column gap-3">
                <div class="p-2.5 rounded-3 border" style="background: var(--indo-red-tint); border-color: #F5C2C7 !important;">
                    <div class="fw-bold small" style="color: var(--indo-red);"><i class="bi bi-shield-lock-fill me-1"></i> Super Admin</div>
                    <p class="mb-0 small text-muted mt-1">Akses penuh seluruh unit gudang, konfigurasi Master Data, Laporan, dan Manajemen Akun Pengguna.</p>
                </div>

                <div class="p-2.5 rounded-3 border" style="background: var(--indo-blue-tint); border-color: #C5DDF3 !important;">
                    <div class="fw-bold small" style="color: var(--indo-blue);"><i class="bi bi-person-badge-fill me-1"></i> Admin Gudang</div>
                    <p class="mb-0 small text-muted mt-1">Mengawasi stok lokasi, mencatat transaksi harian, serta mencetak laporan pada unit gudang yang ditugaskan.</p>
                </div>

                <div class="p-2.5 rounded-3 border" style="background: var(--indo-gold-tint); border-color: #F3E5AB !important;">
                    <div class="fw-bold small" style="color: #8A6D1B;"><i class="bi bi-eye-fill me-1"></i> Supervisor</div>
                    <p class="mb-0 small text-muted mt-1">Akses pemantauan (read-only) untuk Dashboard, Monitoring Kapasitas, Lokasi Produk, dan Laporan.</p>
                </div>

                <div class="p-2.5 rounded-3 border" style="background: var(--indo-green-tint); border-color: #BADBCC !important;">
                    <div class="fw-bold small" style="color: var(--indo-green);"><i class="bi bi-person-workspace me-1"></i> Petugas FG</div>
                    <p class="mb-0 small text-muted mt-1">Fokus pada pencatatan operasional barang masuk (IN), barang keluar (OUT), dan pemindahan lokasi rak (MOVE).</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(() => {
    const roleSelect = document.getElementById('role');
    const warehouseSelect = document.getElementById('warehouse_id');
    const warehouseHint = document.getElementById('warehouseHint');

    function syncWarehouseField() {
        if (roleSelect.value === 'super_admin') {
            warehouseSelect.value = '';
            warehouseSelect.disabled = true;
            warehouseHint.textContent = 'Super Admin otomatis memiliki akses global ke seluruh unit gudang.';
        } else {
            warehouseSelect.disabled = false;
            warehouseHint.textContent = 'Wajib dipilih untuk Admin Gudang, Supervisor, dan Petugas FG.';
        }
    }

    roleSelect.addEventListener('change', syncWarehouseField);
    syncWarehouseField();
})();
</script>
@endpush