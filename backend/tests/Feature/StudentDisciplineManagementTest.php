<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\School;
use App\Models\Student;
use App\Models\User;
use Database\Seeders\SekolahkuAttendanceSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentDisciplineManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(SekolahkuAttendanceSeeder::class);
    }

    public function test_waka_kesiswaan_can_view_tatib_page_and_manage_master_categories_and_items(): void
    {
        $school = School::first();
        $waka = User::where('role', 'waka_kesiswaan')->firstOrFail();

        $pageRes = $this->actingAs($waka)->get('/admin/kesiswaan/tatib');
        $pageRes->assertStatus(200);
        $pageRes->assertSee('Master Kategori & Item Pelanggaran', false);
        $pageRes->assertSee('Kerajinan & Kehadiran');

        // 1. Create a new Master Kategori Pelanggaran
        $catRes = $this->actingAs($waka)->postJson('/api/v1/discipline/categories', [
            'school_id' => $school->id,
            'code' => 'KAT-GAWAI',
            'name' => 'Ketertiban Penggunaan Gawai & Digital',
            'severity' => 'SEDANG',
            'color' => '#8b5cf6',
            'description' => 'Aturan penggunaan ponsel pintar selama jam KBM.',
        ]);

        $catRes->assertStatus(201);
        $catId = $catRes->json('data.id');

        // 2. Add an Item Pelanggaran beserta Besar Poin inside that category
        $itemRes = $this->actingAs($waka)->postJson('/api/v1/discipline/items', [
            'category_id' => $catId,
            'code' => 'G-01',
            'name' => 'Bermain game online saat jam pelajaran berlangsung',
            'points' => 20,
            'default_sanction' => 'Penitipan gawai di ruang Waka Kesiswaan hingga pulang sekolah',
        ]);

        $itemRes->assertStatus(201);
        $itemId = $itemRes->json('data.id');
        $this->assertEquals(20, $itemRes->json('data.points'));

        // 3. Update item points
        $updRes = $this->actingAs($waka)->putJson("/api/v1/discipline/items/{$itemId}", [
            'points' => 25,
        ]);
        $updRes->assertStatus(200);
        $this->assertEquals(25, $updRes->json('data.points'));
    }

    public function test_waka_kesiswaan_can_configure_thresholds_and_amnesty_policy_and_apply_to_student(): void
    {
        $school = School::first();
        $year = AcademicYear::where('school_id', $school->id)->first();
        $waka = User::where('role', 'waka_kesiswaan')->firstOrFail();
        $student = Student::where('school_id', $school->id)->firstOrFail();

        // 1. Waka Kesiswaan configures Policy (ambang batas & pemutihan)
        $policyRes = $this->actingAs($waka)->putJson('/api/v1/discipline/policy', [
            'school_id' => $school->id,
            'max_violation_points' => 100,
            'reset_period' => 'ACADEMIC_YEAR',
            'amnesty_enabled' => true,
            'max_amnesty_points_per_semester' => 40,
            'min_clean_days_before_amnesty' => 10,
            'auto_refer_bk_threshold' => 45,
            'allow_amnesty_for_heavy_violations' => false,
        ]);
        $policyRes->assertStatus(200);
        $this->assertEquals(40, $policyRes->json('data.max_amnesty_points_per_semester'));
        $this->assertEquals(10, $policyRes->json('data.min_clean_days_before_amnesty'));

        // 2. Waka Kesiswaan updates SP1 threshold to start from 45 points
        $indexData = $this->actingAs($waka)->getJson("/api/v1/discipline?school_id={$school->id}")->json('data');
        $sp1 = collect($indexData['thresholds'])->firstWhere('level_code', 'SP1');
        $this->assertNotNull($sp1);

        $thUpdate = $this->actingAs($waka)->putJson("/api/v1/discipline/thresholds/{$sp1['id']}", [
            'min_points' => 45,
            'max_points' => 74,
        ]);
        $thUpdate->assertStatus(200);

        // 3. Waka Kesiswaan creates a custom Amnesty Rule (-20 points)
        $amnestyRuleRes = $this->actingAs($waka)->postJson('/api/v1/discipline/amnesty-rules', [
            'school_id' => $school->id,
            'code' => 'PMT-LIT',
            'name' => 'Pembinaan Literasi Perpustakaan 5 Buku',
            'credit_points' => 20,
            'min_violation_points_required' => 20,
            'clean_days_required' => 10,
            'max_claims_per_semester' => 2,
        ]);
        $amnestyRuleRes->assertStatus(201);
        $amnestyRuleId = $amnestyRuleRes->json('data.id');

        // 4. Record violations totaling 50 points -> triggers SP1 (>= 45 points)
        $catC = collect($indexData['categories'])->firstWhere('code', 'KAT-C');
        $item25 = collect($catC['items'])->firstWhere('code', 'C-02'); // 25 points

        $this->actingAs($waka)->postJson('/api/v1/discipline/violations', [
            'school_id' => $school->id,
            'student_id' => $student->id,
            'discipline_item_id' => $item25['id'],
            'academic_year_id' => $year->id,
            'academic_semester' => 'odd',
            'record_date' => '2026-09-01',
        ])->assertStatus(201);

        $v2Res = $this->actingAs($waka)->postJson('/api/v1/discipline/violations', [
            'school_id' => $school->id,
            'student_id' => $student->id,
            'discipline_item_id' => $item25['id'],
            'academic_year_id' => $year->id,
            'academic_semester' => 'odd',
            'record_date' => '2026-09-05',
        ]);
        $v2Res->assertStatus(201);
        $this->assertEquals(50, $v2Res->json('summary.net_points'));
        $this->assertEquals('SP1', $v2Res->json('summary.threshold.level_code'));

        // 5. Attempt amnesty too early (only 3 days after 2026-09-05, requires 10 clean days) -> rejected!
        $earlyAmnesty = $this->actingAs($waka)->postJson('/api/v1/discipline/amnesties', [
            'school_id' => $school->id,
            'student_id' => $student->id,
            'amnesty_rule_id' => $amnestyRuleId,
            'academic_year_id' => $year->id,
            'academic_semester' => 'odd',
            'record_date' => '2026-09-08',
        ]);
        $earlyAmnesty->assertStatus(422);

        // 6. Apply amnesty after 12 clean days (2026-09-17) -> succeeds (-20 points -> net 30 points -> drops from SP1 to PEMBINAAN!)
        $validAmnesty = $this->actingAs($waka)->postJson('/api/v1/discipline/amnesties', [
            'school_id' => $school->id,
            'student_id' => $student->id,
            'amnesty_rule_id' => $amnestyRuleId,
            'academic_year_id' => $year->id,
            'academic_semester' => 'odd',
            'record_date' => '2026-09-17',
        ]);
        $validAmnesty->assertStatus(201);
        $this->assertEquals(30, $validAmnesty->json('summary.net_points'));
        $this->assertEquals('PEMBINAAN', $validAmnesty->json('summary.threshold.level_code'));
    }
}
