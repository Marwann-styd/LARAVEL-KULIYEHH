<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') &mdash; Warehouse FG PT Indofood</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="{{ asset('css/indofood-wms.css') }}" rel="stylesheet">
    <style>
        /* Transisi & Collapsible Sidebar Modern (Mini-Rail Mode) */
        .wms-sidebar,
        .wms-workspace {
            transition: width 0.26s cubic-bezier(0.16, 1, 0.3, 1),
                margin-left 0.26s cubic-bezier(0.16, 1, 0.3, 1),
                padding 0.26s cubic-bezier(0.16, 1, 0.3, 1),
                transform 0.26s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .wms-nav-link {
            transition: background-color 0.16s ease, color 0.16s ease, padding-left 0.16s ease;
        }

        .wms-nav-link:hover {
            padding-left: 18px;
        }

        /* Tombol Toggle Sidebar di Topbar */
        .wms-sidebar-toggle-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 34px;
            height: 34px;
            border-radius: 8px;
            border: 1px solid #D8E2EC;
            background: #FFFFFF;
            color: #0E2340;
            font-size: 16px;
            cursor: pointer;
            margin-right: 12px;
            flex-shrink: 0;
            transition: background-color 0.15s ease, border-color 0.15s ease, color 0.15s ease, transform 0.12s ease;
        }

        .wms-sidebar-toggle-btn:hover {
            background: #F1F6FC;
            border-color: #00529B;
            color: #00529B;
        }

        .wms-sidebar-toggle-btn:active {
            transform: scale(0.94);
        }

        /* Animasi Konten Utama */
        @keyframes wmsPageEnter {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        .wms-main {
            animation: wmsPageEnter 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        /* Mode Ciut (Mini-Rail) Khusus Layar Desktop */
        @media (min-width: 992px) {
            body.sidebar-collapsed .wms-sidebar {
                width: 76px !important;
                min-width: 76px !important;
            }

            body.sidebar-collapsed .wms-workspace {
                margin-left: 76px !important;
                width: calc(100% - 76px) !important;
            }

            body.sidebar-collapsed .wms-brand {
                justify-content: center !important;
                padding-left: 0 !important;
                padding-right: 0 !important;
            }

            body.sidebar-collapsed .wms-brand-text,
            body.sidebar-collapsed .wms-nav-section,
            body.sidebar-collapsed .wms-nav-link span,
            body.sidebar-collapsed .wms-sidebar-user-info {
                display: none !important;
            }

            body.sidebar-collapsed .wms-nav-link {
                justify-content: center !important;
                padding: 11px 0 !important;
                margin-left: 10px !important;
                margin-right: 10px !important;
            }

            body.sidebar-collapsed .wms-nav-link i {
                margin: 0 !important;
                font-size: 18px !important;
            }

            body.sidebar-collapsed .wms-sidebar-user {
                justify-content: center !important;
                padding-left: 0 !important;
                padding-right: 0 !important;
            }

            body.sidebar-collapsed .wms-sidebar-toggle-btn i {
                transform: rotate(180deg);
            }
        }

        .wms-sidebar-toggle-btn i {
            transition: transform 0.25s ease;
        }

        /* Pastikan Modal Bootstrap selalu di atas semua elemen & backdrop */
        .modal-backdrop {
            z-index: 1040 !important;
        }

        .modal {
            z-index: 1055 !important;
        }
    </style>
    @stack('styles')
</head>

<body>
    <script>
        // Terapkan status sidebar sebelum render agar tidak berkedip saat pindah halaman
        if (window.innerWidth >= 992 && localStorage.getItem('wms_sidebar_collapsed') === '1') {
            document.body.classList.add('sidebar-collapsed');
        }

        document.addEventListener('show.bs.modal', (e) => {
            document.body.appendChild(e.target);
        });
    </script>

    @php
        $currentUser = auth()->user();
        $isSuperAdmin = $currentUser && $currentUser->role === 'super_admin';

        $roleLabels = [
            'super_admin' => 'Super Admin',
            'admin_gudang' => 'Admin Gudang',
            'supervisor' => 'Supervisor',
            'petugas_fg' => 'Petugas FG',
        ];
        $displayRole =
            $roleLabels[$currentUser->role ?? ''] ?? ucfirst(str_replace('_', ' ', $currentUser->role ?? 'Pengguna'));
        $userInitial = mb_strtoupper(mb_substr($currentUser->name ?? 'U', 0, 1));

        $warehouseScope = $isSuperAdmin
            ? 'Semua Gudang Aktif'
            : ($currentUser?->warehouse
                ? $currentUser->warehouse->warehouse_code . ' - ' . $currentUser->warehouse->warehouse_name
                : 'Gudang Penugasan');

        $breadcrumbSection = match (true) {
            request()->routeIs('dashboard') => 'Ringkasan Utama',
            request()->routeIs('warehouses.*', 'areas.*', 'racks.*', 'categories.*', 'products.*') => 'Master Data',
            request()->routeIs('monitoring.*', 'storage-locations.*', 'transactions.*', 'damaged-stocks.*')
                => 'Operasional Gudang',
            request()->routeIs('reports.*') => 'Pusat Laporan',
            request()->routeIs('users.*') => 'Administrasi Sistem',
            default => 'Warehouse FG',
        };
    @endphp

    <div class="wms-shell">
        {{-- SIDEBAR NAVIGASI INDOFOOD --}}
        <aside class="wms-sidebar" id="wmsSidebar" aria-label="Navigasi utama">
            <a href="{{ route('dashboard') }}" class="wms-brand" title="Warehouse FG PT Indofood">
                <div class="wms-brand-logo">
                    <img src="{{ asset('images/indofood-logo.svg') }}" alt="PT Indofood">
                </div>
                <div class="wms-brand-text">
                    <span class="wms-brand-name">Warehouse FG</span>
                    <span class="wms-brand-sub">Finished Goods WMS</span>
                </div>
            </a>

            <nav class="wms-nav-scroll">
                <div class="wms-nav-section">Utama</div>
                <a href="{{ route('dashboard') }}" title="Dashboard"
                    class="wms-nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="bi bi-grid-1x2-fill"></i>
                    <span>Dashboard</span>
                </a>

                @if ($isSuperAdmin)
                    <div class="wms-nav-section">Master Data</div>
                    <a href="{{ route('warehouses.index') }}" title="Data Gudang"
                        class="wms-nav-link {{ request()->routeIs('warehouses.*') ? 'active' : '' }}">
                        <i class="bi bi-buildings"></i>
                        <span>Data Gudang</span>
                    </a>
                    <a href="{{ route('areas.index') }}" title="Data Area"
                        class="wms-nav-link {{ request()->routeIs('areas.*') ? 'active' : '' }}">
                        <i class="bi bi-map"></i>
                        <span>Data Area</span>
                    </a>
                    <a href="{{ route('racks.index') }}" title="Data Rak & Level"
                        class="wms-nav-link {{ request()->routeIs('racks.*') ? 'active' : '' }}">
                        <i class="bi bi-bookshelf"></i>
                        <span>Data Rak & Level</span>
                    </a>
                    <a href="{{ route('categories.index') }}" title="Kategori Produk"
                        class="wms-nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}">
                        <i class="bi bi-tags"></i>
                        <span>Kategori Produk</span>
                    </a>
                    <a href="{{ route('products.index') }}" title="Master Produk"
                        class="wms-nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}">
                        <i class="bi bi-box-seam"></i>
                        <span>Master Produk</span>
                    </a>
                @endif

                <div class="wms-nav-section">Operasional Gudang</div>
                <a href="{{ route('monitoring.index') }}" title="Monitoring Kapasitas"
                    class="wms-nav-link {{ request()->routeIs('monitoring.*') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2"></i>
                    <span>Monitoring Kapasitas</span>
                </a>
                <a href="{{ route('storage-locations.index') }}" title="Lokasi Produk"
                    class="wms-nav-link {{ request()->routeIs('storage-locations.*') ? 'active' : '' }}">
                    <i class="bi bi-geo-alt"></i>
                    <span>Lokasi Produk</span>
                </a>
                <a href="{{ route('transactions.index') }}" title="Transaksi Penyimpanan"
                    class="wms-nav-link {{ request()->routeIs('transactions.*') ? 'active' : '' }}">
                    <i class="bi bi-arrow-left-right"></i>
                    <span>Transaksi Penyimpanan</span>
                </a>
                <a href="{{ route('damaged-stocks.index') }}" title="Barang Rusak"
                    class="wms-nav-link {{ request()->routeIs('damaged-stocks.*') ? 'active' : '' }}">
                    <i class="bi bi-exclamation-triangle"></i>
                    <span>Barang Rusak</span>
                </a>

                <div class="wms-nav-section">Laporan</div>
                <a href="{{ route('reports.stock-location') }}" title="Laporan Stok Produk"
                    class="wms-nav-link {{ request()->routeIs('reports.stock-location') ? 'active' : '' }}">
                    <i class="bi bi-clipboard-data"></i>
                    <span>Laporan Stok Produk</span>
                </a>
                <a href="{{ route('reports.transactions') }}" title="Riwayat Transaksi"
                    class="wms-nav-link {{ request()->routeIs('reports.transactions') ? 'active' : '' }}">
                    <i class="bi bi-clock-history"></i>
                    <span>Riwayat Transaksi</span>
                </a>

                @if ($isSuperAdmin)
                    <div class="wms-nav-section">Administrasi</div>
                    <a href="{{ route('users.index') }}" title="Manajemen Pengguna"
                        class="wms-nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                        <i class="bi bi-people"></i>
                        <span>Manajemen Pengguna</span>
                    </a>
                @endif
            </nav>

            <div class="wms-sidebar-footer">
                <div class="wms-sidebar-user" title="{{ $currentUser->name }} ({{ $displayRole }})">
                    <div class="wms-sidebar-avatar">{{ $userInitial }}</div>
                    <div class="wms-sidebar-user-info">
                        <div class="wms-sidebar-user-name">{{ $currentUser->name }}</div>
                        <div class="wms-sidebar-user-role">{{ $displayRole }}</div>
                    </div>
                </div>
            </div>
        </aside>

        <div class="wms-backdrop" id="wmsBackdrop"></div>

        {{-- AREA KERJA UTAMA --}}
        <div class="wms-workspace">
            <header class="wms-topbar">
                <div class="wms-topbar-left d-flex align-items-center">
                    <button type="button" class="wms-sidebar-toggle-btn" id="wmsUnifiedToggle"
                        aria-label="Sembunyikan atau tampilkan sidebar" title="Tampilkan / Ciutkan Sidebar">
                        <i class="bi bi-layout-sidebar-inset"></i>
                    </button>

                    <nav class="wms-breadcrumb" aria-label="Breadcrumb">
                        <span class="wms-breadcrumb-root">{{ $breadcrumbSection }}</span>
                        <span class="wms-breadcrumb-sep">/</span>
                        <span class="wms-breadcrumb-current">@yield('page-heading', 'Dashboard')</span>
                    </nav>
                </div>

                <div class="wms-topbar-right">
                    <div class="wms-scope-pill" title="Cakupan Akses Gudang">
                        <span class="wms-scope-dot"></span>
                        <span>{{ $warehouseScope }}</span>
                    </div>

                    <div class="wms-date-pill">
                        <i class="bi bi-calendar3"></i>
                        <span>{{ now()->translatedFormat('d M Y') }}</span>
                    </div>

                    <div class="dropdown">
                        <button class="wms-account-btn dropdown-toggle" type="button" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            <i class="bi bi-person-circle text-primary"></i>
                            <span class="d-none d-md-inline">{{ $displayRole }}</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm"
                            style="border-radius: 10px; border-color: #D8E2EC; min-width: 220px;">
                            <li class="px-3 py-2">
                                <div class="fw-bold text-dark" style="font-size: 13px;">{{ $currentUser->name }}
                                </div>
                                <div class="text-muted" style="font-size: 11.5px; word-break: break-all;">
                                    {{ $currentUser->email }}</div>
                            </li>
                            <li>
                                <hr class="dropdown-divider my-1">
                            </li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger fw-semibold py-2"
                                        style="font-size: 13px;">
                                        <i class="bi bi-box-arrow-right me-2"></i>Keluar dari Sistem
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </header>

            <main class="wms-main">
                @yield('content')
            </main>

            <footer class="wms-footer">
                <span><strong>PT Indofood</strong> &mdash; Warehouse Finished Goods Monitoring System</span>
                <span>&copy; {{ date('Y') }} Internal Logistics Operations</span>
            </footer>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        (() => {
            const sidebar = document.getElementById('wmsSidebar');
            const backdrop = document.getElementById('wmsBackdrop');
            const toggleBtn = document.getElementById('wmsUnifiedToggle');

            const setMobileOpen = (state) => {
                sidebar?.classList.toggle('open', state);
                backdrop?.classList.toggle('show', state);
            };

            toggleBtn?.addEventListener('click', () => {
                if (window.innerWidth >= 992) {
                    const isCollapsed = document.body.classList.toggle('sidebar-collapsed');
                    localStorage.setItem('wms_sidebar_collapsed', isCollapsed ? '1' : '0');
                } else {
                    setMobileOpen(!sidebar.classList.contains('open'));
                }
            });

            backdrop?.addEventListener('click', () => setMobileOpen(false));

            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') setMobileOpen(false);
            });
        })();
    </script>
    @stack('scripts')
</body>

</html>
