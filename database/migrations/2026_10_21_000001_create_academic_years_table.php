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
        Schema::create('academic_years', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->string('name', 20); // e.g. "2026/2027"
            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('is_active')->default(false);
            $table->timestamps();

            $table->index(['school_id', 'is_active']);
        });

        Schema::create('academic_semesters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_year_id')->constrained('academic_years')->cascadeOnDelete();
            $table->enum('semester', ['odd', 'even'])->default('odd'); // Ganjil / Genap
            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('academic_semesters');
        Schema::dropIfExists('academic_years');
    }
};
