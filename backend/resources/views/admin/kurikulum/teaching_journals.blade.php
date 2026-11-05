@extends('layouts.vuexy')

@section('title', 'Jurnal Mengajar KBM & Agenda Guru')
@section('breadcrumb_parent', 'Kurikulum & KBM')
@section('breadcrumb_current', 'Jurnal Mengajar')

@push('styles')
<style>
    /* Teaching Journal & KBM Bento System */
    .kbm-schedule-card {
        background: var(--bg-surface);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-xl);
        box-shadow: var(--shadow-card);
        transition: var(--transition);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        position: relative;
        overflow: hidden;
        height: 100%;
    }
    .kbm-schedule-card:hover {
        transform: translateY(-3px);
        box-shadow: var(--shadow-card-hover);
        border-color: var(--bento-primary);
    }
    .kbm-stripe-accent {
        position: absolute;
        top: 0;
        left: 0;
        bottom: 0;
        width: 5px;
    }
    .kbm-card-body {
        padding: 1.25rem 1.25rem 1.25rem 1.5rem;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        height: 100%;
        gap: 1rem;
    }
    .kbm-period-badge {
        font-family: 'JetBrains Mono', monospace;
        font-size: 0.73rem;
        font-weight: 700;
        padding: 0.28rem 0.65rem;
        border-radius: var(--radius-pill);
        background: var(--vx-primary-light);
        color: var(--bento-primary);
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
    }
    .kbm-status-pill {
        font-size: 0.72rem;
        font-weight: 700;
        padding: 0.28rem 0.65rem;
        border-radius: var(--radius-pill);
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
    }
    .kbm-status-pill.completed {
        background: var(--vx-success-light);
        color: var(--vx-success);
        border: 1px solid var(--vx-success-border);
    }
    .kbm-status-pill.in-progress {
        background: var(--vx-warning-light);
        color: var(--vx-warning);
        border: 1px solid var(--vx-warning-border);
    }
    .kbm-status-pill.pending {
        background: var(--bg-subtle);
        color: var(--text-muted);
        border: 1px solid var(--border-color);
    }
    .kbm-subject-title {
        font-size: 1.08rem;
        font-weight: 800;
        color: var(--text-heading);
        line-height: 1.35;
        margin-bottom: 0.35rem;
        letter-spacing: -0.015em;
    }
    .kbm-meta-tag {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.25rem 0.6rem;
        border-radius: var(--radius-sm);
        background: var(--bg-subtle);
        font-size: 0.78rem;
        color: var(--text-heading);
        font-weight: 600;
        border: 1px solid var(--border-subtle);
    }
    .kbm-journal-preview {
        background: var(--bg-subtle);
        border: 1px dashed var(--border-color);
        border-radius: var(--radius-md);
        padding: 0.75rem 0.85rem;
        font-size: 0.8rem;
    }
    .kbm-att-counter {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        font-family: 'JetBrains Mono', monospace;
        font-size: 0.74rem;
        font-weight: 700;
    }
    .kbm-filter-bar {
        background: var(--bg-surface);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-lg);
        padding: 0.75rem 1rem;
    }
    .teacher-avatar-mini {
        width: 24px;
        height: 24px;
        border-radius: 50%;
        background: var(--vx-primary-light);
        color: var(--bento-primary);
        font-weight: 800;
        font-size: 0.7rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-0">

    <!-- PAGE HEADER -->
    <div class="page-title-row">
        <div class="page-heading">
            <h1>
                <i class='bx bx-book-bookmark' style="color: var(--bento-primary);"></i>
                Jurnal Mengajar KBM & Agenda Guru
                <span class="vx-badge badge-primary">Buku Kerja 3</span>
            </h1>
            <p>Pencatatan materi pembelajaran, capaian tujuan KBM, presensi kehadiran siswa per tatap muka, dan supervisi akademik resmi Waka Kurikulum.</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('admin.kurikulum.jurnal.report') }}" class="btn btn-outline-primary btn-sm d-inline-flex align-items-center gap-2" style="border-radius: var(--radius-md);">
                <i class='bx bx-printer'></i> Cetak Laporan Jurnal Guru
            </a>
            <a href="{{ route('admin.schedules') }}" class="btn btn-outline-secondary btn-sm d-inline-flex align-items-center gap-2" style="border-radius: var(--radius-md);">
                <i class='bx bx-calendar-event'></i> Matriks Jadwal Pelajaran
            </a>
        </div>
    </div>

    <!-- RADAR KPI STATS GRID -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon-wrap stat-icon-primary">
                <i class='bx bx-calendar-check'></i>
            </div>
            <div class="stat-body">
                <div class="stat-label">KBM Terjadwal Hari Ini</div>
                <div class="stat-value">{{ $kpi['total_scheduled'] }} <span style="font-size: 13px; font-weight: 500; color: var(--text-muted);">Sesi</span></div>
                <div class="stat-sub">
                    <span class="stat-dot stat-dot-primary"></span>
                    <span>Jadwal aktif semester ini</span>
                </div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon-wrap stat-icon-success">
                <i class='bx bx-check-double'></i>
            </div>
            <div class="stat-body">
                <div class="stat-label">KBM Telah Selesai</div>
                <div class="stat-value">{{ $kpi['completed'] }} <span style="font-size: 13px; font-weight: 500; color: var(--text-muted);">Sesi</span></div>
                <div class="stat-sub">
                    <span class="stat-dot stat-dot-success"></span>
                    <span>Tervalidasi & jurnal terkunci</span>
                </div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon-wrap stat-icon-warning">
                <i class='bx bx-loader-alt bx-spin'></i>
            </div>
            <div class="stat-body">
                <div class="stat-label">Sedang Berlangsung</div>
                <div class="stat-value">{{ $kpi['in_progress'] }} <span style="font-size: 13px; font-weight: 500; color: var(--text-muted);">Kelas</span></div>
                <div class="stat-sub">
                    <span class="stat-dot stat-dot-warning"></span>
                    <span>Sesi kelas sedang tatap muka</span>
                </div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon-wrap stat-icon-info">
                <i class='bx bx-time'></i>
            </div>
            <div class="stat-body">
                <div class="stat-label">Belum Dimulai</div>
                <div class="stat-value">{{ $kpi['not_started'] }} <span style="font-size: 13px; font-weight: 500; color: var(--text-muted);">Sesi</span></div>
                <div class="stat-sub">
                    <span class="stat-dot stat-dot-info"></span>
                    <span>Menunggu jam pelajaran dibuka</span>
                </div>
            </div>
        </div>
    </div>

    <!-- FLASH MESSAGES -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 mb-4" role="alert" style="border-radius: var(--radius-lg); border: 1px solid var(--vx-success-border); background: var(--vx-success-light); color: var(--vx-success);">
            <i class='bx bx-check-circle' style="font-size: 1.35rem;"></i>
            <div class="font-weight-600">{{ session('success') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2 mb-4" role="alert" style="border-radius: var(--radius-lg); border: 1px solid var(--vx-danger-border); background: var(--vx-danger-light); color: var(--vx-danger);">
            <i class='bx bx-error-circle' style="font-size: 1.35rem;"></i>
            <div class="font-weight-600">{{ session('error') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- SECTION 1: JADWAL KBM HARI INI (MASUK KELAS & BUKA SESI) -->
    <div class="apex-card mb-4">
        <div class="d-flex align-items-center justify-content-between p-3.5 border-bottom flex-wrap gap-2">
            <div>
                <h5 class="mb-1 font-weight-bold d-flex align-items-center gap-2" style="font-family:'Plus Jakarta Sans',sans-serif; color: var(--text-heading);">
                    <i class='bx bx-calendar-star' style="color:var(--bento-primary); font-size: 1.25rem;"></i>
                    Jadwal KBM Hari Ini — <span class="text-primary">{{ \Carbon\Carbon::now()->isoFormat('dddd, D MMMM Y') }}</span>
                </h5>
                <small class="text-muted">
                    Pilih sesi kelas di bawah untuk mencatat materi tatap muka, capaian pembelajaran (TP), dan verifikasi presensi siswa.
                </small>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="vx-badge badge-primary px-3 py-1.5" style="font-size: 0.78rem;">
                    <i class='bx bx-calendar'></i> TA {{ $activeYear->name ?? '-' }} • Sem. {{ ucfirst($activeSemester) }}
                </span>
            </div>
        </div>

        <div class="p-3.5">
            @if($todaySchedules->isEmpty())
                <div class="text-center py-5">
                    <div style="width: 64px; height: 64px; border-radius: 50%; background: var(--bg-subtle); display: inline-flex; align-items: center; justify-content: center; margin-bottom: 0.75rem;">
                        <i class='bx bx-calendar-x text-muted' style="font-size: 2rem;"></i>
                    </div>
                    <h6 class="font-weight-bold text-heading mb-1">Tidak Ada Jadwal KBM Hari Ini</h6>
                    <p class="text-muted small mb-0">Tidak ada sesi mengajar yang terjadwal untuk hari ini atau akun Anda sedang tidak memiliki jam tatap muka.</p>
                </div>
            @else
                <div class="row g-3">
                    @foreach($todaySchedules as $sch)
                        @php
                            $key = $sch->id;
                            $existingJournal = $todayJournals->get($key);
                            $isCompleted = $existingJournal && $existingJournal->status === 'completed';
                            $isInProgress = $existingJournal && $existingJournal->status === 'in_progress';
                            $subjColor = $sch->subject?->color_badge ?: '#4680ff';
                        @endphp
                        <div class="col-12 col-md-6 col-xl-4">
                            <div class="kbm-schedule-card">
                                <div class="kbm-stripe-accent" style="background-color: {{ $subjColor }};"></div>
                                <div class="kbm-card-body">
                                    <div>
                                        <!-- Header Badges -->
                                        <div class="d-flex align-items-center justify-content-between mb-2.5">
                                            <span class="kbm-period-badge">
                                                <i class='bx bx-time-five'></i> Jam Ke-{{ $sch->lessonPeriod?->period_number ?? '1-2' }}
                                                @if($sch->lessonPeriod?->start_time)
                                                    <span style="opacity: 0.75;">({{ substr($sch->lessonPeriod->start_time, 0, 5) }}-{{ substr($sch->lessonPeriod->end_time, 0, 5) }})</span>
                                                @endif
                                            </span>

                                            @if($isCompleted)
                                                <span class="kbm-status-pill completed">
                                                    <i class='bx bx-check-circle'></i> Selesai
                                                </span>
                                            @elseif($isInProgress)
                                                <span class="kbm-status-pill in-progress">
                                                    <i class='bx bx-loader-alt bx-spin'></i> Berlangsung
                                                </span>
                                            @else
                                                <span class="kbm-status-pill pending">
                                                    <i class='bx bx-time'></i> Belum Dibuka
                                                </span>
                                            @endif
                                        </div>

                                        <!-- Subject Name -->
                                        <div class="kbm-subject-title">
                                            {{ $sch->subject?->name ?? 'Mata Pelajaran' }}
                                        </div>

                                        <!-- Class & Room Metadata Tags -->
                                        <div class="d-flex align-items-center gap-1.5 flex-wrap mb-2.5">
                                            <span class="kbm-meta-tag">
                                                <i class='bx bx-door-open text-primary'></i>
                                                {{ $sch->schoolClass?->name ?? ($sch->teachingGroup?->name ?? 'Kelas') }}
                                            </span>
                                            <span class="kbm-meta-tag">
                                                <i class='bx bx-buildings text-info'></i>
                                                {{ $sch->room?->name ?? ($sch->room_name ?? 'Ruang Kelas') }}
                                            </span>
                                        </div>

                                        <!-- Teacher Info -->
                                        <div class="d-flex align-items-center gap-2 mb-3">
                                            <span class="teacher-avatar-mini">
                                                {{ substr($sch->teacher?->full_name ?? 'G', 0, 1) }}
                                            </span>
                                            <div class="small text-truncate" style="color: var(--text-body);">
                                                {{ $sch->teacher?->formatted_name ?? $sch->teacher?->full_name }}
                                            </div>
                                        </div>

                                        <!-- Existing Journal Preview -->
                                        @if($existingJournal)
                                            <div class="kbm-journal-preview mb-3">
                                                <div class="d-flex align-items-center justify-content-between mb-1">
                                                    <span class="font-weight-700 text-heading">
                                                        <i class='bx bx-bookmark-alt text-primary'></i> Pertemuan #{{ $existingJournal->meeting_number }}
                                                    </span>
                                                    @if($existingJournal->verified_by_waka_at)
                                                        <span class="badge bg-info text-white" style="font-size: 10px;">
                                                            <i class='bx bx-check-shield'></i> Disupervisi
                                                        </span>
                                                    @endif
                                                </div>
                                                <div class="text-truncate text-muted mb-1.5" style="font-size: 0.78rem;" title="{{ $existingJournal->learning_objective }}">
                                                    {{ $existingJournal->learning_objective ?: '(Tujuan pembelajaran belum diisi)' }}
                                                </div>
                                                @if($existingJournal->attendance_summary)
                                                    <div class="kbm-att-counter">
                                                        <span class="text-success"><i class='bx bx-check'></i> {{ $existingJournal->attendance_summary['present'] ?? 0 }}H</span>
                                                        <span class="text-warning"><i class='bx bx-info-circle'></i> {{ ($existingJournal->attendance_summary['sick'] ?? 0) + ($existingJournal->attendance_summary['excused'] ?? 0) }}S/I</span>
                                                        <span class="text-danger"><i class='bx bx-x'></i> {{ $existingJournal->attendance_summary['absent'] ?? 0 }}A</span>
                                                        <span class="text-muted"><i class='bx bx-award'></i> {{ $existingJournal->attendance_summary['dispensation'] ?? 0 }}D</span>
                                                    </div>
                                                @endif
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Action Button -->
                                    <div>
                                        @if($existingJournal)
                                            <a href="{{ route('admin.kurikulum.jurnal.show', $existingJournal->id) }}" class="btn {{ $isCompleted ? 'btn-outline-primary' : 'btn-primary' }} btn-sm w-100 d-flex align-items-center justify-content-center gap-1.5" style="border-radius: var(--radius-md); font-weight: 600;">
                                                <i class='bx {{ $isCompleted ? 'bx-file-blank' : 'bx-edit-alt' }}'></i>
                                                {{ $isCompleted ? 'Lihat & Periksa Jurnal' : 'Lanjutkan Sesi KBM' }}
                                            </a>
                                        @else
                                            <form action="{{ route('admin.kurikulum.jurnal.open') }}" method="POST" style="margin: 0;">
                                                @csrf
                                                <input type="hidden" name="class_schedule_id" value="{{ $sch->id }}">
                                                <button type="submit" class="btn btn-outline-primary btn-sm w-100 d-flex align-items-center justify-content-center gap-1.5" style="border-radius: var(--radius-md); font-weight: 700;">
                                                    <i class='bx bx-play-circle'></i> Buka Sesi KBM Sekarang
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <!-- SECTION 2: RIWAYAT JURNAL KBM & SUPERVISI AKADEMIK -->
    <div class="apex-card">
        <div class="d-flex align-items-center justify-content-between p-3.5 border-bottom flex-wrap gap-3">
            <div>
                <h5 class="mb-1 font-weight-bold d-flex align-items-center gap-2" style="font-family:'Plus Jakarta Sans',sans-serif; color: var(--text-heading);">
                    <i class='bx bx-history' style="color:var(--bento-primary); font-size: 1.25rem;"></i>
                    Daftar & Riwayat Jurnal Mengajar Semester Ini
                </h5>
                <small class="text-muted">Rekaman histori agenda tatap muka dan verifikasi administrasi guru per pertemuan.</small>
            </div>

            <!-- Filter Controls -->
            <form action="{{ route('admin.kurikulum.jurnal') }}" method="GET" class="d-flex align-items-center gap-2 flex-wrap">
                <select name="class_id" class="form-select form-select-sm" style="width: auto; border-radius: var(--radius-md);" onchange="this.form.submit()">
                    <option value="">Semua Kelas</option>
                    @foreach($classes as $c)
                        <option value="{{ $c->id }}" {{ request('class_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>

                <select name="subject_id" class="form-select form-select-sm" style="width: auto; border-radius: var(--radius-md);" onchange="this.form.submit()">
                    <option value="">Semua Mapel</option>
                    @foreach($subjects as $s)
                        <option value="{{ $s->id }}" {{ request('subject_id') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                    @endforeach
                </select>

                <input type="date" name="date" class="form-control form-control-sm" style="width: auto; border-radius: var(--radius-md);" value="{{ request('date') }}" onchange="this.form.submit()">

                @if(request('class_id') || request('subject_id') || request('date'))
                    <a href="{{ route('admin.kurikulum.jurnal') }}" class="btn btn-sm btn-outline-secondary" style="border-radius: var(--radius-md);" title="Reset Filter">
                        <i class='bx bx-reset'></i>
                    </a>
                @endif
            </form>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size: 0.85rem;">
                <thead>
                    <tr style="background: var(--bg-subtle);">
                        <th class="text-center" style="width: 60px; font-weight: 700; color: var(--text-heading);">Pert.</th>
                        <th style="font-weight: 700; color: var(--text-heading);">Tanggal & Waktu</th>
                        <th style="font-weight: 700; color: var(--text-heading);">Kelas & Mapel</th>
                        <th style="font-weight: 700; color: var(--text-heading);">Guru Pengajar</th>
                        <th style="font-weight: 700; color: var(--text-heading);">Capaian Pembelajaran (TP)</th>
                        <th style="font-weight: 700; color: var(--text-heading);">Presensi</th>
                        <th style="font-weight: 700; color: var(--text-heading);">Status</th>
                        <th style="font-weight: 700; color: var(--text-heading);">Supervisi Waka</th>
                        <th class="text-center" style="width: 90px; font-weight: 700; color: var(--text-heading);">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentJournals as $j)
                        <tr>
                            <td class="text-center">
                                <span class="vx-badge badge-primary font-monospace" style="font-size: 0.75rem; font-weight: 700;">
                                    #{{ $j->meeting_number }}
                                </span>
                            </td>
                            <td>
                                <div class="font-weight-600 text-heading">{{ $j->date->isoFormat('D MMM Y') }}</div>
                                <small class="text-muted font-monospace" style="font-size: 0.74rem;">{{ $j->time_slot_label }}</small>
                            </td>
                            <td>
                                <div class="font-weight-700 text-heading">{{ $j->schoolClass?->name ?? ($j->teachingGroup?->name ?? '-') }}</div>
                                <span class="badge" style="background: rgba(70,128,255,0.08); color: var(--bento-primary); font-size: 0.72rem;">
                                    {{ $j->subject?->name }}
                                </span>
                            </td>
                            <td>
                                <div class="font-weight-600 text-heading">{{ $j->teacher?->formatted_name ?? $j->teacher?->full_name }}</div>
                                @if($j->substitute_teacher_id)
                                    <span class="badge bg-warning text-dark" style="font-size: 0.7rem;">
                                        Pengganti: {{ $j->substituteTeacher?->full_name }}
                                    </span>
                                @endif
                            </td>
                            <td>
                                <div style="max-width: 260px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; color: var(--text-heading);" title="{{ $j->learning_objective }}">
                                    {{ $j->learning_objective ?: '-' }}
                                </div>
                                @if($j->homework_assignment)
                                    <small class="text-primary d-block text-truncate" style="max-width: 260px; font-size: 0.74rem;">
                                        <i class='bx bx-task'></i> PR: {{ $j->homework_assignment }}
                                    </small>
                                @endif
                            </td>
                            <td>
                                @if($j->attendance_summary)
                                    <div class="kbm-att-counter">
                                        <span class="badge bg-success" style="font-size: 0.72rem;" title="Hadir">{{ $j->attendance_summary['present'] ?? 0 }}H</span>
                                        <span class="badge bg-warning text-dark" style="font-size: 0.72rem;" title="Sakit & Izin">{{ ($j->attendance_summary['sick'] ?? 0) + ($j->attendance_summary['excused'] ?? 0) }}S</span>
                                        <span class="badge bg-danger" style="font-size: 0.72rem;" title="Alpa">{{ $j->attendance_summary['absent'] ?? 0 }}A</span>
                                    </div>
                                @else
                                    <span class="text-muted" style="font-size: 0.75rem;">-</span>
                                @endif
                            </td>
                            <td>
                                @if($j->status === 'completed')
                                    <span class="badge bg-success" style="border-radius: var(--radius-pill); font-size: 0.72rem;">
                                        <i class='bx bx-check-circle'></i> Selesai
                                    </span>
                                @elseif($j->status === 'in_progress')
                                    <span class="badge bg-warning text-dark" style="border-radius: var(--radius-pill); font-size: 0.72rem;">
                                        <i class='bx bx-loader-alt bx-spin'></i> KBM
                                    </span>
                                @else
                                    <span class="badge bg-secondary" style="border-radius: var(--radius-pill); font-size: 0.72rem;">
                                        {{ ucfirst($j->status) }}
                                    </span>
                                @endif
                            </td>
                            <td>
                                @if($j->verified_by_waka_at)
                                    <span class="badge bg-info text-white" style="border-radius: var(--radius-pill); font-size: 0.72rem;" title="Diverifikasi oleh {{ $j->verifiedByWaka?->name }}">
                                        <i class='bx bx-check-shield'></i> Tervalidasi
                                    </span>
                                @else
                                    <span class="text-muted" style="font-size: 0.74rem;">Belum</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <a href="{{ route('admin.kurikulum.jurnal.show', $j->id) }}" class="btn btn-sm btn-outline-primary" style="padding: 0.25rem 0.6rem; font-size: 0.75rem; border-radius: var(--radius-sm);" title="Buka Detail Jurnal">
                                    <i class='bx bx-show-alt'></i> Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted">
                                <i class='bx bx-file-blank' style="font-size: 2.2rem; opacity: 0.4;"></i>
                                <p class="mt-2 mb-0 font-weight-500">Belum ada riwayat jurnal KBM yang sesuai dengan kriteria filter.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($recentJournals->hasPages())
            <div class="p-3 border-top d-flex justify-content-end">
                {{ $recentJournals->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
