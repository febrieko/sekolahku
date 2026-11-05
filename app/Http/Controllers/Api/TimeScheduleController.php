<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LessonPeriod;
use App\Models\TimeScheduleCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TimeScheduleController extends Controller
{
    /**
     * List all time schedule categories.
     */
    public function indexCategories(Request $request): JsonResponse
    {
        $schoolId = $request->query('school_id', $request->user()?->school_id ?? 1);

        $categories = TimeScheduleCategory::with('academicYear')
            ->withCount(['periods', 'schedules'])
            ->where('school_id', $schoolId)
            ->orderByDesc('is_default')
            ->orderByDesc('effective_start_date')
            ->orderBy('name')
            ->get()
            ->map(function (TimeScheduleCategory $cat) {
                $cat->setAttribute('validity_status', $cat->validity_status);

                return $cat;
            });

        return response()->json([
            'success' => true,
            'data' => $categories,
        ]);
    }

    /**
     * Store new time schedule category.
     */
    public function storeCategory(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'school_id' => 'required|exists:schools,id',
            'academic_year_id' => 'nullable|exists:academic_years,id',
            'academic_semester' => 'nullable|in:odd,even',
            'name' => 'required|string|max:100', // e.g. "Jam KBM Normal", "Jam Khusus Ramadhan"
            'code' => 'required|string|max:30', // NORMAL, RAMADHAN, UJIAN
            'description' => 'nullable|string',
            'is_default' => 'boolean',
            'is_active' => 'boolean',
            'effective_start_date' => 'nullable|date',
            'effective_end_date' => 'nullable|date|after_or_equal:effective_start_date',
            'clone_periods_from_category_id' => 'nullable|exists:time_schedule_categories,id',
        ]);

        $cloneFromId = $validated['clone_periods_from_category_id'] ?? null;
        unset($validated['clone_periods_from_category_id']);

        if (! empty($validated['is_default'])) {
            TimeScheduleCategory::where('school_id', $validated['school_id'])->update(['is_default' => false]);
        }

        $category = TimeScheduleCategory::create($validated);

        // Clone lesson periods from source category if requested
        if ($cloneFromId) {
            $sourcePeriods = LessonPeriod::where('category_id', $cloneFromId)->orderBy('day_of_week')->orderBy('start_time')->get();
            foreach ($sourcePeriods as $sp) {
                LessonPeriod::create([
                    'category_id' => $category->id,
                    'school_id' => $category->school_id,
                    'day_of_week' => $sp->day_of_week,
                    'name' => $sp->name,
                    'period_number' => $sp->period_number,
                    'start_time' => substr($sp->start_time, 0, 5),
                    'end_time' => substr($sp->end_time, 0, 5),
                    'is_break' => $sp->is_break,
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Kategori jadwal & masa berlaku berhasil ditambahkan.',
            'data' => $category->loadCount(['periods', 'schedules']),
        ], 201);
    }

    /**
     * Activate a schedule category as default.
     */
    public function activateCategory(int $id): JsonResponse
    {
        $category = TimeScheduleCategory::findOrFail($id);

        TimeScheduleCategory::where('school_id', $category->school_id)->where('id', '!=', $category->id)->update(['is_default' => false]);
        TimeScheduleCategory::where('id', $category->id)->update(['is_default' => true, 'is_active' => true]);
        $category->refresh();

        return response()->json([
            'success' => true,
            'message' => "Kategori waktu '{$category->name}' telah diaktifkan sebagai jadwal utama.",
            'data' => $category,
        ]);
    }

    /**
     * List lesson periods for a specific category.
     */
    public function periods(Request $request, int $categoryId): JsonResponse
    {
        $category = TimeScheduleCategory::findOrFail($categoryId);
        $dayOfWeek = $request->query('day_of_week');

        $query = LessonPeriod::where('category_id', $category->id);

        if ($dayOfWeek) {
            $query->where('day_of_week', (int) $dayOfWeek);
        }

        $periods = $query->orderBy('day_of_week')->orderBy('start_time')->get();

        return response()->json([
            'success' => true,
            'category' => $category,
            'data' => $periods,
        ]);
    }

    /**
     * Store a single lesson period.
     */
    public function storePeriod(Request $request, int $categoryId): JsonResponse
    {
        $category = TimeScheduleCategory::findOrFail($categoryId);

        $validated = $request->validate([
            'day_of_week' => 'required|integer|between:1,7',
            'name' => 'required|string|max:50',
            'period_number' => 'nullable|integer|min:1',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'is_break' => 'boolean',
        ]);

        $period = LessonPeriod::create([
            'category_id' => $category->id,
            'school_id' => $category->school_id,
            ...$validated,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Sesi jam pelajaran berhasil ditambahkan.',
            'data' => $period,
        ], 201);
    }

    /**
     * Delete a lesson period.
     */
    public function destroyPeriod(int $periodId): JsonResponse
    {
        $period = LessonPeriod::findOrFail($periodId);
        $period->delete();

        return response()->json([
            'success' => true,
            'message' => 'Sesi jam pelajaran berhasil dihapus.',
        ]);
    }

    /**
     * Get a specific time schedule category.
     */
    public function showCategory(int $id): JsonResponse
    {
        $category = TimeScheduleCategory::with(['periods' => function ($q) {
            $q->orderBy('day_of_week')->orderBy('start_time');
        }])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $category,
        ]);
    }

    /**
     * Update an existing time schedule category.
     */
    public function updateCategory(Request $request, int $id): JsonResponse
    {
        $category = TimeScheduleCategory::findOrFail($id);

        $validated = $request->validate([
            'academic_year_id' => 'nullable|exists:academic_years,id',
            'academic_semester' => 'nullable|in:odd,even',
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:30',
            'description' => 'nullable|string',
            'effective_start_date' => 'nullable|date',
            'effective_end_date' => 'nullable|date|after_or_equal:effective_start_date',
            'is_active' => 'boolean',
            'is_default' => 'boolean',
        ]);

        if (! empty($validated['is_default'])) {
            TimeScheduleCategory::where('school_id', $category->school_id)
                ->where('id', '!=', $category->id)
                ->update(['is_default' => false]);
        }

        $category->update($validated);

        return response()->json([
            'success' => true,
            'message' => "Kategori jadwal '{$category->name}' berhasil diperbarui.",
            'data' => $category->fresh(['academicYear']),
        ]);
    }

    /**
     * Clone lesson periods from another category into this category.
     */
    public function clonePeriods(Request $request, int $id): JsonResponse
    {
        $targetCategory = TimeScheduleCategory::findOrFail($id);

        $validated = $request->validate([
            'source_category_id' => 'required|exists:time_schedule_categories,id',
            'overwrite' => 'boolean',
        ]);

        if (! empty($validated['overwrite'])) {
            LessonPeriod::where('category_id', $targetCategory->id)->delete();
        }

        $sourcePeriods = LessonPeriod::where('category_id', $validated['source_category_id'])
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get();

        $copied = 0;
        foreach ($sourcePeriods as $sp) {
            $exists = LessonPeriod::where('category_id', $targetCategory->id)
                ->where('day_of_week', $sp->day_of_week)
                ->where('name', $sp->name)
                ->exists();

            if (! $exists) {
                LessonPeriod::create([
                    'category_id' => $targetCategory->id,
                    'school_id' => $targetCategory->school_id,
                    'day_of_week' => $sp->day_of_week,
                    'name' => $sp->name,
                    'period_number' => $sp->period_number,
                    'start_time' => substr($sp->start_time, 0, 5),
                    'end_time' => substr($sp->end_time, 0, 5),
                    'is_break' => $sp->is_break,
                ]);
                $copied++;
            }
        }

        return response()->json([
            'success' => true,
            'message' => "{$copied} sesi jam pelajaran berhasil disalin ke kategori '{$targetCategory->name}'.",
            'copied_count' => $copied,
        ]);
    }

    /**
     * Delete a time schedule category.
     */
    public function destroyCategory(int $id): JsonResponse
    {
        $category = TimeScheduleCategory::findOrFail($id);

        // Disallow deletion if this is the active default category
        if ($category->is_default) {
            return response()->json([
                'success' => false,
                'message' => 'Kategori jadwal utama (default) tidak dapat dihapus. Silakan jadikan kategori lain sebagai jadwal utama terlebih dahulu.',
            ], 422);
        }

        $name = $category->name;
        $category->delete();

        return response()->json([
            'success' => true,
            'message' => "Kategori jadwal '{$name}' dan seluruh sesi jam di dalamnya berhasil dihapus.",
        ]);
    }

    /**
     * Update a single lesson period (or all matching periods across days in the same category when apply_all_days is true).
     */
    public function updatePeriod(Request $request, int $periodId): JsonResponse
    {
        $period = LessonPeriod::findOrFail($periodId);
        $oldName = $period->name;
        $oldPeriodNumber = $period->period_number;

        $validated = $request->validate([
            'day_of_week' => 'nullable|integer|between:1,7',
            'name' => 'required|string|max:50',
            'period_number' => 'nullable|integer|min:1',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'is_break' => 'boolean',
            'apply_all_days' => 'nullable|boolean',
        ]);

        $applyAllDays = ! empty($validated['apply_all_days']);
        unset($validated['apply_all_days']);

        if (empty($validated['day_of_week'])) {
            $validated['day_of_week'] = $period->day_of_week;
        }

        if ($applyAllDays) {
            $updateData = $validated;
            unset($updateData['day_of_week']);

            LessonPeriod::where('category_id', $period->category_id)
                ->where(function ($q) use ($oldPeriodNumber, $oldName) {
                    if ($oldPeriodNumber !== null) {
                        $q->where('period_number', $oldPeriodNumber);
                    } else {
                        $q->where('name', $oldName);
                    }
                })
                ->update($updateData);

            $period->refresh();
        } else {
            $period->update($validated);
        }

        return response()->json([
            'success' => true,
            'message' => 'Sesi jam pelajaran berhasil diperbarui.',
            'data' => $period,
        ]);
    }
}
