<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminBulkImportController extends Controller
{
    /**
     * Helper to get active school.
     */
    protected function getActiveSchool(Request $request): School
    {
        $schoolId = $request->query('school_id') ?? auth()->user()?->school_id;
        return School::find($schoolId) ?? School::first();
    }

    /**
     * Download Excel Template for Students.
     */
    public function downloadStudentTemplate(Request $request): StreamedResponse
    {
        $school = $this->getActiveSchool($request);
        $classes = SchoolClass::where('school_id', $school->id)->orderBy('grade_level')->orderBy('name')->get();

        $spreadsheet = new Spreadsheet();

        // --- SHEET 1: FORM IMPORT SISWA ---
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data Siswa');

        // Headers
        $headers = [
            'A1' => 'NISN*',
            'B1' => 'NIS',
            'C1' => 'Nama Lengkap*',
            'D1' => 'Jenis Kelamin (L/P)*',
            'E1' => 'Tempat Lahir',
            'F1' => 'Tanggal Lahir (YYYY-MM-DD)*',
            'G1' => 'Agama',
            'H1' => 'Nama Kelas*',
            'I1' => 'Tahun Angkatan',
            'J1' => 'NIK',
            'K1' => 'Nama Wali',
            'L1' => 'No HP/WA Wali',
            'M1' => 'Email Siswa (Opsional)',
            'N1' => 'Password (Opsional)',
        ];

        foreach ($headers as $cell => $text) {
            $sheet->setCellValue($cell, $text);
        }

        // Header Styling
        $headerRange = 'A1:N1';
        $sheet->getStyle($headerRange)->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 10,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '10B981'], // Emerald Green
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'D1D5DB'],
                ],
            ],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(28);

        // Sample Data Rows
        $sampleClass1 = $classes->first()?->name ?? 'X-1';
        $sampleClass2 = $classes->skip(1)->first()?->name ?? ($classes->first()?->name ?? 'X-2');

        $sampleRows = [
            [
                '0091234501',
                '262710001',
                'Ahmad Fauzi Pratama',
                'L',
                'Jakarta',
                '2009-05-14',
                'Islam',
                $sampleClass1,
                '2026/2027',
                '3171012345670001',
                'Budi Pratama',
                '081234567890',
                '', // email opsional (otomatis nisn@siswa.sekolahku.sch.id)
                '', // password opsional (default Siswa123!)
            ],
            [
                '0091234502',
                '262710002',
                'Siti Aisyah Rahmawati',
                'P',
                'Bandung',
                '2009-11-20',
                'Islam',
                $sampleClass2,
                '2026/2027',
                '3171012345670002',
                'Hendra Wijaya',
                '081388881111',
                '',
                '',
            ],
        ];

        $rowIdx = 2;
        foreach ($sampleRows as $row) {
            $colLetter = 'A';
            foreach ($row as $val) {
                $sheet->setCellValueExplicit($colLetter . $rowIdx, $val, DataType::TYPE_STRING);
                $colLetter++;
            }
            $sheet->getRowDimension($rowIdx)->setRowHeight(22);
            $rowIdx++;
        }

        // Apply Borders to Sample Data
        $sheet->getStyle('A2:N' . ($rowIdx - 1))->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'E5E7EB'],
                ],
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Auto-fit Column Widths
        foreach (range('A', 'N') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // --- SHEET 2: DAFTAR KELAS & PETUNJUK ---
        $infoSheet = $spreadsheet->createSheet();
        $infoSheet->setTitle('Petunjuk & Daftar Kelas');

        $infoSheet->setCellValue('A1', 'PANDUAN PENGISIAN IMPORT DATA SISWA');
        $infoSheet->getStyle('A1')->getFont()->setBold(true)->setSize(12);

        $instructions = [
            '1. Kolom dengan tanda bintang (*) WAJIB diisi.',
            '2. NISN harus berupa 10 digit angka unik.',
            '3. Jenis Kelamin hanya diisi huruf "L" (Laki-laki) atau "P" (Perempuan).',
            '4. Format tanggal lahir: YYYY-MM-DD (Contoh: 2009-08-17).',
            '5. Nama Kelas harus sama persis dengan daftar kelas aktif yang terdaftar di sekolah (Lihat tabel di samping).',
            '6. Email Siswa bersifat OPSIONAL. Jika kosong, akun login otomatis dibuat dengan username NISN.',
            '7. Siswa dapat login ke portal sekolah langsung menggunakan NISN dan Password.',
            '8. Password bersifat OPSIONAL. Jika kosong, password default adalah: Siswa123!',
        ];

        $instRow = 3;
        foreach ($instructions as $inst) {
            $infoSheet->setCellValue('A' . $instRow, $inst);
            $instRow++;
        }

        // Table of existing classes
        $infoSheet->setCellValue('D1', 'DAFTAR KELAS TERDAFTAR DI SEKOLAH');
        $infoSheet->getStyle('D1')->getFont()->setBold(true)->setSize(11);

        $infoSheet->setCellValue('D2', 'Tingkat');
        $infoSheet->setCellValue('E2', 'Nama Kelas');
        $infoSheet->setCellValue('F2', 'Jurusan / Peminatan');
        $infoSheet->getStyle('D2:F2')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4680FF']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        $cRow = 3;
        foreach ($classes as $c) {
            $infoSheet->setCellValue('D' . $cRow, 'Tingkat ' . $c->grade_level);
            $infoSheet->setCellValue('E' . $cRow, $c->name);
            $infoSheet->setCellValue('F' . $cRow, $c->department?->name ?? 'Umum');
            $cRow++;
        }

        foreach (['A', 'B', 'C', 'D', 'E', 'F'] as $col) {
            $infoSheet->getColumnDimension($col)->setAutoSize(true);
        }

        $spreadsheet->setActiveSheetIndex(0);

        $fileName = 'template_import_siswa_' . strtolower(preg_replace('/[^a-zA-Z0-9]/', '_', $school->name)) . '.xlsx';

        return new StreamedResponse(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    /**
     * Download Excel Template for Teachers / GTK.
     */
    public function downloadTeacherTemplate(Request $request): StreamedResponse
    {
        $school = $this->getActiveSchool($request);

        $spreadsheet = new Spreadsheet();

        // --- SHEET 1: FORM IMPORT GTK ---
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data GTK & Karyawan');

        $headers = [
            'A1' => 'Nama Lengkap*',
            'B1' => 'Email Login*',
            'C1' => 'Jenis Kelamin (L/P)*',
            'D1' => 'Peran Akun*',
            'E1' => 'Tugas Tambahan',
            'F1' => 'NIP',
            'G1' => 'NUPTK',
            'H1' => 'NIK',
            'I1' => 'Jenis Kepegawaian',
            'J1' => 'Status Kepegawaian',
            'K1' => 'No HP/WA',
            'L1' => 'Pendidikan Terakhir',
            'M1' => 'Jurusan/Prodi',
            'N1' => 'Password (Opsional)',
        ];

        foreach ($headers as $cell => $text) {
            $sheet->setCellValue($cell, $text);
        }

        // Header Styling
        $sheet->getStyle('A1:N1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 10,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4680FF'], // Apex Blue
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'D1D5DB'],
                ],
            ],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(28);

        // Sample Data Rows
        $sampleRows = [
            [
                'Dra. Hj. Nurhayati, M.Pd.',
                'nurhayati@sekolahku.sch.id',
                'P',
                'Guru',
                'Waka Kurikulum',
                '197008151995122001',
                '9876543210123456',
                '3171012345670003',
                'GURU_MAPEL',
                'PNS',
                '081233334444',
                'S2',
                'Pendidikan Matematika',
                '', // password opsional (default: Sekolahku2026!)
            ],
            [
                'Budi Haryono, S.Kom.',
                'budi.tu@sekolahku.sch.id',
                'L',
                'Staf TU',
                '',
                '198503102010011002',
                '',
                '3171012345670004',
                'TENAGA_ADMINISTRASI',
                'PPPK',
                '081277778888',
                'S1',
                'Sistem Informasi',
                '',
            ],
        ];

        $rowIdx = 2;
        foreach ($sampleRows as $row) {
            $colLetter = 'A';
            foreach ($row as $val) {
                $sheet->setCellValueExplicit($colLetter . $rowIdx, $val, DataType::TYPE_STRING);
                $colLetter++;
            }
            $sheet->getRowDimension($rowIdx)->setRowHeight(22);
            $rowIdx++;
        }

        $sheet->getStyle('A2:N' . ($rowIdx - 1))->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'E5E7EB'],
                ],
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        foreach (range('A', 'N') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // --- SHEET 2: PETUNJUK & REFERENSI PERAN ---
        $infoSheet = $spreadsheet->createSheet();
        $infoSheet->setTitle('Petunjuk & Referensi');

        $infoSheet->setCellValue('A1', 'PANDUAN IMPORT GURU & TENAGA KEPENDIDIKAN (GTK)');
        $infoSheet->getStyle('A1')->getFont()->setBold(true)->setSize(12);

        $instructions = [
            '1. Kolom bertanda (*) WAJIB diisi.',
            '2. Email Login harus unik dan merupakan email aktif untuk notifikasi sistem.',
            '3. Jenis Kelamin hanya huruf "L" atau "P".',
            '4. Password bersifat OPSIONAL. Jika kosong, default adalah: Sekolahku2026!',
            '5. Nilai kolom Peran Akun yang didukung:',
            '   - Guru (Pendidik KBM)',
            '   - Staf TU (Tenaga Administrasi Sekolah)',
            '   - BK (Guru Bimbingan Konseling)',
            '   - Bendahara (Staf Keuangan / Kasir)',
            '6. Nilai Tugas Tambahan yang didukung:',
            '   - Waka Kurikulum, Waka Kesiswaan, Waka Sarpras, Waka Humas',
            '   - Kepala Lab, Kepala Perpus, Pembina Osis',
            '7. Jenis Kepegawaian: GURU_MAPEL, GURU_KELAS, GURU_BK, TENAGA_ADMINISTRASI, LABORAN, PUSTAKAWAN',
            '8. Status Kepegawaian: PNS, PPPK, GTY, GTT, HONOR_SEKOLAH',
        ];

        $instRow = 3;
        foreach ($instructions as $inst) {
            $infoSheet->setCellValue('A' . $instRow, $inst);
            $instRow++;
        }

        foreach (['A', 'B'] as $col) {
            $infoSheet->getColumnDimension($col)->setAutoSize(true);
        }

        $spreadsheet->setActiveSheetIndex(0);

        $fileName = 'template_import_gtk_' . strtolower(preg_replace('/[^a-zA-Z0-9]/', '_', $school->name)) . '.xlsx';

        return new StreamedResponse(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    /**
     * Import Students from Excel / CSV.
     */
    public function importStudents(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:10240',
            'duplicate_mode' => 'nullable|in:skip,update',
        ]);

        $school = $this->getActiveSchool($request);
        $duplicateMode = $request->input('duplicate_mode', 'skip');

        $activeYear = AcademicYear::where('school_id', $school->id)->where('is_active', true)->first()
            ?? AcademicYear::where('school_id', $school->id)->latest('id')->first();

        $classes = SchoolClass::where('school_id', $school->id)->get()->keyBy(function ($c) {
            return strtolower(trim($c->name));
        });

        $file = $request->file('file');

        try {
            $spreadsheet = IOFactory::load($file->getRealPath());
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray(null, true, true, true);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membaca file Excel/CSV: ' . $e->getMessage(),
            ], 422);
        }

        if (count($rows) < 2) {
            return response()->json([
                'success' => false,
                'message' => 'File Excel kosong atau tidak memiliki baris data.',
            ], 422);
        }

        $imported = 0;
        $updated = 0;
        $skipped = 0;
        $errors = [];

        DB::beginTransaction();

        try {
            $rowNumber = 1;
            foreach ($rows as $row) {
                $rowNumber++;
                if ($rowNumber === 2) {
                    continue; // Skip Header row
                }

                $nisn = trim((string)($row['A'] ?? ''));
                $nis = trim((string)($row['B'] ?? ''));
                $fullName = trim((string)($row['C'] ?? ''));
                $gender = strtoupper(trim((string)($row['D'] ?? 'L')));
                $birthPlace = trim((string)($row['E'] ?? ''));
                $birthDateRaw = trim((string)($row['F'] ?? ''));
                $religion = trim((string)($row['G'] ?? 'Islam'));
                $className = trim((string)($row['H'] ?? ''));
                $cohortName = trim((string)($row['I'] ?? ''));
                $nik = trim((string)($row['J'] ?? ''));
                $parentName = trim((string)($row['K'] ?? ''));
                $parentPhone = trim((string)($row['L'] ?? ''));
                $customEmail = trim((string)($row['M'] ?? ''));
                $customPassword = trim((string)($row['N'] ?? ''));

                // Lewati baris kosong
                if (empty($nisn) && empty($fullName)) {
                    continue;
                }

                // Validasi data pokok
                if (empty($nisn)) {
                    $errors[] = "Baris $rowNumber: NISN tidak boleh kosong.";
                    continue;
                }
                if (empty($fullName)) {
                    $errors[] = "Baris $rowNumber: Nama lengkap siswa tidak boleh kosong.";
                    continue;
                }

                $gender = in_array($gender, ['L', 'P']) ? $gender : 'L';

                // Parsing Tanggal Lahir
                $birthDate = null;
                if (!empty($birthDateRaw)) {
                    try {
                        if (is_numeric($birthDateRaw)) {
                            $birthDate = ExcelDate::excelToDateTimeObject($birthDateRaw)->format('Y-m-d');
                        } else {
                            $birthDate = Carbon::parse($birthDateRaw)->format('Y-m-d');
                        }
                    } catch (\Exception $e) {
                        $birthDate = null;
                    }
                }

                // Resolve Kelas
                $matchedClass = $classes->get(strtolower($className));
                if (!$matchedClass && !empty($className)) {
                    // Cari parsial nama kelas
                    $matchedClass = $classes->first(function ($c) use ($className) {
                        return str_contains(strtolower($c->name), strtolower($className));
                    });
                }
                $classId = $matchedClass?->id;

                // Resolve Tahun Angkatan
                $cohortYear = null;
                if (!empty($cohortName)) {
                    $cohortYear = AcademicYear::where('school_id', $school->id)
                        ->where('name', $cohortName)
                        ->first();
                }
                $entryAcademicYearId = $cohortYear?->id ?? $activeYear?->id;

                // Check existing student by NISN in this school
                $existingStudent = Student::where('school_id', $school->id)
                    ->where('nisn', $nisn)
                    ->first();

                if ($existingStudent) {
                    if ($duplicateMode === 'skip') {
                        $skipped++;
                        continue;
                    }

                    // Update existing student
                    $existingStudent->update([
                        'nis' => $nis ?: $existingStudent->nis,
                        'full_name' => $fullName,
                        'gender' => $gender,
                        'birth_place' => $birthPlace ?: $existingStudent->birth_place,
                        'birth_date' => $birthDate ?: $existingStudent->birth_date,
                        'religion' => $religion ?: $existingStudent->religion,
                        'class_id' => $classId ?: $existingStudent->class_id,
                        'nik' => $nik ?: $existingStudent->nik,
                        'parent_name' => $parentName ?: $existingStudent->parent_name,
                        'parent_phone' => $parentPhone ?: $existingStudent->parent_phone,
                    ]);

                    // Update User name
                    if ($existingStudent->user_id) {
                        $user = User::find($existingStudent->user_id);
                        if ($user) {
                            $user->update(['name' => $fullName]);
                            if (!empty($customPassword)) {
                                $user->update(['password' => Hash::make($customPassword)]);
                            }
                        }
                    }

                    $updated++;
                    continue;
                }

                // Akun Login: Jika email dikosongkan, gunakan NISN
                $studentEmail = !empty($customEmail) ? $customEmail : ($nisn . '@siswa.sekolahku.sch.id');
                $passwordPlain = !empty($customPassword) ? $customPassword : 'Siswa123!';

                // Pastikan email user belum dipakai akun lain
                $existingUser = User::where('email', $studentEmail)->first();
                if ($existingUser) {
                    $studentEmail = $nisn . '.' . time() . '@siswa.sekolahku.sch.id';
                }

                $user = User::create([
                    'school_id' => $school->id,
                    'name' => $fullName,
                    'email' => $studentEmail,
                    'password' => Hash::make($passwordPlain),
                    'role' => 'student',
                    'is_active' => true,
                ]);

                Student::create([
                    'school_id' => $school->id,
                    'user_id' => $user->id,
                    'class_id' => $classId,
                    'nis' => $nis ?: null,
                    'nisn' => $nisn,
                    'full_name' => $fullName,
                    'gender' => $gender,
                    'birth_place' => $birthPlace ?: null,
                    'birth_date' => $birthDate,
                    'religion' => $religion ?: 'Islam',
                    'nik' => $nik ?: null,
                    'parent_name' => $parentName ?: null,
                    'parent_phone' => $parentPhone ?: null,
                    'status' => 'active',
                    'entry_academic_year_id' => $entryAcademicYearId,
                ]);

                $imported++;
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Proses import selesai. Berhasil menambahkan $imported siswa baru, memperbarui $updated siswa, dan melewati $skipped data.",
                'data' => [
                    'imported' => $imported,
                    'updated' => $updated,
                    'skipped' => $skipped,
                    'errors' => $errors,
                ],
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memproses data import: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Import Teachers & Staff from Excel / CSV.
     */
    public function importTeachers(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:10240',
            'duplicate_mode' => 'nullable|in:skip,update',
        ]);

        $school = $this->getActiveSchool($request);
        $duplicateMode = $request->input('duplicate_mode', 'skip');

        $file = $request->file('file');

        try {
            $spreadsheet = IOFactory::load($file->getRealPath());
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray(null, true, true, true);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membaca file Excel/CSV: ' . $e->getMessage(),
            ], 422);
        }

        if (count($rows) < 2) {
            return response()->json([
                'success' => false,
                'message' => 'File Excel kosong atau tidak memiliki baris data.',
            ], 422);
        }

        $imported = 0;
        $updated = 0;
        $skipped = 0;
        $errors = [];

        DB::beginTransaction();

        try {
            $rowNumber = 1;
            foreach ($rows as $row) {
                $rowNumber++;
                if ($rowNumber === 2) {
                    continue; // Skip header
                }

                $fullName = trim((string)($row['A'] ?? ''));
                $email = strtolower(trim((string)($row['B'] ?? '')));
                $gender = strtoupper(trim((string)($row['C'] ?? 'L')));
                $roleLabel = strtolower(trim((string)($row['D'] ?? 'guru')));
                $duties = trim((string)($row['E'] ?? ''));
                $nip = trim((string)($row['F'] ?? ''));
                $nuptk = trim((string)($row['G'] ?? ''));
                $nik = trim((string)($row['H'] ?? ''));
                $employmentType = strtoupper(trim((string)($row['I'] ?? 'GURU_MAPEL')));
                $employmentStatus = strtoupper(trim((string)($row['J'] ?? 'GTT')));
                $phone = trim((string)($row['K'] ?? ''));
                $education = strtoupper(trim((string)($row['L'] ?? 'S1')));
                $majorStudy = trim((string)($row['M'] ?? ''));
                $customPassword = trim((string)($row['N'] ?? ''));

                if (empty($fullName) && empty($email)) {
                    continue;
                }

                if (empty($fullName)) {
                    $errors[] = "Baris $rowNumber: Nama lengkap wajib diisi.";
                    continue;
                }
                if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $errors[] = "Baris $rowNumber: Format email tidak valid ($email).";
                    continue;
                }

                // Mapping Role Sistem
                $role = match(true) {
                    str_contains($roleLabel, 'staf') || str_contains($roleLabel, 'tu') || str_contains($roleLabel, 'admin') => 'staff',
                    str_contains($roleLabel, 'bk') || str_contains($roleLabel, 'konseling') => 'counselor',
                    str_contains($roleLabel, 'bendahara') || str_contains($roleLabel, 'kasir') => 'treasurer',
                    str_contains($roleLabel, 'kepala sekolah') => 'principal',
                    default => 'teacher',
                };

                // Mapping Tugas Tambahan
                $additionalRole = null;
                $loweredDuty = strtolower($duties);
                if (str_contains($loweredDuty, 'kurikulum')) $additionalRole = 'waka_kurikulum';
                elseif (str_contains($loweredDuty, 'kesiswaan')) $additionalRole = 'waka_kesiswaan';
                elseif (str_contains($loweredDuty, 'sarpras')) $additionalRole = 'waka_sarpras';
                elseif (str_contains($loweredDuty, 'humas') || str_contains($loweredDuty, 'hubin')) $additionalRole = 'waka_humas';
                elseif (str_contains($loweredDuty, 'lab')) $additionalRole = 'kepala_lab';
                elseif (str_contains($loweredDuty, 'perpus')) $additionalRole = 'kepala_perpus';
                elseif (str_contains($loweredDuty, 'osis')) $additionalRole = 'pembina_osis';

                $gender = in_array($gender, ['L', 'P']) ? $gender : 'L';
                $passwordPlain = !empty($customPassword) ? $customPassword : 'Sekolahku2026!';

                // Check existing user by email
                $existingUser = User::where('email', $email)->first();

                if ($existingUser) {
                    if ($duplicateMode === 'skip') {
                        $skipped++;
                        continue;
                    }

                    // Update existing User & Teacher
                    $existingUser->update([
                        'name' => $fullName,
                        'role' => $role,
                        'additional_role' => $additionalRole,
                        'phone' => $phone ?: $existingUser->phone,
                    ]);
                    if (!empty($customPassword)) {
                        $existingUser->update(['password' => Hash::make($customPassword)]);
                    }

                    $teacher = Teacher::where('school_id', $school->id)->where('user_id', $existingUser->id)->first();
                    if ($teacher) {
                        $teacher->update([
                            'full_name' => $fullName,
                            'nip' => $nip ?: $teacher->nip,
                            'nuptk' => $nuptk ?: $teacher->nuptk,
                            'nik' => $nik ?: $teacher->nik,
                            'gender' => $gender,
                            'phone' => $phone ?: $teacher->phone,
                            'employment_type' => in_array($employmentType, ['GURU_MAPEL', 'GURU_KELAS', 'GURU_BK', 'TENAGA_ADMINISTRASI', 'LABORAN', 'PUSTAKAWAN', 'KEPALA_SEKOLAH']) ? $employmentType : 'GURU_MAPEL',
                            'employment_status' => in_array($employmentStatus, ['PNS', 'CPNS', 'PPPK', 'GTY', 'GTT', 'HONOR_SEKOLAH']) ? $employmentStatus : 'GTT',
                            'last_education' => in_array($education, ['D1', 'D2', 'D3', 'D4', 'S1', 'S2', 'S3']) ? $education : 'S1',
                            'major_study' => $majorStudy ?: $teacher->major_study,
                            'additional_duties' => $duties ?: $teacher->additional_duties,
                        ]);
                    }

                    $updated++;
                    continue;
                }

                // Create new User
                $user = User::create([
                    'school_id' => $school->id,
                    'name' => $fullName,
                    'email' => $email,
                    'password' => Hash::make($passwordPlain),
                    'role' => $role,
                    'additional_role' => $additionalRole,
                    'phone' => $phone ?: null,
                    'is_active' => true,
                ]);

                // Create Teacher
                Teacher::create([
                    'school_id' => $school->id,
                    'user_id' => $user->id,
                    'nip' => $nip ?: null,
                    'nuptk' => $nuptk ?: null,
                    'nik' => $nik ?: null,
                    'full_name' => $fullName,
                    'gender' => $gender,
                    'email' => $email,
                    'phone' => $phone ?: null,
                    'employment_type' => in_array($employmentType, ['GURU_MAPEL', 'GURU_KELAS', 'GURU_BK', 'TENAGA_ADMINISTRASI', 'LABORAN', 'PUSTAKAWAN', 'KEPALA_SEKOLAH']) ? $employmentType : 'GURU_MAPEL',
                    'employment_status' => in_array($employmentStatus, ['PNS', 'CPNS', 'PPPK', 'GTY', 'GTT', 'HONOR_SEKOLAH']) ? $employmentStatus : 'GTT',
                    'last_education' => in_array($education, ['D1', 'D2', 'D3', 'D4', 'S1', 'S2', 'S3']) ? $education : 'S1',
                    'major_study' => $majorStudy ?: null,
                    'additional_duties' => $duties ?: null,
                    'is_active' => true,
                ]);

                $imported++;
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Proses import selesai. Berhasil menambahkan $imported GTK/karyawan baru, memperbarui $updated GTK, dan melewati $skipped data.",
                'data' => [
                    'imported' => $imported,
                    'updated' => $updated,
                    'skipped' => $skipped,
                    'errors' => $errors,
                ],
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memproses data import GTK: ' . $e->getMessage(),
            ], 500);
        }
    }
}

