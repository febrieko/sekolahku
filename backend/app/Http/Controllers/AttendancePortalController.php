<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\SchoolGeofence;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

use Illuminate\Support\Facades\Auth;

class AttendancePortalController extends Controller
{
    /**
     * Show interactive Attendance Portal UI.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        $isStudent = $user && $user->role === 'student';

        $currentStudent = null;
        $todayAttendance = null;
        $myAttendances = collect();
        $studentStats = null;

        if ($isStudent) {
            // 1. Identifikasi siswa dari akun login
            $currentStudent = Student::with(['schoolClass', 'entryAcademicYear', 'school'])
                ->where('user_id', $user->id)
                ->first();

            if (! $currentStudent && $user->school_id) {
                $currentStudent = Student::with(['schoolClass', 'entryAcademicYear', 'school'])
                    ->where('school_id', $user->school_id)
                    ->where('full_name', 'like', "%{$user->name}%")
                    ->first();
            }

            if (! $currentStudent) {
                $currentStudent = Student::with(['schoolClass', 'entryAcademicYear', 'school'])->first();
            }

            if ($currentStudent) {
                $school = $currentStudent->school ?? School::find($user->school_id) ?? School::first();

                // Status absensi hari ini untuk siswa yang sedang login
                $todayAttendance = Attendance::where('student_id', $currentStudent->id)
                    ->whereDate('date', Carbon::today()->toDateString())
                    ->first();

                // Riwayat kehadiran siswa (30 hari terakhir)
                $myAttendances = Attendance::where('student_id', $currentStudent->id)
                    ->orderByDesc('date')
                    ->limit(30)
                    ->get();

                // Statistik kehadiran siswa
                $totalRecords = $myAttendances->count();
                $presentCount = $myAttendances->where('status', 'present')->count();
                $lateCount = $myAttendances->where('status', 'late')->count();
                $sickCount = $myAttendances->where('status', 'sick')->count();
                $excusedCount = $myAttendances->where('status', 'excused')->count();
                $absentCount = $myAttendances->where('status', 'absent')->count();

                $studentStats = [
                    'total' => $totalRecords,
                    'present' => $presentCount,
                    'late' => $lateCount,
                    'sick' => $sickCount,
                    'excused' => $excusedCount,
                    'absent' => $absentCount,
                    'rate' => $totalRecords > 0 ? round((($presentCount + $lateCount) / $totalRecords) * 100, 1) : 100,
                ];
            } else {
                $school = School::where('tenant_id', 'sman1jkt')->first() ?? School::first();
            }
        } else {
            // Guru, Admin, atau mode pengujian
            $school = ($user && $user->school_id ? School::find($user->school_id) : null)
                ?? School::where('tenant_id', 'sman1jkt')->first()
                ?? School::whereHas('students')->first()
                ?? School::first();
        }

        $schoolId = $school ? $school->id : 1;

        $geofences = SchoolGeofence::where('school_id', $schoolId)
            ->where('is_active', true)
            ->orderByDesc('is_primary')
            ->get();

        // Fallback jika sekolah belum punya geofence sendiri
        if ($geofences->isEmpty()) {
            $geofences = SchoolGeofence::where('is_active', true)->orderByDesc('is_primary')->get();
        }

        $primaryGeofence = $geofences->firstWhere('is_primary', true) ?? $geofences->first();

        $classes = SchoolClass::where('school_id', $schoolId)
            ->where('is_active', true)
            ->orderBy('grade_level')
            ->orderBy('name')
            ->get();

        $students = Student::with('schoolClass')
            ->where('school_id', $schoolId)
            ->where('status', 'active')
            ->orderBy('full_name')
            ->get();

        // Jika daftar siswa di sekolah kosong, fallback ke seluruh siswa aktif
        if ($students->isEmpty()) {
            $students = Student::with('schoolClass')->where('status', 'active')->orderBy('full_name')->get();
        }

        return view('attendance.portal', [
            'school' => $school,
            'user' => $user,
            'isStudent' => $isStudent,
            'currentStudent' => $currentStudent,
            'todayAttendance' => $todayAttendance,
            'myAttendances' => $myAttendances,
            'studentStats' => $studentStats,
            'geofences' => $geofences,
            'primaryGeofence' => $primaryGeofence,
            'classes' => $classes,
            'students' => $students,
            'todayDate' => Carbon::now()->isoFormat('dddd, D MMMM Y'),
            'todayIso' => Carbon::today()->toDateString(),
        ]);
    }
}
