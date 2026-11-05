@extends('layouts.student')

@section('title', 'Dashboard Siswa — ' . $student->full_name . ' | Sekolahku')
@section('breadcrumb', 'Dashboard Utama')

@push('styles')
<style>
    .student-hero-banner {
        background: linear-gradient(135deg, #1e1b4b 0%, #312e81 48%, #4338ca 100%);
        border-radius: 20px;
        padding: 1.5rem 1.75rem;
        color: #ffffff;
        margin-bottom: 1.35rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1.5rem;
        flex-wrap: wrap;
        position: relative;
        overflow: hidden;
        box-shadow: 0 14px 32px -10px rgba(49, 46, 129, 0.45);
    }
    .student-hero-banner::after {
        content: '';
        position: absolute;
        right: -60px;
        top: -80px;
        width: 260px;
        height: 260px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(129, 140, 248, 0.28) 0%, rgba(129, 140, 248, 0) 70%);
        pointer-events: none;
    }
    .hero-left {
        display: flex;
        align-items: center;
        gap: 1.25rem;
        flex: 1;
        min-width: 280px;
        z-index: 1;
    }
    .hero-photo-4x6 {
        width: 78px;
        height: 104px;
        border-radius: 12px;
        object-fit: cover;
        border: 3px solid rgba(255, 255, 255, 0.85);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.28);
        flex-shrink: 0;
        background: #312e81;
    }
    .hero-greeting-tag {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        font-size: 0.74rem;
        font-weight: 700;
        padding: 0.22rem 0.65rem;
        border-radius: 99px;
        background: rgba(255, 255, 255, 0.14);
        color: #e0e7ff;
        margin-bottom: 0.35rem;
    }
    .hero-student-name {
        margin: 0;
        font-size: 1.45rem;
        font-weight: 800;
        letter-spacing: -0.025em;
        line-height: 1.2;
    }
    .hero-chips {
        display: flex;
        flex-wrap: wrap;
        gap: 0.45rem;
        margin-top: 0.6rem;
    }
    .hero-chip {
        display: inline-flex;
        align-items: center;
        gap: 0.32rem;
        padding: 0.24rem 0.65rem;
        border-radius: 8px;
        background: rgba(255, 255, 255, 0.12);
        border: 1px solid rgba(255, 255, 255, 0.16);
        font-size: 0.74rem;
        font-weight: 600;
        color: #f1f5f9;
    }

    .hero-attendance-box {
        background: rgba(255, 255, 255, 0.11);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 16px;
        padding: 1rem 1.25rem;
        min-width: 285px;
        z-index: 1;
    }

    .bento-dashboard-grid {
        display: grid;
        grid-template-columns: 1.55fr 1fr;
        gap: 1.25rem;
        align-items: start;
    }
    @media (max-width: 1080px) {
        .bento-dashboard-grid { grid-template-columns: 1fr; }
    }

    /* Timeline Items */
    .kbm-timeline { display: flex; flex-direction: column; gap: 0.7rem; }
    .kbm-item {
        display: flex;
        align-items: stretch;
        gap: 0.9rem;
        padding: 0.85rem 1rem;
        border-radius: 14px;
        border: 1px solid var(--border-color);
        background: var(--bg-surface);
        transition: var(--transition);
    }
    .kbm-item:hover { border-color: var(--vx-primary-border); box-shadow: var(--shadow-card); }
    .kbm-item.live-now {
        border: 1.5px solid var(--vx-primary);
        background: var(--vx-primary-light);
    }
    .kbm-item.is-break {
        background: var(--bg-subtle);
        border-style: dashed;
        padding: 0.55rem 1rem;
    }
    .kbm-time-col {
        min-width: 98px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        border-right: 1px dashed var(--border-color);
        padding-right: 0.8rem;
    }
    .kbm-body-col {
        flex: 1;
        min-width: 0;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
        flex-wrap: wrap;
    }

    /* 7-Day Attendance Strip */
    .att-strip-grid {
        display: grid;
        grid-template-columns: repeat(7, minmax(0, 1fr));
        gap: 0.5rem;
    }
    @media (max-width: 640px) {
        .att-strip-grid { grid-template-columns: repeat(4, minmax(0, 1fr)); }
    }
    .att-day-pill {
        border: 1px solid var(--border-color);
        border-radius: 12px;
        padding: 0.6rem 0.4rem;
        text-align: center;
        background: var(--bg-subtle);
    }

    /* Info Alert List */
    .important-notice-list { display: flex; flex-direction: column; gap: 0.7rem; }
    .important-notice-item {
        display: flex;
        gap: 0.75rem;
        padding: 0.85rem 0.95rem;
        border-radius: 12px;
        border: 1px solid var(--border-color);
        background: var(--bg-subtle);
    }
    .notice-icon {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.15rem;
        flex-shrink: 0;
    }
</style>
@endpush

@section('content')
@php
    $previewQuery = (!empty($isStaffPreview) && request('student_id')) ? ['student_id' => request('student_id')] : [];
    $avatarUrl = $student->photo_url ?: 'https://ui-avatars.com/api/?name=' . urlencode($student->full_name) . '&background=4338ca&color=fff&size=240';
    $todayAtt = $attendanceData['todayAttendance'];
    $semLabel = $activeSemester === 'even' ? 'Semester Genap' : 'Semester Ganjil';
    $homeroomName = $student->schoolClass?->homeroomTeacher?->name ?? 'Belum Diatur';
    $homeRoomName = $student->schoolClass?->defaultRoom?->name ?? 'R. Kelas Reguler';

    $netViolPts = $disciplineData['netViolationPoints'];
    $creditScore = $disciplineData['positiveCreditScore'];
    $maxCap = $disciplineData['maxCap'];
    $curThreshold = $disciplineData['currentThreshold'];
@endphp

<!-- 1. HERO GREETING & IDENTITAS AKTIF SISWA + LIVE PRESENSI HARI INI -->
<div class="student-hero-banner">
    <div class="hero-left">
        <img src="{{ $avatarUrl }}" alt="{{ $student->full_name }}" class="hero-photo-4x6">
        <div>
            <div class="hero-greeting-tag">
                <i class='bx bx-sparkles'></i>
                <span>{{ $greeting }}, semangat belajar hari ini!</span>
            </div>
            <h1 class="hero-student-name">{{ $student->full_name }}</h1>
            <div class="hero-chips">
                <span class="hero-chip cell-mono"><i class='bx bx-id-card'></i> NIS: {{ $student->nis }}</span>
                <span class="hero-chip cell-mono"><i class='bx bx-barcode'></i> NISN: {{ $student->nisn ?? '-' }}</span>
                <span class="hero-chip"><i class='bx bxs-school'></i> Kelas {{ $student->schoolClass?->name ?? '-' }}</span>
                @if($student->religion)
                    <span class="hero-chip"><i class='bx bx-book-heart'></i> {{ $student->religion }}</span>
                @endif
                <span class="hero-chip"><i class='bx bx-calendar'></i> TA {{ $activeYear?->name ?? '-' }} &bull; {{ $semLabel }}</span>
            </div>
            <div style="font-size: 0.76rem; color: #c7d2fe; margin-top: 0.55rem;">
                <i class='bx bx-user-voice'></i> Wali Kelas: <strong style="color: #fff;">{{ $homeroomName }}</strong>
                &nbsp;&bull;&nbsp;
                <i class='bx bx-door-open'></i> Homebase: <strong style="color: #fff;">{{ $homeRoomName }}</strong>
            </div>
        </div>
    </div>

    <div class="hero-attendance-box">
        <div style="font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.06em; color: #c7d2fe; font-weight: 700; margin-bottom: 0.35rem;">
            Status Presensi Hari Ini ({{ now()->translatedFormat('d M Y') }})
        </div>

        @if($todayAtt)
            <div style="display: flex; align-items: center; gap: 0.55rem; margin-bottom: 0.45rem;">
                @if($todayAtt->status === 'present')
                    <span class="vx-badge" style="background: rgba(16,185,129,0.25); color: #6ee7b7; border: 1px solid rgba(110,231,183,0.4);">
                        <i class='bx bxs-check-circle'></i> HADIR TEPAT WAKTU
                    </span>
                @elseif($todayAtt->status === 'late')
                    <span class="vx-badge" style="background: rgba(245,158,11,0.28); color: #fde68a; border: 1px solid rgba(253,230,138,0.4);">
                        <i class='bx bxs-time-five'></i> HADIR TERLAMBAT
                    </span>
                @else
                    <span class="vx-badge" style="background: rgba(56,189,248,0.25); color: #bae6fd;">
                        {{ strtoupper($todayAtt->status) }}
                    </span>
                @endif
                @if($todayAtt->is_within_radius)
                    <span style="font-size: 0.72rem; color: #a7f3d0;"><i class='bx bx-current-location'></i> Geofence Valid</span>
                @endif
            </div>
            <div class="cell-mono" style="font-size: 0.82rem; color: #f8fafc; margin-bottom: 0.65rem;">
                Masuk: <strong>{{ $todayAtt->check_in_at ? substr((string) $todayAtt->check_in_at, 0, 5) : '--:--' }}</strong>
                &nbsp;|&nbsp;
                Pulang: <strong>{{ $todayAtt->check_out_at ? substr((string) $todayAtt->check_out_at, 0, 5) : 'Belum' }}</strong>
            </div>
            @if(!$todayAtt->check_out_at && in_array($todayAtt->status, ['present', 'late']))
                <a href="{{ route('attendance.portal') }}" class="btn btn-white btn-sm" style="width: 100%;">
                    <i class='bx bx-log-out-circle'></i> Presensi Pulang Sekarang
                </a>
            @else
                <a href="{{ route('student.attendance', $previewQuery) }}" class="btn btn-white btn-sm" style="width: 100%;">
                    <i class='bx bx-history'></i> Lihat Riwayat Kehadiran
                </a>
            @endif
        @else
            <div style="font-size: 0.88rem; font-weight: 700; color: #fde68a; margin-bottom: 0.25rem;">
                <i class='bx bx-alarm-exclamation'></i> Kamu Belum Presensi Masuk
            </div>
            <div style="font-size: 0.74rem; color: #e0e7ff; margin-bottom: 0.7rem;">
                Segera lakukan presensi selfie & verifikasi titik GPS sekolah.
            </div>
            <a href="{{ route('attendance.portal') }}" class="btn btn-white btn-sm" style="width: 100%;">
                <i class='bx bx-camera'></i> Absen Selfie & GPS Sekarang
            </a>
        @endif
    </div>
</div>

<!-- 2. BARISAN KPI RINGKASAN INFORMASI PENTING SISWA -->
<div class="stats-grid">
    <!-- KPI 1: Kehadiran -->
    <div class="stat-card">
        <div class="stat-icon-wrap stat-icon-success">
            <i class='bx bx-user-check'></i>
        </div>
        <div class="stat-body">
            <div class="stat-label">Kehadiran (30 Hari)</div>
            <div class="stat-value">{{ $attendanceData['attendanceRate'] }}%</div>
            <div class="stat-sub">
                {{ $attendanceData['presentCount'] }} Hadir &bull; {{ $attendanceData['lateCount'] }} Terlambat &bull; {{ $attendanceData['sickCount'] + $attendanceData['excusedCount'] }} Izin/Sakit
            </div>
        </div>
    </div>

    <!-- KPI 2: Agenda Belajar Hari Ini -->
    <div class="stat-card">
        <div class="stat-icon-wrap stat-icon-primary">
            <i class='bx bx-book-open'></i>
        </div>
        <div class="stat-body">
            <div class="stat-label">{{ $scheduleData['isTodaySchoolDay'] ? 'Jadwal Hari Ini ('.$scheduleData['timelineDayName'].')' : 'Jadwal '.$scheduleData['timelineDayName'].' Terdekat' }}</div>
            <div class="stat-value">{{ $scheduleData['todaySubjectCount'] }} Mapel <span style="font-size: 0.85rem; font-weight: 600; color: var(--text-muted);">({{ $scheduleData['todayTotalJp'] }} JP)</span></div>
            <div class="stat-sub">
                @if($scheduleData['liveBlock'] && $scheduleData['liveBlock']['schedule'])
                    <span style="color: var(--vx-success); font-weight: 700;">Sedang KBM: {{ $scheduleData['liveBlock']['schedule']->subject?->code }}</span>
                @elseif($scheduleData['nextBlock'] && $scheduleData['nextBlock']['schedule'])
                    Berikutnya: {{ $scheduleData['nextBlock']['start_time'] }} ({{ $scheduleData['nextBlock']['schedule']->subject?->code }})
                @else
                    Total {{ $scheduleData['weeklyTotalJp'] }} JP / Minggu
                @endif
            </div>
        </div>
    </div>

    <!-- KPI 3: Skor Disiplin & Tata Tertib -->
    <div class="stat-card">
        <div class="stat-icon-wrap {{ $netViolPts === 0 ? 'stat-icon-info' : 'stat-icon-warning' }}">
            <i class='bx bx-shield-quarter'></i>
        </div>
        <div class="stat-body">
            <div class="stat-label">Skor Kredit Disiplin</div>
            <div class="stat-value">{{ $creditScore }} <span style="font-size: 0.82rem; font-weight: 600; color: var(--text-muted);">/ {{ $maxCap }}</span></div>
            <div class="stat-sub">
                @if($curThreshold)
                    <span style="color: {{ $curThreshold->color ?? '#f59e0b' }}; font-weight: 700;">Status: {{ $curThreshold->name }}</span>
                @else
                    <span style="color: var(--vx-success); font-weight: 700;">Predikat Baik ({{ $netViolPts }} Poin Pelanggaran)</span>
                @endif
            </div>
        </div>
    </div>

    <!-- KPI 4: Kelengkapan Biodata Dapodik -->
    <div class="stat-card">
        <div class="stat-icon-wrap stat-icon-purple">
            <i class='bx bx-id-card'></i>
        </div>
        <div class="stat-body">
            <div class="stat-label">Biodata & Foto 4x6</div>
            <div class="stat-value">{{ $profileStats['percentage'] }}%</div>
            <div class="stat-sub">
                @if($profileStats['isEditWindowOpen'])
                    <span style="color: var(--vx-success); font-weight: 700;">Periode Edit Dibuka</span>
                @else
                    <span>Periode Edit Ditutup</span>
                @endif
            </div>
        </div>
    </div>

    <!-- KPI 5: Saku Digital Siswa -->
    <div class="stat-card">
        <div class="stat-icon-wrap stat-icon-warning">
            <i class='bx bx-wallet'></i>
        </div>
        <div class="stat-body">
            <div class="stat-label">Saldo Saku Digital</div>
            <div class="stat-value cell-mono" style="font-size: 1.2rem;">Rp {{ number_format((float) ($student->wallet_balance ?? 0), 0, ',', '.') }}</div>
            <div class="stat-sub">
                @if($student->kip_receiver)
                    <span style="color: var(--vx-info); font-weight: 700;">Penerima KIP Terdaftar</span>
                @elseif($student->kps_receiver)
                    <span style="color: var(--vx-info); font-weight: 700;">Penerima KPS Terdaftar</span>
                @else
                    Non-Tunai Kantin & Koperasi
                @endif
            </div>
        </div>
    </div>
</div>

<!-- 3. BENTO GRID UTAMA (KIRI: TIMELINE JADWAL & KEHADIRAN | KANAN: PENGUMUMAN PENTING & RADAR DISIPLIN) -->
<div class="bento-dashboard-grid">
    <!-- KOLOM KIRI -->
    <div>
        <!-- Card 1: Timeline Jadwal Pelajaran Hari Ini -->
        <div class="apex-card">
            <div class="apex-card-header">
                <div>
                    <h2 class="apex-card-title">
                        <i class='bx bx-calendar-star' style="color: var(--vx-primary);"></i>
                        @if($scheduleData['isTodaySchoolDay'])
                            Agenda Jadwal Pelajaran Hari Ini — {{ $scheduleData['timelineDayName'] }}
                        @else
                            Pratinjau Jadwal Hari Sekolah Terdekat — {{ $scheduleData['timelineDayName'] }}
                        @endif
                    </h2>
                    <p class="apex-card-desc">
                        Disusun otomatis sesuai rombel <strong>{{ $student->schoolClass?->name ?? '-' }}</strong>, kelas gabungan lintas rombel, dan filter biodata siswa.
                    </p>
                </div>
                <a href="{{ route('student.schedules', $previewQuery) }}" class="btn btn-secondary btn-sm">
                    <i class='bx bx-table'></i> Matriks Sepekan
                </a>
            </div>

            <div class="apex-card-body">
                @if(!$scheduleData['isTodaySchoolDay'])
                    <div style="padding: 0.65rem 0.9rem; border-radius: 10px; background: var(--vx-info-light); color: var(--vx-info); font-size: 0.79rem; font-weight: 600; margin-bottom: 0.9rem;">
                        <i class='bx bx-sun'></i> Hari ini tidak ada sesi KBM aktif. Berikut pratinjau jadwal pelajaranmu untuk hari <strong>{{ $scheduleData['timelineDayName'] }}</strong>.
                    </div>
                @endif

                @if(count($scheduleData['timelineBlocks']) === 0)
                    <div style="text-align: center; padding: 2rem 1rem; color: var(--text-muted);">
                        <i class='bx bx-calendar-x' style="font-size: 2.2rem; display: block; margin-bottom: 0.4rem;"></i>
                        Belum ada sesi jadwal pelajaran yang dipublikasikan untuk kelasmu.
                    </div>
                @else
                    <div class="kbm-timeline">
                        @foreach($scheduleData['timelineBlocks'] as $block)
                            @if($block['is_break'])
                                <div class="kbm-item is-break">
                                    <div class="kbm-time-col cell-mono" style="font-size: 0.74rem; color: var(--text-muted);">
                                        {{ $block['start_time'] }} - {{ $block['end_time'] }}
                                    </div>
                                    <div class="kbm-body-col">
                                        <span style="font-size: 0.8rem; font-weight: 700; color: var(--text-muted);">
                                            <i class='bx bx-coffee'></i> {{ $block['label'] }}
                                        </span>
                                        @if($block['state'] === 'LIVE_NOW')
                                            <span class="vx-badge badge-warning">Waktu Istirahat</span>
                                        @endif
                                    </div>
                                </div>
                            @else
                                @php
                                    $bSched = $block['schedule'];
                                    $subjColor = $bSched?->subject?->resolved_color ?? '#94a3b8';
                                    $roomLabel = $bSched ? ($bSched->room?->name ?? $bSched->room_name ?? $homeRoomName) : '-';
                                @endphp
                                <div class="kbm-item {{ $block['state'] === 'LIVE_NOW' ? 'live-now' : '' }}"
                                     @if($bSched) style="border-left: 4px solid {{ $subjColor }};" @endif>
                                    <div class="kbm-time-col">
                                        <span class="cell-mono" style="font-size: 0.8rem; font-weight: 800; color: var(--text-heading);">
                                            {{ $block['start_time'] }} - {{ $block['end_time'] }}
                                        </span>
                                        <span style="font-size: 0.7rem; color: var(--text-muted); margin-top: 2px;">
                                            {{ $block['label'] }} @if($bSched) ({{ $block['jp_count'] }} JP) @endif
                                        </span>
                                    </div>

                                    <div class="kbm-body-col">
                                        @if($bSched)
                                            <div>
                                                <div style="display: flex; align-items: center; gap: 0.4rem; flex-wrap: wrap;">
                                                    <span style="font-weight: 800; font-size: 0.9rem; color: var(--text-heading);">
                                                        {{ $bSched->subject?->name ?? '-' }}
                                                    </span>
                                                    <span class="vx-badge cell-mono" style="background: {{ $subjColor }}; color: #fff; font-size: 0.64rem;">
                                                        {{ $bSched->subject?->code }}
                                                    </span>
                                                    @if($bSched->is_combined)
                                                        <span class="vx-badge badge-purple" style="font-size: 0.65rem;">
                                                            <i class='bx bx-git-merge'></i> Kelas Gabungan ({{ $bSched->participating_class_names }})
                                                        </span>
                                                    @endif
                                                </div>
                                                <div style="font-size: 0.78rem; color: var(--text-muted); margin-top: 0.25rem; display: flex; gap: 0.85rem; flex-wrap: wrap;">
                                                    <span><i class='bx bx-user-voice' style="color: {{ $subjColor }};"></i> {{ $bSched->teacher?->full_name ?? '-' }}{{ $bSched->teacher?->title_suffix ? ', '.$bSched->teacher->title_suffix : '' }}</span>
                                                    <span><i class='bx bx-door-open'></i> {{ $roomLabel }}</span>
                                                </div>
                                            </div>

                                            <div>
                                                @if($block['state'] === 'LIVE_NOW')
                                                    <span class="vx-badge badge-success"><i class='bx bx-broadcast'></i> Sedang Berlangsung</span>
                                                @elseif($block['state'] === 'COMPLETED')
                                                    <span class="vx-badge" style="background: var(--bg-subtle); color: var(--text-muted);">Selesai</span>
                                                @else
                                                    <span class="vx-badge badge-primary">Akan Datang</span>
                                                @endif
                                            </div>
                                        @else
                                            <span style="font-size: 0.8rem; color: var(--text-muted); font-style: italic;">Belum ada mata pelajaran terjadwal pada sesi ini</span>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <!-- Card 2: Jejak Kehadiran 7 Hari Sekolah Terakhir -->
        <div class="apex-card">
            <div class="apex-card-header">
                <div>
                    <h2 class="apex-card-title">
                        <i class='bx bx-trending-up' style="color: var(--vx-success);"></i>
                        Jejak Kehadiran 7 Hari Sekolah Terakhir
                    </h2>
                    <p class="apex-card-desc">
                        Rata-rata jam check-in kamu: <strong class="cell-mono">{{ $attendanceData['avgCheckIn'] ? $attendanceData['avgCheckIn'].' WIB' : 'Belum tercatat' }}</strong>
                    </p>
                </div>
                <a href="{{ route('student.attendance', $previewQuery) }}" class="btn btn-secondary btn-sm">
                    Detail Kehadiran
                </a>
            </div>
            <div class="apex-card-body">
                <div class="att-strip-grid">
                    @foreach($attendanceData['recentDaysStrip'] as $dayItem)
                        @php
                            $st = $dayItem['status'];
                            $pillBg = 'var(--bg-subtle)';
                            $pillColor = 'var(--text-muted)';
                            $stLabel = '—';
                            if ($st === 'present') { $pillBg = 'var(--vx-success-light)'; $pillColor = 'var(--vx-success)'; $stLabel = 'Hadir'; }
                            elseif ($st === 'late') { $pillBg = 'var(--vx-warning-light)'; $pillColor = '#d97706'; $stLabel = 'Terlambat'; }
                            elseif ($st === 'sick') { $pillBg = 'var(--vx-info-light)'; $pillColor = 'var(--vx-info)'; $stLabel = 'Sakit'; }
                            elseif ($st === 'excused') { $pillBg = 'var(--vx-purple-light)'; $pillColor = 'var(--vx-purple)'; $stLabel = 'Izin'; }
                            elseif ($st === 'absent' || $st === 'alpha') { $pillBg = 'var(--vx-danger-light)'; $pillColor = 'var(--vx-danger)'; $stLabel = 'Alpha'; }
                            elseif ($st === 'pending_today') { $pillBg = 'var(--vx-warning-light)'; $pillColor = '#b45309'; $stLabel = 'Hari Ini'; }
                        @endphp
                        <div class="att-day-pill" style="background: {{ $pillBg }};">
                            <div style="font-size: 0.7rem; font-weight: 700; color: var(--text-muted);">{{ $dayItem['day_short'] }}</div>
                            <div class="cell-mono" style="font-size: 0.76rem; font-weight: 800; color: var(--text-heading); margin: 2px 0;">{{ $dayItem['day_num'] }}</div>
                            <div style="font-size: 0.7rem; font-weight: 800; color: {{ $pillColor }};">{{ $stLabel }}</div>
                            <div class="cell-mono" style="font-size: 0.65rem; color: var(--text-muted); margin-top: 2px;">{{ $dayItem['check_in_at'] ?? '--:--' }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- KOLOM KANAN: INFORMASI PENTING, RADAR DISIPLIN & KARTU DIGITAL -->
    <div>
        <!-- Card 3: Papan Informasi Penting Untukmu -->
        <div class="apex-card">
            <div class="apex-card-header">
                <div>
                    <h2 class="apex-card-title">
                        <i class='bx bx-bell' style="color: var(--vx-warning);"></i>
                        Informasi Penting Untukmu
                    </h2>
                    <p class="apex-card-desc">Pemberitahuan sistem akademik, jadwal aktif, dan biodata Dapodik.</p>
                </div>
            </div>
            <div class="apex-card-body">
                <div class="important-notice-list">
                    <!-- Info 1: Status Jendela Edit Biodata & Kelengkapan -->
                    <div class="important-notice-item">
                        <div class="notice-icon {{ $profileStats['percentage'] < 100 ? 'stat-icon-warning' : 'stat-icon-success' }}">
                            <i class='bx bx-user-check'></i>
                        </div>
                        <div style="flex: 1;">
                            <div style="font-weight: 700; font-size: 0.83rem; color: var(--text-heading);">
                                Kelengkapan Biodata Dapodik: {{ $profileStats['percentage'] }}% ({{ $profileStats['filledCount'] }}/{{ $profileStats['totalCount'] }})
                            </div>
                            @if(count($profileStats['missingLabels']) > 0)
                                <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 2px;">
                                    Belum lengkap: <strong>{{ implode(', ', array_slice($profileStats['missingLabels'], 0, 4)) }}{{ count($profileStats['missingLabels']) > 4 ? ', dll.' : '' }}</strong>
                                </div>
                            @else
                                <div style="font-size: 0.75rem; color: var(--vx-success); margin-top: 2px;">
                                    Seluruh atribut pokok Dapodik & titik GPS rumah sudah lengkap!
                                </div>
                            @endif
                            <div style="margin-top: 0.45rem; display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
                                @if($profileStats['isEditWindowOpen'])
                                    <a href="{{ route('student.profile', $previewQuery) }}" class="btn btn-primary btn-sm">
                                        <i class='bx bx-edit'></i> Perbarui Biodata & Foto 4x6
                                    </a>
                                    @if($profileStats['editEnd'])
                                        <span style="font-size: 0.7rem; color: var(--text-muted);">s/d {{ $profileStats['editEnd']->format('d M Y') }}</span>
                                    @endif
                                @else
                                    <a href="{{ route('student.profile', $previewQuery) }}" class="btn btn-secondary btn-sm">
                                        Lihat Biodata Saya
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Info 2: Pola Jadwal KBM Aktif -->
                    @if($scheduleData['activeCategory'])
                        <div class="important-notice-item">
                            <div class="notice-icon stat-icon-primary">
                                <i class='bx bx-time-five'></i>
                            </div>
                            <div>
                                <div style="font-weight: 700; font-size: 0.83rem; color: var(--text-heading);">
                                    Pola Jam Berlaku: {{ $scheduleData['activeCategory']->name }}
                                </div>
                                <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 2px;">
                                    {{ $scheduleData['activeCategory']->description ?: 'Pola waktu KBM aktif semester berjalan.' }}
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Info 3: Kelas Gabungan / Kelompok Mapel yang Diikuti -->
                    @if($scheduleData['combinedCount'] > 0)
                        <div class="important-notice-item">
                            <div class="notice-icon stat-icon-purple">
                                <i class='bx bx-git-merge'></i>
                            </div>
                            <div>
                                <div style="font-weight: 700; font-size: 0.83rem; color: var(--text-heading);">
                                    Terdaftar di {{ $scheduleData['combinedCount'] }} Sesi JP Kelas Gabungan
                                </div>
                                <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 2px;">
                                    Jadwal kelas gabungan lintas rombel (sesuai biodata agama/mapel pilihan) sudah terintegrasi otomatis ke tabel jadwalmu.
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Card 4: Radar Kedisiplinan & Tata Tertib Siswa -->
        <div class="apex-card">
            <div class="apex-card-header">
                <div>
                    <h2 class="apex-card-title">
                        <i class='bx bx-award' style="color: var(--vx-info);"></i>
                        Kedisiplinan & Tata Tertib
                    </h2>
                    <p class="apex-card-desc">Pantauan poin tata tertib & program pembinaan Waka Kesiswaan.</p>
                </div>
                <a href="{{ route('student.discipline', $previewQuery) }}" class="btn btn-secondary btn-sm">
                    Buku Tatib
                </a>
            </div>
            <div class="apex-card-body">
                @php
                    $barPercent = min(100, (int) round(($netViolPts / $maxCap) * 100));
                    $barColor = $netViolPts === 0 ? '#10b981' : ($curThreshold?->color ?? '#f59e0b');
                @endphp
                <div style="display: flex; justify-content: space-between; align-items: baseline; margin-bottom: 0.4rem;">
                    <span style="font-size: 0.8rem; font-weight: 700; color: var(--text-heading);">
                        Akumulasi Poin Pelanggaran: <strong class="cell-mono">{{ $netViolPts }}</strong> / {{ $maxCap }} Poin
                    </span>
                    <span class="vx-badge {{ $netViolPts === 0 ? 'badge-success' : 'badge-warning' }}">
                        {{ $curThreshold ? $curThreshold->name : 'Aman / Teladan' }}
                    </span>
                </div>
                <div style="height: 10px; border-radius: 99px; background: var(--bg-subtle); border: 1px solid var(--border-color); overflow: hidden; margin-bottom: 0.75rem;">
                    <div style="height: 100%; width: {{ max(4, $barPercent) }}%; background: {{ $barColor }}; border-radius: 99px;"></div>
                </div>

                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 0.5rem; text-align: center; margin-bottom: 0.75rem;">
                    <div style="padding: 0.5rem; border-radius: 10px; background: var(--bg-subtle);">
                        <div style="font-size: 0.68rem; color: var(--text-muted);">Pelanggaran</div>
                        <div class="cell-mono" style="font-weight: 800; color: var(--vx-danger);">{{ $disciplineData['violationPoints'] }}</div>
                    </div>
                    <div style="padding: 0.5rem; border-radius: 10px; background: var(--bg-subtle);">
                        <div style="font-size: 0.68rem; color: var(--text-muted);">Pemutihan</div>
                        <div class="cell-mono" style="font-weight: 800; color: var(--vx-success);">-{{ $disciplineData['amnestyPoints'] }}</div>
                    </div>
                    <div style="padding: 0.5rem; border-radius: 10px; background: var(--bg-subtle);">
                        <div style="font-size: 0.68rem; color: var(--text-muted);">Hari Bersih</div>
                        <div class="cell-mono" style="font-weight: 800; color: var(--vx-info);">{{ $disciplineData['cleanDays'] }} hr</div>
                    </div>
                </div>

                @if($disciplineData['policy']->amnesty_enabled && $netViolPts > 0)
                    <div style="font-size: 0.75rem; color: var(--text-muted); padding: 0.55rem 0.75rem; border-radius: 9px; background: var(--vx-success-light); color: #065f46;">
                        <i class='bx bx-bulb'></i> Sekolah menyediakan program <strong>Pemutihan Poin</strong> setelah minimal {{ $disciplineData['policy']->min_clean_days_before_amnesty }} hari bersih tanpa pelanggaran.
                    </div>
                @endif
            </div>
        </div>

        <!-- Card 5: Akses Cepat Kartu Pelajar Digital 4x6 -->
        <div class="apex-card">
            <div class="apex-card-body" style="display: flex; align-items: center; justify-content: space-between; gap: 1rem; flex-wrap: wrap;">
                <div style="display: flex; align-items: center; gap: 0.85rem;">
                    <div class="stat-icon-wrap stat-icon-primary">
                        <i class='bx bx-qr'></i>
                    </div>
                    <div>
                        <div style="font-weight: 800; color: var(--text-heading); font-size: 0.88rem;">Kartu Pelajar Digital & QR NISN</div>
                        <div style="font-size: 0.75rem; color: var(--text-muted);">Gunakan untuk verifikasi perpustakaan, ujian, & presensi gerbang.</div>
                    </div>
                </div>
                <a href="{{ route('student.card', $previewQuery) }}" class="btn btn-primary btn-sm">
                    Buka Kartu 4x6
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

