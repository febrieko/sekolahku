<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TeachingGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'academic_year_id',
        'code',
        'name',
        'subject_id',
        'teacher_id',
        'default_room_id',
        'membership_mode',
        'religion_filter',
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

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    public function defaultRoom(): BelongsTo
    {
        return $this->belongsTo(SchoolRoom::class, 'default_room_id');
    }

    public function classes(): BelongsToMany
    {
        return $this->belongsToMany(SchoolClass::class, 'teaching_group_classes', 'teaching_group_id', 'class_id')
            ->withTimestamps();
    }

    public function manualStudents(): BelongsToMany
    {
        return $this->belongsToMany(Student::class, 'teaching_group_students', 'teaching_group_id', 'student_id')
            ->withTimestamps();
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(ClassSchedule::class, 'teaching_group_id');
    }

    /**
     * Normalize religion variants in Dapodik biodata for accurate matching.
     *
     * @return array<int, string>
     */
    public static function religionSynonyms(string $religion): array
    {
        $norm = strtolower(trim($religion));

        return match ($norm) {
            'kristen', 'protestan', 'kristen protestan' => ['kristen', 'protestan', 'kristen protestan'],
            'katolik', 'katholik' => ['katolik', 'katholik'],
            'buddha', 'budha' => ['buddha', 'budha'],
            'konghucu', 'khonghucu' => ['konghucu', 'khonghucu'],
            'islam' => ['islam'],
            'hindu' => ['hindu'],
            default => [$norm],
        };
    }

    /**
     * Resolve students participating in this teaching group based on membership_mode & student biodata.
     */
    public function resolveStudents(): Collection
    {
        if ($this->membership_mode === 'MANUAL') {
            return $this->manualStudents()
                ->with('schoolClass')
                ->orderBy('class_id')
                ->orderBy('full_name')
                ->get();
        }

        $classIds = $this->classes()->pluck('classes.id')->all();

        $query = Student::with('schoolClass')
            ->where('school_id', $this->school_id)
            ->when(! empty($classIds), fn ($q) => $q->whereIn('class_id', $classIds));

        $effectiveReligion = $this->membership_mode === 'RELIGION'
            ? ($this->religion_filter ?: $this->subject?->resolved_religion_filter)
            : null;

        if ($effectiveReligion) {
            $synonyms = self::religionSynonyms($effectiveReligion);
            $query->whereIn(\Illuminate\Support\Facades\DB::raw('LOWER(TRIM(COALESCE(religion, "")))'), $synonyms);
        }

        return $query->orderBy('class_id')->orderBy('full_name')->get();
    }
}

