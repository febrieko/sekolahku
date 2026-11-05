<?php

namespace App\Services;

use App\Models\AcademicYear;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\StudentClassHistory;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Penempatan rombel & kenaikan kelas peserta didik.
 *
 * Sumber kebenaran riwayat: student_class_histories (satu baris per siswa per tahun ajaran).
 * students.class_id selalu dicerminkan sebagai rombel aktif saat ini.
 */
class StudentEnrollmentService
{
    /** Aksi akhir tahun => status riwayat tahun berjalan. */
    public const ACTIONS = [
        'promote' => 'promoted',
        'retain' => 'retained',
        'graduate' => 'graduated',
        'transfer' => 'transferred',
        'dropout' => 'dropout',
    ];

    /** Aksi yang mengakhiri status siswa aktif => status di tabel students. */
    private const EXIT_STATUS = [
        'graduate' => 'graduated',
        'transfer' => 'transferred',
        'dropout' => 'dropout',
    ];

    /**
     * Tempatkan siswa pada rombel untuk tahun ajaran tertentu (buat/perbarui riwayat).
     */
    public function place(Student $student, SchoolClass $class, ?AcademicYear $year = null, ?string $startDate = null): StudentClassHistory
    {
        $year ??= $class->academicYear
            ?? AcademicYear::where('school_id', $student->school_id)->where('is_active', true)->first();

        if (! $year) {
            throw ValidationException::withMessages([
                'academic_year_id' => 'Tahun ajaran untuk rombel ini belum ditentukan.',
            ]);
        }

        $history = StudentClassHistory::updateOrCreate(
            ['student_id' => $student->id, 'academic_year_id' => $year->id],
            [
                'school_id' => $student->school_id,
                'class_id' => $class->id,
                'grade_level' => $class->grade_level,
                'status' => 'active',
                'start_date' => $startDate ?? $year->start_date,
                'end_date' => null,
            ]
        );

        $student->update(['class_id' => $class->id]);

        return $history;
    }

    /**
     * Proses kenaikan kelas massal.
     *
     * @param  array<int, array{student_id:int, action:string, target_class_id?:int|null, notes?:string|null}>  $entries
     * @return array{processed:int, summary:array<string,int>}
     */
    public function promote(int $schoolId, AcademicYear $targetYear, array $entries): array
    {
        $summary = array_fill_keys(array_keys(self::ACTIONS), 0);

        DB::transaction(function () use ($schoolId, $targetYear, $entries, &$summary) {
            foreach ($entries as $i => $entry) {
                $action = $entry['action'];
                $student = Student::where('school_id', $schoolId)->with('schoolClass')->findOrFail($entry['student_id']);

                $current = $this->currentHistory($student);

                if ($current && (int) $current->academic_year_id === (int) $targetYear->id) {
                    throw ValidationException::withMessages([
                        "entries.$i.student_id" => "{$student->full_name} sudah terdaftar di tahun ajaran {$targetYear->name}.",
                    ]);
                }

                // 1. Tutup riwayat tahun berjalan dengan hasil akhirnya.
                if ($current) {
                    $current->update([
                        'status' => self::ACTIONS[$action],
                        'end_date' => $current->academicYear?->end_date ?? now()->toDateString(),
                        'notes' => $entry['notes'] ?? $current->notes,
                    ]);
                }

                // 2. Siswa keluar (lulus/pindah/putus sekolah).
                if (isset(self::EXIT_STATUS[$action])) {
                    $student->update(['status' => self::EXIT_STATUS[$action], 'class_id' => null]);
                    $summary[$action]++;
                    continue;
                }

                // 3. Naik / tinggal kelas -> tempatkan di rombel tahun ajaran tujuan.
                $target = SchoolClass::where('school_id', $schoolId)->find($entry['target_class_id'] ?? null);
                if (! $target) {
                    throw ValidationException::withMessages([
                        "entries.$i.target_class_id" => "Rombel tujuan untuk {$student->full_name} wajib dipilih.",
                    ]);
                }

                if ($target->academic_year_id && (int) $target->academic_year_id !== (int) $targetYear->id) {
                    throw ValidationException::withMessages([
                        "entries.$i.target_class_id" => "Rombel {$target->name} bukan milik tahun ajaran {$targetYear->name}.",
                    ]);
                }

                $fromGrade = $current?->grade_level ?? $student->schoolClass?->grade_level;
                if ($fromGrade !== null) {
                    if ($action === 'promote' && $target->grade_level <= $fromGrade) {
                        throw ValidationException::withMessages([
                            "entries.$i.target_class_id" => "{$student->full_name}: rombel tujuan naik kelas harus di tingkat lebih tinggi dari {$fromGrade}.",
                        ]);
                    }
                    if ($action === 'retain' && $target->grade_level !== $fromGrade) {
                        throw ValidationException::withMessages([
                            "entries.$i.target_class_id" => "{$student->full_name}: rombel tujuan tinggal kelas harus di tingkat {$fromGrade}.",
                        ]);
                    }
                }

                $this->place($student, $target, $targetYear);
                $summary[$action]++;
            }
        });

        return ['processed' => array_sum($summary), 'summary' => $summary];
    }

    /**
     * Riwayat aktif siswa; jika belum ada (data lama), dibentuk dari rombel saat ini.
     */
    private function currentHistory(Student $student): ?StudentClassHistory
    {
        $history = $student->classHistories()->with('academicYear')
            ->where('status', 'active')
            ->latest('start_date')
            ->first();

        if ($history || ! $student->schoolClass?->academic_year_id) {
            return $history;
        }

        return StudentClassHistory::create([
            'school_id' => $student->school_id,
            'student_id' => $student->id,
            'class_id' => $student->class_id,
            'academic_year_id' => $student->schoolClass->academic_year_id,
            'grade_level' => $student->schoolClass->grade_level,
            'status' => 'active',
            'start_date' => $student->enrollment_date,
        ])->load('academicYear');
    }
}
