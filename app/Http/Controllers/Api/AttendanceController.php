<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Student;
use App\Services\GeofenceService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AttendanceController extends Controller
{
    public function __construct(
        protected GeofenceService $geofenceService
    ) {}

    /**
     * Resolve authorized school_id for attendance APIs.
     * Super Admin can query any school; Non-Super Admin is strictly locked to their enrolled school_id.
     */
    protected function getAuthorizedSchoolId(Request $request): int
    {
        $user = \Illuminate\Support\Facades\Auth::user() ?? $request->user();

        if ($user && ! $user->isSuperAdmin()) {
            return (int) ($user->school_id ?? 1);
        }

        return (int) ($request->query('school_id') ?? session('active_school_id') ?? ($user?->school_id ?? 1));
    }

    /**
     * Display a listing of attendances with filters and pagination.
     */
    public function index(Request $request): JsonResponse
    {
        $schoolId = $this->getAuthorizedSchoolId($request);
        $user = \Illuminate\Support\Facades\Auth::user() ?? $request->user();
        $studentId = $request->query('student_id');

        $date = $request->query('date', Carbon::today()->toDateString());
        $classId = $request->query('class_id');
        $status = $request->query('status');
        $search = $request->query('search');
        $perPage = (int) $request->query('per_page', 25);

        // Jika user yang login adalah siswa, kunci hanya ke data presensi miliknya sendiri
        if ($user && $user->role === 'student' && $user->student) {
            $studentId = $user->student->id;
            // Jika siswa meminta riwayat dan tidak menyertakan filter tanggal spesifik, jangan paksakan hari ini
            if (! $request->has('date')) {
                $date = null;
            }
        }

        $query = Attendance::with(['student.schoolClass', 'verifiedBy'])
            ->where('school_id', $schoolId);

        if ($studentId) {
            $query->where('student_id', $studentId);
        }

        if ($date && $date !== 'all') {
            $query->whereDate('date', $date);
        }

        if ($status) {
            $query->where('status', $status);
        }

        if ($classId) {
            $query->whereHas('student', function ($q) use ($classId) {
                $q->where('class_id', $classId);
            });
        }

        if ($search) {
            $query->whereHas('student', function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                    ->orWhere('nis', 'like', "%{$search}%");
            });
        }

        $attendances = $query->orderByDesc('id')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $attendances->items(),
            'meta' => [
                'current_page' => $attendances->currentPage(),
                'last_page' => $attendances->lastPage(),
                'per_page' => $attendances->perPage(),
                'total' => $attendances->total(),
            ],
        ]);
    }

    /**
     * Handle Selfie + GPS Check-in for Students.
     */
    public function selfieCheckIn(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'student_id' => 'nullable|exists:students,id',
            'selfie_image' => 'required', // Can be uploaded file or base64 data URI
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'gps_accuracy' => 'nullable|numeric',
            'is_mock_location' => 'nullable|boolean',
            'device_info' => 'nullable|string|max:255',
            'bypass_geofence' => 'nullable|boolean', // Useful for testing/demo toggle if needed
        ]);

        // Determine student
        $user = $request->user();
        if ($user && $user->role === 'student' && $user->student) {
            $studentId = $user->student->id;
        } else {
            $studentId = $validated['student_id'] ?? $user?->student?->id;
        }

        if (! $studentId) {
            return response()->json([
                'success' => false,
                'message' => 'Parameter student_id wajib disertakan.',
            ], 422);
        }

        $student = Student::with(['schoolClass', 'school'])->findOrFail($studentId);
        $schoolId = $student->school_id;
        $today = Carbon::today()->toDateString();
        $now = Carbon::now();

        // 1. Check if already checked in today
        $existing = Attendance::where('school_id', $schoolId)
            ->where('student_id', $student->id)
            ->whereDate('date', $today)
            ->first();

        if ($existing && $existing->check_in_at !== null) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'ALREADY_CHECKED_IN',
                    'message' => "Siswa sudah melakukan presensi masuk hari ini pada pukul {$existing->check_in_at}.",
                    'attendance' => $existing,
                ],
            ], 422);
        }

        // 2. Validate Geofencing
        $latitude = (float) $validated['latitude'];
        $longitude = (float) $validated['longitude'];
        $isMock = ! empty($validated['is_mock_location']);

        if ($isMock) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'MOCK_LOCATION_DETECTED',
                    'message' => 'Terdeteksi penggunaan lokasi palsu (Fake GPS / Mock Location). Presensi ditolak demi integritas data.',
                ],
            ], 422);
        }

        $locationCheck = $this->geofenceService->validateLocation($schoolId, $latitude, $longitude);
        $bypassGeofence = ! empty($validated['bypass_geofence']);

        if (! $locationCheck['is_within_radius'] && ! $bypassGeofence) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'OUTSIDE_GEOFENCE',
                    'message' => 'Anda berada di luar area sekolah. Jarak Anda ' . number_format($locationCheck['distance_meters'], 1) . ' meter dari titik sekolah (Batas radius: ' . $locationCheck['allowed_radius'] . ' meter).',
                    'data' => [
                        'your_location' => [
                            'lat' => $latitude,
                            'lng' => $longitude,
                        ],
                        'school_location' => $locationCheck['geofence'] ? [
                            'name' => $locationCheck['geofence']->name,
                            'lat' => $locationCheck['geofence']->latitude,
                            'lng' => $locationCheck['geofence']->longitude,
                        ] : null,
                        'distance_meters' => $locationCheck['distance_meters'],
                        'allowed_radius_meters' => $locationCheck['allowed_radius'],
                    ],
                ],
            ], 422);
        }

        // 3. Process & Save Selfie Image
        $selfieUrl = null;
        if ($request->hasFile('selfie_image')) {
            $path = $request->file('selfie_image')->store('attendances/selfies', 'public');
            $selfieUrl = asset('storage/' . $path);
        } elseif (is_string($request->input('selfie_image')) && str_starts_with($request->input('selfie_image'), 'data:image')) {
            $rawImage = $request->input('selfie_image');
            if (preg_match('/^data:image\/(\w+);base64,/', $rawImage, $type)) {
                $rawImage = substr($rawImage, strpos($rawImage, ',') + 1);
                $ext = strtolower($type[1]);
                $decoded = base64_decode($rawImage);
                $filename = 'selfie_' . $student->id . '_' . Str::random(10) . '_' . time() . '.' . $ext;
                Storage::disk('public')->put('attendances/selfies/' . $filename, $decoded);
                $selfieUrl = asset('storage/attendances/selfies/' . $filename);
            }
        }

        // 4. Determine Status (present vs late based on cutoff 07:15)
        $currentTime = $now->format('H:i:s');
        $status = $this->geofenceService->determineStatus($currentTime);

        // 5. Create / Update Attendance Record
        $attendance = $existing ?? new Attendance([
            'school_id' => $schoolId,
            'student_id' => $student->id,
            'date' => $today,
        ]);

        $attendance->fill([
            'check_in_at' => $currentTime,
            'status' => $status,
            'method' => 'selfie',
            'selfie_url' => $selfieUrl,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'gps_accuracy' => $validated['gps_accuracy'] ?? null,
            'distance_meters' => $locationCheck['distance_meters'],
            'is_within_radius' => $locationCheck['is_within_radius'] || $bypassGeofence,
            'is_mock_location' => $isMock,
            'device_info' => $validated['device_info'] ?? $request->header('User-Agent'),
        ]);
        $attendance->save();

        return response()->json([
            'success' => true,
            'message' => 'Presensi selfie berhasil dicatat.',
            'data' => [
                'attendance_id' => $attendance->id,
                'student_name' => $student->full_name,
                'nis' => $student->nis,
                'class_name' => $student->schoolClass?->name,
                'status' => $attendance->status,
                'status_label' => $attendance->status === 'late' ? 'Terlambat' : 'Hadir Tepat Waktu',
                'method' => $attendance->method,
                'check_in_at' => $attendance->check_in_at,
                'is_within_radius' => $attendance->is_within_radius,
                'distance_meters' => $attendance->distance_meters,
                'selfie_url' => $attendance->selfie_url,
            ],
        ]);
    }

    /**
     * Check-out (Presensi Pulang)
     */
    public function checkOut(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'student_id' => 'nullable|exists:students,id',
        ]);

        // Determine student
        $user = $request->user();
        if ($user && $user->role === 'student' && $user->student) {
            $studentId = $user->student->id;
        } else {
            $studentId = $validated['student_id'] ?? $user?->student?->id;
        }

        if (! $studentId) {
            return response()->json([
                'success' => false,
                'message' => 'Parameter student_id wajib disertakan.',
            ], 422);
        }

        $today = Carbon::today()->toDateString();
        $attendance = Attendance::where('student_id', $studentId)
            ->whereDate('date', $today)
            ->first();

        if (! $attendance || ! $attendance->check_in_at) {
            return response()->json([
                'success' => false,
                'message' => 'Siswa belum melakukan presensi masuk hari ini.',
            ], 422);
        }

        $nowTime = Carbon::now()->format('H:i:s');
        $attendance->update([
            'check_out_at' => $nowTime,
        ]);

        return response()->json([
            'success' => true,
            'message' => "Presensi pulang berhasil dicatat pada pukul {$nowTime}.",
            'data' => $attendance,
        ]);
    }

    /**
     * Manual Attendance input by Teacher / Admin.
     */
    public function manualRecord(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'date' => 'required|date',
            'status' => 'required|in:present,late,sick,excused,absent',
            'check_in_at' => 'nullable|date_format:H:i',
            'check_out_at' => 'nullable|date_format:H:i',
            'notes' => 'nullable|string|max:500',
        ]);

        $student = Student::findOrFail($validated['student_id']);

        $user = \Illuminate\Support\Facades\Auth::user() ?? $request->user();
        if ($user && ! $user->isSuperAdmin()) {
            if ((int) $student->school_id !== (int) $user->school_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Akses ditolak: Anda hanya berwenang mencatat presensi untuk siswa pada satuan pendidikan yang telah dienrol.',
                ], 403);
            }
        }

        $attendance = Attendance::where('school_id', $student->school_id)
            ->where('student_id', $student->id)
            ->whereDate('date', $validated['date'])
            ->first();

        if (! $attendance) {
            $attendance = new Attendance([
                'school_id' => $student->school_id,
                'student_id' => $student->id,
                'date' => $validated['date'],
            ]);
        }

        $attendance->fill([
            'status' => $validated['status'],
            'method' => 'manual',
            'check_in_at' => $validated['check_in_at'] ?? ($validated['status'] === 'present' ? '07:00:00' : null),
            'check_out_at' => $validated['check_out_at'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'verified_by' => $request->user()?->id,
        ]);
        $attendance->save();

        return response()->json([
            'success' => true,
            'message' => 'Data presensi manual berhasil disimpan.',
            'data' => $attendance->load(['student.schoolClass', 'verifiedBy']),
        ]);
    }

    /**
     * QR Code Scan Check-in.
     */
    public function qrScan(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nis' => 'required|string',
            'school_id' => 'nullable|exists:schools,id',
        ]);

        $schoolId = $validated['school_id'] ?? $request->user()?->school_id ?? 1;
        $student = Student::where('school_id', $schoolId)
            ->where('nis', $validated['nis'])
            ->first();

        if (! $student) {
            return response()->json([
                'success' => false,
                'message' => 'Siswa dengan NIS tersebut tidak ditemukan.',
            ], 404);
        }

        $today = Carbon::today()->toDateString();
        $now = Carbon::now();
        $currentTime = $now->format('H:i:s');
        $status = $this->geofenceService->determineStatus($currentTime);

        $attendance = Attendance::where('school_id', $schoolId)
            ->where('student_id', $student->id)
            ->whereDate('date', $today)
            ->first();

        if (! $attendance) {
            $attendance = new Attendance([
                'school_id' => $schoolId,
                'student_id' => $student->id,
                'date' => $today,
            ]);
        }

        $attendance->fill([
            'check_in_at' => $currentTime,
            'status' => $status,
            'method' => 'qr_code',
            'is_within_radius' => true,
            'verified_by' => $request->user()?->id,
        ]);
        $attendance->save();

        return response()->json([
            'success' => true,
            'message' => 'Presensi via QR Code berhasil dicatat.',
            'data' => [
                'attendance_id' => $attendance->id,
                'student_name' => $student->full_name,
                'nis' => $student->nis,
                'status' => $attendance->status,
                'check_in_at' => $attendance->check_in_at,
            ],
        ]);
    }

    /**
     * Today's Summary & Statistics.
     */
    public function todaySummary(Request $request): JsonResponse
    {
        $schoolId = $this->getAuthorizedSchoolId($request);
        $date = $request->query('date', Carbon::today()->toDateString());

        $totalStudents = Student::where('school_id', $schoolId)
            ->where('status', 'active')
            ->count();

        $attendances = Attendance::where('school_id', $schoolId)
            ->where('date', $date)
            ->get();

        $presentCount = $attendances->where('status', 'present')->count();
        $lateCount = $attendances->where('status', 'late')->count();
        $sickCount = $attendances->where('status', 'sick')->count();
        $excusedCount = $attendances->where('status', 'excused')->count();
        $recordedAbsent = $attendances->where('status', 'absent')->count();

        $totalCheckedIn = $presentCount + $lateCount;
        $unrecorded = max(0, $totalStudents - $attendances->count());
        $totalAbsent = $recordedAbsent + $unrecorded;

        $attendancePercentage = $totalStudents > 0
            ? round(($totalCheckedIn / $totalStudents) * 100, 1)
            : 0;

        return response()->json([
            'success' => true,
            'data' => [
                'date' => $date,
                'total_students' => $totalStudents,
                'present' => $presentCount,
                'late' => $lateCount,
                'sick' => $sickCount,
                'excused' => $excusedCount,
                'absent' => $totalAbsent,
                'total_checked_in' => $totalCheckedIn,
                'attendance_percentage' => $attendancePercentage,
            ],
        ]);
    }

    /**
     * Presensi Map Data (GPS coordinates of today's check-ins).
     */
    public function mapData(Request $request): JsonResponse
    {
        $schoolId = $this->getAuthorizedSchoolId($request);
        $date = $request->query('date', Carbon::today()->toDateString());

        $attendances = Attendance::with(['student.schoolClass'])
            ->where('school_id', $schoolId)
            ->where('date', $date)
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'student_name' => $item->student?->full_name,
                    'nis' => $item->student?->nis,
                    'class_name' => $item->student?->schoolClass?->name,
                    'status' => $item->status,
                    'check_in_at' => $item->check_in_at,
                    'latitude' => (float) $item->latitude,
                    'longitude' => (float) $item->longitude,
                    'distance_meters' => (float) $item->distance_meters,
                    'is_within_radius' => (bool) $item->is_within_radius,
                    'selfie_url' => $item->selfie_url,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $attendances,
        ]);
    }

    /**
     * Attendance Report / Recap.
     */
    public function report(Request $request): JsonResponse
    {
        $schoolId = $this->getAuthorizedSchoolId($request);
        $month = (int) $request->query('month', Carbon::now()->month);
        $year = (int) $request->query('year', Carbon::now()->year);
        $classId = $request->query('class_id');

        $studentsQuery = Student::with(['schoolClass'])
            ->where('school_id', $schoolId)
            ->where('status', 'active');

        if ($classId) {
            $studentsQuery->where('class_id', $classId);
        }

        $students = $studentsQuery->get();

        $attendances = Attendance::where('school_id', $schoolId)
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->get();

        $recap = $students->map(function ($student) use ($attendances) {
            $studentAtts = $attendances->where('student_id', $student->id);

            return [
                'student_id' => $student->id,
                'nis' => $student->nis,
                'full_name' => $student->full_name,
                'class_name' => $student->schoolClass?->name,
                'present' => $studentAtts->where('status', 'present')->count(),
                'late' => $studentAtts->where('status', 'late')->count(),
                'sick' => $studentAtts->where('status', 'sick')->count(),
                'excused' => $studentAtts->where('status', 'excused')->count(),
                'absent' => $studentAtts->where('status', 'absent')->count(),
                'total_records' => $studentAtts->count(),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'month' => $month,
                'year' => $year,
                'recap' => $recap,
            ],
        ]);
    }
}
