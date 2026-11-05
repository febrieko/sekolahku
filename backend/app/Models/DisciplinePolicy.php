<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DisciplinePolicy extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'max_violation_points',
        'reset_period',
        'amnesty_enabled',
        'max_amnesty_points_per_semester',
        'min_clean_days_before_amnesty',
        'auto_refer_bk_threshold',
        'allow_amnesty_for_heavy_violations',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'max_violation_points' => 'integer',
            'amnesty_enabled' => 'boolean',
            'max_amnesty_points_per_semester' => 'integer',
            'min_clean_days_before_amnesty' => 'integer',
            'auto_refer_bk_threshold' => 'integer',
            'allow_amnesty_for_heavy_violations' => 'boolean',
        ];
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}

