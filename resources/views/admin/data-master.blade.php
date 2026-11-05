@extends('layouts.vuexy')

@section('title', 'Data Master')
@section('description', 'Kelola data pokok pendidikan, tahun ajaran, jadwal belajar, GTK, dan peserta didik sesuai standar Dapodik.')

@section('breadcrumb')
    <span class="breadcrumb-sep">/</span>
    <span class="breadcrumb-item">Akademik</span>
    <span class="breadcrumb-sep">/</span>
    <span class="breadcrumb-current">Data Master</span>
@endsection

@push('styles')
<style>
    /* Data Master Header & KPI Grid Design System */
    .master-header-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 1.25rem;
        margin-bottom: 1.5rem;
    }
    .master-header-left h1 {
        font-size: 1.45rem;
        font-weight: 800;
        color: var(--text-heading);
        margin: 0 0 0.35rem 0;
        display: flex;
        align-items: center;
        gap: 0.65rem;
        letter-spacing: -0.025em;
        flex-wrap: wrap;
    }
    .master-header-left p {
        font-size: 0.85rem;
        color: var(--text-muted);
        margin: 0;
        line-height: 1.45;
    }
    .master-header-actions {
        display: flex;
        align-items: center;
        gap: 0.65rem;
        flex-wrap: wrap;
    }

    /* KPI Stats Grid (Responsive 6 -> 3 -> 2 -> 1) */
    .master-stats-grid {
        display: grid;
        grid-template-columns: repeat(6, 1fr);
        gap: 1rem;
        margin-bottom: 1.5rem;
    }
    @media (max-width: 1400px) {
        .master-stats-grid {
            grid-template-columns: repeat(3, 1fr);
        }
    }
    @media (max-width: 840px) {
        .master-stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    @media (max-width: 520px) {
        .master-stats-grid {
            grid-template-columns: 1fr;
        }
    }

    .master-stat-card {
        background-color: var(--bg-surface);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-xl);
        padding: 1.15rem 1.25rem;
        box-shadow: var(--shadow-card);
        display: flex;
        align-items: center;
        gap: 0.95rem;
        transition: var(--transition);
        position: relative;
        overflow: hidden;
    }
    .master-stat-card:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-card-hover);
        border-color: var(--bento-primary);
    }
    .master-stat-icon-wrap {
        width: 46px;
        height: 46px;
        border-radius: var(--radius-md);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.35rem;
        flex-shrink: 0;
        transition: var(--transition);
    }
    .master-stat-card:hover .master-stat-icon-wrap {
        transform: scale(1.05);
    }
    .master-stat-body {
        flex: 1;
        min-width: 0;
    }
    .master-stat-label {
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--text-muted);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .master-stat-value {
        font-size: 1.45rem;
        font-weight: 800;
        color: var(--text-heading);
        letter-spacing: -0.03em;
        line-height: 1.2;
        margin: 0.15rem 0;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .master-stat-sub {
        font-size: 0.72rem;
        color: var(--text-muted);
        display: flex;
        align-items: center;
        gap: 0.35rem;
        white-space: nowrap;
    }

    /* Active Indicator Banner */
    .master-active-indicator {
        background: var(--bg-surface);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-xl);
        padding: 1rem 1.4rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 0.75rem;
        margin-bottom: 1.5rem;
        box-shadow: var(--shadow-card);
        border-left: 4px solid var(--bento-primary);
        transition: var(--transition);
    }
    .master-indicator-icon {
        width: 42px;
        height: 42px;
        border-radius: var(--radius-lg);
        background: var(--vx-primary-light);
        color: var(--vx-primary);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.35rem;
        flex-shrink: 0;
    }

    /* Active Curriculum Row Highlight & Structure Badge */
    .curriculum-table-row {
        transition: all 0.25s ease;
    }
    .curriculum-table-row.active-cur-row {
        background: rgba(115, 103, 240, 0.08) !important;
        border-left: 4px solid var(--vx-primary) !important;
    }
    .curriculum-table-row:hover {
        background: rgba(115, 103, 240, 0.04);
    }
    .btn-cur-structure-badge {
        transition: all 0.2s ease;
    }
    .btn-cur-structure-badge:hover {
        transform: translateY(-1px);
        box-shadow: 0 3px 8px rgba(115, 103, 240, 0.25);
    }
    @keyframes panelPulseHighlight {
        0% { box-shadow: 0 0 0 0 rgba(115, 103, 240, 0.5); }
        50% { box-shadow: 0 0 0 10px rgba(115, 103, 240, 0); }
        100% { box-shadow: 0 0 0 0 rgba(115, 103, 240, 0); }
    }
    .panel-pulse-active {
        animation: panelPulseHighlight 1.5s ease-out;
    }
</style>
@endpush

@section('content')
<!-- Page Header -->
<div class="master-header-row page-title-row">
    <div class="master-header-left page-heading">
        <h1>
            Master Data Terpadu
            <span class="vx-badge badge-primary" style="font-size:0.72rem; vertical-align: middle;">Dapodik Standard</span>
        </h1>
        <p>Kelola data pokok pendidikan, tahun ajaran aktif, multi-kategori jadwal belajar, GTK, dan peserta didik.</p>
    </div>
    <div class="master-header-actions page-actions">
        <button class="btn btn-secondary" onclick="openModal('modalAddStudent')">
            <i class='bx bx-user-plus'></i> Tambah Siswa
        </button>
        <button class="btn btn-primary" onclick="openModal('modalAddTeacher')">
            <i class='bx bx-plus'></i> Tambah GTK
        </button>
    </div>
</div>

<!-- KPI Stats Grid -->
<div class="stats-grid master-stats-grid">
    <div class="stat-card master-stat-card">
        <div class="stat-icon-wrap master-stat-icon-wrap stat-icon-primary">
            <i class='bx bxs-user-detail'></i>
        </div>
        <div class="stat-body master-stat-body">
            <div class="stat-label master-stat-label">Total Peserta Didik</div>
            <div class="stat-value master-stat-value">{{ $stats['total_students'] }}</div>
            <div class="stat-sub master-stat-sub">Siswa terdaftar aktif</div>
        </div>
    </div>

    <div class="stat-card master-stat-card">
        <div class="stat-icon-wrap master-stat-icon-wrap stat-icon-info">
            <i class='bx bxs-chalkboard'></i>
        </div>
        <div class="stat-body master-stat-body">
            <div class="stat-label master-stat-label">Guru & Tendik (GTK)</div>
            <div class="stat-value master-stat-value">{{ $stats['total_teachers'] }}</div>
            <div class="stat-sub master-stat-sub">PNS, PPPK, Yayasan</div>
        </div>
    </div>

    <div class="stat-card master-stat-card">
        <div class="stat-icon-wrap master-stat-icon-wrap stat-icon-warning">
            <i class='bx bxs-school'></i>
        </div>
        <div class="stat-body master-stat-body">
            <div class="stat-label master-stat-label">Rombongan Belajar</div>
            <div class="stat-value master-stat-value">{{ $stats['total_classes'] }}</div>
            <div class="stat-sub master-stat-sub">Kelas aktif saat ini</div>
        </div>
    </div>

    <div class="stat-card master-stat-card">
        <div class="stat-icon-wrap master-stat-icon-wrap stat-icon-purple">
            <i class='bx bx-book-bookmark'></i>
        </div>
        <div class="stat-body master-stat-body">
            <div class="stat-label master-stat-label">Kurikulum Sekolah</div>
            <div class="stat-value master-stat-value">{{ $stats['total_curriculums'] }}</div>
            <div class="stat-sub master-stat-sub">Multi-jenjang aktif</div>
        </div>
    </div>

    <div class="stat-card master-stat-card">
        <div class="stat-icon-wrap master-stat-icon-wrap stat-icon-success">
            <i class='bx bx-calendar-check'></i>
        </div>
        <div class="stat-body master-stat-body">
            <div class="stat-label master-stat-label">Tahun Ajaran Aktif</div>
            <div class="stat-value master-stat-value" style="font-size:1.15rem;">{{ $stats['active_year'] }}</div>
            <div class="stat-sub master-stat-sub">
                <span class="stat-dot stat-dot-success"></span> Berlaku sekarang
            </div>
        </div>
    </div>

    <div class="stat-card master-stat-card">
        <div class="stat-icon-wrap master-stat-icon-wrap stat-icon-info">
            <i class='bx bx-time-five'></i>
        </div>
        <div class="stat-body master-stat-body">
            <div class="stat-label master-stat-label">Pola Jadwal Aktif</div>
            <div class="stat-value master-stat-value" style="font-size:1.05rem;" title="{{ $stats['active_schedule_cat'] }}">{{ $stats['active_schedule_cat'] }}</div>
            <div class="stat-sub master-stat-sub">
                <span class="stat-dot stat-dot-success"></span> Multi-kategori KBM
            </div>
        </div>
    </div>
</div>

<!-- Active Master Data Section Header / Quick Indicator -->
<div class="master-active-indicator">
    <div class="d-flex align-center gap-1">
        <div id="activeSectionIcon" class="master-indicator-icon">
            <i class='bx bx-calendar'></i>
        </div>
        <div>
            <div style="font-size: 0.73rem; text-transform: uppercase; letter-spacing: 0.06em; color: var(--text-muted); font-weight: 600;">Menu Data Master Aktif (Sidebar)</div>
            <h3 id="activeSectionTitle" style="font-size: 1.15rem; font-weight: 700; color: var(--text-heading); margin: 0.1rem 0 0;">Tahun Ajaran & Semester</h3>
        </div>
    </div>
    <div class="d-flex align-center gap-1">
        <span class="vx-badge badge-primary" id="activeSectionCount" style="padding: 0.35rem 0.85rem; font-size: 0.82rem;">
            {{ $academicYears->count() }} Data Terdaftar
        </span>
    </div>
</div>

<!-- Apex Segmented Horizontal Tabs Navigation -->
<div class="apex-tabs-nav">
    <button type="button" class="apex-tab-pill side-tab-link" id="side-tab-schools" onclick="switchTab('tab-schools')">
        <i class='bx bxs-institution'></i> Satuan Pendidikan
    </button>
    <button type="button" class="apex-tab-pill side-tab-link active" id="side-tab-years" onclick="switchTab('tab-years')">
        <i class='bx bx-calendar'></i> Tahun Ajaran
    </button>
    <button type="button" class="apex-tab-pill side-tab-link" id="side-tab-curriculums" onclick="switchTab('tab-curriculums')">
        <i class='bx bx-book-bookmark'></i> Kurikulum
    </button>
    @if(($school->level ?? '') === 'SMK' || ($isVocational ?? false))
    <button type="button" class="apex-tab-pill side-tab-link" id="side-tab-vocational" onclick="switchTab('tab-vocational')">
        <i class='bx bx-wrench'></i> Spektrum SMK
    </button>
    @endif
    <button type="button" class="apex-tab-pill side-tab-link" id="side-tab-schedules" onclick="switchTab('tab-schedules')">
        <i class='bx bx-time'></i> Pola Jam KBM
    </button>
    <button type="button" class="apex-tab-pill side-tab-link" id="side-tab-teachers" onclick="switchTab('tab-teachers')">
        <i class='bx bxs-chalkboard'></i> Guru & GTK
    </button>
    <button type="button" class="apex-tab-pill side-tab-link" id="side-tab-students" onclick="switchTab('tab-students')">
        <i class='bx bxs-user-detail'></i> Peserta Didik (4x6)
    </button>
    <button type="button" class="apex-tab-pill side-tab-link" id="side-tab-classes" onclick="switchTab('tab-classes')">
        <i class='bx bxs-school'></i> Rombongan Belajar
    </button>
    <button type="button" class="apex-tab-pill side-tab-link" id="side-tab-subjects" onclick="switchTab('tab-subjects')">
        <i class='bx bx-book-open'></i> Mapel & Jadwal
    </button>
    <button type="button" class="apex-tab-pill side-tab-link" id="side-tab-parents" onclick="switchTab('tab-parents')">
        <i class='bx bxs-group'></i> Orang Tua / Wali
    </button>
</div>

<!-- Tab Content Area -->
<div style="background: transparent; min-height: 400px;">

    <!-- TAB 0: SATUAN PENDIDIKAN (DAPODIK & MULTI-SEKOLAH) -->
    <div id="tab-schools" class="tab-pane">
        <!-- School Level Info & Adaptation Banner -->
        <div class="vx-card" style="margin-bottom: 1.5rem; border-left: 4px solid var(--vx-primary); background: linear-gradient(to right, var(--vx-primary-light), transparent);">
            <div class="vx-card-body" style="padding: 1.25rem 1.5rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                    <div style="display: flex; align-items: center; gap: 1rem;">
                        <div style="width: 52px; height: 52px; border-radius: 14px; background: {{ ($school->level ?? '') === 'SMK' ? '#F59E0B' : (($school->level ?? '') === 'SMA' ? '#06B6D4' : (($school->level ?? '') === 'SMP' ? '#4F46E5' : '#10B981')) }}; color: #fff; display: flex; align-items: center; justify-content: center; font-size: 1.6rem; font-weight: 800; box-shadow: 0 4px 12px rgba(0,0,0,0.12);">
                            {{ $school->level ?? 'SK' }}
                        </div>
                        <div>
                            <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
                                <h2 style="font-size: 1.3rem; font-weight: 700; color: var(--vx-text-heading); margin: 0;">{{ $school->name ?? 'Satuan Pendidikan' }}</h2>
                                <span class="vx-badge {{ ($school->level ?? '') === 'SMK' ? 'badge-warning' : (($school->level ?? '') === 'SMA' ? 'badge-info' : (($school->level ?? '') === 'SMP' ? 'badge-primary' : 'badge-success')) }}" style="font-size: 0.72rem; padding: 0.2rem 0.6rem;">
                                    Bentuk Pendidikan: {{ $school->level ?? '-' }}
                                </span>
                                <span class="vx-badge badge-success" style="font-size: 0.72rem; padding: 0.2rem 0.6rem;">
                                    Status: {{ $school->school_status ?? 'Negeri' }}
                                </span>
                                <span class="vx-badge badge-primary" style="font-size: 0.72rem; padding: 0.2rem 0.6rem;">
                                    Akreditasi: {{ $school->accreditation ?? 'A' }}
                                </span>
                            </div>
                            <div style="font-size: 0.84rem; color: var(--vx-text-muted); margin-top: 0.25rem;">
                                <span><strong>NPSN:</strong> {{ $school->npsn ?? '-' }}</span> &bull;
                                <span><strong>Kepala Sekolah:</strong> {{ $school->principal_name ?? '-' }} (NIP: {{ $school->principal_nip ?? '-' }})</span>
                            </div>
                        </div>
                    </div>
                    <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                        <button class="btn btn-primary" onclick="openModal('modalAddSchool')">
                            <i class='bx bx-plus-circle'></i> Tambah Satuan Pendidikan Baru
                        </button>
                    </div>
                </div>

                <!-- Penyesuaian Fitur Otomatis Berdasarkan Jenjang -->
                <div style="margin-top: 1rem; padding: 0.85rem 1.1rem; border-radius: 8px; background: rgba(115,103,240,0.06); border: 1px dashed rgba(115,103,240,0.3); font-size: 0.82rem; color: var(--vx-text-body); line-height: 1.5;">
                    <strong style="color: var(--vx-primary);"><i class='bx bx-check-shield'></i> Penyesuaian Otomatis Modul & Menu Sistem:</strong>
                    @if(($school->level ?? '') === 'SMK')
                        Sekolah ini bertipe <strong>SMK (Sekolah Menengah Kejuruan)</strong>. Sistem otomatis mengaktifkan menu <strong>Spektrum Keahlian SMK</strong> (Bidang Keahlian, Program Keahlian 3-4 Tahun, Konsentrasi Keahlian Fase F), Rombel Rujukan Kejuruan, serta kemitraan DUDI & PKL.
                    @elseif(($school->level ?? '') === 'SMA')
                        Sekolah ini bertipe <strong>SMA (Sekolah Menengah Atas)</strong>. Sistem otomatis menyesuaikan tingkat 10-12 (Fase E & F), rombel peminatan akademik (MIPA, IPS, Bahasa), dan menyembunyikan modul spektrum kejuruan SMK.
                    @elseif(($school->level ?? '') === 'SMP')
                        Sekolah ini bertipe <strong>SMP (Sekolah Menengah Pertama)</strong>. Sistem otomatis menyesuaikan tingkat 7-9 (Fase D), kurikulum mata pelajaran umum SMP, dan menyembunyikan penjurusan.
                    @elseif(($school->level ?? '') === 'SD')
                        Sekolah ini bertipe <strong>SD (Sekolah Dasar)</strong>. Sistem otomatis menyesuaikan tingkat 1-6 (Fase A: Kls 1-2, Fase B: Kls 3-4, Fase C: Kls 5-6), struktur Guru Kelas, dan menyembunyikan penjurusan.
                    @else
                        Sistem berjalan dalam mode umum multi-jenjang pendidikan.
                    @endif
                </div>
            </div>
        </div>

        <!-- Detail Profil Dapodik Grid -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
            <!-- Data Dokumen & Legalitas -->
            <div class="vx-card" style="margin-bottom: 0;">
                <div class="vx-card-header">
                    <h3 class="vx-card-title"><i class='bx bx-file-blank text-primary'></i> Dokumen Legalitas & Perizinan (Dapodik)</h3>
                </div>
                <div class="vx-card-body" style="padding: 1rem 1.5rem;">
                    <table class="clean-table" style="font-size: 0.84rem;">
                        <tbody>
                            <tr>
                                <td style="width: 40%; color: var(--vx-text-muted); font-weight: 600;">Nomor SK Pendirian</td>
                                <td class="fw-600">{{ $school->establishment_decree ?? '01/SK/PEND/1968' }}</td>
                            </tr>
                            <tr>
                                <td style="color: var(--vx-text-muted); font-weight: 600;">Tanggal SK Pendirian</td>
                                <td>{{ $school->establishment_date ?? '1968-01-15' }}</td>
                            </tr>
                            <tr>
                                <td style="color: var(--vx-text-muted); font-weight: 600;">SK Izin Operasional</td>
                                <td class="fw-600">{{ $school->operational_license_decree ?? '421.5/DISDIK/2010' }}</td>
                            </tr>
                            <tr>
                                <td style="color: var(--vx-text-muted); font-weight: 600;">Tanggal SK Operasional</td>
                                <td>{{ $school->operational_license_date ?? '2010-06-20' }}</td>
                            </tr>
                            <tr>
                                <td style="color: var(--vx-text-muted); font-weight: 600;">NPWP Satuan Pendidikan</td>
                                <td class="cell-mono">{{ $school->tax_number ?? '00.123.456.7-015.000' }}</td>
                            </tr>
                            <tr>
                                <td style="color: var(--vx-text-muted); font-weight: 600;">Paket Langganan Platform</td>
                                <td><span class="vx-badge badge-primary">{{ ucfirst($school->subscription ?? 'Enterprise') }}</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Lokasi & Kontak Dapodik -->
            <div class="vx-card" style="margin-bottom: 0;">
                <div class="vx-card-header">
                    <h3 class="vx-card-title"><i class='bx bx-map-pin text-primary'></i> Wilayah, Titik Koordinat & Kontak</h3>
                </div>
                <div class="vx-card-body" style="padding: 1rem 1.5rem;">
                    <table class="clean-table" style="font-size: 0.84rem;">
                        <tbody>
                            <tr>
                                <td style="width: 40%; color: var(--vx-text-muted); font-weight: 600;">Alamat Lengkap</td>
                                <td class="fw-600">{{ $school->address ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td style="color: var(--vx-text-muted); font-weight: 600;">Wilayah / RT RW</td>
                                <td>RT {{ $school->rt_rw ?? '005/002' }}, Dusun {{ $school->sub_village ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td style="color: var(--vx-text-muted); font-weight: 600;">Desa/Kelurahan - Kec.</td>
                                <td>{{ $school->village ?? 'Pasar Baru' }}, Kec. {{ $school->subdistrict ?? 'Sawah Besar' }}</td>
                            </tr>
                            <tr>
                                <td style="color: var(--vx-text-muted); font-weight: 600;">Kab/Kota - Provinsi</td>
                                <td>{{ $school->city ?? 'Jakarta Pusat' }}, {{ $school->province ?? 'DKI Jakarta' }} ({{ $school->postal_code ?? '10710' }})</td>
                            </tr>
                            <tr>
                                <td style="color: var(--vx-text-muted); font-weight: 600;">Titik Koordinat GPS</td>
                                <td class="cell-mono">Lat: {{ $school->latitude ?? '-6.1685' }}, Long: {{ $school->longitude ?? '106.8378' }}</td>
                            </tr>
                            <tr>
                                <td style="color: var(--vx-text-muted); font-weight: 600;">Telepon / Fax / Web</td>
                                <td>{{ $school->phone ?? '-' }} &bull; <a href="{{ $school->website ?? '#' }}" target="_blank" class="text-primary">{{ $school->website ?? '-' }}</a></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Pengaturan Jadwal Pemutakhiran Biodata Siswa Mandiri -->
        <div class="vx-card" style="margin-bottom: 1.5rem; border-left: 4px solid var(--vx-info); background: #ffffff;">
            <div class="vx-card-header" style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
                <h3 class="vx-card-title" style="margin:0; font-size:1.05rem;">
                    <i class='bx bx-user-check text-info'></i> Pengaturan Pemutakhiran Profil Siswa Mandiri (Dapodik)
                </h3>
                <span id="badgeWindowStatus" class="vx-badge {{ $school->isStudentProfileEditOpen() ? 'badge-success' : 'badge-warning' }}" style="padding: 0.35rem 0.8rem; font-size: 0.8rem;">
                    <i class='bx {{ $school->isStudentProfileEditOpen() ? "bx-lock-open-alt" : "bx-lock-alt" }}'></i>
                    {{ $school->isStudentProfileEditOpen() ? 'Periode Pemutakhiran Dibuka' : 'Periode Pemutakhiran Ditutup' }}
                </span>
            </div>
            <div class="vx-card-body" style="padding: 1.25rem 1.5rem;">
                <p style="font-size: 0.85rem; color: var(--vx-text-muted); margin-bottom: 1.2rem;">
                    Atur izin dan rentang tanggal kapan peserta didik dapat memperbarui identitas, kontak, pasfoto resmi 4x6, dan titik koordinat GPS rumah secara mandiri dari akun masing-masing.
                </p>
                <form id="formProfileEditWindow" onsubmit="submitProfileEditWindow(event)">
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)) 160px; gap: 1rem; align-items: flex-end;">
                        <div>
                            <label class="vx-label" style="font-size: 0.82rem; font-weight: 600;">Status Akses Siswa</label>
                            <select class="vx-input form-input-clean" id="cfgEditEnabled" name="enabled" style="font-size: 0.85rem;">
                                <option value="1" {{ $school->student_profile_edit_enabled ? 'selected' : '' }}>Izinkan Siswa Edit Biodata (Buka)</option>
                                <option value="0" {{ !$school->student_profile_edit_enabled ? 'selected' : '' }}>Kunci / Nonaktifkan Akses Edit (Tutup)</option>
                            </select>
                        </div>
                        <div>
                            <label class="vx-label" style="font-size: 0.82rem; font-weight: 600;">Tanggal Mulai</label>
                            <input type="date" class="vx-input form-input-clean" id="cfgEditStart" name="start_date" value="{{ $school->student_profile_edit_start?->toDateString() }}" style="font-size: 0.85rem;">
                        </div>
                        <div>
                            <label class="vx-label" style="font-size: 0.82rem; font-weight: 600;">Tanggal Berakhir</label>
                            <input type="date" class="vx-input form-input-clean" id="cfgEditEnd" name="end_date" value="{{ $school->student_profile_edit_end?->toDateString() }}" style="font-size: 0.85rem;">
                        </div>
                        <div>
                            <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center; font-size: 0.85rem;">
                                <i class='bx bx-save'></i> Simpan Jadwal
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Daftar Semua Satuan Pendidikan (Multi-Tenancy) -->
        <div class="panel" style="border-radius: var(--vx-radius-xl);">
            <div class="panel-header">
                <div class="panel-title">
                    <h2>Daftar Satuan Pendidikan Terdaftar (Multi-Sekolah)</h2>
                    <p>Pilih sekolah untuk beralih instansi dan mengelola data master masing-masing jenjang.</p>
                </div>
                <div class="panel-toolbar">
                    <button class="btn btn-primary btn-sm" onclick="openModal('modalAddSchool')">
                        <i class='bx bx-plus'></i> Tambah Satuan Pendidikan
                    </button>
                </div>
            </div>
            <div class="table-wrap">
                <table class="clean-table">
                    <thead>
                        <tr>
                            <th>Satuan Pendidikan</th>
                            <th>NPSN</th>
                            <th>Bentuk & Status</th>
                            <th>Akreditasi</th>
                            <th>Kepala Sekolah</th>
                            <th>Wilayah (Kota/Kab)</th>
                            <th>Kapasitas KBM</th>
                            <th style="text-align: right;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(isset($availableSchools))
                            @foreach($availableSchools as $item)
                                <tr style="{{ ($school->id ?? 1) == $item->id ? 'background: var(--vx-primary-light);' : '' }}">
                                    <td>
                                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                                            <div style="width: 38px; height: 38px; border-radius: 10px; background: {{ $item->level === 'SMK' ? '#F59E0B' : ($item->level === 'SMA' ? '#06B6D4' : ($item->level === 'SMP' ? '#4F46E5' : '#10B981')) }}; color: #fff; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 0.9rem; flex-shrink: 0; box-shadow: 0 2px 6px rgba(0,0,0,0.1);">
                                                {{ $item->level }}
                                            </div>
                                            <div>
                                                <div class="cell-title">{{ $item->name }}</div>
                                                <div class="cell-sub">Tenant ID: <code>{{ $item->tenant_id }}</code></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td><span class="cell-mono fw-600">{{ $item->npsn }}</span></td>
                                    <td>
                                        <span class="vx-badge {{ $item->level === 'SMK' ? 'badge-warning' : ($item->level === 'SMA' ? 'badge-info' : ($item->level === 'SMP' ? 'badge-primary' : 'badge-success')) }}">
                                            {{ $item->level }}
                                        </span>
                                        <span class="vx-badge" style="background: rgba(47,51,73,0.06); color: var(--vx-text-heading);">
                                            {{ $item->school_status ?? 'Negeri' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="vx-badge badge-primary">Akreditasi {{ $item->accreditation ?? 'A' }}</span>
                                    </td>
                                    <td>
                                        <div style="font-size: 0.84rem; font-weight: 600;">{{ $item->principal_name ?? '-' }}</div>
                                        <div class="cell-sub">NIP: {{ $item->principal_nip ?? '-' }}</div>
                                    </td>
                                    <td>
                                        <div style="font-size: 0.84rem;">{{ $item->city ?? 'Jakarta Pusat' }}</div>
                                        <div class="cell-sub">{{ $item->province ?? 'DKI Jakarta' }}</div>
                                    </td>
                                    <td>
                                        <span class="cell-sub" style="font-weight: 600;">
                                            {{ $item->classes_count ?? 0 }} Rombel &bull; {{ $item->students_count ?? 0 }} Siswa
                                        </span>
                                    </td>
                                    <td style="text-align: right;">
                                        @if(($school->id ?? 1) == $item->id)
                                            <span class="vx-badge badge-success" style="padding: 0.35rem 0.75rem; font-size: 0.78rem;">
                                                <i class='bx bx-check-circle'></i> Sedang Aktif
                                            </span>
                                        @else
                                            <a href="{{ route('admin.switch-school.get', $item->id) }}" class="btn btn-sm btn-primary-light">
                                                <i class='bx bx-sync'></i> Beralih ke Sekolah Ini
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- TAB 1: TAHUN AJARAN -->
    <div id="tab-years" class="tab-pane active">
        <div class="panel" style="margin: 0 0 1.5rem 0; border-radius: var(--vx-radius-xl);">
            <div class="panel-header">
                <div class="panel-title">
                    <h2>Tahun Ajaran & Semester</h2>
                    <p>Daftar kalender akademik dan periode semester ganjil/genap.</p>
                </div>
                <div class="panel-toolbar">
                    <button class="btn btn-primary btn-sm" onclick="openModal('modalAddYear')">
                        <i class='bx bx-plus'></i> Tambah Tahun Ajaran
                    </button>
                </div>
            </div>
            <div class="table-wrap">
                <table class="clean-table">
                    <thead>
                        <tr>
                            <th>Tahun Ajaran</th>
                            <th>Rentang Tanggal</th>
                            <th>Semester Ganjil</th>
                            <th>Semester Genap</th>
                            <th>Status</th>
                            <th style="text-align: right;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($academicYears as $year)
                        <tr>
                            <td>
                                <div class="cell-title">{{ $year->name }}</div>
                                <div class="cell-sub cell-mono">ID #{{ $year->id }}</div>
                            </td>
                            <td>
                                <span style="font-weight: 500; color: var(--vx-text-heading);">
                                    {{ $year->start_date->format('d M Y') }} — {{ $year->end_date->format('d M Y') }}
                                </span>
                            </td>
                            <td>
                                @php $odd = $year->semesters->firstWhere('semester', 'odd'); @endphp
                                @if($odd)
                                    <span class="vx-badge {{ $odd->is_active ? 'badge-success' : 'badge-muted' }}">
                                        {{ $odd->is_active ? 'Aktif' : 'Non-aktif' }}
                                    </span>
                                    <div class="cell-sub">{{ \Carbon\Carbon::parse($odd->start_date)->format('d M') }} - {{ \Carbon\Carbon::parse($odd->end_date)->format('d M Y') }}</div>
                                @else
                                    <span class="vx-badge badge-muted">—</span>
                                @endif
                            </td>
                            <td>
                                @php $even = $year->semesters->firstWhere('semester', 'even'); @endphp
                                @if($even)
                                    <span class="vx-badge {{ $even->is_active ? 'badge-success' : 'badge-muted' }}">
                                        {{ $even->is_active ? 'Aktif' : 'Non-aktif' }}
                                    </span>
                                    <div class="cell-sub">{{ \Carbon\Carbon::parse($even->start_date)->format('d M') }} - {{ \Carbon\Carbon::parse($even->end_date)->format('d M Y') }}</div>
                                @else
                                    <span class="vx-badge badge-muted">—</span>
                                @endif
                            </td>
                            <td>
                                @if($year->is_active)
                                    <span class="vx-badge badge-success">
                                        <span class="badge-dot"></span> Aktif
                                    </span>
                                @else
                                    <span class="vx-badge badge-muted">Tidak Aktif</span>
                                @endif
                            </td>
                            <td style="text-align: right;">
                                @if(!$year->is_active)
                                    <button class="btn btn-secondary btn-sm" onclick="activateAcademicYear({{ $year->id }})">
                                        Set Aktif
                                    </button>
                                @else
                                    <span style="font-size: 0.78rem; color: var(--vx-success); font-weight: 600;">
                                        <i class='bx bx-check-circle'></i> Periode Utama
                                    </span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 3rem; color: var(--vx-text-muted);">
                                <i class='bx bx-calendar-x' style="font-size: 2.5rem; display: block; margin-bottom: 0.75rem;"></i>
                                Belum ada data tahun ajaran. Klik <strong>Tambah Tahun Ajaran</strong> untuk memulai.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- TAB: KURIKULUM & PEMETAAN JENJANG (MULTI-JENJANG) -->
    <div id="tab-curriculums" class="tab-pane">
        @php
            $activeGradeLevels = $classes->pluck('grade_level')
                ->merge($gradeLevelMappings->pluck('grade_level'))
                ->filter()
                ->unique()
                ->sort()
                ->values();

            if ($activeGradeLevels->isEmpty()) {
                $activeGradeLevels = collect(match(strtoupper($school->level ?? 'SMA')) {
                    'SD', 'MI' => [1, 2, 3, 4, 5, 6],
                    'SMP', 'MTS' => [7, 8, 9],
                    default => [10, 11, 12],
                });
            }

            $romanMap = [
                1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI',
                7 => 'VII', 8 => 'VIII', 9 => 'IX',
                10 => 'X', 11 => 'XI', 12 => 'XII', 13 => 'XIII'
            ];
            $mappedGradesCount = $gradeLevelMappings->whereNotNull('curriculum_id')->count();
        @endphp

        <!-- Top Banner / Intro Hero -->
        <div class="panel" style="margin-bottom: 1.5rem; border-radius: var(--vx-radius-xl); background: linear-gradient(135deg, rgba(115, 103, 240, 0.08) 0%, rgba(115, 103, 240, 0.02) 100%); border-left: 4px solid var(--vx-primary);">
            <div style="padding: 1.25rem 1.5rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                <div>
                    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.35rem;">
                        <span class="vx-badge badge-primary" style="font-size: 0.72rem; padding: 0.2rem 0.55rem;">
                            <i class='bx bx-git-branch'></i> Multi-Kurikulum Aktif
                        </span>
                        <span class="vx-badge badge-success" style="font-size: 0.72rem; padding: 0.2rem 0.55rem;">
                            <span class="badge-dot"></span> {{ $mappedGradesCount }} dari {{ $activeGradeLevels->count() }} Jenjang Terpetakan
                        </span>
                    </div>
                    <h2 style="font-size: 1.2rem; font-weight: 700; color: var(--vx-text-heading); margin: 0 0 0.35rem 0;">
                        <i class='bx bx-book-bookmark' style="color: var(--vx-primary); margin-right: 0.35rem;"></i>
                        Kurikulum Operasional & Pemetaan Multi-Jenjang
                    </h2>
                    <p style="font-size: 0.85rem; color: var(--vx-text-muted); margin: 0; max-width: 820px; line-height: 1.5;">
                        Sistem mendukung pelaksanaan beberapa kurikulum secara simultan (misal Tingkat 10 & 11 Kurikulum Merdeka, Tingkat 12 Kurikulum 2013). Seluruh rombel di suatu jenjang otomatis tersinkronisasi ke kurikulum dan struktur JP yang ditetapkan.
                    </p>
                </div>
                <div class="d-flex gap-1" style="flex-wrap: wrap;">
                    <button class="btn btn-secondary btn-sm" onclick="openModal('modalAssignGradeCurriculum')">
                        <i class='bx bx-git-branch'></i> Petakan Jenjang
                    </button>
                    <button class="btn btn-primary btn-sm" onclick="openModal('modalAddCurriculum')">
                        <i class='bx bx-plus'></i> Tambah Kurikulum
                    </button>
                </div>
            </div>
        </div>

        <!-- 1. Penerapan Kurikulum per Jenjang (Grade Level) - Bento & Matrix View -->
        <div class="panel" style="margin-bottom: 1.75rem; border-radius: var(--vx-radius-xl); box-shadow: 0 4px 18px rgba(0,0,0,0.03);">
            <div class="panel-header" style="border-bottom: 1px solid var(--vx-border-light); padding: 1.25rem 1.5rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                <div class="panel-title">
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <div style="width: 32px; height: 32px; border-radius: 8px; background: rgba(115, 103, 240, 0.12); color: var(--vx-primary); display: flex; align-items: center; justify-content: center; font-size: 1.15rem;">
                            <i class='bx bx-sitemap'></i>
                        </div>
                        <div>
                            <h3 style="font-size: 1.05rem; font-weight: 700; margin: 0; color: var(--vx-text-heading);">Penerapan Kurikulum per Jenjang (Grade Level)</h3>
                            <p style="font-size: 0.8rem; color: var(--vx-text-muted); margin: 0.15rem 0 0;">
                                Pemetaan kurikulum operasional yang aktif diberlakukan untuk setiap tingkatan kelas di sekolah.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- View Switcher & Action -->
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                    <div class="segmented-control" style="background: var(--vx-body-bg); padding: 3px; border-radius: 8px; border: 1px solid var(--vx-border-light); display: inline-flex;">
                        <button type="button" id="btnGradeViewBento" class="btn-seg active" onclick="toggleGradeView('bento')" style="padding: 0.35rem 0.75rem; font-size: 0.78rem; font-weight: 600; border: none; border-radius: 6px; cursor: pointer; transition: all 0.2s; background: var(--vx-card-bg); color: var(--vx-primary); box-shadow: 0 1px 4px rgba(0,0,0,0.06);">
                            <i class='bx bx-grid-alt'></i> Kartu Bento
                        </button>
                        <button type="button" id="btnGradeViewTable" class="btn-seg" onclick="toggleGradeView('table')" style="padding: 0.35rem 0.75rem; font-size: 0.78rem; font-weight: 600; border: none; border-radius: 6px; cursor: pointer; transition: all 0.2s; background: transparent; color: var(--vx-text-muted);">
                            <i class='bx bx-list-ul'></i> Matriks Tabel
                        </button>
                    </div>
                    <button class="btn btn-outline-primary btn-sm" onclick="openModal('modalAssignGradeCurriculum')" style="border-radius: 8px;">
                        <i class='bx bx-edit-alt'></i> Ubah Pemetaan
                    </button>
                </div>
            </div>

            <!-- Bento Cards Container -->
            <div id="curriculumGradeBentoView" style="padding: 1.5rem; display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 1.25rem;">
                @foreach($activeGradeLevels as $gLevel)
                    @php
                        $roman = $romanMap[$gLevel] ?? (string)$gLevel;
                        $mapping = $gradeLevelMappings->firstWhere('grade_level', $gLevel);
                        $assignedCurriculum = $mapping?->curriculum;
                        $classesInGrade = $classes->where('grade_level', $gLevel);

                        $isMerdeka = $assignedCurriculum && (str_contains(strtolower($assignedCurriculum->name), 'merdeka') || str_contains(strtolower($assignedCurriculum->code), 'merdeka'));
                        $isK13 = $assignedCurriculum && (str_contains(strtolower($assignedCurriculum->name), '2013') || str_contains(strtolower($assignedCurriculum->code), 'k13'));

                        if ($isMerdeka) {
                            $themeGrad = 'linear-gradient(135deg, #10b981 0%, #059669 100%)';
                            $themeLightBg = 'rgba(16, 185, 129, 0.04)';
                            $themeBorder = 'rgba(16, 185, 129, 0.22)';
                            $themeColor = '#10b981';
                            $themeIcon = 'bx-compass';
                            $curTypeLabel = 'Kurikulum Merdeka';
                            $defaultPhase = $gLevel == 10 ? 'Fase E (Umum)' : 'Fase F (Peminatan / Konsentrasi)';
                        } elseif ($isK13) {
                            $themeGrad = 'linear-gradient(135deg, #3b82f6 0%, #2563eb 100%)';
                            $themeLightBg = 'rgba(59, 130, 246, 0.04)';
                            $themeBorder = 'rgba(59, 130, 246, 0.22)';
                            $themeColor = '#3b82f6';
                            $themeIcon = 'bx-book-open';
                            $curTypeLabel = 'Kurikulum 2013 Revisi';
                            $defaultPhase = 'Tingkat Akhir';
                        } elseif ($assignedCurriculum) {
                            $themeGrad = 'linear-gradient(135deg, #7367f0 0%, #5e50ee 100%)';
                            $themeLightBg = 'rgba(115, 103, 240, 0.04)';
                            $themeBorder = 'rgba(115, 103, 240, 0.22)';
                            $themeColor = '#7367f0';
                            $themeIcon = 'bx-book-bookmark';
                            $curTypeLabel = 'Kurikulum Operasional';
                            $defaultPhase = 'Reguler';
                        } else {
                            $themeGrad = 'linear-gradient(135deg, #94a3b8 0%, #64748b 100%)';
                            $themeLightBg = 'rgba(241, 245, 249, 0.6)';
                            $themeBorder = 'rgba(203, 213, 225, 0.8)';
                            $themeColor = '#64748b';
                            $themeIcon = 'bx-help-circle';
                            $curTypeLabel = 'Belum Ditetapkan';
                            $defaultPhase = '—';
                        }

                        $displayPhase = $mapping?->phase ?: $defaultPhase;
                        $totalSubjects = $assignedCurriculum ? $assignedCurriculum->curriculumSubjects->count() : 0;
                        $totalHours = $assignedCurriculum ? $assignedCurriculum->curriculumSubjects->sum('hours_per_week') : 0;
                    @endphp

                    <div class="grade-bento-card" style="background: var(--vx-card-bg); border: 1px solid {{ $themeBorder }}; border-radius: var(--vx-radius-lg); padding: 1.35rem; display: flex; flex-direction: column; justify-content: space-between; position: relative; transition: all 0.25s ease; box-shadow: 0 2px 10px rgba(0,0,0,0.02);">
                        <div>
                            <!-- Header: Grade Badge & Phase -->
                            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem;">
                                <div style="display: flex; align-items: center; gap: 0.85rem;">
                                    <div style="width: 48px; height: 48px; border-radius: 14px; background: {{ $themeGrad }}; color: #ffffff; display: flex; flex-direction: column; align-items: center; justify-content: center; font-weight: 800; box-shadow: 0 4px 12px rgba(0,0,0,0.12);">
                                        <span style="font-size: 1.25rem; line-height: 1;">{{ $roman }}</span>
                                        <span style="font-size: 0.6rem; opacity: 0.9; text-transform: uppercase; letter-spacing: 0.05em; font-weight: 600;">Grade</span>
                                    </div>
                                    <div>
                                        <div style="display: flex; align-items: center; gap: 0.4rem;">
                                            <span style="font-size: 0.72rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: {{ $themeColor }};">
                                                Tingkat {{ $gLevel }}
                                            </span>
                                            <span style="font-size: 0.7rem; color: var(--vx-text-muted);">•</span>
                                            <span class="vx-badge" style="background: {{ $themeLightBg }}; color: {{ $themeColor }}; font-size: 0.7rem; padding: 0.15rem 0.5rem; font-weight: 600; border: 1px solid {{ $themeBorder }};">
                                                {{ $displayPhase }}
                                            </span>
                                        </div>
                                        <div style="font-size: 1.1rem; font-weight: 700; color: var(--vx-text-heading); margin-top: 0.2rem;">
                                            Kelas {{ $roman }}
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    @if($assignedCurriculum)
                                        <span class="vx-badge badge-success" style="font-size: 0.72rem; padding: 0.2rem 0.55rem;">
                                            <span class="badge-dot"></span> Aktif
                                        </span>
                                    @else
                                        <span class="vx-badge badge-warning" style="font-size: 0.72rem; padding: 0.2rem 0.55rem;">
                                            <span class="badge-dot"></span> Belum Diatur
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <!-- Curriculum Operasional Hero Box -->
                            <div style="background: {{ $themeLightBg }}; border: 1px solid {{ $themeBorder }}; border-radius: var(--vx-radius-md); padding: 1rem; margin-bottom: 1rem;">
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.4rem;">
                                    <span style="font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: {{ $themeColor }};">
                                        <i class='bx {{ $themeIcon }}'></i> {{ $curTypeLabel }}
                                    </span>
                                    @if($assignedCurriculum)
                                        <span class="cell-mono vx-badge" style="background: var(--vx-card-bg); font-size: 0.68rem; color: var(--vx-text-heading); border: 1px solid var(--vx-border-light);">
                                            {{ $assignedCurriculum->code }}
                                        </span>
                                    @endif
                                </div>

                                @if($assignedCurriculum)
                                    <div style="font-size: 0.98rem; font-weight: 700; color: var(--vx-text-heading); line-height: 1.35; margin-bottom: 0.65rem;">
                                        {{ $assignedCurriculum->name }}
                                    </div>
                                    <div style="display: flex; align-items: center; gap: 0.75rem; font-size: 0.78rem; color: var(--vx-text-muted); flex-wrap: wrap;">
                                        <div style="display: flex; align-items: center; gap: 0.3rem;">
                                            <i class='bx bx-time-five' style="color: {{ $themeColor }};"></i>
                                            <span><strong>{{ $totalHours }}</strong> JP/Minggu</span>
                                        </div>
                                        <div style="display: flex; align-items: center; gap: 0.3rem;">
                                            <i class='bx bx-book-content' style="color: {{ $themeColor }};"></i>
                                            <span><strong>{{ $totalSubjects }}</strong> Mata Pelajaran</span>
                                        </div>
                                    </div>
                                @else
                                    <div style="padding: 0.5rem 0; font-size: 0.82rem; color: var(--vx-text-muted); font-style: italic;">
                                        <i class='bx bx-error-circle' style="color: var(--vx-warning); margin-right: 0.3rem;"></i>
                                        Jenjang ini belum memiliki kurikulum operasional yang ditetapkan.
                                    </div>
                                @endif
                            </div>

                            <!-- Rombongan Belajar (Rombel) Section -->
                            <div style="background: var(--vx-body-bg); border: 1px solid var(--vx-border-light); border-radius: var(--vx-radius-md); padding: 0.85rem 1rem; margin-bottom: 1.15rem;">
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                                    <span style="font-size: 0.75rem; font-weight: 600; color: var(--vx-text-muted);">
                                        <i class='bx bx-group'></i> Rombel Terkait:
                                    </span>
                                    <strong style="font-size: 0.78rem; color: var(--vx-text-heading);">
                                        {{ $classesInGrade->count() }} Rombongan Belajar
                                    </strong>
                                </div>

                                <div style="display: flex; flex-wrap: wrap; gap: 0.35rem; align-items: center;">
                                    @forelse($classesInGrade as $c)
                                        <span class="vx-badge" style="background: var(--vx-card-bg); border: 1px solid var(--vx-border-light); font-size: 0.72rem; padding: 0.2rem 0.5rem; font-weight: 600; color: var(--vx-text-heading);">
                                            {{ $c->name }}
                                        </span>
                                    @empty
                                        <span style="font-size: 0.75rem; color: var(--vx-text-muted); font-style: italic;">
                                            Belum ada rombel terdaftar untuk tingkat ini.
                                        </span>
                                    @endforelse
                                </div>
                            </div>
                        </div>

                        <!-- Card Action Buttons -->
                        <div style="display: flex; gap: 0.5rem; border-top: 1px solid var(--vx-border-light); padding-top: 1rem;">
                            <button type="button" class="btn btn-secondary btn-sm" style="flex: 1; justify-content: center; font-size: 0.8rem; border-radius: 8px;" onclick="openAssignGradeModal({{ $gLevel }}, {{ $assignedCurriculum?->id ?? 'null' }}, '{{ $mapping?->phase ?? '' }}')">
                                <i class='bx bx-edit-alt'></i> Ubah Kurikulum
                            </button>
                            @if($assignedCurriculum)
                                <button type="button" class="btn btn-secondary btn-sm" style="padding: 0 0.75rem; font-size: 0.8rem; border-radius: 8px;" onclick="openCurriculumStructureDetailModal({{ $assignedCurriculum->id }})" title="Lihat & Kelola Struktur Mata Pelajaran">
                                    <i class='bx bx-list-check'></i> Struktur
                                </button>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Matriks Table View (Alternative) -->
            <div id="curriculumGradeTableView" style="display: none; padding: 1rem 1.5rem 1.5rem;">
                <div class="table-wrap" style="border: 1px solid var(--vx-border-light); border-radius: var(--vx-radius-md); overflow: hidden;">
                    <table class="clean-table" style="margin: 0;">
                        <thead>
                            <tr style="background: var(--vx-body-bg);">
                                <th style="width: 130px;">Jenjang / Tingkat</th>
                                <th style="width: 140px;">Fase Belajar</th>
                                <th>Kurikulum Operasional</th>
                                <th style="width: 120px; text-align: center;">Beban JP</th>
                                <th style="width: 120px; text-align: center;">Total Mapel</th>
                                <th>Rombel Terdaftar</th>
                                <th style="width: 110px; text-align: center;">Status</th>
                                <th style="width: 160px; text-align: right;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($activeGradeLevels as $gLevel)
                                @php
                                    $roman = $romanMap[$gLevel] ?? (string)$gLevel;
                                    $mapping = $gradeLevelMappings->firstWhere('grade_level', $gLevel);
                                    $assignedCurriculum = $mapping?->curriculum;
                                    $classesInGrade = $classes->where('grade_level', $gLevel);
                                    $totalSubjects = $assignedCurriculum ? $assignedCurriculum->curriculumSubjects->count() : 0;
                                    $totalHours = $assignedCurriculum ? $assignedCurriculum->curriculumSubjects->sum('hours_per_week') : 0;
                                @endphp
                                <tr>
                                    <td>
                                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                                            <span class="vx-badge badge-primary font-bold" style="font-size: 0.8rem; padding: 0.25rem 0.5rem;">
                                                {{ $roman }}
                                            </span>
                                            <div>
                                                <strong style="color: var(--vx-text-heading);">Tingkat {{ $gLevel }}</strong>
                                                <div style="font-size: 0.72rem; color: var(--vx-text-muted);">Kelas {{ $roman }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($mapping?->phase)
                                            <span class="vx-badge badge-info" style="font-size: 0.75rem;">{{ $mapping->phase }}</span>
                                        @else
                                            <span style="font-size: 0.75rem; color: var(--vx-text-muted);">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($assignedCurriculum)
                                            <div style="font-weight: 700; color: var(--vx-text-heading);">{{ $assignedCurriculum->name }}</div>
                                            <div class="cell-mono" style="font-size: 0.72rem; color: var(--vx-primary);">{{ $assignedCurriculum->code }}</div>
                                        @else
                                            <span class="vx-badge badge-warning" style="font-size: 0.75rem;">Belum Ditetapkan</span>
                                        @endif
                                    </td>
                                    <td style="text-align: center;">
                                        <span class="cell-mono font-bold" style="font-size: 0.92rem; color: var(--vx-text-heading);">{{ $totalHours }}</span>
                                        <span style="font-size: 0.72rem; color: var(--vx-text-muted);"> JP/Mgg</span>
                                    </td>
                                    <td style="text-align: center;">
                                        <span class="cell-mono font-bold" style="font-size: 0.92rem; color: var(--vx-primary);">{{ $totalSubjects }}</span>
                                        <span style="font-size: 0.72rem; color: var(--vx-text-muted);"> Mapel</span>
                                    </td>
                                    <td>
                                        <div style="font-size: 0.8rem; font-weight: 600; color: var(--vx-text-heading); margin-bottom: 0.25rem;">
                                            {{ $classesInGrade->count() }} Kelas
                                        </div>
                                        <div style="display: flex; flex-wrap: wrap; gap: 0.25rem;">
                                            @foreach($classesInGrade->take(4) as $c)
                                                <span class="vx-badge badge-secondary" style="font-size: 0.68rem;">{{ $c->name }}</span>
                                            @endforeach
                                            @if($classesInGrade->count() > 4)
                                                <span class="vx-badge badge-muted" style="font-size: 0.68rem;">+{{ $classesInGrade->count() - 4 }}</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td style="text-align: center;">
                                        @if($assignedCurriculum)
                                            <span class="vx-badge badge-success" style="font-size: 0.72rem;">
                                                <span class="badge-dot"></span> Aktif
                                            </span>
                                        @else
                                            <span class="vx-badge badge-warning" style="font-size: 0.72rem;">Belum</span>
                                        @endif
                                    </td>
                                    <td style="text-align: right;">
                                        <div class="d-flex gap-1 justify-end">
                                            <button class="btn btn-secondary btn-sm" onclick="openAssignGradeModal({{ $gLevel }}, {{ $assignedCurriculum?->id ?? 'null' }}, '{{ $mapping?->phase ?? '' }}')">
                                                <i class='bx bx-edit'></i> Ubah
                                            </button>
                                            @if($assignedCurriculum)
                                                <button class="btn btn-secondary btn-sm" onclick="openCurriculumStructureDetailModal({{ $assignedCurriculum->id }})" title="Lihat & Kelola Struktur Mapel">
                                                    <i class='bx bx-list-check'></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Helpful Guidance Callout Strip -->
            <div style="border-top: 1px solid var(--vx-border-light); background: rgba(115, 103, 240, 0.03); padding: 0.9rem 1.5rem; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.75rem; border-bottom-left-radius: var(--vx-radius-xl); border-bottom-right-radius: var(--vx-radius-xl);">
                <div style="display: flex; align-items: center; gap: 0.6rem; font-size: 0.8rem; color: var(--vx-text-muted);">
                    <i class='bx bx-info-circle' style="font-size: 1.15rem; color: var(--vx-primary);"></i>
                    <span>
                        <strong>Tips Sinkronisasi:</strong> Saat Anda mengubah kurikulum di suatu jenjang, seluruh rombongan belajar (kelas) pada jenjang tersebut akan otomatis mengacu pada alokasi mata pelajaran dan beban JP kurikulum yang baru.
                    </span>
                </div>
                <div style="font-size: 0.75rem; color: var(--vx-primary); font-weight: 600;">
                    <a href="#curriculumStructurePanel" style="color: inherit; text-decoration: none; display: flex; align-items: center; gap: 0.25rem;">
                        <span>Pelajari Struktur Beban Mapel</span>
                        <i class='bx bx-chevron-right'></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- 2. Master Table of Curriculums -->
        <div class="panel" style="margin-bottom: 1.5rem; border-radius: var(--vx-radius-xl);">
            <div class="panel-header">
                <div class="panel-title">
                    <h2>Daftar Kurikulum Terdaftar</h2>
                    <p>Basis kurikulum yang digunakan untuk menyusun struktur mata pelajaran dan beban belajar sekolah.</p>
                </div>
                <div class="panel-toolbar">
                    <button class="btn btn-primary btn-sm" onclick="openModal('modalAddCurriculum')">
                        <i class='bx bx-plus'></i> Tambah Kurikulum
                    </button>
                </div>
            </div>
            <div class="table-wrap">
                <table class="clean-table">
                    <thead>
                        <tr>
                            <th>Kurikulum</th>
                            <th>Target Jenjang</th>
                            <th>Deskripsi & Karakteristik</th>
                            <th>Penerapan Jenjang</th>
                            <th>Rombel</th>
                            <th>Struktur Mapel</th>
                            <th>Status</th>
                            <th style="text-align: right;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($curriculums as $cur)
                            <tr id="curTableRow-{{ $cur->id }}" class="curriculum-table-row {{ $loop->first ? 'active-cur-row' : '' }}">
                                <td>
                                    <div class="cell-title">{{ $cur->name }}</div>
                                    <div class="cell-sub cell-mono">{{ $cur->code }}</div>
                                </td>
                                <td>
                                    <span class="vx-badge badge-info">{{ $cur->target_level ?? 'SMA/SMK' }}</span>
                                </td>
                                <td style="max-width: 320px;">
                                    <div style="font-size: 0.8rem; color: var(--vx-text-heading); line-height: 1.35;">
                                        {{ $cur->description ?? '—' }}
                                    </div>
                                </td>
                                <td>
                                    @if($cur->gradeLevels->count() > 0)
                                        <div style="display: flex; flex-wrap: wrap; gap: 0.35rem;">
                                            @foreach($cur->gradeLevels as $gl)
                                                <span class="vx-badge badge-primary">
                                                    Tingkat {{ $gl->grade_level }} {{ $gl->phase ? '('.$gl->phase.')' : '' }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="vx-badge badge-muted">Belum Dipetakan</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="cell-mono font-bold" style="font-size: 0.9rem;">{{ $cur->classes_count ?? $cur->classes->count() }}</span>
                                    <span style="font-size: 0.75rem; color: var(--vx-text-muted);"> Kelas</span>
                                </td>
                                <td>
                                    <!-- Tombol Pilihan Struktur Mapel -->
                                    <button type="button" class="btn btn-outline-primary btn-xs btn-cur-structure-badge" onclick="openCurriculumStructureDetailModal({{ $cur->id }})" title="Klik untuk membuka & melihat struktur mata pelajaran kurikulum ini" style="display: inline-flex; align-items: center; gap: 0.35rem; font-weight: 600; padding: 0.28rem 0.65rem; border-radius: var(--radius-pill); cursor: pointer; white-space: nowrap;">
                                        <i class='bx bx-list-check' style="font-size: 1rem;"></i>
                                        <span class="cell-mono font-bold">{{ $cur->curriculumSubjects->count() }} Mapel</span>
                                        <i class='bx bx-chevron-right' style="font-size: 0.85rem; opacity: 0.7;"></i>
                                    </button>
                                </td>
                                <td>
                                    @if($cur->is_active)
                                        <span class="vx-badge badge-success"><span class="badge-dot"></span> Aktif</span>
                                    @else
                                        <span class="vx-badge badge-muted">Non-aktif</span>
                                    @endif
                                </td>
                                <td style="text-align: right;">
                                    <div class="d-flex gap-1 justify-end">
                                        <button type="button" class="btn btn-primary btn-sm" onclick="openCurriculumStructureDetailModal({{ $cur->id }})" title="Lihat & Kelola Struktur Mata Pelajaran">
                                            <i class='bx bx-book-content'></i> Struktur Mapel
                                        </button>
                                        <button type="button" class="btn btn-danger-light btn-sm btn-icon-sm" onclick="deleteCurriculum({{ $cur->id }}, '{{ addslashes($cur->name) }}')" title="Hapus Kurikulum">
                                            <i class='bx bx-trash'></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" style="text-align: center; padding: 3rem; color: var(--vx-text-muted);">
                                    <i class='bx bx-book-open' style="font-size: 2.5rem; display: block; margin-bottom: 0.75rem;"></i>
                                    Belum ada data kurikulum. Klik <strong>Tambah Kurikulum</strong> untuk memulai.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 3. Detail Struktur Mata Pelajaran & Alokasi JP per Kurikulum -->
        <div class="panel" id="curriculumStructurePanel" style="margin-bottom: 1.5rem; border-radius: var(--vx-radius-xl);">
            <div class="panel-header" style="border-bottom: 1px solid var(--vx-border-light);">
                <div class="panel-title">
                    <h2 id="curriculumStructureTitle">Struktur Mata Pelajaran & Alokasi Beban Belajar</h2>
                    <p id="curriculumStructureSub">Alokasi Jam Pelajaran (JP/Minggu), Kelompok Mapel, dan KKM/KKTP per kurikulum.</p>
                </div>
                <div class="panel-toolbar" style="display: flex; gap: 0.5rem; align-items: center;">
                    <button class="btn btn-secondary btn-sm" onclick="openManageSubjectGroupsModal(activeCurriculumTabId)">
                        <i class='bx bx-category'></i> Kelola Kelompok Mapel
                    </button>
                    <button class="btn btn-primary btn-sm" onclick="openAddCurriculumSubjectModal()">
                        <i class='bx bx-plus-circle'></i> Petakan Mapel ke Kurikulum Ini
                    </button>
                </div>
            </div>

            <!-- Curriculum Tabs / Pills -->
            <div style="padding: 1rem 1.5rem 0; border-bottom: 1px solid var(--vx-border-light); display: flex; gap: 0.5rem; overflow-x: auto;">
                @foreach($curriculums as $idx => $cTab)
                    <button class="btn btn-sm {{ $idx === 0 ? 'btn-primary' : 'btn-secondary' }} cur-tab-btn" id="curTabBtn-{{ $cTab->id }}" onclick="switchCurriculumTab({{ $cTab->id }})" style="margin-bottom: -1px; border-bottom-left-radius: 0; border-bottom-right-radius: 0;">
                        <i class='bx bx-book-content'></i> {{ $cTab->name }}
                        <span class="vx-badge badge-dark" style="margin-left: 0.4rem; font-size: 0.7rem;">{{ $cTab->curriculumSubjects->count() }} Mapel</span>
                    </button>
                @endforeach
            </div>

            <!-- Curriculum Subject Content Panes -->
            @foreach($curriculums as $idx => $cTab)
                <div class="cur-structure-pane" id="curPane-{{ $cTab->id }}" style="display: {{ $idx === 0 ? 'block' : 'none' }};">
                    <!-- Info Summary Strip -->
                    <div style="background: var(--vx-body-bg); padding: 1rem 1.5rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; border-bottom: 1px solid var(--vx-border-light);">
                        <div class="d-flex align-center gap-1">
                            <span class="vx-badge badge-primary cell-mono">{{ $cTab->code }}</span>
                            <span style="font-size: 0.85rem; color: var(--vx-text-heading); font-weight: 600;">
                                Total Alokasi: <span style="color: var(--vx-primary);">{{ $cTab->curriculumSubjects->sum('hours_per_week') }} Jam Pelajaran (JP) / Minggu</span>
                            </span>
                        </div>
                        <div style="font-size: 0.8rem; color: var(--vx-text-muted);">
                            Diterapkan di: <strong>{{ $cTab->gradeLevels->pluck('grade_level')->map(fn($g) => 'Tingkat '.$g)->join(', ') ?: 'Belum diterapkan di jenjang manapun' }}</strong>
                        </div>
                    </div>

                    <div class="table-wrap">
                        <table class="clean-table">
                            <thead>
                                <tr>
                                    <th>Mata Pelajaran</th>
                                    <th>Kelompok Mapel</th>
                                    <th style="text-align: center;">Beban Belajar</th>
                                    <th style="text-align: center;">Standar KKTP / KKM</th>
                                    <th>Status Pembelajaran</th>
                                    <th style="text-align: right;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($cTab->curriculumSubjects as $cs)
                                    <tr>
                                        <td>
                                            <div class="cell-title">{{ $cs->subject?->name ?? '—' }}</div>
                                            <div class="cell-sub cell-mono">{{ $cs->subject?->code ?? '—' }}</div>
                                        </td>
                                        <td>
                                            <span class="vx-badge {{ $cs->resolved_group_badge }}" style="font-size: 0.74rem;">
                                                {{ $cs->resolved_group_name }}
                                            </span>
                                        </td>
                                        <td style="text-align: center;">
                                            <span class="cell-mono font-bold" style="font-size: 1rem; color: var(--vx-primary);">
                                                {{ $cs->hours_per_week }}
                                            </span>
                                            <span style="font-size: 0.78rem; color: var(--vx-text-muted);"> JP / mg</span>
                                        </td>
                                        <td style="text-align: center;">
                                            <span class="vx-badge badge-dark cell-mono" style="font-size: 0.85rem;">
                                                {{ number_format($cs->passing_grade, 0) }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="vx-badge badge-success">
                                                <span class="badge-dot"></span> Wajib KBM
                                            </span>
                                        </td>
                                        <td style="text-align: right;">
                                            <div class="d-flex gap-1 justify-end">
                                                <button class="btn btn-secondary btn-xs" onclick="openEditCurriculumSubjectModal({{ $cTab->id }}, {{ $cs->subject_id }}, {{ $cs->curriculum_subject_group_id ?? 'null' }}, '{{ $cs->subject_group }}', {{ $cs->hours_per_week }}, {{ $cs->passing_grade }})" title="Ubah Beban Belajar">
                                                    <i class='bx bx-edit'></i> Edit Beban
                                                </button>
                                                <button class="btn btn-danger-light btn-xs btn-icon-sm" onclick="removeSubjectFromCurriculum({{ $cTab->id }}, {{ $cs->subject_id }}, '{{ addslashes($cs->subject?->name ?? 'Mapel') }}')" title="Keluarkan dari Kurikulum">
                                                    <i class='bx bx-trash'></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" style="text-align: center; padding: 2.5rem; color: var(--vx-text-muted);">
                                            <i class='bx bx-book-open' style="font-size: 2rem; display: block; margin-bottom: 0.5rem;"></i>
                                            Belum ada mata pelajaran yang dipetakan pada kurikulum ini.<br>
                                            <button class="btn btn-primary btn-sm" style="margin-top: 0.75rem;" onclick="openAddCurriculumSubjectModal({{ $cTab->id }})">
                                                <i class='bx bx-plus-circle'></i> Petakan Mata Pelajaran Sekarang
                                            </button>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- TAB: SPEKTRUM KEAHLIAN SMK -->
    <div id="tab-vocational" class="tab-pane">
        <!-- Banner Mode Sekolah & Spektrum Keahlian -->
        <div class="panel" style="margin-bottom: 1.5rem; border-radius: var(--vx-radius-xl); background: linear-gradient(135deg, rgba(255, 159, 67, 0.09) 0%, rgba(115, 103, 240, 0.03) 100%); border-left: 4px solid var(--vx-warning);">
            <div style="padding: 1.25rem 1.5rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                <div>
                    <div class="d-flex align-center gap-1" style="margin-bottom: 0.35rem;">
                        <h2 style="font-size: 1.15rem; font-weight: 700; color: var(--vx-text-heading); margin: 0;">
                            <i class='bx bx-wrench' style="color: var(--vx-warning); margin-right: 0.35rem;"></i>
                            Spektrum Keahlian SMK (Standar Kemendikbudristek)
                        </h2>
                        @if($isVocational)
                            <span class="vx-badge badge-warning" style="font-size: 0.72rem;">Status: SMK Aktif</span>
                        @else
                            <span class="vx-badge badge-secondary" style="font-size: 0.72rem;">Status: Mode SMA</span>
                        @endif
                    </div>
                    <p style="font-size: 0.85rem; color: var(--vx-text-muted); margin: 0; max-width: 820px;">
                        Struktur kurikulum vokasi nasional berbasis 3-Tier: <strong>1. Bidang Keahlian</strong> &rarr; <strong>2. Program Keahlian</strong> (Dasar Kejuruan Fase E / Kelas X) &rarr; <strong>3. Konsentrasi Keahlian</strong> (Spesialisasi Fase F / Kelas XI-XII, Kode Dapodik, Kemitraan DUDI, & PKL Industri).
                    </p>
                </div>
                <div class="d-flex gap-1 align-center flex-wrap">
                    @if(!$isVocational)
                        <button class="btn btn-warning btn-sm" onclick="switchSchoolType('SMK')">
                            <i class='bx bx-refresh'></i> Aktifkan Mode SMK (SMKN 1 Jakarta)
                        </button>
                    @else
                        <button class="btn btn-secondary btn-sm" onclick="switchSchoolType('SMA')">
                            <i class='bx bx-refresh'></i> Beralih ke Mode SMA
                        </button>
                    @endif
                    <button class="btn btn-secondary btn-sm" onclick="openModal('modalAddVocationalProgram')">
                        <i class='bx bx-folder-plus'></i> Tambah Program
                    </button>
                    <button class="btn btn-primary btn-sm" onclick="openModal('modalAddVocationalConcentration')">
                        <i class='bx bx-plus'></i> Tambah Konsentrasi
                    </button>
                </div>
            </div>
        </div>

        <!-- KPI Mini Stats SMK -->
        <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; margin-bottom: 1.5rem;">
            <div style="background: var(--vx-body-bg); border: 1px solid var(--vx-border-light); border-radius: var(--vx-radius-lg); padding: 1.1rem; display: flex; align-items: center; gap: 0.85rem;">
                <div style="width: 44px; height: 44px; border-radius: 12px; background: rgba(115, 103, 240, 0.12); color: var(--vx-primary); display: flex; align-items: center; justify-content: center; font-size: 1.35rem; flex-shrink: 0;">
                    <i class='bx bx-layer'></i>
                </div>
                <div>
                    <div style="font-size: 0.75rem; color: var(--vx-text-muted); font-weight: 600; text-transform: uppercase;">Bidang Keahlian</div>
                    <div style="font-size: 1.35rem; font-weight: 700; color: var(--vx-text-heading); margin-top: 0.1rem;">{{ $stats['total_vocational_fields'] }}</div>
                    <div style="font-size: 0.72rem; color: var(--vx-text-muted);">Disiplin vokasi utama</div>
                </div>
            </div>

            <div style="background: var(--vx-body-bg); border: 1px solid var(--vx-border-light); border-radius: var(--vx-radius-lg); padding: 1.1rem; display: flex; align-items: center; gap: 0.85rem;">
                <div style="width: 44px; height: 44px; border-radius: 12px; background: rgba(0, 207, 232, 0.12); color: var(--vx-info); display: flex; align-items: center; justify-content: center; font-size: 1.35rem; flex-shrink: 0;">
                    <i class='bx bx-folder'></i>
                </div>
                <div>
                    <div style="font-size: 0.75rem; color: var(--vx-text-muted); font-weight: 600; text-transform: uppercase;">Program Keahlian</div>
                    <div style="font-size: 1.35rem; font-weight: 700; color: var(--vx-text-heading); margin-top: 0.1rem;">{{ $stats['total_vocational_programs'] }}</div>
                    <div style="font-size: 0.72rem; color: var(--vx-text-muted);">Dasar kejuruan (Fase E)</div>
                </div>
            </div>

            <div style="background: var(--vx-body-bg); border: 1px solid var(--vx-border-light); border-radius: var(--vx-radius-lg); padding: 1.1rem; display: flex; align-items: center; gap: 0.85rem;">
                <div style="width: 44px; height: 44px; border-radius: 12px; background: rgba(255, 159, 67, 0.12); color: var(--vx-warning); display: flex; align-items: center; justify-content: center; font-size: 1.35rem; flex-shrink: 0;">
                    <i class='bx bx-award'></i>
                </div>
                <div>
                    <div style="font-size: 0.75rem; color: var(--vx-text-muted); font-weight: 600; text-transform: uppercase;">Konsentrasi Keahlian</div>
                    <div style="font-size: 1.35rem; font-weight: 700; color: var(--vx-text-heading); margin-top: 0.1rem;">{{ $stats['total_vocational_concentrations'] }}</div>
                    <div style="font-size: 0.72rem; color: var(--vx-text-muted);">Spesialisasi (Fase F / Dapodik)</div>
                </div>
            </div>

            <div style="background: var(--vx-body-bg); border: 1px solid var(--vx-border-light); border-radius: var(--vx-radius-lg); padding: 1.1rem; display: flex; align-items: center; gap: 0.85rem;">
                <div style="width: 44px; height: 44px; border-radius: 12px; background: rgba(40, 199, 111, 0.12); color: var(--vx-success); display: flex; align-items: center; justify-content: center; font-size: 1.35rem; flex-shrink: 0;">
                    <i class='bx bx-group'></i>
                </div>
                <div>
                    <div style="font-size: 0.75rem; color: var(--vx-text-muted); font-weight: 600; text-transform: uppercase;">Rombel Kejuruan</div>
                    <div style="font-size: 1.35rem; font-weight: 700; color: var(--vx-text-heading); margin-top: 0.1rem;">{{ $classes->whereNotNull('vocational_program_id')->count() }}</div>
                    <div style="font-size: 0.72rem; color: var(--vx-text-muted);">Kelas vokasi aktif</div>
                </div>
            </div>
        </div>

        <!-- 1. Interactive 3-Tier Spectrum Explorer -->
        <div class="panel" style="margin-bottom: 1.5rem; border-radius: var(--vx-radius-xl);">
            <div class="panel-header" style="border-bottom: 1px solid var(--vx-border-light); padding: 1.25rem 1.5rem;">
                <div class="panel-title">
                    <h3 style="font-size: 1rem; font-weight: 700; margin: 0;">Matriks Struktur Spektrum Keahlian SMK</h3>
                    <p style="font-size: 0.8rem; color: var(--vx-text-muted); margin: 0.2rem 0 0;">Hubungan berjenjang Bidang Keahlian &rarr; Program Keahlian &rarr; Konsentrasi Keahlian & Kemitraan Industri DUDI.</p>
                </div>
                <div class="panel-toolbar">
                    <button class="btn btn-secondary btn-sm" onclick="openModal('modalAddVocationalField')">
                        <i class='bx bx-plus'></i> Tambah Bidang Keahlian
                    </button>
                </div>
            </div>

            <!-- Field Selector Tabs -->
            <div style="padding: 1rem 1.5rem 0; border-bottom: 1px solid var(--vx-border-light); display: flex; gap: 0.5rem; overflow-x: auto;">
                @foreach($vocationalFields as $fIdx => $vf)
                    <button class="btn btn-sm {{ $fIdx === 0 ? 'btn-primary' : 'btn-secondary' }} voc-field-tab-btn" id="vocFieldTabBtn-{{ $vf->id }}" onclick="selectVocationalField({{ $vf->id }})" style="margin-bottom: -1px; border-bottom-left-radius: 0; border-bottom-right-radius: 0;">
                        <i class='bx bx-layer'></i> {{ $vf->name }} ({{ $vf->code }})
                        <span class="vx-badge badge-dark" style="margin-left: 0.4rem; font-size: 0.7rem;">{{ $vf->programs->count() }} Program</span>
                    </button>
                @endforeach
            </div>

            <!-- Field Content Panes -->
            @foreach($vocationalFields as $fIdx => $vf)
                <div class="voc-field-pane" id="vocFieldPane-{{ $vf->id }}" style="display: {{ $fIdx === 0 ? 'block' : 'none' }}; padding: 1.5rem;">
                    <div style="margin-bottom: 1.25rem; background: var(--vx-body-bg); border-radius: var(--vx-radius-lg); padding: 1rem 1.25rem; border: 1px solid var(--vx-border-light);">
                        <div style="font-size: 0.82rem; color: var(--vx-text-muted); line-height: 1.4;">
                            <strong style="color: var(--vx-text-heading);">Deskripsi Bidang:</strong> {{ $vf->description ?: 'Bidang keahlian resmi SMK sesuai Kepmendikbudristek.' }}
                        </div>
                    </div>

                    <!-- Programs under this field -->
                    <div style="display: flex; flex-direction: column; gap: 1.25rem;">
                        @forelse($vf->programs as $vp)
                            <div style="border: 1px solid var(--vx-border-light); border-radius: var(--vx-radius-lg); background: var(--vx-card-bg); overflow: hidden;">
                                <div style="padding: 1rem 1.25rem; background: rgba(115, 103, 240, 0.04); border-bottom: 1px solid var(--vx-border-light); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.5rem;">
                                    <div class="d-flex align-center gap-1">
                                        <span class="vx-badge badge-primary cell-mono" style="font-size: 0.82rem; font-weight: 700;">{{ $vp->code }}</span>
                                        <div>
                                            <h4 style="font-size: 0.98rem; font-weight: 700; color: var(--vx-text-heading); margin: 0;">{{ $vp->name }}</h4>
                                            <span style="font-size: 0.75rem; color: var(--vx-text-muted);">
                                                Fase E (Kelas 10) &bull; Durasi: <strong>{{ $vp->program_duration_years }} Tahun</strong> &bull; {{ $vp->concentrations->count() }} Konsentrasi Keahlian
                                            </span>
                                        </div>
                                    </div>
                                    <button class="btn btn-secondary btn-xs" onclick="openAddConcentrationWithProgram({{ $vp->id }}, '{{ addslashes($vp->name) }}')">
                                        <i class='bx bx-plus'></i> Tambah Konsentrasi
                                    </button>
                                </div>

                                <div style="padding: 1rem 1.25rem;">
                                    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 1rem;">
                                        @forelse($vp->concentrations as $vc)
                                            <div style="background: var(--vx-body-bg); border: 1px solid var(--vx-border-light); border-radius: var(--vx-radius-md); padding: 1rem; display: flex; flex-direction: column; justify-content: space-between;">
                                                <div>
                                                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.5rem;">
                                                        <span class="vx-badge badge-warning cell-mono" style="font-size: 0.72rem;">{{ $vc->code }}</span>
                                                        @if($vc->dapodik_code)
                                                            <span class="vx-badge badge-dark cell-mono" style="font-size: 0.68rem;" title="Kode Dapodik Kemendikbud">Dapodik #{{ $vc->dapodik_code }}</span>
                                                        @endif
                                                    </div>
                                                    <div style="font-size: 0.92rem; font-weight: 700; color: var(--vx-text-heading); margin-bottom: 0.35rem;">
                                                        {{ $vc->name }}
                                                    </div>
                                                    @if($vc->description)
                                                        <p style="font-size: 0.78rem; color: var(--vx-text-muted); line-height: 1.35; margin: 0 0 0.5rem 0;">
                                                            {{ $vc->description }}
                                                        </p>
                                                    @endif

                                                    @if($vc->industry_partners)
                                                        <div style="margin-top: 0.5rem; font-size: 0.75rem; color: var(--vx-text-heading); background: rgba(0, 207, 232, 0.08); padding: 0.45rem 0.65rem; border-radius: var(--vx-radius-sm);">
                                                            <i class='bx bx-building-house' style="color: var(--vx-info);"></i> <strong>Mitra DUDI:</strong> {{ $vc->industry_partners }}
                                                        </div>
                                                    @endif

                                                    @if($vc->competencies_summary)
                                                        <div style="margin-top: 0.4rem; font-size: 0.73rem; color: var(--vx-text-muted); line-height: 1.3;">
                                                            <i class='bx bx-check-double' style="color: var(--vx-success);"></i> <strong>Kompetensi:</strong> {{ $vc->competencies_summary }}
                                                        </div>
                                                    @endif
                                                </div>

                                                <div style="border-top: 1px dashed var(--vx-border-light); margin-top: 0.75rem; padding-top: 0.65rem; display: flex; justify-content: space-between; align-items: center;">
                                                    <span style="font-size: 0.75rem; color: var(--vx-text-muted);">
                                                        Rombel: <strong>{{ $vc->classes->count() }} Kelas</strong>
                                                    </span>
                                                    <div class="d-flex gap-1">
                                                        <button class="btn btn-secondary btn-xs" onclick="openEditConcentrationModal({{ $vc->id }}, '{{ addslashes($vc->name) }}', '{{ $vc->dapodik_code }}', '{{ addslashes($vc->description ?? '') }}', '{{ addslashes($vc->industry_partners ?? '') }}', '{{ addslashes($vc->competencies_summary ?? '') }}')" title="Edit">
                                                            <i class='bx bx-edit'></i>
                                                        </button>
                                                        <button class="btn btn-danger-light btn-xs" onclick="deleteVocationalConcentration({{ $vc->id }}, '{{ addslashes($vc->name) }}')" title="Hapus">
                                                            <i class='bx bx-trash'></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        @empty
                                            <div style="grid-column: 1 / -1; padding: 1.5rem; text-align: center; color: var(--vx-text-muted); font-size: 0.82rem;">
                                                Belum ada konsentrasi keahlian di bawah program ini. Klik <strong>Tambah Konsentrasi</strong> untuk mendaftarkan.
                                            </div>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div style="padding: 2rem; text-align: center; color: var(--vx-text-muted);">
                                Belum ada program keahlian pada bidang ini.
                            </div>
                        @endforelse
                    </div>
                </div>
            @endforeach
        </div>

        <!-- 2. Master Table of Vocational Concentrations -->
        <div class="panel" style="margin-bottom: 1.5rem; border-radius: var(--vx-radius-xl);">
            <div class="panel-header">
                <div class="panel-title">
                    <h2>Tabel Master Konsentrasi Keahlian SMK</h2>
                    <p>Daftar seluruh konsentrasi spesialisasi kejuruan yang diakui Dapodik dan dibuka pada rombongan belajar sekolah.</p>
                </div>
                <div class="panel-toolbar">
                    <button class="btn btn-primary btn-sm" onclick="openModal('modalAddVocationalConcentration')">
                        <i class='bx bx-plus'></i> Tambah Konsentrasi
                    </button>
                </div>
            </div>
            <div class="table-wrap">
                <table class="clean-table">
                    <thead>
                        <tr>
                            <th>Konsentrasi Keahlian</th>
                            <th>Program Keahlian</th>
                            <th>Bidang Keahlian</th>
                            <th>Kode Dapodik</th>
                            <th>Durasi</th>
                            <th>Mitra Industri (DUDI)</th>
                            <th>Rombel Aktif</th>
                            <th style="text-align: right;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($vocationalConcentrations as $conc)
                            <tr>
                                <td>
                                    <div class="cell-title">{{ $conc->name }}</div>
                                    <div class="cell-sub cell-mono">{{ $conc->code }}</div>
                                </td>
                                <td>
                                    <span class="vx-badge badge-primary">{{ $conc->program?->name ?? '—' }}</span>
                                    <div class="cell-sub cell-mono">{{ $conc->program?->code ?? '' }}</div>
                                </td>
                                <td>
                                    <span class="vx-badge badge-info">{{ $conc->program?->field?->name ?? '—' }}</span>
                                </td>
                                <td>
                                    @if($conc->dapodik_code)
                                        <span class="vx-badge badge-dark cell-mono">{{ $conc->dapodik_code }}</span>
                                    @else
                                        <span style="color: var(--vx-text-muted);">—</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="cell-mono font-bold">{{ $conc->program?->program_duration_years ?? 3 }} Thn</span>
                                </td>
                                <td style="max-width: 250px;">
                                    <div style="font-size: 0.78rem; line-height: 1.35; color: var(--vx-text-heading);">
                                        {{ $conc->industry_partners ?: '—' }}
                                    </div>
                                </td>
                                <td>
                                    <span class="cell-mono font-bold" style="color: var(--vx-primary);">{{ $conc->classes_count ?? $conc->classes->count() }}</span>
                                    <span style="font-size: 0.75rem; color: var(--vx-text-muted);"> Kelas</span>
                                </td>
                                <td style="text-align: right;">
                                    <div class="d-flex gap-1 justify-end">
                                        <button class="btn btn-secondary btn-xs" onclick="openEditConcentrationModal({{ $conc->id }}, '{{ addslashes($conc->name) }}', '{{ $conc->dapodik_code }}', '{{ addslashes($conc->description ?? '') }}', '{{ addslashes($conc->industry_partners ?? '') }}', '{{ addslashes($conc->competencies_summary ?? '') }}')">
                                            <i class='bx bx-edit'></i>
                                        </button>
                                        <button class="btn btn-danger-light btn-xs" onclick="deleteVocationalConcentration({{ $conc->id }}, '{{ addslashes($conc->name) }}')">
                                            <i class='bx bx-trash'></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" style="text-align: center; padding: 3rem; color: var(--vx-text-muted);">
                                    <i class='bx bx-wrench' style="font-size: 2.5rem; display: block; margin-bottom: 0.75rem;"></i>
                                    Belum ada data konsentrasi keahlian SMK.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- TAB 2: MULTI-KATEGORI JAM BELAJAR -->
    <!-- TAB 2: MULTI-KATEGORI JAM BELAJAR (VIBRANT BENTO) -->
    <div id="tab-schedules" class="tab-pane">
        <!-- Top Banner / Summary Card -->
        <div class="vx-card" style="margin-bottom: 1.5rem; background: linear-gradient(to right, rgba(79, 70, 229, 0.08), transparent); border-left: 4px solid var(--bento-primary); padding: 1.25rem 1.5rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <div style="width: 48px; height: 48px; border-radius: var(--radius-lg); background: var(--bento-primary-grad); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; box-shadow: 0 4px 14px var(--bento-primary-glow);">
                        <i class='bx bx-time'></i>
                    </div>
                    <div>
                        <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
                            <h2 style="font-size: 1.25rem; font-weight: 800; color: var(--text-heading); margin: 0;">Pola Jadwal & Sesi Jam Pelajaran</h2>
                            <span class="vx-badge badge-primary">Multi-Kategori KBM</span>
                        </div>
                        <p style="font-size: 0.82rem; color: var(--text-muted); margin: 0.25rem 0 0;">
                            Pola jam yang sedang aktif akan otomatis menjadi acuan absensi, status KBM di dashboard, dan jam tatap muka pendidik.
                        </p>
                    </div>
                </div>
                <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                    <a href="{{ route('admin.schedules') }}" class="btn btn-secondary">
                        <i class='bx bx-calendar-event'></i> Buka Matriks Jadwal Pelajaran
                    </a>
                    <button class="btn btn-primary" onclick="openModal('modalAddCategory')">
                        <i class='bx bx-plus-circle'></i> Tambah Kategori Baru
                    </button>
                </div>
            </div>
        </div>

        <!-- Grid Selector Kategori Bento -->
        <div class="cat-grid" id="catGrid">
            @foreach($timeCategories as $cat)
            <div class="cat-box {{ $cat->is_default ? 'active' : '' }}" id="cat-card-{{ $cat->id }}"
                onclick="selectCategory({{ $cat->id }}, '{{ addslashes($cat->name) }}')">
                <div class="cat-box-header">
                    <div style="display: flex; align-items: center; gap: 0.55rem;">
                        <div style="width: 32px; height: 32px; border-radius: 8px; background: {{ $cat->is_default ? 'var(--bento-primary-grad)' : 'var(--bg-subtle)' }}; color: {{ $cat->is_default ? '#fff' : 'var(--text-muted)' }}; display: flex; align-items: center; justify-content: center; font-size: 1.1rem;">
                            <i class='bx bx-time-five'></i>
                        </div>
                        <div>
                            <div class="cat-box-title" id="cat-name-{{ $cat->id }}">{{ $cat->name }}</div>
                            <span class="vx-badge badge-muted cell-mono" id="cat-code-{{ $cat->id }}" style="font-size: 0.65rem; padding: 1px 5px;">{{ $cat->code }}</span>
                        </div>
                    </div>
                    <div style="display: flex; align-items: center; gap: 0.25rem;" onclick="event.stopPropagation();">
                        <button class="btn btn-ghost btn-xs btn-icon-sm" title="Edit Kategori"
                            onclick="openEditCategoryModal({{ $cat->id }}, '{{ addslashes($cat->name) }}', '{{ addslashes($cat->code) }}', '{{ addslashes($cat->description ?? '') }}', '{{ $cat->effective_start_date ? $cat->effective_start_date->format('Y-m-d') : '' }}', '{{ $cat->effective_end_date ? $cat->effective_end_date->format('Y-m-d') : '' }}')">
                            <i class='bx bx-edit-alt'></i>
                        </button>
                        @if(!$cat->is_default)
                            <button class="btn btn-danger-light btn-xs btn-icon-sm" title="Hapus Kategori"
                                onclick="deleteCategory({{ $cat->id }}, '{{ addslashes($cat->name) }}')">
                                <i class='bx bx-trash'></i>
                            </button>
                        @endif
                    </div>
                </div>
                <p class="cat-box-desc" id="cat-desc-{{ $cat->id }}">{{ $cat->description ?? 'Jadwal operasional jam pelajaran KBM sekolah.' }}</p>
                <div class="cat-box-footer">
                    <span id="cat-count-{{ $cat->id }}" style="font-weight: 600; color: var(--text-heading); display: inline-flex; align-items: center; gap: 0.35rem;">
                        <i class='bx bx-list-ul' style="color: var(--bento-primary);"></i> {{ $cat->periods->count() }} Sesi Jam
                    </span>
                    <div id="cat-action-{{ $cat->id }}">
                        @if($cat->is_default)
                            <span class="vx-badge badge-success" style="font-weight: 700; padding: 2px 8px;">
                                <span class="live-dot-pulse" style="width: 6px; height: 6px;"></span> Pola Aktif Utama
                            </span>
                        @else
                            <button class="btn btn-secondary btn-xs" onclick="event.stopPropagation(); activateScheduleCategory({{ $cat->id }});">
                                Aktifkan Pola Ini
                            </button>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Periods Table Card -->
        <div class="apex-card" style="border-radius: var(--radius-xl);">
            <div class="apex-card-header">
                <div>
                    <h2 class="apex-card-title" style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
                        Rincian Sesi Jam: <span id="currentCategoryTitle" class="vx-badge badge-primary" style="font-size: 0.85rem; padding: 0.25rem 0.75rem;">{{ $activeCategory->name ?? 'NORMAL' }}</span>
                    </h2>
                    <p class="apex-card-desc">Alokasi waktu sesi jam ke-1 s/d selesai dan penanda istirahat.</p>
                </div>
                <div style="display: flex; align-items: center; gap: 0.65rem; flex-wrap: wrap;">
                    <!-- Day Filter Buttons -->
                    <div class="day-pills-wrap">
                        <button type="button" class="day-pill-btn" onclick="setPeriodDay('', this)">Semua Hari</button>
                        <button type="button" class="day-pill-btn active" onclick="setPeriodDay('1', this)">Senin</button>
                        <button type="button" class="day-pill-btn" onclick="setPeriodDay('2', this)">Selasa</button>
                        <button type="button" class="day-pill-btn" onclick="setPeriodDay('3', this)">Rabu</button>
                        <button type="button" class="day-pill-btn" onclick="setPeriodDay('4', this)">Kamis</button>
                        <button type="button" class="day-pill-btn" onclick="setPeriodDay('5', this)">Jumat</button>
                        <button type="button" class="day-pill-btn" onclick="setPeriodDay('6', this)">Sabtu</button>
                    </div>
                    <input type="hidden" id="periodDayFilter" value="1">
                    <button class="btn btn-primary btn-sm" onclick="openAddPeriodModal()">
                        <i class='bx bx-plus'></i> Tambah Jam Pelajaran
                    </button>
                </div>
            </div>
            <div style="overflow-x: auto;">
                <table class="clean-table">
                    <thead>
                        <tr>
                            <th>Hari</th>
                            <th>Sesi Jam</th>
                            <th>Nama Sesi Jam Pelajaran</th>
                            <th>Waktu Mulai</th>
                            <th>Waktu Selesai</th>
                            <th>Durasi Waktu</th>
                            <th>Tipe Sesi</th>
                            <th style="text-align: right;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="periodTableBody">
                        @php
                            $daysName = [1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis', 5 => 'Jumat', 6 => 'Sabtu', 7 => 'Minggu'];
                            $periods = $activeCategory ? $activeCategory->periods : collect();
                        @endphp
                        @forelse($periods as $period)
                        <tr data-day="{{ $period->day_of_week }}" style="{{ $period->day_of_week != 1 ? 'display:none;' : '' }}">
                            <td><span class="vx-badge badge-primary font-bold">{{ $daysName[$period->day_of_week] ?? $period->day_of_week }}</span></td>
                            <td>
                                @if($period->period_number)
                                    <span class="vx-badge badge-dark cell-mono">Jam Ke-{{ $period->period_number }}</span>
                                @else
                                    <span style="color: var(--text-muted);">—</span>
                                @endif
                            </td>
                            <td><strong style="color: var(--text-heading); font-size: 0.9rem;">{{ $period->name }}</strong></td>
                            <td><span class="cell-mono font-bold" style="color: var(--text-heading);"><i class='bx bx-time-five' style="color: var(--text-muted); font-size: 0.85rem;"></i> {{ substr($period->start_time, 0, 5) }}</span></td>
                            <td><span class="cell-mono font-bold" style="color: var(--text-heading);"><i class='bx bx-time-five' style="color: var(--text-muted); font-size: 0.85rem;"></i> {{ substr($period->end_time, 0, 5) }}</span></td>
                            <td>
                                @php
                                    $duration = \Carbon\Carbon::parse($period->start_time)->diffInMinutes(\Carbon\Carbon::parse($period->end_time));
                                @endphp
                                <span class="vx-badge badge-muted" style="font-weight: 600;">{{ $duration }} Menit</span>
                            </td>
                            <td>
                                @if($period->is_break)
                                    <span class="vx-badge badge-warning"><i class='bx bx-coffee'></i> Istirahat</span>
                                @else
                                    <span class="vx-badge badge-info"><i class='bx bx-book-reader'></i> KBM</span>
                                @endif
                            </td>
                            <td style="text-align: right;">
                                <div style="display: inline-flex; gap: 0.3rem; justify-content: flex-end;">
                                    <button class="btn btn-secondary btn-xs btn-icon-sm" onclick="openEditPeriodModal({{ $period->id }}, {{ $period->day_of_week }}, '{{ addslashes($period->name) }}', '{{ $period->period_number ?? '' }}', '{{ substr($period->start_time, 0, 5) }}', '{{ substr($period->end_time, 0, 5) }}', {{ $period->is_break ? 1 : 0 }})" title="Edit Jam Sesi">
                                        <i class='bx bx-edit-alt'></i>
                                    </button>
                                    <button class="btn btn-danger-light btn-xs btn-icon-sm" onclick="deletePeriod({{ $period->id }})" title="Hapus Sesi">
                                        <i class='bx bx-trash'></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" style="text-align: center; padding: 3rem; color: var(--text-muted);">
                                <i class='bx bx-time-five' style="font-size: 2.5rem; display: block; margin-bottom: 0.75rem; color: var(--text-muted);"></i>
                                Belum ada sesi jam pelajaran pada kategori ini.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- TAB 3: GURU & TENDIK (GTK DAPODIK) -->
    <div id="tab-teachers" class="tab-pane">
        <div class="panel" style="border-radius: var(--vx-radius-xl);">
            <div class="panel-header">
                <div class="panel-title">
                    <h2>Guru & Tenaga Kependidikan (GTK)</h2>
                    <p>Data identitas PTK, status kepegawaian Dapodik, dan akun login.</p>
                </div>
                <div class="panel-toolbar">
                    <div class="vx-search">
                        <i class='bx bx-search vx-search-icon'></i>
                        <input type="text" id="searchTeacherInput" placeholder="Cari nama, NIP, NUPTK..." oninput="filterTeachers()">
                    </div>
                    <select class="vx-select-filter" id="filterPtkSelect" onchange="filterTeachers()">
                        <option value="">Semua PTK</option>
                        <option value="Guru Mapel">Guru Mapel</option>
                        <option value="Guru BK">Guru BK</option>
                        <option value="Tenaga Administrasi">Tenaga Administrasi</option>
                        <option value="Kepala Sekolah">Kepala Sekolah</option>
                    </select>
                    <button type="button" class="btn btn-secondary btn-sm" onclick="openBulkImportModal('teachers')">
                        <i class='bx bx-file-blank text-primary'></i> Impor GTK Excel
                    </button>
                    <button class="btn btn-primary btn-sm" onclick="openModal('modalAddTeacher')">
                        <i class='bx bx-plus'></i> Tambah GTK
                    </button>
                </div>
            </div>
            <div class="table-wrap">
                <table class="clean-table" id="teachersTable">
                    <thead>
                        <tr>
                            <th>Nama Lengkap & Gelar</th>
                            <th>NIP / NUPTK</th>
                            <th>Jenis PTK</th>
                            <th>Status Kepegawaian</th>
                            <th>Pendidikan</th>
                            <th>Sertifikasi</th>
                            <th>Akun Login</th>
                            <th style="width: 110px; text-align: center;">Profil & Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($teachers as $t)
                        <tr data-name="{{ strtolower($t->full_name) }}" data-nip="{{ $t->nip }}"
                            data-nuptk="{{ $t->nuptk }}" data-ptk="{{ $t->ptk_type }}">
                            <td>
                                <div class="cell-title">
                                    {{ $t->front_title ? $t->front_title . ' ' : '' }}{{ $t->full_name }}{{ $t->back_title ? ', ' . $t->back_title : '' }}
                                </div>
                                <div class="cell-sub">
                                    {{ $t->gender === 'L' ? 'Laki-laki' : 'Perempuan' }} &bull; {{ $t->phone_number ?? '—' }}
                                </div>
                            </td>
                            <td>
                                <div class="cell-mono fw-600" style="color: var(--vx-text-heading);">NIP: {{ $t->nip ?? '—' }}</div>
                                <div class="cell-sub cell-mono">NUPTK: {{ $t->nuptk ?? '—' }}</div>
                            </td>
                            <td><span class="vx-badge badge-dark">{{ $t->ptk_type ?? 'Guru Mapel' }}</span></td>
                            <td><span class="vx-badge badge-info">{{ $t->employment_status }}</span></td>
                            <td style="color: var(--vx-text-body);">{{ $t->highest_education ?? 'S1' }}</td>
                            <td>
                                @if($t->is_certified)
                                    <span class="vx-badge badge-success">Bersertifikasi</span>
                                @else
                                    <span class="vx-badge badge-muted">Belum</span>
                                @endif
                            </td>
                            <td>
                                @if($t->user)
                                    <span class="vx-badge badge-primary" title="{{ $t->user->email }}">
                                        <i class='bx bx-envelope'></i> {{ $t->user->email }}
                                    </span>
                                @else
                                    <span class="vx-badge badge-muted">Tanpa Akun</span>
                                @endif
                            </td>
                            <td style="text-align: center;">
                                <a href="{{ route('teacher.profile', ['teacher_id' => $t->id]) }}" class="btn btn-secondary btn-xs" style="display: inline-flex; align-items: center; gap: 0.25rem; font-weight: 600;" title="Buka Profil Dapodik 4x6">
                                    <i class='bx bx-id-card text-primary'></i> Profil
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" style="text-align: center; padding: 3rem; color: var(--vx-text-muted);">
                                <i class='bx bxs-chalkboard' style="font-size: 2.5rem; display: block; margin-bottom: 0.75rem;"></i>
                                Belum ada data guru atau tenaga kependidikan.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- TAB 4: DATA SISWA (DAPODIK) -->
    <div id="tab-students" class="tab-pane">
        <div class="panel" style="border-radius: var(--vx-radius-xl);">
            <div class="panel-header">
                <div class="panel-title">
                    <h2>Peserta Didik (Dapodik)</h2>
                    <p>Identitas pokok siswa, nomor induk, status kesejahteraan, dan rombel.</p>
                </div>
                <div class="panel-toolbar" style="flex-wrap: wrap; gap: 0.5rem;">
                    <div class="vx-search">
                        <i class='bx bx-search vx-search-icon'></i>
                        <input type="text" id="searchStudentInput" placeholder="Cari nama, NIS, NISN, NIK..." oninput="filterStudents()">
                    </div>
                    <select class="vx-select-filter" id="filterStudentCohort" onchange="filterStudents()">
                        <option value="">Semua Angkatan</option>
                        @foreach($cohorts as $coh)
                            <option value="{{ $coh['id'] }}">Angkatan {{ $coh['name'] }} ({{ $coh['total'] }})</option>
                        @endforeach
                    </select>
                    <select class="vx-select-filter" id="filterStudentClass" onchange="filterStudents()">
                        <option value="">Semua Rombel</option>
                        @foreach($classes as $c)
                            <option value="{{ $c->name }}">{{ $c->name }}</option>
                        @endforeach
                    </select>
                    <select class="vx-select-filter" id="filterStudentStatus" onchange="filterStudents()">
                        <option value="active">Siswa Aktif</option>
                        <option value="graduated">Alumni / Lulus</option>
                        <option value="transferred">Pindah</option>
                        <option value="dropout">Putus Sekolah</option>
                        <option value="all">Semua Status</option>
                    </select>
                    <button class="btn btn-secondary btn-sm" onclick="openModal('modalPromotion')">
                        <i class='bx bx-upvote'></i> Kenaikan Kelas
                    </button>
                    <button type="button" class="btn btn-secondary btn-sm" onclick="openBulkImportModal('students')">
                        <i class='bx bx-file-blank text-success'></i> Impor Siswa Excel
                    </button>
                    <button class="btn btn-primary btn-sm" onclick="openModal('modalAddStudent')">
                        <i class='bx bx-plus'></i> Tambah Siswa
                    </button>

                    <!-- View Mode Toggle (Opsi A Clean Table vs Opsi B 4x6 Photo Cards) -->
                    <div style="display: inline-flex; align-items: center; background: rgba(15, 23, 42, 0.06); padding: 3px; border-radius: 20px; border: 1px solid rgba(226, 232, 240, 0.8); gap: 2px;">
                        <button type="button" id="btnModeTable" onclick="setStudentViewMode('table')" title="Mode Tabel Rapi (Opsi A)" style="border: none; background: #fff; color: var(--vx-primary); padding: 5px 12px; border-radius: 16px; font-size: 0.78rem; font-weight: 700; cursor: pointer; display: flex; align-items: center; gap: 4px; box-shadow: 0 1px 3px rgba(0,0,0,0.08); transition: all 0.2s ease;">
                            <i class='bx bx-list-ul'></i> Tabel
                        </button>
                        <button type="button" id="btnModeCards" onclick="setStudentViewMode('cards')" title="Mode Kartu Foto 4x6 (Opsi B)" style="border: none; background: transparent; color: #64748B; padding: 5px 12px; border-radius: 16px; font-size: 0.78rem; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 4px; transition: all 0.2s ease;">
                            <i class='bx bxs-grid'></i> Kartu Foto 4x6
                        </button>
                    </div>
                </div>
            </div>

            <!-- VIEW 1: CLEAN SCANDINAVIAN TABLE (OPSI A) -->
            <div class="table-wrap" id="studentsTableView">
                <table class="clean-table" id="studentsTable">
                    <thead>
                        <tr>
                            <th>Nama Peserta Didik</th>
                            <th>NIS / NISN</th>
                            <th>Angkatan (Tahun Masuk)</th>
                            <th>Rombel Aktif</th>
                            <th>L/P</th>
                            <th>Bantuan KIP/KPS</th>
                            <th>Riwayat & Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($students as $s)
                        <tr class="student-item-row"
                            data-name="{{ strtolower($s->full_name) }}"
                            data-nis="{{ $s->nis }}"
                            data-nisn="{{ $s->nisn }}"
                            data-nik="{{ $s->nik }}"
                            data-class="{{ $s->schoolClass?->name }}"
                            data-cohort="{{ $s->entry_academic_year_id ?? '' }}"
                            data-status="{{ $s->status }}">
                            <td>
                                <div style="display: flex; align-items: center; gap: 0.75rem;">
                                    <div style="width: 36px; height: 36px; border-radius: 50%; overflow: hidden; background: {{ $s->gender === 'L' ? '#EEF2FF' : '#FFF1F2' }}; color: {{ $s->gender === 'L' ? '#4F46E5' : '#F43F5E' }}; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 0.82rem; flex-shrink: 0; border: 1px solid rgba(0,0,0,0.08);">
                                        @if($s->photo_url)
                                            <img src="{{ $s->photo_url }}" alt="{{ $s->full_name }}" style="width:100%; height:100%; object-fit:cover;">
                                        @else
                                            {{ strtoupper(substr($s->full_name, 0, 1)) }}
                                        @endif
                                    </div>
                                    <div>
                                        <div class="cell-title">{{ $s->full_name }}</div>
                                        <div class="cell-sub">{{ $s->birth_place ?? 'Jakarta' }}, {{ $s->birth_date ? $s->birth_date->format('d M Y') : '—' }} &bull; NIK: {{ $s->nik ?? '—' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="cell-mono fw-600" style="color: var(--vx-text-heading);">NIS: {{ $s->nis }}</div>
                                <div class="cell-sub cell-mono">NISN: {{ $s->nisn ?? '—' }}</div>
                            </td>
                            <td>
                                <span class="vx-badge badge-primary">
                                    <i class='bx bx-calendar-event' style="font-size: 0.8rem; margin-right: 2px;"></i>
                                    {{ $s->entryAcademicYear?->name ?? 'Belum Diatur' }}
                                </span>
                                <div class="cell-sub" style="margin-top: 3px; text-transform: capitalize;">
                                    {{ $s->registration_type === 'pindahan' ? 'Pindahan (' . ($s->previous_school ?: 'Sekolah Lain') . ')' : ($s->registration_type ?: 'Siswa Baru') }}
                                </div>
                            </td>
                            <td>
                                <span class="vx-badge badge-dark">{{ $s->schoolClass?->name ?? 'Belum ada rombel' }}</span>
                                @if($s->status !== 'active')
                                    <div class="cell-sub" style="margin-top: 2px;">
                                        <span class="vx-badge {{ $s->status === 'graduated' ? 'badge-success' : 'badge-danger' }}" style="font-size: 0.68rem; padding: 1px 6px;">
                                            {{ ucfirst($s->status) }}
                                        </span>
                                    </div>
                                @endif
                            </td>
                            <td>
                                <span class="vx-badge {{ $s->gender === 'L' ? 'badge-info' : 'badge-warning' }}">
                                    {{ $s->gender === 'L' ? 'L' : 'P' }}
                                </span>
                            </td>
                            <td>
                                @if($s->kip_receiver)
                                    <span class="vx-badge badge-success">KIP Aktif</span>
                                @elseif($s->kps_receiver)
                                    <span class="vx-badge badge-warning">KPS</span>
                                @else
                                    <span class="vx-badge badge-muted">Reguler</span>
                                @endif
                            </td>
                            <td>
                                <div style="display: flex; gap: 4px; flex-wrap: wrap;">
                                    <a href="{{ route('student.profile', ['student_id' => $s->id]) }}" class="btn btn-primary-light btn-xs" style="padding: 4px 8px; font-size: 0.75rem;" title="Detail & Edit Biodata Dapodik Siswa" target="_blank">
                                        <i class='bx bx-user-pin'></i> Biodata
                                    </a>
                                    <button type="button" class="btn btn-secondary btn-xs" style="padding: 4px 8px; font-size: 0.75rem;" onclick="viewStudentHistory({{ $s->id }}, '{{ addslashes($s->full_name) }}')">
                                        <i class='bx bx-history'></i> Riwayat
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 3rem; color: var(--vx-text-muted);">
                                <i class='bx bxs-user-x' style="font-size: 2.5rem; display: block; margin-bottom: 0.75rem;"></i>
                                Belum ada data peserta didik.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- VIEW 2: VISUAL CARD & PROFILE GRID (OPSI B) -->
            <div class="students-card-grid-wrap" id="studentsCardGridView" style="display: none; padding: 1.5rem; background: rgba(248, 250, 252, 0.4);">
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(230px, 1fr)); gap: 1.25rem;" id="studentsCardContainer">
                    @forelse($students as $s)
                    <div class="student-item-card"
                         data-name="{{ strtolower($s->full_name) }}"
                         data-nis="{{ $s->nis }}"
                         data-nisn="{{ $s->nisn }}"
                         data-nik="{{ $s->nik }}"
                         data-class="{{ $s->schoolClass?->name }}"
                         data-cohort="{{ $s->entry_academic_year_id ?? '' }}"
                         data-status="{{ $s->status }}"
                         style="background: #ffffff; border-radius: 20px; border: 1px solid rgba(226, 232, 240, 0.85); box-shadow: 0 4px 16px -2px rgba(15, 23, 42, 0.05); padding: 1.25rem 1rem; display: flex; flex-direction: column; align-items: center; text-align: center; transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1); position: relative;">
                        
                        <!-- 4x6 Photo Frame -->
                        <div style="width: 95px; height: 126px; border-radius: 12px; overflow: hidden; background: {{ $s->gender === 'L' ? '#2563EB' : '#DC2626' }}; box-shadow: 0 4px 14px rgba(0, 0, 0, 0.12); margin-bottom: 0.85rem; display: flex; align-items: center; justify-content: center; position: relative; border: 2.5px solid #FFFFFF;">
                            @if($s->photo_url)
                                <img src="{{ $s->photo_url }}" alt="{{ $s->full_name }}" style="width: 100%; height: 100%; object-fit: cover;">
                            @else
                                <div style="display: flex; flex-direction: column; align-items: center; color: #fff;">
                                    <i class='bx bxs-user' style="font-size: 3rem;"></i>
                                    <span style="font-size: 0.62rem; font-weight: 700; opacity: 0.9;">FOTO 4x6</span>
                                </div>
                            @endif
                            <span class="vx-badge {{ $s->status === 'active' ? 'badge-success' : 'badge-danger' }}" style="position: absolute; bottom: 4px; right: 4px; font-size: 0.6rem; padding: 1px 6px;">
                                {{ $s->status === 'active' ? 'Hadir' : ucfirst($s->status) }}
                            </span>
                        </div>

                        <div style="font-size: 0.92rem; font-weight: 800; color: var(--vx-text-heading); margin-bottom: 0.2rem; line-height: 1.25; max-width: 100%; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                            {{ $s->full_name }}
                        </div>

                        <div style="display: flex; align-items: center; justify-content: center; gap: 0.35rem; margin-bottom: 0.45rem; flex-wrap: wrap;">
                            <span class="cell-mono" style="font-size: 0.72rem; color: #64748B; background: #F1F5F9; padding: 2px 6px; border-radius: 6px; font-weight: 600;">NISN: {{ $s->nisn ?? '—' }}</span>
                            <span class="vx-badge badge-primary" style="font-size: 0.68rem; padding: 2px 6px;">
                                {{ $s->schoolClass?->name ?? 'Belum Rombel' }}
                            </span>
                        </div>

                        <div style="font-size: 0.72rem; color: #94A3B8; margin-bottom: 0.85rem;">
                            Angkatan: <strong>{{ $s->entryAcademicYear?->name ?? '2026/2027' }}</strong>
                        </div>

                        <!-- Action Buttons -->
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.45rem; width: 100%; margin-top: auto;">
                            <a href="{{ route('student.profile', ['student_id' => $s->id]) }}" class="btn btn-secondary btn-sm" style="font-size: 0.72rem; padding: 5px 6px; justify-content: center; border-radius: 8px;" target="_blank">
                                <i class='bx bx-user-pin'></i> Biodata
                            </a>
                            <button type="button" class="btn btn-primary btn-sm" style="font-size: 0.72rem; padding: 5px 6px; justify-content: center; border-radius: 8px;" onclick="viewStudentHistory({{ $s->id }}, '{{ addslashes($s->full_name) }}')">
                                <i class='bx bx-history'></i> Riwayat
                            </button>
                        </div>
                    </div>
                    @empty
                    <div style="grid-column: 1 / -1; text-align: center; padding: 3rem; color: var(--vx-text-muted);">
                        <i class='bx bxs-user-x' style="font-size: 3rem; display: block; margin-bottom: 0.5rem;"></i>
                        Belum ada data peserta didik.
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- TAB 5: ORANG TUA / WALI -->
    <div id="tab-parents" class="tab-pane">
        <div class="panel" style="border-radius: var(--vx-radius-xl);">
            <div class="panel-header">
                <div class="panel-title">
                    <h2>Orang Tua / Wali Murid</h2>
                    <p>Pemetaan akun wali murid ke satu atau beberapa siswa (Multi-Anak).</p>
                </div>
            </div>
            <div class="table-wrap">
                <table class="clean-table">
                    <thead>
                        <tr>
                            <th>Nama Wali</th>
                            <th>NIK & No KK</th>
                            <th>Pekerjaan & Penghasilan</th>
                            <th>Kontak WhatsApp</th>
                            <th>Siswa Terhubung</th>
                            <th>Akun Login</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($parents as $p)
                        <tr>
                            <td>
                                <div class="cell-title">{{ $p->full_name }}</div>
                                <div class="cell-sub">Pendidikan: {{ $p->education ?? '—' }}</div>
                            </td>
                            <td>
                                <div class="cell-mono fw-600" style="color: var(--vx-text-heading);">{{ $p->nik ?? '—' }}</div>
                                <div class="cell-sub cell-mono">KK: {{ $p->no_kk ?? '—' }}</div>
                            </td>
                            <td>
                                <div style="color: var(--vx-text-body);">{{ $p->occupation ?? '—' }}</div>
                                <div class="cell-sub">{{ $p->monthly_income ? (is_numeric($p->monthly_income) ? 'Rp ' . number_format((float)$p->monthly_income, 0, ',', '.') : $p->monthly_income) : '—' }}</div>
                            </td>
                            <td>
                                @if($p->whatsapp_number)
                                    <span class="vx-badge badge-success">
                                        <i class='bx bxl-whatsapp'></i> {{ $p->whatsapp_number }}
                                    </span>
                                @else
                                    <span class="vx-badge badge-muted">{{ $p->phone ?? '—' }}</span>
                                @endif
                            </td>
                            <td>
                                @foreach($p->students as $student)
                                    <span class="vx-badge badge-primary" style="margin: 1px 2px;">
                                        {{ $student->full_name }}
                                    </span>
                                @endforeach
                            </td>
                            <td>
                                @if($p->user)
                                    <span class="vx-badge badge-success"><span class="badge-dot"></span> Terdaftar</span>
                                    <div class="cell-sub">{{ $p->user->email }}</div>
                                @else
                                    <span class="vx-badge badge-muted">Tanpa Akun</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 3rem; color: var(--vx-text-muted);">
                                <i class='bx bxs-group' style="font-size: 2.5rem; display: block; margin-bottom: 0.75rem;"></i>
                                Belum ada data orang tua / wali murid.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- TAB 6: KONSENTRASI & ROMBEL -->
    <div id="tab-classes" class="tab-pane">
        <div class="panel" style="border-radius: var(--vx-radius-xl);">
            <div class="panel-header">
                <div class="panel-title">
                    <h2>{{ (($school->level ?? '') === 'SMK' || $isVocational) ? 'Konsentrasi Keahlian & Rombongan Belajar' : 'Jurusan & Rombongan Belajar' }}</h2>
                    <p>Struktur {{ (($school->level ?? '') === 'SMK' || $isVocational) ? 'konsentrasi/program keahlian' : 'jurusan/program' }} dan daftar rombongan belajar aktif per tahun ajaran.</p>
                </div>
                <div class="panel-toolbar" style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                    @if(($school->level ?? '') === 'SMK' || $isVocational)
                        <button type="button" class="btn btn-secondary btn-sm" onclick="openModal('modalAddVocationalConcentration')">
                            <i class='bx bx-award'></i> Tambah Konsentrasi
                        </button>
                    @endif
                    <button type="button" class="btn btn-primary btn-sm" onclick="openModal('modalAddClass')">
                        <i class='bx bx-plus'></i> Tambah Rombel
                    </button>
                </div>
            </div>

            <!-- Filter & Search Toolbar -->
            <div style="display: flex; gap: 0.75rem; padding: 1rem 1.5rem; background: var(--vx-body-bg); border-bottom: 1px solid var(--vx-border-light); flex-wrap: wrap; align-items: center;">
                <div style="flex: 1; min-width: 220px; position: relative;">
                    <i class='bx bx-search' style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--vx-text-muted);"></i>
                    <input type="text" id="searchClassInput" onkeyup="filterClasses()" class="vx-input" style="padding-left: 36px; width: 100%; font-size: 0.85rem;" placeholder="Cari nama rombel (misal: X PPLG 1)...">
                </div>
                <div style="min-width: 160px;">
                    <select id="filterClassGrade" onchange="filterClasses()" class="vx-input" style="font-size: 0.85rem; width: 100%;">
                        <option value="">Semua Tingkat</option>
                        @if(($school->level ?? '') === 'SMK' || $isVocational)
                            <option value="10">Kelas 10 (Fase E)</option>
                            <option value="11">Kelas 11 (Fase F)</option>
                            <option value="12">Kelas 12 (Fase F)</option>
                            <option value="13">Kelas 13 (Fast Track)</option>
                        @elseif(($school->level ?? '') === 'SMA')
                            <option value="10">Kelas 10</option>
                            <option value="11">Kelas 11</option>
                            <option value="12">Kelas 12</option>
                        @elseif(($school->level ?? '') === 'SMP')
                            <option value="7">Kelas 7</option>
                            <option value="8">Kelas 8</option>
                            <option value="9">Kelas 9</option>
                        @elseif(($school->level ?? '') === 'SD')
                            <option value="1">Kelas 1</option>
                            <option value="2">Kelas 2</option>
                            <option value="3">Kelas 3</option>
                            <option value="4">Kelas 4</option>
                            <option value="5">Kelas 5</option>
                            <option value="6">Kelas 6</option>
                        @else
                            <option value="10">Kelas 10</option>
                            <option value="11">Kelas 11</option>
                            <option value="12">Kelas 12</option>
                        @endif
                    </select>
                </div>
                @if(($school->level ?? '') === 'SMK' || $isVocational)
                    <div style="min-width: 200px;">
                        <select id="filterClassConcentration" onchange="filterClasses()" class="vx-input" style="font-size: 0.85rem; width: 100%;">
                            <option value="">Semua Konsentrasi / Program</option>
                            @foreach($vocationalConcentrations as $conc)
                                <option value="{{ $conc->id }}">{{ $conc->name }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif
            </div>

            <div class="table-wrap">
                <table class="clean-table" id="classesTable">
                    <thead>
                        <tr>
                            <th>Nama Rombel</th>
                            <th>Tingkat</th>
                            <th>Kurikulum</th>
                            <th>{{ (($school->level ?? '') === 'SMK' || $isVocational) ? 'Konsentrasi / Program' : 'Jurusan / Program' }}</th>
                            <th>Wali Kelas</th>
                            <th>Kapasitas</th>
                            <th>Jumlah Siswa</th>
                            <th style="text-align: right;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($classes as $c)
                        <tr data-name="{{ strtolower($c->name) }}" data-grade="{{ $c->grade_level }}" data-conc="{{ $c->vocational_concentration_id ?? '' }}">
                            <td>
                                <div class="cell-title fw-600" style="color: var(--vx-primary);">{{ $c->name }}</div>
                                <div class="cell-sub cell-mono" style="font-size: 0.72rem;">TA: {{ $c->academic_year ?? ($c->academicYear?->name ?? '—') }}</div>
                            </td>
                            <td><span class="vx-badge badge-primary">Kelas {{ $c->grade_level }}</span></td>
                            <td>
                                @if($c->curriculum)
                                    <span class="vx-badge badge-primary cell-mono" style="font-size: 0.72rem;" title="{{ $c->curriculum->name }}">
                                        <i class='bx bx-book-bookmark' style="font-size: 0.7rem;"></i> {{ $c->curriculum->code }}
                                    </span>
                                @else
                                    <span class="vx-badge badge-muted" style="font-size: 0.72rem;">—</span>
                                @endif
                            </td>
                            <td>
                                @if($c->vocationalConcentration)
                                    <div><span class="vx-badge badge-warning" style="font-size: 0.72rem;">{{ $c->vocationalConcentration->name }}</span></div>
                                    <div class="cell-sub cell-mono">Program: {{ $c->vocationalProgram?->code ?? $c->vocationalConcentration->program?->code }}</div>
                                @elseif($c->vocationalProgram)
                                    <div><span class="vx-badge badge-info" style="font-size: 0.72rem;">{{ $c->vocationalProgram->name }}</span></div>
                                    <div class="cell-sub cell-mono">Fase E Dasar Kejuruan</div>
                                @elseif($c->department)
                                    <div class="cell-title" style="font-size: 0.85rem;">{{ $c->department->name }}</div>
                                    <div class="cell-sub cell-mono">{{ $c->major ?: $c->department->code }}</div>
                                @else
                                    <span style="color: var(--vx-text-muted);">{{ $c->major ?: '—' }}</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $homeroomName = $c->homeroomTeacher?->teacher?->full_name 
                                        ?? ($c->homeroomTeacher?->name ?? '—');
                                @endphp
                                <span style="font-weight: 500;">{{ $homeroomName }}</span>
                            </td>
                            <td style="color: var(--vx-text-muted);">{{ $c->capacity ?? 36 }}</td>
                            <td>
                                @php $count = $c->students_count ?? $c->students->count(); @endphp
                                <span class="fw-600" style="color: var(--vx-text-heading);">{{ $count }}</span>
                                <span style="color: var(--vx-text-muted); font-size: 0.78rem;"> siswa</span>
                            </td>
                            <td style="text-align: right;">
                                <div style="display: flex; gap: 0.35rem; justify-content: flex-end;">
                                    <button type="button" class="btn btn-secondary btn-xs btn-icon-sm"
                                        onclick="openEditClassModal({{ $c->id }}, '{{ addslashes($c->name) }}', {{ $c->grade_level }}, '{{ $c->academic_year_id ?? '' }}', '{{ $c->curriculum_id ?? '' }}', '{{ $c->vocational_concentration_id ?? '' }}', '{{ $c->vocational_program_id ?? '' }}', '{{ $c->department_id ?? '' }}', '{{ $c->homeroom_teacher_id ?? '' }}', '{{ addslashes($c->major ?? '') }}')"
                                        title="Ubah Rombel">
                                        <i class='bx bx-edit'></i>
                                    </button>
                                    <button type="button" class="btn btn-danger-light btn-xs btn-icon-sm"
                                        onclick="deleteClass({{ $c->id }}, '{{ addslashes($c->name) }}')"
                                        title="Hapus Rombel">
                                        <i class='bx bx-trash'></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" style="text-align: center; padding: 3rem; color: var(--vx-text-muted);">
                                <i class='bx bxs-school' style="font-size: 2.5rem; display: block; margin-bottom: 0.75rem;"></i>
                                Belum ada data rombel.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- TAB 7: MATA PELAJARAN -->
    <div id="tab-subjects" class="tab-pane">
        <div class="panel" style="border-radius: var(--vx-radius-xl);">
            <div class="panel-header">
                <div class="panel-title">
                    <h2>Mata Pelajaran & Kelompok Kurikulum</h2>
                    <p>Katalog mata pelajaran, kelompok mapel mengikuti kurikulum operasional sekolah, alokasi beban JP, dan standar ketuntasan (KKM/KKTP).</p>
                </div>
                <div class="panel-toolbar" style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                    <a href="{{ route('admin.schedules') }}" class="btn btn-secondary btn-sm">
                        <i class='bx bx-calendar-event'></i> Susun Matriks Jadwal KBM
                    </a>
                    <button type="button" class="btn btn-secondary btn-sm" onclick="openAddCurriculumSubjectModal()">
                        <i class='bx bx-book-bookmark'></i> Alokasi ke Kurikulum
                    </button>
                    <button type="button" class="btn btn-primary btn-sm" onclick="openModal('modalAddSubject')">
                        <i class='bx bx-plus'></i> Tambah Mapel
                    </button>
                </div>
            </div>

            <!-- Filter & Search Toolbar -->
            <div style="display: flex; gap: 0.75rem; padding: 1rem 1.5rem; background: var(--vx-body-bg); border-bottom: 1px solid var(--vx-border-light); flex-wrap: wrap; align-items: center;">
                <div style="flex: 1; min-width: 220px; position: relative;">
                    <i class='bx bx-search' style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--vx-text-muted);"></i>
                    <input type="text" id="searchSubjectInput" onkeyup="filterSubjects()" class="vx-input" style="padding-left: 36px; width: 100%; font-size: 0.85rem;" placeholder="Cari nama atau kode mata pelajaran...">
                </div>
                <div style="min-width: 180px;">
                    <select id="filterSubjectCurriculum" onchange="filterSubjects()" class="vx-input" style="font-size: 0.85rem; width: 100%;">
                        <option value="">Semua Kurikulum</option>
                        @foreach($curriculums as $cOpt)
                            <option value="{{ $cOpt->id }}">{{ $cOpt->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div style="min-width: 180px;">
                    <select id="filterSubjectGroup" onchange="filterSubjects()" class="vx-input" style="font-size: 0.85rem; width: 100%;">
                        <option value="">Semua Kelompok Mapel</option>
                        <option value="WAJIB_UMUM">Kelompok Wajib Umum</option>
                        <option value="PEMINATAN_PILIHAN">Kelompok Kejuruan / Pilihan</option>
                        <option value="P5_PROJEK">Projek Profil Pelajar Pancasila (P5)</option>
                        <option value="MULOK">Muatan Lokal (Mulok)</option>
                        <option value="KEAGAMAAN">Keagamaan / Kepesantrenan</option>
                    </select>
                </div>
            </div>

            <div class="table-wrap">
                <table class="clean-table" id="subjectsTable">
                    <thead>
                        <tr>
                            <th>Mata Pelajaran</th>
                            <th>Kode</th>
                            <th>Kurikulum Terkait</th>
                            <th>Kelompok Mapel (Kurikulum)</th>
                            <th>Alokasi Jam/Minggu</th>
                            <th>Standar KKM / KKTP</th>
                            <th>Jadwal Terdaftar</th>
                            <th>Status</th>
                            <th style="text-align: right;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($subjects ?? [] as $subj)
                        @php
                            $primaryCs = $subj->curriculumSubjects->first();
                            $allCurriculumIds = $subj->curriculumSubjects->pluck('curriculum_id')->implode(',');
                            $groupCode = $primaryCs?->subject_group ?? $subj->group;
                            $hours = $primaryCs?->hours_per_week ?? $subj->weekly_hours;
                            $kkm = $primaryCs?->passing_grade ?? $subj->passing_grade;
                        @endphp
                        <tr data-name="{{ strtolower($subj->name) }} {{ strtolower($subj->code) }}"
                            data-curriculum="{{ $allCurriculumIds }}"
                            data-group="{{ $groupCode }}">
                            <td>
                                <div class="cell-title fw-600" style="color: var(--vx-text-heading); display: flex; align-items: center; gap: 0.45rem;">
                                    <span style="width: 11px; height: 11px; border-radius: 50%; background: {{ $subj->resolved_color }}; display: inline-block; flex-shrink: 0;"></span>
                                    {{ $subj->name }}
                                </div>
                                <div class="cell-sub cell-mono" style="font-size: 0.72rem;">Kategori: {{ str_replace('_', ' ', $subj->category) }}</div>
                            </td>
                            <td><span class="vx-badge cell-mono" style="background: {{ $subj->resolved_color }}; color: #fff;">{{ $subj->code }}</span></td>
                            <td>
                                @if($subj->curriculumSubjects->count() > 0)
                                    <div style="display: flex; flex-direction: column; gap: 0.25rem;">
                                        @foreach($subj->curriculumSubjects as $csItem)
                                            <span class="vx-badge badge-primary cell-mono" style="font-size: 0.7rem;" title="{{ $csItem->curriculum?->name }}">
                                                <i class='bx bx-book-bookmark' style="font-size: 0.65rem;"></i> {{ $csItem->curriculum?->code ?? 'KURIKULUM' }}
                                            </span>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="vx-badge badge-muted" style="font-size: 0.72rem;">Belum Terpetakan</span>
                                @endif
                            </td>
                            <td>
                                @if($primaryCs)
                                    <span class="vx-badge {{ $primaryCs->resolved_group_badge }}" style="font-size: 0.75rem;">
                                        {{ $primaryCs->resolved_group_name }}
                                    </span>
                                @else
                                    <span class="vx-badge badge-muted" style="font-size: 0.75rem;">{{ $groupCode }}</span>
                                @endif
                            </td>
                            <td>
                                <span class="fw-600" style="color: var(--vx-text-heading); font-size: 0.9rem;">{{ $hours }}</span>
                                <span class="cell-sub" style="font-size: 0.75rem;"> JP / minggu</span>
                            </td>
                            <td>
                                <span class="vx-badge badge-light cell-mono fw-600" style="font-size: 0.8rem; color: var(--vx-primary);">
                                    {{ number_format((float)$kkm, 1) }}
                                </span>
                            </td>
                            <td>
                                <span class="fw-600" style="color: var(--vx-text-heading);">{{ $subj->schedules_count ?? $subj->schedules->count() }}</span>
                                <span class="cell-sub" style="font-size: 0.75rem;"> sesi KBM</span>
                            </td>
                            <td>
                                @if($subj->is_active)
                                    <span class="vx-badge badge-success"><span class="badge-dot"></span> Aktif</span>
                                @else
                                    <span class="vx-badge badge-muted">Non-aktif</span>
                                @endif
                            </td>
                            <td style="text-align: right;">
                                <div style="display: flex; gap: 0.35rem; justify-content: flex-end;">
                                    <button type="button" class="btn btn-secondary btn-xs btn-icon-sm"
                                        onclick="openEditSubjectModal({{ $subj->id }}, '{{ addslashes($subj->name) }}', '{{ addslashes($subj->code) }}', '{{ $primaryCs?->curriculum_id ?? '' }}', '{{ $groupCode }}', {{ $hours }}, {{ (float)$kkm }}, {{ $subj->is_active ? 1 : 0 }}, '{{ $subj->category }}', '{{ $subj->resolved_color }}')"
                                        title="Ubah Mapel & Kelompok">
                                        <i class='bx bx-edit'></i>
                                    </button>
                                    <button type="button" class="btn btn-danger-light btn-xs btn-icon-sm"
                                        onclick="deleteSubject({{ $subj->id }}, '{{ addslashes($subj->name) }}')"
                                        title="Hapus Mapel">
                                        <i class='bx bx-trash'></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" style="text-align: center; padding: 3rem; color: var(--vx-text-muted);">
                                <i class='bx bx-book-open' style="font-size: 2.5rem; display: block; margin-bottom: 0.75rem;"></i>
                                Belum ada data mata pelajaran. Klik <strong>Tambah Mapel</strong> untuk memulai.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div><!-- /.tab content area -->


<!-- ================================================================
     MODALS
================================================================ -->

<!-- MODAL: Tambah Tahun Ajaran -->
<div class="vx-modal-backdrop modal-backdrop" id="modalAddYear">
    <div class="vx-modal modal-dialog">
        <div class="vx-modal-header modal-title-bar">
            <h3 class="vx-modal-title">
                <span class="modal-title-icon"><i class='bx bx-calendar-plus'></i></span>
                Tambah Tahun Ajaran
            </h3>
            <button class="vx-modal-close close-btn" onclick="closeModal('modalAddYear')">&times;</button>
        </div>
        <form id="formAddYear" onsubmit="submitAddYear(event)">
            <div class="vx-modal-body modal-content">
                <div class="vx-form-grid form-grid-2">
                    <div class="form-row span-2">
                        <label class="vx-label">Nama Tahun Ajaran *</label>
                        <input type="text" class="vx-input form-input-clean" name="name" placeholder="2026/2027" required>
                    </div>
                    <div class="form-row">
                        <label class="vx-label">Tanggal Mulai *</label>
                        <input type="date" class="vx-input form-input-clean" name="start_date" required>
                    </div>
                    <div class="form-row">
                        <label class="vx-label">Tanggal Selesai *</label>
                        <input type="date" class="vx-input form-input-clean" name="end_date" required>
                    </div>
                </div>
            </div>
            <div class="vx-modal-footer modal-bottom">
                <button type="button" class="btn btn-secondary" onclick="closeModal('modalAddYear')">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Tahun Ajaran</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL: Tambah Kategori Jadwal -->
<div class="vx-modal-backdrop modal-backdrop" id="modalAddCategory">
    <div class="vx-modal modal-dialog">
        <div class="vx-modal-header modal-title-bar">
            <h3 class="vx-modal-title">
                <span class="modal-title-icon"><i class='bx bx-time-five'></i></span>
                Tambah Kategori Jadwal
            </h3>
            <button class="vx-modal-close close-btn" onclick="closeModal('modalAddCategory')">&times;</button>
        </div>
        <form id="formAddCategory" onsubmit="submitAddCategory(event)">
            <div class="vx-modal-body modal-content">
                <div class="vx-form-grid form-grid-2">
                    <div class="form-row span-2">
                        <label class="vx-label">Nama Kategori Jadwal *</label>
                        <input type="text" class="vx-input form-input-clean" name="name" placeholder="Jam Ramadhan 2026" required>
                    </div>
                    <div class="form-row">
                        <label class="vx-label">Kode Singkat *</label>
                        <input type="text" class="vx-input form-input-clean" name="code" placeholder="RAMADHAN_26" required>
                    </div>
                    <div class="form-row">
                        <label class="vx-label">Jadikan Jadwal Utama?</label>
                        <select class="vx-input form-input-clean" name="is_default">
                            <option value="0">Tidak (Kategori Tambahan)</option>
                            <option value="1">Ya (Langsung Aktifkan)</option>
                        </select>
                    </div>
                    <div class="form-row">
                        <label class="vx-label">Tanggal Mulai Berlaku</label>
                        <input type="date" class="vx-input form-input-clean" name="effective_start_date">
                    </div>
                    <div class="form-row">
                        <label class="vx-label">Tanggal Selesai Berlaku</label>
                        <input type="date" class="vx-input form-input-clean" name="effective_end_date">
                    </div>
                    <div class="form-row span-2">
                        <label class="vx-label">Keterangan / Deskripsi</label>
                        <input type="text" class="vx-input form-input-clean" name="description" placeholder="Catatan jam KBM atau ujian khusus">
                    </div>
                </div>
            </div>
            <div class="vx-modal-footer modal-bottom">
                <button type="button" class="btn btn-secondary" onclick="closeModal('modalAddCategory')">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Kategori</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL: Edit Kategori Jadwal -->
<div class="vx-modal-backdrop modal-backdrop" id="modalEditCategory">
    <div class="vx-modal modal-dialog">
        <div class="vx-modal-header modal-title-bar">
            <h3 class="vx-modal-title">
                <span class="modal-title-icon"><i class='bx bx-edit-alt'></i></span>
                Ubah Kategori Jadwal
            </h3>
            <button class="vx-modal-close close-btn" onclick="closeModal('modalEditCategory')">&times;</button>
        </div>
        <form id="formEditCategory" onsubmit="submitEditCategory(event)">
            <input type="hidden" id="editCatId" name="id">
            <div class="vx-modal-body modal-content">
                <div class="vx-form-grid form-grid-2">
                    <div class="form-row span-2">
                        <label class="vx-label">Nama Kategori Jadwal *</label>
                        <input type="text" class="vx-input form-input-clean" id="editCatName" name="name" required>
                    </div>
                    <div class="form-row">
                        <label class="vx-label">Kode Singkat *</label>
                        <input type="text" class="vx-input form-input-clean" id="editCatCode" name="code" required>
                    </div>
                    <div class="form-row">
                        <label class="vx-label">Status Aktif</label>
                        <select class="vx-input form-input-clean" id="editCatActive" name="is_active">
                            <option value="1">Aktif</option>
                            <option value="0">Non-aktif</option>
                        </select>
                    </div>
                    <div class="form-row">
                        <label class="vx-label">Tanggal Mulai</label>
                        <input type="date" class="vx-input form-input-clean" id="editCatStart" name="effective_start_date">
                    </div>
                    <div class="form-row">
                        <label class="vx-label">Tanggal Selesai</label>
                        <input type="date" class="vx-input form-input-clean" id="editCatEnd" name="effective_end_date">
                    </div>
                    <div class="form-row span-2">
                        <label class="vx-label">Deskripsi</label>
                        <input type="text" class="vx-input form-input-clean" id="editCatDesc" name="description">
                    </div>
                </div>
            </div>
            <div class="vx-modal-footer modal-bottom">
                <button type="button" class="btn btn-secondary" onclick="closeModal('modalEditCategory')">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL: Tambah Sesi Jam Pelajaran -->
<div class="vx-modal-backdrop modal-backdrop" id="modalAddPeriod">
    <div class="vx-modal modal-dialog">
        <div class="vx-modal-header modal-title-bar">
            <h3 class="vx-modal-title">
                <span class="modal-title-icon"><i class='bx bx-plus-circle'></i></span>
                Tambah Sesi Jam Pelajaran
            </h3>
            <button class="vx-modal-close close-btn" onclick="closeModal('modalAddPeriod')">&times;</button>
        </div>
        <form id="formAddPeriod" onsubmit="submitAddPeriod(event)">
            <div class="vx-modal-body modal-content">
                <div class="vx-form-grid form-grid-2">
                    <div class="form-row">
                        <label class="vx-label">Hari *</label>
                        <select class="vx-input form-input-clean" name="day_of_week" id="periodInputDay" required>
                            <option value="1">Senin</option>
                            <option value="2">Selasa</option>
                            <option value="3">Rabu</option>
                            <option value="4">Kamis</option>
                            <option value="5">Jumat</option>
                            <option value="6">Sabtu</option>
                        </select>
                    </div>
                    <div class="form-row">
                        <label class="vx-label">Tipe Sesi *</label>
                        <select class="vx-input form-input-clean" name="is_break" id="periodIsBreak">
                            <option value="0">KBM (Jam Pelajaran)</option>
                            <option value="1">Istirahat / Sholat</option>
                        </select>
                    </div>
                    <div class="form-row span-2">
                        <label class="vx-label">Nama Sesi *</label>
                        <input type="text" class="vx-input form-input-clean" name="name" placeholder="Contoh: Jam Ke-1, Upacara, Istirahat 1" required>
                    </div>
                    <div class="form-row">
                        <label class="vx-label">Jam Ke- (Nomor Urut)</label>
                        <input type="number" class="vx-input form-input-clean" name="period_number" placeholder="1, 2, 3...">
                    </div>
                    <div class="form-row">
                        <label class="vx-label">Waktu Mulai *</label>
                        <input type="time" class="vx-input form-input-clean" name="start_time" required>
                    </div>
                    <div class="form-row span-2">
                        <label class="vx-label">Waktu Selesai *</label>
                        <input type="time" class="vx-input form-input-clean" name="end_time" required>
                    </div>
                </div>
            </div>
            <div class="vx-modal-footer modal-bottom">
                <button type="button" class="btn btn-secondary" onclick="closeModal('modalAddPeriod')">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Sesi Jam</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL: Edit Sesi Jam Pelajaran -->
<div class="vx-modal-backdrop modal-backdrop" id="modalEditPeriod">
    <div class="vx-modal modal-dialog">
        <div class="vx-modal-header modal-title-bar">
            <h3 class="vx-modal-title">
                <span class="modal-title-icon"><i class='bx bx-time-five'></i></span>
                Edit Sesi Jam Pelajaran
            </h3>
            <button class="vx-modal-close close-btn" onclick="closeModal('modalEditPeriod')">&times;</button>
        </div>
        <form id="formEditPeriod" onsubmit="submitEditPeriod(event)">
            <input type="hidden" id="editPeriodId" name="id">
            <div class="vx-modal-body modal-content">
                <div class="vx-form-grid form-grid-2">
                    <div class="form-row">
                        <label class="vx-label">Hari *</label>
                        <select class="vx-input form-input-clean" name="day_of_week" id="editPeriodDay" required>
                            <option value="1">Senin</option>
                            <option value="2">Selasa</option>
                            <option value="3">Rabu</option>
                            <option value="4">Kamis</option>
                            <option value="5">Jumat</option>
                            <option value="6">Sabtu</option>
                        </select>
                    </div>
                    <div class="form-row">
                        <label class="vx-label">Tipe Sesi *</label>
                        <select class="vx-input form-input-clean" name="is_break" id="editPeriodIsBreak">
                            <option value="0">KBM (Jam Pelajaran)</option>
                            <option value="1">Istirahat / Sholat</option>
                        </select>
                    </div>
                    <div class="form-row span-2">
                        <label class="vx-label">Nama Sesi *</label>
                        <input type="text" class="vx-input form-input-clean" name="name" id="editPeriodName" required>
                    </div>
                    <div class="form-row">
                        <label class="vx-label">Jam Ke- (Nomor Urut)</label>
                        <input type="number" class="vx-input form-input-clean" name="period_number" id="editPeriodNumber" placeholder="1, 2, 3...">
                    </div>
                    <div class="form-row">
                        <label class="vx-label">Waktu Mulai *</label>
                        <input type="time" class="vx-input form-input-clean" name="start_time" id="editPeriodStart" required>
                    </div>
                    <div class="form-row span-2">
                        <label class="vx-label">Waktu Selesai *</label>
                        <input type="time" class="vx-input form-input-clean" name="end_time" id="editPeriodEnd" required>
                    </div>
                    <div class="form-row span-2">
                        <label style="display: inline-flex; align-items: center; gap: 0.5rem; font-size: 0.82rem; cursor: pointer;">
                            <input type="checkbox" id="editPeriodApplyAllDays">
                            <span>Terapkan perubahan waktu jam ini untuk seluruh hari pada kategori ini</span>
                        </label>
                    </div>
                </div>
            </div>
            <div class="vx-modal-footer modal-bottom">
                <button type="button" class="btn btn-secondary" onclick="closeModal('modalEditPeriod')">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Perubahan Jam</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL: Tambah GTK -->
<div class="vx-modal-backdrop modal-backdrop" id="modalAddTeacher">
    <div class="vx-modal vx-modal-lg modal-dialog" style="max-width: 720px;">
        <div class="vx-modal-header modal-title-bar">
            <h3 class="vx-modal-title">
                <span class="modal-title-icon"><i class='bx bx-user-plus'></i></span>
                Tambah Guru / Tenaga Kependidikan
            </h3>
            <button class="vx-modal-close close-btn" onclick="closeModal('modalAddTeacher')">&times;</button>
        </div>
        <form id="formAddTeacher" onsubmit="submitAddTeacher(event)">
            <div class="vx-modal-body modal-content">
                <div class="vx-form-grid form-grid-2">
                    <div class="form-row">
                        <label class="vx-label">Gelar Depan</label>
                        <input type="text" class="vx-input form-input-clean" name="front_title" placeholder="Prof., Dr., Drs.">
                    </div>
                    <div class="form-row">
                        <label class="vx-label">Gelar Belakang</label>
                        <input type="text" class="vx-input form-input-clean" name="back_title" placeholder="S.Pd., M.M., Ph.D.">
                    </div>
                    <div class="form-row span-2">
                        <label class="vx-label">Nama Lengkap *</label>
                        <input type="text" class="vx-input form-input-clean" name="full_name" placeholder="Nama sesuai KTP/Dapodik" required>
                    </div>
                    <div class="form-row">
                        <label class="vx-label">NIP</label>
                        <input type="text" class="vx-input form-input-clean" name="nip" placeholder="18 digit (kosongkan jika GTT)">
                    </div>
                    <div class="form-row">
                        <label class="vx-label">NUPTK</label>
                        <input type="text" class="vx-input form-input-clean" name="nuptk" placeholder="16 digit nomor NUPTK">
                    </div>
                    <div class="form-row">
                        <label class="vx-label">Jenis Kelamin *</label>
                        <select class="vx-input form-input-clean" name="gender" required>
                            <option value="">Pilih Jenis Kelamin</option>
                            <option value="L">Laki-laki</option>
                            <option value="P">Perempuan</option>
                        </select>
                    </div>
                    <div class="form-row">
                        <label class="vx-label">Tanggal Lahir *</label>
                        <input type="date" class="vx-input form-input-clean" name="birth_date" required>
                    </div>
                    <div class="form-row">
                        <label class="vx-label">Jenis PTK *</label>
                        <select class="vx-input form-input-clean" name="ptk_type" required>
                            <option value="">Pilih Jenis PTK</option>
                            <option value="Kepala Sekolah">Kepala Sekolah</option>
                            <option value="Guru Mapel">Guru Mapel</option>
                            <option value="Guru BK">Guru BK / Konselor</option>
                            <option value="Guru Kelas">Guru Kelas</option>
                            <option value="Tenaga Administrasi">Tenaga Administrasi</option>
                            <option value="Tenaga Perpustakaan">Tenaga Perpustakaan</option>
                            <option value="Tenaga Laboratorium">Tenaga Laboratorium</option>
                        </select>
                    </div>
                    <div class="form-row">
                        <label class="vx-label">Status Kepegawaian *</label>
                        <select class="vx-input form-input-clean" name="employment_status" required>
                            <option value="">Pilih Status</option>
                            <option value="PNS">PNS</option>
                            <option value="PPPK">PPPK / P3K</option>
                            <option value="GTT">GTT / Honorer</option>
                            <option value="Yayasan">Guru Tetap Yayasan</option>
                        </select>
                    </div>
                    <div class="form-row">
                        <label class="vx-label">Pendidikan Terakhir *</label>
                        <select class="vx-input form-input-clean" name="highest_education" required>
                            <option value="S1">S1</option>
                            <option value="S2">S2</option>
                            <option value="S3">S3</option>
                            <option value="D4">D4</option>
                            <option value="D3">D3</option>
                            <option value="SMA/Sederajat">SMA/Sederajat</option>
                        </select>
                    </div>
                    <div class="form-row">
                        <label class="vx-label">No. HP / WhatsApp</label>
                        <input type="text" class="vx-input form-input-clean" name="phone_number" placeholder="08xxxx">
                    </div>
                </div>
            </div>
            <div class="vx-modal-footer modal-bottom">
                <button type="button" class="btn btn-secondary" onclick="closeModal('modalAddTeacher')">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Data GTK</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL: Tambah Siswa -->
<div class="vx-modal-backdrop modal-backdrop" id="modalAddStudent">
    <div class="vx-modal vx-modal-lg modal-dialog" style="max-width: 720px;">
        <div class="vx-modal-header modal-title-bar">
            <h3 class="vx-modal-title">
                <span class="modal-title-icon"><i class='bx bx-user-plus'></i></span>
                Tambah Peserta Didik
            </h3>
            <button class="vx-modal-close close-btn" onclick="closeModal('modalAddStudent')">&times;</button>
        </div>
        <form id="formAddStudent" onsubmit="submitAddStudent(event)">
            <div class="vx-modal-body modal-content">
                <div class="vx-form-grid form-grid-2">
                    <div class="form-row span-2">
                        <label class="vx-label">Nama Lengkap *</label>
                        <input type="text" class="vx-input form-input-clean" name="full_name" placeholder="Nama sesuai Akte Lahir" required>
                    </div>
                    <div class="form-row">
                        <label class="vx-label">NISN</label>
                        <input type="text" class="vx-input form-input-clean" name="nisn" placeholder="10 digit NISN">
                    </div>
                    <div class="form-row">
                        <label class="vx-label">NIS *</label>
                        <input type="text" class="vx-input form-input-clean" name="nis" placeholder="Nomor Induk Sekolah" required>
                    </div>
                    <div class="form-row">
                        <label class="vx-label">NIK</label>
                        <input type="text" class="vx-input form-input-clean" name="nik" placeholder="16 digit NIK">
                    </div>
                    <div class="form-row">
                        <label class="vx-label">No. KK</label>
                        <input type="text" class="vx-input form-input-clean" name="no_kk" placeholder="16 digit No. KK">
                    </div>
                    <div class="form-row">
                        <label class="vx-label">Jenis Kelamin *</label>
                        <select class="vx-input form-input-clean" name="gender" required>
                            <option value="">Pilih</option>
                            <option value="L">Laki-laki</option>
                            <option value="P">Perempuan</option>
                        </select>
                    </div>
                    <div class="form-row">
                        <label class="vx-label">Agama *</label>
                        <select class="vx-input form-input-clean" name="religion" required>
                            <option value="">Pilih Agama</option>
                            <option value="Islam">Islam</option>
                            <option value="Kristen">Kristen</option>
                            <option value="Katolik">Katolik</option>
                            <option value="Hindu">Hindu</option>
                            <option value="Buddha">Buddha</option>
                            <option value="Konghucu">Konghucu</option>
                        </select>
                    </div>
                    <div class="form-row">
                        <label class="vx-label">Tempat Lahir</label>
                        <input type="text" class="vx-input form-input-clean" name="birth_place" placeholder="Jakarta">
                    </div>
                    <div class="form-row">
                        <label class="vx-label">Tanggal Lahir *</label>
                        <input type="date" class="vx-input form-input-clean" name="birth_date" required>
                    </div>
                    <div class="form-row">
                        <label class="vx-label">Angkatan (Tahun Masuk) *</label>
                        <select class="vx-input form-input-clean" name="entry_academic_year_id" required>
                            @foreach($academicYears as $ay)
                                <option value="{{ $ay->id }}" {{ $ay->is_active ? 'selected' : '' }}>{{ $ay->name }} {{ $ay->is_active ? '(Aktif)' : '' }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-row">
                        <label class="vx-label">Jenis Pendaftaran *</label>
                        <select class="vx-input form-input-clean" name="registration_type" id="addStudentRegType" onchange="togglePreviousSchoolField(this.value)" required>
                            <option value="baru" selected>Siswa Baru</option>
                            <option value="pindahan">Siswa Pindahan</option>
                            <option value="kembali_bersekolah">Kembali Bersekolah</option>
                        </select>
                    </div>
                    <div class="form-row span-2" id="rowPreviousSchool" style="display: none;">
                        <label class="vx-label">Sekolah Asal (Khusus Pindahan) *</label>
                        <input type="text" class="vx-input form-input-clean" name="previous_school" id="inputPreviousSchool" placeholder="Nama sekolah asal sebelumnya">
                    </div>
                    <div class="form-row">
                        <label class="vx-label">Tanggal Masuk</label>
                        <input type="date" class="vx-input form-input-clean" name="enrollment_date" value="{{ date('Y-m-d') }}">
                    </div>
                    <div class="form-row">
                        <label class="vx-label">Rombongan Belajar</label>
                        <select class="vx-input form-input-clean" name="school_class_id">
                            <option value="">— Belum Ditentukan —</option>
                            @foreach($classes as $c)
                                <option value="{{ $c->id }}">{{ $c->name }} (Tingkat {{ $c->grade_level }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="vx-modal-footer modal-bottom">
                <button type="button" class="btn btn-secondary" onclick="closeModal('modalAddStudent')">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Data Siswa</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL: Riwayat Rombel Peserta Didik -->
<div class="vx-modal-backdrop modal-backdrop" id="modalStudentHistory">
    <div class="vx-modal vx-modal-lg modal-dialog" style="max-width: 680px;">
        <div class="vx-modal-header modal-title-bar">
            <h3 class="vx-modal-title">
                <span class="modal-title-icon"><i class='bx bx-history'></i></span>
                <span id="historyModalStudentName">Riwayat Rombel Peserta Didik</span>
            </h3>
            <button class="vx-modal-close close-btn" onclick="closeModal('modalStudentHistory')">&times;</button>
        </div>
        <div class="vx-modal-body modal-content">
            <div id="historyStudentMeta" style="background: var(--vx-bg-subtle); padding: 0.85rem 1rem; border-radius: var(--vx-radius-md); margin-bottom: 1.25rem; font-size: 0.85rem; line-height: 1.6;">
                <!-- dynamic metadata -->
            </div>
            <div class="table-wrap">
                <table class="clean-table" id="studentHistoryTable">
                    <thead>
                        <tr>
                            <th>Tahun Ajaran</th>
                            <th>Tingkat</th>
                            <th>Rombel / Kelas</th>
                            <th>Status Kenaikan</th>
                            <th>Catatan</th>
                        </tr>
                    </thead>
                    <tbody id="studentHistoryTableBody">
                        <tr><td colspan="5" style="text-align: center; padding: 2rem; color: var(--vx-text-muted);">Memuat riwayat...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="vx-modal-footer modal-bottom">
            <button type="button" class="btn btn-secondary" onclick="closeModal('modalStudentHistory')">Tutup</button>
        </div>
    </div>
</div>

<!-- MODAL: Kenaikan Kelas Massal -->
<div class="vx-modal-backdrop modal-backdrop" id="modalPromotion">
    <div class="vx-modal vx-modal-lg modal-dialog" style="max-width: 820px;">
        <div class="vx-modal-header modal-title-bar">
            <h3 class="vx-modal-title">
                <span class="modal-title-icon"><i class='bx bx-upvote'></i></span>
                Proses Kenaikan Kelas (Massal)
            </h3>
            <button class="vx-modal-close close-btn" onclick="closeModal('modalPromotion')">&times;</button>
        </div>
        <form id="formPromotion" onsubmit="submitPromotion(event)">
            <div class="vx-modal-body modal-content">
                <div style="background: var(--vx-info-light); color: var(--vx-info); padding: 0.75rem 1rem; border-radius: var(--vx-radius-md); margin-bottom: 1rem; font-size: 0.83rem;">
                    <i class='bx bx-info-circle'></i> Pilih rombel asal dan tahun ajaran tujuan. Sistem akan memuat seluruh siswa aktif di rombel tersebut dan memperbarui riwayat rombelnya secara otomatis.
                </div>
                <div class="vx-form-grid form-grid-2" style="margin-bottom: 1.25rem;">
                    <div class="form-row">
                        <label class="vx-label">Rombongan Belajar Asal *</label>
                        <select class="vx-input form-input-clean" id="promoSourceClass" onchange="loadPromotionCandidates()" required>
                            <option value="">— Pilih Rombel Asal —</option>
                            @foreach($classes as $c)
                                <option value="{{ $c->id }}" data-grade="{{ $c->grade_level }}" data-year="{{ $c->academic_year_id }}">
                                    {{ $c->name }} (Tingkat {{ $c->grade_level }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-row">
                        <label class="vx-label">Tahun Ajaran Tujuan *</label>
                        <select class="vx-input form-input-clean" id="promoTargetYear" required>
                            @foreach($academicYears as $ay)
                                <option value="{{ $ay->id }}" {{ $ay->is_active ? 'selected' : '' }}>
                                    {{ $ay->name }} {{ $ay->is_active ? '(Tahun Aktif)' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="table-wrap" style="max-height: 320px; overflow-y: auto;">
                    <table class="clean-table" id="promoCandidatesTable">
                        <thead>
                            <tr>
                                <th>Nama Siswa</th>
                                <th>NIS</th>
                                <th style="width: 140px;">Aksi</th>
                                <th>Rombel Tujuan</th>
                            </tr>
                        </thead>
                        <tbody id="promoCandidatesBody">
                            <tr>
                                <td colspan="4" style="text-align: center; padding: 2rem; color: var(--vx-text-muted);">
                                    Pilih Rombongan Belajar Asal terlebih dahulu.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="vx-modal-footer modal-bottom">
                <button type="button" class="btn btn-secondary" onclick="closeModal('modalPromotion')">Batal</button>
                <button type="submit" class="btn btn-primary" id="btnSubmitPromo" disabled>
                    <i class='bx bx-check-double'></i> Terapkan Kenaikan Kelas
                </button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL: Tambah Kurikulum -->
<div class="vx-modal-backdrop modal-backdrop" id="modalAddCurriculum">
    <div class="vx-modal modal-dialog">
        <div class="vx-modal-header modal-title-bar">
            <h3 class="vx-modal-title">
                <span class="modal-title-icon"><i class='bx bx-book-bookmark'></i></span>
                Tambah Kurikulum Baru
            </h3>
            <button class="vx-modal-close close-btn" onclick="closeModal('modalAddCurriculum')">&times;</button>
        </div>
        <form id="formAddCurriculum" onsubmit="submitAddCurriculum(event)">
            <div class="vx-modal-body modal-content">
                <div class="vx-form-grid form-grid-2">
                    <div class="form-row span-2">
                        <label class="vx-label">Nama Kurikulum *</label>
                        <input type="text" class="vx-input form-input-clean" name="name" placeholder="Kurikulum Merdeka Mandiri Berbagi" required>
                    </div>
                    <div class="form-row">
                        <label class="vx-label">Kode Singkat *</label>
                        <input type="text" class="vx-input form-input-clean" name="code" placeholder="KURMER-2026" required>
                    </div>
                    <div class="form-row">
                        <label class="vx-label">Target Jenjang Pendidikan *</label>
                        <select class="vx-input form-input-clean" name="target_level" required>
                            <option value="SMA">SMA (Sekolah Menengah Atas)</option>
                            <option value="SMK">SMK (Sekolah Menengah Kejuruan)</option>
                            <option value="MA">MA (Madrasah Aliyah)</option>
                            <option value="SMP">SMP / MTs</option>
                            <option value="SD">SD / MI</option>
                            <option value="UMUM">Umum / Semua Jenjang</option>
                        </select>
                    </div>
                    <div class="form-row span-2">
                        <label class="vx-label">Deskripsi / Karakteristik Kurikulum</label>
                        <textarea class="vx-input form-input-clean" name="description" rows="3" placeholder="Fokus kompetensi holistik, pembelajaran berdiferensiasi, kokurikuler projek P5, dll."></textarea>
                    </div>
                </div>
            </div>
            <div class="vx-modal-footer modal-bottom">
                <button type="button" class="btn btn-secondary" onclick="closeModal('modalAddCurriculum')">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Kurikulum</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL: Petakan Kurikulum ke Jenjang / Tingkat Kelas -->
<div class="vx-modal-backdrop modal-backdrop" id="modalAssignGradeCurriculum">
    <div class="vx-modal modal-dialog" style="max-width: 580px;">
        <div class="vx-modal-header modal-title-bar">
            <h3 class="vx-modal-title">
                <span class="modal-title-icon"><i class='bx bx-git-branch'></i></span>
                Petakan Kurikulum per Jenjang
            </h3>
            <button class="vx-modal-close close-btn" onclick="closeModal('modalAssignGradeCurriculum')">&times;</button>
        </div>
        <form id="formAssignGradeCurriculum" onsubmit="submitAssignGrade(event)">
            <div class="vx-modal-body modal-content">
                <div class="vx-form-grid form-grid-2">
                    <div class="form-row">
                        <label class="vx-label">Tingkat / Jenjang Kelas *</label>
                        <select class="vx-input form-input-clean" id="assignGradeLevelSelect" name="grade_level" required>
                            <optgroup label="Tingkat Menengah Atas / Kejuruan (SMA / SMK)">
                                <option value="10">Tingkat 10 (Kelas X)</option>
                                <option value="11">Tingkat 11 (Kelas XI)</option>
                                <option value="12">Tingkat 12 (Kelas XII)</option>
                                <option value="13">Tingkat 13 (SMK 4 Tahun)</option>
                            </optgroup>
                            <optgroup label="Tingkat Menengah Pertama (SMP / MTs)">
                                <option value="7">Tingkat 7 (Kelas VII)</option>
                                <option value="8">Tingkat 8 (Kelas VIII)</option>
                                <option value="9">Tingkat 9 (Kelas IX)</option>
                            </optgroup>
                            <optgroup label="Tingkat Dasar (SD / MI)">
                                <option value="1">Tingkat 1 (Kelas I)</option>
                                <option value="2">Tingkat 2 (Kelas II)</option>
                                <option value="3">Tingkat 3 (Kelas III)</option>
                                <option value="4">Tingkat 4 (Kelas IV)</option>
                                <option value="5">Tingkat 5 (Kelas V)</option>
                                <option value="6">Tingkat 6 (Kelas VI)</option>
                            </optgroup>
                        </select>
                    </div>
                    <div class="form-row">
                        <label class="vx-label">Fase Belajar (Opsional)</label>
                        <input type="text" class="vx-input form-input-clean" id="assignGradePhaseInput" name="phase" placeholder="Contoh: Fase E / Fase F">
                        <div style="display: flex; gap: 0.3rem; margin-top: 0.35rem; flex-wrap: wrap;">
                            <button type="button" class="btn btn-secondary btn-sm" style="font-size: 0.68rem; padding: 0.15rem 0.45rem; border-radius: 4px;" onclick="document.getElementById('assignGradePhaseInput').value='Fase E'">+ Fase E</button>
                            <button type="button" class="btn btn-secondary btn-sm" style="font-size: 0.68rem; padding: 0.15rem 0.45rem; border-radius: 4px;" onclick="document.getElementById('assignGradePhaseInput').value='Fase F'">+ Fase F</button>
                            <button type="button" class="btn btn-secondary btn-sm" style="font-size: 0.68rem; padding: 0.15rem 0.45rem; border-radius: 4px;" onclick="document.getElementById('assignGradePhaseInput').value='Fase D'">+ Fase D</button>
                            <button type="button" class="btn btn-secondary btn-sm" style="font-size: 0.68rem; padding: 0.15rem 0.45rem; border-radius: 4px;" onclick="document.getElementById('assignGradePhaseInput').value='Tingkat Akhir'">+ Akhir</button>
                        </div>
                    </div>
                    <div class="form-row span-2">
                        <label class="vx-label">Pilih Kurikulum yang Diterapkan *</label>
                        <select class="vx-input form-input-clean" id="assignGradeCurriculumSelect" name="curriculum_id" required>
                            @foreach($curriculums as $cOption)
                                <option value="{{ $cOption->id }}">{{ $cOption->name }} ({{ $cOption->code }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-row span-2">
                        <div style="background: rgba(115, 103, 240, 0.08); border-radius: var(--vx-radius-md); padding: 0.85rem 1rem; font-size: 0.8rem; color: var(--vx-primary); line-height: 1.45;">
                            <i class='bx bx-info-circle' style="font-size: 1rem; vertical-align: middle;"></i> <strong>Otomatis Sinkron:</strong> Seluruh rombongan belajar (kelas) yang berada di jenjang yang dipilih akan otomatis diperbarui kurikulum operasionalnya dan mewarisi struktur mata pelajaran serta beban jam pelajaran (JP).
                        </div>
                    </div>
                </div>
            </div>
            <div class="vx-modal-footer modal-bottom">
                <button type="button" class="btn btn-secondary" onclick="closeModal('modalAssignGradeCurriculum')">Batal</button>
                <button type="submit" class="btn btn-primary">
                    <i class='bx bx-check'></i> Simpan Pemetaan Kurikulum
                </button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL: Rincian Struktur Mata Pelajaran Kurikulum -->
<div class="vx-modal-backdrop modal-backdrop" id="modalCurriculumStructureDetail">
    <div class="vx-modal modal-dialog" style="max-width: 920px; width: 95%;">
        <div class="vx-modal-header modal-title-bar">
            <div>
                <h3 class="vx-modal-title" id="modalCurStructureTitle">
                    <span class="modal-title-icon"><i class='bx bx-book-content'></i></span>
                    Struktur Mata Pelajaran Kurikulum
                </h3>
                <p class="cell-sub" id="modalCurStructureSub" style="margin-top: 3px; font-size: 0.82rem; color: var(--vx-text-muted);">
                    Rincian alokasi Jam Pelajaran (JP/Minggu), kelompok mapel, dan standar KKTP / KKM.
                </p>
            </div>
            <button class="vx-modal-close close-btn" onclick="closeModal('modalCurriculumStructureDetail')">&times;</button>
        </div>
        <div class="vx-modal-body modal-content" style="padding: 1.25rem 1.5rem; max-height: 72vh; overflow-y: auto;">
            <!-- Tab Navigasi Cepat Antar Kurikulum di dalam Modal -->
            <div style="display: flex; gap: 0.4rem; overflow-x: auto; padding-bottom: 0.75rem; margin-bottom: 1.25rem; border-bottom: 1px solid var(--vx-border-light);">
                @foreach($curriculums as $cModalTab)
                    <button type="button" class="btn btn-xs modal-cur-tab-btn {{ $loop->first ? 'btn-primary' : 'btn-secondary' }}" 
                        id="modalCurTabBtn-{{ $cModalTab->id }}" 
                        onclick="switchModalCurriculumPane({{ $cModalTab->id }}, '{{ addslashes($cModalTab->name) }}', '{{ $cModalTab->code }}')"
                        style="white-space: nowrap; border-radius: var(--radius-pill); font-weight: 600; padding: 0.35rem 0.85rem;">
                        <i class='bx bx-book-open'></i> {{ $cModalTab->name }}
                        <span class="vx-badge badge-dark" style="margin-left: 0.35rem; font-size: 0.68rem;">{{ $cModalTab->curriculumSubjects->count() }} Mapel</span>
                    </button>
                @endforeach
            </div>

            @foreach($curriculums as $idxModal => $cModal)
                <div class="modal-cur-pane" id="modalCurPane-{{ $cModal->id }}" style="display: {{ $idxModal === 0 ? 'block' : 'none' }};">
                    <!-- Stat & Info Card -->
                    <div style="background: var(--vx-body-bg); border: 1px solid var(--vx-border-light); border-radius: var(--radius-md); padding: 1rem 1.25rem; margin-bottom: 1rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                        <div style="display: flex; align-items: center; gap: 0.85rem; flex-wrap: wrap;">
                            <div>
                                <span class="vx-badge badge-primary cell-mono" style="font-size: 0.8rem;">{{ $cModal->code }}</span>
                                <span class="vx-badge badge-info" style="font-size: 0.8rem;">{{ $cModal->target_level ?? 'SMA/SMK' }}</span>
                            </div>
                            <div style="font-size: 0.85rem; color: var(--vx-text-heading); font-weight: 600;">
                                Total Beban Belajar: <span style="color: var(--bento-primary); font-size: 1rem;">{{ $cModal->curriculumSubjects->sum('hours_per_week') }} JP</span> / Minggu
                            </div>
                            <div style="font-size: 0.82rem; color: var(--vx-text-muted);">
                                Dipetakan: <strong>{{ $cModal->curriculumSubjects->count() }}</strong> Mata Pelajaran
                            </div>
                        </div>
                        <div style="display: flex; gap: 0.5rem; align-items: center;">
                            <button type="button" class="btn btn-secondary btn-sm" onclick="openManageSubjectGroupsModal({{ $cModal->id }}, '{{ addslashes($cModal->name) }}', '{{ $cModal->code }}')" style="display: inline-flex; align-items: center; gap: 0.35rem; font-weight: 600;">
                                <i class='bx bx-category'></i> Kelola Kelompok Mapel
                            </button>
                            <button type="button" class="btn btn-primary btn-sm" onclick="openAddCurriculumSubjectModal({{ $cModal->id }})" style="display: inline-flex; align-items: center; gap: 0.35rem; font-weight: 600;">
                                <i class='bx bx-plus-circle'></i> + Petakan Mapel Baru
                            </button>
                        </div>
                    </div>

                    <!-- Jenjang Penerapan Info -->
                    <div style="margin-bottom: 0.85rem; font-size: 0.82rem; color: var(--vx-text-muted); display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
                        <span style="font-weight: 600; color: var(--vx-text-heading);"><i class='bx bx-buildings text-primary'></i> Diterapkan di Jenjang:</span>
                        @if($cModal->gradeLevels->count() > 0)
                            @foreach($cModal->gradeLevels as $gl)
                                <span class="vx-badge badge-primary" style="font-size: 0.72rem;">
                                    Tingkat {{ $gl->grade_level }} {{ $gl->phase ? '('.$gl->phase.')' : '' }}
                                </span>
                            @endforeach
                        @else
                            <span class="vx-badge badge-muted" style="font-size: 0.72rem;">Belum dipetakan ke tingkat kelas manapun</span>
                        @endif
                    </div>

                    <!-- Tabel Daftar Mata Pelajaran -->
                    <div class="table-wrap" style="border: 1px solid var(--vx-border-light); border-radius: var(--radius-md); overflow: hidden;">
                        <table class="clean-table" style="margin-bottom: 0;">
                            <thead>
                                <tr>
                                    <th style="width: 40px; text-align: center;">No</th>
                                    <th>Mata Pelajaran</th>
                                    <th>Kelompok Mapel</th>
                                    <th style="text-align: center; width: 115px;">Beban Belajar</th>
                                    <th style="text-align: center; width: 110px;">KKTP / KKM</th>
                                    <th style="text-align: right; width: 110px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($cModal->curriculumSubjects as $csIdx => $cs)
                                    <tr>
                                        <td style="text-align: center; color: var(--vx-text-muted); font-size: 0.78rem;" class="cell-mono">
                                            {{ $csIdx + 1 }}
                                        </td>
                                        <td>
                                            <div class="cell-title" style="font-size: 0.86rem;">{{ $cs->subject?->name ?? '—' }}</div>
                                            <div class="cell-sub cell-mono" style="font-size: 0.74rem;">{{ $cs->subject?->code ?? '—' }} &bull; {{ $cs->subject?->category ?? '' }}</div>
                                        </td>
                                        <td>
                                            <span class="vx-badge {{ $cs->resolved_group_badge }}" style="font-size: 0.74rem;">
                                                {{ $cs->resolved_group_name }}
                                            </span>
                                        </td>
                                        <td style="text-align: center;">
                                            <span class="cell-mono font-bold" style="font-size: 1rem; color: var(--bento-primary);">
                                                {{ $cs->hours_per_week }}
                                            </span>
                                            <span style="font-size: 0.75rem; color: var(--vx-text-muted);"> JP / mg</span>
                                        </td>
                                        <td style="text-align: center;">
                                            <span class="vx-badge badge-dark cell-mono" style="font-size: 0.85rem; font-weight: 700;">
                                                {{ number_format($cs->passing_grade, 0) }}
                                            </span>
                                        </td>
                                        <td style="text-align: right;">
                                            <div class="d-flex gap-1 justify-end">
                                                <button type="button" class="btn btn-secondary btn-xs btn-icon-sm" 
                                                    onclick="closeModal('modalCurriculumStructureDetail'); openEditCurriculumSubjectModal({{ $cModal->id }}, {{ $cs->subject_id }}, {{ $cs->curriculum_subject_group_id ?? 'null' }}, '{{ $cs->subject_group }}', {{ $cs->hours_per_week }}, {{ $cs->passing_grade }})" 
                                                    title="Ubah Beban JP / KKTP / Kelompok">
                                                    <i class='bx bx-edit'></i>
                                                </button>
                                                <button type="button" class="btn btn-danger-light btn-xs btn-icon-sm" 
                                                    onclick="removeSubjectFromCurriculum({{ $cModal->id }}, {{ $cs->subject_id }}, '{{ addslashes($cs->subject?->name ?? 'Mapel') }}')" 
                                                    title="Hapus dari Kurikulum">
                                                    <i class='bx bx-trash'></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" style="text-align: center; padding: 2.5rem 1.5rem; color: var(--vx-text-muted);">
                                            <i class='bx bx-book-open' style="font-size: 2.2rem; display: block; margin-bottom: 0.5rem; opacity: 0.5;"></i>
                                            <div style="font-weight: 600; color: var(--vx-text-heading); margin-bottom: 0.25rem;">Belum ada mata pelajaran yang dipetakan</div>
                                            <p style="font-size: 0.82rem; margin-bottom: 0.75rem;">Tambahkan alokasi mata pelajaran untuk kurikulum {{ $cModal->name }} ini.</p>
                                            <button type="button" class="btn btn-primary btn-xs" onclick="openAddCurriculumSubjectModal({{ $cModal->id }})">
                                                <i class='bx bx-plus-circle'></i> Petakan Mata Pelajaran Sekarang
                                            </button>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="vx-modal-footer modal-bottom" style="display: flex; justify-content: space-between; align-items: center;">
            <button type="button" class="btn btn-outline-primary btn-sm" onclick="viewCurriculumInPanelFromModal()">
                <i class='bx bx-fullscreen'></i> Lihat di Panel Halaman Penuh
            </button>
            <button type="button" class="btn btn-secondary btn-sm" onclick="closeModal('modalCurriculumStructureDetail')">
                Tutup
            </button>
        </div>
    </div>
</div>

<!-- MODAL: Petakan / Ubah Beban Mapel dalam Kurikulum -->
<div class="vx-modal-backdrop modal-backdrop" id="modalAddCurriculumSubject">
    <div class="vx-modal modal-dialog">
        <div class="vx-modal-header modal-title-bar">
            <h3 class="vx-modal-title">
                <span class="modal-title-icon"><i class='bx bx-book-plus'></i></span>
                Petakan Mata Pelajaran ke Kurikulum
            </h3>
            <button class="vx-modal-close close-btn" onclick="closeModal('modalAddCurriculumSubject')">&times;</button>
        </div>
        <form id="formAddCurriculumSubject" onsubmit="submitSaveCurriculumSubject(event)">
            <div class="vx-modal-body modal-content">
                <div class="vx-form-grid form-grid-2">
                    <div class="form-row span-2">
                        <label class="vx-label">Pilih Kurikulum yang Diterapkan *</label>
                        <select class="vx-input form-input-clean" id="curSubTargetCurriculumId" name="curriculum_id" onchange="onCurriculumSelectChange(this.value)" required>
                            @foreach($curriculums as $cOpt)
                                <option value="{{ $cOpt->id }}">{{ $cOpt->name }} ({{ $cOpt->code }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-row span-2">
                        <label class="vx-label">Pilih Mata Pelajaran *</label>
                        <select class="vx-input form-input-clean" id="curSubSubjectSelect" name="subject_id" required>
                            @foreach($subjects as $sub)
                                <option value="{{ $sub->id }}">{{ $sub->code }} — {{ $sub->name }} ({{ $sub->category }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-row">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.35rem;">
                            <label class="vx-label" style="margin-bottom: 0;">Kelompok Mata Pelajaran *</label>
                            <a href="javascript:void(0)" onclick="openManageSubjectGroupsModalFromAddModal()" style="font-size: 0.72rem; color: var(--bento-primary); text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 0.2rem;">
                                <i class='bx bx-cog'></i> Atur Kelompok
                            </a>
                        </div>
                        <select class="vx-input form-input-clean" id="curSubGroupSelect" name="curriculum_subject_group_id" required>
                            <!-- Dinamis sesuai kurikulum terpilih -->
                        </select>
                        <input type="hidden" id="curSubLegacyGroupInput" name="subject_group" value="">
                    </div>
                    <div class="form-row">
                        <label class="vx-label">Beban Jam Pelajaran (JP / Minggu) *</label>
                        <input type="number" class="vx-input form-input-clean" id="curSubJpInput" name="hours_per_week" min="1" max="15" value="4" required>
                    </div>
                    <div class="form-row span-2">
                        <label class="vx-label">Standar Kelulusan / KKTP / KKM *</label>
                        <input type="number" step="0.1" class="vx-input form-input-clean" id="curSubKkmInput" name="passing_grade" min="0" max="100" value="75.0" required>
                    </div>
                </div>
            </div>
            <div class="vx-modal-footer modal-bottom">
                <button type="button" class="btn btn-secondary" onclick="closeModal('modalAddCurriculumSubject')">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Alokasi Mapel</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL: Kelola Kelompok Mata Pelajaran Kurikulum -->
<div class="vx-modal-backdrop modal-backdrop" id="modalManageSubjectGroups">
    <div class="vx-modal modal-dialog" style="max-width: 860px; width: 95%;">
        <div class="vx-modal-header modal-title-bar">
            <div>
                <h3 class="vx-modal-title">
                    <span class="modal-title-icon"><i class='bx bx-category'></i></span>
                    Kelola Kelompok Mata Pelajaran Kurikulum
                </h3>
                <p class="cell-sub" id="manageSubjectGroupsCurTitle" style="margin-top: 3px; font-size: 0.82rem; color: var(--vx-text-muted);">
                    Kurikulum Terpilih &bull; Tambah, ubah urutan, atau sesuaikan kelompok mapel kurikulum operasional sekolah.
                </p>
            </div>
            <button class="vx-modal-close close-btn" onclick="closeModal('modalManageSubjectGroups')">&times;</button>
        </div>
        <div class="vx-modal-body modal-content" style="padding: 1.25rem 1.5rem; max-height: 75vh; overflow-y: auto;">
            
            <!-- Toolbar: Tambah Kelompok & Muat Template Standar -->
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.75rem; margin-bottom: 1.25rem; background: var(--vx-body-bg); padding: 0.85rem 1.15rem; border-radius: var(--radius-md); border: 1px solid var(--vx-border-light);">
                <div>
                    <button type="button" class="btn btn-primary btn-sm" onclick="toggleSubjectGroupForm(true)" style="display: inline-flex; align-items: center; gap: 0.35rem; font-weight: 600;">
                        <i class='bx bx-plus-circle'></i> + Tambah Kelompok Baru
                    </button>
                </div>
                <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
                    <span style="font-size: 0.78rem; font-weight: 600; color: var(--vx-text-muted);"><i class='bx bx-copy'></i> Muat Template:</span>
                    <button type="button" class="btn btn-secondary btn-xs" onclick="applySubjectGroupTemplate('merdeka')" title="Terapkan kelompok standar Kurikulum Merdeka">
                        Kur. Merdeka
                    </button>
                    <button type="button" class="btn btn-secondary btn-xs" onclick="applySubjectGroupTemplate('k13')" title="Terapkan kelompok standar Kurikulum 2013">
                        Kur. 2013
                    </button>
                    <button type="button" class="btn btn-secondary btn-xs" onclick="applySubjectGroupTemplate('kemenag')" title="Terapkan kelompok standar Madrasah / Kemenag">
                        Kemenag / Madrasah
                    </button>
                    <button type="button" class="btn btn-secondary btn-xs" onclick="applySubjectGroupTemplate('pesantren')" title="Terapkan kelompok standar Pesantren / Diniyah">
                        Pesantren
                    </button>
                </div>
            </div>

            <!-- Form Collapse: Tambah / Edit Kelompok -->
            <div id="collapseSubjectGroupForm" style="display: none; background: rgba(70, 128, 255, 0.04); border: 1px solid rgba(70, 128, 255, 0.25); border-radius: var(--radius-md); padding: 1.25rem; margin-bottom: 1.25rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; border-bottom: 1px solid var(--vx-border-light); padding-bottom: 0.5rem;">
                    <strong id="subjectGroupFormHeading" style="font-size: 0.9rem; color: var(--bento-primary); display: flex; align-items: center; gap: 0.35rem;">
                        <i class='bx bx-edit-alt'></i> Tambah Kelompok Mapel Baru
                    </strong>
                    <button type="button" class="btn btn-secondary btn-xs" onclick="toggleSubjectGroupForm(false)">&times; Batal</button>
                </div>
                <form id="formSubjectGroup" onsubmit="submitSaveSubjectGroup(event)">
                    <input type="hidden" id="sgCurriculumId" name="curriculum_id" value="">
                    <input type="hidden" id="sgGroupId" name="group_id" value="">
                    <div class="vx-form-grid form-grid-2">
                        <div class="form-row">
                            <label class="vx-label">Kode Kelompok *</label>
                            <input type="text" class="vx-input form-input-clean" id="sgCodeInput" name="code" placeholder="Misal: UMUM, KEL_A, P5, MULOK" style="text-transform: uppercase;" required>
                            <span class="cell-sub" style="font-size: 0.72rem;">Singkatan unik pengenal kelompok</span>
                        </div>
                        <div class="form-row">
                            <label class="vx-label">Urutan Tampil (No) *</label>
                            <input type="number" class="vx-input form-input-clean" id="sgOrderInput" name="order_no" min="1" max="99" value="1" required>
                            <span class="cell-sub" style="font-size: 0.72rem;">Urutan susunan dalam rapor & kurikulum</span>
                        </div>
                        <div class="form-row span-2">
                            <label class="vx-label">Nama Lengkap Kelompok Mapel *</label>
                            <input type="text" class="vx-input form-input-clean" id="sgNameInput" name="name" placeholder="Misal: Kelompok Mata Pelajaran Umum (Nasional)" required>
                        </div>
                        <div class="form-row">
                            <label class="vx-label">Warna Badge Visual *</label>
                            <select class="vx-input form-input-clean" id="sgColorSelect" name="color" required>
                                <option value="badge-primary">Biru (Utama / Wajib)</option>
                                <option value="badge-info">Cyan (Pilihan / Kejuruan)</option>
                                <option value="badge-success">Hijau (Keagamaan / PAI)</option>
                                <option value="badge-warning">Kuning / Amber (Projek P5)</option>
                                <option value="badge-secondary">Abu-abu (Muatan Lokal)</option>
                                <option value="badge-danger">Merah (Khusus / Matrikulasi)</option>
                                <option value="badge-dark">Dark / Hitam</option>
                            </select>
                        </div>
                        <div class="form-row">
                            <label class="vx-label">Keterangan / Deskripsi (Opsional)</label>
                            <input type="text" class="vx-input form-input-clean" id="sgDescInput" name="description" placeholder="Penjelasan karakteristik kelompok">
                        </div>
                    </div>
                    <div style="display: flex; justify-content: flex-end; gap: 0.5rem; margin-top: 1rem;">
                        <button type="button" class="btn btn-secondary btn-sm" onclick="toggleSubjectGroupForm(false)">Batal</button>
                        <button type="submit" class="btn btn-primary btn-sm" id="btnSaveSubjectGroup">
                            <i class='bx bx-check'></i> Simpan Kelompok
                        </button>
                    </div>
                </form>
            </div>

            <!-- Tabel Daftar Kelompok Mapel Kurikulum -->
            <div class="table-wrap" style="border: 1px solid var(--vx-border-light); border-radius: var(--radius-md); overflow: hidden;">
                <table class="clean-table" style="margin-bottom: 0;">
                    <thead>
                        <tr>
                            <th style="width: 50px; text-align: center;">Urutan</th>
                            <th style="width: 100px;">Kode</th>
                            <th>Nama Kelompok Mata Pelajaran</th>
                            <th style="width: 130px; text-align: center;">Tampilan Badge</th>
                            <th style="width: 110px; text-align: center;">Mapel Terdaftar</th>
                            <th style="width: 100px; text-align: right;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="subjectGroupsTableBody">
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 2rem; color: var(--vx-text-muted);">
                                Memuat data kelompok mata pelajaran...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <div style="margin-top: 0.85rem; font-size: 0.78rem; color: var(--vx-text-muted); display: flex; align-items: center; gap: 0.35rem;">
                <i class='bx bx-info-circle text-primary'></i>
                <span>Kelompok mata pelajaran ini menentukan pengelompokan resmi alokasi jam pelajaran dan pembagian pada lembar rapor nilai siswa.</span>
            </div>
        </div>
        <div class="vx-modal-footer modal-bottom" style="display: flex; justify-content: space-between; align-items: center;">
            <span class="cell-sub" style="font-size: 0.8rem;">Perubahan tersimpan otomatis ke kurikulum ini.</span>
            <button type="button" class="btn btn-secondary btn-sm" onclick="closeModal('modalManageSubjectGroups')">
                Selesai & Tutup
            </button>
        </div>
    </div>
</div>

<!-- MODAL: Tambah Bidang Keahlian SMK -->
<div class="vx-modal-backdrop modal-backdrop" id="modalAddVocationalField">
    <div class="vx-modal modal-dialog">
        <div class="vx-modal-header modal-title-bar">
            <h3 class="vx-modal-title">
                <span class="modal-title-icon"><i class='bx bx-layer'></i></span>
                Tambah Bidang Keahlian SMK
            </h3>
            <button class="vx-modal-close close-btn" onclick="closeModal('modalAddVocationalField')">&times;</button>
        </div>
        <form id="formAddVocationalField" onsubmit="submitAddVocationalField(event)">
            <div class="vx-modal-body modal-content">
                <div class="vx-form-grid form-grid-2">
                    <div class="form-row span-2">
                        <label class="vx-label">Nama Bidang Keahlian *</label>
                        <input type="text" class="vx-input form-input-clean" name="name" placeholder="Contoh: Energi dan Pertambangan" required>
                    </div>
                    <div class="form-row">
                        <label class="vx-label">Kode Bidang *</label>
                        <input type="text" class="vx-input form-input-clean" name="code" placeholder="EP" required>
                    </div>
                    <div class="form-row span-2">
                        <label class="vx-label">Deskripsi / Ruang Lingkup</label>
                        <textarea class="vx-input form-input-clean" name="description" rows="2" placeholder="Cakupan kompetensi keahlian..."></textarea>
                    </div>
                </div>
            </div>
            <div class="vx-modal-footer modal-bottom">
                <button type="button" class="btn btn-secondary" onclick="closeModal('modalAddVocationalField')">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Bidang Keahlian</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL: Tambah Program Keahlian SMK -->
<div class="vx-modal-backdrop modal-backdrop" id="modalAddVocationalProgram">
    <div class="vx-modal modal-dialog">
        <div class="vx-modal-header modal-title-bar">
            <h3 class="vx-modal-title">
                <span class="modal-title-icon"><i class='bx bx-folder-plus'></i></span>
                Tambah Program Keahlian SMK (Fase E)
            </h3>
            <button class="vx-modal-close close-btn" onclick="closeModal('modalAddVocationalProgram')">&times;</button>
        </div>
        <form id="formAddVocationalProgram" onsubmit="submitAddVocationalProgram(event)">
            <div class="vx-modal-body modal-content">
                <div class="vx-form-grid form-grid-2">
                    <div class="form-row span-2">
                        <label class="vx-label">Pilih Bidang Keahlian Induk *</label>
                        <select class="vx-input form-input-clean" id="addProgFieldSelect" name="vocational_field_id" required>
                            @foreach($vocationalFields as $vf)
                                <option value="{{ $vf->id }}">{{ $vf->name }} ({{ $vf->code }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-row span-2">
                        <label class="vx-label">Nama Program Keahlian *</label>
                        <input type="text" class="vx-input form-input-clean" name="name" placeholder="Contoh: Desain Komunikasi Visual" required>
                    </div>
                    <div class="form-row">
                        <label class="vx-label">Kode Singkat *</label>
                        <input type="text" class="vx-input form-input-clean" name="code" placeholder="DKV" required>
                    </div>
                    <div class="form-row">
                        <label class="vx-label">Durasi Program Belajar *</label>
                        <select class="vx-input form-input-clean" name="program_duration_years" required>
                            <option value="3">SMK 3 Tahun (Standar Reguler)</option>
                            <option value="4">SMK 4 Tahun (Fast Track / Magang 1 Thn)</option>
                        </select>
                    </div>
                    <div class="form-row span-2">
                        <label class="vx-label">Deskripsi Dasar Kejuruan (Fase E)</label>
                        <textarea class="vx-input form-input-clean" name="description" rows="2" placeholder="Kompetensi dasar kejuruan kelas 10..."></textarea>
                    </div>
                </div>
            </div>
            <div class="vx-modal-footer modal-bottom">
                <button type="button" class="btn btn-secondary" onclick="closeModal('modalAddVocationalProgram')">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Program Keahlian</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL: Tambah Konsentrasi Keahlian SMK -->
<div class="vx-modal-backdrop modal-backdrop" id="modalAddVocationalConcentration">
    <div class="vx-modal modal-dialog" style="max-width: 650px;">
        <div class="vx-modal-header modal-title-bar">
            <h3 class="vx-modal-title">
                <span class="modal-title-icon"><i class='bx bx-award'></i></span>
                Tambah Konsentrasi Keahlian SMK (Fase F)
            </h3>
            <button class="vx-modal-close close-btn" onclick="closeModal('modalAddVocationalConcentration')">&times;</button>
        </div>
        <form id="formAddVocationalConcentration" onsubmit="submitAddVocationalConcentration(event)">
            <div class="vx-modal-body modal-content">
                <div class="vx-form-grid form-grid-2">
                    <div class="form-row span-2">
                        <label class="vx-label">Pilih Program Keahlian *</label>
                        <select class="vx-input form-input-clean" id="addConcProgramSelect" name="vocational_program_id" required>
                            @foreach($vocationalPrograms as $vp)
                                <option value="{{ $vp->id }}">{{ $vp->name }} ({{ $vp->code }}) — Bidang: {{ $vp->field?->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-row span-2">
                        <label class="vx-label">Nama Konsentrasi Keahlian *</label>
                        <input type="text" class="vx-input form-input-clean" name="name" placeholder="Contoh: Rekayasa Perangkat Lunak (RPL)" required>
                    </div>
                    <div class="form-row">
                        <label class="vx-label">Kode Konsentrasi *</label>
                        <input type="text" class="vx-input form-input-clean" name="code" placeholder="PPLG-RPL" required>
                    </div>
                    <div class="form-row">
                        <label class="vx-label">Kode Dapodik Resmi (Opsional)</label>
                        <input type="text" class="vx-input form-input-clean" name="dapodik_code" placeholder="201111">
                    </div>
                    <div class="form-row span-2">
                        <label class="vx-label">Mitra Dunia Usaha & Industri (DUDI)</label>
                        <input type="text" class="vx-input form-input-clean" name="industry_partners" placeholder="Contoh: PT Telkom Indonesia, PT Tokopedia, Dicoding Academy">
                    </div>
                    <div class="form-row span-2">
                        <label class="vx-label">Ringkasan Profil Kompetensi Lulusan (SKKNI)</label>
                        <textarea class="vx-input form-input-clean" name="competencies_summary" rows="2" placeholder="Kompetensi inti yang dikuasai siswa saat lulus..."></textarea>
                    </div>
                    <div class="form-row span-2">
                        <label class="vx-label">Deskripsi Spesialisasi Kejuruan</label>
                        <textarea class="vx-input form-input-clean" name="description" rows="2" placeholder="Fokus materi pembelajaran tingkat 11 dan 12..."></textarea>
                    </div>
                </div>
            </div>
            <div class="vx-modal-footer modal-bottom">
                <button type="button" class="btn btn-secondary" onclick="closeModal('modalAddVocationalConcentration')">Batal</button>
                <button type="submit" class="btn btn-primary">Daftarkan Konsentrasi</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL: Edit Konsentrasi Keahlian SMK -->
<div class="vx-modal-backdrop modal-backdrop" id="modalEditVocationalConcentration">
    <div class="vx-modal modal-dialog" style="max-width: 650px;">
        <div class="vx-modal-header modal-title-bar">
            <h3 class="vx-modal-title">
                <span class="modal-title-icon"><i class='bx bx-edit'></i></span>
                Edit Konsentrasi Keahlian SMK
            </h3>
            <button class="vx-modal-close close-btn" onclick="closeModal('modalEditVocationalConcentration')">&times;</button>
        </div>
        <form id="formEditVocationalConcentration" onsubmit="submitEditVocationalConcentration(event)">
            <input type="hidden" id="editConcId" name="id">
            <div class="vx-modal-body modal-content">
                <div class="vx-form-grid form-grid-2">
                    <div class="form-row span-2">
                        <label class="vx-label">Nama Konsentrasi Keahlian *</label>
                        <input type="text" class="vx-input form-input-clean" id="editConcName" name="name" required>
                    </div>
                    <div class="form-row span-2">
                        <label class="vx-label">Kode Dapodik Resmi</label>
                        <input type="text" class="vx-input form-input-clean" id="editConcDapodikCode" name="dapodik_code">
                    </div>
                    <div class="form-row span-2">
                        <label class="vx-label">Mitra Dunia Usaha & Industri (DUDI)</label>
                        <input type="text" class="vx-input form-input-clean" id="editConcPartners" name="industry_partners">
                    </div>
                    <div class="form-row span-2">
                        <label class="vx-label">Ringkasan Profil Kompetensi Lulusan (SKKNI)</label>
                        <textarea class="vx-input form-input-clean" id="editConcCompetencies" name="competencies_summary" rows="2"></textarea>
                    </div>
                    <div class="form-row span-2">
                        <label class="vx-label">Deskripsi Spesialisasi Kejuruan</label>
                        <textarea class="vx-input form-input-clean" id="editConcDesc" name="description" rows="2"></textarea>
                    </div>
                </div>
            </div>
            <div class="vx-modal-footer modal-bottom">
                <button type="button" class="btn btn-secondary" onclick="closeModal('modalEditVocationalConcentration')">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL: Tambah Satuan Pendidikan Dapodik -->
<div class="vx-modal-backdrop modal-backdrop" id="modalAddSchool">
    <div class="vx-modal modal-dialog" style="max-width: 800px;">
        <div class="vx-modal-header modal-title-bar">
            <h3 class="vx-modal-title">
                <span class="modal-title-icon"><i class='bx bxs-institution'></i></span>
                Tambah Satuan Pendidikan (Standar Dapodik)
            </h3>
            <button class="vx-modal-close close-btn" onclick="closeModal('modalAddSchool')">&times;</button>
        </div>
        <form id="formAddSchool" onsubmit="submitAddSchool(event)">
            <div class="vx-modal-body modal-content" style="max-height: 75vh; overflow-y: auto;">
                <!-- Step 1: Identitas Pokok Dapodik -->
                <div style="font-weight: 700; font-size: 0.85rem; color: var(--vx-primary); margin-bottom: 0.6rem; border-bottom: 1px solid var(--vx-border-light); padding-bottom: 0.3rem;">
                    1. IDENTITAS UTAMA PENDIDIKAN
                </div>
                <div class="vx-form-grid form-grid-2">
                    <div class="form-row span-2">
                        <label class="vx-label">Nama Satuan Pendidikan *</label>
                        <input type="text" class="vx-input form-input-clean" name="name" placeholder="Contoh: SMK Negeri 2 Bandung" required>
                    </div>
                    <div class="form-row">
                        <label class="vx-label">NPSN (8 Digit) *</label>
                        <input type="text" class="vx-input form-input-clean" name="npsn" maxlength="8" placeholder="Contoh: 20219876" required>
                    </div>
                    <div class="form-row">
                        <label class="vx-label">Bentuk Pendidikan (Jenjang) *</label>
                        <select class="vx-input form-input-clean" name="level" required>
                            <option value="SD">SD (Sekolah Dasar)</option>
                            <option value="SMP">SMP (Sekolah Menengah Pertama)</option>
                            <option value="SMA">SMA (Sekolah Menengah Atas)</option>
                            <option value="SMK" selected>SMK (Sekolah Menengah Kejuruan)</option>
                        </select>
                    </div>
                    <div class="form-row">
                        <label class="vx-label">Status Sekolah *</label>
                        <select class="vx-input form-input-clean" name="school_status" required>
                            <option value="Negeri" selected>Negeri</option>
                            <option value="Swasta">Swasta</option>
                        </select>
                    </div>
                    <div class="form-row">
                        <label class="vx-label">Akreditasi</label>
                        <select class="vx-input form-input-clean" name="accreditation">
                            <option value="A" selected>A (Unggul)</option>
                            <option value="B">B (Baik)</option>
                            <option value="C">C (Cukup)</option>
                            <option value="Belum Terakreditasi">Belum Terakreditasi</option>
                        </select>
                    </div>
                </div>

                <!-- Step 2: Legalitas & Perizinan -->
                <div style="font-weight: 700; font-size: 0.85rem; color: var(--vx-primary); margin: 1rem 0 0.6rem; border-bottom: 1px solid var(--vx-border-light); padding-bottom: 0.3rem;">
                    2. LEGALITAS, IZIN OPERASIONAL & PERPAJAKAN
                </div>
                <div class="vx-form-grid form-grid-2">
                    <div class="form-row">
                        <label class="vx-label">Nomor SK Pendirian</label>
                        <input type="text" class="vx-input form-input-clean" name="establishment_decree" placeholder="Contoh: 01/SK/PEND/1975">
                    </div>
                    <div class="form-row">
                        <label class="vx-label">Tanggal SK Pendirian</label>
                        <input type="date" class="vx-input form-input-clean" name="establishment_date">
                    </div>
                    <div class="form-row">
                        <label class="vx-label">Nomor SK Izin Operasional</label>
                        <input type="text" class="vx-input form-input-clean" name="operational_license_decree" placeholder="Contoh: 421.5/789/DISDIK/2015">
                    </div>
                    <div class="form-row">
                        <label class="vx-label">Tanggal SK Izin Operasional</label>
                        <input type="date" class="vx-input form-input-clean" name="operational_license_date">
                    </div>
                    <div class="form-row span-2">
                        <label class="vx-label">NPWP Satuan Pendidikan</label>
                        <input type="text" class="vx-input form-input-clean" name="tax_number" placeholder="Contoh: 00.123.456.7-015.000">
                    </div>
                </div>

                <!-- Step 3: Alamat & Wilayah Dapodik -->
                <div style="font-weight: 700; font-size: 0.85rem; color: var(--vx-primary); margin: 1rem 0 0.6rem; border-bottom: 1px solid var(--vx-border-light); padding-bottom: 0.3rem;">
                    3. ALAMAT & TITIK KOORDINAT SPASIAL (GPS)
                </div>
                <div class="vx-form-grid form-grid-2">
                    <div class="form-row span-2">
                        <label class="vx-label">Alamat Jalan / Gedung *</label>
                        <input type="text" class="vx-input form-input-clean" name="address" placeholder="Jl. Raya Utama No. 123" required>
                    </div>
                    <div class="form-row">
                        <label class="vx-label">RT / RW</label>
                        <input type="text" class="vx-input form-input-clean" name="rt_rw" placeholder="001/002">
                    </div>
                    <div class="form-row">
                        <label class="vx-label">Nama Dusun / Lingkungan</label>
                        <input type="text" class="vx-input form-input-clean" name="sub_village" placeholder="Contoh: Sukajadi">
                    </div>
                    <div class="form-row">
                        <label class="vx-label">Desa / Kelurahan</label>
                        <input type="text" class="vx-input form-input-clean" name="village" placeholder="Contoh: Pasirkaliki">
                    </div>
                    <div class="form-row">
                        <label class="vx-label">Kecamatan</label>
                        <input type="text" class="vx-input form-input-clean" name="subdistrict" placeholder="Contoh: Cicendo">
                    </div>
                    <div class="form-row">
                        <label class="vx-label">Kabupaten / Kota</label>
                        <input type="text" class="vx-input form-input-clean" name="city" placeholder="Contoh: Kota Bandung">
                    </div>
                    <div class="form-row">
                        <label class="vx-label">Provinsi</label>
                        <input type="text" class="vx-input form-input-clean" name="province" placeholder="Contoh: Jawa Barat">
                    </div>
                    <div class="form-row">
                        <label class="vx-label">Kode Pos</label>
                        <input type="text" class="vx-input form-input-clean" name="postal_code" placeholder="40171">
                    </div>
                    <div class="form-row">
                        <label class="vx-label">Lintang (Latitude GPS)</label>
                        <input type="number" step="any" class="vx-input form-input-clean" name="latitude" placeholder="-6.917464">
                    </div>
                    <div class="form-row">
                        <label class="vx-label">Bujur (Longitude GPS)</label>
                        <input type="number" step="any" class="vx-input form-input-clean" name="longitude" placeholder="107.619122">
                    </div>
                </div>

                <!-- Step 4: Kepala Sekolah & Kontak Resmi -->
                <div style="font-weight: 700; font-size: 0.85rem; color: var(--vx-primary); margin: 1rem 0 0.6rem; border-bottom: 1px solid var(--vx-border-light); padding-bottom: 0.3rem;">
                    4. PIMPINAN SEKOLAH & KONTAK RESMI
                </div>
                <div class="vx-form-grid form-grid-2">
                    <div class="form-row">
                        <label class="vx-label">Nama Kepala Sekolah Lengkap</label>
                        <input type="text" class="vx-input form-input-clean" name="principal_name" placeholder="Drs. H. Ahmad Fauzi, M.M.">
                    </div>
                    <div class="form-row">
                        <label class="vx-label">NIP Kepala Sekolah</label>
                        <input type="text" class="vx-input form-input-clean" name="principal_nip" placeholder="197508122000031001">
                    </div>
                    <div class="form-row">
                        <label class="vx-label">Nomor Telepon Sekolah</label>
                        <input type="text" class="vx-input form-input-clean" name="phone" placeholder="(022) 4201234">
                    </div>
                    <div class="form-row">
                        <label class="vx-label">Nomor Fax</label>
                        <input type="text" class="vx-input form-input-clean" name="fax" placeholder="(022) 4201235">
                    </div>
                    <div class="form-row">
                        <label class="vx-label">Email Resmi Sekolah</label>
                        <input type="email" class="vx-input form-input-clean" name="email" placeholder="kontak@smkn2bandung.sch.id">
                    </div>
                    <div class="form-row">
                        <label class="vx-label">Website Resmi</label>
                        <input type="url" class="vx-input form-input-clean" name="website" placeholder="https://smkn2bandung.sch.id">
                    </div>
                </div>
            </div>
            <div class="vx-modal-footer modal-bottom">
                <button type="button" class="btn btn-secondary" onclick="closeModal('modalAddSchool')">Batal</button>
                <button type="submit" class="btn btn-primary"><i class='bx bx-check'></i> Daftarkan Satuan Pendidikan</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL: Tambah Rombel (Kelas) -->
<div class="vx-modal-backdrop modal-backdrop" id="modalAddClass">
    <div class="vx-modal modal-dialog" style="max-width: 680px;">
        <div class="vx-modal-header modal-title-bar">
            <h3 class="vx-modal-title">
                <span class="modal-title-icon"><i class='bx bxs-school'></i></span>
                Tambah Rombongan Belajar (Rombel)
            </h3>
            <button class="vx-modal-close close-btn" onclick="closeModal('modalAddClass')">&times;</button>
        </div>
        <form id="formAddClass" onsubmit="submitAddClass(event)">
            <div class="vx-modal-body modal-content">
                <div class="vx-form-grid form-grid-2">
                    <div class="form-row span-2">
                        <label class="vx-label">Nama Rombel / Kelas *</label>
                        <input type="text" class="vx-input form-input-clean" name="name" placeholder="Contoh: X PPLG 1, XI RPL 2, XII TKJ 1" required>
                    </div>
                    <div class="form-row">
                        <label class="vx-label">Tingkat Kelas *</label>
                        <select class="vx-input form-input-clean" name="grade_level" required>
                            @if(($school->level ?? '') === 'SMK' || $isVocational)
                                <option value="10">Tingkat 10 (Kelas X - Fase E)</option>
                                <option value="11">Tingkat 11 (Kelas XI - Fase F)</option>
                                <option value="12">Tingkat 12 (Kelas XII - Fase F)</option>
                                <option value="13">Tingkat 13 (Kelas XIII - Fast Track)</option>
                            @elseif(($school->level ?? '') === 'SMA')
                                <option value="10">Tingkat 10 (Kelas X)</option>
                                <option value="11">Tingkat 11 (Kelas XI)</option>
                                <option value="12">Tingkat 12 (Kelas XII)</option>
                            @elseif(($school->level ?? '') === 'SMP')
                                <option value="7">Tingkat 7 (Kelas VII)</option>
                                <option value="8">Tingkat 8 (Kelas VIII)</option>
                                <option value="9">Tingkat 9 (Kelas IX)</option>
                            @elseif(($school->level ?? '') === 'SD')
                                <option value="1">Kelas 1</option>
                                <option value="2">Kelas 2</option>
                                <option value="3">Kelas 3</option>
                                <option value="4">Kelas 4</option>
                                <option value="5">Kelas 5</option>
                                <option value="6">Kelas 6</option>
                            @else
                                <option value="10">Tingkat 10</option>
                                <option value="11">Tingkat 11</option>
                                <option value="12">Tingkat 12</option>
                            @endif
                        </select>
                    </div>
                    <div class="form-row">
                        <label class="vx-label">Tahun Ajaran *</label>
                        <select class="vx-input form-input-clean" name="academic_year_id" required>
                            @foreach($academicYears as $ay)
                                <option value="{{ $ay->id }}" {{ $ay->is_active ? 'selected' : '' }}>
                                    {{ $ay->name }} {{ $ay->is_active ? '(Aktif)' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-row span-2">
                        <label class="vx-label">Kurikulum Terkait</label>
                        <select class="vx-input form-input-clean" name="curriculum_id">
                            <option value="">— Pilih Kurikulum (Opsional) —</option>
                            @foreach($curriculums as $curr)
                                <option value="{{ $curr->id }}">{{ $curr->name }} ({{ $curr->code }})</option>
                            @endforeach
                        </select>
                    </div>

                    @if(($school->level ?? '') === 'SMK' || $isVocational)
                        <div class="form-row span-2">
                            <label class="vx-label">Konsentrasi Keahlian (Fase F Kelas 11-12)</label>
                            <select class="vx-input form-input-clean" name="vocational_concentration_id">
                                <option value="">— Pilih Konsentrasi Keahlian —</option>
                                @foreach($vocationalConcentrations as $conc)
                                    <option value="{{ $conc->id }}">
                                        {{ $conc->name }} ({{ $conc->code }}) — Program: {{ $conc->program?->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-row span-2">
                            <label class="vx-label">Program Keahlian Induk (Fase E Kelas 10)</label>
                            <select class="vx-input form-input-clean" name="vocational_program_id">
                                <option value="">— Pilih Program Keahlian (Opsional) —</option>
                                @foreach($vocationalPrograms as $prog)
                                    <option value="{{ $prog->id }}">{{ $prog->name }} ({{ $prog->code }})</option>
                                @endforeach
                            </select>
                        </div>
                    @else
                        <div class="form-row span-2">
                            <label class="vx-label">Jurusan / Peminatan</label>
                            <select class="vx-input form-input-clean" name="department_id">
                                <option value="">— Pilih Jurusan / Peminatan —</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->id }}">{{ $dept->name }} ({{ $dept->code }})</option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <div class="form-row span-2">
                        <label class="vx-label">Wali Kelas (Homeroom Teacher)</label>
                        <select class="vx-input form-input-clean" name="homeroom_teacher_id">
                            <option value="">— Belum Ditentukan —</option>
                            @foreach($teachers as $t)
                                @if($t->user_id)
                                    <option value="{{ $t->user_id }}">{{ $t->full_name }} (NIP: {{ $t->nip ?: '—' }})</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="vx-modal-footer modal-bottom">
                <button type="button" class="btn btn-secondary" onclick="closeModal('modalAddClass')">Batal</button>
                <button type="submit" class="btn btn-primary"><i class='bx bx-check'></i> Simpan Rombel</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL: Edit Rombel (Kelas) -->
<div class="vx-modal-backdrop modal-backdrop" id="modalEditClass">
    <div class="vx-modal modal-dialog" style="max-width: 680px;">
        <div class="vx-modal-header modal-title-bar">
            <h3 class="vx-modal-title">
                <span class="modal-title-icon"><i class='bx bx-edit'></i></span>
                Edit Rombongan Belajar (Rombel)
            </h3>
            <button class="vx-modal-close close-btn" onclick="closeModal('modalEditClass')">&times;</button>
        </div>
        <form id="formEditClass" onsubmit="submitEditClass(event)">
            <input type="hidden" id="editClassId" name="id">
            <div class="vx-modal-body modal-content">
                <div class="vx-form-grid form-grid-2">
                    <div class="form-row span-2">
                        <label class="vx-label">Nama Rombel / Kelas *</label>
                        <input type="text" class="vx-input form-input-clean" id="editClassName" name="name" required>
                    </div>
                    <div class="form-row">
                        <label class="vx-label">Tingkat Kelas *</label>
                        <select class="vx-input form-input-clean" id="editClassGradeLevel" name="grade_level" required>
                            @if(($school->level ?? '') === 'SMK' || $isVocational)
                                <option value="10">Tingkat 10 (Kelas X)</option>
                                <option value="11">Tingkat 11 (Kelas XI)</option>
                                <option value="12">Tingkat 12 (Kelas XII)</option>
                                <option value="13">Tingkat 13 (Kelas XIII)</option>
                            @elseif(($school->level ?? '') === 'SMA')
                                <option value="10">Tingkat 10 (Kelas X)</option>
                                <option value="11">Tingkat 11 (Kelas XI)</option>
                                <option value="12">Tingkat 12 (Kelas XII)</option>
                            @elseif(($school->level ?? '') === 'SMP')
                                <option value="7">Tingkat 7 (Kelas VII)</option>
                                <option value="8">Tingkat 8 (Kelas VIII)</option>
                                <option value="9">Tingkat 9 (Kelas IX)</option>
                            @elseif(($school->level ?? '') === 'SD')
                                <option value="1">Kelas 1</option>
                                <option value="2">Kelas 2</option>
                                <option value="3">Kelas 3</option>
                                <option value="4">Kelas 4</option>
                                <option value="5">Kelas 5</option>
                                <option value="6">Kelas 6</option>
                            @else
                                <option value="10">Tingkat 10</option>
                                <option value="11">Tingkat 11</option>
                                <option value="12">Tingkat 12</option>
                            @endif
                        </select>
                    </div>
                    <div class="form-row">
                        <label class="vx-label">Tahun Ajaran</label>
                        <select class="vx-input form-input-clean" id="editClassAcademicYear" name="academic_year_id">
                            <option value="">— Pilih Tahun Ajaran —</option>
                            @foreach($academicYears as $ay)
                                <option value="{{ $ay->id }}">{{ $ay->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-row span-2">
                        <label class="vx-label">Kurikulum Terkait</label>
                        <select class="vx-input form-input-clean" id="editClassCurriculum" name="curriculum_id">
                            <option value="">— Tidak Terikat Kurikulum —</option>
                            @foreach($curriculums as $curr)
                                <option value="{{ $curr->id }}">{{ $curr->name }} ({{ $curr->code }})</option>
                            @endforeach
                        </select>
                    </div>

                    @if(($school->level ?? '') === 'SMK' || $isVocational)
                        <div class="form-row span-2">
                            <label class="vx-label">Konsentrasi Keahlian (Fase F)</label>
                            <select class="vx-input form-input-clean" id="editClassConcentration" name="vocational_concentration_id">
                                <option value="">— Pilih Konsentrasi Keahlian —</option>
                                @foreach($vocationalConcentrations as $conc)
                                    <option value="{{ $conc->id }}">
                                        {{ $conc->name }} ({{ $conc->code }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-row span-2">
                            <label class="vx-label">Program Keahlian Induk (Fase E)</label>
                            <select class="vx-input form-input-clean" id="editClassProgram" name="vocational_program_id">
                                <option value="">— Pilih Program Keahlian —</option>
                                @foreach($vocationalPrograms as $prog)
                                    <option value="{{ $prog->id }}">{{ $prog->name }} ({{ $prog->code }})</option>
                                @endforeach
                            </select>
                        </div>
                    @else
                        <div class="form-row span-2">
                            <label class="vx-label">Jurusan / Peminatan</label>
                            <select class="vx-input form-input-clean" id="editClassDepartment" name="department_id">
                                <option value="">— Pilih Jurusan / Peminatan —</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->id }}">{{ $dept->name }} ({{ $dept->code }})</option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <div class="form-row span-2">
                        <label class="vx-label">Wali Kelas</label>
                        <select class="vx-input form-input-clean" id="editClassHomeroom" name="homeroom_teacher_id">
                            <option value="">— Belum Ditentukan —</option>
                            @foreach($teachers as $t)
                                @if($t->user_id)
                                    <option value="{{ $t->user_id }}">{{ $t->full_name }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="vx-modal-footer modal-bottom">
                <button type="button" class="btn btn-secondary" onclick="closeModal('modalEditClass')">Batal</button>
                <button type="submit" class="btn btn-primary"><i class='bx bx-check'></i> Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL: Tambah Mata Pelajaran & Penyelarasan Kelompok Kurikulum -->
<div class="vx-modal-backdrop modal-backdrop" id="modalAddSubject">
    <div class="vx-modal modal-dialog" style="max-width: 680px;">
        <div class="vx-modal-header modal-title-bar">
            <h3 class="vx-modal-title">
                <span class="modal-title-icon"><i class='bx bx-book-plus'></i></span>
                Tambah Mata Pelajaran
            </h3>
            <button class="vx-modal-close close-btn" onclick="closeModal('modalAddSubject')">&times;</button>
        </div>
        <form id="formAddSubject" onsubmit="submitAddSubject(event)">
            <div class="vx-modal-body modal-content">
                <div class="vx-form-grid form-grid-2">
                    <div class="form-row span-2">
                        <label class="vx-label">Nama Mata Pelajaran *</label>
                        <input type="text" class="vx-input form-input-clean" name="name" placeholder="Contoh: Matematika, Dasar-Dasar Kejuruan PPLG" required>
                    </div>
                    <div class="form-row">
                        <label class="vx-label">Kode Mata Pelajaran *</label>
                        <input type="text" class="vx-input form-input-clean" name="code" placeholder="Contoh: MTK, PPLG-01" required>
                    </div>
                    <div class="form-row">
                        <label class="vx-label">Standar Ketuntasan (KKM / KKTP) *</label>
                        <input type="number" step="0.1" class="vx-input form-input-clean" name="passing_grade" value="75.0" min="0" max="100" required>
                    </div>
                    <div class="form-row span-2">
                        <label class="vx-label">Warna Tampilan di Tabel Jadwal Pelajaran</label>
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <input type="color" name="color" id="addSubjectColorInput" value="#4680ff" style="width: 44px; height: 36px; padding: 2px; border: 1px solid var(--vx-border-light); border-radius: 6px; cursor: pointer;">
                            @foreach(['#4680ff', '#10b981', '#f59e0b', '#8b5cf6', '#ec4899', '#06b6d4', '#ef4444', '#14b8a6'] as $presetHex)
                                <button type="button" onclick="document.getElementById('addSubjectColorInput').value='{{ $presetHex }}'" style="width: 22px; height: 22px; border-radius: 50%; background: {{ $presetHex }}; border: 2px solid #fff; box-shadow: 0 0 0 1px rgba(0,0,0,0.2); cursor: pointer;"></button>
                            @endforeach
                        </div>
                    </div>

                    <!-- Penyelarasan Kelompok Mapel & Kurikulum -->
                    <div class="form-row span-2" style="background: rgba(115, 103, 240, 0.05); border: 1px solid var(--vx-border-light); border-radius: var(--vx-radius-md); padding: 1rem; margin-top: 0.5rem;">
                        <div style="font-weight: 700; font-size: 0.85rem; color: var(--vx-primary); margin-bottom: 0.6rem; display: flex; align-items: center; gap: 0.4rem;">
                            <i class='bx bx-git-repo-forked'></i> Penyelarasan Kurikulum & Kelompok Mapel
                        </div>
                        <div class="vx-form-grid form-grid-2" style="margin: 0;">
                            <div class="form-row span-2">
                                <label class="vx-label">Pilih Kurikulum yang Menerapkan</label>
                                <select class="vx-input form-input-clean" name="curriculum_id" id="addSubjectCurriculumSelect" onchange="updateAddSubjectGroupDropdown(this.value)">
                                    <option value="">— Tidak Langsung Terikat Kurikulum —</option>
                                    @foreach($curriculums as $cOpt)
                                        <option value="{{ $cOpt->id }}" {{ $loop->first ? 'selected' : '' }}>
                                            {{ $cOpt->name }} ({{ $cOpt->code }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-row">
                                <label class="vx-label">Kelompok Mata Pelajaran *</label>
                                <select class="vx-input form-input-clean" name="subject_group" id="addSubjectGroupSelect" required>
                                    <!-- Dinamis dari kurikulum -->
                                    <option value="WAJIB_UMUM" selected>Kelompok Umum / Wajib Nasional</option>
                                    <option value="PEMINATAN_PILIHAN">Kelompok Kejuruan / Pilihan</option>
                                    <option value="P5_PROJEK">Projek Profil Pelajar Pancasila (P5)</option>
                                    <option value="MULOK">Muatan Lokal (Mulok)</option>
                                    <option value="KEAGAMAAN">Keagamaan / Kepesantrenan</option>
                                </select>
                            </div>
                            <div class="form-row">
                                <label class="vx-label">Alokasi Beban (JP / Minggu) *</label>
                                <input type="number" class="vx-input form-input-clean" name="hours_per_week" value="4" min="1" max="15" required>
                            </div>
                        </div>
                        <div style="font-size: 0.74rem; color: var(--vx-text-muted); margin-top: 0.5rem;">
                            <i class='bx bx-info-circle'></i> Kelompok mapel mengikuti kurikulum yang dipilih. Mata pelajaran otomatis disinkronkan ke struktur kurikulum.
                        </div>
                    </div>
                </div>
            </div>
            <div class="vx-modal-footer modal-bottom">
                <button type="button" class="btn btn-secondary" onclick="closeModal('modalAddSubject')">Batal</button>
                <button type="submit" class="btn btn-primary"><i class='bx bx-check'></i> Simpan Mata Pelajaran</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL: Edit Mata Pelajaran -->
<div class="vx-modal-backdrop modal-backdrop" id="modalEditSubject">
    <div class="vx-modal modal-dialog" style="max-width: 680px;">
        <div class="vx-modal-header modal-title-bar">
            <h3 class="vx-modal-title">
                <span class="modal-title-icon"><i class='bx bx-edit'></i></span>
                Edit Mata Pelajaran & Kelompok
            </h3>
            <button class="vx-modal-close close-btn" onclick="closeModal('modalEditSubject')">&times;</button>
        </div>
        <form id="formEditSubject" onsubmit="submitEditSubject(event)">
            <input type="hidden" id="editSubjectId" name="id">
            <div class="vx-modal-body modal-content">
                <div class="vx-form-grid form-grid-2">
                    <div class="form-row span-2">
                        <label class="vx-label">Nama Mata Pelajaran *</label>
                        <input type="text" class="vx-input form-input-clean" id="editSubjectName" name="name" required>
                    </div>
                    <div class="form-row">
                        <label class="vx-label">Kode Mata Pelajaran *</label>
                        <input type="text" class="vx-input form-input-clean" id="editSubjectCode" name="code" required>
                    </div>
                    <div class="form-row">
                        <label class="vx-label">Standar Ketuntasan (KKM / KKTP) *</label>
                        <input type="number" step="0.1" class="vx-input form-input-clean" id="editSubjectPassingGrade" name="passing_grade" min="0" max="100" required>
                    </div>

                    <!-- Penyelarasan Kurikulum & Kelompok -->
                    <div class="form-row span-2" style="background: rgba(115, 103, 240, 0.05); border: 1px solid var(--vx-border-light); border-radius: var(--vx-radius-md); padding: 1rem; margin-top: 0.5rem;">
                        <div style="font-weight: 700; font-size: 0.85rem; color: var(--vx-primary); margin-bottom: 0.6rem; display: flex; align-items: center; gap: 0.4rem;">
                            <i class='bx bx-git-repo-forked'></i> Penyelarasan Kurikulum & Kelompok Mapel
                        </div>
                        <div class="vx-form-grid form-grid-2" style="margin: 0;">
                            <div class="form-row span-2">
                                <label class="vx-label">Kurikulum Terkait</label>
                                <select class="vx-input form-input-clean" id="editSubjectCurriculum" name="curriculum_id" onchange="updateEditSubjectGroupDropdown(this.value)">
                                    <option value="">— Tidak Terikat Kurikulum —</option>
                                    @foreach($curriculums as $cOpt)
                                        <option value="{{ $cOpt->id }}">{{ $cOpt->name }} ({{ $cOpt->code }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-row">
                                <label class="vx-label">Kelompok Mata Pelajaran *</label>
                                <select class="vx-input form-input-clean" id="editSubjectGroup" name="subject_group" required>
                                    <option value="WAJIB_UMUM">Kelompok Umum / Wajib Nasional</option>
                                    <option value="PEMINATAN_PILIHAN">Kelompok Kejuruan / Pilihan</option>
                                    <option value="P5_PROJEK">Projek Profil Pelajar Pancasila (P5)</option>
                                    <option value="MULOK">Muatan Lokal (Mulok)</option>
                                    <option value="KEAGAMAAN">Keagamaan / Kepesantrenan</option>
                                </select>
                            </div>
                            <div class="form-row">
                                <label class="vx-label">Alokasi Beban (JP / Minggu) *</label>
                                <input type="number" class="vx-input form-input-clean" id="editSubjectJp" name="hours_per_week" min="1" max="15" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-row">
                        <label class="vx-label">Status Mapel</label>
                        <select class="vx-input form-input-clean" id="editSubjectActive" name="is_active">
                            <option value="1">Aktif</option>
                            <option value="0">Non-aktif</option>
                        </select>
                    </div>
                    <div class="form-row">
                        <label class="vx-label">Warna Jadwal Pelajaran</label>
                        <div style="display: flex; align-items: center; gap: 0.4rem;">
                            <input type="color" id="editSubjectColor" name="color" value="#4680ff" style="width: 44px; height: 36px; padding: 2px; border: 1px solid var(--vx-border-light); border-radius: 6px; cursor: pointer;">
                            @foreach(['#4680ff', '#10b981', '#f59e0b', '#8b5cf6', '#ec4899', '#06b6d4', '#ef4444'] as $presetHex)
                                <button type="button" onclick="document.getElementById('editSubjectColor').value='{{ $presetHex }}'" style="width: 20px; height: 20px; border-radius: 50%; background: {{ $presetHex }}; border: 2px solid #fff; box-shadow: 0 0 0 1px rgba(0,0,0,0.2); cursor: pointer;"></button>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            <div class="vx-modal-footer modal-bottom">
                <button type="button" class="btn btn-secondary" onclick="closeModal('modalEditSubject')">Batal</button>
                <button type="submit" class="btn btn-primary"><i class='bx bx-check'></i> Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<!-- ==================================================== -->
<!-- MODAL: UNGGAH MASAL EXCEL (SISWA & GTK) -->
<!-- ==================================================== -->
<div class="vx-modal-backdrop modal-backdrop" id="modalBulkImport">
    <div class="vx-modal modal-dialog" style="max-width: 620px; width: 95%;">
        <div class="vx-modal-header modal-title-bar">
            <div>
                <h3 class="vx-modal-title" id="bulkImportTitle">
                    <span class="modal-title-icon"><i class='bx bxs-file-import'></i></span>
                    Impor Masal Data
                </h3>
                <p class="cell-sub" id="bulkImportSub" style="margin-top: 3px; font-size: 0.8rem; color: var(--vx-text-muted);">
                    Unggah file Excel/CSV untuk menambahkan data dan akun pengguna secara masal.
                </p>
            </div>
            <button class="vx-modal-close close-btn" onclick="closeModal('modalBulkImport')">&times;</button>
        </div>
        <form id="formBulkImport" onsubmit="submitBulkImport(event)" enctype="multipart/form-data">
            <input type="hidden" id="bulkImportType" value="students">
            
            <div class="vx-modal-body modal-content" style="padding: 1.25rem 1.5rem; max-height: 75vh; overflow-y: auto;">
                <!-- Step 1: Download Template Official Box -->
                <div style="background: var(--vx-body-bg); border: 1px solid var(--vx-border-light); border-radius: var(--radius-md); padding: 1rem; margin-bottom: 1.25rem; display: flex; align-items: center; justify-content: space-between; gap: 1rem;">
                    <div>
                        <div style="font-weight: 700; font-size: 0.85rem; color: var(--vx-text-heading); display: flex; align-items: center; gap: 0.35rem; margin-bottom: 0.2rem;">
                            <i class='bx bxs-download text-primary'></i> Langkah 1: Unduh Format Template
                        </div>
                        <div style="font-size: 0.76rem; color: var(--vx-text-muted);">
                            Gunakan template resmi untuk memastikan urutan kolom dan data tervalidasi dengan tepat.
                        </div>
                    </div>
                    <a href="javascript:void(0)" id="btnDownloadTemplate" class="btn btn-outline-primary btn-sm" style="white-space: nowrap; font-weight: 600; display: inline-flex; align-items: center; gap: 0.35rem;">
                        <i class='bx bx-download'></i> Unduh .xlsx
                    </a>
                </div>

                <!-- Info Box Otomasi Akun -->
                <div id="bulkImportAccountNotice" style="background: var(--vx-body-bg); border-left: 4px solid #10b981; padding: 0.85rem 1rem; border-radius: var(--radius-md); font-size: 0.8rem; margin-bottom: 1.25rem; line-height: 1.45;">
                    <!-- Diisi dinamis via JS -->
                </div>

                <!-- Step 2: Upload File -->
                <div class="form-row" style="margin-bottom: 1rem;">
                    <label class="vx-label">Langkah 2: Pilih File Excel / CSV Hasil Isian *</label>
                    <input type="file" id="bulkImportFileInput" name="file" class="vx-input form-input-clean" accept=".xlsx,.xls,.csv" required style="padding: 0.45rem; font-size: 0.82rem;">
                    <span class="cell-sub" style="font-size: 0.72rem; margin-top: 0.25rem; display: block;">Format didukung: <strong>.xlsx, .xls, .csv</strong> (Ukuran maksimal: 10 MB).</span>
                </div>

                <!-- Step 3: Opsi Penanganan Duplikasi -->
                <div class="form-row" style="margin-bottom: 1rem;">
                    <label class="vx-label">Penanganan Data yang Sudah Terdaftar</label>
                    <select name="duplicate_mode" id="bulkImportDuplicateMode" class="vx-input form-input-clean" style="font-size: 0.82rem;">
                        <option value="skip" selected>Lewati (Abaikan baris yang NISN / Email-nya sudah ada)</option>
                        <option value="update">Perbarui (Update data lama dengan isian Excel terbaru)</option>
                    </select>
                </div>

                <!-- Dynamic Result Alert Container -->
                <div id="bulkImportResultBox" style="display: none; margin-top: 1rem;">
                    <!-- Will show success stats & errors -->
                </div>
            </div>

            <div class="vx-modal-footer modal-bottom" style="display: flex; justify-content: space-between; align-items: center;">
                <button type="button" class="btn btn-secondary btn-sm" onclick="closeModal('modalBulkImport')">Batal</button>
                <button type="submit" class="btn btn-primary btn-sm" id="btnSubmitBulkImport" style="display: inline-flex; align-items: center; gap: 0.35rem; font-weight: 600;">
                    <i class='bx bx-cloud-upload'></i> Unggah & Proses Sekarang
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
    const SCHOOL_ID = {{ $school->id ?? 1 }};
    const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    let currentSelectedCategoryId = {{ $activeCategory->id ?? 1 }};
    const ALL_CLASSES = {!! json_encode($classes->map(function ($c) {
        return [
            'id' => $c->id,
            'name' => $c->name,
            'grade_level' => $c->grade_level,
            'academic_year_id' => $c->academic_year_id,
        ];
    })) !!};

    const sectionMeta = {
        'tab-schools': {
            title: 'Satuan Pendidikan & Profil Dapodik',
            icon: 'bx bxs-institution',
            count: '{{ isset($availableSchools) ? $availableSchools->count() : 1 }} Satuan Pendidikan'
        },
        'tab-years': {
            title: 'Tahun Ajaran & Semester',
            icon: 'bx bx-calendar',
            count: '{{ $academicYears->count() }} Data Terdaftar'
        },
        'tab-curriculums': {
            title: 'Kurikulum & Alokasi Mapel',
            icon: 'bx bx-book-bookmark',
            count: '{{ $stats["total_curriculums"] }} Kurikulum Terdaftar'
        },
        'tab-vocational': {
            title: 'Spektrum Keahlian SMK',
            icon: 'bx bx-wrench',
            count: '{{ $stats["total_vocational_concentrations"] }} Konsentrasi Keahlian'
        },
        'tab-schedules': {
            title: 'Jadwal & Jam Belajar (Multi-Kategori)',
            icon: 'bx bx-time',
            count: '{{ $timeCategories->count() }} Kategori Pola KBM'
        },
        'tab-teachers': {
            title: 'Guru & Tenaga Kependidikan (GTK)',
            icon: 'bx bxs-chalkboard',
            count: '{{ $stats["total_teachers"] }} Pendidik & Staf'
        },
        'tab-students': {
            title: 'Peserta Didik (Siswa)',
            icon: 'bx bxs-user-detail',
            count: '{{ $stats["total_students"] }} Siswa Aktif'
        },
        'tab-parents': {
            title: 'Orang Tua / Wali Murid',
            icon: 'bx bxs-group',
            count: '{{ $stats["total_parents"] }} Akun Wali'
        },
        'tab-classes': {
            title: '{{ (($school->level ?? "") === "SMK" || $isVocational) ? "Konsentrasi & Rombel (Kelas)" : "Jurusan & Rombel (Kelas)" }}',
            icon: 'bx bxs-school',
            count: '{{ $stats["total_classes"] }} Rombongan Belajar'
        },
        'tab-subjects': {
            title: 'Mata Pelajaran & Jadwal KBM',
            icon: 'bx bx-book-open',
            count: '{{ $stats["total_subjects"] }} Mata Pelajaran'
        }
    };

    function switchTab(tabId) {
        document.querySelectorAll('.tab-pane').forEach(pane => pane.classList.remove('active'));
        const targetPane = document.getElementById(tabId);
        if (targetPane) targetPane.classList.add('active');

        // Highlight active sub-menu item in sidebar
        document.querySelectorAll('.side-tab-link').forEach(link => link.classList.remove('active'));
        const sideLink = document.getElementById('side-' + tabId);
        if (sideLink) sideLink.classList.add('active');

        // Sync left sidebar item
        document.querySelectorAll('.sidebar-nav-scroll .apex-nav-item').forEach(el => el.classList.remove('active'));
        const sidebarNav = document.getElementById('sidebar-nav-' + tabId);
        if (sidebarNav) sidebarNav.classList.add('active');

        // Update active section banner
        const meta = sectionMeta[tabId];
        if (meta) {
            const titleEl = document.getElementById('activeSectionTitle');
            const iconEl = document.getElementById('activeSectionIcon');
            const countEl = document.getElementById('activeSectionCount');
            if (titleEl) titleEl.textContent = meta.title;
            if (iconEl) iconEl.innerHTML = `<i class='${meta.icon}'></i>`;
            if (countEl) countEl.textContent = meta.count;
        }

        // Keep hash in URL
        if (history.replaceState) {
            history.replaceState(null, null, '#' + tabId);
        } else {
            window.location.hash = tabId;
        }
    }

    window.addEventListener('DOMContentLoaded', () => {
        let initialTab = 'tab-years';
        if (window.location.hash) {
            const h = window.location.hash.replace('#', '');
            if (document.getElementById(h)) initialTab = h;
        } else {
            const params = new URLSearchParams(window.location.search);
            if (params.has('tab')) {
                const t = 'tab-' + params.get('tab').replace('tab-', '');
                if (document.getElementById(t)) initialTab = t;
            }
        }
        switchTab(initialTab);
    });

    function openEditCategoryModal(id, name, code, desc, start, end) {
        document.getElementById('editCatId').value = id;
        document.getElementById('editCatName').value = name;
        document.getElementById('editCatCode').value = code;
        document.getElementById('editCatDesc').value = desc || '';
        document.getElementById('editCatStart').value = start || '';
        document.getElementById('editCatEnd').value = end || '';
        openModal('modalEditCategory');
    }

    async function submitAddCategory(e) {
        e.preventDefault();
        const form = e.target;
        const payload = Object.fromEntries(new FormData(form).entries());
        payload.school_id = SCHOOL_ID;
        payload.is_default = payload.is_default === '1';
        try {
            const res = await fetch('/api/v1/time-categories', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN },
                body: JSON.stringify(payload)
            });
            const result = await res.json();
            if (res.ok && result.success) {
                vxShowToast(result.message, 'success');
                closeModal('modalAddCategory');
                form.reset();
                setTimeout(() => location.reload(), 700);
            } else {
                vxShowToast(result.message || 'Validasi gagal', 'error');
            }
        } catch (err) { vxShowToast(err.message, 'error'); }
    }

    async function submitEditCategory(e) {
        e.preventDefault();
        const form = e.target;
        const id = document.getElementById('editCatId').value;
        const payload = Object.fromEntries(new FormData(form).entries());
        delete payload.id;
        try {
            const res = await fetch(`/api/v1/time-categories/${id}`, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN },
                body: JSON.stringify(payload)
            });
            const result = await res.json();
            if (res.ok && result.success) {
                vxShowToast(result.message, 'success');
                closeModal('modalEditCategory');
                setTimeout(() => location.reload(), 700);
            } else {
                vxShowToast(result.message || 'Gagal mengubah kategori', 'error');
            }
        } catch (err) { vxShowToast(err.message, 'error'); }
    }

    async function deleteCategory(id, name) {
        if (!confirm(`Hapus kategori jadwal '${name}' beserta seluruh sesi jam di dalamnya?`)) return;
        try {
            const res = await fetch(`/api/v1/time-categories/${id}`, {
                method: 'DELETE',
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN }
            });
            const result = await res.json();
            if (res.ok && result.success) {
                vxShowToast(result.message, 'success');
                setTimeout(() => location.reload(), 700);
            } else {
                vxShowToast(result.message || 'Gagal menghapus kategori', 'error');
            }
        } catch (err) { vxShowToast(err.message, 'error'); }
    }

    async function selectCategory(id, name) {
        currentSelectedCategoryId = id;
        document.querySelectorAll('.cat-box').forEach(b => b.classList.remove('active'));
        const activeCard = document.getElementById(`cat-card-${id}`);
        if (activeCard) activeCard.classList.add('active');
        if (name) document.getElementById('currentCategoryTitle').innerText = name;
        try {
            const dayFilter = document.getElementById('periodDayFilter').value;
            const url = dayFilter ? `/api/v1/time-categories/${id}/periods?day_of_week=${dayFilter}` : `/api/v1/time-categories/${id}/periods`;
            const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
            const result = await res.json();
            if (res.ok && result.success) renderPeriodsTable(result.data);
        } catch (err) { console.error(err); }
    }

    function openAddPeriodModal() {
        document.getElementById('periodInputDay').value = document.getElementById('periodDayFilter').value || '1';
        openModal('modalAddPeriod');
    }

    async function submitAddPeriod(e) {
        e.preventDefault();
        const form = e.target;
        const payload = Object.fromEntries(new FormData(form).entries());
        payload.is_break = payload.is_break === '1';
        try {
            const res = await fetch(`/api/v1/time-categories/${currentSelectedCategoryId}/periods`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN },
                body: JSON.stringify(payload)
            });
            const result = await res.json();
            if (res.ok && result.success) {
                vxShowToast(result.message, 'success');
                closeModal('modalAddPeriod');
                form.reset();
                selectCategory(currentSelectedCategoryId);
            } else {
                vxShowToast(result.message || 'Validasi gagal', 'error');
            }
        } catch (err) { vxShowToast(err.message, 'error'); }
    }

    const daysMap = {1:'Senin',2:'Selasa',3:'Rabu',4:'Kamis',5:'Jumat',6:'Sabtu',7:'Minggu'};

    function setPeriodDay(day, btn) {
        document.querySelectorAll('.day-pills-wrap .day-pill-btn').forEach(b => b.classList.remove('active'));
        if (btn) btn.classList.add('active');
        const input = document.getElementById('periodDayFilter');
        if (input) input.value = day;
        filterPeriodByDay(day);
    }

    function filterPeriodByDay(day) {
        const rows = document.querySelectorAll('#periodTableBody tr');
        rows.forEach(r => {
            if (!day || r.getAttribute('data-day') === String(day)) {
                r.style.display = '';
            } else {
                r.style.display = 'none';
            }
        });
    }

    function renderPeriodsTable(periods) {
        const tbody = document.getElementById('periodTableBody');
        if (!periods || periods.length === 0) {
            tbody.innerHTML = `<tr><td colspan="8" style="text-align:center;padding:3rem;color:var(--text-muted);">
                <i class='bx bx-time-five' style="font-size:2.5rem;display:block;margin-bottom:0.75rem;color:var(--text-muted);"></i>
                Belum ada sesi jam pelajaran pada kategori ini.</td></tr>`;
            return;
        }
        const currentDay = document.getElementById('periodDayFilter')?.value || '';
        tbody.innerHTML = periods.map(p => {
            const start = p.start_time.substring(0, 5);
            const end = p.end_time.substring(0, 5);
            const typeChip = p.is_break
                ? '<span class="vx-badge badge-warning"><i class="bx bx-coffee"></i> Istirahat</span>'
                : '<span class="vx-badge badge-info"><i class="bx bx-book-reader"></i> KBM</span>';
            const periodNum = p.period_number
                ? `<span class="vx-badge badge-dark cell-mono">Jam Ke-${p.period_number}</span>`
                : '<span style="color:var(--text-muted);">—</span>';
            const isHidden = currentDay && String(p.day_of_week) !== String(currentDay);
            return `<tr data-day="${p.day_of_week}" style="${isHidden ? 'display:none;' : ''}">
                <td><span class="vx-badge badge-primary font-bold">${daysMap[p.day_of_week]||p.day_of_week}</span></td>
                <td>${periodNum}</td>
                <td><strong style="color:var(--text-heading); font-size:0.9rem;">${p.name}</strong></td>
                <td><span class="cell-mono font-bold" style="color:var(--text-heading);"><i class='bx bx-time-five' style="color:var(--text-muted);font-size:0.85rem;"></i> ${start}</span></td>
                <td><span class="cell-mono font-bold" style="color:var(--text-heading);"><i class='bx bx-time-five' style="color:var(--text-muted);font-size:0.85rem;"></i> ${end}</span></td>
                <td><span class="vx-badge badge-muted" style="font-weight:600;">${p.duration_minutes || '—'} Menit</span></td>
                <td>${typeChip}</td>
                <td style="text-align:right;">
                    <div style="display:inline-flex; gap:0.3rem; justify-content:flex-end;">
                        <button class="btn btn-secondary btn-xs btn-icon-sm" onclick="openEditPeriodModal(${p.id}, ${p.day_of_week}, '${(p.name || '').replace(/'/g, "\\'")}', '${p.period_number || ''}', '${start}', '${end}', ${p.is_break ? 1 : 0})" title="Edit Jam Sesi">
                            <i class='bx bx-edit-alt'></i>
                        </button>
                        <button class="btn btn-danger-light btn-xs btn-icon-sm" onclick="deletePeriod(${p.id})" title="Hapus Sesi">
                            <i class='bx bx-trash'></i>
                        </button>
                    </div>
                </td>
            </tr>`;
        }).join('');
    }

    function openEditPeriodModal(id, day, name, periodNumber, startTime, endTime, isBreak) {
        document.getElementById('editPeriodId').value = id;
        document.getElementById('editPeriodDay').value = String(day);
        document.getElementById('editPeriodName').value = name || '';
        document.getElementById('editPeriodNumber').value = periodNumber || '';
        document.getElementById('editPeriodStart').value = startTime || '07:00';
        document.getElementById('editPeriodEnd').value = endTime || '07:45';
        document.getElementById('editPeriodIsBreak').value = isBreak ? '1' : '0';
        document.getElementById('editPeriodApplyAllDays').checked = false;
        openModal('modalEditPeriod');
    }

    async function submitEditPeriod(e) {
        e.preventDefault();
        const id = document.getElementById('editPeriodId').value;
        const payload = {
            day_of_week: parseInt(document.getElementById('editPeriodDay').value, 10),
            name: document.getElementById('editPeriodName').value,
            period_number: document.getElementById('editPeriodNumber').value ? parseInt(document.getElementById('editPeriodNumber').value, 10) : null,
            start_time: document.getElementById('editPeriodStart').value,
            end_time: document.getElementById('editPeriodEnd').value,
            is_break: document.getElementById('editPeriodIsBreak').value === '1',
            apply_all_days: document.getElementById('editPeriodApplyAllDays').checked
        };
        try {
            const res = await fetch(`/api/v1/periods/${id}`, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN },
                body: JSON.stringify(payload)
            });
            const result = await res.json();
            if (res.ok && result.success) {
                vxShowToast(result.message, 'success');
                closeModal('modalEditPeriod');
                selectCategory(currentSelectedCategoryId);
            } else {
                vxShowToast(result.message || 'Gagal memperbarui jam pelajaran', 'error');
            }
        } catch (err) { vxShowToast(err.message, 'error'); }
    }

    async function activateAcademicYear(id) {
        if (!confirm('Aktifkan tahun ajaran ini sebagai periode aktif utama?')) return;
        try {
            const res = await fetch(`/api/v1/academic-years/${id}/activate`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN }
            });
            const result = await res.json();
            if (res.ok && result.success) {
                vxShowToast(result.message, 'success');
                setTimeout(() => location.reload(), 700);
            } else { vxShowToast(result.message || 'Gagal mengubah status', 'error'); }
        } catch (err) { vxShowToast(err.message, 'error'); }
    }

    async function activateScheduleCategory(id) {
        try {
            const res = await fetch(`/api/v1/time-categories/${id}/activate`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN }
            });
            const result = await res.json();
            if (res.ok && result.success) {
                vxShowToast(result.message, 'success');
                setTimeout(() => location.reload(), 700);
            } else { vxShowToast(result.message || 'Gagal mengubah pola jadwal', 'error'); }
        } catch (err) { vxShowToast(err.message, 'error'); }
    }

    function filterTeachers() {
        const search = document.getElementById('searchTeacherInput').value.toLowerCase();
        const ptk = document.getElementById('filterPtkSelect').value;
        document.querySelectorAll('#teachersTable tbody tr').forEach(r => {
            const name = r.getAttribute('data-name') || '';
            const nip = r.getAttribute('data-nip') || '';
            const nuptk = r.getAttribute('data-nuptk') || '';
            const rPtk = r.getAttribute('data-ptk') || '';
            const matchSearch = !search || name.includes(search) || nip.includes(search) || nuptk.includes(search);
            const matchPtk = !ptk || rPtk === ptk;
            r.style.display = (matchSearch && matchPtk) ? '' : 'none';
        });
    }

    function togglePreviousSchoolField(val) {
        const row = document.getElementById('rowPreviousSchool');
        const input = document.getElementById('inputPreviousSchool');
        if (val === 'pindahan') {
            row.style.display = '';
            input.setAttribute('required', 'required');
        } else {
            row.style.display = 'none';
            input.removeAttribute('required');
            input.value = '';
        }
    }

    function filterStudents() {
        const search = (document.getElementById('searchStudentInput')?.value || '').toLowerCase();
        const cls = document.getElementById('filterStudentClass')?.value || '';
        const cohort = document.getElementById('filterStudentCohort')?.value || '';
        const status = document.getElementById('filterStudentStatus')?.value || 'active';

        document.querySelectorAll('#studentsTable tbody tr').forEach(r => {
            const name = r.getAttribute('data-name') || '';
            const nis = r.getAttribute('data-nis') || '';
            const nisn = r.getAttribute('data-nisn') || '';
            const nik = r.getAttribute('data-nik') || '';
            const rClass = r.getAttribute('data-class') || '';
            const rCohort = r.getAttribute('data-cohort') || '';
            const rStatus = r.getAttribute('data-status') || '';

            const matchSearch = !search || name.includes(search) || nis.includes(search) || nisn.includes(search) || nik.includes(search);
            const matchClass = !cls || rClass === cls;
            const matchCohort = !cohort || rCohort === cohort;
            const matchStatus = (status === 'all') || (rStatus === status);

            r.style.display = (matchSearch && matchClass && matchCohort && matchStatus) ? '' : 'none';
        });
    }

    async function viewStudentHistory(studentId, studentName) {
        openModal('modalStudentHistory');
        document.getElementById('historyModalStudentName').innerText = 'Riwayat Rombel: ' + studentName;
        const metaEl = document.getElementById('historyStudentMeta');
        const tbody = document.getElementById('studentHistoryTableBody');
        metaEl.innerHTML = '<span style="color:var(--vx-text-muted);">Memuat data peserta didik...</span>';
        tbody.innerHTML = '<tr><td colspan="5" style="text-align: center; padding: 2rem; color: var(--vx-text-muted);">Memuat riwayat...</td></tr>';

        try {
            const res = await fetch(`/api/v1/students/${studentId}/class-histories`, { credentials: 'same-origin' });
            const json = await res.json();
            if (!res.ok || !json.success) throw new Error(json.message || 'Gagal memuat riwayat rombel');

            const s = json.data.student;
            const histories = json.data.histories || [];

            metaEl.innerHTML = `
                <div style="display:flex; flex-wrap:wrap; gap:1.25rem;">
                    <div><strong>NIS:</strong> ${s.nis || '—'} &bull; <strong>NISN:</strong> ${s.nisn || '—'}</div>
                    <div><strong>Angkatan:</strong> <span class="vx-badge badge-primary">${s.entry_academic_year ? s.entry_academic_year.name : 'Belum Ditentukan'}</span></div>
                    <div><strong>Jenis Masuk:</strong> ${s.registration_type ? s.registration_type.toUpperCase() : 'BARU'}</div>
                    <div><strong>Status Terkini:</strong> <span class="vx-badge ${s.status === 'active' ? 'badge-success' : 'badge-danger'}">${s.status.toUpperCase()}</span></div>
                </div>
            `;

            if (histories.length === 0) {
                tbody.innerHTML = '<tr><td colspan="5" style="text-align: center; padding: 2rem; color: var(--vx-text-muted);">Belum ada catatan riwayat rombel.</td></tr>';
                return;
            }

            tbody.innerHTML = histories.map(h => {
                const yearName = h.academic_year ? h.academic_year.name : '—';
                const className = h.school_class ? h.school_class.name : '(Tidak Ada Rombel)';
                const grade = h.grade_level ? 'Tingkat ' + h.grade_level : '—';
                let badgeClass = 'badge-muted';
                if (h.status === 'active') badgeClass = 'badge-primary';
                else if (h.status === 'promoted' || h.status === 'graduated') badgeClass = 'badge-success';
                else if (h.status === 'retained') badgeClass = 'badge-warning';
                else if (h.status === 'transferred' || h.status === 'dropout') badgeClass = 'badge-danger';

                return `
                    <tr>
                        <td class="cell-title fw-600">${yearName}</td>
                        <td>${grade}</td>
                        <td><span class="vx-badge badge-dark">${className}</span></td>
                        <td><span class="vx-badge ${badgeClass}">${h.status_label || h.status}</span></td>
                        <td class="cell-sub">${h.notes || '—'}</td>
                    </tr>
                `;
            }).join('');
        } catch (err) {
            tbody.innerHTML = `<tr><td colspan="5" style="text-align: center; padding: 2rem; color: var(--vx-danger);">${err.message}</td></tr>`;
        }
    }

    let currentCandidates = [];

    async function loadPromotionCandidates() {
        const sourceSelect = document.getElementById('promoSourceClass');
        const classId = sourceSelect.value;
        const targetYearId = document.getElementById('promoTargetYear').value;
        const tbody = document.getElementById('promoCandidatesBody');
        const submitBtn = document.getElementById('btnSubmitPromo');

        if (!classId) {
            tbody.innerHTML = '<tr><td colspan="4" style="text-align: center; padding: 2rem; color: var(--vx-text-muted);">Pilih Rombongan Belajar Asal terlebih dahulu.</td></tr>';
            submitBtn.disabled = true;
            return;
        }

        const selectedOpt = sourceSelect.options[sourceSelect.selectedIndex];
        const currentGrade = parseInt(selectedOpt.getAttribute('data-grade') || 10, 10);
        const nextGrade = currentGrade + 1;

        tbody.innerHTML = '<tr><td colspan="4" style="text-align: center; padding: 2rem; color: var(--vx-text-muted);">Memuat daftar siswa...</td></tr>';
        submitBtn.disabled = true;

        try {
            const res = await fetch(`/api/v1/promotions/candidates?school_id=${SCHOOL_ID}&class_id=${classId}`, { credentials: 'same-origin' });
            const json = await res.json();
            if (!res.ok || !json.success) throw new Error(json.message || 'Gagal memuat kandidat');

            currentCandidates = json.data || [];
            if (currentCandidates.length === 0) {
                tbody.innerHTML = '<tr><td colspan="4" style="text-align: center; padding: 2rem; color: var(--vx-text-muted);">Tidak ada siswa aktif terdaftar di rombel ini.</td></tr>';
                return;
            }

            // Opsi kelas untuk Naik Kelas (grade > current) dan Tinggal Kelas (grade == current)
            const higherClasses = ALL_CLASSES.filter(c => c.grade_level > currentGrade);
            const sameClasses = ALL_CLASSES.filter(c => c.grade_level === currentGrade && c.id != classId);

            tbody.innerHTML = currentCandidates.map(st => {
                return `
                    <tr id="promoRow_${st.id}" data-student-id="${st.id}">
                        <td>
                            <div class="cell-title fw-600">${st.full_name}</div>
                            <div class="cell-sub cell-mono">NISN: ${st.nisn || '—'}</div>
                        </td>
                        <td class="cell-mono">${st.nis}</td>
                        <td>
                            <select class="vx-input form-input-clean promo-action-select" style="padding: 4px 8px; font-size: 0.8rem;" onchange="handlePromoActionChange(${st.id}, ${currentGrade}, this.value)">
                                <option value="promote" selected>Naik Kelas</option>
                                <option value="retain">Tinggal Kelas</option>
                                <option value="graduate">Lulus (Alumni)</option>
                                <option value="transfer">Pindah Sekolah</option>
                                <option value="dropout">Putus Sekolah</option>
                            </select>
                        </td>
                        <td>
                            <select class="vx-input form-input-clean promo-target-select" id="promoTargetSelect_${st.id}" style="padding: 4px 8px; font-size: 0.8rem;">
                                ${higherClasses.length > 0 ? higherClasses.map(c => `<option value="${c.id}">${c.name} (Tingkat ${c.grade_level})</option>`).join('') : '<option value="">(Tidak ada kelas lanjutan)</option>'}
                            </select>
                        </td>
                    </tr>
                `;
            }).join('');

            submitBtn.disabled = false;
        } catch (err) {
            tbody.innerHTML = `<tr><td colspan="4" style="text-align: center; padding: 2rem; color: var(--vx-danger);">${err.message}</td></tr>`;
        }
    }

    function handlePromoActionChange(studentId, currentGrade, action) {
        const targetSelect = document.getElementById(`promoTargetSelect_${studentId}`);
        if (!targetSelect) return;

        if (action === 'promote') {
            targetSelect.disabled = false;
            const higher = ALL_CLASSES.filter(c => c.grade_level > currentGrade);
            targetSelect.innerHTML = higher.length > 0
                ? higher.map(c => `<option value="${c.id}">${c.name} (Tingkat ${c.grade_level})</option>`).join('')
                : '<option value="">(Belum ada rombel tingkat berikutnya)</option>';
        } else if (action === 'retain') {
            targetSelect.disabled = false;
            const same = ALL_CLASSES.filter(c => c.grade_level === currentGrade);
            targetSelect.innerHTML = same.map(c => `<option value="${c.id}">${c.name} (Tingkat ${c.grade_level})</option>`).join('');
        } else {
            // graduate, transfer, dropout
            targetSelect.disabled = true;
            targetSelect.innerHTML = `<option value="">— Tidak memerlukan rombel (${action}) —</option>`;
        }
    }

    async function submitPromotion(e) {
        e.preventDefault();
        const targetYearId = document.getElementById('promoTargetYear').value;
        const rows = document.querySelectorAll('#promoCandidatesBody tr[data-student-id]');

        if (rows.length === 0) {
            vxShowToast('Tidak ada data siswa yang diproses', 'error');
            return;
        }

        const entries = [];
        for (const row of rows) {
            const studentId = parseInt(row.getAttribute('data-student-id'), 10);
            const action = row.querySelector('.promo-action-select').value;
            const targetClassVal = row.querySelector('.promo-target-select')?.value;

            if ((action === 'promote' || action === 'retain') && !targetClassVal) {
                vxShowToast('Mohon pilih rombel tujuan untuk semua siswa yang naik/tinggal kelas', 'error');
                return;
            }

            entries.push({
                student_id: studentId,
                action: action,
                target_class_id: (action === 'promote' || action === 'retain') ? parseInt(targetClassVal, 10) : null
            });
        }

        const submitBtn = document.getElementById('btnSubmitPromo');
        submitBtn.disabled = true;
        submitBtn.innerHTML = "<i class='bx bx-loader-alt bx-spin'></i> Memproses...";

        try {
            const res = await fetch('/api/v1/promotions', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN
                },
                body: JSON.stringify({
                    school_id: SCHOOL_ID,
                    target_academic_year_id: parseInt(targetYearId, 10),
                    entries: entries
                })
            });

            const result = await res.json();
            if (res.ok && result.success) {
                vxShowToast(result.message || 'Kenaikan kelas berhasil diproses!', 'success');
                closeModal('modalPromotion');
                setTimeout(() => location.reload(), 900);
            } else {
                vxShowToast(result.message || 'Gagal memproses kenaikan kelas', 'error');
                submitBtn.disabled = false;
                submitBtn.innerHTML = "<i class='bx bx-check-double'></i> Terapkan Kenaikan Kelas";
            }
        } catch (err) {
            vxShowToast(err.message, 'error');
            submitBtn.disabled = false;
            submitBtn.innerHTML = "<i class='bx bx-check-double'></i> Terapkan Kenaikan Kelas";
        }
    }

    async function submitAddStudent(e) {
        e.preventDefault();
        const form = e.target;
        const payload = Object.fromEntries(new FormData(form).entries());
        payload.school_id = SCHOOL_ID;
        try {
            const res = await fetch('/api/v1/students', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN },
                body: JSON.stringify(payload)
            });
            const result = await res.json();
            if (res.ok && result.success) {
                vxShowToast('Siswa berhasil ditambahkan', 'success');
                closeModal('modalAddStudent');
                form.reset();
                setTimeout(() => location.reload(), 700);
            } else { vxShowToast(result.message || 'Validasi gagal', 'error'); }
        } catch (err) { vxShowToast(err.message, 'error'); }
    }

    async function submitAddTeacher(e) {
        e.preventDefault();
        const form = e.target;
        const payload = Object.fromEntries(new FormData(form).entries());
        payload.school_id = SCHOOL_ID;
        try {
            const res = await fetch('/api/v1/teachers', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN },
                body: JSON.stringify(payload)
            });
            const result = await res.json();
            if (res.ok && result.success) {
                vxShowToast('Data GTK berhasil disimpan', 'success');
                closeModal('modalAddTeacher');
                form.reset();
                setTimeout(() => location.reload(), 700);
            } else { vxShowToast(result.message || 'Validasi gagal', 'error'); }
        } catch (err) { vxShowToast(err.message, 'error'); }
    }

    async function submitAddYear(e) {
        e.preventDefault();
        const form = e.target;
        const payload = Object.fromEntries(new FormData(form).entries());
        payload.school_id = SCHOOL_ID;
        payload.is_active = false;
        try {
            const res = await fetch('/api/v1/academic-years', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN },
                body: JSON.stringify(payload)
            });
            const result = await res.json();
            if (res.ok && result.success) {
                vxShowToast('Tahun ajaran berhasil dibuat', 'success');
                closeModal('modalAddYear');
                form.reset();
                setTimeout(() => location.reload(), 700);
            } else { vxShowToast(result.message || 'Validasi gagal', 'error'); }
        } catch (err) { vxShowToast(err.message, 'error'); }
    }

    async function deletePeriod(id) {
        if (!confirm('Hapus sesi jam ini?')) return;
        try {
            const res = await fetch(`/api/v1/periods/${id}`, {
                method: 'DELETE',
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN }
            });
            const result = await res.json();
            if (res.ok && result.success) {
                vxShowToast('Sesi jam berhasil dihapus', 'success');
                selectCategory(currentSelectedCategoryId);
            }
        } catch (err) { vxShowToast(err.message, 'error'); }
    }

    async function deleteSchedule(id) {
        if (!confirm('Hapus jadwal pelajaran ini?')) return;
        try {
            const res = await fetch(`/api/v1/schedules/${id}`, {
                method: 'DELETE',
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN }
            });
            const result = await res.json();
            if (res.ok && result.success) {
                vxShowToast('Jadwal berhasil dihapus', 'success');
                setTimeout(() => location.reload(), 700);
            }
        } catch (err) { vxShowToast(err.message, 'error'); }
    }

    // --- KURIKULUM & PEMETAAN MULTI-JENJANG HANDLERS ---
    let activeCurriculumTabId = {{ $curriculums->first()?->id ?? 1 }};

    function switchCurriculumTab(curId) {
        activeCurriculumTabId = curId;

        // 1. Update button tab di panel bawah
        document.querySelectorAll('.cur-tab-btn').forEach(b => {
            b.classList.remove('btn-primary');
            b.classList.add('btn-secondary');
        });
        const activeBtn = document.getElementById('curTabBtn-' + curId);
        if (activeBtn) {
            activeBtn.classList.remove('btn-secondary');
            activeBtn.classList.add('btn-primary');
        }

        // 2. Tampilkan pane kurikulum yang sesuai di panel bawah
        document.querySelectorAll('.cur-structure-pane').forEach(p => p.style.display = 'none');
        const activePane = document.getElementById('curPane-' + curId);
        if (activePane) activePane.style.display = 'block';

        // 3. Highlight baris kurikulum di tabel master
        document.querySelectorAll('.curriculum-table-row').forEach(r => r.classList.remove('active-cur-row'));
        const activeRow = document.getElementById('curTableRow-' + curId);
        if (activeRow) activeRow.classList.add('active-cur-row');
    }

    function openCurriculumStructureDetailModal(curId) {
        switchModalCurriculumPane(curId);
        openModal('modalCurriculumStructureDetail');
    }

    function switchModalCurriculumPane(curId, curName = null, curCode = null) {
        activeCurriculumTabId = curId;

        // Update tabs di dalam modal
        document.querySelectorAll('.modal-cur-tab-btn').forEach(b => {
            b.classList.remove('btn-primary');
            b.classList.add('btn-secondary');
        });
        const activeModalBtn = document.getElementById('modalCurTabBtn-' + curId);
        if (activeModalBtn) {
            activeModalBtn.classList.remove('btn-secondary');
            activeModalBtn.classList.add('btn-primary');
        }

        // Tampilkan pane aktif di dalam modal
        document.querySelectorAll('.modal-cur-pane').forEach(p => p.style.display = 'none');
        const activeModalPane = document.getElementById('modalCurPane-' + curId);
        if (activeModalPane) activeModalPane.style.display = 'block';

        // Update title modal jika ada nama
        if (curName) {
            const titleEl = document.getElementById('modalCurStructureTitle');
            if (titleEl) {
                titleEl.innerHTML = `<span class="modal-title-icon"><i class='bx bx-book-content'></i></span> Struktur Mapel: ${curName} ${curCode ? '<span class="cell-mono font-bold" style="color: var(--bento-primary); font-size: 0.9rem;">('+curCode+')</span>' : ''}`;
            }
        }

        // Sinkronkan juga tab di halaman utama
        switchCurriculumTab(curId);
    }

    function viewCurriculumInPanelFromModal() {
        closeModal('modalCurriculumStructureDetail');
        const panel = document.getElementById('curriculumStructurePanel');
        if (panel) {
            panel.scrollIntoView({ behavior: 'smooth', block: 'start' });
            panel.classList.remove('panel-pulse-highlight');
            void panel.offsetWidth; // force reflow
            panel.classList.add('panel-pulse-highlight');
        }
    }

    function selectCurriculumStructure(curId) {
        openCurriculumStructureDetailModal(curId);
    }

    function toggleGradeView(mode) {
        const bento = document.getElementById('curriculumGradeBentoView');
        const table = document.getElementById('curriculumGradeTableView');
        const btnBento = document.getElementById('btnGradeViewBento');
        const btnTable = document.getElementById('btnGradeViewTable');

        if (mode === 'table') {
            if (bento) bento.style.display = 'none';
            if (table) table.style.display = 'block';
            if (btnBento) {
                btnBento.style.background = 'transparent';
                btnBento.style.color = 'var(--vx-text-muted)';
                btnBento.style.boxShadow = 'none';
            }
            if (btnTable) {
                btnTable.style.background = 'var(--vx-card-bg)';
                btnTable.style.color = 'var(--vx-primary)';
                btnTable.style.boxShadow = '0 1px 4px rgba(0,0,0,0.06)';
            }
        } else {
            if (bento) bento.style.display = 'grid';
            if (table) table.style.display = 'none';
            if (btnBento) {
                btnBento.style.background = 'var(--vx-card-bg)';
                btnBento.style.color = 'var(--vx-primary)';
                btnBento.style.boxShadow = '0 1px 4px rgba(0,0,0,0.06)';
            }
            if (btnTable) {
                btnTable.style.background = 'transparent';
                btnTable.style.color = 'var(--vx-text-muted)';
                btnTable.style.boxShadow = 'none';
            }
        }
    }

    function openAssignGradeModal(gradeLevel, currentCurId, phase) {
        if (gradeLevel) document.getElementById('assignGradeLevelSelect').value = gradeLevel;
        if (currentCurId && currentCurId !== 'null') document.getElementById('assignGradeCurriculumSelect').value = currentCurId;
        if (phase) document.getElementById('assignGradePhaseInput').value = phase;
        else document.getElementById('assignGradePhaseInput').value = '';
        openModal('modalAssignGradeCurriculum');
    }

    async function submitAssignGrade(e) {
        e.preventDefault();
        const form = e.target;
        const payload = Object.fromEntries(new FormData(form).entries());
        payload.school_id = SCHOOL_ID;
        payload.grade_level = parseInt(payload.grade_level);
        payload.curriculum_id = parseInt(payload.curriculum_id);

        try {
            const res = await fetch('/api/v1/curriculums/assign-grade', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN },
                body: JSON.stringify(payload)
            });
            const result = await res.json();
            if (res.ok && result.success) {
                vxShowToast(result.message, 'success');
                closeModal('modalAssignGradeCurriculum');
                setTimeout(() => location.reload(), 700);
            } else {
                vxShowToast(result.message || 'Gagal menetapkan kurikulum jenjang', 'error');
            }
        } catch (err) { vxShowToast(err.message, 'error'); }
    }

    async function submitAddCurriculum(e) {
        e.preventDefault();
        const form = e.target;
        const payload = Object.fromEntries(new FormData(form).entries());
        payload.school_id = SCHOOL_ID;

        try {
            const res = await fetch('/api/v1/curriculums', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN },
                body: JSON.stringify(payload)
            });
            const result = await res.json();
            if (res.ok && result.success) {
                vxShowToast('Kurikulum baru berhasil didaftarkan', 'success');
                closeModal('modalAddCurriculum');
                form.reset();
                setTimeout(() => location.reload(), 700);
            } else {
                vxShowToast(result.message || 'Validasi gagal', 'error');
            }
        } catch (err) { vxShowToast(err.message, 'error'); }
    }

    // --- MASTER KELOMPOK MATA PELAJARAN PER KURIKULUM ---
    let CURRICULUM_DATA = @json($curriculums);
    let currentManagingCurriculumId = {{ $curriculums->first()?->id ?? 'null' }};

    function updateSubjectGroupDropdown(curId, selectedGroupId = null, selectedGroupCode = null) {
        const select = document.getElementById('curSubGroupSelect');
        const legacyInput = document.getElementById('curSubLegacyGroupInput');
        if (!select) return;

        select.innerHTML = '';
        const cur = CURRICULUM_DATA.find(c => c.id == curId);
        const groups = (cur && cur.subject_groups) ? cur.subject_groups : [];

        if (groups.length > 0) {
            groups.forEach(g => {
                const opt = document.createElement('option');
                opt.value = g.id;
                opt.textContent = `${g.name} (${g.code})`;
                opt.dataset.code = g.code;
                if (selectedGroupId && g.id == selectedGroupId) {
                    opt.selected = true;
                } else if (!selectedGroupId && selectedGroupCode && g.code === selectedGroupCode) {
                    opt.selected = true;
                }
                select.appendChild(opt);
            });
            if (legacyInput && select.selectedOptions[0]) {
                legacyInput.value = select.selectedOptions[0].dataset.code || '';
            }
        } else {
            // Fallback default jika kurikulum belum memiliki kelompok dinamis
            const fallbacks = [
                { id: '', code: 'WAJIB_UMUM', name: 'Wajib Umum (Nasional)' },
                { id: '', code: 'PEMINATAN_PILIHAN', name: 'Peminatan / Pilihan' },
                { id: '', code: 'P5_PROJEK', name: 'Projek Profil Pelajar Pancasila (P5)' },
                { id: '', code: 'MULOK', name: 'Muatan Lokal (Mulok)' },
                { id: '', code: 'KEAGAMAAN', name: 'Keagamaan / Kepesantrenan' },
            ];
            fallbacks.forEach(f => {
                const opt = document.createElement('option');
                opt.value = f.id;
                opt.textContent = f.name;
                opt.dataset.code = f.code;
                if (selectedGroupCode && f.code === selectedGroupCode) opt.selected = true;
                select.appendChild(opt);
            });
            if (legacyInput) legacyInput.value = selectedGroupCode || 'WAJIB_UMUM';
        }

        select.onchange = function() {
            if (legacyInput && select.selectedOptions[0]) {
                legacyInput.value = select.selectedOptions[0].dataset.code || '';
            }
        };
    }

    function onCurriculumSelectChange(curId) {
        updateSubjectGroupDropdown(curId);
    }

    function openAddCurriculumSubjectModal(curId) {
        const targetCurId = curId || activeCurriculumTabId;
        const curSelect = document.getElementById('curSubTargetCurriculumId');
        if (curSelect) curSelect.value = targetCurId;
        updateSubjectGroupDropdown(targetCurId);
        openModal('modalAddCurriculumSubject');
    }

    function openEditCurriculumSubjectModal(curId, subjectId, groupId, groupCode, jp, kkm) {
        const curSelect = document.getElementById('curSubTargetCurriculumId');
        if (curSelect) curSelect.value = curId;
        document.getElementById('curSubSubjectSelect').value = subjectId;
        updateSubjectGroupDropdown(curId, groupId, groupCode);
        document.getElementById('curSubJpInput').value = jp;
        document.getElementById('curSubKkmInput').value = kkm;
        openModal('modalAddCurriculumSubject');
    }

    function openManageSubjectGroupsModalFromAddModal() {
        const targetCurId = document.getElementById('curSubTargetCurriculumId').value;
        closeModal('modalAddCurriculumSubject');
        openManageSubjectGroupsModal(targetCurId);
    }

    function openManageSubjectGroupsModal(curId, curName = null, curCode = null) {
        currentManagingCurriculumId = curId;
        document.getElementById('sgCurriculumId').value = curId;

        const cur = CURRICULUM_DATA.find(c => c.id == curId);
        const displayName = curName || (cur ? cur.name : 'Kurikulum');
        const displayCode = curCode || (cur ? cur.code : '');

        const titleEl = document.getElementById('manageSubjectGroupsCurTitle');
        if (titleEl) {
            titleEl.innerHTML = `Kurikulum: <strong>${displayName}</strong> ${displayCode ? '<span class="cell-mono text-primary font-bold">('+displayCode+')</span>' : ''} &bull; Kelola kelompok mata pelajaran kurikulum ini.`;
        }

        toggleSubjectGroupForm(false);
        loadSubjectGroups(curId);
        openModal('modalManageSubjectGroups');
    }

    async function loadSubjectGroups(curId) {
        const tbody = document.getElementById('subjectGroupsTableBody');
        tbody.innerHTML = `<tr><td colspan="6" style="text-align: center; padding: 2rem; color: var(--vx-text-muted);"><i class='bx bx-loader-alt bx-spin' style="font-size: 1.5rem;"></i><div style="margin-top: 0.35rem;">Memuat daftar kelompok...</div></td></tr>`;

        try {
            const res = await fetch(`/api/v1/curriculums/${curId}/subject-groups`, {
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN }
            });
            const result = await res.json();
            if (res.ok && result.success) {
                renderSubjectGroupsTable(result.data);
                // Update cache lokal
                const targetCur = CURRICULUM_DATA.find(c => c.id == curId);
                if (targetCur) targetCur.subject_groups = result.data;
            } else {
                tbody.innerHTML = `<tr><td colspan="6" style="text-align: center; padding: 2rem; color: var(--vx-danger);">${result.message || 'Gagal memuat kelompok mapel.'}</td></tr>`;
            }
        } catch (err) {
            tbody.innerHTML = `<tr><td colspan="6" style="text-align: center; padding: 2rem; color: var(--vx-danger);">Gagal menghubungi server.</td></tr>`;
        }
    }

    function renderSubjectGroupsTable(groups) {
        const tbody = document.getElementById('subjectGroupsTableBody');
        if (!groups || groups.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="6" style="text-align: center; padding: 2.5rem 1rem; color: var(--vx-text-muted);">
                        <i class='bx bx-category' style="font-size: 2rem; opacity: 0.5; display: block; margin-bottom: 0.5rem;"></i>
                        <strong>Belum ada kelompok mapel pada kurikulum ini</strong>
                        <p style="font-size: 0.8rem; margin-top: 0.25rem;">Klik tombol "+ Tambah Kelompok Baru" atau pilih salah satu "Muat Template Standar" di atas.</p>
                    </td>
                </tr>
            `;
            return;
        }

        let html = '';
        groups.forEach((g, idx) => {
            const safeGroupJson = JSON.stringify(g).replace(/"/g, '&quot;');
            html += `
                <tr>
                    <td style="text-align: center; font-weight: 700; color: var(--vx-text-muted);" class="cell-mono">
                        ${g.order_no ?? (idx + 1)}
                    </td>
                    <td>
                        <span class="cell-mono font-bold" style="color: var(--bento-primary); font-size: 0.85rem;">
                            ${g.code}
                        </span>
                    </td>
                    <td>
                        <div class="cell-title" style="font-size: 0.86rem;">${g.name}</div>
                        ${g.description ? `<div class="cell-sub" style="font-size: 0.74rem;">${g.description}</div>` : ''}
                    </td>
                    <td style="text-align: center;">
                        <span class="vx-badge ${g.color || 'badge-primary'}" style="font-size: 0.74rem;">
                            ${g.name}
                        </span>
                    </td>
                    <td style="text-align: center;">
                        <span class="vx-badge badge-dark cell-mono" style="font-size: 0.78rem;">
                            ${g.curriculum_subjects_count ?? 0} Mapel
                        </span>
                    </td>
                    <td style="text-align: right;">
                        <div class="d-flex gap-1 justify-end">
                            <button type="button" class="btn btn-secondary btn-xs btn-icon-sm" onclick="editSubjectGroup(${safeGroupJson})" title="Ubah Kelompok">
                                <i class='bx bx-edit'></i>
                            </button>
                            <button type="button" class="btn btn-danger-light btn-xs btn-icon-sm" onclick="deleteSubjectGroup(${g.id}, '${g.name.replace(/'/g, "\\'")}')" title="Hapus Kelompok">
                                <i class='bx bx-trash'></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        });
        tbody.innerHTML = html;
    }

    function toggleSubjectGroupForm(show, groupData = null) {
        const formDiv = document.getElementById('collapseSubjectGroupForm');
        const heading = document.getElementById('subjectGroupFormHeading');
        const form = document.getElementById('formSubjectGroup');

        if (!show) {
            formDiv.style.display = 'none';
            form.reset();
            document.getElementById('sgGroupId').value = '';
            return;
        }

        formDiv.style.display = 'block';
        if (groupData) {
            heading.innerHTML = `<i class='bx bx-edit-alt'></i> Ubah Kelompok: <strong>${groupData.name}</strong>`;
            document.getElementById('sgGroupId').value = groupData.id;
            document.getElementById('sgCodeInput').value = groupData.code;
            document.getElementById('sgNameInput').value = groupData.name;
            document.getElementById('sgOrderInput').value = groupData.order_no || 1;
            document.getElementById('sgColorSelect').value = groupData.color || 'badge-primary';
            document.getElementById('sgDescInput').value = groupData.description || '';
        } else {
            heading.innerHTML = `<i class='bx bx-plus-circle'></i> Tambah Kelompok Mapel Baru`;
            form.reset();
            document.getElementById('sgCurriculumId').value = currentManagingCurriculumId;
            document.getElementById('sgGroupId').value = '';
            document.getElementById('sgColorSelect').value = 'badge-primary';
            document.getElementById('sgCodeInput').focus();
        }
        formDiv.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    function editSubjectGroup(groupData) {
        toggleSubjectGroupForm(true, groupData);
    }

    async function submitSaveSubjectGroup(e) {
        e.preventDefault();
        const form = e.target;
        const curId = currentManagingCurriculumId;
        const groupId = document.getElementById('sgGroupId').value;
        const payload = {
            code: document.getElementById('sgCodeInput').value.trim().toUpperCase(),
            name: document.getElementById('sgNameInput').value.trim(),
            order_no: parseInt(document.getElementById('sgOrderInput').value) || 1,
            color: document.getElementById('sgColorSelect').value,
            description: document.getElementById('sgDescInput').value.trim() || null,
        };

        const isEdit = !!groupId;
        const url = isEdit 
            ? `/api/v1/curriculums/${curId}/subject-groups/${groupId}` 
            : `/api/v1/curriculums/${curId}/subject-groups`;
        const method = isEdit ? 'PUT' : 'POST';

        const btn = document.getElementById('btnSaveSubjectGroup');
        const origText = btn.innerHTML;
        btn.innerHTML = `<i class='bx bx-loader-alt bx-spin'></i> Menyimpan...`;
        btn.disabled = true;

        try {
            const res = await fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN
                },
                body: JSON.stringify(payload)
            });
            const result = await res.json();
            if (res.ok && result.success) {
                vxShowToast(result.message || 'Kelompok mapel berhasil disimpan', 'success');
                toggleSubjectGroupForm(false);
                loadSubjectGroups(curId);
                updateSubjectGroupDropdown(curId);
            } else {
                vxShowToast(result.message || 'Gagal menyimpan kelompok', 'error');
            }
        } catch (err) {
            vxShowToast(err.message, 'error');
        } finally {
            btn.innerHTML = origText;
            btn.disabled = false;
        }
    }

    async function deleteSubjectGroup(groupId, groupName) {
        if (!confirm(`Hapus kelompok mata pelajaran "${groupName}"?`)) return;
        const curId = currentManagingCurriculumId;

        try {
            const res = await fetch(`/api/v1/curriculums/${curId}/subject-groups/${groupId}`, {
                method: 'DELETE',
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN }
            });
            const result = await res.json();
            if (res.ok && result.success) {
                vxShowToast(result.message || 'Kelompok berhasil dihapus', 'success');
                loadSubjectGroups(curId);
                updateSubjectGroupDropdown(curId);
            } else {
                vxShowToast(result.message || 'Gagal menghapus kelompok', 'error');
            }
        } catch (err) {
            vxShowToast(err.message, 'error');
        }
    }

    async function applySubjectGroupTemplate(templateKey) {
        const labels = {
            'merdeka': 'Kurikulum Merdeka (Umum, Pilihan, P5, Mulok)',
            'k13': 'Kurikulum 2013 (Kelompok A, B, C, Mulok)',
            'kemenag': 'Kemenag / Madrasah (PAI & Arab, Umum, Pilihan, P5RA, Mulok)',
            'pesantren': 'Pesantren / Diniyah (Diniyah, Tahfidz, Diknas, Bahasa, Mulok)'
        };
        const templateLabel = labels[templateKey] || templateKey;

        if (!confirm(`Terapkan template kelompok standar "${templateLabel}" untuk kurikulum ini? Kelompok yang belum ada akan otomatis ditambahkan.`)) {
            return;
        }

        const curId = currentManagingCurriculumId;
        try {
            const res = await fetch(`/api/v1/curriculums/${curId}/subject-groups/template`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN
                },
                body: JSON.stringify({ template: templateKey })
            });
            const result = await res.json();
            if (res.ok && result.success) {
                vxShowToast(result.message || 'Template berhasil diterapkan', 'success');
                loadSubjectGroups(curId);
                updateSubjectGroupDropdown(curId);
            } else {
                vxShowToast(result.message || 'Gagal menerapkan template', 'error');
            }
        } catch (err) {
            vxShowToast(err.message, 'error');
        }
    }

    async function submitSaveCurriculumSubject(e) {
        e.preventDefault();
        const form = e.target;
        const curId = document.getElementById('curSubTargetCurriculumId').value;
        const payload = Object.fromEntries(new FormData(form).entries());
        delete payload.curriculum_id;

        try {
            const res = await fetch(`/api/v1/curriculums/${curId}/subjects`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN },
                body: JSON.stringify(payload)
            });
            const result = await res.json();
            if (res.ok && result.success) {
                vxShowToast(result.message || 'Alokasi mapel berhasil disimpan', 'success');
                closeModal('modalAddCurriculumSubject');
                setTimeout(() => location.reload(), 700);
            } else {
                vxShowToast(result.message || 'Gagal menyimpan alokasi mapel', 'error');
            }
        } catch (err) { vxShowToast(err.message, 'error'); }
    }

    async function removeSubjectFromCurriculum(currId, subjectId, name) {
        if (!confirm(`Keluarkan mata pelajaran '${name}' dari kurikulum ini?`)) return;
        try {
            const res = await fetch(`/api/v1/curriculums/${currId}/subjects/${subjectId}`, {
                method: 'DELETE',
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN }
            });
            const result = await res.json();
            if (res.ok && result.success) {
                vxShowToast(result.message || 'Mata pelajaran berhasil dikeluarkan dari kurikulum', 'success');
                setTimeout(() => location.reload(), 700);
            } else {
                vxShowToast(result.message || 'Gagal mengeluarkan mapel', 'error');
            }
        } catch (err) { vxShowToast(err.message, 'error'); }
    }

    async function deleteCurriculum(id, name) {
        if (!confirm(`Hapus kurikulum '${name}'? Pastikan tidak sedang digunakan oleh rombongan belajar aktif.`)) return;
        try {
            const res = await fetch(`/api/v1/curriculums/${id}`, {
                method: 'DELETE',
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN }
            });
            const result = await res.json();
            if (res.ok && result.success) {
                vxShowToast(result.message, 'success');
                setTimeout(() => location.reload(), 700);
            } else {
                vxShowToast(result.message || 'Gagal menghapus kurikulum', 'error');
            }
        } catch (err) { vxShowToast(err.message, 'error'); }
    }

    // --- SPEKTRUM KEAHLIAN SMK HANDLERS ---
    async function switchSchoolType(level) {
        const confirmMsg = level === 'SMK'
            ? 'Aktifkan mode SMK (SMK Negeri 1 Jakarta)? Spektrum Keahlian akan menjadi basis utama pemetaan kejuruan sekolah.'
            : 'Beralih ke mode SMA (SMA Negeri 1 Jakarta)?';
        if (!confirm(confirmMsg)) return;

        try {
            const res = await fetch('/api/v1/schools/switch-level', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN },
                body: JSON.stringify({ school_id: SCHOOL_ID, level: level })
            });
            const result = await res.json();
            if (res.ok && result.success) {
                vxShowToast(result.message, 'success');
                setTimeout(() => location.reload(), 700);
            } else {
                vxShowToast(result.message || 'Gagal mengubah status jenjang sekolah', 'error');
            }
        } catch (err) { vxShowToast(err.message, 'error'); }
    }

    function selectVocationalField(fieldId) {
        document.querySelectorAll('.voc-field-tab-btn').forEach(b => {
            b.classList.remove('btn-primary');
            b.classList.add('btn-secondary');
        });
        const activeBtn = document.getElementById('vocFieldTabBtn-' + fieldId);
        if (activeBtn) {
            activeBtn.classList.remove('btn-secondary');
            activeBtn.classList.add('btn-primary');
        }

        document.querySelectorAll('.voc-field-pane').forEach(p => p.style.display = 'none');
        const activePane = document.getElementById('vocFieldPane-' + fieldId);
        if (activePane) activePane.style.display = 'block';
    }

    function openAddConcentrationWithProgram(progId, progName) {
        document.getElementById('addConcProgramSelect').value = progId;
        openModal('modalAddVocationalConcentration');
    }

    function openEditConcentrationModal(id, name, dapodikCode, desc, partners, competencies) {
        document.getElementById('editConcId').value = id;
        document.getElementById('editConcName').value = name;
        document.getElementById('editConcDapodikCode').value = dapodikCode || '';
        document.getElementById('editConcDesc').value = desc || '';
        document.getElementById('editConcPartners').value = partners || '';
        document.getElementById('editConcCompetencies').value = competencies || '';
        openModal('modalEditVocationalConcentration');
    }

    async function submitAddVocationalField(e) {
        e.preventDefault();
        const form = e.target;
        const payload = Object.fromEntries(new FormData(form).entries());
        payload.school_id = SCHOOL_ID;

        try {
            const res = await fetch('/api/v1/vocational-fields', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN },
                body: JSON.stringify(payload)
            });
            const result = await res.json();
            if (res.ok && result.success) {
                vxShowToast(result.message, 'success');
                closeModal('modalAddVocationalField');
                setTimeout(() => location.reload(), 700);
            } else {
                vxShowToast(result.message || 'Gagal menambahkan bidang keahlian', 'error');
            }
        } catch (err) { vxShowToast(err.message, 'error'); }
    }

    async function submitAddVocationalProgram(e) {
        e.preventDefault();
        const form = e.target;
        const payload = Object.fromEntries(new FormData(form).entries());
        payload.school_id = SCHOOL_ID;
        payload.program_duration_years = parseInt(payload.program_duration_years);
        payload.vocational_field_id = parseInt(payload.vocational_field_id);

        try {
            const res = await fetch('/api/v1/vocational-programs', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN },
                body: JSON.stringify(payload)
            });
            const result = await res.json();
            if (res.ok && result.success) {
                vxShowToast(result.message, 'success');
                closeModal('modalAddVocationalProgram');
                setTimeout(() => location.reload(), 700);
            } else {
                vxShowToast(result.message || 'Gagal menambahkan program keahlian', 'error');
            }
        } catch (err) { vxShowToast(err.message, 'error'); }
    }

    async function submitAddVocationalConcentration(e) {
        e.preventDefault();
        const form = e.target;
        const payload = Object.fromEntries(new FormData(form).entries());
        payload.school_id = SCHOOL_ID;
        payload.vocational_program_id = parseInt(payload.vocational_program_id);

        try {
            const res = await fetch('/api/v1/vocational-concentrations', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN },
                body: JSON.stringify(payload)
            });
            const result = await res.json();
            if (res.ok && result.success) {
                vxShowToast(result.message, 'success');
                closeModal('modalAddVocationalConcentration');
                setTimeout(() => location.reload(), 700);
            } else {
                vxShowToast(result.message || 'Gagal menambahkan konsentrasi keahlian', 'error');
            }
        } catch (err) { vxShowToast(err.message, 'error'); }
    }

    async function submitEditVocationalConcentration(e) {
        e.preventDefault();
        const form = e.target;
        const id = document.getElementById('editConcId').value;
        const payload = Object.fromEntries(new FormData(form).entries());
        delete payload.id;

        try {
            const res = await fetch(`/api/v1/vocational-concentrations/${id}`, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN },
                body: JSON.stringify(payload)
            });
            const result = await res.json();
            if (res.ok && result.success) {
                vxShowToast(result.message, 'success');
                closeModal('modalEditVocationalConcentration');
                setTimeout(() => location.reload(), 700);
            } else {
                vxShowToast(result.message || 'Gagal memperbarui konsentrasi keahlian', 'error');
            }
        } catch (err) { vxShowToast(err.message, 'error'); }
    }

    async function deleteVocationalConcentration(id, name) {
        if (!confirm(`Hapus konsentrasi keahlian '${name}'? Pastikan tidak sedang digunakan rombongan belajar aktif.`)) return;
        try {
            const res = await fetch(`/api/v1/vocational-concentrations/${id}`, {
                method: 'DELETE',
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN }
            });
            const result = await res.json();
            if (res.ok && result.success) {
                vxShowToast(result.message, 'success');
                setTimeout(() => location.reload(), 700);
            } else {
                vxShowToast(result.message || 'Gagal menghapus konsentrasi keahlian', 'error');
            }
        } catch (err) { vxShowToast(err.message, 'error'); }
    }

    async function submitAddSchool(e) {
        e.preventDefault();
        const form = e.target;
        const payload = Object.fromEntries(new FormData(form).entries());

        if (payload.latitude) payload.latitude = parseFloat(payload.latitude);
        if (payload.longitude) payload.longitude = parseFloat(payload.longitude);

        try {
            const res = await fetch('/api/v1/schools', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN },
                body: JSON.stringify(payload)
            });
            const result = await res.json();
            if (res.ok && result.success) {
                vxShowToast(result.message || 'Satuan pendidikan berhasil didaftarkan!', 'success');
                closeModal('modalAddSchool');
                form.reset();
                setTimeout(() => location.reload(), 700);
            } else {
                let errMsg = result.message || 'Gagal mendaftarkan satuan pendidikan.';
                if (result.errors) {
                    const firstErr = Object.values(result.errors)[0];
                    if (firstErr) errMsg = Array.isArray(firstErr) ? firstErr[0] : firstErr;
                }
                vxShowToast(errMsg, 'error');
            }
        } catch (err) { vxShowToast(err.message, 'error'); }
    }

    // --- ROMBEL (KELAS) HANDLERS ---
    function filterClasses() {
        const search = (document.getElementById('searchClassInput')?.value || '').toLowerCase();
        const grade = document.getElementById('filterClassGrade')?.value || '';
        const conc = document.getElementById('filterClassConcentration')?.value || '';

        document.querySelectorAll('#classesTable tbody tr').forEach(r => {
            const name = r.getAttribute('data-name') || '';
            const rGrade = r.getAttribute('data-grade') || '';
            const rConc = r.getAttribute('data-conc') || '';

            const matchSearch = !search || name.includes(search);
            const matchGrade = !grade || rGrade === grade;
            const matchConc = !conc || rConc === conc;

            r.style.display = (matchSearch && matchGrade && matchConc) ? '' : 'none';
        });
    }

    function openEditClassModal(id, name, grade, yearId, curId, concId, progId, deptId, homeroomId, major) {
        document.getElementById('editClassId').value = id;
        document.getElementById('editClassName').value = name;
        document.getElementById('editClassGradeLevel').value = grade;
        if (document.getElementById('editClassAcademicYear')) document.getElementById('editClassAcademicYear').value = yearId || '';
        if (document.getElementById('editClassCurriculum')) document.getElementById('editClassCurriculum').value = curId || '';
        if (document.getElementById('editClassConcentration')) document.getElementById('editClassConcentration').value = concId || '';
        if (document.getElementById('editClassProgram')) document.getElementById('editClassProgram').value = progId || '';
        if (document.getElementById('editClassDepartment')) document.getElementById('editClassDepartment').value = deptId || '';
        if (document.getElementById('editClassHomeroom')) document.getElementById('editClassHomeroom').value = homeroomId || '';
        openModal('modalEditClass');
    }

    async function submitAddClass(e) {
        e.preventDefault();
        const form = e.target;
        const payload = Object.fromEntries(new FormData(form).entries());
        payload.school_id = SCHOOL_ID;
        payload.grade_level = parseInt(payload.grade_level);
        if (payload.academic_year_id) payload.academic_year_id = parseInt(payload.academic_year_id);
        if (payload.curriculum_id) payload.curriculum_id = parseInt(payload.curriculum_id);
        else delete payload.curriculum_id;
        if (payload.vocational_concentration_id) payload.vocational_concentration_id = parseInt(payload.vocational_concentration_id);
        else delete payload.vocational_concentration_id;
        if (payload.vocational_program_id) payload.vocational_program_id = parseInt(payload.vocational_program_id);
        else delete payload.vocational_program_id;
        if (payload.department_id) payload.department_id = parseInt(payload.department_id);
        else delete payload.department_id;
        if (payload.homeroom_teacher_id) payload.homeroom_teacher_id = parseInt(payload.homeroom_teacher_id);
        else delete payload.homeroom_teacher_id;

        try {
            const res = await fetch('/api/v1/classes', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN },
                body: JSON.stringify(payload)
            });
            const result = await res.json();
            if (res.ok && result.success) {
                vxShowToast(result.message || 'Rombel berhasil ditambahkan!', 'success');
                closeModal('modalAddClass');
                form.reset();
                setTimeout(() => location.reload(), 700);
            } else {
                let errMsg = result.message || 'Gagal menambahkan rombel.';
                if (result.errors) {
                    const firstErr = Object.values(result.errors)[0];
                    if (firstErr) errMsg = Array.isArray(firstErr) ? firstErr[0] : firstErr;
                }
                vxShowToast(errMsg, 'error');
            }
        } catch (err) { vxShowToast(err.message, 'error'); }
    }

    async function submitEditClass(e) {
        e.preventDefault();
        const form = e.target;
        const id = document.getElementById('editClassId').value;
        const payload = Object.fromEntries(new FormData(form).entries());
        delete payload.id;
        payload.grade_level = parseInt(payload.grade_level);
        if (payload.academic_year_id) payload.academic_year_id = parseInt(payload.academic_year_id);
        else payload.academic_year_id = null;
        if (payload.curriculum_id) payload.curriculum_id = parseInt(payload.curriculum_id);
        else payload.curriculum_id = null;
        if (payload.vocational_concentration_id) payload.vocational_concentration_id = parseInt(payload.vocational_concentration_id);
        else payload.vocational_concentration_id = null;
        if (payload.vocational_program_id) payload.vocational_program_id = parseInt(payload.vocational_program_id);
        else payload.vocational_program_id = null;
        if (payload.department_id) payload.department_id = parseInt(payload.department_id);
        else payload.department_id = null;
        if (payload.homeroom_teacher_id) payload.homeroom_teacher_id = parseInt(payload.homeroom_teacher_id);
        else payload.homeroom_teacher_id = null;

        try {
            const res = await fetch(`/api/v1/classes/${id}`, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN },
                body: JSON.stringify(payload)
            });
            const result = await res.json();
            if (res.ok && result.success) {
                vxShowToast(result.message || 'Rombel berhasil diperbarui!', 'success');
                closeModal('modalEditClass');
                setTimeout(() => location.reload(), 700);
            } else {
                let errMsg = result.message || 'Gagal memperbarui rombel.';
                if (result.errors) {
                    const firstErr = Object.values(result.errors)[0];
                    if (firstErr) errMsg = Array.isArray(firstErr) ? firstErr[0] : firstErr;
                }
                vxShowToast(errMsg, 'error');
            }
        } catch (err) { vxShowToast(err.message, 'error'); }
    }

    async function deleteClass(id, name) {
        if (!confirm(`Hapus rombongan belajar '${name}'? Pastikan tidak ada siswa yang sedang terdaftar di kelas ini.`)) return;
        try {
            const res = await fetch(`/api/v1/classes/${id}`, {
                method: 'DELETE',
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN }
            });
            const result = await res.json();
            if (res.ok && result.success) {
                vxShowToast(result.message || 'Rombel berhasil dihapus!', 'success');
                setTimeout(() => location.reload(), 700);
            } else {
                vxShowToast(result.message || 'Gagal menghapus rombel.', 'error');
            }
        } catch (err) { vxShowToast(err.message, 'error'); }
    }

    // --- MATA PELAJARAN (SUBJECTS) HANDLERS ---
    function filterSubjects() {
        const search = (document.getElementById('searchSubjectInput')?.value || '').toLowerCase();
        const curr = document.getElementById('filterSubjectCurriculum')?.value || '';
        const group = document.getElementById('filterSubjectGroup')?.value || '';

        document.querySelectorAll('#subjectsTable tbody tr').forEach(r => {
            const name = r.getAttribute('data-name') || '';
            const rCurr = r.getAttribute('data-curriculum') || '';
            const rGroup = r.getAttribute('data-group') || '';

            const matchSearch = !search || name.includes(search);
            const matchCurr = !curr || rCurr === curr || (rCurr.includes(',') && rCurr.split(',').includes(curr));
            const matchGroup = !group || rGroup === group;

            r.style.display = (matchSearch && matchCurr && matchGroup) ? '' : 'none';
        });
    }

    function updateAddSubjectGroupDropdown(curId) {
        const select = document.getElementById('addSubjectGroupSelect');
        if (!select) return;
        select.innerHTML = '';
        const cur = CURRICULUM_DATA.find(c => c.id == curId);
        const groups = (cur && cur.subject_groups) ? cur.subject_groups : [];
        if (groups.length > 0) {
            groups.forEach(g => {
                const opt = document.createElement('option');
                opt.value = g.code;
                opt.textContent = `${g.name} (${g.code})`;
                select.appendChild(opt);
            });
        } else {
            const defaults = [
                { code: 'WAJIB_UMUM', name: 'Kelompok Umum / Wajib Nasional' },
                { code: 'PEMINATAN_PILIHAN', name: 'Kelompok Kejuruan / Pilihan' },
                { code: 'P5_PROJEK', name: 'Projek Profil Pelajar Pancasila (P5)' },
                { code: 'MULOK', name: 'Muatan Lokal (Mulok)' },
                { code: 'KEAGAMAAN', name: 'Keagamaan / Kepesantrenan' },
            ];
            defaults.forEach(d => {
                const opt = document.createElement('option');
                opt.value = d.code;
                opt.textContent = d.name;
                select.appendChild(opt);
            });
        }
    }

    function updateEditSubjectGroupDropdown(curId, selectedGroup = null) {
        const select = document.getElementById('editSubjectGroup');
        if (!select) return;
        select.innerHTML = '';
        const cur = CURRICULUM_DATA.find(c => c.id == curId);
        const groups = (cur && cur.subject_groups) ? cur.subject_groups : [];
        if (groups.length > 0) {
            groups.forEach(g => {
                const opt = document.createElement('option');
                opt.value = g.code;
                opt.textContent = `${g.name} (${g.code})`;
                if (selectedGroup && (g.code === selectedGroup || g.id == selectedGroup)) {
                    opt.selected = true;
                }
                select.appendChild(opt);
            });
        } else {
            const defaults = [
                { code: 'WAJIB_UMUM', name: 'Kelompok Umum / Wajib Nasional' },
                { code: 'PEMINATAN_PILIHAN', name: 'Kelompok Kejuruan / Pilihan' },
                { code: 'P5_PROJEK', name: 'Projek Profil Pelajar Pancasila (P5)' },
                { code: 'MULOK', name: 'Muatan Lokal (Mulok)' },
                { code: 'KEAGAMAAN', name: 'Keagamaan / Kepesantrenan' },
            ];
            defaults.forEach(d => {
                const opt = document.createElement('option');
                opt.value = d.code;
                opt.textContent = d.name;
                if (selectedGroup && d.code === selectedGroup) opt.selected = true;
                select.appendChild(opt);
            });
        }
    }

    function openEditSubjectModal(id, name, code, currId, group, jp, kkm, isActive, category, color) {
        document.getElementById('editSubjectId').value = id;
        document.getElementById('editSubjectName').value = name;
        document.getElementById('editSubjectCode').value = code;
        if (document.getElementById('editSubjectCurriculum')) {
            document.getElementById('editSubjectCurriculum').value = currId || '';
            updateEditSubjectGroupDropdown(currId, group);
        }
        if (document.getElementById('editSubjectJp')) document.getElementById('editSubjectJp').value = jp || 2;
        if (document.getElementById('editSubjectPassingGrade')) document.getElementById('editSubjectPassingGrade').value = kkm || 75.0;
        if (document.getElementById('editSubjectActive')) document.getElementById('editSubjectActive').value = isActive ? '1' : '0';
        if (document.getElementById('editSubjectColor')) document.getElementById('editSubjectColor').value = color || '#4680ff';
        openModal('modalEditSubject');
    }

    async function submitAddSubject(e) {
        e.preventDefault();
        const form = e.target;
        const payload = Object.fromEntries(new FormData(form).entries());
        payload.school_id = SCHOOL_ID;
        payload.passing_grade = parseFloat(payload.passing_grade);
        if (payload.hours_per_week) payload.hours_per_week = parseInt(payload.hours_per_week);
        if (payload.curriculum_id) payload.curriculum_id = parseInt(payload.curriculum_id);
        else delete payload.curriculum_id;

        try {
            const res = await fetch('/api/v1/subjects', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN },
                body: JSON.stringify(payload)
            });
            const result = await res.json();
            if (res.ok && result.success) {
                vxShowToast(result.message || 'Mata pelajaran berhasil ditambahkan!', 'success');
                closeModal('modalAddSubject');
                form.reset();
                setTimeout(() => location.reload(), 700);
            } else {
                let errMsg = result.message || 'Gagal menambahkan mata pelajaran.';
                if (result.errors) {
                    const firstErr = Object.values(result.errors)[0];
                    if (firstErr) errMsg = Array.isArray(firstErr) ? firstErr[0] : firstErr;
                }
                vxShowToast(errMsg, 'error');
            }
        } catch (err) { vxShowToast(err.message, 'error'); }
    }

    async function submitEditSubject(e) {
        e.preventDefault();
        const form = e.target;
        const id = document.getElementById('editSubjectId').value;
        const payload = Object.fromEntries(new FormData(form).entries());
        delete payload.id;
        payload.passing_grade = parseFloat(payload.passing_grade);
        payload.is_active = payload.is_active === '1';
        if (payload.hours_per_week) payload.hours_per_week = parseInt(payload.hours_per_week);
        if (payload.curriculum_id) payload.curriculum_id = parseInt(payload.curriculum_id);
        else payload.curriculum_id = null;

        try {
            const res = await fetch(`/api/v1/subjects/${id}`, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN },
                body: JSON.stringify(payload)
            });
            const result = await res.json();
            if (res.ok && result.success) {
                vxShowToast(result.message || 'Mata pelajaran berhasil diperbarui!', 'success');
                closeModal('modalEditSubject');
                setTimeout(() => location.reload(), 700);
            } else {
                let errMsg = result.message || 'Gagal memperbarui mata pelajaran.';
                if (result.errors) {
                    const firstErr = Object.values(result.errors)[0];
                    if (firstErr) errMsg = Array.isArray(firstErr) ? firstErr[0] : firstErr;
                }
                vxShowToast(errMsg, 'error');
            }
        } catch (err) { vxShowToast(err.message, 'error'); }
    }

    async function deleteSubject(id, name) {
        if (!confirm(`Hapus mata pelajaran '${name}'? Pastikan tidak ada jadwal KBM yang sedang menggunakan mapel ini.`)) return;
        try {
            const res = await fetch(`/api/v1/subjects/${id}`, {
                method: 'DELETE',
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN }
            });
            const result = await res.json();
            if (res.ok && result.success) {
                vxShowToast(result.message || 'Mata pelajaran berhasil dihapus!', 'success');
                setTimeout(() => location.reload(), 700);
            } else {
                vxShowToast(result.message || 'Gagal menghapus mata pelajaran.', 'error');
            }
        } catch (err) { vxShowToast(err.message, 'error'); }
    }

    async function submitProfileEditWindow(e) {
        e.preventDefault();
        const form = e.target;
        const payload = {
            enabled: form.enabled.value === '1',
            start_date: form.start_date.value || null,
            end_date: form.end_date.value || null
        };

        try {
            const res = await fetch(`/api/v1/schools/${SCHOOL_ID}/profile-edit-window`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN
                },
                body: JSON.stringify(payload)
            });
            const json = await res.json();
            if (res.ok && json.success) {
                vxShowToast(json.message, 'success');
                const badge = document.getElementById('badgeWindowStatus');
                if (badge) {
                    if (json.data.is_currently_open) {
                        badge.className = 'vx-badge badge-success';
                        badge.innerHTML = "<i class='bx bx-lock-open-alt'></i> Periode Pemutakhiran Dibuka";
                    } else {
                        badge.className = 'vx-badge badge-warning';
                        badge.innerHTML = "<i class='bx bx-lock-alt'></i> Periode Pemutakhiran Ditutup";
                    }
                }
            } else {
                vxShowToast(json.message || 'Gagal menyimpan pengaturan jadwal pemutakhiran', 'error');
            }
        } catch (err) {
            vxShowToast(err.message, 'error');
        }
    }

    // --- PESERTA DIDIK VIEW MODE SWITCHER & FILTERING ---
    function setStudentViewMode(mode) {
        const tableView = document.getElementById('studentsTableView');
        const cardView = document.getElementById('studentsCardGridView');
        const btnTable = document.getElementById('btnModeTable');
        const btnCards = document.getElementById('btnModeCards');

        if (mode === 'cards') {
            if (tableView) tableView.style.display = 'none';
            if (cardView) cardView.style.display = 'block';
            if (btnCards) {
                btnCards.style.background = '#fff';
                btnCards.style.color = 'var(--vx-primary)';
                btnCards.style.fontWeight = '700';
                btnCards.style.boxShadow = '0 1px 3px rgba(0,0,0,0.08)';
            }
            if (btnTable) {
                btnTable.style.background = 'transparent';
                btnTable.style.color = '#64748B';
                btnTable.style.fontWeight = '600';
                btnTable.style.boxShadow = 'none';
            }
            localStorage.setItem('student-view-mode', 'cards');
        } else {
            if (tableView) tableView.style.display = 'block';
            if (cardView) cardView.style.display = 'none';
            if (btnTable) {
                btnTable.style.background = '#fff';
                btnTable.style.color = 'var(--vx-primary)';
                btnTable.style.fontWeight = '700';
                btnTable.style.boxShadow = '0 1px 3px rgba(0,0,0,0.08)';
            }
            if (btnCards) {
                btnCards.style.background = 'transparent';
                btnCards.style.color = '#64748B';
                btnCards.style.fontWeight = '600';
                btnCards.style.boxShadow = 'none';
            }
            localStorage.setItem('student-view-mode', 'table');
        }
    }

    function filterStudents() {
        const q = (document.getElementById('searchStudentInput')?.value || '').toLowerCase().trim();
        const cohort = document.getElementById('filterStudentCohort')?.value || '';
        const sClass = document.getElementById('filterStudentClass')?.value || '';
        const status = document.getElementById('filterStudentStatus')?.value || 'all';

        // Filter Table Rows
        document.querySelectorAll('.student-item-row').forEach(row => {
            const name = row.getAttribute('data-name') || '';
            const nis = row.getAttribute('data-nis') || '';
            const nisn = row.getAttribute('data-nisn') || '';
            const nik = row.getAttribute('data-nik') || '';
            const rClass = row.getAttribute('data-class') || '';
            const rCohort = row.getAttribute('data-cohort') || '';
            const rStatus = row.getAttribute('data-status') || '';

            const matchQuery = !q || name.includes(q) || nis.includes(q) || nisn.includes(q) || nik.includes(q);
            const matchCohort = !cohort || rCohort === cohort;
            const matchClass = !sClass || rClass === sClass;
            const matchStatus = status === 'all' || rStatus === status;

            row.style.display = (matchQuery && matchCohort && matchClass && matchStatus) ? '' : 'none';
        });

        // Filter Card Grid
        document.querySelectorAll('.student-item-card').forEach(card => {
            const name = card.getAttribute('data-name') || '';
            const nis = card.getAttribute('data-nis') || '';
            const nisn = card.getAttribute('data-nisn') || '';
            const nik = card.getAttribute('data-nik') || '';
            const rClass = card.getAttribute('data-class') || '';
            const rCohort = card.getAttribute('data-cohort') || '';
            const rStatus = card.getAttribute('data-status') || '';

            const matchQuery = !q || name.includes(q) || nis.includes(q) || nisn.includes(q) || nik.includes(q);
            const matchCohort = !cohort || rCohort === cohort;
            const matchClass = !sClass || rClass === sClass;
            const matchStatus = status === 'all' || rStatus === status;

            card.style.display = (matchQuery && matchCohort && matchClass && matchStatus) ? 'flex' : 'none';
        });
    }

    // --- BULK IMPORT EXCEL (SISWA & GTK) ---
    function openBulkImportModal(type) {
        document.getElementById('bulkImportType').value = type;
        document.getElementById('formBulkImport').reset();
        document.getElementById('bulkImportResultBox').style.display = 'none';
        document.getElementById('bulkImportResultBox').innerHTML = '';

        const titleEl = document.getElementById('bulkImportTitle');
        const subEl = document.getElementById('bulkImportSub');
        const dlBtn = document.getElementById('btnDownloadTemplate');
        const noticeEl = document.getElementById('bulkImportAccountNotice');

        if (type === 'students') {
            titleEl.innerHTML = `<span class="modal-title-icon"><i class='bx bxs-user-detail' style="color: #10b981;"></i></span> Impor Masal Data Siswa (Excel)`;
            subEl.innerText = 'Unggah file Excel untuk mendaftarkan siswa baru, menempatkan ke kelas, dan otomatis membuatkan akun login portal siswa.';
            dlBtn.href = `{{ route('admin.import.template.students') }}`;
            dlBtn.innerHTML = `<i class='bx bx-download'></i> Unduh Template Siswa (.xlsx)`;
            noticeEl.style.borderLeftColor = '#10b981';
            noticeEl.innerHTML = `
                <div style="font-weight: 700; color: #047857; margin-bottom: 0.25rem;">
                    <i class='bx bx-check-shield'></i> Otomasi Akun Login Siswa:
                </div>
                <div>• Siswa dapat langsung login menggunakan <strong>NISN</strong> (10 digit) sebagai username dan kata sandi (default: <code>Siswa123!</code>).</div>
                <div>• Kolom email siswa bersifat <strong>opsional</strong>. Jika kosong, sistem otomatis meng-generate alamat email sistem berbasis NISN.</div>
            `;
        } else {
            titleEl.innerHTML = `<span class="modal-title-icon"><i class='bx bxs-briefcase-alt-2' style="color: #4680ff;"></i></span> Impor Masal Guru & GTK / Karyawan (Excel)`;
            subEl.innerText = 'Unggah file Excel untuk mendaftarkan tenaga pendidik (guru) dan kependidikan (staf/TU/BK/kasir) beserta akun login.';
            dlBtn.href = `{{ route('admin.import.template.teachers') }}`;
            dlBtn.innerHTML = `<i class='bx bx-download'></i> Unduh Template GTK (.xlsx)`;
            noticeEl.style.borderLeftColor = '#4680ff';
            noticeEl.innerHTML = `
                <div style="font-weight: 700; color: #1d4ed8; margin-bottom: 0.25rem;">
                    <i class='bx bx-check-shield'></i> Otomasi Akun Login GTK:
                </div>
                <div>• GTK/karyawan dapat langsung login menggunakan <strong>Email Login</strong> dan kata sandi (default: <code>Sekolahku2026!</code>).</div>
                <div>• Peran hak akses akun (Guru, Staf TU, Guru BK, Bendahara) dan Tugas Tambahan (Waka Kurikulum/Kesiswaan/Sarpras/Humas) otomatis diselaraskan.</div>
            `;
        }

        openModal('modalBulkImport');
    }

    async function submitBulkImport(e) {
        e.preventDefault();
        const type = document.getElementById('bulkImportType').value;
        const fileInput = document.getElementById('bulkImportFileInput');
        if (!fileInput.files || fileInput.files.length === 0) {
            vxShowToast('Silakan pilih file Excel / CSV terlebih dahulu.', 'error');
            return;
        }

        const formData = new FormData();
        formData.append('_token', CSRF_TOKEN);
        formData.append('file', fileInput.files[0]);
        formData.append('duplicate_mode', document.getElementById('bulkImportDuplicateMode').value);

        const btn = document.getElementById('btnSubmitBulkImport');
        const origBtnText = btn.innerHTML;
        btn.innerHTML = `<i class='bx bx-loader-alt bx-spin'></i> Memproses Data...`;
        btn.disabled = true;

        const resultBox = document.getElementById('bulkImportResultBox');
        resultBox.style.display = 'block';
        resultBox.innerHTML = `
            <div style="padding: 1rem; border-radius: var(--radius-md); background: rgba(70, 128, 255, 0.08); border: 1px solid rgba(70, 128, 255, 0.25); text-align: center; font-size: 0.85rem; color: var(--vx-text-heading);">
                <i class='bx bx-loader-alt bx-spin' style="font-size: 1.5rem; display: block; margin-bottom: 0.35rem; color: var(--bento-primary);"></i>
                Sedang membaca dan memvalidasi file Excel... Harap tunggu beberapa saat.
            </div>
        `;

        const targetUrl = (type === 'students') 
            ? `{{ route('admin.import.students') }}` 
            : `{{ route('admin.import.teachers') }}`;

        try {
            const res = await fetch(targetUrl, {
                method: 'POST',
                headers: { 'Accept': 'application/json' },
                body: formData
            });

            const data = await res.json();

            if (res.ok && data.success) {
                const stats = data.data || {};
                let errorsHtml = '';
                if (stats.errors && stats.errors.length > 0) {
                    errorsHtml = `
                        <div style="margin-top: 0.75rem; max-height: 120px; overflow-y: auto; text-align: left; font-size: 0.76rem; background: rgba(0,0,0,0.04); padding: 0.5rem; border-radius: 4px;">
                            <strong>Catatan Peringatan (${stats.errors.length}):</strong>
                            <ul style="margin: 0.25rem 0 0; padding-left: 1.25rem;">
                                ${stats.errors.map(err => `<li>${err}</li>`).join('')}
                            </ul>
                        </div>
                    `;
                }

                resultBox.innerHTML = `
                    <div style="padding: 1rem; border-radius: var(--radius-md); background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.3); color: #065f46; font-size: 0.82rem;">
                        <div style="font-weight: 700; font-size: 0.9rem; display: flex; align-items: center; gap: 0.35rem; margin-bottom: 0.35rem;">
                            <i class='bx bx-check-circle' style="font-size: 1.25rem;"></i> Impor Berhasil!
                        </div>
                        <div>${data.message}</div>
                        <div style="margin-top: 0.5rem; display: flex; gap: 0.75rem; flex-wrap: wrap;">
                            <span class="vx-badge badge-success">Baru: ${stats.imported || 0}</span>
                            <span class="vx-badge badge-info">Diperbarui: ${stats.updated || 0}</span>
                            <span class="vx-badge badge-secondary">Dilewati: ${stats.skipped || 0}</span>
                        </div>
                        ${errorsHtml}
                        <div style="margin-top: 0.75rem;">
                            <button type="button" class="btn btn-primary btn-xs" onclick="location.reload()">
                                <i class='bx bx-refresh'></i> Muat Ulang Halaman
                            </button>
                        </div>
                    </div>
                `;
            } else {
                resultBox.innerHTML = `
                    <div style="padding: 1rem; border-radius: var(--radius-md); background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.3); color: #991b1b; font-size: 0.82rem;">
                        <div style="font-weight: 700; font-size: 0.9rem; display: flex; align-items: center; gap: 0.35rem; margin-bottom: 0.35rem;">
                            <i class='bx bx-error-circle' style="font-size: 1.25rem;"></i> Gagal Mengimpor
                        </div>
                        <div>${data.message || 'Terjadi kesalahan saat memproses data.'}</div>
                    </div>
                `;
            }
        } catch (err) {
            resultBox.innerHTML = `
                <div style="padding: 1rem; border-radius: var(--radius-md); background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.3); color: #991b1b; font-size: 0.82rem;">
                    Terjadi kesalahan koneksi jaringan.
                </div>
            `;
        } finally {
            btn.innerHTML = origBtnText;
            btn.disabled = false;
        }
    }

    // Auto-load saved student view mode on init (Default: Mode Kartu 4x6)
    document.addEventListener('DOMContentLoaded', function() {
        const savedMode = localStorage.getItem('student-view-mode');
        if (savedMode !== 'table') {
            setStudentViewMode('cards');
        } else {
            setStudentViewMode('table');
        }
    });
</script>
@endpush