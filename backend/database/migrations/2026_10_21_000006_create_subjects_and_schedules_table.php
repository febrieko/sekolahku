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
        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->string('code', 20); // MTK, BIND, BING
            $table->string('name', 150); // Matematika, Bahasa Indonesia
            $table->enum('category', [
                'A_MUATAN_NASIONAL',
                'B_MUATAN_KEWILAYAHAN',
                'C_KEJURUAN_PEMINATAN',
                'MULOK'
            ])->default('A_MUATAN_NASIONAL');
            $table->decimal('passing_grade', 5, 2)->default(75.00); // KKM
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['school_id', 'code']);
        });

        Schema::create('class_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('class_id')->constrained('classes')->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained('subjects')->cascadeOnDelete();
            $table->foreignId('teacher_id')->constrained('teachers')->cascadeOnDelete();
            $table->foreignId('lesson_period_id')->constrained('lesson_periods')->cascadeOnDelete();
            $table->foreignId('academic_year_id')->constrained('academic_years')->cascadeOnDelete();
            $table->enum('academic_semester', ['odd', 'even'])->default('odd');
            $table->string('room_name', 50)->nullable(); // e.g. "Lab Komputer 1", "R. Kelas X-1"
            $table->timestamps();

            $table->index(['school_id', 'class_id']);
            $table->index(['school_id', 'teacher_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('class_schedules');
        Schema::dropIfExists('subjects');
    }
};
