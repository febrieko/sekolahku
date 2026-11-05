<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Buat Tabel Kelompok Mata Pelajaran per Kurikulum
        Schema::create('curriculum_subject_groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('curriculum_id')->constrained('curriculums')->cascadeOnDelete();
            $table->string('code', 30);
            $table->string('name', 150);
            $table->text('description')->nullable();
            $table->integer('order_no')->default(1);
            $table->string('color', 30)->default('badge-primary');
            $table->timestamps();

            $table->unique(['curriculum_id', 'code']);
            $table->index(['curriculum_id', 'order_no']);
        });

        // 2. Tambah foreign key curriculum_subject_group_id pada curriculum_subjects
        Schema::table('curriculum_subjects', function (Blueprint $table) {
            $table->foreignId('curriculum_subject_group_id')
                ->nullable()
                ->after('subject_id')
                ->constrained('curriculum_subject_groups')
                ->nullOnDelete();
        });

        // 3. Otomatis generate kelompok default untuk kurikulum yang sudah ada dan migrasikan relasi data
        $curriculums = DB::table('curriculums')->get();
        foreach ($curriculums as $c) {
            $defaultGroups = [
                [
                    'school_id' => $c->school_id,
                    'curriculum_id' => $c->id,
                    'code' => 'WAJIB_UMUM',
                    'name' => 'Kelompok Umum (Wajib Nasional)',
                    'description' => 'Mata pelajaran wajib umum nasional yang diikuti oleh seluruh peserta didik.',
                    'order_no' => 1,
                    'color' => 'badge-primary',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'school_id' => $c->school_id,
                    'curriculum_id' => $c->id,
                    'code' => 'PEMINATAN_PILIHAN',
                    'name' => 'Kelompok Pilihan / Peminatan',
                    'description' => 'Mata pelajaran peminatan keahlian, kejuruan, atau mata pelajaran pilihan.',
                    'order_no' => 2,
                    'color' => 'badge-info',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'school_id' => $c->school_id,
                    'curriculum_id' => $c->id,
                    'code' => 'P5_PROJEK',
                    'name' => 'Projek Profil Pelajar Pancasila (P5)',
                    'description' => 'Projek penguatan karakter dan profil pelajar pancasila.',
                    'order_no' => 3,
                    'color' => 'badge-warning',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'school_id' => $c->school_id,
                    'curriculum_id' => $c->id,
                    'code' => 'MULOK',
                    'name' => 'Muatan Lokal (Mulok)',
                    'description' => 'Mata pelajaran keunggulan dan kearifan lokal daerah.',
                    'order_no' => 4,
                    'color' => 'badge-secondary',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'school_id' => $c->school_id,
                    'curriculum_id' => $c->id,
                    'code' => 'KEAGAMAAN',
                    'name' => 'Keagamaan / Kepesantrenan',
                    'description' => 'Mata pelajaran khusus keagamaan, tahfidz, atau diniyah.',
                    'order_no' => 5,
                    'color' => 'badge-success',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ];

            foreach ($defaultGroups as $grp) {
                $groupId = DB::table('curriculum_subject_groups')->insertGetId($grp);

                // Migrasikan relasi curriculum_subjects yang ada
                DB::table('curriculum_subjects')
                    ->where('curriculum_id', $c->id)
                    ->where('subject_group', $grp['code'])
                    ->update(['curriculum_subject_group_id' => $groupId]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('curriculum_subjects', function (Blueprint $table) {
            $table->dropForeign(['curriculum_subject_group_id']);
            $table->dropColumn('curriculum_subject_group_id');
        });

        Schema::dropIfExists('curriculum_subject_groups');
    }
};

