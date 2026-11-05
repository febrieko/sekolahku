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
        Schema::create('parents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('no_kk', 16)->nullable(); // Nomor Kartu Keluarga
            // Data Ayah Kandung Dapodik
            $table->string('father_nik', 16)->nullable();
            $table->string('father_name', 255)->nullable();
            $table->integer('father_birth_year')->nullable();
            $table->string('father_education', 50)->nullable();
            $table->string('father_occupation', 100)->nullable();
            $table->string('father_income_range', 100)->nullable();
            $table->string('father_phone', 30)->nullable();
            // Data Ibu Kandung Dapodik
            $table->string('mother_nik', 16)->nullable();
            $table->string('mother_name', 255)->nullable();
            $table->integer('mother_birth_year')->nullable();
            $table->string('mother_education', 50)->nullable();
            $table->string('mother_occupation', 100)->nullable();
            $table->string('mother_income_range', 100)->nullable();
            $table->string('mother_phone', 30)->nullable();
            // Data Wali Dapodik (Opsional)
            $table->string('guardian_nik', 16)->nullable();
            $table->string('guardian_name', 255)->nullable();
            $table->integer('guardian_birth_year')->nullable();
            $table->string('guardian_education', 50)->nullable();
            $table->string('guardian_occupation', 100)->nullable();
            $table->string('guardian_income_range', 100)->nullable();
            $table->string('guardian_phone', 30)->nullable();
            // Alamat Domisili
            $table->string('primary_phone', 30)->nullable();
            $table->text('address')->nullable();
            $table->string('rt', 5)->nullable();
            $table->string('rw', 5)->nullable();
            $table->string('sub_district', 100)->nullable();
            $table->string('district', 100)->nullable();
            $table->string('city', 100)->nullable();
            $table->string('province', 100)->nullable();
            $table->string('postal_code', 10)->nullable();
            $table->timestamps();

            $table->index(['school_id', 'no_kk']);
        });

        Schema::create('parent_student', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->constrained('parents')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->enum('relationship', ['father', 'mother', 'guardian'])->default('father');
            $table->timestamps();

            $table->unique(['parent_id', 'student_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parent_student');
        Schema::dropIfExists('parents');
    }
};
