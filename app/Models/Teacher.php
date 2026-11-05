<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Teacher extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'user_id',
        'nip',
        'nuptk',
        'nik',
        'full_name',
        'photo_path',
        'title_prefix',
        'title_suffix',
        'gender',
        'birth_place',
        'birth_date',
        'mother_maiden_name',
        'religion',
        'marital_status',
        'spouse_name',
        'spouse_job',
        'address',
        'rt',
        'rw',
        'sub_district',
        'district',
        'city',
        'province',
        'postal_code',
        'phone',
        'email',
        'employment_type',
        'employment_status',
        'sk_appointment_number',
        'sk_appointment_date',
        'tmt_appointment',
        'institution_assignment',
        'institution_assignment_status',
        'salary_source',
        'is_certified',
        'certification_number',
        'nrg',
        'certification_subject',
        'last_education',
        'major_study',
        'university_name',
        'graduation_year',
        'additional_duties',
        'teaching_hours',
        'npwp',
        'bank_name',
        'bank_account_number',
        'bank_account_name',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
            'sk_appointment_date' => 'date',
            'tmt_appointment' => 'date',
            'is_certified' => 'boolean',
            'is_active' => 'boolean',
            'teaching_hours' => 'integer',
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

    public function homeroomClasses(): HasMany
    {
        return $this->hasMany(SchoolClass::class, 'homeroom_teacher_id');
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(ClassSchedule::class);
    }

    public function teachingJournals(): HasMany
    {
        return $this->hasMany(TeachingJournal::class, 'teacher_id');
    }

    /**
     * Get formatted full name with academic titles
     */
    public function getFormattedNameAttribute(): string
    {
        $prefix = $this->title_prefix ? $this->title_prefix . ' ' : '';
        $suffix = $this->title_suffix ? ', ' . $this->title_suffix : '';
        return $prefix . $this->full_name . $suffix;
    }

    /**
     * URL Foto Resmi 4x6 GTK
     */
    public function getPhotoUrlAttribute(): ?string
    {
        if ($this->photo_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($this->photo_path)) {
            return \Illuminate\Support\Facades\Storage::disk('public')->url($this->photo_path);
        }
        return null;
    }

    /**
     * Hitung persentase kelengkapan profil Dapodik GTK (0 - 100%)
     */
    public function getProfileCompletenessAttribute(): int
    {
        $fields = [
            'full_name', 'nik', 'gender', 'birth_place', 'birth_date',
            'mother_maiden_name', 'religion', 'address', 'phone', 'email',
            'employment_type', 'employment_status', 'last_education', 'major_study',
            'university_name', 'photo_path', 'npwp', 'bank_account_number'
        ];

        $filled = 0;
        foreach ($fields as $field) {
            if (!empty($this->{$field})) {
                $filled++;
            }
        }

        return (int) round(($filled / count($fields)) * 100);
    }

    /**
     * Parse daftar tugas tambahan sebagai array
     */
    public function getDutiesListAttribute(): array
    {
        if (empty($this->additional_duties)) {
            return [];
        }

        if (is_array($this->additional_duties)) {
            return $this->additional_duties;
        }

        // Support JSON format or comma-separated string
        $decoded = json_decode($this->additional_duties, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return $decoded;
        }

        return array_map('trim', explode(',', $this->additional_duties));
    }
}
