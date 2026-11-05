<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SchoolRoom extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'code',
        'name',
        'type',
        'building',
        'floor',
        'capacity',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'capacity' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function classes(): HasMany
    {
        return $this->hasMany(SchoolClass::class, 'default_room_id');
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(ClassSchedule::class, 'room_id');
    }
}

