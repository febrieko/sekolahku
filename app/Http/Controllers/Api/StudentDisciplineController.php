<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\DisciplineAmnestyRule;
use App\Models\DisciplineCategory;
use App\Models\DisciplineItem;
use App\Models\DisciplinePolicy;
use App\Models\DisciplineThreshold;
use App\Models\Student;
use App\Models\StudentDisciplineRecord;
use App\Services\DisciplineBootstrapService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class StudentDisciplineController extends Controller
{
    /**
     * Get full discipline configuration & items for a school.
     */
    public function index(Request $request): JsonResponse
    {
        $schoolId = (int) ($request->query('school_id') ?: ($request->user()?->school_id ?? 1));
        $policy = DisciplineBootstrapService::ensureDefaultsForSchool($schoolId);

        $categories = DisciplineCategory::with('items')
            ->where('school_id', $schoolId)
            ->orderBy('sort_order')
            ->orderBy('code')
            ->get();

        $thresholds = DisciplineThreshold::where('school_id', $schoolId)
            ->orderBy('min_points')
            ->get();

        $amnestyRules = DisciplineAmnestyRule::where('school_id', $schoolId)
            ->orderBy('code')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'policy' => $policy,
                'categories' => $categories,
                'thresholds' => $thresholds,
                'amnesty_rules' => $amnestyRules,
            ],
        ]);
    }

    // =========================================================================
    // 1. MASTER KATEGORI PELANGGARAN
    // =========================================================================

    public function storeCategory(Request $request): JsonResponse
    {
        $schoolId = (int) $request->input('school_id', $request->user()?->school_id ?? 1);

        $validated = $request->validate([
            'school_id' => ['required', 'integer', 'exists:schools,id'],
            'code' => [
                'required',
                'string',
                'max:30',
                Rule::unique('discipline_categories', 'code')->where('school_id', $schoolId),
            ],
            'name' => ['required', 'string', 'max:150'],
            'severity' => ['required', Rule::in(['RINGAN', 'SEDANG', 'BERAT'])],
            'color' => ['nullable', 'string', 'max:25'],
            'description' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:1'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $category = DisciplineCategory::create([
            'school_id' => $validated['school_id'],
            'code' => strtoupper(trim($validated['code'])),
            'name' => $validated['name'],
            'severity' => $validated['severity'],
            'color' => $validated['color'] ?? '#f59e0b',
            'description' => $validated['description'] ?? null,
            'sort_order' => $validated['sort_order'] ?? (DisciplineCategory::where('school_id', $schoolId)->max('sort_order') + 1),
            'is_active' => $validated['is_active'] ?? true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Master kategori pelanggaran berhasil ditambahkan.',
            'data' => $category->load('items'),
        ], 201);
    }

    public function updateCategory(Request $request, int $id): JsonResponse
    {
        $category = DisciplineCategory::findOrFail($id);

        $validated = $request->validate([
            'code' => [
                'sometimes',
                'required',
                'string',
                'max:30',
                Rule::unique('discipline_categories', 'code')
                    ->where('school_id', $category->school_id)
                    ->ignore($category->id),
            ],
            'name' => ['sometimes', 'required', 'string', 'max:150'],
            'severity' => ['sometimes', 'required', Rule::in(['RINGAN', 'SEDANG', 'BERAT'])],
            'color' => ['nullable', 'string', 'max:25'],
            'description' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:1'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        if (isset($validated['code'])) {
            $validated['code'] = strtoupper(trim($validated['code']));
        }

        $category->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Kategori pelanggaran berhasil diperbarui.',
            'data' => $category->fresh('items'),
        ]);
    }

    public function destroyCategory(int $id): JsonResponse
    {
        $category = DisciplineCategory::withCount('items')->findOrFail($id);
        $category->delete();

        return response()->json([
            'success' => true,
            'message' => 'Kategori pelanggaran berhasil dihapus.',
        ]);
    }

    // =========================================================================
    // 2. MASTER ITEM PELANGGARAN SISWA & BESAR POIN
    // =========================================================================

    public function storeItem(Request $request): JsonResponse
    {
        $category = DisciplineCategory::findOrFail($request->input('category_id'));
        $schoolId = $category->school_id;

        $validated = $request->validate([
            'category_id' => ['required', 'integer', 'exists:discipline_categories,id'],
            'code' => [
                'required',
                'string',
                'max:30',
                Rule::unique('discipline_items', 'code')->where('school_id', $schoolId),
            ],
            'name' => ['required', 'string', 'max:255'],
            'points' => ['required', 'integer', 'min:1', 'max:500'],
            'default_sanction' => ['nullable', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $item = DisciplineItem::create([
            'school_id' => $schoolId,
            'category_id' => $category->id,
            'code' => strtoupper(trim($validated['code'])),
            'name' => $validated['name'],
            'points' => $validated['points'],
            'default_sanction' => $validated['default_sanction'] ?? null,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Item pelanggaran siswa berhasil ditambahkan.',
            'data' => $item->load('category'),
        ], 201);
    }

    public function updateItem(Request $request, int $id): JsonResponse
    {
        $item = DisciplineItem::findOrFail($id);

        $validated = $request->validate([
            'category_id' => ['sometimes', 'required', 'integer', 'exists:discipline_categories,id'],
            'code' => [
                'sometimes',
                'required',
                'string',
                'max:30',
                Rule::unique('discipline_items', 'code')
                    ->where('school_id', $item->school_id)
                    ->ignore($item->id),
            ],
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'points' => ['sometimes', 'required', 'integer', 'min:1', 'max:500'],
            'default_sanction' => ['nullable', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        if (isset($validated['code'])) {
            $validated['code'] = strtoupper(trim($validated['code']));
        }

        $item->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Item pelanggaran & besar poin berhasil diperbarui.',
            'data' => $item->fresh('category'),
        ]);
    }

    public function destroyItem(int $id): JsonResponse
    {
        $item = DisciplineItem::findOrFail($id);
        $item->delete();

        return response()->json([
            'success' => true,
            'message' => 'Item pelanggaran berhasil dihapus.',
        ]);
    }

    // =========================================================================
    // 3. PENGATURAN AMBANG BATAS PELANGGARAN & SANKSI (WAKA KESISWAAN)
    // =========================================================================

    public function storeThreshold(Request $request): JsonResponse
    {
        $schoolId = (int) $request->input('school_id', $request->user()?->school_id ?? 1);

        $validated = $request->validate([
            'school_id' => ['required', 'integer', 'exists:schools,id'],
            'level_code' => [
                'required',
                'string',
                'max:30',
                Rule::unique('discipline_thresholds', 'level_code')->where('school_id', $schoolId),
            ],
            'name' => ['required', 'string', 'max:150'],
            'min_points' => ['required', 'integer', 'min:1'],
            'max_points' => ['nullable', 'integer', 'gte:min_points'],
            'sanction_action' => ['nullable', 'string'],
            'require_parent_call' => ['nullable', 'boolean'],
            'require_bk_counseling' => ['nullable', 'boolean'],
            'color' => ['nullable', 'string', 'max:25'],
            'sort_order' => ['nullable', 'integer', 'min:1'],
        ]);

        $threshold = DisciplineThreshold::create([
            'school_id' => $schoolId,
            'level_code' => strtoupper(trim($validated['level_code'])),
            'name' => $validated['name'],
            'min_points' => $validated['min_points'],
            'max_points' => $validated['max_points'] ?? null,
            'sanction_action' => $validated['sanction_action'] ?? null,
            'require_parent_call' => (bool) ($validated['require_parent_call'] ?? false),
            'require_bk_counseling' => (bool) ($validated['require_bk_counseling'] ?? false),
            'color' => $validated['color'] ?? '#f59e0b',
            'sort_order' => $validated['sort_order'] ?? (DisciplineThreshold::where('school_id', $schoolId)->max('sort_order') + 1),
            'is_active' => true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Ambang batas sanksi pelanggaran berhasil disimpan.',
            'data' => $threshold,
        ], 201);
    }

    public function updateThreshold(Request $request, int $id): JsonResponse
    {
        $threshold = DisciplineThreshold::findOrFail($id);

        $validated = $request->validate([
            'level_code' => [
                'sometimes',
                'required',
                'string',
                'max:30',
                Rule::unique('discipline_thresholds', 'level_code')
                    ->where('school_id', $threshold->school_id)
                    ->ignore($threshold->id),
            ],
            'name' => ['sometimes', 'required', 'string', 'max:150'],
            'min_points' => ['sometimes', 'required', 'integer', 'min:1'],
            'max_points' => ['nullable', 'integer'],
            'sanction_action' => ['nullable', 'string'],
            'require_parent_call' => ['nullable', 'boolean'],
            'require_bk_counseling' => ['nullable', 'boolean'],
            'color' => ['nullable', 'string', 'max:25'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        if (isset($validated['level_code'])) {
            $validated['level_code'] = strtoupper(trim($validated['level_code']));
        }

        $threshold->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Ambang batas pelanggaran berhasil diperbarui oleh Waka Kesiswaan.',
            'data' => $threshold->fresh(),
        ]);
    }

    public function destroyThreshold(int $id): JsonResponse
    {
        $threshold = DisciplineThreshold::findOrFail($id);
        $threshold->delete();

        return response()->json([
            'success' => true,
            'message' => 'Ambang batas pelanggaran berhasil dihapus.',
        ]);
    }

    // =========================================================================
    // 4. PENGATURAN ATURAN PEMUTIHAN & KEBIJAKAN KESISWAAN (WAKA KESISWAAN)
    // =========================================================================

    public function updatePolicy(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'school_id' => ['required', 'integer', 'exists:schools,id'],
            'max_violation_points' => ['required', 'integer', 'min:10', 'max:1000'],
            'reset_period' => ['required', Rule::in(['SEMESTER', 'ACADEMIC_YEAR', 'NEVER'])],
            'amnesty_enabled' => ['required', 'boolean'],
            'max_amnesty_points_per_semester' => ['required', 'integer', 'min:0', 'max:500'],
            'min_clean_days_before_amnesty' => ['required', 'integer', 'min:0', 'max:365'],
            'auto_refer_bk_threshold' => ['required', 'integer', 'min:1', 'max:1000'],
            'allow_amnesty_for_heavy_violations' => ['required', 'boolean'],
        ]);

        $policy = DisciplinePolicy::updateOrCreate(
            ['school_id' => $validated['school_id']],
            array_merge($validated, [
                'updated_by' => $request->user()?->id,
            ])
        );

        return response()->json([
            'success' => true,
            'message' => 'Pengaturan ambang batas & kebijakan pemutihan Kesiswaan berhasil disimpan.',
            'data' => $policy,
        ]);
    }

    public function storeAmnestyRule(Request $request): JsonResponse
    {
        $schoolId = (int) $request->input('school_id', $request->user()?->school_id ?? 1);

        $validated = $request->validate([
            'school_id' => ['required', 'integer', 'exists:schools,id'],
            'code' => [
                'required',
                'string',
                'max:30',
                Rule::unique('discipline_amnesty_rules', 'code')->where('school_id', $schoolId),
            ],
            'name' => ['required', 'string', 'max:200'],
            'credit_points' => ['required', 'integer', 'min:1', 'max:200'],
            'min_violation_points_required' => ['nullable', 'integer', 'min:0'],
            'clean_days_required' => ['nullable', 'integer', 'min:0'],
            'max_claims_per_semester' => ['nullable', 'integer', 'min:1'],
            'description' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $rule = DisciplineAmnestyRule::create([
            'school_id' => $schoolId,
            'code' => strtoupper(trim($validated['code'])),
            'name' => $validated['name'],
            'credit_points' => $validated['credit_points'],
            'min_violation_points_required' => $validated['min_violation_points_required'] ?? 0,
            'clean_days_required' => $validated['clean_days_required'] ?? 0,
            'max_claims_per_semester' => $validated['max_claims_per_semester'] ?? null,
            'description' => $validated['description'] ?? null,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Program pemutihan poin berhasil ditambahkan.',
            'data' => $rule,
        ], 201);
    }

    public function updateAmnestyRule(Request $request, int $id): JsonResponse
    {
        $rule = DisciplineAmnestyRule::findOrFail($id);

        $validated = $request->validate([
            'code' => [
                'sometimes',
                'required',
                'string',
                'max:30',
                Rule::unique('discipline_amnesty_rules', 'code')
                    ->where('school_id', $rule->school_id)
                    ->ignore($rule->id),
            ],
            'name' => ['sometimes', 'required', 'string', 'max:200'],
            'credit_points' => ['sometimes', 'required', 'integer', 'min:1', 'max:200'],
            'min_violation_points_required' => ['nullable', 'integer', 'min:0'],
            'clean_days_required' => ['nullable', 'integer', 'min:0'],
            'max_claims_per_semester' => ['nullable', 'integer', 'min:1'],
            'description' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        if (isset($validated['code'])) {
            $validated['code'] = strtoupper(trim($validated['code']));
        }

        $rule->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Aturan pemutihan poin berhasil diperbarui.',
            'data' => $rule->fresh(),
        ]);
    }

    public function destroyAmnestyRule(int $id): JsonResponse
    {
        $rule = DisciplineAmnestyRule::findOrFail($id);
        $rule->delete();

        return response()->json([
            'success' => true,
            'message' => 'Aturan pemutihan poin berhasil dihapus.',
        ]);
    }

    // =========================================================================
    // 5. BUKU KREDIT POIN SISWA (CATAT PELANGGARAN & CATAT PEMUTIHAN)
    // =========================================================================

    public function storeViolationRecord(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'school_id' => ['required', 'integer', 'exists:schools,id'],
            'student_id' => ['required', 'integer', 'exists:students,id'],
            'discipline_item_id' => ['required', 'integer', 'exists:discipline_items,id'],
            'academic_year_id' => ['nullable', 'integer', 'exists:academic_years,id'],
            'academic_semester' => ['nullable', Rule::in(['odd', 'even'])],
            'record_date' => ['required', 'date'],
            'location' => ['nullable', 'string', 'max:150'],
            'notes' => ['nullable', 'string'],
        ]);

        $item = DisciplineItem::with('category')->findOrFail($validated['discipline_item_id']);
        $student = Student::findOrFail($validated['student_id']);

        $yearId = $validated['academic_year_id']
            ?? AcademicYear::where('school_id', $validated['school_id'])->where('is_active', true)->value('id');

        $record = StudentDisciplineRecord::create([
            'school_id' => $validated['school_id'],
            'student_id' => $student->id,
            'academic_year_id' => $yearId,
            'academic_semester' => $validated['academic_semester'] ?? 'odd',
            'record_type' => 'VIOLATION',
            'discipline_item_id' => $item->id,
            'record_date' => $validated['record_date'],
            'points' => $item->points,
            'title' => "[{$item->code}] {$item->name}",
            'location' => $validated['location'] ?? null,
            'notes' => $validated['notes'] ?? $item->default_sanction,
            'recorded_by' => $request->user()?->id,
            'approved_by' => $request->user()?->id,
            'status' => 'APPROVED',
        ]);

        $summary = $this->calculateStudentPointSummary($student->id, $validated['school_id'], $yearId, $validated['academic_semester'] ?? 'odd');

        return response()->json([
            'success' => true,
            'message' => "Pelanggaran tercatat (+{$item->points} Poin). Total Poin Bersih {$student->full_name}: {$summary['net_points']} Poin.",
            'data' => $record,
            'summary' => $summary,
        ], 201);
    }

    public function storeAmnestyRecord(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'school_id' => ['required', 'integer', 'exists:schools,id'],
            'student_id' => ['required', 'integer', 'exists:students,id'],
            'amnesty_rule_id' => ['required', 'integer', 'exists:discipline_amnesty_rules,id'],
            'academic_year_id' => ['nullable', 'integer', 'exists:academic_years,id'],
            'academic_semester' => ['nullable', Rule::in(['odd', 'even'])],
            'record_date' => ['required', 'date'],
            'custom_credit_points' => ['nullable', 'integer', 'min:1', 'max:200'],
            'notes' => ['nullable', 'string'],
            'force_override' => ['nullable', 'boolean'],
        ]);

        $schoolId = (int) $validated['school_id'];
        $policy = DisciplineBootstrapService::ensureDefaultsForSchool($schoolId);

        if (! $policy->amnesty_enabled) {
            return response()->json([
                'success' => false,
                'message' => 'Fitur pemutihan poin saat ini dinonaktifkan dalam pengaturan kebijakan Waka Kesiswaan.',
            ], 422);
        }

        $rule = DisciplineAmnestyRule::findOrFail($validated['amnesty_rule_id']);
        $student = Student::findOrFail($validated['student_id']);
        $yearId = $validated['academic_year_id']
            ?? AcademicYear::where('school_id', $schoolId)->where('is_active', true)->value('id');
        $semester = $validated['academic_semester'] ?? 'odd';
        $forceOverride = (bool) ($validated['force_override'] ?? false);

        $currentSummary = $this->calculateStudentPointSummary($student->id, $schoolId, $yearId, $semester);

        if ($currentSummary['net_points'] <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Siswa ini memiliki 0 poin pelanggaran bersih sehingga tidak memerlukan pemutihan poin.',
            ], 422);
        }

        if (! $forceOverride) {
            // Check heavy violation restriction if policy disallows amnesty for BERAT violations
            if (! $policy->allow_amnesty_for_heavy_violations && $currentSummary['has_heavy_violation']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Siswa tercatat memiliki pelanggaran kategori BERAT. Sesuai pengaturan Waka Kesiswaan, pelanggaran berat tidak dapat diputihkan tanpa otorisasi khusus.',
                ], 422);
            }

            if ($currentSummary['net_points'] < $rule->min_violation_points_required) {
                return response()->json([
                    'success' => false,
                    'message' => "Program pemutihan '{$rule->name}' mensyaratkan minimal {$rule->min_violation_points_required} poin aktif (poin siswa saat ini: {$currentSummary['net_points']}).",
                ], 422);
            }

            // Check clean days requirement
            $requiredCleanDays = max((int) $policy->min_clean_days_before_amnesty, (int) $rule->clean_days_required);
            if ($requiredCleanDays > 0 && $currentSummary['last_violation_date']) {
                $daysSinceLastViolation = Carbon::parse($currentSummary['last_violation_date'])->diffInDays(Carbon::parse($validated['record_date']), false);
                if ($daysSinceLastViolation < $requiredCleanDays) {
                    return response()->json([
                        'success' => false,
                        'message' => "Syarat pemutihan belum terpenuhi: siswa harus bebas pelanggaran baru minimal {$requiredCleanDays} hari (baru berjalan {$daysSinceLastViolation} hari sejak pelanggaran terakhir).",
                    ], 422);
                }
            }

            // Check semester max amnesty cap
            $creditToApply = (int) ($validated['custom_credit_points'] ?? $rule->credit_points);
            if (($currentSummary['semester_amnesty_points'] + $creditToApply) > $policy->max_amnesty_points_per_semester) {
                return response()->json([
                    'success' => false,
                    'message' => "Melebihi batas maksimal pemutihan per semester ({$policy->max_amnesty_points_per_semester} poin). Siswa sudah mendapatkan {$currentSummary['semester_amnesty_points']} poin pemutihan semester ini.",
                ], 422);
            }
        }

        $creditPoints = min(
            (int) ($validated['custom_credit_points'] ?? $rule->credit_points),
            $currentSummary['net_points']
        );

        $record = StudentDisciplineRecord::create([
            'school_id' => $schoolId,
            'student_id' => $student->id,
            'academic_year_id' => $yearId,
            'academic_semester' => $semester,
            'record_type' => 'AMNESTY',
            'amnesty_rule_id' => $rule->id,
            'record_date' => $validated['record_date'],
            'points' => $creditPoints,
            'title' => "[{$rule->code}] {$rule->name}",
            'notes' => $validated['notes'] ?? $rule->description,
            'recorded_by' => $request->user()?->id,
            'approved_by' => $request->user()?->id,
            'status' => 'APPROVED',
        ]);

        $newSummary = $this->calculateStudentPointSummary($student->id, $schoolId, $yearId, $semester);

        return response()->json([
            'success' => true,
            'message' => "Pemutihan poin berhasil diterapkan (-{$creditPoints} Poin). Sisa Poin Bersih {$student->full_name}: {$newSummary['net_points']} Poin.",
            'data' => $record,
            'summary' => $newSummary,
        ], 201);
    }

    public function destroyRecord(int $id): JsonResponse
    {
        $record = StudentDisciplineRecord::findOrFail($id);
        $record->delete();

        return response()->json([
            'success' => true,
            'message' => 'Catatan buku poin berhasil dihapus.',
        ]);
    }

    /**
     * Calculate violation points, amnesty credits, net points, and active threshold status for a student.
     */
    public function calculateStudentPointSummary(int $studentId, int $schoolId, ?int $academicYearId, string $semester = 'odd'): array
    {
        $policy = DisciplineBootstrapService::ensureDefaultsForSchool($schoolId);

        $query = StudentDisciplineRecord::with('disciplineItem.category')
            ->where('school_id', $schoolId)
            ->where('student_id', $studentId)
            ->where('status', 'APPROVED');

        if ($policy->reset_period === 'SEMESTER') {
            if ($academicYearId) {
                $query->where('academic_year_id', $academicYearId);
            }
            $query->where('academic_semester', $semester);
        } elseif ($policy->reset_period === 'ACADEMIC_YEAR' && $academicYearId) {
            $query->where('academic_year_id', $academicYearId);
        }

        $records = $query->orderByDesc('record_date')->get();

        $totalViolation = (int) $records->where('record_type', 'VIOLATION')->sum('points');
        $totalAmnesty = (int) $records->where('record_type', 'AMNESTY')->sum('points');
        $netPoints = max(0, $totalViolation - $totalAmnesty);

        $semesterAmnesty = (int) $records
            ->where('record_type', 'AMNESTY')
            ->where('academic_semester', $semester)
            ->sum('points');

        $lastViolation = $records->where('record_type', 'VIOLATION')->first();
        $hasHeavyViolation = $records->where('record_type', 'VIOLATION')->contains(function ($rec) {
            return $rec->disciplineItem?->category?->severity === 'BERAT';
        });

        $activeThreshold = DisciplineThreshold::where('school_id', $schoolId)
            ->where('is_active', true)
            ->where('min_points', '<=', $netPoints)
            ->where(function ($q) use ($netPoints) {
                $q->whereNull('max_points')->orWhere('max_points', '>=', $netPoints);
            })
            ->orderByDesc('min_points')
            ->first();

        return [
            'violation_points' => $totalViolation,
            'amnesty_points' => $totalAmnesty,
            'semester_amnesty_points' => $semesterAmnesty,
            'net_points' => $netPoints,
            'last_violation_date' => $lastViolation?->record_date?->format('Y-m-d'),
            'has_heavy_violation' => $hasHeavyViolation,
            'threshold' => $activeThreshold ? [
                'level_code' => $activeThreshold->level_code,
                'name' => $activeThreshold->name,
                'color' => $activeThreshold->color,
                'sanction_action' => $activeThreshold->sanction_action,
                'require_parent_call' => $activeThreshold->require_parent_call,
                'require_bk_counseling' => $activeThreshold->require_bk_counseling,
            ] : null,
        ];
    }
}

