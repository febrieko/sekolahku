<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use App\Models\TeachingGroup;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TeachingGroupController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $schoolId = (int) ($request->query('school_id') ?: ($request->user()?->school_id ?? 1));

        $groups = TeachingGroup::with(['classes', 'subject', 'teacher', 'defaultRoom', 'manualStudents'])
            ->where('school_id', $schoolId)
            ->orderBy('name')
            ->get()
            ->map(function (TeachingGroup $group) {
                $students = $group->resolveStudents();
                $arr = $group->toArray();
                $arr['participants_count'] = $students->count();

                return $arr;
            });

        return response()->json([
            'success' => true,
            'data' => $groups,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'school_id' => 'required|exists:schools,id',
            'academic_year_id' => 'nullable|exists:academic_years,id',
            'code' => 'required|string|max:30',
            'name' => 'required|string|max:150',
            'subject_id' => 'nullable|exists:subjects,id',
            'teacher_id' => 'nullable|exists:teachers,id',
            'default_room_id' => 'nullable|exists:school_rooms,id',
            'membership_mode' => 'required|in:RELIGION,MANUAL,ALL',
            'religion_filter' => 'nullable|string|max:30',
            'description' => 'nullable|string|max:255',
            'class_ids' => 'required|array|min:1',
            'class_ids.*' => 'exists:classes,id',
            'student_ids' => 'nullable|array',
            'student_ids.*' => 'exists:students,id',
        ]);

        $religionFilter = $validated['religion_filter'] ?? null;
        if ($validated['membership_mode'] === 'RELIGION' && empty($religionFilter) && ! empty($validated['subject_id'])) {
            $subj = Subject::find($validated['subject_id']);
            $religionFilter = $subj?->resolved_religion_filter;
        }

        $group = DB::transaction(function () use ($validated, $religionFilter) {
            $created = TeachingGroup::create([
                'school_id' => $validated['school_id'],
                'academic_year_id' => $validated['academic_year_id'] ?? null,
                'code' => strtoupper(trim($validated['code'])),
                'name' => trim($validated['name']),
                'subject_id' => $validated['subject_id'] ?? null,
                'teacher_id' => $validated['teacher_id'] ?? null,
                'default_room_id' => $validated['default_room_id'] ?? null,
                'membership_mode' => $validated['membership_mode'],
                'religion_filter' => $validated['membership_mode'] === 'RELIGION' ? $religionFilter : null,
                'description' => $validated['description'] ?? null,
                'is_active' => true,
            ]);

            $created->classes()->sync(array_values(array_unique(array_map('intval', $validated['class_ids']))));

            if ($validated['membership_mode'] === 'MANUAL' && ! empty($validated['student_ids'])) {
                $created->manualStudents()->sync(array_values(array_unique(array_map('intval', $validated['student_ids']))));
            }

            return $created->load(['classes', 'subject', 'teacher', 'defaultRoom', 'manualStudents']);
        });

        return response()->json([
            'success' => true,
            'message' => 'Kelompok mapel / kelas gabungan berhasil dibuat.',
            'data' => $group,
            'participants_count' => $group->resolveStudents()->count(),
        ], 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $group = TeachingGroup::findOrFail($id);

        $validated = $request->validate([
            'code' => 'sometimes|required|string|max:30',
            'name' => 'sometimes|required|string|max:150',
            'subject_id' => 'nullable|exists:subjects,id',
            'teacher_id' => 'nullable|exists:teachers,id',
            'default_room_id' => 'nullable|exists:school_rooms,id',
            'membership_mode' => 'required|in:RELIGION,MANUAL,ALL',
            'religion_filter' => 'nullable|string|max:30',
            'description' => 'nullable|string|max:255',
            'class_ids' => 'required|array|min:1',
            'class_ids.*' => 'exists:classes,id',
            'student_ids' => 'nullable|array',
            'student_ids.*' => 'exists:students,id',
        ]);

        $religionFilter = $validated['religion_filter'] ?? null;
        if ($validated['membership_mode'] === 'RELIGION' && empty($religionFilter) && ! empty($validated['subject_id'])) {
            $subj = Subject::find($validated['subject_id']);
            $religionFilter = $subj?->resolved_religion_filter;
        }

        DB::transaction(function () use ($group, $validated, $religionFilter) {
            $group->update([
                'code' => isset($validated['code']) ? strtoupper(trim($validated['code'])) : $group->code,
                'name' => isset($validated['name']) ? trim($validated['name']) : $group->name,
                'subject_id' => $validated['subject_id'] ?? null,
                'teacher_id' => $validated['teacher_id'] ?? null,
                'default_room_id' => $validated['default_room_id'] ?? null,
                'membership_mode' => $validated['membership_mode'],
                'religion_filter' => $validated['membership_mode'] === 'RELIGION' ? $religionFilter : null,
                'description' => $validated['description'] ?? null,
            ]);

            $group->classes()->sync(array_values(array_unique(array_map('intval', $validated['class_ids']))));

            if ($validated['membership_mode'] === 'MANUAL') {
                $group->manualStudents()->sync(array_values(array_unique(array_map('intval', $validated['student_ids'] ?? []))));
            } else {
                $group->manualStudents()->detach();
            }
        });

        $group->load(['classes', 'subject', 'teacher', 'defaultRoom', 'manualStudents']);

        return response()->json([
            'success' => true,
            'message' => 'Kelompok mapel / kelas gabungan berhasil diperbarui.',
            'data' => $group,
            'participants_count' => $group->resolveStudents()->count(),
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $group = TeachingGroup::findOrFail($id);
        $group->delete();

        return response()->json([
            'success' => true,
            'message' => 'Kelompok mapel / kelas gabungan berhasil dihapus.',
        ]);
    }

    public function students(int $id): JsonResponse
    {
        $group = TeachingGroup::with(['classes', 'subject', 'teacher', 'defaultRoom'])->findOrFail($id);
        $students = $group->resolveStudents();

        return response()->json([
            'success' => true,
            'group' => [
                'id' => $group->id,
                'code' => $group->code,
                'name' => $group->name,
                'membership_mode' => $group->membership_mode,
                'religion_filter' => $group->religion_filter ?: $group->subject?->resolved_religion_filter,
                'classes' => $group->classes->pluck('name')->values(),
            ],
            'count' => $students->count(),
            'data' => $students->map(fn ($st) => [
                'id' => $st->id,
                'nis' => $st->nis,
                'nisn' => $st->nisn,
                'full_name' => $st->full_name,
                'gender' => $st->gender,
                'religion' => $st->religion,
                'class_id' => $st->class_id,
                'class_name' => $st->schoolClass?->name ?? '-',
            ])->values(),
        ]);
    }
}

