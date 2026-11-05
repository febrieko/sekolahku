<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\LessonPeriod;
use App\Models\ParentGuardian;
use App\Models\School;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\TimeScheduleCategory;
use Database\Seeders\SekolahkuAttendanceSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DataMasterTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(SekolahkuAttendanceSeeder::class);
        $this->seed(\Database\Seeders\CurriculumSeeder::class);
    }

    public function test_academic_years_listing_and_activation(): void
    {
        $response = $this->getJson('/api/v1/academic-years');
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'data' => [
                '*' => ['id', 'name', 'start_date', 'end_date', 'is_active', 'semesters'],
            ],
        ]);

        $year = AcademicYear::first();
        $activateResponse = $this->postJson("/api/v1/academic-years/{$year->id}/activate");
        $activateResponse->assertStatus(200);
        $this->assertTrue((bool) $year->fresh()->is_active);
    }

    public function test_multi_category_time_schedules_and_periods(): void
    {
        // 1. List Categories (Normal, Ramadhan, Exam)
        $response = $this->getJson('/api/v1/time-categories');
        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);

        $normalCat = TimeScheduleCategory::where('code', 'NORMAL')->first();
        $this->assertNotNull($normalCat);
        $this->assertTrue((bool) $normalCat->is_default);

        // 2. List Periods for category
        $periodResponse = $this->getJson("/api/v1/time-categories/{$normalCat->id}/periods?day_of_week=1");
        $periodResponse->assertStatus(200);
        $this->assertNotEmpty($periodResponse->json('data'));

        // 3. Switch active category to Ramadhan
        $ramadhanCat = TimeScheduleCategory::where('code', 'RAMADHAN')->first();
        $activateResponse = $this->postJson("/api/v1/time-categories/{$ramadhanCat->id}/activate");
        $activateResponse->assertStatus(200);
        $this->assertTrue((bool) $ramadhanCat->fresh()->is_default);
        $this->assertFalse((bool) $normalCat->fresh()->is_default);

        // 4. Dynamic Add Category
        $createCatResponse = $this->postJson('/api/v1/time-categories', [
            'school_id' => $normalCat->school_id,
            'name' => 'Jadwal Peringatan HUT RI',
            'code' => 'HUT_RI',
            'description' => 'Jadwal khusus perlombaan 17 Agustus',
            'is_default' => false,
            'is_active' => true,
        ]);
        $createCatResponse->assertStatus(201);
        $newCatId = $createCatResponse->json('data.id');

        // 5. Dynamic Update Category
        $updateCatResponse = $this->putJson("/api/v1/time-categories/{$newCatId}", [
            'name' => 'Jadwal HUT RI & Kebangsaan',
            'code' => 'HUT_RI_REVISED',
            'description' => 'Jadwal upacara & lomba kemerdekaan',
            'is_active' => true,
        ]);
        $updateCatResponse->assertStatus(200);
        $this->assertEquals('Jadwal HUT RI & Kebangsaan', $updateCatResponse->json('data.name'));

        // 6. Disallow deleting default category
        $delDefaultResponse = $this->deleteJson("/api/v1/time-categories/{$ramadhanCat->id}");
        $delDefaultResponse->assertStatus(422);

        // 7. Dynamic Delete non-default category
        $delResponse = $this->deleteJson("/api/v1/time-categories/{$newCatId}");
        $delResponse->assertStatus(200);
        $this->assertNull(TimeScheduleCategory::find($newCatId));
    }

    public function test_teachers_with_dapodik_attributes(): void
    {
        $response = $this->getJson('/api/v1/teachers');
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'data' => [
                '*' => ['id', 'nip', 'nuptk', 'full_name', 'gender', 'employment_type'],
            ],
        ]);

        $teacher = Teacher::where('nip', '198503152010012015')->first();
        $this->assertNotNull($teacher);
        $this->assertEquals('Dian Prameswari', $teacher->full_name);
    }

    public function test_parents_with_multi_child_relation(): void
    {
        $response = $this->getJson('/api/v1/parents');
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'data' => [
                '*' => ['id', 'father_name', 'primary_phone', 'students'],
            ],
        ]);

        $parent = ParentGuardian::with('students')->first();
        $this->assertNotNull($parent);
        $this->assertGreaterThanOrEqual(1, $parent->students->count());
    }

    public function test_class_schedules_with_conflict_prevention(): void
    {
        $school = School::first();
        $teacher = Teacher::first();
        $period = LessonPeriod::first();
        $academicYear = AcademicYear::first();
        $subject = Subject::first();
        $class1 = \App\Models\SchoolClass::first();
        $class2 = \App\Models\SchoolClass::skip(1)->first();

        // 1. First assignment succeeds
        \App\Models\ClassSchedule::where('teacher_id', $teacher->id)
            ->where('lesson_period_id', $period->id)
            ->delete();

        $response1 = $this->postJson('/api/v1/schedules', [
            'school_id' => $school->id,
            'class_id' => $class1->id,
            'subject_id' => $subject->id,
            'teacher_id' => $teacher->id,
            'lesson_period_id' => $period->id,
            'academic_year_id' => $academicYear->id,
            'academic_semester' => 'odd',
        ]);
        $response1->assertStatus(201);

        // 2. Conflicting assignment (same teacher, same period, different class) is rejected
        $response2 = $this->postJson('/api/v1/schedules', [
            'school_id' => $school->id,
            'class_id' => $class2->id,
            'subject_id' => $subject->id,
            'teacher_id' => $teacher->id,
            'lesson_period_id' => $period->id,
            'academic_year_id' => $academicYear->id,
            'academic_semester' => 'odd',
        ]);
        $response2->assertStatus(422);
        $response2->assertJson([
            'success' => false,
        ]);
    }

    public function test_class_crud_operations(): void
    {
        $school = School::first();
        $academicYear = AcademicYear::first();
        $conc = \App\Models\VocationalConcentration::first();

        // 1. Create Class
        $payload = [
            'school_id' => $school->id,
            'name' => 'X PPLG 99',
            'grade_level' => 10,
            'academic_year_id' => $academicYear->id,
            'vocational_concentration_id' => $conc?->id,
        ];

        $createRes = $this->postJson('/api/v1/classes', $payload);
        $createRes->assertStatus(201);
        $createRes->assertJson([
            'success' => true,
        ]);
        $classId = $createRes->json('data.id');
        $this->assertNotNull($classId);

        // 2. Update Class
        $updateRes = $this->putJson("/api/v1/classes/{$classId}", [
            'name' => 'XI PPLG 99',
            'grade_level' => 11,
        ]);
        $updateRes->assertStatus(200);
        $this->assertEquals('XI PPLG 99', $updateRes->json('data.name'));

        // 3. Delete Class
        $deleteRes = $this->deleteJson("/api/v1/classes/{$classId}");
        $deleteRes->assertStatus(200);
        $this->assertNull(\App\Models\SchoolClass::find($classId));
    }

    public function test_subject_crud_with_curriculum_group_operations(): void
    {
        $school = School::first();
        $curriculum = \App\Models\Curriculum::first();

        // 1. Create Subject with Curriculum Group
        $createRes = $this->postJson('/api/v1/subjects', [
            'school_id' => $school->id,
            'code' => 'WEBDEV',
            'name' => 'Pemrograman Web & Perangkat Bergerak',
            'curriculum_id' => $curriculum->id,
            'subject_group' => 'PEMINATAN_PILIHAN',
            'hours_per_week' => 6,
            'passing_grade' => 78.5,
        ]);

        $createRes->assertStatus(201);
        $createRes->assertJson([
            'success' => true,
        ]);
        $subjectId = $createRes->json('data.id');
        $this->assertNotNull($subjectId);

        // Verify CurriculumSubject mapping was automatically created
        $currSub = \App\Models\CurriculumSubject::where('curriculum_id', $curriculum->id)
            ->where('subject_id', $subjectId)
            ->first();
        $this->assertNotNull($currSub);
        $this->assertEquals('PEMINATAN_PILIHAN', $currSub->subject_group);
        $this->assertEquals(6, $currSub->hours_per_week);

        // 2. Update Subject and its Curriculum Group
        $updateRes = $this->putJson("/api/v1/subjects/{$subjectId}", [
            'name' => 'Pemrograman Web & Mobile Tingkat Lanjut',
            'curriculum_id' => $curriculum->id,
            'subject_group' => 'WAJIB_UMUM',
            'hours_per_week' => 4,
            'passing_grade' => 80.0,
        ]);
        $updateRes->assertStatus(200);
        $this->assertEquals('Pemrograman Web & Mobile Tingkat Lanjut', $updateRes->json('data.name'));
        $this->assertEquals('WAJIB_UMUM', $currSub->fresh()->subject_group);
        $this->assertEquals(4, $currSub->fresh()->hours_per_week);

        // 3. Remove Subject from Curriculum
        $removeCurrRes = $this->deleteJson("/api/v1/curriculums/{$curriculum->id}/subjects/{$subjectId}");
        $removeCurrRes->assertStatus(200);
        $this->assertNull(\App\Models\CurriculumSubject::where('curriculum_id', $curriculum->id)->where('subject_id', $subjectId)->first());

        // 4. Delete Subject
        $deleteRes = $this->deleteJson("/api/v1/subjects/{$subjectId}");
        $deleteRes->assertStatus(200);
        $this->assertNull(\App\Models\Subject::find($subjectId));
    }
}
