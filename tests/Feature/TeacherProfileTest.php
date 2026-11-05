<?php

namespace Tests\Feature;

use App\Models\School;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TeacherProfileTest extends TestCase
{
    use RefreshDatabase;

    protected School $school;
    protected User $adminUser;
    protected User $teacherUser;
    protected Teacher $teacher;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        $this->school = School::create([
            'name' => 'SMAN 1 Antigravity',
            'npsn' => '20101589',
            'level' => 'SMA',
            'tenant_id' => 'sman1-anti',
            'is_active' => true,
            'gtk_profile_edit_enabled' => true,
            'gtk_profile_edit_start' => now()->subDay()->toDateString(),
            'gtk_profile_edit_end' => now()->addDays(14)->toDateString(),
        ]);

        $this->adminUser = User::factory()->create([
            'name' => 'Admin Sekolah',
            'email' => 'admin@sekolah.sch.id',
            'role' => 'school_admin',
            'school_id' => $this->school->id,
            'is_active' => true,
        ]);

        $this->teacherUser = User::factory()->create([
            'name' => 'Budi Santoso, M.Pd.',
            'email' => 'budi@sekolah.sch.id',
            'role' => 'teacher',
            'school_id' => $this->school->id,
            'is_active' => true,
        ]);

        $this->teacher = Teacher::create([
            'school_id' => $this->school->id,
            'user_id' => $this->teacherUser->id,
            'full_name' => 'Budi Santoso',
            'title_suffix' => 'M.Pd.',
            'gender' => 'L',
            'email' => 'budi@sekolah.sch.id',
            'phone' => '081234567890',
            'employment_type' => 'GURU_MAPEL',
            'employment_status' => 'PNS',
            'nip' => '198501012010011001',
            'nuptk' => '1234567890123456',
            'nik' => '3171010101850001',
            'is_active' => true,
        ]);
    }

    public function test_teacher_can_view_own_profile_page(): void
    {
        $response = $this->actingAs($this->teacherUser)
            ->withSession(['active_school_id' => $this->school->id])
            ->get(route('teacher.profile'));

        $response->assertStatus(200);
        $response->assertSee('Budi Santoso, M.Pd.');
        $response->assertSee('198501012010011001');
        $response->assertSee('Periode Edit Mandiri Terbuka');
    }

    public function test_admin_can_view_any_teacher_profile(): void
    {
        $response = $this->actingAs($this->adminUser)
            ->withSession(['active_school_id' => $this->school->id])
            ->get(route('teacher.profile', ['teacher_id' => $this->teacher->id]));

        $response->assertStatus(200);
        $response->assertSee('Atur Jadwal Durasi Edit');
        $response->assertSee('Budi Santoso, M.Pd.');
    }

    public function test_teacher_can_update_profile_and_upload_4x6_photo(): void
    {
        $fakePhoto = UploadedFile::fake()->image('pas_foto_4x6.jpg', 400, 600);

        $response = $this->actingAs($this->teacherUser)
            ->withSession(['active_school_id' => $this->school->id])
            ->put(route('teacher.profile.update', $this->teacher->id), [
                'full_name' => 'Budi Santoso',
                'title_prefix' => 'Drs.',
                'title_suffix' => 'M.Pd.',
                'gender' => 'L',
                'nik' => '3171010101850001',
                'nip' => '198501012010011001',
                'nuptk' => '1234567890123456',
                'birth_place' => 'Jakarta',
                'birth_date' => '1985-01-01',
                'religion' => 'Islam',
                'marital_status' => 'Kawin',
                'phone' => '081299998888',
                'email' => 'budi.santoso@sekolah.sch.id',
                'last_education' => 'S2',
                'major_study' => 'Pendidikan Matematika',
                'university_name' => 'Universitas Negeri Jakarta',
                'is_certified' => '1',
                'certification_number' => '12345/CERT/2018',
                'nrg' => '987654321',
                'npwp' => '012345678901000',
                'bank_name' => 'Bank Mandiri',
                'bank_account_number' => '1230009876543',
                'bank_account_name' => 'Budi Santoso',
                'additional_duties' => 'Waka Kurikulum, Kepala Laboratorium',
                'teaching_hours' => 28,
                'photo' => $fakePhoto,
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->teacher->refresh();
        $this->assertEquals('Drs.', $this->teacher->title_prefix);
        $this->assertEquals('Bank Mandiri', $this->teacher->bank_name);
        $this->assertEquals(28, $this->teacher->teaching_hours);
        $this->assertTrue($this->teacher->is_certified);
        $this->assertNotNull($this->teacher->photo_path);

        Storage::disk('public')->assertExists($this->teacher->photo_path);
    }

    public function test_teacher_cannot_update_when_edit_window_is_closed(): void
    {
        // Tutup periode edit mandiri oleh sekolah
        $this->school->update([
            'gtk_profile_edit_enabled' => false,
        ]);

        $response = $this->actingAs($this->teacherUser)
            ->withSession(['active_school_id' => $this->school->id])
            ->put(route('teacher.profile.update', $this->teacher->id), [
                'full_name' => 'Budi Santoso Perubahan Ilegal',
                'gender' => 'L',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');

        $this->teacher->refresh();
        $this->assertEquals('Budi Santoso', $this->teacher->full_name);
    }

    public function test_admin_can_override_and_update_even_when_window_is_closed_for_teachers(): void
    {
        // Tutup periode edit mandiri
        $this->school->update([
            'gtk_profile_edit_enabled' => false,
        ]);

        $response = $this->actingAs($this->adminUser)
            ->withSession(['active_school_id' => $this->school->id])
            ->put(route('teacher.profile.update', $this->teacher->id), [
                'full_name' => 'Budi Santoso Diperbarui Admin',
                'gender' => 'L',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->teacher->refresh();
        $this->assertEquals('Budi Santoso Diperbarui Admin', $this->teacher->full_name);
    }

    public function test_admin_can_configure_edit_window_duration(): void
    {
        $startDate = now()->addDays(1)->toDateString();
        $endDate = now()->addDays(20)->toDateString();

        $response = $this->actingAs($this->adminUser)
            ->withSession(['active_school_id' => $this->school->id])
            ->post(route('admin.gtk.profile.window'), [
                'school_id' => $this->school->id,
                'gtk_profile_edit_enabled' => '1',
                'gtk_profile_edit_start' => $startDate,
                'gtk_profile_edit_end' => $endDate,
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->school->refresh();
        $this->assertTrue($this->school->gtk_profile_edit_enabled);
        $this->assertEquals($startDate, $this->school->gtk_profile_edit_start->toDateString());
        $this->assertEquals($endDate, $this->school->gtk_profile_edit_end->toDateString());
    }
}

