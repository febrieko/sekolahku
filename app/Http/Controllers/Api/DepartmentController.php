<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    /**
     * List departments.
     */
    public function index(Request $request): JsonResponse
    {
        $schoolId = $request->query('school_id', $request->user()?->school_id ?? 1);

        $departments = Department::withCount('classes')
            ->where('school_id', $schoolId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $departments,
        ]);
    }

    /**
     * Store new department.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'school_id' => 'required|exists:schools,id',
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:20',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $department = Department::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Jurusan berhasil ditambahkan.',
            'data' => $department,
        ], 201);
    }
}
