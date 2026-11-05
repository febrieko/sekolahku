<?php

namespace Tests\Feature;

use App\Models\School;
use App\Models\Student;
use App\Models\User;
use Database\Seeders\SekolahkuAttendanceSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class StudentProfileTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(SekolahkuAttendanceSeeder::class);
        Storage::fake('public');
    }

    public function test_student_can_view_profile_page(): void
    {
        $studentUser = User::where('role', 'student')->first();
        $this->assertNotNull($studentUser);

        $response = $this->actingAs($studentUser)->get('/student/profile');
        $response->assertStatus(200);
        $response->assertSee('Peserta Didik (Dapodik)');
        $response->assertSee('Pasfoto Resmi 4 x 6 cm');
        $response->assertSee('Ambil Titik Rumah dari GPS Perangkat');
    }

    public function test_student_can_update_profile_and_upload_4x6_photo(): void
    {
        $student = Student::where('nis', '20261001')->first();
        $this->assertNotNull($student);
        $studentUser = User::find($student->user_id);

        $fakePhoto = UploadedFile::fake()->image('pasfoto_4x6.jpg', 600, 900); // 4x6 aspect ratio

        $payload = [
            'full_name' => 'Ahmad Fauzi Updated',
            'gender' => 'L',
            'birth_place' => 'Jakarta',
            'birth_date' => '2010-05-15',
            'nik' => '3171012345670001',
            'no_kk' => '3171012304050001',
            'birth_certificate_no' => '12345/DIS/2010',
            'religion' => 'Islam',
            'student_phone' => '081234567890',
            'parent_name' => 'H. Hendra Wijaya',
            'parent_phone' => '081388881111',
            'address' => 'Jl. Kebon Sirih No. 10',
            'rt' => '05',
            'rw' => '02',
            'sub_district' => 'Gambir',
            'district' => 'Gambir',
            'city' => 'Jakarta Pusat',
            'province' => 'DKI Jakarta',
            'postal_code' => '10110',
            'home_latitude' => -6.1805000,
            'home_longitude' => 106.8283000,
            'residence_type' => 'Bersama Orang Tua',
            'transportation_mode' => 'Kendaraan Pribadi',
            'previous_school' => 'SMP Negeri 1 Jakarta',
            'height_cm' => 170,
            'weight_kg' => 60,
            'head_circumference_cm' => 55,
            'blood_type' => 'O',
            'photo' => $fakePhoto,
        ];

        $response = $this->actingAs($studentUser)->post('/student/profile', $payload);
        $response->assertStatus(302);
        $response->assertSessionHas('success');

        $student->refresh();
        $this->assertEquals('Ahmad Fauzi Updated', $student->full_name);
        $this->assertEquals('081234567890', $student->student_phone);
        $this->assertEquals('SMP Negeri 1 Jakarta', $student->previous_school);
        $this->assertEquals(-6.1805000, $student->home_latitude);
        $this->assertEquals(106.8283000, $student->home_longitude);
        $this->assertNotNull($student->distance_to_school_km);
        $this->assertNotNull($student->photo_url);
        $this->assertStringContainsString('/storage/student_photos/', $student->photo_url);
    }

    public function test_student_cannot_update_profile_when_edit_window_is_closed(): void
    {
        $school = School::first();
        $school->update([
            'student_profile_edit_enabled' => false,
        ]);

        $student = Student::where('nis', '20261001')->first();
        $studentUser = User::find($student->user_id);

        $response = $this->actingAs($studentUser)->post('/student/profile', [
            'full_name' => 'Should Not Update',
            'gender' => 'L',
            'birth_date' => '2010-05-15',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHas('error');

        $student->refresh();
        $this->assertNotEquals('Should Not Update', $student->full_name);
    }

    public function test_admin_can_update_profile_edit_window_settings(): void
    {
        $school = School::first();

        $response = $this->postJson("/api/v1/schools/{$school->id}/profile-edit-window", [
            'enabled' => true,
            'start_date' => '2026-10-01',
            'end_date' => '2026-11-30',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'data' => [
                'enabled' => true,
                'start_date' => '2026-10-01',
                'end_date' => '2026-11-30',
            ],
        ]);

        $school->refresh();
        $this->assertTrue($school->student_profile_edit_enabled);
        $this->assertEquals('2026-10-01', $school->student_profile_edit_start->toDateString());
        $this->assertEquals('2026-11-30', $school->student_profile_edit_end->toDateString());
    }
}
