<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $student->full_name }} — Profil Peserta Didik Dapodik | Sekolahku</title>

    <!-- Theme Detection (Apex Standard) -->
    <script>
        (function() {
            const savedTheme = localStorage.getItem('apex_theme') || 'system';
            if (savedTheme === 'dark' || (savedTheme === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        })();
    </script>

    <!-- Typography: Instrument Sans (Apex Standard) -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Boxicons -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>

    <style>
        :root {
            --primary: #4f46e5;
            --primary-dark: #4338ca;
            --primary-light: #eef2ff;
            --success: #10b981;
            --success-light: #d1fae5;
            --warning: #f59e0b;
            --warning-light: #fef3c7;
            --danger: #ef4444;
            --danger-light: #fee2e2;
            --info: #0284c7;
            --info-light: #e0f2fe;
            --slate-900: #0f172a;
            --slate-800: #1e293b;
            --slate-700: #334155;
            --slate-600: #475569;
            --slate-500: #64748b;
            --slate-400: #94a3b8;
            --slate-300: #cbd5e1;
            --slate-200: #e2e8f0;
            --slate-100: #f1f5f9;
            --slate-50: #f8fafc;
            --radius-xl: 20px;
            --radius-lg: 16px;
            --radius-md: 12px;
            --radius-sm: 8px;
            --card-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.05), 0 8px 10px -6px rgba(15, 23, 42, 0.02);
            --card-shadow-hover: 0 20px 25px -5px rgba(15, 23, 42, 0.08), 0 8px 10px -6px rgba(15, 23, 42, 0.04);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Instrument Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            letter-spacing: -0.011em;
        }

        body {
            background-color: #f8fafc;
            color: var(--slate-800);
            min-height: 100vh;
            line-height: 1.5;
            padding-bottom: 60px;
        }

        /* Top Navigation Bar */
        .topbar {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--slate-200);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .container {
            max-width: 1240px;
            margin: 0 auto;
            padding: 0 24px;
        }

        .topbar-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 70px;
        }

        .brand-section {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            color: inherit;
        }

        .brand-logo {
            width: 42px;
            height: 42px;
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 22px;
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.25);
        }

        .brand-title h1 {
            font-size: 18px;
            font-weight: 800;
            color: var(--slate-900);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .brand-title p {
            font-size: 12px;
            color: var(--slate-500);
        }

        .topbar-actions {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .btn-link-nav {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 14px;
            border-radius: var(--radius-sm);
            text-decoration: none;
            font-size: 13px;
            font-weight: 600;
            color: var(--slate-700);
            background: var(--slate-100);
            transition: all 0.2s;
        }

        .btn-link-nav:hover {
            background: var(--slate-200);
            color: var(--slate-900);
        }

        /* Hero / Profile Header Banner */
        .profile-hero {
            background: linear-gradient(135deg, #312e81 0%, #4338ca 50%, #4f46e5 100%);
            color: white;
            border-radius: var(--radius-xl);
            margin: 24px 0 20px 0;
            padding: 32px 36px;
            box-shadow: 0 16px 32px -8px rgba(79, 70, 229, 0.35);
            position: relative;
            overflow: hidden;
        }

        .profile-hero::after {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(255,255,255,0.12) 0%, rgba(255,255,255,0) 70%);
            border-radius: 50%;
            pointer-events: none;
        }

        .hero-layout {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 20px;
            position: relative;
            z-index: 1;
        }

        .hero-identity {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .hero-avatar-mini {
            width: 72px;
            height: 96px; /* 4:6 aspect ratio preview */
            border-radius: 12px;
            object-fit: cover;
            border: 3px solid rgba(255, 255, 255, 0.4);
            box-shadow: 0 8px 16px rgba(0,0,0,0.25);
            background: #475569;
        }

        .hero-name-wrap h2 {
            font-size: 24px;
            font-weight: 800;
            letter-spacing: -0.5px;
            margin-bottom: 4px;
        }

        .hero-meta-chips {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 8px;
            font-size: 13px;
        }

        .chip-pill {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 4px 10px;
            border-radius: 20px;
            background: rgba(255, 255, 255, 0.18);
            backdrop-filter: blur(8px);
            font-weight: 600;
        }

        /* Notification / Alert Bar */
        .window-banner {
            border-radius: var(--radius-md);
            padding: 14px 18px;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 13.5px;
            font-weight: 600;
            animation: fadeIn 0.3s ease;
        }

        .window-open {
            background: var(--success-light);
            color: #065f46;
            border: 1px solid #a7f3d0;
        }

        .window-closed {
            background: var(--warning-light);
            color: #92400e;
            border: 1px solid #fde68a;
        }

        .alert-success-flash {
            background: #ecfdf5;
            color: #065f46;
            border: 1px solid #a7f3d0;
            border-radius: var(--radius-md);
            padding: 14px 18px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 600;
        }

        .alert-error-flash {
            background: #fef2f2;
            color: #991b1b;
            border: 1px solid #fecaca;
            border-radius: var(--radius-md);
            padding: 14px 18px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 600;
        }

        /* Two-Column Grid */
        .profile-grid {
            display: grid;
            grid-template-columns: 320px 1fr;
            gap: 24px;
            align-items: start;
        }

        @media (max-width: 960px) {
            .profile-grid {
                grid-template-columns: 1fr;
            }
        }

        /* Cards */
        .card {
            background: white;
            border-radius: var(--radius-lg);
            border: 1px solid var(--slate-200);
            box-shadow: var(--card-shadow);
            overflow: hidden;
            margin-bottom: 24px;
        }

        .card-header {
            padding: 18px 22px;
            border-bottom: 1px solid var(--slate-200);
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: white;
        }

        .card-header h3 {
            font-size: 16px;
            font-weight: 700;
            color: var(--slate-900);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .card-body {
            padding: 22px;
        }

        /* Pasfoto 4x6 Section */
        .pasfoto-wrapper {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            padding: 10px 0;
        }

        .pasfoto-frame-4x6 {
            width: 160px;
            height: 240px; /* Standar 4:6 aspect ratio (rasio 2 banding 3) */
            border-radius: 12px;
            background: linear-gradient(135deg, #e2e8f0 0%, #cbd5e1 100%);
            border: 4px solid white;
            box-shadow: 0 12px 24px -4px rgba(15, 23, 42, 0.15), 0 0 0 1px rgba(0,0,0,0.06);
            overflow: hidden;
            position: relative;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .pasfoto-frame-4x6 img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .pasfoto-badge-size {
            position: absolute;
            bottom: 8px;
            right: 8px;
            background: rgba(15, 23, 42, 0.85);
            backdrop-filter: blur(4px);
            color: white;
            font-size: 10.5px;
            font-weight: 800;
            padding: 2px 8px;
            border-radius: 6px;
            letter-spacing: 0.5px;
        }

        .btn-upload-photo {
            position: relative;
            overflow: hidden;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: var(--primary);
            color: white;
            padding: 8px 16px;
            border-radius: var(--radius-sm);
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s;
            box-shadow: 0 4px 10px rgba(79, 70, 229, 0.25);
        }

        .btn-upload-photo:hover {
            background: var(--primary-dark);
        }

        .btn-upload-photo input[type="file"] {
            position: absolute;
            left: 0;
            top: 0;
            opacity: 0;
            width: 100%;
            height: 100%;
            cursor: pointer;
        }

        .pasfoto-rules {
            margin-top: 12px;
            font-size: 11.5px;
            color: var(--slate-500);
            line-height: 1.5;
            text-align: center;
            max-width: 240px;
        }

        /* Form Controls */
        .form-grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px 20px;
        }

        @media (max-width: 640px) {
            .form-grid-2 {
                grid-template-columns: 1fr;
            }
        }

        .form-row {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .form-row.span-2 {
            grid-column: 1 / -1;
        }

        .form-label {
            font-size: 13px;
            font-weight: 600;
            color: var(--slate-700);
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .form-label .req {
            color: var(--danger);
        }

        .form-input, .form-select, .form-textarea {
            width: 100%;
            padding: 10px 14px;
            border: 1.5px solid var(--slate-300);
            border-radius: var(--radius-sm);
            font-size: 13.5px;
            color: var(--slate-900);
            background: white;
            transition: all 0.2s ease;
        }

        .form-input:focus, .form-select:focus, .form-textarea:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.12);
        }

        .form-input:disabled, .form-select:disabled, .form-textarea:disabled {
            background: var(--slate-100);
            color: var(--slate-500);
            cursor: not-allowed;
            border-color: var(--slate-200);
        }

        /* GPS Button & Coordinates */
        .gps-action-bar {
            background: var(--info-light);
            border: 1px solid #bae6fd;
            border-radius: var(--radius-md);
            padding: 14px 18px;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
        }

        .btn-gps-auto {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 18px;
            background: #0284c7;
            color: white;
            border: none;
            border-radius: var(--radius-sm);
            font-size: 13.5px;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(2, 132, 199, 0.3);
            transition: all 0.2s ease;
        }

        .btn-gps-auto:hover {
            background: #0369a1;
            transform: translateY(-1px);
        }

        .btn-gps-auto:disabled {
            background: var(--slate-400);
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        /* Map Container */
        #homeMapPreview {
            width: 100%;
            height: 250px;
            border-radius: var(--radius-md);
            border: 1px solid var(--slate-300);
            margin-top: 12px;
            z-index: 10;
        }

        /* Submit Button Bar */
        .submit-bar {
            position: sticky;
            bottom: 16px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(12px);
            border: 1px solid var(--slate-200);
            border-radius: var(--radius-lg);
            padding: 16px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 16px 32px rgba(15, 23, 42, 0.12);
            z-index: 100;
        }

        .btn-save-primary {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: linear-gradient(135deg, #4f46e5 0%, #4338ca 100%);
            color: white;
            border: none;
            padding: 12px 28px;
            border-radius: var(--radius-sm);
            font-size: 14.5px;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 6px 16px rgba(79, 70, 229, 0.35);
            transition: all 0.2s;
        }

        .btn-save-primary:hover {
            box-shadow: 0 8px 20px rgba(79, 70, 229, 0.45);
            transform: translateY(-1px);
        }

        .btn-save-primary:disabled {
            background: var(--slate-400);
            cursor: not-allowed;
            box-shadow: none;
            transform: none;
        }
    </style>
</head>
<body>

    <!-- TOPBAR -->
    <header class="topbar">
        <div class="container">
            <div class="topbar-content">
                <a href="{{ route('home') }}" class="brand-section">
                    <div class="brand-logo">
                        <i class='bx bxs-school'></i>
                    </div>
                    <div class="brand-title">
                        <h1>Sekolahku <span style="font-size: 11px; font-weight: 700; padding: 2px 8px; background: var(--primary-light); color: var(--primary); border-radius: 20px;">Dapodik Siswa</span></h1>
                        <p>{{ $school->name ?? 'SMA Negeri 1 Jakarta' }}</p>
                    </div>
                </a>

                <div class="topbar-actions">
                    <a href="{{ route('student.dashboard') }}" class="btn-link-nav" style="background: var(--primary); color: white;">
                        <i class='bx bx-grid-alt'></i> Dashboard Siswa
                    </a>
                    <a href="{{ route('student.schedules') }}" class="btn-link-nav">
                        <i class='bx bx-calendar-event'></i> Jadwal Saya
                    </a>
                    <a href="{{ route('attendance.portal') }}" class="btn-link-nav">
                        <i class='bx bx-camera'></i> Presensi Selfie
                    </a>
                    @if(Auth::user() && in_array(Auth::user()->role, ['super_admin', 'school_admin', 'waka_kurikulum', 'waka_kesiswaan']))
                        <a href="{{ route('admin.data-master') }}" class="btn-link-nav" style="background: var(--primary); color: white;">
                            <i class='bx bx-slider-alt'></i> Panel Admin
                        </a>
                    @endif
                    <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn-link-nav" style="background: none; border: none; cursor: pointer; color: var(--danger);">
                            <i class='bx bx-log-out'></i> Keluar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    <div class="container">

        <!-- PROFILE HERO HEADER -->
        <div class="profile-hero">
            <div class="hero-layout">
                <div class="hero-identity">
                    <img src="{{ $student->photo_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($student->full_name) . '&background=4338ca&color=fff&size=180' }}"
                         alt="{{ $student->full_name }}" class="hero-avatar-mini" id="heroPhotoPreview">
                    <div class="hero-name-wrap">
                        <h2>{{ $student->full_name }}</h2>
                        <div class="hero-meta-chips">
                            <span class="chip-pill"><i class='bx bx-id-card'></i> NIS: {{ $student->nis }}</span>
                            <span class="chip-pill"><i class='bx bx-barcode'></i> NISN: {{ $student->nisn ?? '—' }}</span>
                            <span class="chip-pill"><i class='bx bxs-graduation'></i> {{ $student->schoolClass?->name ?? 'Belum ada rombel' }}</span>
                            <span class="chip-pill"><i class='bx bx-calendar'></i> Angkatan {{ $student->entryAcademicYear?->name ?? '—' }}</span>
                        </div>
                    </div>
                </div>

                <div>
                    <span class="chip-pill" style="font-size: 13px; padding: 6px 14px; background: rgba(255,255,255,0.25);">
                        <i class='bx bxs-check-shield'></i> Status: {{ strtoupper($student->status) }}
                    </span>
                </div>
            </div>
        </div>

        <!-- NOTIFICATION / STATUS PERIODE EDIT BIODATA -->
        @if(session('success'))
            <div class="alert-success-flash">
                <i class='bx bxs-check-circle' style="font-size: 20px;"></i>
                <div>{{ session('success') }}</div>
            </div>
        @endif

        @if(session('error'))
            <div class="alert-error-flash">
                <i class='bx bxs-error-circle' style="font-size: 20px;"></i>
                <div>{{ session('error') }}</div>
            </div>
        @endif

        @if($isWindowOpen)
            <div class="window-banner window-open">
                <div style="display:flex; align-items:center; gap:8px;">
                    <i class='bx bxs-lock-open-alt' style="font-size: 18px;"></i>
                    <span><strong>Periode Pemutakhiran Dibuka:</strong> Anda diizinkan melengkapi dan memvalidasi biodata profil Dapodik secara mandiri
                    @if($school->student_profile_edit_end)
                        sampai dengan <strong>{{ $school->student_profile_edit_end->translatedFormat('d F Y') }}</strong>
                    @endif.
                    </span>
                </div>
                <span class="chip-pill" style="background:#10b981; color:white;">Status: Terbuka</span>
            </div>
        @else
            <div class="window-banner window-closed">
                <div style="display:flex; align-items:center; gap:8px;">
                    <i class='bx bxs-lock-alt' style="font-size: 18px;"></i>
                    <span><strong>Periode Pemutakhiran Ditutup:</strong> Pengubahan biodata saat ini dinonaktifkan oleh pihak sekolah. Anda hanya dapat melihat data profil Anda.
                    @if($isStaff)
                        <em>(Anda sedang mengakses sebagai staf/admin dengan hak koreksi).</em>
                    @endif
                    </span>
                </div>
                <span class="chip-pill" style="background:#f59e0b; color:white;">Status: Ditutup</span>
            </div>
        @endif

        <!-- MAIN FORM -->
        <form action="{{ route('student.profile.update') }}" method="POST" enctype="multipart/form-data" id="formStudentProfile">
            @csrf
            @if($isStaff && request()->query('student_id'))
                <input type="hidden" name="student_id" value="{{ $student->id }}">
            @endif

            <div class="profile-grid">

                <!-- LEFT SIDEBAR: PASFOTO 4x6 & RINGKASAN AKADEMIK -->
                <div class="sidebar-col">

                    <!-- PASFOTO 4x6 CARD -->
                    <div class="card">
                        <div class="card-header">
                            <h3><i class='bx bx-image-alt'></i> Pasfoto Resmi 4 x 6 cm</h3>
                        </div>
                        <div class="card-body">
                            <div class="pasfoto-wrapper">
                                <div class="pasfoto-frame-4x6">
                                    <img src="{{ $student->photo_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($student->full_name) . '&background=4338ca&color=fff&size=240' }}"
                                         alt="Pasfoto {{ $student->full_name }}" id="pasfotoPreview">
                                    <span class="pasfoto-badge-size">4 x 6 cm</span>
                                </div>

                                @if($canEdit)
                                    <label class="btn-upload-photo" title="Pilih file pasfoto ukuran 4x6">
                                        <i class='bx bx-cloud-upload'></i> Ganti Pasfoto 4 x 6
                                        <input type="file" name="photo" id="photoInput" accept="image/jpeg,image/png,image/webp" onchange="previewPasfoto(event)">
                                    </label>
                                    <p class="pasfoto-rules">
                                        Rasio 2:3 (Ukuran standar 4x6 cm). Latar belakang merah atau biru, berseragam sekolah rapi. Format JPG/PNG maks. 2 MB.
                                    </p>
                                @else
                                    <span style="font-size: 12px; color: var(--slate-500);"><i class='bx bx-lock-alt'></i> Foto terkunci</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- RINGKASAN AKADEMIK & ROMBEL -->
                    <div class="card">
                        <div class="card-header">
                            <h3><i class='bx bx-bookmark-alt'></i> Identitas Akademik</h3>
                        </div>
                        <div class="card-body" style="font-size: 13px; line-height: 1.8;">
                            <div><span style="color:var(--slate-500);">Rombongan Belajar:</span><br><strong>{{ $student->schoolClass?->name ?? 'Belum Ditentukan' }}</strong></div>
                            <div style="margin-top: 8px;"><span style="color:var(--slate-500);">Tingkat / Fase:</span><br><strong>Tingkat {{ $student->schoolClass?->grade_level ?? $student->entry_grade_level ?? 10 }}</strong></div>
                            <div style="margin-top: 8px;"><span style="color:var(--slate-500);">Tahun Ajaran Masuk (Angkatan):</span><br><strong>{{ $student->entryAcademicYear?->name ?? '—' }}</strong></div>
                            <div style="margin-top: 8px;"><span style="color:var(--slate-500);">Jenis Pendaftaran:</span><br><strong style="text-transform: capitalize;">{{ $student->registration_type ?: 'Siswa Baru' }}</strong></div>
                            <div style="margin-top: 8px;"><span style="color:var(--slate-500);">Tanggal Terdaftar:</span><br><strong>{{ $student->enrollment_date ? $student->enrollment_date->translatedFormat('d F Y') : '—' }}</strong></div>
                        </div>
                    </div>

                </div>

                <!-- RIGHT CONTENT: TABS / SECTIONS -->
                <div class="main-col">

                    <!-- SEKSI 1: IDENTITAS POKOK SISWA -->
                    <div class="card">
                        <div class="card-header">
                            <h3><i class='bx bx-user'></i> 1. Identitas Pokok Peserta Didik (Dapodik)</h3>
                        </div>
                        <div class="card-body">
                            <div class="form-grid-2">
                                <div class="form-row span-2">
                                    <label class="form-label">Nama Lengkap (Sesuai Akte Lahir / Ijazah) <span class="req">*</span></label>
                                    <input type="text" class="form-input" name="full_name" value="{{ old('full_name', $student->full_name) }}" {{ !$canEdit ? 'disabled' : '' }} required>
                                </div>
                                <div class="form-row">
                                    <label class="form-label">NIS (Nomor Induk Sekolah)</label>
                                    <input type="text" class="form-input" value="{{ $student->nis }}" disabled title="NIS dikelola oleh tata usaha sekolah">
                                </div>
                                <div class="form-row">
                                    <label class="form-label">NISN (10 Digit Resmi Kemdikbud)</label>
                                    <input type="text" class="form-input" value="{{ $student->nisn ?? '—' }}" disabled title="NISN resmi Pusdatin Kemendikbud">
                                </div>
                                <div class="form-row">
                                    <label class="form-label">NIK (Nomor Induk Kependudukan - 16 Digit)</label>
                                    <input type="text" class="form-input" name="nik" maxlength="16" placeholder="16 digit NIK Dukcapil" value="{{ old('nik', $student->nik) }}" {{ !$canEdit ? 'disabled' : '' }}>
                                </div>
                                <div class="form-row">
                                    <label class="form-label">Nomor Kartu Keluarga (No. KK - 16 Digit)</label>
                                    <input type="text" class="form-input" name="no_kk" maxlength="16" placeholder="16 digit No. KK" value="{{ old('no_kk', $student->no_kk) }}" {{ !$canEdit ? 'disabled' : '' }}>
                                </div>
                                <div class="form-row">
                                    <label class="form-label">No. Registrasi Akta Lahir</label>
                                    <input type="text" class="form-input" name="birth_certificate_no" placeholder="Contoh: 12345/DIS/2010" value="{{ old('birth_certificate_no', $student->birth_certificate_no) }}" {{ !$canEdit ? 'disabled' : '' }}>
                                </div>
                                <div class="form-row">
                                    <label class="form-label">Jenis Kelamin <span class="req">*</span></label>
                                    <select class="form-select" name="gender" {{ !$canEdit ? 'disabled' : '' }} required>
                                        <option value="L" {{ old('gender', $student->gender) === 'L' ? 'selected' : '' }}>Laki-laki</option>
                                        <option value="P" {{ old('gender', $student->gender) === 'P' ? 'selected' : '' }}>Perempuan</option>
                                    </select>
                                </div>
                                <div class="form-row">
                                    <label class="form-label">Tempat Lahir</label>
                                    <input type="text" class="form-input" name="birth_place" placeholder="Kota / Kabupaten Kelahiran" value="{{ old('birth_place', $student->birth_place) }}" {{ !$canEdit ? 'disabled' : '' }}>
                                </div>
                                <div class="form-row">
                                    <label class="form-label">Tanggal Lahir <span class="req">*</span></label>
                                    <input type="date" class="form-input" name="birth_date" value="{{ old('birth_date', $student->birth_date?->toDateString()) }}" {{ !$canEdit ? 'disabled' : '' }} required>
                                </div>
                                <div class="form-row">
                                    <label class="form-label">Agama & Kepercayaan</label>
                                    <select class="form-select" name="religion" {{ !$canEdit ? 'disabled' : '' }}>
                                        @foreach(['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Khonghucu'] as $rel)
                                            <option value="{{ $rel }}" {{ old('religion', $student->religion) === $rel ? 'selected' : '' }}>{{ $rel }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-row">
                                    <label class="form-label">Kebutuhan Khusus (Disabilitas)</label>
                                    <input type="text" class="form-input" name="special_needs" placeholder="Tidak Ada" value="{{ old('special_needs', $student->special_needs ?? 'Tidak Ada') }}" {{ !$canEdit ? 'disabled' : '' }}>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SEKSI 2: KONTAK & ORANG TUA / WALI (DIGABUNG) -->
                    <div class="card">
                        <div class="card-header">
                            <h3><i class='bx bx-phone-call'></i> 2. Kontak Siswa & Orang Tua / Wali</h3>
                        </div>
                        <div class="card-body">
                            <div class="form-grid-2">
                                <div class="form-row">
                                    <label class="form-label">Nomor HP / WhatsApp Peserta Didik</label>
                                    <input type="text" class="form-input" name="student_phone" placeholder="08xxxxxxxxxx" value="{{ old('student_phone', $student->student_phone) }}" {{ !$canEdit ? 'disabled' : '' }}>
                                </div>
                                <div class="form-row">
                                    <label class="form-label">Nama Orang Tua / Wali Murid</label>
                                    <input type="text" class="form-input" name="parent_name" placeholder="Nama Orang Tua / Wali yang serumah" value="{{ old('parent_name', $student->parent_name) }}" {{ !$canEdit ? 'disabled' : '' }}>
                                </div>
                                <div class="form-row">
                                    <label class="form-label">Nomor Telepon / WhatsApp Orang Tua / Wali</label>
                                    <input type="text" class="form-input" name="parent_phone" placeholder="08xxxxxxxxxx" value="{{ old('parent_phone', $student->parent_phone) }}" {{ !$canEdit ? 'disabled' : '' }}>
                                </div>
                                <div class="form-row">
                                    <label class="form-label">Anak Ke- (Dalam Keluarga)</label>
                                    <input type="number" class="form-input" name="child_order" min="1" max="20" value="{{ old('child_order', $student->child_order ?? 1) }}" {{ !$canEdit ? 'disabled' : '' }}>
                                </div>
                                <div class="form-row">
                                    <label class="form-label">Jumlah Saudara Kandung</label>
                                    <input type="number" class="form-input" name="siblings_count" min="0" max="20" value="{{ old('siblings_count', $student->siblings_count ?? 0) }}" {{ !$canEdit ? 'disabled' : '' }}>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SEKSI 3: DOMISILI & TITIK KOORDINAT RUMAH (AUTO-GPS) -->
                    <div class="card">
                        <div class="card-header">
                            <h3><i class='bx bx-map-pin'></i> 3. Domisili & Titik Koordinat Rumah (Geotagging)</h3>
                        </div>
                        <div class="card-body">

                            <!-- GPS Auto Detection Bar -->
                            <div class="gps-action-bar">
                                <div>
                                    <div style="font-weight: 700; color: #0369a1; display:flex; align-items:center; gap:6px;">
                                        <i class='bx bx-crosshair'></i> Pengambilan Titik Koordinat Rumah Otomatis
                                    </div>
                                    <div style="font-size: 12px; color: #0284c7; margin-top: 2px;">
                                        Klik tombol di samping saat berada di rumah untuk mendeteksi koordinat GPS secara otomatis dari perangkat Anda.
                                    </div>
                                </div>
                                <button type="button" class="btn-gps-auto" id="btnDetectGps" onclick="detectHomeCoordinates()" {{ !$canEdit ? 'disabled' : '' }}>
                                    <i class='bx bx-navigation'></i> Ambil Titik Rumah dari GPS Perangkat
                                </button>
                            </div>

                            <div class="form-grid-2">
                                <div class="form-row span-2">
                                    <label class="form-label">Alamat Lengkap Rumah (Jalan / Gang / Blok / No. Rumah)</label>
                                    <textarea class="form-textarea" name="address" rows="2" placeholder="Nama jalan, nomor rumah, nama kampung..." {{ !$canEdit ? 'disabled' : '' }}>{{ old('address', $student->address) }}</textarea>
                                </div>
                                <div class="form-row">
                                    <label class="form-label">RT / RW</label>
                                    <div style="display: flex; gap: 8px;">
                                        <input type="text" class="form-input" style="width: 80px;" name="rt" placeholder="RT" value="{{ old('rt', $student->rt) }}" {{ !$canEdit ? 'disabled' : '' }}>
                                        <input type="text" class="form-input" style="width: 80px;" name="rw" placeholder="RW" value="{{ old('rw', $student->rw) }}" {{ !$canEdit ? 'disabled' : '' }}>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <label class="form-label">Kelurahan / Desa</label>
                                    <input type="text" class="form-input" name="sub_district" placeholder="Kelurahan" value="{{ old('sub_district', $student->sub_district) }}" {{ !$canEdit ? 'disabled' : '' }}>
                                </div>
                                <div class="form-row">
                                    <label class="form-label">Kecamatan</label>
                                    <input type="text" class="form-input" name="district" placeholder="Kecamatan" value="{{ old('district', $student->district) }}" {{ !$canEdit ? 'disabled' : '' }}>
                                </div>
                                <div class="form-row">
                                    <label class="form-label">Kabupaten / Kota</label>
                                    <input type="text" class="form-input" name="city" placeholder="Kota / Kabupaten" value="{{ old('city', $student->city) }}" {{ !$canEdit ? 'disabled' : '' }}>
                                </div>
                                <div class="form-row">
                                    <label class="form-label">Provinsi</label>
                                    <input type="text" class="form-input" name="province" placeholder="Provinsi" value="{{ old('province', $student->province) }}" {{ !$canEdit ? 'disabled' : '' }}>
                                </div>
                                <div class="form-row">
                                    <label class="form-label">Kode Pos</label>
                                    <input type="text" class="form-input" name="postal_code" placeholder="Kode Pos" value="{{ old('postal_code', $student->postal_code) }}" {{ !$canEdit ? 'disabled' : '' }}>
                                </div>
                                <div class="form-row">
                                    <label class="form-label">Lintang Rumah (Latitude)</label>
                                    <input type="number" step="0.0000001" class="form-input" name="home_latitude" id="inputHomeLat" placeholder="-6.xxxxxxx" value="{{ old('home_latitude', $student->home_latitude) }}" onchange="updateMarkerFromInputs()" {{ !$canEdit ? 'disabled' : '' }}>
                                </div>
                                <div class="form-row">
                                    <label class="form-label">Bujur Rumah (Longitude)</label>
                                    <input type="number" step="0.0000001" class="form-input" name="home_longitude" id="inputHomeLng" placeholder="106.xxxxxxx" value="{{ old('home_longitude', $student->home_longitude) }}" onchange="updateMarkerFromInputs()" {{ !$canEdit ? 'disabled' : '' }}>
                                </div>
                                <div class="form-row">
                                    <label class="form-label">Tempat Tinggal</label>
                                    <select class="form-select" name="residence_type" {{ !$canEdit ? 'disabled' : '' }}>
                                        @foreach(['Bersama Orang Tua', 'Wali', 'Kos', 'Asrama', 'Panti Asuhan'] as $res)
                                            <option value="{{ $res }}" {{ old('residence_type', $student->residence_type) === $res ? 'selected' : '' }}>{{ $res }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-row">
                                    <label class="form-label">Moda Transportasi ke Sekolah</label>
                                    <select class="form-select" name="transportation_mode" {{ !$canEdit ? 'disabled' : '' }}>
                                        @foreach(['Kendaraan Pribadi', 'Jalan Kaki', 'Angkutan Umum', 'Jemputan Sekolah', 'Kereta/KRL', 'Ojek/Online'] as $trans)
                                            <option value="{{ $trans }}" {{ old('transportation_mode', $student->transportation_mode) === $trans ? 'selected' : '' }}>{{ $trans }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- Peta Interaktif Preview Rumah Siswa -->
                            <div style="margin-top: 16px;">
                                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 6px;">
                                    <span style="font-size: 13px; font-weight: 700; color: var(--slate-700);">
                                        <i class='bx bx-map'></i> Pratinjau Peta Titik Rumah:
                                    </span>
                                    <span id="calculatedDistanceBadge" class="chip-pill" style="background: var(--info-light); color: var(--info); font-size: 12px;">
                                        @if($calculatedDistance)
                                            Jarak ke Sekolah: ~{{ $calculatedDistance }} km
                                        @else
                                            Geser pin untuk sesuaikan titik atap rumah
                                        @endif
                                    </span>
                                </div>
                                <div id="homeMapPreview"></div>
                            </div>

                        </div>
                    </div>

                    <!-- SEKSI 4: SEKOLAH ASAL & DATA PERIODIK FISIK -->
                    <div class="card">
                        <div class="card-header">
                            <h3><i class='bx bx-buildings'></i> 4. Sekolah Sebelumnya & Data Fisik</h3>
                        </div>
                        <div class="card-body">
                            <div class="form-grid-2">
                                <div class="form-row span-2">
                                    <label class="form-label">Sekolah Asal / Sekolah Sebelumnya (SMP / MTs / Sekolah Pindahan)</label>
                                    <input type="text" class="form-input" name="previous_school" placeholder="Nama SMP/MTs asal atau nama sekolah sebelumnya" value="{{ old('previous_school', $student->previous_school) }}" {{ !$canEdit ? 'disabled' : '' }}>
                                </div>
                                <div class="form-row">
                                    <label class="form-label">Tinggi Badan (cm)</label>
                                    <input type="number" class="form-input" name="height_cm" min="50" max="250" placeholder="cm" value="{{ old('height_cm', $student->height_cm) }}" {{ !$canEdit ? 'disabled' : '' }}>
                                </div>
                                <div class="form-row">
                                    <label class="form-label">Berat Badan (kg)</label>
                                    <input type="number" class="form-input" name="weight_kg" min="15" max="250" placeholder="kg" value="{{ old('weight_kg', $student->weight_kg) }}" {{ !$canEdit ? 'disabled' : '' }}>
                                </div>
                                <div class="form-row">
                                    <label class="form-label">Lingkar Kepala (cm)</label>
                                    <input type="number" class="form-input" name="head_circumference_cm" min="30" max="100" placeholder="cm" value="{{ old('head_circumference_cm', $student->head_circumference_cm) }}" {{ !$canEdit ? 'disabled' : '' }}>
                                </div>
                                <div class="form-row">
                                    <label class="form-label">Golongan Darah</label>
                                    <select class="form-select" name="blood_type" {{ !$canEdit ? 'disabled' : '' }}>
                                        @foreach(['Tidak Tahu', 'A', 'B', 'AB', 'O'] as $bt)
                                            <option value="{{ $bt }}" {{ old('blood_type', $student->blood_type) === $bt ? 'selected' : '' }}>{{ $bt }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SEKSI 5: KESEJAHTERAAN & BANTUAN SOSIAL -->
                    <div class="card">
                        <div class="card-header">
                            <h3><i class='bx bx-gift'></i> 5. Program Bantuan Kesejahteraan (KIP / KPS)</h3>
                        </div>
                        <div class="card-body">
                            <div class="form-grid-2">
                                <div class="form-row">
                                    <label class="form-label">Penerima KPS / PKH</label>
                                    <select class="form-select" name="kps_receiver" {{ !$canEdit ? 'disabled' : '' }}>
                                        <option value="0" {{ !old('kps_receiver', $student->kps_receiver) ? 'selected' : '' }}>Tidak</option>
                                        <option value="1" {{ old('kps_receiver', $student->kps_receiver) ? 'selected' : '' }}>Ya</option>
                                    </select>
                                </div>
                                <div class="form-row">
                                    <label class="form-label">Nomor KPS / PKH (Jika Menerima)</label>
                                    <input type="text" class="form-input" name="kps_number" placeholder="Nomor KPS" value="{{ old('kps_number', $student->kps_number) }}" {{ !$canEdit ? 'disabled' : '' }}>
                                </div>
                                <div class="form-row">
                                    <label class="form-label">Penerima KIP (Kartu Indonesia Pintar)</label>
                                    <select class="form-select" name="kip_receiver" {{ !$canEdit ? 'disabled' : '' }}>
                                        <option value="0" {{ !old('kip_receiver', $student->kip_receiver) ? 'selected' : '' }}>Tidak</option>
                                        <option value="1" {{ old('kip_receiver', $student->kip_receiver) ? 'selected' : '' }}>Ya</option>
                                    </select>
                                </div>
                                <div class="form-row">
                                    <label class="form-label">Nomor KIP (Jika Menerima)</label>
                                    <input type="text" class="form-input" name="kip_number" placeholder="Nomor KIP" value="{{ old('kip_number', $student->kip_number) }}" {{ !$canEdit ? 'disabled' : '' }}>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SUBMIT BAR (STICKY) -->
                    @if($canEdit)
                        <div class="submit-bar">
                            <div style="font-size: 13px; color: var(--slate-600);">
                                <i class='bx bx-check-shield'></i> Pastikan seluruh data pokok, foto 4x6, dan titik rumah sesuai dokumen resmi.
                            </div>
                            <button type="submit" class="btn-save-primary" id="btnSubmitProfile">
                                <i class='bx bx-save'></i> Simpan Pembaruan Profil Siswa
                            </button>
                        </div>
                    @endif

                </div>
            </div>
        </form>

    </div>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <script>
        // Koordinat Sekolah
        const SCHOOL_LAT = {{ (float) ($school->latitude ?? -6.166827) }};
        const SCHOOL_LNG = {{ (float) ($school->longitude ?? 106.837562) }};
        const CAN_EDIT = {{ $canEdit ? 'true' : 'false' }};

        let homeLat = parseFloat(document.getElementById('inputHomeLat').value) || SCHOOL_LAT;
        let homeLng = parseFloat(document.getElementById('inputHomeLng').value) || SCHOOL_LNG;

        // Inisialisasi Peta Leaflet
        const map = L.map('homeMapPreview').setView([homeLat, homeLng], 15);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors',
            maxZoom: 19
        }).addTo(map);

        // Marker Sekolah (Merah)
        const schoolIcon = L.divIcon({
            html: '<div style="background:#4f46e5; color:white; width:30px; height:30px; border-radius:50%; display:flex; align-items:center; justify-content:center; box-shadow:0 3px 8px rgba(0,0,0,0.3); font-size:16px;">🏫</div>',
            className: '',
            iconSize: [30, 30],
            iconAnchor: [15, 15]
        });
        L.marker([SCHOOL_LAT, SCHOOL_LNG], { icon: schoolIcon }).addTo(map)
            .bindPopup('<b>{{ addslashes($school->name) }}</b><br>Lokasi Sekolah');

        // Marker Rumah Siswa (Bisa di-drag jika canEdit)
        const houseIcon = L.divIcon({
            html: '<div style="background:#ef4444; color:white; width:32px; height:32px; border-radius:50%; display:flex; align-items:center; justify-content:center; box-shadow:0 4px 10px rgba(239,68,68,0.4); font-size:18px;">🏠</div>',
            className: '',
            iconSize: [32, 32],
            iconAnchor: [16, 16]
        });

        const homeMarker = L.marker([homeLat, homeLng], {
            draggable: CAN_EDIT,
            icon: houseIcon
        }).addTo(map).bindPopup('<b>Titik Rumah Anda</b><br>' + (CAN_EDIT ? 'Geser pin ini tepat ke atas atap rumah Anda.' : 'Lokasi tersimpan.'));

        homeMarker.on('dragend', function (e) {
            const pos = homeMarker.getLatLng();
            document.getElementById('inputHomeLat').value = pos.lat.toFixed(7);
            document.getElementById('inputHomeLng').value = pos.lng.toFixed(7);
            updateDistanceDisplay(pos.lat, pos.lng);
        });

        function updateMarkerFromInputs() {
            const lat = parseFloat(document.getElementById('inputHomeLat').value);
            const lng = parseFloat(document.getElementById('inputHomeLng').value);
            if (!isNaN(lat) && !isNaN(lng)) {
                homeMarker.setLatLng([lat, lng]);
                map.panTo([lat, lng]);
                updateDistanceDisplay(lat, lng);
            }
        }

        // Hitung jarak Haversine sederhana di sisi client
        function calculateDistanceKm(lat1, lon1, lat2, lon2) {
            const R = 6371; // Radius bumi km
            const dLat = (lat2 - lat1) * Math.PI / 180;
            const dLon = (lon2 - lon1) * Math.PI / 180;
            const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                      Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                      Math.sin(dLon/2) * Math.sin(dLon/2);
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
            return (R * c).toFixed(2);
        }

        function updateDistanceDisplay(lat, lng) {
            const dist = calculateDistanceKm(lat, lng, SCHOOL_LAT, SCHOOL_LNG);
            document.getElementById('calculatedDistanceBadge').innerText = `Jarak ke Sekolah: ~${dist} km`;
        }

        // Auto-Detect GPS dari perangkat siswa
        function detectHomeCoordinates() {
            if (!navigator.geolocation) {
                alert('Browser Anda tidak mendukung deteksi lokasi (Geolocation API).');
                return;
            }

            const btn = document.getElementById('btnDetectGps');
            const originalText = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = "<i class='bx bx-loader-alt bx-spin'></i> Mendeteksi GPS Perangkat...";

            navigator.geolocation.getCurrentPosition(
                function (position) {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    const acc = position.coords.accuracy;

                    document.getElementById('inputHomeLat').value = lat.toFixed(7);
                    document.getElementById('inputHomeLng').value = lng.toFixed(7);

                    homeMarker.setLatLng([lat, lng]);
                    map.setView([lat, lng], 17);
                    updateDistanceDisplay(lat, lng);

                    btn.disabled = false;
                    btn.innerHTML = "<i class='bx bx-check-circle'></i> Titik GPS Berhasil Didapat (Akurasi: ~" + Math.round(acc) + "m)";
                    setTimeout(() => { btn.innerHTML = originalText; }, 4000);
                },
                function (error) {
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                    let msg = 'Gagal mendeteksi lokasi perangkat.';
                    if (error.code === 1) msg = 'Izin lokasi (GPS) ditolak. Mohon aktifkan izin akses lokasi pada browser Anda.';
                    else if (error.code === 2) msg = 'Posisi tidak tersedia.';
                    else if (error.code === 3) msg = 'Waktu permintaan lokasi habis.';
                    alert(msg);
                },
                {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 0
                }
            );
        }

        // Live Preview Pasfoto 4x6
        function previewPasfoto(event) {
            const file = event.target.files[0];
            if (file) {
                if (file.size > 2 * 1024 * 1024) {
                    alert('Ukuran file pasfoto melebihi batas 2 MB.');
                    event.target.value = '';
                    return;
                }

                const reader = new FileReader();
                reader.onload = function (e) {
                    document.getElementById('pasfotoPreview').src = e.target.result;
                    const heroMini = document.getElementById('heroPhotoPreview');
                    if (heroMini) heroMini.src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        }
    </script>
</body>
</html>
