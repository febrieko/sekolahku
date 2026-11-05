<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CurriculumSubjectGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'curriculum_id',
        'code',
        'name',
        'description',
        'order_no',
        'color',
    ];

    protected function casts(): array
    {
        return [
            'order_no' => 'integer',
        ];
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function curriculum(): BelongsTo
    {
        return $this->belongsTo(Curriculum::class);
    }

    public function curriculumSubjects(): HasMany
    {
        return $this->hasMany(CurriculumSubject::class, 'curriculum_subject_group_id');
    }

    public function subjects(): \Illuminate\Database\Eloquent\Relations\HasManyThrough
    {
        return $this->hasManyThrough(
            Subject::class,
            CurriculumSubject::class,
            'curriculum_subject_group_id',
            'id',
            'id',
            'subject_id'
        );
    }
}

