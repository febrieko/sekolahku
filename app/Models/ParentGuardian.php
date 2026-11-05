<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ParentGuardian extends Model
{
    use HasFactory;

    protected $table = 'parents';

    protected $fillable = [
        'school_id',
        'user_id',
        'no_kk',
        'father_nik',
        'father_name',
        'father_birth_year',
        'father_education',
        'father_occupation',
        'father_income_range',
        'father_phone',
        'mother_nik',
        'mother_name',
        'mother_birth_year',
        'mother_education',
        'mother_occupation',
        'mother_income_range',
        'mother_phone',
        'guardian_nik',
        'guardian_name',
        'guardian_birth_year',
        'guardian_education',
        'guardian_occupation',
        'guardian_income_range',
        'guardian_phone',
        'primary_phone',
        'address',
        'rt',
        'rw',
        'sub_district',
        'district',
        'city',
        'province',
        'postal_code',
    ];

    protected function casts(): array
    {
        return [
            'father_birth_year' => 'integer',
            'mother_birth_year' => 'integer',
            'guardian_birth_year' => 'integer',
        ];
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getFullNameAttribute(): string
    {
        return $this->father_name ?? $this->mother_name ?? $this->guardian_name ?? 'Wali Murid';
    }

    public function getNikAttribute(): ?string
    {
        return $this->father_nik ?? $this->mother_nik ?? $this->guardian_nik;
    }

    public function getEducationAttribute(): ?string
    {
        return $this->father_education ?? $this->mother_education ?? $this->guardian_education;
    }

    public function getOccupationAttribute(): ?string
    {
        return $this->father_occupation ?? $this->mother_occupation ?? $this->guardian_occupation;
    }

    public function getMonthlyIncomeAttribute(): ?string
    {
        return $this->father_income_range ?? $this->mother_income_range ?? $this->guardian_income_range;
    }

    public function getPhoneNumberAttribute(): ?string
    {
        return $this->primary_phone ?? $this->father_phone ?? $this->mother_phone ?? $this->guardian_phone;
    }

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(Student::class, 'parent_student', 'parent_id', 'student_id')
            ->withPivot('relationship')
            ->withTimestamps();
    }
}
