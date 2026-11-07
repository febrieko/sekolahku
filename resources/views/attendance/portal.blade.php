<!DOCTYPE html>
<html lang="id" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Portal Presensi Siswa & GPS — Sekolahku</title>

    <!-- Theme & Color Detection (Zero-Flicker Apex/Vuexy Standard) -->
    <script>
        (function() {
            const savedTheme = localStorage.getItem('apex_theme') || 'light';
            if (savedTheme === 'dark' || (savedTheme === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
            const savedPreset = localStorage.getItem('admindek_preset') || '#4680ff';
            document.documentElement.style.setProperty('--bento-primary', savedPreset);
            document.documentElement.style.setProperty('--vx-primary', savedPreset);
        })();
    </script>

    <!-- Typography: Inter, Plus Jakarta Sans & JetBrains Mono (Standardized Across Sekolahku) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@500;600;700;800&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Icons: Boxicons & FontAwesome -->
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />

    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>

    <style>
        /* ===== SEKOLAHKU MODERN DESIGN SYSTEM ===== */
        :root {
            --bg-app:             #f4f7fa;
            --bg-surface:         #ffffff;
            --bg-card:            #ffffff;
            --bg-subtle:          #f8f9fa;
            --bg-hover:           #f1f5f9;
            --border-color:       #e2e8f0;
            --border-subtle:      #edf2f7;
            --text-heading:       #1e293b;
            --text-body:          #334155;
            --text-muted:         #64748b;
            --text-placeholder:   #94a3b8;

            /* Admindek/Vuexy Primary */
            --bento-primary:      #4680ff;
            --bento-primary-grad: linear-gradient(135deg, #3069ec 0%, #4680ff 60%, #6899ff 100%);
            --bento-primary-glow: rgba(70, 128, 255, 0.25);
            --vx-primary:         #4680ff;
            --vx-primary-hover:   #3366ff;
            --vx-primary-light:   rgba(70, 128, 255, 0.10);
            --vx-primary-border:  rgba(70, 128, 255, 0.22);

            --vx-success:         #10b981;
            --vx-success-light:   rgba(16, 185, 129, 0.12);
            --vx-success-border:  rgba(16, 185, 129, 0.28);

            --vx-warning:         #f59e0b;
            --vx-warning-light:   rgba(245, 158, 11, 0.12);
            --vx-warning-border:  rgba(245, 158, 11, 0.28);

            --vx-danger:          #ef4444;
            --vx-danger-light:    rgba(239, 68, 68, 0.12);
            --vx-danger-border:   rgba(239, 68, 68, 0.28);

            --vx-info:            #0284c7;
            --vx-info-light:      rgba(2, 132, 199, 0.12);
            --vx-info-border:     rgba(2, 132, 199, 0.28);

            --vx-purple:          #7c3aed;
            --vx-purple-light:    rgba(124, 58, 237, 0.12);

            --radius-sm:          6px;
            --radius-md:          10px;
            --radius-lg:          14px;
            --radius-xl:          18px;
            --radius-pill:        9999px;

            --shadow-card:        0 2px 12px rgba(15, 23, 42, 0.05);
            --shadow-card-hover:  0 10px 24px rgba(15, 23, 42, 0.09);
            --shadow-primary:     0 6px 18px rgba(70, 128, 255, 0.28);
            --transition:         all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        }

        html.dark {
            --bg-app:             #0f172a;
            --bg-surface:         #1e293b;
            --bg-card:            #1e293b;
            --bg-subtle:          #162032;
            --bg-hover:           #273549;
            --border-color:       #334155;
            --border-subtle:      #243347;
            --text-heading:       #f8fafc;
            --text-body:          #cbd5e1;
            --text-muted:         #94a3b8;
            --text-placeholder:   #64748b;
            --shadow-card:        0 6px 20px rgba(0, 0, 0, 0.35);
            --shadow-card-hover:  0 12px 28px rgba(0, 0, 0, 0.5);
        }

        *, *::before, *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: var(--bg-app);
            color: var(--text-body);
            line-height: 1.5;
            min-height: 100vh;
            -webkit-font-smoothing: antialiased;
        }

        .container {
            max-width: 1320px;
            margin: 0 auto;
            padding: 0 24px;
        }

        /* ===== TOPBAR HEADER ===== */
        .portal-header {
            background: var(--bg-surface);
            border-bottom: 1px solid var(--border-color);
            position: sticky;
            top: 0;
            z-index: 1000;
            backdrop-filter: blur(12px);
            transition: var(--transition);
        }

        .portal-header-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 70px;
            gap: 16px;
        }

        .brand-cluster {
            display: flex;
            align-items: center;
            gap: 14px;
            text-decoration: none;
        }

        .brand-icon {
            width: 44px;
            height: 44px;
            background: var(--bento-primary-grad);
            border-radius: var(--radius-md);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            font-size: 24px;
            box-shadow: var(--shadow-primary);
            flex-shrink: 0;
        }

        .brand-meta h1 {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 18px;
            font-weight: 800;
            color: var(--text-heading);
            display: flex;
            align-items: center;
            gap: 8px;
            letter-spacing: -0.3px;
        }

        .brand-badge {
            font-family: 'Inter', sans-serif;
            font-size: 11px;
            font-weight: 700;
            padding: 3px 9px;
            background: var(--vx-primary-light);
            color: var(--bento-primary);
            border-radius: var(--radius-pill);
            border: 1px solid var(--vx-primary-border);
            letter-spacing: 0;
        }

        .brand-meta p {
            font-size: 12px;
            color: var(--text-muted);
            margin-top: 1px;
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .meta-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: var(--bg-subtle);
            border: 1px solid var(--border-color);
            padding: 7px 14px;
            border-radius: var(--radius-pill);
            font-size: 12px;
            font-weight: 600;
            color: var(--text-heading);
        }

        .meta-pill i {
            color: var(--bento-primary);
            font-size: 15px;
        }

        .btn-theme-toggle {
            width: 38px;
            height: 38px;
            border-radius: var(--radius-md);
            border: 1px solid var(--border-color);
            background: var(--bg-surface);
            color: var(--text-muted);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            cursor: pointer;
            transition: var(--transition);
        }

        .btn-theme-toggle:hover {
            color: var(--bento-primary);
            border-color: var(--bento-primary);
            background: var(--vx-primary-light);
        }

        .btn-nav-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 14px;
            border-radius: var(--radius-md);
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            transition: var(--transition);
            border: 1px solid transparent;
        }

        .btn-nav-primary {
            background: var(--bento-primary);
            color: #ffffff;
            box-shadow: 0 2px 8px var(--bento-primary-glow);
        }

        .btn-nav-primary:hover {
            background: var(--vx-primary-hover);
            color: #ffffff;
            transform: translateY(-1px);
        }

        .btn-nav-secondary {
            background: var(--bg-subtle);
            color: var(--text-heading);
            border-color: var(--border-color);
        }

        .btn-nav-secondary:hover {
            background: var(--bg-hover);
            color: var(--bento-primary);
        }

        /* ===== NAVIGATION TABS ===== */
        .portal-tabs-bar {
            background: var(--bg-surface);
            border-bottom: 1px solid var(--border-color);
            padding: 0 24px;
        }

        .portal-tabs-wrapper {
            max-width: 1320px;
            margin: 0 auto;
            display: flex;
            gap: 6px;
            overflow-x: auto;
            scrollbar-width: none;
        }

        .portal-tabs-wrapper::-webkit-scrollbar {
            display: none;
        }

        .tab-btn {
            background: none;
            border: none;
            padding: 14px 18px;
            font-size: 13.5px;
            font-weight: 600;
            color: var(--text-muted);
            cursor: pointer;
            position: relative;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: var(--transition);
            white-space: nowrap;
        }

        .tab-btn i {
            font-size: 17px;
            color: var(--text-muted);
            transition: var(--transition);
        }

        .tab-btn:hover {
            color: var(--bento-primary);
        }

        .tab-btn:hover i {
            color: var(--bento-primary);
        }

        .tab-btn.active {
            color: var(--bento-primary);
            font-weight: 700;
        }

        .tab-btn.active i {
            color: var(--bento-primary);
        }

        .tab-btn.active::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: var(--bento-primary);
            border-radius: 3px 3px 0 0;
        }

        /* ===== MAIN BODY ===== */
        main {
            padding: 28px 0 64px 0;
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
            animation: fadeIn 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(6px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* ===== CARDS & UI SURFACES ===== */
        .card {
            background: var(--bg-surface);
            border-radius: var(--radius-lg);
            border: 1px solid var(--border-color);
            box-shadow: var(--shadow-card);
            padding: 24px;
            transition: var(--transition);
        }

        .card:hover {
            box-shadow: var(--shadow-card-hover);
        }

        .card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .card-title {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 16px;
            font-weight: 700;
            color: var(--text-heading);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .card-title i {
            font-size: 20px;
            color: var(--bento-primary);
        }

        /* ===== STATS KPI GRID ===== */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 18px;
            margin-bottom: 24px;
        }

        .stat-card {
            background: var(--bg-surface);
            border-radius: var(--radius-lg);
            border: 1px solid var(--border-color);
            padding: 18px 20px;
            display: flex;
            align-items: center;
            gap: 16px;
            box-shadow: var(--shadow-card);
            transition: var(--transition);
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-card-hover);
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: var(--radius-md);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            flex-shrink: 0;
        }

        .stat-icon.total { background: var(--vx-primary-light); color: var(--bento-primary); }
        .stat-icon.present { background: var(--vx-success-light); color: var(--vx-success); }
        .stat-icon.late { background: var(--vx-warning-light); color: var(--vx-warning); }
        .stat-icon.sick { background: var(--vx-info-light); color: var(--vx-info); }
        .stat-icon.absent { background: var(--vx-danger-light); color: var(--vx-danger); }

        .stat-meta h4 {
            font-size: 12px;
            font-weight: 600;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .stat-meta .stat-value {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 24px;
            font-weight: 800;
            color: var(--text-heading);
            margin-top: 2px;
            line-height: 1.2;
        }

        /* ===== SELFIE LAYOUT GRID ===== */
        .selfie-layout {
            display: grid;
            grid-template-columns: 1.05fr 1fr;
            gap: 24px;
        }

        @media (max-width: 992px) {
            .selfie-layout {
                grid-template-columns: 1fr;
            }
        }

        /* ===== CAMERA VIEWFINDER CONTAINER ===== */
        .camera-container {
            background: #090d16;
            border-radius: var(--radius-lg);
            position: relative;
            overflow: hidden;
            aspect-ratio: 4/3;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: inset 0 0 24px rgba(0, 0, 0, 0.7);
            border: 2px solid var(--border-color);
        }

        #webcamVideo {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transform: scaleX(-1);
        }

        #snapshotCanvas {
            display: none;
        }

        /* Scanner Frame & Facial Oval Guide */
        .face-oval-guide {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 220px;
            height: 270px;
            border: 2px dashed rgba(255, 255, 255, 0.5);
            border-radius: 50%;
            pointer-events: none;
            box-shadow: 0 0 0 9999px rgba(0, 0, 0, 0.28);
            transition: all 0.3s ease;
        }

        .face-oval-guide.in-range {
            border-color: var(--vx-success);
            border-style: solid;
            box-shadow: 0 0 0 9999px rgba(0, 0, 0, 0.22), 0 0 24px rgba(16, 185, 129, 0.45);
        }

        /* Scanner Cyber Beam Line */
        .scanner-beam {
            position: absolute;
            left: 0;
            right: 0;
            height: 2px;
            background: linear-gradient(90deg, transparent 0%, var(--bento-primary) 50%, transparent 100%);
            box-shadow: 0 0 12px var(--bento-primary);
            animation: scanLaser 3s infinite ease-in-out;
            pointer-events: none;
            opacity: 0.85;
        }

        @keyframes scanLaser {
            0% { top: 12%; opacity: 0.2; }
            50% { top: 88%; opacity: 1; }
            100% { top: 12%; opacity: 0.2; }
        }

        .camera-badge {
            position: absolute;
            top: 14px;
            left: 14px;
            background: rgba(15, 23, 42, 0.82);
            backdrop-filter: blur(8px);
            color: #ffffff;
            padding: 6px 12px;
            border-radius: var(--radius-pill);
            font-size: 12px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 7px;
            border: 1px solid rgba(255, 255, 255, 0.15);
        }

        .pulse-dot {
            width: 8px;
            height: 8px;
            background: var(--vx-success);
            border-radius: 50%;
            display: inline-block;
            animation: pulseSuccess 1.6s infinite;
        }

        @keyframes pulseSuccess {
            0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
            70% { transform: scale(1); box-shadow: 0 0 0 7px rgba(16, 185, 129, 0); }
            100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
        }

        /* ===== STUDENT HERO IDENTITY CARD ===== */
        .student-hero-card {
            background: var(--bento-primary-grad);
            border-radius: var(--radius-lg);
            color: #ffffff;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 10px 24px rgba(70, 128, 255, 0.28);
            display: flex;
            align-items: center;
            gap: 18px;
            position: relative;
            overflow: hidden;
        }

        .student-hero-card::after {
            content: '';
            position: absolute;
            right: -30px;
            top: -30px;
            width: 140px;
            height: 140px;
            background: rgba(255, 255, 255, 0.08);
            border-radius: 50%;
            pointer-events: none;
        }

        .student-photo-box {
            position: relative;
            width: 72px;
            height: 96px; /* Pas Foto 3:4 aspect */
            flex-shrink: 0;
            border-radius: 10px;
            overflow: hidden;
            border: 3px solid rgba(255, 255, 255, 0.9);
            box-shadow: 0 6px 14px rgba(0, 0, 0, 0.22);
            background: rgba(255, 255, 255, 0.2);
        }

        .student-photo-box img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .student-today-status {
            border-radius: var(--radius-md);
            padding: 16px;
            margin-bottom: 18px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            transition: var(--transition);
        }

        .student-today-status.present {
            background: var(--vx-success-light);
            border: 1px solid var(--vx-success-border);
            color: #065f46;
        }

        html.dark .student-today-status.present {
            color: #34d399;
        }

        .student-today-status.late {
            background: var(--vx-warning-light);
            border: 1px solid var(--vx-warning-border);
            color: #92400e;
        }

        html.dark .student-today-status.late {
            color: #fbbf24;
        }

        .student-today-status.none {
            background: var(--bg-subtle);
            border: 1px dashed var(--border-color);
            color: var(--text-muted);
        }

        /* ===== GPS STATUS WIDGET ===== */
        .gps-status-box {
            background: var(--bg-subtle);
            border-radius: var(--radius-md);
            border: 1px solid var(--border-color);
            padding: 16px;
            margin-bottom: 20px;
        }

        .gps-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 10px;
            font-size: 13px;
        }

        .gps-row:last-child {
            margin-bottom: 0;
        }

        .gps-label {
            color: var(--text-muted);
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .gps-label i {
            color: var(--bento-primary);
        }

        .gps-value {
            font-weight: 700;
            color: var(--text-heading);
            font-family: 'JetBrains Mono', monospace;
            font-size: 12.5px;
        }

        .geofence-alert {
            padding: 12px 16px;
            border-radius: var(--radius-md);
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 13px;
            font-weight: 600;
            margin-top: 14px;
            border: 1px solid transparent;
        }

        .geofence-alert.within {
            background: var(--vx-success-light);
            color: #065f46;
            border-color: var(--vx-success-border);
        }

        html.dark .geofence-alert.within {
            color: #34d399;
        }

        .geofence-alert.outside {
            background: var(--vx-danger-light);
            color: #991b1b;
            border-color: var(--vx-danger-border);
        }

        html.dark .geofence-alert.outside {
            color: #f87171;
        }

        /* ===== FORM CONTROLS & BUTTONS ===== */
        .form-group {
            margin-bottom: 16px;
        }

        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: var(--text-heading);
            margin-bottom: 6px;
        }

        .form-select, .form-input, .form-textarea {
            width: 100%;
            padding: 10px 14px;
            border-radius: var(--radius-md);
            border: 1px solid var(--border-color);
            font-size: 13.5px;
            color: var(--text-heading);
            background: var(--bg-surface);
            transition: var(--transition);
            font-family: inherit;
        }

        .form-select:focus, .form-input:focus, .form-textarea:focus {
            outline: none;
            border-color: var(--bento-primary);
            box-shadow: 0 0 0 3px var(--vx-primary-light);
        }

        .sim-buttons-group {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            margin-top: 10px;
        }

        .btn-chip {
            background: var(--bg-surface);
            border: 1px solid var(--border-color);
            color: var(--text-heading);
            font-size: 12px;
            font-weight: 600;
            padding: 6px 12px;
            border-radius: var(--radius-pill);
            cursor: pointer;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-chip:hover {
            background: var(--bg-hover);
            border-color: var(--bento-primary);
            color: var(--bento-primary);
        }

        .btn-chip.active {
            background: var(--bento-primary);
            color: #ffffff;
            border-color: var(--bento-primary);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 11px 18px;
            border-radius: var(--radius-md);
            font-size: 13.5px;
            font-weight: 600;
            cursor: pointer;
            border: 1px solid transparent;
            transition: var(--transition);
            text-decoration: none;
            font-family: inherit;
        }

        .btn-primary {
            background: var(--bento-primary);
            color: #ffffff;
            box-shadow: var(--shadow-primary);
        }

        .btn-primary:hover {
            background: var(--vx-primary-hover);
            color: #ffffff;
            transform: translateY(-1px);
        }

        .btn-primary:disabled {
            opacity: 0.65;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        .btn-outline {
            background: var(--bg-surface);
            border-color: var(--border-color);
            color: var(--text-heading);
        }

        .btn-outline:hover {
            background: var(--bg-hover);
            border-color: var(--bento-primary);
            color: var(--bento-primary);
        }

        .btn-block {
            width: 100%;
        }

        .btn-action-edit {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 5px 10px;
            font-size: 12px;
            font-weight: 600;
            border-radius: var(--radius-sm, 6px);
            border: 1px solid var(--border-color);
            background: var(--bg-surface);
            color: var(--text-heading);
            cursor: pointer;
            transition: var(--transition);
        }
        .btn-action-edit:hover {
            border-color: var(--bento-primary);
            color: var(--bento-primary);
            background: var(--vx-primary-light);
        }

        .btn-action-delete {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 5px 10px;
            font-size: 12px;
            font-weight: 600;
            border-radius: var(--radius-sm, 6px);
            border: 1px solid rgba(239, 68, 68, 0.25);
            background: rgba(239, 68, 68, 0.05);
            color: #ef4444;
            cursor: pointer;
            transition: var(--transition);
        }
        .btn-action-delete:hover {
            background: #ef4444;
            color: #ffffff;
            border-color: #ef4444;
        }

        .pulse-button {
            animation: pulsePrimary 2.2s infinite;
        }

        @keyframes pulsePrimary {
            0% { box-shadow: 0 0 0 0 rgba(70, 128, 255, 0.55); }
            70% { box-shadow: 0 0 0 10px rgba(70, 128, 255, 0); }
            100% { box-shadow: 0 0 0 0 rgba(70, 128, 255, 0); }
        }

        /* ===== TABLES ===== */
        .table-responsive {
            overflow-x: auto;
            border-radius: var(--radius-md);
            border: 1px solid var(--border-color);
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
            text-align: left;
        }

        .data-table th {
            background: var(--bg-subtle);
            color: var(--text-muted);
            font-weight: 700;
            padding: 12px 16px;
            border-bottom: 1px solid var(--border-color);
            white-space: nowrap;
            text-transform: uppercase;
            font-size: 11.5px;
            letter-spacing: 0.4px;
        }

        .data-table td {
            padding: 14px 16px;
            border-bottom: 1px solid var(--border-color);
            vertical-align: middle;
            color: var(--text-body);
        }

        .data-table tbody tr {
            transition: var(--transition);
        }

        .data-table tbody tr:hover {
            background: var(--bg-hover);
        }

        /* ===== BADGES ===== */
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 10px;
            border-radius: var(--radius-pill);
            font-size: 11.5px;
            font-weight: 700;
            text-transform: capitalize;
            letter-spacing: 0.2px;
        }

        .status-badge.present { background: var(--vx-success-light); color: #065f46; border: 1px solid var(--vx-success-border); }
        .status-badge.late { background: var(--vx-warning-light); color: #92400e; border: 1px solid var(--vx-warning-border); }
        .status-badge.sick { background: var(--vx-info-light); color: #0369a1; border: 1px solid var(--vx-info-border); }
        .status-badge.excused { background: var(--vx-purple-light); color: var(--vx-purple); border: 1px solid rgba(124, 58, 237, 0.25); }
        .status-badge.absent { background: var(--vx-danger-light); color: #991b1b; border: 1px solid var(--vx-danger-border); }

        html.dark .status-badge.present { color: #34d399; }
        html.dark .status-badge.late { color: #fbbf24; }
        html.dark .status-badge.sick { color: #38bdf8; }
        html.dark .status-badge.absent { color: #f87171; }

        .method-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 11.5px;
            color: var(--text-muted);
            background: var(--bg-subtle);
            border: 1px solid var(--border-color);
            padding: 3px 8px;
            border-radius: var(--radius-sm);
            font-weight: 600;
        }

        /* ===== MAP CONTAINER ===== */
        #attendanceMap, #geofenceConfigMap {
            width: 100%;
            height: 520px;
            border-radius: var(--radius-md);
            border: 1px solid var(--border-color);
            z-index: 1;
        }

        /* ===== MODAL & TOAST ===== */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(15, 23, 42, 0.65);
            backdrop-filter: blur(6px);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 2000;
            padding: 20px;
        }

        .modal-overlay.active {
            display: flex;
            animation: fadeIn 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .modal-box {
            background: var(--bg-surface);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-xl);
            width: 100%;
            max-width: 520px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.35);
            overflow: hidden;
        }

        .modal-header {
            padding: 18px 22px;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .modal-header h3 {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 16px;
            font-weight: 700;
            color: var(--text-heading);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .modal-close {
            background: none;
            border: none;
            font-size: 20px;
            color: var(--text-muted);
            cursor: pointer;
            transition: var(--transition);
        }

        .modal-close:hover {
            color: var(--vx-danger);
        }

        .modal-body {
            padding: 22px;
            max-height: 80vh;
            overflow-y: auto;
        }

        .modal-footer {
            padding: 14px 22px;
            background: var(--bg-subtle);
            border-top: 1px solid var(--border-color);
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }

        .toast-container {
            position: fixed;
            bottom: 24px;
            right: 24px;
            z-index: 3000;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .toast {
            background: var(--bg-surface);
            color: var(--text-heading);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-md);
            padding: 12px 18px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.18);
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 13.5px;
            font-weight: 600;
            min-width: 300px;
            border-left: 4px solid var(--bento-primary);
            animation: slideInRight 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .toast.success { border-left-color: var(--vx-success); }
        .toast.error { border-left-color: var(--vx-danger); }

        @keyframes slideInRight {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
    </style>
</head>
<body>

    <!-- TOPBAR / MODERN HEADER -->
    <header class="portal-header">
        <div class="container">
            <div class="portal-header-content">
                <!-- Brand Cluster -->
                <a href="{{ route('home') }}" class="brand-cluster">
                    <div class="brand-icon">
                        @if(!empty($appBranding['app_logo']))
                            <img src="{{ $appBranding['app_logo'] }}" alt="{{ $appBranding['app_name'] ?? 'Sekolahku' }}" style="width: 100%; height: 100%; object-fit: contain; border-radius: inherit;">
                        @else
                            <i class='bx bxs-graduation'></i>
                        @endif
                    </div>
                    <div class="brand-meta">
                        <h1>
                            {{ $appBranding['app_name'] ?? 'Sekolahku' }}
                            <span class="brand-badge">Modul Presensi Siswa & GPS</span>
                        </h1>
                        <p>{{ $school->name ?? 'SMA Negeri 1 Jakarta' }} • Tenant: <strong>{{ $school->tenant_id ?? 'sman1jkt' }}</strong></p>
                    </div>
                </a>

                <!-- Meta & User Actions -->
                <div class="header-actions">
                    @if($isSuperAdmin && $availableSchools->count() > 1)
                        <div class="meta-pill" style="gap: 8px; border-color: rgba(245, 158, 11, 0.4); background: rgba(245, 158, 11, 0.08);">
                            <i class='bx bx-crown' style="color: #d97706; font-size: 16px;"></i>
                            <span style="font-size: 11px; font-weight: 700; color: #b45309;">Sekolah:</span>
                            <select id="switchSchoolSelect" onchange="window.location.href='?school_id=' + this.value" style="border: none; background: transparent; font-size: 12px; font-weight: 800; color: var(--text-heading); cursor: pointer; outline: none;">
                                @foreach($availableSchools as $sc)
                                    <option value="{{ $sc->id }}" {{ $sc->id == $school->id ? 'selected' : '' }}>
                                        {{ $sc->name }} ({{ $sc->level ?? 'Unit' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @elseif($isSchoolAdmin)
                        <div class="meta-pill" style="gap: 6px; border-color: rgba(70, 128, 255, 0.35); background: var(--vx-primary-light);">
                            <i class='bx bx-buildings' style="color: var(--bento-primary); font-size: 16px;"></i>
                            <span style="font-size: 11px; font-weight: 700; color: var(--bento-primary);">Enrol: {{ $school->name }}</span>
                        </div>
                    @endif

                    <div class="meta-pill">
                        <i class='bx bx-calendar'></i>
                        <span>{{ $todayDate }}</span>
                    </div>

                    <!-- Theme Toggle -->
                    <button type="button" class="btn-theme-toggle" onclick="toggleApexTheme()" title="Ubah Mode Gelap / Terang">
                        <i class='bx bx-moon' id="themeIcon"></i>
                    </button>

                    @if($user)
                        <div class="meta-pill" style="gap: 8px;">
                            <i class='bx bx-user-circle' style="font-size: 18px;"></i>
                            <span>{{ $user->name }}</span>
                            <span style="font-size: 10px; background: var(--vx-primary-light); color: var(--bento-primary); padding: 2px 7px; border-radius: var(--radius-pill); font-weight: 800; text-transform: uppercase;">
                                {{ $user->role }}
                            </span>
                        </div>

                        @if($isStudent)
                            <a href="{{ route('student.dashboard') }}" class="btn-nav-link btn-nav-primary">
                                <i class='bx bxs-dashboard'></i> Dashboard Siswa
                            </a>
                            <a href="{{ route('student.profile') }}" class="btn-nav-link btn-nav-secondary">
                                <i class='bx bx-id-card'></i> Pas Foto 4x6
                            </a>
                        @else
                            <a href="{{ route('admin.dashboard') }}" class="btn-nav-link btn-nav-primary">
                                <i class='bx bxs-dashboard'></i> Panel Guru/Admin
                            </a>
                            <a href="{{ route('admin.data-master') }}" class="btn-nav-link btn-nav-secondary">
                                <i class='bx bx-data'></i> Data Master
                            </a>
                        @endif

                        <form action="{{ route('logout') }}" method="POST" style="margin: 0; display: inline;">
                            @csrf
                            <button type="submit" class="btn-nav-link btn-nav-secondary" title="Keluar dari sesi" style="cursor: pointer;">
                                <i class='bx bx-log-out'></i> Keluar
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="btn-nav-link btn-nav-primary">
                            <i class='bx bx-log-in'></i> Masuk Akun
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </header>

    <!-- NAVIGATION TABS -->
    <nav class="portal-tabs-bar">
        <div class="portal-tabs-wrapper">
            @if($isStudent)
                <button class="tab-btn active" onclick="switchTab('selfie-tab')">
                    <i class='bx bx-camera'></i> Presensi Wajah & GPS Saya
                </button>
                <button class="tab-btn" onclick="switchTab('my-history-tab')">
                    <i class='bx bx-history'></i> Riwayat Kehadiran Saya
                </button>
            @else
                <button class="tab-btn active" onclick="switchTab('selfie-tab')">
                    <i class='bx bx-camera'></i> Presensi Selfie Siswa
                </button>
                <button class="tab-btn" onclick="switchTab('dashboard-tab')">
                    <i class='bx bx-bar-chart-alt-2'></i> Rekap & Monitoring Guru
                </button>
                <button class="tab-btn" onclick="switchTab('map-tab')">
                    <i class='bx bx-map-pin'></i> Peta Sebaran GPS Live
                </button>
                @if($canManageGeofence)
                <button class="tab-btn" onclick="switchTab('geofence-tab')">
                    <i class='bx bx-target-lock'></i> Konfigurasi Geofencing
                </button>
                @endif
            @endif
        </div>
    </nav>

    <!-- MAIN BODY -->
    <main class="container">

        <!-- ========================================== -->
        <!-- TAB 1: PRESENSI SELFIE SISWA               -->
        <!-- ========================================== -->
        <div id="selfie-tab" class="tab-content active">
            <div class="selfie-layout">
                <!-- Camera Column -->
                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title">
                            <i class='bx bx-camera'></i> Kamera Presensi Wajah
                        </h2>
                        <button class="btn btn-outline" style="padding: 6px 12px; font-size: 12.5px;" onclick="initWebcam()">
                            <i class='bx bx-refresh'></i> Nyalakan Kamera
                        </button>
                    </div>

                    <div class="camera-container">
                        <video id="webcamVideo" autoplay playsinline></video>
                        <canvas id="snapshotCanvas"></canvas>
                        <div class="face-oval-guide" id="faceGuide"></div>
                        <div class="scanner-beam"></div>
                        <div class="camera-badge">
                            <span class="pulse-dot"></span>
                            <span id="cameraStatusText">Siap Merekam</span>
                        </div>
                    </div>

                    <div style="margin-top: 20px; display: flex; gap: 12px;">
                        @if($isStudent)
                            @if(!$todayAttendance || !$todayAttendance->check_in_at)
                                <button id="btnCheckIn" class="btn btn-primary btn-block pulse-button" onclick="submitSelfieAttendance()">
                                    <i class='bx bx-camera'></i> Ambil Foto & Presensi Masuk
                                </button>
                                <button id="btnCheckOut" class="btn btn-outline" style="opacity: 0.6; cursor: not-allowed;" title="Presensi masuk terlebih dahulu" disabled>
                                    <i class='bx bx-log-out'></i> Pulang
                                </button>
                            @elseif($todayAttendance->check_in_at && !$todayAttendance->check_out_at)
                                <button id="btnCheckIn" class="btn btn-outline" style="background: var(--vx-success-light); color: var(--vx-success); border-color: var(--vx-success-border); cursor: default;" disabled>
                                    <i class='bx bx-check-circle'></i> Masuk: {{ substr($todayAttendance->check_in_at, 0, 5) }} WIB
                                </button>
                                <button id="btnCheckOut" class="btn btn-primary btn-block pulse-button" onclick="submitCheckOut()" style="background: var(--vx-info); border-color: var(--vx-info);">
                                    <i class='bx bx-walk'></i> Presensi Pulang Sekarang
                                </button>
                            @else
                                <button class="btn btn-outline" style="background: var(--vx-success-light); color: var(--vx-success); border-color: var(--vx-success-border); width: 100%; cursor: default; font-weight: 700;" disabled>
                                    <i class='bx bx-party'></i> Kehadiran Lengkap (Masuk: {{ substr($todayAttendance->check_in_at, 0, 5) }} • Pulang: {{ substr($todayAttendance->check_out_at, 0, 5) }})
                                </button>
                            @endif
                        @else
                            <button id="btnCheckIn" class="btn btn-primary btn-block" onclick="submitSelfieAttendance()">
                                <i class='bx bx-camera'></i> Ambil Foto & Presensi Masuk
                            </button>
                            <button id="btnCheckOut" class="btn btn-outline" onclick="submitCheckOut()" title="Presensi Pulang">
                                <i class='bx bx-walk'></i> Pulang
                            </button>
                        @endif
                    </div>
                </div>

                <!-- Info & GPS Column -->
                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title">
                            <i class='bx bx-map-pin'></i> Data Siswa & Verifikasi Geofence
                        </h2>
                    </div>

                    @if($isStudent && $currentStudent)
                        <!-- Kartu Identitas Siswa Digital -->
                        <div class="student-hero-card">
                            <div class="student-photo-box">
                                @if($currentStudent->photo_url)
                                    <img src="{{ asset('storage/' . $currentStudent->photo_url) }}" alt="{{ $currentStudent->full_name }}" onclick="viewPhoto('{{ asset('storage/' . $currentStudent->photo_url) }}', '{{ $currentStudent->full_name }}', 'Pas Foto 4x6', '-')" style="cursor: pointer;" title="Klik untuk memperbesar">
                                @else
                                    <div style="display: flex; align-items: center; justify-content: center; height: 100%; font-size: 32px;">
                                        <i class='bx bxs-user'></i>
                                    </div>
                                @endif
                            </div>
                            <div style="flex: 1; min-width: 0;">
                                <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                                    <span style="background: rgba(255,255,255,0.22); padding: 2px 8px; border-radius: var(--radius-pill); font-size: 11px; font-weight: 700; text-transform: uppercase;">
                                        {{ $currentStudent->schoolClass?->name ?? 'Siswa Aktif' }}
                                    </span>
                                    @if($currentStudent->entryAcademicYear)
                                        <span style="background: rgba(255,255,255,0.18); padding: 2px 8px; border-radius: var(--radius-pill); font-size: 11px;">
                                            Angkatan {{ $currentStudent->entryAcademicYear->name }}
                                        </span>
                                    @endif
                                </div>
                                <h3 style="font-size: 17px; font-weight: 800; margin: 4px 0 2px 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; font-family: 'Plus Jakarta Sans', sans-serif;">
                                    {{ $currentStudent->full_name }}
                                </h3>
                                <div style="font-size: 12px; opacity: 0.92; font-family: 'JetBrains Mono', monospace;">
                                    NIS: <strong>{{ $currentStudent->nis ?? '-' }}</strong> • NISN: <strong>{{ $currentStudent->nisn ?? '-' }}</strong>
                                </div>
                                <div style="margin-top: 8px;">
                                    <a href="{{ route('student.profile') }}" style="display: inline-flex; align-items: center; gap: 4px; font-size: 11px; font-weight: 700; background: #ffffff; color: var(--bento-primary); padding: 4px 10px; border-radius: var(--radius-sm); text-decoration: none; box-shadow: 0 2px 6px rgba(0,0,0,0.12);">
                                        <i class='bx bx-edit-alt'></i> Biodata & Pas Foto 4x6
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Status Presensi Realtime Hari Ini -->
                        @if($todayAttendance)
                            <div class="student-today-status {{ $todayAttendance->status === 'late' ? 'late' : 'present' }}">
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <div style="font-size: 26px;">
                                        <i class='bx {{ $todayAttendance->status === 'late' ? 'bx-time-five' : 'bx-check-circle' }}'></i>
                                    </div>
                                    <div>
                                        <div style="font-size: 11px; text-transform: uppercase; font-weight: 800; letter-spacing: 0.5px;">
                                            Status Hari Ini: {{ $todayAttendance->status === 'late' ? 'Terlambat' : 'Hadir Tepat Waktu' }}
                                        </div>
                                        <div style="font-size: 13px; font-weight: 700; margin-top: 2px;">
                                            Masuk: <span style="font-family: 'JetBrains Mono', monospace;">{{ substr($todayAttendance->check_in_at, 0, 5) }} WIB</span>
                                            @if($todayAttendance->check_out_at)
                                                • Pulang: <span style="font-family: 'JetBrains Mono', monospace;">{{ substr($todayAttendance->check_out_at, 0, 5) }} WIB</span>
                                            @else
                                                <span style="font-weight: normal; color: var(--text-muted);">(Belum presensi pulang)</span>
                                            @endif
                                        </div>
                                        <div style="font-size: 11px; opacity: 0.85; margin-top: 2px;">
                                            Jarak GPS: <strong>{{ round($todayAttendance->distance_meters, 1) }}m</strong> dari geofence sekolah
                                        </div>
                                    </div>
                                </div>
                                @if($todayAttendance->selfie_url)
                                    <button type="button" class="btn-chip" onclick="viewPhoto('{{ $todayAttendance->selfie_url }}', '{{ $currentStudent->full_name }}', '{{ $todayAttendance->check_in_at }}', '{{ $todayAttendance->distance_meters }}m')" style="flex-shrink: 0;">
                                        <i class='bx bx-show'></i> Bukti Selfie
                                    </button>
                                @endif
                            </div>
                        @else
                            <div class="student-today-status none">
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <div style="font-size: 26px; color: var(--text-muted);">
                                        <i class='bx bx-radio-circle'></i>
                                    </div>
                                    <div>
                                        <div style="font-size: 13px; font-weight: 700; color: var(--text-heading);">Belum Presensi Hari Ini</div>
                                        <div style="font-size: 12px; color: var(--text-muted);">Arahkan wajah ke panduan kamera, pastikan GPS dalam radius sekolah, lalu tekan tombol Ambil Foto & Presensi Masuk.</div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @else
                        <!-- Student Selector for Testing / Teacher -->
                        <div class="form-group">
                            <label class="form-label">Pilih Akun Siswa (Untuk Pengujian):</label>
                            <select id="studentSelect" class="form-select" onchange="onStudentChanged()">
                                @foreach($students as $s)
                                    <option value="{{ $s->id }}" data-class="{{ $s->schoolClass?->name }}" data-nis="{{ $s->nis }}">
                                        {{ $s->full_name }} (NIS: {{ $s->nis }} • {{ $s->schoolClass?->name }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <!-- GPS Status Box -->
                    <div class="gps-status-box">
                        <div class="gps-row">
                            <span class="gps-label"><i class='bx bx-building'></i> Titik Geofence Sekolah:</span>
                            <span class="gps-value">{{ $primaryGeofence->name ?? 'Kampus Utama' }} (Radius {{ $primaryGeofence->radius_meters ?? 150 }}m)</span>
                        </div>
                        <div class="gps-row">
                            <span class="gps-label"><i class='bx bx-current-location'></i> Koordinat Anda:</span>
                            <span class="gps-value" id="currentCoordText">Mendeteksi GPS...</span>
                        </div>
                        <div class="gps-row">
                            <span class="gps-label"><i class='bx bx-ruler'></i> Jarak ke Titik Sekolah:</span>
                            <span class="gps-value" id="currentDistanceText">- meter</span>
                        </div>
                        <div class="gps-row">
                            <span class="gps-label"><i class='bx bx-radar'></i> Akurasi GPS Device:</span>
                            <span class="gps-value" id="currentAccuracyText">Mencari sinyal...</span>
                        </div>

                        <!-- Geofence Status Alert -->
                        <div id="geofenceAlert" class="geofence-alert" style="background: var(--bg-hover); color: var(--text-muted); border: 1px solid var(--border-color);">
                            <i class='bx bx-loader-alt bx-spin' style="font-size: 18px;"></i>
                            <span id="geofenceAlertText">Meminta izin lokasi dan mengunci sinyal GPS asli...</span>
                        </div>
                    </div>

                    @if($isStudent)
                        <!-- Kontrol GPS Riil Khusus Siswa -->
                        <div class="form-group">
                            <div style="display: flex; align-items: center; justify-content: space-between; gap: 10px;">
                                <label class="form-label" style="margin-bottom: 0;">Status Sensor Lokasi:</label>
                                <button type="button" class="btn-chip" onclick="getRealGps(true)" style="font-weight: 700;">
                                    <i class='bx bx-refresh'></i> Kalibrasi / Refresh GPS
                                </button>
                            </div>
                            <p style="font-size: 11.5px; color: var(--text-muted); margin-top: 6px;">
                                <i class='bx bx-info-circle'></i> Pastikan fitur <strong>Lokasi/GPS</strong> pada perangkat HP/Laptop Anda aktif dan browser telah diberi izin lokasi.
                            </p>
                        </div>
                    @else
                        <!-- Preset Coordinates for Testing / Guru / Admin -->
                        <div class="form-group">
                            <label class="form-label">Mode Pengujian & Simulasi (Khusus Admin/Guru):</label>
                            <div class="sim-buttons-group">
                                <button type="button" class="btn-chip" onclick="setPresetLocation('school')">
                                    <i class='bx bxs-school'></i> Di Sekolah (~25m)
                                </button>
                                <button type="button" class="btn-chip" onclick="setPresetLocation('monas')">
                                    <i class='bx bx-buildings'></i> Luar Sekolah (Monas ~1.8km)
                                </button>
                                <button type="button" class="btn-chip active" onclick="getRealGps(true)">
                                    <i class='bx bx-broadcast'></i> Gunakan GPS Asli Saya
                                </button>
                                <button type="button" class="btn-chip" id="btnToggleMock" onclick="toggleMockLocation()">
                                    <i class='bx bx-shield'></i> Mock GPS: Nonaktif
                                </button>
                            </div>
                        </div>
                    @endif

                    <!-- Result Panel -->
                    <div id="attendanceResultPanel" style="display: none; margin-top: 20px; padding: 16px; border-radius: var(--radius-md); background: var(--vx-primary-light); border: 1px solid var(--vx-primary-border);">
                        <h4 style="font-size: 13.5px; font-weight: 700; color: var(--bento-primary); margin-bottom: 8px; display: flex; align-items: center; gap: 6px;">
                            <i class='bx bx-check-double'></i> Hasil Presensi:
                        </h4>
                        <div id="attendanceResultContent" style="font-size: 13px; color: var(--text-body);"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- TAB 2: RIWAYAT KEHADIRAN SAYA (SISWA)     -->
        <!-- ========================================== -->
        @if($isStudent)
        <div id="my-history-tab" class="tab-content">
            <!-- Stats Grid Siswa -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon total"><i class='bx bx-calendar-check'></i></div>
                    <div class="stat-meta">
                        <h4>Total Rekaman</h4>
                        <div class="stat-value">{{ $studentStats['total'] ?? 0 }} <span style="font-size: 13px; font-weight: 500; color: var(--text-muted);">Hari</span></div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon present"><i class='bx bx-check-circle'></i></div>
                    <div class="stat-meta">
                        <h4>Tepat Waktu</h4>
                        <div class="stat-value">{{ $studentStats['present'] ?? 0 }}</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon late"><i class='bx bx-time-five'></i></div>
                    <div class="stat-meta">
                        <h4>Terlambat</h4>
                        <div class="stat-value">{{ $studentStats['late'] ?? 0 }}</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon sick"><i class='bx bx-plus-medical'></i></div>
                    <div class="stat-meta">
                        <h4>Sakit / Izin</h4>
                        <div class="stat-value">{{ ($studentStats['sick'] ?? 0) + ($studentStats['excused'] ?? 0) }}</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon absent"><i class='bx bx-x-circle'></i></div>
                    <div class="stat-meta">
                        <h4>Alpa</h4>
                        <div class="stat-value">{{ $studentStats['absent'] ?? 0 }}</div>
                    </div>
                </div>
            </div>

            <!-- Card Riwayat Tabel -->
            <div class="card">
                <div class="card-header" style="flex-wrap: wrap; gap: 12px;">
                    <div>
                        <h2 class="card-title">
                            <i class='bx bx-list-check'></i> Riwayat Presensi Kehadiran Siswa
                        </h2>
                        <p style="font-size: 13px; color: var(--text-muted); margin-top: 4px;">
                            Menampilkan riwayat 30 presensi kehadiran terbaru untuk Anda di {{ $school->name ?? 'sekolah' }}.
                        </p>
                    </div>
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <span style="font-size: 13px; font-weight: 800; color: var(--vx-info); background: var(--vx-info-light); border: 1px solid var(--vx-info-border); padding: 6px 14px; border-radius: var(--radius-pill);">
                            Tingkat Kehadiran: {{ $studentStats['rate'] ?? 100 }}%
                        </span>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Tanggal Presensi</th>
                                <th>Jam Masuk</th>
                                <th>Jam Pulang</th>
                                <th>Status Kehadiran</th>
                                <th>Metode</th>
                                <th>Jarak GPS</th>
                                <th>Bukti Selfie</th>
                                <th>Catatan / Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($myAttendances as $att)
                                <tr>
                                    <td>
                                        <strong>{{ \Carbon\Carbon::parse($att->date)->isoFormat('dddd, D MMMM Y') }}</strong>
                                    </td>
                                    <td>
                                        <strong style="font-family: 'JetBrains Mono', monospace; color: var(--text-heading);">
                                            {{ $att->check_in_at ? substr($att->check_in_at, 0, 5) . ' WIB' : '-' }}
                                        </strong>
                                    </td>
                                    <td>
                                        <span style="font-family: 'JetBrains Mono', monospace; color: var(--text-muted);">
                                            {{ $att->check_out_at ? substr($att->check_out_at, 0, 5) . ' WIB' : '-' }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($att->status === 'present')
                                            <span class="status-badge present"><i class='bx bx-check'></i> Hadir Tepat Waktu</span>
                                        @elseif($att->status === 'late')
                                            <span class="status-badge late"><i class='bx bx-time'></i> Terlambat</span>
                                        @elseif($att->status === 'sick')
                                            <span class="status-badge sick"><i class='bx bx-plus-medical'></i> Sakit</span>
                                        @elseif($att->status === 'excused')
                                            <span class="status-badge excused"><i class='bx bx-file'></i> Izin</span>
                                        @else
                                            <span class="status-badge absent"><i class='bx bx-x'></i> Alpa</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="method-badge">
                                            {{ $att->method === 'selfie' ? '🤳 Selfie GPS' : ($att->method === 'qr_code' ? '📱 QR Scan' : '✏️ Manual') }}
                                        </span>
                                    </td>
                                    <td style="font-family: 'JetBrains Mono', monospace;">
                                        {{ $att->distance_meters ? round($att->distance_meters, 1) . ' m' : '-' }}
                                    </td>
                                    <td>
                                        @if($att->selfie_url)
                                            <button type="button" class="btn btn-outline" style="padding: 4px 10px; font-size: 12px;" onclick="viewPhoto('{{ $att->selfie_url }}', '{{ $currentStudent->full_name }}', '{{ $att->check_in_at }}', '{{ $att->distance_meters }}m')">
                                                <i class='bx bx-show'></i> Lihat Foto
                                            </button>
                                        @else
                                            <span style="color: var(--text-muted); font-size: 12px;">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span style="font-size: 12px; color: var(--text-muted);">
                                            {{ $att->notes ?? ($att->is_within_radius ? 'Presensi dalam radius sekolah' : '-') }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" style="text-align: center; color: var(--text-muted); padding: 40px;">
                                        Belum ada riwayat presensi tercatat untuk akun siswa Anda.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif

        @if(!$isStudent)
        <!-- ========================================== -->
        <!-- TAB 2: REKAP & MONITORING GURU             -->
        <!-- ========================================== -->
        <div id="dashboard-tab" class="tab-content">
            <!-- Stats Grid -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon total"><i class='bx bx-group'></i></div>
                    <div class="stat-meta">
                        <h4>Total Siswa</h4>
                        <div class="stat-value" id="kpiTotal">0</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon present"><i class='bx bx-check-circle'></i></div>
                    <div class="stat-meta">
                        <h4>Hadir Tepat Waktu</h4>
                        <div class="stat-value" id="kpiPresent">0</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon late"><i class='bx bx-time-five'></i></div>
                    <div class="stat-meta">
                        <h4>Terlambat</h4>
                        <div class="stat-value" id="kpiLate">0</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon sick"><i class='bx bx-plus-medical'></i></div>
                    <div class="stat-meta">
                        <h4>Sakit / Izin</h4>
                        <div class="stat-value" id="kpiSick">0</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon absent"><i class='bx bx-x-circle'></i></div>
                    <div class="stat-meta">
                        <h4>Belum / Alpa</h4>
                        <div class="stat-value" id="kpiAbsent">0</div>
                    </div>
                </div>
            </div>

            <!-- Attendance Table Card -->
            <div class="card">
                <div class="card-header" style="flex-wrap: wrap; gap: 14px;">
                    <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
                        <h2 class="card-title">
                            <i class='bx bx-list-check'></i> Daftar Kehadiran Hari Ini
                        </h2>
                        <select id="filterClass" class="form-select" style="width: auto;" onchange="loadAttendancesTable()">
                            <option value="">Semua Kelas</option>
                            @foreach($classes as $c)
                                <option value="{{ $c->id }}">{{ $c->name }}</option>
                            @endforeach
                        </select>
                        <select id="filterStatus" class="form-select" style="width: auto;" onchange="loadAttendancesTable()">
                            <option value="">Semua Status</option>
                            <option value="present">Hadir</option>
                            <option value="late">Terlambat</option>
                            <option value="sick">Sakit</option>
                            <option value="excused">Izin</option>
                            <option value="absent">Alpa</option>
                        </select>
                    </div>

                    <div style="display: flex; gap: 10px;">
                        <button class="btn btn-primary" onclick="openManualModal()">
                            <i class='bx bx-edit-alt'></i> Input Presensi Manual (Guru)
                        </button>
                        <button class="btn btn-outline" onclick="loadAttendancesTable()">
                            <i class='bx bx-refresh'></i> Refresh
                        </button>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Siswa</th>
                                <th>Kelas</th>
                                <th>Jam Masuk</th>
                                <th>Jam Pulang</th>
                                <th>Status</th>
                                <th>Metode</th>
                                <th>Jarak GPS</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="attendanceTableBody">
                            <tr>
                                <td colspan="8" style="text-align: center; color: var(--text-muted); padding: 30px;">Memuat data presensi...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- TAB 3: PETA SEBARAN GPS LIVE               -->
        <!-- ========================================== -->
        <div id="map-tab" class="tab-content">
            <div class="card">
                <div class="card-header">
                    <div>
                        <h2 class="card-title">
                            <i class='bx bx-map-pin'></i> Peta Sebaran Lokasi Presensi Siswa & Geofence
                        </h2>
                        <p style="font-size: 13px; color: var(--text-muted); margin-top: 4px;">
                            Lingkaran biru menampilkan batas radius geofencing sekolah. Titik pin menandai lokasi presensi siswa hari ini.
                        </p>
                    </div>
                    <button class="btn btn-outline" onclick="loadMapData()">
                        <i class='bx bx-refresh'></i> Refresh Peta
                    </button>
                </div>

                <div id="attendanceMap"></div>
            </div>
        </div>

        @if($canManageGeofence)
        <!-- ========================================== -->
        <!-- TAB 4: KONFIGURASI GEOFENCING              -->
        <!-- ========================================== -->
        <div id="geofence-tab" class="tab-content">
            <div class="selfie-layout">
                <div class="card">
                    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <h2 class="card-title">
                                <i class='bx bx-target-lock'></i> <span id="geofenceFormTitle">Tambah Titik Geofence</span>
                            </h2>
                            <div style="font-size: 11px; color: var(--text-muted); margin-top: 3px;">
                                Satuan Pendidikan: <strong>{{ $school->name }}</strong>
                                @if($isSuperAdmin)
                                    <span class="brand-badge" style="margin-left: 4px; background: rgba(245, 158, 11, 0.15); color: #d97706; border: 1px solid rgba(245, 158, 11, 0.3);">Super Admin (Semua Sekolah)</span>
                                @elseif($isSchoolAdmin)
                                    <span class="brand-badge" style="margin-left: 4px; background: rgba(70, 128, 255, 0.12); color: var(--bento-primary); border: 1px solid rgba(70, 128, 255, 0.25);">Enrolled Admin</span>
                                @endif
                            </div>
                        </div>
                        <span id="geofenceEditBadge" style="display: none; background: rgba(70, 128, 255, 0.12); color: var(--bento-primary); font-size: 11px; padding: 3px 8px; border-radius: 999px; font-weight: 700; border: 1px solid rgba(70, 128, 255, 0.25);">
                            <i class='bx bx-edit-alt'></i> Mode Edit
                        </span>
                    </div>

                    <form id="geofenceForm" onsubmit="saveGeofence(event)">
                        <input type="hidden" id="geoId" value="">
                        <input type="hidden" id="geoSchoolId" value="{{ $school->id }}">

                        <div class="form-group">
                            <label class="form-label">Nama Titik Lokasi:</label>
                            <input type="text" id="geoName" class="form-input" placeholder="Mis: Kampus Utama, Lapangan Barat" required>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                            <div class="form-group">
                                <label class="form-label">Latitude:</label>
                                <input type="number" step="any" id="geoLat" class="form-input" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Longitude:</label>
                                <input type="number" step="any" id="geoLng" class="form-input" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Radius (Meter):</label>
                            <input type="number" id="geoRadius" class="form-input" value="150" min="10" max="2000" required>
                            <small style="color: var(--text-muted); font-size: 12px;">Siswa hanya bisa presensi jika berada di dalam radius ini.</small>
                        </div>

                        <div class="form-group">
                            <label class="form-label">WiFi SSID Sekolah (Fallback Opsional):</label>
                            <input type="text" id="geoWifi" class="form-input" placeholder="Mis: SMAN1_WIFI">
                        </div>

                        <div class="form-group" style="margin-top: 10px;">
                            <label style="display: flex; align-items: center; gap: 8px; font-size: 13px; font-weight: 600; cursor: pointer; color: var(--text-heading);">
                                <input type="checkbox" id="geoIsPrimary" value="1">
                                <span>Jadikan sebagai Titik Utama (Pusat Kampus)</span>
                            </label>
                        </div>

                        <div style="display: flex; gap: 10px; margin-top: 20px;">
                            <button type="submit" id="btnSaveGeofence" class="btn btn-primary" style="flex: 1;">
                                <i class='bx bx-save'></i> Simpan Titik Geofence
                            </button>
                            <button type="button" id="btnCancelEditGeofence" onclick="cancelEditGeofence()" class="btn btn-outline" style="display: none;">
                                <i class='bx bx-x'></i> Batal
                            </button>
                        </div>
                    </form>
                </div>

                <div class="card">
                    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                        <h2 class="card-title">
                            <i class='bx bx-list-ul'></i> Daftar Titik Geofence Aktif
                        </h2>
                        <span style="font-size: 12px; color: var(--text-muted);">
                            Total: <strong>{{ count($geofences) }} Lokasi</strong>
                        </span>
                    </div>

                    <div class="table-responsive">
                        <table class="data-table" id="geofenceTable">
                            <thead>
                                <tr>
                                    <th>Nama Lokasi</th>
                                    <th>Koordinat</th>
                                    <th>Radius</th>
                                    <th>Status</th>
                                    <th style="text-align: center; width: 150px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="geofenceTableBody">
                                @forelse($geofences as $g)
                                    <tr id="geofence-row-{{ $g->id }}">
                                        <td>
                                            <strong>{{ $g->name }}</strong>
                                            @if($g->is_primary)
                                                <span class="brand-badge" style="margin-left: 6px;">Utama</span>
                                            @endif
                                            @if(!empty($g->wifi_ssid))
                                                <div style="font-size: 11px; color: var(--text-muted); margin-top: 2px;">
                                                    <i class='bx bx-wifi'></i> {{ $g->wifi_ssid }}
                                                </div>
                                            @endif
                                        </td>
                                        <td style="font-family: 'JetBrains Mono', monospace; font-size: 12px;">
                                            {{ number_format($g->latitude, 6) }}, {{ number_format($g->longitude, 6) }}
                                        </td>
                                        <td style="font-family: 'JetBrains Mono', monospace;"><strong>{{ $g->radius_meters }}m</strong></td>
                                        <td>
                                            @if($g->is_active)
                                                <span class="status-badge present"><i class='bx bx-check'></i> Aktif</span>
                                            @else
                                                <span class="status-badge absent"><i class='bx bx-x'></i> Nonaktif</span>
                                            @endif
                                        </td>
                                        <td style="text-align: center; white-space: nowrap;">
                                            <div style="display: inline-flex; gap: 6px;">
                                                <button type="button" 
                                                    class="btn-action-edit" 
                                                    title="Edit Titik Geofence"
                                                    onclick='editGeofence(@json($g))'>
                                                    <i class='bx bx-edit'></i> Edit
                                                </button>
                                                <button type="button" 
                                                    class="btn-action-delete" 
                                                    title="Hapus Titik Geofence"
                                                    onclick="deleteGeofence({{ $g->id }}, '{{ addslashes($g->name) }}')">
                                                    <i class='bx bx-trash'></i> Hapus
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr id="geofenceEmptyRow">
                                        <td colspan="5" style="text-align: center; color: var(--text-muted); padding: 24px;">
                                            Belum ada titik geofence yang dikonfigurasi.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @endif
        @endif

    </main>

    @if(!$isStudent)
    <!-- MODAL: MANUAL ATTENDANCE -->
    <div id="manualModal" class="modal-overlay">
        <div class="modal-box">
            <div class="modal-header">
                <h3><i class='bx bx-edit-alt'></i> Input Presensi Manual (Guru/Admin)</h3>
                <button class="modal-close" onclick="closeManualModal()">&times;</button>
            </div>
            <form onsubmit="submitManualAttendance(event)">
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">Pilih Siswa:</label>
                        <select id="manualStudentId" class="form-select" required>
                            @foreach($students as $s)
                                <option value="{{ $s->id }}">{{ $s->full_name }} ({{ $s->schoolClass?->name }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Tanggal Presensi:</label>
                        <input type="date" id="manualDate" class="form-input" value="{{ $todayIso }}" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Status Kehadiran:</label>
                        <select id="manualStatus" class="form-select" required>
                            <option value="present">Hadir (Present)</option>
                            <option value="late">Terlambat (Late)</option>
                            <option value="sick">Sakit (Sick)</option>
                            <option value="excused">Izin (Excused)</option>
                            <option value="absent">Alpa / Tanpa Keterangan (Absent)</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Catatan / Alasan:</label>
                        <textarea id="manualNotes" class="form-textarea" rows="3" placeholder="Misal: Surat dokter terlampir, izin dispensasi lomba, dll."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline" onclick="closeManualModal()">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class='bx bx-save'></i> Simpan Presensi</button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <!-- MODAL: VIEW SELFIE PHOTO -->
    <div id="photoModal" class="modal-overlay">
        <div class="modal-box" style="max-width: 460px;">
            <div class="modal-header">
                <h3 id="photoModalTitle"><i class='bx bx-image'></i> Foto Bukti Presensi</h3>
                <button class="modal-close" onclick="closePhotoModal()">&times;</button>
            </div>
            <div class="modal-body" style="text-align: center; padding: 18px;">
                <img id="photoModalImg" src="" alt="Selfie" style="max-width: 100%; border-radius: var(--radius-md); box-shadow: 0 4px 14px rgba(0,0,0,0.18); border: 1px solid var(--border-color);">
                <div id="photoModalMeta" style="margin-top: 14px; font-size: 13px; color: var(--text-muted);"></div>
            </div>
        </div>
    </div>

    <!-- TOAST NOTIFICATION CONTAINER -->
    <div id="toastContainer" class="toast-container"></div>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <script>
        // Global variables & state
        const SCHOOL_LAT = {{ $primaryGeofence->latitude ?? -6.16682700 }};
        const SCHOOL_LNG = {{ $primaryGeofence->longitude ?? 106.83756200 }};
        const SCHOOL_RADIUS = {{ $primaryGeofence->radius_meters ?? 150 }};
        const SCHOOL_ID = {{ $school->id ?? 1 }};
        const IS_STUDENT_USER = {{ $isStudent ? 'true' : 'false' }};
        const CURRENT_STUDENT_ID = {{ $currentStudent ? $currentStudent->id : 'null' }};

        let currentCoords = null;
        let isMockLocation = false;
        let webcamStream = null;
        let attendanceMap = null;
        let mapMarkersLayer = null;
        let geofenceCircle = null;
        let gpsWatchId = null;

        // Theme Switcher Helper
        function toggleApexTheme() {
            const isDark = document.documentElement.classList.toggle('dark');
            localStorage.setItem('apex_theme', isDark ? 'dark' : 'light');
            updateThemeIcon();
            if (attendanceMap) {
                attendanceMap.invalidateSize();
            }
        }

        function updateThemeIcon() {
            const icon = document.getElementById('themeIcon');
            if (!icon) return;
            if (document.documentElement.classList.contains('dark')) {
                icon.className = 'bx bx-sun';
            } else {
                icon.className = 'bx bx-moon';
            }
        }

        // On document ready
        document.addEventListener('DOMContentLoaded', () => {
            updateThemeIcon();
            initWebcam();
            
            // Mode Real: Otomatis minta & kunci GPS perangkat nyata saat halaman dibuka
            getRealGps(false);

            if (!IS_STUDENT_USER) {
                loadSummaryKPI();
                loadAttendancesTable();
            }
        });

        // Tab Switcher
        function switchTab(tabId) {
            document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));

            if (event && event.currentTarget) {
                event.currentTarget.classList.add('active');
            }
            const targetEl = document.getElementById(tabId);
            if (targetEl) {
                targetEl.classList.add('active');
            }

            if (tabId === 'map-tab') {
                setTimeout(initMap, 200);
            }
        }

        // Webcam initialization
        async function initWebcam() {
            const video = document.getElementById('webcamVideo');
            const statusText = document.getElementById('cameraStatusText');

            try {
                if (webcamStream) {
                    webcamStream.getTracks().forEach(track => track.stop());
                }

                webcamStream = await navigator.mediaDevices.getUserMedia({
                    video: { width: { ideal: 640 }, height: { ideal: 480 }, facingMode: 'user' },
                    audio: false
                });

                video.srcObject = webcamStream;
                statusText.innerText = "Kamera Aktif";
                showToast("Kamera aktif", "success");
            } catch (err) {
                console.warn("Webcam access error:", err);
                statusText.innerText = "Mode Simulasi Foto";
                showToast("Kamera tidak terdeteksi / izin dinonaktifkan. Mode simulasi foto otomatis aktif.", "error");
            }
        }

        // Take snapshot from video or create realistic placeholder
        function capturePhoto() {
            const video = document.getElementById('webcamVideo');
            const canvas = document.getElementById('snapshotCanvas');
            const ctx = canvas.getContext('2d');

            if (webcamStream && video.videoWidth > 0) {
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
                ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
                return canvas.toDataURL('image/jpeg', 0.85);
            } else {
                // Generate a realistic SVG/Canvas placeholder avatar
                canvas.width = 400;
                canvas.height = 400;
                ctx.fillStyle = "#4680ff";
                ctx.fillRect(0, 0, 400, 400);
                ctx.fillStyle = "#ffffff";
                ctx.font = "bold 24px 'Inter', sans-serif";
                ctx.textAlign = "center";
                ctx.fillText("SELFIE SIMULATION", 200, 180);
                ctx.font = "16px 'JetBrains Mono', monospace";
                ctx.fillText(new Date().toLocaleTimeString(), 200, 220);
                return canvas.toDataURL('image/jpeg', 0.85);
            }
        }

        // Haversine formula on client
        function calculateHaversine(lat1, lon1, lat2, lon2) {
            const R = 6371000;
            const dLat = (lat2 - lat1) * Math.PI / 180;
            const dLon = (lon2 - lon1) * Math.PI / 180;
            const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                      Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                      Math.sin(dLon / 2) * Math.sin(dLon / 2);
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
            return Math.round(R * c * 10) / 10;
        }

        // Distance & Geofence Indicator
        function updateDistanceDisplay() {
            const coordText = document.getElementById('currentCoordText');
            const distanceText = document.getElementById('currentDistanceText');
            const accuracyText = document.getElementById('currentAccuracyText');
            const alertBox = document.getElementById('geofenceAlert');
            const alertText = document.getElementById('geofenceAlertText');
            const faceGuide = document.getElementById('faceGuide');

            if (!currentCoords) {
                if (coordText) coordText.innerText = "Belum Terkunci";
                if (distanceText) distanceText.innerText = "-";
                if (accuracyText) accuracyText.innerText = "Mencari...";
                if (alertBox) {
                    alertBox.className = 'geofence-alert';
                    alertBox.style.background = 'var(--bg-hover)';
                    alertBox.style.color = 'var(--text-muted)';
                    alertBox.style.borderColor = 'var(--border-color)';
                }
                if (alertText) alertText.innerHTML = "<i class='bx bx-loader-alt bx-spin'></i> Menunggu sinyal GPS perangkat aktif...";
                if (faceGuide) faceGuide.classList.remove('in-range');
                return;
            }

            const dist = calculateHaversine(currentCoords.latitude, currentCoords.longitude, SCHOOL_LAT, SCHOOL_LNG);
            if (coordText) coordText.innerText = `${currentCoords.latitude.toFixed(6)}, ${currentCoords.longitude.toFixed(6)}`;
            if (distanceText) distanceText.innerText = `${dist} meter`;
            if (accuracyText) accuracyText.innerText = `± ${currentCoords.accuracy} m`;

            if (dist <= SCHOOL_RADIUS) {
                if (alertBox) {
                    alertBox.className = 'geofence-alert within';
                    alertBox.style.background = '';
                    alertBox.style.color = '';
                    alertBox.style.borderColor = '';
                }
                if (alertText) alertText.innerHTML = `<strong>Lokasi Terverifikasi</strong> (${dist}m &le; ${SCHOOL_RADIUS}m dari sekolah)`;
                if (faceGuide) faceGuide.classList.add('in-range');
            } else {
                if (alertBox) {
                    alertBox.className = 'geofence-alert outside';
                    alertBox.style.background = '';
                    alertBox.style.color = '';
                    alertBox.style.borderColor = '';
                }
                if (alertText) alertText.innerHTML = `<strong>Di Luar Radius Sekolah</strong> (${dist}m &gt; ${SCHOOL_RADIUS}m — Presensi ditolak)`;
                if (faceGuide) faceGuide.classList.remove('in-range');
            }
        }

        // Preset Locations (Khusus Pengujian Guru / Admin)
        function setPresetLocation(type) {
            document.querySelectorAll('.sim-buttons-group .btn-chip').forEach(btn => btn.classList.remove('active'));
            if (event && event.target) {
                event.target.classList.add('active');
            }

            if (type === 'school') {
                currentCoords = { latitude: -6.166840, longitude: 106.837580, accuracy: 8.0 };
                showToast("Simulasi diset: Di area sekolah (~24m)", "success");
            } else if (type === 'monas') {
                currentCoords = { latitude: -6.175392, longitude: 106.827153, accuracy: 12.0 };
                showToast("Simulasi diset: Monas (~1.8km, di luar radius)", "error");
            }
            updateDistanceDisplay();
        }

        // Real GPS: Mengambil posisi nyata dari sensor perangkat siswa
        function getRealGps(isManual = false) {
            const alertText = document.getElementById('geofenceAlertText');

            if (!navigator.geolocation) {
                showToast("Sensor Geolocation tidak didukung oleh browser Anda.", "error");
                if (alertText) alertText.innerHTML = "<i class='bx bx-x-circle'></i> Browser tidak mendukung Geolocation.";
                return;
            }

            if (isManual) {
                showToast("Mengunci sinyal satelit GPS perangkat Anda...", "info");
            }

            if (alertText && !currentCoords) {
                alertText.innerHTML = "<i class='bx bx-loader-alt bx-spin'></i> Mengunci sinyal GPS asli perangkat...";
            }

            navigator.geolocation.getCurrentPosition(
                (pos) => {
                    currentCoords = {
                        latitude: pos.coords.latitude,
                        longitude: pos.coords.longitude,
                        accuracy: Math.round(pos.coords.accuracy)
                    };
                    updateDistanceDisplay();
                    if (isManual) {
                        showToast("Sinyal GPS asli berhasil diperbarui secara akurat!", "success");
                    }
                },
                (err) => {
                    console.warn("Geolocation warning:", err.message);
                    let errMsg = "Gagal mengambil GPS: " + err.message;
                    if (err.code === 1) {
                        errMsg = "Izin akses lokasi ditolak. Silakan aktifkan izin lokasi di browser Anda.";
                    } else if (err.code === 2) {
                        errMsg = "Sinyal lokasi tidak tersedia. Pastikan fitur GPS di HP/Laptop menyala.";
                    } else if (err.code === 3) {
                        errMsg = "Batas waktu (timeout) pencarian GPS terlampaui.";
                    }

                    if (isManual) {
                        showToast(errMsg, "error");
                    }

                    // Jika gagal tapi bukan siswa (mode pengujian di browser desktop tanpa chip GPS), sediakan koordinat fallback
                    if (!IS_STUDENT_USER && !currentCoords) {
                        currentCoords = { latitude: -6.166840, longitude: 106.837580, accuracy: 10.0 };
                        updateDistanceDisplay();
                    } else if (!currentCoords) {
                        const alertBox = document.getElementById('geofenceAlert');
                        if (alertBox) {
                            alertBox.className = 'geofence-alert outside';
                            alertBox.style.background = '';
                            alertBox.style.color = '';
                            alertBox.style.borderColor = '';
                        }
                        if (alertText) {
                            alertText.innerHTML = `<i class='bx bx-error-circle'></i> ${errMsg}`;
                        }
                    }
                },
                { enableHighAccuracy: true, timeout: 12000, maximumAge: 0 }
            );
        }

        // Toggle Mock GPS (Khusus Pengujian Keamanan / Anti-Spoofing Admin)
        function toggleMockLocation() {
            isMockLocation = !isMockLocation;
            const btn = document.getElementById('btnToggleMock');
            if (isMockLocation) {
                btn.innerHTML = "<i class='bx bxs-shield-x'></i> Mock GPS: AKTIF (Uji Spoofing)";
                btn.style.background = "var(--vx-danger)";
                btn.style.color = "#ffffff";
                btn.style.borderColor = "var(--vx-danger)";
                showToast("Simulasi Mock Location diaktifkan untuk pengujian deteksi anti-kecurangan.", "error");
            } else {
                btn.innerHTML = "<i class='bx bx-shield'></i> Mock GPS: Nonaktif";
                btn.style.background = "var(--bg-surface)";
                btn.style.color = "var(--text-heading)";
                btn.style.borderColor = "var(--border-color)";
                showToast("Simulasi Mock Location dinonaktifkan.", "success");
            }
        }

        // Submit Selfie Attendance
        async function submitSelfieAttendance() {
            const studentId = IS_STUDENT_USER ? CURRENT_STUDENT_ID : (document.getElementById('studentSelect')?.value || null);
            if (!studentId) {
                showToast("Identitas akun siswa tidak valid.", "error");
                return;
            }

            // Validasi Sinyal GPS Terkunci
            if (!currentCoords || !currentCoords.latitude) {
                showToast("Sinyal GPS belum terkunci. Klik tombol 'Kalibrasi / Refresh GPS' dan pastikan izin lokasi diizinkan.", "error");
                return;
            }

            // Validasi Radius Geofence untuk Siswa
            const dist = calculateHaversine(currentCoords.latitude, currentCoords.longitude, SCHOOL_LAT, SCHOOL_LNG);
            if (IS_STUDENT_USER && dist > SCHOOL_RADIUS) {
                showToast(`Presensi ditolak: Anda berada ${dist} meter dari sekolah (Maksimal radius ${SCHOOL_RADIUS}m).`, "error");
                return;
            }

            const btn = document.getElementById('btnCheckIn');
            btn.disabled = true;
            btn.innerHTML = "<i class='bx bx-loader-alt bx-spin'></i> Memproses Presensi...";

            const photoBase64 = capturePhoto();

            const payload = {
                student_id: studentId,
                selfie_image: photoBase64,
                latitude: currentCoords.latitude,
                longitude: currentCoords.longitude,
                gps_accuracy: currentCoords.accuracy,
                is_mock_location: isMockLocation,
                device_info: navigator.userAgent
            };

            try {
                const res = await fetch('/api/v1/attendances/selfie', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(payload)
                });

                const data = await res.json();

                if (data.success) {
                    showToast(data.message, "success");
                    displayResult(data.data, true);
                    if (!IS_STUDENT_USER) {
                        loadSummaryKPI();
                        loadAttendancesTable();
                    } else {
                        // Refresh page setelah 1.2 detik untuk memperbarui status dan kartu siswa
                        setTimeout(() => location.reload(), 1200);
                    }
                } else {
                    const msg = data.error?.message || data.message || "Presensi gagal.";
                    showToast(msg, "error");
                    displayResult(data.error || { message: msg }, false);
                }
            } catch (err) {
                showToast("Terjadi kesalahan koneksi ke server", "error");
            } finally {
                btn.disabled = false;
                btn.innerHTML = "<i class='bx bx-camera'></i> Ambil Foto & Presensi Masuk";
            }
        }

        // Submit Check-Out
        async function submitCheckOut() {
            const studentId = IS_STUDENT_USER ? CURRENT_STUDENT_ID : (document.getElementById('studentSelect')?.value || null);
            if (!studentId) {
                showToast("Identitas siswa tidak ditemukan.", "error");
                return;
            }

            try {
                const res = await fetch('/api/v1/attendances/checkout', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify({ student_id: studentId })
                });

                const data = await res.json();
                if (data.success) {
                    showToast(data.message, "success");
                    if (!IS_STUDENT_USER) {
                        loadAttendancesTable();
                    } else {
                        setTimeout(() => location.reload(), 1200);
                    }
                } else {
                    showToast(data.message || "Gagal presensi pulang", "error");
                }
            } catch (err) {
                showToast("Gagal menghubungi server", "error");
            }
        }

        function displayResult(res, isSuccess) {
            const panel = document.getElementById('attendanceResultPanel');
            const content = document.getElementById('attendanceResultContent');
            panel.style.display = 'block';

            if (isSuccess) {
                panel.style.background = 'var(--vx-success-light)';
                panel.style.borderColor = 'var(--vx-success-border)';
                content.innerHTML = `
                    <div style="display: flex; gap: 14px; align-items: center;">
                        ${res.selfie_url ? `<img src="${res.selfie_url}" style="width: 70px; height: 70px; border-radius: var(--radius-md); object-fit: cover; border: 1px solid var(--border-color);">` : ''}
                        <div>
                            <strong>${res.student_name}</strong> (${res.nis} • ${res.class_name})<br>
                            Jam Presensi: <strong>${res.check_in_at}</strong> • Status: <span class="status-badge ${res.status}">${res.status_label}</span><br>
                            Jarak dari Sekolah: <strong>${res.distance_meters} meter</strong>
                        </div>
                    </div>
                `;
            } else {
                panel.style.background = 'var(--vx-danger-light)';
                panel.style.borderColor = 'var(--vx-danger-border)';
                content.innerHTML = `
                    <div style="color: var(--vx-danger);">
                        <strong>Gagal: ${res.code || 'VALIDATION_FAILED'}</strong><br>
                        ${res.message || 'Presensi ditolak.'}
                    </div>
                `;
            }
        }

        // Summary KPI
        async function loadSummaryKPI() {
            try {
                const res = await fetch('/api/v1/attendances/today?school_id=' + SCHOOL_ID);
                const json = await res.json();
                if (json.success) {
                    const d = json.data;
                    document.getElementById('kpiTotal').innerText = d.total_students;
                    document.getElementById('kpiPresent').innerText = d.present;
                    document.getElementById('kpiLate').innerText = d.late;
                    document.getElementById('kpiSick').innerText = d.sick + d.excused;
                    document.getElementById('kpiAbsent').innerText = d.absent;
                }
            } catch (e) {
                console.error("KPI fetch error", e);
            }
        }

        // Load Table
        async function loadAttendancesTable() {
            const classId = document.getElementById('filterClass').value;
            const status = document.getElementById('filterStatus').value;
            const tbody = document.getElementById('attendanceTableBody');

            let url = `/api/v1/attendances?school_id=${SCHOOL_ID}&per_page=50`;
            if (classId) url += `&class_id=${classId}`;
            if (status) url += `&status=${status}`;

            try {
                const res = await fetch(url);
                const json = await res.json();
                if (json.success) {
                    const items = json.data;
                    if (items.length === 0) {
                        tbody.innerHTML = '<tr><td colspan="8" style="text-align: center; color: var(--text-muted); padding: 30px;">Belum ada data presensi untuk filter ini.</td></tr>';
                        return;
                    }

                    tbody.innerHTML = items.map(att => {
                        const s = att.student || {};
                        const cls = s.school_class?.name || '-';
                        const checkIn = att.check_in_at || '-';
                        const checkOut = att.check_out_at || '-';
                        const dist = att.distance_meters ? `${att.distance_meters}m` : '-';
                        const method = att.method === 'selfie' ? '🤳 Selfie GPS' : (att.method === 'qr_code' ? '📱 QR Code' : '✏️ Manual');
                        const statusBadge = `<span class="status-badge ${att.status}">${att.status}</span>`;

                        return `
                            <tr>
                                <td>
                                    <strong>${s.full_name || 'Siswa'}</strong>
                                    <div style="font-size: 12px; color: var(--text-muted); font-family: 'JetBrains Mono', monospace;">NIS: ${s.nis || '-'}</div>
                                </td>
                                <td>${cls}</td>
                                <td><strong style="font-family: 'JetBrains Mono', monospace;">${checkIn}</strong></td>
                                <td><span style="font-family: 'JetBrains Mono', monospace; color: var(--text-muted);">${checkOut}</span></td>
                                <td>${statusBadge}</td>
                                <td><span class="method-badge">${method}</span></td>
                                <td style="font-family: 'JetBrains Mono', monospace;">${dist}</td>
                                <td>
                                    ${att.selfie_url ? `
                                        <button class="btn btn-outline" style="padding: 4px 10px; font-size: 12px;" onclick="viewPhoto('${att.selfie_url}', '${s.full_name}', '${att.check_in_at}', '${dist}')">
                                            <i class='bx bx-show'></i> Lihat Selfie
                                        </button>
                                    ` : '<span style="color: var(--text-muted); font-size: 12px;">-</span>'}
                                </td>
                            </tr>
                        `;
                    }).join('');
                }
            } catch (e) {
                console.error("Table fetch error", e);
            }
        }

        // Map initialization
        function initMap() {
            if (attendanceMap) {
                attendanceMap.invalidateSize();
                loadMapData();
                return;
            }

            attendanceMap = L.map('attendanceMap').setView([SCHOOL_LAT, SCHOOL_LNG], 16);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '© OpenStreetMap contributors'
            }).addTo(attendanceMap);

            // School Marker
            const schoolIcon = L.divIcon({
                className: 'custom-school-icon',
                html: '<div style="background:var(--bento-primary); color:white; padding:7px 12px; border-radius:20px; font-weight:700; font-size:12px; box-shadow:0 4px 12px rgba(0,0,0,0.3); border:2px solid white; display:inline-flex; align-items:center; gap:5px;"><i class=\'bx bxs-school\'></i> SMAN 1 JAKARTA</div>',
                iconSize: [140, 36],
                iconAnchor: [70, 18]
            });

            L.marker([SCHOOL_LAT, SCHOOL_LNG], { icon: schoolIcon }).addTo(attendanceMap)
                .bindPopup("<b>Kampus Utama SMA Negeri 1 Jakarta</b><br>Titik Pusat Geofencing");

            // Geofence Circle
            geofenceCircle = L.circle([SCHOOL_LAT, SCHOOL_LNG], {
                color: '#4680ff',
                fillColor: '#4680ff',
                fillOpacity: 0.15,
                radius: SCHOOL_RADIUS
            }).addTo(attendanceMap);

            mapMarkersLayer = L.layerGroup().addTo(attendanceMap);
            loadMapData();
        }

        // Load attendances on map
        async function loadMapData() {
            if (!mapMarkersLayer) return;
            mapMarkersLayer.clearLayers();

            try {
                const res = await fetch('/api/v1/attendances/map?school_id=' + SCHOOL_ID);
                const json = await res.json();
                if (json.success) {
                    json.data.forEach(item => {
                        const isPresent = item.status === 'present';
                        const pinColor = isPresent ? '#10b981' : '#f59e0b';

                        const pinIcon = L.divIcon({
                            className: 'custom-pin',
                            html: `<div style="background:${pinColor}; width:16px; height:16px; border-radius:50%; border:2px solid white; box-shadow:0 2px 6px rgba(0,0,0,0.35);"></div>`,
                            iconSize: [16, 16],
                            iconAnchor: [8, 8]
                        });

                        const popupHtml = `
                            <div style="font-size:13px; line-height:1.4; font-family:'Inter', sans-serif;">
                                <strong>${item.student_name}</strong> (${item.class_name})<br>
                                Status: <b>${item.status}</b> • Jam: <b>${item.check_in_at}</b><br>
                                Jarak: <b>${item.distance_meters}m</b> dari sekolah<br>
                                ${item.selfie_url ? `<img src="${item.selfie_url}" style="width:100%; border-radius:8px; margin-top:6px; border:1px solid #e2e8f0;">` : ''}
                            </div>
                        `;

                        L.marker([item.latitude, item.longitude], { icon: pinIcon })
                            .addTo(mapMarkersLayer)
                            .bindPopup(popupHtml);
                    });
                }
            } catch (e) {
                console.error("Map load error", e);
            }
        }

        // Photo Modal
        function viewPhoto(url, name, time, dist) {
            document.getElementById('photoModalImg').src = url;
            document.getElementById('photoModalTitle').innerHTML = `<i class='bx bx-image'></i> Presensi: ${name}`;
            document.getElementById('photoModalMeta').innerHTML = `Jam Masuk: <strong style="font-family:'JetBrains Mono',monospace;">${time}</strong> • Jarak Geofence: <strong>${dist}</strong>`;
            document.getElementById('photoModal').classList.add('active');
        }

        function closePhotoModal() {
            document.getElementById('photoModal').classList.remove('active');
        }

        // Manual Modal
        function openManualModal() {
            document.getElementById('manualModal').classList.add('active');
        }

        function closeManualModal() {
            document.getElementById('manualModal').classList.remove('active');
        }

        async function submitManualAttendance(e) {
            e.preventDefault();
            const payload = {
                student_id: document.getElementById('manualStudentId').value,
                date: document.getElementById('manualDate').value,
                status: document.getElementById('manualStatus').value,
                notes: document.getElementById('manualNotes').value
            };

            try {
                const res = await fetch('/api/v1/attendances', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify(payload)
                });

                const data = await res.json();
                if (data.success) {
                    showToast("Presensi manual berhasil disimpan!", "success");
                    closeManualModal();
                    loadSummaryKPI();
                    loadAttendancesTable();
                } else {
                    showToast(data.message || "Gagal menyimpan presensi", "error");
                }
            } catch (err) {
                showToast("Terjadi kesalahan koneksi", "error");
            }
        }

        // Geofence Form: Save / Update
        async function saveGeofence(e) {
            e.preventDefault();
            const geoId = document.getElementById('geoId').value;
            const isEdit = !!geoId;
            const btnSave = document.getElementById('btnSaveGeofence');
            const originalBtnHtml = btnSave.innerHTML;
            btnSave.disabled = true;
            btnSave.innerHTML = "<i class='bx bx-loader-alt bx-spin'></i> Menyimpan...";

            const payload = {
                school_id: parseInt(document.getElementById('geoSchoolId').value) || SCHOOL_ID,
                name: document.getElementById('geoName').value.trim(),
                latitude: parseFloat(document.getElementById('geoLat').value),
                longitude: parseFloat(document.getElementById('geoLng').value),
                radius_meters: parseInt(document.getElementById('geoRadius').value),
                wifi_ssid: document.getElementById('geoWifi').value.trim() || null,
                is_primary: document.getElementById('geoIsPrimary') ? document.getElementById('geoIsPrimary').checked : false
            };

            const url = isEdit ? `/api/v1/geofences/${geoId}` : '/api/v1/geofences';
            const method = isEdit ? 'PUT' : 'POST';

            try {
                const res = await fetch(url, {
                    method: method,
                    headers: { 
                        'Content-Type': 'application/json', 
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    },
                    body: JSON.stringify(payload)
                });

                const data = await res.json();
                if (data.success) {
                    showToast(isEdit ? "Titik geofence berhasil diperbarui!" : "Titik geofence berhasil ditambahkan!", "success");
                    setTimeout(() => location.reload(), 800);
                } else {
                    showToast(data.message || (isEdit ? "Gagal memperbarui geofence" : "Gagal menyimpan geofence"), "error");
                    btnSave.disabled = false;
                    btnSave.innerHTML = originalBtnHtml;
                }
            } catch (err) {
                showToast("Terjadi kesalahan koneksi server", "error");
                btnSave.disabled = false;
                btnSave.innerHTML = originalBtnHtml;
            }
        }

        // Edit Geofence Fill
        function editGeofence(geo) {
            if (typeof geo === 'string') {
                try { geo = JSON.parse(geo); } catch(e) {}
            }
            if (!geo) return;

            document.getElementById('geoId').value = geo.id;
            document.getElementById('geoSchoolId').value = geo.school_id || SCHOOL_ID;
            document.getElementById('geoName').value = geo.name || '';
            document.getElementById('geoLat').value = geo.latitude || '';
            document.getElementById('geoLng').value = geo.longitude || '';
            document.getElementById('geoRadius').value = geo.radius_meters || 150;
            document.getElementById('geoWifi').value = geo.wifi_ssid || '';
            if (document.getElementById('geoIsPrimary')) {
                document.getElementById('geoIsPrimary').checked = !!geo.is_primary;
            }

            const titleEl = document.getElementById('geofenceFormTitle');
            if (titleEl) titleEl.textContent = 'Edit Titik Geofence: ' + (geo.name || '');
            const badgeEl = document.getElementById('geofenceEditBadge');
            if (badgeEl) badgeEl.style.display = 'inline-block';
            
            const btnSave = document.getElementById('btnSaveGeofence');
            if (btnSave) {
                btnSave.innerHTML = "<i class='bx bx-check'></i> Perbarui Titik Geofence";
                btnSave.style.background = '#10b981';
                btnSave.style.color = '#ffffff';
            }
            const btnCancel = document.getElementById('btnCancelEditGeofence');
            if (btnCancel) btnCancel.style.display = 'inline-flex';

            const formCard = document.getElementById('geofenceForm');
            if (formCard) {
                formCard.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
            showToast("Mode edit aktif: " + (geo.name || ''), "info");
        }

        // Cancel Edit
        function cancelEditGeofence() {
            document.getElementById('geofenceForm').reset();
            document.getElementById('geoId').value = '';
            document.getElementById('geoSchoolId').value = SCHOOL_ID;

            const titleEl = document.getElementById('geofenceFormTitle');
            if (titleEl) titleEl.textContent = 'Tambah Titik Geofence';
            const badgeEl = document.getElementById('geofenceEditBadge');
            if (badgeEl) badgeEl.style.display = 'none';

            const btnSave = document.getElementById('btnSaveGeofence');
            if (btnSave) {
                btnSave.innerHTML = "<i class='bx bx-save'></i> Simpan Titik Geofence";
                btnSave.style.background = '';
                btnSave.style.color = '';
            }
            const btnCancel = document.getElementById('btnCancelEditGeofence');
            if (btnCancel) btnCancel.style.display = 'none';
        }

        // Delete Geofence
        async function deleteGeofence(id, name) {
            if (!confirm(`Apakah Anda yakin ingin menghapus titik geofence "${name}"?\nSiswa tidak lagi dapat melakukan absensi pada koordinat radius ini.`)) {
                return;
            }

            try {
                const res = await fetch(`/api/v1/geofences/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    }
                });

                const data = await res.json();
                if (data.success) {
                    showToast(data.message || "Titik geofence berhasil dihapus!", "success");
                    const row = document.getElementById(`geofence-row-${id}`);
                    if (row) {
                        row.style.transition = 'all 0.3s ease';
                        row.style.opacity = '0';
                        row.style.background = 'rgba(239, 68, 68, 0.1)';
                        setTimeout(() => row.remove(), 300);
                    } else {
                        setTimeout(() => location.reload(), 800);
                    }
                    if (document.getElementById('geoId').value == id) {
                        cancelEditGeofence();
                    }
                } else {
                    showToast(data.message || "Gagal menghapus titik geofence", "error");
                }
            } catch (err) {
                showToast("Kesalahan server saat menghapus geofence", "error");
            }
        }

        // Toast Helper
        function showToast(message, type = 'info') {
            const container = document.getElementById('toastContainer');
            const toast = document.createElement('div');
            toast.className = `toast ${type}`;
            const iconClass = type === 'success' ? 'bx-check-circle' : (type === 'error' ? 'bx-x-circle' : 'bx-info-circle');
            toast.innerHTML = `
                <i class='bx ${iconClass}' style="font-size: 20px;"></i>
                <span>${message}</span>
            `;
            container.appendChild(toast);
            setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.transform = 'translateY(10px)';
                toast.style.transition = 'all 0.3s ease';
                setTimeout(() => toast.remove(), 300);
            }, 4000);
        }

        function onStudentChanged() {
            document.getElementById('attendanceResultPanel').style.display = 'none';
        }
    </script>
</body>
</html>
