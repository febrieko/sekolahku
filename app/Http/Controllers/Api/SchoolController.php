<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class SchoolController extends Controller
{
    /**
     * Get list of all registered schools.
     */
    public function index(Request $request): JsonResponse
    {
        /** @var User|null $user */
        $user = Auth::user() ?? $request->user();

        $query = School::withCount(['classes', 'students', 'users']);

        // Jika bukan Super Admin (mis: Admin Sekolah / Guru), batasi hanya melihat sekolah tempatnya dienrol
        if ($user && ! $user->isSuperAdmin()) {
            $query->where('id', $user->school_id ?? 1);
        }

        $schools = $query->orderBy('id')->get();

        return response()->json([
            'success' => true,
            'data' => $schools,
            'active_school_id' => session('active_school_id', $schools->first()?->id ?? 1),
        ]);
    }

    /**
     * Get single school details.
     */
    public function show($id): JsonResponse
    {
        $school = School::withCount(['classes', 'students', 'users'])->find($id);

        if (!$school) {
            return response()->json([
                'success' => false,
                'message' => 'Satuan pendidikan tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $school,
        ]);
    }

    /**
     * Store a new school with Dapodik standard fields (Super Admin only).
     */
    public function store(Request $request): JsonResponse
    {
        /** @var User|null $user */
        $user = Auth::user() ?? $request->user();

        // Hanya Super Admin yang berwenang menambah satuan pendidikan baru
        if (! $user || ! $user->isSuperAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak: Hanya Super Administrator yang berwenang menambah Satuan Pendidikan baru.',
            ], 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'npsn' => 'required|string|size:8|unique:schools,npsn',
            'level' => 'required|in:SD,SMP,SMA,SMK',
            'tenant_id' => 'nullable|string|max:50|unique:schools,tenant_id',
            'school_status' => 'required|in:Negeri,Swasta',
            'accreditation' => 'nullable|in:A,B,C,Belum Terakreditasi',
            'establishment_decree' => 'nullable|string|max:100',
            'establishment_date' => 'nullable|date',
            'operational_license_decree' => 'nullable|string|max:100',
            'operational_license_date' => 'nullable|date',
            'tax_number' => 'nullable|string|max:50',
            'postal_code' => 'nullable|string|max:10',
            'rt_rw' => 'nullable|string|max:20',
            'sub_village' => 'nullable|string|max:100',
            'village' => 'nullable|string|max:100',
            'subdistrict' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'province' => 'nullable|string|max:100',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'address' => 'required|string',
            'phone' => 'nullable|string|max:50',
            'fax' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:100',
            'website' => 'nullable|url|max:150',
            'principal_name' => 'nullable|string|max:150',
            'principal_nip' => 'nullable|string|max:30',
            'subscription' => 'nullable|in:starter,professional,enterprise',
        ]);

        if (empty($validated['tenant_id'])) {
            $validated['tenant_id'] = Str::slug($validated['name']);
        }
        $validated['subscription'] = $validated['subscription'] ?? 'starter';
        $validated['is_active'] = true;

        $school = School::create($validated);

        // Alihkan sesi aktif ke sekolah baru ini
        session(['active_school_id' => $school->id]);

        return response()->json([
            'success' => true,
            'message' => 'Satuan Pendidikan (Sekolah) Dapodik berhasil didaftarkan.',
            'data' => $school,
        ], 201);
    }

    /**
     * Update school details.
     * Super Admin can update any school; School Admin can only update their enrolled school.
     */
    public function update(Request $request, $id): JsonResponse
    {
        /** @var User|null $user */
        $user = Auth::user() ?? $request->user();

        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'Silakan masuk ke akun Anda terlebih dahulu.',
            ], 401);
        }

        $school = School::find($id);

        if (!$school) {
            return response()->json([
                'success' => false,
                'message' => 'Satuan pendidikan tidak ditemukan.',
            ], 404);
        }

        // Jika bukan Super Admin (Admin Sekolah), pastikan hanya bisa mengedit sekolah tempat ia dienrol
        if (! $user->isSuperAdmin()) {
            if (! $user->isSchoolAdmin() || (int) $user->school_id !== (int) $school->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Akses ditolak: Anda hanya berwenang memperbarui data Satuan Pendidikan yang telah dienrol.',
                ], 403);
            }
        }

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'npsn' => 'sometimes|required|string|size:8|unique:schools,npsn,' . $id,
            'level' => 'sometimes|required|in:SD,SMP,SMA,SMK',
            'school_status' => 'nullable|in:Negeri,Swasta',
            'accreditation' => 'nullable|in:A,B,C,Belum Terakreditasi',
            'establishment_decree' => 'nullable|string|max:100',
            'establishment_date' => 'nullable|date',
            'operational_license_decree' => 'nullable|string|max:100',
            'operational_license_date' => 'nullable|date',
            'tax_number' => 'nullable|string|max:50',
            'postal_code' => 'nullable|string|max:10',
            'rt_rw' => 'nullable|string|max:20',
            'sub_village' => 'nullable|string|max:100',
            'village' => 'nullable|string|max:100',
            'subdistrict' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'province' => 'nullable|string|max:100',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'address' => 'sometimes|required|string',
            'phone' => 'nullable|string|max:50',
            'fax' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:100',
            'website' => 'nullable|url|max:150',
            'principal_name' => 'nullable|string|max:150',
            'principal_nip' => 'nullable|string|max:30',
        ]);

        $school->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Data Dapodik Satuan Pendidikan berhasil diperbarui.',
            'data' => $school,
        ]);
    }

    /**
     * Delete a school (Super Admin only).
     */
    public function destroy(Request $request, $id): JsonResponse
    {
        /** @var User|null $user */
        $user = Auth::user() ?? $request->user();

        // Hanya Super Admin yang berwenang menghapus satuan pendidikan
        if (! $user || ! $user->isSuperAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak: Hanya Super Administrator yang berwenang menghapus Satuan Pendidikan.',
            ], 403);
        }

        $school = School::find($id);

        if (!$school) {
            return response()->json([
                'success' => false,
                'message' => 'Satuan pendidikan tidak ditemukan.',
            ], 404);
        }

        // Cegah penghapusan jika ini adalah satu-satunya sekolah di sistem
        if (School::count() <= 1) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus: Minimal harus ada 1 Satuan Pendidikan di dalam sistem.',
            ], 422);
        }

        $schoolName = $school->name;
        $school->delete();

        // Jika session aktif mengarah ke sekolah yang dihapus, switch ke sekolah pertama yang tersisa
        if (session('active_school_id') == $id) {
            $fallback = School::first();
            session(['active_school_id' => $fallback?->id]);
        }

        return response()->json([
            'success' => true,
            'message' => "Satuan Pendidikan \"{$schoolName}\" berhasil dihapus dari sistem.",
        ]);
    }

    /**
     * Switch active school tenant in session (Super Admin only).
     */
    public function switchActive(Request $request): JsonResponse
    {
        /** @var User|null $user */
        $user = Auth::user() ?? $request->user();

        if ($user && ! $user->isSuperAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak: Anda hanya berwenang mengelola data pada satuan pendidikan tempat Anda dienrol.',
            ], 403);
        }

        $schoolId = $request->input('school_id');
        $school = School::find($schoolId);

        if (!$school) {
            return response()->json([
                'success' => false,
                'message' => 'Satuan pendidikan tidak ditemukan.',
            ], 404);
        }

        session(['active_school_id' => $school->id]);

        return response()->json([
            'success' => true,
            'message' => 'Berhasil berpindah ke ' . $school->name,
            'active_school' => $school,
        ]);
    }

    /**
     * Dapatkan pengaturan rentang tanggal pemutakhiran biodata siswa.
     */
    public function getProfileEditWindow($id): JsonResponse
    {
        $school = School::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => [
                'enabled' => $school->student_profile_edit_enabled,
                'start_date' => $school->student_profile_edit_start?->toDateString(),
                'end_date' => $school->student_profile_edit_end?->toDateString(),
                'is_currently_open' => $school->isStudentProfileEditOpen(),
            ],
        ]);
    }

    /**
     * Atur rentang tanggal pemutakhiran biodata siswa oleh admin.
     */
    public function updateProfileEditWindow(Request $request, $id): JsonResponse
    {
        $school = School::findOrFail($id);

        $validated = $request->validate([
            'enabled' => 'required|boolean',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ], [
            'end_date.after_or_equal' => 'Tanggal berakhir harus sama atau setelah tanggal mulai.',
        ]);

        $school->update([
            'student_profile_edit_enabled' => $validated['enabled'],
            'student_profile_edit_start' => $validated['start_date'] ?? null,
            'student_profile_edit_end' => $validated['end_date'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pengaturan jadwal pemutakhiran profil siswa berhasil disimpan.',
            'data' => [
                'enabled' => $school->student_profile_edit_enabled,
                'start_date' => $school->student_profile_edit_start?->toDateString(),
                'end_date' => $school->student_profile_edit_end?->toDateString(),
                'is_currently_open' => $school->isStudentProfileEditOpen(),
            ],
        ]);
    }
}
