@extends('layouts.vuexy')

@section('title', 'Jadwal Pelajaran KBM')
@section('breadcrumb_current', 'Jadwal Pelajaran KBM')
@section('description', 'Penyusunan matriks jadwal pelajaran per kelas tiap hari, tampilan per guru, per ruang kelas/lab, kategori dinamis bermasa berlaku, dan salin jadwal antar semester.')

@push('styles')
<style>
    .sched-header-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 1rem;
        margin-bottom: 1.25rem;
    }
    .sched-context-bar {
        background: var(--bg-surface);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-xl);
        padding: 1rem 1.25rem;
        margin-bottom: 1.25rem;
        box-shadow: var(--shadow-card);
        border-left: 4px solid var(--bento-primary);
    }
    .sched-context-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 0.9rem;
        align-items: end;
    }
    @media (max-width: 1100px) {
        .sched-context-grid { grid-template-columns: repeat(2, 1fr); }
    }
    @media (max-width: 640px) {
        .sched-context-grid { grid-template-columns: 1fr; }
    }

    /* Matrix Grid Table */
    .matrix-table-wrap {
        overflow-x: auto;
        border-radius: var(--radius-lg);
        border: 1px solid var(--border-color);
        background: var(--bg-surface);
    }
    .matrix-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 960px;
    }
    .matrix-table th {
        background: var(--bg-subtle);
        color: var(--text-heading);
        font-size: 0.78rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        padding: 0.85rem 0.75rem;
        border-bottom: 2px solid var(--border-color);
        border-right: 1px solid var(--border-color);
        text-align: center;
    }
    .matrix-table td {
        border-bottom: 1px solid var(--border-color);
        border-right: 1px solid var(--border-color);
        padding: 0.55rem;
        vertical-align: middle;
        text-align: center;
        width: 14.5%;
        transition: background-color 0.15s ease;
    }
    .matrix-table td.period-col {
        width: 13%;
        background: var(--bg-subtle);
        vertical-align: middle;
        text-align: center;
        font-weight: 600;
    }
    .matrix-table tr.break-row td {
        background: rgba(229, 138, 0, 0.08);
        text-align: center;
        vertical-align: middle;
        padding: 0.5rem;
        font-weight: 700;
        color: var(--vx-warning);
        font-size: 0.8rem;
    }

    /* Slot Content inside Colored Matrix Cell (<td>) */
    .matrix-table td.has-subject-slot {
        padding: 0.65rem 0.75rem;
        vertical-align: middle;
        text-align: center;
    }
    .slot-card {
        border-radius: 0;
        padding: 0;
        background: transparent;
        border: none;
        position: relative;
        transition: var(--transition);
        min-height: 74px;
        height: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        gap: 0.35rem;
    }
    .slot-card.slot-teacher-view,
    .slot-card.slot-room-view {
        background: transparent;
        border: none;
    }
    .slot-subject {
        font-size: 0.82rem;
        font-weight: 700;
        color: var(--text-heading);
        line-height: 1.25;
        text-align: center;
    }
    .slot-meta {
        font-size: 0.73rem;
        color: var(--text-body);
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        gap: 0.3rem;
    }
    .slot-room-pill {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.25rem;
        font-size: 0.68rem;
        font-weight: 700;
        padding: 2px 6px;
        border-radius: 4px;
        background: var(--bg-surface);
        color: var(--bento-primary);
        border: 1px solid var(--border-color);
        width: fit-content;
    }
    .slot-actions {
        display: flex;
        gap: 0.25rem;
        justify-content: center;
        margin-top: 0.2rem;
    }
    .slot-empty-btn {
        width: 100%;
        min-height: 74px;
        border: 1.5px dashed var(--border-color);
        border-radius: var(--radius-lg);
        background: transparent;
        color: var(--text-muted);
        font-size: 0.75rem;
        font-weight: 600;
        cursor: pointer;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 0.2rem;
        transition: var(--transition);
    }
    .slot-empty-btn:hover {
        border-color: var(--bento-primary);
        color: var(--bento-primary);
        background: var(--vx-primary-light);
    }
    .slot-available-box {
        width: 100%;
        min-height: 74px;
        border: 1px dashed var(--vx-success-border);
        border-radius: var(--radius-lg);
        background: var(--vx-success-light);
        color: var(--vx-success);
        font-size: 0.74rem;
        font-weight: 700;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 0.25rem;
        cursor: pointer;
        transition: var(--transition);
    }
    .slot-available-box:hover {
        filter: brightness(0.96);
    }

    /* Quota Pills */
    .quota-strip {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
        padding: 0.75rem 1rem;
        background: var(--bg-subtle);
        border-radius: var(--radius-lg);
        margin-bottom: 1rem;
        border: 1px solid var(--border-color);
    }
    .quota-chip {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.3rem 0.65rem;
        border-radius: 999px;
        font-size: 0.75rem;
        font-weight: 600;
        background: var(--bg-surface);
        border: 1px solid var(--border-color);
        color: var(--text-heading);
    }
    .matrix-table td.merged-cell {
        height: 1px;
        vertical-align: middle;
        text-align: center;
    }
    .slot-card.slot-merged {
        height: 100%;
        min-height: 100%;
        align-items: center;
        justify-content: center;
        text-align: center;
    }
    .slot-merged-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        font-size: 0.68rem;
        font-weight: 800;
        padding: 2px 7px;
        border-radius: 999px;
        background: var(--bento-primary);
        color: #ffffff;
        letter-spacing: 0.01em;
        width: fit-content;
    }
    .sched-view-pane { display: none; }
    .sched-view-pane.active { display: block; }
</style>
@endpush

@section('content')
@php
    $daysList = [
        1 => 'Senin',
        2 => 'Selasa',
        3 => 'Rabu',
        4 => 'Kamis',
        5 => 'Jumat',
        6 => 'Sabtu',
    ];

    // Group periods of the selected category into unique time rows (by period_number/name/start_time)
    $categoryPeriods = $selectedCategory ? $selectedCategory->periods : collect();
    // Map periods by [day_of_week][row_key]
    $periodRows = [];
    $periodByDayAndRow = [];
    foreach ($categoryPeriods as $p) {
        $rowKey = ($p->is_break ? 'BRK_' : 'PRD_') . ($p->period_number ?? substr($p->start_time, 0, 5)) . '_' . $p->name;
        if (!isset($periodRows[$rowKey])) {
            $periodRows[$rowKey] = [
                'key' => $rowKey,
                'sample_period_id' => $p->id,
                'name' => $p->name,
                'period_number' => $p->period_number,
                'start_time' => substr($p->start_time, 0, 5),
                'end_time' => substr($p->end_time, 0, 5),
                'is_break' => $p->is_break,
            ];
        }
        $periodByDayAndRow[$p->day_of_week][$rowKey] = $p;
    }

    // Separate Regular Rombel Schedules vs Combined Cross-Class Schedules
    $regularSchedules = $schedules->where('is_combined', false);
    $combinedSchedules = $schedules->where('is_combined', true);

    // Index current context schedules by lesson_period_id
    $classSchedulesByPeriod = $selectedClass
        ? $regularSchedules->where('class_id', $selectedClass->id)->keyBy('lesson_period_id')
        : collect();

    $teacherSchedulesByPeriod = $selectedTeacher
        ? $schedules->where('teacher_id', $selectedTeacher->id)->keyBy('lesson_period_id')
        : collect();

    $roomSchedulesByPeriod = $selectedRoom
        ? $schedules->filter(function ($s) use ($selectedRoom) {
            return (int) $s->room_id === (int) $selectedRoom->id
                || ($s->room_name && strtolower($s->room_name) === strtolower($selectedRoom->name));
        })->keyBy('lesson_period_id')
        : collect();

    $filteredCombinedByPeriod = ($selectedGroup
        ? $combinedSchedules->where('teaching_group_id', $selectedGroup->id)
        : $combinedSchedules
    )->groupBy('lesson_period_id');

    // Combined schedules involving the currently selected class (for contextual summary below View 1)
    $selectedClassCombinedList = $selectedClass
        ? $combinedSchedules->filter(function ($cs) use ($selectedClass) {
            return in_array((int) $selectedClass->id, $cs->participating_class_ids, true);
        })->sortBy(fn ($cs) => ($cs->lessonPeriod?->day_of_week ?? 9).'_'.($cs->lessonPeriod?->start_time ?? '99:99'))
        : collect();

    // Helper function to compute automatic rowspan merging for consecutive periods of the same subject/teacher/class/room
    $orderedRowList = array_values($periodRows);
    $buildMergedCells = function ($scheduleByPeriodMap) use ($daysList, $orderedRowList, $periodByDayAndRow) {
        $cells = [];
        $totalRows = count($orderedRowList);

        foreach ($daysList as $dNum => $dName) {
            $rIdx = 0;
            while ($rIdx < $totalRows) {
                $rInfo = $orderedRowList[$rIdx];
                $rKey = $rInfo['key'];

                if ($rInfo['is_break']) {
                    $cells[$dNum][$rKey] = ['skip' => false, 'rowspan' => 1, 'schedule' => null, 'period' => null];
                    $rIdx++;
                    continue;
                }

                $currPeriod = $periodByDayAndRow[$dNum][$rKey] ?? null;
                $currSched = $currPeriod ? ($scheduleByPeriodMap[$currPeriod->id] ?? null) : null;

                if (!$currPeriod || !$currSched) {
                    $cells[$dNum][$rKey] = [
                        'skip' => false,
                        'rowspan' => 1,
                        'schedule' => null,
                        'period' => $currPeriod,
                    ];
                    $rIdx++;
                    continue;
                }

                // Look ahead for consecutive non-break rows with the same (classes, subject_id, teacher_id, room)
                $span = 1;
                $scheduleIds = [$currSched->id];
                $endPeriod = $currPeriod;
                $currRoomSig = (string) ($currSched->room_id ?: strtolower(trim((string) $currSched->room_name)));
                $currClassSig = ($currSched->is_combined ? 'CMB:' : 'REG:').implode('-', $currSched->participating_class_ids);

                while (($rIdx + $span) < $totalRows) {
                    $nextInfo = $orderedRowList[$rIdx + $span];
                    if ($nextInfo['is_break']) {
                        break; // Do not merge across a full-width break row
                    }
                    $nextKey = $nextInfo['key'];
                    $nextPeriod = $periodByDayAndRow[$dNum][$nextKey] ?? null;
                    $nextSched = $nextPeriod ? ($scheduleByPeriodMap[$nextPeriod->id] ?? null) : null;

                    if (!$nextPeriod || !$nextSched) {
                        break;
                    }

                    $nextRoomSig = (string) ($nextSched->room_id ?: strtolower(trim((string) $nextSched->room_name)));
                    $nextClassSig = ($nextSched->is_combined ? 'CMB:' : 'REG:').implode('-', $nextSched->participating_class_ids);
                    $isSameBlock = $nextClassSig === $currClassSig
                        && (int) $nextSched->subject_id === (int) $currSched->subject_id
                        && (int) $nextSched->teacher_id === (int) $currSched->teacher_id
                        && $nextRoomSig === $currRoomSig;

                    if (!$isSameBlock) {
                        break;
                    }

                    $scheduleIds[] = $nextSched->id;
                    $endPeriod = $nextPeriod;
                    $span++;
                }

                $startLabel = $currPeriod->period_number ? 'Jam Ke-'.$currPeriod->period_number : $currPeriod->name;
                $endLabel = $endPeriod->period_number ? 'Jam Ke-'.$endPeriod->period_number : $endPeriod->name;
                $rangeText = $span > 1
                    ? "{$startLabel} s/d {$endLabel} ({$span} JP)"
                    : "{$startLabel} (1 JP)";

                $cells[$dNum][$rKey] = [
                    'skip' => false,
                    'rowspan' => $span,
                    'schedule' => $currSched,
                    'schedule_ids' => $scheduleIds,
                    'last_schedule_id' => end($scheduleIds),
                    'period' => $currPeriod,
                    'end_period' => $endPeriod,
                    'start_time' => substr($currPeriod->start_time, 0, 5),
                    'end_time' => substr($endPeriod->end_time, 0, 5),
                    'range_label' => $rangeText,
                ];

                // Mark subsequent merged rows as skipped so no duplicate <td> is rendered
                for ($k = 1; $k < $span; $k++) {
                    $coveredKey = $orderedRowList[$rIdx + $k]['key'];
                    $cells[$dNum][$coveredKey] = ['skip' => true];
                }

                $rIdx += $span;
            }
        }

        return $cells;
    };

    // Helper for the dedicated Combined Classes Matrix Table (supports 1 or multiple parallel combined groups per period with rowspan)
    $buildCombinedMergedCells = function ($groupedByPeriod) use ($daysList, $orderedRowList, $periodByDayAndRow) {
        $cells = [];
        $totalRows = count($orderedRowList);

        $signatureForCollection = function ($col) {
            if (!$col || $col->isEmpty()) {
                return '';
            }
            return $col->sortBy(fn ($s) => ($s->teaching_group_id ?? 0).'_'.$s->subject_id.'_'.$s->teacher_id)
                ->map(function ($s) {
                    $rmSig = (string) ($s->room_id ?: strtolower(trim((string) $s->room_name)));
                    $clsSig = implode('-', $s->participating_class_ids);
                    return ($s->teaching_group_id ?? 0).':'.$s->subject_id.':'.$s->teacher_id.':'.$rmSig.':'.$clsSig;
                })
                ->implode('|');
        };

        foreach ($daysList as $dNum => $dName) {
            $rIdx = 0;
            while ($rIdx < $totalRows) {
                $rInfo = $orderedRowList[$rIdx];
                $rKey = $rInfo['key'];

                if ($rInfo['is_break']) {
                    $cells[$dNum][$rKey] = ['skip' => false, 'rowspan' => 1, 'entries' => [], 'period' => null];
                    $rIdx++;
                    continue;
                }

                $currPeriod = $periodByDayAndRow[$dNum][$rKey] ?? null;
                $currList = $currPeriod ? ($groupedByPeriod[$currPeriod->id] ?? collect()) : collect();

                if (!$currPeriod || $currList->isEmpty()) {
                    $cells[$dNum][$rKey] = [
                        'skip' => false,
                        'rowspan' => 1,
                        'entries' => [],
                        'period' => $currPeriod,
                    ];
                    $rIdx++;
                    continue;
                }

                $currSig = $signatureForCollection($currList);
                $span = 1;
                $endPeriod = $currPeriod;
                $periodLists = [$currList];

                while (($rIdx + $span) < $totalRows) {
                    $nextInfo = $orderedRowList[$rIdx + $span];
                    if ($nextInfo['is_break']) {
                        break;
                    }
                    $nextKey = $nextInfo['key'];
                    $nextPeriod = $periodByDayAndRow[$dNum][$nextKey] ?? null;
                    $nextList = $nextPeriod ? ($groupedByPeriod[$nextPeriod->id] ?? collect()) : collect();

                    if (!$nextPeriod || $nextList->isEmpty()) {
                        break;
                    }

                    if ($signatureForCollection($nextList) !== $currSig) {
                        break;
                    }

                    $periodLists[] = $nextList;
                    $endPeriod = $nextPeriod;
                    $span++;
                }

                $startLabel = $currPeriod->period_number ? 'Jam Ke-'.$currPeriod->period_number : $currPeriod->name;
                $endLabel = $endPeriod->period_number ? 'Jam Ke-'.$endPeriod->period_number : $endPeriod->name;
                $rangeText = $span > 1
                    ? "{$startLabel} s/d {$endLabel} ({$span} JP)"
                    : "{$startLabel} (1 JP)";

                // Build merged entry descriptors for each combined group in this slot
                $sortedCurr = $currList->sortBy(fn ($s) => ($s->teaching_group_id ?? 0).'_'.$s->subject_id.'_'.$s->teacher_id)->values();
                $entries = [];
                foreach ($sortedCurr as $idx => $baseSched) {
                    $ids = [];
                    foreach ($periodLists as $pList) {
                        $sortedP = $pList->sortBy(fn ($s) => ($s->teaching_group_id ?? 0).'_'.$s->subject_id.'_'.$s->teacher_id)->values();
                        if (isset($sortedP[$idx])) {
                            $ids[] = $sortedP[$idx]->id;
                        }
                    }
                    $entries[] = [
                        'schedule' => $baseSched,
                        'schedule_ids' => $ids,
                        'last_schedule_id' => end($ids),
                    ];
                }

                $cells[$dNum][$rKey] = [
                    'skip' => false,
                    'rowspan' => $span,
                    'entries' => $entries,
                    'period' => $currPeriod,
                    'end_period' => $endPeriod,
                    'range_label' => $rangeText,
                ];

                for ($k = 1; $k < $span; $k++) {
                    $coveredKey = $orderedRowList[$rIdx + $k]['key'];
                    $cells[$dNum][$coveredKey] = ['skip' => true];
                }

                $rIdx += $span;
            }
        }

        return $cells;
    };

    $classMergedCells = $buildMergedCells($classSchedulesByPeriod);
    $combinedMergedCells = $buildCombinedMergedCells($filteredCombinedByPeriod);
    $teacherMergedCells = $buildMergedCells($teacherSchedulesByPeriod);
    $roomMergedCells = $buildMergedCells($roomSchedulesByPeriod);

    $catValidity = $selectedCategory?->validity_status ?? 'STANDBY';
@endphp

<!-- 1. Page Title & Top Actions -->
<div class="sched-header-row">
    <div class="page-heading">
        <h1>
            Matriks & Pengaturan Jadwal Pelajaran
            <span class="vx-badge badge-primary" style="font-size: 0.72rem;">Multi-View & Kategori Dinamis</span>
        </h1>
        <p style="margin:0; color: var(--text-muted); font-size: 0.84rem;">
            Susun jadwal tiap kelas tiap hari, tampilkan per Guru, Kelas, dan Ruang Kelas/Lab, serta siapkan jadwal semester mendatang dengan masa berlaku.
        </p>
    </div>
    <div style="display: flex; gap: 0.6rem; flex-wrap: wrap;">
        <button type="button" class="btn btn-secondary" onclick="openModal('modalCopySemester')">
            <i class='bx bx-copy-alt'></i> Salin Jadwal dari Semester Lalu
        </button>
        <button type="button" class="btn btn-secondary" onclick="openModal('modalAddScheduleCategory')">
            <i class='bx bx-calendar-plus'></i> Buat Kategori & Masa Berlaku
        </button>
        <button type="button" class="btn btn-secondary" onclick="openAssignCombinedModal()">
            <i class='bx bx-git-merge'></i> + Jadwal Kelas Gabungan
        </button>
        <button type="button" class="btn btn-primary" onclick="openQuickAssignModal()">
            <i class='bx bx-plus-circle'></i> Tambah Slot Jadwal
        </button>
    </div>
</div>

<!-- 2. Global Context Filter Bar (Academic Year, Semester, Dynamic Category & Validity Period) -->
<div class="sched-context-bar">
    <form method="GET" action="{{ route('admin.schedules') }}" id="contextFilterForm">
        <input type="hidden" name="view" id="contextViewInput" value="{{ request('view', 'view-class') }}">
        <input type="hidden" name="class_id" value="{{ $selectedClass?->id }}">
        <input type="hidden" name="teacher_id" value="{{ $selectedTeacher?->id }}">
        <input type="hidden" name="room_id" value="{{ $selectedRoom?->id }}">
        <input type="hidden" name="teaching_group_id" value="{{ $selectedGroup?->id ?? 0 }}">

        <div class="sched-context-grid">
            <div>
                <label class="vx-label" style="font-size: 0.74rem; text-transform: uppercase;">Tahun Ajaran</label>
                <select name="academic_year_id" class="vx-input" onchange="document.getElementById('contextFilterForm').submit()">
                    @foreach($academicYears as $ay)
                        <option value="{{ $ay->id }}" {{ ($selectedYear && $selectedYear->id === $ay->id) ? 'selected' : '' }}>
                            {{ $ay->name }} {{ $ay->is_active ? '(Aktif)' : '' }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="vx-label" style="font-size: 0.74rem; text-transform: uppercase;">Semester</label>
                <select name="academic_semester" class="vx-input" onchange="document.getElementById('contextFilterForm').submit()">
                    <option value="odd" {{ $selectedSemester === 'odd' ? 'selected' : '' }}>Semester Ganjil (Gasal)</option>
                    <option value="even" {{ $selectedSemester === 'even' ? 'selected' : '' }}>Semester Genap</option>
                </select>
            </div>

            <div>
                <label class="vx-label" style="font-size: 0.74rem; text-transform: uppercase;">Kategori Pola Jadwal Dinamis</label>
                <select name="category_id" class="vx-input" onchange="document.getElementById('contextFilterForm').submit()">
                    @foreach($timeCategories as $cat)
                        @php
                            $vStat = $cat->validity_status;
                            $vTag = $cat->is_default ? '★ Utama' : ($vStat === 'UPCOMING' ? 'Persiapan Mendatang' : ($vStat === 'EXPIRED' ? 'Berakhir' : 'Siap'));
                        @endphp
                        <option value="{{ $cat->id }}" {{ ($selectedCategory && $selectedCategory->id === $cat->id) ? 'selected' : '' }}>
                            {{ $cat->name }} [{{ $vTag }}]
                        </option>
                    @endforeach
                </select>
            </div>

            <div style="display: flex; flex-direction: column; justify-content: flex-end;">
                <div style="background: var(--bg-subtle); border: 1px solid var(--border-color); border-radius: var(--radius-lg); padding: 0.55rem 0.85rem; display: flex; align-items: center; justify-content: space-between; gap: 0.5rem;">
                    <div>
                        <div style="font-size: 0.7rem; color: var(--text-muted); font-weight: 700; text-transform: uppercase;">Masa Berlaku Kategori</div>
                        <div style="font-size: 0.8rem; font-weight: 700; color: var(--text-heading);">
                            @if($selectedCategory && ($selectedCategory->effective_start_date || $selectedCategory->effective_end_date))
                                {{ $selectedCategory->effective_start_date ? $selectedCategory->effective_start_date->format('d M Y') : 'Awal' }}
                                &mdash;
                                {{ $selectedCategory->effective_end_date ? $selectedCategory->effective_end_date->format('d M Y') : 'Seterusnya' }}
                            @else
                                Berlaku Reguler (Tanpa Batas Tanggal)
                            @endif
                        </div>
                    </div>
                    <div style="display: flex; align-items: center; gap: 0.35rem;">
                        @if($catValidity === 'UPCOMING')
                            <span class="vx-badge badge-warning" title="Disiapkan untuk masa mendatang"><i class='bx bx-Rocket'></i> Persiapan</span>
                        @elseif($catValidity === 'ACTIVE_DEFAULT' || $catValidity === 'ACTIVE_PERIOD')
                            <span class="vx-badge badge-success"><i class='bx bx-check-circle'></i> Berlaku</span>
                        @elseif($catValidity === 'EXPIRED')
                            <span class="vx-badge badge-muted">Berakhir</span>
                        @endif
                        @if($selectedCategory)
                            <button type="button" class="btn btn-secondary btn-xs" title="Atur Masa Berlaku Kategori Ini"
                                onclick="openEditScheduleCategoryModal({{ $selectedCategory->id }}, '{{ addslashes($selectedCategory->name) }}', '{{ addslashes($selectedCategory->code) }}', '{{ addslashes($selectedCategory->description ?? '') }}', '{{ $selectedCategory->effective_start_date ? $selectedCategory->effective_start_date->format('Y-m-d') : '' }}', '{{ $selectedCategory->effective_end_date ? $selectedCategory->effective_end_date->format('Y-m-d') : '' }}', '{{ $selectedCategory->academic_year_id ?? '' }}', '{{ $selectedCategory->academic_semester ?? '' }}', {{ $selectedCategory->is_default ? 1 : 0 }})">
                                <i class='bx bx-Slider-alt'></i> Atur Tanggal
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- 3. Navigation Tabs: 1. Kelas, 2. Kelas Gabungan, 3. Guru, 4. Ruang Kelas, 5. Kategori & Masa Berlaku, 6. Master Ruangan -->
<div class="apex-tabs-nav" style="margin-bottom: 1.25rem;">
    <button type="button" class="apex-tab-pill sched-tab-btn active" id="btn-view-class" onclick="switchScheduleView('view-class')">
        <i class='bx bxs-school'></i> 1. Jadwal per Kelas (Setting Matriks)
    </button>
    <button type="button" class="apex-tab-pill sched-tab-btn" id="btn-view-combined" onclick="switchScheduleView('view-combined')">
        <i class='bx bx-git-merge'></i> 2. Tabel Kelas Gabungan & Kelompok Mapel ({{ $combinedSchedules->count() }} JP)
    </button>
    <button type="button" class="apex-tab-pill sched-tab-btn" id="btn-view-teacher" onclick="switchScheduleView('view-teacher')">
        <i class='bx bxs-chalkboard'></i> 3. Jadwal per Guru ({{ $teachers->count() }})
    </button>
    <button type="button" class="apex-tab-pill sched-tab-btn" id="btn-view-room" onclick="switchScheduleView('view-room')">
        <i class='bx bx-buildings'></i> 4. Jadwal per Ruang Kelas / Lab ({{ $rooms->count() }})
    </button>
    <button type="button" class="apex-tab-pill sched-tab-btn" id="btn-view-categories" onclick="switchScheduleView('view-categories')">
        <i class='bx bx-time-five'></i> 5. Kategori Dinamis & Masa Berlaku ({{ $timeCategories->count() }})
    </button>
    <button type="button" class="apex-tab-pill sched-tab-btn" id="btn-view-rooms-master" onclick="switchScheduleView('view-rooms-master')">
        <i class='bx bx-door-open'></i> 6. Master Ruangan
    </button>
</div>

<!-- =========================================================================
     VIEW 1: JADWAL BERDASARKAN KELAS (SETTING TIAP KELAS TIAP HARI)
========================================================================= -->
<div id="view-class" class="sched-view-pane active">
    <div class="apex-card" style="margin-bottom: 1.25rem;">
        <div class="apex-card-header" style="flex-wrap: wrap; gap: 1rem;">
            <div style="display: flex; align-items: center; gap: 0.85rem; flex-wrap: wrap;">
                <div>
                    <label class="vx-label" style="margin-bottom: 0.25rem; font-size: 0.72rem; text-transform: uppercase;">Pilih Kelas / Rombel</label>
                    <select class="vx-input" style="min-width: 240px; font-weight: 700;" onchange="changeFilterParam('class_id', this.value, 'view-class')">
                        @foreach($classes as $cls)
                            <option value="{{ $cls->id }}" {{ ($selectedClass && $selectedClass->id === $cls->id) ? 'selected' : '' }}>
                                {{ $cls->name }} — Tingkat {{ $cls->grade_level }} ({{ $cls->defaultRoom?->name ?? 'Tanpa Ruang Default' }})
                            </option>
                        @endforeach
                    </select>
                </div>
                @if($selectedClass)
                <div style="display: flex; gap: 0.5rem; flex-wrap: wrap; align-items: center; padding-top: 1.1rem;">
                    <span class="vx-badge badge-primary">
                        <i class='bx bx-user'></i> Wali Kelas: {{ $selectedClass->homeroomTeacher?->name ?? 'Belum Diatur' }}
                    </span>
                    <span class="vx-badge badge-info">
                        <i class='bx bx-door-open'></i> Ruang Homebase: {{ $selectedClass->defaultRoom?->name ?? 'Belum Diatur' }}
                    </span>
                    <span class="vx-badge badge-success">
                        <i class='bx bx-check-shield'></i> {{ $classSchedulesByPeriod->count() }} JP Terjadwal
                    </span>
                </div>
                @endif
            </div>

            <!-- Filter Hari (Semua Hari vs Harian) -->
            <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
                <div class="day-pills-wrap">
                    <button type="button" class="day-pill-btn active" onclick="filterMatrixDay('all', this)">Semua Hari</button>
                    @foreach($daysList as $dNum => $dName)
                        <button type="button" class="day-pill-btn" onclick="filterMatrixDay('{{ $dNum }}', this)">{{ $dName }}</button>
                    @endforeach
                </div>
            </div>
        </div>

        <div style="padding: 1rem 1.25rem;">
            @if($selectedClass)
                <!-- Indikator Alokasi JP Mapel Kelas Ini -->
                @php
                    $classSubjectCounts = $schedules->where('class_id', $selectedClass->id)->groupBy('subject_id')->map->count();
                @endphp
                <div class="quota-strip">
                    <span style="font-size: 0.75rem; font-weight: 700; color: var(--text-muted); display: inline-flex; align-items: center; gap: 0.3rem; margin-right: 0.35rem;">
                        <i class='bx bx-bar-chart-alt-2' style="color: var(--bento-primary);"></i> Rekap JP & Warna Mapel Kelas {{ $selectedClass->name }}:
                    </span>
                    @forelse($subjects as $subj)
                        @php
                            $scheduledJp = $classSubjectCounts[$subj->id] ?? 0;
                            $targetJp = $subj->curriculumSubjects->first()?->hours_per_week ?? 2;
                            $chipColor = $subj->resolved_color;
                        @endphp
                        <span class="quota-chip" style="border-left: 3.5px solid {{ $chipColor }}; {{ $scheduledJp >= $targetJp ? 'background: var(--vx-success-light);' : '' }}" title="Klik Warna Mapel untuk mengubah warna {{ $subj->name }}">
                            <span style="width: 9px; height: 9px; border-radius: 50%; background: {{ $chipColor }}; display: inline-block;"></span>
                            <span>{{ $subj->code }}</span>
                            <strong style="color: {{ $scheduledJp >= $targetJp ? 'var(--vx-success)' : 'var(--bento-primary)' }};">{{ $scheduledJp }}/{{ $targetJp }} JP</strong>
                        </span>
                    @empty
                        <span style="font-size: 0.75rem; color: var(--text-muted);">Belum ada mata pelajaran aktif.</span>
                    @endforelse
                    <button type="button" class="btn btn-secondary btn-xs" style="margin-left: auto;" onclick="openModal('modalSubjectColors')">
                        <i class='bx bx-palette'></i> Warna Mapel
                    </button>
                </div>
            @endif

            @if(count($periodRows) === 0)
                <div style="text-align: center; padding: 3rem 1rem; border: 1.5px dashed var(--border-color); border-radius: var(--radius-xl);">
                    <i class='bx bx-time-five' style="font-size: 2.5rem; color: var(--text-muted); display: block; margin-bottom: 0.5rem;"></i>
                    <h3 style="margin: 0 0 0.35rem; font-size: 1rem; color: var(--text-heading);">Kategori "{{ $selectedCategory?->name }}" Belum Memiliki Sesi Jam Pelajaran</h3>
                    <p style="font-size: 0.83rem; color: var(--text-muted); margin: 0 0 1rem;">
                        Anda dapat langsung menyalin pola sesi jam dari kategori lain agar matriks jadwal siap diisi.
                    </p>
                    @if($timeCategories->where('id', '!=', $selectedCategory?->id)->count() > 0)
                        <button type="button" class="btn btn-primary btn-sm" onclick="clonePeriodsToCurrentCategory()">
                            <i class='bx bx-copy'></i> Salin Pola Sesi Jam dari Kategori Utama
                        </button>
                    @endif
                </div>
            @else
                <div class="matrix-table-wrap">
                    <table class="matrix-table" id="classMatrixTable">
                        <thead>
                            <tr>
                                <th style="width: 13%;">Sesi / Jam KBM</th>
                                @foreach($daysList as $dNum => $dName)
                                    <th class="matrix-day-col" data-day="{{ $dNum }}">{{ $dName }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($periodRows as $rowKey => $rowInfo)
                                @if($rowInfo['is_break'])
                                    <tr class="break-row">
                                        <td class="period-col">
                                            <div style="font-size: 0.76rem; font-weight: 700;">{{ $rowInfo['name'] }}</div>
                                            <div class="cell-mono" style="font-size: 0.7rem;">{{ $rowInfo['start_time'] }} - {{ $rowInfo['end_time'] }}</div>
                                            <button type="button" class="btn btn-secondary btn-xs" style="margin-top: 0.2rem; font-size: 0.64rem; padding: 1px 6px;"
                                                onclick="openEditPeriodTimeModal({{ $rowInfo['sample_period_id'] }}, '{{ addslashes($rowInfo['name']) }}', '{{ $rowInfo['period_number'] ?? '' }}', '{{ $rowInfo['start_time'] }}', '{{ $rowInfo['end_time'] }}', 1)">
                                                <i class='bx bx-time'></i> Edit Jam
                                            </button>
                                        </td>
                                        @foreach($daysList as $dNum => $dName)
                                            <td class="matrix-day-col" data-day="{{ $dNum }}">
                                                <i class='bx bx-coffee'></i> {{ $rowInfo['name'] }} ({{ $rowInfo['start_time'] }} - {{ $rowInfo['end_time'] }})
                                            </td>
                                        @endforeach
                                    </tr>
                                @else
                                    <tr>
                                        <td class="period-col">
                                            <div class="vx-badge badge-dark cell-mono" style="margin-bottom: 0.25rem;">
                                                {{ $rowInfo['period_number'] ? 'Jam Ke-'.$rowInfo['period_number'] : $rowInfo['name'] }}
                                            </div>
                                            <div class="cell-mono" style="font-size: 0.73rem; color: var(--text-muted);">
                                                {{ $rowInfo['start_time'] }} - {{ $rowInfo['end_time'] }}
                                            </div>
                                            <button type="button" class="btn btn-secondary btn-xs" style="margin-top: 0.25rem; font-size: 0.64rem; padding: 1px 6px;"
                                                title="Ubah Waktu Mulai & Selesai Sesi Jam Ini"
                                                onclick="openEditPeriodTimeModal({{ $rowInfo['sample_period_id'] }}, '{{ addslashes($rowInfo['name']) }}', '{{ $rowInfo['period_number'] ?? '' }}', '{{ $rowInfo['start_time'] }}', '{{ $rowInfo['end_time'] }}', 0)">
                                                <i class='bx bx-time'></i> Edit Jam
                                            </button>
                                        </td>
                                        @foreach($daysList as $dNum => $dName)
                                            @php
                                                $cellData = $classMergedCells[$dNum][$rowKey] ?? ['skip' => false, 'rowspan' => 1, 'schedule' => null, 'period' => null];
                                            @endphp
                                            @if(empty($cellData['skip']))
                                                @php
                                                    $cellPeriod = $cellData['period'] ?? null;
                                                    $endCellPeriod = $cellData['end_period'] ?? $cellPeriod;
                                                    $schedItem = $cellData['schedule'] ?? null;
                                                    $spanCount = $cellData['rowspan'] ?? 1;
                                                    $schedIdsJson = isset($cellData['schedule_ids']) ? implode(',', $cellData['schedule_ids']) : ($schedItem?->id ?? '');
                                                    $resolvedRoomName = $schedItem ? ($schedItem->room?->name ?? $schedItem->room_name ?? ($selectedClass?->defaultRoom?->name ?? 'R. Kelas')) : '';
                                                    $subjColor = $schedItem ? ($schedItem->subject?->resolved_color ?? '#4680ff') : null;
                                                @endphp
                                                <td class="matrix-day-col {{ $spanCount > 1 ? 'merged-cell' : '' }} {{ $schedItem ? 'has-subject-slot' : '' }}"
                                                    data-day="{{ $dNum }}"
                                                    @if($spanCount > 1) rowspan="{{ $spanCount }}" @endif
                                                    @if($schedItem) style="background-color: {{ $subjColor }}24; border-left: 4px solid {{ $subjColor }};" @endif>
                                                    @if(!$cellPeriod)
                                                        <div style="text-align: center; padding: 1.2rem 0.25rem; color: var(--text-placeholder); font-size: 0.72rem;">
                                                            — Tidak Ada Sesi —
                                                        </div>
                                                    @elseif($schedItem)
                                                        <div class="slot-card {{ $spanCount > 1 ? 'slot-merged' : '' }}">
                                                            <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 0.25rem; width: 100%;">
                                                                <div style="display: flex; justify-content: center; align-items: center; gap: 0.35rem; flex-wrap: wrap;">
                                                                    <span class="slot-subject" style="{{ $spanCount > 1 ? 'font-size: 0.9rem;' : '' }}">{{ $schedItem->subject?->name ?? '-' }}</span>
                                                                    <span class="vx-badge cell-mono" style="background: {{ $subjColor }}; color: #fff; font-size: 0.63rem; padding: 1px 6px;">
                                                                        {{ $schedItem->subject?->code }}
                                                                    </span>
                                                                </div>
                                                                <div class="slot-meta">
                                                                    <i class='bx bx-user-voice' style="color: {{ $subjColor }};"></i>
                                                                    <span>{{ $schedItem->teacher?->full_name ?? '-' }}{{ $schedItem->teacher?->title_suffix ? ', '.$schedItem->teacher->title_suffix : '' }}</span>
                                                                </div>
                                                                @if($schedItem->effective_religion_filter)
                                                                    <span class="vx-badge badge-info" style="font-size: 0.64rem; padding: 1px 6px;" title="Sistem otomatis memfilter siswa dari biodata agama">
                                                                        <i class='bx bx-id-card'></i> Biodata: {{ $schedItem->effective_religion_filter }}
                                                                    </span>
                                                                @endif
                                                            </div>
                                                            <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 0.3rem; margin-top: 0.2rem; width: 100%;">
                                                                <span class="slot-room-pill">
                                                                    <i class='bx bx-buildings'></i> {{ $resolvedRoomName }}
                                                                </span>
                                                                <div class="slot-actions">
                                                                    @if($schedItem->effective_religion_filter || $schedItem->teaching_group_id)
                                                                        <button type="button" class="btn btn-secondary btn-xs btn-icon-sm"
                                                                            title="Lihat Daftar Siswa Peserta (Filter Biodata)"
                                                                            onclick="openScheduleParticipantsModal({{ $schedItem->id }})">
                                                                            <i class='bx bx-group'></i>
                                                                        </button>
                                                                    @endif
                                                                    @if($spanCount > 1)
                                                                        <button type="button" class="btn btn-secondary btn-xs" style="font-size: 0.66rem; padding: 2px 5px;"
                                                                            title="Kurangi 1 Jam Pelajaran Terakhir dari Blok Ini"
                                                                            onclick="deleteScheduleSlot({{ $cellData['last_schedule_id'] }}, '', false)">
                                                                            -1 JP
                                                                        </button>
                                                                    @endif
                                                                    <button type="button" class="btn btn-secondary btn-xs btn-icon-sm" title="Ubah Mapel, Guru, Ruang & Jam ({{ $cellData['range_label'] ?? '1 JP' }})"
                                                                        onclick="openEditSlotModal({{ $schedItem->id }}, '{{ addslashes($dName.' — '.($cellData['range_label'] ?? $cellPeriod->name)) }}', {{ $schedItem->subject_id }}, {{ $schedItem->teacher_id }}, '{{ $schedItem->room_id ?? '' }}', '{{ addslashes($schedItem->room_name ?? '') }}', '{{ addslashes($schedItem->notes ?? '') }}', '{{ $schedIdsJson }}', {{ $dNum }}, {{ $cellPeriod->id }}, {{ $endCellPeriod?->id ?? $cellPeriod->id }})">
                                                                        <i class='bx bx-edit-alt'></i>
                                                                    </button>
                                                                    <button type="button" class="btn btn-danger-light btn-xs btn-icon-sm" title="Hapus Blok Jadwal ({{ $spanCount }} JP)"
                                                                        onclick="deleteScheduleSlot({{ $schedItem->id }}, '{{ $schedIdsJson }}', {{ $spanCount > 1 ? 'true' : 'false' }})">
                                                                        <i class='bx bx-trash'></i>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @else
                                                        <button type="button" class="slot-empty-btn"
                                                            onclick="openAssignSlotModal({{ $dNum }}, '{{ $dName }}', {{ $cellPeriod->id }}, '{{ addslashes($cellPeriod->name) }}')">
                                                            <i class='bx bx-plus-circle' style="font-size: 1.1rem;"></i>
                                                            <span>Set Mapel</span>
                                                        </button>
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

    <!-- Tabel Kontekstual: Jadwal Kelas Gabungan yang Diikuti Siswa dari Rombel Ini -->
    @if($selectedClass && $selectedClassCombinedList->isNotEmpty())
        <div class="apex-card">
            <div class="apex-card-header">
                <div>
                    <h2 class="apex-card-title">
                        <i class='bx bx-git-merge' style="color: var(--bento-primary);"></i>
                        Jadwal Kelas Gabungan Lintas Rombel yang Diikuti Siswa {{ $selectedClass->name }}
                    </h2>
                    <p class="apex-card-desc">
                        Mata pelajaran paralel/lintas rombel (seperti Pendidikan Agama non-mayoritas atau Mapel Pilihan) ditampung pada tabel khusus kelas gabungan.
                    </p>
                </div>
                <button type="button" class="btn btn-primary btn-xs" onclick="switchScheduleView('view-combined')">
                    <i class='bx bx-table'></i> Buka Matriks Kelas Gabungan Penuh
                </button>
            </div>
            <div class="table-responsive">
                <table class="vx-table">
                    <thead>
                        <tr>
                            <th>Hari & Jam</th>
                            <th>Mata Pelajaran</th>
                            <th>Guru Pengajar</th>
                            <th>Rombel yang Digabung</th>
                            <th>Filter Peserta Siswa</th>
                            <th>Ruang Belajar</th>
                            <th style="text-align: right;">Peserta</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($selectedClassCombinedList as $cSlot)
                            @php
                                $cSubjColor = $cSlot->subject?->resolved_color ?? '#8b5cf6';
                                $cRel = $cSlot->effective_religion_filter;
                            @endphp
                            <tr>
                                <td class="cell-mono">
                                    <strong>{{ $daysList[$cSlot->lessonPeriod?->day_of_week] ?? '-' }}</strong>
                                    &bull; {{ $cSlot->lessonPeriod?->name }}
                                    ({{ substr((string) $cSlot->lessonPeriod?->start_time, 0, 5) }} - {{ substr((string) $cSlot->lessonPeriod?->end_time, 0, 5) }})
                                </td>
                                <td>
                                    <span class="vx-badge cell-mono" style="background: {{ $cSubjColor }}; color: #fff;">{{ $cSlot->subject?->code }}</span>
                                    <span class="cell-title" style="margin-left: 0.35rem;">{{ $cSlot->subject?->name }}</span>
                                </td>
                                <td>{{ $cSlot->teacher?->full_name }}{{ $cSlot->teacher?->title_suffix ? ', '.$cSlot->teacher->title_suffix : '' }}</td>
                                <td><span class="vx-badge badge-primary">Gabungan: {{ $cSlot->participating_class_names }}</span></td>
                                <td>
                                    @if($cRel)
                                        <span class="vx-badge badge-info"><i class='bx bx-id-card'></i> Biodata Agama: {{ $cRel }}</span>
                                    @elseif($cSlot->teachingGroup)
                                        <span class="vx-badge badge-warning"><i class='bx bx-group'></i> {{ $cSlot->teachingGroup->name }}</span>
                                    @else
                                        <span class="vx-badge badge-dark">Seluruh Siswa Rombel</span>
                                    @endif
                                </td>
                                <td>{{ $cSlot->room?->name ?? $cSlot->room_name ?? '-' }}</td>
                                <td style="text-align: right;">
                                    <button type="button" class="btn btn-secondary btn-xs" onclick="openScheduleParticipantsModal({{ $cSlot->id }})">
                                        <i class='bx bx-group'></i> Lihat Siswa
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>

<!-- =========================================================================
     VIEW 1B: TABEL KHUSUS KELAS GABUNGAN & KELOMPOK MAPEL LINTAS ROMBEL
========================================================================= -->
<div id="view-combined" class="sched-view-pane">
    <div class="apex-card" style="margin-bottom: 1.25rem;">
        <div class="apex-card-header" style="flex-wrap: wrap; gap: 1rem;">
            <div style="display: flex; align-items: center; gap: 0.85rem; flex-wrap: wrap;">
                <div>
                    <label class="vx-label" style="margin-bottom: 0.25rem; font-size: 0.72rem; text-transform: uppercase;">Filter Kelompok Kelas Gabungan / Mapel Pilihan</label>
                    <select class="vx-input" style="min-width: 310px; font-weight: 700;" onchange="changeFilterParam('teaching_group_id', this.value, 'view-combined')">
                        <option value="0" {{ !$selectedGroup ? 'selected' : '' }}>
                            Semua Jadwal Kelas Gabungan ({{ $combinedSchedules->count() }} Sesi JP)
                        </option>
                        @foreach($teachingGroups as $grp)
                            <option value="{{ $grp->id }}" {{ ($selectedGroup && $selectedGroup->id === $grp->id) ? 'selected' : '' }}>
                                [{{ $grp->code }}] {{ $grp->name }} ({{ $grp->classes->pluck('name')->implode(', ') }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div style="display: flex; gap: 0.5rem; flex-wrap: wrap; padding-top: 1.1rem;">
                    <span class="vx-badge badge-primary">
                        <i class='bx bx-git-merge'></i> Tabel Khusus Lintas Rombel
                    </span>
                    <span class="vx-badge badge-info">
                        <i class='bx bx-id-card'></i> Otomatis Baca Biodata Agama Siswa
                    </span>
                </div>
            </div>

            <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                <button type="button" class="btn btn-secondary btn-sm" onclick="openCreateTeachingGroupModal()">
                    <i class='bx bx-layer-plus'></i> + Kelompok Mapel / Agama
                </button>
                <button type="button" class="btn btn-primary btn-sm" onclick="openAssignCombinedModal()">
                    <i class='bx bx-plus-circle'></i> + Jadwalkan Kelas Gabungan
                </button>
            </div>
        </div>

        <div style="padding: 1rem 1.25rem;">
            @if(count($periodRows) === 0)
                <div style="text-align: center; padding: 2.5rem 1rem; color: var(--text-muted);">
                    Kategori jadwal ini belum memiliki sesi jam pelajaran.
                </div>
            @else
                <div class="matrix-table-wrap">
                    <table class="matrix-table" id="combinedMatrixTable">
                        <thead>
                            <tr>
                                <th style="width: 13%;">Sesi / Jam KBM</th>
                                @foreach($daysList as $dNum => $dName)
                                    <th>{{ $dName }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($periodRows as $rowKey => $rowInfo)
                                @if($rowInfo['is_break'])
                                    <tr class="break-row">
                                        <td class="period-col">{{ $rowInfo['name'] }}</td>
                                        @foreach($daysList as $dNum => $dName)
                                            <td><i class='bx bx-coffee'></i> {{ $rowInfo['name'] }} ({{ $rowInfo['start_time'] }} - {{ $rowInfo['end_time'] }})</td>
                                        @endforeach
                                    </tr>
                                @else
                                    <tr>
                                        <td class="period-col">
                                            <div class="vx-badge badge-dark cell-mono" style="margin-bottom: 0.25rem;">
                                                {{ $rowInfo['period_number'] ? 'Jam Ke-'.$rowInfo['period_number'] : $rowInfo['name'] }}
                                            </div>
                                            <div class="cell-mono" style="font-size: 0.72rem; color: var(--text-muted);">
                                                {{ $rowInfo['start_time'] }} - {{ $rowInfo['end_time'] }}
                                            </div>
                                        </td>
                                        @foreach($daysList as $dNum => $dName)
                                            @php
                                                $cmbCell = $combinedMergedCells[$dNum][$rowKey] ?? ['skip' => false, 'rowspan' => 1, 'entries' => [], 'period' => null];
                                            @endphp
                                            @if(empty($cmbCell['skip']))
                                                @php
                                                    $cmbPeriod = $cmbCell['period'] ?? null;
                                                    $cmbEndPeriod = $cmbCell['end_period'] ?? $cmbPeriod;
                                                    $cmbEntries = $cmbCell['entries'] ?? [];
                                                    $cmbSpan = $cmbCell['rowspan'] ?? 1;
                                                    $singleEntry = count($cmbEntries) === 1 ? $cmbEntries[0]['schedule'] : null;
                                                    $singleColor = $singleEntry ? ($singleEntry->subject?->resolved_color ?? '#8b5cf6') : null;
                                                @endphp
                                                <td class="{{ $cmbSpan > 1 ? 'merged-cell' : '' }} {{ count($cmbEntries) > 0 ? 'has-subject-slot' : '' }}"
                                                    @if($cmbSpan > 1) rowspan="{{ $cmbSpan }}" @endif
                                                    @if($singleColor) style="background-color: {{ $singleColor }}24; border-left: 4px solid {{ $singleColor }};" @endif>
                                                    @if(!$cmbPeriod)
                                                        <div style="text-align: center; padding: 1.2rem 0.25rem; color: var(--text-placeholder); font-size: 0.72rem;">—</div>
                                                    @elseif(count($cmbEntries) > 0)
                                                        <div style="display: flex; flex-direction: column; gap: 0.45rem; height: 100%; justify-content: center; align-items: center;">
                                                            @foreach($cmbEntries as $entry)
                                                                @php
                                                                    $cItem = $entry['schedule'];
                                                                    $cIdsCsv = implode(',', $entry['schedule_ids']);
                                                                    $cColor = $cItem->subject?->resolved_color ?? '#8b5cf6';
                                                                    $cRoom = $cItem->room?->name ?? $cItem->room_name ?? 'Ruang Bersama';
                                                                    $cReligion = $cItem->effective_religion_filter;
                                                                    $cClassIdsCsv = implode(',', $cItem->participating_class_ids);
                                                                @endphp
                                                                <div class="slot-card {{ $cmbSpan > 1 ? 'slot-merged' : '' }}"
                                                                    @if(count($cmbEntries) > 1)
                                                                        style="width: 100%; padding: 0.5rem; border-radius: var(--radius-md); background-color: {{ $cColor }}24; border-left: 4px solid {{ $cColor }};"
                                                                    @endif>
                                                                    <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 0.25rem; width: 100%;">
                                                                        <div style="display: flex; justify-content: center; align-items: center; gap: 0.35rem; flex-wrap: wrap;">
                                                                            <span class="slot-subject" style="{{ $cmbSpan > 1 ? 'font-size: 0.88rem;' : '' }}">{{ $cItem->subject?->name ?? '-' }}</span>
                                                                            <span class="vx-badge cell-mono" style="background: {{ $cColor }}; color: #fff; font-size: 0.62rem; padding: 1px 6px;">
                                                                                {{ $cItem->subject?->code }}
                                                                            </span>
                                                                        </div>
                                                                        <div class="slot-meta">
                                                                            <i class='bx bx-user-voice' style="color: {{ $cColor }};"></i>
                                                                            <span>{{ $cItem->teacher?->full_name ?? '-' }}{{ $cItem->teacher?->title_suffix ? ', '.$cItem->teacher->title_suffix : '' }}</span>
                                                                        </div>
                                                                        <span class="vx-badge badge-primary" style="font-size: 0.65rem;">
                                                                            <i class='bx bx-git-merge'></i> {{ $cItem->participating_class_names }}
                                                                        </span>
                                                                        @if($cReligion)
                                                                            <span class="vx-badge badge-info" style="font-size: 0.63rem;">
                                                                                <i class='bx bx-id-card'></i> Biodata: {{ $cReligion }}
                                                                            </span>
                                                                        @elseif($cItem->teachingGroup)
                                                                            <span class="vx-badge badge-warning" style="font-size: 0.63rem;">
                                                                                <i class='bx bx-layer'></i> {{ $cItem->teachingGroup->code }}
                                                                            </span>
                                                                        @endif
                                                                    </div>
                                                                    <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 0.25rem; margin-top: 0.2rem; width: 100%;">
                                                                        <span class="slot-room-pill">
                                                                            <i class='bx bx-door-open'></i> {{ $cRoom }}
                                                                        </span>
                                                                        <div class="slot-actions">
                                                                            <button type="button" class="btn btn-secondary btn-xs btn-icon-sm" title="Lihat Siswa Peserta Kelas Gabungan"
                                                                                onclick="openScheduleParticipantsModal({{ $cItem->id }})">
                                                                                <i class='bx bx-group'></i>
                                                                            </button>
                                                                            @if($cmbSpan > 1)
                                                                                <button type="button" class="btn btn-secondary btn-xs" style="font-size: 0.65rem; padding: 2px 5px;"
                                                                                    title="Kurangi 1 JP Terakhir"
                                                                                    onclick="deleteScheduleSlot({{ $entry['last_schedule_id'] }}, '', false)">
                                                                                    -1 JP
                                                                                </button>
                                                                            @endif
                                                                            <button type="button" class="btn btn-secondary btn-xs btn-icon-sm" title="Edit Jadwal Kelas Gabungan"
                                                                                onclick="openEditSlotModal({{ $cItem->id }}, '{{ addslashes($dName.' — '.($cmbCell['range_label'] ?? $cmbPeriod->name)) }}', {{ $cItem->subject_id }}, {{ $cItem->teacher_id }}, '{{ $cItem->room_id ?? '' }}', '{{ addslashes($cItem->room_name ?? '') }}', '{{ addslashes($cItem->notes ?? '') }}', '{{ $cIdsCsv }}', {{ $dNum }}, {{ $cmbPeriod->id }}, {{ $cmbEndPeriod?->id ?? $cmbPeriod->id }}, '{{ $cClassIdsCsv }}')">
                                                                                <i class='bx bx-edit-alt'></i>
                                                                            </button>
                                                                            <button type="button" class="btn btn-danger-light btn-xs btn-icon-sm" title="Hapus Blok Kelas Gabungan"
                                                                                onclick="deleteScheduleSlot({{ $cItem->id }}, '{{ $cIdsCsv }}', {{ $cmbSpan > 1 ? 'true' : 'false' }})">
                                                                                <i class='bx bx-trash'></i>
                                                                            </button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    @else
                                                        <button type="button" class="slot-empty-btn"
                                                            onclick="openAssignCombinedSlotModal({{ $dNum }}, '{{ $dName }}', {{ $cmbPeriod->id }}, '{{ addslashes($cmbPeriod->name) }}')">
                                                            <i class='bx bx-git-merge' style="font-size: 1.05rem;"></i>
                                                            <span>+ Kelas Gabungan</span>
                                                        </button>
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

    <!-- Master Kelompok Mapel & Kelas Gabungan (Filter Biodata Agama / Mapel Pilihan) -->
    <div class="apex-card">
        <div class="apex-card-header">
            <div>
                <h2 class="apex-card-title">Master Kelompok Mapel & Kelas Gabungan Lintas Rombel</h2>
                <p class="apex-card-desc">
                    Siapkan kelompok mata pelajaran lintas kelas. Untuk mapel Pendidikan Agama, sistem otomatis menjaring siswa sesuai agama pada Biodata Siswa.
                </p>
            </div>
            <button type="button" class="btn btn-primary btn-sm" onclick="openCreateTeachingGroupModal()">
                <i class='bx bx-plus-circle'></i> Buat Kelompok Mapel Baru
            </button>
        </div>
        <div class="table-responsive">
            <table class="vx-table">
                <thead>
                    <tr>
                        <th style="width: 115px;">Kode</th>
                        <th>Nama Kelompok Mapel</th>
                        <th>Mata Pelajaran & Guru</th>
                        <th>Rombel Tergabung</th>
                        <th>Filter Peserta Siswa</th>
                        <th style="text-align: center;">Siswa Terjaring</th>
                        <th style="text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($teachingGroups as $grp)
                        @php
                            $grpResolvedStudents = $grp->resolveStudents();
                            $grpClassIdsJson = json_encode($grp->classes->pluck('id')->values());
                            $grpStudentIdsJson = json_encode($grp->manualStudents->pluck('id')->values());
                            $grpEffRel = $grp->religion_filter ?: $grp->subject?->resolved_religion_filter;
                        @endphp
                        <tr>
                            <td><span class="vx-badge badge-dark cell-mono">{{ $grp->code }}</span></td>
                            <td>
                                <div class="cell-title">{{ $grp->name }}</div>
                                @if($grp->description)
                                    <div class="cell-sub">{{ $grp->description }}</div>
                                @endif
                            </td>
                            <td>
                                <div class="cell-title">{{ $grp->subject?->name ?? '-' }}</div>
                                <div class="cell-sub">{{ $grp->teacher?->full_name ?? 'Belum diatur' }} &bull; {{ $grp->defaultRoom?->name ?? 'Ruang fleksibel' }}</div>
                            </td>
                            <td>
                                <div style="display: flex; gap: 0.25rem; flex-wrap: wrap;">
                                    @foreach($grp->classes as $gCls)
                                        <span class="vx-badge badge-primary">{{ $gCls->name }}</span>
                                    @endforeach
                                </div>
                            </td>
                            <td>
                                @if($grp->membership_mode === 'RELIGION')
                                    <span class="vx-badge badge-info">
                                        <i class='bx bx-id-card'></i> Biodata Agama: {{ $grpEffRel ?? 'Otomatis Mapel' }}
                                    </span>
                                @elseif($grp->membership_mode === 'MANUAL')
                                    <span class="vx-badge badge-warning">
                                        <i class='bx bx-list-check'></i> Kelompok Pilihan Manual
                                    </span>
                                @else
                                    <span class="vx-badge badge-success">
                                        <i class='bx bx-group'></i> Seluruh Siswa Rombel
                                    </span>
                                @endif
                            </td>
                            <td style="text-align: center;">
                                <button type="button" class="btn btn-secondary btn-xs" onclick="openTeachingGroupStudentsModal({{ $grp->id }})">
                                    <i class='bx bx-user-check'></i> {{ $grpResolvedStudents->count() }} Siswa
                                </button>
                            </td>
                            <td style="text-align: right;">
                                <div style="display: flex; justify-content: flex-end; gap: 0.35rem;">
                                    <button type="button" class="btn btn-primary btn-xs"
                                        onclick="openAssignCombinedForGroup({{ $grp->id }})">
                                        <i class='bx bx-calendar-plus'></i> Jadwalkan
                                    </button>
                                    <button type="button" class="btn btn-secondary btn-xs btn-icon-sm" title="Edit Kelompok"
                                        onclick="openEditTeachingGroupModal({{ $grp->id }}, '{{ addslashes($grp->code) }}', '{{ addslashes($grp->name) }}', '{{ $grp->subject_id ?? '' }}', '{{ $grp->teacher_id ?? '' }}', '{{ $grp->default_room_id ?? '' }}', '{{ $grp->membership_mode }}', '{{ addslashes($grp->religion_filter ?? '') }}', '{{ addslashes($grp->description ?? '') }}', {{ $grpClassIdsJson }}, {{ $grpStudentIdsJson }})">
                                        <i class='bx bx-edit'></i>
                                    </button>
                                    <button type="button" class="btn btn-danger-light btn-xs btn-icon-sm" title="Hapus Kelompok"
                                        onclick="deleteTeachingGroup({{ $grp->id }}, '{{ addslashes($grp->name) }}')">
                                        <i class='bx bx-trash'></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 2rem; color: var(--text-muted); font-size: 0.84rem;">
                                Belum ada kelompok mapel / kelas gabungan terdaftar. Klik <strong>Buat Kelompok Mapel Baru</strong> untuk membuat.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- =========================================================================
     VIEW 2: JADWAL BERDASARKAN GURU (TEACHER SCHEDULE & TEACHING LOAD)
========================================================================= -->
<div id="view-teacher" class="sched-view-pane">
    <div class="apex-card">
        <div class="apex-card-header" style="flex-wrap: wrap; gap: 1rem;">
            <div style="display: flex; align-items: center; gap: 1rem; flex-wrap: wrap;">
                <div>
                    <label class="vx-label" style="margin-bottom: 0.25rem; font-size: 0.72rem; text-transform: uppercase;">Pilih Guru / Pendidik (GTK)</label>
                    <select class="vx-input" style="min-width: 280px; font-weight: 700;" onchange="changeFilterParam('teacher_id', this.value, 'view-teacher')">
                        @foreach($teachers as $tch)
                            <option value="{{ $tch->id }}" {{ ($selectedTeacher && $selectedTeacher->id === $tch->id) ? 'selected' : '' }}>
                                {{ $tch->full_name }}{{ $tch->title_suffix ? ', '.$tch->title_suffix : '' }} (NIP: {{ $tch->nip ?? '-' }})
                            </option>
                        @endforeach
                    </select>
                </div>
                @if($selectedTeacher)
                    @php
                        $teacherTotalJp = $teacherSchedulesByPeriod->count();
                        $teacherClassesCount = $teacherSchedulesByPeriod->flatMap(fn($s) => $s->participating_class_ids)->unique()->count();
                    @endphp
                    <div style="display: flex; gap: 0.5rem; flex-wrap: wrap; padding-top: 1.1rem;">
                        <span class="vx-badge {{ $teacherTotalJp >= 24 ? 'badge-success' : 'badge-primary' }}" style="font-size: 0.8rem; padding: 0.35rem 0.75rem;">
                            <i class='bx bx-time-five'></i> Beban Tatap Muka: <strong>{{ $teacherTotalJp }} JP / Minggu</strong>
                        </span>
                        <span class="vx-badge badge-info" style="font-size: 0.8rem; padding: 0.35rem 0.75rem;">
                            <i class='bx bxs-school'></i> Mengajar {{ $teacherClassesCount }} Rombel
                        </span>
                    </div>
                @endif
            </div>
        </div>

        <div style="padding: 1rem 1.25rem;">
            <div class="matrix-table-wrap">
                <table class="matrix-table">
                    <thead>
                        <tr>
                            <th style="width: 13%;">Sesi / Jam KBM</th>
                            @foreach($daysList as $dNum => $dName)
                                <th>{{ $dName }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($periodRows as $rowKey => $rowInfo)
                            @if($rowInfo['is_break'])
                                <tr class="break-row">
                                    <td class="period-col">{{ $rowInfo['name'] }}</td>
                                    @foreach($daysList as $dNum => $dName)
                                        <td><i class='bx bx-coffee'></i> {{ $rowInfo['name'] }}</td>
                                    @endforeach
                                </tr>
                            @else
                                <tr>
                                    <td class="period-col">
                                        <div class="vx-badge badge-dark cell-mono" style="margin-bottom: 0.2rem;">
                                            {{ $rowInfo['period_number'] ? 'Jam Ke-'.$rowInfo['period_number'] : $rowInfo['name'] }}
                                        </div>
                                        <div class="cell-mono" style="font-size: 0.72rem; color: var(--text-muted);">
                                            {{ $rowInfo['start_time'] }} - {{ $rowInfo['end_time'] }}
                                        </div>
                                    </td>
                                    @foreach($daysList as $dNum => $dName)
                                        @php
                                            $tCell = $teacherMergedCells[$dNum][$rowKey] ?? ['skip' => false, 'rowspan' => 1, 'schedule' => null];
                                        @endphp
                                        @if(empty($tCell['skip']))
                                            @php
                                                $tPeriod = $tCell['period'] ?? null;
                                                $tEndPeriod = $tCell['end_period'] ?? $tPeriod;
                                                $tSched = $tCell['schedule'] ?? null;
                                                $tSpan = $tCell['rowspan'] ?? 1;
                                                $tIdsJson = isset($tCell['schedule_ids']) ? implode(',', $tCell['schedule_ids']) : ($tSched?->id ?? '');
                                                $tRoomName = $tSched ? ($tSched->room?->name ?? $tSched->room_name ?? 'R. Kelas') : '';
                                                $tSubjColor = $tSched ? ($tSched->subject?->resolved_color ?? '#4680ff') : null;
                                            @endphp
                                            <td class="{{ $tSpan > 1 ? 'merged-cell' : '' }} {{ $tSched ? 'has-subject-slot' : '' }}"
                                                @if($tSpan > 1) rowspan="{{ $tSpan }}" @endif
                                                @if($tSched) style="background-color: {{ $tSubjColor }}24; border-left: 4px solid {{ $tSubjColor }};" @endif>
                                                @if($tSched)
                                                    <div class="slot-card slot-teacher-view {{ $tSpan > 1 ? 'slot-merged' : '' }}">
                                                        <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 0.25rem; width: 100%;">
                                                            <div style="display: flex; justify-content: center; align-items: center; gap: 0.3rem; flex-wrap: wrap;">
                                                                <span class="vx-badge badge-primary" style="font-weight: 800;">
                                                                    <i class='bx {{ $tSched->is_combined ? "bx-git-merge" : "bxs-school" }}'></i>
                                                                    {{ $tSched->is_combined ? 'Gabungan: '.$tSched->participating_class_names : 'Kelas '.($tSched->schoolClass?->name ?? '-') }}
                                                                </span>
                                                                <span class="vx-badge cell-mono" style="background: {{ $tSubjColor }}; color: #fff; font-size: 0.62rem; padding: 1px 5px;">
                                                                    {{ $tSched->subject?->code }}
                                                                </span>
                                                            </div>
                                                            <div class="slot-subject" style="{{ $tSpan > 1 ? 'font-size: 0.9rem;' : '' }}">
                                                                {{ $tSched->subject?->name ?? '-' }}
                                                            </div>
                                                            <div class="slot-meta">
                                                                <i class='bx bx-user-voice' style="color: {{ $tSubjColor }};"></i>
                                                                <span>{{ $tSched->teacher?->full_name ?? '-' }}{{ $tSched->teacher?->title_suffix ? ', '.$tSched->teacher->title_suffix : '' }}</span>
                                                            </div>
                                                        </div>
                                                        <div style="display: flex; justify-content: center; align-items: center; gap: 0.35rem; margin-top: 0.2rem; flex-wrap: wrap;">
                                                            <span class="slot-room-pill">
                                                                <i class='bx bx-door-open'></i> {{ $tRoomName }}
                                                            </span>
                                                            @if($tPeriod)
                                                            @php
                                                                $tClassIdsCsv = $tSched->is_combined ? implode(',', $tSched->participating_class_ids) : '';
                                                            @endphp
                                                            <button type="button" class="btn btn-secondary btn-xs btn-icon-sm" title="Edit Jadwal & Jam"
                                                                onclick="openEditSlotModal({{ $tSched->id }}, '{{ addslashes($dName.' — '.($tCell['range_label'] ?? $tPeriod->name)) }}', {{ $tSched->subject_id }}, {{ $tSched->teacher_id }}, '{{ $tSched->room_id ?? '' }}', '{{ addslashes($tSched->room_name ?? '') }}', '{{ addslashes($tSched->notes ?? '') }}', '{{ $tIdsJson }}', {{ $dNum }}, {{ $tPeriod->id }}, {{ $tEndPeriod?->id ?? $tPeriod->id }}, '{{ $tClassIdsCsv }}')">
                                                                <i class='bx bx-edit-alt'></i>
                                                            </button>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @else
                                                    <div style="text-align: center; padding: 1.35rem 0.25rem; color: var(--text-placeholder); font-size: 0.73rem;">
                                                        — Bebas Mengajar —
                                                    </div>
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
        </div>
    </div>
</div>

<!-- =========================================================================
     VIEW 3: JADWAL BERDASARKAN RUANG KELAS & LAB (ROOM OCCUPANCY MATRIX)
========================================================================= -->
<div id="view-room" class="sched-view-pane">
    <div class="apex-card">
        <div class="apex-card-header" style="flex-wrap: wrap; gap: 1rem;">
            <div style="display: flex; align-items: center; gap: 1rem; flex-wrap: wrap;">
                <div>
                    <label class="vx-label" style="margin-bottom: 0.25rem; font-size: 0.72rem; text-transform: uppercase;">Pilih Ruang Kelas / Laboratorium / Bengkel</label>
                    <select class="vx-input" style="min-width: 280px; font-weight: 700;" onchange="changeFilterParam('room_id', this.value, 'view-room')">
                        @foreach($rooms as $rm)
                            <option value="{{ $rm->id }}" {{ ($selectedRoom && $selectedRoom->id === $rm->id) ? 'selected' : '' }}>
                                [{{ $rm->code }}] {{ $rm->name }} — {{ $rm->type }} (Kapasitas {{ $rm->capacity }})
                            </option>
                        @endforeach
                    </select>
                </div>
                @if($selectedRoom)
                    @php
                        $roomUsedSlots = $roomSchedulesByPeriod->count();
                    @endphp
                    <div style="display: flex; gap: 0.5rem; flex-wrap: wrap; padding-top: 1.1rem;">
                        <span class="vx-badge badge-primary" style="font-size: 0.8rem; padding: 0.35rem 0.75rem;">
                            <i class='bx bx-buildings'></i> {{ $selectedRoom->building ?? 'Gedung Utama' }} ({{ $selectedRoom->floor ?? 'Lt. 1' }})
                        </span>
                        <span class="vx-badge badge-info" style="font-size: 0.8rem; padding: 0.35rem 0.75rem;">
                            <i class='bx bx-group'></i> Kapasitas: {{ $selectedRoom->capacity }} Kursi
                        </span>
                        <span class="vx-badge badge-warning" style="font-size: 0.8rem; padding: 0.35rem 0.75rem;">
                            <i class='bx bx-calendar-check'></i> Terpakai: {{ $roomUsedSlots }} Sesi JP / Minggu
                        </span>
                    </div>
                @endif
            </div>
        </div>

        <div style="padding: 1rem 1.25rem;">
            <div class="matrix-table-wrap">
                <table class="matrix-table">
                    <thead>
                        <tr>
                            <th style="width: 13%;">Sesi / Jam KBM</th>
                            @foreach($daysList as $dNum => $dName)
                                <th>{{ $dName }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($periodRows as $rowKey => $rowInfo)
                            @if($rowInfo['is_break'])
                                <tr class="break-row">
                                    <td class="period-col">{{ $rowInfo['name'] }}</td>
                                    @foreach($daysList as $dNum => $dName)
                                        <td><i class='bx bx-coffee'></i> {{ $rowInfo['name'] }}</td>
                                    @endforeach
                                </tr>
                            @else
                                <tr>
                                    <td class="period-col">
                                        <div class="vx-badge badge-dark cell-mono" style="margin-bottom: 0.2rem;">
                                            {{ $rowInfo['period_number'] ? 'Jam Ke-'.$rowInfo['period_number'] : $rowInfo['name'] }}
                                        </div>
                                        <div class="cell-mono" style="font-size: 0.72rem; color: var(--text-muted);">
                                            {{ $rowInfo['start_time'] }} - {{ $rowInfo['end_time'] }}
                                        </div>
                                    </td>
                                    @foreach($daysList as $dNum => $dName)
                                        @php
                                            $rCell = $roomMergedCells[$dNum][$rowKey] ?? ['skip' => false, 'rowspan' => 1, 'schedule' => null, 'period' => null];
                                        @endphp
                                        @if(empty($rCell['skip']))
                                            @php
                                                $cellPeriod = $rCell['period'] ?? null;
                                                $rEndPeriod = $rCell['end_period'] ?? $cellPeriod;
                                                $rSched = $rCell['schedule'] ?? null;
                                                $rSpan = $rCell['rowspan'] ?? 1;
                                                $rIdsJson = isset($rCell['schedule_ids']) ? implode(',', $rCell['schedule_ids']) : ($rSched?->id ?? '');
                                                $rRoomName = $rSched ? ($rSched->room?->name ?? $rSched->room_name ?? ($selectedRoom?->name ?? 'R. Kelas')) : '';
                                                $rSubjColor = $rSched ? ($rSched->subject?->resolved_color ?? '#4680ff') : null;
                                            @endphp
                                            <td class="{{ $rSpan > 1 ? 'merged-cell' : '' }} {{ $rSched ? 'has-subject-slot' : '' }}"
                                                @if($rSpan > 1) rowspan="{{ $rSpan }}" @endif
                                                @if($rSched) style="background-color: {{ $rSubjColor }}24; border-left: 4px solid {{ $rSubjColor }};" @endif>
                                                @if(!$cellPeriod)
                                                    <div style="text-align: center; padding: 1.2rem; color: var(--text-placeholder); font-size: 0.72rem;">—</div>
                                                @elseif($rSched)
                                                    <div class="slot-card slot-room-view {{ $rSpan > 1 ? 'slot-merged' : '' }}">
                                                        <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 0.25rem; width: 100%;">
                                                            <div style="display: flex; justify-content: center; align-items: center; gap: 0.3rem; flex-wrap: wrap;">
                                                                <span class="vx-badge badge-warning" style="font-weight: 800;">
                                                                    Dipakai: {{ $rSched->is_combined ? 'Gabungan '.$rSched->participating_class_names : ($rSched->schoolClass?->name ?? '-') }}
                                                                </span>
                                                                <span class="vx-badge cell-mono" style="background: {{ $rSubjColor }}; color: #fff; font-size: 0.62rem; padding: 1px 5px;">
                                                                    {{ $rSched->subject?->code }}
                                                                </span>
                                                            </div>
                                                            <div class="slot-subject" style="{{ $rSpan > 1 ? 'font-size: 0.9rem;' : '' }}">
                                                                {{ $rSched->subject?->name ?? '-' }}
                                                            </div>
                                                            <div class="slot-meta">
                                                                <i class='bx bx-user-voice' style="color: {{ $rSubjColor }};"></i>
                                                                <span>{{ $rSched->teacher?->full_name ?? '-' }}{{ $rSched->teacher?->title_suffix ? ', '.$rSched->teacher->title_suffix : '' }}</span>
                                                            </div>
                                                        </div>
                                                        <div style="display: flex; justify-content: center; align-items: center; gap: 0.35rem; margin-top: 0.2rem; flex-wrap: wrap;">
                                                            <span class="slot-room-pill">
                                                                <i class='bx bx-door-open'></i> {{ $rRoomName }}
                                                            </span>
                                                            @php
                                                                $rClassIdsCsv = $rSched->is_combined ? implode(',', $rSched->participating_class_ids) : '';
                                                            @endphp
                                                            <button type="button" class="btn btn-secondary btn-xs btn-icon-sm" title="Edit Jadwal & Jam"
                                                                onclick="openEditSlotModal({{ $rSched->id }}, '{{ addslashes($dName.' — '.($rCell['range_label'] ?? $cellPeriod->name)) }}', {{ $rSched->subject_id }}, {{ $rSched->teacher_id }}, '{{ $rSched->room_id ?? '' }}', '{{ addslashes($rSched->room_name ?? '') }}', '{{ addslashes($rSched->notes ?? '') }}', '{{ $rIdsJson }}', {{ $dNum }}, {{ $cellPeriod->id }}, {{ $rEndPeriod?->id ?? $cellPeriod->id }}, '{{ $rClassIdsCsv }}')">
                                                                <i class='bx bx-edit-alt'></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                @else
                                                    <div class="slot-available-box"
                                                        onclick="openAssignSlotWithRoomModal({{ $dNum }}, '{{ $dName }}', {{ $cellPeriod->id }}, '{{ addslashes($cellPeriod->name) }}', {{ $selectedRoom?->id ?? 'null' }})">
                                                        <i class='bx bx-check-circle' style="font-size: 1.05rem;"></i>
                                                        <span>Ruang Tersedia</span>
                                                        <span style="font-size: 0.65rem; opacity: 0.85;">+ Jadwalkan Pemakaian</span>
                                                    </div>
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
        </div>
    </div>
</div>

<!-- =========================================================================
     VIEW 4: KATEGORI JADWAL DINAMIS & MASA BERLAKU (UNTUK PERSIAPAN SEMESTER DEPAN)
========================================================================= -->
<div id="view-categories" class="sched-view-pane">
    <div class="apex-card">
        <div class="apex-card-header" style="flex-wrap: wrap; gap: 1rem;">
            <div>
                <h2 class="apex-card-title">Kategori Jadwal Dinamis & Pengaturan Masa Berlaku</h2>
                <p class="apex-card-desc">
                    Siapkan kategori jadwal untuk semester yang akan datang, jadwal khusus Ramadhan, atau pekan ujian lengkap dengan tanggal masa berlakunya.
                </p>
            </div>
            <button type="button" class="btn btn-primary btn-sm" onclick="openModal('modalAddScheduleCategory')">
                <i class='bx bx-plus-circle'></i> Tambah Kategori & Masa Berlaku
            </button>
        </div>

        <div style="overflow-x: auto;">
            <table class="clean-table">
                <thead>
                    <tr>
                        <th>Nama Kategori Jadwal</th>
                        <th>Kode</th>
                        <th>Target Tahun Ajaran / Smt</th>
                        <th>Masa Berlaku (Mulai s/d Selesai)</th>
                        <th>Status Masa Berlaku</th>
                        <th>Sesi Jam & Slot KBM</th>
                        <th style="text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($timeCategories as $cat)
                        @php
                            $vStatus = $cat->validity_status;
                        @endphp
                        <tr>
                            <td>
                                <div class="cell-title fw-700" style="color: var(--text-heading);">{{ $cat->name }}</div>
                                <div style="font-size: 0.75rem; color: var(--text-muted);">{{ $cat->description ?? 'Pola jadwal pelajaran KBM.' }}</div>
                            </td>
                            <td><span class="vx-badge badge-dark cell-mono">{{ $cat->code }}</span></td>
                            <td>
                                @if($cat->academicYear)
                                    <span class="vx-badge badge-primary">{{ $cat->academicYear->name }}</span>
                                    @if($cat->academic_semester)
                                        <span class="vx-badge badge-info">{{ $cat->academic_semester === 'odd' ? 'Ganjil' : 'Genap' }}</span>
                                    @endif
                                @else
                                    <span class="vx-badge badge-muted">Lintas Periode</span>
                                @endif
                            </td>
                            <td>
                                @if($cat->effective_start_date || $cat->effective_end_date)
                                    <div class="cell-mono fw-600" style="font-size: 0.82rem; color: var(--text-heading);">
                                        <i class='bx bx-calendar-event' style="color: var(--bento-primary);"></i>
                                        {{ $cat->effective_start_date ? $cat->effective_start_date->format('d/m/Y') : '...' }}
                                        &rarr;
                                        {{ $cat->effective_end_date ? $cat->effective_end_date->format('d/m/Y') : '...' }}
                                    </div>
                                @else
                                    <span style="color: var(--text-muted); font-size: 0.8rem;">Berlaku Reguler</span>
                                @endif
                            </td>
                            <td>
                                @if($vStatus === 'UPCOMING')
                                    <span class="vx-badge badge-warning"><i class='bx bx-time'></i> Persiapan (Semester Mendatang)</span>
                                @elseif($vStatus === 'ACTIVE_DEFAULT')
                                    <span class="vx-badge badge-success"><i class='bx bx-check-double'></i> Jadwal Utama Aktif</span>
                                @elseif($vStatus === 'ACTIVE_PERIOD')
                                    <span class="vx-badge badge-info"><i class='bx bx-calendar-check'></i> Dalam Masa Berlaku</span>
                                @elseif($vStatus === 'EXPIRED')
                                    <span class="vx-badge badge-muted">Masa Berlaku Berakhir</span>
                                @else
                                    <span class="vx-badge badge-muted">Siaga / Draft</span>
                                @endif
                            </td>
                            <td>
                                <span class="vx-badge badge-primary">{{ $cat->periods_count }} Sesi Jam</span>
                                <span class="vx-badge badge-info">{{ $cat->schedules_count }} Slot KBM</span>
                            </td>
                            <td style="text-align: right;">
                                <div style="display: flex; gap: 0.35rem; justify-content: flex-end; flex-wrap: wrap;">
                                    <a href="{{ route('admin.schedules', ['category_id' => $cat->id, 'academic_year_id' => $cat->academic_year_id ?? $selectedYear?->id, 'academic_semester' => $cat->academic_semester ?? $selectedSemester]) }}"
                                        class="btn btn-primary btn-xs">
                                        <i class='bx bx-grid-alt'></i> Buka Matriks
                                    </a>
                                    <button type="button" class="btn btn-secondary btn-xs"
                                        onclick="openEditScheduleCategoryModal({{ $cat->id }}, '{{ addslashes($cat->name) }}', '{{ addslashes($cat->code) }}', '{{ addslashes($cat->description ?? '') }}', '{{ $cat->effective_start_date ? $cat->effective_start_date->format('Y-m-d') : '' }}', '{{ $cat->effective_end_date ? $cat->effective_end_date->format('Y-m-d') : '' }}', '{{ $cat->academic_year_id ?? '' }}', '{{ $cat->academic_semester ?? '' }}', {{ $cat->is_default ? 1 : 0 }})">
                                        <i class='bx bx-edit'></i> Masa Berlaku
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- =========================================================================
     VIEW 5: MASTER RUANG KELAS & LABORATORIUM (CRUD SCHOOL ROOMS)
========================================================================= -->
<div id="view-rooms-master" class="sched-view-pane">
    <div class="apex-card">
        <div class="apex-card-header" style="flex-wrap: wrap; gap: 1rem;">
            <div>
                <h2 class="apex-card-title">Master Ruang Kelas, Laboratorium & Bengkel</h2>
                <p class="apex-card-desc">Kelola daftar ruangan untuk penjadwalan kelas reguler, moving class, dan pemakaian laboratorium.</p>
            </div>
            <button type="button" class="btn btn-primary btn-sm" onclick="openModal('modalAddRoom')">
                <i class='bx bx-plus'></i> Tambah Ruang Kelas / Lab
            </button>
        </div>

        <div style="overflow-x: auto;">
            <table class="clean-table">
                <thead>
                    <tr>
                        <th>Kode Ruang</th>
                        <th>Nama Ruangan</th>
                        <th>Jenis Ruangan</th>
                        <th>Gedung & Lantai</th>
                        <th>Kapasitas</th>
                        <th>Rombel Homebase</th>
                        <th>Jadwal Terdaftar</th>
                        <th style="text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rooms as $rm)
                        <tr>
                            <td><span class="vx-badge badge-dark cell-mono">{{ $rm->code }}</span></td>
                            <td>
                                <div class="fw-700" style="color: var(--text-heading);">{{ $rm->name }}</div>
                                @if($rm->description)
                                    <div style="font-size: 0.73rem; color: var(--text-muted);">{{ $rm->description }}</div>
                                @endif
                            </td>
                            <td>
                                @if($rm->type === 'CLASSROOM')
                                    <span class="vx-badge badge-primary">Ruang Kelas Teori</span>
                                @elseif($rm->type === 'LABORATORY')
                                    <span class="vx-badge badge-info">Laboratorium</span>
                                @elseif($rm->type === 'WORKSHOP')
                                    <span class="vx-badge badge-warning">Bengkel / Workshop SMK</span>
                                @elseif($rm->type === 'SPORTS')
                                    <span class="vx-badge badge-success">Olahraga / GOR</span>
                                @else
                                    <span class="vx-badge badge-muted">{{ $rm->type }}</span>
                                @endif
                            </td>
                            <td>{{ $rm->building ?? '-' }} {{ $rm->floor ? '('.$rm->floor.')' : '' }}</td>
                            <td><span class="fw-700">{{ $rm->capacity }}</span> Kursi</td>
                            <td>
                                @if($rm->classes->count() > 0)
                                    @foreach($rm->classes as $cHome)
                                        <span class="vx-badge badge-primary">{{ $cHome->name }}</span>
                                    @endforeach
                                @else
                                    <span style="color: var(--text-muted); font-size: 0.78rem;">Ruang Bersama / Umum</span>
                                @endif
                            </td>
                            <td><span class="fw-700">{{ $rm->schedules_count }}</span> sesi</td>
                            <td style="text-align: right;">
                                <div style="display: flex; gap: 0.35rem; justify-content: flex-end;">
                                    <button type="button" class="btn btn-secondary btn-xs"
                                        onclick="changeFilterParam('room_id', '{{ $rm->id }}', 'view-room')">
                                        <i class='bx bx-calendar'></i> Lihat Jadwal
                                    </button>
                                    <button type="button" class="btn btn-danger-light btn-xs btn-icon-sm"
                                        onclick="deleteRoom({{ $rm->id }}, '{{ addslashes($rm->name) }}')">
                                        <i class='bx bx-trash'></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="text-align: center; padding: 2.5rem; color: var(--text-muted);">
                                Belum ada data ruangan terdaftar.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>


<!-- =========================================================================
     MODALS
========================================================================= -->

<!-- MODAL 1: ASSIGN / TAMBAH SLOT JADWAL PELAJARAN (TUNGGAL & KELAS GABUNGAN) -->
<div class="vx-modal-backdrop modal-backdrop" id="modalAssignSchedule">
    <div class="vx-modal modal-dialog" style="max-width: 640px;">
        <div class="vx-modal-header modal-title-bar">
            <h3 class="vx-modal-title">
                <span class="modal-title-icon"><i class='bx bx-calendar-plus'></i></span>
                <span id="assignModalTitle">Set Jadwal Pelajaran Kelas</span>
            </h3>
            <button class="vx-modal-close close-btn" onclick="closeModal('modalAssignSchedule')">&times;</button>
        </div>
        <form id="formAssignSchedule" onsubmit="submitAssignSchedule(event)">
            <input type="hidden" name="school_id" value="{{ $school?->id ?? 1 }}">
            <input type="hidden" name="academic_year_id" value="{{ $selectedYear?->id }}">
            <input type="hidden" name="academic_semester" value="{{ $selectedSemester }}">
            <input type="hidden" name="category_id" value="{{ $selectedCategory?->id }}">

            <div class="vx-modal-body modal-content">
                <div class="vx-form-grid form-grid-2">
                    <div class="form-row span-2">
                        <label class="vx-label">Jenis Penempatan Jadwal *</label>
                        <select class="vx-input" id="assignScheduleMode" onchange="toggleAssignScheduleMode()">
                            <option value="single">Rombel Tunggal (Tabel Jadwal Reguler Kelas)</option>
                            <option value="combined">Kelas Gabungan Lintas Rombel (Tabel Khusus Kelas Gabungan)</option>
                        </select>
                    </div>

                    <!-- Mode Rombel Tunggal -->
                    <div class="form-row" id="assignSingleClassRow">
                        <label class="vx-label">Kelas / Rombel *</label>
                        <select class="vx-input" name="class_id" id="assignClassId" onchange="syncDefaultRoomForClass(this.value)">
                            @foreach($classes as $cls)
                                <option value="{{ $cls->id }}" data-default-room="{{ $cls->default_room_id }}"
                                    {{ ($selectedClass && $selectedClass->id === $cls->id) ? 'selected' : '' }}>
                                    {{ $cls->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Mode Kelas Gabungan: Pilih Kelompok Mapel (Opsional) -->
                    <div class="form-row span-2" id="assignCombinedGroupRow" style="display: none;">
                        <label class="vx-label">Gunakan Master Kelompok Mapel / Agama (Opsional)</label>
                        <select class="vx-input" id="assignTeachingGroupId" name="teaching_group_id" onchange="onAssignTeachingGroupChange(this.value)">
                            <option value="">— Pilih Manual Rombel & Mapel di Bawah —</option>
                            @foreach($teachingGroups as $grp)
                                <option value="{{ $grp->id }}"
                                    data-subject="{{ $grp->subject_id ?? '' }}"
                                    data-teacher="{{ $grp->teacher_id ?? '' }}"
                                    data-room="{{ $grp->default_room_id ?? '' }}"
                                    data-mode="{{ $grp->membership_mode }}"
                                    data-religion="{{ $grp->religion_filter ?? '' }}"
                                    data-classes="{{ $grp->classes->pluck('id')->implode(',') }}">
                                    [{{ $grp->code }}] {{ $grp->name }} ({{ $grp->classes->pluck('name')->implode(', ') }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Mode Kelas Gabungan: Pilih Multi Rombel -->
                    <div class="form-row span-2" id="assignCombinedClassesRow" style="display: none;">
                        <label class="vx-label">Pilih Rombel yang Digabung (Minimal 2 Kelas atau Sesuai Kelompok) *</label>
                        <div style="display: flex; flex-wrap: wrap; gap: 0.5rem; padding: 0.65rem 0.75rem; border: 1px solid var(--border-color); border-radius: var(--radius-md); background: var(--bg-subtle); max-height: 130px; overflow-y: auto;">
                            @foreach($classes as $cls)
                                <label style="display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.28rem 0.65rem; border: 1px solid var(--border-color); border-radius: 6px; background: var(--bg-card); font-size: 0.8rem; font-weight: 600; cursor: pointer;">
                                    <input type="checkbox" class="assign-combined-class-cb" value="{{ $cls->id }}">
                                    <span>{{ $cls->name }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="form-row" id="assignDayRow">
                        <label class="vx-label">Hari KBM *</label>
                        <select class="vx-input" id="assignDaySelect" onchange="filterPeriodOptionsByDay(this.value)" required>
                            @foreach($daysList as $dNum => $dName)
                                <option value="{{ $dNum }}">{{ $dName }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-row">
                        <label class="vx-label">Mulai Sesi Jam *</label>
                        <select class="vx-input" name="lesson_period_id" id="assignStartPeriodId" required>
                            @foreach($categoryPeriods->where('is_break', false) as $p)
                                <option value="{{ $p->id }}" data-day="{{ $p->day_of_week }}">
                                    [{{ $daysList[$p->day_of_week] ?? $p->day_of_week }}] {{ $p->name }} ({{ substr($p->start_time, 0, 5) }} - {{ substr($p->end_time, 0, 5) }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-row">
                        <label class="vx-label">Sampai Sesi Jam (Opsional Rentang)</label>
                        <select class="vx-input" name="end_lesson_period_id" id="assignEndPeriodId">
                            <option value="">— Hanya 1 Sesi Jam Ini —</option>
                            @foreach($categoryPeriods->where('is_break', false) as $p)
                                <option value="{{ $p->id }}" data-day="{{ $p->day_of_week }}">
                                    Sampai {{ $p->name }} (Selesai {{ substr($p->end_time, 0, 5) }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-row span-2">
                        <label class="vx-label">Mata Pelajaran *</label>
                        <select class="vx-input" name="subject_id" id="assignSubjectId" required onchange="onAssignSubjectChange()">
                            <option value="" data-religion="">— Pilih Mata Pelajaran —</option>
                            @foreach($subjects as $subj)
                                <option value="{{ $subj->id }}" data-religion="{{ $subj->resolved_religion_filter ?? '' }}">
                                    [{{ $subj->code }}] {{ $subj->name }}{{ $subj->resolved_religion_filter ? ' — (Filter Biodata: '.$subj->resolved_religion_filter.')' : '' }}
                                </option>
                            @endforeach
                        </select>
                        <div id="assignReligionBiodataNotice" style="display: none; margin-top: 0.45rem; padding: 0.5rem 0.75rem; border-radius: var(--radius-md); background: var(--vx-info-light, #e0f2fe); border: 1px solid #bae6fd; font-size: 0.78rem; color: #0369a1;">
                            <i class='bx bx-id-card'></i>
                            Mapel Agama terdeteksi: Sistem otomatis membaca <strong>Biodata Siswa</strong> dan hanya mengikutsertakan siswa beragama <strong id="assignReligionBiodataLabel"></strong>.
                        </div>
                    </div>

                    <!-- Filter Peserta Siswa Tambahan untuk Kelas Gabungan -->
                    <div class="form-row" id="assignFilterTypeRow" style="display: none;">
                        <label class="vx-label">Filter Peserta Siswa</label>
                        <select class="vx-input" id="assignStudentFilterType" onchange="onAssignFilterTypeChange()">
                            <option value="AUTO">Otomatis Ikuti Mapel / Kelompok Mapel</option>
                            <option value="RELIGION">Filter Biodata Agama Spesifik</option>
                            <option value="ALL">Seluruh Siswa Rombel Tergabung</option>
                        </select>
                    </div>
                    <div class="form-row" id="assignFilterReligionRow" style="display: none;">
                        <label class="vx-label">Agama pada Biodata Siswa</label>
                        <select class="vx-input" id="assignStudentFilterReligion">
                            <option value="Islam">Islam</option>
                            <option value="Kristen">Kristen</option>
                            <option value="Katolik">Katolik</option>
                            <option value="Hindu">Hindu</option>
                            <option value="Buddha">Buddha</option>
                            <option value="Konghucu">Konghucu</option>
                        </select>
                    </div>

                    <div class="form-row span-2">
                        <label class="vx-label">Guru Pengajar (GTK) *</label>
                        <select class="vx-input" name="teacher_id" id="assignTeacherId" required>
                            <option value="">— Pilih Guru Pengajar —</option>
                            @foreach($teachers as $tch)
                                <option value="{{ $tch->id }}">
                                    {{ $tch->full_name }}{{ $tch->title_suffix ? ', '.$tch->title_suffix : '' }} ({{ $tch->major_study ?? 'Guru Mapel' }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-row">
                        <label class="vx-label">Ruang Kelas / Laboratorium</label>
                        <select class="vx-input" name="room_id" id="assignRoomId">
                            <option value="">— Ikuti Ruang Default Kelas —</option>
                            @foreach($rooms as $rm)
                                <option value="{{ $rm->id }}" {{ ($selectedClass && $selectedClass->default_room_id === $rm->id) ? 'selected' : '' }}>
                                    [{{ $rm->code }}] {{ $rm->name }} ({{ $rm->type }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-row">
                        <label class="vx-label">Catatan / Keterangan</label>
                        <input type="text" class="vx-input" name="notes" placeholder="Misal: Kelas Gabungan Paralel / Praktik">
                    </div>
                    <div class="form-row span-2">
                        <label style="display: inline-flex; align-items: center; gap: 0.5rem; font-size: 0.82rem; cursor: pointer;">
                            <input type="checkbox" name="replace_existing" value="1">
                            <span>Timpa otomatis jika slot jam pada kelas/kelompok ini sudah terisi</span>
                        </label>
                    </div>
                </div>
            </div>
            <div class="vx-modal-footer modal-bottom">
                <button type="button" class="btn btn-secondary" onclick="closeModal('modalAssignSchedule')">Batal</button>
                <button type="submit" class="btn btn-primary"><i class='bx bx-check'></i> Simpan Jadwal</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL 2: EDIT SLOT JADWAL (MAPEL, GURU, RUANG & RENTANG JAM PELAJARAN) -->
<div class="vx-modal-backdrop modal-backdrop" id="modalEditScheduleSlot">
    <div class="vx-modal modal-dialog" style="max-width: 620px;">
        <div class="vx-modal-header modal-title-bar">
            <h3 class="vx-modal-title">
                <span class="modal-title-icon"><i class='bx bx-edit'></i></span>
                Ubah Jadwal & Jam: <span id="editSlotPeriodLabel"></span>
            </h3>
            <button class="vx-modal-close close-btn" onclick="closeModal('modalEditScheduleSlot')">&times;</button>
        </div>
        <form id="formEditScheduleSlot" onsubmit="submitEditScheduleSlot(event)">
            <input type="hidden" id="editScheduleId">
            <input type="hidden" id="editScheduleIdsCsv">
            <input type="hidden" id="editSlotIsCombined" value="0">
            <div class="vx-modal-body modal-content">
                <div class="vx-form-grid form-grid-2">
                    <div class="form-row span-2" id="editCombinedClassesRow" style="display: none;">
                        <label class="vx-label">Rombel yang Digabung pada Sesi Ini *</label>
                        <div style="display: flex; flex-wrap: wrap; gap: 0.5rem; padding: 0.65rem 0.75rem; border: 1px solid var(--border-color); border-radius: var(--radius-md); background: var(--bg-subtle);">
                            @foreach($classes as $cls)
                                <label style="display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.25rem 0.6rem; border: 1px solid var(--border-color); border-radius: 6px; background: var(--bg-card); font-size: 0.8rem; font-weight: 600; cursor: pointer;">
                                    <input type="checkbox" class="edit-combined-class-cb" value="{{ $cls->id }}">
                                    <span>{{ $cls->name }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                    <div class="form-row span-2">
                        <label class="vx-label">Hari KBM *</label>
                        <select class="vx-input" id="editSlotDaySelect" onchange="filterEditPeriodOptionsByDay(this.value)" required>
                            @foreach($daysList as $dNum => $dName)
                                <option value="{{ $dNum }}">{{ $dName }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-row">
                        <label class="vx-label">Mulai Sesi Jam *</label>
                        <select class="vx-input" name="lesson_period_id" id="editSlotStartPeriodId" required>
                            @foreach($categoryPeriods->where('is_break', false) as $p)
                                <option value="{{ $p->id }}" data-day="{{ $p->day_of_week }}">
                                    [{{ $daysList[$p->day_of_week] ?? $p->day_of_week }}] {{ $p->name }} ({{ substr($p->start_time, 0, 5) }} - {{ substr($p->end_time, 0, 5) }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-row">
                        <label class="vx-label">Sampai Sesi Jam (Rentang JP)</label>
                        <select class="vx-input" name="end_lesson_period_id" id="editSlotEndPeriodId">
                            <option value="">— Hanya 1 Sesi Jam —</option>
                            @foreach($categoryPeriods->where('is_break', false) as $p)
                                <option value="{{ $p->id }}" data-day="{{ $p->day_of_week }}">
                                    Sampai {{ $p->name }} (Selesai {{ substr($p->end_time, 0, 5) }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-row span-2">
                        <label class="vx-label">Mata Pelajaran *</label>
                        <select class="vx-input" name="subject_id" id="editSlotSubjectId" required>
                            @foreach($subjects as $subj)
                                <option value="{{ $subj->id }}">
                                    [{{ $subj->code }}] {{ $subj->name }}{{ $subj->resolved_religion_filter ? ' (Biodata: '.$subj->resolved_religion_filter.')' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-row span-2">
                        <label class="vx-label">Guru Pengajar *</label>
                        <select class="vx-input" name="teacher_id" id="editSlotTeacherId" required>
                            @foreach($teachers as $tch)
                                <option value="{{ $tch->id }}">{{ $tch->full_name }}{{ $tch->title_suffix ? ', '.$tch->title_suffix : '' }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-row">
                        <label class="vx-label">Ruang Kelas / Lab</label>
                        <select class="vx-input" name="room_id" id="editSlotRoomId">
                            <option value="">— Tanpa Ruangan Spesifik —</option>
                            @foreach($rooms as $rm)
                                <option value="{{ $rm->id }}">[{{ $rm->code }}] {{ $rm->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-row">
                        <label class="vx-label">Catatan</label>
                        <input type="text" class="vx-input" name="notes" id="editSlotNotes">
                    </div>
                    <div class="form-row span-2">
                        <label style="display: inline-flex; align-items: center; gap: 0.5rem; font-size: 0.82rem; cursor: pointer;">
                            <input type="checkbox" name="replace_existing" id="editSlotReplaceExisting" value="1">
                            <span>Timpa otomatis jika jam tujuan pada kelas ini sudah terisi</span>
                        </label>
                    </div>
                </div>
            </div>
            <div class="vx-modal-footer modal-bottom">
                <button type="button" class="btn btn-secondary" onclick="closeModal('modalEditScheduleSlot')">Batal</button>
                <button type="submit" class="btn btn-primary"><i class='bx bx-save'></i> Simpan Perubahan Jadwal & Jam</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL 2B: EDIT WAKTU SESI JAM PELAJARAN (START TIME & END TIME) -->
<div class="vx-modal-backdrop modal-backdrop" id="modalEditPeriodTime">
    <div class="vx-modal modal-dialog">
        <div class="vx-modal-header modal-title-bar">
            <h3 class="vx-modal-title">
                <span class="modal-title-icon"><i class='bx bx-time-five'></i></span>
                Ubah Waktu Sesi Jam Pelajaran
            </h3>
            <button class="vx-modal-close close-btn" onclick="closeModal('modalEditPeriodTime')">&times;</button>
        </div>
        <form id="formEditPeriodTime" onsubmit="submitEditPeriodTime(event)">
            <input type="hidden" id="editPeriodId">
            <div class="vx-modal-body modal-content">
                <div class="vx-form-grid form-grid-2">
                    <div class="form-row">
                        <label class="vx-label">Nama Sesi Jam *</label>
                        <input type="text" class="vx-input" id="editPeriodName" name="name" required>
                    </div>
                    <div class="form-row">
                        <label class="vx-label">Nomor Urut Jam (Kosong jika Istirahat)</label>
                        <input type="number" class="vx-input" id="editPeriodNumber" name="period_number" min="1">
                    </div>
                    <div class="form-row">
                        <label class="vx-label">Waktu Mulai (HH:MM) *</label>
                        <input type="time" class="vx-input" id="editPeriodStartTime" name="start_time" required>
                    </div>
                    <div class="form-row">
                        <label class="vx-label">Waktu Selesai (HH:MM) *</label>
                        <input type="time" class="vx-input" id="editPeriodEndTime" name="end_time" required>
                    </div>
                    <div class="form-row span-2">
                        <label class="vx-label">Tipe Sesi</label>
                        <select class="vx-input" id="editPeriodIsBreak" name="is_break">
                            <option value="0">Jam Pelajaran (KBM)</option>
                            <option value="1">Jam Istirahat / Non-KBM</option>
                        </select>
                    </div>
                    <div class="form-row span-2">
                        <label style="display: inline-flex; align-items: center; gap: 0.5rem; font-size: 0.82rem; cursor: pointer;">
                            <input type="checkbox" id="editPeriodApplyAllDays" checked>
                            <span>Terapkan perubahan waktu jam ini untuk seluruh hari (Senin–Sabtu) pada kategori ini</span>
                        </label>
                    </div>
                </div>
            </div>
            <div class="vx-modal-footer modal-bottom">
                <button type="button" class="btn btn-secondary" onclick="closeModal('modalEditPeriodTime')">Batal</button>
                <button type="submit" class="btn btn-primary"><i class='bx bx-check'></i> Simpan Waktu Jam</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL 3: SALIN JADWAL DARI SEMESTER LALU / KATEGORI LAIN -->
<div class="vx-modal-backdrop modal-backdrop" id="modalCopySemester">
    <div class="vx-modal modal-dialog" style="max-width: 620px;">
        <div class="vx-modal-header modal-title-bar">
            <h3 class="vx-modal-title">
                <span class="modal-title-icon"><i class='bx bx-copy-alt'></i></span>
                Salin Jadwal Pelajaran Antar Semester / Kategori
            </h3>
            <button class="vx-modal-close close-btn" onclick="closeModal('modalCopySemester')">&times;</button>
        </div>
        <form id="formCopySemester" onsubmit="submitCopySemester(event)">
            <input type="hidden" name="school_id" value="{{ $school?->id ?? 1 }}">
            <div class="vx-modal-body modal-content">
                <div style="background: var(--vx-primary-light); border: 1px solid var(--vx-primary-border); border-radius: var(--radius-lg); padding: 0.75rem 1rem; margin-bottom: 1rem; font-size: 0.8rem; color: var(--text-heading);">
                    <i class='bx bx-info-circle' style="color: var(--bento-primary);"></i>
                    Fitur ini menyalin jadwal pelajaran dari semester/kategori sebelumnya ke semester yang akan datang (mencocokkan hari, sesi jam ke-N, kelas, guru, dan ruangan secara otomatis).
                </div>

                <div class="vx-form-grid form-grid-2">
                    <!-- SUMBER -->
                    <div class="form-row span-2" style="border-bottom: 1px solid var(--border-color); padding-bottom: 0.25rem; margin-top: 0.25rem;">
                        <strong style="font-size: 0.82rem; color: var(--bento-primary); text-transform: uppercase;">1. Periode Sumber (Asal Jadwal)</strong>
                    </div>
                    <div class="form-row">
                        <label class="vx-label">Tahun Ajaran Sumber *</label>
                        <select class="vx-input" name="source_academic_year_id" required>
                            @foreach($academicYears as $ay)
                                <option value="{{ $ay->id }}" {{ ($activeYear && $activeYear->id === $ay->id) ? 'selected' : '' }}>
                                    {{ $ay->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-row">
                        <label class="vx-label">Semester Sumber *</label>
                        <select class="vx-input" name="source_semester" required>
                            <option value="odd">Semester Ganjil (Gasal)</option>
                            <option value="even">Semester Genap</option>
                        </select>
                    </div>
                    <div class="form-row">
                        <label class="vx-label">Kategori Pola Jam Sumber</label>
                        <select class="vx-input" name="source_category_id">
                            <option value="">— Semua Kategori di Semester Sumber —</option>
                            @foreach($timeCategories as $cat)
                                <option value="{{ $cat->id }}" {{ $cat->is_default ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-row">
                        <label class="vx-label">Filter Rombel yang Disalin</label>
                        <select class="vx-input" name="class_id">
                            <option value="">— Salin Seluruh Kelas ({{ $classes->count() }} Rombel) —</option>
                            @foreach($classes as $cls)
                                <option value="{{ $cls->id }}">Hanya Kelas {{ $cls->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- TUJUAN -->
                    <div class="form-row span-2" style="border-bottom: 1px solid var(--border-color); padding-bottom: 0.25rem; margin-top: 0.5rem;">
                        <strong style="font-size: 0.82rem; color: var(--vx-success); text-transform: uppercase;">2. Periode Tujuan (Target Semester Mendatang)</strong>
                    </div>
                    <div class="form-row">
                        <label class="vx-label">Tahun Ajaran Tujuan *</label>
                        <select class="vx-input" name="target_academic_year_id" required>
                            @foreach($academicYears as $ay)
                                <option value="{{ $ay->id }}" {{ ($selectedYear && $selectedYear->id === $ay->id) ? 'selected' : '' }}>
                                    {{ $ay->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-row">
                        <label class="vx-label">Semester Tujuan *</label>
                        <select class="vx-input" name="target_semester" required>
                            <option value="even" {{ $selectedSemester === 'odd' ? 'selected' : '' }}>Semester Genap</option>
                            <option value="odd" {{ $selectedSemester === 'even' ? 'selected' : '' }}>Semester Ganjil (Gasal)</option>
                        </select>
                    </div>
                    <div class="form-row span-2">
                        <label class="vx-label">Kategori Pola Jam Tujuan</label>
                        <select class="vx-input" name="target_category_id">
                            @foreach($timeCategories as $cat)
                                <option value="{{ $cat->id }}" {{ ($selectedCategory && $selectedCategory->id === $cat->id) ? 'selected' : '' }}>
                                    {{ $cat->name }} ({{ $cat->effective_start_date ? 'Berlaku '.$cat->effective_start_date->format('d/m/Y') : 'Reguler' }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-row span-2">
                        <label style="display: inline-flex; align-items: center; gap: 0.5rem; font-size: 0.82rem; cursor: pointer;">
                            <input type="checkbox" name="overwrite" value="1">
                            <span>Timpa jadwal di semester tujuan jika slot sudah terisi</span>
                        </label>
                    </div>
                </div>
            </div>
            <div class="vx-modal-footer modal-bottom">
                <button type="button" class="btn btn-secondary" onclick="closeModal('modalCopySemester')">Batal</button>
                <button type="submit" class="btn btn-primary"><i class='bx bx-copy-alt'></i> Proses Salin Jadwal</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL 4: TAMBAH KATEGORI JADWAL DINAMIS & MASA BERLAKU -->
<div class="vx-modal-backdrop modal-backdrop" id="modalAddScheduleCategory">
    <div class="vx-modal modal-dialog">
        <div class="vx-modal-header modal-title-bar">
            <h3 class="vx-modal-title">
                <span class="modal-title-icon"><i class='bx bx-time-five'></i></span>
                Buat Kategori Jadwal Dinamis & Masa Berlaku
            </h3>
            <button class="vx-modal-close close-btn" onclick="closeModal('modalAddScheduleCategory')">&times;</button>
        </div>
        <form id="formAddScheduleCategory" onsubmit="submitAddScheduleCategory(event)">
            <input type="hidden" name="school_id" value="{{ $school?->id ?? 1 }}">
            <div class="vx-modal-body modal-content">
                <div class="vx-form-grid form-grid-2">
                    <div class="form-row span-2">
                        <label class="vx-label">Nama Kategori / Versi Jadwal *</label>
                        <input type="text" class="vx-input" name="name" placeholder="Contoh: Jadwal Reguler Semester Genap 2026/2027" required>
                    </div>
                    <div class="form-row">
                        <label class="vx-label">Kode Kategori *</label>
                        <input type="text" class="vx-input" name="code" placeholder="Contoh: GENAP_2027" required>
                    </div>
                    <div class="form-row">
                        <label class="vx-label">Salin Sesi Jam Dari Kategori</label>
                        <select class="vx-input" name="clone_periods_from_category_id">
                            <option value="">— Kosongkan Sesi Jam —</option>
                            @foreach($timeCategories as $cat)
                                <option value="{{ $cat->id }}" {{ $cat->is_default ? 'selected' : '' }}>
                                    Salin dari: {{ $cat->name }} ({{ $cat->periods_count }} sesi)
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-row">
                        <label class="vx-label">Target Tahun Ajaran</label>
                        <select class="vx-input" name="academic_year_id">
                            <option value="">— Semua Tahun Ajaran —</option>
                            @foreach($academicYears as $ay)
                                <option value="{{ $ay->id }}" {{ ($selectedYear && $selectedYear->id === $ay->id) ? 'selected' : '' }}>{{ $ay->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-row">
                        <label class="vx-label">Target Semester</label>
                        <select class="vx-input" name="academic_semester">
                            <option value="">— Semua Semester —</option>
                            <option value="odd">Semester Ganjil</option>
                            <option value="even" selected>Semester Genap</option>
                        </select>
                    </div>
                    <div class="form-row">
                        <label class="vx-label">Mulai Berlaku (Effective Start) *</label>
                        <input type="date" class="vx-input" name="effective_start_date">
                    </div>
                    <div class="form-row">
                        <label class="vx-label">Selesai Berlaku (Effective End) *</label>
                        <input type="date" class="vx-input" name="effective_end_date">
                    </div>
                    <div class="form-row span-2">
                        <label class="vx-label">Keterangan / Tujuan Jadwal</label>
                        <input type="text" class="vx-input" name="description" placeholder="Disiapkan oleh Waka Kurikulum untuk perkuliahan/KBM semester mendatang">
                    </div>
                </div>
            </div>
            <div class="vx-modal-footer modal-bottom">
                <button type="button" class="btn btn-secondary" onclick="closeModal('modalAddScheduleCategory')">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Kategori & Masa Berlaku</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL 5: EDIT KATEGORI & MASA BERLAKU -->
<div class="vx-modal-backdrop modal-backdrop" id="modalEditScheduleCategory">
    <div class="vx-modal modal-dialog">
        <div class="vx-modal-header modal-title-bar">
            <h3 class="vx-modal-title">
                <span class="modal-title-icon"><i class='bx bx-calendar-edit'></i></span>
                Atur Masa Berlaku & Kategori Jadwal
            </h3>
            <button class="vx-modal-close close-btn" onclick="closeModal('modalEditScheduleCategory')">&times;</button>
        </div>
        <form id="formEditScheduleCategory" onsubmit="submitEditScheduleCategory(event)">
            <input type="hidden" id="editCatId">
            <div class="vx-modal-body modal-content">
                <div class="vx-form-grid form-grid-2">
                    <div class="form-row span-2">
                        <label class="vx-label">Nama Kategori Jadwal *</label>
                        <input type="text" class="vx-input" id="editCatName" name="name" required>
                    </div>
                    <div class="form-row">
                        <label class="vx-label">Kode *</label>
                        <input type="text" class="vx-input" id="editCatCode" name="code" required>
                    </div>
                    <div class="form-row">
                        <label class="vx-label">Jadikan Jadwal Utama Aktif?</label>
                        <select class="vx-input" id="editCatDefault" name="is_default">
                            <option value="0">Tidak (Sesuai Masa Berlaku / Persiapan)</option>
                            <option value="1">Ya (Aktifkan Sebagai Jadwal Utama)</option>
                        </select>
                    </div>
                    <div class="form-row">
                        <label class="vx-label">Target Tahun Ajaran</label>
                        <select class="vx-input" id="editCatYearId" name="academic_year_id">
                            <option value="">— Semua Tahun Ajaran —</option>
                            @foreach($academicYears as $ay)
                                <option value="{{ $ay->id }}">{{ $ay->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-row">
                        <label class="vx-label">Target Semester</label>
                        <select class="vx-input" id="editCatSemester" name="academic_semester">
                            <option value="">— Semua Semester —</option>
                            <option value="odd">Semester Ganjil</option>
                            <option value="even">Semester Genap</option>
                        </select>
                    </div>
                    <div class="form-row">
                        <label class="vx-label">Tanggal Mulai Berlaku</label>
                        <input type="date" class="vx-input" id="editCatStartDate" name="effective_start_date">
                    </div>
                    <div class="form-row">
                        <label class="vx-label">Tanggal Selesai Berlaku</label>
                        <input type="date" class="vx-input" id="editCatEndDate" name="effective_end_date">
                    </div>
                    <div class="form-row span-2">
                        <label class="vx-label">Deskripsi</label>
                        <input type="text" class="vx-input" id="editCatDesc" name="description">
                    </div>
                </div>
            </div>
            <div class="vx-modal-footer modal-bottom">
                <button type="button" class="btn btn-secondary" onclick="closeModal('modalEditScheduleCategory')">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Masa Berlaku</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL 6: TAMBAH MASTER RUANGAN -->
<div class="vx-modal-backdrop modal-backdrop" id="modalAddRoom">
    <div class="vx-modal modal-dialog">
        <div class="vx-modal-header modal-title-bar">
            <h3 class="vx-modal-title">
                <span class="modal-title-icon"><i class='bx bx-door-open'></i></span>
                Tambah Ruang Kelas / Laboratorium
            </h3>
            <button class="vx-modal-close close-btn" onclick="closeModal('modalAddRoom')">&times;</button>
        </div>
        <form id="formAddRoom" onsubmit="submitAddRoom(event)">
            <input type="hidden" name="school_id" value="{{ $school?->id ?? 1 }}">
            <div class="vx-modal-body modal-content">
                <div class="vx-form-grid form-grid-2">
                    <div class="form-row">
                        <label class="vx-label">Kode Ruangan *</label>
                        <input type="text" class="vx-input" name="code" placeholder="R-X1 / LAB-KOM1" required>
                    </div>
                    <div class="form-row">
                        <label class="vx-label">Jenis Ruangan *</label>
                        <select class="vx-input" name="type" required>
                            <option value="CLASSROOM">Ruang Kelas Teori</option>
                            <option value="LABORATORY">Laboratorium</option>
                            <option value="WORKSHOP">Bengkel / Workshop SMK</option>
                            <option value="SPORTS">Lapangan / GOR Olahraga</option>
                            <option value="HALL">Aula / Ruang Serbaguna</option>
                            <option value="OTHER">Lainnya</option>
                        </select>
                    </div>
                    <div class="form-row span-2">
                        <label class="vx-label">Nama Lengkap Ruangan *</label>
                        <input type="text" class="vx-input" name="name" placeholder="Ruang Kelas X MIPA 1 / Lab Komputer 1" required>
                    </div>
                    <div class="form-row">
                        <label class="vx-label">Gedung</label>
                        <input type="text" class="vx-input" name="building" placeholder="Gedung Utama A">
                    </div>
                    <div class="form-row">
                        <label class="vx-label">Lantai</label>
                        <input type="text" class="vx-input" name="floor" placeholder="Lantai 1">
                    </div>
                    <div class="form-row">
                        <label class="vx-label">Kapasitas Kursi</label>
                        <input type="number" class="vx-input" name="capacity" value="36" min="1">
                    </div>
                    <div class="form-row">
                        <label class="vx-label">Jadikan Ruang Default Kelas</label>
                        <select class="vx-input" name="default_for_class_id">
                            <option value="">— Tidak Terikat (Ruang Bersama/Lab) —</option>
                            @foreach($classes as $cls)
                                <option value="{{ $cls->id }}">{{ $cls->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="vx-modal-footer modal-bottom">
                <button type="button" class="btn btn-secondary" onclick="closeModal('modalAddRoom')">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Ruangan</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL 7: PENGATURAN WARNA MATA PELAJARAN -->
<div class="vx-modal-backdrop modal-backdrop" id="modalSubjectColors">
    <div class="vx-modal modal-dialog" style="max-width: 600px;">
        <div class="vx-modal-header modal-title-bar">
            <h3 class="vx-modal-title">
                <span class="modal-title-icon"><i class='bx bx-palette'></i></span>
                Pengaturan Warna Mata Pelajaran
            </h3>
            <button class="vx-modal-close close-btn" onclick="closeModal('modalSubjectColors')">&times;</button>
        </div>
        <div class="vx-modal-body modal-content">
            <div style="font-size: 0.78rem; color: var(--text-muted); margin-bottom: 0.85rem;">
                Pilih warna identitas untuk setiap mata pelajaran. Warna akan otomatis ditampilkan pada sel jadwal pelajaran di tabel Kelas, Guru, dan Ruangan.
            </div>
            <div style="display: flex; flex-direction: column; gap: 0.55rem; max-height: 420px; overflow-y: auto; padding-right: 0.25rem;">
                @foreach($subjects as $subj)
                    @php
                        $curSubjColor = $subj->resolved_color;
                    @endphp
                    <div style="display: flex; align-items: center; justify-content: space-between; gap: 0.75rem; padding: 0.55rem 0.75rem; border: 1px solid var(--border-color); border-left: 5px solid {{ $curSubjColor }}; border-radius: var(--radius-md); background: var(--bg-card);" id="subj-color-row-{{ $subj->id }}">
                        <div>
                            <div style="font-weight: 700; font-size: 0.84rem; color: var(--text-heading);">{{ $subj->name }}</div>
                            <span class="vx-badge cell-mono" id="subj-color-badge-{{ $subj->id }}" style="background: {{ $curSubjColor }}; color: #fff; font-size: 0.65rem;">{{ $subj->code }}</span>
                        </div>
                        <div style="display: flex; align-items: center; gap: 0.45rem;">
                            @foreach(['#4680ff', '#10b981', '#f59e0b', '#8b5cf6', '#ec4899', '#06b6d4', '#ef4444', '#14b8a6'] as $presetHex)
                                <button type="button"
                                    onclick="updateSubjectColorLive({{ $subj->id }}, '{{ $presetHex }}')"
                                    title="Pilih warna {{ $presetHex }}"
                                    style="width: 20px; height: 20px; border-radius: 50%; background: {{ $presetHex }}; border: 2px solid #fff; box-shadow: 0 0 0 1px rgba(0,0,0,0.18); cursor: pointer; padding: 0;"></button>
                            @endforeach
                            <input type="color" id="subj-color-input-{{ $subj->id }}" value="{{ $curSubjColor }}"
                                onchange="updateSubjectColorLive({{ $subj->id }}, this.value)"
                                style="width: 34px; height: 28px; padding: 0; border: 1px solid var(--border-color); border-radius: 6px; cursor: pointer; background: transparent;">
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="vx-modal-footer modal-bottom">
            <button type="button" class="btn btn-primary" onclick="window.location.reload()">
                <i class='bx bx-check'></i> Selesai & Muat Ulang Tabel
            </button>
        </div>
    </div>
</div>

<!-- MODAL 8: KELOLA MASTER KELOMPOK MAPEL / KELAS GABUNGAN LINTAS ROMBEL -->
<div class="vx-modal-backdrop modal-backdrop" id="modalTeachingGroup">
    <div class="vx-modal modal-dialog" style="max-width: 680px;">
        <div class="vx-modal-header modal-title-bar">
            <h3 class="vx-modal-title">
                <span class="modal-title-icon"><i class='bx bx-layer-plus'></i></span>
                <span id="tgModalTitle">Buat Kelompok Mapel & Kelas Gabungan</span>
            </h3>
            <button class="vx-modal-close close-btn" onclick="closeModal('modalTeachingGroup')">&times;</button>
        </div>
        <form id="formTeachingGroup" onsubmit="submitTeachingGroup(event)">
            <input type="hidden" id="tgId" value="">
            <input type="hidden" id="tgSchoolId" value="{{ $school?->id ?? 1 }}">
            <input type="hidden" id="tgAcademicYearId" value="{{ $selectedYear?->id }}">

            <div class="vx-modal-body modal-content">
                <div class="vx-form-grid form-grid-2">
                    <div class="form-row">
                        <label class="vx-label">Kode Kelompok *</label>
                        <input type="text" class="vx-input" id="tgCode" placeholder="Contoh: GRP-PAK-X / PIL-INF-XI" required>
                    </div>
                    <div class="form-row">
                        <label class="vx-label">Nama Kelompok Mapel / Gabungan *</label>
                        <input type="text" class="vx-input" id="tgName" placeholder="Contoh: PAK Kelas X Gabungan (MIPA 1 & 2)" required>
                    </div>
                    <div class="form-row">
                        <label class="vx-label">Mata Pelajaran Terkait</label>
                        <select class="vx-input" id="tgSubjectId" onchange="onTeachingGroupSubjectChange()">
                            <option value="" data-religion="">— Pilih Mata Pelajaran —</option>
                            @foreach($subjects as $subj)
                                <option value="{{ $subj->id }}" data-religion="{{ $subj->resolved_religion_filter ?? '' }}">
                                    [{{ $subj->code }}] {{ $subj->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-row">
                        <label class="vx-label">Guru Pengampu Default</label>
                        <select class="vx-input" id="tgTeacherId">
                            <option value="">— Pilih Guru Pengampu —</option>
                            @foreach($teachers as $tch)
                                <option value="{{ $tch->id }}">{{ $tch->full_name }}{{ $tch->title_suffix ? ', '.$tch->title_suffix : '' }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-row">
                        <label class="vx-label">Ruang Belajar Default</label>
                        <select class="vx-input" id="tgDefaultRoomId">
                            <option value="">— Fleksibel / Sesuai Jadwal —</option>
                            @foreach($rooms as $rm)
                                <option value="{{ $rm->id }}">[{{ $rm->code }}] {{ $rm->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-row">
                        <label class="vx-label">Mode Filter Peserta Siswa *</label>
                        <select class="vx-input" id="tgMembershipMode" onchange="toggleTeachingGroupMode()" required>
                            <option value="RELIGION">Otomatis Baca Agama dari Biodata Siswa</option>
                            <option value="MANUAL">Pilih Siswa Manual Lintas Rombel (Mapel Pilihan)</option>
                            <option value="ALL">Seluruh Siswa pada Rombel Tergabung</option>
                        </select>
                    </div>

                    <div class="form-row span-2" id="tgReligionRow">
                        <label class="vx-label">Agama Siswa yang Dijaring dari Biodata *</label>
                        <select class="vx-input" id="tgReligionFilter">
                            <option value="">— Otomatis Deteksi dari Nama Mata Pelajaran —</option>
                            <option value="Islam">Islam</option>
                            <option value="Kristen">Kristen</option>
                            <option value="Katolik">Katolik</option>
                            <option value="Hindu">Hindu</option>
                            <option value="Buddha">Buddha</option>
                            <option value="Konghucu">Konghucu</option>
                        </select>
                        <div style="font-size: 0.76rem; color: var(--text-muted); margin-top: 0.3rem;">
                            Sistem membaca kolom <code>Agama</code> pada Biodata Siswa dari rombel-rombel yang dicentang di bawah secara otomatis.
                        </div>
                    </div>

                    <div class="form-row span-2">
                        <label class="vx-label">Rombel yang Digabung dalam Kelompok Ini *</label>
                        <div style="display: flex; flex-wrap: wrap; gap: 0.5rem; padding: 0.65rem 0.75rem; border: 1px solid var(--border-color); border-radius: var(--radius-md); background: var(--bg-subtle);">
                            @foreach($classes as $cls)
                                <label style="display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.28rem 0.65rem; border: 1px solid var(--border-color); border-radius: 6px; background: var(--bg-card); font-size: 0.8rem; font-weight: 600; cursor: pointer;">
                                    <input type="checkbox" class="tg-class-cb" value="{{ $cls->id }}" onchange="filterTeachingGroupManualStudents()">
                                    <span>{{ $cls->name }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="form-row span-2" id="tgManualStudentsRow" style="display: none;">
                        <label class="vx-label">Daftar Siswa Pilihan Lintas Rombel</label>
                        <div style="max-height: 190px; overflow-y: auto; border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 0.6rem; background: var(--bg-card); display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 0.4rem;">
                            @foreach(($students ?? collect()) as $std)
                                <label class="tg-student-item" data-class-id="{{ $std->class_id }}" style="display: inline-flex; align-items: center; gap: 0.45rem; font-size: 0.78rem; padding: 0.25rem 0.45rem; border-radius: 5px; border: 1px solid var(--border-color); cursor: pointer;">
                                    <input type="checkbox" class="tg-student-cb" value="{{ $std->id }}">
                                    <span>
                                        <strong>{{ $std->full_name }}</strong>
                                        <span style="color: var(--text-muted); font-size: 0.72rem;">({{ $std->schoolClass?->name ?? '-' }} &bull; {{ $std->religion ?? '-' }})</span>
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="form-row span-2">
                        <label class="vx-label">Keterangan Kelompok</label>
                        <input type="text" class="vx-input" id="tgDescription" placeholder="Misal: Kelas gabungan paralel Pendidikan Agama Kristen kelas X">
                    </div>
                </div>
            </div>
            <div class="vx-modal-footer modal-bottom">
                <button type="button" class="btn btn-secondary" onclick="closeModal('modalTeachingGroup')">Batal</button>
                <button type="submit" class="btn btn-primary"><i class='bx bx-check'></i> Simpan Kelompok Mapel</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL 9: DAFTAR SISWA PESERTA JADWAL / FILTER BIODATA AGAMA -->
<div class="vx-modal-backdrop modal-backdrop" id="modalScheduleParticipants">
    <div class="vx-modal modal-dialog" style="max-width: 680px;">
        <div class="vx-modal-header modal-title-bar">
            <h3 class="vx-modal-title">
                <span class="modal-title-icon"><i class='bx bx-group'></i></span>
                <span id="participantsModalTitle">Daftar Peserta Siswa</span>
            </h3>
            <button class="vx-modal-close close-btn" onclick="closeModal('modalScheduleParticipants')">&times;</button>
        </div>
        <div class="vx-modal-body modal-content">
            <div id="participantsMetaBox" style="display: flex; flex-wrap: wrap; gap: 0.45rem; margin-bottom: 0.85rem;"></div>
            <div class="table-responsive" style="max-height: 380px; overflow-y: auto; border: 1px solid var(--border-color); border-radius: var(--radius-md);">
                <table class="vx-table">
                    <thead>
                        <tr>
                            <th style="width: 48px;">No</th>
                            <th>NIS</th>
                            <th>Nama Lengkap Siswa</th>
                            <th>Rombel Asal</th>
                            <th>Agama (Biodata)</th>
                        </tr>
                    </thead>
                    <tbody id="participantsTableBody">
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 1.5rem; color: var(--text-muted);">Memuat data peserta siswa...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="vx-modal-footer modal-bottom">
            <button type="button" class="btn btn-primary" onclick="closeModal('modalScheduleParticipants')">Tutup</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    function openModal(id) {
        const el = document.getElementById(id);
        if (el) el.classList.add('open', 'show');
    }

    function closeModal(id) {
        const el = document.getElementById(id);
        if (el) el.classList.remove('open', 'show');
    }

    function switchScheduleView(viewId) {
        document.querySelectorAll('.sched-view-pane').forEach(p => p.classList.remove('active'));
        document.querySelectorAll('.sched-tab-btn').forEach(b => b.classList.remove('active'));

        const pane = document.getElementById(viewId);
        const btn = document.getElementById('btn-' + viewId);
        if (pane) pane.classList.add('active');
        if (btn) btn.classList.add('active');

        const viewInput = document.getElementById('contextViewInput');
        if (viewInput) viewInput.value = viewId;

        const url = new URL(window.location.href);
        url.searchParams.set('view', viewId);
        window.history.replaceState({}, '', url);
    }

    function changeFilterParam(paramName, paramValue, targetView) {
        const url = new URL(window.location.href);
        url.searchParams.set(paramName, paramValue);
        if (targetView) url.searchParams.set('view', targetView);
        window.location.href = url.toString();
    }

    function filterMatrixDay(day, btnEl) {
        if (btnEl) {
            btnEl.parentElement.querySelectorAll('.day-pill-btn').forEach(b => b.classList.remove('active'));
            btnEl.classList.add('active');
        }
        document.querySelectorAll('#classMatrixTable .matrix-day-col').forEach(col => {
            if (day === 'all' || col.getAttribute('data-day') === String(day)) {
                col.style.display = '';
            } else {
                col.style.display = 'none';
            }
        });
    }

    function filterPeriodOptionsByDay(day) {
        const startSel = document.getElementById('assignStartPeriodId');
        const endSel = document.getElementById('assignEndPeriodId');
        let firstMatching = null;

        Array.from(startSel.options).forEach(opt => {
            const match = String(opt.getAttribute('data-day')) === String(day);
            opt.style.display = match ? '' : 'none';
            if (match && !firstMatching) firstMatching = opt.value;
        });

        Array.from(endSel.options).forEach(opt => {
            if (!opt.value) return;
            const match = String(opt.getAttribute('data-day')) === String(day);
            opt.style.display = match ? '' : 'none';
        });

        if (firstMatching) startSel.value = firstMatching;
        endSel.value = '';
    }

    function syncDefaultRoomForClass(classId) {
        const clsSel = document.getElementById('assignClassId');
        const selectedOpt = clsSel?.options[clsSel.selectedIndex];
        const defaultRoomId = selectedOpt?.getAttribute('data-default-room');
        const roomSel = document.getElementById('assignRoomId');
        if (roomSel && defaultRoomId) {
            roomSel.value = defaultRoomId;
        }
    }

    function toggleAssignScheduleMode() {
        const isCombined = document.getElementById('assignScheduleMode')?.value === 'combined';
        document.getElementById('assignSingleClassRow').style.display = isCombined ? 'none' : '';
        document.getElementById('assignCombinedGroupRow').style.display = isCombined ? '' : 'none';
        document.getElementById('assignCombinedClassesRow').style.display = isCombined ? '' : 'none';
        document.getElementById('assignFilterTypeRow').style.display = isCombined ? '' : 'none';
        const dayRow = document.getElementById('assignDayRow');
        if (dayRow) {
            dayRow.classList.toggle('span-2', isCombined);
        }
        onAssignFilterTypeChange();
    }

    function onAssignSubjectChange() {
        const subjSel = document.getElementById('assignSubjectId');
        const selectedOpt = subjSel?.options[subjSel.selectedIndex];
        const rel = selectedOpt?.getAttribute('data-religion') || '';
        const noticeEl = document.getElementById('assignReligionBiodataNotice');
        const labelEl = document.getElementById('assignReligionBiodataLabel');
        if (noticeEl && labelEl) {
            if (rel) {
                labelEl.textContent = rel;
                noticeEl.style.display = '';
            } else {
                noticeEl.style.display = 'none';
            }
        }
    }

    function onAssignFilterTypeChange() {
        const isCombined = document.getElementById('assignScheduleMode')?.value === 'combined';
        const filterType = document.getElementById('assignStudentFilterType')?.value || 'AUTO';
        const relRow = document.getElementById('assignFilterReligionRow');
        if (relRow) {
            relRow.style.display = (isCombined && filterType === 'RELIGION') ? '' : 'none';
        }
    }

    function onAssignTeachingGroupChange(groupId) {
        const grpSel = document.getElementById('assignTeachingGroupId');
        const opt = grpSel?.options[grpSel.selectedIndex];
        if (!groupId || !opt) return;

        const subjId = opt.getAttribute('data-subject');
        const teacherId = opt.getAttribute('data-teacher');
        const roomId = opt.getAttribute('data-room');
        const classCsv = opt.getAttribute('data-classes') || '';
        const classIds = classCsv.split(',').filter(Boolean);

        if (subjId) {
            document.getElementById('assignSubjectId').value = subjId;
            onAssignSubjectChange();
        }
        if (teacherId) document.getElementById('assignTeacherId').value = teacherId;
        if (roomId) document.getElementById('assignRoomId').value = roomId;

        document.querySelectorAll('.assign-combined-class-cb').forEach(cb => {
            cb.checked = classIds.includes(String(cb.value));
        });
        document.getElementById('assignStudentFilterType').value = 'AUTO';
        onAssignFilterTypeChange();
    }

    function openQuickAssignModal() {
        document.getElementById('assignScheduleMode').value = 'single';
        toggleAssignScheduleMode();
        document.getElementById('assignModalTitle').textContent = 'Set Jadwal Pelajaran Kelas';
        filterPeriodOptionsByDay(document.getElementById('assignDaySelect').value);
        onAssignSubjectChange();
        openModal('modalAssignSchedule');
    }

    function openAssignCombinedModal() {
        document.getElementById('assignScheduleMode').value = 'combined';
        toggleAssignScheduleMode();
        document.getElementById('assignModalTitle').textContent = 'Set Jadwal Kelas Gabungan Lintas Rombel';
        filterPeriodOptionsByDay(document.getElementById('assignDaySelect').value);
        onAssignSubjectChange();
        openModal('modalAssignSchedule');
    }

    function openAssignCombinedSlotModal(dayNum, dayName, periodId, periodName) {
        document.getElementById('assignScheduleMode').value = 'combined';
        toggleAssignScheduleMode();
        document.getElementById('assignDaySelect').value = String(dayNum);
        filterPeriodOptionsByDay(dayNum);
        document.getElementById('assignStartPeriodId').value = String(periodId);
        document.getElementById('assignEndPeriodId').value = '';
        document.getElementById('assignModalTitle').textContent = `Set Kelas Gabungan (${dayName} — ${periodName})`;
        onAssignSubjectChange();
        openModal('modalAssignSchedule');
    }

    function openAssignCombinedForGroup(groupId) {
        openAssignCombinedModal();
        const grpSel = document.getElementById('assignTeachingGroupId');
        if (grpSel) {
            grpSel.value = String(groupId);
            onAssignTeachingGroupChange(groupId);
        }
    }

    function openAssignSlotModal(dayNum, dayName, periodId, periodName) {
        document.getElementById('assignScheduleMode').value = 'single';
        toggleAssignScheduleMode();
        document.getElementById('assignDaySelect').value = String(dayNum);
        filterPeriodOptionsByDay(dayNum);
        document.getElementById('assignStartPeriodId').value = String(periodId);
        document.getElementById('assignEndPeriodId').value = '';
        document.getElementById('assignModalTitle').textContent = `Set Jadwal (${dayName} — ${periodName})`;
        onAssignSubjectChange();
        openModal('modalAssignSchedule');
    }

    function openAssignSlotWithRoomModal(dayNum, dayName, periodId, periodName, roomId) {
        openAssignSlotModal(dayNum, dayName, periodId, periodName);
        if (roomId) {
            document.getElementById('assignRoomId').value = String(roomId);
        }
        document.getElementById('assignModalTitle').textContent = `Jadwalkan Pemakaian Ruangan (${dayName} — ${periodName})`;
    }

    async function submitAssignSchedule(e) {
        e.preventDefault();
        const form = e.target;
        const formData = new FormData(form);
        const payload = Object.fromEntries(formData.entries());
        const isCombined = document.getElementById('assignScheduleMode')?.value === 'combined';

        payload.is_combined = isCombined;
        payload.replace_existing = form.querySelector('[name="replace_existing"]').checked;
        if (!payload.end_lesson_period_id) delete payload.end_lesson_period_id;
        if (!payload.room_id) delete payload.room_id;

        if (isCombined) {
            const checkedClassIds = Array.from(document.querySelectorAll('.assign-combined-class-cb:checked')).map(cb => parseInt(cb.value, 10));
            if (checkedClassIds.length === 0 && !payload.teaching_group_id) {
                alert('Pilih minimal satu rombel atau pilih Master Kelompok Mapel untuk Kelas Gabungan.');
                return;
            }
            payload.class_ids = checkedClassIds;
            if (checkedClassIds.length > 0) {
                payload.class_id = checkedClassIds[0];
            }
            if (!payload.teaching_group_id) delete payload.teaching_group_id;

            const filterType = document.getElementById('assignStudentFilterType')?.value || 'AUTO';
            if (filterType === 'RELIGION') {
                payload.student_filter_type = 'RELIGION';
                payload.student_filter_value = document.getElementById('assignStudentFilterReligion')?.value || null;
            } else if (filterType === 'ALL') {
                payload.student_filter_type = 'ALL';
                payload.student_filter_value = null;
            }
        } else {
            delete payload.teaching_group_id;
        }

        try {
            const res = await fetch('/api/v1/schedules', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify(payload)
            });
            const data = await res.json();
            if (!res.ok || !data.success) {
                alert(data.message || 'Gagal menyimpan jadwal (Terjadi bentrok).');
                return;
            }
            if (isCombined) {
                changeFilterParam('view', 'view-combined', 'view-combined');
                return;
            }
            window.location.reload();
        } catch (err) {
            alert('Terjadi kesalahan jaringan saat menyimpan jadwal.');
        }
    }

    function filterEditPeriodOptionsByDay(day, defaultStartId, defaultEndId) {
        const startSel = document.getElementById('editSlotStartPeriodId');
        const endSel = document.getElementById('editSlotEndPeriodId');
        let firstMatching = null;

        Array.from(startSel.options).forEach(opt => {
            const match = String(opt.getAttribute('data-day')) === String(day);
            opt.style.display = match ? '' : 'none';
            if (match && !firstMatching) firstMatching = opt.value;
        });

        Array.from(endSel.options).forEach(opt => {
            if (!opt.value) return;
            const match = String(opt.getAttribute('data-day')) === String(day);
            opt.style.display = match ? '' : 'none';
        });

        if (defaultStartId) {
            startSel.value = String(defaultStartId);
        } else if (firstMatching) {
            startSel.value = firstMatching;
        }

        if (defaultEndId && String(defaultEndId) !== String(defaultStartId)) {
            endSel.value = String(defaultEndId);
        } else {
            endSel.value = '';
        }
    }

    function openEditSlotModal(scheduleId, label, subjectId, teacherId, roomId, roomName, notes, scheduleIdsCsv, dayNum, startPeriodId, endPeriodId, combinedClassIdsCsv) {
        document.getElementById('editScheduleId').value = scheduleId;
        document.getElementById('editScheduleIdsCsv').value = scheduleIdsCsv || String(scheduleId);
        document.getElementById('editSlotPeriodLabel').textContent = label;
        document.getElementById('editSlotSubjectId').value = String(subjectId);
        document.getElementById('editSlotTeacherId').value = String(teacherId);
        document.getElementById('editSlotRoomId').value = roomId ? String(roomId) : '';
        document.getElementById('editSlotNotes').value = notes || '';
        document.getElementById('editSlotReplaceExisting').checked = false;

        const isCombined = Boolean(combinedClassIdsCsv && String(combinedClassIdsCsv).trim().length > 0);
        document.getElementById('editSlotIsCombined').value = isCombined ? '1' : '0';
        const cmbRow = document.getElementById('editCombinedClassesRow');
        if (cmbRow) {
            cmbRow.style.display = isCombined ? '' : 'none';
            const selectedCls = isCombined ? String(combinedClassIdsCsv).split(',').filter(Boolean) : [];
            document.querySelectorAll('.edit-combined-class-cb').forEach(cb => {
                cb.checked = selectedCls.includes(String(cb.value));
            });
        }

        if (dayNum) {
            document.getElementById('editSlotDaySelect').value = String(dayNum);
            filterEditPeriodOptionsByDay(dayNum, startPeriodId, endPeriodId);
        }

        openModal('modalEditScheduleSlot');
    }

    async function submitEditScheduleSlot(e) {
        e.preventDefault();
        const form = e.target;
        const id = document.getElementById('editScheduleId').value;
        const idsCsv = document.getElementById('editScheduleIdsCsv').value;
        const isCombined = document.getElementById('editSlotIsCombined').value === '1';
        const formData = new FormData(form);
        const payload = Object.fromEntries(formData.entries());
        payload.replace_existing = document.getElementById('editSlotReplaceExisting')?.checked || false;
        if (!payload.room_id) payload.room_id = null;
        if (!payload.end_lesson_period_id) delete payload.end_lesson_period_id;
        if (idsCsv) {
            payload.schedule_ids = idsCsv.split(',').map(v => parseInt(v, 10)).filter(Boolean);
        }
        if (isCombined) {
            const clsIds = Array.from(document.querySelectorAll('.edit-combined-class-cb:checked')).map(cb => parseInt(cb.value, 10));
            if (clsIds.length === 0) {
                alert('Pilih minimal satu rombel untuk kelas gabungan.');
                return;
            }
            payload.is_combined = true;
            payload.class_ids = clsIds;
        }

        const res = await fetch(`/api/v1/schedules/${id}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify(payload)
        });
        const data = await res.json();
        if (!res.ok || !data.success) {
            alert(data.message || 'Gagal memperbarui slot jadwal.');
            return;
        }
        window.location.reload();
    }

    function openEditPeriodTimeModal(periodId, name, periodNumber, startTime, endTime, isBreak) {
        document.getElementById('editPeriodId').value = periodId;
        document.getElementById('editPeriodName').value = name || '';
        document.getElementById('editPeriodNumber').value = periodNumber || '';
        document.getElementById('editPeriodStartTime').value = startTime || '07:00';
        document.getElementById('editPeriodEndTime').value = endTime || '07:45';
        document.getElementById('editPeriodIsBreak').value = isBreak ? '1' : '0';
        document.getElementById('editPeriodApplyAllDays').checked = true;
        openModal('modalEditPeriodTime');
    }

    async function submitEditPeriodTime(e) {
        e.preventDefault();
        const periodId = document.getElementById('editPeriodId').value;
        const payload = {
            name: document.getElementById('editPeriodName').value,
            period_number: document.getElementById('editPeriodNumber').value ? parseInt(document.getElementById('editPeriodNumber').value, 10) : null,
            start_time: document.getElementById('editPeriodStartTime').value,
            end_time: document.getElementById('editPeriodEndTime').value,
            is_break: document.getElementById('editPeriodIsBreak').value === '1',
            apply_all_days: document.getElementById('editPeriodApplyAllDays').checked
        };

        const res = await fetch(`/api/v1/periods/${periodId}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify(payload)
        });
        const data = await res.json();
        if (!res.ok || !data.success) {
            alert(data.message || 'Gagal memperbarui waktu sesi jam pelajaran.');
            return;
        }
        window.location.reload();
    }

    async function deleteScheduleSlot(id, idsCsv, isMergedBlock) {
        const msg = isMergedBlock
            ? 'Hapus seluruh blok jam pelajaran yang ter-merge untuk mata pelajaran ini?'
            : 'Hapus slot jam pelajaran ini?';
        if (!confirm(msg)) return;

        const query = (isMergedBlock && idsCsv) ? `?ids=${encodeURIComponent(idsCsv)}` : '';
        const res = await fetch(`/api/v1/schedules/${id}${query}`, {
            method: 'DELETE',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        });
        const data = await res.json();
        if (data.success) {
            window.location.reload();
        } else {
            alert(data.message || 'Gagal menghapus jadwal.');
        }
    }

    async function submitCopySemester(e) {
        e.preventDefault();
        const form = e.target;
        const formData = new FormData(form);
        const payload = Object.fromEntries(formData.entries());
        payload.overwrite = form.querySelector('[name="overwrite"]').checked;
        if (!payload.source_category_id) delete payload.source_category_id;
        if (!payload.target_category_id) delete payload.target_category_id;
        if (!payload.class_id) delete payload.class_id;

        const res = await fetch('/api/v1/schedules/copy-semester', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify(payload)
        });
        const data = await res.json();
        if (!res.ok || !data.success) {
            alert(data.message || 'Gagal menyalin jadwal dari semester sumber.');
            return;
        }
        alert(data.message);
        const url = new URL(window.location.href);
        url.searchParams.set('academic_year_id', payload.target_academic_year_id);
        url.searchParams.set('academic_semester', payload.target_semester);
        if (payload.target_category_id) url.searchParams.set('category_id', payload.target_category_id);
        window.location.href = url.toString();
    }

    async function submitAddScheduleCategory(e) {
        e.preventDefault();
        const formData = new FormData(e.target);
        const payload = Object.fromEntries(formData.entries());
        if (!payload.clone_periods_from_category_id) delete payload.clone_periods_from_category_id;
        if (!payload.academic_year_id) delete payload.academic_year_id;
        if (!payload.academic_semester) delete payload.academic_semester;
        if (!payload.effective_start_date) delete payload.effective_start_date;
        if (!payload.effective_end_date) delete payload.effective_end_date;

        const res = await fetch('/api/v1/time-categories', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify(payload)
        });
        const data = await res.json();
        if (!res.ok || !data.success) {
            alert(data.message || 'Gagal membuat kategori jadwal baru.');
            return;
        }
        const url = new URL(window.location.href);
        url.searchParams.set('category_id', data.data.id);
        if (payload.academic_year_id) url.searchParams.set('academic_year_id', payload.academic_year_id);
        if (payload.academic_semester) url.searchParams.set('academic_semester', payload.academic_semester);
        window.location.href = url.toString();
    }

    function openEditScheduleCategoryModal(id, name, code, desc, startDate, endDate, yearId, semester, isDefault) {
        document.getElementById('editCatId').value = id;
        document.getElementById('editCatName').value = name;
        document.getElementById('editCatCode').value = code;
        document.getElementById('editCatDesc').value = desc || '';
        document.getElementById('editCatStartDate').value = startDate || '';
        document.getElementById('editCatEndDate').value = endDate || '';
        document.getElementById('editCatYearId').value = yearId || '';
        document.getElementById('editCatSemester').value = semester || '';
        document.getElementById('editCatDefault').value = isDefault ? '1' : '0';
        openModal('modalEditScheduleCategory');
    }

    async function submitEditScheduleCategory(e) {
        e.preventDefault();
        const id = document.getElementById('editCatId').value;
        const formData = new FormData(e.target);
        const payload = Object.fromEntries(formData.entries());
        payload.is_default = payload.is_default === '1';
        if (!payload.academic_year_id) payload.academic_year_id = null;
        if (!payload.academic_semester) payload.academic_semester = null;
        if (!payload.effective_start_date) payload.effective_start_date = null;
        if (!payload.effective_end_date) payload.effective_end_date = null;

        const res = await fetch(`/api/v1/time-categories/${id}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify(payload)
        });
        const data = await res.json();
        if (!res.ok || !data.success) {
            alert(data.message || 'Gagal memperbarui masa berlaku kategori jadwal.');
            return;
        }
        window.location.reload();
    }

    async function clonePeriodsToCurrentCategory() {
        const currentCatId = {{ $selectedCategory?->id ?? 'null' }};
        const defaultCatId = {{ $activeCategory?->id ?? 'null' }};
        if (!currentCatId || !defaultCatId) return;

        const res = await fetch(`/api/v1/time-categories/${currentCatId}/clone-periods`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({ source_category_id: defaultCatId })
        });
        const data = await res.json();
        if (data.success) {
            window.location.reload();
        } else {
            alert(data.message || 'Gagal menyalin sesi jam.');
        }
    }

    async function submitAddRoom(e) {
        e.preventDefault();
        const formData = new FormData(e.target);
        const payload = Object.fromEntries(formData.entries());
        if (!payload.default_for_class_id) delete payload.default_for_class_id;

        const res = await fetch('/api/v1/rooms', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify(payload)
        });
        const data = await res.json();
        if (!res.ok || !data.success) {
            alert(data.message || 'Gagal menambahkan ruangan.');
            return;
        }
        changeFilterParam('room_id', data.data.id, 'view-rooms-master');
    }

    async function deleteRoom(id, name) {
        if (!confirm(`Hapus ruangan "${name}"?`)) return;
        const res = await fetch(`/api/v1/rooms/${id}`, {
            method: 'DELETE',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        });
        const data = await res.json();
        if (data.success) {
            window.location.reload();
        }
    }

    async function updateSubjectColorLive(subjectId, hexColor) {
        const inputEl = document.getElementById('subj-color-input-' + subjectId);
        const rowEl = document.getElementById('subj-color-row-' + subjectId);
        const badgeEl = document.getElementById('subj-color-badge-' + subjectId);
        if (inputEl) inputEl.value = hexColor;
        if (rowEl) rowEl.style.borderLeftColor = hexColor;
        if (badgeEl) badgeEl.style.background = hexColor;

        await fetch(`/api/v1/subjects/${subjectId}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({ color: hexColor })
        });
    }

    // =========================================================================
    // KELOMPOK MAPEL / KELAS GABUNGAN & DAFTAR PESERTA BIODATA SISWA
    // =========================================================================
    function toggleTeachingGroupMode() {
        const mode = document.getElementById('tgMembershipMode')?.value || 'RELIGION';
        document.getElementById('tgReligionRow').style.display = mode === 'RELIGION' ? '' : 'none';
        document.getElementById('tgManualStudentsRow').style.display = mode === 'MANUAL' ? '' : 'none';
        if (mode === 'MANUAL') {
            filterTeachingGroupManualStudents();
        }
    }

    function onTeachingGroupSubjectChange() {
        const subjSel = document.getElementById('tgSubjectId');
        const opt = subjSel?.options[subjSel.selectedIndex];
        const autoRel = opt?.getAttribute('data-religion') || '';
        if (autoRel) {
            document.getElementById('tgMembershipMode').value = 'RELIGION';
            document.getElementById('tgReligionFilter').value = autoRel;
            toggleTeachingGroupMode();
        }
    }

    function filterTeachingGroupManualStudents() {
        const checkedClasses = Array.from(document.querySelectorAll('.tg-class-cb:checked')).map(cb => String(cb.value));
        document.querySelectorAll('.tg-student-item').forEach(lbl => {
            const clsId = String(lbl.getAttribute('data-class-id'));
            lbl.style.display = (checkedClasses.length === 0 || checkedClasses.includes(clsId)) ? 'inline-flex' : 'none';
        });
    }

    function openCreateTeachingGroupModal() {
        document.getElementById('tgId').value = '';
        document.getElementById('tgCode').value = '';
        document.getElementById('tgName').value = '';
        document.getElementById('tgSubjectId').value = '';
        document.getElementById('tgTeacherId').value = '';
        document.getElementById('tgDefaultRoomId').value = '';
        document.getElementById('tgMembershipMode').value = 'RELIGION';
        document.getElementById('tgReligionFilter').value = '';
        document.getElementById('tgDescription').value = '';
        document.querySelectorAll('.tg-class-cb').forEach(cb => cb.checked = false);
        document.querySelectorAll('.tg-student-cb').forEach(cb => cb.checked = false);
        document.getElementById('tgModalTitle').textContent = 'Buat Kelompok Mapel & Kelas Gabungan';
        toggleTeachingGroupMode();
        openModal('modalTeachingGroup');
    }

    function openEditTeachingGroupModal(id, code, name, subjectId, teacherId, roomId, mode, religion, desc, classIds, studentIds) {
        document.getElementById('tgId').value = id;
        document.getElementById('tgCode').value = code || '';
        document.getElementById('tgName').value = name || '';
        document.getElementById('tgSubjectId').value = subjectId ? String(subjectId) : '';
        document.getElementById('tgTeacherId').value = teacherId ? String(teacherId) : '';
        document.getElementById('tgDefaultRoomId').value = roomId ? String(roomId) : '';
        document.getElementById('tgMembershipMode').value = mode || 'RELIGION';
        document.getElementById('tgReligionFilter').value = religion || '';
        document.getElementById('tgDescription').value = desc || '';

        const clsSet = (classIds || []).map(String);
        const stdSet = (studentIds || []).map(String);
        document.querySelectorAll('.tg-class-cb').forEach(cb => cb.checked = clsSet.includes(String(cb.value)));
        document.querySelectorAll('.tg-student-cb').forEach(cb => cb.checked = stdSet.includes(String(cb.value)));

        document.getElementById('tgModalTitle').textContent = `Edit Kelompok Mapel: ${name}`;
        toggleTeachingGroupMode();
        openModal('modalTeachingGroup');
    }

    async function submitTeachingGroup(e) {
        e.preventDefault();
        const id = document.getElementById('tgId').value;
        const classIds = Array.from(document.querySelectorAll('.tg-class-cb:checked')).map(cb => parseInt(cb.value, 10));
        if (classIds.length === 0) {
            alert('Pilih minimal satu rombel yang tergabung dalam kelompok mapel ini.');
            return;
        }

        const mode = document.getElementById('tgMembershipMode').value;
        const studentIds = mode === 'MANUAL'
            ? Array.from(document.querySelectorAll('.tg-student-cb:checked')).map(cb => parseInt(cb.value, 10))
            : [];

        const payload = {
            school_id: parseInt(document.getElementById('tgSchoolId').value, 10),
            academic_year_id: document.getElementById('tgAcademicYearId').value ? parseInt(document.getElementById('tgAcademicYearId').value, 10) : null,
            code: document.getElementById('tgCode').value,
            name: document.getElementById('tgName').value,
            subject_id: document.getElementById('tgSubjectId').value ? parseInt(document.getElementById('tgSubjectId').value, 10) : null,
            teacher_id: document.getElementById('tgTeacherId').value ? parseInt(document.getElementById('tgTeacherId').value, 10) : null,
            default_room_id: document.getElementById('tgDefaultRoomId').value ? parseInt(document.getElementById('tgDefaultRoomId').value, 10) : null,
            membership_mode: mode,
            religion_filter: mode === 'RELIGION' ? (document.getElementById('tgReligionFilter').value || null) : null,
            description: document.getElementById('tgDescription').value || null,
            class_ids: classIds,
            student_ids: studentIds
        };

        const url = id ? `/api/v1/teaching-groups/${id}` : '/api/v1/teaching-groups';
        const method = id ? 'PUT' : 'POST';

        const res = await fetch(url, {
            method,
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify(payload)
        });
        const data = await res.json();
        if (!res.ok || !data.success) {
            alert(data.message || 'Gagal menyimpan kelompok mapel.');
            return;
        }
        changeFilterParam('view', 'view-combined', 'view-combined');
    }

    async function deleteTeachingGroup(id, name) {
        if (!confirm(`Hapus kelompok mapel "${name}"?`)) return;
        const res = await fetch(`/api/v1/teaching-groups/${id}`, {
            method: 'DELETE',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        });
        const data = await res.json();
        if (data.success) {
            changeFilterParam('view', 'view-combined', 'view-combined');
        } else {
            alert(data.message || 'Gagal menghapus kelompok mapel.');
        }
    }

    function escapeHtml(str) {
        return String(str ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    function renderParticipantsModalContent(titleText, badgesHtml, students) {
        document.getElementById('participantsModalTitle').textContent = titleText;
        document.getElementById('participantsMetaBox').innerHTML = badgesHtml;
        const tbody = document.getElementById('participantsTableBody');

        if (!students || students.length === 0) {
            tbody.innerHTML = `<tr><td colspan="5" style="text-align: center; padding: 1.75rem; color: var(--text-muted);">Belum ada siswa yang cocok dengan kriteria biodata agama / rombel pada sesi ini.</td></tr>`;
            return;
        }

        tbody.innerHTML = students.map((s, idx) => `
            <tr>
                <td class="cell-mono">${idx + 1}</td>
                <td class="cell-mono">${escapeHtml(s.nis || '-')}</td>
                <td><span class="cell-title">${escapeHtml(s.full_name)}</span></td>
                <td><span class="vx-badge badge-primary">${escapeHtml(s.class_name || s.school_class?.name || '-')}</span></td>
                <td><span class="vx-badge badge-info"><i class='bx bx-id-card'></i> ${escapeHtml(s.religion || '-')}</span></td>
            </tr>
        `).join('');
    }

    async function openScheduleParticipantsModal(scheduleId) {
        document.getElementById('participantsTableBody').innerHTML = `<tr><td colspan="5" style="text-align: center; padding: 1.5rem; color: var(--text-muted);">Memuat daftar peserta dari Biodata Siswa...</td></tr>`;
        document.getElementById('participantsMetaBox').innerHTML = '';
        openModal('modalScheduleParticipants');

        const res = await fetch(`/api/v1/schedules/${scheduleId}/students`, {
            headers: { 'Accept': 'application/json' }
        });
        const json = await res.json();
        if (!res.ok || !json.success) {
            document.getElementById('participantsTableBody').innerHTML = `<tr><td colspan="5" style="text-align: center; padding: 1.5rem; color: var(--vx-danger);">Gagal memuat peserta siswa.</td></tr>`;
            return;
        }

        const sch = json.schedule || {};
        const badges = [
            `<span class="vx-badge badge-primary"><i class='bx bx-book'></i> ${escapeHtml(sch.subject_name || '-')}</span>`,
            `<span class="vx-badge badge-dark"><i class='bx bxs-school'></i> ${escapeHtml(sch.classes_label || '-')}</span>`,
            sch.effective_religion_filter
                ? `<span class="vx-badge badge-info"><i class='bx bx-id-card'></i> Filter Biodata Agama: ${escapeHtml(sch.effective_religion_filter)}</span>`
                : `<span class="vx-badge badge-success"><i class='bx bx-group'></i> Sumber: ${escapeHtml(sch.filter_source || 'ROMBEL')}</span>`,
            `<span class="vx-badge badge-warning">Total: ${json.count ?? 0} Siswa</span>`
        ].join('');

        renderParticipantsModalContent(`Peserta Sesi: ${sch.subject_name || 'Jadwal Pelajaran'}`, badges, json.data || []);
    }

    async function openTeachingGroupStudentsModal(groupId) {
        document.getElementById('participantsTableBody').innerHTML = `<tr><td colspan="5" style="text-align: center; padding: 1.5rem; color: var(--text-muted);">Memuat daftar siswa kelompok mapel...</td></tr>`;
        document.getElementById('participantsMetaBox').innerHTML = '';
        openModal('modalScheduleParticipants');

        const res = await fetch(`/api/v1/teaching-groups/${groupId}/students`, {
            headers: { 'Accept': 'application/json' }
        });
        const json = await res.json();
        if (!res.ok || !json.success) {
            document.getElementById('participantsTableBody').innerHTML = `<tr><td colspan="5" style="text-align: center; padding: 1.5rem; color: var(--vx-danger);">Gagal memuat peserta kelompok mapel.</td></tr>`;
            return;
        }

        const grp = json.group || {};
        const clsNames = Array.isArray(grp.classes) ? grp.classes.join(', ') : '-';
        const badges = [
            `<span class="vx-badge badge-dark cell-mono">${escapeHtml(grp.code || '')}</span>`,
            `<span class="vx-badge badge-primary"><i class='bx bxs-school'></i> ${escapeHtml(clsNames)}</span>`,
            grp.religion_filter
                ? `<span class="vx-badge badge-info"><i class='bx bx-id-card'></i> Filter Biodata Agama: ${escapeHtml(grp.religion_filter)}</span>`
                : `<span class="vx-badge badge-warning">Mode: ${escapeHtml(grp.membership_mode || 'ALL')}</span>`,
            `<span class="vx-badge badge-success">Total: ${json.count ?? 0} Siswa</span>`
        ].join('');

        renderParticipantsModalContent(`Siswa Kelompok: ${grp.name || ''}`, badges, json.data || []);
    }

    // Restore active view from URL query parameter on load
    document.addEventListener('DOMContentLoaded', () => {
        const urlParams = new URLSearchParams(window.location.search);
        const initialView = urlParams.get('view');
        if (initialView && document.getElementById(initialView)) {
            switchScheduleView(initialView);
        }
    });
</script>
@endpush

