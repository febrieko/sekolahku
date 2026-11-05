<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use App\Models\SchoolRoom;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SchoolRoomController extends Controller
{
    /**
     * List school rooms with schedule and class counts.
     */
    public function index(Request $request): JsonResponse
    {
        $schoolId = $request->query('school_id', $request->user()?->school_id ?? 1);
        $type = $request->query('type');

        $query = SchoolRoom::with('classes')
            ->withCount('schedules')
            ->where('school_id', $schoolId)
            ->orderBy('type')
            ->orderBy('code');

        if ($type) {
            $query->where('type', $type);
        }

        return response()->json([
            'success' => true,
            'data' => $query->get(),
        ]);
    }

    /**
     * Store a new school room.
     */
    public function store(Request $request): JsonResponse
    {
        $schoolId = $request->input('school_id', $request->user()?->school_id ?? 1);

        $validated = $request->validate([
            'school_id' => 'required|exists:schools,id',
            'code' => [
                'required',
                'string',
                'max:30',
                Rule::unique('school_rooms', 'code')->where('school_id', $schoolId),
            ],
            'name' => 'required|string|max:100',
            'type' => 'required|in:CLASSROOM,LABORATORY,WORKSHOP,SPORTS,HALL,OTHER',
            'building' => 'nullable|string|max:60',
            'floor' => 'nullable|string|max:20',
            'capacity' => 'nullable|integer|min:1|max:1000',
            'description' => 'nullable|string|max:500',
            'is_active' => 'boolean',
            'default_for_class_id' => 'nullable|exists:classes,id',
        ]);

        $defaultForClassId = $validated['default_for_class_id'] ?? null;
        unset($validated['default_for_class_id']);

        $validated['capacity'] = $validated['capacity'] ?? 36;
        $validated['is_active'] = $validated['is_active'] ?? true;

        $room = SchoolRoom::create($validated);

        if ($defaultForClassId) {
            SchoolClass::where('id', $defaultForClassId)
                ->where('school_id', $room->school_id)
                ->update(['default_room_id' => $room->id]);
        }

        return response()->json([
            'success' => true,
            'message' => "Ruang '{$room->name}' berhasil ditambahkan.",
            'data' => $room->load('classes')->loadCount('schedules'),
        ], 201);
    }

    /**
     * Update an existing school room.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $room = SchoolRoom::findOrFail($id);

        $validated = $request->validate([
            'code' => [
                'required',
                'string',
                'max:30',
                Rule::unique('school_rooms', 'code')
                    ->where('school_id', $room->school_id)
                    ->ignore($room->id),
            ],
            'name' => 'required|string|max:100',
            'type' => 'required|in:CLASSROOM,LABORATORY,WORKSHOP,SPORTS,HALL,OTHER',
            'building' => 'nullable|string|max:60',
            'floor' => 'nullable|string|max:20',
            'capacity' => 'nullable|integer|min:1|max:1000',
            'description' => 'nullable|string|max:500',
            'is_active' => 'boolean',
            'default_for_class_id' => 'nullable|exists:classes,id',
        ]);

        $defaultForClassId = $validated['default_for_class_id'] ?? null;
        $hasDefaultClassField = $request->has('default_for_class_id');
        unset($validated['default_for_class_id']);

        $room->update($validated);

        if ($hasDefaultClassField) {
            SchoolClass::where('default_room_id', $room->id)->update(['default_room_id' => null]);
            if ($defaultForClassId) {
                SchoolClass::where('id', $defaultForClassId)
                    ->where('school_id', $room->school_id)
                    ->update(['default_room_id' => $room->id]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Ruang '{$room->name}' berhasil diperbarui.",
            'data' => $room->fresh(['classes'])->loadCount('schedules'),
        ]);
    }

    /**
     * Delete a school room.
     */
    public function destroy(int $id): JsonResponse
    {
        $room = SchoolRoom::findOrFail($id);
        $name = $room->name;
        $room->delete();

        return response()->json([
            'success' => true,
            'message' => "Ruang '{$name}' berhasil dihapus.",
        ]);
    }
}

