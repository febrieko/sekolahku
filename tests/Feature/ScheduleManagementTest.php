<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\ClassSchedule;
use App\Models\LessonPeriod;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\SchoolRoom;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\TeachingGroup;
use App\Models\TimeScheduleCategory;
use App\Models\User;
use Database\Seeders\SekolahkuAttendanceSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ScheduleManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(SekolahkuAttendanceSeeder::class);
    }

    public function test_admin_can_open_dedicated_schedules_page(): void
    {
        $admin = User::whereIn('role', ['super_admin', 'school_admin', 'teacher'])->first()
            ?? User::factory()->create(['role' => 'school_admin']);

        $response = $this->actingAs($admin)->get('/admin/schedules');

        $response->assertStatus(200);
        $response->assertSee('Matriks & Pengaturan Jadwal Pelajaran', false);
        $response->assertSee('Salin Jadwal dari Semester Lalu', false);
        $response->assertSee('Jadwal per Kelas');
        $response->assertSee('Jadwal per Guru');
        $response->assertSee('Jadwal per Ruang Kelas / Lab');
    }

    public function test_dynamic_category_with_validity_dates_and_cloned_periods(): void
    {
        $school = School::first();
        $year = AcademicYear::first();
        $defaultCategory = TimeScheduleCategory::where('school_id', $school->id)->where('is_default', true)->first();

        $futureStart = now()->addMonths(2)->format('Y-m-d');
        $futureEnd = now()->addMonths(7)->format('Y-m-d');

        $response = $this->postJson('/api/v1/time-categories', [
            'school_id' => $school->id,
            'academic_year_id' => $year->id,
            'academic_semester' => 'even',
            'name' => 'Jadwal Persiapan Semester Genap 2026/2027',
            'code' => 'PREP_GENAP_27',
            'description' => 'Disiapkan untuk semester mendatang',
            'effective_start_date' => $futureStart,
            'effective_end_date' => $futureEnd,
            'clone_periods_from_category_id' => $defaultCategory->id,
        ]);

        $response->assertStatus(201);
        $newCatId = $response->json('data.id');

        $newCat = TimeScheduleCategory::findOrFail($newCatId);
        $this->assertEquals('UPCOMING', $newCat->validity_status);
        $this->assertEquals($defaultCategory->periods()->count(), $newCat->periods()->count());
    }

    public function test_school_rooms_crud_and_triple_conflict_detection(): void
    {
        $school = School::first();
        $year = AcademicYear::first();
        $class1 = SchoolClass::where('school_id', $school->id)->first();
        $class2 = SchoolClass::where('school_id', $school->id)->skip(1)->first();
        $teacher1 = Teacher::where('school_id', $school->id)->first();
        $teacher2 = Teacher::where('school_id', $school->id)->skip(1)->first();
        $subject = Subject::where('school_id', $school->id)->first();

        // Create a Computer Lab room
        $roomRes = $this->postJson('/api/v1/rooms', [
            'school_id' => $school->id,
            'code' => 'LAB-KOM-99',
            'name' => 'Lab Komputer Terpadu 99',
            'type' => 'LABORATORY',
            'building' => 'Gedung TIK',
            'floor' => 'Lantai 2',
            'capacity' => 40,
        ]);
        $roomRes->assertStatus(201);
        $labRoomId = $roomRes->json('data.id');

        // Pick a period on Day 3 (Rabu)
        $period = LessonPeriod::where('school_id', $school->id)
            ->where('day_of_week', 3)
            ->where('is_break', false)
            ->orderBy('start_time')
            ->first();

        // 1. Schedule Class 1 with Teacher 1 in Lab Room
        $sched1 = $this->postJson('/api/v1/schedules', [
            'school_id' => $school->id,
            'class_id' => $class1->id,
            'subject_id' => $subject->id,
            'teacher_id' => $teacher1->id,
            'lesson_period_id' => $period->id,
            'academic_year_id' => $year->id,
            'academic_semester' => 'odd',
            'room_id' => $labRoomId,
        ]);
        $sched1->assertStatus(201);

        // 2. Room Conflict: Schedule Class 2 with Teacher 2 in the SAME Lab Room at the SAME period
        $roomConflictRes = $this->postJson('/api/v1/schedules', [
            'school_id' => $school->id,
            'class_id' => $class2->id,
            'subject_id' => $subject->id,
            'teacher_id' => $teacher2->id,
            'lesson_period_id' => $period->id,
            'academic_year_id' => $year->id,
            'academic_semester' => 'odd',
            'room_id' => $labRoomId,
        ]);
        $roomConflictRes->assertStatus(422);
        $roomConflictRes->assertJsonFragment(['conflict_type' => 'ROOM']);

        // 3. Filter schedules by room_id, teacher_id, and class_id
        $byRoom = $this->getJson("/api/v1/schedules?school_id={$school->id}&room_id={$labRoomId}");
        $byRoom->assertStatus(200);
        $this->assertCount(1, $byRoom->json('data'));
    }

    public function test_copy_schedules_from_previous_semester_to_upcoming_semester(): void
    {
        $school = School::first();
        $year = AcademicYear::first();
        $defaultCategory = TimeScheduleCategory::where('school_id', $school->id)->where('is_default', true)->first();

        // Create upcoming category for Even semester ( initially with 0 periods to test auto-period-cloning )
        $upcomingCat = TimeScheduleCategory::create([
            'school_id' => $school->id,
            'academic_year_id' => $year->id,
            'academic_semester' => 'even',
            'name' => 'Jadwal Semester Genap Mendatang',
            'code' => 'GENAP_NEXT',
            'is_default' => false,
            'is_active' => true,
            'effective_start_date' => '2027-01-05',
            'effective_end_date' => '2027-06-25',
        ]);

        $oddCount = ClassSchedule::where('school_id', $school->id)
            ->where('academic_year_id', $year->id)
            ->where('academic_semester', 'odd')
            ->count();

        $this->assertGreaterThan(0, $oddCount);

        // Copy from Odd semester (defaultCategory) to Even semester (upcomingCat)
        $copyRes = $this->postJson('/api/v1/schedules/copy-semester', [
            'school_id' => $school->id,
            'source_academic_year_id' => $year->id,
            'source_semester' => 'odd',
            'source_category_id' => $defaultCategory->id,
            'target_academic_year_id' => $year->id,
            'target_semester' => 'even',
            'target_category_id' => $upcomingCat->id,
            'overwrite' => true,
        ]);

        $copyRes->assertStatus(200);
        $copyRes->assertJsonPath('success', true);
        $this->assertEquals($oddCount, $copyRes->json('summary.copied_count'));

        $evenCount = ClassSchedule::where('school_id', $school->id)
            ->where('academic_year_id', $year->id)
            ->where('academic_semester', 'even')
            ->where('category_id', $upcomingCat->id)
            ->count();

        $this->assertEquals($oddCount, $evenCount);
    }

    public function test_consecutive_lesson_periods_are_automatically_merged_with_rowspan_in_table_view(): void
    {
        $school = School::first();
        $year = AcademicYear::first();
        $class1 = SchoolClass::where('school_id', $school->id)->first();
        $teacher1 = Teacher::where('school_id', $school->id)->first();
        $subject = Subject::where('school_id', $school->id)->first();
        $admin = User::whereIn('role', ['super_admin', 'school_admin', 'teacher'])->first()
            ?? User::factory()->create(['role' => 'school_admin']);

        // Get Jam Ke-1, Jam Ke-2, Jam Ke-3 on Selasa (day_of_week = 2)
        $tuesdayPeriods = LessonPeriod::where('school_id', $school->id)
            ->where('day_of_week', 2)
            ->where('is_break', false)
            ->orderBy('start_time')
            ->take(3)
            ->get();

        $this->assertCount(3, $tuesdayPeriods);

        // Assign 3 consecutive periods at once (Jam Ke-1 s/d Jam Ke-3)
        $storeRes = $this->postJson('/api/v1/schedules', [
            'school_id' => $school->id,
            'class_id' => $class1->id,
            'subject_id' => $subject->id,
            'teacher_id' => $teacher1->id,
            'lesson_period_id' => $tuesdayPeriods->first()->id,
            'end_lesson_period_id' => $tuesdayPeriods->last()->id,
            'academic_year_id' => $year->id,
            'academic_semester' => 'odd',
            'replace_existing' => true,
        ]);

        $storeRes->assertStatus(201);
        $this->assertCount(3, $storeRes->json('items'));

        // Verify the rendered table view merges the 3 consecutive periods using rowspan="3"
        // and displays Subject Name, Teacher Name directly (without 'Guru:' or 'Ruang:' prefixes), Room Pill, and Subject Color
        $colorRes = $this->putJson("/api/v1/subjects/{$subject->id}", [
            'color' => '#e11d48',
        ]);
        $colorRes->assertStatus(200);
        $this->assertEquals('#e11d48', $subject->fresh()->color);

        $viewRes = $this->actingAs($admin)->get("/admin/schedules?class_id={$class1->id}&teacher_id={$teacher1->id}");
        $viewRes->assertStatus(200);
        $viewRes->assertSee('rowspan="3"', false);
        $viewRes->assertSee($subject->name, false);
        $viewRes->assertSee($teacher1->full_name, false);
        $viewRes->assertSee('background-color: #e11d4824; border-left: 4px solid #e11d48;', false);
        $viewRes->assertDontSee('<strong>Guru:</strong>', false);
        $viewRes->assertDontSee('<strong>Ruang:</strong>', false);
        $viewRes->assertDontSee('<span class="slot-merged-badge">', false);

        // Now test editing the schedule slot's jam/period range (shrinking from 3 JP to 2 JP: Jam Ke-1 s/d Jam Ke-2)
        $createdIds = collect($storeRes->json('items'))->pluck('id')->all();
        $firstSchedId = $createdIds[0];

        $updateRes = $this->putJson("/api/v1/schedules/{$firstSchedId}", [
            'subject_id' => $subject->id,
            'teacher_id' => $teacher1->id,
            'lesson_period_id' => $tuesdayPeriods[0]->id,
            'end_lesson_period_id' => $tuesdayPeriods[1]->id,
            'schedule_ids' => $createdIds,
        ]);

        $updateRes->assertStatus(200);
        $this->assertCount(2, $updateRes->json('items'));

        // Also test editing LessonPeriod clock time (start_time & end_time) with apply_all_days
        $periodUpdateRes = $this->putJson("/api/v1/periods/{$tuesdayPeriods[0]->id}", [
            'name' => 'Jam Ke-1',
            'period_number' => 1,
            'start_time' => '07:05',
            'end_time' => '07:45',
            'is_break' => false,
            'apply_all_days' => true,
        ]);
        $periodUpdateRes->assertStatus(200);
        $this->assertEquals('07:05', substr($tuesdayPeriods[0]->fresh()->start_time, 0, 5));
    }

    public function test_religious_subjects_filter_students_by_biodata_religion_automatically(): void
    {
        $school = School::first();
        $paiSubject = Subject::where('school_id', $school->id)->where('code', 'PAI')->firstOrFail();
        $pakSubject = Subject::where('school_id', $school->id)->where('code', 'PAK')->firstOrFail();

        $this->assertEquals('Islam', $paiSubject->resolved_religion_filter);
        $this->assertEquals('Kristen', $pakSubject->resolved_religion_filter);

        // 1. Regular single-class PAI schedule in X MIPA 1 -> must ONLY return Muslim students of X MIPA 1
        $paiSchedule = ClassSchedule::where('school_id', $school->id)
            ->where('subject_id', $paiSubject->id)
            ->where('is_combined', false)
            ->firstOrFail();

        $paiRes = $this->getJson("/api/v1/schedules/{$paiSchedule->id}/students");
        $paiRes->assertStatus(200);
        $paiRes->assertJsonPath('schedule.effective_religion_filter', 'Islam');
        $paiRes->assertJsonPath('schedule.filter_source', 'BIODATA_AGAMA_SISWA');

        $paiStudents = collect($paiRes->json('data'));
        $this->assertGreaterThan(0, $paiStudents->count());
        $this->assertTrue($paiStudents->every(fn ($s) => $s['religion'] === 'Islam'));
        $this->assertTrue($paiStudents->every(fn ($s) => $s['class_id'] === $paiSchedule->class_id));

        // 2. Combined cross-class PAK schedule (X MIPA 1 + X MIPA 2) -> must ONLY return Christian students from both classes
        $pakSchedule = ClassSchedule::where('school_id', $school->id)
            ->where('subject_id', $pakSubject->id)
            ->where('is_combined', true)
            ->firstOrFail();

        $pakRes = $this->getJson("/api/v1/schedules/{$pakSchedule->id}/students");
        $pakRes->assertStatus(200);
        $pakRes->assertJsonPath('schedule.is_combined', true);
        $pakRes->assertJsonPath('schedule.effective_religion_filter', 'Kristen');
        $pakRes->assertJsonPath('schedule.filter_source', 'BIODATA_AGAMA_SISWA');

        $pakStudents = collect($pakRes->json('data'));
        $this->assertGreaterThanOrEqual(2, $pakStudents->count());
        $this->assertTrue($pakStudents->every(fn ($s) => $s['religion'] === 'Kristen'));
        $this->assertGreaterThanOrEqual(2, $pakStudents->pluck('class_id')->unique()->count());
    }

    public function test_combined_schedule_dedicated_matrix_table_and_teaching_group_management(): void
    {
        $school = School::first();
        $year = AcademicYear::first();
        $admin = User::whereIn('role', ['super_admin', 'school_admin', 'teacher'])->first()
            ?? User::factory()->create(['role' => 'school_admin']);

        $classes = SchoolClass::where('school_id', $school->id)->orderBy('id')->take(2)->get();
        $class1 = $classes[0];
        $class2 = $classes[1];

        // Create a manual cross-class elective group (e.g., Informatika Lintas Rombel)
        $pickedStudents = [
            Student::where('class_id', $class1->id)->firstOrFail()->id,
            Student::where('class_id', $class2->id)->firstOrFail()->id,
        ];

        $electiveTeacher = Teacher::create([
            'school_id' => $school->id,
            'nip' => '199105102022011009',
            'full_name' => 'Rizky Pratama',
            'title_suffix' => 'S.Kom.',
            'employment_status' => 'PNS',
            'major_study' => 'Informatika Pilihan',
            'is_active' => true,
        ]);

        $electiveRoom = SchoolRoom::create([
            'school_id' => $school->id,
            'code' => 'LAB-PIL-1',
            'name' => 'Lab Mapel Pilihan 1',
            'type' => 'LABORATORY',
            'capacity' => 36,
            'is_active' => true,
        ]);

        $electiveSubject = Subject::create([
            'school_id' => $school->id,
            'code' => 'INF-PIL',
            'name' => 'Informatika Lanjut (Pilihan)',
            'color' => '#0ea5e9',
            'category' => 'C_KEJURUAN_PEMINATAN',
            'is_active' => true,
        ]);

        $grpRes = $this->postJson('/api/v1/teaching-groups', [
            'school_id' => $school->id,
            'academic_year_id' => $year->id,
            'subject_id' => $electiveSubject->id,
            'teacher_id' => $electiveTeacher->id,
            'default_room_id' => $electiveRoom->id,
            'code' => 'PIL-INF-X',
            'name' => 'Kelompok Pilihan Informatika Kelas X',
            'membership_mode' => 'MANUAL',
            'class_ids' => [$class1->id, $class2->id],
            'student_ids' => $pickedStudents,
        ]);

        $grpRes->assertStatus(201);
        $groupId = $grpRes->json('data.id');

        $grpStudentsRes = $this->getJson("/api/v1/teaching-groups/{$groupId}/students");
        $grpStudentsRes->assertStatus(200);
        $grpStudentsRes->assertJsonPath('count', 2);

        // Schedule 2 consecutive periods on Rabu (day_of_week = 3) as a Combined Class
        $wedPeriods = LessonPeriod::where('school_id', $school->id)
            ->where('day_of_week', 3)
            ->where('is_break', false)
            ->orderBy('start_time')
            ->take(2)
            ->get();

        $combSchedRes = $this->postJson('/api/v1/schedules', [
            'school_id' => $school->id,
            'is_combined' => true,
            'teaching_group_id' => $groupId,
            'class_ids' => [$class1->id, $class2->id],
            'subject_id' => $electiveSubject->id,
            'teacher_id' => $electiveTeacher->id,
            'room_id' => $electiveRoom->id,
            'lesson_period_id' => $wedPeriods[0]->id,
            'end_lesson_period_id' => $wedPeriods[1]->id,
            'academic_year_id' => $year->id,
            'academic_semester' => 'odd',
        ]);

        $combSchedRes->assertStatus(201);
        $this->assertCount(2, $combSchedRes->json('items'));
        $firstCombSchedId = $combSchedRes->json('items.0.id');

        // Verify schedule participants endpoint returns the 2 selected cross-class students
        $partRes = $this->getJson("/api/v1/schedules/{$firstCombSchedId}/students");
        $partRes->assertStatus(200);
        $partRes->assertJsonPath('count', 2);
        $partRes->assertJsonPath('schedule.filter_source', 'KELOMPOK_MAPEL_PILIHAN');

        // Verify dedicated Combined Classes Matrix Table renders cleanly with rowspan="2" and participating class badges
        $viewRes = $this->actingAs($admin)->get("/admin/schedules?view=view-combined&teaching_group_id={$groupId}");
        $viewRes->assertStatus(200);
        $viewRes->assertSee('id="combinedMatrixTable"', false);
        $viewRes->assertSee('Tabel Kelas Gabungan & Kelompok Mapel', false);
        $viewRes->assertSee('Informatika Lanjut (Pilihan)', false);
        $viewRes->assertSee("{$class1->name}, {$class2->name}", false);
        $viewRes->assertSee('rowspan="2"', false);
    }
}
