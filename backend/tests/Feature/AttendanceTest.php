<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\Student;
use App\Models\User;
use App\Services\GeofenceService;
use Carbon\Carbon;
use Database\Seeders\SekolahkuAttendanceSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttendanceTest extends TestCase
{
    use RefreshDatabase;

    protected GeofenceService $geofenceService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(SekolahkuAttendanceSeeder::class);
        $this->geofenceService = new GeofenceService();
    }

    public function test_haversine_distance_calculation(): void
    {
        // Coordinates for SMAN 1 Jakarta (-6.166827, 106.837562)
        // Point nearby (~25 meters away): -6.166840, 106.837580
        $dist = $this->geofenceService->calculateDistance(-6.166827, 106.837562, -6.166840, 106.837580);
        $this->assertGreaterThan(0, $dist);
        $this->assertLessThan(50, $dist);
    }

    public function test_selfie_attendance_within_geofence_succeeds(): void
    {
        $student = Student::where('nis', '20261003')->first(); // Budi Santoso
        $this->assertNotNull($student);

        // Delete any today record if exists
        Attendance::where('student_id', $student->id)->where('date', Carbon::today()->toDateString())->delete();

        // 1x1 transparent PNG base64 for test
        $fakeBase64 = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+A8AAQUBAScY42YAAAAASUVORK5CYII=';

        $response = $this->postJson('/api/v1/attendances/selfie', [
            'student_id' => $student->id,
            'selfie_image' => $fakeBase64,
            'latitude' => -6.166840,
            'longitude' => 106.837580,
            'gps_accuracy' => 10.0,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'data' => [
                'student_name' => 'Budi Santoso',
                'is_within_radius' => true,
            ],
        ]);

        $savedAttendance = Attendance::where('student_id', $student->id)->first();
        $this->assertNotNull($savedAttendance);
        $this->assertTrue((bool) $savedAttendance->is_within_radius);
    }

    public function test_selfie_attendance_outside_geofence_is_rejected(): void
    {
        $student = Student::where('nis', '20261006')->first(); // Farhan
        $this->assertNotNull($student);

        Attendance::where('student_id', $student->id)->where('date', Carbon::today()->toDateString())->delete();

        $fakeBase64 = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+A8AAQUBAScY42YAAAAASUVORK5CYII=';

        // Coordinates in Monas (~1.5 km away from school)
        $response = $this->postJson('/api/v1/attendances/selfie', [
            'student_id' => $student->id,
            'selfie_image' => $fakeBase64,
            'latitude' => -6.175392,
            'longitude' => 106.827153,
            'gps_accuracy' => 15.0,
        ]);

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
            'error' => [
                'code' => 'OUTSIDE_GEOFENCE',
            ],
        ]);
    }

    public function test_duplicate_checkin_is_rejected(): void
    {
        $student = Student::where('nis', '20261001')->first(); // Ahmad Fauzi (already checked in via seeder)
        $this->assertNotNull($student);

        $fakeBase64 = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+A8AAQUBAScY42YAAAAASUVORK5CYII=';

        $response = $this->postJson('/api/v1/attendances/selfie', [
            'student_id' => $student->id,
            'selfie_image' => $fakeBase64,
            'latitude' => -6.166840,
            'longitude' => 106.837580,
        ]);

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
            'error' => [
                'code' => 'ALREADY_CHECKED_IN',
            ],
        ]);
    }

    public function test_checkout_attendance(): void
    {
        $student = Student::where('nis', '20261001')->first();

        $response = $this->postJson('/api/v1/attendances/checkout', [
            'student_id' => $student->id,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);

        $this->assertNotNull(Attendance::where('student_id', $student->id)->first()->check_out_at);
    }

    public function test_manual_attendance_by_teacher(): void
    {
        $student = Student::where('nis', '20261006')->first(); // Farhan
        $teacher = User::where('role', 'teacher')->first();

        $response = $this->actingAs($teacher)->postJson('/api/v1/attendances', [
            'student_id' => $student->id,
            'date' => Carbon::today()->toDateString(),
            'status' => 'sick',
            'notes' => 'Surat dokter terverifikasi',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'data' => [
                'status' => 'sick',
                'method' => 'manual',
            ],
        ]);
    }

    public function test_qr_scan_attendance(): void
    {
        $student = Student::where('nis', '20261006')->first(); // Farhan
        Attendance::where('student_id', $student->id)->delete();

        $response = $this->postJson('/api/v1/attendances/qr-scan', [
            'nis' => $student->nis,
            'school_id' => $student->school_id,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'data' => [
                'student_name' => $student->full_name,
                'nis' => $student->nis,
            ],
        ]);
    }

    public function test_attendance_summary_endpoint(): void
    {
        $response = $this->getJson('/api/v1/attendances/today');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'data' => [
                'date',
                'total_students',
                'present',
                'late',
                'sick',
                'excused',
                'absent',
                'attendance_percentage',
            ],
        ]);
    }

    public function test_attendance_web_portal_renders(): void
    {
        $response = $this->get('/attendance');
        $response->assertStatus(200);
        $response->assertSee('Modul Presensi Siswa');
        $response->assertSee('SMA Negeri 1 Jakarta');
    }
}
