<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\DisciplineAmnestyRule;
use App\Models\DisciplineItem;
use App\Models\School;
use App\Models\Student;
use App\Models\StudentDisciplineRecord;
use App\Models\User;
use Database\Seeders\SekolahkuAttendanceSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentPortalModuleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(SekolahkuAttendanceSeeder::class);
    }

    public function test_student_login_redirects_directly_to_student_dashboard(): void
    {
        $student = Student::where('nis', '20261001')->firstOrFail();
        $studentUser = User::findOrFail($student->user_id);

        $loginResponse = $this->post('/login', [
            'email' => $studentUser->email,
            'password' => 'password',
        ]);

        $loginResponse->assertRedirect(route('student.dashboard'));

        $dashboardRes = $this->actingAs($studentUser)->get('/student/dashboard');
        $dashboardRes->assertStatus(200);
        $dashboardRes->assertSee($student->full_name, false);
        $dashboardRes->assertSee('NIS: ' . $student->nis, false);
        $dashboardRes->assertSee('Skor Kredit Disiplin', false);
        $dashboardRes->assertSee('Informasi Penting Untukmu', false);
        $dashboardRes->assertSee('Saldo Saku Digital', false);
    }

    public function test_student_cannot_impersonate_another_student_via_query_parameter(): void
    {
        $ahmad = Student::where('nis', '20261001')->firstOrFail();
        $siti = Student::where('nis', '20261002')->firstOrFail();
        $ahmadUser = User::findOrFail($ahmad->user_id);

        $res = $this->actingAs($ahmadUser)->get('/student/dashboard?student_id=' . $siti->id);
        $res->assertStatus(200);
        $res->assertSee($ahmad->full_name, false);
        $res->assertSee('NIS: ' . $ahmad->nis, false);
        $res->assertDontSee('NIS: ' . $siti->nis, false);
    }

    public function test_student_personal_schedule_automatically_filters_by_biodata_religion_and_merges_combined_classes(): void
    {
        // 1. Ahmad Fauzi (X MIPA 1, Biodata Agama: Islam) -> sees PAI, does NOT see PAK
        $muslimStudent = Student::where('nis', '20261001')->firstOrFail();
        $muslimUser = User::findOrFail($muslimStudent->user_id);

        $muslimSchedRes = $this->actingAs($muslimUser)->get('/student/schedules');
        $muslimSchedRes->assertStatus(200);
        $muslimSchedRes->assertSee('Pendidikan Agama Islam', false);
        $muslimSchedRes->assertDontSee('Pendidikan Agama Kristen', false);

        // 2. Budi Santoso (X MIPA 1, Biodata Agama: Kristen) -> sees Combined Class PAK (X MIPA 1, X MIPA 2) with rowspan="2", does NOT see PAI
        $christianStudent = Student::where('nis', '20261003')->firstOrFail();
        $christianUser = User::findOrFail($christianStudent->user_id);

        $christianSchedRes = $this->actingAs($christianUser)->get('/student/schedules');
        $christianSchedRes->assertStatus(200);
        $christianSchedRes->assertSee('Pendidikan Agama Kristen', false);
        $christianSchedRes->assertSee('Gabungan: X MIPA 1, X MIPA 2', false);
        $christianSchedRes->assertSee('rowspan="2"', false);
        $christianSchedRes->assertDontSee('Pendidikan Agama Islam', false);
    }

    public function test_student_attendance_discipline_and_digital_card_pages_render_accurate_data(): void
    {
        $school = School::firstOrFail();
        $year = AcademicYear::where('school_id', $school->id)->firstOrFail();
        $student = Student::where('nis', '20261001')->firstOrFail();
        $studentUser = User::findOrFail($student->user_id);

        // Record a 20-point violation and a 10-point amnesty -> net violation = 10, credit score = 90/100
        $item = DisciplineItem::where('school_id', $school->id)->firstOrFail();
        $amnesty = DisciplineAmnestyRule::where('school_id', $school->id)->firstOrFail();

        StudentDisciplineRecord::create([
            'school_id' => $school->id,
            'student_id' => $student->id,
            'academic_year_id' => $year->id,
            'academic_semester' => 'odd',
            'record_type' => 'VIOLATION',
            'discipline_item_id' => $item->id,
            'record_date' => now()->subDays(5)->toDateString(),
            'points' => 20,
            'title' => 'Terlambat hadir apel pagi',
            'status' => 'APPROVED',
        ]);

        StudentDisciplineRecord::create([
            'school_id' => $school->id,
            'student_id' => $student->id,
            'academic_year_id' => $year->id,
            'academic_semester' => 'odd',
            'record_type' => 'AMNESTY',
            'amnesty_rule_id' => $amnesty->id,
            'record_date' => now()->subDay()->toDateString(),
            'points' => 10,
            'title' => 'Pemutihan kebersihan perpustakaan',
            'status' => 'APPROVED',
        ]);

        // 1. Discipline page
        $discRes = $this->actingAs($studentUser)->get('/student/discipline');
        $discRes->assertStatus(200);
        $discRes->assertSee('Terlambat hadir apel pagi', false);
        $discRes->assertSee('Pemutihan kebersihan perpustakaan', false);
        $discRes->assertSee('Buku Saku Pasal Tata Tertib Sekolah', false);
        $discRes->assertSee('90', false);

        // 2. Attendance page
        $attRes = $this->actingAs($studentUser)->get('/student/attendance');
        $attRes->assertStatus(200);
        $attRes->assertSee('Riwayat Kehadiran Harian Saya', false);
        $attRes->assertSee('Kampus Utama SMAN 1 Jakarta', false);

        // 3. Digital ID Card page
        $cardRes = $this->actingAs($studentUser)->get('/student/card');
        $cardRes->assertStatus(200);
        $cardRes->assertSee('Kartu Identitas Peserta Didik', false);
        $cardRes->assertSee('Checklist Kelengkapan Atribut Dapodik', false);
        $cardRes->assertSee('Riwayat Penempatan Rombel & Kenaikan Kelas', false);
    }
}

