<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Curriculum extends Model
{
    use HasFactory;

    protected $table = 'curriculums';

    protected $fillable = [
        'school_id',
        'code',
        'name',
        'target_level',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function gradeLevels(): HasMany
    {
        return $this->hasMany(CurriculumGradeLevel::class);
    }

    public function curriculumSubjects(): HasMany
    {
        return $this->hasMany(CurriculumSubject::class);
    }

    public function subjectGroups(): HasMany
    {
        return $this->hasMany(CurriculumSubjectGroup::class)->orderBy('order_no');
    }

    public function subjects(): BelongsToMany
    {
        return $this->belongsToMany(Subject::class, 'curriculum_subjects')
            ->withPivot(['id', 'grade_level', 'subject_group', 'hours_per_week', 'passing_grade', 'is_active'])
            ->withTimestamps();
    }

    public function classes(): HasMany
    {
        return $this->hasMany(SchoolClass::class, 'curriculum_id');
    }
}
