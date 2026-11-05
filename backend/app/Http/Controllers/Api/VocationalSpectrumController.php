<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\VocationalConcentration;
use App\Models\VocationalField;
use App\Models\VocationalProgram;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VocationalSpectrumController extends Controller
{
    /**
     * Get complete vocational spectrum (Fields, Programs, Concentrations).
     */
    public function index(Request $request): JsonResponse
    {
        $schoolId = $request->query('school_id', 1);

        $fields = VocationalField::where('school_id', $schoolId)
            ->with(['programs.concentrations' => function ($q) {
                $q->withCount('classes');
            }])
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $fields,
        ]);
    }

    /**
     * Store new Vocational Field (Bidang Keahlian).
     */
    public function storeField(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'school_id' => 'required|exists:schools,id',
            'code' => 'required|string|max:20',
            'name' => 'required|string|max:150',
            'description' => 'nullable|string',
        ]);

        $field = VocationalField::create([
            'school_id' => $validated['school_id'],
            'code' => strtoupper($validated['code']),
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'is_active' => true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Bidang keahlian berhasil ditambahkan',
            'data' => $field,
        ], 201);
    }

    /**
     * Store new Vocational Program (Program Keahlian).
     */
    public function storeProgram(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'school_id' => 'required|exists:schools,id',
            'vocational_field_id' => 'required|exists:vocational_fields,id',
            'code' => 'required|string|max:30',
            'name' => 'required|string|max:150',
            'program_duration_years' => 'required|integer|in:3,4',
            'description' => 'nullable|string',
        ]);

        $program = VocationalProgram::create([
            'school_id' => $validated['school_id'],
            'vocational_field_id' => $validated['vocational_field_id'],
            'code' => strtoupper($validated['code']),
            'name' => $validated['name'],
            'program_duration_years' => $validated['program_duration_years'],
            'description' => $validated['description'] ?? null,
            'is_active' => true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Program keahlian berhasil ditambahkan',
            'data' => $program->load('field'),
        ], 201);
    }

    /**
     * Store new Vocational Concentration (Konsentrasi Keahlian).
     */
    public function storeConcentration(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'school_id' => 'required|exists:schools,id',
            'vocational_program_id' => 'required|exists:vocational_programs,id',
            'code' => 'required|string|max:30',
            'dapodik_code' => 'nullable|string|max:20',
            'name' => 'required|string|max:150',
            'description' => 'nullable|string',
            'competencies_summary' => 'nullable|string',
            'industry_partners' => 'nullable|string',
        ]);

        $concentration = VocationalConcentration::create([
            'school_id' => $validated['school_id'],
            'vocational_program_id' => $validated['vocational_program_id'],
            'code' => strtoupper($validated['code']),
            'dapodik_code' => $validated['dapodik_code'] ?? null,
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'competencies_summary' => $validated['competencies_summary'] ?? null,
            'industry_partners' => $validated['industry_partners'] ?? null,
            'is_active' => true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Konsentrasi keahlian SMK berhasil didaftarkan',
            'data' => $concentration->load('program.field'),
        ], 201);
    }

    /**
     * Update Vocational Concentration.
     */
    public function updateConcentration(Request $request, int $id): JsonResponse
    {
        $concentration = VocationalConcentration::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:150',
            'dapodik_code' => 'nullable|string|max:20',
            'description' => 'nullable|string',
            'competencies_summary' => 'nullable|string',
            'industry_partners' => 'nullable|string',
            'is_active' => 'sometimes|boolean',
        ]);

        $concentration->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Konsentrasi keahlian berhasil diperbarui',
            'data' => $concentration,
        ]);
    }

    /**
     * Delete Vocational Concentration.
     */
    public function destroyConcentration(int $id): JsonResponse
    {
        $concentration = VocationalConcentration::findOrFail($id);

        if ($concentration->classes()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak dapat menghapus konsentrasi keahlian yang memiliki rombongan belajar aktif.',
            ], 422);
        }

        $concentration->delete();

        return response()->json([
            'success' => true,
            'message' => 'Konsentrasi keahlian berhasil dihapus',
        ]);
    }

    /**
     * Toggle/Switch School Type (SMA <-> SMK) for testing and multi-tenancy.
     */
    public function switchSchoolLevel(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'school_id' => 'required|exists:schools,id',
            'level' => 'required|in:SD,SMP,SMA,SMK,PONPES',
        ]);

        $school = School::findOrFail($validated['school_id']);
        $school->level = $validated['level'];
        
        // Auto-update school name if switching between generic SMA/SMK
        if ($validated['level'] === 'SMK' && str_contains($school->name, 'SMA Negeri 1')) {
            $school->name = 'SMK Negeri 1 Jakarta';
        } elseif ($validated['level'] === 'SMA' && str_contains($school->name, 'SMK Negeri 1')) {
            $school->name = 'SMA Negeri 1 Jakarta';
        }

        $school->save();

        return response()->json([
            'success' => true,
            'message' => "Tipe jenjang sekolah berhasil diubah menjadi {$school->level} ({$school->name})",
            'data' => $school,
        ]);
    }
}
