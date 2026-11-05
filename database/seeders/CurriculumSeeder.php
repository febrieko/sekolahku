<?php

namespace Database\Seeders;

use App\Models\Curriculum;
use App\Models\CurriculumGradeLevel;
use App\Models\CurriculumSubject;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\Subject;
use Illuminate\Database\Seeder;

class CurriculumSeeder extends Seeder
{
    public function run(): void
    {
        $school = School::where('tenant_id', 'sman1jkt')->first() ?? School::first();
        $schoolId = $school ? $school->id : 1;

        // 1. Kurikulum Merdeka (Nasional)
        $kurMerdeka = Curriculum::firstOrCreate(
            ['school_id' => $schoolId, 'code' => 'KUR-MERDEKA'],
            [
                'name' => 'Kurikulum Merdeka (Fase E & F)',
                'target_level' => 'SMA / SMK',
                'description' => 'Pembelajaran intrakurikuler yang beragam, fokus materi esensial, fleksibilitas peminatan siswa, dan Projek Penguatan Profil Pelajar Pancasila (P5).',
                'is_active' => true,
            ]
        );

        // 2. Kurikulum 2013 Revisi
        $k13 = Curriculum::firstOrCreate(
            ['school_id' => $schoolId, 'code' => 'K13-REVISI'],
            [
                'name' => 'Kurikulum 2013 Revisi (K-13)',
                'target_level' => 'SMA',
                'description' => 'Pendekatan saintifik (scientific approach) dengan peminatan jurusan Matematika dan Ilmu Alam (MIA) serta Ilmu-Ilmu Sosial (IIS).',
                'is_active' => true,
            ]
        );

        // 3. Kurikulum Muatan Khusus Pesantren / Boarding (Opsional)
        $kurPesantren = Curriculum::firstOrCreate(
            ['school_id' => $schoolId, 'code' => 'KUR-PESANTREN'],
            [
                'name' => 'Kurikulum Terpadu Keagamaan / Pesantren',
                'target_level' => 'Boarding School / MA',
                'description' => 'Integrasi kurikulum Kementerian Agama dan kepesantrenan (Tahfidz Al-Qur\'an, Bahasa Arab, Nahwu-Shorof, dan Fiqih).',
                'is_active' => true,
            ]
        );

        // 4. Pemetaan Kurikulum per Jenjang / Tingkat Kelas
        // Tingkat 10 -> Kurikulum Merdeka (Fase E)
        CurriculumGradeLevel::updateOrCreate(
            ['school_id' => $schoolId, 'grade_level' => 10],
            ['curriculum_id' => $kurMerdeka->id, 'phase' => 'Fase E']
        );

        // Tingkat 11 -> Kurikulum Merdeka (Fase F)
        CurriculumGradeLevel::updateOrCreate(
            ['school_id' => $schoolId, 'grade_level' => 11],
            ['curriculum_id' => $kurMerdeka->id, 'phase' => 'Fase F']
        );

        // Tingkat 12 -> Kurikulum 2013 Revisi (Transisi K-13)
        CurriculumGradeLevel::updateOrCreate(
            ['school_id' => $schoolId, 'grade_level' => 12],
            ['curriculum_id' => $k13->id, 'phase' => 'Tingkat Akhir']
        );

        // 5. Update Rombel/Kelas agar terhubung ke kurikulum jenjangnya
        SchoolClass::where('school_id', $schoolId)->whereIn('grade_level', [10, 11])->update([
            'curriculum_id' => $kurMerdeka->id,
        ]);
        SchoolClass::where('school_id', $schoolId)->where('grade_level', 12)->update([
            'curriculum_id' => $k13->id,
        ]);

        // 6. Hubungkan Mata Pelajaran ke Kurikulum Merdeka
        $subjects = Subject::where('school_id', $schoolId)->get();

        foreach ($subjects as $sub) {
            $group = match ($sub->category) {
                'A_MUATAN_NASIONAL' => 'WAJIB_UMUM',
                'C_KEJURUAN_PEMINATAN' => 'PEMINATAN_PILIHAN',
                'MULOK' => 'MULOK',
                default => 'WAJIB_UMUM',
            };

            $jp = match ($sub->code) {
                'MTK', 'MAT' => 4,
                'BIND' => 4,
                'BING' => 3,
                'FIS', 'KIM', 'BIO' => 5,
                default => 2,
            };

            CurriculumSubject::updateOrCreate(
                ['curriculum_id' => $kurMerdeka->id, 'subject_id' => $sub->id],
                [
                    'subject_group' => $group,
                    'hours_per_week' => $jp,
                    'passing_grade' => $sub->passing_grade ?? 75.00,
                    'is_active' => true,
                ]
            );

            // Juga masukkan ke K-13 dengan variasi jam pelajaran
            CurriculumSubject::updateOrCreate(
                ['curriculum_id' => $k13->id, 'subject_id' => $sub->id],
                [
                    'subject_group' => $group,
                    'hours_per_week' => max(2, $jp - 1),
                    'passing_grade' => 75.00,
                    'is_active' => true,
                ]
            );
        }

        // Tambahkan mata pelajaran P5 khusus Kurikulum Merdeka
        $subP5 = Subject::firstOrCreate(
            ['school_id' => $schoolId, 'code' => 'P5'],
            [
                'name' => 'Projek Penguatan Profil Pelajar Pancasila (P5)',
                'category' => 'A_MUATAN_NASIONAL',
                'passing_grade' => 78.00,
                'is_active' => true,
            ]
        );

        CurriculumSubject::updateOrCreate(
            ['curriculum_id' => $kurMerdeka->id, 'subject_id' => $subP5->id],
            [
                'subject_group' => 'P5_PROJEK',
                'hours_per_week' => 3,
                'passing_grade' => 78.00,
                'is_active' => true,
            ]
        );
    }
}
