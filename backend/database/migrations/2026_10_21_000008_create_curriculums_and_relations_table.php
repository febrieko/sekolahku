<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Master Kurikulum
        Schema::create('curriculums', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->string('code', 30); // KUR-MERDEKA, K13-REV, KUR-PESANTREN
            $table->string('name', 150); // Kurikulum Merdeka (Nasional), Kurikulum 2013 Revisi
            $table->string('target_level', 50)->default('SMA'); // SD, SMP, SMA, SMK, MA
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['school_id', 'code']);
        });

        // 2. Pemetaan Kurikulum per Jenjang / Tingkat
        Schema::create('curriculum_grade_levels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('curriculum_id')->constrained('curriculums')->cascadeOnDelete();
            $table->integer('grade_level'); // 1-12 (misal: 10, 11, 12)
            $table->string('phase', 20)->nullable(); // Fase A, B, C, D, E, F
            $table->timestamps();

            $table->unique(['school_id', 'grade_level']);
        });

        // 3. Relasi Kurikulum menentukan Mata Pelajaran & Alokasi JP
        Schema::create('curriculum_subjects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('curriculum_id')->constrained('curriculums')->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained('subjects')->cascadeOnDelete();
            $table->integer('grade_level')->nullable(); // Jika null berlaku untuk semua jenjang kurikulum tsb
            $table->enum('subject_group', [
                'WAJIB_UMUM',
                'PEMINATAN_PILIHAN',
                'P5_PROJEK',
                'MULOK',
                'KEAGAMAAN'
            ])->default('WAJIB_UMUM');
            $table->integer('hours_per_week')->default(2); // Alokasi JP (Jam Pelajaran) per minggu
            $table->decimal('passing_grade', 5, 2)->default(75.00); // KKM / KKTP
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['curriculum_id', 'subject_id']);
        });

        // 4. Tambah curriculum_id pada tabel classes agar tiap rombel terikat langsung ke kurikulum
        Schema::table('classes', function (Blueprint $table) {
            $table->foreignId('curriculum_id')->nullable()->after('major')->constrained('curriculums')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('classes', function (Blueprint $table) {
            $table->dropForeign(['curriculum_id']);
            $table->dropColumn('curriculum_id');
        });

        Schema::dropIfExists('curriculum_subjects');
        Schema::dropIfExists('curriculum_grade_levels');
        Schema::dropIfExists('curriculums');
    }
};
