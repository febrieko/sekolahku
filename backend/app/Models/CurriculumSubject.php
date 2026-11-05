<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CurriculumSubject extends Model
{
    use HasFactory;

    protected $table = 'curriculum_subjects';

    protected $fillable = [
        'curriculum_id',
        'subject_id',
        'curriculum_subject_group_id',
        'grade_level',
        'subject_group',
        'hours_per_week',
        'passing_grade',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'hours_per_week' => 'integer',
            'passing_grade' => 'float',
            'is_active' => 'boolean',
        ];
    }

    public function curriculum(): BelongsTo
    {
        return $this->belongsTo(Curriculum::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(CurriculumSubjectGroup::class, 'curriculum_subject_group_id');
    }

    public function getResolvedGroupNameAttribute(): string
    {
        if ($this->group) {
            return $this->group->name;
        }

        return match ($this->subject_group) {
            'WAJIB_UMUM' => 'Kelompok Umum (Nasional)',
            'PEMINATAN_PILIHAN' => 'Peminatan / Pilihan',
            'P5_PROJEK' => 'Projek Profil Pelajar Pancasila (P5)',
            'MULOK' => 'Muatan Lokal (Mulok)',
            'KEAGAMAAN' => 'Keagamaan / Kepesantrenan',
            default => $this->subject_group ?? 'Umum',
        };
    }

    public function getResolvedGroupBadgeAttribute(): string
    {
        if ($this->group && !empty($this->group->color)) {
            return $this->group->color;
        }

        return match ($this->subject_group) {
            'WAJIB_UMUM' => 'badge-primary',
            'PEMINATAN_PILIHAN' => 'badge-info',
            'P5_PROJEK' => 'badge-warning',
            'MULOK' => 'badge-secondary',
            'KEAGAMAAN' => 'badge-success',
            default => 'badge-dark',
        };
    }
}
