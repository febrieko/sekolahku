<?php

namespace Tests\Feature;

use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    protected School $schoolA;
    protected School $schoolB;
    protected User $superAdmin;
    protected User $schoolAdmin;
    protected User $teacher;
    protected User $student;

    protected function setUp(): void
    {
        parent::setUp();

        $this->schoolA = School::create([
            'name' => 'SMAN 1 Antigravity',
            'npsn' => '20100001',
            'level' => 'SMA',
            'tenant_id' => 'sman1-anti',
            'is_active' => true,
        ]);

        $this->schoolB = School::create([
            'name' => 'SMKN 2 Horizon',
            'npsn' => '20200002',
            'level' => 'SMK',
            'tenant_id' => 'smkn2-horiz',
            'is_active' => true,
        ]);

        $this->superAdmin = User::factory()->create([
            'name' => 'Pusat Super Admin',
            'email' => 'superadmin@pusat.test',
            'role' => 'super_admin',
            'school_id' => null,
            'is_active' => true,
        ]);

        $this->schoolAdmin = User::factory()->create([
            'name' => 'Admin TU SMAN 1',
            'email' => 'admin@sman1.test',
            'role' => 'school_admin',
            'school_id' => $this->schoolA->id,
            'is_active' => true,
        ]);

        $this->teacher = User::factory()->create([
            'name' => 'Budi Santoso, S.Pd.',
            'email' => 'budi@sman1.test',
            'role' => 'teacher',
            'school_id' => $this->schoolA->id,
            'is_active' => true,
        ]);

        $this->student = User::factory()->create([
            'name' => 'Dinda Putri',
            'email' => 'dinda@sman1.test',
            'role' => 'student',
            'school_id' => $this->schoolA->id,
            'is_active' => true,
        ]);
    }

    public function test_super_admin_can_access_user_management_and_see_all_schools(): void
    {
        $response = $this->actingAs($this->superAdmin)->get(route('admin.users.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.users.index');
        $response->assertSee('Manajemen Pengguna &amp; Hak Akses (RBAC)', false);
        $response->assertSee('Pusat Super Admin');
        $response->assertSee('Budi Santoso, S.Pd.');
    }

    public function test_school_admin_can_access_user_management_scoped_to_their_school(): void
    {
        $userOtherSchool = User::factory()->create([
            'name' => 'Guru SMKN 2',
            'email' => 'guru@smkn2.test',
            'role' => 'teacher',
            'school_id' => $this->schoolB->id,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->schoolAdmin)->get(route('admin.users.index'));

        $response->assertStatus(200);
        $response->assertSee('Budi Santoso, S.Pd.');
        $response->assertDontSee('Guru SMKN 2');
    }

    public function test_non_admin_cannot_access_user_management(): void
    {
        // Teacher
        $responseTeacher = $this->actingAs($this->teacher)->get(route('admin.users.index'));
        $responseTeacher->assertStatus(403);

        // Student
        $responseStudent = $this->actingAs($this->student)->get(route('admin.users.index'));
        $responseStudent->assertStatus(403);
    }

    public function test_admin_can_filter_by_user_category(): void
    {
        $response = $this->actingAs($this->superAdmin)->get(route('admin.users.index', ['category' => 'teacher']));

        $response->assertStatus(200);
        $response->assertSee('Budi Santoso, S.Pd.');
        // student should not be in the teacher category view
        $response->assertDontSee('Dinda Putri');
    }

    public function test_super_admin_can_create_a_new_user(): void
    {
        $payload = [
            'name' => 'Siti Nurhaliza, M.Pd.',
            'email' => 'siti.nur@sman1.test',
            'phone' => '081299887766',
            'role' => 'teacher',
            'additional_role' => 'waka_kurikulum',
            'school_id' => $this->schoolA->id,
            'password' => 'secret123',
            'is_active' => '1',
        ];

        $response = $this->actingAs($this->superAdmin)->post(route('admin.users.store'), $payload);

        $response->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseHas('users', [
            'name' => 'Siti Nurhaliza, M.Pd.',
            'email' => 'siti.nur@sman1.test',
            'role' => 'teacher',
            'additional_role' => 'waka_kurikulum',
            'school_id' => $this->schoolA->id,
            'is_active' => 1,
        ]);

        $createdUser = User::where('email', 'siti.nur@sman1.test')->first();
        $this->assertTrue(Hash::check('secret123', $createdUser->password));
        $this->assertTrue($createdUser->isWaka());
        $this->assertTrue($createdUser->isTeacher());
    }

    public function test_can_create_users_with_new_roles(): void
    {
        $rolesToTest = ['principal', 'staff', 'counselor', 'treasurer'];

        foreach ($rolesToTest as $role) {
            $payload = [
                'name' => 'User ' . ucfirst($role),
                'email' => "{$role}@sman1.test",
                'role' => $role,
                'school_id' => $this->schoolA->id,
                'password' => 'secret123',
                'is_active' => '1',
            ];

            $response = $this->actingAs($this->superAdmin)->post(route('admin.users.store'), $payload);
            $response->assertRedirect(route('admin.users.index'));

            $this->assertDatabaseHas('users', [
                'email' => "{$role}@sman1.test",
                'role' => $role,
                'school_id' => $this->schoolA->id,
            ]);
        }
    }

    public function test_school_admin_creating_user_is_locked_to_their_own_school(): void
    {
        $payload = [
            'name' => 'Ahmad Guru Baru',
            'email' => 'ahmad.guru@sman1.test',
            'phone' => '0811223344',
            'role' => 'teacher',
            'school_id' => $this->schoolB->id, // Attempting to assign to school B
            'password' => 'password123',
            'is_active' => '1',
        ];

        $response = $this->actingAs($this->schoolAdmin)->post(route('admin.users.store'), $payload);

        $response->assertRedirect(route('admin.users.index'));
        // Must be locked to schoolA, not schoolB
        $this->assertDatabaseHas('users', [
            'email' => 'ahmad.guru@sman1.test',
            'school_id' => $this->schoolA->id,
        ]);
    }

    public function test_admin_can_update_user_details(): void
    {
        $payload = [
            'name' => 'Budi Santoso, S.Pd., M.Kom.',
            'email' => 'budi.santoso@sman1.test',
            'phone' => '08123456789',
            'role' => 'teacher',
            'school_id' => $this->schoolA->id,
            'password' => 'newpassword123',
            'is_active' => '1',
        ];

        $response = $this->actingAs($this->superAdmin)->put(route('admin.users.update', $this->teacher->id), $payload);

        $response->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseHas('users', [
            'id' => $this->teacher->id,
            'name' => 'Budi Santoso, S.Pd., M.Kom.',
            'email' => 'budi.santoso@sman1.test',
        ]);

        $this->teacher->refresh();
        $this->assertTrue(Hash::check('newpassword123', $this->teacher->password));
    }

    public function test_admin_can_toggle_user_status(): void
    {
        $this->assertTrue($this->teacher->is_active);

        // Disable
        $response = $this->actingAs($this->superAdmin)->patch(route('admin.users.toggle-status', $this->teacher->id));
        $response->assertRedirect(route('admin.users.index'));
        $this->teacher->refresh();
        $this->assertFalse($this->teacher->is_active);

        // Enable again
        $this->actingAs($this->superAdmin)->patch(route('admin.users.toggle-status', $this->teacher->id));
        $this->teacher->refresh();
        $this->assertTrue($this->teacher->is_active);
    }

    public function test_user_cannot_disable_own_account(): void
    {
        $response = $this->actingAs($this->superAdmin)->patch(route('admin.users.toggle-status', $this->superAdmin->id));

        $response->assertRedirect(route('admin.users.index'));
        $this->superAdmin->refresh();
        $this->assertTrue($this->superAdmin->is_active);
    }

    public function test_admin_can_enroll_user_to_school(): void
    {
        $unassignedUser = User::factory()->create([
            'name' => 'Calon Guru Magang',
            'email' => 'magang@pusat.test',
            'role' => 'teacher',
            'school_id' => null,
            'is_active' => true,
        ]);

        // Enrol to School B
        $response = $this->actingAs($this->superAdmin)->post(route('admin.users.enroll', $unassignedUser->id), [
            'school_id' => $this->schoolB->id,
        ]);

        $response->assertRedirect(route('admin.users.index'));
        $unassignedUser->refresh();
        $this->assertEquals($this->schoolB->id, $unassignedUser->school_id);

        // Un-enrol (unassign)
        $this->actingAs($this->superAdmin)->post(route('admin.users.enroll', $unassignedUser->id), [
            'school_id' => null,
        ]);
        $unassignedUser->refresh();
        $this->assertNull($unassignedUser->school_id);
    }

    public function test_admin_can_delete_user(): void
    {
        $userToDelete = User::factory()->create([
            'name' => 'Akun Sementara',
            'email' => 'temp@test.com',
            'role' => 'student',
            'school_id' => $this->schoolA->id,
        ]);

        $response = $this->actingAs($this->superAdmin)->delete(route('admin.users.destroy', $userToDelete->id));

        $response->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseMissing('users', ['id' => $userToDelete->id]);
    }

    public function test_user_cannot_delete_own_account(): void
    {
        $response = $this->actingAs($this->superAdmin)->delete(route('admin.users.destroy', $this->superAdmin->id));

        $response->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseHas('users', ['id' => $this->superAdmin->id]);
    }

    public function test_admin_can_access_dedicated_create_user_page(): void
    {
        $response = $this->actingAs($this->superAdmin)->get(route('admin.users.create'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.users.create');
        $response->assertSee('Tambah Pengguna Baru');
    }

    public function test_admin_can_access_dedicated_edit_user_page(): void
    {
        $response = $this->actingAs($this->superAdmin)->get(route('admin.users.edit', $this->teacher->id));

        $response->assertStatus(200);
        $response->assertViewIs('admin.users.edit');
        $response->assertSee('Ubah Data Pengguna');
        $response->assertSee($this->teacher->name);
    }

    public function test_admin_can_access_dedicated_enrollments_page(): void
    {
        $response = $this->actingAs($this->superAdmin)->get(route('admin.users.enrollments'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.users.enrollments');
        $response->assertSee('Enrol Pengguna ke Satuan Pendidikan');
    }
}
