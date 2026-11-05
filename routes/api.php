<?php

use App\Http\Controllers\Api\AcademicController;
use App\Http\Controllers\Api\AcademicYearController;
use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ClassScheduleController;
use App\Http\Controllers\Api\CurriculumController;
use App\Http\Controllers\Api\DepartmentController;
use App\Http\Controllers\Api\ParentController;
use App\Http\Controllers\Api\SchoolController;
use App\Http\Controllers\Api\SchoolGeofenceController;
use App\Http\Controllers\Api\SchoolRoomController;
use App\Http\Controllers\Api\StudentDisciplineController;
use App\Http\Controllers\Api\StudentPromotionController;
use App\Http\Controllers\Api\SubjectController;
use App\Http\Controllers\Api\TeacherController;
use App\Http\Controllers\Api\TeachingGroupController;
use App\Http\Controllers\Api\TimeScheduleController;
use App\Http\Controllers\Api\VocationalSpectrumController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes - Sekolahku Platform (v1)
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {
    // 1. Auth Endpoints
    Route::post('/auth/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/auth/logout', [AuthController::class, 'logout']);
        Route::get('/auth/me', [AuthController::class, 'me']);
    });

    // 2. Data Master - Periode Akademik (Tahun Ajaran & Semester)
    Route::get('/academic-years', [AcademicYearController::class, 'index']);
    Route::post('/academic-years', [AcademicYearController::class, 'store']);
    Route::post('/academic-years/{id}/activate', [AcademicYearController::class, 'activate']);

    // 3. Data Master - Waktu Belajar & Jam Pelajaran Multi-Kategori (Dinamis Add/Edit/Delete)
    Route::get('/time-categories', [TimeScheduleController::class, 'indexCategories']);
    Route::post('/time-categories', [TimeScheduleController::class, 'storeCategory']);
    Route::get('/time-categories/{id}', [TimeScheduleController::class, 'showCategory']);
    Route::put('/time-categories/{id}', [TimeScheduleController::class, 'updateCategory']);
    Route::delete('/time-categories/{id}', [TimeScheduleController::class, 'destroyCategory']);
    Route::post('/time-categories/{id}/activate', [TimeScheduleController::class, 'activateCategory']);
    Route::post('/time-categories/{id}/clone-periods', [TimeScheduleController::class, 'clonePeriods']);
    Route::get('/time-categories/{id}/periods', [TimeScheduleController::class, 'periods']);
    Route::post('/time-categories/{id}/periods', [TimeScheduleController::class, 'storePeriod']);
    Route::put('/periods/{id}', [TimeScheduleController::class, 'updatePeriod']);
    Route::delete('/periods/{id}', [TimeScheduleController::class, 'destroyPeriod']);

    // 4. Data Master - Satuan Pendidikan (Sekolah Dapodik Multi-Tenant)
    Route::get('/schools', [SchoolController::class, 'index']);
    Route::post('/schools', [SchoolController::class, 'store']);
    Route::get('/schools/{id}', [SchoolController::class, 'show']);
    Route::put('/schools/{id}', [SchoolController::class, 'update']);
    Route::post('/schools/switch-active', [SchoolController::class, 'switchActive']);
    Route::get('/schools/{id}/profile-edit-window', [SchoolController::class, 'getProfileEditWindow']);
    Route::post('/schools/{id}/profile-edit-window', [SchoolController::class, 'updateProfileEditWindow']);

    // 5. Data Master - Jurusan, Kelas & Siswa
    Route::get('/departments', [DepartmentController::class, 'index']);
    Route::post('/departments', [DepartmentController::class, 'store']);
    Route::get('/classes', [AcademicController::class, 'classes']);
    Route::post('/classes', [AcademicController::class, 'storeClass']);
    Route::put('/classes/{id}', [AcademicController::class, 'updateClass']);
    Route::delete('/classes/{id}', [AcademicController::class, 'destroyClass']);
    Route::get('/students', [AcademicController::class, 'students']);
    Route::post('/students', [AcademicController::class, 'storeStudent']);
    Route::get('/students/{id}/class-histories', [StudentPromotionController::class, 'history']);

    // 5b. Kenaikan Kelas (Riwayat Rombel per Tahun Ajaran)
    Route::get('/promotions/candidates', [StudentPromotionController::class, 'candidates']);
    Route::post('/promotions', [StudentPromotionController::class, 'promote']);

    // 5. Data Master - Guru & Tenaga Kependidikan (GTK Dapodik)
    Route::get('/teachers', [TeacherController::class, 'index']);
    Route::post('/teachers', [TeacherController::class, 'store']);
    Route::get('/teachers/{id}', [TeacherController::class, 'show']);

    // 6. Data Master - Orang Tua / Wali (Multi-Anak Dapodik)
    Route::get('/parents', [ParentController::class, 'index']);
    Route::post('/parents', [ParentController::class, 'store']);
    Route::get('/parents/{id}', [ParentController::class, 'show']);
    Route::post('/parents/{id}/students', [ParentController::class, 'attachStudent']);
    Route::delete('/parents/{id}/students/{studentId}', [ParentController::class, 'detachStudent']);

    // 7. Data Master - Mata Pelajaran, Ruangan & Jadwal KBM
    Route::get('/subjects', [SubjectController::class, 'index']);
    Route::post('/subjects', [SubjectController::class, 'store']);
    Route::put('/subjects/{id}', [SubjectController::class, 'update']);
    Route::delete('/subjects/{id}', [SubjectController::class, 'destroy']);
    Route::get('/rooms', [SchoolRoomController::class, 'index']);
    Route::post('/rooms', [SchoolRoomController::class, 'store']);
    Route::put('/rooms/{id}', [SchoolRoomController::class, 'update']);
    Route::delete('/rooms/{id}', [SchoolRoomController::class, 'destroy']);
    Route::get('/schedules', [ClassScheduleController::class, 'index']);
    Route::post('/schedules', [ClassScheduleController::class, 'store']);
    Route::get('/schedules/{id}/students', [ClassScheduleController::class, 'participants']);
    Route::put('/schedules/{id}', [ClassScheduleController::class, 'update']);
    Route::delete('/schedules/{id}', [ClassScheduleController::class, 'destroy']);
    Route::post('/schedules/copy-semester', [ClassScheduleController::class, 'copyFromSemester']);

    // 7b. Kelompok Mapel & Kelas Gabungan Lintas Rombel (Agama / Mapel Pilihan)
    Route::get('/teaching-groups', [TeachingGroupController::class, 'index']);
    Route::post('/teaching-groups', [TeachingGroupController::class, 'store']);
    Route::get('/teaching-groups/{id}/students', [TeachingGroupController::class, 'students']);
    Route::put('/teaching-groups/{id}', [TeachingGroupController::class, 'update']);
    Route::delete('/teaching-groups/{id}', [TeachingGroupController::class, 'destroy']);

    // 8. Data Master - Kurikulum & Pemetaan Jenjang/Mapel
    Route::get('/curriculums', [CurriculumController::class, 'index']);
    Route::post('/curriculums', [CurriculumController::class, 'store']);
    Route::put('/curriculums/{id}', [CurriculumController::class, 'update']);
    Route::delete('/curriculums/{id}', [CurriculumController::class, 'destroy']);
    Route::post('/curriculums/assign-grade', [CurriculumController::class, 'assignGradeLevel']);
    Route::post('/curriculums/{id}/subjects', [CurriculumController::class, 'saveSubject']);
    Route::delete('/curriculums/{id}/subjects/{subjectId}', [CurriculumController::class, 'removeSubject']);
    Route::get('/curriculums/{id}/subject-groups', [CurriculumController::class, 'getSubjectGroups']);
    Route::post('/curriculums/{id}/subject-groups', [CurriculumController::class, 'saveSubjectGroup']);
    Route::put('/curriculums/{id}/subject-groups/{groupId}', [CurriculumController::class, 'updateSubjectGroup']);
    Route::delete('/curriculums/{id}/subject-groups/{groupId}', [CurriculumController::class, 'deleteSubjectGroup']);
    Route::post('/curriculums/{id}/subject-groups/template', [CurriculumController::class, 'applyTemplateSubjectGroups']);

    // 9. Data Master - Spektrum Keahlian SMK (Bidang, Program, Konsentrasi Keahlian)
    Route::get('/vocational-spectrum', [VocationalSpectrumController::class, 'index']);
    Route::post('/vocational-fields', [VocationalSpectrumController::class, 'storeField']);
    Route::post('/vocational-programs', [VocationalSpectrumController::class, 'storeProgram']);
    Route::post('/vocational-concentrations', [VocationalSpectrumController::class, 'storeConcentration']);
    Route::put('/vocational-concentrations/{id}', [VocationalSpectrumController::class, 'updateConcentration']);
    Route::delete('/vocational-concentrations/{id}', [VocationalSpectrumController::class, 'destroyConcentration']);
    Route::post('/schools/switch-level', [VocationalSpectrumController::class, 'switchSchoolLevel']);

    // 10. Geofencing Management
    Route::apiResource('/geofences', SchoolGeofenceController::class);

    // 9. Attendance Endpoints
    Route::get('/attendances', [AttendanceController::class, 'index']);
    Route::post('/attendances', [AttendanceController::class, 'manualRecord']);
    Route::post('/attendances/selfie', [AttendanceController::class, 'selfieCheckIn']);
    Route::post('/attendances/checkout', [AttendanceController::class, 'checkOut']);
    Route::post('/attendances/qr-scan', [AttendanceController::class, 'qrScan']);
    Route::get('/attendances/today', [AttendanceController::class, 'todaySummary']);
    Route::get('/attendances/map', [AttendanceController::class, 'mapData']);
    Route::get('/attendances/report', [AttendanceController::class, 'report']);

    // 11. Modul Kesiswaan - Master Kategori Pelanggaran, Item Poin, Ambang Batas & Pemutihan
    Route::prefix('discipline')->group(function () {
        Route::get('/', [StudentDisciplineController::class, 'index']);
        Route::post('/categories', [StudentDisciplineController::class, 'storeCategory']);
        Route::put('/categories/{id}', [StudentDisciplineController::class, 'updateCategory']);
        Route::delete('/categories/{id}', [StudentDisciplineController::class, 'destroyCategory']);

        Route::post('/items', [StudentDisciplineController::class, 'storeItem']);
        Route::put('/items/{id}', [StudentDisciplineController::class, 'updateItem']);
        Route::delete('/items/{id}', [StudentDisciplineController::class, 'destroyItem']);

        Route::post('/thresholds', [StudentDisciplineController::class, 'storeThreshold']);
        Route::put('/thresholds/{id}', [StudentDisciplineController::class, 'updateThreshold']);
        Route::delete('/thresholds/{id}', [StudentDisciplineController::class, 'destroyThreshold']);

        Route::put('/policy', [StudentDisciplineController::class, 'updatePolicy']);
        Route::post('/amnesty-rules', [StudentDisciplineController::class, 'storeAmnestyRule']);
        Route::put('/amnesty-rules/{id}', [StudentDisciplineController::class, 'updateAmnestyRule']);
        Route::delete('/amnesty-rules/{id}', [StudentDisciplineController::class, 'destroyAmnestyRule']);

        Route::post('/violations', [StudentDisciplineController::class, 'storeViolationRecord']);
        Route::post('/amnesties', [StudentDisciplineController::class, 'storeAmnestyRecord']);
        Route::delete('/records/{id}', [StudentDisciplineController::class, 'destroyRecord']);
    });
});
