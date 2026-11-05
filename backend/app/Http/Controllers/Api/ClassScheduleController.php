<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ClassSchedule;
use App\Models\LessonPeriod;
use App\Models\SchoolClass;
use App\Models\SchoolRoom;
use App\Models\Subject;
use App\Models\TeachingGroup;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClassScheduleController extends Controller
{
    /**
     * List schedules with filters (class_id, teacher_id, room_id, category_id, day_of_week, is_combined, teaching_group_id).
     */
    public function index(Request $request): JsonResponse
    {
        $schoolId = $request->query('school_id', $request->user()?->school_id ?? 1);
        $categoryId = $request->query('category_id');
        $academicYearId = $request->query('academic_year_id');
        $semester = $request->query('academic_semester');
        $classId = $request->query('class_id');
        $teacherId = $request->query('teacher_id');
        $roomId = $request->query('room_id');
        $roomName = $request->query('room_name');
        $day = $request->query('day_of_week');
        $isCombined = $request->query('is_combined');
        $teachingGroupId = $request->query('teaching_group_id');

        $query = ClassSchedule::with([
            'schoolClass',
            'combinedClasses',
            'teachingGroup.classes',
            'subject',
            'teacher',
            'lessonPeriod',
            'academicYear',
            'room',
            'category',
        ])->where('school_id', $schoolId);

        if ($categoryId) {
            $query->where(function ($q) use ($categoryId) {
                $q->where('category_id', $categoryId)
                    ->orWhereHas('lessonPeriod', function ($lp) use ($categoryId) {
                        $lp->where('category_id', $categoryId);
                    });
            });
        }

        if ($academicYearId) {
            $query->where('academic_year_id', $academicYearId);
        }

        if ($semester) {
            $query->where('academic_semester', $semester);
        }

        if ($isCombined !== null && $isCombined !== '') {
            $query->where('is_combined', filter_var($isCombined, FILTER_VALIDATE_BOOLEAN));
        }

        if ($teachingGroupId) {
            $query->where('teaching_group_id', $teachingGroupId);
        }

        if ($classId) {
            $query->where(function ($q) use ($classId) {
                $q->where('class_id', $classId)
                    ->orWhereHas('combinedClasses', fn ($cc) => $cc->where('classes.id', $classId));
            });
        }

        if ($teacherId) {
            $query->where('teacher_id', $teacherId);
        }

        if ($roomId) {
            $query->where('room_id', $roomId);
        } elseif ($roomName) {
            $query->where('room_name', $roomName);
        }

        if ($day) {
            $query->whereHas('lessonPeriod', function ($q) use ($day) {
                $q->where('day_of_week', $day);
            });
        }

        $schedules = $query->get();

        return response()->json([
            'success' => true,
            'data' => $schedules,
        ]);
    }

    /**
     * Return resolved student roster for a schedule slot (reading biodata religion or teaching group).
     */
    public function participants(int $id): JsonResponse
    {
        $schedule = ClassSchedule::with([
            'schoolClass',
            'combinedClasses',
            'teachingGroup.classes',
            'teachingGroup.manualStudents',
            'subject',
            'teacher',
            'room',
            'lessonPeriod',
        ])->findOrFail($id);

        $students = $schedule->resolveParticipants();
        $effectiveReligion = $schedule->effective_religion_filter;

        $filterSource = 'SELURUH_SISWA_ROMBEL';
        if ($schedule->teaching_group_id && $schedule->teachingGroup?->membership_mode === 'MANUAL') {
            $filterSource = 'KELOMPOK_MAPEL_PILIHAN';
        } elseif ($effectiveReligion) {
            $filterSource = 'BIODATA_AGAMA_SISWA';
        } elseif ($schedule->is_combined) {
            $filterSource = 'ROMBEL_GABUNGAN';
        }

        return response()->json([
            'success' => true,
            'schedule' => [
                'id' => $schedule->id,
                'is_combined' => $schedule->is_combined,
                'subject_name' => $schedule->subject?->name,
                'subject_code' => $schedule->subject?->code,
                'teacher_name' => $schedule->teacher?->full_name,
                'room_name' => $schedule->room?->name ?? $schedule->room_name,
                'classes_label' => $schedule->participating_class_names,
                'teaching_group_name' => $schedule->teachingGroup?->name,
                'effective_religion_filter' => $effectiveReligion,
                'filter_source' => $filterSource,
            ],
            'count' => $students->count(),
            'data' => $students->map(fn ($st) => [
                'id' => $st->id,
                'nis' => $st->nis,
                'nisn' => $st->nisn,
                'full_name' => $st->full_name,
                'gender' => $st->gender,
                'religion' => $st->religion,
                'class_id' => $st->class_id,
                'class_name' => $st->schoolClass?->name ?? '-',
            ])->values(),
        ]);
    }

    /**
     * Store one or multiple (range) class schedule items (Regular or Combined Multi-Class) with Smart Conflict Validation.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'school_id' => 'required|exists:schools,id',
            'category_id' => 'nullable|exists:time_schedule_categories,id',
            'class_id' => 'nullable|exists:classes,id',
            'class_ids' => 'nullable|array',
            'class_ids.*' => 'exists:classes,id',
            'is_combined' => 'nullable|boolean',
            'teaching_group_id' => 'nullable|exists:teaching_groups,id',
            'student_filter_type' => 'nullable|in:AUTO,ALL,RELIGION,TEACHING_GROUP',
            'student_filter_value' => 'nullable|string|max:40',
            'subject_id' => 'nullable|exists:subjects,id',
            'teacher_id' => 'nullable|exists:teachers,id',
            'lesson_period_id' => 'required|exists:lesson_periods,id',
            'end_lesson_period_id' => 'nullable|exists:lesson_periods,id',
            'lesson_period_ids' => 'nullable|array',
            'lesson_period_ids.*' => 'exists:lesson_periods,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'academic_semester' => 'required|in:odd,even',
            'room_id' => 'nullable|exists:school_rooms,id',
            'room_name' => 'nullable|string|max:50',
            'notes' => 'nullable|string|max:150',
            'replace_existing' => 'nullable|boolean',
        ]);

        // Resolve TeachingGroup defaults if provided
        $teachingGroup = ! empty($validated['teaching_group_id'])
            ? TeachingGroup::with('classes')->findOrFail($validated['teaching_group_id'])
            : null;

        $classIds = ! empty($validated['class_ids'])
            ? array_values(array_unique(array_map('intval', $validated['class_ids'])))
            : [];

        if (empty($classIds) && $teachingGroup) {
            $classIds = $teachingGroup->classes->pluck('id')->map(fn ($id) => (int) $id)->values()->all();
        }
        if (empty($classIds) && ! empty($validated['class_id'])) {
            $classIds = [(int) $validated['class_id']];
        }

        if (empty($classIds)) {
            return response()->json([
                'success' => false,
                'message' => 'Pilih minimal satu kelas/rombel peserta jadwal.',
            ], 422);
        }

        $primaryClassId = $classIds[0];
        $isCombined = ! empty($validated['is_combined']) || count($classIds) > 1 || $teachingGroup !== null;

        $subjectId = $validated['subject_id'] ?? $teachingGroup?->subject_id;
        $teacherId = $validated['teacher_id'] ?? $teachingGroup?->teacher_id;

        if (! $subjectId || ! $teacherId) {
            return response()->json([
                'success' => false,
                'message' => 'Mata pelajaran dan guru pengajar wajib dipilih.',
            ], 422);
        }

        $subjectObj = Subject::findOrFail($subjectId);
        $studentFilterType = $validated['student_filter_type'] ?? ($teachingGroup ? 'TEACHING_GROUP' : 'AUTO');
        $studentFilterValue = $validated['student_filter_value'] ?? null;

        if ($studentFilterType === 'AUTO' && $subjectObj->resolved_religion_filter) {
            $studentFilterType = 'RELIGION';
            $studentFilterValue = $subjectObj->resolved_religion_filter;
        } elseif ($teachingGroup && $teachingGroup->membership_mode === 'RELIGION' && ! $studentFilterValue) {
            $studentFilterValue = $teachingGroup->religion_filter ?: $subjectObj->resolved_religion_filter;
        }

        $startPeriod = LessonPeriod::findOrFail($validated['lesson_period_id']);
        $categoryId = $validated['category_id'] ?? $startPeriod->category_id;

        // Resolve room_id & room_name
        $roomId = $validated['room_id'] ?? ($teachingGroup?->default_room_id);
        $roomName = $validated['room_name'] ?? null;

        if ($roomId) {
            $roomObj = SchoolRoom::find($roomId);
            if ($roomObj && ! $roomName) {
                $roomName = $roomObj->name;
            }
        } elseif ($roomName) {
            $matchedRoom = SchoolRoom::where('school_id', $validated['school_id'])
                ->where(function ($q) use ($roomName) {
                    $q->where('name', $roomName)->orWhere('code', $roomName);
                })->first();
            if ($matchedRoom) {
                $roomId = $matchedRoom->id;
            }
        } elseif (! $isCombined) {
            // Auto-fill from class default_room_id ONLY for regular single-class schedules
            $schoolClass = SchoolClass::with('defaultRoom')->find($primaryClassId);
            if ($schoolClass && $schoolClass->defaultRoom) {
                $roomId = $schoolClass->defaultRoom->id;
                $roomName = $schoolClass->defaultRoom->name;
            }
        }

        // Determine target lesson periods (single or range on the same day)
        $targetPeriods = collect([$startPeriod]);

        if (! empty($validated['lesson_period_ids'])) {
            $targetPeriods = LessonPeriod::whereIn('id', $validated['lesson_period_ids'])
                ->where('is_break', false)
                ->orderBy('start_time')
                ->get();
        } elseif (! empty($validated['end_lesson_period_id']) && (int) $validated['end_lesson_period_id'] !== (int) $startPeriod->id) {
            $endPeriod = LessonPeriod::findOrFail($validated['end_lesson_period_id']);
            if ($endPeriod->category_id === $startPeriod->category_id && $endPeriod->day_of_week === $startPeriod->day_of_week) {
                $minTime = min($startPeriod->start_time, $endPeriod->start_time);
                $maxTime = max($startPeriod->start_time, $endPeriod->start_time);
                $targetPeriods = LessonPeriod::where('category_id', $startPeriod->category_id)
                    ->where('day_of_week', $startPeriod->day_of_week)
                    ->where('is_break', false)
                    ->whereBetween('start_time', [$minTime, $maxTime])
                    ->orderBy('start_time')
                    ->get();
            }
        }

        $daysName = [1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis', 5 => 'Jumat', 6 => 'Sabtu', 7 => 'Minggu'];
        $replaceExisting = ! empty($validated['replace_existing']);

        foreach ($targetPeriods as $period) {
            $dayLabel = $daysName[$period->day_of_week] ?? "Hari {$period->day_of_week}";
            $periodLabel = "{$dayLabel} ({$period->name})";

            // 1. Check Teacher Conflict
            $teacherConflict = ClassSchedule::with(['schoolClass', 'combinedClasses', 'subject', 'teacher'])
                ->where('teacher_id', $teacherId)
                ->where('lesson_period_id', $period->id)
                ->where('academic_year_id', $validated['academic_year_id'])
                ->where('academic_semester', $validated['academic_semester'])
                ->when($replaceExisting && ! $isCombined, fn ($q) => $q->where(function ($sub) use ($primaryClassId) {
                    $sub->where('class_id', '!=', $primaryClassId)->orWhere('is_combined', true);
                }))
                ->when($replaceExisting && $isCombined && $teachingGroup, fn ($q) => $q->where('teaching_group_id', '!=', $teachingGroup->id))
                ->first();

            if ($teacherConflict) {
                $conflictClass = $teacherConflict->participating_class_names;
                $conflictSubj = $teacherConflict->subject?->name ?? 'mapel lain';

                return response()->json([
                    'success' => false,
                    'conflict_type' => 'TEACHER',
                    'message' => "Jadwal bentrok! Guru tersebut sudah memiliki jadwal mengajar ({$conflictSubj}) di kelas {$conflictClass} pada {$periodLabel}.",
                ], 422);
            }

            // 2. Check Room Conflict
            if ($roomId || $roomName) {
                $roomConflict = ClassSchedule::with(['schoolClass', 'combinedClasses', 'teacher', 'room'])
                    ->where('lesson_period_id', $period->id)
                    ->where('academic_year_id', $validated['academic_year_id'])
                    ->where('academic_semester', $validated['academic_semester'])
                    ->when(! $isCombined, fn ($q) => $q->where(function ($sub) use ($primaryClassId) {
                        $sub->where('class_id', '!=', $primaryClassId)->orWhere('is_combined', true);
                    }))
                    ->when($isCombined && $teachingGroup && $replaceExisting, fn ($q) => $q->where('teaching_group_id', '!=', $teachingGroup->id))
                    ->where(function ($q) use ($roomId, $roomName) {
                        if ($roomId) {
                            $q->where('room_id', $roomId);
                        } else {
                            $q->where('room_name', $roomName);
                        }
                    })
                    ->first();

                if ($roomConflict) {
                    $conflictClass = $roomConflict->participating_class_names;
                    $conflictRoom = $roomConflict->room?->name ?? $roomConflict->room_name ?? $roomName;

                    return response()->json([
                        'success' => false,
                        'conflict_type' => 'ROOM',
                        'message' => "Ruangan bentrok! Ruang '{$conflictRoom}' sedang digunakan oleh kelas {$conflictClass} pada {$periodLabel}.",
                    ], 422);
                }
            }

            // 3. Check Slot Conflict
            if (! $isCombined) {
                // Regular schedule checks only regular slots of the same class
                if (! $replaceExisting) {
                    $classConflict = ClassSchedule::with(['subject', 'teacher'])
                        ->where('class_id', $primaryClassId)
                        ->where('is_combined', false)
                        ->where('lesson_period_id', $period->id)
                        ->where('academic_year_id', $validated['academic_year_id'])
                        ->where('academic_semester', $validated['academic_semester'])
                        ->first();

                    if ($classConflict) {
                        $conflictSubj = $classConflict->subject?->name ?? 'Mata Pelajaran lain';

                        return response()->json([
                            'success' => false,
                            'conflict_type' => 'CLASS',
                            'message' => "Slot kelas sudah terisi oleh '{$conflictSubj}' pada {$periodLabel}. Centang opsi 'Timpa Slot' atau edit slot tersebut.",
                        ], 422);
                    }
                }
            } else {
                // Combined schedule: prevent duplicate combined schedule for the exact same group/subject on overlapping classes
                if (! $replaceExisting) {
                    $dupCombined = ClassSchedule::with(['subject', 'combinedClasses'])
                        ->where('is_combined', true)
                        ->where('lesson_period_id', $period->id)
                        ->where('academic_year_id', $validated['academic_year_id'])
                        ->where('academic_semester', $validated['academic_semester'])
                        ->where(function ($q) use ($teachingGroup, $subjectId, $classIds) {
                            if ($teachingGroup) {
                                $q->where('teaching_group_id', $teachingGroup->id);
                            } else {
                                $q->where('subject_id', $subjectId)
                                    ->whereHas('combinedClasses', fn ($cc) => $cc->whereIn('classes.id', $classIds));
                            }
                        })
                        ->first();

                    if ($dupCombined) {
                        $conflictSubj = $dupCombined->subject?->name ?? 'Mapel Gabungan';

                        return response()->json([
                            'success' => false,
                            'conflict_type' => 'COMBINED_SLOT',
                            'message' => "Kelas gabungan untuk '{$conflictSubj}' sudah terjadwal pada {$periodLabel}. Centang opsi 'Timpa Slot' jika ingin memperbaruinya.",
                        ], 422);
                    }
                }
            }
        }

        $savedSchedules = DB::transaction(function () use (
            $targetPeriods,
            $validated,
            $categoryId,
            $primaryClassId,
            $classIds,
            $isCombined,
            $teachingGroup,
            $studentFilterType,
            $studentFilterValue,
            $subjectId,
            $teacherId,
            $roomId,
            $roomName,
            $replaceExisting
        ) {
            $items = [];
            foreach ($targetPeriods as $period) {
                if ($replaceExisting) {
                    if (! $isCombined) {
                        ClassSchedule::where('class_id', $primaryClassId)
                            ->where('is_combined', false)
                            ->where('lesson_period_id', $period->id)
                            ->where('academic_year_id', $validated['academic_year_id'])
                            ->where('academic_semester', $validated['academic_semester'])
                            ->delete();
                    } else {
                        ClassSchedule::where('is_combined', true)
                            ->where('lesson_period_id', $period->id)
                            ->where('academic_year_id', $validated['academic_year_id'])
                            ->where('academic_semester', $validated['academic_semester'])
                            ->where(function ($q) use ($teachingGroup, $subjectId, $classIds) {
                                if ($teachingGroup) {
                                    $q->where('teaching_group_id', $teachingGroup->id);
                                } else {
                                    $q->where('subject_id', $subjectId)
                                        ->whereHas('combinedClasses', fn ($cc) => $cc->whereIn('classes.id', $classIds));
                                }
                            })
                            ->delete();
                    }
                }

                $schedule = ClassSchedule::create([
                    'school_id' => $validated['school_id'],
                    'category_id' => $categoryId,
                    'class_id' => $primaryClassId,
                    'is_combined' => $isCombined,
                    'teaching_group_id' => $teachingGroup?->id,
                    'student_filter_type' => $studentFilterType,
                    'student_filter_value' => $studentFilterValue,
                    'subject_id' => $subjectId,
                    'teacher_id' => $teacherId,
                    'lesson_period_id' => $period->id,
                    'academic_year_id' => $validated['academic_year_id'],
                    'academic_semester' => $validated['academic_semester'],
                    'room_id' => $roomId,
                    'room_name' => $roomName,
                    'notes' => $validated['notes'] ?? null,
                ]);

                if ($isCombined) {
                    $schedule->combinedClasses()->sync($classIds);
                }

                $items[] = $schedule->load(['schoolClass', 'combinedClasses', 'teachingGroup', 'subject', 'teacher', 'lessonPeriod', 'room', 'category']);
            }

            return $items;
        });

        return response()->json([
            'success' => true,
            'message' => count($savedSchedules) > 1
                ? count($savedSchedules).' sesi jadwal pelajaran berhasil disimpan.'
                : 'Jadwal pelajaran berhasil ditambahkan.',
            'data' => $savedSchedules[0],
            'items' => $savedSchedules,
        ], 201);
    }

    /**
     * Update an existing schedule slot (or a merged block of schedule slots), supporting both regular and combined schedules.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $schedule = ClassSchedule::with('combinedClasses')->findOrFail($id);

        $validated = $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'teacher_id' => 'required|exists:teachers,id',
            'class_ids' => 'nullable|array',
            'class_ids.*' => 'exists:classes,id',
            'teaching_group_id' => 'nullable|exists:teaching_groups,id',
            'student_filter_type' => 'nullable|in:AUTO,ALL,RELIGION,TEACHING_GROUP',
            'student_filter_value' => 'nullable|string|max:40',
            'lesson_period_id' => 'nullable|exists:lesson_periods,id',
            'end_lesson_period_id' => 'nullable|exists:lesson_periods,id',
            'room_id' => 'nullable|exists:school_rooms,id',
            'room_name' => 'nullable|string|max:50',
            'notes' => 'nullable|string|max:150',
            'schedule_ids' => 'nullable|array',
            'schedule_ids.*' => 'exists:class_schedules,id',
            'replace_existing' => 'nullable|boolean',
        ]);

        $targetIds = ! empty($validated['schedule_ids'])
            ? array_map('intval', $validated['schedule_ids'])
            : [$schedule->id];

        $isCombined = $schedule->is_combined;
        $classIds = ! empty($validated['class_ids'])
            ? array_values(array_unique(array_map('intval', $validated['class_ids'])))
            : $schedule->participating_class_ids;

        if (count($classIds) > 1) {
            $isCombined = true;
        }
        $primaryClassId = $classIds[0] ?? $schedule->class_id;

        $teachingGroupId = array_key_exists('teaching_group_id', $validated)
            ? $validated['teaching_group_id']
            : $schedule->teaching_group_id;

        $subjectObj = Subject::findOrFail($validated['subject_id']);
        $studentFilterType = $validated['student_filter_type'] ?? $schedule->student_filter_type ?? 'AUTO';
        $studentFilterValue = array_key_exists('student_filter_value', $validated)
            ? $validated['student_filter_value']
            : $schedule->student_filter_value;

        if ($studentFilterType === 'AUTO' && $subjectObj->resolved_religion_filter) {
            $studentFilterType = 'RELIGION';
            $studentFilterValue = $subjectObj->resolved_religion_filter;
        }

        $replaceExisting = ! empty($validated['replace_existing']);
        $roomId = $validated['room_id'] ?? null;
        $roomName = $validated['room_name'] ?? null;

        if ($roomId) {
            $roomObj = SchoolRoom::find($roomId);
            if ($roomObj && ! $roomName) {
                $roomName = $roomObj->name;
            }
        }

        if (! empty($validated['lesson_period_id'])) {
            $startPeriod = LessonPeriod::findOrFail($validated['lesson_period_id']);
            $targetPeriods = collect([$startPeriod]);

            if (! empty($validated['end_lesson_period_id']) && (int) $validated['end_lesson_period_id'] !== (int) $startPeriod->id) {
                $endPeriod = LessonPeriod::findOrFail($validated['end_lesson_period_id']);
                if ($endPeriod->category_id === $startPeriod->category_id && $endPeriod->day_of_week === $startPeriod->day_of_week) {
                    $minTime = min($startPeriod->start_time, $endPeriod->start_time);
                    $maxTime = max($startPeriod->start_time, $endPeriod->start_time);
                    $targetPeriods = LessonPeriod::where('category_id', $startPeriod->category_id)
                        ->where('day_of_week', $startPeriod->day_of_week)
                        ->where('is_break', false)
                        ->whereBetween('start_time', [$minTime, $maxTime])
                        ->orderBy('start_time')
                        ->get();
                }
            }
        } else {
            $existingPeriodIds = ClassSchedule::whereIn('id', $targetIds)->pluck('lesson_period_id');
            $targetPeriods = LessonPeriod::whereIn('id', $existingPeriodIds)->orderBy('start_time')->get();
        }

        $daysName = [1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis', 5 => 'Jumat', 6 => 'Sabtu', 7 => 'Minggu'];

        foreach ($targetPeriods as $period) {
            $dayLabel = $daysName[$period->day_of_week] ?? "Hari {$period->day_of_week}";
            $periodLabel = "{$dayLabel} ({$period->name})";

            // 1. Check Teacher Conflict (excluding current merged block ids)
            $teacherConflict = ClassSchedule::with(['schoolClass', 'combinedClasses', 'subject'])
                ->whereNotIn('id', $targetIds)
                ->where('teacher_id', $validated['teacher_id'])
                ->where('lesson_period_id', $period->id)
                ->where('academic_year_id', $schedule->academic_year_id)
                ->where('academic_semester', $schedule->academic_semester)
                ->when($replaceExisting && ! $isCombined, fn ($q) => $q->where(function ($sub) use ($primaryClassId) {
                    $sub->where('class_id', '!=', $primaryClassId)->orWhere('is_combined', true);
                }))
                ->first();

            if ($teacherConflict) {
                return response()->json([
                    'success' => false,
                    'conflict_type' => 'TEACHER',
                    'message' => 'Jadwal bentrok! Guru tersebut sudah mengajar di kelas '.$teacherConflict->participating_class_names." pada {$periodLabel}.",
                ], 422);
            }

            // 2. Check Room Conflict (excluding current merged block ids)
            if ($roomId || $roomName) {
                $roomConflict = ClassSchedule::with(['schoolClass', 'combinedClasses', 'room'])
                    ->whereNotIn('id', $targetIds)
                    ->when(! $isCombined, fn ($q) => $q->where(function ($sub) use ($primaryClassId) {
                        $sub->where('class_id', '!=', $primaryClassId)->orWhere('is_combined', true);
                    }))
                    ->where('lesson_period_id', $period->id)
                    ->where('academic_year_id', $schedule->academic_year_id)
                    ->where('academic_semester', $schedule->academic_semester)
                    ->where(function ($q) use ($roomId, $roomName) {
                        if ($roomId) {
                            $q->where('room_id', $roomId);
                        } else {
                            $q->where('room_name', $roomName);
                        }
                    })
                    ->first();

                if ($roomConflict) {
                    return response()->json([
                        'success' => false,
                        'conflict_type' => 'ROOM',
                        'message' => 'Ruangan bentrok! Ruangan tersebut sedang dipakai oleh kelas '.$roomConflict->participating_class_names." pada {$periodLabel}.",
                    ], 422);
                }
            }

            // 3. Check Class Slot Conflict (for regular schedules)
            if (! $isCombined && ! $replaceExisting) {
                $classConflict = ClassSchedule::with('subject')
                    ->whereNotIn('id', $targetIds)
                    ->where('class_id', $primaryClassId)
                    ->where('is_combined', false)
                    ->where('lesson_period_id', $period->id)
                    ->where('academic_year_id', $schedule->academic_year_id)
                    ->where('academic_semester', $schedule->academic_semester)
                    ->first();

                if ($classConflict) {
                    $conflictSubj = $classConflict->subject?->name ?? 'Mata Pelajaran lain';

                    return response()->json([
                        'success' => false,
                        'conflict_type' => 'CLASS',
                        'message' => "Slot kelas pada {$periodLabel} sudah terisi oleh '{$conflictSubj}'. Centang opsi 'Timpa Slot' jika ingin menggantinya.",
                    ], 422);
                }
            }
        }

        $updatedItems = DB::transaction(function () use (
            $schedule,
            $targetIds,
            $targetPeriods,
            $validated,
            $isCombined,
            $primaryClassId,
            $classIds,
            $teachingGroupId,
            $studentFilterType,
            $studentFilterValue,
            $roomId,
            $roomName,
            $replaceExisting
        ) {
            $baseSchoolId = $schedule->school_id;
            $baseYearId = $schedule->academic_year_id;
            $baseSemester = $schedule->academic_semester;
            $baseCategoryId = $schedule->category_id ?? $targetPeriods->first()?->category_id;

            ClassSchedule::whereIn('id', $targetIds)->delete();

            $items = [];
            foreach ($targetPeriods as $period) {
                if ($replaceExisting && ! $isCombined) {
                    ClassSchedule::where('class_id', $primaryClassId)
                        ->where('is_combined', false)
                        ->where('lesson_period_id', $period->id)
                        ->where('academic_year_id', $baseYearId)
                        ->where('academic_semester', $baseSemester)
                        ->delete();
                }

                $created = ClassSchedule::create([
                    'school_id' => $baseSchoolId,
                    'category_id' => $baseCategoryId,
                    'class_id' => $primaryClassId,
                    'is_combined' => $isCombined,
                    'teaching_group_id' => $teachingGroupId,
                    'student_filter_type' => $studentFilterType,
                    'student_filter_value' => $studentFilterValue,
                    'subject_id' => $validated['subject_id'],
                    'teacher_id' => $validated['teacher_id'],
                    'lesson_period_id' => $period->id,
                    'academic_year_id' => $baseYearId,
                    'academic_semester' => $baseSemester,
                    'room_id' => $roomId,
                    'room_name' => $roomName,
                    'notes' => $validated['notes'] ?? null,
                ]);

                if ($isCombined) {
                    $created->combinedClasses()->sync($classIds);
                }

                $items[] = $created->load(['schoolClass', 'combinedClasses', 'teachingGroup', 'subject', 'teacher', 'lessonPeriod', 'room', 'category']);
            }

            return $items;
        });

        return response()->json([
            'success' => true,
            'message' => 'Jadwal pelajaran & jam KBM berhasil diperbarui.',
            'data' => $updatedItems[0] ?? null,
            'items' => $updatedItems,
        ]);
    }

    /**
     * Delete a schedule item (or a merged block of schedule items if ids query/body is provided).
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        $idsParam = $request->input('schedule_ids') ?? $request->query('ids');
        if ($idsParam) {
            $ids = is_array($idsParam) ? $idsParam : explode(',', (string) $idsParam);
            ClassSchedule::whereIn('id', array_map('intval', $ids))->delete();
        } else {
            $schedule = ClassSchedule::findOrFail($id);
            $schedule->delete();
        }

        return response()->json([
            'success' => true,
            'message' => 'Jadwal pelajaran berhasil dihapus.',
        ]);
    }

    /**
     * Copy / Clone schedules (including Combined Classes & Teaching Group links) from a previous semester/category.
     */
    public function copyFromSemester(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'school_id' => 'required|exists:schools,id',
            'source_academic_year_id' => 'required|exists:academic_years,id',
            'source_semester' => 'required|in:odd,even',
            'source_category_id' => 'nullable|exists:time_schedule_categories,id',
            'target_academic_year_id' => 'required|exists:academic_years,id',
            'target_semester' => 'required|in:odd,even',
            'target_category_id' => 'nullable|exists:time_schedule_categories,id',
            'class_id' => 'nullable|exists:classes,id',
            'overwrite' => 'nullable|boolean',
        ]);

        $schoolId = (int) $validated['school_id'];
        $sourceYearId = (int) $validated['source_academic_year_id'];
        $sourceSemester = $validated['source_semester'];
        $sourceCategoryId = $validated['source_category_id'] ?? null;

        $targetYearId = (int) $validated['target_academic_year_id'];
        $targetSemester = $validated['target_semester'];
        $targetCategoryId = $validated['target_category_id'] ?? $sourceCategoryId;
        $filterClassId = $validated['class_id'] ?? null;
        $overwrite = ! empty($validated['overwrite']);

        if (
            $sourceYearId === $targetYearId &&
            $sourceSemester === $targetSemester &&
            (int) $sourceCategoryId === (int) $targetCategoryId
        ) {
            return response()->json([
                'success' => false,
                'message' => 'Sumber dan Tujuan salin jadwal tidak boleh berada pada Tahun Ajaran, Semester, dan Kategori yang persis sama.',
            ], 422);
        }

        $sourceQuery = ClassSchedule::with(['lessonPeriod', 'combinedClasses'])
            ->where('school_id', $schoolId)
            ->where('academic_year_id', $sourceYearId)
            ->where('academic_semester', $sourceSemester);

        if ($sourceCategoryId) {
            $sourceQuery->where(function ($q) use ($sourceCategoryId) {
                $q->where('category_id', $sourceCategoryId)
                    ->orWhereHas('lessonPeriod', fn ($lp) => $lp->where('category_id', $sourceCategoryId));
            });
        }

        if ($filterClassId) {
            $sourceQuery->where(function ($q) use ($filterClassId) {
                $q->where('class_id', $filterClassId)
                    ->orWhereHas('combinedClasses', fn ($cc) => $cc->where('classes.id', $filterClassId));
            });
        }

        $sourceSchedules = $sourceQuery->get();

        if ($sourceSchedules->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ditemukan data jadwal pada periode sumber yang dipilih untuk disalin.',
            ], 422);
        }

        $copiedCount = 0;
        $overwrittenCount = 0;
        $skippedCount = 0;

        DB::transaction(function () use (
            $sourceSchedules,
            $schoolId,
            $targetYearId,
            $targetSemester,
            $targetCategoryId,
            $overwrite,
            &$copiedCount,
            &$overwrittenCount,
            &$skippedCount
        ) {
            if ($targetCategoryId) {
                $targetPeriodCount = LessonPeriod::where('category_id', $targetCategoryId)->count();
                $firstSourceCategoryId = $sourceSchedules->first()?->lessonPeriod?->category_id;

                if ($targetPeriodCount === 0 && $firstSourceCategoryId && (int) $firstSourceCategoryId !== (int) $targetCategoryId) {
                    $srcPeriods = LessonPeriod::where('category_id', $firstSourceCategoryId)->get();
                    foreach ($srcPeriods as $sp) {
                        LessonPeriod::create([
                            'category_id' => $targetCategoryId,
                            'school_id' => $schoolId,
                            'day_of_week' => $sp->day_of_week,
                            'name' => $sp->name,
                            'period_number' => $sp->period_number,
                            'start_time' => substr($sp->start_time, 0, 5),
                            'end_time' => substr($sp->end_time, 0, 5),
                            'is_break' => $sp->is_break,
                        ]);
                    }
                }
            }

            foreach ($sourceSchedules as $src) {
                $srcPeriod = $src->lessonPeriod;
                if (! $srcPeriod) {
                    $skippedCount++;
                    continue;
                }

                $targetPeriodId = $src->lesson_period_id;
                $resolvedCategoryId = $targetCategoryId ?? $src->category_id ?? $srcPeriod->category_id;

                if ($targetCategoryId && (int) $srcPeriod->category_id !== (int) $targetCategoryId) {
                    $matchedPeriod = LessonPeriod::where('category_id', $targetCategoryId)
                        ->where('day_of_week', $srcPeriod->day_of_week)
                        ->where(function ($q) use ($srcPeriod) {
                            if ($srcPeriod->period_number !== null) {
                                $q->where('period_number', $srcPeriod->period_number);
                            } else {
                                $q->where('name', $srcPeriod->name);
                            }
                        })
                        ->first();

                    if (! $matchedPeriod) {
                        $skippedCount++;
                        continue;
                    }
                    $targetPeriodId = $matchedPeriod->id;
                }

                $existingQuery = ClassSchedule::where('school_id', $schoolId)
                    ->where('is_combined', $src->is_combined)
                    ->where('lesson_period_id', $targetPeriodId)
                    ->where('academic_year_id', $targetYearId)
                    ->where('academic_semester', $targetSemester);

                if ($src->is_combined) {
                    $existingQuery->where('subject_id', $src->subject_id)
                        ->where('teacher_id', $src->teacher_id);
                } else {
                    $existingQuery->where('class_id', $src->class_id);
                }

                $existingSlot = $existingQuery->first();

                if ($existingSlot && ! $overwrite) {
                    $skippedCount++;
                    continue;
                }

                $teacherBusy = ClassSchedule::where('school_id', $schoolId)
                    ->where('teacher_id', $src->teacher_id)
                    ->where('lesson_period_id', $targetPeriodId)
                    ->where('academic_year_id', $targetYearId)
                    ->where('academic_semester', $targetSemester)
                    ->when($existingSlot, fn ($q) => $q->where('id', '!=', $existingSlot->id))
                    ->exists();

                if ($teacherBusy && ! $overwrite) {
                    $skippedCount++;
                    continue;
                }

                if ($existingSlot && $overwrite) {
                    $existingSlot->update([
                        'category_id' => $resolvedCategoryId,
                        'teaching_group_id' => $src->teaching_group_id,
                        'student_filter_type' => $src->student_filter_type,
                        'student_filter_value' => $src->student_filter_value,
                        'subject_id' => $src->subject_id,
                        'teacher_id' => $src->teacher_id,
                        'room_id' => $src->room_id,
                        'room_name' => $src->room_name,
                        'notes' => $src->notes,
                    ]);
                    if ($src->is_combined) {
                        $existingSlot->combinedClasses()->sync($src->combinedClasses->pluck('id')->all());
                    }
                    $overwrittenCount++;
                } else {
                    $created = ClassSchedule::create([
                        'school_id' => $schoolId,
                        'category_id' => $resolvedCategoryId,
                        'class_id' => $src->class_id,
                        'is_combined' => $src->is_combined,
                        'teaching_group_id' => $src->teaching_group_id,
                        'student_filter_type' => $src->student_filter_type,
                        'student_filter_value' => $src->student_filter_value,
                        'subject_id' => $src->subject_id,
                        'teacher_id' => $src->teacher_id,
                        'lesson_period_id' => $targetPeriodId,
                        'academic_year_id' => $targetYearId,
                        'academic_semester' => $targetSemester,
                        'room_id' => $src->room_id,
                        'room_name' => $src->room_name,
                        'notes' => $src->notes,
                    ]);
                    if ($src->is_combined) {
                        $created->combinedClasses()->sync($src->combinedClasses->pluck('id')->all());
                    }
                    $copiedCount++;
                }
            }
        });

        $totalProcessed = $copiedCount + $overwrittenCount;

        return response()->json([
            'success' => true,
            'message' => "Berhasil menyalin {$totalProcessed} sesi jadwal pelajaran ({$copiedCount} baru, {$overwrittenCount} diperbarui, {$skippedCount} dilewati).",
            'summary' => [
                'copied_count' => $copiedCount,
                'overwritten_count' => $overwrittenCount,
                'skipped_count' => $skippedCount,
                'total_source' => $sourceSchedules->count(),
            ],
        ]);
    }
}
