<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations to add official Dapodik Kemendikbudristek fields to schools table.
     */
    public function up(): void
    {
        Schema::table('schools', function (Blueprint $table) {
            $table->string('school_status', 20)->default('NEGERI')->after('level'); // NEGERI / SWASTA
            $table->string('accreditation', 20)->default('A')->after('school_status'); // A, B, C, BELUM
            $table->string('establishment_decree', 100)->nullable()->after('accreditation'); // SK Pendirian
            $table->date('establishment_date')->nullable()->after('establishment_decree');
            $table->string('operational_license_decree', 100)->nullable()->after('establishment_date'); // SK Izin Operasional
            $table->date('operational_license_date')->nullable()->after('operational_license_decree');
            $table->string('tax_number', 30)->nullable()->after('operational_license_date'); // NPWP Sekolah

            // Lokasi & Wilayah Administrasi Dapodik
            $table->string('postal_code', 10)->nullable()->after('address');
            $table->string('rt_rw', 20)->nullable()->after('postal_code');
            $table->string('sub_village', 100)->nullable()->after('rt_rw'); // Dusun
            $table->string('village', 100)->nullable()->after('sub_village'); // Desa / Kelurahan
            $table->string('subdistrict', 100)->nullable()->after('village'); // Kecamatan
            $table->string('city', 100)->nullable()->after('subdistrict'); // Kabupaten / Kota
            $table->string('province', 100)->nullable()->after('city'); // Provinsi
            $table->decimal('latitude', 10, 8)->nullable()->after('province');
            $table->decimal('longitude', 11, 8)->nullable()->after('latitude');

            // Kontak & Media
            $table->string('website', 150)->nullable()->after('email');
            $table->string('fax', 30)->nullable()->after('phone');

            // Pimpinan Satuan Pendidikan
            $table->string('principal_name', 150)->nullable()->after('logo_url');
            $table->string('principal_nip', 30)->nullable()->after('principal_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('schools', function (Blueprint $table) {
            $table->dropColumn([
                'school_status',
                'accreditation',
                'establishment_decree',
                'establishment_date',
                'operational_license_decree',
                'operational_license_date',
                'tax_number',
                'postal_code',
                'rt_rw',
                'sub_village',
                'village',
                'subdistrict',
                'city',
                'province',
                'latitude',
                'longitude',
                'website',
                'fax',
                'principal_name',
                'principal_nip',
            ]);
        });
    }
};
