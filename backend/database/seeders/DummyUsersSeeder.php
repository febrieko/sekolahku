<?php

namespace Database\Seeders;

use App\Models\School;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\ParentGuardian;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DummyUsersSeeder extends Seeder
{
    public function run(): void
    {
        $school = School::where('tenant_id', 'sman1jkt')->first() ?? School::first();
        $schoolId = $school ? $school->id : 1;
        $defaultPassword = Hash::make('password');

        // 1. Admin Sekolah (School Administrator - TU)
        $admin = User::updateOrCreate(
            ['email' => 'admin@sekolahku.sch.id'],
            [
                'name' => 'Administrator Sekolah',
                'password' => $defaultPassword,
                'role' => 'school_admin',
                'additional_role' => null,
                'school_id' => $schoolId,
                'phone' => '081211112222',
                'is_active' => true,
            ]
        );

        // 2. Kepala Sekolah (Principal)
        $principal = User::updateOrCreate(
            ['email' => 'kepsek@sekolahku.sch.id'],
            [
                'name' => 'Dr. H. Mulyadi, M.Pd.',
                'password' => $defaultPassword,
                'role' => 'principal',
                'additional_role' => null,
                'school_id' => $schoolId,
                'phone' => '081266667777',
                'is_active' => true,
            ]
        );
        Teacher::updateOrCreate(
            ['email' => 'kepsek@sekolahku.sch.id'],
            [
                'school_id' => $schoolId,
                'user_id' => $principal->id,
                'full_name' => 'Mulyadi',
                'title_prefix' => 'Dr. H.',
                'title_suffix' => 'M.Pd.',
                'gender' => 'L',
                'phone' => '081266667777',
                'employment_type' => 'KEPALA_SEKOLAH',
                'employment_status' => 'PNS',
                'nip' => '196502151990031002',
                'nuptk' => '3456789012345678',
                'nik' => '3171011502650001',
                'is_certified' => true,
                'last_education' => 'S3',
                'major_study' => 'Manajemen Pendidikan Kebijakan',
                'university_name' => 'Universitas Pendidikan Indonesia',
                'additional_duties' => 'Kepala Sekolah',
                'teaching_hours' => 6,
                'is_active' => true,
            ]
        );

        // 3. Waka Kurikulum (Role dasar Guru + Tugas Tambahan Waka Kurikulum)
        $wakaKurikulum = User::updateOrCreate(
            ['email' => 'waka.kurikulum@sekolahku.sch.id'],
            [
                'name' => 'Dra. Hj. Nurhayati, M.Pd.',
                'password' => $defaultPassword,
                'role' => 'teacher',
                'additional_role' => 'waka_kurikulum',
                'school_id' => $schoolId,
                'phone' => '081233334444',
                'is_active' => true,
            ]
        );
        Teacher::updateOrCreate(
            ['email' => 'waka.kurikulum@sekolahku.sch.id'],
            [
                'school_id' => $schoolId,
                'user_id' => $wakaKurikulum->id,
                'full_name' => 'Nurhayati',
                'title_prefix' => 'Dra. Hj.',
                'title_suffix' => 'M.Pd.',
                'gender' => 'P',
                'phone' => '081233334444',
                'employment_type' => 'GURU_MAPEL',
                'employment_status' => 'PNS',
                'nip' => '197103141995122001',
                'nuptk' => '4567890123456789',
                'nik' => '3171015403710001',
                'is_certified' => true,
                'last_education' => 'S2',
                'major_study' => 'Manajemen Pendidikan',
                'university_name' => 'Universitas Negeri Jakarta',
                'additional_duties' => 'Waka Kurikulum & Akademik',
                'teaching_hours' => 24,
                'is_active' => true,
            ]
        );

        // 4. Waka Kesiswaan (Role dasar Guru + Tugas Tambahan Waka Kesiswaan)
        $wakaKesiswaan = User::updateOrCreate(
            ['email' => 'waka.kesiswaan@sekolahku.sch.id'],
            [
                'name' => 'Drs. Agus Setiawan, M.Pd.',
                'password' => $defaultPassword,
                'role' => 'teacher',
                'additional_role' => 'waka_kesiswaan',
                'school_id' => $schoolId,
                'phone' => '081255556666',
                'is_active' => true,
            ]
        );
        Teacher::updateOrCreate(
            ['email' => 'waka.kesiswaan@sekolahku.sch.id'],
            [
                'school_id' => $schoolId,
                'user_id' => $wakaKesiswaan->id,
                'full_name' => 'Agus Setiawan',
                'title_prefix' => 'Drs.',
                'title_suffix' => 'M.Pd.',
                'gender' => 'L',
                'phone' => '081255556666',
                'employment_type' => 'GURU_MAPEL',
                'employment_status' => 'PNS',
                'nip' => '196808201994031003',
                'nuptk' => '5678901234567890',
                'nik' => '3171012008680002',
                'is_certified' => true,
                'last_education' => 'S2',
                'major_study' => 'Pendidikan Jasmani & Kesehatan',
                'university_name' => 'Universitas Negeri Jakarta',
                'additional_duties' => 'Waka Kesiswaan & Kedisiplinan',
                'teaching_hours' => 24,
                'is_active' => true,
            ]
        );

        // 5. Waka Sarpras (Role dasar Guru + Tugas Tambahan Waka Sarpras)
        $wakaSarpras = User::updateOrCreate(
            ['email' => 'waka.sarpras@sekolahku.sch.id'],
            [
                'name' => 'Ir. H. Budi Santoso, M.T.',
                'password' => $defaultPassword,
                'role' => 'teacher',
                'additional_role' => 'waka_sarpras',
                'school_id' => $schoolId,
                'phone' => '081277779999',
                'is_active' => true,
            ]
        );
        Teacher::updateOrCreate(
            ['email' => 'waka.sarpras@sekolahku.sch.id'],
            [
                'school_id' => $schoolId,
                'user_id' => $wakaSarpras->id,
                'full_name' => 'Budi Santoso',
                'title_prefix' => 'Ir. H.',
                'title_suffix' => 'M.T.',
                'gender' => 'L',
                'phone' => '081277779999',
                'employment_type' => 'GURU_MAPEL',
                'employment_status' => 'PNS',
                'nip' => '197005151996021001',
                'nuptk' => '6789012345678901',
                'nik' => '3171011505700004',
                'is_certified' => true,
                'last_education' => 'S2',
                'major_study' => 'Teknik Elektro / Informatika',
                'university_name' => 'Institut Teknologi Bandung',
                'additional_duties' => 'Waka Sarana & Prasarana',
                'teaching_hours' => 24,
                'is_active' => true,
            ]
        );

        // 6. Waka Humas / Hubin (Role dasar Guru + Tugas Tambahan Waka Humas)
        $wakaHumas = User::updateOrCreate(
            ['email' => 'waka.humas@sekolahku.sch.id'],
            [
                'name' => 'Siti Rahmawati, S.Pd., M.M.',
                'password' => $defaultPassword,
                'role' => 'teacher',
                'additional_role' => 'waka_humas',
                'school_id' => $schoolId,
                'phone' => '081288880000',
                'is_active' => true,
            ]
        );
        Teacher::updateOrCreate(
            ['email' => 'waka.humas@sekolahku.sch.id'],
            [
                'school_id' => $schoolId,
                'user_id' => $wakaHumas->id,
                'full_name' => 'Siti Rahmawati',
                'title_prefix' => null,
                'title_suffix' => 'S.Pd., M.M.',
                'gender' => 'P',
                'phone' => '081288880000',
                'employment_type' => 'GURU_MAPEL',
                'employment_status' => 'PPPK',
                'nip' => '198204102022212005',
                'nuptk' => '7890123456789012',
                'nik' => '3171015004820005',
                'is_certified' => true,
                'last_education' => 'S2',
                'major_study' => 'Manajemen Pemasaran / Humas',
                'university_name' => 'Universitas Indonesia',
                'additional_duties' => 'Waka Hubungan Industri & Humas',
                'teaching_hours' => 24,
                'is_active' => true,
            ]
        );

        // 7. Guru BK (Counselor)
        $counselor = User::updateOrCreate(
            ['email' => 'bk@sekolahku.sch.id'],
            [
                'name' => 'Dra. Hj. Endang Purwanti, M.Psi.',
                'password' => $defaultPassword,
                'role' => 'counselor',
                'additional_role' => null,
                'school_id' => $schoolId,
                'phone' => '081244445555',
                'is_active' => true,
            ]
        );
        Teacher::updateOrCreate(
            ['email' => 'bk@sekolahku.sch.id'],
            [
                'school_id' => $schoolId,
                'user_id' => $counselor->id,
                'full_name' => 'Endang Purwanti',
                'title_prefix' => 'Dra. Hj.',
                'title_suffix' => 'M.Psi.',
                'gender' => 'P',
                'phone' => '081244445555',
                'employment_type' => 'GURU_BK',
                'employment_status' => 'PNS',
                'nip' => '197304151998022001',
                'nuptk' => '5678901234567891',
                'nik' => '3171015504730002',
                'is_certified' => true,
                'last_education' => 'S2',
                'major_study' => 'Psikologi Pendidikan & Bimbingan Konseling',
                'university_name' => 'Universitas Indonesia',
                'additional_duties' => 'Koordinator BK',
                'teaching_hours' => 24,
                'is_active' => true,
            ]
        );

        // 8. Guru / Pendidik Reguler
        $teacherUser = User::updateOrCreate(
            ['email' => 'guru@sekolahku.sch.id'],
            [
                'name' => 'Drs. Bambang Sudarsono, M.Pd.',
                'password' => $defaultPassword,
                'role' => 'teacher',
                'additional_role' => null,
                'school_id' => $schoolId,
                'phone' => '081299990001',
                'is_active' => true,
            ]
        );
        Teacher::updateOrCreate(
            ['email' => 'guru@sekolahku.sch.id'],
            [
                'school_id' => $schoolId,
                'user_id' => $teacherUser->id,
                'full_name' => 'Bambang Sudarsono',
                'title_prefix' => 'Drs.',
                'title_suffix' => 'M.Pd.',
                'gender' => 'L',
                'phone' => '081299990001',
                'employment_type' => 'GURU_MAPEL',
                'employment_status' => 'PNS',
                'nip' => '197505122000031002',
                'nuptk' => '8945753655200013',
                'nik' => '3171011205750003',
                'is_certified' => true,
                'last_education' => 'S2',
                'major_study' => 'Pendidikan Bahasa Indonesia',
                'university_name' => 'Universitas Negeri Jakarta',
                'additional_duties' => 'Wali Kelas X-MIPA-1',
                'teaching_hours' => 28,
                'is_active' => true,
            ]
        );

        // 9. Staf Tenaga Kependidikan (Tendik - TU)
        $staffUser = User::updateOrCreate(
            ['email' => 'tu@sekolahku.sch.id'],
            [
                'name' => 'Rudi Hermawan, S.Kom.',
                'password' => $defaultPassword,
                'role' => 'staff',
                'additional_role' => null,
                'school_id' => $schoolId,
                'phone' => '081399998888',
                'is_active' => true,
            ]
        );

        // 10. Bendahara Sekolah (Treasurer / Keuangan)
        $treasurerUser = User::updateOrCreate(
            ['email' => 'bendahara@sekolahku.sch.id'],
            [
                'name' => 'Hj. Ratna Sari, S.E.',
                'password' => $defaultPassword,
                'role' => 'treasurer',
                'additional_role' => null,
                'school_id' => $schoolId,
                'phone' => '081377776666',
                'is_active' => true,
            ]
        );

        // 3. Orang Tua / Wali Murid
        $parentUser = User::updateOrCreate(
            ['email' => 'ortu@sekolahku.sch.id'],
            [
                'name' => 'H. Hendra Wijaya',
                'password' => $defaultPassword,
                'role' => 'parent',
                'school_id' => $schoolId,
                'phone' => '081388881111',
                'is_active' => true,
            ]
        );

        $parent = ParentGuardian::where('school_id', $schoolId)->first();
        if ($parent && !$parent->user_id) {
            $parent->update(['user_id' => $parentUser->id]);
        }

        // 4. Siswa / Peserta Didik
        $studentUser = User::updateOrCreate(
            ['email' => 'siswa@sekolahku.sch.id'],
            [
                'name' => 'Ahmad Fauzi',
                'password' => $defaultPassword,
                'role' => 'student',
                'school_id' => $schoolId,
                'phone' => '081277778888',
                'is_active' => true,
            ]
        );

        $student = Student::where('school_id', $schoolId)->first() ?? Student::first();
        if ($student) {
            $student->update(['user_id' => $studentUser->id]);
            if ($studentUser->school_id !== $student->school_id) {
                $studentUser->update(['school_id' => $student->school_id]);
            }
        }
    }
}
