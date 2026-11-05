<?php

namespace App\Http\Controllers;

use App\Models\School;
use App\Models\Student;
use App\Services\GeofenceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class StudentProfileController extends Controller
{
    public function __construct(private GeofenceService $geofenceService)
    {
    }

    /**
     * Tampilkan halaman profil & edit biodata peserta didik.
     */
    public function show(Request $request): View|RedirectResponse
    {
        $user = Auth::user();

        // 1. Dapatkan model siswa terkait
        if ($user && $user->role === 'student') {
            $student = Student::with(['schoolClass', 'entryAcademicYear', 'school', 'parents'])
                ->where('user_id', $user->id)
                ->first();

            // Fallback jika belum di-link user_id
            if (! $student && $user->school_id) {
                $student = Student::with(['schoolClass', 'entryAcademicYear', 'school', 'parents'])
                    ->where('school_id', $user->school_id)
                    ->where('full_name', 'like', "%{$user->name}%")
                    ->first();
            }
        } else {
            // Akses preview oleh Admin / Guru
            $studentId = $request->query('student_id');
            $schoolId = session('active_school_id') ?? $user?->school_id ?? 1;

            $studentQuery = Student::with(['schoolClass', 'entryAcademicYear', 'school', 'parents'])
                ->where('school_id', $schoolId);

            $student = $studentId ? $studentQuery->find($studentId) : $studentQuery->first();
        }

        if (! $student) {
            return redirect()->route('home')->with('error', 'Data profil peserta didik tidak ditemukan.');
        }

        $school = $student->school ?? School::find($student->school_id) ?? School::first();

        // 2. Evaluasi status periode edit
        $isWindowOpen = $school ? $school->isStudentProfileEditOpen() : true;
        $isStaff = $user && in_array($user->role, ['super_admin', 'school_admin', 'waka_kesiswaan', 'waka_kurikulum']);
        $canEdit = $isWindowOpen || $isStaff;

        // 3. Hitung estimasi jarak rumah ke sekolah jika koordinat tersedia
        $schoolLat = $school?->latitude ?? $school?->geofences()->where('is_primary', true)->value('latitude') ?? $school?->geofences()->value('latitude') ?? -6.166827;
        $schoolLng = $school?->longitude ?? $school?->geofences()->where('is_primary', true)->value('longitude') ?? $school?->geofences()->value('longitude') ?? 106.837562;

        $calculatedDistance = null;
        if ($student->home_latitude && $student->home_longitude && $schoolLat && $schoolLng) {
            $calculatedDistance = round($this->geofenceService->calculateDistance(
                (float) $student->home_latitude,
                (float) $student->home_longitude,
                (float) $schoolLat,
                (float) $schoolLng
            ) / 1000, 2);
        }

        return view('student.profile', [
            'student' => $student,
            'school' => $school,
            'isWindowOpen' => $isWindowOpen,
            'isStaff' => $isStaff,
            'canEdit' => $canEdit,
            'calculatedDistance' => $calculatedDistance,
        ]);
    }

    /**
     * Proses pembaruan biodata profil oleh siswa (atau admin).
     */
    public function update(Request $request): RedirectResponse
    {
        $user = Auth::user();

        // 1. Tentukan target siswa
        if ($user && $user->role === 'student') {
            $student = Student::where('user_id', $user->id)->first()
                ?? Student::where('school_id', $user->school_id)->where('full_name', 'like', "%{$user->name}%")->firstOrFail();
        } else {
            $studentId = $request->input('student_id');
            $student = Student::findOrFail($studentId);
        }

        $school = $student->school ?? School::find($student->school_id);
        $isStaff = $user && in_array($user->role, ['super_admin', 'school_admin', 'waka_kesiswaan']);

        // 2. Validasi rentang tanggal edit jika bukan staff
        if (! $isStaff && $school && ! $school->isStudentProfileEditOpen()) {
            return back()->with('error', 'Maaf, periode pemutakhiran biodata profil peserta didik saat ini sedang ditutup oleh pihak sekolah.');
        }

        // 3. Validasi input formulir
        $validated = $request->validate([
            // Identitas Pokok
            'full_name' => 'required|string|max:100',
            'gender' => 'required|in:L,P',
            'birth_place' => 'nullable|string|max:100',
            'birth_date' => 'nullable|date',
            'nik' => 'nullable|string|size:16',
            'no_kk' => 'nullable|string|size:16',
            'birth_certificate_no' => 'nullable|string|max:100',
            'religion' => 'nullable|string|max:30',
            'special_needs' => 'nullable|string|max:100',

            // Kontak & Orang Tua / Wali (Digabung)
            'student_phone' => 'nullable|string|max:25',
            'parent_name' => 'nullable|string|max:100',
            'parent_phone' => 'nullable|string|max:25',

            // Domisili & Titik Koordinat Rumah
            'address' => 'nullable|string',
            'rt' => 'nullable|string|max:5',
            'rw' => 'nullable|string|max:5',
            'sub_district' => 'nullable|string|max:100',
            'district' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'province' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:10',
            'home_latitude' => 'nullable|numeric|between:-90,90',
            'home_longitude' => 'nullable|numeric|between:-180,180',
            'residence_type' => 'nullable|string|max:50',
            'transportation_mode' => 'nullable|string|max:50',

            // Sekolah Asal / Sekolah Sebelumnya
            'previous_school' => 'nullable|string|max:150',

            // Data Periodik & Fisik
            'height_cm' => 'nullable|integer|between:40,250',
            'weight_kg' => 'nullable|integer|between:15,250',
            'head_circumference_cm' => 'nullable|integer|between:30,100',
            'blood_type' => 'nullable|in:A,B,AB,O,Tidak Tahu',
            'child_order' => 'nullable|integer|min:1',
            'siblings_count' => 'nullable|integer|min:0',

            // Kesejahteraan
            'kps_receiver' => 'nullable|boolean',
            'kps_number' => 'nullable|string|max:50',
            'kip_receiver' => 'nullable|boolean',
            'kip_number' => 'nullable|string|max:50',

            // Pasfoto Resmi 4 x 6 cm
            'photo' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:2048',
        ], [
            'photo.image' => 'File pasfoto harus berupa gambar yang valid (JPG, JPEG, PNG, WEBP).',
            'photo.max' => 'Ukuran file pasfoto maksimal 2 MB.',
            'nik.size' => 'Nomor Induk Kependudukan (NIK) harus tepat 16 digit.',
            'no_kk.size' => 'Nomor Kartu Keluarga (KK) harus tepat 16 digit.',
        ]);

        // 4. Upload Pasfoto 4x6 jika disertakan
        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $filename = 'student_' . $student->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('student_photos', $filename, 'public');
            $validated['photo_url'] = '/storage/' . $path;
        }

        // 5. Otomatis hitung jarak ke sekolah jika titik koordinat rumah diisi
        $schoolLat = $school?->latitude ?? $school?->geofences()->where('is_primary', true)->value('latitude') ?? $school?->geofences()->value('latitude') ?? -6.166827;
        $schoolLng = $school?->longitude ?? $school?->geofences()->where('is_primary', true)->value('longitude') ?? $school?->geofences()->value('longitude') ?? 106.837562;

        if (! empty($validated['home_latitude']) && ! empty($validated['home_longitude']) && $schoolLat && $schoolLng) {
            $distMeters = $this->geofenceService->calculateDistance(
                (float) $validated['home_latitude'],
                (float) $validated['home_longitude'],
                (float) $schoolLat,
                (float) $schoolLng
            );
            $validated['distance_to_school_km'] = round($distMeters / 1000, 2);
        }

        // Hapus key photo upload file agar tidak masuk fillable
        unset($validated['photo']);

        $student->update($validated);

        return back()->with('success', 'Biodata profil peserta didik dan pasfoto 4x6 berhasil diperbarui.');
    }
}
