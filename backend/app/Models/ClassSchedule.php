<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\DB;

class ClassSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'category_id',
        'class_id',
        'is_combined',
        'teaching_group_id',
        'student_filter_type',
        'student_filter_value',
        'subject_id',
        'teacher_id',
        'lesson_period_id',
        'academic_year_id',
        'academic_semester',
        'room_id',
        'room_name',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'is_combined' => 'boolean',
        ];
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(TimeScheduleCategory::class, 'category_id');
    }

    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function combinedClasses(): BelongsToMany
    {
        return $this->belongsToMany(SchoolClass::class, 'class_schedule_classes', 'class_schedule_id', 'class_id')
            ->withTimestamps();
    }

    public function teachingGroup(): BelongsTo
    {
        return $this->belongsTo(TeachingGroup::class, 'teaching_group_id');
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    public function lessonPeriod(): BelongsTo
    {
        return $this->belongsTo(LessonPeriod::class);
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(SchoolRoom::class, 'room_id');
    }

    /**
     * Return all participating class IDs (multi-class if combined, or single class_id).
     *
     * @return array<int, int>
     */
    public function getParticipatingClassIdsAttribute(): array
    {
        if ($this->relationLoaded('combinedClasses') && $this->combinedClasses->isNotEmpty()) {
            return $this->combinedClasses->pluck('id')->map(fn ($id) => (int) $id)->values()->all();
        }

        if ($this->is_combined) {
            $ids = $this->combinedClasses()->pluck('classes.id')->map(fn ($id) => (int) $id)->values()->all();
            if (! empty($ids)) {
                return $ids;
            }
        }

        return $this->class_id ? [(int) $this->class_id] : [];
    }

    /**
     * Return human-readable class list (e.g. "X MIPA 1, X MIPA 2" or "X MIPA 1").
     */
    public function getParticipatingClassNamesAttribute(): string
    {
        if ($this->relationLoaded('combinedClasses') && $this->combinedClasses->isNotEmpty()) {
            return $this->combinedClasses->pluck('name')->implode(', ');
        }

        if ($this->is_combined) {
            $names = $this->combinedClasses()->orderBy('grade_level')->orderBy('name')->pluck('classes.name')->all();
            if (! empty($names)) {
                return implode(', ', $names);
            }
        }

        return $this->schoolClass?->name ?? '-';
    }

    /**
     * Determine effective religion filter (from explicit schedule setting, teaching group, or religious subject biodata).
     */
    public function getEffectiveReligionFilterAttribute(): ?string
    {
        if ($this->teaching_group_id && $this->teachingGroup) {
            if ($this->teachingGroup->membership_mode === 'RELIGION') {
                return $this->teachingGroup->religion_filter ?: $this->subject?->resolved_religion_filter;
            }
            if ($this->teachingGroup->membership_mode === 'MANUAL' || $this->teachingGroup->membership_mode === 'ALL') {
                return null;
            }
        }

        if ($this->student_filter_type === 'RELIGION' && ! empty($this->student_filter_value)) {
            return trim($this->student_filter_value);
        }

        if ($this->student_filter_type === 'ALL') {
            return null;
        }

        // Default ('AUTO' or empty): automatically inspect Subject (e.g., Pendidikan Agama Islam -> Islam)
        return $this->subject?->resolved_religion_filter;
    }

    /**
     * Resolve the exact list of Student models participating in this schedule slot
     * (reading student biodata religion for religious subjects, or TeachingGroup for combined/elective groups).
     */
    public function resolveParticipants(): Collection
    {
        if ($this->teaching_group_id && $this->teachingGroup) {
            // If teaching group is MANUAL, return its explicit student roster
            if ($this->teachingGroup->membership_mode === 'MANUAL') {
                return $this->teachingGroup->resolveStudents();
            }
        }

        $classIds = $this->participating_class_ids;
        if (empty($classIds) && $this->teaching_group_id && $this->teachingGroup) {
            $classIds = $this->teachingGroup->classes()->pluck('classes.id')->all();
        }

        $query = Student::with('schoolClass')
            ->where('school_id', $this->school_id)
            ->when(! empty($classIds), fn ($q) => $q->whereIn('class_id', $classIds));

        $religion = $this->effective_religion_filter;
        if ($religion) {
            $synonyms = TeachingGroup::religionSynonyms($religion);
            $query->whereIn(DB::raw('LOWER(TRIM(COALESCE(religion, "")))'), $synonyms);
        }

        return $query->orderBy('class_id')->orderBy('full_name')->get();
    }
}
