<?php

namespace Database\Seeders;

use App\Models\Curriculum;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\VocationalConcentration;
use App\Models\VocationalField;
use App\Models\VocationalProgram;
use Illuminate\Database\Seeder;

class VocationalSpectrumSeeder extends Seeder
{
    public function run(): void
    {
        $school = School::where('tenant_id', 'sman1jkt')->first() ?? School::first();
        $schoolId = $school ? $school->id : 1;

        // -------------------------------------------------------------
        // 1. BIDANG KEAHLIAN: TEKNOLOGI INFORMASI (TI)
        // -------------------------------------------------------------
        $fieldTI = VocationalField::firstOrCreate(
            ['school_id' => $schoolId, 'code' => 'TI'],
            [
                'name' => 'Teknologi Informasi',
                'description' => 'Bidang keahlian yang mencakup rekayasa perangkat lunak, sistem komputasi awan, kecerdasan buatan, jaringan telekomunikasi, dan keamanan siber.',
                'is_active' => true,
            ]
        );

        // Program: PPLG
        $progPPLG = VocationalProgram::firstOrCreate(
            ['school_id' => $schoolId, 'code' => 'PPLG'],
            [
                'vocational_field_id' => $fieldTI->id,
                'name' => 'Pengembangan Perangkat Lunak dan Gim',
                'program_duration_years' => 3,
                'description' => 'Membekali peserta didik dengan logika pemrograman, algoritma dasar, arsitektur software, game design, dan integrasi API.',
                'is_active' => true,
            ]
        );

        $concRPL = VocationalConcentration::firstOrCreate(
            ['school_id' => $schoolId, 'code' => 'PPLG-RPL'],
            [
                'vocational_program_id' => $progPPLG->id,
                'dapodik_code' => '201111',
                'name' => 'Rekayasa Perangkat Lunak (RPL)',
                'description' => 'Fokus spesialisasi Full-Stack Web Development, Mobile Apps (Android/iOS), Cloud Deployment, dan Database Engineering.',
                'competencies_summary' => 'Pemrograman Berorientasi Objek (OOP), Web Framework (Laravel, React), Mobile Dev (Flutter), Cloud Architecture & DevOps.',
                'industry_partners' => 'PT Telkom Indonesia, PT Tokopedia, PT Bukalapak, Dicoding Indonesia, Google Developers Group.',
                'is_active' => true,
            ]
        );

        $concGIM = VocationalConcentration::firstOrCreate(
            ['school_id' => $schoolId, 'code' => 'PPLG-GIM'],
            [
                'vocational_program_id' => $progPPLG->id,
                'dapodik_code' => '201112',
                'name' => 'Pengembangan Gim (Game Development)',
                'description' => 'Fokus perancangan interaktif gim, 2D/3D gameplay mechanics, game engine (Unity/Unreal), dan visual scripting.',
                'competencies_summary' => 'Game Engine Programming (C#/Unity), 2D/3D Asset Pipeline, Audio Logic, Interactive Physics, UI/UX Game Design.',
                'industry_partners' => 'Agate International, Gambir Studio, Toge Productions, Asosiasi Game Indonesia (AGI).',
                'is_active' => true,
            ]
        );

        // Program: TJKT
        $progTJKT = VocationalProgram::firstOrCreate(
            ['school_id' => $schoolId, 'code' => 'TJKT'],
            [
                'vocational_field_id' => $fieldTI->id,
                'name' => 'Teknik Jaringan Komputer dan Telekomunikasi',
                'program_duration_years' => 3,
                'description' => 'Mempelajari dasar instalasi jaringan LAN/WAN, routing-switching, fiber optic, dan administrasi server linux.',
                'is_active' => true,
            ]
        );

        $concTKJ = VocationalConcentration::firstOrCreate(
            ['school_id' => $schoolId, 'code' => 'TJKT-TKJ'],
            [
                'vocational_program_id' => $progTJKT->id,
                'dapodik_code' => '201211',
                'name' => 'Teknik Komputer dan Jaringan (TKJ)',
                'description' => 'Spesialisasi konfigurasi router mikrotik/cisco, server virtualization, cloud network, dan network security monitoring.',
                'competencies_summary' => 'MikroTik Certified Network Associate (MTCNA), Cisco CCNA Dasar, Linux Sysadmin, Network Penetration Testing, Cloud Infrastructure.',
                'industry_partners' => 'Cisco Networking Academy, MikroTik Academy, PT Telkom Akses, Biznet Networks, PT Lintasarta.',
                'is_active' => true,
            ]
        );

        // -------------------------------------------------------------
        // 2. BIDANG KEAHLIAN: BISNIS DAN MANAJEMEN (BM)
        // -------------------------------------------------------------
        $fieldBM = VocationalField::firstOrCreate(
            ['school_id' => $schoolId, 'code' => 'BM'],
            [
                'name' => 'Bisnis dan Manajemen',
                'description' => 'Bidang keahlian yang berfokus pada akuntansi keuangan, administrasi digital perkantoran, perbankan, dan logistik.',
                'is_active' => true,
            ]
        );

        // Program: AKL
        $progAKL = VocationalProgram::firstOrCreate(
            ['school_id' => $schoolId, 'code' => 'AKL'],
            [
                'vocational_field_id' => $fieldBM->id,
                'name' => 'Akuntansi dan Keuangan Lembaga',
                'program_duration_years' => 3,
                'description' => 'Mempelajari siklus akuntansi perusahaan jasa, dagang, manufaktur, perpajakan, dan aplikasi komputer akuntansi.',
                'is_active' => true,
            ]
        );

        $concAK = VocationalConcentration::firstOrCreate(
            ['school_id' => $schoolId, 'code' => 'AKL-AKT'],
            [
                'vocational_program_id' => $progAKL->id,
                'dapodik_code' => '203111',
                'name' => 'Layanan Akuntansi & Perbankan',
                'description' => 'Fokus penyusunan laporan keuangan PSAK, audit dasar, sistem informasi akuntansi (MYOB/Accurate), dan perbankan digital.',
                'competencies_summary' => 'Siklus Akuntansi Komprehensif, Aplikasi Spreadsheet & Software Akuntansi (Accurate/MYOB), Pajak Penghasilan (PPh), Customer Service Perbankan.',
                'industry_partners' => 'PT Bank Mandiri (Persero) Tbk, PT Bank Central Asia Tbk, Ikatan Akuntan Indonesia (IAI), Kantor Akuntan Publik (KAP).',
                'is_active' => true,
            ]
        );

        // Program: MPLB
        $progMPLB = VocationalProgram::firstOrCreate(
            ['school_id' => $schoolId, 'code' => 'MPLB'],
            [
                'vocational_field_id' => $fieldBM->id,
                'name' => 'Manajemen Perkantoran dan Layanan Bisnis',
                'program_duration_years' => 3,
                'description' => 'Mempelajari otomatisasi tata kelola perkantoran, korespondensi bisnis, kearsipan digital, dan protokoler.',
                'is_active' => true,
            ]
        );

        $concMP = VocationalConcentration::firstOrCreate(
            ['school_id' => $schoolId, 'code' => 'MPLB-MP'],
            [
                'vocational_program_id' => $progMPLB->id,
                'dapodik_code' => '203211',
                'name' => 'Manajemen Perkantoran Digital',
                'description' => 'Fokus pada pengelolaan administrasi modern berbasis digital, ERP perkantoran, public relations, dan pelayanan prima.',
                'competencies_summary' => 'Korespondensi Niaga Bilingual, Manajemen Arsip Elektronik (E-Filing), Otomatisasi Perkantoran (Office 365/Google Workspace), Public Speaking & Protokoler.',
                'industry_partners' => 'Kementerian PAN-RB, PT Astra International Tbk, Dinas Penanaman Modal & PTSP, Hotel & Corporate HRD.',
                'is_active' => true,
            ]
        );

        // -------------------------------------------------------------
        // 3. BIDANG KEAHLIAN: TEKNOLOGI MANUFAKTUR & REKAYASA (TMR)
        // -------------------------------------------------------------
        $fieldTMR = VocationalField::firstOrCreate(
            ['school_id' => $schoolId, 'code' => 'TMR'],
            [
                'name' => 'Teknologi Manufaktur dan Rekayasa',
                'description' => 'Bidang keahlian yang mencakup teknik otomotif kendaraan bermotor, permesinan industri, elektronika, dan robotika.',
                'is_active' => true,
            ]
        );

        // Program: Teknik Otomotif
        $progTO = VocationalProgram::firstOrCreate(
            ['school_id' => $schoolId, 'code' => 'TO'],
            [
                'vocational_field_id' => $fieldTMR->id,
                'name' => 'Teknik Otomotif',
                'program_duration_years' => 3,
                'description' => 'Mempelajari sistem motor bakar, pemindah tenaga, sasis, suspensi, dan kelistrikan kendaraan bermotor.',
                'is_active' => true,
            ]
        );

        $concTKR = VocationalConcentration::firstOrCreate(
            ['school_id' => $schoolId, 'code' => 'TO-TKR'],
            [
                'vocational_program_id' => $progTO->id,
                'dapodik_code' => '202111',
                'name' => 'Teknik Kendaraan Ringan (TKR)',
                'description' => 'Spesialisasi perawatan dan perbaikan mesin bensin/diesel mobil, transmisi otomatis, EFI, sistem pengereman ABS, dan AC mobil.',
                'competencies_summary' => 'Tune-up Engine Konvensional & EFI, Overhaul Mesin, Diagnosa Scanner OBD-II, Perbaikan Sasis & Pemindah Tenaga, Service Kelistrikan Bodi.',
                'industry_partners' => 'PT Toyota Astra Motor, PT Astra Daihatsu Motor, Auto2000, PT Mitsubishi Motors Krama Yudha Sales Indonesia.',
                'is_active' => true,
            ]
        );

        // -------------------------------------------------------------
        // 4. Update / Hubungkan Kelas SMK Contoh
        // -------------------------------------------------------------
        $curKurmer = Curriculum::where('school_id', $schoolId)->where('code', 'KUR-MERDEKA')->first();

        // Kelas X PPLG 1 (Fase E)
        SchoolClass::firstOrCreate(
            ['school_id' => $schoolId, 'name' => 'X PPLG 1'],
            [
                'grade_level' => 10,
                'major' => 'PPLG',
                'curriculum_id' => $curKurmer?->id,
                'vocational_program_id' => $progPPLG->id,
                'academic_year' => '2026/2027',
                'is_active' => true,
            ]
        );

        // Kelas XI RPL 1 (Fase F)
        SchoolClass::firstOrCreate(
            ['school_id' => $schoolId, 'name' => 'XI RPL 1'],
            [
                'grade_level' => 11,
                'major' => 'RPL',
                'curriculum_id' => $curKurmer?->id,
                'vocational_program_id' => $progPPLG->id,
                'vocational_concentration_id' => $concRPL->id,
                'academic_year' => '2026/2027',
                'is_active' => true,
            ]
        );

        // Kelas XII RPL 1 (Tingkat Akhir)
        SchoolClass::firstOrCreate(
            ['school_id' => $schoolId, 'name' => 'XII RPL 1'],
            [
                'grade_level' => 12,
                'major' => 'RPL',
                'curriculum_id' => $curKurmer?->id,
                'vocational_program_id' => $progPPLG->id,
                'vocational_concentration_id' => $concRPL->id,
                'academic_year' => '2026/2027',
                'is_active' => true,
            ]
        );

        // Kelas X TJKT 1 (Fase E)
        SchoolClass::firstOrCreate(
            ['school_id' => $schoolId, 'name' => 'X TJKT 1'],
            [
                'grade_level' => 10,
                'major' => 'TJKT',
                'curriculum_id' => $curKurmer?->id,
                'vocational_program_id' => $progTJKT->id,
                'academic_year' => '2026/2027',
                'is_active' => true,
            ]
        );

        // Kelas XI TKJ 1 (Fase F)
        SchoolClass::firstOrCreate(
            ['school_id' => $schoolId, 'name' => 'XI TKJ 1'],
            [
                'grade_level' => 11,
                'major' => 'TKJ',
                'curriculum_id' => $curKurmer?->id,
                'vocational_program_id' => $progTJKT->id,
                'vocational_concentration_id' => $concTKJ->id,
                'academic_year' => '2026/2027',
                'is_active' => true,
            ]
        );

        // Kelas X AKL 1 (Fase E)
        SchoolClass::firstOrCreate(
            ['school_id' => $schoolId, 'name' => 'X AKL 1'],
            [
                'grade_level' => 10,
                'major' => 'AKL',
                'curriculum_id' => $curKurmer?->id,
                'vocational_program_id' => $progAKL->id,
                'academic_year' => '2026/2027',
                'is_active' => true,
            ]
        );
    }
}
