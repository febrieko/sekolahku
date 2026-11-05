<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SchoolClass extends Model
{
    use HasFactory;

    protected $table = 'classes';

    protected $fillable = [
        'school_id',
        'curriculum_id',
        'department_id',
        'vocational_program_id',
        'vocational_concentration_id',
        'academic_year_id',
        'name',
        'grade_level',
        'major',
        'academic_year',
        'homeroom_teacher_id',
        'default_room_id',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'grade_level' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function vocationalProgram(): BelongsTo
    {
        return $this->belongsTo(VocationalProgram::class, 'vocational_program_id');
    }

    public function vocationalConcentration(): BelongsTo
    {
        return $this->belongsTo(VocationalConcentration::class, 'vocational_concentration_id');
    }

    public function curriculum(): BelongsTo
    {
        return $this->belongsTo(Curriculum::class);
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function homeroomTeacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'homeroom_teacher_id');
    }

    public function defaultRoom(): BelongsTo
    {
        return $this->belongsTo(SchoolRoom::class, 'default_room_id');
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class, 'class_id');
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(ClassSchedule::class, 'class_id');
    }
}
