# 🏫 Dokumentasi Implementasi: Modul Kehadiran Siswa di Sekolah

> **Referensi Rancangan:** [`rancangan sistem sekolahku.md`](file:///c:/laragon/www/sekolahku/rancangan%20sistem%20sekolahku.md)  
> **Status:** Selesai Diimplementasikan & Lolos Seluruh Uji Otomatis (11/11 Test Passed)  
> **Tanggal:** 21 Oktober 2026

---

## 1. Ringkasan Implementasi

Sesuai dokumen rancangan sistem pada bagian **Modul Presensi (Bagian 5.1 & 5.2)** dan **Desain API (Bagian 7.2)**, modul kehadiran siswa telah selesai dibangun dengan fitur utama:

1. **Presensi Selfie Wajah + GPS Geofencing**:
   - Pengambilan foto wajah langsung via webcam / kamera depan.
   - Validasi koordinat GPS menggunakan formula lingkaran bumi **Haversine** (satuan meter).
   - Validasi batas radius sekolah (default 150 meter dari titik pusat SMAN 1 Jakarta).
   - Penolakan presensi otomatis jika berada di luar radius (`OUTSIDE_GEOFENCE`) atau jika terdeteksi Fake GPS (`MOCK_LOCATION_DETECTED`).
   - Penentuan status otomatis: Hadir Tepat Waktu (`present`) atau Terlambat (`late`, jika di atas pukul 07:15).
   - Pencegahan duplikasi presensi (1 siswa = 1 presensi masuk per hari).
2. **Presensi Pulang (Check-out)**:
   - Pencatatan waktu kepulangan siswa (`check_out_at`).
3. **Presensi Manual oleh Guru / Wali Kelas**:
   - Untuk siswa yang berhalangan hadir dengan status `sick` (sakit), `excused` (izin), atau `absent` (alpa) disertai pencatatan catatan/surat keterangan dan pencatatan verifikator (`verified_by`).
4. **Presensi Cepat via QR Code / Scan NIS**:
   - Untuk alternatif presensi barcode / kartu identitas sekolah.
5. **Dashboard Rekap & Analitik Kehadiran**:
   - Statistik real-time harian: Total Siswa, Hadir Tepat Waktu, Terlambat, Sakit/Izin, Alpa/Belum Masuk, dan Persentase Kehadiran.
   - Filter tabel berdasarkan tanggal, kelas, dan status kehadiran.
6. **Live Map (Peta Sebaran GPS Siswa)**:
   - Visualisasi Leaflet.js yang menampilkan batas radius lingkaran geofencing sekolah serta titik pin presensi siswa secara real-time.
7. **CRUD Konfigurasi Geofencing**:
   - Pengaturan titik koordinat sekolah (Latitude, Longitude, Radius dalam meter, dan WiFi SSID fallback).

---

## 2. Struktur Komponen yang Dibangun

```
backend/
├── app/
│   ├── Http/Controllers/
│   │   ├── Api/
│   │   │   ├── AttendanceController.php       # Controller utama modul presensi
│   │   │   ├── SchoolGeofenceController.php   # Manajemen titik geofencing
│   │   │   ├── AuthController.php             # Autentikasi Sanctum & Profil
│   │   │   └── AcademicController.php         # Lookup data kelas & siswa
│   │   └── AttendancePortalController.php     # Controller antarmuka web interaktif
│   ├── Models/
│   │   ├── Attendance.php                     # Model Presensi
│   │   ├── SchoolGeofence.php                 # Model Geofence Sekolah
│   │   ├── Student.php                        # Model Siswa
│   │   ├── SchoolClass.php                    # Model Kelas
│   │   ├── School.php                         # Model Sekolah (Multi-tenant)
│   │   └── User.php                           # Model Pengguna
│   └── Services/
│       └── GeofenceService.php                # Service kalkulasi Haversine & validasi radius
├── database/
│   ├── migrations/                            # 9 Migration tabel terkait presensi & sekolah
│   └── seeders/
│       └── SekolahkuAttendanceSeeder.php      # Seeder sekolah, geofence, guru, siswa, kelas, & log presensi
├── resources/views/
│   └── attendance/
│       └── portal.blade.php                   # Portal interaktif: Kamera, GPS simulator, Rekap, & Peta
├── routes/
│   ├── api.php                                # Endpoint REST API v1
│   └── web.php                                # Route antarmuka web /
└── tests/Feature/
    └── AttendanceTest.php                     # 11 Unit/Feature test suite (100% Passed)
```

---

## 3. Daftar Endpoint RESTful API (`/api/v1`)

| Method | Endpoint | Deskripsi | Akses |
|--------|----------|-----------|-------|
| `POST` | `/api/v1/attendances/selfie` | Presensi Selfie + Validasi GPS & Radius Geofence | Siswa |
| `POST` | `/api/v1/attendances/checkout` | Presensi Pulang (Check-out) | Siswa / Guru |
| `POST` | `/api/v1/attendances` | Input Presensi Manual (Hadir/Sakit/Izin/Alpa) | Guru / Admin |
| `POST` | `/api/v1/attendances/qr-scan` | Presensi cepat via Scan NIS / QR Code | Siswa / Petugas |
| `GET` | `/api/v1/attendances` | Daftar presensi (filter: `date`, `class_id`, `status`, `search`) | Semua |
| `GET` | `/api/v1/attendances/today` | Ringkasan statistik & KPI kehadiran hari ini | Semua |
| `GET` | `/api/v1/attendances/map` | Data koordinat GPS siswa hari ini untuk peta | Admin / Guru |
| `GET` | `/api/v1/attendances/report` | Rekap kehadiran bulanan per siswa | Admin / Guru |
| `GET` | `/api/v1/geofences` | Daftar titik geofence sekolah | Semua |
| `POST` | `/api/v1/geofences` | Tambah titik geofence baru | Admin |
| `PUT` | `/api/v1/geofences/{id}` | Update radius atau koordinat geofence | Admin |
| `DELETE` | `/api/v1/geofences/{id}` | Hapus titik geofence | Admin |

---

## 4. Akun Pengujian & Data Demo (Hasil Seeder)

Data demo sudah diisi untuk **SMA Negeri 1 Jakarta** (Tenant: `sman1jkt`, Pusat Geofence: `-6.166827, 106.837562`, Radius: `150m`):

| Role | Nama | Email / Akun | Password | Keterangan |
|------|------|--------------|----------|------------|
| Super Admin | Super Administrator | `superadmin@sekolahku.id` | `password` | Manajemen tenant |
| School Admin | Admin Sekolah | `admin@sman1jkt.sch.id` | `password` | Admin SMAN 1 Jakarta |
| Guru | Dian Prameswari, S.Pd. | `guru.dian@sman1jkt.sch.id` | `password` | Wali Kelas X MIPA 1 |
| Guru | Bambang Wicaksono, M.Pd. | `guru.bambang@sman1jkt.sch.id` | `password` | Wali Kelas X MIPA 2 |
| Siswa 1 | Ahmad Fauzi | `ahmad.fauzi@sekolahku.test` (NIS: 20261001) | `password` | Hadir Tepat Waktu (06:58) |
| Siswa 2 | Siti Nurhaliza | `siti.nurhaliza@sekolahku.test` (NIS: 20261002) | `password` | Terlambat (07:22) |
| Siswa 3 | Budi Santoso | `budi.santoso@sekolahku.test` (NIS: 20261003) | `password` | Siap diuji coba live |
| Siswa 4 | Cantika Dewi | `cantika.dewi@sekolahku.test` (NIS: 20261004) | `password` | Sakit (Surat dokter terlampir) |
| Siswa 5 | Doni Pratama | `doni.pratama@sekolahku.test` (NIS: 20261005) | `password` | Izin (Dispensasi Olimpiade) |
| Siswa 6 | Farhan Maulana | `farhan.maulana@sekolahku.test` (NIS: 20261006) | `password` | Siap diuji coba live |

---

## 5. Cara Menguji Aplikasi di Browser

Aplikasi web portal saat ini telah aktif dan dapat langsung dibuka di browser:

👉 **URL Web Portal:** [http://127.0.0.1:8000/](http://127.0.0.1:8000/)

### Skenario Uji Coba yang Tersedia:
1. **Uji Coba Presensi Masuk di Dalam Radius Sekolah**:
   - Buka tab **🤳 Presensi Selfie Siswa**.
   - Pilih siswa (misal: *Budi Santoso*).
   - Klik tombol simulasi **"🏫 Di Sekolah (~25m)"** (indikator geofence berwarna hijau).
   - Klik **"Ambil Foto & Presensi Masuk"** -> Presensi berhasil dicatat dengan foto dan jarak.
2. **Uji Coba Validasi Penolakan di Luar Radius (Geofencing)**:
   - Pilih siswa lain (misal: *Farhan Maulana*).
   - Klik tombol simulasi **"🏙️ Luar Sekolah (Monas ~1.8km)"** (indikator geofence berubah merah).
   - Klik **"Ambil Foto & Presensi Masuk"** -> Sistem menolak dengan alert detail selisih jarak.
3. **Uji Coba Anti-Spoofing (Mock Location / Fake GPS)**:
   - Klik tombol **"🛡️ Mock GPS: Nonaktif"** untuk mengaktifkan flag mock GPS.
   - Presensi akan otomatis ditolak dengan kode `MOCK_LOCATION_DETECTED`.
4. **Monitoring & Live Map**:
   - Buka tab **📊 Rekap & Monitoring Guru** untuk melihat daftar kehadiran dan filter kelas.
   - Buka tab **🗺️ Peta Sebaran GPS Live** untuk melihat visualisasi batas lingkaran radius 150m dan lokasi siswa.
