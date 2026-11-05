<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DisciplineThreshold extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'level_code',
        'name',
        'min_points',
        'max_points',
        'sanction_action',
        'require_parent_call',
        'require_bk_counseling',
        'color',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'min_points' => 'integer',
            'max_points' => 'integer',
            'require_parent_call' => 'boolean',
            'require_bk_counseling' => 'boolean',
            'sort_order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }
}

