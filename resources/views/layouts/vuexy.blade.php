<!DOCTYPE html>
<html lang="id" dir="ltr" class="">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>@yield('title', 'Dashboard') — Apex Admin Sekolahku</title>
    <meta name="description" content="@yield('description', 'Apex Admin — Sistem Manajemen Sekolah Terpadu Berbasis shadcn/ui & Instrument Sans')" />
    <link rel="icon" type="image/x-icon" href="/favicon.ico" />

    <!-- Admindek Instant Theme & Preset Detection (Zero-Flicker) -->
    <script>
        (function() {
            // Theme Mode
            const savedTheme = localStorage.getItem('apex_theme') || 'light';
            if (savedTheme === 'dark' || (savedTheme === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
            // Admindek Color Preset
            const savedColor = localStorage.getItem('admindek_preset') || '#4680ff';
            document.documentElement.style.setProperty('--bento-primary', savedColor);
            document.documentElement.style.setProperty('--vx-primary', savedColor);
            document.documentElement.style.setProperty('--apex-primary', savedColor);
            document.documentElement.style.setProperty('--admindek-primary', savedColor);
            // Layout width
            if (localStorage.getItem('admindek_layout') === 'boxed') {
                document.documentElement.classList.add('layout-boxed');
            }
            // Sidebar Caption
            if (localStorage.getItem('admindek_sidebar_caption') === 'hide') {
                document.documentElement.classList.add('sidebar-hide-caption');
            }
        })();
    </script>

    <!-- Typography: Inter (Admindek Signature), Outfit, Plus Jakarta Sans & JetBrains Mono -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Outfit:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Chart.js for High-Performance Analytics -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Icons: Boxicons & Lucide/FontAwesome -->
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        /* ===== ADMINDEK MODERN DESIGN SYSTEM ===== */
        :root {
            /* Admindek Light Tokens (Clean, Crisp, Professional Dashboard) */
            --bg-app:             #f4f7fa;
            --bg-surface:         #ffffff;
            --bg-subtle:          #f0f4f8;
            --bg-muted:           #e2e8f0;
            --border-color:       #e3e8ee;
            --border-subtle:      #edf2f7;
            --text-heading:       #1e293b;
            --text-body:          #475569;
            --text-muted:         #64748b;
            --text-placeholder:   #94a3b8;
            
            /* Dynamic Admindek Primary */
            --bento-primary:      #4680ff;
            --bento-primary-grad: linear-gradient(135deg, #3069ec 0%, #4680ff 60%, #6899ff 100%);
            --bento-primary-text: #ffffff;
            --bento-primary-glow: rgba(70, 128, 255, 0.25);
            --admindek-primary:   #4680ff;
            --admindek-navy:      #263544;
            
            --vx-success:         #2ca87f;
            --vx-success-light:   rgba(44, 168, 127, 0.12);
            --vx-success-border:  rgba(44, 168, 127, 0.28);
            --vx-warning:         #e58a00;
            --vx-warning-light:   rgba(229, 138, 0, 0.12);
            --vx-warning-border:  rgba(229, 138, 0, 0.28);
            --vx-info:            #04a9f5;
            --vx-info-light:      rgba(4, 169, 245, 0.12);
            --vx-info-border:     rgba(4, 169, 245, 0.28);
            --vx-danger:          #dc2626;
            --vx-danger-light:    rgba(220, 38, 38, 0.12);
            --vx-danger-border:   rgba(220, 38, 38, 0.28);
            --vx-purple:          #7c4dff;
            --vx-purple-light:    rgba(124, 77, 255, 0.12);

            /* Compatibility Tokens */
            --vx-primary:         #4680ff;
            --vx-primary-light:   rgba(70, 128, 255, 0.12);
            --vx-primary-border:  rgba(70, 128, 255, 0.28);
            --vx-border-light:    #e3e8ee;
            --vx-text-heading:    #1e293b;
            --vx-text-body:       #475569;
            --vx-text-muted:      #64748b;
            --vx-radius-sm:       4px;
            --vx-radius-md:       6px;
            --vx-radius-lg:       8px;
            --vx-radius-xl:       10px;
            
            --apex-primary:       #4680ff;
            --apex-primary-text:  #ffffff;
            --apex-indigo:        #4680ff;
            --apex-indigo-light:  rgba(70, 128, 255, 0.12);
            --apex-indigo-border: rgba(70, 128, 255, 0.28);

            /* Admindek Spec Radius & Dimensions */
            --radius-sm:          4px;
            --radius-md:          4px;
            --radius-lg:          6px;
            --radius-xl:          8px;
            --radius-pill:        999px;

            --sidebar-width:      264px;
            --sidebar-collapsed:  80px;
            --header-height:      74px;

            --shadow-subtle:      0 1px 3px 0 rgba(69, 90, 100, 0.06);
            --shadow-card:        0 1px 4px 0 rgba(69, 90, 100, 0.08);
            --shadow-card-hover:  0 8px 24px -2px rgba(69, 90, 100, 0.14);
            --shadow-dropdown:    0 12px 32px -4px rgba(69, 90, 100, 0.18);
            
            --transition:         all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        }

        /* Dark Mode Tokens (Admindek Slate Dark #212224 / #1a1b1d) */
        html.dark {
            --bg-app:             #212224;
            --bg-surface:         #1a1b1d;
            --bg-subtle:          #282a2d;
            --bg-muted:           #323539;
            --border-color:       #323539;
            --border-subtle:      #2b2d30;
            --text-heading:       #f8fafc;
            --text-body:          #cbd5e1;
            --text-muted:         #94a3b8;
            --text-placeholder:   #64748b;
            
            --bento-primary:      #4680ff;
            --bento-primary-grad: linear-gradient(135deg, #3069ec 0%, #4680ff 60%, #6899ff 100%);
            --bento-primary-text: #ffffff;
            --bento-primary-glow: rgba(70, 128, 255, 0.35);

            --vx-success:         #2ca87f;
            --vx-success-light:   rgba(44, 168, 127, 0.18);
            --vx-success-border:  rgba(44, 168, 127, 0.35);
            --vx-warning:         #e58a00;
            --vx-warning-light:   rgba(229, 138, 0, 0.18);
            --vx-warning-border:  rgba(229, 138, 0, 0.35);
            --vx-info:            #04a9f5;
            --vx-info-light:      rgba(4, 169, 245, 0.18);
            --vx-info-border:     rgba(4, 169, 245, 0.35);
            --vx-danger:          #f87171;
            --vx-danger-light:    rgba(248, 113, 113, 0.18);
            --vx-danger-border:   rgba(248, 113, 113, 0.35);
            --vx-purple:          #a78bfa;
            --vx-purple-light:    rgba(167, 139, 250, 0.18);

            /* Dark Compatibility Tokens */
            --vx-primary:         #4680ff;
            --vx-primary-light:   rgba(70, 128, 255, 0.18);
            --vx-primary-border:  rgba(70, 128, 255, 0.35);
            --vx-border-light:    #323539;
            --vx-text-heading:    #f8fafc;
            --vx-text-body:       #cbd5e1;
            --vx-text-muted:      #94a3b8;
            
            --apex-primary:       #4680ff;
            --apex-primary-text:  #ffffff;
            --apex-indigo:        #6899ff;
            --apex-indigo-light:  rgba(104, 153, 255, 0.18);
            --apex-indigo-border: rgba(104, 153, 255, 0.35);

            --shadow-subtle:      0 1px 3px 0 rgba(0, 0, 0, 0.5);
            --shadow-card:        0 2px 10px 0 rgba(0, 0, 0, 0.35);
            --shadow-card-hover:  0 8px 24px -2px rgba(0, 0, 0, 0.55);
            --shadow-dropdown:    0 16px 36px -4px rgba(0, 0, 0, 0.7);
        }

        *, *::before, *::after { box-sizing: border-box; }
        
        html, body {
            min-height: 100%;
            margin: 0;
            padding: 0;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: var(--bg-app);
            color: var(--text-body);
            -webkit-font-smoothing: antialiased;
            letter-spacing: -0.011em;
            transition: background-color 0.2s ease, color 0.2s ease;
        }

        h1, h2, h3, h4, h5, h6, .brand-link-apex, .stat-value, .master-stat-value, .cell-title {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            letter-spacing: -0.02em;
        }

        button, input, select, textarea {
            font-family: inherit;
        }

        /* === BOXED & CAPTION TOGGLE BEHAVIORS === */
        html.layout-boxed .apex-content-body {
            max-width: 1200px !important;
            margin: 0 auto !important;
        }
        html.sidebar-hide-caption .nav-section-title {
            display: none !important;
        }

        /* === GLOBAL UTILITY & LAYOUT PRIMITIVES === */
        .d-flex { display: flex; }
        .align-center { align-items: center; }
        .justify-between { justify-content: space-between; }
        .justify-center { justify-center: center; }
        .gap-05 { gap: 0.25rem; }
        .gap-1 { gap: 0.5rem; }
        .gap-15 { gap: 0.75rem; }
        .gap-2 { gap: 1rem; }
        .flex-wrap { flex-wrap: wrap; }
        .flex-col { flex-direction: column; }
        .fw-600 { font-weight: 600; }
        .fw-700 { font-weight: 700; }
        .fw-800 { font-weight: 800; }
        .text-primary { color: var(--vx-primary) !important; }
        .text-muted { color: var(--text-muted) !important; }
        .text-success { color: var(--vx-success) !important; }
        .text-warning { color: var(--vx-warning) !important; }
        .text-danger { color: var(--vx-danger) !important; }
        .text-info { color: var(--vx-info) !important; }

        /* Page Title Row */
        .page-title-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }
        .page-heading h1 {
            font-size: 1.45rem;
            font-weight: 800;
            color: var(--text-heading);
            margin: 0 0 0.35rem 0;
            letter-spacing: -0.025em;
            display: flex;
            align-items: center;
            gap: 0.65rem;
            flex-wrap: wrap;
        }
        .page-heading p {
            font-size: 0.85rem;
            color: var(--text-muted);
            margin: 0;
            line-height: 1.45;
        }
        .page-actions {
            display: flex;
            align-items: center;
            gap: 0.65rem;
            flex-wrap: wrap;
        }

        /* Standard KPI Stats Grid & Stat Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 1rem;
            margin-bottom: 1.5rem;
        }
        .stat-card {
            background-color: var(--bg-surface);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-xl);
            padding: 1.15rem 1.25rem;
            box-shadow: var(--shadow-card);
            display: flex;
            align-items: center;
            gap: 1rem;
            transition: var(--transition);
            position: relative;
            overflow: hidden;
        }
        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-card-hover);
            border-color: var(--bento-primary);
        }
        .stat-icon-wrap {
            width: 44px;
            height: 44px;
            border-radius: var(--radius-md);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.35rem;
            flex-shrink: 0;
        }
        .stat-icon-primary { background: var(--vx-primary-light); color: var(--vx-primary); }
        .stat-icon-info { background: var(--vx-info-light); color: var(--vx-info); }
        .stat-icon-warning { background: var(--vx-warning-light); color: var(--vx-warning); }
        .stat-icon-success { background: var(--vx-success-light); color: var(--vx-success); }
        .stat-icon-danger { background: var(--vx-danger-light); color: var(--vx-danger); }
        .stat-icon-purple { background: var(--vx-purple-light); color: var(--vx-purple); }

        .stat-body {
            flex: 1;
            min-width: 0;
        }
        .stat-label {
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--text-muted);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .stat-value {
            font-size: 1.45rem;
            font-weight: 800;
            color: var(--text-heading);
            letter-spacing: -0.03em;
            line-height: 1.2;
            margin: 0.2rem 0;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .stat-sub {
            font-size: 0.72rem;
            color: var(--text-muted);
            display: flex;
            align-items: center;
            gap: 0.35rem;
            white-space: nowrap;
        }
        .stat-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            display: inline-block;
        }
        .stat-dot-success { background-color: var(--vx-success); }
        .stat-dot-warning { background-color: var(--vx-warning); }
        .stat-dot-info { background-color: var(--vx-info); }
        .stat-dot-danger { background-color: var(--vx-danger); }

        /* === APEX APP SHELL LAYOUT === */
        .apex-shell {
            display: flex;
            min-height: 100vh;
            width: 100%;
            background-color: var(--bg-app);
        }

        /* === APEX SIDEBAR (ADMINDEK SIGNATURE NAVY #263544) === */
        .apex-sidebar {
            width: var(--sidebar-width);
            background: #263544;
            border-right: 1px solid rgba(0, 0, 0, 0.12);
            display: flex;
            flex-direction: column;
            position: sticky;
            top: 0;
            height: 100vh;
            flex-shrink: 0;
            transition: width 0.22s cubic-bezier(0.16, 1, 0.3, 1), transform 0.22s ease;
            z-index: 50;
            box-shadow: 2px 0 16px rgba(0, 0, 0, 0.14);
        }
        html.dark .apex-sidebar {
            background: #1a1b1d;
            border-right: 1px solid rgba(255, 255, 255, 0.08);
        }
        .apex-sidebar.collapsed {
            width: var(--sidebar-collapsed);
        }

        /* Sidebar Brand Header */
        .sidebar-brand-box {
            height: var(--header-height);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 1.15rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            background: rgba(11, 17, 32, 0.5);
            backdrop-filter: blur(8px);
        }
        .brand-link-apex {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            text-decoration: none;
            color: #F8FAFC;
            font-weight: 800;
            font-size: 1.05rem;
            letter-spacing: -0.025em;
        }
        .brand-icon-apex {
            width: 38px;
            height: 38px;
            border-radius: var(--radius-md);
            background: linear-gradient(135deg, #6366F1 0%, #8B5CF6 50%, #EC4899 100%);
            color: #FFFFFF;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.35rem;
            flex-shrink: 0;
            box-shadow: 0 4px 16px rgba(99, 102, 241, 0.45);
            transition: transform 0.2s ease;
        }
        .brand-link-apex:hover .brand-icon-apex {
            transform: scale(1.06) rotate(3deg);
        }
        .brand-name-apex {
            display: flex;
            align-items: center;
            gap: 0.45rem;
            white-space: nowrap;
        }
        .brand-name-apex span {
            background: linear-gradient(135deg, #FFFFFF 60%, #CBD5E1 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            font-weight: 800;
        }
        .brand-tag-apex {
            font-size: 0.65rem;
            font-family: 'JetBrains Mono', monospace;
            padding: 2px 7px;
            border-radius: var(--radius-pill);
            background: rgba(16, 185, 129, 0.15);
            color: #34D399;
            font-weight: 700;
            border: 1px solid rgba(52, 211, 153, 0.35);
            box-shadow: 0 0 10px rgba(16, 185, 129, 0.15);
        }
        .apex-sidebar.collapsed .brand-name-apex { display: none; }

        .sidebar-brand-box .topbar-btn-icon {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #94A3B8;
        }
        .sidebar-brand-box .topbar-btn-icon:hover {
            color: #FFFFFF;
            background: rgba(255, 255, 255, 0.12);
            border-color: rgba(255, 255, 255, 0.2);
        }

        /* Sidebar Navigation Scroll Area */
        .sidebar-nav-scroll {
            flex: 1;
            overflow-y: auto;
            padding: 0.95rem 0.75rem;
            scrollbar-width: thin;
        }
        .sidebar-nav-scroll::-webkit-scrollbar {
            width: 4px;
        }
        .sidebar-nav-scroll::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 4px;
        }

        .nav-section-title {
            font-size: 0.65rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: #64748B;
            padding: 1rem 0.85rem 0.35rem;
            white-space: nowrap;
            overflow: hidden;
            display: flex;
            align-items: center;
            gap: 0.45rem;
        }
        .nav-section-title::before {
            content: '';
            display: inline-block;
            width: 5px;
            height: 5px;
            border-radius: 50%;
            background: var(--admindek-primary, #4680ff);
            box-shadow: 0 0 6px var(--admindek-primary, #4680ff);
        }
        .apex-sidebar.collapsed .nav-section-title { opacity: 0; pointer-events: none; }

        .apex-nav-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.65rem 0.85rem;
            border-radius: var(--radius-md);
            text-decoration: none;
            color: #94A3B8;
            font-size: 0.88rem;
            font-weight: 600;
            transition: var(--transition);
            margin-bottom: 4px;
            cursor: pointer;
            border: 1px solid transparent;
            position: relative;
            overflow: hidden;
        }
        .apex-nav-item:hover {
            background: rgba(255, 255, 255, 0.06);
            color: #FFFFFF;
            transform: translateX(3px);
            border-color: rgba(255, 255, 255, 0.08);
        }
        .apex-nav-item.active {
            background: var(--admindek-primary, #4680ff);
            color: #FFFFFF;
            font-weight: 700;
            box-shadow: 0 4px 16px rgba(70, 128, 255, 0.35), inset 0 1px 0 rgba(255, 255, 255, 0.2);
            border-color: rgba(255, 255, 255, 0.15);
            transform: translateX(2px);
        }
        .apex-nav-item.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 3.5px;
            height: 20px;
            border-radius: 0 4px 4px 0;
            background: #38BDF8;
            box-shadow: 0 0 10px #38BDF8;
        }
        .apex-nav-item.active .apex-nav-icon {
            color: #FFFFFF !important;
            filter: drop-shadow(0 0 6px rgba(255, 255, 255, 0.6));
        }
        .apex-nav-item.active .apex-nav-badge {
            background: rgba(255, 255, 255, 0.22);
            color: #FFFFFF;
            border-color: rgba(255, 255, 255, 0.4);
        }
        .apex-nav-left {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .apex-nav-icon {
            font-size: 1.2rem;
            min-width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 6px;
            transition: var(--transition);
            color: #94A3B8;
        }
        .apex-nav-item:hover .apex-nav-icon {
            color: #FFFFFF;
            transform: scale(1.1);
        }

        /* Distinct Vibrant Icon Color Tokens */
        .nav-icon-overview    { color: #818CF8; }
        .nav-icon-students    { color: #38BDF8; }
        .nav-icon-teachers    { color: #34D399; }
        .nav-icon-classes     { color: #FBBF24; }
        .nav-icon-years       { color: #FB7185; }
        .nav-icon-curriculums { color: #A78BFA; }
        .nav-icon-schedules   { color: #60A5FA; }
        .nav-icon-schools     { color: #F472B6; }
        .nav-icon-attendance  { color: #4ADE80; }
        .nav-icon-subjects    { color: #2DD4BF; }
        .nav-icon-cmd         { color: #CBD5E1; }

        .apex-sidebar.collapsed .apex-nav-label { display: none; }
        .apex-sidebar.collapsed .apex-nav-badge { display: none; }
        .apex-sidebar.collapsed .apex-nav-item { justify-content: center; padding: 0.65rem; }

        .apex-nav-badge {
            font-size: 0.65rem;
            padding: 0.12rem 0.5rem;
            border-radius: var(--radius-pill);
            background: rgba(255, 255, 255, 0.08);
            color: #CBD5E1;
            font-weight: 700;
            font-family: 'JetBrains Mono', monospace;
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: var(--transition);
        }
        .badge-gps {
            background: rgba(16, 185, 129, 0.2) !important;
            color: #34D399 !important;
            border-color: rgba(52, 211, 153, 0.4) !important;
            box-shadow: 0 0 8px rgba(16, 185, 129, 0.2);
        }

        /* Sidebar Footer Status */
        .sidebar-footer-box {
            padding: 0.85rem 1.1rem;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 0.8rem;
            color: #94A3B8;
            background: rgba(11, 17, 32, 0.6);
        }
        .sidebar-footer-school {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 600;
            color: #E2E8F0;
            max-width: 175px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .status-indicator-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: #10B981;
            box-shadow: 0 0 8px #10B981;
            flex-shrink: 0;
            animation: statusPulse 2s infinite ease-in-out;
        }
        @keyframes statusPulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.5; transform: scale(1.2); }
        }
        .apex-sidebar.collapsed .sidebar-footer-box { display: none; }

        /* === APEX MAIN CANVAS AREA === */
        .apex-canvas {
            flex: 1;
            display: flex;
            flex-direction: column;
            min-width: 0;
            background-color: var(--bg-app);
        }

        /* === APEX TOPBAR (STICKY) === */
        .apex-topbar {
            height: var(--header-height);
            background-color: var(--bg-surface);
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 1.25rem;
            position: sticky;
            top: 0;
            z-index: 40;
        }
        .topbar-left {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        .topbar-btn-icon {
            background: transparent;
            border: 1px solid var(--border-color);
            border-radius: var(--radius-md);
            color: var(--text-muted);
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: var(--transition);
            font-size: 1.15rem;
        }
        .topbar-btn-icon:hover {
            color: var(--text-heading);
            background-color: var(--bg-subtle);
        }

        .apex-breadcrumbs {
            display: flex;
            align-items: center;
            gap: 0.45rem;
            font-size: 0.82rem;
            font-weight: 500;
            color: var(--text-muted);
        }
        .apex-breadcrumbs a {
            color: var(--text-muted);
            text-decoration: none;
        }
        .apex-breadcrumbs a:hover { color: var(--text-heading); }
        .apex-breadcrumbs .current {
            color: var(--text-heading);
            font-weight: 600;
        }

        /* Topbar Center / Spotlight Search */
        .topbar-center {
            display: flex;
            align-items: center;
        }
        .apex-search-pill {
            display: flex;
            align-items: center;
            gap: 0.65rem;
            background-color: var(--bg-subtle);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-md);
            padding: 0.35rem 0.75rem;
            color: var(--text-muted);
            font-size: 0.82rem;
            cursor: pointer;
            transition: var(--transition);
            width: 260px;
        }
        .apex-search-pill:hover {
            border-color: var(--text-muted);
            color: var(--text-heading);
        }
        .apex-search-kbd {
            margin-left: auto;
            background: var(--bg-surface);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-sm);
            padding: 0.05rem 0.35rem;
            font-size: 0.68rem;
            font-weight: 700;
            font-family: 'JetBrains Mono', monospace;
            color: var(--text-muted);
        }

        /* Topbar Right Actions */
        .topbar-right {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        /* School Switcher Pill */
        .school-pill-apex {
            display: flex;
            align-items: center;
            gap: 0.45rem;
            background: var(--bg-subtle);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-md);
            padding: 0.32rem 0.75rem;
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--text-heading);
            cursor: pointer;
            transition: var(--transition);
        }
        .school-pill-apex:hover { background-color: var(--border-color); }

        /* User Menu Pill */
        .user-pill-apex {
            display: flex;
            align-items: center;
            gap: 0.55rem;
            background: transparent;
            border: 1px solid transparent;
            border-radius: var(--radius-md);
            padding: 0.25rem 0.5rem;
            cursor: pointer;
            transition: var(--transition);
        }
        .user-pill-apex:hover {
            background-color: var(--bg-subtle);
            border-color: var(--border-color);
        }
        .user-avatar-apex {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: var(--text-heading);
            color: var(--bg-surface);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
            font-weight: 700;
            flex-shrink: 0;
        }

        /* === APEX MAIN PAGE BODY === */
        .apex-content-body {
            flex: 1;
            padding: 1.5rem 1.75rem;
            max-width: 1440px;
            width: 100%;
            margin: 0 auto;
        }

        /* === SHADCN / APEX CARD PRIMITIVES === */
        .apex-card, .vx-card, .panel {
            background-color: var(--bg-surface);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-card);
            margin-bottom: 1.5rem;
            overflow: hidden;
            transition: var(--transition);
        }
        .apex-card-header, .vx-card-header, .panel-header {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 0.75rem;
        }
        .apex-card-title, .vx-card-title, .panel-title h2 {
            font-size: 0.98rem;
            font-weight: 700;
            color: var(--text-heading);
            margin: 0;
            letter-spacing: -0.015em;
        }
        .apex-card-desc, .vx-card-subtitle, .panel-title p {
            font-size: 0.8rem;
            color: var(--text-muted);
            margin: 2px 0 0;
        }
        .apex-card-body, .vx-card-body {
            padding: 1.5rem;
        }

        /* === SHADCN BUTTONS === */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.45rem;
            padding: 0.48rem 0.95rem;
            border-radius: var(--radius-md);
            font-family: inherit;
            font-size: 0.84rem;
            font-weight: 600;
            border: 1px solid transparent;
            cursor: pointer;
            transition: var(--transition);
            text-decoration: none;
            white-space: nowrap;
            line-height: 1.4;
        }
        .btn:active { transform: scale(0.98); }
        .btn-primary {
            background-color: var(--apex-primary);
            color: var(--apex-primary-text);
            border-color: transparent;
        }
        .btn-primary:hover {
            opacity: 0.92;
            color: var(--apex-primary-text);
        }
        .btn-secondary {
            background-color: var(--bg-surface);
            color: var(--text-heading);
            border-color: var(--border-color);
        }
        .btn-secondary:hover {
            background-color: var(--bg-subtle);
            color: var(--text-heading);
        }
        .btn-outline {
            background-color: transparent;
            color: var(--text-heading);
            border-color: var(--border-color);
        }
        .btn-outline:hover {
            background-color: var(--bg-subtle);
        }
        .btn-danger {
            background-color: var(--vx-danger);
            color: #ffffff;
            border-color: transparent;
        }
        .btn-danger:hover {
            opacity: 0.92;
            color: #ffffff;
        }
        .btn-success {
            background-color: var(--vx-success);
            color: #ffffff;
            border-color: transparent;
        }
        .btn-success:hover {
            opacity: 0.92;
            color: #ffffff;
        }
        .btn-sm { padding: 0.35rem 0.75rem; font-size: 0.78rem; }
        .btn-xs { padding: 0.22rem 0.55rem; font-size: 0.72rem; }

        /* === SHADCN TABLES === */
        .table-wrap, .table-responsive {
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        .clean-table, .vx-table, .apex-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            font-size: 0.85rem;
        }
        .clean-table thead th, .vx-table thead th, .apex-table thead th {
            padding: 0.75rem 1rem;
            text-align: left;
            font-size: 0.74rem;
            font-weight: 600;
            color: var(--text-muted);
            border-bottom: 1px solid var(--border-color);
            background-color: var(--bg-subtle);
            white-space: nowrap;
        }
        .clean-table tbody td, .vx-table tbody td, .apex-table tbody td {
            padding: 0.85rem 1rem;
            border-bottom: 1px solid var(--border-subtle);
            color: var(--text-body);
            vertical-align: middle;
            background-color: var(--bg-surface);
        }
        .clean-table tbody tr:hover td, .vx-table tbody tr:hover td, .apex-table tbody tr:hover td {
            background-color: var(--bg-subtle);
        }
        .clean-table tbody tr:last-child td, .vx-table tbody tr:last-child td, .apex-table tbody tr:last-child td {
            border-bottom: none;
        }
        .cell-title { font-weight: 600; color: var(--text-heading); font-size: 0.86rem; }
        .cell-sub { font-size: 0.75rem; color: var(--text-muted); margin-top: 1px; }
        .cell-mono, .mono { font-family: 'JetBrains Mono', monospace; font-size: 0.78rem; }

        /* === SHADCN BADGES === */
        .vx-badge, .chip {
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            padding: 0.15rem 0.55rem;
            border-radius: var(--radius-sm);
            font-size: 0.72rem;
            font-weight: 600;
            line-height: 1.35;
            white-space: nowrap;
            border: 1px solid transparent;
        }
        .badge-primary, .chip-primary { background: var(--apex-indigo-light); color: var(--apex-indigo); border-color: var(--apex-indigo-border); }
        .badge-success, .chip-success { background: var(--vx-success-light); color: var(--vx-success); border-color: var(--vx-success-border); }
        .badge-warning, .chip-warning { background: var(--vx-warning-light); color: var(--vx-warning); border-color: var(--vx-warning-border); }
        .badge-danger,  .chip-danger  { background: var(--vx-danger-light); color: var(--vx-danger); border-color: var(--vx-danger-border); }
        .badge-info,    .chip-info    { background: var(--vx-info-light); color: var(--vx-info); border-color: var(--vx-info-border); }
        .badge-dark                   { background: var(--bg-muted); color: var(--text-heading); border-color: var(--border-color); }

        /* === SHADCN INPUTS & FORMS === */
        .vx-input, .form-input-clean, .search-input, .select-control, input[type="text"], input[type="date"], input[type="email"], input[type="password"], input[type="number"], select, textarea {
            padding: 0.5rem 0.85rem;
            border: 1px solid var(--border-color);
            border-radius: var(--radius-md);
            font-family: inherit;
            font-size: 0.85rem;
            color: var(--text-heading);
            background-color: var(--bg-surface);
            outline: none;
            transition: var(--transition);
            width: 100%;
        }
        .vx-input:focus, .form-input-clean:focus, .search-input:focus, .select-control:focus, input:focus, select:focus, textarea:focus {
            border-color: var(--apex-primary);
            box-shadow: 0 0 0 1px var(--apex-primary);
        }
        .vx-label, label {
            display: block;
            font-size: 0.78rem;
            font-weight: 600;
            color: var(--text-heading);
            margin-bottom: 0.35rem;
        }
        .form-row { margin-bottom: 0.85rem; }
        .vx-form-grid, .form-grid-2 {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 0.85rem;
        }
        .form-grid-3 { display: grid; grid-template-columns: repeat(3, 1fr); gap: 0.85rem; }
        .span-2 { grid-column: span 2; }
        .span-3 { grid-column: span 3; }

        /* === APEX TABS COMPONENT (shadcn TabsList & TabsTrigger) === */
        .tab-pane { display: none; }
        .tab-pane.active { display: block; animation: tabFadeIn 0.2s cubic-bezier(0.16, 1, 0.3, 1); }
        @keyframes tabFadeIn {
            from { opacity: 0; transform: translateY(4px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .apex-tabs-nav {
            display: flex;
            align-items: center;
            gap: 0.3rem;
            padding: 0.3rem;
            background-color: var(--bg-subtle);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-lg);
            overflow-x: auto;
            margin-bottom: 1.5rem;
            scrollbar-width: none;
        }
        .apex-tabs-nav::-webkit-scrollbar { display: none; }
        .apex-tab-pill {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            padding: 0.45rem 0.85rem;
            border-radius: var(--radius-md);
            font-family: inherit;
            font-size: 0.82rem;
            font-weight: 500;
            color: var(--text-muted);
            text-decoration: none;
            cursor: pointer;
            white-space: nowrap;
            border: 1px solid transparent;
            background: transparent;
            transition: var(--transition);
        }
        .apex-tab-pill:hover {
            color: var(--text-heading);
            background-color: rgba(255, 255, 255, 0.5);
        }
        html.dark .apex-tab-pill:hover {
            background-color: rgba(255, 255, 255, 0.05);
        }
        .apex-tab-pill.active {
            background: var(--bento-primary-grad);
            color: #FFFFFF;
            font-weight: 700;
            box-shadow: 0 4px 14px var(--bento-primary-glow);
            border-color: transparent;
        }
        .apex-tab-pill.active i {
            color: #FFFFFF;
        }

        /* === BENTO SCHEDULES & CATEGORY CARDS === */
        .cat-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(290px, 1fr));
            gap: 1.15rem;
            margin-bottom: 1.5rem;
        }
        .cat-box {
            background-color: var(--bg-surface);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-xl);
            padding: 1.25rem 1.4rem;
            box-shadow: var(--shadow-card);
            transition: var(--transition);
            cursor: pointer;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            min-height: 155px;
            position: relative;
        }
        .cat-box:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-card-hover);
            border-color: var(--bento-primary);
        }
        .cat-box.active {
            border: 2px solid var(--bento-primary);
            background: rgba(79, 70, 229, 0.03);
            box-shadow: 0 8px 24px var(--bento-primary-glow);
        }
        html.dark .cat-box.active {
            background: rgba(99, 102, 241, 0.08);
        }
        .cat-box-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 0.65rem;
        }
        .cat-box-title {
            font-size: 1.02rem;
            font-weight: 700;
            color: var(--text-heading);
        }
        .cat-box-desc {
            font-size: 0.8rem;
            color: var(--text-muted);
            line-height: 1.45;
            margin: 0 0 1rem 0;
            flex: 1;
        }
        .cat-box-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-top: 1px solid var(--border-subtle);
            padding-top: 0.75rem;
            font-size: 0.78rem;
            color: var(--text-muted);
        }
        .cat-box.active .cat-box-footer {
            border-top-color: rgba(79, 70, 229, 0.15);
        }

        /* Day Pills Navigation */
        .day-pills-wrap {
            display: flex;
            align-items: center;
            gap: 0.35rem;
            flex-wrap: wrap;
        }
        .day-pill-btn {
            padding: 0.35rem 0.75rem;
            border-radius: var(--radius-pill);
            font-size: 0.78rem;
            font-weight: 600;
            color: var(--text-muted);
            background-color: var(--bg-subtle);
            border: 1px solid var(--border-color);
            cursor: pointer;
            transition: var(--transition);
        }
        .day-pill-btn:hover {
            color: var(--text-heading);
            border-color: var(--bento-primary);
        }
        .day-pill-btn.active {
            background: var(--bento-primary-grad);
            color: #FFFFFF;
            border-color: transparent;
            box-shadow: 0 2px 8px var(--bento-primary-glow);
        }

        /* === BENTO GRADE CURRICULUM CARDS === */
        .grade-bento-card {
            transition: transform 0.22s ease, box-shadow 0.22s ease, border-color 0.22s ease;
        }
        .grade-bento-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 28px rgba(0, 0, 0, 0.08) !important;
        }
        .btn-seg:hover {
            opacity: 0.88;
        }

        /* Button variants */
        .btn-ghost {
            background: transparent;
            border: none;
            color: var(--text-muted);
            padding: 0.35rem;
            border-radius: var(--radius-sm);
            cursor: pointer;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        .btn-ghost:hover {
            background-color: var(--bg-subtle);
            color: var(--text-heading);
        }
        .btn-danger-light {
            background: var(--vx-danger-light);
            color: var(--vx-danger);
            border: 1px solid var(--vx-danger-border);
            padding: 0.35rem;
            border-radius: var(--radius-sm);
            cursor: pointer;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        .btn-danger-light:hover {
            background: var(--vx-danger);
            color: #FFFFFF;
        }
        .btn-icon-sm {
            width: 28px;
            height: 28px;
            font-size: 1rem;
        }

        /* === SHADCN MODALS (Dialog Primitive) === */
        .vx-modal-backdrop, .modal-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.65);
            backdrop-filter: blur(4px);
            z-index: 2000;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
            overflow-y: auto;
        }
        .vx-modal-backdrop.open, .modal-backdrop.open { display: flex; }
        .vx-modal, .modal-dialog {
            background-color: var(--bg-surface);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow-dropdown);
            width: 100%;
            max-width: 640px;
            max-height: 90vh;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            animation: modalPopIn 0.2s cubic-bezier(0.16, 1, 0.3, 1);
            position: relative;
        }
        .vx-modal.modal-lg, .modal-dialog.modal-lg { max-width: 860px; }
        .vx-modal.modal-xl, .modal-dialog.modal-xl { max-width: 1040px; }
        @keyframes modalPopIn {
            from { opacity: 0; transform: scale(0.96) translateY(6px); }
            to { opacity: 1; transform: scale(1) translateY(0); }
        }
        .vx-modal-header, .modal-title-bar {
            padding: 1.15rem 1.5rem;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: space-between;
            background-color: var(--bg-surface);
        }
        .vx-modal-title {
            font-size: 1.05rem;
            font-weight: 700;
            color: var(--text-heading);
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .modal-title-icon {
            width: 28px;
            height: 28px;
            border-radius: var(--radius-md);
            background: var(--bg-subtle);
            color: var(--apex-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
        }
        .vx-modal-close, .close-btn {
            background: transparent;
            border: none;
            color: var(--text-muted);
            font-size: 1.4rem;
            cursor: pointer;
            line-height: 1;
            padding: 0.25rem;
            border-radius: var(--radius-sm);
            transition: var(--transition);
        }
        .vx-modal-close:hover, .close-btn:hover { color: var(--text-heading); background: var(--bg-subtle); }
        .vx-modal-body, .modal-content {
            padding: 1.5rem;
            overflow-y: auto;
            flex: 1;
            background-color: var(--bg-surface);
        }
        .vx-modal-footer, .modal-bottom {
            padding: 1rem 1.5rem;
            border-top: 1px solid var(--border-color);
            background-color: var(--bg-subtle);
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 0.65rem;
        }

        /* === APEX TOAST NOTIFICATIONS (Sonner Primitive) === */
        #apexToastContainer {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 3000;
            display: flex;
            flex-direction: column;
            gap: 8px;
            pointer-events: none;
        }
        .apex-toast {
            pointer-events: auto;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.85rem 1.15rem;
            border-radius: var(--radius-lg);
            background-color: var(--bg-surface);
            border: 1px solid var(--border-color);
            box-shadow: var(--shadow-dropdown);
            font-size: 0.84rem;
            font-weight: 500;
            color: var(--text-heading);
            min-width: 280px;
            max-width: 420px;
            animation: toastIn 0.2s cubic-bezier(0.16, 1, 0.3, 1);
            transition: opacity 0.2s ease, transform 0.2s ease;
        }
        .apex-toast.toast-success { border-color: var(--vx-success-border); }
        .apex-toast.toast-success i { color: var(--vx-success); font-size: 1.2rem; }
        .apex-toast.toast-error { border-color: var(--vx-danger-border); }
        .apex-toast.toast-error i { color: var(--vx-danger); font-size: 1.2rem; }
        .apex-toast.toast-info { border-color: var(--vx-info-border); }
        .apex-toast.toast-info i { color: var(--vx-info); font-size: 1.2rem; }
        @keyframes toastIn {
            from { opacity: 0; transform: translateY(-8px) scale(0.96); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }

        /* === COMMAND PALETTE MODAL (⌘K) === */
        .cmd-modal-backdrop {
            position: fixed; inset: 0; background: rgba(0, 0, 0, 0.65); backdrop-filter: blur(4px);
            z-index: 2500; display: none; align-items: flex-start; justify-content: center; padding-top: 14vh;
        }
        .cmd-modal-backdrop.open { display: flex; }
        .cmd-palette {
            background-color: var(--bg-surface); border: 1px solid var(--border-color);
            border-radius: var(--radius-lg); width: 100%; max-width: 620px;
            box-shadow: var(--shadow-dropdown); overflow: hidden;
            animation: cmdIn 0.15s ease-out;
        }
        @keyframes cmdIn { from { opacity:0; transform:scale(0.97); } to { opacity:1; transform:scale(1); } }
        .cmd-search-bar { display: flex; align-items: center; gap: 0.75rem; padding: 0.95rem 1.25rem; border-bottom: 1px solid var(--border-color); }
        .cmd-search-bar i { font-size: 1.25rem; color: var(--text-muted); }
        .cmd-input { flex: 1; border: none; outline: none; font-size: 0.95rem; font-family: inherit; color: var(--text-heading); background: transparent; }
        .cmd-results { max-height: 360px; overflow-y: auto; padding: 0.5rem; }
        .cmd-group-title { padding: 0.4rem 0.75rem 0.2rem; font-size: 0.65rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; color: var(--text-muted); }
        .cmd-item {
            display: flex; align-items: center; justify-content: space-between;
            padding: 0.65rem 0.85rem; border-radius: var(--radius-md); cursor: pointer;
            text-decoration: none; color: var(--text-body); font-size: 0.85rem; font-weight: 500;
            transition: var(--transition);
        }
        .cmd-item:hover { background-color: var(--bg-subtle); color: var(--text-heading); }
        .cmd-item-left { display: flex; align-items: center; gap: 0.65rem; }
        .cmd-footer { padding: 0.65rem 1.25rem; background-color: var(--bg-subtle); border-top: 1px solid var(--border-color); display: flex; align-items: center; justify-content: space-between; font-size: 0.72rem; color: var(--text-muted); }

        /* User Dropdown */
        .user-dropdown-apex {
            position: absolute; top: calc(100% + 6px); right: 0; width: 230px;
            background-color: var(--bg-surface); border: 1px solid var(--border-color);
            border-radius: var(--radius-lg); box-shadow: var(--shadow-dropdown);
            display: none; z-index: 100; overflow: hidden;
        }
        .user-dropdown-apex.open { display: block; animation: dropIn 0.15s ease-out; }
        @keyframes dropIn { from { opacity:0; transform:translateY(-4px); } to { opacity:1; transform:translateY(0); } }
        .dropdown-header-apex { padding: 0.85rem 1rem; border-bottom: 1px solid var(--border-color); background-color: var(--bg-subtle); }
        .dropdown-name-apex { font-size: 0.85rem; font-weight: 700; color: var(--text-heading); }
        .dropdown-email-apex { font-size: 0.72rem; color: var(--text-muted); margin-top: 1px; font-family: 'JetBrains Mono', monospace; }
        .dropdown-item-apex {
            display: flex; align-items: center; gap: 0.65rem; padding: 0.55rem 1rem;
            text-decoration: none; color: var(--text-body); font-size: 0.82rem; font-weight: 500;
            transition: var(--transition); cursor: pointer; border: none; background: transparent; width: 100%; text-align: left;
        }
        .dropdown-item-apex:hover { background-color: var(--bg-subtle); color: var(--text-heading); }
        .dropdown-item-apex.danger:hover { color: var(--vx-danger); background-color: var(--vx-danger-light); }
        /* === ADMINDEK THEME CUSTOMIZER DRAWER === */
        .admindek-customizer-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.45);
            backdrop-filter: blur(3px);
            z-index: 2500;
            display: none;
            opacity: 0;
            transition: opacity 0.25s ease;
        }
        .admindek-customizer-backdrop.open {
            display: block;
            opacity: 1;
        }
        .admindek-customizer-drawer {
            position: fixed;
            top: 0;
            right: 0;
            width: 350px;
            max-width: 90vw;
            height: 100vh;
            background: var(--bg-surface);
            box-shadow: -6px 0 32px rgba(0, 0, 0, 0.22);
            z-index: 2510;
            transform: translateX(100%);
            transition: transform 0.28s cubic-bezier(0.16, 1, 0.3, 1);
            display: flex;
            flex-direction: column;
            border-left: 1px solid var(--border-color);
        }
        .admindek-customizer-drawer.open {
            transform: translateX(0);
        }
        .customizer-header {
            padding: 1.25rem 1.4rem;
            border-bottom: 1px solid var(--border-color);
            background: var(--bg-surface);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .customizer-title-wrap {
            display: flex;
            align-items: center;
            gap: 0.65rem;
        }
        .customizer-title-icon {
            width: 34px;
            height: 34px;
            border-radius: var(--radius-md);
            background: var(--vx-primary-light);
            color: var(--admindek-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
        }
        .customizer-title-text {
            font-size: 1rem;
            font-weight: 700;
            color: var(--text-heading);
            margin: 0;
            line-height: 1.2;
        }
        .customizer-sub-text {
            font-size: 0.72rem;
            color: var(--text-muted);
            margin: 2px 0 0;
        }
        .customizer-close-btn {
            background: transparent;
            border: 1px solid var(--border-color);
            border-radius: var(--radius-sm);
            width: 30px;
            height: 30px;
            color: var(--text-muted);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.15rem;
            transition: var(--transition);
        }
        .customizer-close-btn:hover {
            color: var(--text-heading);
            background: var(--bg-subtle);
        }
        .customizer-body {
            flex: 1;
            overflow-y: auto;
            padding: 1.25rem 1.4rem;
            scrollbar-width: thin;
        }
        .customizer-section {
            margin-bottom: 1.75rem;
        }
        .customizer-section-title {
            font-size: 0.72rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--text-muted);
            margin-bottom: 0.35rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .customizer-section-desc {
            font-size: 0.75rem;
            color: var(--text-muted);
            margin-bottom: 0.75rem;
            line-height: 1.35;
        }
        
        /* 10 Swatches Grid */
        .color-swatches-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 0.55rem;
        }
        .color-swatch-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.3rem;
            cursor: pointer;
            padding: 0.4rem 0.2rem;
            border-radius: var(--radius-md);
            transition: var(--transition);
        }
        .color-swatch-item:hover {
            background: var(--bg-subtle);
        }
        .color-swatch-btn {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            border: 2px solid transparent;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            font-size: 0.9rem;
            transition: transform 0.2s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.2s ease;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
            position: relative;
        }
        .color-swatch-btn:hover {
            transform: scale(1.12);
        }
        .color-swatch-item.active .color-swatch-btn {
            border-color: #ffffff;
            box-shadow: 0 0 0 2px var(--admindek-primary), 0 4px 12px rgba(0,0,0,0.25);
            transform: scale(1.08);
        }
        .color-swatch-label {
            font-size: 0.65rem;
            font-weight: 600;
            color: var(--text-muted);
            text-align: center;
            white-space: nowrap;
        }
        .color-swatch-item.active .color-swatch-label {
            color: var(--text-heading);
            font-weight: 700;
        }

        /* Segmented Option Buttons */
        .customizer-btn-group {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 0.45rem;
        }
        .customizer-btn-group.triple {
            grid-template-columns: repeat(3, 1fr);
        }
        .customizer-opt-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.45rem;
            padding: 0.6rem 0.75rem;
            border-radius: var(--radius-md);
            border: 1px solid var(--border-color);
            background: var(--bg-subtle);
            color: var(--text-muted);
            font-size: 0.78rem;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            text-align: center;
        }
        .customizer-opt-btn:hover {
            border-color: var(--text-muted);
            color: var(--text-heading);
        }
        .customizer-opt-btn.active {
            background: var(--vx-primary-light);
            border-color: var(--admindek-primary);
            color: var(--admindek-primary);
            font-weight: 700;
        }

        .customizer-footer {
            padding: 1rem 1.4rem;
            border-top: 1px solid var(--border-color);
            background: var(--bg-subtle);
            display: flex;
            gap: 0.65rem;
        }
        .customizer-reset-btn {
            flex: 1;
            padding: 0.6rem 1rem;
            border-radius: var(--radius-md);
            border: 1px solid var(--border-color);
            background: var(--bg-surface);
            color: var(--text-heading);
            font-size: 0.8rem;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.45rem;
            transition: var(--transition);
        }
        .customizer-reset-btn:hover {
            background: var(--border-color);
        }

        /* Floating Customizer Button (Right Edge) */
        .floating-customizer-trigger {
            position: fixed;
            bottom: 24px;
            right: 24px;
            width: 46px;
            height: 46px;
            border-radius: 50%;
            background: var(--admindek-primary, #4680ff);
            color: #ffffff;
            border: 2px solid rgba(255, 255, 255, 0.4);
            box-shadow: 0 6px 20px rgba(70, 128, 255, 0.45);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            cursor: pointer;
            z-index: 2000;
            transition: transform 0.25s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.25s ease;
            animation: customizerSpin 10s linear infinite;
        }
        .floating-customizer-trigger:hover {
            transform: scale(1.12);
            box-shadow: 0 10px 28px rgba(70, 128, 255, 0.6);
            animation-play-state: paused;
        }
        @keyframes customizerSpin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .apex-search-pill { width: 180px; }
            .apex-sidebar { position: fixed; left: 0; top: 0; height: 100vh; transform: translateX(-100%); z-index: 100; }
            .apex-sidebar.mobile-open { transform: translateX(0); }
        }
        @media (max-width: 640px) {
            .apex-search-pill { display: none; }
            .apex-breadcrumbs { display: none; }
            .apex-content-body { padding: 1rem; }
        }
    </style>
    @stack('styles')
</head>
<body>
    <div class="apex-shell">
        <!-- 1. APEX SIDEBAR -->
        <aside class="apex-sidebar" id="apexSidebar">
            <!-- Brand Box -->
            <div class="sidebar-brand-box">
                <a href="{{ route('admin.dashboard') }}" class="brand-link-apex">
                    <div class="brand-icon-apex">
                        <i class='bx bxs-graduation'></i>
                    </div>
                    <div class="brand-name-apex">
                        <span>Apex Sekolahku</span>
                        <span class="brand-tag-apex">v2.0</span>
                    </div>
                </a>
                <button type="button" class="topbar-btn-icon" onclick="toggleApexSidebar()" title="Toggle Sidebar">
                    <i class='bx bx-chevron-left' id="sidebarToggleIcon"></i>
                </button>
            </div>

            <!-- Navigation Items -->
            <div class="sidebar-nav-scroll">
                <div class="nav-section-title">Overview</div>
                <a href="{{ route('admin.dashboard') }}" id="sidebar-nav-dashboard" class="apex-nav-item {{ Request::routeIs('admin.dashboard') || Request::routeIs('admin.home') ? 'active' : '' }}">
                    <div class="apex-nav-left">
                        <i class='bx bx-grid-alt apex-nav-icon nav-icon-overview'></i>
                        <span class="apex-nav-label">Dashboard Analytics</span>
                    </div>
                </a>

                <div class="nav-section-title">Data Master Dapodik</div>
                <a href="{{ route('admin.data-master') }}#tab-students" id="sidebar-nav-tab-students" onclick="if(window.switchTab) switchTab('tab-students');" class="apex-nav-item">
                    <div class="apex-nav-left">
                        <i class='bx bx-id-card apex-nav-icon nav-icon-students'></i>
                        <span class="apex-nav-label">Peserta Didik (4x6)</span>
                    </div>
                    <span class="apex-nav-badge">Kartu</span>
                </a>
                <a href="{{ route('admin.data-master') }}#tab-teachers" id="sidebar-nav-tab-teachers" onclick="if(window.switchTab) switchTab('tab-teachers');" class="apex-nav-item">
                    <div class="apex-nav-left">
                        <i class='bx bx-briefcase apex-nav-icon nav-icon-teachers'></i>
                        <span class="apex-nav-label">Pendidik & GTK</span>
                    </div>
                </a>
                @if(Auth::check() && (in_array(Auth::user()->role, ['teacher', 'waka_kurikulum', 'waka_kesiswaan', 'waka_sarpras', 'waka_humas', 'waka_hubin']) || Auth::user()->isSchoolAdmin() || Auth::user()->isSuperAdmin()))
                <a href="{{ route('teacher.profile') }}" id="sidebar-nav-gtk-profile-quick" class="apex-nav-item {{ Request::routeIs('teacher.profile*') ? 'active' : '' }}">
                    <div class="apex-nav-left">
                        <i class='bx bx-id-card apex-nav-icon' style="color: #10B981;"></i>
                        <span class="apex-nav-label">Edit Profil GTK (4x6)</span>
                    </div>
                    <span class="apex-nav-badge" style="background: rgba(16, 185, 129, 0.15); color: #10B981; font-weight: 700;">Dapodik</span>
                </a>
                @endif
                <a href="{{ route('admin.data-master') }}#tab-classes" id="sidebar-nav-tab-classes" onclick="if(window.switchTab) switchTab('tab-classes');" class="apex-nav-item">
                    <div class="apex-nav-left">
                        <i class='bx bx-chalkboard apex-nav-icon nav-icon-classes'></i>
                        <span class="apex-nav-label">Rombongan Belajar</span>
                    </div>
                </a>
                <a href="{{ route('admin.data-master') }}#tab-years" id="sidebar-nav-tab-years" onclick="if(window.switchTab) switchTab('tab-years');" class="apex-nav-item">
                    <div class="apex-nav-left">
                        <i class='bx bx-calendar-check apex-nav-icon nav-icon-years'></i>
                        <span class="apex-nav-label">Tahun Ajaran & Smt</span>
                    </div>
                </a>
                <a href="{{ route('admin.data-master') }}#tab-curriculums" id="sidebar-nav-tab-curriculums" onclick="if(window.switchTab) switchTab('tab-curriculums');" class="apex-nav-item">
                    <div class="apex-nav-left">
                        <i class='bx bx-book-bookmark apex-nav-icon nav-icon-curriculums'></i>
                        <span class="apex-nav-label">Kurikulum & Mapel</span>
                    </div>
                </a>
                <a href="{{ route('admin.data-master') }}#tab-schedules" id="sidebar-nav-tab-schedules" onclick="if(window.switchTab) switchTab('tab-schedules');" class="apex-nav-item">
                    <div class="apex-nav-left">
                        <i class='bx bx-time apex-nav-icon nav-icon-schedules'></i>
                        <span class="apex-nav-label">Pola Jam Belajar</span>
                    </div>
                </a>
                <a href="{{ route('admin.data-master') }}#tab-schools" id="sidebar-nav-tab-schools" onclick="if(window.switchTab) switchTab('tab-schools');" class="apex-nav-item">
                    <div class="apex-nav-left">
                        <i class='bx bxs-institution apex-nav-icon nav-icon-schools'></i>
                        <span class="apex-nav-label">Satuan Pendidikan</span>
                    </div>
                </a>

                <div class="nav-section-title">Presensi & KBM</div>
                <a href="{{ route('admin.kurikulum.jurnal') }}" id="sidebar-nav-jurnal-kbm" class="apex-nav-item {{ Request::routeIs('admin.kurikulum.jurnal*') ? 'active' : '' }}">
                    <div class="apex-nav-left">
                        <i class='bx bx-book-bookmark apex-nav-icon' style="color: #F59E0B;"></i>
                        <span class="apex-nav-label">Jurnal Mengajar KBM</span>
                    </div>
                    <span class="apex-nav-badge">Guru</span>
                </a>
                <a href="{{ route('admin.schedules') }}" id="sidebar-nav-schedules-kbm" class="apex-nav-item {{ Request::routeIs('admin.schedules') ? 'active' : '' }}">
                    <div class="apex-nav-left">
                        <i class='bx bx-calendar-event apex-nav-icon nav-icon-schedules'></i>
                        <span class="apex-nav-label">Jadwal Pelajaran KBM</span>
                    </div>
                    <span class="apex-nav-badge">Matriks</span>
                </a>
                <a href="{{ route('attendance.portal') }}" target="_blank" class="apex-nav-item">
                    <div class="apex-nav-left">
                        <i class='bx bx-scan apex-nav-icon nav-icon-attendance'></i>
                        <span class="apex-nav-label">Portal Presensi Live</span>
                    </div>
                    <span class="apex-nav-badge badge-gps">GPS</span>
                </a>
                <a href="{{ route('admin.data-master') }}#tab-subjects" id="sidebar-nav-tab-subjects" onclick="if(window.switchTab) switchTab('tab-subjects');" class="apex-nav-item">
                    <div class="apex-nav-left">
                        <i class='bx bx-book-open apex-nav-icon nav-icon-subjects'></i>
                        <span class="apex-nav-label">Katalog Mata Pelajaran</span>
                    </div>
                </a>

                <div class="nav-section-title">Kesiswaan & BK</div>
                <a href="{{ route('admin.kesiswaan.tatib') }}" id="sidebar-nav-kesiswaan-tatib" class="apex-nav-item {{ Request::routeIs('admin.kesiswaan.tatib') ? 'active' : '' }}">
                    <div class="apex-nav-left">
                        <i class='bx bx-shield-quarter apex-nav-icon nav-icon-students'></i>
                        <span class="apex-nav-label">Tata Tertib & Kredit Poin</span>
                    </div>
                    <span class="apex-nav-badge">Tatib</span>
                </a>

                <div class="nav-section-title">Sistem & Profil</div>
                @if(Auth::check() && (Auth::user()->isSuperAdmin() || Auth::user()->isSchoolAdmin()))
                <div class="nav-section-title">Akses & Pengguna</div>
                <a href="{{ route('admin.users.index') }}" id="sidebar-nav-users-list" class="apex-nav-item {{ Request::routeIs('admin.users.index') ? 'active' : '' }}">
                    <div class="apex-nav-left">
                        <i class='bx bx-user-check apex-nav-icon' style="color: #4680FF;"></i>
                        <span class="apex-nav-label">Daftar Pengguna</span>
                    </div>
                    <span class="apex-nav-badge">Direktori</span>
                </a>
                <a href="{{ route('admin.users.create') }}" id="sidebar-nav-users-create" class="apex-nav-item {{ Request::routeIs('admin.users.create') ? 'active' : '' }}">
                    <div class="apex-nav-left">
                        <i class='bx bx-user-plus apex-nav-icon' style="color: #10B981;"></i>
                        <span class="apex-nav-label">Tambah Pengguna Baru</span>
                    </div>
                    <span class="apex-nav-badge">Form</span>
                </a>
                <a href="{{ route('admin.users.enrollments') }}" id="sidebar-nav-users-enroll" class="apex-nav-item {{ Request::routeIs('admin.users.enrollments') ? 'active' : '' }}">
                    <div class="apex-nav-left">
                        <i class='bx bx-buildings apex-nav-icon' style="color: #06B6D4;"></i>
                        <span class="apex-nav-label">Enrol Satuan Pendidikan</span>
                    </div>
                    <span class="apex-nav-badge">Unit</span>
                </a>
                @endif
                @if(Auth::check() && (in_array(Auth::user()->role, ['teacher', 'waka_kurikulum', 'waka_kesiswaan', 'waka_sarpras', 'waka_humas', 'waka_hubin']) || Auth::user()->isSchoolAdmin() || Auth::user()->isSuperAdmin()))
                <a href="{{ route('teacher.profile') }}" id="sidebar-nav-gtk-profile" class="apex-nav-item {{ Request::routeIs('teacher.profile*') ? 'active' : '' }}">
                    <div class="apex-nav-left">
                        <i class='bx bx-id-card apex-nav-icon' style="color: #10B981;"></i>
                        <span class="apex-nav-label">Profil & Biodata GTK</span>
                    </div>
                    <span class="apex-nav-badge">Dapodik</span>
                </a>
                @endif
                @if(Auth::check() && Auth::user()->role === 'student')
                <a href="{{ route('student.profile') }}" class="apex-nav-item">
                    <div class="apex-nav-left">
                        <i class='bx bx-user-pin apex-nav-icon'></i>
                        <span class="apex-nav-label">Biodata Mandiri</span>
                    </div>
                </a>
                @endif
                <a href="javascript:void(0)" onclick="openCommandPalette()" class="apex-nav-item">
                    <div class="apex-nav-left">
                        <i class='bx bx-command apex-nav-icon nav-icon-cmd'></i>
                        <span class="apex-nav-label">Spotlight Palette</span>
                    </div>
                    <span class="apex-nav-badge">⌘K</span>
                </a>
            </div>

            <!-- Footer Unit Info -->
            <div class="sidebar-footer-box">
                <div class="sidebar-footer-school" title="{{ $school->name ?? 'SMAN 1 Jakarta' }}">
                    <span class="status-indicator-dot"></span>
                    <span>{{ $school->name ?? 'SMAN 1 Jakarta' }}</span>
                </div>
                <span class="brand-tag-apex">{{ $school->level ?? 'SMA' }}</span>
            </div>
        </aside>

        <!-- 2. APEX CANVAS MAIN -->
        <div class="apex-canvas">
            <!-- Topbar Header -->
            <header class="apex-topbar">
                <div class="topbar-left">
                    <button type="button" class="topbar-btn-icon" onclick="toggleMobileSidebar()" title="Buka Menu">
                        <i class='bx bx-menu'></i>
                    </button>
                    
                    <div class="apex-breadcrumbs">
                        <a href="{{ route('admin.dashboard') }}">Sekolahku</a>
                        <span>/</span>
                        <span class="current">@yield('breadcrumb_current', 'Dashboard')</span>
                    </div>
                </div>

                <!-- Center: Spotlight Search -->
                <div class="topbar-center">
                    <div class="apex-search-pill" onclick="openCommandPalette()" role="button" title="Pencarian Spotlight (Ctrl+K)">
                        <i class='bx bx-search'></i>
                        <span>Cari modul, siswa, atau aksi...</span>
                        <kbd class="apex-search-kbd">⌘K</kbd>
                    </div>
                </div>

                <!-- Right Actions -->
                <div class="topbar-right">
                    <!-- Theme Toggle Button (Light/Dark Apex Mode) -->
                    <button type="button" class="topbar-btn-icon" onclick="toggleApexTheme()" id="themeToggleBtn" title="Ganti Tema (Light/Dark)">
                        <i class='bx bx-moon' id="themeIcon"></i>
                    </button>

                    <!-- Admindek Live Customizer Trigger -->
                    <button type="button" class="topbar-btn-icon" onclick="openThemeCustomizer()" title="Pengaturan Tema & Tampilan Admindek">
                        <i class='bx bx-cog' style="font-size:1.25rem;"></i>
                    </button>

                    <!-- School Switcher Pill -->
                    <div style="position: relative;" id="schoolSwitcherWrap">
                        @if(Auth::user()?->isSuperAdmin())
                            <button class="school-pill-apex" onclick="toggleSchoolDropdown(event)" type="button" title="Ganti Satuan Pendidikan (Super Admin)">
                                <i class='bx bxs-institution' style="color:var(--text-muted);"></i>
                                <span style="max-width: 140px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $school->name ?? 'Sekolahku' }}</span>
                                <i class='bx bx-chevron-down' style="color:var(--text-muted); font-size: 0.9rem;"></i>
                            </button>
                            <!-- School Dropdown -->
                            <div class="user-dropdown-apex" id="schoolDropdownApex">
                                <div class="dropdown-header-apex">
                                    <div class="dropdown-name-apex">Ganti Unit Pendidikan</div>
                                    <div class="dropdown-email-apex">Multi-Tenant Terpadu (Super Admin)</div>
                                </div>
                                <div style="max-height: 240px; overflow-y: auto; padding: 4px;">
                                    @php $allSchools = \App\Models\School::where('is_active', true)->get(); @endphp
                                    @foreach($allSchools as $sc)
                                        <a href="{{ route('admin.switch-school.get', $sc->id) }}" class="dropdown-item-apex">
                                            <i class='bx bxs-school'></i>
                                            <div>
                                                <div style="font-weight:600; font-size:0.82rem;">{{ $sc->name }}</div>
                                                <div style="font-size:0.7rem; color:var(--text-muted);">{{ $sc->npsn ?? $sc->tenant_id }}</div>
                                            </div>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @else
                            <div class="school-pill-apex" style="cursor: default;" title="Unit Tugas Resmi Anda (Terkunci)">
                                <i class='bx bxs-institution' style="color:var(--bento-primary);"></i>
                                <span style="max-width: 160px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; font-weight:600;">{{ $school->name ?? 'Sekolahku' }}</span>
                                <span style="font-size: 9.5px; background: var(--vx-primary-light); color: var(--bento-primary); padding: 2px 6px; border-radius: 9999px; font-weight: 700; margin-left: 2px;">Unit Anda</span>
                            </div>
                        @endif
                    </div>

                    <!-- User Avatar Pill & Menu -->
                    <div style="position: relative;" id="userProfileWrap">
                        <button class="user-pill-apex" onclick="toggleUserDropdown(event)" type="button">
                            <div class="user-avatar-apex">
                                {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}
                            </div>
                            <span style="font-weight:600; font-size:0.82rem; color:var(--text-heading); max-width:110px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;" class="d-none d-md-inline">
                                {{ Auth::user()->name ?? 'Admin' }}
                            </span>
                            <i class='bx bx-chevron-down' style="color:var(--text-muted); font-size:0.85rem;"></i>
                        </button>

                        <div class="user-dropdown-apex" id="userDropdownApex">
                            <div class="dropdown-header-apex">
                                <div class="dropdown-name-apex">{{ Auth::user()->name ?? 'Admin' }}</div>
                                <div class="dropdown-email-apex">{{ Auth::user()->email ?? 'admin@sekolahku.sch.id' }}</div>
                            </div>
                            <div style="padding: 4px;">
                                <a href="{{ route('admin.dashboard') }}" class="dropdown-item-apex">
                                    <i class='bx bx-grid-alt'></i> Dashboard
                                </a>
                                @if(Auth::check() && in_array(Auth::user()->role, ['teacher', 'waka_kurikulum', 'waka_kesiswaan', 'waka_sarpras', 'waka_humas', 'waka_hubin', 'school_admin', 'super_admin']))
                                <a href="{{ route('teacher.profile') }}" class="dropdown-item-apex">
                                    <i class='bx bx-id-card' style="color: #10B981;"></i> Edit Profil & Pas Foto GTK
                                </a>
                                @endif
                                @if(Auth::check() && Auth::user()->role === 'student')
                                <a href="{{ route('student.profile') }}" class="dropdown-item-apex">
                                    <i class='bx bx-user-pin' style="color: #06B6D4;"></i> Edit Biodata Mandiri
                                </a>
                                @endif
                                <a href="{{ route('admin.data-master') }}#tab-students" class="dropdown-item-apex">
                                    <i class='bx bx-image'></i> Mode Kartu Siswa
                                </a>
                                <div style="height: 1px; background: var(--border-color); margin: 4px 0;"></div>
                                <form action="{{ route('logout') }}" method="POST" style="margin: 0;">
                                    @csrf
                                    <button type="submit" class="dropdown-item-apex danger">
                                        <i class='bx bx-log-out'></i> Keluar Akun
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Main Page Content Body -->
            <main class="apex-content-body">
                @if(session('login_success'))
                    <div style="padding: 0.75rem 1rem; border-radius: var(--radius-md); background: var(--vx-success-light); border: 1px solid var(--vx-success-border); color: var(--vx-success); font-size: 0.84rem; font-weight: 600; margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.5rem;">
                        <i class='bx bx-check-circle' style="font-size:1.15rem;"></i>
                        <span>{{ session('login_success') }}</span>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <!-- 3. APEX SPOTLIGHT COMMAND PALETTE (⌘K) -->
    <div class="cmd-modal-backdrop" id="apexCommandPaletteModal" onclick="closeCommandPalette(event)">
        <div class="cmd-palette" onclick="event.stopPropagation()">
            <div class="cmd-search-bar">
                <i class='bx bx-search'></i>
                <input type="text" class="cmd-input" id="cmdInputApex" placeholder="Ketik perintah atau cari menu..." oninput="filterApexCommands()" autofocus />
                <kbd class="apex-search-kbd">ESC</kbd>
            </div>
            <div class="cmd-results" id="cmdResultsApex">
                <div class="cmd-group-title">Aksi Cepat</div>
                @if(Auth::check() && (Auth::user()->isSuperAdmin() || Auth::user()->isSchoolAdmin()))
                <a href="{{ route('admin.users.index') }}" class="cmd-item" onclick="closeCommandPalette();">
                    <div class="cmd-item-left">
                        <i class='bx bx-user-check' style="font-size:1.15rem; color:#4680FF;"></i>
                        <span>Direktori Pengguna & Hak Akses</span>
                    </div>
                    <span class="apex-nav-badge">Users</span>
                </a>
                <a href="{{ route('admin.users.create') }}" class="cmd-item" onclick="closeCommandPalette();">
                    <div class="cmd-item-left">
                        <i class='bx bx-user-plus' style="font-size:1.15rem; color:#10B981;"></i>
                        <span>Tambah Pengguna Baru</span>
                    </div>
                    <span class="apex-nav-badge">Buat</span>
                </a>
                <a href="{{ route('admin.users.enrollments') }}" class="cmd-item" onclick="closeCommandPalette();">
                    <div class="cmd-item-left">
                        <i class='bx bx-buildings' style="font-size:1.15rem; color:#06B6D4;"></i>
                        <span>Enrol Pengguna ke Satuan Pendidikan</span>
                    </div>
                    <span class="apex-nav-badge">Enrol</span>
                </a>
                @endif
                <a href="{{ route('teacher.profile') }}" class="cmd-item" onclick="closeCommandPalette();">
                    <div class="cmd-item-left">
                        <i class='bx bx-id-card' style="font-size:1.15rem; color:#10B981;"></i>
                        <span>Profil & Biodata GTK (Dapodik 4x6)</span>
                    </div>
                    <span class="apex-nav-badge">GTK</span>
                </a>
                <a href="{{ route('admin.data-master') }}#tab-students" class="cmd-item" onclick="if(window.switchTab) switchTab('tab-students'); closeCommandPalette();">
                    <div class="cmd-item-left">
                        <i class='bx bx-id-card' style="font-size:1.15rem; color:var(--text-muted);"></i>
                        <span>Buka Mode Kartu Foto Siswa 4x6</span>
                    </div>
                    <span class="apex-nav-badge">Siswa</span>
                </a>
                <a href="{{ route('admin.dashboard') }}" class="cmd-item" onclick="closeCommandPalette();">
                    <div class="cmd-item-left">
                        <i class='bx bx-grid-alt' style="font-size:1.15rem; color:var(--text-muted);"></i>
                        <span>Lihat Dashboard & Sesi KBM</span>
                    </div>
                    <span class="apex-nav-badge">Home</span>
                </a>
                <a href="{{ route('attendance.portal') }}" target="_blank" class="cmd-item" onclick="closeCommandPalette();">
                    <div class="cmd-item-left">
                        <i class='bx bx-scan' style="font-size:1.15rem; color:var(--text-muted);"></i>
                        <span>Buka Portal Presensi Live (GPS)</span>
                    </div>
                    <span class="apex-nav-badge">Live</span>
                </a>

                <div class="cmd-group-title">Navigasi Master</div>
                <a href="{{ route('admin.data-master') }}#tab-teachers" class="cmd-item" onclick="if(window.switchTab) switchTab('tab-teachers'); closeCommandPalette();">
                    <div class="cmd-item-left">
                        <i class='bx bx-briefcase' style="font-size:1.15rem; color:var(--text-muted);"></i>
                        <span>Data Pendidik & Tenaga Kependidikan</span>
                    </div>
                </a>
                <a href="{{ route('admin.data-master') }}#tab-classes" class="cmd-item" onclick="if(window.switchTab) switchTab('tab-classes'); closeCommandPalette();">
                    <div class="cmd-item-left">
                        <i class='bx bx-chalkboard' style="font-size:1.15rem; color:var(--text-muted);"></i>
                        <span>Data Rombongan Belajar (Rombel)</span>
                    </div>
                </a>
                <a href="javascript:void(0)" class="cmd-item" onclick="toggleApexTheme(); closeCommandPalette();">
                    <div class="cmd-item-left">
                        <i class='bx bx-sun' style="font-size:1.15rem; color:var(--text-muted);"></i>
                        <span>Ganti Mode Gelap / Terang (Toggle Theme)</span>
                    </div>
                    <span class="apex-nav-badge">Tema</span>
                </a>
            </div>
            <div class="cmd-footer">
                <span>Navigasi: <strong>&uarr;&darr;</strong> untuk pilih &bull; <strong>ESC</strong> untuk tutup</span>
                <span>Apex Admin &bull; shadcn/ui</span>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        // --- 1. Theme Manager (Apex Light/Dark Mode) ---
        function updateThemeUI() {
            const isDark = document.documentElement.classList.contains('dark');
            const icon = document.getElementById('themeIcon');
            if (icon) {
                icon.className = isDark ? 'bx bx-sun' : 'bx bx-moon';
            }
        }
        function toggleApexTheme() {
            const isDark = document.documentElement.classList.toggle('dark');
            localStorage.setItem('apex_theme', isDark ? 'dark' : 'light');
            updateThemeUI();
        }
        document.addEventListener('DOMContentLoaded', updateThemeUI);

        // --- 2. Sidebar Collapse ---
        function toggleApexSidebar() {
            const sidebar = document.getElementById('apexSidebar');
            sidebar.classList.toggle('collapsed');
            const isCol = sidebar.classList.contains('collapsed');
            const icon = document.getElementById('sidebarToggleIcon');
            if (icon) icon.className = isCol ? 'bx bx-chevron-right' : 'bx bx-chevron-left';
            localStorage.setItem('apex_sidebar_collapsed', isCol ? '1' : '0');
        }
        function toggleMobileSidebar() {
            const sidebar = document.getElementById('apexSidebar');
            sidebar.classList.toggle('mobile-open');
        }

        // Restore sidebar preference
        document.addEventListener('DOMContentLoaded', function() {
            if (localStorage.getItem('apex_sidebar_collapsed') === '1') {
                const sidebar = document.getElementById('apexSidebar');
                if (sidebar) sidebar.classList.add('collapsed');
                const icon = document.getElementById('sidebarToggleIcon');
                if (icon) icon.className = 'bx bx-chevron-right';
            }
        });

        // --- 3. Spotlight Command Palette (⌘K) ---
        function openCommandPalette() {
            const modal = document.getElementById('apexCommandPaletteModal');
            if (modal) {
                modal.classList.add('open');
                setTimeout(() => document.getElementById('cmdInputApex')?.focus(), 50);
            }
        }
        function closeCommandPalette(e) {
            const modal = document.getElementById('apexCommandPaletteModal');
            if (modal) modal.classList.remove('open');
        }
        function filterApexCommands() {
            const q = (document.getElementById('cmdInputApex')?.value || '').toLowerCase();
            document.querySelectorAll('#cmdResultsApex .cmd-item').forEach(item => {
                const text = item.textContent.toLowerCase();
                item.style.display = text.includes(q) ? 'flex' : 'none';
            });
        }
        document.addEventListener('keydown', function(e) {
            if ((e.metaKey || e.ctrlKey) && e.key === 'k') {
                e.preventDefault();
                openCommandPalette();
            }
            if (e.key === 'Escape') {
                closeCommandPalette();
            }
        });

        // --- 4. Dropdowns ---
        function toggleSchoolDropdown(e) {
            e.stopPropagation();
            document.getElementById('schoolDropdownApex')?.classList.toggle('open');
            document.getElementById('userDropdownApex')?.classList.remove('open');
        }
        function toggleUserDropdown(e) {
            e.stopPropagation();
            document.getElementById('userDropdownApex')?.classList.toggle('open');
            document.getElementById('schoolDropdownApex')?.classList.remove('open');
        }
        // --- 5. Modal Helpers (Apex shadcn Dialog) ---
        window.openModal = function(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.add('open');
                document.body.style.overflow = 'hidden';
            }
        };

        window.closeModal = function(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.remove('open');
                document.body.style.overflow = '';
            }
        };

        // Close modal on backdrop click or ESC, and close header dropdowns on outside click
        document.addEventListener('click', function(e) {
            if (!e.target.closest('#schoolSwitcherWrap')) {
                document.getElementById('schoolDropdownApex')?.classList.remove('open');
            }
            if (!e.target.closest('#userProfileWrap')) {
                document.getElementById('userDropdownApex')?.classList.remove('open');
            }
            if (e.target.classList.contains('vx-modal-backdrop') || e.target.classList.contains('modal-backdrop')) {
                e.target.classList.remove('open');
                document.body.style.overflow = '';
            }
        });

        // --- 6. Toast Notifications (Apex Sonner) ---
        window.vxShowToast = function(message, type = 'info') {
            let container = document.getElementById('apexToastContainer');
            if (!container) {
                container = document.createElement('div');
                container.id = 'apexToastContainer';
                document.body.appendChild(container);
            }

            const toast = document.createElement('div');
            toast.className = `apex-toast toast-${type}`;
            
            let iconClass = 'bx bx-info-circle';
            if (type === 'success') iconClass = 'bx bx-check-circle';
            else if (type === 'error' || type === 'danger') iconClass = 'bx bx-error-circle';
            else if (type === 'warning') iconClass = 'bx bx-alarm-exclamation';

            toast.innerHTML = `
                <i class='${iconClass}'></i>
                <div style="flex:1; line-height: 1.35;">${message}</div>
                <button type="button" style="background:transparent; border:none; color:var(--text-muted); cursor:pointer; font-size:1.1rem; line-height:1;" onclick="this.parentElement.remove()">&times;</button>
            `;

            container.appendChild(toast);

            setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.transform = 'translateY(-10px)';
                setTimeout(() => toast.remove(), 250);
            }, 3500);
        };
    </script>

    <!-- === ADMINDEK THEME CUSTOMIZER DRAWER === -->
    <button type="button" class="floating-customizer-trigger" onclick="openThemeCustomizer()" title="Pengaturan Tampilan Admindek">
        <i class='bx bx-cog'></i>
    </button>

    <div class="admindek-customizer-backdrop" id="admindekCustomizerBackdrop" onclick="closeThemeCustomizer()"></div>

    <aside class="admindek-customizer-drawer" id="admindekCustomizerDrawer" aria-label="Admindek Customizer">
        <!-- Header -->
        <div class="customizer-header">
            <div class="customizer-title-wrap">
                <div class="customizer-title-icon">
                    <i class='bx bx-palette'></i>
                </div>
                <div>
                    <h3 class="customizer-title-text">Admindek Customizer</h3>
                    <p class="customizer-sub-text">Live Theme & Layout Configurator</p>
                </div>
            </div>
            <button type="button" class="customizer-close-btn" onclick="closeThemeCustomizer()" title="Tutup">
                <i class='bx bx-x'></i>
            </button>
        </div>

        <!-- Body -->
        <div class="customizer-body">
            <!-- 1. Color Presets -->
            <div class="customizer-section">
                <div class="customizer-section-title">
                    <span>Preset Warna Primer</span>
                    <span style="font-size:0.65rem; color:var(--admindek-primary); font-weight:700;" id="activeColorName">Ocean Blue</span>
                </div>
                <div class="customizer-section-desc">Pilih 1 dari 10 skema palet warna resmi Admindek.</div>
                
                <div class="color-swatches-grid">
                    <!-- 1. Ocean Blue -->
                    <div class="color-swatch-item" onclick="setAdmindekPreset('#4680ff', 'Ocean Blue')" data-color="#4680ff" title="Ocean Blue (Admindek Default)">
                        <div class="color-swatch-btn" style="background:#4680ff;">
                            <i class='bx bx-check swatch-check'></i>
                        </div>
                        <span class="color-swatch-label">Blue</span>
                    </div>

                    <!-- 2. Royal Purple -->
                    <div class="color-swatch-item" onclick="setAdmindekPreset('#7c4dff', 'Royal Purple')" data-color="#7c4dff" title="Royal Purple">
                        <div class="color-swatch-btn" style="background:#7c4dff;">
                            <i class='bx bx-check swatch-check'></i>
                        </div>
                        <span class="color-swatch-label">Purple</span>
                    </div>

                    <!-- 3. Rose Pink -->
                    <div class="color-swatch-item" onclick="setAdmindekPreset('#e91e63', 'Rose Pink')" data-color="#e91e63" title="Rose Pink">
                        <div class="color-swatch-btn" style="background:#e91e63;">
                            <i class='bx bx-check swatch-check'></i>
                        </div>
                        <span class="color-swatch-label">Pink</span>
                    </div>

                    <!-- 4. Crimson Red -->
                    <div class="color-swatch-item" onclick="setAdmindekPreset('#dc2626', 'Crimson Red')" data-color="#dc2626" title="Crimson Red">
                        <div class="color-swatch-btn" style="background:#dc2626;">
                            <i class='bx bx-check swatch-check'></i>
                        </div>
                        <span class="color-swatch-label">Red</span>
                    </div>

                    <!-- 5. Vibrant Orange -->
                    <div class="color-swatch-item" onclick="setAdmindekPreset('#ff9800', 'Vibrant Orange')" data-color="#ff9800" title="Vibrant Orange">
                        <div class="color-swatch-btn" style="background:#ff9800;">
                            <i class='bx bx-check swatch-check'></i>
                        </div>
                        <span class="color-swatch-label">Orange</span>
                    </div>

                    <!-- 6. Golden Yellow -->
                    <div class="color-swatch-item" onclick="setAdmindekPreset('#e58a00', 'Golden Yellow')" data-color="#e58a00" title="Golden Yellow">
                        <div class="color-swatch-btn" style="background:#e58a00;">
                            <i class='bx bx-check swatch-check'></i>
                        </div>
                        <span class="color-swatch-label">Yellow</span>
                    </div>

                    <!-- 7. Forest Green -->
                    <div class="color-swatch-item" onclick="setAdmindekPreset('#2ca87f', 'Forest Green')" data-color="#2ca87f" title="Forest Green">
                        <div class="color-swatch-btn" style="background:#2ca87f;">
                            <i class='bx bx-check swatch-check'></i>
                        </div>
                        <span class="color-swatch-label">Green</span>
                    </div>

                    <!-- 8. Aqua Cyan -->
                    <div class="color-swatch-item" onclick="setAdmindekPreset('#00bcd4', 'Aqua Cyan')" data-color="#00bcd4" title="Aqua Cyan">
                        <div class="color-swatch-btn" style="background:#00bcd4;">
                            <i class='bx bx-check swatch-check'></i>
                        </div>
                        <span class="color-swatch-label">Cyan</span>
                    </div>

                    <!-- 9. Dark Slate -->
                    <div class="color-swatch-item" onclick="setAdmindekPreset('#212529', 'Dark Slate')" data-color="#212529" title="Dark Slate">
                        <div class="color-swatch-btn" style="background:#212529;">
                            <i class='bx bx-check swatch-check'></i>
                        </div>
                        <span class="color-swatch-label">Dark</span>
                    </div>

                    <!-- 10. Deep Navy -->
                    <div class="color-swatch-item" onclick="setAdmindekPreset('#34495e', 'Deep Navy')" data-color="#34495e" title="Deep Navy">
                        <div class="color-swatch-btn" style="background:#34495e;">
                            <i class='bx bx-check swatch-check'></i>
                        </div>
                        <span class="color-swatch-label">Navy</span>
                    </div>
                </div>
            </div>

            <!-- 2. Theme Mode -->
            <div class="customizer-section">
                <div class="customizer-section-title">
                    <span>Mode Tampilan</span>
                </div>
                <div class="customizer-section-desc">Gaya kontras terang, gelap malam, atau mengikuti sistem.</div>
                
                <div class="customizer-btn-group triple">
                    <button type="button" class="customizer-opt-btn" id="optThemeLight" onclick="setThemeMode('light')">
                        <i class='bx bx-sun'></i>
                        <span>Light</span>
                    </button>
                    <button type="button" class="customizer-opt-btn" id="optThemeDark" onclick="setThemeMode('dark')">
                        <i class='bx bx-moon'></i>
                        <span>Dark</span>
                    </button>
                    <button type="button" class="customizer-opt-btn" id="optThemeSystem" onclick="setThemeMode('system')">
                        <i class='bx bx-desktop'></i>
                        <span>System</span>
                    </button>
                </div>
            </div>

            <!-- 3. Layout Width -->
            <div class="customizer-section">
                <div class="customizer-section-title">
                    <span>Lebar Tata Letak (Layout)</span>
                </div>
                <div class="customizer-section-desc">Pilih tampilan lebar penuh atau batas terpusat 1200px.</div>
                
                <div class="customizer-btn-group">
                    <button type="button" class="customizer-opt-btn" id="optLayoutFluid" onclick="setLayoutWidth('fluid')">
                        <i class='bx bx-expand-alt'></i>
                        <span>Fluid (Full)</span>
                    </button>
                    <button type="button" class="customizer-opt-btn" id="optLayoutBoxed" onclick="setLayoutWidth('boxed')">
                        <i class='bx bx-collapse-alt'></i>
                        <span>Boxed (1200px)</span>
                    </button>
                </div>
            </div>

            <!-- 4. Sidebar Nav Style -->
            <div class="customizer-section">
                <div class="customizer-section-title">
                    <span>Navigasi Sidebar</span>
                </div>
                <div class="customizer-section-desc">Sidebar standar (264px) atau mode ikon ringkas (80px).</div>
                
                <div class="customizer-btn-group">
                    <button type="button" class="customizer-opt-btn" id="optSidebarExpanded" onclick="setSidebarState('expanded')">
                        <i class='bx bx-dock-left'></i>
                        <span>Expanded</span>
                    </button>
                    <button type="button" class="customizer-opt-btn" id="optSidebarCollapsed" onclick="setSidebarState('collapsed')">
                        <i class='bx bx-minus-back'></i>
                        <span>Icon Only</span>
                    </button>
                </div>
            </div>

            <!-- 5. Sidebar Menu Caption -->
            <div class="customizer-section">
                <div class="customizer-section-title">
                    <span>Label Kategori Menu (Caption)</span>
                </div>
                <div class="customizer-section-desc">Tampilkan atau sembunyikan judul bagian menu sidebar.</div>
                
                <div class="customizer-btn-group">
                    <button type="button" class="customizer-opt-btn" id="optCaptionShow" onclick="setSidebarCaption('show')">
                        <i class='bx bx-show'></i>
                        <span>Tampilkan</span>
                    </button>
                    <button type="button" class="customizer-opt-btn" id="optCaptionHide" onclick="setSidebarCaption('hide')">
                        <i class='bx bx-hide'></i>
                        <span>Sembunyikan</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="customizer-footer">
            <button type="button" class="customizer-reset-btn" onclick="resetThemeSettings()" title="Kembali ke setelan pabrik Admindek">
                <i class='bx bx-reset'></i>
                <span>Reset ke Default</span>
            </button>
            <button type="button" class="customizer-reset-btn" style="background:var(--admindek-primary); color:#ffffff; border-color:transparent;" onclick="closeThemeCustomizer()">
                <i class='bx bx-check'></i>
                <span>Selesai</span>
            </button>
        </div>
    </aside>

    <script>
        // --- 7. Admindek Theme Customizer Manager ---
        function openThemeCustomizer() {
            document.getElementById('admindekCustomizerDrawer')?.classList.add('open');
            document.getElementById('admindekCustomizerBackdrop')?.classList.add('open');
            syncCustomizerUI();
        }

        function closeThemeCustomizer() {
            document.getElementById('admindekCustomizerDrawer')?.classList.remove('open');
            document.getElementById('admindekCustomizerBackdrop')?.classList.remove('open');
        }

        function setAdmindekPreset(hex, name) {
            document.documentElement.style.setProperty('--bento-primary', hex);
            document.documentElement.style.setProperty('--vx-primary', hex);
            document.documentElement.style.setProperty('--apex-primary', hex);
            document.documentElement.style.setProperty('--admindek-primary', hex);
            
            localStorage.setItem('admindek_preset', hex);
            localStorage.setItem('admindek_preset_name', name);

            syncCustomizerUI();
            if (window.vxShowToast) {
                vxShowToast(`Warna tema diperbarui ke ${name}`, 'success');
            }
        }

        function setThemeMode(mode) {
            localStorage.setItem('apex_theme', mode);
            if (mode === 'dark' || (mode === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
            updateThemeUI();
            syncCustomizerUI();
        }

        function setLayoutWidth(layout) {
            localStorage.setItem('admindek_layout', layout);
            if (layout === 'boxed') {
                document.documentElement.classList.add('layout-boxed');
            } else {
                document.documentElement.classList.remove('layout-boxed');
            }
            syncCustomizerUI();
        }

        function setSidebarState(state) {
            const sidebar = document.getElementById('apexSidebar');
            if (state === 'collapsed') {
                sidebar?.classList.add('collapsed');
                localStorage.setItem('apex_sidebar_collapsed', '1');
                const icon = document.getElementById('sidebarToggleIcon');
                if (icon) icon.className = 'bx bx-chevron-right';
            } else {
                sidebar?.classList.remove('collapsed');
                localStorage.setItem('apex_sidebar_collapsed', '0');
                const icon = document.getElementById('sidebarToggleIcon');
                if (icon) icon.className = 'bx bx-chevron-left';
            }
            syncCustomizerUI();
        }

        function setSidebarCaption(caption) {
            localStorage.setItem('admindek_sidebar_caption', caption);
            if (caption === 'hide') {
                document.documentElement.classList.add('sidebar-hide-caption');
            } else {
                document.documentElement.classList.remove('sidebar-hide-caption');
            }
            syncCustomizerUI();
        }

        function resetThemeSettings() {
            localStorage.removeItem('admindek_preset');
            localStorage.removeItem('admindek_preset_name');
            localStorage.removeItem('apex_theme');
            localStorage.removeItem('admindek_layout');
            localStorage.removeItem('apex_sidebar_collapsed');
            localStorage.removeItem('admindek_sidebar_caption');

            setAdmindekPreset('#4680ff', 'Ocean Blue');
            setThemeMode('light');
            setLayoutWidth('fluid');
            setSidebarState('expanded');
            setSidebarCaption('show');

            if (window.vxShowToast) {
                vxShowToast('Setelan tema telah direset ke default Admindek', 'info');
            }
        }

        function syncCustomizerUI() {
            const currentColor = localStorage.getItem('admindek_preset') || '#4680ff';
            const currentName = localStorage.getItem('admindek_preset_name') || 'Ocean Blue';
            
            // Swatches
            document.querySelectorAll('.color-swatch-item').forEach(item => {
                const col = item.getAttribute('data-color');
                if (col && col.toLowerCase() === currentColor.toLowerCase()) {
                    item.classList.add('active');
                } else {
                    item.classList.remove('active');
                }
            });

            const nameEl = document.getElementById('activeColorName');
            if (nameEl) nameEl.textContent = currentName;

            // Theme Mode
            const savedTheme = localStorage.getItem('apex_theme') || 'light';
            document.getElementById('optThemeLight')?.classList.toggle('active', savedTheme === 'light');
            document.getElementById('optThemeDark')?.classList.toggle('active', savedTheme === 'dark');
            document.getElementById('optThemeSystem')?.classList.toggle('active', savedTheme === 'system');

            // Layout Width
            const savedLayout = localStorage.getItem('admindek_layout') || 'fluid';
            document.getElementById('optLayoutFluid')?.classList.toggle('active', savedLayout === 'fluid');
            document.getElementById('optLayoutBoxed')?.classList.toggle('active', savedLayout === 'boxed');

            // Sidebar State
            const sidebar = document.getElementById('apexSidebar');
            const isCollapsed = sidebar?.classList.contains('collapsed');
            document.getElementById('optSidebarExpanded')?.classList.toggle('active', !isCollapsed);
            document.getElementById('optSidebarCollapsed')?.classList.toggle('active', !!isCollapsed);

            // Sidebar Caption
            const isHideCaption = document.documentElement.classList.contains('sidebar-hide-caption');
            document.getElementById('optCaptionShow')?.classList.toggle('active', !isHideCaption);
            document.getElementById('optCaptionHide')?.classList.toggle('active', isHideCaption);
        }

        document.addEventListener('DOMContentLoaded', function() {
            syncCustomizerUI();
        });
    </script>
    <div id="apexToastContainer"></div>
    @stack('scripts')
</body>
</html>