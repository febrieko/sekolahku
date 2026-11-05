<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DisciplineItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'category_id',
        'code',
        'name',
        'points',
        'default_sanction',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'points' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(DisciplineCategory::class, 'category_id');
    }

    public function records(): HasMany
    {
        return $this->hasMany(StudentDisciplineRecord::class, 'discipline_item_id');
    }
}

