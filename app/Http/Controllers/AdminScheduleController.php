<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\ClassSchedule;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\SchoolRoom;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\TeachingGroup;
use App\Models\TimeScheduleCategory;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminScheduleController extends Controller
{
    /**
     * Display the dedicated Lesson Schedule Management & Multi-View page
     * (By Class, By Teacher, By Room, Dynamic Categories with Validity Dates, and Copy from Semester).
     */
    public function index(Request $request): View
    {
        $school = $this->resolveSchool($request);
        $schoolId = $school->id;

        $availableSchools = $request->user()?->isSuperAdmin()
            ? School::withCount(['classes', 'students', 'users'])->orderBy('id')->get()
            : School::where('id', $schoolId)->withCount(['classes', 'students', 'users'])->get();

        // 1. Academic Years & Semesters
        $academicYears = AcademicYear::with('semesters')
            ->where('school_id', $schoolId)
            ->orderByDesc('is_active')
            ->orderByDesc('name')
            ->get();

        $activeYear = $academicYears->firstWhere('is_active', true) ?? $academicYears->first();
        $selectedYearId = (int) ($request->query('academic_year_id') ?: ($activeYear?->id ?? 0));
        $selectedYear = $academicYears->firstWhere('id', $selectedYearId) ?? $activeYear;

        $activeSemObj = $selectedYear?->semesters?->firstWhere('is_active', true);
        $selectedSemester = $request->query('academic_semester') ?: ($activeSemObj?->semester ?? 'odd');
        if (! in_array($selectedSemester, ['odd', 'even'], true)) {
            $selectedSemester = 'odd';
        }

        // 2. Dynamic Time Schedule Categories (with effective_start_date & effective_end_date)
        $timeCategories = TimeScheduleCategory::with([
            'academicYear',
            'periods' => function ($q) {
                $q->orderBy('day_of_week')->orderBy('start_time');
            },
        ])
            ->withCount(['periods', 'schedules'])
            ->where('school_id', $schoolId)
            ->orderByDesc('is_default')
            ->orderByDesc('effective_start_date')
            ->get();

        $activeCategory = $timeCategories->firstWhere('is_default', true) ?? $timeCategories->first();
        $selectedCategoryId = (int) ($request->query('category_id') ?: ($activeCategory?->id ?? 0));
        $selectedCategory = $timeCategories->firstWhere('id', $selectedCategoryId) ?? $activeCategory;

        // 3. Classes (Rombel)
        $classes = SchoolClass::where('school_id', $schoolId)
            ->with([
                'department',
                'homeroomTeacher',
                'defaultRoom',
                'curriculum.curriculumSubjects.subject',
            ])
            ->withCount('students')
            ->orderBy('grade_level')
            ->orderBy('name')
            ->get();

        // Auto-bootstrap initial School Rooms if none exist yet for this school
        if ($school && SchoolRoom::where('school_id', $schoolId)->count() === 0) {
            $this->bootstrapDefaultRooms($school, $classes);
            $classes->load('defaultRoom');
        }

        // 4. Master Rooms (Ruang Kelas, Lab, Bengkel)
        $rooms = SchoolRoom::where('school_id', $schoolId)
            ->with('classes')
            ->withCount('schedules')
            ->orderBy('type')
            ->orderBy('code')
            ->get();

        // 5. Teachers & Subjects
        $teachers = Teacher::where('school_id', $schoolId)
            ->where('is_active', true)
            ->orderBy('full_name')
            ->get();

        $subjects = Subject::where('school_id', $schoolId)
            ->with(['curriculumSubjects.curriculum'])
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        // 6. Selected Class, Teacher, and Room filters for the 3 views
        $selectedClassId = (int) ($request->query('class_id') ?: ($classes->first()?->id ?? 0));
        $selectedClass = $classes->firstWhere('id', $selectedClassId) ?? $classes->first();

        $selectedTeacherId = (int) ($request->query('teacher_id') ?: ($teachers->first()?->id ?? 0));
        $selectedTeacher = $teachers->firstWhere('id', $selectedTeacherId) ?? $teachers->first();

        $selectedRoomId = (int) ($request->query('room_id') ?: ($rooms->first()?->id ?? 0));
        $selectedRoom = $rooms->firstWhere('id', $selectedRoomId) ?? $rooms->first();

        // 6b. Kelompok Mapel & Kelas Gabungan + Biodata Siswa (untuk Filter Agama & Mapel Pilihan)
        $teachingGroups = TeachingGroup::with(['classes', 'subject', 'teacher', 'defaultRoom', 'manualStudents'])
            ->where('school_id', $schoolId)
            ->orderBy('name')
            ->get();

        $selectedGroupId = (int) $request->query('teaching_group_id', 0);
        $selectedGroup = $selectedGroupId > 0 ? $teachingGroups->firstWhere('id', $selectedGroupId) : null;

        $students = Student::with('schoolClass')
            ->where('school_id', $schoolId)
            ->orderBy('class_id')
            ->orderBy('full_name')
            ->get();

        // 7. Schedules for the active/selected Year + Semester + Category
        $schedulesQuery = ClassSchedule::with([
            'schoolClass',
            'combinedClasses',
            'teachingGroup.classes',
            'teachingGroup.manualStudents',
            'subject',
            'teacher',
            'lessonPeriod',
            'room',
            'category',
        ])->where('school_id', $schoolId);

        if ($selectedYear) {
            $schedulesQuery->where('academic_year_id', $selectedYear->id);
        }

        if ($selectedSemester) {
            $schedulesQuery->where('academic_semester', $selectedSemester);
        }

        if ($selectedCategory) {
            $schedulesQuery->where(function ($q) use ($selectedCategory) {
                $q->where('category_id', $selectedCategory->id)
                    ->orWhereHas('lessonPeriod', fn ($lp) => $lp->where('category_id', $selectedCategory->id));
            });
        }

        $schedules = $schedulesQuery->get();

        // 8. Summary of available schedules across all semesters/categories (for Copy Schedule modal)
        $semesterSummaries = ClassSchedule::where('school_id', $schoolId)
            ->selectRaw('academic_year_id, academic_semester, COUNT(*) as total_slots')
            ->groupBy('academic_year_id', 'academic_semester')
            ->get()
            ->keyBy(fn ($item) => $item->academic_year_id.'_'.$item->academic_semester);

        return view('admin.schedules', [
            'school' => $school,
            'availableSchools' => $availableSchools,
            'academicYears' => $academicYears,
            'activeYear' => $activeYear,
            'selectedYear' => $selectedYear,
            'selectedSemester' => $selectedSemester,
            'timeCategories' => $timeCategories,
            'activeCategory' => $activeCategory,
            'selectedCategory' => $selectedCategory,
            'classes' => $classes,
            'selectedClass' => $selectedClass,
            'teachers' => $teachers,
            'selectedTeacher' => $selectedTeacher,
            'rooms' => $rooms,
            'selectedRoom' => $selectedRoom,
            'teachingGroups' => $teachingGroups,
            'selectedGroup' => $selectedGroup,
            'students' => $students,
            'subjects' => $subjects,
            'schedules' => $schedules,
            'semesterSummaries' => $semesterSummaries,
        ]);
    }

    /**
     * Create default classroom & lab rooms if a school does not have any rooms yet.
     */
    private function bootstrapDefaultRooms(School $school, $classes): void
    {
        foreach ($classes as $idx => $cls) {
            $code = 'R-'.strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $cls->name));
            $room = SchoolRoom::firstOrCreate(
                ['school_id' => $school->id, 'code' => substr($code, 0, 28)],
                [
                    'name' => 'Ruang Kelas '.$cls->name,
                    'type' => 'CLASSROOM',
                    'building' => 'Gedung Utama',
                    'floor' => 'Lantai '.(($idx % 3) + 1),
                    'capacity' => 36,
                    'description' => 'Ruang kelas teori reguler untuk rombel '.$cls->name,
                    'is_active' => true,
                ]
            );

            if (! $cls->default_room_id) {
                $cls->update(['default_room_id' => $room->id]);
            }
        }

        $specialRooms = [
            ['code' => 'LAB-KOM1', 'name' => 'Laboratorium Komputer 1', 'type' => 'LABORATORY', 'building' => 'Gedung Sains & TIK', 'floor' => 'Lantai 2', 'capacity' => 40],
            ['code' => 'LAB-IPA', 'name' => 'Laboratorium Terpadu IPA', 'type' => 'LABORATORY', 'building' => 'Gedung Sains & TIK', 'floor' => 'Lantai 1', 'capacity' => 36],
            ['code' => 'GOR-01', 'name' => 'Lapangan & GOR Olahraga', 'type' => 'SPORTS', 'building' => 'Area Olahraga', 'floor' => 'Lantai 1', 'capacity' => 80],
        ];

        if ($school->isVocational()) {
            $specialRooms[] = ['code' => 'BENGKEL-01', 'name' => 'Bengkel Praktik Kejuruan Utama', 'type' => 'WORKSHOP', 'building' => 'Gedung Workshop SMK', 'floor' => 'Lantai 1', 'capacity' => 36];
        }

        foreach ($specialRooms as $sr) {
            SchoolRoom::firstOrCreate(
                ['school_id' => $school->id, 'code' => $sr['code']],
                [
                    'name' => $sr['name'],
                    'type' => $sr['type'],
                    'building' => $sr['building'],
                    'floor' => $sr['floor'],
                    'capacity' => $sr['capacity'],
                    'is_active' => true,
                ]
            );
        }
    }
}

