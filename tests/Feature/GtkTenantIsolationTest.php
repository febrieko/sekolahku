<?php

namespace Tests\Feature;

use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GtkTenantIsolationTest extends TestCase
{
    use RefreshDatabase;

    protected School $schoolA;
    protected School $schoolB;
    protected User $teacherA;
    protected User $wakaKesiswaanA;
    protected User $superAdmin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->schoolA = School::create([
            'name' => 'SMA Negeri 1 Jakarta',
            'npsn' => '20100001',
            'level' => 'SMA',
            'tenant_id' => 'sman1jkt',
            'is_active' => true,
        ]);

        $this->schoolB = School::create([
            'name' => 'SMK Negeri 2 Bandung',
            'npsn' => '20200002',
            'level' => 'SMK',
            'tenant_id' => 'smkn2bdg',
            'is_active' => true,
        ]);

        $this->teacherA = User::create([
            'name' => 'Guru Budi Santoso',
            'email' => 'budi.guru@sman1jkt.sch.id',
            'password' => bcrypt('password'),
            'school_id' => $this->schoolA->id,
            'role' => 'teacher',
            'is_active' => true,
        ]);

        $this->wakaKesiswaanA = User::create([
            'name' => 'Waka Kesiswaan Rudi',
            'email' => 'rudi.kesiswaan@sman1jkt.sch.id',
            'password' => bcrypt('password'),
            'school_id' => $this->schoolA->id,
            'role' => 'waka_kesiswaan',
            'is_active' => true,
        ]);

        $this->superAdmin = User::create([
            'name' => 'Super Administrator',
            'email' => 'superadmin@sekolahku.id',
            'password' => bcrypt('password'),
            'school_id' => $this->schoolA->id,
            'role' => 'super_admin',
            'is_active' => true,
        ]);
    }

    public function test_gtk_is_strictly_locked_to_their_school_and_cannot_access_other_schools_via_query_param(): void
    {
        // Guru dari Sekolah A mencoba inject ?school_id=2 (Sekolah B)
        $response = $this->actingAs($this->teacherA)->get('/admin/data-master?school_id=' . $this->schoolB->id);

        $response->assertStatus(200);
        // Harus tetap merender Sekolah A milik sang Guru
        $response->assertSee('SMA Negeri 1 Jakarta');
        $response->assertSee('Unit Tugas Resmi Anda (Terkunci)', false);
        // Tidak boleh menampilkan dropdown ganti unit pendidikan
        $response->assertDontSee('Ganti Unit Pendidikan');
    }

    public function test_gtk_cannot_switch_school_via_switch_endpoint(): void
    {
        // Guru mencoba switch ke Sekolah B
        $response = $this->actingAs($this->teacherA)->get('/admin/switch-school/' . $this->schoolB->id);

        $response->assertStatus(302);
        $response->assertSessionHas('error', 'Akses Ditolak: Anda hanya berwenang mengelola data pada satuan pendidikan tempat Anda bertugas.');

        // Session tetap tidak berubah
        $this->assertNotEquals($this->schoolB->id, session('active_school_id'));
    }

    public function test_waka_kesiswaan_is_locked_to_assigned_school_in_tatib(): void
    {
        $response = $this->actingAs($this->wakaKesiswaanA)->get('/admin/kesiswaan/tatib?school_id=' . $this->schoolB->id);

        $response->assertStatus(200);
        $response->assertSee('SMA Negeri 1 Jakarta');
        $response->assertDontSee('Ganti Unit Pendidikan');
    }

    public function test_super_admin_can_freely_switch_between_schools(): void
    {
        $response = $this->actingAs($this->superAdmin)->get('/admin/switch-school/' . $this->schoolB->id);

        $response->assertStatus(302);
        $response->assertSessionHas('success');
        $this->assertEquals($this->schoolB->id, session('active_school_id'));
    }
}

