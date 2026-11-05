<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'student_id',
        'date',
        'check_in_at',
        'check_out_at',
        'status',
        'method',
        'selfie_url',
        'latitude',
        'longitude',
        'gps_accuracy',
        'distance_meters',
        'is_within_radius',
        'is_mock_location',
        'device_info',
        'verified_by',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date:Y-m-d',
            'latitude' => 'float',
            'longitude' => 'float',
            'gps_accuracy' => 'float',
            'distance_meters' => 'float',
            'is_within_radius' => 'boolean',
            'is_mock_location' => 'boolean',
        ];
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}
