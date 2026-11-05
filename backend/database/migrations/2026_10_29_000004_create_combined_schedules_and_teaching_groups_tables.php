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
        // 1. Tambahkan kolom religion_filter pada tabel subjects (opsional, untuk mapel Pendidikan Agama)
        Schema::table('subjects', function (Blueprint $table) {
            $table->string('religion_filter', 30)
                ->nullable()
                ->after('color');
        });

        // 2. Tabel Master Kelompok Mapel / Kelas Gabungan Lintas Rombel
        Schema::create('teaching_groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('academic_year_id')->nullable()->constrained('academic_years')->nullOnDelete();
            $table->string('code', 30);
            $table->string('name', 150);
            $table->foreignId('subject_id')->nullable()->constrained('subjects')->nullOnDelete();
            $table->foreignId('teacher_id')->nullable()->constrained('teachers')->nullOnDelete();
            $table->foreignId('default_room_id')->nullable()->constrained('school_rooms')->nullOnDelete();
            $table->string('membership_mode', 25)->default('RELIGION'); // RELIGION, MANUAL, ALL
            $table->string('religion_filter', 30)->nullable(); // Islam, Kristen, Katolik, Hindu, Buddha, Konghucu
            $table->string('description', 255)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['school_id', 'code'], 'tg_school_code_unq');
            $table->index(['school_id', 'is_active'], 'tg_school_active_idx');
        });

        // 3. Pivot Rombel yang Tergabung dalam Kelompok Mapel
        Schema::create('teaching_group_classes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teaching_group_id')->constrained('teaching_groups')->cascadeOnDelete();
            $table->foreignId('class_id')->constrained('classes')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['teaching_group_id', 'class_id'], 'tg_cls_unq');
        });

        // 4. Pivot Siswa Pilihan Manual (untuk Mapel Pilihan Lintas Rombel)
        Schema::create('teaching_group_students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teaching_group_id')->constrained('teaching_groups')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['teaching_group_id', 'student_id'], 'tg_std_unq');
        });

        // 5. Kolom Kelas Gabungan & Filter Peserta pada class_schedules
        Schema::table('class_schedules', function (Blueprint $table) {
            $table->boolean('is_combined')
                ->default(false)
                ->after('class_id');
            $table->foreignId('teaching_group_id')
                ->nullable()
                ->after('is_combined')
                ->constrained('teaching_groups')
                ->nullOnDelete();
            $table->string('student_filter_type', 25)
                ->default('AUTO') // AUTO (baca mapel/biodata), ALL, RELIGION, TEACHING_GROUP
                ->after('teaching_group_id');
            $table->string('student_filter_value', 40)
                ->nullable()
                ->after('student_filter_type');

            $table->index(['school_id', 'is_combined'], 'cs_school_combined_idx');
        });

        // 6. Pivot Multi-Rombel untuk Jadwal Kelas Gabungan
        Schema::create('class_schedule_classes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_schedule_id')->constrained('class_schedules')->cascadeOnDelete();
            $table->foreignId('class_id')->constrained('classes')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['class_schedule_id', 'class_id'], 'cs_cls_unq');
            $table->index('class_id', 'cs_cls_class_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('class_schedule_classes');

        Schema::table('class_schedules', function (Blueprint $table) {
            $table->dropIndex('cs_school_combined_idx');
            $table->dropForeign(['teaching_group_id']);
            $table->dropColumn([
                'is_combined',
                'teaching_group_id',
                'student_filter_type',
                'student_filter_value',
            ]);
        });

        Schema::dropIfExists('teaching_group_students');
        Schema::dropIfExists('teaching_group_classes');
        Schema::dropIfExists('teaching_groups');

        Schema::table('subjects', function (Blueprint $table) {
            $table->dropColumn('religion_filter');
        });
    }
};

