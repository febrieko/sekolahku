<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\SchoolGeofence;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AttendancePortalController extends Controller
{
    /**
     * Show interactive Attendance Portal UI.
     */
    public function index(Request $request): View
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        $isStudent = $user && $user->role === 'student';
        $isSuperAdmin = $user && $user->isSuperAdmin();
        $isSchoolAdmin = $user && $user->isSchoolAdmin();
        $canManageGeofence = $isSuperAdmin || $isSchoolAdmin;

        $currentStudent = null;
        $todayAttendance = null;
        $myAttendances = collect();
        $studentStats = null;
        $availableSchools = collect();

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
        } elseif ($isSchoolAdmin) {
            // 2. Admin Sekolah: Kunci mutlak ke satuan pendidikan tempat ia dienrol
            if (! $user->school_id) {
                abort(403, 'Akses Ditolak: Akun Admin Sekolah Anda belum dienrol ke Satuan Pendidikan manapun. Silakan hubungi Super Administrator.');
            }
            $school = School::find($user->school_id);
            if (! $school) {
                abort(404, 'Satuan pendidikan tempat akun Anda dienrol tidak ditemukan.');
            }
            $availableSchools = collect([$school]);
        } elseif ($isSuperAdmin) {
            // 3. Super Admin: Bebas memilih dan mengelola seluruh satuan pendidikan
            $availableSchools = School::orderBy('name')->get();

            if ($request->has('school_id')) {
                $requestedSchool = School::find($request->query('school_id'));
                if ($requestedSchool) {
                    $school = $requestedSchool;
                    session(['active_school_id' => $requestedSchool->id]);
                } else {
                    $school = $this->resolveSchool($request);
                }
            } else {
                $school = $this->resolveSchool($request);
            }
        } else {
            // 4. Guru, Staf, atau Mode Pratinjau
            if ($user && $user->school_id) {
                $school = School::find($user->school_id) ?? $this->resolveSchool($request);
            } else {
                $school = $this->resolveSchool($request);
            }
            $availableSchools = $school ? collect([$school]) : collect();
        }

        $schoolId = $school ? $school->id : 1;

        // Ambil titik geofence KHUSUS untuk sekolah ini (tanpa fallback lintas tenant)
        $geofences = SchoolGeofence::where('school_id', $schoolId)
            ->where('is_active', true)
            ->orderByDesc('is_primary')
            ->get();

        $primaryGeofence = $geofences->firstWhere('is_primary', true) ?? $geofences->first();

        // Ambil kelas hanya milik sekolah ini
        $classes = SchoolClass::where('school_id', $schoolId)
            ->where('is_active', true)
            ->orderBy('grade_level')
            ->orderBy('name')
            ->get();

        // Ambil daftar siswa hanya milik sekolah ini
        $students = Student::with('schoolClass')
            ->where('school_id', $schoolId)
            ->where('status', 'active')
            ->orderBy('full_name')
            ->get();

        return view('attendance.portal', [
            'school' => $school,
            'user' => $user,
            'isStudent' => $isStudent,
            'isSuperAdmin' => $isSuperAdmin,
            'isSchoolAdmin' => $isSchoolAdmin,
            'canManageGeofence' => $canManageGeofence,
            'availableSchools' => $availableSchools,
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
