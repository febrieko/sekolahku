<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Curriculum;
use App\Models\CurriculumGradeLevel;
use App\Models\CurriculumSubject;
use App\Models\CurriculumSubjectGroup;
use App\Models\SchoolClass;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CurriculumController extends Controller
{
    /**
     * Get all curriculums for a school.
     */
    public function index(Request $request): JsonResponse
    {
        $schoolId = $request->query('school_id', 1);

        $curriculums = Curriculum::where('school_id', $schoolId)
            ->with(['gradeLevels', 'curriculumSubjects.subject', 'classes'])
            ->withCount(['classes', 'subjects'])
            ->get();

        return response()->json([
            'success' => true,
            'data' => $curriculums,
        ]);
    }

    /**
     * Store a new curriculum.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'school_id' => 'required|exists:schools,id',
            'code' => 'required|string|max:30',
            'name' => 'required|string|max:150',
            'target_level' => 'nullable|string|max:50',
            'description' => 'nullable|string',
        ]);

        $curriculum = Curriculum::create([
            'school_id' => $validated['school_id'],
            'code' => strtoupper($validated['code']),
            'name' => $validated['name'],
            'target_level' => $validated['target_level'] ?? 'SMA / SMK',
            'description' => $validated['description'] ?? null,
            'is_active' => true,
        ]);

        // Otomatis buat template kelompok default untuk kurikulum baru
        $defaultGroups = [
            ['code' => 'WAJIB_UMUM', 'name' => 'Kelompok Umum (Wajib Nasional)', 'order_no' => 1, 'color' => 'badge-primary'],
            ['code' => 'PEMINATAN_PILIHAN', 'name' => 'Kelompok Pilihan / Peminatan', 'order_no' => 2, 'color' => 'badge-info'],
            ['code' => 'P5_PROJEK', 'name' => 'Projek Profil Pelajar Pancasila (P5)', 'order_no' => 3, 'color' => 'badge-warning'],
            ['code' => 'MULOK', 'name' => 'Muatan Lokal (Mulok)', 'order_no' => 4, 'color' => 'badge-secondary'],
            ['code' => 'KEAGAMAAN', 'name' => 'Keagamaan / Kepesantrenan', 'order_no' => 5, 'color' => 'badge-success'],
        ];
        foreach ($defaultGroups as $grp) {
            CurriculumSubjectGroup::create([
                'school_id' => $curriculum->school_id,
                'curriculum_id' => $curriculum->id,
                'code' => $grp['code'],
                'name' => $grp['name'],
                'order_no' => $grp['order_no'],
                'color' => $grp['color'],
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Kurikulum berhasil ditambahkan',
            'data' => $curriculum,
        ], 201);
    }

    /**
     * Update a curriculum.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $curriculum = Curriculum::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:150',
            'code' => 'sometimes|required|string|max:30',
            'target_level' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'is_active' => 'sometimes|boolean',
        ]);

        $curriculum->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Kurikulum berhasil diperbarui',
            'data' => $curriculum,
        ]);
    }

    /**
     * Delete a curriculum.
     */
    public function destroy(int $id): JsonResponse
    {
        $curriculum = Curriculum::findOrFail($id);

        if ($curriculum->classes()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak dapat menghapus kurikulum yang sedang digunakan oleh rombongan belajar aktif.',
            ], 422);
        }

        $curriculum->delete();

        return response()->json([
            'success' => true,
            'message' => 'Kurikulum berhasil dihapus',
        ]);
    }

    /**
     * Assign curriculum to a specific grade level (Multi-Jenjang Kurikulum).
     */
    public function assignGradeLevel(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'school_id' => 'required|exists:schools,id',
            'curriculum_id' => 'required|exists:curriculums,id',
            'grade_level' => 'required|integer|min:1|max:12',
            'phase' => 'nullable|string|max:30',
        ]);

        $mapping = CurriculumGradeLevel::updateOrCreate(
            [
                'school_id' => $validated['school_id'],
                'grade_level' => $validated['grade_level'],
            ],
            [
                'curriculum_id' => $validated['curriculum_id'],
                'phase' => $validated['phase'] ?? null,
            ]
        );

        // Update all classes at this grade level to inherit the newly assigned curriculum
        SchoolClass::where('school_id', $validated['school_id'])
            ->where('grade_level', $validated['grade_level'])
            ->update(['curriculum_id' => $validated['curriculum_id']]);

        return response()->json([
            'success' => true,
            'message' => 'Kurikulum untuk Tingkat ' . $validated['grade_level'] . ' berhasil ditetapkan',
            'data' => $mapping->load('curriculum'),
        ]);
    }

    /**
     * Attach / update subject in curriculum.
     */
    public function saveSubject(Request $request, int $id): JsonResponse
    {
        $curriculum = Curriculum::findOrFail($id);

        $validated = $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'curriculum_subject_group_id' => 'nullable|exists:curriculum_subject_groups,id',
            'subject_group' => 'nullable|string',
            'hours_per_week' => 'required|integer|min:1|max:15',
            'passing_grade' => 'required|numeric|min:0|max:100',
        ]);

        $groupId = $validated['curriculum_subject_group_id'] ?? null;
        $groupCode = 'WAJIB_UMUM';

        if ($groupId) {
            $group = CurriculumSubjectGroup::where('curriculum_id', $curriculum->id)->find($groupId);
            if ($group) {
                $validEnums = ['WAJIB_UMUM', 'PEMINATAN_PILIHAN', 'P5_PROJEK', 'MULOK', 'KEAGAMAAN'];
                $groupCode = in_array($group->code, $validEnums) ? $group->code : 'WAJIB_UMUM';
            }
        } elseif (!empty($validated['subject_group'])) {
            $matchedGroup = CurriculumSubjectGroup::where('curriculum_id', $curriculum->id)
                ->where('code', $validated['subject_group'])
                ->first();
            if ($matchedGroup) {
                $groupId = $matchedGroup->id;
                $groupCode = $matchedGroup->code;
            } else {
                $validEnums = ['WAJIB_UMUM', 'PEMINATAN_PILIHAN', 'P5_PROJEK', 'MULOK', 'KEAGAMAAN'];
                $groupCode = in_array($validated['subject_group'], $validEnums) ? $validated['subject_group'] : 'WAJIB_UMUM';
            }
        }

        $curriculumSubject = CurriculumSubject::updateOrCreate(
            [
                'curriculum_id' => $curriculum->id,
                'subject_id' => $validated['subject_id'],
            ],
            [
                'curriculum_subject_group_id' => $groupId,
                'subject_group' => $groupCode,
                'hours_per_week' => $validated['hours_per_week'],
                'passing_grade' => $validated['passing_grade'],
                'is_active' => true,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Mata pelajaran berhasil diselaraskan ke kurikulum',
            'data' => $curriculumSubject->load(['subject', 'group']),
        ]);
    }

    /**
     * Remove subject from curriculum.
     */
    public function removeSubject(int $id, int $subjectId): JsonResponse
    {
        $curriculum = Curriculum::findOrFail($id);
        CurriculumSubject::where('curriculum_id', $curriculum->id)
            ->where('subject_id', $subjectId)
            ->delete();

        return response()->json([
            'success' => true,
            'message' => 'Mata pelajaran berhasil dikeluarkan dari kurikulum.',
        ]);
    }

    /**
     * Get subject groups for a curriculum.
     */
    public function getSubjectGroups(int $id): JsonResponse
    {
        $curriculum = Curriculum::findOrFail($id);
        $groups = CurriculumSubjectGroup::where('curriculum_id', $curriculum->id)
            ->withCount('curriculumSubjects')
            ->orderBy('order_no')
            ->get();

        return response()->json([
            'success' => true,
            'curriculum' => [
                'id' => $curriculum->id,
                'name' => $curriculum->name,
                'code' => $curriculum->code,
            ],
            'data' => $groups,
        ]);
    }

    /**
     * Store a new subject group.
     */
    public function saveSubjectGroup(Request $request, int $id): JsonResponse
    {
        $curriculum = Curriculum::findOrFail($id);

        $validated = $request->validate([
            'code' => 'required|string|max:30',
            'name' => 'required|string|max:150',
            'description' => 'nullable|string|max:255',
            'order_no' => 'nullable|integer|min:1',
            'color' => 'nullable|string|max:30',
        ]);

        $code = strtoupper(trim($validated['code']));

        $exists = CurriculumSubjectGroup::where('curriculum_id', $curriculum->id)
            ->where('code', $code)
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Kode kelompok mata pelajaran "' . $code . '" sudah digunakan pada kurikulum ini.',
            ], 422);
        }

        $orderNo = $validated['order_no'] ?? ((CurriculumSubjectGroup::where('curriculum_id', $curriculum->id)->max('order_no') ?? 0) + 1);

        $group = CurriculumSubjectGroup::create([
            'school_id' => $curriculum->school_id,
            'curriculum_id' => $curriculum->id,
            'code' => $code,
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'order_no' => $orderNo,
            'color' => $validated['color'] ?? 'badge-primary',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Kelompok mata pelajaran berhasil ditambahkan',
            'data' => $group,
        ], 201);
    }

    /**
     * Update an existing subject group.
     */
    public function updateSubjectGroup(Request $request, int $id, int $groupId): JsonResponse
    {
        $curriculum = Curriculum::findOrFail($id);
        $group = CurriculumSubjectGroup::where('curriculum_id', $curriculum->id)->findOrFail($groupId);

        $validated = $request->validate([
            'code' => 'sometimes|required|string|max:30',
            'name' => 'sometimes|required|string|max:150',
            'description' => 'nullable|string|max:255',
            'order_no' => 'nullable|integer|min:1',
            'color' => 'nullable|string|max:30',
        ]);

        if (isset($validated['code'])) {
            $code = strtoupper(trim($validated['code']));
            $exists = CurriculumSubjectGroup::where('curriculum_id', $curriculum->id)
                ->where('code', $code)
                ->where('id', '!=', $group->id)
                ->exists();

            if ($exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kode kelompok mata pelajaran "' . $code . '" sudah digunakan pada kurikulum ini.',
                ], 422);
            }
            $validated['code'] = $code;
        }

        $group->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Kelompok mata pelajaran berhasil diperbarui',
            'data' => $group,
        ]);
    }

    /**
     * Delete a subject group.
     */
    public function deleteSubjectGroup(int $id, int $groupId): JsonResponse
    {
        $curriculum = Curriculum::findOrFail($id);
        $group = CurriculumSubjectGroup::where('curriculum_id', $curriculum->id)->findOrFail($groupId);

        $subjectCount = $group->curriculumSubjects()->count();
        if ($subjectCount > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak dapat menghapus kelompok "' . $group->name . '" karena masih terdapat ' . $subjectCount . ' mata pelajaran yang terpetakan ke kelompok ini. Silakan pindahkan mapel terlebih dahulu.',
            ], 422);
        }

        $group->delete();

        return response()->json([
            'success' => true,
            'message' => 'Kelompok mata pelajaran berhasil dihapus',
        ]);
    }

    /**
     * Apply preset template subject groups.
     */
    public function applyTemplateSubjectGroups(Request $request, int $id): JsonResponse
    {
        $curriculum = Curriculum::findOrFail($id);
        $templateKey = $request->input('template', 'merdeka');

        $templates = [
            'merdeka' => [
                ['code' => 'UMUM', 'name' => 'Kelompok Mata Pelajaran Umum', 'color' => 'badge-primary'],
                ['code' => 'PILIHAN', 'name' => 'Kelompok Mata Pelajaran Pilihan / Kejuruan', 'color' => 'badge-info'],
                ['code' => 'P5', 'name' => 'Projek Penguatan Profil Pelajar Pancasila (P5)', 'color' => 'badge-warning'],
                ['code' => 'MULOK', 'name' => 'Muatan Lokal (Mulok)', 'color' => 'badge-secondary'],
            ],
            'k13' => [
                ['code' => 'KEL_A', 'name' => 'Kelompok A (Wajib / Muatan Nasional)', 'color' => 'badge-primary'],
                ['code' => 'KEL_B', 'name' => 'Kelompok B (Wajib / Muatan Kewilayahan)', 'color' => 'badge-info'],
                ['code' => 'KEL_C', 'name' => 'Kelompok C (Peminatan / Kejuruan)', 'color' => 'badge-warning'],
                ['code' => 'MULOK', 'name' => 'Muatan Lokal', 'color' => 'badge-secondary'],
            ],
            'kemenag' => [
                ['code' => 'PAI_ARAB', 'name' => 'Pendidikan Agama Islam (PAI) & Bahasa Arab', 'color' => 'badge-success'],
                ['code' => 'UMUM', 'name' => 'Kelompok Mata Pelajaran Umum', 'color' => 'badge-primary'],
                ['code' => 'PILIHAN', 'name' => 'Kelompok Pilihan / Peminatan', 'color' => 'badge-info'],
                ['code' => 'P5RA', 'name' => 'Projek Profil Pelajar Pancasila & Rahmatan Lil Alamin (P5RA)', 'color' => 'badge-warning'],
                ['code' => 'MULOK', 'name' => 'Muatan Lokal', 'color' => 'badge-secondary'],
            ],
            'pesantren' => [
                ['code' => 'DINIYAH', 'name' => 'Kelompok Diniyah / Salafiyah / Kepesantrenan', 'color' => 'badge-success'],
                ['code' => 'TAHFIDZ', 'name' => 'Tahfidz Al-Qur\'an & Mutun Ilmiah', 'color' => 'badge-info'],
                ['code' => 'DIKNAS', 'name' => 'Mata Pelajaran Umum (Kemendikbud)', 'color' => 'badge-primary'],
                ['code' => 'BAHASA', 'name' => 'Bahasa Arab & Asing', 'color' => 'badge-warning'],
                ['code' => 'MULOK', 'name' => 'Muatan Lokal / Keterampilan Santri', 'color' => 'badge-secondary'],
            ],
        ];

        $selectedTemplate = $templates[$templateKey] ?? $templates['merdeka'];

        $createdCount = 0;
        foreach ($selectedTemplate as $idx => $item) {
            $existing = CurriculumSubjectGroup::where('curriculum_id', $curriculum->id)
                ->where('code', $item['code'])
                ->first();

            if (!$existing) {
                CurriculumSubjectGroup::create([
                    'school_id' => $curriculum->school_id,
                    'curriculum_id' => $curriculum->id,
                    'code' => $item['code'],
                    'name' => $item['name'],
                    'order_no' => $idx + 1,
                    'color' => $item['color'],
                ]);
                $createdCount++;
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Berhasil menerapkan template kelompok mapel (' . $createdCount . ' kelompok baru ditambahkan).',
            'data' => CurriculumSubjectGroup::where('curriculum_id', $curriculum->id)->orderBy('order_no')->get(),
        ]);
    }
}
