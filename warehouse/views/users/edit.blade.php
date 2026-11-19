@extends('layouts.app')

@section('title', 'Edit Data Pengguna')
@section('page-heading', 'Edit Data Pengguna')

@section('content')
@php
    $isSelf = auth()->id() === $user->id;
@endphp

<div class="wms-page-header">
    <div>
        <h1 class="wms-page-title">Edit Akun & Hak Akses Pengguna</h1>
        <p class="wms-page-subtitle">Perbarui profil akun, reset kata sandi, atau ubah penugasan unit gudang personel.</p>
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
                <div class="d-flex align-items-center gap-2">
                    <h2 class="wms-panel-title"><i class="bi bi-pencil-square"></i> Edit Profil Pengguna</h2>
                    @if($isSelf)
                        <span class="wms-pill wms-pill-gold">Akun Anda</span>
                    @endif
                </div>
                <span class="wms-pill wms-pill-slate">ID #{{ $user->id }}</span>
            </div>
            <div class="wms-panel-body">
                <form action="{{ route('users.update', $user) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="wms-form-section-title">1. Informasi Profil Akun</div>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="name" class="wms-form-label">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" id="name" name="name" class="form-control wms-control"
                                   value="{{ old('name', $user->name) }}" required>
                        </div>

                        <div class="col-md-6">
                            <label for="email" class="wms-form-label">Alamat Email Login <span class="text-danger">*</span></label>
                            <input type="email" id="email" name="email" class="form-control wms-control"
                                   value="{{ old('email', $user->email) }}" required>
                        </div>
                    </div>

                    <div class="wms-form-section-title">2. Pembaruan Kata Sandi (Opsional)</div>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="password" class="wms-form-label">Password Baru</label>
                            <input type="password" id="password" name="password" class="form-control wms-control"
                                   placeholder="Kosongkan jika tidak diubah">
                            <span class="wms-help-text">Isi hanya jika ingin mengganti kata sandi pengguna (min. 8 karakter).</span>
                        </div>

                        <div class="col-md-6">
                            <label for="password_confirmation" class="wms-form-label">Konfirmasi Password Baru</label>
                            <input type="password" id="password_confirmation" name="password_confirmation" class="form-control wms-control"
                                   placeholder="Ulangi password baru">
                        </div>
                    </div>

                    <div class="wms-form-section-title">3. Hak Akses & Penugasan Gudang</div>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="role" class="wms-form-label">Role Akses Sistem <span class="text-danger">*</span></label>
                            <select id="role" name="role" class="form-select wms-control" required {{ $isSelf ? 'disabled' : '' }}>
                                <option value="super_admin" {{ old('role', $user->role) === 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                                <option value="admin_gudang" {{ old('role', $user->role) === 'admin_gudang' ? 'selected' : '' }}>Admin Gudang</option>
                                <option value="supervisor" {{ old('role', $user->role) === 'supervisor' ? 'selected' : '' }}>Supervisor</option>
                                <option value="petugas_fg" {{ old('role', $user->role) === 'petugas_fg' ? 'selected' : '' }}>Petugas FG</option>
                            </select>
                            @if($isSelf)
                                <input type="hidden" name="role" value="{{ $user->role }}">
                                <span class="wms-help-text text-warning">Role akun yang sedang login tidak dapat diubah sendiri.</span>
                            @endif
                        </div>

                        <div class="col-md-6">
                            <label for="warehouse_id" class="wms-form-label">Penugasan Unit Gudang</label>
                            <select id="warehouse_id" name="warehouse_id" class="form-select wms-control">
                                <option value="">-- Semua Gudang / Belum Ditugaskan --</option>
                                @foreach($warehouses as $warehouse)
                                    <option value="{{ $warehouse->id }}" {{ (string) old('warehouse_id', $user->warehouse_id) === (string) $warehouse->id ? 'selected' : '' }}>
                                        {{ $warehouse->warehouse_code }} — {{ $warehouse->warehouse_name }}
                                    </option>
                                @endforeach
                            </select>
                            <span class="wms-help-text" id="warehouseHint">Pilih unit gudang tempat personel ini bertugas.</span>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 pt-3 border-top" style="border-color: var(--border-light) !important;">
                        <a href="{{ route('users.index') }}" class="btn btn-indo-outline">Batal</a>
                        <button type="submit" class="btn btn-indo-primary">
                            <i class="bi bi-check2-circle"></i> Perbarui Pengguna
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
                <div class="fw-bold mb-1">Keamanan Perubahan Akun</div>
                <p class="mb-2 small">Riwayat transaksi barang masuk/keluar yang pernah dicatat oleh pengguna ini akan tetap mempertahankan nama personel secara akurat.</p>
                <p class="mb-0 small text-muted">Jika kata sandi tidak perlu diubah, biarkan kolom password tetap kosong.</p>
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