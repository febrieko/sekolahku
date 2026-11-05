<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class School extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'name',
        'npsn',
        'level',
        'subscription',
        'school_status',
        'accreditation',
        'establishment_decree',
        'establishment_date',
        'operational_license_decree',
        'operational_license_date',
        'tax_number',
        'postal_code',
        'rt_rw',
        'sub_village',
        'village',
        'subdistrict',
        'city',
        'province',
        'latitude',
        'longitude',
        'website',
        'fax',
        'principal_name',
        'principal_nip',
        'address',
        'phone',
        'email',
        'logo_url',
        'is_active',
        'student_profile_edit_enabled',
        'student_profile_edit_start',
        'student_profile_edit_end',
        'gtk_profile_edit_enabled',
        'gtk_profile_edit_start',
        'gtk_profile_edit_end',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'student_profile_edit_enabled' => 'boolean',
            'student_profile_edit_start' => 'date',
            'student_profile_edit_end' => 'date',
            'gtk_profile_edit_enabled' => 'boolean',
            'gtk_profile_edit_start' => 'date',
            'gtk_profile_edit_end' => 'date',
        ];
    }

    /**
     * Apakah periode pemutakhiran biodata profil siswa sedang dibuka oleh sekolah.
     */
    public function isStudentProfileEditOpen(): bool
    {
        if (! $this->student_profile_edit_enabled) {
            return false;
        }

        $today = now()->toDateString();

        if ($this->student_profile_edit_start && $today < $this->student_profile_edit_start->toDateString()) {
            return false;
        }

        if ($this->student_profile_edit_end && $today > $this->student_profile_edit_end->toDateString()) {
            return false;
        }

        return true;
    }

    /**
     * Apakah periode pemutakhiran biodata profil GTK sedang dibuka oleh sekolah.
     */
    public function isGtkProfileEditOpen(): bool
    {
        if (! $this->gtk_profile_edit_enabled) {
            return false;
        }

        $today = now()->toDateString();

        if ($this->gtk_profile_edit_start && $today < $this->gtk_profile_edit_start->toDateString()) {
            return false;
        }

        if ($this->gtk_profile_edit_end && $today > $this->gtk_profile_edit_end->toDateString()) {
            return false;
        }

        return true;
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function classes(): HasMany
    {
        return $this->hasMany(SchoolClass::class);
    }

    public function rooms(): HasMany
    {
        return $this->hasMany(SchoolRoom::class);
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    public function geofences(): HasMany
    {
        return $this->hasMany(SchoolGeofence::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function isVocational(): bool
    {
        return strtoupper($this->level ?? '') === 'SMK';
    }

    public function isSeniorHigh(): bool
    {
        return strtoupper($this->level ?? '') === 'SMA';
    }

    public function isJuniorHigh(): bool
    {
        return strtoupper($this->level ?? '') === 'SMP';
    }

    public function isElementary(): bool
    {
        return strtoupper($this->level ?? '') === 'SD';
    }

    public function getGradeLevels(): array
    {
        return match (strtoupper($this->level ?? '')) {
            'SD' => [1, 2, 3, 4, 5, 6],
            'SMP' => [7, 8, 9],
            'SMA' => [10, 11, 12],
            'SMK' => [10, 11, 12, 13],
            default => [10, 11, 12],
        };
    }

    public function vocationalFields(): HasMany
    {
        return $this->hasMany(VocationalField::class);
    }

    public function vocationalPrograms(): HasMany
    {
        return $this->hasMany(VocationalProgram::class);
    }

    public function vocationalConcentrations(): HasMany
    {
        return $this->hasMany(VocationalConcentration::class);
    }
}
