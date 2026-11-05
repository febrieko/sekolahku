<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\SchoolGeofence;
use App\Models\Student;
use App\Models\User;
use Carbon\Carbon;
use Database\Seeders\SekolahkuAttendanceSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentAttendanceSystemTest extends TestCase
{
    use RefreshDatabase;

    protected User $studentUser;
    protected Student $student;
    protected School $school;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(SekolahkuAttendanceSeeder::class);

        $this->school = School::first();
        $this->student = Student::where('nis', '20261001')->first(); // Ahmad Fauzi

        $this->studentUser = User::create([
            'name' => 'Ahmad Fauzi',
            'email' => 'siswa@sekolahku.sch.id',
            'password' => bcrypt('password'),
            'role' => 'student',
            'school_id' => $this->school->id,
            'is_active' => true,
        ]);

        $this->student->update(['user_id' => $this->studentUser->id]);
    }

    public function test_student_portal_renders_personalized_student_card_and_hides_admin_tabs(): void
    {
        $response = $this->actingAs($this->studentUser)->get('/attendance');

        $response->assertStatus(200);
        // Student name and NIS are shown on card
        $response->assertSee('Ahmad Fauzi');
        $response->assertSee('20261001');
        $response->assertSee('Presensi Wajah & GPS Saya', false);
        $response->assertSee('Riwayat Kehadiran Saya');

        // Testing dropdown is hidden for logged-in students
        $response->assertDontSee('Pilih Akun Siswa (Untuk Pengujian)');

        // Staff tabs are hidden for students
        $response->assertDontSee('Rekap & Monitoring Guru', false);
        $response->assertDontSee('Peta Sebaran GPS Live');
        $response->assertDontSee('Konfigurasi Geofencing');
    }

    public function test_student_selfie_check_in_and_checkout_flow(): void
    {
        // Clear any attendance for this student today
        Attendance::where('student_id', $this->student->id)->where('date', Carbon::today()->toDateString())->delete();

        $fakeBase64 = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+A8AAQUBAScY42YAAAAASUVORK5CYII=';

        // 1. Check-in
        $response = $this->actingAs($this->studentUser)->postJson('/api/v1/attendances/selfie', [
            'selfie_image' => $fakeBase64,
            'latitude' => -6.166840,
            'longitude' => 106.837580,
            'gps_accuracy' => 5.0,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'data' => [
                'student_name' => 'Ahmad Fauzi',
                'is_within_radius' => true,
            ],
        ]);

        $attendance = Attendance::where('student_id', $this->student->id)->where('date', Carbon::today()->toDateString())->first();
        $this->assertNotNull($attendance);
        $this->assertNotNull($attendance->check_in_at);
        $this->assertNull($attendance->check_out_at);

        // 2. Check-out
        $checkoutResponse = $this->actingAs($this->studentUser)->postJson('/api/v1/attendances/checkout', []);
        $checkoutResponse->assertStatus(200);
        $checkoutResponse->assertJson(['success' => true]);

        $attendance->refresh();
        $this->assertNotNull($attendance->check_out_at);
    }

    public function test_student_cannot_check_in_for_another_student(): void
    {
        $otherStudent = Student::where('nis', '20261002')->first(); // Siti Nurhaliza
        Attendance::where('student_id', $otherStudent->id)->where('date', Carbon::today()->toDateString())->delete();
        Attendance::where('student_id', $this->student->id)->where('date', Carbon::today()->toDateString())->delete();

        $fakeBase64 = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+A8AAQUBAScY42YAAAAASUVORK5CYII=';

        // Attempting to send other student's id should be ignored/locked to authenticated student
        $response = $this->actingAs($this->studentUser)->postJson('/api/v1/attendances/selfie', [
            'student_id' => $otherStudent->id,
            'selfie_image' => $fakeBase64,
            'latitude' => -6.166840,
            'longitude' => 106.837580,
        ]);

        $response->assertStatus(200);

        // Verify that attendance is recorded for Ahmad Fauzi (the authenticated student), NOT Siti
        $myAttendance = Attendance::where('student_id', $this->student->id)->where('date', Carbon::today()->toDateString())->first();
        $this->assertNotNull($myAttendance);

        $otherAttendance = Attendance::where('student_id', $otherStudent->id)->where('date', Carbon::today()->toDateString())->first();
        $this->assertNull($otherAttendance);
    }

    public function test_student_attendance_history_scoping(): void
    {
        $response = $this->actingAs($this->studentUser)->getJson('/api/v1/attendances');
        $response->assertStatus(200);

        // Every item returned must belong to the logged-in student
        $data = $response->json('data');
        foreach ($data as $item) {
            $this->assertEquals($this->student->id, $item['student_id']);
        }
    }
}
