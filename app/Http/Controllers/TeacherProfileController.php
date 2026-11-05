<?php

namespace App\Http\Controllers;

use App\Models\School;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class TeacherProfileController extends Controller
{
    /**
     * Tampilkan halaman rincian profil GTK (Guru & Tenaga Kependidikan)
     */
    public function show(Request $request): View|RedirectResponse
    {
        /** @var User|null $user */
        $user = Auth::user();

        $schoolId = session('active_school_id') ?? $user?->school_id ?? 1;
        $school = School::find($schoolId) ?? School::first();

        $isStaff = $user && in_array($user->role, ['super_admin', 'school_admin']);
        $allTeachers = collect();

        // 1. Dapatkan model Teacher sesuai role pengguna
        if ($user && in_array($user->role, ['teacher', 'waka_kurikulum', 'waka_kesiswaan', 'waka_sarpras', 'waka_humas', 'waka_hubin'])) {
            $teacher = Teacher::with(['school', 'homeroomClasses', 'schedules.subject', 'schedules.schoolClass'])
                ->where('user_id', $user->id)
                ->first();

            // Fallback jika belum terhubung user_id secara eksplisit
            if (! $teacher && $user->school_id) {
                $teacher = Teacher::with(['school', 'homeroomClasses', 'schedules.subject', 'schedules.schoolClass'])
                    ->where('school_id', $user->school_id)
                    ->where(function ($q) use ($user) {
                        $q->where('full_name', 'like', "%{$user->name}%")
                          ->orWhere('email', $user->email);
                    })
                    ->first();

                // Hubungkan otomatis jika ditemukan
                if ($teacher && ! $teacher->user_id) {
                    $teacher->update(['user_id' => $user->id]);
                }
            }
        } else {
            // Mode Admin: dapat melihat dan mengelola seluruh profil GTK
            $teacherId = $request->query('teacher_id');
            $teacherQuery = Teacher::with(['school', 'homeroomClasses', 'schedules.subject', 'schedules.schoolClass'])
                ->where('school_id', $schoolId);

            $allTeachers = (clone $teacherQuery)->orderBy('full_name')->get();

            $teacher = $teacherId ? (clone $teacherQuery)->find($teacherId) : (clone $teacherQuery)->first();

            // Jika belum ada data guru di sekolah ini, buat dummy dasar untuk admin
            if (! $teacher && $school) {
                $teacher = Teacher::create([
                    'school_id' => $school->id,
                    'user_id' => $user?->id,
                    'full_name' => $user?->name ?? 'Guru / GTK Sekolah',
                    'email' => $user?->email,
                    'gender' => 'L',
                    'employment_type' => 'GURU_MAPEL',
                    'employment_status' => 'PNS',
                    'is_active' => true,
                ]);
            }
        }

        if (! $teacher) {
            return redirect()->route('admin.users.index')->with('error', 'Profil data Guru / GTK belum terdaftar di sistem. Silakan daftarkan terlebih dahulu.');
        }

        // 2. Evaluasi status periode edit mandiri
        $targetSchool = $teacher->school ?? $school;
        $isWindowOpen = $targetSchool ? $targetSchool->isGtkProfileEditOpen() : true;
        $canEdit = $isWindowOpen || $isStaff;

        return view('teacher.profile', [
            'teacher' => $teacher,
            'school' => $targetSchool,
            'isWindowOpen' => $isWindowOpen,
            'isStaff' => $isStaff,
            'canEdit' => $canEdit,
            'allTeachers' => $allTeachers,
        ]);
    }

    /**
     * Perbarui data profil dan pas foto 4x6 GTK
     */
    public function update(Request $request, Teacher $teacher): RedirectResponse
    {
        /** @var User|null $user */
        $user = Auth::user();
        $isStaff = $user && in_array($user->role, ['super_admin', 'school_admin']);
        $isOwner = $user && ($user->id === $teacher->user_id || ($teacher->email && $user->email === $teacher->email));

        $school = $teacher->school ?? School::find($teacher->school_id);
        $isWindowOpen = $school ? $school->isGtkProfileEditOpen() : true;

        if (! $isStaff && (! $isOwner || ! $isWindowOpen)) {
            return redirect()->back()->with('error', 'Periode pemutakhiran profil GTK telah ditutup atau Anda tidak memiliki otorisasi.');
        }

        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'title_prefix' => 'nullable|string|max:50',
            'title_suffix' => 'nullable|string|max:50',
            'gender' => 'required|in:L,P',
            'nik' => 'nullable|string|max:16',
            'nip' => 'nullable|string|max:30',
            'nuptk' => 'nullable|string|max:30',
            'birth_place' => 'nullable|string|max:100',
            'birth_date' => 'nullable|date',
            'mother_maiden_name' => 'nullable|string|max:255',
            'religion' => 'nullable|string|max:50',
            'marital_status' => 'nullable|string|max:50',
            'spouse_name' => 'nullable|string|max:255',
            'spouse_job' => 'nullable|string|max:150',

            // Domisili & Kontak
            'address' => 'nullable|string',
            'rt' => 'nullable|string|max:5',
            'rw' => 'nullable|string|max:5',
            'sub_district' => 'nullable|string|max:100',
            'district' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'province' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:10',
            'phone' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:100',

            // Kepegawaian & Penugasan
            'employment_type' => 'nullable|string|max:100',
            'employment_status' => 'nullable|string|max:50',
            'sk_appointment_number' => 'nullable|string|max:100',
            'sk_appointment_date' => 'nullable|date',
            'tmt_appointment' => 'nullable|date',
            'institution_assignment' => 'nullable|string|max:150',
            'institution_assignment_status' => 'nullable|string|max:50',
            'salary_source' => 'nullable|string|max:100',

            // Pendidikan & Sertifikasi
            'last_education' => 'nullable|string|max:20',
            'major_study' => 'nullable|string|max:150',
            'university_name' => 'nullable|string|max:255',
            'graduation_year' => 'nullable|string|max:10',
            'is_certified' => 'nullable|boolean',
            'certification_number' => 'nullable|string|max:50',
            'nrg' => 'nullable|string|max:50',
            'certification_subject' => 'nullable|string|max:150',

            // Tugas Tambahan & JJM
            'additional_duties' => 'nullable|string',
            'teaching_hours' => 'nullable|integer|min:0|max:80',

            // Rekening & Pajak
            'npwp' => 'nullable|string|max:30',
            'bank_name' => 'nullable|string|max:100',
            'bank_account_number' => 'nullable|string|max:50',
            'bank_account_name' => 'nullable|string|max:255',

            // Pas Foto 4x6
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        // Upload Pas Foto Resmi 4x6
        if ($request->hasFile('photo')) {
            // Hapus file lama jika ada
            if ($teacher->photo_path && Storage::disk('public')->exists($teacher->photo_path)) {
                Storage::disk('public')->delete($teacher->photo_path);
            }

            $photoPath = $request->file('photo')->store('teachers/photos', 'public');
            $validated['photo_path'] = $photoPath;
        }

        $validated['is_certified'] = $request->boolean('is_certified');

        $teacher->update($validated);

        // Selaraskan data akun login jika terhubung
        if ($teacher->user) {
            $userUpdates = ['name' => $teacher->full_name];
            if (!empty($validated['phone'])) {
                $userUpdates['phone'] = $validated['phone'];
            }
            $teacher->user->update($userUpdates);
        }

        return redirect()->back()->with('success', 'Data profil Dapodik dan pas foto resmi GTK berhasil disimpan.');
    }

    /**
     * Konfigurasi jadwal durasi pengeditan profil mandiri GTK (Admin Only)
     */
    public function updateWindowSettings(Request $request): RedirectResponse
    {
        /** @var User|null $user */
        $user = Auth::user();
        if (! $user || ! in_array($user->role, ['super_admin', 'school_admin'])) {
            abort(403, 'Hanya Administrator yang berhak mengatur jadwal durasi pengeditan profil GTK.');
        }

        $validated = $request->validate([
            'school_id' => 'required|exists:schools,id',
            'gtk_profile_edit_enabled' => 'nullable|boolean',
            'gtk_profile_edit_start' => 'nullable|date',
            'gtk_profile_edit_end' => 'nullable|date|after_or_equal:gtk_profile_edit_start',
        ]);

        $school = School::findOrFail($validated['school_id']);

        $school->update([
            'gtk_profile_edit_enabled' => $request->boolean('gtk_profile_edit_enabled'),
            'gtk_profile_edit_start' => $validated['gtk_profile_edit_start'] ?? null,
            'gtk_profile_edit_end' => $validated['gtk_profile_edit_end'] ?? null,
        ]);

        return redirect()->back()->with('success', 'Jadwal durasi pemutakhiran profil GTK berhasil diperbarui.');
    }
}

