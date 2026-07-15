<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — Sistem Remunerasi</title>
    <link rel="icon" type="image/webp" href="{{ asset('storage/logo/logo.webp') }}">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    @stack('styles')
</head>

<body>

    {{-- ─── SIDEBAR ─────────────────────────────────────── --}}
    <aside class="sidebar" id="sidebar">

        {{-- Logo --}}
        <a href="{{ route('dashboard') }}" class="sidebar-logo">
            <img src="{{ asset('storage/logo/logo.webp') }}" alt="Logo" class="sidebar-logo-img">
            <div class="sidebar-logo-text">
                <span>Remunerasi</span>
                <span>Restoran</span>
            </div>
        </a>

        {{-- Navigation --}}
        <nav class="sidebar-nav">

            {{-- MENU UTAMA — semua role --}}
            <div class="sidebar-section-label">Menu Utama</div>

            <a href="{{ route('dashboard') }}"
                class="sidebar-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <span class="sidebar-icon"><i class="bi bi-grid-1x2-fill"></i></span>
                Dashboard
            </a>

            {{-- MASTER DATA — Owner & HR (tulis tetap dibatasi per-halaman) --}}
            <div class="sidebar-section-label">Master Data</div>

            <a href="{{ route('categorical.index') }}"
                class="sidebar-item {{ request()->routeIs('categorical.*') ? 'active' : '' }}">
                <span class="sidebar-icon"><i class="bi bi-sliders"></i></span>
                Kategorikal
            </a>

            <a href="{{ route('notification-settings.index') }}"
                class="sidebar-item {{ request()->routeIs('notification-settings.*') ? 'active' : '' }}">
                <span class="sidebar-icon"><i class="bi bi-whatsapp"></i></span>
                Template Pesan WA
            </a>

            <a href="{{ route('branches.index') }}"
                class="sidebar-item {{ request()->routeIs('branches.*') ? 'active' : '' }}">
                <span class="sidebar-icon"><i class="bi bi-shop"></i></span>
                Cabang
            </a>

            <a href="{{ route('positions.index') }}"
                class="sidebar-item {{ request()->routeIs('positions.*') ? 'active' : '' }}">
                <span class="sidebar-icon"><i class="bi bi-person-badge"></i></span>
                Jabatan
            </a>

            {{-- PENGGAJIAN — Owner & HR --}}
            <div class="sidebar-section-label">Penggajian</div>

            <a href="{{ route('employees.index') }}"
                class="sidebar-item {{ request()->routeIs('employees.*') ? 'active' : '' }}">
                <span class="sidebar-icon"><i class="bi bi-people-fill"></i></span>
                Karyawan
            </a>

            <a href="{{ route('periods.index') }}"
                class="sidebar-item {{ request()->routeIs('periods.*') ? 'active' : '' }}">
                <span class="sidebar-icon"><i class="bi bi-calendar3"></i></span>
                Periode
            </a>

            <a href="{{ route('salary-slips.index') }}"
                class="sidebar-item {{ request()->routeIs('salary-slips.*') ? 'active' : '' }}">
                <span class="sidebar-icon"><i class="bi bi-file-earmark-text-fill"></i></span>
                Slip Gaji
            </a>

            <a href="{{ route('distribution.index') }}"
                class="sidebar-item {{ request()->routeIs('distribution.*') ? 'active' : '' }}">
                <span class="sidebar-icon"><i class="bi bi-send-fill"></i></span>
                Distribusi Gaji
            </a>
            {{-- ANALITIK — Owner & HR --}}
            <div class="sidebar-section-label">Analitik</div>

            <a href="{{ route('reports.index') }}"
                class="sidebar-item {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                <span class="sidebar-icon"><i class="bi bi-bar-chart-fill"></i></span>
                Laporan
            </a>

            {{-- SISTEM — Owner only --}}
            @if (session('user.role') === 'owner')
                <div class="sidebar-section-label">Sistem</div>

                <a href="{{ route('hr-management.index') }}"
                    class="sidebar-item {{ request()->routeIs('hr-management.*') ? 'active' : '' }}">
                    <span class="sidebar-icon"><i class="bi bi-person-lines-fill"></i></span>
                    Manajemen HR
                </a>

                <a href="{{ route('activity-logs.index') }}"
                    class="sidebar-item {{ request()->routeIs('activity-logs.*') ? 'active' : '' }}">
                    <span class="sidebar-icon"><i class="bi bi-clock-history"></i></span>
                    Activity Log
                </a>
            @endif

            {{-- AKUN --}}
            <div class="sidebar-section-label">Akun</div>

            <a href="{{ route('profile.index') }}"
                class="sidebar-item {{ request()->routeIs('profile.*') ? 'active' : '' }}">
                <span class="sidebar-icon"><i class="bi bi-person-fill"></i></span>
                Profil Saya
            </a>

        </nav>

        {{-- User Footer --}}
        <div class="sidebar-footer">
            <div class="sidebar-user">
                <div class="sidebar-avatar">
                    {{ strtoupper(substr(session('user.name', 'U'), 0, 1)) }}
                </div>
                <div class="sidebar-user-info">
                    <div class="sidebar-user-name">{{ session('user.name', 'User') }}</div>
                    <div class="sidebar-user-role">{{ session('user.role', '-') }}</div>
                </div>
                <form action="{{ route('auth.logout') }}" method="POST" style="margin:0">
                    @csrf
                    <button type="submit" class="topbar-btn" title="Logout">
                        <i class="bi bi-box-arrow-right"></i>
                    </button>
                </form>
            </div>
        </div>

    </aside>

    {{-- ─── MAIN WRAPPER ────────────────────────────────── --}}
    <div class="main-wrapper">

        {{-- Topbar --}}
        <div class="topbar">
            <div class="topbar-left">
                <button class="topbar-btn d-md-none" onclick="toggleSidebar()">
                    <i class="bi bi-list"></i>
                </button>
                <div>
                    <div class="topbar-title">@yield('page-title', 'Dashboard')</div>
                    <div class="topbar-subtitle">@yield('page-subtitle', 'Sistem Remunerasi Restoran')</div>
                </div>
            </div>
            <div class="topbar-right">
                <span class="badge-custom badge-{{ session('user.role') }} fs-13">
                    <i class="bi bi-person-badge-fill me-1"></i>
                    {{ session('user.role') === 'owner' ? 'Owner' : 'HR' }}
                </span>
            </div>
        </div>

        {{-- Flash Messages --}}
        <div class="px-4 pt-3">
            @if (session('success'))
                <div class="alert-custom alert-success alert-auto-hide">
                    <i class="bi bi-check-circle-fill"></i>
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="alert-custom alert-error alert-auto-hide">
                    <i class="bi bi-exclamation-circle-fill"></i>
                    {{ session('error') }}
                </div>
            @endif

            @if (session('warning'))
                <div class="alert-custom alert-warning alert-auto-hide">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    {{ session('warning') }}
                </div>
            @endif
        </div>

        {{-- Page Content --}}
        <div class="page-content">
            @yield('content')
        </div>

        {{-- Footer --}}
        <footer
            style="padding: 16px 24px; border-top: 1px solid var(--border); background: var(--white); text-align: center;">
            <span style="font-size: 13px; color: #6c757d;">
                &copy; {{ date('Y') }} Sistem Remunerasi Restoran &mdash; Versi 2.0.0
            </span>
        </footer>

    </div>

    {{-- Bootstrap 5 JS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JS -->
    <script src="{{ asset('js/app.js') }}"></script>

    @stack('scripts')
</body>

</html>
