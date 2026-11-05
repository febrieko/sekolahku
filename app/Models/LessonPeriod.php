<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LessonPeriod extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'school_id',
        'day_of_week',
        'name',
        'period_number',
        'start_time',
        'end_time',
        'is_break',
    ];

    protected function casts(): array
    {
        return [
            'day_of_week' => 'integer',
            'period_number' => 'integer',
            'is_break' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(TimeScheduleCategory::class, 'category_id');
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(ClassSchedule::class);
    }
}
