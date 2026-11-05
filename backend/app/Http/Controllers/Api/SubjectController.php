<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    /**
     * List subjects.
     */
    public function index(Request $request): JsonResponse
    {
        $schoolId = $request->query('school_id', $request->user()?->school_id ?? 1);
        $category = $request->query('category');
        $curriculumId = $request->query('curriculum_id');

        $query = Subject::with(['curriculumSubjects.curriculum'])
            ->where('school_id', $schoolId)
            ->where('is_active', true);

        if ($category) {
            $query->where('category', $category);
        }

        if ($curriculumId) {
            $query->whereHas('curriculumSubjects', function ($q) use ($curriculumId) {
                $q->where('curriculum_id', $curriculumId);
            });
        }

        $subjects = $query->orderBy('name')->get();

        return response()->json([
            'success' => true,
            'data' => $subjects,
        ]);
    }

    /**
     * Store new subject.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'school_id' => 'required|exists:schools,id',
            'code' => 'required|string|max:20',
            'name' => 'required|string|max:150',
            'category' => 'nullable|in:A_MUATAN_NASIONAL,B_MUATAN_KEWILAYAHAN,C_KEJURUAN_PEMINATAN,MULOK',
            'color' => 'nullable|string|max:25',
            'religion_filter' => 'nullable|string|max:30',
            'passing_grade' => 'nullable|numeric|between:0,100',
            'is_active' => 'boolean',
            // Kelompok Mapel & Kurikulum
            'curriculum_id' => 'nullable|exists:curriculums,id',
            'curriculum_subject_group_id' => 'nullable|exists:curriculum_subject_groups,id',
            'subject_group' => 'nullable|string',
            'hours_per_week' => 'nullable|integer|between:1,15',
        ]);

        if (empty($validated['category'])) {
            $group = $validated['subject_group'] ?? 'WAJIB_UMUM';
            $validated['category'] = match ($group) {
                'PEMINATAN_PILIHAN', 'PILIHAN', 'KEL_C' => 'C_KEJURUAN_PEMINATAN',
                'MULOK' => 'MULOK',
                default => 'A_MUATAN_NASIONAL',
            };
        }

        $subject = Subject::create([
            'school_id' => $validated['school_id'],
            'code' => strtoupper(trim($validated['code'])),
            'name' => trim($validated['name']),
            'category' => $validated['category'],
            'color' => $validated['color'] ?? '#4680ff',
            'religion_filter' => $validated['religion_filter'] ?? Subject::detectReligionFromName($validated['name'], $validated['code']),
            'passing_grade' => $validated['passing_grade'] ?? 75.00,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        // Hubungkan ke kurikulum dan kelompok mapel jika kurikulum dipilih
        if (!empty($validated['curriculum_id'])) {
            $matchedGrp = null;
            if (!empty($validated['curriculum_subject_group_id'])) {
                $matchedGrp = \App\Models\CurriculumSubjectGroup::where('curriculum_id', $validated['curriculum_id'])->find($validated['curriculum_subject_group_id']);
            } elseif (!empty($validated['subject_group'])) {
                $matchedGrp = \App\Models\CurriculumSubjectGroup::where('curriculum_id', $validated['curriculum_id'])
                    ->where('code', $validated['subject_group'])
                    ->first();
            }

            $validEnums = ['WAJIB_UMUM', 'PEMINATAN_PILIHAN', 'P5_PROJEK', 'MULOK', 'KEAGAMAAN'];
            $codeForEnum = in_array($validated['subject_group'] ?? '', $validEnums) ? $validated['subject_group'] : ($matchedGrp && in_array($matchedGrp->code, $validEnums) ? $matchedGrp->code : 'WAJIB_UMUM');

            \App\Models\CurriculumSubject::updateOrCreate(
                [
                    'curriculum_id' => $validated['curriculum_id'],
                    'subject_id' => $subject->id,
                ],
                [
                    'curriculum_subject_group_id' => $matchedGrp?->id,
                    'subject_group' => $codeForEnum,
                    'hours_per_week' => $validated['hours_per_week'] ?? 2,
                    'passing_grade' => $validated['passing_grade'] ?? 75.00,
                    'is_active' => true,
                ]
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Mata pelajaran berhasil ditambahkan dan diselaraskan ke kurikulum.',
            'data' => $subject->load(['curriculumSubjects.curriculum']),
        ], 201);
    }

    /**
     * Update existing subject.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $subject = Subject::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:150',
            'code' => 'sometimes|required|string|max:20',
            'category' => 'nullable|in:A_MUATAN_NASIONAL,B_MUATAN_KEWILAYAHAN,C_KEJURUAN_PEMINATAN,MULOK',
            'color' => 'nullable|string|max:25',
            'religion_filter' => 'nullable|string|max:30',
            'passing_grade' => 'nullable|numeric|between:0,100',
            'is_active' => 'sometimes|boolean',
            // Kelompok Mapel & Kurikulum
            'curriculum_id' => 'nullable|exists:curriculums,id',
            'curriculum_subject_group_id' => 'nullable|exists:curriculum_subject_groups,id',
            'subject_group' => 'nullable|string',
            'hours_per_week' => 'nullable|integer|between:1,15',
        ]);

        if (isset($validated['code'])) {
            $validated['code'] = strtoupper(trim($validated['code']));
        }

        if (isset($validated['subject_group']) && empty($validated['category'])) {
            $validated['category'] = match ($validated['subject_group']) {
                'PEMINATAN_PILIHAN', 'PILIHAN', 'KEL_C' => 'C_KEJURUAN_PEMINATAN',
                'MULOK' => 'MULOK',
                default => 'A_MUATAN_NASIONAL',
            };
        }

        $subject->update(collect($validated)->only(['name', 'code', 'category', 'color', 'religion_filter', 'passing_grade', 'is_active'])->toArray());

        // Sinkronisasi kelompok mapel & alokasi jam ke kurikulum terkait
        if (!empty($validated['curriculum_id'])) {
            $matchedGrp = null;
            if (!empty($validated['curriculum_subject_group_id'])) {
                $matchedGrp = \App\Models\CurriculumSubjectGroup::where('curriculum_id', $validated['curriculum_id'])->find($validated['curriculum_subject_group_id']);
            } elseif (!empty($validated['subject_group'])) {
                $matchedGrp = \App\Models\CurriculumSubjectGroup::where('curriculum_id', $validated['curriculum_id'])
                    ->where('code', $validated['subject_group'])
                    ->first();
            }

            $validEnums = ['WAJIB_UMUM', 'PEMINATAN_PILIHAN', 'P5_PROJEK', 'MULOK', 'KEAGAMAAN'];
            $codeForEnum = in_array($validated['subject_group'] ?? '', $validEnums) ? $validated['subject_group'] : ($matchedGrp && in_array($matchedGrp->code, $validEnums) ? $matchedGrp->code : 'WAJIB_UMUM');

            \App\Models\CurriculumSubject::updateOrCreate(
                [
                    'curriculum_id' => $validated['curriculum_id'],
                    'subject_id' => $subject->id,
                ],
                [
                    'curriculum_subject_group_id' => $matchedGrp?->id,
                    'subject_group' => $codeForEnum,
                    'hours_per_week' => $validated['hours_per_week'] ?? 2,
                    'passing_grade' => $validated['passing_grade'] ?? $subject->passing_grade ?? 75.00,
                    'is_active' => true,
                ]
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Mata pelajaran berhasil diperbarui.',
            'data' => $subject->load(['curriculumSubjects.curriculum']),
        ]);
    }

    /**
     * Delete a subject.
     */
    public function destroy(int $id): JsonResponse
    {
        $subject = Subject::findOrFail($id);

        if ($subject->schedules()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Mata pelajaran tidak dapat dihapus karena masih digunakan dalam ' . $subject->schedules()->count() . ' jadwal KBM aktif.',
            ], 422);
        }

        $subject->curriculumSubjects()->delete();
        $subject->delete();

        return response()->json([
            'success' => true,
            'message' => 'Mata pelajaran berhasil dihapus.',
        ]);
    }
}
