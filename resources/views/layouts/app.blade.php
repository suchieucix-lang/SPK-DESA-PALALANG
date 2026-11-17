<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Beranda') - Bantuan Sosial Desa Palalang</title>
    
    <!-- Google Font: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/datatables.net-bs5@1.13.8/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <style>
        :root {
            /* Soft Pastel Palette */
            --bg-canvas: #f8fafc;
            --sidebar-bg: #2d3748; /* Soft Slate */
            --primary-soft: #38bdf8; /* Soft Sky */
            --primary-button: #0284c7;
            --text-dark: #334155;
            --text-muted: #64748b;
            --border-soft: #e2e8f0;
            --card-radius: 16px;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-canvas);
            color: var(--text-dark);
            min-height: 100vh;
        }

        /* Sidebar Soft & Clean */
        #sidebar-wrapper {
            width: 260px;
            height: 100vh;
            background: #242f3d; /* Soft navy-slate */
            color: #f1f5f9;
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 1040;
            transition: all 0.25s ease;
            box-shadow: 2px 0 16px rgba(0, 0, 0, 0.04);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .sidebar-scroll-area {
            flex: 1;
            overflow-y: auto;
            overflow-x: hidden;
        }

        .sidebar-brand {
            padding: 1.35rem 1.25rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.07);
            display: flex;
            align-items: center;
            gap: 0.8rem;
        }

        .sidebar-brand-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #7dd3fc, #0284c7);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            color: #ffffff;
            box-shadow: 0 4px 10px rgba(56, 189, 248, 0.25);
        }

        .sidebar-brand-text h6 {
            font-size: 0.95rem;
            font-weight: 700;
            margin-bottom: 0.1rem;
            color: #ffffff;
            letter-spacing: -0.2px;
        }

        .sidebar-brand-text small {
            font-size: 0.72rem;
            color: #94a3b8;
        }

        .menu-category {
            font-size: 0.68rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 700;
            color: #818cf8; /* Soft periwinkle */
            padding: 1.2rem 1.4rem 0.4rem;
        }

        .nav-link-item {
            display: flex;
            align-items: center;
            padding: 0.72rem 1.15rem;
            color: #cbd5e1;
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
            border-radius: 10px;
            margin: 0.2rem 0.85rem;
            gap: 0.75rem;
            transition: all 0.2s ease;
        }

        .nav-link-item i {
            font-size: 1.15rem;
            width: 24px;
            text-align: center;
            color: #94a3b8;
            transition: color 0.2s ease;
        }

        .nav-link-item:hover {
            color: #ffffff;
            background: rgba(255, 255, 255, 0.07);
        }

        .nav-link-item:hover i {
            color: #7dd3fc;
        }

        .nav-link-item.active {
            color: #ffffff;
            background: #0284c7;
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(2, 132, 199, 0.3);
        }

        .nav-link-item.active i {
            color: #ffffff;
        }

        /* Content Area */
        #content-wrapper {
            margin-left: 260px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            background-color: var(--bg-canvas);
        }

        .top-navbar {
            background: #ffffff;
            border-bottom: 1px solid var(--border-soft);
            padding: 0.9rem 1.75rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 1020;
        }

        .main-content {
            padding: 1.75rem;
            flex: 1;
        }

        /* Soft Cards */
        .card-custom {
            background: #ffffff;
            border: 1px solid #edf2f7;
            border-radius: var(--card-radius);
            box-shadow: 0 2px 12px rgba(148, 163, 184, 0.06);
            margin-bottom: 1.35rem;
            transition: all 0.2s ease;
        }

        .card-custom:hover {
            box-shadow: 0 4px 18px rgba(148, 163, 184, 0.1);
        }

        .card-custom .card-header {
            background: transparent;
            border-bottom: 1px solid #f1f5f9;
            padding: 1.15rem 1.4rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .card-custom .card-body {
            padding: 1.4rem;
        }

        /* Soft Banners */
        .guide-banner-soft {
            background: linear-gradient(135deg, #f0f9ff 0%, #f0fdf4 50%, #faf5ff 100%);
            border: 1px solid #e0f2fe;
            border-radius: var(--card-radius);
            padding: 1.25rem 1.5rem;
            margin-bottom: 1.35rem;
            box-shadow: 0 2px 8px rgba(186, 230, 253, 0.2);
        }

        /* Soft Badges */
        .badge-soft-sky {
            background-color: #e0f2fe;
            color: #0369a1;
        }
        .badge-soft-emerald {
            background-color: #ecfdf5;
            color: #047857;
        }
        .badge-soft-amber {
            background-color: #fffbeb;
            color: #b45309;
        }
        .badge-soft-rose {
            background-color: #fff1f2;
            color: #be123c;
        }
        .badge-soft-slate {
            background-color: #f1f5f9;
            color: #475569;
        }

        /* Responsive toggle */
        @media (max-width: 991.98px) {
            #sidebar-wrapper {
                margin-left: -260px;
            }
            #sidebar-wrapper.show {
                margin-left: 0;
            }
            #content-wrapper {
                margin-left: 0;
            }
        }

        .app-footer {
            background: #ffffff;
            border-top: 1px solid var(--border-soft);
            padding: 0.9rem 1.75rem;
            color: var(--text-muted);
            font-size: 0.8rem;
        }
    </style>
    @stack('styles')
</head>
<body>

    <!-- SIDEBAR SOFT SLATE -->
    <div id="sidebar-wrapper">
        <div class="sidebar-scroll-area">
            <div class="sidebar-brand">
                <div class="sidebar-brand-icon">
                    <i class="fa-solid fa-landmark"></i>
                </div>
                <div class="sidebar-brand-text">
                    <h6>SPK DESA PALALANG</h6>
                    <small>Bantuan Sosial DTKS / DTSEN</small>
                </div>
            </div>

            <div class="py-2">
                <div class="menu-category">Menu Utama</div>
                
                <a href="{{ route('dashboard') }}" class="nav-link-item {{ request()->routeIs('dashboard') || request()->routeIs('home') ? 'active' : '' }}">
                    <i class="bi bi-house-door-fill"></i>
                    <span>Beranda</span>
                </a>

                <a href="{{ route('warga.index') }}" class="nav-link-item {{ request()->routeIs('warga.*') || request()->routeIs('penilaian.*') ? 'active' : '' }}">
                    <i class="bi bi-people-fill"></i>
                    <span>Data Warga & Nilai</span>
                </a>

                <a href="{{ route('bwm.index') }}" class="nav-link-item {{ request()->routeIs('bwm.*') || request()->routeIs('kriteria.*') ? 'active' : '' }}">
                    <i class="bi bi-sliders"></i>
                    <span>Atur Kriteria & Bobot</span>
                </a>

                <a href="{{ route('marcos.index') }}" class="nav-link-item {{ request()->routeIs('marcos.*') || request()->routeIs('laporan.*') ? 'active' : '' }}">
                    <i class="bi bi-trophy-fill"></i>
                    <span>Hasil & Cetak Surat</span>
                </a>

                <div class="menu-category mt-2">Bantuan & Panduan</div>
                <a href="{{ route('panduan') }}" class="nav-link-item {{ request()->routeIs('panduan') ? 'active' : '' }}">
                    <i class="bi bi-lightbulb-fill" style="color: #fbbf24;"></i>
                    <span>Panduan Cara Kerja</span>
                </a>
                <a href="{{ route('pengaturan.index') }}" class="nav-link-item {{ request()->routeIs('pengaturan.*') ? 'active' : '' }}">
                    <i class="bi bi-gear-fill"></i>
                    <span>Profil & Kop Surat</span>
                </a>
            </div>
        </div>

        <!-- SIDEBAR FOOTER (USER & LOGOUT LANGSUNG) -->
        <div class="p-3 border-top" style="border-color: rgba(255, 255, 255, 0.08) !important; background: rgba(15, 23, 42, 0.35);">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <div class="d-flex align-items-center gap-2 overflow-hidden">
                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white" style="width: 32px; height: 32px; background: linear-gradient(135deg, #0284c7, #38bdf8); font-weight: 700; font-size: 0.8rem;">
                        {{ strtoupper(substr(Auth::user()->nama_admin ?? 'A', 0, 1)) }}
                    </div>
                    <div class="text-truncate" style="line-height: 1.2;">
                        <span class="d-block fw-semibold text-white text-truncate" style="font-size: 0.82rem;">{{ Auth::user()->nama_admin ?? 'Admin Desa' }}</span>
                        <small class="text-white-50" style="font-size: 0.7rem;">@ {{ Auth::user()->username ?? 'admin' }}</small>
                    </div>
                </div>
            </div>
            <a href="{{ route('logout') }}" class="btn btn-sm w-100 rounded-3 d-flex align-items-center justify-content-center gap-2 text-decoration-none shadow-sm" style="background-color: rgba(244, 63, 94, 0.15); color: #fda4af; border: 1px solid rgba(244, 63, 94, 0.3); font-size: 0.8rem; font-weight: 600; padding: 0.45rem;">
                <i class="bi bi-box-arrow-right"></i> Keluar (Logout)
            </a>
        </div>
    </div>

    <!-- CONTENT WRAPPER -->
    <div id="content-wrapper">
        <!-- TOP NAVBAR -->
        <header class="top-navbar">
            <div class="d-flex align-items-center gap-2">
                <button class="btn btn-sm btn-light border d-lg-none" id="sidebarToggle">
                    <i class="bi bi-list fs-5"></i>
                </button>
                <div>
                    <h6 class="mb-0 fw-bold text-dark">@yield('page_title', 'Sistem Bantuan Sosial')</h6>
                    <small class="text-muted"><i class="bi bi-geo-alt me-1"></i>Pemerintah Desa Palalang, Kec. Pakong, Kab. Pamekasan</small>
                </div>
            </div>

            <div class="d-flex align-items-center gap-2">
                <!-- Tombol Panduan Cara Kerja Soft Pill -->
                <a href="{{ route('panduan') }}" class="btn btn-sm rounded-pill px-3 shadow-sm" style="background-color: #fef3c7; color: #92400e; border: 1px solid #fde68a;">
                    <i class="bi bi-lightbulb-fill me-1" style="color: #d97706;"></i> Cara Kerja Sistem
                </a>

                <!-- User Profile Menu -->
                <div class="dropdown">
                    <button class="btn btn-light rounded-pill px-3 py-1.5 border shadow-sm d-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown">
                        <i class="bi bi-person-circle fs-5" style="color: #0284c7;"></i>
                        <span class="d-none d-sm-inline fw-semibold text-dark" style="font-size: 0.85rem;">{{ Auth::user()->nama_admin ?? 'Admin Desa' }}</span>
                        <i class="bi bi-chevron-down text-muted" style="font-size: 0.7rem;"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3 mt-2">
                        <li>
                            <div class="px-3 py-2 border-bottom">
                                <span class="d-block fw-bold text-dark">{{ Auth::user()->nama_admin ?? 'Administrator' }}</span>
                                <small class="text-muted">{{ Auth::user()->username ?? 'admin' }}</small>
                            </div>
                        </li>
                        <li>
                            <a class="dropdown-item py-2" href="{{ route('profile') }}">
                                <i class="bi bi-person me-2" style="color: #0284c7;"></i> Profil Saya
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item py-2" href="{{ route('pengaturan.index') }}">
                                <i class="bi bi-gear me-2 text-warning"></i> Pengaturan Desa
                            </a>
                        </li>
                        <li><hr class="dropdown-divider my-1"></li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item py-2 text-danger">
                                    <i class="bi bi-box-arrow-right me-2"></i> Keluar (Logout)
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </header>

        <!-- FLASH MESSAGES -->
        <main class="main-content">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 mb-3 py-2.5 px-3" style="background-color: #ecfdf5; color: #065f46;" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-check-circle-fill fs-5 me-2" style="color: #10b981;"></i>
                        <div>{{ session('success') }}</div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3 mb-3 py-2.5 px-3" style="background-color: #fff1f2; color: #9f1239;" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-exclamation-triangle-fill fs-5 me-2" style="color: #f43f5e;"></i>
                        <div>{{ session('error') }}</div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </main>

        <footer class="app-footer text-center">
            <span>&copy; {{ date('Y') }} Sistem Pendukung Keputusan Koreksi DTKS &bull; Desa Palalang, Pakong, Pamekasan</span>
        </footer>
    </div>

    <!-- SCRIPTS -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/datatables.net@1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/datatables.net-bs5@1.13.8/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $('#sidebarToggle').on('click', function() {
            $('#sidebar-wrapper').toggleClass('show');
        });

        $(document).ready(function() {
            $('.datatable-init').DataTable({
                language: {
                    search: "Cari Data:",
                    lengthMenu: "Tampilkan _MENU_",
                    info: "Menampilkan _START_ s.d. _END_ dari _TOTAL_ data",
                    infoEmpty: "Data kosong",
                    zeroRecords: "Data tidak ditemukan",
                    paginate: {
                        next: "Lanjut",
                        previous: "Kembali"
                    }
                },
                pageLength: 10,
                responsive: true
            });
        });
    </script>
    @stack('scripts')
</body>
</html>
