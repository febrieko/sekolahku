<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TimeScheduleCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'academic_year_id',
        'academic_semester',
        'name',
        'code',
        'description',
        'is_default',
        'is_active',
        'effective_start_date',
        'effective_end_date',
    ];

    protected function casts(): array
    {
        return [
            'is_default' => 'boolean',
            'is_active' => 'boolean',
            'effective_start_date' => 'date',
            'effective_end_date' => 'date',
        ];
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function periods(): HasMany
    {
        return $this->hasMany(LessonPeriod::class, 'category_id')->orderBy('day_of_week')->orderBy('start_time');
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(ClassSchedule::class, 'category_id');
    }

    /**
     * Compute dynamic validity status based on effective dates & active status.
     */
    public function getValidityStatusAttribute(): string
    {
        $today = now()->startOfDay();

        if ($this->effective_start_date && $this->effective_start_date->startOfDay()->gt($today)) {
            return 'UPCOMING';
        }

        if ($this->effective_end_date && $this->effective_end_date->endOfDay()->lt($today)) {
            return 'EXPIRED';
        }

        if ($this->is_default) {
            return 'ACTIVE_DEFAULT';
        }

        if ($this->effective_start_date && $this->effective_end_date) {
            return 'ACTIVE_PERIOD';
        }

        return $this->is_active ? 'STANDBY' : 'INACTIVE';
    }
}
