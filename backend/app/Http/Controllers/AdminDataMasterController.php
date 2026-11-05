<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\ClassSchedule;
use App\Models\Department;
use App\Models\ParentGuardian;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\TimeScheduleCategory;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminDataMasterController extends Controller
{
    /**
     * Display the Admin Data Master UI.
     */
    public function index(Request $request): View
    {
        $school = $this->resolveSchool($request);
        $schoolId = $school->id;

        $availableSchools = $request->user()?->isSuperAdmin()
            ? School::withCount(['classes', 'students', 'users'])->orderBy('id')->get()
            : School::where('id', $schoolId)->withCount(['classes', 'students', 'users'])->get();

        $academicYears = AcademicYear::with('semesters')
            ->where('school_id', $schoolId)
            ->orderByDesc('is_active')
            ->orderByDesc('name')
            ->get();

        $activeYear = $academicYears->firstWhere('is_active', true);

        $timeCategories = TimeScheduleCategory::with(['periods' => function ($q) {
            $q->orderBy('day_of_week')->orderBy('start_time');
        }])
            ->where('school_id', $schoolId)
            ->orderByDesc('is_default')
            ->get();

        $activeCategory = $timeCategories->firstWhere('is_default', true) ?? $timeCategories->first();

        $departments = Department::where('school_id', $schoolId)
            ->withCount('classes')
            ->orderBy('name')
            ->get();

        $classes = SchoolClass::where('school_id', $schoolId)
            ->with(['department', 'homeroomTeacher', 'curriculum', 'vocationalProgram', 'vocationalConcentration'])
            ->withCount('students')
            ->orderBy('grade_level')
            ->orderBy('name')
            ->get();

        $teachers = Teacher::where('school_id', $schoolId)
            ->with('user')
            ->orderBy('full_name')
            ->get();

        $students = Student::where('school_id', $schoolId)
            ->with(['schoolClass', 'parents', 'entryAcademicYear'])
            ->orderBy('full_name')
            ->get();

        // Rekap angkatan (tahun ajaran masuk) untuk filter & ringkasan
        $cohorts = $academicYears->sortByDesc('start_date')->map(fn ($y) => [
            'id' => $y->id,
            'name' => $y->name,
            'total' => $students->where('entry_academic_year_id', $y->id)->count(),
            'active' => $students->where('entry_academic_year_id', $y->id)->where('status', 'active')->count(),
            'graduated' => $students->where('entry_academic_year_id', $y->id)->where('status', 'graduated')->count(),
        ])->values();

        $parents = ParentGuardian::where('school_id', $schoolId)
            ->with(['students', 'user'])
            ->orderBy('id')
            ->get();

        $subjects = Subject::where('school_id', $schoolId)
            ->with(['curriculumSubjects.curriculum', 'curriculums'])
            ->withCount('schedules')
            ->orderBy('name')
            ->get();

        $schedules = ClassSchedule::where('school_id', $schoolId)
            ->with(['schoolClass', 'subject', 'teacher', 'lessonPeriod'])
            ->get();

        $curriculums = \App\Models\Curriculum::where('school_id', $schoolId)
            ->with(['gradeLevels', 'subjectGroups', 'curriculumSubjects.subject', 'curriculumSubjects.group', 'classes'])
            ->withCount(['classes', 'subjects'])
            ->get();

        $gradeLevelMappings = \App\Models\CurriculumGradeLevel::where('school_id', $schoolId)
            ->with('curriculum')
            ->orderBy('grade_level')
            ->get();

        // Spektrum Keahlian SMK (Bidang, Program, Konsentrasi Keahlian)
        $vocationalFields = \App\Models\VocationalField::where('school_id', $schoolId)
            ->with(['programs.concentrations' => function ($q) {
                $q->withCount('classes');
            }])
            ->orderBy('name')
            ->get();

        $vocationalPrograms = \App\Models\VocationalProgram::where('school_id', $schoolId)
            ->with(['field', 'concentrations'])
            ->withCount(['concentrations', 'classes'])
            ->orderBy('name')
            ->get();

        $vocationalConcentrations = \App\Models\VocationalConcentration::where('school_id', $schoolId)
            ->with('program.field')
            ->withCount('classes')
            ->orderBy('name')
            ->get();

        $isVocational = $school->isVocational() || $request->query('mode') === 'smk';

        $stats = [
            'total_students' => $students->count(),
            'total_teachers' => $teachers->count(),
            'total_classes' => $classes->count(),
            'total_parents' => $parents->count(),
            'total_subjects' => $subjects->count(),
            'total_curriculums' => $curriculums->count(),
            'total_vocational_fields' => $vocationalFields->count(),
            'total_vocational_programs' => $vocationalPrograms->count(),
            'total_vocational_concentrations' => $vocationalConcentrations->count(),
            'active_year' => $activeYear?->name ?? 'Belum Diatur',
            'active_schedule_cat' => $activeCategory?->name ?? 'Belum Diatur',
            'is_vocational' => $isVocational,
        ];

        return view('admin.data-master', [
            'school' => $school,
            'availableSchools' => $availableSchools,
            'isVocational' => $isVocational,
            'academicYears' => $academicYears,
            'activeYear' => $activeYear,
            'timeCategories' => $timeCategories,
            'activeCategory' => $activeCategory,
            'departments' => $departments,
            'classes' => $classes,
            'teachers' => $teachers,
            'students' => $students,
            'parents' => $parents,
            'subjects' => $subjects,
            'schedules' => $schedules,
            'curriculums' => $curriculums,
            'gradeLevelMappings' => $gradeLevelMappings,
            'vocationalFields' => $vocationalFields,
            'vocationalPrograms' => $vocationalPrograms,
            'vocationalConcentrations' => $vocationalConcentrations,
            'cohorts' => $cohorts,
            'stats' => $stats,
        ]);
    }

    /**
     * Switch the active school session (Super Admin only).
     */
    public function switchSchool(Request $request)
    {
        if (! $request->user()?->isSuperAdmin()) {
            return redirect()->back()->with('error', 'Akses Ditolak: Anda hanya berwenang mengelola data pada satuan pendidikan tempat Anda bertugas.');
        }

        $schoolId = $request->input('school_id');
        $school = School::findOrFail($schoolId);
        session(['active_school_id' => $school->id]);

        return redirect()->route('admin.data-master')->with('success', 'Berhasil beralih ke satuan pendidikan: ' . $school->name);
    }

    /**
     * Switch the active school session via GET (Super Admin only).
     */
    public function switchSchoolGet($id, Request $request)
    {
        if (! $request->user()?->isSuperAdmin()) {
            return redirect()->back()->with('error', 'Akses Ditolak: Anda hanya berwenang mengelola data pada satuan pendidikan tempat Anda bertugas.');
        }

        $school = School::findOrFail($id);
        session(['active_school_id' => $school->id]);

        return redirect()->route('admin.data-master')->with('success', 'Berhasil beralih ke satuan pendidikan: ' . $school->name);
    }
}
