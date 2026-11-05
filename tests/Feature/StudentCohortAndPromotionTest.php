<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\StudentClassHistory;
use Database\Seeders\SekolahkuAttendanceSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentCohortAndPromotionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(SekolahkuAttendanceSeeder::class);
    }

    public function test_students_api_supports_cohort_filter(): void
    {
        $school = School::first();
        $year = AcademicYear::where('school_id', $school->id)->first();

        $response = $this->getJson("/api/v1/students?school_id={$school->id}&entry_academic_year_id={$year->id}");
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $data = $response->json('data');
        $this->assertNotEmpty($data);
        foreach ($data as $s) {
            $this->assertEquals($year->id, $s['entry_academic_year_id']);
        }
    }

    public function test_store_student_sets_entry_academic_year_and_initial_history(): void
    {
        $school = School::first();
        $class = SchoolClass::where('school_id', $school->id)->first();
        $year = AcademicYear::where('school_id', $school->id)->first();

        $response = $this->postJson('/api/v1/students', [
            'school_id' => $school->id,
            'class_id' => $class->id,
            'entry_academic_year_id' => $year->id,
            'entry_grade_level' => $class->grade_level,
            'registration_type' => 'baru',
            'nis' => 'TESTNIS999',
            'nisn' => '0099887766',
            'full_name' => 'Bintang Pratama',
            'gender' => 'L',
            'birth_date' => '2010-01-01',
            'enrollment_date' => '2026-07-15',
        ]);

        $response->assertStatus(201);
        $response->assertJson([
            'success' => true,
            'data' => [
                'nis' => 'TESTNIS999',
                'entry_academic_year_id' => $year->id,
            ],
        ]);

        $student = Student::where('nis', 'TESTNIS999')->first();
        $this->assertNotNull($student);
        $this->assertEquals($year->id, $student->entry_academic_year_id);

        $history = StudentClassHistory::where('student_id', $student->id)->first();
        $this->assertNotNull($history);
        $this->assertEquals($class->id, $history->class_id);
        $this->assertEquals('active', $history->status);
    }

    public function test_get_student_class_histories(): void
    {
        $student = Student::first();
        $this->assertNotNull($student);

        $response = $this->getJson("/api/v1/students/{$student->id}/class-histories");
        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'data' => [
                'student' => ['id' => $student->id],
            ],
        ]);
    }

    public function test_bulk_promotion_promotes_students_to_next_year(): void
    {
        $school = School::first();
        $year2026 = AcademicYear::where('school_id', $school->id)->first();

        // Buat tahun ajaran baru 2027/2028
        $year2027 = AcademicYear::create([
            'school_id' => $school->id,
            'name' => '2027/2028',
            'start_date' => '2027-07-15',
            'end_date' => '2028-06-25',
            'is_active' => false,
        ]);

        // Kelas asal (tingkat 10)
        $class10 = SchoolClass::where('school_id', $school->id)->where('grade_level', 10)->first();
        // Kelas tujuan (tingkat 11)
        $class11 = SchoolClass::create([
            'school_id' => $school->id,
            'name' => 'XI-MIPA-1',
            'grade_level' => 11,
            'academic_year_id' => $year2027->id,
            'is_active' => true,
        ]);

        $student = Student::where('class_id', $class10->id)->first();
        $this->assertNotNull($student);

        $payload = [
            'school_id' => $school->id,
            'target_academic_year_id' => $year2027->id,
            'entries' => [
                [
                    'student_id' => $student->id,
                    'action' => 'promote',
                    'target_class_id' => $class11->id,
                ],
            ],
        ];

        $response = $this->postJson('/api/v1/promotions', $payload);
        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'data' => [
                'processed' => 1,
                'summary' => [
                    'promote' => 1,
                ],
            ],
        ]);

        // Siswa sekarang berada di kelas 11
        $student->refresh();
        $this->assertEquals($class11->id, $student->class_id);

        // Riwayat lama statusnya 'promoted', riwayat baru statusnya 'active'
        $histories = StudentClassHistory::where('student_id', $student->id)->get();
        $this->assertCount(2, $histories);

        $oldHistory = $histories->firstWhere('academic_year_id', $year2026->id);
        $this->assertEquals('promoted', $oldHistory->status);

        $newHistory = $histories->firstWhere('academic_year_id', $year2027->id);
        $this->assertEquals('active', $newHistory->status);
        $this->assertEquals($class11->id, $newHistory->class_id);
    }
}
