<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Services\StudentEnrollmentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AcademicController extends Controller
{
    /**
     * Get list of schools.
     */
    public function schools(): JsonResponse
    {
        $schools = School::where('is_active', true)->get();

        return response()->json([
            'success' => true,
            'data' => $schools,
        ]);
    }

    /**
     * Get list of classes for a school.
     */
    public function classes(Request $request): JsonResponse
    {
        $schoolId = $request->query('school_id', $request->user()?->school_id ?? 1);

        $classes = SchoolClass::with('homeroomTeacher')
            ->where('school_id', $schoolId)
            ->where('is_active', true)
            ->orderBy('grade_level')
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $classes,
        ]);
    }

    /**
     * Get list of students for a school/class.
     */
    public function students(Request $request): JsonResponse
    {
        $schoolId = $request->query('school_id', $request->user()?->school_id ?? 1);
        $classId = $request->query('class_id');
        $search = $request->query('search');
        $entryYearId = $request->query('entry_academic_year_id');
        $status = $request->query('status', 'active'); // active|graduated|transferred|dropout|all

        $query = Student::with(['schoolClass', 'user', 'entryAcademicYear'])
            ->where('school_id', $schoolId)
            ->ofCohort($entryYearId ? (int) $entryYearId : null);

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        if ($classId) {
            $query->where('class_id', $classId);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                    ->orWhere('nis', 'like', "%{$search}%")
                    ->orWhere('nisn', 'like', "%{$search}%");
            });
        }

        $students = $query->orderBy('full_name')->get();

        return response()->json([
            'success' => true,
            'data' => $students,
        ]);
    }

    /**
     * Store a new class.
     */
    public function storeClass(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'school_id' => 'required|exists:schools,id',
            'name' => 'required|string|max:50',
            'grade_level' => 'required|integer|between:1,13',
            'academic_year_id' => 'nullable|exists:academic_years,id',
            'academic_year' => 'nullable|string|max:20',
            'curriculum_id' => 'nullable|exists:curriculums,id',
            'department_id' => 'nullable|exists:departments,id',
            'vocational_program_id' => 'nullable|exists:vocational_programs,id',
            'vocational_concentration_id' => 'nullable|exists:vocational_concentrations,id',
            'major' => 'nullable|string|max:50',
            'homeroom_teacher_id' => 'nullable|exists:users,id',
        ]);

        if (empty($validated['academic_year']) && !empty($validated['academic_year_id'])) {
            $ay = \App\Models\AcademicYear::find($validated['academic_year_id']);
            if ($ay) {
                $validated['academic_year'] = $ay->name;
            }
        }

        if (!empty($validated['vocational_concentration_id']) && empty($validated['vocational_program_id'])) {
            $conc = \App\Models\VocationalConcentration::find($validated['vocational_concentration_id']);
            if ($conc) {
                $validated['vocational_program_id'] = $conc->vocational_program_id;
            }
        }

        $class = SchoolClass::create([
            ...$validated,
            'is_active' => true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Rombongan belajar (kelas) berhasil ditambahkan.',
            'data' => $class->load(['department', 'homeroomTeacher', 'curriculum', 'vocationalProgram', 'vocationalConcentration']),
        ], 201);
    }

    /**
     * Update an existing class.
     */
    public function updateClass(Request $request, int $id): JsonResponse
    {
        $class = SchoolClass::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:50',
            'grade_level' => 'sometimes|required|integer|between:1,13',
            'academic_year_id' => 'nullable|exists:academic_years,id',
            'academic_year' => 'nullable|string|max:20',
            'curriculum_id' => 'nullable|exists:curriculums,id',
            'department_id' => 'nullable|exists:departments,id',
            'vocational_program_id' => 'nullable|exists:vocational_programs,id',
            'vocational_concentration_id' => 'nullable|exists:vocational_concentrations,id',
            'major' => 'nullable|string|max:50',
            'homeroom_teacher_id' => 'nullable|exists:users,id',
            'is_active' => 'sometimes|boolean',
        ]);

        if (isset($validated['academic_year_id']) && empty($validated['academic_year'])) {
            $ay = \App\Models\AcademicYear::find($validated['academic_year_id']);
            if ($ay) {
                $validated['academic_year'] = $ay->name;
            }
        }

        if (!empty($validated['vocational_concentration_id']) && empty($validated['vocational_program_id'])) {
            $conc = \App\Models\VocationalConcentration::find($validated['vocational_concentration_id']);
            if ($conc) {
                $validated['vocational_program_id'] = $conc->vocational_program_id;
            }
        }

        $class->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Rombongan belajar (kelas) berhasil diperbarui.',
            'data' => $class->load(['department', 'homeroomTeacher', 'curriculum', 'vocationalProgram', 'vocationalConcentration']),
        ]);
    }

    /**
     * Delete a class.
     */
    public function destroyClass(int $id): JsonResponse
    {
        $class = SchoolClass::findOrFail($id);

        if ($class->students()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Kelas tidak dapat dihapus karena masih memiliki ' . $class->students()->count() . ' siswa terdaftar.',
            ], 422);
        }

        $class->delete();

        return response()->json([
            'success' => true,
            'message' => 'Rombongan belajar berhasil dihapus.',
        ]);
    }

    /**
     * Store a new student with Dapodik fields.
     */
    public function storeStudent(Request $request, StudentEnrollmentService $enrollment): JsonResponse
    {
        // Kompatibilitas dengan form lama yang mengirim school_class_id
        if (! $request->filled('class_id') && $request->filled('school_class_id')) {
            $request->merge(['class_id' => $request->input('school_class_id')]);
        }

        $validated = $request->validate([
            'school_id' => 'required|exists:schools,id',
            'class_id' => 'nullable|exists:classes,id',
            'entry_academic_year_id' => 'nullable|exists:academic_years,id',
            'entry_grade_level' => 'nullable|integer|between:1,13',
            'registration_type' => 'nullable|in:baru,pindahan,kembali_bersekolah',
            'previous_school' => 'nullable|string|max:150|required_if:registration_type,pindahan',
            'enrollment_date' => 'nullable|date',
            'nis' => 'required|string|max:20|unique:students,nis',
            'nisn' => 'nullable|string|size:10|unique:students,nisn',
            'nik' => 'nullable|string|size:16|unique:students,nik',
            'no_kk' => 'nullable|string|size:16',
            'full_name' => 'required|string|max:100',
            'gender' => 'required|in:L,P',
            'birth_place' => 'nullable|string|max:100',
            'birth_date' => 'nullable|date',
            'religion' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'kps_receiver' => 'boolean',
            'kip_receiver' => 'boolean',
            'parent_phone' => 'nullable|string|max:20',
            'parent_name' => 'nullable|string|max:100',
        ], [
            'previous_school.required_if' => 'Sekolah asal wajib diisi untuk siswa pindahan.',
        ]);

        $class = ! empty($validated['class_id']) ? SchoolClass::find($validated['class_id']) : null;

        // Angkatan default: tahun ajaran rombel, lalu tahun ajaran aktif sekolah.
        $entryYear = ! empty($validated['entry_academic_year_id'])
            ? AcademicYear::find($validated['entry_academic_year_id'])
            : ($class?->academicYear
                ?? AcademicYear::where('school_id', $validated['school_id'])->where('is_active', true)->first());

        $student = DB::transaction(function () use ($validated, $class, $entryYear, $enrollment) {
            $student = Student::create([
                ...collect($validated)->except('class_id')->all(),
                'entry_academic_year_id' => $entryYear?->id,
                'entry_grade_level' => $validated['entry_grade_level'] ?? $class?->grade_level,
                'registration_type' => $validated['registration_type'] ?? 'baru',
                'status' => 'active',
                'enrollment_date' => $validated['enrollment_date'] ?? now()->toDateString(),
            ]);

            if ($class) {
                $enrollment->place($student, $class, $class->academicYear ?? $entryYear, $student->enrollment_date?->toDateString());
            }

            return $student;
        });

        return response()->json([
            'success' => true,
            'message' => 'Data Siswa Dapodik berhasil ditambahkan.',
            'data' => $student->fresh(['schoolClass', 'entryAcademicYear', 'classHistories']),
        ], 201);
    }
}
