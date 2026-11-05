<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentDisciplineRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'student_id',
        'academic_year_id',
        'academic_semester',
        'record_type',
        'discipline_item_id',
        'amnesty_rule_id',
        'record_date',
        'points',
        'title',
        'notes',
        'location',
        'recorded_by',
        'approved_by',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'record_date' => 'date',
            'points' => 'integer',
        ];
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function disciplineItem(): BelongsTo
    {
        return $this->belongsTo(DisciplineItem::class, 'discipline_item_id');
    }

    public function amnestyRule(): BelongsTo
    {
        return $this->belongsTo(DisciplineAmnestyRule::class, 'amnesty_rule_id');
    }

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}

