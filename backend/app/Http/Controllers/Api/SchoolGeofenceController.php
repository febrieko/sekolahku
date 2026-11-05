<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SchoolGeofence;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SchoolGeofenceController extends Controller
{
    /**
     * Display a listing of geofences.
     */
    public function index(Request $request): JsonResponse
    {
        $schoolId = $request->query('school_id', $request->user()?->school_id ?? 1);

        $geofences = SchoolGeofence::where('school_id', $schoolId)
            ->orderByDesc('is_primary')
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $geofences,
        ]);
    }

    /**
     * Store a newly created geofence.
     */
    public function store(Request $request): JsonResponse
    {
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

        if (! empty($validated['is_primary'])) {
            SchoolGeofence::where('school_id', $validated['school_id'])
                ->update(['is_primary' => false]);
        }

        $geofence = SchoolGeofence::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Konfigurasi geofencing berhasil disimpan.',
            'data' => $geofence,
        ], 201);
    }

    /**
     * Display the specified geofence.
     */
    public function show(int $id): JsonResponse
    {
        $geofence = SchoolGeofence::findOrFail($id);

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

        $validated = $request->validate([
            'name' => 'sometimes|string|max:100',
            'latitude' => 'sometimes|numeric|between:-90,90',
            'longitude' => 'sometimes|numeric|between:-180,180',
            'radius_meters' => 'sometimes|integer|min:10|max:5000',
            'is_primary' => 'sometimes|boolean',
            'wifi_ssid' => 'nullable|string|max:100',
            'is_active' => 'sometimes|boolean',
        ]);

        if (! empty($validated['is_primary'])) {
            SchoolGeofence::where('school_id', $geofence->school_id)
                ->where('id', '!=', $geofence->id)
                ->update(['is_primary' => false]);
        }

        $geofence->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Konfigurasi geofencing berhasil diperbarui.',
            'data' => $geofence,
        ]);
    }

    /**
     * Remove the specified geofence.
     */
    public function destroy(int $id): JsonResponse
    {
        $geofence = SchoolGeofence::findOrFail($id);
        $geofence->delete();

        return response()->json([
            'success' => true,
            'message' => 'Konfigurasi geofencing berhasil dihapus.',
        ]);
    }
}
