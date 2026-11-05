<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ParentGuardian;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ParentController extends Controller
{
    /**
     * List parents with children.
     */
    public function index(Request $request): JsonResponse
    {
        $schoolId = $request->query('school_id', $request->user()?->school_id ?? 1);
        $search = $request->query('search');

        $query = ParentGuardian::with('students.schoolClass')
            ->where('school_id', $schoolId);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('father_name', 'like', "%{$search}%")
                    ->orWhere('mother_name', 'like', "%{$search}%")
                    ->orWhere('primary_phone', 'like', "%{$search}%")
                    ->orWhere('no_kk', 'like', "%{$search}%");
            });
        }

        $parents = $query->orderBy('id', 'desc')->paginate(25);

        return response()->json([
            'success' => true,
            'data' => $parents->items(),
            'meta' => [
                'current_page' => $parents->currentPage(),
                'last_page' => $parents->lastPage(),
                'total' => $parents->total(),
            ],
        ]);
    }

    /**
     * Store new parent complying with Dapodik.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'school_id' => 'required|exists:schools,id',
            'no_kk' => 'nullable|string|size:16',
            'father_name' => 'nullable|string|max:255',
            'father_nik' => 'nullable|string|size:16',
            'father_phone' => 'nullable|string|max:30',
            'father_occupation' => 'nullable|string|max:100',
            'father_education' => 'nullable|string|max:50',
            'mother_name' => 'nullable|string|max:255',
            'mother_nik' => 'nullable|string|size:16',
            'mother_phone' => 'nullable|string|max:30',
            'primary_phone' => 'required|string|max:30',
            'address' => 'nullable|string',
            'student_ids' => 'nullable|array',
            'student_ids.*' => 'exists:students,id',
            'email' => 'nullable|email|max:100',
            'create_user_account' => 'boolean',
            'password' => 'nullable|string|min:6',
        ]);

        $userId = null;
        if (! empty($validated['create_user_account']) && ! empty($validated['email'])) {
            $user = User::create([
                'school_id' => $validated['school_id'],
                'name' => $validated['father_name'] ?? $validated['mother_name'] ?? 'Orang Tua Siswa',
                'email' => $validated['email'],
                'password' => Hash::make($validated['password'] ?? 'password'),
                'role' => 'parent',
                'phone' => $validated['primary_phone'],
                'is_active' => true,
            ]);
            $userId = $user->id;
        }

        $parent = ParentGuardian::create([
            ...$validated,
            'user_id' => $userId,
        ]);

        // Attach children (multi-student)
        if (! empty($validated['student_ids'])) {
            $parent->students()->syncWithoutDetaching($validated['student_ids']);
        }

        return response()->json([
            'success' => true,
            'message' => 'Data Orang Tua / Wali berhasil disimpan.',
            'data' => $parent->load('students.schoolClass'),
        ], 201);
    }

    /**
     * Detail parent with children.
     */
    public function show(int $id): JsonResponse
    {
        $parent = ParentGuardian::with(['students.schoolClass', 'user'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $parent,
        ]);
    }

    /**
     * Connect an additional student child to this parent.
     */
    public function attachStudent(Request $request, int $id): JsonResponse
    {
        $parent = ParentGuardian::findOrFail($id);

        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'relationship' => 'nullable|in:father,mother,guardian',
        ]);

        $parent->students()->syncWithoutDetaching([
            $validated['student_id'] => ['relationship' => $validated['relationship'] ?? 'father']
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Siswa berhasil dihubungkan ke data orang tua.',
            'data' => $parent->load('students.schoolClass'),
        ]);
    }

    /**
     * Disconnect a student child from this parent.
     */
    public function detachStudent(int $id, int $studentId): JsonResponse
    {
        $parent = ParentGuardian::findOrFail($id);
        $parent->students()->detach($studentId);

        return response()->json([
            'success' => true,
            'message' => 'Hubungan siswa dengan orang tua berhasil dilepas.',
        ]);
    }
}
