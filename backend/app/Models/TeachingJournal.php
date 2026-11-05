<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TeachingJournal extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'academic_year_id',
        'academic_semester',
        'class_schedule_id',
        'teacher_id',
        'substitute_teacher_id',
        'school_class_id',
        'subject_id',
        'teaching_group_id',
        'school_room_id',
        'date',
        'time_slot_label',
        'start_period',
        'end_period',
        'meeting_number',
        'learning_objective',
        'teaching_activity',
        'obstacles_and_followup',
        'homework_assignment',
        'status',
        'attendance_summary',
        'completed_at',
        'verified_by_waka_at',
        'verified_by_waka_id',
        'waka_supervision_notes',
    ];

    protected $casts = [
        'date' => 'date',
        'start_period' => 'integer',
        'end_period' => 'integer',
        'meeting_number' => 'integer',
        'attendance_summary' => 'array',
        'completed_at' => 'datetime',
        'verified_by_waka_at' => 'datetime',
    ];

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function classSchedule(): BelongsTo
    {
        return $this->belongsTo(ClassSchedule::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class, 'teacher_id');
    }

    public function substituteTeacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class, 'substitute_teacher_id');
    }

    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'school_class_id');
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function teachingGroup(): BelongsTo
    {
        return $this->belongsTo(TeachingGroup::class);
    }

    public function schoolRoom(): BelongsTo
    {
        return $this->belongsTo(SchoolRoom::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(TeachingJournalAttendance::class);
    }

    public function verifiedByWaka(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by_waka_id');
    }

    /**
     * Hitung dan simpan ringkasan kehadiran siswa pada sesi KBM ini
     */
    public function recalculateAttendanceSummary(): array
    {
        $counts = $this->attendances()
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        $totalStudents = $this->attendances()->count();

        $summary = [
            'total' => $totalStudents,
            'present' => $counts['present'] ?? 0,
            'sick' => $counts['sick'] ?? 0,
            'excused' => $counts['excused'] ?? 0,
            'absent' => $counts['absent'] ?? 0,
            'dispensation' => $counts['dispensation'] ?? 0,
        ];

        $this->update(['attendance_summary' => $summary]);

        return $summary;
    }
}

