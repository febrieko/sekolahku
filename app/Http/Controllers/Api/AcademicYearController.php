<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AcademicSemester;
use App\Models\AcademicYear;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AcademicYearController extends Controller
{
    /**
     * Display a listing of academic years.
     */
    public function index(Request $request): JsonResponse
    {
        $schoolId = $request->query('school_id', $request->user()?->school_id ?? 1);

        $years = AcademicYear::with('semesters')
            ->where('school_id', $schoolId)
            ->orderByDesc('name')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $years,
        ]);
    }

    /**
     * Store a newly created academic year.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'school_id' => 'required|exists:schools,id',
            'name' => 'required|string|max:20', // e.g. "2026/2027"
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'is_active' => 'boolean',
        ]);

        if (! empty($validated['is_active'])) {
            AcademicYear::where('school_id', $validated['school_id'])->update(['is_active' => false]);
        }

        $academicYear = AcademicYear::create($validated);

        // Auto-create Odd & Even semesters
        AcademicSemester::create([
            'academic_year_id' => $academicYear->id,
            'semester' => 'odd',
            'start_date' => $validated['start_date'],
            'end_date' => date('Y-m-d', strtotime($validated['start_date'] . ' + 5 months')),
            'is_active' => ! empty($validated['is_active']),
        ]);

        AcademicSemester::create([
            'academic_year_id' => $academicYear->id,
            'semester' => 'even',
            'start_date' => date('Y-m-d', strtotime($validated['start_date'] . ' + 6 months')),
            'end_date' => $validated['end_date'],
            'is_active' => false,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Tahun ajaran berhasil dibuat.',
            'data' => $academicYear->load('semesters'),
        ], 201);
    }

    /**
     * Set active academic year.
     */
    public function activate(int $id): JsonResponse
    {
        $year = AcademicYear::findOrFail($id);

        AcademicYear::where('school_id', $year->school_id)->where('id', '!=', $year->id)->update(['is_active' => false]);
        AcademicYear::where('id', $year->id)->update(['is_active' => true]);
        $year->refresh();

        return response()->json([
            'success' => true,
            'message' => "Tahun ajaran {$year->name} telah diaktifkan.",
            'data' => $year,
        ]);
    }
}
