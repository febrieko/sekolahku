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
        Schema::create('school_rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->string('code', 30);
            $table->string('name', 100);
            $table->string('type', 30)->default('CLASSROOM'); // CLASSROOM, LABORATORY, WORKSHOP, SPORTS, HALL, OTHER
            $table->string('building', 60)->nullable();
            $table->string('floor', 20)->nullable();
            $table->unsignedSmallInteger('capacity')->default(36);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['school_id', 'code']);
            $table->index(['school_id', 'type', 'is_active']);
        });

        Schema::table('classes', function (Blueprint $table) {
            $table->foreignId('default_room_id')
                ->nullable()
                ->after('homeroom_teacher_id')
                ->constrained('school_rooms')
                ->nullOnDelete();
        });

        Schema::table('time_schedule_categories', function (Blueprint $table) {
            $table->foreignId('academic_year_id')
                ->nullable()
                ->after('school_id')
                ->constrained('academic_years')
                ->nullOnDelete();
            $table->string('academic_semester', 10)
                ->nullable()
                ->after('academic_year_id'); // odd, even
        });

        Schema::table('class_schedules', function (Blueprint $table) {
            $table->foreignId('category_id')
                ->nullable()
                ->after('school_id')
                ->constrained('time_schedule_categories')
                ->cascadeOnDelete();
            $table->foreignId('room_id')
                ->nullable()
                ->after('academic_semester')
                ->constrained('school_rooms')
                ->nullOnDelete();
            $table->string('notes', 150)
                ->nullable()
                ->after('room_name');

            $table->index(['school_id', 'category_id', 'academic_year_id', 'academic_semester'], 'idx_schedule_context');
            $table->index(['school_id', 'room_id'], 'idx_schedule_room');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('class_schedules', function (Blueprint $table) {
            $table->dropIndex('idx_schedule_context');
            $table->dropIndex('idx_schedule_room');
            $table->dropForeign(['category_id']);
            $table->dropForeign(['room_id']);
            $table->dropColumn(['category_id', 'room_id', 'notes']);
        });

        Schema::table('time_schedule_categories', function (Blueprint $table) {
            $table->dropForeign(['academic_year_id']);
            $table->dropColumn(['academic_year_id', 'academic_semester']);
        });

        Schema::table('classes', function (Blueprint $table) {
            $table->dropForeign(['default_room_id']);
            $table->dropColumn('default_room_id');
        });

        Schema::dropIfExists('school_rooms');
    }
};

