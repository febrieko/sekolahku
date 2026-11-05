<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\ClassSchedule;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\TeachingGroup;
use App\Models\TeachingJournal;
use App\Models\TeachingJournalAttendance;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminTeachingJournalController extends Controller
{
    /**
     * Tampilan utama Administrasi KBM & Monitoring Jurnal Mengajar.
     */
    public function index(Request $request): View
    {
        $school = $this->resolveSchool($request);
        $schoolId = $school->id;
        $user = $request->user();

        $academicYears = AcademicYear::with('semesters')
            ->where('school_id', $schoolId)
            ->orderByDesc('is_active')
            ->orderByDesc('name')
            ->get();

        $activeYear = $academicYears->firstWhere('is_active', true) ?? $academicYears->first();
        $activeSemester = $request->query('semester') ?: ($activeYear?->semesters?->firstWhere('is_active', true)?->semester ?? 'odd');

        $today = Carbon::today()->toDateString();
        $dayOfWeek = Carbon::now()->dayOfWeekIso; // 1 = Senin, 7 = Minggu

        // Ambil profil guru jika user saat ini adalah guru
        $currentTeacher = null;
        if ($user) {
            $currentTeacher = Teacher::where('school_id', $schoolId)->where('user_id', $user->id)->first();
            if (! $currentTeacher && $user->isTeacher()) {
                $currentTeacher = Teacher::where('school_id', $schoolId)->where('email', $user->email)->first();
            }
        }

        // 1. Jadwal KBM Hari Ini
        $todaySchedulesQuery = ClassSchedule::with(['schoolClass', 'subject', 'teacher', 'room', 'teachingGroup', 'lessonPeriod'])
            ->where('school_id', $schoolId)
            ->where(function ($query) use ($dayOfWeek) {
                $query->whereHas('lessonPeriod', function ($q) use ($dayOfWeek) {
                    $q->where('day_of_week', $dayOfWeek);
                })->orWhereDoesntHave('lessonPeriod');
            });

        if ($currentTeacher && ! $user->isSuperAdmin() && ! $user->isWakaKurikulum()) {
            $todaySchedulesQuery->where('teacher_id', $currentTeacher->id);
        }

        $todaySchedules = $todaySchedulesQuery->get();

        // Ambil jurnal KBM yang sudah dibuat hari ini
        $todayJournals = TeachingJournal::with(['schoolClass', 'subject', 'teacher', 'attendances'])
            ->where('school_id', $schoolId)
            ->where('date', $today)
            ->get()
            ->keyBy(function ($j) {
                return $j->class_schedule_id ?: ($j->school_class_id . '_' . $j->subject_id);
            });

        // 2. Radar KBM Sekolah Hari Ini (KPI Ringkasan)
        $kpi = [
            'total_scheduled' => $todaySchedules->count(),
            'completed' => $todayJournals->where('status', 'completed')->count(),
            'in_progress' => $todayJournals->where('status', 'in_progress')->count(),
            'not_started' => max(0, $todaySchedules->count() - $todayJournals->count()),
        ];

        // 3. Riwayat Jurnal KBM Terbaru
        $journalsQuery = TeachingJournal::with(['schoolClass', 'subject', 'teacher', 'verifiedByWaka'])
            ->where('school_id', $schoolId)
            ->where('academic_year_id', $activeYear?->id ?? 0)
            ->where('academic_semester', $activeSemester);

        if ($currentTeacher && ! $user->isSuperAdmin() && ! $user->isWakaKurikulum()) {
            $journalsQuery->where('teacher_id', $currentTeacher->id);
        }

        if ($request->query('class_id')) {
            $journalsQuery->where('school_class_id', $request->query('class_id'));
        }

        if ($request->query('subject_id')) {
            $journalsQuery->where('subject_id', $request->query('subject_id'));
        }

        if ($request->query('date')) {
            $journalsQuery->where('date', $request->query('date'));
        }

        $recentJournals = $journalsQuery->orderByDesc('date')->orderByDesc('id')->paginate(20)->withQueryString();

        $classes = SchoolClass::where('school_id', $schoolId)->orderBy('grade_level')->orderBy('name')->get();
        $subjects = Subject::where('school_id', $schoolId)->orderBy('name')->get();
        $teachers = Teacher::where('school_id', $schoolId)->orderBy('full_name')->get();

        return view('admin.kurikulum.teaching_journals', compact(
            'school',
            'academicYears',
            'activeYear',
            'activeSemester',
            'currentTeacher',
            'todaySchedules',
            'todayJournals',
            'kpi',
            'recentJournals',
            'classes',
            'subjects',
            'teachers'
        ));
    }

    /**
     * Membuka / Memulai sesi KBM baru berdasarkan jadwal pelajaran atau kelas.
     * Mengotomatiskan sinkronisasi baseline presensi pagi.
     */
    public function openSession(Request $request): RedirectResponse
    {
        $school = $this->resolveSchool($request);
        $schoolId = $school->id;
        $user = $request->user();

        $scheduleId = $request->input('class_schedule_id');
        $schedule = null;

        if ($scheduleId) {
            $schedule = ClassSchedule::where('school_id', $schoolId)->findOrFail($scheduleId);
            $classId = $schedule->class_id;
            $subjectId = $schedule->subject_id;
            $teacherId = $schedule->teacher_id;
            $teachingGroupId = $schedule->teaching_group_id;
            $roomId = $schedule->room_id;
            $period = $schedule->lessonPeriod;
            $startPeriod = $period?->period_number ?? 1;
            $endPeriod = $period?->period_number ?? 2;
            $academicYearId = $schedule->academic_year_id ?: 1;
            $semester = $schedule->academic_semester ?: 'odd';
        } else {
            $request->validate([
                'school_class_id' => 'required|exists:classes,id',
                'subject_id' => 'required|exists:subjects,id',
                'teacher_id' => 'required|exists:teachers,id',
                'date' => 'required|date',
                'start_period' => 'required|integer|min:1',
                'end_period' => 'required|integer|min:1',
            ]);

            $classId = $request->input('school_class_id');
            $subjectId = $request->input('subject_id');
            $teacherId = $request->input('teacher_id');
            $teachingGroupId = $request->input('teaching_group_id');
            $roomId = $request->input('school_room_id');
            $startPeriod = (int) $request->input('start_period');
            $endPeriod = (int) $request->input('end_period');

            $activeYear = AcademicYear::where('school_id', $schoolId)->where('is_active', true)->first();
            $academicYearId = $activeYear?->id ?? 1;
            $semester = $activeYear?->semesters?->firstWhere('is_active', true)?->semester ?? 'odd';
        }

        $date = $request->input('date') ?: Carbon::today()->toDateString();

        // 1. Cek apakah sesi KBM untuk jadwal dan tanggal ini sudah ada
        $journalQuery = TeachingJournal::where('school_id', $schoolId)
            ->where('date', $date)
            ->where('subject_id', $subjectId);

        if ($scheduleId) {
            $journalQuery->where('class_schedule_id', $scheduleId);
        } else {
            $journalQuery->where('school_class_id', $classId)->where('start_period', $startPeriod);
        }

        $journal = $journalQuery->first();

        // 2. Jika belum ada, buat jurnal KBM baru
        if (! $journal) {
            // Hitung nomor pertemuan otomatis
            $lastMeeting = TeachingJournal::where('school_id', $schoolId)
                ->where('subject_id', $subjectId)
                ->where(function ($q) use ($classId, $teachingGroupId) {
                    if ($teachingGroupId) {
                        $q->where('teaching_group_id', $teachingGroupId);
                    } else {
                        $q->where('school_class_id', $classId);
                    }
                })
                ->where('academic_year_id', $academicYearId)
                ->where('academic_semester', $semester)
                ->max('meeting_number');

            $meetingNumber = ($lastMeeting ?? 0) + 1;

            $journal = TeachingJournal::create([
                'school_id' => $schoolId,
                'academic_year_id' => $academicYearId,
                'academic_semester' => $semester,
                'class_schedule_id' => $scheduleId,
                'teacher_id' => $teacherId,
                'school_class_id' => $classId,
                'subject_id' => $subjectId,
                'teaching_group_id' => $teachingGroupId,
                'school_room_id' => $roomId,
                'date' => $date,
                'time_slot_label' => "Jam Ke {$startPeriod} - {$endPeriod}",
                'start_period' => $startPeriod,
                'end_period' => $endPeriod,
                'meeting_number' => $meetingNumber,
                'status' => 'in_progress',
            ]);

            // 3. Ambil daftar siswa aktif
            if ($teachingGroupId) {
                $teachingGroup = TeachingGroup::with('students')->find($teachingGroupId);
                $students = $teachingGroup ? $teachingGroup->students : collect();
            } else {
                $students = Student::where(function ($q) use ($classId) {
                    $q->where('class_id', $classId);
                })
                    ->where('status', 'active')
                    ->orderBy('full_name')
                    ->get();
            }

            // 4. Pre-fill baseline presensi dari data presensi harian / gerbang GPS hari ini
            $todayAttendances = Attendance::where('school_id', $schoolId)
                ->where('date', $date)
                ->whereIn('student_id', $students->pluck('id'))
                ->get()
                ->keyBy('student_id');

            foreach ($students as $student) {
                $dailyAtt = $todayAttendances->get($student->id);
                $initialStatus = 'present';

                if ($dailyAtt) {
                    if (in_array($dailyAtt->status, ['sick', 'excused', 'absent'], true)) {
                        $initialStatus = $dailyAtt->status;
                    }
                }

                TeachingJournalAttendance::firstOrCreate(
                    [
                        'teaching_journal_id' => $journal->id,
                        'student_id' => $student->id,
                    ],
                    [
                        'status' => $initialStatus,
                        'notes' => $dailyAtt?->notes,
                    ]
                );
            }

            $journal->recalculateAttendanceSummary();
        }

        return redirect()->route('admin.kurikulum.jurnal.show', $journal->id)
            ->with('success', "Sesi KBM Pertemuan ke-{$journal->meeting_number} berhasil dibuka. Silakan lengkapi materi & presensi.");
    }

    /**
     * Halaman form pengisian & evaluasi sesi KBM.
     */
    public function show(Request $request, int $id): View
    {
        $school = $this->resolveSchool($request);
        $journal = TeachingJournal::with([
            'schoolClass',
            'subject',
            'teacher',
            'schoolRoom',
            'teachingGroup',
            'attendances.student',
            'verifiedByWaka',
        ])
            ->where('school_id', $school->id)
            ->findOrFail($id);

        $teachers = Teacher::where('school_id', $school->id)->orderBy('full_name')->get();

        return view('admin.kurikulum.teaching_journal_detail', compact('school', 'journal', 'teachers'));
    }

    /**
     * Simpan pembaruan materi dan presensi KBM.
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $school = $this->resolveSchool($request);
        $journal = TeachingJournal::where('school_id', $school->id)->findOrFail($id);

        $request->validate([
            'meeting_number' => 'required|integer|min:1',
            'learning_objective' => 'required|string',
            'teaching_activity' => 'nullable|string',
            'obstacles_and_followup' => 'nullable|string',
            'homework_assignment' => 'nullable|string',
            'substitute_teacher_id' => 'nullable|exists:teachers,id',
            'attendances' => 'nullable|array',
            'attendances.*.status' => 'required|in:present,sick,excused,absent,dispensation',
            'attendances.*.notes' => 'nullable|string|max:255',
        ]);

        $journal->update([
            'meeting_number' => $request->input('meeting_number'),
            'learning_objective' => $request->input('learning_objective'),
            'teaching_activity' => $request->input('teaching_activity'),
            'obstacles_and_followup' => $request->input('obstacles_and_followup'),
            'homework_assignment' => $request->input('homework_assignment'),
            'substitute_teacher_id' => $request->input('substitute_teacher_id'),
        ]);

        // Simpan checklist presensi
        if ($request->has('attendances')) {
            foreach ($request->input('attendances') as $studentId => $attData) {
                TeachingJournalAttendance::where('teaching_journal_id', $journal->id)
                    ->where('student_id', $studentId)
                    ->update([
                        'status' => $attData['status'],
                        'notes' => $attData['notes'] ?? null,
                    ]);
            }
        }

        // Jika tombol "Selesai & Kunci KBM" ditekan
        if ($request->input('action') === 'complete') {
            $journal->update([
                'status' => 'completed',
                'completed_at' => Carbon::now(),
            ]);
        }

        $summary = $journal->recalculateAttendanceSummary();

        $msg = $journal->status === 'completed'
            ? 'Sesi KBM Pertemuan ke-' . $journal->meeting_number . ' telah selesai dan jurnal berhasil dikunci!'
            : 'Perubahan materi dan presensi KBM berhasil disimpan.';

        return redirect()->route('admin.kurikulum.jurnal.show', $journal->id)->with('success', $msg);
    }

    /**
     * Halaman Cetak & Laporan Resmi Jurnal Mengajar Guru (Buku Kerja Guru 3 / Supervisi).
     */
    public function report(Request $request): View
    {
        $school = $this->resolveSchool($request);
        $schoolId = $school->id;

        $academicYears = AcademicYear::with('semesters')->where('school_id', $schoolId)->get();
        $activeYear = AcademicYear::where('school_id', $schoolId)->where('is_active', true)->first() ?? $academicYears->first();
        $selectedYearId = (int) ($request->query('academic_year_id') ?: ($activeYear?->id ?? 0));
        $selectedYear = $academicYears->firstWhere('id', $selectedYearId) ?? $activeYear;

        $semester = $request->query('semester') ?: ($selectedYear?->semesters?->firstWhere('is_active', true)?->semester ?? 'odd');

        $teacherId = $request->query('teacher_id');
        $subjectId = $request->query('subject_id');
        $classId = $request->query('class_id');

        $query = TeachingJournal::with(['schoolClass', 'subject', 'teacher', 'schoolRoom', 'attendances'])
            ->where('school_id', $schoolId)
            ->where('academic_year_id', $selectedYearId)
            ->where('academic_semester', $semester)
            ->where('status', 'completed');

        if ($teacherId) {
            $query->where('teacher_id', $teacherId);
        }
        if ($subjectId) {
            $query->where('subject_id', $subjectId);
        }
        if ($classId) {
            $query->where('school_class_id', $classId);
        }

        $journals = $query->orderBy('meeting_number')->orderBy('date')->get();

        $selectedTeacher = $teacherId ? Teacher::where('school_id', $schoolId)->find($teacherId) : null;
        $selectedSubject = $subjectId ? Subject::where('school_id', $schoolId)->find($subjectId) : null;
        $selectedClass = $classId ? SchoolClass::where('school_id', $schoolId)->find($classId) : null;

        $teachers = Teacher::where('school_id', $schoolId)->orderBy('full_name')->get();
        $subjects = Subject::where('school_id', $schoolId)->orderBy('name')->get();
        $classes = SchoolClass::where('school_id', $schoolId)->orderBy('grade_level')->orderBy('name')->get();

        return view('admin.kurikulum.teaching_journal_report', compact(
            'school',
            'academicYears',
            'selectedYear',
            'semester',
            'journals',
            'selectedTeacher',
            'selectedSubject',
            'selectedClass',
            'teachers',
            'subjects',
            'classes'
        ));
    }

    /**
     * Supervisi & Verifikasi Jurnal KBM oleh Waka Kurikulum / Kepala Sekolah.
     */
    public function verify(Request $request, int $id): JsonResponse
    {
        $school = $this->resolveSchool($request);
        $journal = TeachingJournal::where('school_id', $school->id)->findOrFail($id);

        $journal->update([
            'verified_by_waka_at' => Carbon::now(),
            'verified_by_waka_id' => $request->user()->id,
            'waka_supervision_notes' => $request->input('notes'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Jurnal KBM berhasil diverifikasi supervisi akademik.',
            'data' => [
                'verified_at' => $journal->verified_by_waka_at->isoFormat('D MMMM Y, HH:mm') . ' WIB',
                'verifier_name' => $request->user()->name,
            ],
        ]);
    }
}
