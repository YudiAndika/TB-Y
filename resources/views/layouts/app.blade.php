<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Toko Bangunan Modern</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <!-- Google Fonts: Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #4f46e5;
            --sidebar-bg: #1e1b4b;
            --bg-color: #f8fafc;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-color);
            color: #334155;
            overflow-x: hidden;
        }

        /* Konfigurasi Sidebar Animasi */
        #sidebar {
            background: linear-gradient(180deg, #1e1b4b 0%, #312e81 100%);
            min-height: 100vh;
            color: white;
            box-shadow: 4px 0 24px rgba(0, 0, 0, 0.05);
            width: 280px; /* DIPERLEBAR AGAR NAMA TOKO MUAT */
            transition: all 0.3s ease;
            overflow-x: hidden;
            white-space: nowrap;
        }

        /* Perbaikan Area Logo di Sidebar agar tidak terblok */
        .sidebar-brand-box {
            padding: 15px 10px;
            text-align: center;
            overflow: hidden;
            white-space: normal !important;
            word-break: break-word;
        }
        
        .sidebar-brand-box h5 {
            font-size: 1.1rem;
            font-weight: 700;
            color: #ffffff;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
            line-height: 1.4;
        }

        .sidebar-brand-box small {
            color: #94a3b8 !important;
            font-size: 0.72rem;
            display: block;
        }

        /* Saat Sidebar Ditutup (Mengecil di Laptop) */
        @media (min-width: 769px) {
            #sidebar.collapsed {
                width: 80px; 
            }
            #sidebar.collapsed .hide-on-collapse {
                display: none;
                opacity: 0;
                transition: opacity 0.2s;
            }
            #sidebar.collapsed .nav-link, 
            #sidebar.collapsed .btn-logout {
                text-align: center;
                padding: 10px 0 !important;
                justify-content: center;
            }
            #sidebar.collapsed .nav-link i, 
            #sidebar.collapsed .btn-logout i {
                margin-right: 0 !important;
                font-size: 1.3rem;
            }
        }

        /* TAMBAHAN KODE RESPONSIVE UNTUK HP (MOBILE) */
        @media (max-width: 768px) {
            #sidebar {
                position: fixed; /* Sidebar mengambang di atas konten */
                z-index: 1050; /* Pastikan selalu di paling depan */
                left: -300px; /* Sembunyikan ke luar layar kiri saat awal */
                width: 280px; /* DIPERLEBAR SESUAI UKURAN BARU */
                transition: left 0.3s ease;
            }

            /* Saat tombol garis tiga diklik di HP */
            #sidebar.show-mobile {
                left: 0; /* Sidebar meluncur masuk ke layar */
            }

            /* Area konten utama melebar penuh di HP */
            .main-content {
                width: 100%;
            }
        }

       .sidebar .nav-link {
            color: #94a3b8;
            border-radius: 8px;
            margin-bottom: 6px;
            font-weight: 500;
            transition: all 0.2s ease-in-out;
            display: flex;
            align-items: center;
            white-space: normal !important; /* Agar teks panjang bisa turun ke bawah jika mentok */
            word-break: break-word;
            padding: 8px 12px;
        }

        .sidebar .nav-link:hover {
            color: #ffffff;
            background: rgba(255, 255, 255, 0.08);
            transform: translateX(4px);
        }

        .sidebar .nav-link.active {
            color: #ffffff;
            background: var(--primary-color);
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.35);
        }

        .card {
            border: none;
            border-radius: 14px;
            box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.05), 0 2px 6px -1px rgba(0, 0, 0, 0.03);
            background: #ffffff;
        }

        .btn {
            border-radius: 8px;
            font-weight: 500;
            padding: 8px 16px;
            transition: all 0.2s;
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.25);
        }
    </style>
</head>
<body>

<div class="d-flex">
    <!-- Sidebar -->
    <div id="sidebar" class="p-3 d-flex flex-column sidebar">
        
        <!-- BAGIAN LOGO YANG SUDAH DIPERBAIKI (Font Disesuaikan) -->
        <div class="sidebar-brand-box py-3 mb-3 border-bottom border-secondary border-opacity-25" style="overflow: hidden; padding-left: 15px; padding-right: 15px;">
            <h5 class="m-0 text-white" style="font-size: 0.85rem; font-weight: 800; letter-spacing: 0.5px;">
                <i class="bi bi-box-seam me-2 text-info" style="font-size: 1.1rem;"></i><span class="hide-on-collapse">TB DUTA PRATAMA</span>
            </h5>
            <small class="hide-on-collapse text-muted" style="display: block; margin-top: 5px; font-size: 0.7rem;">Enterprise POS System</small>
        </div>
        
        <ul class="nav nav-pills flex-column mb-auto">
            <!-- MENU UTAMA -->
            <li class="nav-item">
                <a href="/home" class="nav-link py-2.5 px-3 {{ request()->is('home') ? 'active' : '' }}">
                    <i class="bi bi-house-door-fill me-3 text-primary"></i> <span class="hide-on-collapse">Menu Utama</span>
                </a>
            </li>
            
            <!-- DASHBOARD -->
            <li class="nav-item">
                <a href="/" class="nav-link py-2.5 px-3 {{ request()->is('/') || request()->is('dashboard*') ? 'active' : '' }}">
                    <i class="bi bi-grid-1x2-fill me-3 text-success"></i> <span class="hide-on-collapse">Dashboard</span>
                </a>
            </li>
            
            <!-- KASIR -->
            <li class="nav-item">
                <a href="/kasir" class="nav-link py-2.5 px-3 {{ request()->is('kasir*') ? 'active' : '' }}">
                    <i class="bi bi-cart-check-fill me-3 text-info"></i> <span class="hide-on-collapse">Kasir POS</span>
                </a>
            </li>
            
            <!-- INVENTARIS (Hanya Owner & Admin) -->
            @if(Auth::check() && in_array(Auth::user()->role, ['owner', 'admin']))
            <li class="nav-item">
                <a href="/barang" class="nav-link py-2.5 px-3 {{ request()->is('barang*') ? 'active' : '' }}">
                    <i class="bi bi-boxes me-3 text-warning"></i> <span class="hide-on-collapse">Inventaris Gudang</span>
                </a>
            </li>
            @endif
            
           <!-- LAPORAN (Hanya Owner) -->
            @if(Auth::check() && Auth::user()->role == 'owner')
            <li class="nav-item">
                <a href="/laporan" class="nav-link py-2.5 px-3 {{ request()->is('laporan*') ? 'active' : '' }}">
                    <i class="bi bi-file-earmark-bar-graph-fill me-3 text-danger"></i> <span class="hide-on-collapse">Laporan Keuangan</span>
                </a>
            </li>

            <!-- BIAYA OPERASIONAL (Modul 6) -->
            <li class="nav-item">
                <a href="{{ route('pengeluaran.index') }}" class="nav-link py-2.5 px-3 {{ request()->routeIs('pengeluaran.*') ? 'active' : '' }}">
                    <i class="bi bi-wallet2 me-3 text-warning"></i> <span class="hide-on-collapse">Biaya Operasional</span>
                </a>
            </li>

            <!-- REKAP BARANG TERJUAL (Hanya Owner) -->
            <li class="nav-item">
                <a href="{{ route('rekap.barang') }}" class="nav-link py-2.5 px-3 {{ request()->routeIs('rekap.barang') ? 'active' : '' }}">
                    <i class="bi bi-bar-chart-line-fill me-3 text-success"></i> <span class="hide-on-collapse">Rekap Barang Terjual</span>
                </a>
            </li>
            @endif
            
            <!-- PIUTANG (Hanya Owner & Admin) -->
            @if(Auth::check() && in_array(Auth::user()->role, ['owner', 'admin']))
            <li class="nav-item">
                <a href="/piutang" class="nav-link py-2.5 px-3 {{ request()->is('piutang*') ? 'active' : '' }}">
                    <i class="bi bi-journal-bookmark-fill me-3 text-info"></i> <span class="hide-on-collapse">Data Piutang</span>
                </a>
            </li>

            <!-- DATA SUPPLIER (Modul 5) -->
            <li class="nav-item">
                <a href="{{ route('supplier.index') }}" class="nav-link py-2.5 px-3 {{ request()->routeIs('supplier.*') ? 'active' : '' }}">
                    <i class="bi bi-truck me-3 text-primary"></i> <span class="hide-on-collapse">Data Supplier</span>
                </a>
            </li>

            <!-- PEMBELIAN STOK / PO (Modul 5) -->
            <li class="nav-item">
                <a href="{{ route('pembelian.index') }}" class="nav-link py-2.5 px-3 {{ request()->routeIs('pembelian.*') ? 'active' : '' }}">
                    <i class="bi bi-cart-plus-fill me-3 text-success"></i> <span class="hide-on-collapse">Pembelian Stok (PO)</span>
                </a>
            </li>
            @endif


            <!-- DATA PELANGGAN (Khusus Owner) -->
            @if(Auth::check() && Auth::user()->role == 'owner')
            <li class="nav-item">
                <a href="/pelanggan" class="nav-link py-2.5 px-3 {{ request()->is('pelanggan*') ? 'active' : '' }}">
                    <i class="bi bi-people-fill me-3 text-primary"></i> <span class="hide-on-collapse">Data Pelanggan</span>
                </a>
            </li>

            <!-- AUDIT LOG (Khusus Owner) -->
            <li class="nav-item">
                <a href="/audit-log" class="nav-link py-2.5 px-3 {{ request()->is('audit-log*') ? 'active' : '' }}">
                    <i class="bi bi-shield-check me-3 text-warning"></i> <span class="hide-on-collapse">Audit Log</span>
                </a>
            </li>
            @endif
        </ul> 

        <!-- Tombol Logout & Info User -->
        <div class="pt-3 border-top border-secondary border-opacity-25 text-center">
            <div class="text-white small mb-3 text-truncate hide-on-collapse">
                <i class="bi bi-person-circle me-1"></i> {{ Auth::user()->name ?? 'Admin' }}
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-outline-light btn-sm w-100 btn-logout d-flex align-items-center justify-content-center">
                    <i class="bi bi-box-arrow-right me-2"></i> <span class="hide-on-collapse">Keluar (Logout)</span>
                </button>
            </form>
        </div>
    </div>

    <!-- Konten Utama -->
    <div class="flex-grow-1 d-flex flex-column main-content" style="max-height: 100vh; overflow-y: auto; width: 100%;">
        
        <!-- Header Atas -->
        <header class="bg-white px-4 py-3 border-bottom d-flex justify-content-between align-items-center shadow-sm">
            <div class="d-flex align-items-center">
                <button class="btn btn-light border shadow-sm me-3" onclick="toggleSidebar()" title="Buka/Tutup Menu">
                    <i class="bi bi-list fs-5"></i>
                </button>
                <span class="text-secondary fw-semibold d-none d-sm-inline">Sistem Kasir Aktif</span>
            </div>
            
            <span class="badge bg-success-subtle text-success px-3 py-2 rounded-pill fw-bold">
                <i class="bi bi-circle-fill fs-8 me-1"></i> Online
            </span>
        </header>

        <!-- Area Konten Dinamis -->
        <div class="container-fluid p-3 p-md-4">
            @yield('content')
        </div>
    </div>
</div>

<!-- Bootstrap JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- JAVASCRIPT UNTUK BUKA TUTUP SIDEBAR RESPONSIVE -->
<script>
    function toggleSidebar() {
        var sidebar = document.getElementById("sidebar");
        
        if (window.innerWidth <= 768) {
            sidebar.classList.toggle("show-mobile");
            sidebar.classList.remove("collapsed"); 
        } else {
            sidebar.classList.toggle("collapsed");
            sidebar.classList.remove("show-mobile");
        }
    }
</script>

</body>
</html>