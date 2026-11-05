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
        Schema::table('teachers', function (Blueprint $table) {
            // Pas foto resmi formal 4x6
            $table->string('photo_path', 255)->nullable()->after('full_name');

            // Identitas keluarga & perkawinan
            $table->string('marital_status', 50)->nullable()->default('Belum Kawin')->after('religion');
            $table->string('spouse_name', 255)->nullable()->after('marital_status');
            $table->string('spouse_job', 150)->nullable()->after('spouse_name');

            // Detail Kepegawaian & Penugasan Dapodik
            $table->date('tmt_appointment')->nullable()->after('sk_appointment_date');
            $table->string('institution_assignment_status', 50)->default('Induk')->after('institution_assignment');
            $table->string('salary_source', 100)->nullable()->default('APBD Provinsi')->after('institution_assignment_status');

            // Riwayat Pendidikan Formal
            $table->string('university_name', 255)->nullable()->after('major_study');
            $table->string('graduation_year', 10)->nullable()->after('university_name');

            // Sertifikasi Pendidik
            $table->string('nrg', 50)->nullable()->after('certification_number');
            $table->string('certification_subject', 150)->nullable()->after('nrg');

            // Tugas Tambahan & Jam Mengajar (JJM)
            $table->text('additional_duties')->nullable()->after('certification_subject');
            $table->unsignedSmallInteger('teaching_hours')->default(24)->after('additional_duties');

            // Kesejahteraan, Bank & Pajak
            $table->string('npwp', 30)->nullable()->after('teaching_hours');
            $table->string('bank_name', 100)->nullable()->after('npwp');
            $table->string('bank_account_number', 50)->nullable()->after('bank_name');
            $table->string('bank_account_name', 255)->nullable()->after('bank_account_number');
        });

        Schema::table('schools', function (Blueprint $table) {
            $table->boolean('gtk_profile_edit_enabled')->default(true)->after('student_profile_edit_end');
            $table->date('gtk_profile_edit_start')->nullable()->after('gtk_profile_edit_enabled');
            $table->date('gtk_profile_edit_end')->nullable()->after('gtk_profile_edit_start');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('teachers', function (Blueprint $table) {
            $table->dropColumn([
                'photo_path',
                'marital_status',
                'spouse_name',
                'spouse_job',
                'tmt_appointment',
                'institution_assignment_status',
                'salary_source',
                'university_name',
                'graduation_year',
                'nrg',
                'certification_subject',
                'additional_duties',
                'teaching_hours',
                'npwp',
                'bank_name',
                'bank_account_number',
                'bank_account_name',
            ]);
        });

        Schema::table('schools', function (Blueprint $table) {
            $table->dropColumn([
                'gtk_profile_edit_enabled',
                'gtk_profile_edit_start',
                'gtk_profile_edit_end',
            ]);
        });
    }
};

