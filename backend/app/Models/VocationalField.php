<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class VocationalField extends Model
{
    use HasFactory;

    protected $table = 'vocational_fields';

    protected $fillable = [
        'school_id',
        'code',
        'name',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function programs(): HasMany
    {
        return $this->hasMany(VocationalProgram::class, 'vocational_field_id');
    }

    public function concentrations(): HasManyThrough
    {
        return $this->hasManyThrough(VocationalConcentration::class, VocationalProgram::class, 'vocational_field_id', 'vocational_program_id');
    }
}
