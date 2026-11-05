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
        Schema::create('teaching_journals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('academic_year_id')->constrained('academic_years')->cascadeOnDelete();
            $table->string('academic_semester', 10)->default('odd'); // 'odd' or 'even'
            $table->foreignId('class_schedule_id')->nullable()->constrained('class_schedules')->nullOnDelete();
            $table->foreignId('teacher_id')->constrained('teachers')->cascadeOnDelete();
            $table->foreignId('substitute_teacher_id')->nullable()->constrained('teachers')->nullOnDelete();
            $table->foreignId('school_class_id')->nullable()->constrained('classes')->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained('subjects')->cascadeOnDelete();
            $table->foreignId('teaching_group_id')->nullable()->constrained('teaching_groups')->nullOnDelete();
            $table->foreignId('school_room_id')->nullable()->constrained('school_rooms')->nullOnDelete();

            $table->date('date');
            $table->string('time_slot_label', 100)->nullable(); // Misal: "Jam Ke 1 - 3 (07:00 - 09:15)"
            $table->unsignedSmallInteger('start_period')->default(1);
            $table->unsignedSmallInteger('end_period')->default(2);
            $table->unsignedSmallInteger('meeting_number')->default(1); // Pertemuan Ke-1, 2, ...

            $table->text('learning_objective')->nullable(); // Capaian / Tujuan Pembelajaran / Materi Pokok
            $table->text('teaching_activity')->nullable(); // Aktivitas Pembelajaran & Metode
            $table->text('obstacles_and_followup')->nullable(); // Hambatan KBM & Tindak Lanjut Guru
            $table->text('homework_assignment')->nullable(); // Tugas / PR / Tagihan Formatif

            $table->string('status', 30)->default('in_progress'); // 'in_progress', 'completed', 'substituted', 'cancelled'
            $table->json('attendance_summary')->nullable(); // Rekap: {total: 36, present: 34, sick: 1, excused: 1, absent: 0, dispensation: 0}

            $table->timestamp('completed_at')->nullable();
            $table->timestamp('verified_by_waka_at')->nullable();
            $table->foreignId('verified_by_waka_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('waka_supervision_notes')->nullable();

            $table->timestamps();

            $table->index(['school_id', 'date']);
            $table->index(['school_id', 'teacher_id']);
            $table->index(['school_id', 'school_class_id']);
        });

        Schema::create('teaching_journal_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teaching_journal_id')->constrained('teaching_journals')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->string('status', 20)->default('present'); // 'present', 'sick', 'excused', 'absent', 'dispensation'
            $table->string('notes')->nullable(); // Catatan per siswa (mis: "Izin ke ruang UKS jam ke-2")
            $table->timestamps();

            $table->unique(['teaching_journal_id', 'student_id'], 'journal_student_unique');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teaching_journal_attendances');
        Schema::dropIfExists('teaching_journals');
    }
};
