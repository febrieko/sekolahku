<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DisciplineAmnestyRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'code',
        'name',
        'credit_points',
        'min_violation_points_required',
        'clean_days_required',
        'max_claims_per_semester',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'credit_points' => 'integer',
            'min_violation_points_required' => 'integer',
            'clean_days_required' => 'integer',
            'max_claims_per_semester' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }
}

