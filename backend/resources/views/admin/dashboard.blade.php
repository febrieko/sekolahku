@extends('layouts.vuexy')

@section('title', 'Dashboard Analytics')
@section('breadcrumb_current', 'Dashboard Analytics')

@push('styles')
<style>
    /* ===== ADMINDEK ANALYTICS DASHBOARD STYLES ===== */

    /* 1. Page Header Toolbar */
    .admindek-page-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 1rem;
        margin-bottom: 1.5rem;
    }
    .admindek-header-title h1 {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--text-heading);
        margin: 0 0 0.25rem 0;
        letter-spacing: -0.02em;
        display: flex;
        align-items: center;
        gap: 0.65rem;
    }
    .admindek-header-title p {
        font-size: 0.84rem;
        color: var(--text-muted);
        margin: 0;
    }
    .admindek-header-actions {
        display: flex;
        align-items: center;
        gap: 0.65rem;
        flex-wrap: wrap;
    }

    /* 2. Admindek 4-Solid-Color KPI Cards */
    .admindek-kpi-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1.25rem;
        margin-bottom: 1.5rem;
    }
    .admindek-kpi-card {
        border-radius: var(--radius-sm);
        padding: 1.45rem 1.6rem;
        color: #ffffff;
        position: relative;
        overflow: hidden;
        box-shadow: 0 4px 14px rgba(69, 90, 100, 0.12);
        transition: transform 0.22s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.22s ease;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        min-height: 148px;
    }
    .admindek-kpi-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 24px rgba(69, 90, 100, 0.2);
    }
    /* 4 Distinct Solid Color Backgrounds */
    .admindek-kpi-blue   { background: linear-gradient(135deg, #3069ec 0%, #4680ff 100%); }
    .admindek-kpi-green  { background: linear-gradient(135deg, #23946e 0%, #2ca87f 100%); }
    .admindek-kpi-yellow { background: linear-gradient(135deg, #cc7b00 0%, #e58a00 100%); }
    .admindek-kpi-cyan   { background: linear-gradient(135deg, #0394d6 0%, #04a9f5 100%); }

    .kpi-bg-watermark {
        position: absolute;
        right: 12px;
        bottom: 4px;
        font-size: 5.2rem;
        color: rgba(255, 255, 255, 0.16);
        pointer-events: none;
        line-height: 1;
        transition: transform 0.25s ease;
    }
    .admindek-kpi-card:hover .kpi-bg-watermark {
        transform: scale(1.08) rotate(-4deg);
    }

    .kpi-top-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 0.5rem;
        position: relative;
        z-index: 2;
    }
    .kpi-label {
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: rgba(255, 255, 255, 0.88);
    }
    .kpi-chip {
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        padding: 0.2rem 0.55rem;
        border-radius: var(--radius-pill);
        background: rgba(255, 255, 255, 0.22);
        backdrop-filter: blur(4px);
        font-size: 0.7rem;
        font-weight: 600;
    }

    .kpi-main-number {
        font-size: 2.25rem;
        font-weight: 300;
        letter-spacing: -0.03em;
        line-height: 1.1;
        margin: 0.15rem 0 0.45rem;
        position: relative;
        z-index: 2;
    }

    .kpi-bottom-meta {
        font-size: 0.76rem;
        color: rgba(255, 255, 255, 0.9);
        display: flex;
        align-items: center;
        gap: 0.5rem;
        position: relative;
        z-index: 2;
        font-weight: 500;
    }

    /* 3. Analytics Grid Layout */
    .admindek-analytics-row {
        display: grid;
        grid-template-columns: 2.2fr 1fr;
        gap: 1.25rem;
        margin-bottom: 1.5rem;
    }

    .admindek-card {
        background: var(--bg-surface);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-sm);
        box-shadow: var(--shadow-card);
        overflow: hidden;
        transition: var(--transition);
        margin-bottom: 1.25rem;
    }
    .admindek-card:hover {
        box-shadow: var(--shadow-card-hover);
    }
    .admindek-card-header {
        padding: 1.15rem 1.4rem;
        border-bottom: 1px solid var(--border-color);
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: var(--bg-surface);
    }
    .admindek-card-title {
        font-size: 0.96rem;
        font-weight: 700;
        color: var(--text-heading);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .admindek-card-subtitle {
        font-size: 0.76rem;
        color: var(--text-muted);
        margin: 2px 0 0;
    }
    .admindek-card-body {
        padding: 1.4rem;
    }

    /* Live Pulse Indicator */
    .live-dot-pulse {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background-color: #2ca87f;
        box-shadow: 0 0 0 2px rgba(44, 168, 127, 0.4);
        display: inline-block;
        animation: admindekPulse 1.8s infinite;
    }
    @keyframes admindekPulse {
        0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(44, 168, 127, 0.7); }
        70% { transform: scale(1.1); box-shadow: 0 0 0 6px rgba(44, 168, 127, 0); }
        100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(44, 168, 127, 0); }
    }

    /* KBM Session Tracker Bar */
    .kbm-session-box {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1rem 1.2rem;
        border-radius: var(--radius-sm);
        background: var(--bg-subtle);
        border: 1px solid var(--border-color);
        margin-bottom: 1rem;
    }
    .kbm-time-tag {
        font-size: 1.35rem;
        font-weight: 700;
        font-family: 'JetBrains Mono', monospace;
        color: var(--text-heading);
        line-height: 1;
    }

    /* Progress Distribution List */
    .progress-list-item {
        display: flex;
        flex-direction: column;
        gap: 0.35rem;
        margin-bottom: 0.85rem;
    }
    .progress-list-meta {
        display: flex;
        justify-content: space-between;
        font-size: 0.78rem;
        font-weight: 600;
        color: var(--text-heading);
    }
    .admindek-progress-bar {
        height: 6px;
        border-radius: 3px;
        background: var(--border-color);
        overflow: hidden;
    }
    .admindek-progress-fill {
        height: 100%;
        border-radius: 3px;
        transition: width 0.4s ease;
    }

    /* Modern Clean Table */
    .table-container {
        overflow-x: auto;
    }
    .admindek-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        font-size: 0.82rem;
    }
    .admindek-table th {
        background: var(--bg-subtle);
        color: var(--text-muted);
        font-weight: 700;
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        padding: 0.85rem 1.15rem;
        border-bottom: 1px solid var(--border-color);
        text-align: left;
    }
    .admindek-table td {
        padding: 0.95rem 1.15rem;
        border-bottom: 1px solid var(--border-subtle);
        color: var(--text-body);
        vertical-align: middle;
    }
    .admindek-table tr:hover td {
        background-color: var(--bg-subtle);
    }
    .admindek-table tr:last-child td {
        border-bottom: none;
    }

    .badge-admindek {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.25rem 0.6rem;
        border-radius: 3px;
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.02em;
    }
    .badge-admindek.badge-success { background: rgba(44, 168, 127, 0.14); color: #2ca87f; }
    .badge-admindek.badge-warning { background: rgba(229, 138, 0, 0.14); color: #e58a00; }
    .badge-admindek.badge-danger  { background: rgba(220, 38, 38, 0.14); color: #dc2626; }
    .badge-admindek.badge-info    { background: rgba(4, 169, 245, 0.14); color: #04a9f5; }
    .badge-admindek.badge-primary { background: rgba(70, 128, 255, 0.14); color: #4680ff; }

    /* Search Filter Bar inside card */
    .table-filter-bar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
        padding: 0.85rem 1.4rem;
        border-bottom: 1px solid var(--border-color);
        background: var(--bg-surface);
        flex-wrap: wrap;
    }
    .table-search-input {
        background: var(--bg-subtle);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-sm);
        padding: 0.4rem 0.85rem;
        font-size: 0.82rem;
        color: var(--text-heading);
        outline: none;
        width: 260px;
        transition: var(--transition);
    }
    .table-search-input:focus {
        border-color: var(--admindek-primary);
        box-shadow: 0 0 0 2px var(--vx-primary-light);
    }

    /* Responsive */
    @media (max-width: 1200px) {
        .admindek-kpi-grid { grid-template-columns: repeat(2, 1fr); }
        .admindek-analytics-row { grid-template-columns: 1fr; }
    }
    @media (max-width: 640px) {
        .admindek-kpi-grid { grid-template-columns: 1fr; }
        .table-search-input { width: 100%; }
    }
</style>
@endpush

@section('content')
<!-- 1. ADMINDEK PAGE HEADER -->
<div class="admindek-page-header">
    <div class="admindek-header-title">
        <h1>
            <span>Dashboard Analytics</span>
            <span class="badge-admindek badge-primary" style="font-size:0.75rem;">
                <span class="live-dot-pulse"></span> KBM Real-Time
            </span>
        </h1>
        <p>
            {{ $school->name ?? 'SMAN 1 Jakarta' }} &bull; Tahun Ajaran {{ $activeYear->name ?? '2026/2027' }} {{ $activeSemester ? ($activeSemester->semester == 'odd' ? 'Ganjil' : 'Genap') : 'Ganjil' }} &bull; NPSN: {{ $school->npsn ?? '20101234' }}
        </p>
    </div>

    <div class="admindek-header-actions">
        @if(Auth::check() && in_array(Auth::user()->role, ['teacher', 'waka_kurikulum', 'waka_kesiswaan', 'waka_sarpras', 'waka_humas', 'waka_hubin']))
        <a href="{{ route('teacher.profile') }}" class="btn btn-primary" style="display:inline-flex; align-items:center; gap:0.45rem; padding:0.5rem 1rem; border-radius:var(--radius-sm); font-size:0.82rem; font-weight:600; background: #10b981; border-color: #10b981; color: #fff;">
            <i class='bx bx-id-card'></i>
            <span>Edit Profil & Pas Foto GTK</span>
        </a>
        @endif
        <a href="{{ route('attendance.portal') }}" target="_blank" class="btn btn-primary" style="display:inline-flex; align-items:center; gap:0.45rem; padding:0.5rem 1rem; border-radius:var(--radius-sm); font-size:0.82rem; font-weight:600;">
            <i class='bx bx-scan'></i>
            <span>Portal Presensi Live</span>
        </a>
        <a href="{{ route('admin.data-master') }}" class="btn btn-secondary" style="display:inline-flex; align-items:center; gap:0.45rem; padding:0.5rem 1rem; border-radius:var(--radius-sm); font-size:0.82rem; font-weight:600;">
            <i class='bx bx-data'></i>
            <span>Master Dapodik</span>
        </a>
        <button type="button" class="btn btn-secondary" onclick="openThemeCustomizer()" title="Pengaturan Tampilan" style="display:inline-flex; align-items:center; gap:0.35rem; padding:0.5rem 0.85rem; border-radius:var(--radius-sm); font-size:0.82rem; font-weight:600;">
            <i class='bx bx-slider'></i>
            <span>Kustomizer</span>
        </button>
    </div>
</div>

@if(Auth::check() && in_array(Auth::user()->role, ['teacher', 'waka_kurikulum', 'waka_kesiswaan', 'waka_sarpras', 'waka_humas', 'waka_hubin']))
    @php
        $isEditOpen = $school ? $school->isGtkProfileEditOpen() : true;
    @endphp
    <div style="background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: var(--radius-xl); padding: 1.15rem 1.5rem; margin-bottom: 1.5rem; display: flex; align-items: center; justify-content: space-between; gap: 1rem; flex-wrap: wrap; box-shadow: var(--shadow-card); border-left: 5px solid #10b981;">
        <div style="display: flex; align-items: center; gap: 1rem;">
            <div style="width: 44px; height: 44px; border-radius: var(--radius-md); background: rgba(16, 185, 129, 0.14); color: #10b981; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; flex-shrink: 0;">
                <i class='bx bx-id-card'></i>
            </div>
            <div>
                <div style="font-weight: 700; font-size: 0.95rem; color: var(--text-heading); display: flex; align-items: center; gap: 0.5rem;">
                    <span>Lengkapi Profil & Pas Foto 4x6 GTK Anda</span>
                    @if($isEditOpen)
                        <span class="badge-admindek badge-success"><i class='bx bx-check'></i> Periode Edit Dibuka</span>
                    @else
                        <span class="badge-admindek badge-danger"><i class='bx bx-lock-alt'></i> Ditutup Admin</span>
                    @endif
                </div>
                <div class="cell-sub" style="margin-top: 2px; font-size: 0.78rem; color: var(--text-muted);">
                    Perbarui biodata Dapodik, riwayat kampus, sertifikasi profesi pendidik, tugas tambahan, dan pas foto formal 4x6.
                </div>
            </div>
        </div>
        <div>
            <a href="{{ route('teacher.profile') }}" class="btn btn-primary" style="display: inline-flex; align-items: center; gap: 0.4rem; font-weight: 600; padding: 0.45rem 1rem; border-radius: var(--radius-sm); font-size: 0.82rem; background: #10b981; border-color: #10b981; color: #fff;">
                <i class='bx bx-edit'></i> Lengkapi Profil Sekarang
            </a>
        </div>
    </div>
@endif

<!-- 2. ADMINDEK 4 SOLID KPI CARDS -->
<div class="admindek-kpi-grid">
    <!-- Card 1: Total Siswa (Blue) -->
    <div class="admindek-kpi-card admindek-kpi-blue">
        <i class='bx bxs-user-detail kpi-bg-watermark'></i>
        <div class="kpi-top-row">
            <span class="kpi-label">Total Peserta Didik</span>
            <span class="kpi-chip">
                <i class='bx bx-user-check'></i> Terdaftar
            </span>
        </div>
        <div class="kpi-main-number">{{ number_format($totalStudents) }}</div>
        <div class="kpi-bottom-meta">
            <span><i class='bx bx-male'></i> {{ $maleStudents }} Laki-laki</span>
            <span>&bull;</span>
            <span><i class='bx bx-female'></i> {{ $femaleStudents }} Perempuan</span>
        </div>
    </div>

    <!-- Card 2: Kehadiran Hari Ini (Green) -->
    <div class="admindek-kpi-card admindek-kpi-green">
        <i class='bx bx-check-shield kpi-bg-watermark'></i>
        <div class="kpi-top-row">
            <span class="kpi-label">Presensi Siswa Hari Ini</span>
            <span class="kpi-chip">
                <span class="live-dot-pulse" style="background:#ffffff; box-shadow:none;"></span> Live
            </span>
        </div>
        <div class="kpi-main-number">{{ $attendanceRate }}%</div>
        <div class="kpi-bottom-meta">
            <span>{{ $attendedCount }}/{{ $totalStudents }} Hadir ({{ $presentCount }} tepat, {{ $lateCount }} telat)</span>
        </div>
    </div>

    <!-- Card 3: Pendidik & GTK (Amber/Yellow) -->
    <div class="admindek-kpi-card admindek-kpi-yellow">
        <i class='bx bxs-graduation kpi-bg-watermark'></i>
        <div class="kpi-top-row">
            <span class="kpi-label">Pendidik & GTK</span>
            <span class="kpi-chip">
                <i class='bx bx-badge-check'></i> Aktif
            </span>
        </div>
        <div class="kpi-main-number">{{ number_format($totalTeachers) }}</div>
        <div class="kpi-bottom-meta">
            <span>{{ $pnsCount }} PNS &bull; {{ $gttCount }} Non-PNS &bull; {{ $yayasanCount }} Yayasan</span>
        </div>
    </div>

    <!-- Card 4: Rombongan Belajar (Sky/Cyan) -->
    <div class="admindek-kpi-card admindek-kpi-cyan">
        <i class='bx bxs-school kpi-bg-watermark'></i>
        <div class="kpi-top-row">
            <span class="kpi-label">Rombongan Belajar</span>
            <span class="kpi-chip">
                <i class='bx bx-layer'></i> Spektrum
            </span>
        </div>
        <div class="kpi-main-number">{{ number_format($totalClasses) }}</div>
        <div class="kpi-bottom-meta">
            <span>{{ $totalDepartments }} Program Keahlian / Jurusan</span>
        </div>
    </div>
</div>

<!-- 3. ROW 2: REAL-TIME CHARTS (LINE & DONUT ANALYTICS) -->
<div class="admindek-analytics-row">
    <!-- Left: Weekly Attendance Trend Line Chart -->
    <div class="admindek-card">
        <div class="admindek-card-header">
            <div>
                <h2 class="admindek-card-title">
                    <i class='bx bx-trending-up' style="color:var(--admindek-primary);"></i>
                    <span>Tren Presensi 7 Hari Terakhir</span>
                </h2>
                <p class="admindek-card-subtitle">Analisis fluktuasi kedatangan siswa tepat waktu, keterlambatan, dan ketidakhadiran</p>
            </div>
            <div style="display:flex; align-items:center; gap:0.4rem;">
                <span class="badge-admindek badge-success">
                    <i class='bx bx-check-circle'></i> Real-Time Synced
                </span>
            </div>
        </div>
        <div class="admindek-card-body" style="padding: 1.25rem 1.4rem;">
            <div style="position: relative; height: 280px; width: 100%;">
                <canvas id="admindekWeeklyChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Right: Attendance Composition Doughnut Chart -->
    <div class="admindek-card">
        <div class="admindek-card-header">
            <div>
                <h2 class="admindek-card-title">
                    <i class='bx bx-pie-chart-alt-2' style="color:var(--vx-success);"></i>
                    <span>Komposisi Hari Ini</span>
                </h2>
                <p class="admindek-card-subtitle">Distribusi status presensi siswa</p>
            </div>
        </div>
        <div class="admindek-card-body">
            <div style="position: relative; height: 180px; width: 100%; display:flex; align-items:center; justify-content:center;">
                <canvas id="admindekDonutChart"></canvas>
            </div>

            <!-- Breakdown Legend -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.65rem; margin-top: 1.25rem; padding-top: 1rem; border-top: 1px solid var(--border-color); font-size: 0.78rem;">
                <div style="display:flex; align-items:center; gap:0.4rem;">
                    <span style="width:8px; height:8px; border-radius:50%; background:#2ca87f;"></span>
                    <span style="color:var(--text-muted);">Tepat Waktu:</span>
                    <strong style="color:var(--text-heading); margin-left:auto;">{{ $presentCount }}</strong>
                </div>
                <div style="display:flex; align-items:center; gap:0.4rem;">
                    <span style="width:8px; height:8px; border-radius:50%; background:#e58a00;"></span>
                    <span style="color:var(--text-muted);">Terlambat:</span>
                    <strong style="color:var(--text-heading); margin-left:auto;">{{ $lateCount }}</strong>
                </div>
                <div style="display:flex; align-items:center; gap:0.4rem;">
                    <span style="width:8px; height:8px; border-radius:50%; background:#04a9f5;"></span>
                    <span style="color:var(--text-muted);">Sakit / Izin:</span>
                    <strong style="color:var(--text-heading); margin-left:auto;">{{ $sickCount + $excusedCount }}</strong>
                </div>
                <div style="display:flex; align-items:center; gap:0.4rem;">
                    <span style="width:8px; height:8px; border-radius:50%; background:#dc2626;"></span>
                    <span style="color:var(--text-muted);">Alpa / Belum:</span>
                    <strong style="color:var(--text-heading); margin-left:auto;">{{ $absentCount }}</strong>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 4. ROW 3: KBM ACTIVE SESSION TRACKER & SEBARAN ROMBEL -->
<div class="admindek-analytics-row">
    <!-- Left: KBM Active Tracker & Geofence -->
    <div class="admindek-card">
        <div class="admindek-card-header">
            <div>
                <h2 class="admindek-card-title">
                    <i class='bx bx-time-five' style="color:var(--admindek-primary);"></i>
                    <span>Pelaksanaan KBM & Geofence Kampus</span>
                </h2>
                <p class="admindek-card-subtitle">Status jadwal pelajaran berlangsung dan radius presensi GPS sekolah</p>
            </div>
            <span class="badge-admindek badge-success">
                <span class="live-dot-pulse"></span> GPS Aktif
            </span>
        </div>
        <div class="admindek-card-body">
            <!-- Active Period Box -->
            <div class="kbm-session-box">
                <div>
                    <div style="font-size:0.72rem; text-transform:uppercase; letter-spacing:0.06em; color:var(--text-muted); font-weight:700;">
                        Sesi Jam Pelajaran Aktif
                    </div>
                    <div style="font-size:1.1rem; font-weight:800; color:var(--text-heading); margin:3px 0 2px;">
                        {{ $currentPeriod->name ?? 'Jam Ke-3 (KBM Reguler)' }}
                    </div>
                    <div style="font-size:0.78rem; color:var(--text-muted);">
                        Pola Sesi: {{ $activeCategory->name ?? 'Jam KBM Normal / Standar' }}
                    </div>
                </div>
                <div style="text-align:right;">
                    <div class="kbm-time-tag">
                        {{ \Carbon\Carbon::now()->format('H:i') }} <span style="font-size:0.75rem; color:var(--text-muted);">WIB</span>
                    </div>
                    <span class="badge-admindek badge-primary" style="margin-top:4px;">
                        {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
                    </span>
                </div>
            </div>

            <!-- School Geofence Specs -->
            <div style="display:grid; grid-template-columns: repeat(3, 1fr); gap: 0.85rem; font-size: 0.8rem;">
                <div style="background:var(--bg-subtle); border:1px solid var(--border-color); border-radius:var(--radius-sm); padding:0.85rem;">
                    <div style="font-size:0.7rem; color:var(--text-muted); text-transform:uppercase; font-weight:700;">Radius Presensi</div>
                    <div style="font-size:1.15rem; font-weight:700; color:var(--text-heading); margin:3px 0;">150 m</div>
                    <div style="font-size:0.7rem; color:var(--vx-success); font-weight:600;"><i class='bx bx-check'></i> Geofence Valid</div>
                </div>
                <div style="background:var(--bg-subtle); border:1px solid var(--border-color); border-radius:var(--radius-sm); padding:0.85rem;">
                    <div style="font-size:0.7rem; color:var(--text-muted); text-transform:uppercase; font-weight:700;">Metode Presensi</div>
                    <div style="font-size:1.15rem; font-weight:700; color:var(--text-heading); margin:3px 0;">GPS + Selfie</div>
                    <div style="font-size:0.7rem; color:var(--admindek-primary); font-weight:600;"><i class='bx bx-camera'></i> Face Verification</div>
                </div>
                <div style="background:var(--bg-subtle); border:1px solid var(--border-color); border-radius:var(--radius-sm); padding:0.85rem;">
                    <div style="font-size:0.7rem; color:var(--text-muted); text-transform:uppercase; font-weight:700;">Unit Sekolah</div>
                    <div style="font-size:1.15rem; font-weight:700; color:var(--text-heading); margin:3px 0;">{{ $school->level ?? 'SMA' }}</div>
                    <div style="font-size:0.7rem; color:var(--text-muted); overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">{{ $school->city ?? 'Indonesia' }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Right: Student Distribution per Rombel -->
    <div class="admindek-card">
        <div class="admindek-card-header">
            <div>
                <h2 class="admindek-card-title">
                    <i class='bx bx-bar-chart-alt' style="color:var(--vx-warning);"></i>
                    <span>Sebaran Rombel</span>
                </h2>
                <p class="admindek-card-subtitle">Kapasitas peserta didik per rombel</p>
            </div>
            <a href="{{ route('admin.data-master') }}#tab-classes" style="font-size:0.75rem; color:var(--admindek-primary); font-weight:600; text-decoration:none;">
                Lihat Semua &rarr;
            </a>
        </div>
        <div class="admindek-card-body">
            @php 
                $colors = ['#4680ff', '#2ca87f', '#e58a00', '#04a9f5', '#7c4dff'];
                $maxStudents = max(1, $classesDistribution->max('students_count') ?? 36);
            @endphp
            @foreach($classesDistribution->take(5) as $idx => $cls)
                @php 
                    $pct = round(($cls->students_count / $maxStudents) * 100);
                    $c = $colors[$idx % count($colors)];
                @endphp
                <div class="progress-list-item">
                    <div class="progress-list-meta">
                        <span>{{ $cls->name }}</span>
                        <span style="color:var(--text-muted);">{{ $cls->students_count }} Siswa ({{ $pct }}%)</span>
                    </div>
                    <div class="admindek-progress-bar">
                        <div class="admindek-progress-fill" style="width: {{ $pct }}%; background: {{ $c }};"></div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

<!-- 5. ROW 4: RECENT ATTENDANCE TABLE (LIVE LOG STREAM) -->
<div class="admindek-card">
    <div class="admindek-card-header">
        <div>
            <h2 class="admindek-card-title">
                <span class="live-dot-pulse"></span>
                <span>Log Presensi Siswa Terkini</span>
            </h2>
            <p class="admindek-card-subtitle">Data presensi harian siswa yang tercatat real-time dari scanner dan portal mandiri</p>
        </div>
        <div style="display:flex; align-items:center; gap:0.5rem;">
            <a href="{{ route('attendance.portal') }}" target="_blank" class="btn btn-secondary btn-sm" style="font-size:0.78rem;">
                <i class='bx bx-scan'></i> Buka Portal Scanner
            </a>
        </div>
    </div>

    <!-- Live Search Input -->
    <div class="table-filter-bar">
        <input type="text" id="attendanceTableSearch" class="table-search-input" placeholder="🔍 Cari siswa, NISN, atau kelas..." onkeyup="filterRecentAttendance()">
        <div style="font-size:0.78rem; color:var(--text-muted);">
            Menampilkan <strong>{{ count($recentAttendances) }}</strong> aktivitas terkini
        </div>
    </div>

    <div class="table-container">
        <table class="admindek-table" id="recentAttendanceTable">
            <thead>
                <tr>
                    <th>Peserta Didik</th>
                    <th>Rombel / Kelas</th>
                    <th>Waktu Presensi</th>
                    <th>Status Kehadiran</th>
                    <th>Validasi GPS Geofence</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentAttendances as $att)
                <tr class="attendance-row">
                    <td>
                        <div style="display:flex; align-items:center; gap:0.65rem;">
                            <div style="width:34px; height:34px; border-radius:50%; background:var(--admindek-primary); color:#ffffff; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:0.8rem; flex-shrink:0;">
                                {{ strtoupper(substr($att->student->full_name ?? 'S', 0, 1)) }}
                            </div>
                            <div>
                                <div style="font-weight:700; color:var(--text-heading); font-size:0.84rem;" class="student-name">
                                    {{ $att->student->full_name ?? 'Peserta Didik' }}
                                </div>
                                <div style="font-size:0.7rem; color:var(--text-muted); font-family:'JetBrains Mono', monospace;" class="student-nisn">
                                    NISN: {{ $att->student->nisn ?? '—' }}
                                </div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="badge-admindek" style="background:var(--bg-subtle); color:var(--text-heading); border:1px solid var(--border-color);" class="student-class">
                            {{ $att->student->schoolClass->name ?? '—' }}
                        </span>
                    </td>
                    <td>
                        <div style="font-weight:700; color:var(--text-heading); font-family:'JetBrains Mono', monospace; font-size:0.82rem;">
                            {{ $att->check_in_at ? substr($att->check_in_at, 0, 5) . ' WIB' : '—' }}
                        </div>
                        <div style="font-size:0.7rem; color:var(--text-muted);">{{ ucfirst($att->method) }}</div>
                    </td>
                    <td>
                        @if($att->status === 'present')
                            <span class="badge-admindek badge-success"><i class='bx bx-check'></i> Tepat Waktu</span>
                        @elseif($att->status === 'late')
                            <span class="badge-admindek badge-warning"><i class='bx bx-time-five'></i> Terlambat</span>
                        @elseif($att->status === 'sick')
                            <span class="badge-admindek badge-info"><i class='bx bx-plus-medical'></i> Sakit</span>
                        @elseif($att->status === 'excused')
                            <span class="badge-admindek badge-primary"><i class='bx bx-envelope'></i> Izin</span>
                        @else
                            <span class="badge-admindek badge-danger"><i class='bx bx-x'></i> Alpa</span>
                        @endif
                    </td>
                    <td>
                        @if($att->is_within_radius)
                            <span class="badge-admindek badge-success">
                                <i class='bx bx-check-shield'></i> Dalam Radius ({{ round($att->distance_meters, 0) }}m)
                            </span>
                        @elseif($att->distance_meters)
                            <span class="badge-admindek badge-danger">
                                <i class='bx bx-error-circle'></i> Luar Radius ({{ round($att->distance_meters, 0) }}m)
                            </span>
                        @else
                            <span style="font-size:0.75rem; color:var(--text-muted);">Verifikasi Manual</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align:center; padding: 2.5rem; color: var(--text-muted);">
                        <i class='bx bx-calendar-x' style="font-size:2rem; display:block; margin-bottom:0.5rem; color:var(--text-muted);"></i>
                        Belum ada catatan presensi siswa hari ini.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const isDark = document.documentElement.classList.contains('dark');
    const textColor = isDark ? '#94a3b8' : '#64748b';
    const gridColor = isDark ? 'rgba(255, 255, 255, 0.08)' : '#e3e8ee';

    // 1. Chart.js: Real-Time Weekly Attendance Trend Line Chart
    const weeklyCanvas = document.getElementById('admindekWeeklyChart');
    if (weeklyCanvas) {
        const ctx = weeklyCanvas.getContext('2d');

        // Gradients
        const gradPresent = ctx.createLinearGradient(0, 0, 0, 260);
        gradPresent.addColorStop(0, 'rgba(44, 168, 127, 0.35)');
        gradPresent.addColorStop(1, 'rgba(44, 168, 127, 0.0)');

        const gradLate = ctx.createLinearGradient(0, 0, 0, 260);
        gradLate.addColorStop(0, 'rgba(229, 138, 0, 0.35)');
        gradLate.addColorStop(1, 'rgba(229, 138, 0, 0.0)');

        const gradAbsent = ctx.createLinearGradient(0, 0, 0, 260);
        gradAbsent.addColorStop(0, 'rgba(220, 38, 38, 0.30)');
        gradAbsent.addColorStop(1, 'rgba(220, 38, 38, 0.0)');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: @json($weeklyDays),
                datasets: [
                    {
                        label: 'Hadir Tepat Waktu',
                        data: @json($weeklyPresent),
                        borderColor: '#2ca87f',
                        backgroundColor: gradPresent,
                        fill: true,
                        tension: 0.35,
                        borderWidth: 2.5,
                        pointRadius: 4,
                        pointBackgroundColor: '#2ca87f',
                        pointHoverRadius: 6
                    },
                    {
                        label: 'Terlambat',
                        data: @json($weeklyLate),
                        borderColor: '#e58a00',
                        backgroundColor: gradLate,
                        fill: true,
                        tension: 0.35,
                        borderWidth: 2,
                        pointRadius: 3,
                        pointBackgroundColor: '#e58a00',
                        pointHoverRadius: 5
                    },
                    {
                        label: 'Alpa / Belum Hadir',
                        data: @json($weeklyAbsent),
                        borderColor: '#dc2626',
                        backgroundColor: gradAbsent,
                        fill: true,
                        tension: 0.35,
                        borderWidth: 2,
                        pointRadius: 3,
                        pointBackgroundColor: '#dc2626',
                        pointHoverRadius: 5
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        align: 'end',
                        labels: {
                            color: textColor,
                            font: { family: 'Inter', size: 12, weight: '500' },
                            usePointStyle: true,
                            boxWidth: 8
                        }
                    },
                    tooltip: {
                        backgroundColor: isDark ? '#1a1b1d' : '#ffffff',
                        titleColor: isDark ? '#f8fafc' : '#1e293b',
                        bodyColor: isDark ? '#cbd5e1' : '#475569',
                        borderColor: gridColor,
                        borderWidth: 1,
                        padding: 10,
                        boxPadding: 4,
                        usePointStyle: true
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { color: textColor, font: { family: 'Inter', size: 11 } }
                    },
                    y: {
                        grid: { color: gridColor, drawBorder: false },
                        ticks: { color: textColor, font: { family: 'Inter', size: 11 }, precision: 0 }
                    }
                }
            }
        });
    }

    // 2. Chart.js: Attendance Status Doughnut Chart
    const donutCanvas = document.getElementById('admindekDonutChart');
    if (donutCanvas) {
        new Chart(donutCanvas.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: ['Tepat Waktu', 'Terlambat', 'Sakit/Izin', 'Alpa'],
                datasets: [{
                    data: [
                        {{ $presentCount }},
                        {{ $lateCount }},
                        {{ $sickCount + $excusedCount }},
                        {{ $absentCount }}
                    ],
                    backgroundColor: ['#2ca87f', '#e58a00', '#04a9f5', '#dc2626'],
                    borderWidth: isDark ? 2 : 2,
                    borderColor: isDark ? '#1a1b1d' : '#ffffff',
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '72%',
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: isDark ? '#1a1b1d' : '#ffffff',
                        titleColor: isDark ? '#f8fafc' : '#1e293b',
                        bodyColor: isDark ? '#cbd5e1' : '#475569',
                        borderColor: gridColor,
                        borderWidth: 1,
                        padding: 10
                    }
                }
            }
        });
    }
});

// 3. Live search filter for recent attendance table
function filterRecentAttendance() {
    const query = (document.getElementById('attendanceTableSearch')?.value || '').toLowerCase();
    const rows = document.querySelectorAll('#recentAttendanceTable tbody tr.attendance-row');
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(query) ? '' : 'none';
    });
}
</script>
@endpush
