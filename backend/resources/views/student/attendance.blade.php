@extends('layouts.student')

@section('title', 'Rekap Kehadiran Saya — ' . $student->full_name . ' | Sekolahku')
@section('breadcrumb', 'Rekap Kehadiran Saya')

@section('content')
@php
    $todayAtt = $attendanceData['todayAttendance'];
@endphp

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon-wrap stat-icon-success"><i class='bx bx-check-circle'></i></div>
        <div class="stat-body">
            <div class="stat-label">Persentase Hadir (30 Hari)</div>
            <div class="stat-value">{{ $attendanceData['attendanceRate'] }}%</div>
            <div class="stat-sub">Rata-rata masuk: {{ $attendanceData['avgCheckIn'] ? $attendanceData['avgCheckIn'].' WIB' : '-' }}</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon-wrap stat-icon-primary"><i class='bx bx-calendar-check'></i></div>
        <div class="stat-body">
            <div class="stat-label">Hadir Tepat Waktu</div>
            <div class="stat-value">{{ $attendanceData['presentCount'] }} <span style="font-size: 0.82rem; font-weight: 600; color: var(--text-muted);">Hari</span></div>
            <div class="stat-sub">Terlambat: {{ $attendanceData['lateCount'] }} Hari</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon-wrap stat-icon-info"><i class='bx bx-plus-medical'></i></div>
        <div class="stat-body">
            <div class="stat-label">Sakit & Izin Resmi</div>
            <div class="stat-value">{{ $attendanceData['sickCount'] + $attendanceData['excusedCount'] }} <span style="font-size: 0.82rem; font-weight: 600; color: var(--text-muted);">Hari</span></div>
            <div class="stat-sub">{{ $attendanceData['sickCount'] }} Sakit &bull; {{ $attendanceData['excusedCount'] }} Izin</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon-wrap stat-icon-danger"><i class='bx bx-error-circle'></i></div>
        <div class="stat-body">
            <div class="stat-label">Tanpa Keterangan (Alpha)</div>
            <div class="stat-value">{{ $attendanceData['alphaCount'] }} <span style="font-size: 0.82rem; font-weight: 600; color: var(--text-muted);">Hari</span></div>
            <div class="stat-sub">Pertahankan 0 Alpha!</div>
        </div>
    </div>
</div>

<!-- Banner Aksi Presensi Hari Ini & Titik Geofence Sekolah -->
<div class="apex-card">
    <div class="apex-card-header">
        <div>
            <h2 class="apex-card-title">
                <i class='bx bx-current-location' style="color: var(--vx-primary);"></i>
                Presensi Hari Ini & Titik Geofence Kampus Aktif
            </h2>
            <p class="apex-card-desc">Pastikan kamu berada dalam radius lokasi resmi sekolah saat melakukan presensi selfie.</p>
        </div>
        <a href="{{ route('attendance.portal') }}" class="btn btn-primary btn-sm">
            <i class='bx bx-camera'></i> Buka Kamera Presensi Selfie & GPS
        </a>
    </div>
    <div class="apex-card-body">
        <div style="display: flex; flex-wrap: wrap; gap: 0.75rem;">
            @forelse($geofences as $geo)
                <div style="padding: 0.7rem 1rem; border-radius: 12px; border: 1px solid var(--border-color); background: var(--bg-subtle); flex: 1; min-width: 240px;">
                    <div style="display: flex; align-items: center; justify-content: space-between; gap: 0.5rem;">
                        <span class="cell-title">{{ $geo->name }}</span>
                        @if($geo->is_primary)
                            <span class="vx-badge badge-primary">Utama</span>
                        @endif
                    </div>
                    <div class="cell-mono" style="font-size: 0.74rem; color: var(--text-muted); margin-top: 4px;">
                        Radius Maksimal: <strong>{{ $geo->radius_meters }} Meter</strong>
                        @if($geo->wifi_ssid) &bull; WiFi: {{ $geo->wifi_ssid }} @endif
                    </div>
                </div>
            @empty
                <span style="font-size: 0.8rem; color: var(--text-muted);">Zona geofence mengikuti koordinat utama sekolah.</span>
            @endforelse
        </div>
    </div>
</div>

<!-- Tabel Riwayat Kehadiran -->
<div class="apex-card">
    <div class="apex-card-header">
        <div>
            <h2 class="apex-card-title">
                <i class='bx bx-history' style="color: var(--vx-success);"></i>
                Riwayat Kehadiran Harian Saya
            </h2>
            <p class="apex-card-desc">Catatan lengkap jam datang, jam pulang, metode verifikasi, dan jarak titik GPS.</p>
        </div>
    </div>
    <div class="table-responsive">
        <table class="vx-table">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Status</th>
                    <th>Jam Masuk</th>
                    <th>Jam Pulang</th>
                    <th>Metode & Geofence</th>
                    <th>Catatan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($attendanceData['recentRecords'] as $rec)
                    <tr>
                        <td class="cell-mono">
                            <strong>{{ \Carbon\Carbon::parse($rec->date)->translatedFormat('d M Y') }}</strong>
                            <div class="cell-sub">{{ \Carbon\Carbon::parse($rec->date)->translatedFormat('l') }}</div>
                        </td>
                        <td>
                            @if($rec->status === 'present')
                                <span class="vx-badge badge-success">Hadir Tepat Waktu</span>
                            @elseif($rec->status === 'late')
                                <span class="vx-badge badge-warning">Terlambat</span>
                            @elseif($rec->status === 'sick')
                                <span class="vx-badge badge-info">Sakit</span>
                            @elseif($rec->status === 'excused')
                                <span class="vx-badge badge-purple">Izin / Dispensasi</span>
                            @else
                                <span class="vx-badge badge-danger">{{ strtoupper($rec->status) }}</span>
                            @endif
                        </td>
                        <td class="cell-mono">{{ $rec->check_in_at ? substr((string) $rec->check_in_at, 0, 5).' WIB' : '—' }}</td>
                        <td class="cell-mono">{{ $rec->check_out_at ? substr((string) $rec->check_out_at, 0, 5).' WIB' : '—' }}</td>
                        <td>
                            <span class="vx-badge badge-primary" style="text-transform: uppercase;">{{ $rec->method ?? 'manual' }}</span>
                            @if($rec->distance_meters !== null)
                                <span class="cell-mono" style="font-size: 0.73rem; color: var(--text-muted); margin-left: 4px;">
                                    ({{ round($rec->distance_meters) }} m)
                                </span>
                            @endif
                        </td>
                        <td style="font-size: 0.78rem; color: var(--text-muted);">{{ $rec->notes ?: '—' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 2rem; color: var(--text-muted);">
                            Belum ada riwayat presensi tercatat.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

