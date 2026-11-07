<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SchoolGeofence;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SchoolGeofenceController extends Controller
{
    /**
     * Resolve authorized school_id for geofence operations.
     * Super Admin can query any school; School Admin is strictly locked to their enrolled school_id.
     */
    protected function getAuthorizedSchoolId(Request $request): int
    {
        /** @var User|null $user */
        $user = Auth::user() ?? $request->user();

        if ($user && ! $user->isSuperAdmin()) {
            return (int) ($user->school_id ?? 1);
        }

        return (int) ($request->query('school_id') ?? session('active_school_id') ?? ($user?->school_id ?? 1));
    }

    /**
     * Validate that the user is authorized to manage geofencing (Super Admin or School Admin).
     */
    protected function authorizeGeofenceManager(?User $user): void
    {
        if (! $user) {
            abort(response()->json([
                'success' => false,
                'message' => 'Silakan masuk ke akun Anda terlebih dahulu untuk mengelola geofencing.',
            ], 401));
        }

        if (! $user->isSuperAdmin() && ! $user->isSchoolAdmin()) {
            abort(response()->json([
                'success' => false,
                'message' => 'Akses ditolak: Hanya Super Admin dan Admin Sekolah yang memiliki wewenang mengelola konfigurasi geofencing.',
            ], 403));
        }

        if ($user->isSchoolAdmin() && empty($user->school_id)) {
            abort(response()->json([
                'success' => false,
                'message' => 'Akses ditolak: Akun Admin Sekolah Anda belum dienrol ke Satuan Pendidikan manapun.',
            ], 403));
        }
    }

    /**
     * Validate that the user can mutate a specific geofence record.
     */
    protected function authorizeGeofenceMutation(?User $user, SchoolGeofence $geofence, string $action = 'mengubah'): void
    {
        $this->authorizeGeofenceManager($user);

        if (! $user->isSuperAdmin()) {
            if ((int) $geofence->school_id !== (int) $user->school_id) {
                abort(response()->json([
                    'success' => false,
                    'message' => "Akses ditolak: Anda hanya berwenang {$action} titik geofence pada satuan pendidikan yang telah dienrol.",
                ], 403));
            }
        }
    }

    /**
     * Display a listing of geofences.
     */
    public function index(Request $request): JsonResponse
    {
        $schoolId = $this->getAuthorizedSchoolId($request);

        $geofences = SchoolGeofence::where('school_id', $schoolId)
            ->where('is_active', true)
            ->orderByDesc('is_primary')
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'school_id' => $schoolId,
            'data' => $geofences,
        ]);
    }

    /**
     * Store a newly created geofence.
     */
    public function store(Request $request): JsonResponse
    {
        /** @var User|null $user */
        $user = Auth::user() ?? $request->user();
        $this->authorizeGeofenceManager($user);

        $validated = $request->validate([
            'school_id' => 'required|exists:schools,id',
            'name' => 'required|string|max:100',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'radius_meters' => 'required|integer|min:10|max:5000',
            'is_primary' => 'boolean',
            'wifi_ssid' => 'nullable|string|max:100',
            'is_active' => 'boolean',
        ]);

        // Jika bukan Super Admin (mis: Admin Sekolah), kunci mutlak school_id ke sekolah yang dienrol
        if (! $user->isSuperAdmin()) {
            $validated['school_id'] = (int) $user->school_id;
        }

        if (! empty($validated['is_primary'])) {
            SchoolGeofence::where('school_id', $validated['school_id'])
                ->update(['is_primary' => false]);
        }

        $geofence = SchoolGeofence::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Konfigurasi titik geofencing berhasil disimpan.',
            'data' => $geofence,
        ], 201);
    }

    /**
     * Display the specified geofence.
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $geofence = SchoolGeofence::findOrFail($id);

        /** @var User|null $user */
        $user = Auth::user() ?? $request->user();
        if ($user && ! $user->isSuperAdmin()) {
            if ((int) $geofence->school_id !== (int) $user->school_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Akses ditolak: Anda tidak memiliki akses ke data geofence satuan pendidikan lain.',
                ], 403);
            }
        }

        return response()->json([
            'success' => true,
            'data' => $geofence,
        ]);
    }

    /**
     * Update the specified geofence.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $geofence = SchoolGeofence::findOrFail($id);

        /** @var User|null $user */
        $user = Auth::user() ?? $request->user();
        $this->authorizeGeofenceMutation($user, $geofence, 'mengubah');

        $validated = $request->validate([
            'name' => 'sometimes|string|max:100',
            'latitude' => 'sometimes|numeric|between:-90,90',
            'longitude' => 'sometimes|numeric|between:-180,180',
            'radius_meters' => 'sometimes|integer|min:10|max:5000',
            'is_primary' => 'sometimes|boolean',
            'wifi_ssid' => 'nullable|string|max:100',
            'is_active' => 'sometimes|boolean',
        ]);

        // Lindungi pemindahan sekolah: school_id tidak boleh diubah
        unset($validated['school_id']);

        if (! empty($validated['is_primary'])) {
            SchoolGeofence::where('school_id', $geofence->school_id)
                ->where('id', '!=', $geofence->id)
                ->update(['is_primary' => false]);
        }

        $geofence->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Konfigurasi titik geofencing berhasil diperbarui.',
            'data' => $geofence,
        ]);
    }

    /**
     * Remove the specified geofence.
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        $geofence = SchoolGeofence::findOrFail($id);

        /** @var User|null $user */
        $user = Auth::user() ?? $request->user();
        $this->authorizeGeofenceMutation($user, $geofence, 'menghapus');

        $geofence->delete();

        return response()->json([
            'success' => true,
            'message' => 'Konfigurasi titik geofencing berhasil dihapus.',
        ]);
    }
}
