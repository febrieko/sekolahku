<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class TeacherController extends Controller
{
    /**
     * List teachers and education staff.
     */
    public function index(Request $request): JsonResponse
    {
        $schoolId = $request->query('school_id', $request->user()?->school_id ?? 1);
        $type = $request->query('employment_type');
        $status = $request->query('employment_status');
        $search = $request->query('search');

        $query = Teacher::with('homeroomClasses')
            ->where('school_id', $schoolId);

        if ($type) {
            $query->where('employment_type', $type);
        }

        if ($status) {
            $query->where('employment_status', $status);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                    ->orWhere('nip', 'like', "%{$search}%")
                    ->orWhere('nuptk', 'like', "%{$search}%");
            });
        }

        $teachers = $query->orderBy('full_name')->paginate(25);

        return response()->json([
            'success' => true,
            'data' => $teachers->items(),
            'meta' => [
                'current_page' => $teachers->currentPage(),
                'last_page' => $teachers->lastPage(),
                'total' => $teachers->total(),
            ],
        ]);
    }

    /**
     * Store new teacher complying with Dapodik.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'school_id' => 'required|exists:schools,id',
            'full_name' => 'required|string|max:255',
            'nip' => 'nullable|string|max:30|unique:teachers,nip',
            'nuptk' => 'nullable|string|max:30|unique:teachers,nuptk',
            'nik' => 'nullable|string|size:16',
            'title_prefix' => 'nullable|string|max:50',
            'title_suffix' => 'nullable|string|max:50',
            'gender' => 'required|in:L,P',
            'birth_place' => 'nullable|string|max:100',
            'birth_date' => 'nullable|date',
            'mother_maiden_name' => 'nullable|string|max:255',
            'religion' => 'nullable|in:Islam,Kristen,Katolik,Hindu,Buddha,Khonghucu',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:100',
            'employment_type' => 'required|in:GURU_KELAS,GURU_MAPEL,GURU_BK,TENAGA_ADMINISTRASI,LABORAN,PUSTAKAWAN,KEPALA_SEKOLAH',
            'employment_status' => 'required|in:PNS,CPNS,PPPK,GTY,GTT,HONOR_SEKOLAH',
            'sk_appointment_number' => 'nullable|string|max:100',
            'sk_appointment_date' => 'nullable|date',
            'is_certified' => 'boolean',
            'last_education' => 'nullable|in:D1,D2,D3,D4,S1,S2,S3',
            'major_study' => 'nullable|string|max:100',
            'create_user_account' => 'boolean',
            'password' => 'nullable|string|min:6',
        ]);

        $userId = null;
        if (! empty($validated['create_user_account']) && ! empty($validated['email'])) {
            $user = User::create([
                'school_id' => $validated['school_id'],
                'name' => $validated['full_name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password'] ?? 'password'),
                'role' => 'teacher',
                'phone' => $validated['phone'] ?? null,
                'is_active' => true,
            ]);
            $userId = $user->id;
        }

        $teacher = Teacher::create([
            ...$validated,
            'user_id' => $userId,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data Guru / Tenaga Kependidikan berhasil didaftarkan.',
            'data' => $teacher,
        ], 201);
    }

    /**
     * Detail teacher.
     */
    public function show(int $id): JsonResponse
    {
        $teacher = Teacher::with(['homeroomClasses', 'schedules.subject', 'schedules.schoolClass', 'schedules.lessonPeriod'])
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $teacher,
        ]);
    }
}
