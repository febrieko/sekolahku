<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\ClassSchedule;
use App\Models\Department;
use App\Models\LessonPeriod;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\TeachingJournal;
use App\Models\TimeScheduleCategory;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeachingJournalModuleTest extends TestCase
{
    use RefreshDatabase;

    protected School $school;
    protected AcademicYear $academicYear;
    protected Teacher $teacher;
    protected User $teacherUser;
    protected User $wakaUser;
    protected SchoolClass $class;
    protected Subject $subject;
    protected Student $student1;
    protected Student $student2;
    protected ClassSchedule $schedule;

    protected function setUp(): void
    {
        parent::setUp();

        $this->school = School::create([
            'name' => 'SMA Negeri 1 Jakarta',
            'npsn' => '20100001',
            'level' => 'SMA',
            'tenant_id' => 'sman1jkt',
            'is_active' => true,
        ]);

        $this->academicYear = AcademicYear::create([
            'school_id' => $this->school->id,
            'name' => '2026/2027',
            'start_date' => '2026-07-15',
            'end_date' => '2027-06-20',
            'is_active' => true,
        ]);

        $this->teacherUser = User::create([
            'name' => 'Budi Santoso, M.Pd.',
            'email' => 'budi.guru@sman1jkt.sch.id',
            'password' => bcrypt('password'),
            'school_id' => $this->school->id,
            'role' => 'teacher',
            'is_active' => true,
        ]);

        $this->teacher = Teacher::create([
            'school_id' => $this->school->id,
            'user_id' => $this->teacherUser->id,
            'nip' => '198501012010011001',
            'full_name' => 'Budi Santoso',
            'title_suffix' => 'M.Pd.',
            'gender' => 'L',
            'email' => 'budi.guru@sman1jkt.sch.id',
            'is_active' => true,
        ]);

        $this->wakaUser = User::create([
            'name' => 'Waka Kurikulum Hendra',
            'email' => 'hendra.waka@sman1jkt.sch.id',
            'password' => bcrypt('password'),
            'school_id' => $this->school->id,
            'role' => 'waka_kurikulum',
            'is_active' => true,
        ]);

        $dept = Department::create([
            'school_id' => $this->school->id,
            'code' => 'MIPA',
            'name' => 'Matematika dan Ilmu Pengetahuan Alam',
        ]);

        $this->class = SchoolClass::create([
            'school_id' => $this->school->id,
            'department_id' => $dept->id,
            'name' => 'X-MIPA-1',
            'grade_level' => 10,
            'academic_year' => '2026/2027',
        ]);

        $this->subject = Subject::create([
            'school_id' => $this->school->id,
            'code' => 'FIS-10',
            'name' => 'Fisika Dasar',
            'category' => 'A_MUATAN_NASIONAL',
        ]);

        $this->student1 = Student::create([
            'school_id' => $this->school->id,
            'class_id' => $this->class->id,
            'nis' => '20261001',
            'nisn' => '0081234561',
            'full_name' => 'Ahmad Fauzi',
            'gender' => 'L',
            'status' => 'active',
        ]);

        $this->student2 = Student::create([
            'school_id' => $this->school->id,
            'class_id' => $this->class->id,
            'nis' => '20261002',
            'nisn' => '0081234562',
            'full_name' => 'Siti Nurhaliza',
            'gender' => 'P',
            'status' => 'active',
        ]);

        $category = TimeScheduleCategory::create([
            'school_id' => $this->school->id,
            'name' => 'Waktu Pembelajaran Normal',
            'code' => 'NORMAL',
            'is_default' => true,
        ]);

        $dayOfWeek = Carbon::now()->dayOfWeekIso;

        $period = LessonPeriod::create([
            'school_id' => $this->school->id,
            'category_id' => $category->id,
            'day_of_week' => $dayOfWeek,
            'name' => 'Jam Ke-1',
            'period_number' => 1,
            'start_time' => '07:00:00',
            'end_time' => '07:45:00',
            'is_break' => false,
        ]);

        $this->schedule = ClassSchedule::create([
            'school_id' => $this->school->id,
            'academic_year_id' => $this->academicYear->id,
            'academic_semester' => 'odd',
            'class_id' => $this->class->id,
            'subject_id' => $this->subject->id,
            'teacher_id' => $this->teacher->id,
            'lesson_period_id' => $period->id,
        ]);
    }

    public function test_teacher_can_view_teaching_journal_dashboard_and_today_schedules(): void
    {
        $response = $this->actingAs($this->teacherUser)->get('/admin/kurikulum/jurnal');

        $response->assertStatus(200);
        $response->assertSee('Jurnal Mengajar KBM & Agenda Guru');
        $response->assertSee('Fisika Dasar');
        $response->assertSee('X-MIPA-1');
        $response->assertSee('Buka Sesi KBM Sekarang');
    }

    public function test_teacher_can_open_kbm_session_and_sync_baseline_student_attendances(): void
    {
        // Simulasi siswa 2 sakit di presensi pagi
        Attendance::create([
            'school_id' => $this->school->id,
            'student_id' => $this->student2->id,
            'date' => Carbon::today()->toDateString(),
            'status' => 'sick',
            'method' => 'manual',
            'notes' => 'Surat dokter',
        ]);

        $response = $this->actingAs($this->teacherUser)->post('/admin/kurikulum/jurnal/open', [
            'class_schedule_id' => $this->schedule->id,
            'date' => Carbon::today()->toDateString(),
        ]);

        $response->assertStatus(302);

        $journal = TeachingJournal::first();
        $this->assertNotNull($journal);
        $this->assertEquals(1, $journal->meeting_number);
        $this->assertEquals('in_progress', $journal->status);

        // Siswa 1 default Hadir, Siswa 2 sinkron Sakit
        $this->assertEquals(2, $journal->attendances()->count());
        $att1 = $journal->attendances()->where('student_id', $this->student1->id)->first();
        $att2 = $journal->attendances()->where('student_id', $this->student2->id)->first();

        $this->assertEquals('present', $att1->status);
        $this->assertEquals('sick', $att2->status);

        // Rekap summary terhitung otomatis
        $this->assertEquals(1, $journal->attendance_summary['present']);
        $this->assertEquals(1, $journal->attendance_summary['sick']);
    }

    public function test_teacher_can_update_learning_objectives_and_complete_kbm_session(): void
    {
        $journal = TeachingJournal::create([
            'school_id' => $this->school->id,
            'academic_year_id' => $this->academicYear->id,
            'academic_semester' => 'odd',
            'class_schedule_id' => $this->schedule->id,
            'teacher_id' => $this->teacher->id,
            'school_class_id' => $this->class->id,
            'subject_id' => $this->subject->id,
            'date' => Carbon::today()->toDateString(),
            'meeting_number' => 1,
            'status' => 'in_progress',
        ]);

        $journal->attendances()->create([
            'student_id' => $this->student1->id,
            'status' => 'present',
        ]);
        $journal->attendances()->create([
            'student_id' => $this->student2->id,
            'status' => 'present',
        ]);

        // Guru mengisi jurnal dan mengunci sesi KBM
        $response = $this->actingAs($this->teacherUser)->put('/admin/kurikulum/jurnal/' . $journal->id, [
            'meeting_number' => 1,
            'learning_objective' => 'Hukum Newton I, II, dan III tentang Gerak',
            'teaching_activity' => 'Praktikum dinamika gerak menggunakan neraca pegas dan bidang miring',
            'obstacles_and_followup' => 'Semua alat bekerja baik',
            'homework_assignment' => 'Laporan praktikum dikumpulkan minggu depan',
            'action' => 'complete',
            'attendances' => [
                $this->student1->id => ['status' => 'present', 'notes' => 'Aktif bertanya'],
                $this->student2->id => ['status' => 'excused', 'notes' => 'Izin ke UKS jam ke-2'],
            ],
        ]);

        $response->assertStatus(302);

        $journal->refresh();
        $this->assertEquals('completed', $journal->status);
        $this->assertNotNull($journal->completed_at);
        $this->assertEquals('Hukum Newton I, II, dan III tentang Gerak', $journal->learning_objective);
        $this->assertEquals(1, $journal->attendance_summary['present']);
        $this->assertEquals(1, $journal->attendance_summary['excused']);
    }

    public function test_waka_kurikulum_can_verify_teaching_journal(): void
    {
        $journal = TeachingJournal::create([
            'school_id' => $this->school->id,
            'academic_year_id' => $this->academicYear->id,
            'academic_semester' => 'odd',
            'class_schedule_id' => $this->schedule->id,
            'teacher_id' => $this->teacher->id,
            'school_class_id' => $this->class->id,
            'subject_id' => $this->subject->id,
            'date' => Carbon::today()->toDateString(),
            'meeting_number' => 1,
            'status' => 'completed',
            'completed_at' => Carbon::now(),
        ]);

        $response = $this->actingAs($this->wakaUser)->postJson('/admin/kurikulum/jurnal/' . $journal->id . '/verify', [
            'notes' => 'Materi telah sesuai dengan silabus kurikulum merdeka.',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $journal->refresh();
        $this->assertNotNull($journal->verified_by_waka_at);
        $this->assertEquals($this->wakaUser->id, $journal->verified_by_waka_id);
    }

    public function test_teacher_can_generate_and_view_printable_buku_kerja_guru_report(): void
    {
        TeachingJournal::create([
            'school_id' => $this->school->id,
            'academic_year_id' => $this->academicYear->id,
            'academic_semester' => 'odd',
            'class_schedule_id' => $this->schedule->id,
            'teacher_id' => $this->teacher->id,
            'school_class_id' => $this->class->id,
            'subject_id' => $this->subject->id,
            'date' => Carbon::today()->toDateString(),
            'start_period' => 1,
            'end_period' => 2,
            'meeting_number' => 1,
            'learning_objective' => 'Pengukuran Besaran Pokok dan Turunan',
            'status' => 'completed',
            'completed_at' => Carbon::now(),
            'attendance_summary' => ['present' => 2, 'sick' => 0, 'excused' => 0, 'absent' => 0],
        ]);

        $response = $this->actingAs($this->teacherUser)->get('/admin/kurikulum/jurnal-laporan?teacher_id=' . $this->teacher->id);

        $response->assertStatus(200);
        $response->assertSee('JURNAL AGENDA MENGAJAR GURU');
        $response->assertSee('Budi Santoso');
        $response->assertSee('Pengukuran Besaran Pokok dan Turunan');
        $response->assertSee('Kepala Sekolah');
        $response->assertSee('Guru Mata Pelajaran');
    }
}
