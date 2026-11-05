<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Student;
use App\Models\StudentClassHistory;
use App\Services\StudentEnrollmentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Riwayat rombel & kenaikan kelas peserta didik.
 */
class StudentPromotionController extends Controller
{
    public function __construct(private StudentEnrollmentService $enrollment)
    {
    }

    /**
     * Riwayat rombel seorang siswa (urut tahun ajaran terbaru).
     */
    public function history(int $id): JsonResponse
    {
        $student = Student::with('entryAcademicYear')->findOrFail($id);

        $histories = StudentClassHistory::with(['schoolClass', 'academicYear'])
            ->where('student_id', $student->id)
            ->join('academic_years', 'academic_years.id', '=', 'student_class_histories.academic_year_id')
            ->orderByDesc('academic_years.start_date')
            ->select('student_class_histories.*')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'student' => $student,
                'histories' => $histories,
            ],
        ]);
    }

    /**
     * Daftar siswa aktif per rombel asal sebagai kandidat kenaikan kelas.
     */
    public function candidates(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'school_id' => 'required|exists:schools,id',
            'class_id' => 'required|exists:classes,id',
        ]);

        $students = Student::where('school_id', $validated['school_id'])
            ->where('class_id', $validated['class_id'])
            ->where('status', 'active')
            ->with(['schoolClass', 'entryAcademicYear'])
            ->orderBy('full_name')
            ->get(['id', 'full_name', 'nis', 'nisn', 'class_id', 'entry_academic_year_id']);

        return response()->json(['success' => true, 'data' => $students]);
    }

    /**
     * Proses kenaikan kelas massal ke tahun ajaran tujuan.
     */
    public function promote(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'school_id' => 'required|exists:schools,id',
            'target_academic_year_id' => [
                'required',
                Rule::exists('academic_years', 'id')->where('school_id', $request->input('school_id')),
            ],
            'entries' => 'required|array|min:1',
            'entries.*.student_id' => 'required|integer|distinct|exists:students,id',
            'entries.*.action' => ['required', Rule::in(array_keys(StudentEnrollmentService::ACTIONS))],
            'entries.*.target_class_id' => 'nullable|integer|exists:classes,id',
            'entries.*.notes' => 'nullable|string|max:255',
        ]);

        $result = $this->enrollment->promote(
            (int) $validated['school_id'],
            AcademicYear::findOrFail($validated['target_academic_year_id']),
            $validated['entries']
        );

        $s = $result['summary'];

        return response()->json([
            'success' => true,
            'message' => "Kenaikan kelas diproses: {$s['promote']} naik, {$s['retain']} tinggal, {$s['graduate']} lulus"
                . ($s['transfer'] + $s['dropout'] ? ", " . ($s['transfer'] + $s['dropout']) . " keluar" : '') . '.',
            'data' => $result,
        ]);
    }
}
