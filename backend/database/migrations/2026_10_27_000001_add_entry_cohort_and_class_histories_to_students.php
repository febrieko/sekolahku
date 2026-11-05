<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * - Angkatan (tahun ajaran masuk) & jenis pendaftaran Dapodik pada tabel students.
     * - Riwayat rombel per tahun ajaran (student_class_histories) untuk kenaikan kelas.
     */
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->foreignId('entry_academic_year_id')->nullable()->after('class_id')
                ->constrained('academic_years')->nullOnDelete();
            $table->unsignedTinyInteger('entry_grade_level')->nullable()->after('entry_academic_year_id');
            $table->enum('registration_type', ['baru', 'pindahan', 'kembali_bersekolah'])
                ->default('baru')->after('entry_grade_level');
            $table->string('previous_school', 150)->nullable()->after('registration_type');

            $table->index(['school_id', 'entry_academic_year_id']);
        });

        Schema::create('student_class_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('class_id')->nullable()->constrained('classes')->nullOnDelete();
            $table->foreignId('academic_year_id')->constrained('academic_years')->cascadeOnDelete();
            $table->unsignedTinyInteger('grade_level')->nullable();
            // active = sedang berjalan; sisanya = hasil akhir tahun ajaran tersebut
            $table->enum('status', ['active', 'promoted', 'retained', 'graduated', 'transferred', 'dropout'])
                ->default('active');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('notes', 255)->nullable();
            $table->timestamps();

            $table->unique(['student_id', 'academic_year_id'], 'sch_student_ay_uniq');
            $table->index(['school_id', 'academic_year_id', 'class_id'], 'sch_school_ay_class_idx');
        });

        $this->backfill();
    }

    /**
     * Isi angkatan & riwayat rombel untuk data siswa yang sudah ada.
     */
    private function backfill(): void
    {
        $students = DB::table('students')
            ->leftJoin('classes', 'classes.id', '=', 'students.class_id')
            ->select('students.id', 'students.school_id', 'students.class_id', 'students.enrollment_date',
                'classes.academic_year_id as class_year_id', 'classes.grade_level')
            ->get();

        foreach ($students as $s) {
            $yearId = $s->class_year_id
                ?? DB::table('academic_years')->where('school_id', $s->school_id)
                    ->orderByDesc('is_active')->orderByDesc('start_date')->value('id');

            if (! $yearId) {
                continue;
            }

            DB::table('students')->where('id', $s->id)->update([
                'entry_academic_year_id' => $yearId,
                'entry_grade_level' => $s->grade_level,
            ]);

            if ($s->class_id) {
                DB::table('student_class_histories')->insert([
                    'school_id' => $s->school_id,
                    'student_id' => $s->id,
                    'class_id' => $s->class_id,
                    'academic_year_id' => $yearId,
                    'grade_level' => $s->grade_level,
                    'status' => 'active',
                    'start_date' => $s->enrollment_date,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_class_histories');

        Schema::table('students', function (Blueprint $table) {
            $table->dropIndex(['school_id', 'entry_academic_year_id']);
            $table->dropConstrainedForeignId('entry_academic_year_id');
            $table->dropColumn(['entry_grade_level', 'registration_type', 'previous_school']);
        });
    }
};
