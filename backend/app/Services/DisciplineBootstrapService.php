<?php

namespace App\Services;

use App\Models\DisciplineAmnestyRule;
use App\Models\DisciplineCategory;
use App\Models\DisciplineItem;
use App\Models\DisciplinePolicy;
use App\Models\DisciplineThreshold;

class DisciplineBootstrapService
{
    /**
     * Ensure default master categories, items, thresholds, amnesty rules, and policy exist for a school.
     */
    public static function ensureDefaultsForSchool(int $schoolId): DisciplinePolicy
    {
        $policy = DisciplinePolicy::firstOrCreate(
            ['school_id' => $schoolId],
            [
                'max_violation_points' => 100,
                'reset_period' => 'ACADEMIC_YEAR',
                'amnesty_enabled' => true,
                'max_amnesty_points_per_semester' => 50,
                'min_clean_days_before_amnesty' => 7,
                'auto_refer_bk_threshold' => 50,
                'allow_amnesty_for_heavy_violations' => false,
            ]
        );

        if (DisciplineCategory::where('school_id', $schoolId)->count() === 0) {
            $defaultCategories = [
                [
                    'code' => 'KAT-A',
                    'name' => 'Kerajinan & Kehadiran',
                    'severity' => 'RINGAN',
                    'color' => '#3b82f6',
                    'description' => 'Ketertiban kehadiran pagi, upacara bendera, dan kepatuhan jam KBM.',
                    'sort_order' => 1,
                    'items' => [
                        ['code' => 'A-01', 'name' => 'Terlambat hadir di sekolah lebih dari 15 menit', 'points' => 5, 'default_sanction' => 'Teguran lisan & pembinaan Guru Piket'],
                        ['code' => 'A-02', 'name' => 'Tidak mengikuti upacara bendera / apel pagi tanpa keterangan', 'points' => 10, 'default_sanction' => 'Pembinaan Wali Kelas'],
                        ['code' => 'A-03', 'name' => 'Meninggalkan kelas saat jam pelajaran tanpa izin (membolos KBM)', 'points' => 15, 'default_sanction' => 'Pencatatan Tatib & konfirmasi Wali Kelas'],
                        ['code' => 'A-04', 'name' => 'Tidak hadir tanpa keterangan (Alpha) 3 hari berturut-turut', 'points' => 20, 'default_sanction' => 'Pembinaan Wali Kelas & Guru BK'],
                    ],
                ],
                [
                    'code' => 'KAT-B',
                    'name' => 'Kerapian, Seragam & Atribut',
                    'severity' => 'RINGAN',
                    'color' => '#10b981',
                    'description' => 'Kelengkapan seragam sekolah, atribut resmi, dan kerapian penampilan.',
                    'sort_order' => 2,
                    'items' => [
                        ['code' => 'B-01', 'name' => 'Atribut seragam tidak lengkap (dasi, sabuk, badge, sepatu hitam)', 'points' => 5, 'default_sanction' => 'Penertiban atribut oleh Tim Tatib'],
                        ['code' => 'B-02', 'name' => 'Potongan atau warna rambut tidak sesuai ketentuan sekolah', 'points' => 10, 'default_sanction' => 'Peringatan merapikan rambut 1x24 jam'],
                        ['code' => 'B-03', 'name' => 'Memakai aksesoris berlebihan atau pakaian tidak sesuai jadwal hari', 'points' => 10, 'default_sanction' => 'Penertiban oleh Tim Tatib'],
                    ],
                ],
                [
                    'code' => 'KAT-C',
                    'name' => 'Sikap, Etika & Ketertiban Lingkungan',
                    'severity' => 'SEDANG',
                    'color' => '#f59e0b',
                    'description' => 'Etika pergaulan, penghormatan kepada warga sekolah, dan pemeliharaan fasilitas.',
                    'sort_order' => 3,
                    'items' => [
                        ['code' => 'C-01', 'name' => 'Membuat kegaduhan atau mengganggu kelancaran KBM di kelas', 'points' => 15, 'default_sanction' => 'Pembinaan Guru Mapel & Wali Kelas'],
                        ['code' => 'C-02', 'name' => 'Merusak atau mencoret fasilitas / sarana prasarana sekolah', 'points' => 25, 'default_sanction' => 'Wajib memperbaiki/mengganti & pembinaan Tatib'],
                        ['code' => 'C-03', 'name' => 'Membawa atau merokok (termasuk vape) di lingkungan sekitar sekolah', 'points' => 35, 'default_sanction' => 'Panggilan Orang Tua & Konseling BK'],
                        ['code' => 'C-04', 'name' => 'Bersikap tidak sopan terhadap Guru atau Tenaga Kependidikan', 'points' => 40, 'default_sanction' => 'Pembinaan Waka Kesiswaan & Guru BK'],
                    ],
                ],
                [
                    'code' => 'KAT-D',
                    'name' => 'Pelanggaran Berat & Ketertiban Umum',
                    'severity' => 'BERAT',
                    'color' => '#ef4444',
                    'description' => 'Pelanggaran berat yang membahayakan keselamatan warga sekolah dan nama baik institusi.',
                    'sort_order' => 4,
                    'items' => [
                        ['code' => 'D-01', 'name' => 'Melakukan perundungan (bullying) fisik, verbal, maupun siber', 'points' => 50, 'default_sanction' => 'SP1/SP2, Panggilan Orang Tua & Konseling BK'],
                        ['code' => 'D-02', 'name' => 'Terlibat perkelahian atau tawuran di dalam maupun luar sekolah', 'points' => 75, 'default_sanction' => 'SP2/Skorsing & Konferensi Kasus'],
                        ['code' => 'D-03', 'name' => 'Membawa senjata tajam, minuman keras, atau penyalahgunaan narkoba', 'points' => 100, 'default_sanction' => 'Konferensi Kasus Kepala Sekolah / Dikembalikan ke Orang Tua'],
                    ],
                ],
            ];

            foreach ($defaultCategories as $catData) {
                $items = $catData['items'];
                unset($catData['items']);
                $cat = DisciplineCategory::create(array_merge($catData, [
                    'school_id' => $schoolId,
                    'is_active' => true,
                ]));

                foreach ($items as $itemData) {
                    DisciplineItem::create(array_merge($itemData, [
                        'school_id' => $schoolId,
                        'category_id' => $cat->id,
                        'is_active' => true,
                    ]));
                }
            }
        }

        if (DisciplineThreshold::where('school_id', $schoolId)->count() === 0) {
            $defaultThresholds = [
                [
                    'level_code' => 'PEMBINAAN',
                    'name' => 'Pembinaan Wali Kelas',
                    'min_points' => 25,
                    'max_points' => 49,
                    'sanction_action' => 'Pembinaan intensif oleh Wali Kelas dan penandatanganan komitmen perbaikan disiplin siswa.',
                    'require_parent_call' => false,
                    'require_bk_counseling' => false,
                    'color' => '#3b82f6',
                    'sort_order' => 1,
                ],
                [
                    'level_code' => 'SP1',
                    'name' => 'Surat Peringatan 1 (SP1) & Rujukan BK',
                    'min_points' => 50,
                    'max_points' => 74,
                    'sanction_action' => 'Penerbitan SP1, pemanggilan Orang Tua/Wali ke-1, serta wajib mengikuti sesi konseling Guru BK.',
                    'require_parent_call' => true,
                    'require_bk_counseling' => true,
                    'color' => '#f59e0b',
                    'sort_order' => 2,
                ],
                [
                    'level_code' => 'SP2',
                    'name' => 'Surat Peringatan 2 (SP2) & Pembinaan Khusus',
                    'min_points' => 75,
                    'max_points' => 99,
                    'sanction_action' => 'Penerbitan SP2, pemanggilan Orang Tua/Wali ke-2 bersama Waka Kesiswaan & Guru BK, serta skorsing pembinaan terukur.',
                    'require_parent_call' => true,
                    'require_bk_counseling' => true,
                    'color' => '#f97316',
                    'sort_order' => 3,
                ],
                [
                    'level_code' => 'SP3',
                    'name' => 'SP 3 / Konferensi Kasus Kepala Sekolah',
                    'min_points' => 100,
                    'max_points' => null,
                    'sanction_action' => 'Sidang Konferensi Kasus bersama Kepala Sekolah, Waka Kesiswaan, Guru BK, Wali Kelas, dan Orang Tua/Wali.',
                    'require_parent_call' => true,
                    'require_bk_counseling' => true,
                    'color' => '#ef4444',
                    'sort_order' => 4,
                ],
            ];

            foreach ($defaultThresholds as $th) {
                DisciplineThreshold::create(array_merge($th, [
                    'school_id' => $schoolId,
                    'is_active' => true,
                ]));
            }
        }

        if (DisciplineAmnestyRule::where('school_id', $schoolId)->count() === 0) {
            $defaultAmnesties = [
                [
                    'code' => 'PMT-01',
                    'name' => 'Bakti Sosial & Kebersihan Lingkungan Sekolah',
                    'credit_points' => 10,
                    'min_violation_points_required' => 10,
                    'clean_days_required' => 7,
                    'max_claims_per_semester' => 3,
                    'description' => 'Melaksanakan tugas kebersihan / penataan fasilitas sekolah di bawah pengawasan Tim Tatib minimal 3 hari.',
                ],
                [
                    'code' => 'PMT-02',
                    'name' => 'Tuntas Konseling & Kontrak Perilaku BK',
                    'credit_points' => 15,
                    'min_violation_points_required' => 20,
                    'clean_days_required' => 14,
                    'max_claims_per_semester' => 2,
                    'description' => 'Menyelesaikan sesi bimbingan konseling dan tidak melakukan pelanggaran baru selama 14 hari berturut-turut.',
                ],
                [
                    'code' => 'PMT-03',
                    'name' => 'Program Pembinaan Karakter, Literasi & Ibadah',
                    'credit_points' => 20,
                    'min_violation_points_required' => 25,
                    'clean_days_required' => 14,
                    'max_claims_per_semester' => 2,
                    'description' => 'Menyelesaikan jurnal pembinaan karakter / literasi perpustakaan yang diverifikasi Waka Kesiswaan.',
                ],
                [
                    'code' => 'PMT-04',
                    'name' => 'Apresiasi Prestasi Kejuaraan Akademik / Non-Akademik',
                    'credit_points' => 25,
                    'min_violation_points_required' => 5,
                    'clean_days_required' => 0,
                    'max_claims_per_semester' => 2,
                    'description' => 'Meraih prestasi kejuaraan resmi yang mengharumkan nama sekolah minimal tingkat Kabupaten/Kota.',
                ],
            ];

            foreach ($defaultAmnesties as $am) {
                DisciplineAmnestyRule::create(array_merge($am, [
                    'school_id' => $schoolId,
                    'is_active' => true,
                ]));
            }
        }

        return $policy;
    }
}

