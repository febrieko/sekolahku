<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VocationalConcentration extends Model
{
    use HasFactory;

    protected $table = 'vocational_concentrations';

    protected $fillable = [
        'school_id',
        'vocational_program_id',
        'code',
        'dapodik_code',
        'name',
        'description',
        'competencies_summary',
        'industry_partners',
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

    public function program(): BelongsTo
    {
        return $this->belongsTo(VocationalProgram::class, 'vocational_program_id');
    }

    public function classes(): HasMany
    {
        return $this->hasMany(SchoolClass::class, 'vocational_concentration_id');
    }
}
