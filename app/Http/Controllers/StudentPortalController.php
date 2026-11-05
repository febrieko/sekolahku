<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\ClassSchedule;
use App\Models\DisciplineAmnestyRule;
use App\Models\DisciplineCategory;
use App\Models\DisciplineThreshold;
use App\Models\LessonPeriod;
use App\Models\School;
use App\Models\SchoolGeofence;
use App\Models\Student;
use App\Models\StudentDisciplineRecord;
use App\Models\TeachingGroup;
use App\Models\TimeScheduleCategory;
use App\Models\User;
use App\Services\DisciplineBootstrapService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class StudentPortalController extends Controller
{
    /**
     * Dashboard Utama Siswa setelah berhasil login.
     */
    public function dashboard(Request $request): View|RedirectResponse
    {
        $ctx = $this->resolveStudentContext($request);
        if ($ctx instanceof RedirectResponse) {
            return $ctx;
        }

        /** @var Student $student */
        $student = $ctx['student'];
        /** @var School $school */
        $school = $ctx['school'];
        $activeYear = $ctx['activeYear'];
        $activeSemester = $ctx['activeSemester'];

        $scheduleData = $this->buildPersonalScheduleData($student, $school, $activeYear, $activeSemester);
        $attendanceData = $this->buildAttendanceSummary($student, $school);
        $disciplineData = $this->buildDisciplineSummary($student, $school, $activeYear, $activeSemester);
        $profileStats = $this->buildProfileCompleteness($student, $school);

        return view('student.dashboard', array_merge($ctx, [
            'scheduleData' => $scheduleData,
            'attendanceData' => $attendanceData,
            'disciplineData' => $disciplineData,
            'profileStats' => $profileStats,
        ]));
    }

    /**
     * Halaman Jadwal Pelajaran Personal Siswa (Otomatis Gabung Kelas Reguler + Kelas Gabungan & Filter Biodata Agama).
     */
    public function schedules(Request $request): View|RedirectResponse
    {
        $ctx = $this->resolveStudentContext($request);
        if ($ctx instanceof RedirectResponse) {
            return $ctx;
        }

        /** @var Student $student */
        $student = $ctx['student'];
        /** @var School $school */
        $school = $ctx['school'];

        $scheduleData = $this->buildPersonalScheduleData($student, $school, $ctx['activeYear'], $ctx['activeSemester']);

        return view('student.schedules', array_merge($ctx, [
            'scheduleData' => $scheduleData,
        ]));
    }

    /**
     * Halaman Rekap & Statistik Kehadiran Personal Siswa.
     */
    public function attendance(Request $request): View|RedirectResponse
    {
        $ctx = $this->resolveStudentContext($request);
        if ($ctx instanceof RedirectResponse) {
            return $ctx;
        }

        /** @var Student $student */
        $student = $ctx['student'];
        /** @var School $school */
        $school = $ctx['school'];

        $attendanceData = $this->buildAttendanceSummary($student, $school, 60);
        $geofences = SchoolGeofence::where('school_id', $school->id)
            ->where('is_active', true)
            ->orderByDesc('is_primary')
            ->get();

        return view('student.attendance', array_merge($ctx, [
            'attendanceData' => $attendanceData,
            'geofences' => $geofences,
        ]));
    }

    /**
     * Halaman Tata Tertib, Saldo Kredit Poin & Buku Saku Aturan Sekolah untuk Siswa.
     */
    public function discipline(Request $request): View|RedirectResponse
    {
        $ctx = $this->resolveStudentContext($request);
        if ($ctx instanceof RedirectResponse) {
            return $ctx;
        }

        /** @var Student $student */
        $student = $ctx['student'];
        /** @var School $school */
        $school = $ctx['school'];

        $disciplineData = $this->buildDisciplineSummary($student, $school, $ctx['activeYear'], $ctx['activeSemester'], true);

        return view('student.discipline', array_merge($ctx, [
            'disciplineData' => $disciplineData,
        ]));
    }

    /**
     * Halaman Kartu Pelajar Digital 4x6 (Visual Passbook), QR Identitas & Riwayat Akademik Angkatan.
     */
    public function card(Request $request): View|RedirectResponse
    {
        $ctx = $this->resolveStudentContext($request);
        if ($ctx instanceof RedirectResponse) {
            return $ctx;
        }

        /** @var Student $student */
        $student = $ctx['student'];
        /** @var School $school */
        $school = $ctx['school'];

        $student->load(['classHistories.schoolClass', 'classHistories.academicYear', 'parents']);
        $profileStats = $this->buildProfileCompleteness($student, $school);

        return view('student.card', array_merge($ctx, [
            'profileStats' => $profileStats,
        ]));
    }

    /**
     * Resolve authenticated student safely (strictly locked for role=student; previewable by staff).
     *
     * @return array<string, mixed>|RedirectResponse
     */
    private function resolveStudentContext(Request $request): array|RedirectResponse
    {
        $user = Auth::user();
        if (! $user) {
            return redirect()->route('login');
        }

        $isStaffPreview = false;

        if ($user->role === 'student') {
            $student = Student::with([
                'schoolClass.homeroomTeacher',
                'schoolClass.defaultRoom',
                'schoolClass.department',
                'entryAcademicYear',
                'school',
                'parents',
            ])->where('user_id', $user->id)->first();

            if (! $student && $user->school_id) {
                $student = Student::with([
                    'schoolClass.homeroomTeacher',
                    'schoolClass.defaultRoom',
                    'schoolClass.department',
                    'entryAcademicYear',
                    'school',
                    'parents',
                ])
                    ->where('school_id', $user->school_id)
                    ->where('full_name', 'like', '%' . $user->name . '%')
                    ->first();
            }

            if (! $student) {
                $student = Student::with([
                    'schoolClass.homeroomTeacher',
                    'schoolClass.defaultRoom',
                    'schoolClass.department',
                    'entryAcademicYear',
                    'school',
                    'parents',
                ])->where('school_id', $user->school_id ?? 1)->first();
            }
        } elseif ($user->role === 'parent') {
            $studentId = $request->query('student_id');
            $parentGuardian = \App\Models\ParentGuardian::where('user_id', $user->id)->first();
            $childrenQuery = $parentGuardian
                ? $parentGuardian->students()->with(['schoolClass.homeroomTeacher', 'schoolClass.defaultRoom', 'schoolClass.department', 'entryAcademicYear', 'school'])
                : Student::with(['schoolClass.homeroomTeacher', 'schoolClass.defaultRoom', 'schoolClass.department', 'entryAcademicYear', 'school'])->where('school_id', $user->school_id ?? 1);

            $student = $studentId ? $childrenQuery->find($studentId) : $childrenQuery->first();
        } else {
            // Staff preview mode
            $isStaffPreview = true;
            $studentId = $request->query('student_id');
            $schoolId = session('active_school_id') ?? $user->school_id ?? 1;
            $query = Student::with([
                'schoolClass.homeroomTeacher',
                'schoolClass.defaultRoom',
                'schoolClass.department',
                'entryAcademicYear',
                'school',
                'parents',
            ])->where('school_id', $schoolId);

            $student = $studentId ? $query->find($studentId) : $query->first();
        }

        if (! $student) {
            return redirect()->route('attendance.portal')
                ->with('error', 'Data peserta didik belum terhubung dengan akun ini.');
        }

        $school = $student->school ?? School::find($student->school_id) ?? School::first();

        $academicYears = AcademicYear::with('semesters')
            ->where('school_id', $school->id)
            ->orderByDesc('is_active')
            ->orderByDesc('name')
            ->get();

        $activeYear = $academicYears->firstWhere('is_active', true) ?? $academicYears->first();
        $activeSemObj = $activeYear?->semesters?->firstWhere('is_active', true);
        $activeSemester = $activeSemObj?->semester ?? 'odd';

        $hour = (int) now()->format('H');
        if ($hour < 11) {
            $greeting = 'Selamat Pagi';
        } elseif ($hour < 15) {
            $greeting = 'Selamat Siang';
        } elseif ($hour < 18) {
            $greeting = 'Selamat Sore';
        } else {
            $greeting = 'Selamat Malam';
        }

        return [
            'user' => $user,
            'student' => $student,
            'school' => $school,
            'activeYear' => $activeYear,
            'activeSemester' => $activeSemester,
            'isStaffPreview' => $isStaffPreview,
            'greeting' => $greeting,
        ];
    }

    /**
     * Susun jadwal personal siswa (menggabungkan jadwal reguler kelas + jadwal kelas gabungan lintas rombel,
     * serta otomatis memfilter mata pelajaran agama berdasarkan Biodata Agama Siswa).
     *
     * @return array<string, mixed>
     */
    private function buildPersonalScheduleData(
        Student $student,
        School $school,
        ?AcademicYear $activeYear,
        string $activeSemester
    ): array {
        $todayDate = Carbon::today()->toDateString();

        // Pilih kategori pola jam yang sedang berlaku tanggal hari ini, atau fallback ke default
        $categories = TimeScheduleCategory::where('school_id', $school->id)
            ->where('is_active', true)
            ->orderByDesc('is_default')
            ->get();

        $activeCategory = $categories->first(function (TimeScheduleCategory $cat) use ($todayDate) {
            if (! $cat->effective_start_date && ! $cat->effective_end_date) {
                return false;
            }
            $afterStart = ! $cat->effective_start_date || $cat->effective_start_date->toDateString() <= $todayDate;
            $beforeEnd = ! $cat->effective_end_date || $cat->effective_end_date->toDateString() >= $todayDate;

            return $afterStart && $beforeEnd;
        }) ?? $categories->firstWhere('is_default', true) ?? $categories->first();

        $daysList = [
            1 => 'Senin',
            2 => 'Selasa',
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat',
            6 => 'Sabtu',
        ];

        $categoryPeriods = $activeCategory
            ? LessonPeriod::where('category_id', $activeCategory->id)
                ->orderBy('day_of_week')
                ->orderBy('start_time')
                ->get()
            : collect();

        // Hapus hari Sabtu jika tidak ada sesi KBM pada hari Sabtu
        if ($categoryPeriods->where('day_of_week', 6)->isEmpty()) {
            unset($daysList[6]);
        }

        // Ambil seluruh kandidat jadwal: kelas reguler siswa ATAU kelas gabungan yang mencakup kelas siswa / kelompok manual siswa
        $manualGroupIds = TeachingGroup::where('school_id', $school->id)
            ->where('membership_mode', 'MANUAL')
            ->whereHas('manualStudents', fn ($q) => $q->where('students.id', $student->id))
            ->pluck('id')
            ->all();

        $candidateSchedules = ClassSchedule::with([
            'schoolClass',
            'combinedClasses',
            'teachingGroup.classes',
            'teachingGroup.manualStudents',
            'subject',
            'teacher',
            'lessonPeriod',
            'room',
        ])
            ->where('school_id', $school->id)
            ->when($activeYear, fn ($q) => $q->where('academic_year_id', $activeYear->id))
            ->where('academic_semester', $activeSemester)
            ->when($activeCategory, fn ($q) => $q->where(function ($sub) use ($activeCategory) {
                $sub->where('category_id', $activeCategory->id)
                    ->orWhereHas('lessonPeriod', fn ($lp) => $lp->where('category_id', $activeCategory->id));
            }))
            ->where(function ($q) use ($student, $manualGroupIds) {
                if ($student->class_id) {
                    $q->where(function ($reg) use ($student) {
                        $reg->where('is_combined', false)->where('class_id', $student->class_id);
                    })->orWhere(function ($comb) use ($student) {
                        $comb->where('is_combined', true)
                            ->where(function ($cSub) use ($student) {
                                $cSub->where('class_id', $student->class_id)
                                    ->orWhereHas('combinedClasses', fn ($cc) => $cc->where('classes.id', $student->class_id));
                            });
                    });
                }
                if (! empty($manualGroupIds)) {
                    $q->orWhereIn('teaching_group_id', $manualGroupIds);
                }
            })
            ->get();

        // Filter kandidat jadwal sesuai Biodata Agama Siswa & keanggotaan Kelompok Mapel
        $studentReligionNorm = strtolower(trim((string) ($student->religion ?? '')));

        $personalSchedules = $candidateSchedules->filter(function (ClassSchedule $sched) use ($student, $studentReligionNorm) {
            if ($sched->teaching_group_id && $sched->teachingGroup) {
                $grp = $sched->teachingGroup;
                if ($grp->membership_mode === 'MANUAL') {
                    return $grp->manualStudents->contains('id', $student->id);
                }
            }

            $effectiveReligion = $sched->effective_religion_filter;
            if ($effectiveReligion) {
                if ($studentReligionNorm === '') {
                    return true; // Jika biodata agama belum diisi, tampilkan sementara agar tidak kosong
                }
                $synonyms = TeachingGroup::religionSynonyms($effectiveReligion);

                return in_array($studentReligionNorm, $synonyms, true);
            }

            return true;
        })->values();

        // Kelompokkan jadwal per lesson_period_id (jika ada reguler + gabungan di jam yang sama, prioritaskan kelas gabungan/pilihan yang spesifik diikuti siswa)
        $personalByPeriod = [];
        foreach ($personalSchedules as $sched) {
            $pid = $sched->lesson_period_id;
            if (! isset($personalByPeriod[$pid]) || $sched->is_combined) {
                $personalByPeriod[$pid] = $sched;
            }
        }

        // Bangun baris matriks mingguan & auto-merge rowspan untuk jam berurutan
        $periodRows = [];
        $periodsByDayAndRow = [];
        foreach ($categoryPeriods as $p) {
            $rowKey = ($p->is_break ? 'B_' : 'P_') . ($p->period_number ?? 'X') . '_' . substr((string) $p->start_time, 0, 5);
            if (! isset($periodRows[$rowKey])) {
                $periodRows[$rowKey] = [
                    'key' => $rowKey,
                    'name' => $p->name,
                    'period_number' => $p->period_number,
                    'start_time' => substr((string) $p->start_time, 0, 5),
                    'end_time' => substr((string) $p->end_time, 0, 5),
                    'is_break' => (bool) $p->is_break,
                ];
            }
            $periodsByDayAndRow[$p->day_of_week][$rowKey] = $p;
        }

        $mergedCells = [];
        $orderedRowKeys = array_keys($periodRows);
        $totalRows = count($orderedRowKeys);

        foreach ($daysList as $dNum => $dName) {
            $idx = 0;
            while ($idx < $totalRows) {
                $rKey = $orderedRowKeys[$idx];
                $rInfo = $periodRows[$rKey];
                if ($rInfo['is_break']) {
                    $idx++;
                    continue;
                }

                $startPeriod = $periodsByDayAndRow[$dNum][$rKey] ?? null;
                $startSched = $startPeriod ? ($personalByPeriod[$startPeriod->id] ?? null) : null;

                if (! $startPeriod || ! $startSched) {
                    $mergedCells[$dNum][$rKey] = [
                        'skip' => false,
                        'rowspan' => 1,
                        'schedule' => null,
                        'period' => $startPeriod,
                    ];
                    $idx++;
                    continue;
                }

                $span = 1;
                $endPeriod = $startPeriod;
                $nextIdx = $idx + 1;

                while ($nextIdx < $totalRows) {
                    $nextKey = $orderedRowKeys[$nextIdx];
                    if ($periodRows[$nextKey]['is_break']) {
                        break;
                    }
                    $nextPeriod = $periodsByDayAndRow[$dNum][$nextKey] ?? null;
                    $nextSched = $nextPeriod ? ($personalByPeriod[$nextPeriod->id] ?? null) : null;

                    if (
                        $nextSched &&
                        (int) $nextSched->subject_id === (int) $startSched->subject_id &&
                        (int) $nextSched->teacher_id === (int) $startSched->teacher_id &&
                        (bool) $nextSched->is_combined === (bool) $startSched->is_combined &&
                        (string) ($nextSched->room_id ?? $nextSched->room_name) === (string) ($startSched->room_id ?? $startSched->room_name)
                    ) {
                        $span++;
                        $endPeriod = $nextPeriod;
                        $mergedCells[$dNum][$nextKey] = ['skip' => true];
                        $nextIdx++;
                    } else {
                        break;
                    }
                }

                $mergedCells[$dNum][$rKey] = [
                    'skip' => false,
                    'rowspan' => $span,
                    'schedule' => $startSched,
                    'period' => $startPeriod,
                    'end_period' => $endPeriod,
                    'start_time' => substr((string) $startPeriod->start_time, 0, 5),
                    'end_time' => substr((string) $endPeriod->end_time, 0, 5),
                ];

                $idx += $span;
            }
        }

        // Susun Timeline Hari Ini (atau preview hari efektif terdekat jika akhir pekan / tidak ada jadwal hari ini)
        $todayIsoDay = (int) now()->dayOfWeekIso; // 1 = Senin ... 7 = Minggu
        $isTodaySchoolDay = isset($daysList[$todayIsoDay]) && $categoryPeriods->where('day_of_week', $todayIsoDay)->isNotEmpty();
        $timelineDayNum = $isTodaySchoolDay ? $todayIsoDay : 1;
        $timelineDayName = $daysList[$timelineDayNum] ?? 'Senin';
        $nowTime = now()->format('H:i');

        $dayPeriods = $categoryPeriods->where('day_of_week', $timelineDayNum)->values();
        $timelineBlocks = [];
        $pCount = $dayPeriods->count();
        $i = 0;

        while ($i < $pCount) {
            $p = $dayPeriods[$i];
            $pStart = substr((string) $p->start_time, 0, 5);
            $pEnd = substr((string) $p->end_time, 0, 5);

            if ($p->is_break) {
                $state = 'UPCOMING';
                if ($isTodaySchoolDay) {
                    if ($nowTime > $pEnd) {
                        $state = 'COMPLETED';
                    } elseif ($nowTime >= $pStart && $nowTime <= $pEnd) {
                        $state = 'LIVE_NOW';
                    }
                }
                $timelineBlocks[] = [
                    'is_break' => true,
                    'label' => $p->name,
                    'start_time' => $pStart,
                    'end_time' => $pEnd,
                    'jp_count' => 0,
                    'state' => $state,
                    'schedule' => null,
                ];
                $i++;
                continue;
            }

            $sched = $personalByPeriod[$p->id] ?? null;
            $jpCount = 1;
            $endTime = $pEnd;
            $endPeriodLabel = $p->name;
            $j = $i + 1;

            if ($sched) {
                while ($j < $pCount) {
                    $nextP = $dayPeriods[$j];
                    if ($nextP->is_break) {
                        break;
                    }
                    $nextSched = $personalByPeriod[$nextP->id] ?? null;
                    if (
                        $nextSched &&
                        (int) $nextSched->subject_id === (int) $sched->subject_id &&
                        (int) $nextSched->teacher_id === (int) $sched->teacher_id &&
                        (bool) $nextSched->is_combined === (bool) $sched->is_combined
                    ) {
                        $jpCount++;
                        $endTime = substr((string) $nextP->end_time, 0, 5);
                        $endPeriodLabel = $nextP->name;
                        $j++;
                    } else {
                        break;
                    }
                }
            }

            $state = 'UPCOMING';
            if ($isTodaySchoolDay) {
                if ($nowTime > $endTime) {
                    $state = 'COMPLETED';
                } elseif ($nowTime >= $pStart && $nowTime <= $endTime) {
                    $state = 'LIVE_NOW';
                }
            }

            $timelineBlocks[] = [
                'is_break' => false,
                'label' => $jpCount > 1 ? "{$p->name} – {$endPeriodLabel}" : $p->name,
                'start_time' => $pStart,
                'end_time' => $endTime,
                'jp_count' => $jpCount,
                'state' => $state,
                'schedule' => $sched,
            ];

            $i = $sched ? $j : ($i + 1);
        }

        $liveBlock = collect($timelineBlocks)->firstWhere('state', 'LIVE_NOW');
        $nextBlock = collect($timelineBlocks)->first(fn ($b) => ! $b['is_break'] && $b['schedule'] && $b['state'] === 'UPCOMING');
        $scheduledBlocksToday = collect($timelineBlocks)->filter(fn ($b) => ! $b['is_break'] && $b['schedule']);

        return [
            'activeCategory' => $activeCategory,
            'daysList' => $daysList,
            'periodRows' => $periodRows,
            'mergedCells' => $mergedCells,
            'personalSchedules' => $personalSchedules,
            'combinedCount' => $personalSchedules->where('is_combined', true)->count(),
            'weeklyTotalJp' => count($personalByPeriod),
            'uniqueSubjectsCount' => $personalSchedules->pluck('subject_id')->unique()->count(),
            'isTodaySchoolDay' => $isTodaySchoolDay,
            'timelineDayNum' => $timelineDayNum,
            'timelineDayName' => $timelineDayName,
            'timelineBlocks' => $timelineBlocks,
            'todaySubjectCount' => $scheduledBlocksToday->count(),
            'todayTotalJp' => $scheduledBlocksToday->sum('jp_count'),
            'liveBlock' => $liveBlock,
            'nextBlock' => $nextBlock,
        ];
    }

    /**
     * Hitung ringkasan kehadiran, status presensi hari ini, dan grafik/jejak hari terakhir.
     *
     * @return array<string, mixed>
     */
    private function buildAttendanceSummary(Student $student, School $school, int $limitRecords = 15): array
    {
        $todayStr = Carbon::today()->toDateString();
        $thirtyDaysAgo = Carbon::today()->subDays(29)->toDateString();

        $todayAttendance = Attendance::where('student_id', $student->id)
            ->where('date', $todayStr)
            ->first();

        $monthAttendances = Attendance::where('student_id', $student->id)
            ->where('date', '>=', $thirtyDaysAgo)
            ->orderByDesc('date')
            ->get();

        $allRecent = Attendance::where('student_id', $student->id)
            ->orderByDesc('date')
            ->limit($limitRecords)
            ->get();

        $presentCount = $monthAttendances->where('status', 'present')->count();
        $lateCount = $monthAttendances->where('status', 'late')->count();
        $sickCount = $monthAttendances->where('status', 'sick')->count();
        $excusedCount = $monthAttendances->where('status', 'excused')->count();
        $alphaCount = $monthAttendances->whereIn('status', ['absent', 'alpha'])->count();

        $totalRecorded = $monthAttendances->count();
        $attendedCount = $presentCount + $lateCount;
        $attendanceRate = $totalRecorded > 0
            ? (int) round(($attendedCount / $totalRecorded) * 100)
            : 100;

        // Rata-rata jam datang dari record yang memiliki check_in_at
        $checkInMinutes = $monthAttendances
            ->filter(fn ($a) => ! empty($a->check_in_at))
            ->map(function ($a) {
                $parts = explode(':', (string) $a->check_in_at);

                return ((int) ($parts[0] ?? 7)) * 60 + ((int) ($parts[1] ?? 0));
            });

        $avgCheckIn = null;
        if ($checkInMinutes->isNotEmpty()) {
            $avgMin = (int) round($checkInMinutes->avg());
            $avgCheckIn = sprintf('%02d:%02d', intdiv($avgMin, 60), $avgMin % 60);
        }

        // Susun strip 7 hari sekolah terakhir (Senin-Jumat/Sabtu)
        $byDate = $monthAttendances->keyBy(fn ($a) => Carbon::parse($a->date)->toDateString());
        $recentDaysStrip = [];
        $cursor = Carbon::today();
        while (count($recentDaysStrip) < 7) {
            if (! $cursor->isSunday()) {
                $dStr = $cursor->toDateString();
                $att = $byDate->get($dStr);
                $recentDaysStrip[] = [
                    'date' => $dStr,
                    'day_short' => $cursor->translatedFormat('D'),
                    'day_num' => $cursor->format('d/m'),
                    'status' => $att?->status ?? ($dStr === $todayStr ? 'pending_today' : 'unrecorded'),
                    'check_in_at' => $att?->check_in_at ? substr((string) $att->check_in_at, 0, 5) : null,
                ];
            }
            $cursor->subDay();
        }
        $recentDaysStrip = array_reverse($recentDaysStrip);

        return [
            'todayAttendance' => $todayAttendance,
            'presentCount' => $presentCount,
            'lateCount' => $lateCount,
            'sickCount' => $sickCount,
            'excusedCount' => $excusedCount,
            'alphaCount' => $alphaCount,
            'totalRecorded' => $totalRecorded,
            'attendanceRate' => $attendanceRate,
            'avgCheckIn' => $avgCheckIn,
            'recentDaysStrip' => $recentDaysStrip,
            'recentRecords' => $allRecent,
        ];
    }

    /**
     * Hitung saldo poin disiplin, ambang batas sanksi, dan kelayakan pemutihan poin.
     *
     * @return array<string, mixed>
     */
    private function buildDisciplineSummary(
        Student $student,
        School $school,
        ?AcademicYear $activeYear,
        string $activeSemester,
        bool $includeRulebook = false
    ): array {
        $policy = DisciplineBootstrapService::ensureDefaultsForSchool($school->id);

        $thresholds = DisciplineThreshold::where('school_id', $school->id)
            ->where('is_active', true)
            ->orderBy('min_points')
            ->get();

        $amnestyRules = DisciplineAmnestyRule::where('school_id', $school->id)
            ->where('is_active', true)
            ->orderBy('code')
            ->get();

        $recordsQuery = StudentDisciplineRecord::with(['disciplineItem.category', 'amnestyRule', 'recorder'])
            ->where('school_id', $school->id)
            ->where('student_id', $student->id)
            ->where('status', 'APPROVED');

        if ($policy->reset_period === 'SEMESTER') {
            if ($activeYear) {
                $recordsQuery->where('academic_year_id', $activeYear->id);
            }
            $recordsQuery->where('academic_semester', $activeSemester);
        } elseif ($policy->reset_period === 'ACADEMIC_YEAR' && $activeYear) {
            $recordsQuery->where('academic_year_id', $activeYear->id);
        }

        $records = $recordsQuery->orderByDesc('record_date')->orderByDesc('id')->get();

        $violationPoints = (int) $records->where('record_type', 'VIOLATION')->sum('points');
        $amnestyPoints = (int) $records->where('record_type', 'AMNESTY')->sum('points');
        $netViolationPoints = max(0, $violationPoints - $amnestyPoints);
        $maxCap = max(1, (int) $policy->max_violation_points);
        $positiveCreditScore = max(0, $maxCap - $netViolationPoints);

        $currentThreshold = $thresholds
            ->filter(fn ($th) => $netViolationPoints >= $th->min_points && ($th->max_points === null || $netViolationPoints <= $th->max_points))
            ->sortByDesc('min_points')
            ->first();

        $nextThreshold = $thresholds
            ->first(fn ($th) => $th->min_points > $netViolationPoints);

        $lastViolation = $records->firstWhere('record_type', 'VIOLATION');
        $cleanDays = $lastViolation && $lastViolation->record_date
            ? (int) Carbon::parse($lastViolation->record_date)->diffInDays(Carbon::today())
            : 30;

        $categories = $includeRulebook
            ? DisciplineCategory::with(['items' => fn ($q) => $q->where('is_active', true)->orderBy('points')])
                ->where('school_id', $school->id)
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get()
            : collect();

        return [
            'policy' => $policy,
            'thresholds' => $thresholds,
            'amnestyRules' => $amnestyRules,
            'records' => $records,
            'violationPoints' => $violationPoints,
            'amnestyPoints' => $amnestyPoints,
            'netViolationPoints' => $netViolationPoints,
            'positiveCreditScore' => $positiveCreditScore,
            'maxCap' => $maxCap,
            'currentThreshold' => $currentThreshold,
            'nextThreshold' => $nextThreshold,
            'cleanDays' => $cleanDays,
            'categories' => $categories,
        ];
    }

    /**
     * Evaluasi persentase kelengkapan biodata Dapodik & status jendela edit mandiri.
     *
     * @return array<string, mixed>
     */
    private function buildProfileCompleteness(Student $student, School $school): array
    {
        $checks = [
            'NISN' => ! empty($student->nisn),
            'NIK (16 Digit)' => ! empty($student->nik),
            'Nomor KK' => ! empty($student->no_kk),
            'Tempat & Tgl Lahir' => ! empty($student->birth_place) && ! empty($student->birth_date),
            'Agama Biodata' => ! empty($student->religion),
            'Alamat Domisili' => ! empty($student->address),
            'Titik GPS Rumah' => ! empty($student->home_latitude) && ! empty($student->home_longitude),
            'Pasfoto Resmi 4x6' => ! empty($student->photo_url),
            'No. HP Siswa' => ! empty($student->student_phone),
            'Nama & HP Orang Tua' => ! empty($student->parent_name) && ! empty($student->parent_phone),
        ];

        $filledCount = collect($checks)->filter()->count();
        $totalCount = count($checks);
        $percentage = (int) round(($filledCount / $totalCount) * 100);
        $missingLabels = array_keys(array_filter($checks, fn ($ok) => ! $ok));

        return [
            'percentage' => $percentage,
            'filledCount' => $filledCount,
            'totalCount' => $totalCount,
            'missingLabels' => $missingLabels,
            'checks' => $checks,
            'isEditWindowOpen' => $school->isStudentProfileEditOpen(),
            'editStart' => $school->student_profile_edit_start,
            'editEnd' => $school->student_profile_edit_end,
        ];
    }
}

