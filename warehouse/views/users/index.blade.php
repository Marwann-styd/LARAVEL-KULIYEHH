@extends('layouts.app')

@section('title', 'Manajemen Pengguna')
@section('page-heading', 'Manajemen Pengguna')

@section('content')
@php
    $superAdminCount = $users->getCollection()->where('role', 'super_admin')->count();
    $adminGudangCount = $users->getCollection()->where('role', 'admin_gudang')->count();
    $supervisorCount = $users->getCollection()->where('role', 'supervisor')->count();
    $petugasCount = $users->getCollection()->where('role', 'petugas_fg')->count();
@endphp

{{-- PAGE HEADER --}}
<div class="wms-page-header">
    <div>
        <h1 class="wms-page-title">Manajemen Akun & Hak Akses</h1>
        <p class="wms-page-subtitle">Pengelolaan pengguna sistem WMS PT Indofood beserta penetapan role dan unit gudang tugas.</p>
    </div>
    <a href="{{ route('users.create') }}" class="btn btn-indo-primary">
        <i class="bi bi-person-plus-fill"></i> Tambah Pengguna
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert" style="border-radius: 8px; font-size: 13px;">
        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-close="alert" aria-label="Tutup"></button>
    </div>
@endif

@if(session('error') || $errors->any())
    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert" style="border-radius: 8px; font-size: 13px;">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') ?? $errors->first() }}
        <button type="button" class="btn-close" data-bs-close="alert" aria-label="Tutup"></button>
    </div>
@endif

{{-- RINGKASAN DATA PENGGUNA --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-xl-3">
        <div class="wms-kpi-card" style="--kpi-accent: var(--indo-navy); --kpi-tint: #E8EEF5;">
            <div class="wms-kpi-top">
                <span class="wms-kpi-label">Total Akun Terdaftar</span>
                <span class="wms-kpi-icon"><i class="bi bi-people-fill"></i></span>
            </div>
            <div class="wms-kpi-value">{{ number_format($users->total()) }}</div>
            <div class="wms-kpi-note">Seluruh pengguna di sistem</div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="wms-kpi-card" style="--kpi-accent: var(--indo-red); --kpi-tint: var(--indo-red-tint);">
            <div class="wms-kpi-top">
                <span class="wms-kpi-label">Super Admin & Admin</span>
                <span class="wms-kpi-icon"><i class="bi bi-shield-lock-fill"></i></span>
            </div>
            <div class="wms-kpi-value" style="color: var(--indo-red);">{{ number_format($superAdminCount + $adminGudangCount) }}</div>
            <div class="wms-kpi-note">{{ $superAdminCount }} Super Admin &bull; {{ $adminGudangCount }} Admin Gudang</div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="wms-kpi-card" style="--kpi-accent: var(--indo-blue); --kpi-tint: var(--indo-blue-tint);">
            <div class="wms-kpi-top">
                <span class="wms-kpi-label">Supervisor (Halaman)</span>
                <span class="wms-kpi-icon"><i class="bi bi-eye-fill"></i></span>
            </div>
            <div class="wms-kpi-value" style="color: var(--indo-blue);">{{ number_format($supervisorCount) }}</div>
            <div class="wms-kpi-note">Akses monitoring & laporan</div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="wms-kpi-card" style="--kpi-accent: var(--indo-green); --kpi-tint: var(--indo-green-tint);">
            <div class="wms-kpi-top">
                <span class="wms-kpi-label">Petugas FG (Halaman)</span>
                <span class="wms-kpi-icon"><i class="bi bi-person-workspace"></i></span>
            </div>
            <div class="wms-kpi-value" style="color: var(--indo-green);">{{ number_format($petugasCount) }}</div>
            <div class="wms-kpi-note">Pelaksana transaksi IN / OUT / MOVE</div>
        </div>
    </div>
</div>

{{-- FILTER PENCARIAN --}}
<div class="wms-panel mb-4">
    <div class="wms-panel-header">
        <h2 class="wms-panel-title"><i class="bi bi-funnel"></i> Filter & Pencarian Pengguna</h2>
    </div>
    <div class="wms-panel-body">
        <form action="{{ route('users.index') }}" method="GET">
            <div class="row g-3 align-items-end">
                <div class="col-lg-5">
                    <label for="user-search" class="wms-form-label">Nama Lengkap atau Alamat Email</label>
                    <input type="text" id="user-search" name="search" class="form-control wms-control"
                           placeholder="Contoh: Budi Santoso atau admin@indofood.co.id..."
                           value="{{ request('search') }}">
                </div>
                <div class="col-lg-4">
                    <label for="role-filter" class="wms-form-label">Filter Role Akses</label>
                    <select id="role-filter" name="role" class="form-select wms-control">
                        <option value="">Semua Role</option>
                        <option value="super_admin" {{ request('role') === 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                        <option value="admin_gudang" {{ request('role') === 'admin_gudang' ? 'selected' : '' }}>Admin Gudang</option>
                        <option value="supervisor" {{ request('role') === 'supervisor' ? 'selected' : '' }}>Supervisor</option>
                        <option value="petugas_fg" {{ request('role') === 'petugas_fg' ? 'selected' : '' }}>Petugas FG</option>
                    </select>
                </div>
                <div class="col-lg-3 d-flex gap-2">
                    <button type="submit" class="btn btn-indo-primary flex-fill justify-content-center">
                        <i class="bi bi-search"></i> Tampilkan
                    </button>
                    <a href="{{ route('users.index') }}" class="btn btn-indo-outline">Reset</a>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- TABEL DATA PENGGUNA --}}
<div class="wms-panel">
    <div class="wms-panel-header">
        <div>
            <h2 class="wms-panel-title"><i class="bi bi-table"></i> Daftar Pengguna Sistem</h2>
            <p class="wms-panel-subtitle">Informasi akun login, peran operasional, status aktif, dan penugasan unit gudang.</p>
        </div>
        <span class="wms-pill wms-pill-slate tabular-nums">{{ number_format($users->total()) }} akun</span>
    </div>

    <div class="table-responsive">
        <table class="wms-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Pengguna</th>
                    <th>Email</th>
                    <th>Role Akses</th>
                    <th>Penugasan Gudang</th>
                    <th>Status</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    @php
                        $roleBadge = match($user->role) {
                            'super_admin' => ['wms-pill-red', 'bi-shield-lock-fill', 'Super Admin'],
                            'admin_gudang' => ['wms-pill-blue', 'bi-person-badge-fill', 'Admin Gudang'],
                            'supervisor' => ['wms-pill-gold', 'bi-eye-fill', 'Supervisor'],
                            'petugas_fg' => ['wms-pill-green', 'bi-person-workspace', 'Petugas FG'],
                            default => ['wms-pill-slate', 'bi-person', ucfirst(str_replace('_', ' ', $user->role))],
                        };
                    @endphp
                    <tr>
                        <td class="text-muted tabular-nums">{{ $users->firstItem() + $loop->index }}</td>
                        <td>
                            <div class="fw-bold d-flex align-items-center gap-2">
                                <span>{{ $user->name }}</span>
                                @if(auth()->id() === $user->id)
                                    <span class="wms-pill wms-pill-gold" style="font-size: 10px;">Akun Anda</span>
                                @endif
                            </div>
                        </td>
                        <td class="text-muted">{{ $user->email }}</td>
                        <td>
                            <span class="wms-pill {{ $roleBadge[0] }}">
                                <i class="bi {{ $roleBadge[1] }}"></i> {{ $roleBadge[2] }}
                            </span>
                        </td>
                        <td>
                            @if($user->role === 'super_admin')
                                <span class="text-muted small fw-semibold"><i class="bi bi-globe2 me-1"></i>Seluruh Gudang (Global)</span>
                            @elseif($user->warehouse)
                                <span class="wms-code-badge">{{ $user->warehouse->warehouse_code }}</span>
                                <span class="ms-1">{{ $user->warehouse->warehouse_name }}</span>
                            @else
                                <span class="wms-pill wms-pill-amber">Belum Ditugaskan</span>
                            @endif
                        </td>
                        <td>
                            @if($user->is_active)
                                <span class="wms-pill wms-pill-green">
                                    <i class="bi bi-circle-fill" style="font-size: 6px;"></i> Aktif
                                </span>
                            @else
                                <span class="wms-pill wms-pill-red">
                                    <i class="bi bi-circle-fill" style="font-size: 6px;"></i> Nonaktif
                                </span>
                            @endif
                        </td>
                        <td class="text-end">
                            <div class="d-inline-flex gap-1">
                                <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-outline-primary wms-action-btn" title="Edit Pengguna">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                @if(auth()->id() !== $user->id)
                                    <form action="{{ route('users.toggle-status', $user) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-sm btn-outline-warning wms-action-btn" title="{{ $user->is_active ? 'Nonaktifkan Akun' : 'Aktifkan Akun' }}">
                                            <i class="bi bi-power"></i>
                                        </button>
                                    </form>
                                    <form action="{{ route('users.destroy', $user) }}" method="POST" onsubmit="return confirm('Hapus akun pengguna {{ $user->name }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger wms-action-btn" title="Hapus Pengguna">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="bi bi-people fs-2 d-block mb-2"></i>
                            <div class="fw-bold text-dark">Tidak ada data pengguna ditemukan</div>
                            <div class="small">Sesuaikan kata kunci pencarian atau klik <strong>Tambah Pengguna</strong>.</div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($users->hasPages())
        <div class="wms-pagination-bar">
            <span>Menampilkan {{ $users->firstItem() }}–{{ $users->lastItem() }} dari {{ $users->total() }} pengguna</span>
            {{ $users->withQueryString()->links() }}
        </div>
    @endif
</div>
@endsection