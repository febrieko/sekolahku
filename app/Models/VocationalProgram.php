<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VocationalProgram extends Model
{
    use HasFactory;

    protected $table = 'vocational_programs';

    protected $fillable = [
        'school_id',
        'vocational_field_id',
        'code',
        'name',
        'program_duration_years',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'program_duration_years' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function field(): BelongsTo
    {
        return $this->belongsTo(VocationalField::class, 'vocational_field_id');
    }

    public function concentrations(): HasMany
    {
        return $this->hasMany(VocationalConcentration::class, 'vocational_program_id');
    }

    public function classes(): HasMany
    {
        return $this->hasMany(SchoolClass::class, 'vocational_program_id');
    }
}
