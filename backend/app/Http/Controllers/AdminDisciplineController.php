<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\DisciplineAmnestyRule;
use App\Models\DisciplineCategory;
use App\Models\DisciplineThreshold;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\StudentDisciplineRecord;
use App\Services\DisciplineBootstrapService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminDisciplineController extends Controller
{
    public function index(Request $request): View
    {
        $school = $this->resolveSchool($request);
        $schoolId = $school->id;

        $availableSchools = $request->user()?->isSuperAdmin()
            ? School::withCount(['classes', 'students', 'users'])->orderBy('id')->get()
            : School::where('id', $schoolId)->withCount(['classes', 'students', 'users'])->get();

        $policy = DisciplineBootstrapService::ensureDefaultsForSchool($schoolId);

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

        $categories = DisciplineCategory::with('items')
            ->where('school_id', $schoolId)
            ->orderBy('sort_order')
            ->orderBy('code')
            ->get();

        $thresholds = DisciplineThreshold::where('school_id', $schoolId)
            ->orderBy('min_points')
            ->get();

        $amnestyRules = DisciplineAmnestyRule::where('school_id', $schoolId)
            ->orderBy('code')
            ->get();

        $classes = SchoolClass::where('school_id', $schoolId)
            ->orderBy('grade_level')
            ->orderBy('name')
            ->get();

        $students = Student::with('schoolClass')
            ->where('school_id', $schoolId)
            ->where('status', 'active')
            ->orderBy('full_name')
            ->get();

        // Load discipline records according to reset_period policy
        $recordsQuery = StudentDisciplineRecord::with(['student.schoolClass', 'disciplineItem.category', 'amnestyRule', 'recorder'])
            ->where('school_id', $schoolId)
            ->where('status', 'APPROVED');

        if ($policy->reset_period === 'SEMESTER') {
            if ($selectedYear) {
                $recordsQuery->where('academic_year_id', $selectedYear->id);
            }
            $recordsQuery->where('academic_semester', $selectedSemester);
        } elseif ($policy->reset_period === 'ACADEMIC_YEAR' && $selectedYear) {
            $recordsQuery->where('academic_year_id', $selectedYear->id);
        }

        $allActiveRecords = $recordsQuery->orderByDesc('record_date')->orderByDesc('id')->get();
        $recordsByStudent = $allActiveRecords->groupBy('student_id');

        $studentSummaries = $students->map(function ($st) use ($recordsByStudent, $thresholds) {
            $stRecords = $recordsByStudent->get($st->id, collect());
            $violationPts = (int) $stRecords->where('record_type', 'VIOLATION')->sum('points');
            $amnestyPts = (int) $stRecords->where('record_type', 'AMNESTY')->sum('points');
            $netPts = max(0, $violationPts - $amnestyPts);

            $matchedThreshold = $thresholds
                ->where('is_active', true)
                ->filter(function ($th) use ($netPts) {
                    return $netPts >= $th->min_points && ($th->max_points === null || $netPts <= $th->max_points);
                })
                ->sortByDesc('min_points')
                ->first();

            return [
                'student' => $st,
                'violation_points' => $violationPts,
                'amnesty_points' => $amnestyPts,
                'net_points' => $netPts,
                'violation_count' => $stRecords->where('record_type', 'VIOLATION')->count(),
                'amnesty_count' => $stRecords->where('record_type', 'AMNESTY')->count(),
                'threshold' => $matchedThreshold,
            ];
        })->sortByDesc('net_points')->values();

        $user = $request->user();
        $canManageSettings = ! $user || in_array($user->role, ['super_admin', 'school_admin', 'waka_kesiswaan'], true);

        return view('admin.kesiswaan.tatib', [
            'school' => $school,
            'policy' => $policy,
            'academicYears' => $academicYears,
            'selectedYear' => $selectedYear,
            'selectedSemester' => $selectedSemester,
            'categories' => $categories,
            'thresholds' => $thresholds,
            'amnestyRules' => $amnestyRules,
            'classes' => $classes,
            'students' => $students,
            'studentSummaries' => $studentSummaries,
            'recentRecords' => $allActiveRecords->take(60),
            'canManageSettings' => $canManageSettings,
        ]);
    }
}

