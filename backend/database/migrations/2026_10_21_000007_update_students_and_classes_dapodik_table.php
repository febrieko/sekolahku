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
        Schema::table('classes', function (Blueprint $table) {
            $table->foreignId('department_id')->nullable()->after('school_id')->constrained('departments')->nullOnDelete();
            $table->foreignId('academic_year_id')->nullable()->after('department_id')->constrained('academic_years')->nullOnDelete();
        });

        Schema::table('students', function (Blueprint $table) {
            // Identitas Pokok Dapodik
            $table->string('nik', 16)->nullable()->after('nisn');
            $table->string('no_kk', 16)->nullable()->after('nik');
            $table->string('birth_certificate_no', 100)->nullable()->after('birth_date');
            $table->enum('religion', ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Khonghucu'])->default('Islam')->after('gender');
            $table->string('special_needs', 100)->default('Tidak Ada')->after('religion');
            // Alamat Domisili Dapodik
            $table->string('rt', 5)->nullable()->after('address');
            $table->string('rw', 5)->nullable()->after('rt');
            $table->string('sub_district', 100)->nullable()->after('rw');
            $table->string('district', 100)->nullable()->after('sub_district');
            $table->string('city', 100)->nullable()->after('district');
            $table->string('province', 100)->nullable()->after('city');
            $table->string('postal_code', 10)->nullable()->after('province');
            $table->enum('residence_type', [
                'Bersama Orang Tua', 'Wali', 'Kos', 'Asrama', 'Panti Asuhan'
            ])->default('Bersama Orang Tua')->after('postal_code');
            $table->enum('transportation_mode', [
                'Jalan Kaki', 'Kendaraan Pribadi', 'Angkutan Umum', 'Jemputan Sekolah', 'Kereta/KRL', 'Ojek/Online'
            ])->default('Kendaraan Pribadi')->after('residence_type');
            // Kesejahteraan & Keluarga
            $table->integer('child_order')->default(1)->after('transportation_mode');
            $table->integer('siblings_count')->default(0)->after('child_order');
            $table->boolean('kps_receiver')->default(false)->after('siblings_count');
            $table->string('kps_number', 50)->nullable()->after('kps_receiver');
            $table->boolean('kip_receiver')->default(false)->after('kps_number');
            $table->string('kip_number', 50)->nullable()->after('kip_receiver');
            $table->string('kip_name', 255)->nullable()->after('kip_number');
            // Data Fisik & Periodik
            $table->integer('height_cm')->nullable()->after('kip_name');
            $table->integer('weight_kg')->nullable()->after('height_cm');
            $table->integer('head_circumference_cm')->nullable()->after('weight_kg');
            $table->decimal('distance_to_school_km', 6, 2)->nullable()->after('head_circumference_cm');
            $table->integer('travel_time_minutes')->nullable()->after('distance_to_school_km');
            $table->enum('blood_type', ['A', 'B', 'AB', 'O', 'Tidak Tahu'])->default('Tidak Tahu')->after('travel_time_minutes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn([
                'nik', 'no_kk', 'birth_certificate_no', 'religion', 'special_needs',
                'rt', 'rw', 'sub_district', 'district', 'city', 'province', 'postal_code',
                'residence_type', 'transportation_mode', 'child_order', 'siblings_count',
                'kps_receiver', 'kps_number', 'kip_receiver', 'kip_number', 'kip_name',
                'height_cm', 'weight_kg', 'head_circumference_cm', 'distance_to_school_km',
                'travel_time_minutes', 'blood_type'
            ]);
        });

        Schema::table('classes', function (Blueprint $table) {
            $table->dropConstrainedForeignId('academic_year_id');
            $table->dropConstrainedForeignId('department_id');
        });
    }
};
