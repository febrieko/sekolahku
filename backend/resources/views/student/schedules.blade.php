@extends('layouts.student')

@section('title', 'Jadwal Pelajaran Saya — ' . $student->full_name . ' | Sekolahku')
@section('breadcrumb', 'Jadwal Pelajaran Saya')

@push('styles')
<style>
    .matrix-wrap {
        overflow-x: auto;
        border: 1px solid var(--border-color);
        border-radius: 14px;
    }
    .student-matrix-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 820px;
    }
    .student-matrix-table th {
        background: var(--bg-subtle);
        padding: 0.8rem 0.75rem;
        text-align: center;
        font-size: 0.76rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: var(--text-heading);
        border-bottom: 1.5px solid var(--border-color);
        border-right: 1px solid var(--border-color);
    }
    .student-matrix-table td {
        padding: 0.65rem 0.6rem;
        border-bottom: 1px solid var(--border-color);
        border-right: 1px solid var(--border-color);
        vertical-align: middle;
        text-align: center;
    }
    .student-matrix-table tr.break-row td {
        background: var(--bg-subtle);
        color: var(--text-muted);
        font-size: 0.76rem;
        font-weight: 700;
        padding: 0.48rem;
    }
    .period-time-cell {
        background: var(--bg-subtle);
        width: 128px;
    }
    .slot-centered {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        gap: 0.25rem;
        min-height: 62px;
    }
    .day-filter-bar { display: flex; gap: 0.4rem; flex-wrap: wrap; }
    .day-pill {
        padding: 0.35rem 0.8rem;
        border-radius: 99px;
        border: 1px solid var(--border-color);
        background: var(--bg-surface);
        color: var(--text-body);
        font-size: 0.76rem;
        font-weight: 700;
        cursor: pointer;
    }
    .day-pill.active {
        background: var(--vx-primary);
        color: #fff;
        border-color: var(--vx-primary);
    }
</style>
@endpush

@section('content')
@php
    $daysList = $scheduleData['daysList'];
    $periodRows = $scheduleData['periodRows'];
    $mergedCells = $scheduleData['mergedCells'];
    $combinedSlots = $scheduleData['personalSchedules']->where('is_combined', true)->values();
@endphp

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon-wrap stat-icon-primary"><i class='bx bxs-school'></i></div>
        <div class="stat-body">
            <div class="stat-label">Rombel Aktif</div>
            <div class="stat-value">{{ $student->schoolClass?->name ?? '-' }}</div>
            <div class="stat-sub">Homebase: {{ $student->schoolClass?->defaultRoom?->name ?? 'Reguler' }}</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon-wrap stat-icon-success"><i class='bx bx-time-five'></i></div>
        <div class="stat-body">
            <div class="stat-label">Total Jam Belajar</div>
            <div class="stat-value">{{ $scheduleData['weeklyTotalJp'] }} JP <span style="font-size: 0.8rem; font-weight: 600; color: var(--text-muted);">/ Minggu</span></div>
            <div class="stat-sub">{{ $scheduleData['uniqueSubjectsCount'] }} Mata Pelajaran Diikuti</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon-wrap stat-icon-purple"><i class='bx bx-git-merge'></i></div>
        <div class="stat-body">
            <div class="stat-label">Kelas Gabungan & Pilihan</div>
            <div class="stat-value">{{ $scheduleData['combinedCount'] }} Sesi</div>
            <div class="stat-sub">Biodata Agama: <strong>{{ $student->religion ?? '-' }}</strong></div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon-wrap stat-icon-info"><i class='bx bx-calendar-check'></i></div>
        <div class="stat-body">
            <div class="stat-label">Pola Jadwal Aktif</div>
            <div class="stat-value" style="font-size: 1rem;">{{ $scheduleData['activeCategory']?->name ?? 'Reguler' }}</div>
            <div class="stat-sub">TA {{ $activeYear?->name ?? '-' }} ({{ $activeSemester === 'even' ? 'Genap' : 'Ganjil' }})</div>
        </div>
    </div>
</div>

<div class="apex-card">
    <div class="apex-card-header">
        <div>
            <h2 class="apex-card-title">
                <i class='bx bx-calendar-event' style="color: var(--vx-primary);"></i>
                Matriks Jadwal Pelajaran Personal — {{ $student->full_name }}
            </h2>
            <p class="apex-card-desc">
                Otomatis menyesuaikan rombel <strong>{{ $student->schoolClass?->name ?? '-' }}</strong>, filter agama pada biodata (<strong>{{ $student->religion ?? '-' }}</strong>), serta kelompok kelas gabunganmu.
            </p>
        </div>

        <div class="day-filter-bar">
            <button type="button" class="day-pill active" onclick="filterStudentDay('all', this)">Semua Hari</button>
            @foreach($daysList as $dNum => $dName)
                <button type="button" class="day-pill" onclick="filterStudentDay('{{ $dNum }}', this)">{{ $dName }}</button>
            @endforeach
        </div>
    </div>

    <div class="apex-card-body">
        @if(count($periodRows) === 0)
            <div style="text-align: center; padding: 2.5rem 1rem; color: var(--text-muted);">
                Belum ada sesi jam pelajaran yang dipublikasikan pada kategori jadwal aktif ini.
            </div>
        @else
            <div class="matrix-wrap">
                <table class="student-matrix-table" id="studentScheduleMatrix">
                    <thead>
                        <tr>
                            <th>Jam KBM</th>
                            @foreach($daysList as $dNum => $dName)
                                <th class="std-day-col" data-day="{{ $dNum }}">{{ $dName }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($periodRows as $rowKey => $rInfo)
                            @if($rInfo['is_break'])
                                <tr class="break-row">
                                    <td class="period-time-cell cell-mono">{{ $rInfo['start_time'] }} - {{ $rInfo['end_time'] }}</td>
                                    @foreach($daysList as $dNum => $dName)
                                        <td class="std-day-col" data-day="{{ $dNum }}">
                                            <i class='bx bx-coffee'></i> {{ $rInfo['name'] }}
                                        </td>
                                    @endforeach
                                </tr>
                            @else
                                <tr>
                                    <td class="period-time-cell">
                                        <div class="vx-badge badge-dark cell-mono" style="margin-bottom: 3px;">
                                            {{ $rInfo['period_number'] ? 'Jam Ke-'.$rInfo['period_number'] : $rInfo['name'] }}
                                        </div>
                                        <div class="cell-mono" style="font-size: 0.72rem; color: var(--text-muted);">
                                            {{ $rInfo['start_time'] }} - {{ $rInfo['end_time'] }}
                                        </div>
                                    </td>

                                    @foreach($daysList as $dNum => $dName)
                                        @php
                                            $cell = $mergedCells[$dNum][$rowKey] ?? ['skip' => false, 'rowspan' => 1, 'schedule' => null];
                                        @endphp
                                        @if(empty($cell['skip']))
                                            @php
                                                $sched = $cell['schedule'] ?? null;
                                                $span = $cell['rowspan'] ?? 1;
                                                $sColor = $sched ? ($sched->subject?->resolved_color ?? '#4680ff') : null;
                                                $sRoom = $sched ? ($sched->room?->name ?? $sched->room_name ?? ($student->schoolClass?->defaultRoom?->name ?? 'R. Kelas')) : '';
                                            @endphp
                                            <td class="std-day-col" data-day="{{ $dNum }}"
                                                @if($span > 1) rowspan="{{ $span }}" @endif
                                                @if($sched) style="background-color: {{ $sColor }}24; border-left: 4px solid {{ $sColor }};" @endif>
                                                @if($sched)
                                                    <div class="slot-centered">
                                                        <div style="display: flex; align-items: center; justify-content: center; gap: 0.35rem; flex-wrap: wrap;">
                                                            <span style="font-weight: 800; font-size: {{ $span > 1 ? '0.9rem' : '0.83rem' }}; color: var(--text-heading);">
                                                                {{ $sched->subject?->name ?? '-' }}
                                                            </span>
                                                            <span class="vx-badge cell-mono" style="background: {{ $sColor }}; color: #fff; font-size: 0.62rem; padding: 1px 6px;">
                                                                {{ $sched->subject?->code }}
                                                            </span>
                                                        </div>
                                                        <div style="font-size: 0.77rem; font-weight: 600; color: var(--text-body);">
                                                            {{ $sched->teacher?->full_name ?? '-' }}{{ $sched->teacher?->title_suffix ? ', '.$sched->teacher->title_suffix : '' }}
                                                        </div>
                                                        <span class="vx-badge" style="background: rgba(255,255,255,0.75); color: #1e293b; border: 1px solid rgba(15,23,42,0.1); font-size: 0.68rem;">
                                                            <i class='bx bx-door-open'></i> {{ $sRoom }}
                                                        </span>
                                                        @if($sched->is_combined)
                                                            <span class="vx-badge badge-purple" style="font-size: 0.64rem;">
                                                                <i class='bx bx-git-merge'></i> Gabungan: {{ $sched->participating_class_names }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                @else
                                                    <span style="color: var(--text-placeholder); font-size: 0.75rem;">—</span>
                                                @endif
                                            </td>
                                        @endif
                                    @endforeach
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

@if($combinedSlots->isNotEmpty())
<div class="apex-card">
    <div class="apex-card-header">
        <div>
            <h2 class="apex-card-title">
                <i class='bx bx-git-merge' style="color: var(--vx-purple);"></i>
                Rincian Kelas Gabungan & Kelompok Mapel yang Kamu Ikuti
            </h2>
            <p class="apex-card-desc">Daftar sesi lintas rombel yang otomatis dimasukkan ke jadwal belajarmu.</p>
        </div>
    </div>
    <div class="table-responsive">
        <table class="vx-table">
            <thead>
                <tr>
                    <th>Hari & Jam</th>
                    <th>Mata Pelajaran</th>
                    <th>Guru Pengajar</th>
                    <th>Rombel Tergabung</th>
                    <th>Ruang Belajar</th>
                </tr>
            </thead>
            <tbody>
                @foreach($combinedSlots as $cSlot)
                    <tr>
                        <td class="cell-mono">
                            <strong>{{ $daysList[$cSlot->lessonPeriod?->day_of_week] ?? '-' }}</strong>
                            &bull; {{ $cSlot->lessonPeriod?->name }}
                            ({{ substr((string) $cSlot->lessonPeriod?->start_time, 0, 5) }} - {{ substr((string) $cSlot->lessonPeriod?->end_time, 0, 5) }})
                        </td>
                        <td>
                            <span class="cell-title">{{ $cSlot->subject?->name }}</span>
                            @if($cSlot->effective_religion_filter)
                                <span class="vx-badge badge-info" style="margin-left: 4px;">Biodata: {{ $cSlot->effective_religion_filter }}</span>
                            @endif
                        </td>
                        <td>{{ $cSlot->teacher?->full_name }}{{ $cSlot->teacher?->title_suffix ? ', '.$cSlot->teacher->title_suffix : '' }}</td>
                        <td><span class="vx-badge badge-primary">{{ $cSlot->participating_class_names }}</span></td>
                        <td>{{ $cSlot->room?->name ?? $cSlot->room_name ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script>
    function filterStudentDay(day, btn) {
        document.querySelectorAll('.day-pill').forEach(b => b.classList.remove('active'));
        if (btn) btn.classList.add('active');

        document.querySelectorAll('#studentScheduleMatrix .std-day-col').forEach(col => {
            col.style.display = (day === 'all' || col.getAttribute('data-day') === String(day)) ? '' : 'none';
        });
    }
</script>
@endpush

