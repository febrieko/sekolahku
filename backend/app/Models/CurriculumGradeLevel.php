<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CurriculumGradeLevel extends Model
{
    use HasFactory;

    protected $table = 'curriculum_grade_levels';

    protected $fillable = [
        'school_id',
        'curriculum_id',
        'grade_level',
        'phase',
    ];

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function curriculum(): BelongsTo
    {
        return $this->belongsTo(Curriculum::class);
    }
}
