<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'user_id',
        'class_id',
        // Angkatan & Pendaftaran
        'entry_academic_year_id',
        'entry_grade_level',
        'registration_type',
        'previous_school',
        // Identitas Pokok Dapodik
        'nis',
        'nisn',
        'nik',
        'no_kk',
        'full_name',
        'gender',
        'birth_place',
        'birth_date',
        'birth_certificate_no',
        'religion',
        'special_needs',
        // Alamat Domisili Dapodik
        'address',
        'rt',
        'rw',
        'sub_district',
        'district',
        'city',
        'province',
        'postal_code',
        'home_latitude',
        'home_longitude',
        'residence_type',
        'transportation_mode',
        // Kesejahteraan & Keluarga
        'parent_phone',
        'student_phone',
        'parent_name',
        'child_order',
        'siblings_count',
        'kps_receiver',
        'kps_number',
        'kip_receiver',
        'kip_number',
        'kip_name',
        // Data Fisik & Periodik
        'height_cm',
        'weight_kg',
        'head_circumference_cm',
        'distance_to_school_km',
        'travel_time_minutes',
        'blood_type',
        // Akademik & Status
        'photo_url',
        'status',
        'wallet_balance',
        'enrollment_date',
    ];

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
            'enrollment_date' => 'date',
            'home_latitude' => 'float',
            'home_longitude' => 'float',
            'wallet_balance' => 'decimal:2',
            'distance_to_school_km' => 'float',
            'kps_receiver' => 'boolean',
            'kip_receiver' => 'boolean',
            'height_cm' => 'integer',
            'weight_kg' => 'integer',
            'head_circumference_cm' => 'integer',
            'travel_time_minutes' => 'integer',
            'child_order' => 'integer',
            'siblings_count' => 'integer',
            'entry_grade_level' => 'integer',
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

    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    /**
     * Angkatan: tahun ajaran saat peserta didik pertama kali masuk.
     */
    public function entryAcademicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class, 'entry_academic_year_id');
    }

    public function classHistories(): HasMany
    {
        return $this->hasMany(StudentClassHistory::class);
    }

    public function scopeOfCohort($query, ?int $academicYearId)
    {
        return $academicYearId ? $query->where('entry_academic_year_id', $academicYearId) : $query;
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function disciplineRecords(): HasMany
    {
        return $this->hasMany(StudentDisciplineRecord::class);
    }

    public function parents(): BelongsToMany
    {
        return $this->belongsToMany(ParentGuardian::class, 'parent_student', 'student_id', 'parent_id')
            ->withPivot('relationship')
            ->withTimestamps();
    }
}
