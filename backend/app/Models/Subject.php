<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subject extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'code',
        'name',
        'category',
        'color',
        'religion_filter',
        'passing_grade',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'passing_grade' => 'float',
            'is_active' => 'boolean',
        ];
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(ClassSchedule::class);
    }

    public function curriculums(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Curriculum::class, 'curriculum_subjects')
            ->withPivot(['id', 'grade_level', 'subject_group', 'hours_per_week', 'passing_grade', 'is_active'])
            ->withTimestamps();
    }

    public function curriculumSubjects(): HasMany
    {
        return $this->hasMany(CurriculumSubject::class);
    }

    public function getGroupAttribute(): string
    {
        $cs = $this->curriculumSubjects->first();
        return $cs ? $cs->subject_group : ($this->category ?? 'WAJIB_UMUM');
    }

    public function getWeeklyHoursAttribute(): int
    {
        $cs = $this->curriculumSubjects->first();
        return $cs ? $cs->hours_per_week : 2;
    }

    public function getResolvedColorAttribute(): string
    {
        if (! empty($this->color) && preg_match('/^#[0-9A-Fa-f]{6}$/', $this->color)) {
            return $this->color;
        }

        $palette = [
            '#4680ff', // Blue
            '#10b981', // Emerald
            '#f59e0b', // Amber
            '#8b5cf6', // Violet
            '#ec4899', // Pink
            '#06b6d4', // Cyan
            '#ef4444', // Rose
            '#14b8a6', // Teal
            '#f97316', // Orange
            '#6366f1', // Indigo
        ];

        $idx = abs(crc32((string) ($this->code ?: $this->id))) % count($palette);

        return $palette[$idx];
    }

    /**
     * Detect target student religion automatically from explicit religion_filter or subject name/code.
     */
    public function getResolvedReligionFilterAttribute(): ?string
    {
        if (! empty($this->religion_filter)) {
            return trim($this->religion_filter);
        }

        return self::detectReligionFromName($this->name, $this->code);
    }

    public static function detectReligionFromName(?string $name, ?string $code = null): ?string
    {
        $haystack = strtolower(trim((string) $name));
        $normCode = strtoupper(trim((string) $code));

        if ($haystack === '' && $normCode === '') {
            return null;
        }

        if (str_contains($haystack, 'agama islam') || in_array($normCode, ['PAI', 'PABP-IS'], true)) {
            return 'Islam';
        }
        if (str_contains($haystack, 'agama kristen') || str_contains($haystack, 'protestan') || in_array($normCode, ['PAK', 'PABP-KR'], true)) {
            return 'Kristen';
        }
        if (str_contains($haystack, 'agama katolik') || str_contains($haystack, 'agama katholik') || in_array($normCode, ['PAKAT', 'PABP-KT'], true)) {
            return 'Katolik';
        }
        if (str_contains($haystack, 'agama hindu') || in_array($normCode, ['PAH', 'PABP-HD'], true)) {
            return 'Hindu';
        }
        if (str_contains($haystack, 'agama buddha') || str_contains($haystack, 'agama budha') || in_array($normCode, ['PAB', 'PABP-BD'], true)) {
            return 'Buddha';
        }
        if (str_contains($haystack, 'agama konghucu') || str_contains($haystack, 'agama khonghucu') || in_array($normCode, ['PAKHC', 'PABP-KH'], true)) {
            return 'Konghucu';
        }

        return null;
    }
}
