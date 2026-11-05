<!DOCTYPE html>
<html lang="id" dir="ltr">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>@yield('title', 'Portal Siswa — Sekolahku')</title>

    <script>
        (function() {
            const savedTheme = localStorage.getItem('apex_theme') || 'light';
            if (savedTheme === 'dark' || (savedTheme === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
            const savedPreset = localStorage.getItem('admindek_preset');
            if (savedPreset) {
                document.documentElement.style.setProperty('--bento-primary', savedPreset);
                document.documentElement.style.setProperty('--vx-primary', savedPreset);
            }
        })();
    </script>

    <!-- Typography: Inter & JetBrains Mono (Standardized Across Sekolahku) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />

    <style>
        :root {
            --bg-app: #f4f7fa;
            --bg-surface: #ffffff;
            --bg-card: #ffffff;
            --bg-subtle: #f8f9fa;
            --bg-hover: #f1f5f9;
            --border-color: #e2e8f0;
            --text-heading: #1e293b;
            --text-body: #334155;
            --text-muted: #64748b;
            --text-placeholder: #94a3b8;

            --bento-primary: #4680ff;
            --vx-primary: #4680ff;
            --vx-primary-hover: #3366ff;
            --vx-primary-light: rgba(70, 128, 255, 0.11);
            --vx-primary-border: rgba(70, 128, 255, 0.25);

            --vx-success: #10b981;
            --vx-success-light: rgba(16, 185, 129, 0.12);
            --vx-success-border: rgba(16, 185, 129, 0.28);

            --vx-warning: #f59e0b;
            --vx-warning-light: rgba(245, 158, 11, 0.13);

            --vx-danger: #ef4444;
            --vx-danger-light: rgba(239, 68, 68, 0.12);

            --vx-info: #0284c7;
            --vx-info-light: rgba(2, 132, 199, 0.12);

            --vx-purple: #7c3aed;
            --vx-purple-light: rgba(124, 58, 237, 0.12);

            --sidebar-width: 262px;
            --header-height: 64px;
            --radius-sm: 6px;
            --radius-md: 10px;
            --radius-lg: 14px;
            --radius-xl: 18px;
            --radius-pill: 9999px;
            --shadow-card: 0 2px 12px rgba(15, 23, 42, 0.05);
            --shadow-card-hover: 0 10px 24px rgba(15, 23, 42, 0.09);
            --transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        }

        html.dark {
            --bg-app: #0f172a;
            --bg-surface: #1e293b;
            --bg-card: #1e293b;
            --bg-subtle: #162032;
            --bg-hover: #273549;
            --border-color: #334155;
            --text-heading: #f8fafc;
            --text-body: #cbd5e1;
            --text-muted: #94a3b8;
            --shadow-card: 0 6px 20px rgba(0, 0, 0, 0.35);
        }

        *, *::before, *::after { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            font-size: 0.875rem;
            line-height: 1.5;
            background: var(--bg-app);
            color: var(--text-body);
            -webkit-font-smoothing: antialiased;
        }
        button, input, select, textarea { font-family: inherit; }
        .cell-mono { font-family: 'JetBrains Mono', monospace; }

        /* Shell */
        .std-shell { display: flex; min-height: 100vh; width: 100%; }

        /* Sidebar */
        .std-sidebar {
            width: var(--sidebar-width);
            background: #263544;
            color: #cbd5e1;
            display: flex;
            flex-direction: column;
            position: sticky;
            top: 0;
            height: 100vh;
            flex-shrink: 0;
            z-index: 60;
            box-shadow: 2px 0 16px rgba(0, 0, 0, 0.14);
            transition: transform 0.22s ease;
        }
        html.dark .std-sidebar { background: #141c2b; border-right: 1px solid rgba(255,255,255,0.07); }

        .std-brand-box {
            height: var(--header-height);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 1.15rem;
            background: rgba(11, 17, 32, 0.45);
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }
        .std-brand-link {
            display: flex;
            align-items: center;
            gap: 0.7rem;
            text-decoration: none;
            color: #fff;
            font-weight: 800;
            font-size: 1rem;
        }
        .std-brand-icon {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 55%, #ec4899 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 1.25rem;
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.4);
        }
        .std-brand-pill {
            font-size: 0.63rem;
            font-family: 'JetBrains Mono', monospace;
            padding: 2px 7px;
            border-radius: 99px;
            background: rgba(56, 189, 248, 0.18);
            color: #38bdf8;
            border: 1px solid rgba(56, 189, 248, 0.35);
            font-weight: 700;
        }

        /* Sidebar Student Mini Card */
        .std-sidebar-profile {
            margin: 0.9rem 0.85rem 0.35rem;
            padding: 0.8rem;
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.09);
            display: flex;
            align-items: center;
            gap: 0.7rem;
        }
        .std-sidebar-avatar {
            width: 42px;
            height: 52px;
            border-radius: 8px;
            object-fit: cover;
            border: 2px solid rgba(255, 255, 255, 0.4);
            flex-shrink: 0;
            background: #334155;
        }
        .std-sidebar-name {
            font-size: 0.82rem;
            font-weight: 700;
            color: #f8fafc;
            line-height: 1.25;
            display: -webkit-box;
            -webkit-line-clamp: 1;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .std-sidebar-sub {
            font-size: 0.7rem;
            color: #94a3b8;
            margin-top: 2px;
        }

        .std-nav-scroll {
            flex: 1;
            overflow-y: auto;
            padding: 0.5rem 0.75rem 1rem;
        }
        .std-nav-section {
            font-size: 0.64rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.09em;
            color: #94a3b8;
            padding: 0.95rem 0.75rem 0.35rem;
        }
        .std-nav-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.62rem 0.85rem;
            margin-bottom: 3px;
            border-radius: 10px;
            color: #cbd5e1;
            text-decoration: none;
            font-size: 0.84rem;
            font-weight: 600;
            transition: var(--transition);
        }
        .std-nav-item:hover {
            background: rgba(255, 255, 255, 0.08);
            color: #ffffff;
        }
        .std-nav-item.active {
            background: linear-gradient(135deg, #4680ff 0%, #6366f1 100%);
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(70, 128, 255, 0.35);
        }
        .std-nav-left { display: flex; align-items: center; gap: 0.7rem; }
        .std-nav-left i { font-size: 1.15rem; }
        .std-nav-badge {
            font-size: 0.63rem;
            padding: 2px 7px;
            border-radius: 99px;
            background: rgba(255, 255, 255, 0.14);
            color: #f8fafc;
            font-weight: 700;
        }

        .std-sidebar-footer {
            padding: 0.8rem 1rem;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
            font-size: 0.74rem;
            color: #94a3b8;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        /* Main Canvas */
        .std-canvas {
            flex: 1;
            min-width: 0;
            display: flex;
            flex-direction: column;
        }
        .std-topbar {
            height: var(--header-height);
            background: var(--bg-surface);
            border-bottom: 1px solid var(--border-color);
            padding: 0 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 40;
        }
        .std-topbar-left { display: flex; align-items: center; gap: 0.85rem; }
        .std-topbar-right { display: flex; align-items: center; gap: 0.65rem; }

        .icon-btn {
            width: 36px;
            height: 36px;
            border-radius: 9px;
            border: 1px solid var(--border-color);
            background: var(--bg-surface);
            color: var(--text-body);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 1.15rem;
            transition: var(--transition);
        }
        .icon-btn:hover { background: var(--bg-hover); color: var(--bento-primary); }

        .std-breadcrumb {
            display: flex;
            align-items: center;
            gap: 0.45rem;
            font-size: 0.82rem;
            color: var(--text-muted);
        }
        .std-breadcrumb a { color: var(--text-muted); text-decoration: none; font-weight: 600; }
        .std-breadcrumb .current { color: var(--text-heading); font-weight: 700; }

        .std-content {
            padding: 1.5rem;
            max-width: 1400px;
            width: 100%;
            margin: 0 auto;
            padding-bottom: 5rem;
        }

        /* Reusable Cards, Stats, Tables & Badges (Aligned with Vuexy Design System) */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
            gap: 1rem;
            margin-bottom: 1.35rem;
        }
        .stat-card {
            background: var(--bg-surface);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-xl);
            padding: 1.1rem 1.2rem;
            box-shadow: var(--shadow-card);
            display: flex;
            align-items: center;
            gap: 0.95rem;
            transition: var(--transition);
        }
        .stat-card:hover { transform: translateY(-2px); box-shadow: var(--shadow-card-hover); border-color: var(--vx-primary-border); }
        .stat-icon-wrap {
            width: 46px;
            height: 46px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            flex-shrink: 0;
        }
        .stat-icon-primary { background: var(--vx-primary-light); color: var(--vx-primary); }
        .stat-icon-success { background: var(--vx-success-light); color: var(--vx-success); }
        .stat-icon-warning { background: var(--vx-warning-light); color: var(--vx-warning); }
        .stat-icon-danger { background: var(--vx-danger-light); color: var(--vx-danger); }
        .stat-icon-info { background: var(--vx-info-light); color: var(--vx-info); }
        .stat-icon-purple { background: var(--vx-purple-light); color: var(--vx-purple); }

        .stat-body { flex: 1; min-width: 0; }
        .stat-label {
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--text-muted);
        }
        .stat-value {
            font-size: 1.38rem;
            font-weight: 800;
            color: var(--text-heading);
            letter-spacing: -0.025em;
            margin: 0.15rem 0;
        }
        .stat-sub { font-size: 0.73rem; color: var(--text-muted); }

        .apex-card {
            background: var(--bg-surface);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow-card);
            overflow: hidden;
            margin-bottom: 1.25rem;
        }
        .apex-card-header {
            padding: 1.05rem 1.3rem;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.85rem;
            flex-wrap: wrap;
        }
        .apex-card-title { margin: 0; font-size: 0.98rem; font-weight: 800; color: var(--text-heading); display: flex; align-items: center; gap: 0.45rem; }
        .apex-card-desc { margin: 0.18rem 0 0; font-size: 0.78rem; color: var(--text-muted); }
        .apex-card-body { padding: 1.2rem 1.3rem; }

        /* Badges & Buttons */
        .vx-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            padding: 0.22rem 0.6rem;
            border-radius: 999px;
            font-size: 0.72rem;
            font-weight: 700;
            line-height: 1.3;
        }
        .badge-primary { background: var(--vx-primary-light); color: var(--vx-primary); }
        .badge-success { background: var(--vx-success-light); color: var(--vx-success); }
        .badge-warning { background: var(--vx-warning-light); color: #d97706; }
        .badge-danger { background: var(--vx-danger-light); color: var(--vx-danger); }
        .badge-info { background: var(--vx-info-light); color: var(--vx-info); }
        .badge-purple { background: var(--vx-purple-light); color: var(--vx-purple); }
        .badge-dark { background: #1e293b; color: #f8fafc; }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.4rem;
            padding: 0.52rem 0.95rem;
            border-radius: var(--radius-md);
            font-size: 0.82rem;
            font-weight: 700;
            text-decoration: none;
            border: 1px solid transparent;
            cursor: pointer;
            transition: var(--transition);
        }
        .btn-sm { padding: 0.38rem 0.75rem; font-size: 0.76rem; }
        .btn-primary { background: var(--vx-primary); color: #fff; box-shadow: 0 3px 10px rgba(70,128,255,0.28); }
        .btn-primary:hover { background: var(--vx-primary-hover); transform: translateY(-1px); }
        .btn-success { background: var(--vx-success); color: #fff; box-shadow: 0 3px 10px rgba(16,185,129,0.28); }
        .btn-secondary { background: var(--bg-subtle); color: var(--text-heading); border-color: var(--border-color); }
        .btn-secondary:hover { background: var(--bg-hover); }
        .btn-white { background: #ffffff; color: #1e293b; font-weight: 800; }
        .btn-white:hover { background: #f1f5f9; }

        /* Tables */
        .table-responsive { overflow-x: auto; width: 100%; }
        .vx-table { width: 100%; border-collapse: collapse; }
        .vx-table th {
            padding: 0.75rem 1rem;
            text-align: left;
            font-size: 0.7rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--text-muted);
            background: var(--bg-subtle);
            border-bottom: 1px solid var(--border-color);
        }
        .vx-table td {
            padding: 0.8rem 1rem;
            font-size: 0.83rem;
            border-bottom: 1px solid var(--border-color);
            vertical-align: middle;
        }
        .vx-table tr:last-child td { border-bottom: none; }
        .cell-title { font-weight: 700; color: var(--text-heading); font-size: 0.84rem; }
        .cell-sub { font-size: 0.74rem; color: var(--text-muted); margin-top: 2px; }

        /* Mobile Drawer & Bottom Nav */
        .mobile-menu-btn { display: none; }
        .mobile-bottom-nav { display: none; }
        .sidebar-backdrop {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.5);
            z-index: 55;
        }

        @media (max-width: 992px) {
            .std-sidebar {
                position: fixed;
                left: 0;
                top: 0;
                bottom: 0;
                transform: translateX(-100%);
            }
            .std-sidebar.open { transform: translateX(0); }
            .sidebar-backdrop.open { display: block; }
            .mobile-menu-btn { display: inline-flex; }
            .std-content { padding: 1rem; padding-bottom: 5.2rem; }
            .mobile-bottom-nav {
                display: grid;
                grid-template-columns: repeat(5, 1fr);
                position: fixed;
                bottom: 0;
                left: 0;
                right: 0;
                background: var(--bg-surface);
                border-top: 1px solid var(--border-color);
                padding: 0.4rem 0.25rem;
                z-index: 50;
                box-shadow: 0 -4px 16px rgba(15,23,42,0.08);
            }
            .mobile-bottom-link {
                display: flex;
                flex-direction: column;
                align-items: center;
                gap: 2px;
                text-decoration: none;
                color: var(--text-muted);
                font-size: 0.65rem;
                font-weight: 700;
                padding: 0.25rem 0;
            }
            .mobile-bottom-link i { font-size: 1.2rem; }
            .mobile-bottom-link.active { color: var(--vx-primary); }
        }
    </style>
    @stack('styles')
</head>
<body>
    @php
        $avatarUrl = $student->photo_url ?: 'https://ui-avatars.com/api/?name=' . urlencode($student->full_name) . '&background=4f46e5&color=fff&size=180';
        $previewQuery = (!empty($isStaffPreview) && request('student_id')) ? ['student_id' => request('student_id')] : [];
    @endphp

    <div class="sidebar-backdrop" id="stdSidebarBackdrop" onclick="toggleStudentSidebar()"></div>

    <div class="std-shell">
        <!-- SIDEBAR PORTAL SISWA -->
        <aside class="std-sidebar" id="stdSidebar">
            <div class="std-brand-box">
                <a href="{{ route('student.dashboard', $previewQuery) }}" class="std-brand-link">
                    <div class="std-brand-icon"><i class='bx bxs-graduation'></i></div>
                    <div>
                        <div style="line-height: 1.1;">Sekolahku</div>
                        <span class="std-brand-pill">PORTAL SISWA</span>
                    </div>
                </a>
            </div>

            <div class="std-sidebar-profile">
                <img src="{{ $avatarUrl }}" alt="{{ $student->full_name }}" class="std-sidebar-avatar">
                <div style="min-width: 0;">
                    <div class="std-sidebar-name">{{ $student->full_name }}</div>
                    <div class="std-sidebar-sub cell-mono">NIS {{ $student->nis }}</div>
                    <div style="margin-top: 4px;">
                        <span class="std-nav-badge" style="background: rgba(16,185,129,0.2); color: #34d399;">
                            {{ $student->schoolClass?->name ?? 'Siswa Aktif' }}
                        </span>
                    </div>
                </div>
            </div>

            <nav class="std-nav-scroll">
                <div class="std-nav-section">Beranda Utama</div>
                <a href="{{ route('student.dashboard', $previewQuery) }}" class="std-nav-item {{ Request::routeIs('student.dashboard') ? 'active' : '' }}">
                    <div class="std-nav-left">
                        <i class='bx bx-home-smile'></i>
                        <span>Dashboard Saya</span>
                    </div>
                </a>

                <div class="std-nav-section">Akademik & KBM</div>
                <a href="{{ route('student.schedules', $previewQuery) }}" class="std-nav-item {{ Request::routeIs('student.schedules') ? 'active' : '' }}">
                    <div class="std-nav-left">
                        <i class='bx bx-calendar-event'></i>
                        <span>Jadwal Pelajaran Saya</span>
                    </div>
                    <span class="std-nav-badge">KBM</span>
                </a>

                <div class="std-nav-section">Presensi & Kehadiran</div>
                <a href="{{ route('attendance.portal') }}" class="std-nav-item">
                    <div class="std-nav-left">
                        <i class='bx bx-scan'></i>
                        <span>Absen Selfie & GPS</span>
                    </div>
                    <span class="std-nav-badge" style="background: rgba(16,185,129,0.25); color: #6ee7b7;">LIVE</span>
                </a>
                <a href="{{ route('student.attendance', $previewQuery) }}" class="std-nav-item {{ Request::routeIs('student.attendance') ? 'active' : '' }}">
                    <div class="std-nav-left">
                        <i class='bx bx-check-shield'></i>
                        <span>Rekap Kehadiran Saya</span>
                    </div>
                </a>

                <div class="std-nav-section">Kesiswaan & Disiplin</div>
                <a href="{{ route('student.discipline', $previewQuery) }}" class="std-nav-item {{ Request::routeIs('student.discipline') ? 'active' : '' }}">
                    <div class="std-nav-left">
                        <i class='bx bx-award'></i>
                        <span>Tata Tertib & Poin Saya</span>
                    </div>
                    <span class="std-nav-badge">Tatib</span>
                </a>

                <div class="std-nav-section">Identitas & Dapodik</div>
                <a href="{{ route('student.card', $previewQuery) }}" class="std-nav-item {{ Request::routeIs('student.card') ? 'active' : '' }}">
                    <div class="std-nav-left">
                        <i class='bx bx-id-card'></i>
                        <span>Kartu Pelajar Digital</span>
                    </div>
                    <span class="std-nav-badge">4x6</span>
                </a>
                <a href="{{ route('student.profile', $previewQuery) }}" class="std-nav-item {{ Request::routeIs('student.profile') ? 'active' : '' }}">
                    <div class="std-nav-left">
                        <i class='bx bx-user-pin'></i>
                        <span>Biodata Mandiri</span>
                    </div>
                </a>

                @if(!empty($isStaffPreview))
                    <div class="std-nav-section">Navigasi Staf</div>
                    <a href="{{ route('admin.dashboard') }}" class="std-nav-item">
                        <div class="std-nav-left">
                            <i class='bx bx-arrow-back'></i>
                            <span>Kembali ke Panel Admin</span>
                        </div>
                    </a>
                @endif
            </nav>

            <div class="std-sidebar-footer">
                <span style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $school->name ?? 'Sekolahku' }}</span>
                <span class="std-brand-pill">{{ $activeYear?->name ?? 'Aktif' }}</span>
            </div>
        </aside>

        <!-- MAIN CONTENT AREA -->
        <div class="std-canvas">
            <header class="std-topbar">
                <div class="std-topbar-left">
                    <button type="button" class="icon-btn mobile-menu-btn" onclick="toggleStudentSidebar()" title="Menu">
                        <i class='bx bx-menu'></i>
                    </button>
                    <div class="std-breadcrumb">
                        <a href="{{ route('student.dashboard', $previewQuery) }}">Portal Siswa</a>
                        <span>/</span>
                        <span class="current">@yield('breadcrumb', 'Dashboard')</span>
                    </div>
                </div>

                <div class="std-topbar-right">
                    <span class="vx-badge badge-primary cell-mono" style="display: none;" id="liveClockPill"></span>

                    <button type="button" class="icon-btn" onclick="toggleStudentTheme()" title="Mode Terang / Gelap">
                        <i class='bx bx-moon' id="stdThemeIcon"></i>
                    </button>

                    <a href="{{ route('attendance.portal') }}" class="btn btn-primary btn-sm">
                        <i class='bx bx-camera'></i>
                        <span>Presensi GPS</span>
                    </a>

                    <form action="{{ route('logout') }}" method="POST" style="margin: 0;">
                        @csrf
                        <button type="submit" class="btn btn-secondary btn-sm" title="Keluar Akun">
                            <i class='bx bx-log-out' style="color: var(--vx-danger);"></i>
                            <span>Keluar</span>
                        </button>
                    </form>
                </div>
            </header>

            <main class="std-content">
                @if(!empty($isStaffPreview))
                    <div style="padding: 0.7rem 1rem; border-radius: var(--radius-md); background: var(--vx-info-light); border: 1px solid rgba(2,132,199,0.25); color: var(--vx-info); font-size: 0.82rem; font-weight: 600; margin-bottom: 1rem; display: flex; align-items: center; justify-content: space-between; gap: 0.75rem; flex-wrap: wrap;">
                        <span><i class='bx bx-show'></i> <strong>Mode Pratinjau Staf:</strong> Menampilkan Portal Siswa atas nama <strong>{{ $student->full_name }} ({{ $student->schoolClass?->name ?? '-' }})</strong>.</span>
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary btn-sm">Kembali ke Panel Admin</a>
                    </div>
                @endif

                @if(session('login_success') || session('success'))
                    <div style="padding: 0.75rem 1rem; border-radius: var(--radius-md); background: var(--vx-success-light); border: 1px solid var(--vx-success-border); color: var(--vx-success); font-size: 0.84rem; font-weight: 600; margin-bottom: 1.15rem; display: flex; align-items: center; gap: 0.5rem;">
                        <i class='bx bx-check-circle' style="font-size: 1.15rem;"></i>
                        <span>{{ session('login_success') ?? session('success') }}</span>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <!-- Mobile Bottom Navigation -->
    <nav class="mobile-bottom-nav">
        <a href="{{ route('student.dashboard', $previewQuery) }}" class="mobile-bottom-link {{ Request::routeIs('student.dashboard') ? 'active' : '' }}">
            <i class='bx bx-home-smile'></i>
            <span>Beranda</span>
        </a>
        <a href="{{ route('student.schedules', $previewQuery) }}" class="mobile-bottom-link {{ Request::routeIs('student.schedules') ? 'active' : '' }}">
            <i class='bx bx-calendar-event'></i>
            <span>Jadwal</span>
        </a>
        <a href="{{ route('attendance.portal') }}" class="mobile-bottom-link">
            <i class='bx bx-scan'></i>
            <span>Presensi</span>
        </a>
        <a href="{{ route('student.discipline', $previewQuery) }}" class="mobile-bottom-link {{ Request::routeIs('student.discipline') ? 'active' : '' }}">
            <i class='bx bx-award'></i>
            <span>Tatib</span>
        </a>
        <a href="{{ route('student.card', $previewQuery) }}" class="mobile-bottom-link {{ Request::routeIs('student.card') ? 'active' : '' }}">
            <i class='bx bx-id-card'></i>
            <span>Kartu</span>
        </a>
    </nav>

    <script>
        function toggleStudentSidebar() {
            document.getElementById('stdSidebar')?.classList.toggle('open');
            document.getElementById('stdSidebarBackdrop')?.classList.toggle('open');
        }

        function syncStudentThemeIcon() {
            const isDark = document.documentElement.classList.contains('dark');
            const ic = document.getElementById('stdThemeIcon');
            if (ic) ic.className = isDark ? 'bx bx-sun' : 'bx bx-moon';
        }

        function toggleStudentTheme() {
            const isDark = document.documentElement.classList.toggle('dark');
            localStorage.setItem('apex_theme', isDark ? 'dark' : 'light');
            syncStudentThemeIcon();
        }

        function updateLiveClock() {
            const pill = document.getElementById('liveClockPill');
            if (!pill) return;
            const now = new Date();
            pill.style.display = 'inline-flex';
            pill.innerHTML = `<i class='bx bx-time-five'></i> ${now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' })} WIB`;
        }

        document.addEventListener('DOMContentLoaded', () => {
            syncStudentThemeIcon();
            updateLiveClock();
            setInterval(updateLiveClock, 15000);
        });
    </script>
    @stack('scripts')
</body>
</html>

