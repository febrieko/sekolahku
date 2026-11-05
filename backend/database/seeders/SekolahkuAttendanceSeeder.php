<?php

namespace Database\Seeders;

use App\Models\AcademicSemester;
use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\ClassSchedule;
use App\Models\Department;
use App\Models\LessonPeriod;
use App\Models\ParentGuardian;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\SchoolGeofence;
use App\Models\SchoolRoom;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\TeachingGroup;
use App\Models\TimeScheduleCategory;
use App\Models\User;
use App\Services\DisciplineBootstrapService;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SekolahkuAttendanceSeeder extends Seeder
{
    public function run(): void
    {
        $defaultPassword = Hash::make('password');

        // 1. Create School
        $school = School::firstOrCreate(
            ['tenant_id' => 'sman1jkt'],
            [
                'name' => 'SMA Negeri 1 Jakarta',
                'npsn' => '20101234',
                'level' => 'SMA',
                'subscription' => 'professional',
                'address' => 'Jl. Budi Utomo No.7, Ps. Baru, Sawah Besar, Jakarta Pusat',
                'phone' => '(021) 3865001',
                'email' => 'info@sman1jkt.sch.id',
                'is_active' => true,
            ]
        );

        // 2. School Geofences
        SchoolGeofence::firstOrCreate(
            ['school_id' => $school->id, 'name' => 'Kampus Utama SMAN 1 Jakarta'],
            [
                'latitude' => -6.16682700,
                'longitude' => 106.83756200,
                'radius_meters' => 150,
                'is_primary' => true,
                'wifi_ssid' => 'SMAN1_OFFICIAL_WIFI',
                'is_active' => true,
            ]
        );

        SchoolGeofence::firstOrCreate(
            ['school_id' => $school->id, 'name' => 'Gedung Olahraga & Lapangan'],
            [
                'latitude' => -6.16725000,
                'longitude' => 106.83810000,
                'radius_meters' => 100,
                'is_primary' => false,
                'wifi_ssid' => 'SMAN1_GOR',
                'is_active' => true,
            ]
        );

        // 3. Periode Akademik (Tahun Ajaran & Semester)
        $academicYear = AcademicYear::firstOrCreate(
            ['school_id' => $school->id, 'name' => '2026/2027'],
            [
                'start_date' => '2026-07-15',
                'end_date' => '2027-06-25',
                'is_active' => true,
            ]
        );

        AcademicSemester::firstOrCreate(
            ['academic_year_id' => $academicYear->id, 'semester' => 'odd'],
            [
                'start_date' => '2026-07-15',
                'end_date' => '2026-12-20',
                'is_active' => true,
            ]
        );

        AcademicSemester::firstOrCreate(
            ['academic_year_id' => $academicYear->id, 'semester' => 'even'],
            [
                'start_date' => '2027-01-05',
                'end_date' => '2027-06-25',
                'is_active' => false,
            ]
        );

        // 4. Master Waktu Belajar & Jam Pelajaran Multi-Kategori
        $catNormal = TimeScheduleCategory::firstOrCreate(
            ['school_id' => $school->id, 'code' => 'NORMAL'],
            [
                'academic_year_id' => $academicYear->id,
                'academic_semester' => 'odd',
                'name' => 'Jam KBM Normal / Reguler',
                'description' => 'Jadwal jam belajar standar harian 45 menit per jam pelajaran',
                'is_default' => true,
                'is_active' => true,
                'effective_start_date' => '2026-07-15',
                'effective_end_date' => '2026-12-20',
            ]
        );

        $catRamadhan = TimeScheduleCategory::firstOrCreate(
            ['school_id' => $school->id, 'code' => 'RAMADHAN'],
            [
                'name' => 'Jam Khusus Ramadhan',
                'description' => 'Jadwal jam belajar khusus bulan puasa, durasi 35 menit per JP pulang lebih awal',
                'is_default' => false,
                'is_active' => true,
                'effective_start_date' => '2027-02-10',
                'effective_end_date' => '2027-03-12',
            ]
        );

        TimeScheduleCategory::firstOrCreate(
            ['school_id' => $school->id, 'code' => 'EXAM'],
            [
                'name' => 'Jam Pekan Ujian (PAS/PTS)',
                'description' => 'Jadwal khusus pekan penilaian berbasis sesi asesmen 90 menit',
                'is_default' => false,
                'is_active' => true,
            ]
        );

        // Seed Sesi Jam Pelajaran untuk Jam Normal (Senin s/d Kamis)
        $periodsDef = [
            ['name' => 'Jam Ke-1', 'period_number' => 1, 'start_time' => '07:00', 'end_time' => '07:45', 'is_break' => false],
            ['name' => 'Jam Ke-2', 'period_number' => 2, 'start_time' => '07:45', 'end_time' => '08:30', 'is_break' => false],
            ['name' => 'Jam Ke-3', 'period_number' => 3, 'start_time' => '08:30', 'end_time' => '09:15', 'is_break' => false],
            ['name' => 'Istirahat Pagi', 'period_number' => null, 'start_time' => '09:15', 'end_time' => '09:45', 'is_break' => true],
            ['name' => 'Jam Ke-4', 'period_number' => 4, 'start_time' => '09:45', 'end_time' => '10:30', 'is_break' => false],
            ['name' => 'Jam Ke-5', 'period_number' => 5, 'start_time' => '10:30', 'end_time' => '11:15', 'is_break' => false],
            ['name' => 'Istirahat / Dhuhur', 'period_number' => null, 'start_time' => '11:15', 'end_time' => '12:15', 'is_break' => true],
            ['name' => 'Jam Ke-6', 'period_number' => 6, 'start_time' => '12:15', 'end_time' => '13:00', 'is_break' => false],
            ['name' => 'Jam Ke-7', 'period_number' => 7, 'start_time' => '13:00', 'end_time' => '13:45', 'is_break' => false],
        ];

        $firstPeriod = null;
        $secondPeriod = null;

        for ($day = 1; $day <= 5; $day++) {
            foreach ($periodsDef as $idx => $p) {
                $period = LessonPeriod::firstOrCreate(
                    [
                        'category_id' => $catNormal->id,
                        'school_id' => $school->id,
                        'day_of_week' => $day,
                        'name' => $p['name'],
                    ],
                    [
                        'period_number' => $p['period_number'],
                        'start_time' => $p['start_time'],
                        'end_time' => $p['end_time'],
                        'is_break' => $p['is_break'],
                    ]
                );

                if ($day === 1 && $idx === 0) $firstPeriod = $period;
                if ($day === 1 && $idx === 1) $secondPeriod = $period;
            }
        }

        // 5. Jurusan / Peminatan
        $deptMipa = Department::firstOrCreate(
            ['school_id' => $school->id, 'code' => 'MIPA'],
            ['name' => 'Matematika dan Ilmu Pengetahuan Alam', 'is_active' => true]
        );

        $deptIps = Department::firstOrCreate(
            ['school_id' => $school->id, 'code' => 'IPS'],
            ['name' => 'Ilmu Pengetahuan Sosial', 'is_active' => true]
        );

        // 6. Guru & Tenaga Kependidikan (GTK Dapodik)
        $teacherUser1 = User::firstOrCreate(
            ['email' => 'guru.dian@sman1jkt.sch.id'],
            [
                'school_id' => $school->id,
                'name' => 'Dian Prameswari, S.Pd.',
                'password' => $defaultPassword,
                'role' => 'teacher',
                'phone' => '081299990002',
                'is_active' => true,
            ]
        );

        $teacher1 = Teacher::firstOrCreate(
            ['school_id' => $school->id, 'user_id' => $teacherUser1->id],
            [
                'nip' => '198503152010012015',
                'nuptk' => '4536763665210082',
                'nik' => '3171015503850001',
                'full_name' => 'Dian Prameswari',
                'title_suffix' => 'S.Pd.',
                'gender' => 'P',
                'birth_place' => 'Jakarta',
                'birth_date' => '1985-03-15',
                'mother_maiden_name' => 'Siti Aminah',
                'religion' => 'Islam',
                'address' => 'Jl. Cempaka Putih Tengah No. 12',
                'phone' => '081299990002',
                'email' => 'guru.dian@sman1jkt.sch.id',
                'employment_type' => 'GURU_MAPEL',
                'employment_status' => 'PNS',
                'is_certified' => true,
                'certification_number' => '110202600125',
                'last_education' => 'S1',
                'major_study' => 'Pendidikan Matematika',
                'is_active' => true,
            ]
        );

        $teacherUser2 = User::firstOrCreate(
            ['email' => 'guru.bambang@sman1jkt.sch.id'],
            [
                'school_id' => $school->id,
                'name' => 'Bambang Wicaksono, M.Pd.',
                'password' => $defaultPassword,
                'role' => 'teacher',
                'phone' => '081299990003',
                'is_active' => true,
            ]
        );

        $teacher2 = Teacher::firstOrCreate(
            ['school_id' => $school->id, 'user_id' => $teacherUser2->id],
            [
                'nip' => '198207102008011008',
                'nuptk' => '1234760662200053',
                'nik' => '3171011007820002',
                'full_name' => 'Bambang Wicaksono',
                'title_suffix' => 'M.Pd.',
                'gender' => 'L',
                'birth_place' => 'Solo',
                'birth_date' => '1982-07-10',
                'mother_maiden_name' => 'Sri Wahyuni',
                'religion' => 'Islam',
                'address' => 'Jl. Rawamangun Muka No. 8',
                'phone' => '081299990003',
                'email' => 'guru.bambang@sman1jkt.sch.id',
                'employment_type' => 'GURU_MAPEL',
                'employment_status' => 'PNS',
                'is_certified' => true,
                'certification_number' => '110202600126',
                'last_education' => 'S2',
                'major_study' => 'Pendidikan Fisika',
                'is_active' => true,
            ]
        );

        // 7. Mata Pelajaran
        $subMtk = Subject::firstOrCreate(
            ['school_id' => $school->id, 'code' => 'MTK'],
            ['name' => 'Matematika Wajib', 'category' => 'A_MUATAN_NASIONAL', 'color' => '#4680ff', 'passing_grade' => 75.00]
        );

        $subFis = Subject::firstOrCreate(
            ['school_id' => $school->id, 'code' => 'FIS'],
            ['name' => 'Fisika Peminatan', 'category' => 'C_KEJURUAN_PEMINATAN', 'color' => '#10b981', 'passing_grade' => 75.00]
        );

        $subPai = Subject::firstOrCreate(
            ['school_id' => $school->id, 'code' => 'PAI'],
            ['name' => 'Pendidikan Agama Islam', 'category' => 'A_MUATAN_NASIONAL', 'color' => '#059669', 'religion_filter' => 'Islam', 'passing_grade' => 75.00]
        );

        $subPak = Subject::firstOrCreate(
            ['school_id' => $school->id, 'code' => 'PAK'],
            ['name' => 'Pendidikan Agama Kristen', 'category' => 'A_MUATAN_NASIONAL', 'color' => '#8b5cf6', 'religion_filter' => 'Kristen', 'passing_grade' => 75.00]
        );

        $teacherUserPak = User::firstOrCreate(
            ['email' => 'guru.maria@sman1jkt.sch.id'],
            [
                'school_id' => $school->id,
                'name' => 'Maria Tarigan, M.Pd.K.',
                'password' => $defaultPassword,
                'role' => 'teacher',
                'phone' => '081299990009',
                'is_active' => true,
            ]
        );

        $teacherPak = Teacher::firstOrCreate(
            ['school_id' => $school->id, 'user_id' => $teacherUserPak->id],
            [
                'nip' => '198709122014022003',
                'nuptk' => '7744765666210099',
                'nik' => '3171015209870009',
                'full_name' => 'Maria Tarigan',
                'title_suffix' => 'M.Pd.K.',
                'gender' => 'P',
                'birth_place' => 'Medan',
                'birth_date' => '1987-09-12',
                'mother_maiden_name' => 'Rospita Ginting',
                'religion' => 'Kristen',
                'address' => 'Jl. Kramat Raya No. 21',
                'phone' => '081299990009',
                'email' => 'guru.maria@sman1jkt.sch.id',
                'employment_type' => 'GURU_MAPEL',
                'employment_status' => 'PNS',
                'is_certified' => true,
                'certification_number' => '110202600199',
                'last_education' => 'S2',
                'major_study' => 'Pendidikan Agama Kristen',
                'is_active' => true,
            ]
        );

        // 8. Kelas (Rombel)
        $class1 = SchoolClass::firstOrCreate(
            ['school_id' => $school->id, 'name' => 'X MIPA 1'],
            [
                'department_id' => $deptMipa->id,
                'academic_year_id' => $academicYear->id,
                'grade_level' => 10,
                'major' => 'MIPA',
                'academic_year' => '2026/2027',
                'homeroom_teacher_id' => $teacherUser1->id,
                'is_active' => true,
            ]
        );

        $class2 = SchoolClass::firstOrCreate(
            ['school_id' => $school->id, 'name' => 'X MIPA 2'],
            [
                'department_id' => $deptMipa->id,
                'academic_year_id' => $academicYear->id,
                'grade_level' => 10,
                'major' => 'MIPA',
                'academic_year' => '2026/2027',
                'homeroom_teacher_id' => $teacherUser2->id,
                'is_active' => true,
            ]
        );

        $class3 = SchoolClass::firstOrCreate(
            ['school_id' => $school->id, 'name' => 'XI IPS 1'],
            [
                'department_id' => $deptIps->id,
                'academic_year_id' => $academicYear->id,
                'grade_level' => 11,
                'major' => 'IPS',
                'academic_year' => '2026/2027',
                'is_active' => true,
            ]
        );

        // 8b. Master Ruangan Kelas & Laboratorium
        $roomX1 = SchoolRoom::firstOrCreate(
            ['school_id' => $school->id, 'code' => 'R-XMIPA1'],
            [
                'name' => 'R. Kelas X-MIPA-1',
                'type' => 'CLASSROOM',
                'building' => 'Gedung Utama A',
                'floor' => 'Lantai 1',
                'capacity' => 36,
                'is_active' => true,
            ]
        );
        $class1->update(['default_room_id' => $roomX1->id]);

        $roomX2 = SchoolRoom::firstOrCreate(
            ['school_id' => $school->id, 'code' => 'R-XMIPA2'],
            [
                'name' => 'R. Kelas X-MIPA-2',
                'type' => 'CLASSROOM',
                'building' => 'Gedung Utama A',
                'floor' => 'Lantai 1',
                'capacity' => 36,
                'is_active' => true,
            ]
        );
        $class2->update(['default_room_id' => $roomX2->id]);

        $roomLabFis = SchoolRoom::firstOrCreate(
            ['school_id' => $school->id, 'code' => 'LAB-FIS'],
            [
                'name' => 'Lab Fisika',
                'type' => 'LABORATORY',
                'building' => 'Gedung Sains',
                'floor' => 'Lantai 2',
                'capacity' => 36,
                'is_active' => true,
            ]
        );

        // 9. Jadwal Pelajaran (Schedules)
        if ($firstPeriod) {
            ClassSchedule::firstOrCreate(
                [
                    'school_id' => $school->id,
                    'class_id' => $class1->id,
                    'lesson_period_id' => $firstPeriod->id,
                    'academic_year_id' => $academicYear->id,
                ],
                [
                    'category_id' => $catNormal->id,
                    'subject_id' => $subMtk->id,
                    'teacher_id' => $teacher1->id,
                    'academic_semester' => 'odd',
                    'room_id' => $roomX1->id,
                    'room_name' => $roomX1->name,
                ]
            );
        }

        if ($secondPeriod) {
            ClassSchedule::firstOrCreate(
                [
                    'school_id' => $school->id,
                    'class_id' => $class2->id,
                    'lesson_period_id' => $secondPeriod->id,
                    'academic_year_id' => $academicYear->id,
                ],
                [
                    'category_id' => $catNormal->id,
                    'subject_id' => $subFis->id,
                    'teacher_id' => $teacher2->id,
                    'academic_semester' => 'odd',
                    'room_id' => $roomLabFis->id,
                    'room_name' => $roomLabFis->name,
                ]
            );
        }

        // 10. Siswa & Akun (Standar Dapodik)
        $studentsData = [
            [
                'name' => 'Ahmad Fauzi',
                'email' => 'ahmad.fauzi@sekolahku.test',
                'nis' => '20261001',
                'nisn' => '0081234561',
                'nik' => '3171011505080001',
                'gender' => 'L',
                'religion' => 'Islam',
                'class_id' => $class1->id,
                'parent_name' => 'H. Hendra Wijaya',
                'parent_phone' => '081388881111',
            ],
            [
                'name' => 'Siti Nurhaliza',
                'email' => 'siti.nurhaliza@sekolahku.test',
                'nis' => '20261002',
                'nisn' => '0081234562',
                'nik' => '3171015206080002',
                'gender' => 'P',
                'religion' => 'Islam',
                'class_id' => $class1->id,
                'parent_name' => 'Ibu Rahmawati',
                'parent_phone' => '081388881112',
            ],
            [
                'name' => 'Budi Santoso',
                'email' => 'budi.santoso@sekolahku.test',
                'nis' => '20261003',
                'nisn' => '0081234563',
                'nik' => '3171011807080003',
                'gender' => 'L',
                'religion' => 'Kristen',
                'class_id' => $class1->id,
                'parent_name' => 'Bpk. Joko Santoso',
                'parent_phone' => '081388881113',
            ],
            [
                'name' => 'Cantika Dewi',
                'email' => 'cantika.dewi@sekolahku.test',
                'nis' => '20261004',
                'nisn' => '0081234564',
                'nik' => '3171016508080004',
                'gender' => 'P',
                'religion' => 'Islam',
                'class_id' => $class2->id,
                'parent_name' => 'Bpk. Surya Wijaya',
                'parent_phone' => '081388881114',
            ],
            [
                'name' => 'Doni Pratama',
                'email' => 'doni.pratama@sekolahku.test',
                'nis' => '20261005',
                'nisn' => '0081234565',
                'nik' => '3171012109080005',
                'gender' => 'L',
                'religion' => 'Kristen',
                'class_id' => $class2->id,
                'parent_name' => 'Ibu Wulandari',
                'parent_phone' => '081388881115',
            ],
            [
                'name' => 'Farhan Maulana',
                'email' => 'farhan.maulana@sekolahku.test',
                'nis' => '20261006',
                'nisn' => '0081234566',
                'nik' => '3171011110070006',
                'gender' => 'L',
                'religion' => 'Islam',
                'class_id' => $class3->id,
                'parent_name' => 'Bpk. Ridwan',
                'parent_phone' => '081388881116',
            ],
        ];

        $createdStudents = [];

        foreach ($studentsData as $data) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'school_id' => $school->id,
                    'name' => $data['name'],
                    'password' => $defaultPassword,
                    'role' => 'student',
                    'phone' => '08571234' . substr($data['nis'], -4),
                    'is_active' => true,
                ]
            );

            $student = Student::firstOrCreate(
                ['school_id' => $school->id, 'nis' => $data['nis']],
                [
                    'user_id' => $user->id,
                    'class_id' => $data['class_id'],
                    'nisn' => $data['nisn'],
                    'nik' => $data['nik'],
                    'no_kk' => '3171012304050001',
                    'full_name' => $data['name'],
                    'gender' => $data['gender'],
                    'religion' => $data['religion'],
                    'birth_date' => '2010-05-15',
                    'birth_place' => 'Jakarta',
                    'address' => 'Jl. Salemba Raya No. 45, Jakarta Pusat',
                    'parent_name' => $data['parent_name'],
                    'parent_phone' => $data['parent_phone'],
                    'status' => 'active',
                    'wallet_balance' => 50000.00,
                    'enrollment_date' => '2026-07-15',
                    'entry_academic_year_id' => $academicYear->id,
                    'registration_type' => 'baru',
                ]
            );

            if ($student->religion !== $data['religion']) {
                $student->update(['religion' => $data['religion']]);
            }

            $studentClass = \App\Models\SchoolClass::find($data['class_id']);
            if (! $student->entry_grade_level && $studentClass) {
                $student->update(['entry_grade_level' => $studentClass->grade_level]);
            }

            \App\Models\StudentClassHistory::firstOrCreate(
                ['student_id' => $student->id, 'academic_year_id' => $academicYear->id],
                [
                    'school_id' => $school->id,
                    'class_id' => $data['class_id'],
                    'grade_level' => $studentClass?->grade_level,
                    'status' => 'active',
                    'start_date' => '2026-07-15',
                ]
            );

            $createdStudents[] = $student;
        }

        // 11. Orang Tua / Wali (Multi-Anak Dapodik)
        $parentUser = User::firstOrCreate(
            ['email' => 'orangtua.hendra@sekolahku.test'],
            [
                'school_id' => $school->id,
                'name' => 'H. Hendra Wijaya',
                'password' => $defaultPassword,
                'role' => 'parent',
                'phone' => '081388881111',
                'is_active' => true,
            ]
        );

        $parent = ParentGuardian::firstOrCreate(
            ['school_id' => $school->id, 'user_id' => $parentUser->id],
            [
                'no_kk' => '3171012304050001',
                'father_nik' => '3171011204780001',
                'father_name' => 'H. Hendra Wijaya',
                'father_birth_year' => 1978,
                'father_education' => 'D4/S1',
                'father_occupation' => 'Wiraswasta',
                'father_income_range' => '5.000.000 - 20.000.000',
                'father_phone' => '081388881111',
                'mother_nik' => '3171015509800002',
                'mother_name' => 'Hj. Halimah',
                'mother_birth_year' => 1980,
                'mother_education' => 'SMA',
                'mother_occupation' => 'Ibu Rumah Tangga',
                'primary_phone' => '081388881111',
                'address' => 'Jl. Salemba Raya No. 45',
            ]
        );

        // Relasi Kartu Keluarga Multi-Anak: Hubungkan Ahmad Fauzi (anak 1) ke orang tua ini
        $parent->students()->syncWithoutDetaching([
            $createdStudents[0]->id => ['relationship' => 'father'],
        ]);

        // 12. Sample Initial Attendances for today
        $today = Carbon::today()->toDateString();

        Attendance::firstOrCreate(
            ['school_id' => $school->id, 'student_id' => $createdStudents[0]->id, 'date' => $today],
            [
                'check_in_at' => '06:58:30',
                'status' => 'present',
                'method' => 'selfie',
                'latitude' => -6.16684000,
                'longitude' => 106.83758000,
                'gps_accuracy' => 8.5,
                'distance_meters' => 24.3,
                'is_within_radius' => true,
                'is_mock_location' => false,
                'device_info' => 'Android 14 (Samsung SM-A546B)',
                'notes' => 'Presensi selfie mandiri di gerbang utama',
            ]
        );

        Attendance::firstOrCreate(
            ['school_id' => $school->id, 'student_id' => $createdStudents[1]->id, 'date' => $today],
            [
                'check_in_at' => '07:22:15',
                'status' => 'late',
                'method' => 'selfie',
                'latitude' => -6.16690000,
                'longitude' => 106.83762000,
                'gps_accuracy' => 12.0,
                'distance_meters' => 38.6,
                'is_within_radius' => true,
                'is_mock_location' => false,
                'device_info' => 'iOS 17.5 (iPhone 14)',
                'notes' => 'Terlambat karena kendala transportasi KRL',
            ]
        );

        Attendance::firstOrCreate(
            ['school_id' => $school->id, 'student_id' => $createdStudents[3]->id, 'date' => $today],
            [
                'status' => 'sick',
                'method' => 'manual',
                'verified_by' => $teacherUser2->id,
                'notes' => 'Surat dokter terlampir: demam dan flu (istirahat 2 hari)',
            ]
        );

        Attendance::firstOrCreate(
            ['school_id' => $school->id, 'student_id' => $createdStudents[4]->id, 'date' => $today],
            [
                'status' => 'excused',
                'method' => 'manual',
                'verified_by' => $teacherUser2->id,
                'notes' => 'Izin dispensasi: Mewakili sekolah Olimpiade Sains Tingkat Provinsi',
            ]
        );

        // 13. Waka Kesiswaan & Master Tata Tertib / Kredit Poin
        User::firstOrCreate(
            ['email' => 'waka.kesiswaan@sman1jkt.sch.id'],
            [
                'school_id' => $school->id,
                'name' => 'Drs. Rahmat Hidayat (Waka Kesiswaan)',
                'password' => $defaultPassword,
                'role' => 'waka_kesiswaan',
                'phone' => '081299990009',
                'is_active' => true,
            ]
        );

        DisciplineBootstrapService::ensureDefaultsForSchool($school->id);

        // 14. Sample Kelompok Mapel & Jadwal Kelas Gabungan (Pendidikan Agama Kristen Lintas Rombel)
        $roomPak = SchoolRoom::firstOrCreate(
            ['school_id' => $school->id, 'code' => 'R-PAK'],
            [
                'name' => 'Ruang Agama Kristen',
                'type' => 'CLASSROOM',
                'building' => 'Gedung Utama B',
                'floor' => 'Lantai 2',
                'capacity' => 30,
                'is_active' => true,
            ]
        );

        $grpPak = TeachingGroup::firstOrCreate(
            ['school_id' => $school->id, 'code' => 'GRP-PAK-X'],
            [
                'academic_year_id' => $academicYear->id,
                'name' => 'PAK Kelas X (Gabungan X MIPA 1 & X MIPA 2)',
                'subject_id' => $subPak->id,
                'teacher_id' => $teacherPak->id,
                'default_room_id' => $roomPak->id,
                'membership_mode' => 'RELIGION',
                'religion_filter' => 'Kristen',
                'description' => 'Kelompok belajar Pendidikan Agama Kristen lintas rombel kelas X berdasarkan biodata agama siswa.',
                'is_active' => true,
            ]
        );
        $grpPak->classes()->syncWithoutDetaching([$class1->id, $class2->id]);

        $thursdayPeriods = LessonPeriod::where('category_id', $catNormal->id)
            ->where('day_of_week', 4)
            ->where('is_break', false)
            ->orderBy('start_time')
            ->take(2)
            ->get();

        foreach ($thursdayPeriods as $thPeriod) {
            // Jadwal reguler PAI di X MIPA 1 (otomatis hanya menjaring siswa Islam dari biodata X MIPA 1)
            ClassSchedule::firstOrCreate(
                [
                    'school_id' => $school->id,
                    'class_id' => $class1->id,
                    'is_combined' => false,
                    'lesson_period_id' => $thPeriod->id,
                    'academic_year_id' => $academicYear->id,
                    'academic_semester' => 'odd',
                ],
                [
                    'category_id' => $catNormal->id,
                    'student_filter_type' => 'RELIGION',
                    'student_filter_value' => 'Islam',
                    'subject_id' => $subPai->id,
                    'teacher_id' => $teacher1->id,
                    'room_id' => $roomX1->id,
                    'room_name' => $roomX1->name,
                ]
            );

            // Jadwal kelas gabungan PAK untuk X MIPA 1 + X MIPA 2 pada jam yang sama (tampil di Tabel Khusus Kelas Gabungan)
            $combinedSlot = ClassSchedule::firstOrCreate(
                [
                    'school_id' => $school->id,
                    'is_combined' => true,
                    'teaching_group_id' => $grpPak->id,
                    'lesson_period_id' => $thPeriod->id,
                    'academic_year_id' => $academicYear->id,
                    'academic_semester' => 'odd',
                ],
                [
                    'category_id' => $catNormal->id,
                    'class_id' => $class1->id,
                    'student_filter_type' => 'TEACHING_GROUP',
                    'student_filter_value' => 'Kristen',
                    'subject_id' => $subPak->id,
                    'teacher_id' => $teacherPak->id,
                    'room_id' => $roomPak->id,
                    'room_name' => $roomPak->name,
                    'notes' => 'Kelas Gabungan Lintas Rombel',
                ]
            );
            $combinedSlot->combinedClasses()->syncWithoutDetaching([$class1->id, $class2->id]);
        }
    }
}
