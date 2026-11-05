<?php

use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AdminDataMasterController;
use App\Http\Controllers\AdminDisciplineController;
use App\Http\Controllers\AdminScheduleController;
use App\Http\Controllers\AdminTeachingJournalController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\AttendancePortalController;
use App\Http\Controllers\StudentPortalController;
use App\Http\Controllers\StudentProfileController;
use App\Http\Controllers\TeacherProfileController;
use App\Http\Controllers\WebAuthController;
use Illuminate\Support\Facades\Route;

// Authentication & Main Entry (Halaman Utama adalah Login)
Route::get('/', [WebAuthController::class, 'showLoginForm'])->name('home');
Route::get('/login', [WebAuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [WebAuthController::class, 'login']);
Route::get('/quick-login', [WebAuthController::class, 'quickLogin'])->name('quick-login');
Route::post('/logout', [WebAuthController::class, 'logout'])->name('logout');

// Portal Presensi Siswa & GPS Selfie
Route::get('/attendance', [AttendancePortalController::class, 'index'])->name('attendance.portal');

// Modul Portal Siswa Terpadu (Dashboard, Jadwal Personal, Kehadiran, Tata Tertib, Kartu Pelajar & Biodata Dapodik)
Route::middleware(['auth', 'tenant'])->group(function () {
    Route::get('/student', fn () => redirect()->route('student.dashboard'))->name('student.home');
    Route::get('/student/dashboard', [StudentPortalController::class, 'dashboard'])->name('student.dashboard');
    Route::get('/student/schedules', [StudentPortalController::class, 'schedules'])->name('student.schedules');
    Route::get('/student/attendance', [StudentPortalController::class, 'attendance'])->name('student.attendance');
    Route::get('/student/discipline', [StudentPortalController::class, 'discipline'])->name('student.discipline');
    Route::get('/student/card', [StudentPortalController::class, 'card'])->name('student.card');
    Route::get('/student/profile', [StudentProfileController::class, 'show'])->name('student.profile');
    Route::post('/student/profile', [StudentProfileController::class, 'update'])->name('student.profile.update');

    // Modul Profil & Biodata GTK (Pendidik & Tenaga Kependidikan)
    Route::get('/gtk/profile', [TeacherProfileController::class, 'show'])->name('teacher.profile');
    Route::put('/gtk/profile/{teacher}', [TeacherProfileController::class, 'update'])->name('teacher.profile.update');
});

// Admin & Staff Panel (Super Admin, Kepala Sekolah, Admin Sekolah, Staf Tendik, Waka, Guru, BK, Bendahara)
Route::prefix('admin')
    ->name('admin.')
    ->middleware(['role:super_admin,principal,school_admin,staff,counselor,treasurer,waka_kurikulum,waka_kesiswaan,waka_sarpras,waka_humas,waka_hubin,teacher', 'tenant'])
    ->group(function () {
        Route::get('/', [AdminDashboardController::class, 'index'])->name('home');
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::get('/data-master', [AdminDataMasterController::class, 'index'])->name('data-master');
        Route::get('/schedules', [AdminScheduleController::class, 'index'])->name('schedules');
        Route::get('/kesiswaan/tatib', [AdminDisciplineController::class, 'index'])->name('kesiswaan.tatib');
        
        // Modul KBM & Jurnal Mengajar Guru (Teacher Teaching Journals & Classroom Agendas)
        Route::get('/kurikulum/jurnal', [AdminTeachingJournalController::class, 'index'])->name('kurikulum.jurnal');
        Route::post('/kurikulum/jurnal/open', [AdminTeachingJournalController::class, 'openSession'])->name('kurikulum.jurnal.open');
        Route::get('/kurikulum/jurnal/{id}', [AdminTeachingJournalController::class, 'show'])->name('kurikulum.jurnal.show');
        Route::put('/kurikulum/jurnal/{id}', [AdminTeachingJournalController::class, 'update'])->name('kurikulum.jurnal.update');
        Route::get('/kurikulum/jurnal-laporan', [AdminTeachingJournalController::class, 'report'])->name('kurikulum.jurnal.report');
        Route::post('/kurikulum/jurnal/{id}/verify', [AdminTeachingJournalController::class, 'verify'])->name('kurikulum.jurnal.verify');

        // Modul Manajemen Pengguna & Hak Akses (RBAC & Enrollment Sekolah)
        Route::middleware(['role:super_admin,school_admin'])->group(function () {
            Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
            Route::get('/users/create', [AdminUserController::class, 'create'])->name('users.create');
            Route::get('/users/enrollments', [AdminUserController::class, 'enrollments'])->name('users.enrollments');
            Route::post('/users', [AdminUserController::class, 'store'])->name('users.store');
            Route::get('/users/{id}/edit', [AdminUserController::class, 'edit'])->name('users.edit');
            Route::put('/users/{id}', [AdminUserController::class, 'update'])->name('users.update');
            Route::patch('/users/{id}/toggle-status', [AdminUserController::class, 'toggleStatus'])->name('users.toggle-status');
            Route::post('/users/{id}/enroll', [AdminUserController::class, 'enrollSchool'])->name('users.enroll');
            Route::delete('/users/{id}', [AdminUserController::class, 'destroy'])->name('users.destroy');
            Route::post('/gtk/profile/window', [TeacherProfileController::class, 'updateWindowSettings'])->name('gtk.profile.window');

            // Modul Unggah Masal Excel (Bulk Import Siswa & GTK)
            Route::get('/import/template/students', [\App\Http\Controllers\AdminBulkImportController::class, 'downloadStudentTemplate'])->name('import.template.students');
            Route::get('/import/template/teachers', [\App\Http\Controllers\AdminBulkImportController::class, 'downloadTeacherTemplate'])->name('import.template.teachers');
            Route::post('/import/students', [\App\Http\Controllers\AdminBulkImportController::class, 'importStudents'])->name('import.students');
            Route::post('/import/teachers', [\App\Http\Controllers\AdminBulkImportController::class, 'importTeachers'])->name('import.teachers');
        });

        Route::post('/switch-school', [AdminDataMasterController::class, 'switchSchool'])->name('switch-school');
        Route::get('/switch-school/{id}', [AdminDataMasterController::class, 'switchSchoolGet'])->name('switch-school.get');
    });

