<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Riwayat rombel peserta didik per tahun ajaran.
 */
class StudentClassHistory extends Model
{
    use HasFactory;

    public const STATUS_LABELS = [
        'active' => 'Sedang Berjalan',
        'promoted' => 'Naik Kelas',
        'retained' => 'Tinggal Kelas',
        'graduated' => 'Lulus',
        'transferred' => 'Pindah',
        'dropout' => 'Putus Sekolah',
    ];

    protected $fillable = [
        'school_id',
        'student_id',
        'class_id',
        'academic_year_id',
        'grade_level',
        'status',
        'start_date',
        'end_date',
        'notes',
    ];

    protected $appends = ['status_label'];

    protected function casts(): array
    {
        return [
            'grade_level' => 'integer',
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUS_LABELS[$this->status] ?? $this->status;
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }
}
