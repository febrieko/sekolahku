<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

#[Fillable(['name', 'email', 'password', 'school_id', 'role', 'additional_role', 'phone', 'avatar_url', 'is_active'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function student(): HasOne
    {
        return $this->hasOne(Student::class);
    }

    public function teacher(): HasOne
    {
        return $this->hasOne(Teacher::class);
    }

    public function parentProfile(): HasOne
    {
        return $this->hasOne(ParentGuardian::class, 'user_id');
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    public function isPrincipal(): bool
    {
        return $this->role === 'principal' || $this->role === 'kepala_sekolah';
    }

    public function isSchoolAdmin(): bool
    {
        return in_array($this->role, ['school_admin', 'admin']);
    }

    public function isStaff(): bool
    {
        return in_array($this->role, ['staff', 'school_admin', 'admin']);
    }

    public function isCounselor(): bool
    {
        return $this->role === 'counselor' || $this->role === 'guru_bk';
    }

    public function isTreasurer(): bool
    {
        return in_array($this->role, ['treasurer', 'cashier']);
    }

    public function isCashier(): bool
    {
        return $this->isTreasurer();
    }

    /**
     * Memeriksa apakah user mengemban tugas tambahan Wakil Kepala Sekolah (Waka).
     */
    public function isWaka(): bool
    {
        $duty = $this->additional_role;
        return ($duty && str_starts_with($duty, 'waka_'))
            || str_starts_with($this->role, 'waka_');
    }

    public function isWakaKurikulum(): bool
    {
        return $this->additional_role === 'waka_kurikulum' || $this->role === 'waka_kurikulum';
    }

    public function isWakaKesiswaan(): bool
    {
        return $this->additional_role === 'waka_kesiswaan' || $this->role === 'waka_kesiswaan';
    }

    public function isWakaSarpras(): bool
    {
        return $this->additional_role === 'waka_sarpras' || $this->role === 'waka_sarpras';
    }

    public function isWakaHumas(): bool
    {
        return in_array($this->additional_role, ['waka_humas', 'waka_hubin'])
            || in_array($this->role, ['waka_humas', 'waka_hubin']);
    }

    /**
     * Di sekolah Indonesia, seluruh Waka dan Guru BK hakikatnya adalah Guru dengan tugas tambahan.
     */
    public function isTeacher(): bool
    {
        return in_array($this->role, ['teacher', 'guru', 'counselor', 'guru_bk'])
            || $this->isWaka();
    }

    public function isStudent(): bool
    {
        return $this->role === 'student' || $this->role === 'siswa';
    }

    public function isParent(): bool
    {
        return $this->role === 'parent' || $this->role === 'wali_murid';
    }

    public function hasRole(string $role): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        if ($this->role === $role || $this->additional_role === $role) {
            return true;
        }

        if ($role === 'teacher' && $this->isTeacher()) {
            return true;
        }

        if (in_array($role, ['cashier', 'treasurer']) && $this->isTreasurer()) {
            return true;
        }

        if (in_array($role, ['principal', 'kepala_sekolah']) && $this->isPrincipal()) {
            return true;
        }

        if (in_array($role, ['counselor', 'guru_bk']) && $this->isCounselor()) {
            return true;
        }

        if (in_array($role, ['staff', 'tendik']) && $this->isStaff()) {
            return true;
        }

        return false;
    }

    public function hasAnyRole(array $roles): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        foreach ($roles as $r) {
            if ($this->hasRole($r)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Memeriksa apakah user memiliki hak akses menulis/mengubah (CRUD) pada tab data master tertentu.
     */
    public function canManageTab(string $tab): bool
    {
        if ($this->isSuperAdmin() || $this->isSchoolAdmin()) {
            return true;
        }

        // Kepala Sekolah memiliki hak tinjau supervisi luas
        if ($this->isPrincipal()) {
            return in_array($tab, ['tab-years', 'tab-curriculums', 'tab-subjects', 'tab-schedules', 'tab-classes', 'tab-students', 'tab-teachers']);
        }

        return match ($tab) {
            'tab-years', 'tab-curriculums', 'tab-subjects' => $this->isWakaKurikulum(),
            'tab-schedules' => $this->isWakaKurikulum() || $this->isWakaSarpras(),
            'tab-vocational' => $this->isWakaKurikulum() || $this->isWakaHumas(),
            'tab-classes' => $this->isWakaKurikulum() || $this->isWakaKesiswaan() || $this->isWakaSarpras(),
            'tab-students', 'tab-parents' => $this->isWakaKesiswaan() || $this->isCounselor(),
            'tab-schools', 'tab-teachers' => false,
            default => false,
        };
    }

    /**
     * Daftar seluruh peran utama resmi yang didukung sistem Sekolahku.
     */
    public static function getAvailableRoles(): array
    {
        return [
            'super_admin' => [
                'title' => 'Super Administrator (Pusat)',
                'category' => 'admin',
                'category_label' => 'Pimpinan & Admin',
                'badge' => 'badge-danger',
                'description' => 'Akses penuh lintas satuan pendidikan dan pengaturan platform.',
            ],
            'principal' => [
                'title' => 'Kepala Sekolah (Principal)',
                'category' => 'admin',
                'category_label' => 'Pimpinan & Admin',
                'badge' => 'badge-primary',
                'description' => 'Pimpinan tertinggi satuan pendidikan, supervisi KBM, evaluasi kinerja GTK, dan laporan eksekutif.',
            ],
            'school_admin' => [
                'title' => 'Administrator Sekolah (TU)',
                'category' => 'admin',
                'category_label' => 'Pimpinan & Admin',
                'badge' => 'badge-primary',
                'description' => 'Pengelolaan data master, kepegawaian GTK, perizinan jadwal edit, dan administrasi operasional sekolah.',
            ],
            'staff' => [
                'title' => 'Staf Tenaga Kependidikan (Tendik)',
                'category' => 'staff',
                'category_label' => 'Tenaga Kependidikan',
                'badge' => 'badge-info',
                'description' => 'Staf administrasi TU, perpustakaan, dan laboratorium operasional.',
            ],
            'teacher' => [
                'title' => 'Guru / Tenaga Pendidik',
                'category' => 'teacher',
                'category_label' => 'Pendidik & GTK',
                'badge' => 'badge-success',
                'description' => 'Pelaksanaan KBM, isi jurnal mengajar, presensi tatap muka, dan tugas tambahan Waka/Wali Kelas.',
            ],
            'counselor' => [
                'title' => 'Guru Bimbingan Konseling (BK)',
                'category' => 'teacher',
                'category_label' => 'Pendidik & GTK',
                'badge' => 'badge-warning',
                'description' => 'Layanan bimbingan konseling, penanganan kredit poin pelanggaran, dan pembinaan siswa.',
            ],
            'treasurer' => [
                'title' => 'Bendahara Sekolah / Keuangan',
                'category' => 'finance',
                'category_label' => 'Keuangan',
                'badge' => 'badge-danger',
                'description' => 'Pencatatan pembayaran SPP, transaksi kasir, dan pembukuan kas sekolah.',
            ],
            'student' => [
                'title' => 'Peserta Didik (Siswa)',
                'category' => 'student',
                'category_label' => 'Peserta Didik',
                'badge' => 'badge-info',
                'description' => 'Presensi mandiri (GPS Selfie), memantau jadwal KBM, dan kartu pelajar digital.',
            ],
            'parent' => [
                'title' => 'Orang Tua / Wali Murid',
                'category' => 'parent',
                'category_label' => 'Orang Tua / Wali',
                'badge' => 'badge-warning',
                'description' => 'Memantau presensi dan aktivitas belajar anak di sekolah.',
            ],
        ];
    }

    /**
     * Daftar Tugas Tambahan Resmi bagi Guru (GTK) sesuai instrumen Dapodik.
     */
    public static function getAvailableAdditionalDuties(): array
    {
        return [
            'waka_kurikulum' => [
                'title' => 'Waka Kurikulum & Akademik',
                'badge' => 'badge-info',
                'teaching_hours' => 12,
                'description' => 'Penyusunan kurikulum, pembagian beban KBM, alokasi jam, dan jadwal belajar.',
            ],
            'waka_kesiswaan' => [
                'title' => 'Waka Kesiswaan & Kedisiplinan',
                'badge' => 'badge-warning',
                'teaching_hours' => 12,
                'description' => 'Pembinaan kesiswaan, penempatan rombel, presensi siswa, dan tata tertib/kredit poin.',
            ],
            'waka_sarpras' => [
                'title' => 'Waka Sarana & Prasarana',
                'badge' => 'badge-secondary',
                'teaching_hours' => 12,
                'description' => 'Inventarisasi sarpras, ketersediaan ruang kelas KBM, laboratorium, dan fasilitas belajar.',
            ],
            'waka_humas' => [
                'title' => 'Waka Hubungan Industri & Humas',
                'badge' => 'badge-dark',
                'teaching_hours' => 12,
                'description' => 'Kerjasama kemitraan DUDI/Industri, koordinasi program PKL/magang, dan kehumasan.',
            ],
            'kepala_lab' => [
                'title' => 'Kepala Laboratorium / Bengkel',
                'badge' => 'badge-primary',
                'teaching_hours' => 12,
                'description' => 'Pengelolaan laboratorium IPA/Komputer/Bahasa atau bengkel kejuruan.',
            ],
            'kepala_perpus' => [
                'title' => 'Kepala Perpustakaan',
                'badge' => 'badge-primary',
                'teaching_hours' => 12,
                'description' => 'Pengelolaan perpustakaan sekolah dan literasi satuan pendidikan.',
            ],
            'pembina_osis' => [
                'title' => 'Pembina OSIS & Ekstrakurikuler',
                'badge' => 'badge-success',
                'teaching_hours' => 6,
                'description' => 'Pembinaan kegiatan ekstrakurikuler dan kepengurusan OSIS.',
            ],
        ];
    }

    /**
     * Mendapatkan daftar kategori peran untuk filter direktori pengguna.
     */
    public static function getRoleCategories(): array
    {
        return [
            'all' => 'Semua Pengguna',
            'admin' => 'Pimpinan & Admin',
            'teacher' => 'Pendidik & GTK',
            'staff' => 'Tenaga Kependidikan',
            'student' => 'Peserta Didik',
            'parent' => 'Orang Tua / Wali',
            'finance' => 'Keuangan & Bendahara',
        ];
    }

    /**
     * Mendapatkan label peran resmi dalam bahasa Indonesia.
     */
    public function getRoleTitle(): string
    {
        $duties = self::getAvailableAdditionalDuties();
        if ($this->additional_role && isset($duties[$this->additional_role])) {
            return "Guru (" . $duties[$this->additional_role]['title'] . ")";
        }

        return match ($this->role) {
            'super_admin' => 'Super Administrator (Pusat)',
            'principal', 'kepala_sekolah' => 'Kepala Sekolah',
            'school_admin' => 'Administrator Sekolah (TU)',
            'staff' => 'Staf Tenaga Kependidikan',
            'teacher' => 'Guru / Tenaga Pendidik',
            'counselor', 'guru_bk' => 'Guru BK (Konselor)',
            'treasurer', 'cashier' => 'Bendahara Sekolah',
            'student' => 'Peserta Didik (Siswa)',
            'parent' => 'Orang Tua / Wali Murid',
            'waka_kurikulum' => 'Guru (Waka Kurikulum)',
            'waka_kesiswaan' => 'Guru (Waka Kesiswaan)',
            'waka_sarpras' => 'Guru (Waka Sarpras)',
            'waka_humas', 'waka_hubin' => 'Guru (Waka Humas/Hubin)',
            default => ucfirst(str_replace('_', ' ', $this->role)),
        };
    }

    /**
     * Mendapatkan deskripsi Tupoksi berdasarkan peran.
     */
    public function getRoleDescription(): string
    {
        if ($this->additional_role) {
            $duties = self::getAvailableAdditionalDuties();
            if (isset($duties[$this->additional_role])) {
                return $duties[$this->additional_role]['description'];
            }
        }

        return match ($this->role) {
            'super_admin' => 'Akses penuh lintas satuan pendidikan dan pengaturan platform.',
            'principal', 'kepala_sekolah' => 'Pimpinan tertinggi satuan pendidikan, supervisi KBM, evaluasi kinerja GTK, dan laporan eksekutif.',
            'school_admin' => 'Pengelolaan data master, kepegawaian GTK, dan administrasi operasional sekolah.',
            'staff' => 'Staf administrasi TU, perpustakaan, dan laboratorium operasional.',
            'teacher' => 'Tupoksi: Pelaksanaan KBM, presensi kelas tatap muka, dan tugas tambahan pendidik.',
            'counselor', 'guru_bk' => 'Layanan bimbingan konseling, penanganan kredit poin pelanggaran, dan pembinaan siswa.',
            'treasurer', 'cashier' => 'Pencatatan pembayaran SPP, transaksi kasir, dan pembukuan kas sekolah.',
            'student' => 'Presensi mandiri (GPS Selfie), memantau jadwal KBM, dan riwayat belajar.',
            'parent' => 'Memantau presensi dan aktivitas belajar anak di sekolah.',
            'waka_kurikulum' => 'Penyusunan kurikulum, pembagian beban KBM, alokasi mapel, kalender akademik, dan jadwal belajar.',
            'waka_kesiswaan' => 'Pembinaan peserta didik, penempatan rombel, pemantauan presensi/kedisiplinan, dan tata tertib.',
            'waka_sarpras' => 'Inventarisasi aset sarpras, pengelolaan ketersediaan ruang kelas KBM, laboratorium, dan fasilitas belajar.',
            'waka_humas', 'waka_hubin' => 'Kerjasama kemitraan DUDI/Industri, koordinasi program PKL/magang siswa, dan publikasi humas.',
            default => 'Pengguna sistem Sekolahku.',
        };
    }

    /**
     * Mendapatkan warna badge untuk peran.
     */
    public function getRoleBadgeClass(): string
    {
        if ($this->additional_role) {
            $duties = self::getAvailableAdditionalDuties();
            if (isset($duties[$this->additional_role])) {
                return $duties[$this->additional_role]['badge'];
            }
        }

        return match ($this->role) {
            'super_admin' => 'badge-danger',
            'principal', 'kepala_sekolah' => 'badge-primary',
            'school_admin' => 'badge-primary',
            'staff' => 'badge-info',
            'teacher' => 'badge-success',
            'counselor', 'guru_bk' => 'badge-warning',
            'treasurer', 'cashier' => 'badge-danger',
            'parent' => 'badge-warning',
            'student' => 'badge-info',
            'waka_kurikulum' => 'badge-info',
            'waka_kesiswaan' => 'badge-warning',
            'waka_sarpras' => 'badge-secondary',
            'waka_humas', 'waka_hubin' => 'badge-dark',
            default => 'badge-secondary',
        };
    }
}
