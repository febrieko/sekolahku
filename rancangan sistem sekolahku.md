# 📚 SEKOLAHKU — Rancangan Sistem Informasi Manajemen Sekolah Terintegrasi

> **Versi Dokumen:** 2.9.0  
> **Tanggal:** 20 Oktober 2026  
> **Diperbarui:** 7 November 2026 — **Modul Manajemen Identitas & Branding Aplikasi (Khusus Super Admin)**:
> 1. **Kustomisasi Logo, Nama Aplikasi, & Tagline (`/admin/settings/branding`)**: Pengaturan identitas visual aplikasi yang dinamis tersimpan di basis data (`app_settings`) dan di-cache secara otomatis demi performa tinggi.
> 2. **Refleksi Real-time & Global**: Perubahan nama aplikasi dan logo langsung terfleksikan secara menyeluruh pada Sidebar Header (`layouts.vuexy`), Title Bar Browser, Favicon, Halaman Login (`auth.login`), serta Sidebar Portal Siswa (`layouts.student`).
> 3. **Pratinjau Langsung (Live Preview Mockup)**: Antarmuka Super Admin dilengkapi simulasi sidebar header real-time saat mengetik nama aplikasi atau memilih berkas logo baru.
> 4. **Dukungan Format Logo & Fitur Reset Standar**: Mendukung PNG, JPG, SVG, WebP hingga 2MB, penyimpanan otomatis ke storage public, fitur hapus logo custom kembali ke ikon standar, serta 1-click Reset ke Standar Sistem.
> 
> *Riwayat Pembaruan Sebelumnya (v2.8.0 - 5 November 2026)*:
> - Modul Bulk Import Excel/CSV Pengguna (Siswa & GTK) & Autentikasi Multi-Identitas NISN.
> - Generator Template Excel Dinamis Berpetunjuk (`phpoffice/phpspreadsheet`).  
> **Status:** Draf Aktif  
> **Referensi:** [simeela.com](https://simeela.com/)

---

## 📋 Daftar Isi

1. [Latar Belakang & Visi](#1-latar-belakang--visi)
2. [Tujuan Sistem](#2-tujuan-sistem)
3. [Stakeholder & Aktor](#3-stakeholder--aktor)
4. [Arsitektur Sistem](#4-arsitektur-sistem)
5. [Modul & Fitur](#5-modul--fitur)
6. [Desain Database](#6-desain-database)
7. [Desain API](#7-desain-api)
8. [Tech Stack & Teknologi](#8-tech-stack--teknologi)
9. [Keamanan Sistem](#9-keamanan-sistem)
10. [Infrastruktur & Deployment](#10-infrastruktur--deployment)
11. [Roadmap Pengembangan](#11-roadmap-pengembangan)
12. [Estimasi Biaya & Paket Layanan](#12-estimasi-biaya--paket-layanan)

---

## 1. Latar Belakang & Visi

### 1.1 Latar Belakang

Institusi pendidikan di Indonesia, khususnya sekolah formal dan pondok pesantren, masih banyak yang mengelola data secara manual atau menggunakan sistem yang tidak terintegrasi. Hal ini mengakibatkan:

- ❌ Data siswa, guru, dan keuangan tersebar di berbagai sistem berbeda
- ❌ Proses pembayaran SPP yang lambat dan rawan kesalahan pencatatan
- ❌ Orang tua tidak dapat memantau perkembangan anak secara real-time
- ❌ Biaya IT yang tinggi untuk infrastruktur dan maintenance on-premise
- ❌ Laporan akademik dan keuangan membutuhkan waktu lama untuk disusun

### 1.2 Visi

> **"Menjadi platform manajemen pendidikan terdepan yang menghubungkan seluruh ekosistem sekolah — siswa, guru, orang tua, dan administrasi — dalam satu sistem terintegrasi berbasis cloud."**

### 1.3 Misi

- Menyederhanakan proses administrasi sekolah melalui digitalisasi penuh
- Meningkatkan transparansi dan akuntabilitas keuangan sekolah
- Memberikan akses real-time kepada orang tua untuk memantau perkembangan anak
- Mengurangi beban operasional IT sekolah dengan solusi cloud-based SaaS

---

## 2. Tujuan Sistem

| No | Tujuan | Indikator Keberhasilan |
|----|--------|------------------------|
| 1 | Digitalisasi administrasi sekolah | 100% data tercatat digital |
| 2 | Otomasi pembayaran SPP | Waktu proses < 1 menit via Virtual Account |
| 3 | Monitoring real-time orang tua | Notifikasi push dalam < 5 detik |
| 4 | Laporan keuangan otomatis | Laporan tersedia setiap akhir bulan otomatis |
| 5 | Integrasi antar modul | 0 duplikasi data antar modul |
| 6 | Aksesibilitas multi-platform | Web + Android + iOS |

---

## 3. Stakeholder & Aktor

### 3.1 Diagram Aktor

```
┌─────────────────────────────────────────────────────────────────────────────────┐
│                              SISTEM SEKOLAHKU                                   │
│                                                                                 │
│  ┌──────────────┐   ┌──────────────┐   ┌──────────────┐   ┌────────────────┐    │
│  │ SUPER ADMIN  │   │ KEPALA       │   │ ADMIN TU     │   │ STAF TENDIK    │    │
│  │ (Pusat SaaS) │   │ SEKOLAH      │   │ SEKOLAH      │   │ (Administrasi) │    │
│  └──────┬───────┘   └──────┬───────┘   └──────┬───────┘   └───────┬────────┘    │
│         │                  │                  │                   │             │
│  ┌──────┴──────────────────┴──────────────────┴───────────────────┴────────── ┐ │
│  │                    CORE RBAC & TENANCY ENGINE                              │ │
│  └──────┬──────────────────┬──────────────────┬───────────────────┬───────────┘ │
│         │                  │                  │                   │             │
│  ┌──────┴───────┐   ┌──────┴───────┐   ┌──────┴───────┐   ┌───────┴────────┐    │
│  │ GURU (GTK) & │   │ GURU BK      │   │ BENDAHARA    │   │ OPERATOR       │    │
│  │ TUGAS WAKA   │   │ (Konselor)   │   │ KEUANGAN/SPP │   │ KANTIN (POS)   │    │
│  └──────────────┘   └──────────────┘   └──────────────┘   └────────────────┘    │
│         │                  │                                                    │
│  ┌──────┴───────┐   ┌──────┴───────┐                                            │
│  │ PESERTA      │   │ ORANG TUA /  │                                            │
│  │ DIDIK (Siswa)│   │ WALI MURID   │                                            │
│  └──────────────┘   └──────────────┘                                            │
└─────────────────────────────────────────────────────────────────────────────────┘
```

### 3.2 Hak Akses Per Aktor (RBAC & Dapodik Functional Duties)

| Aktor / Peran Resmi (`role`) | Tugas Tambahan (`additional_role`) | Hak Akses & Wewenang Sistem |
|---|---|---|
| **Super Administrator** (`super_admin`) | — | Akses penuh (*Full God Mode*), manajemen multi-tenant sekolah, direktori global pengguna lintas sekolah, dan konfigurasi server. |
| **Kepala Sekolah** (`principal`) | — | Pimpinan tertinggi satuan pendidikan, supervisi KBM guru, pemantauan rekap jurnal mengajar, evaluasi kinerja GTK, dan laporan eksekutif. |
| **Administrator Sekolah (TU)** (`school_admin`) | — | Pengelolaan data master sekolah, kenaikan kelas, direktori pengguna unit, jendela durasi pemutakhiran biodata siswa & GTK, dan administrasi operasional. |
| **Staf Tenaga Kependidikan** (`staff`) | — | Staf administrasi TU, perpustakaan, kearsipan persuratan, dan penunjang operasional sekolah. |
| **Guru / Tenaga Pendidik** (`teacher`) | *Reguler (Tanpa Tugas Tambahan)* | Pelaksanaan KBM, presensi kelas tatap muka, pengisian jurnal mengajar harian, serta input nilai siswa. |
| **Guru + Waka Kurikulum** (`teacher`) | `waka_kurikulum` (+12 JP) | Penyusunan kurikulum, alokasi jam belajar/KBM, matriks jadwal pelajaran (Guru/Kelas/Ruangan), verifikasi jurnal mengajar, dan kalender akademik. |
| **Guru + Waka Kesiswaan** (`teacher`) | `waka_kesiswaan` (+12 JP) | Tata tertib siswa, master kategori pasal & poin pelanggaran, seting ambang batas sanksi (SP1-SP3), pemutihan poin, ekstrakurikuler/OSIS, dan dispensasi. |
| **Guru + Waka Sarpras** (`teacher`) | `waka_sarpras` (+12 JP) | Inventarisasi aset sarpras, pengelolaan ketersediaan ruang kelas, laboratorium, bengkel kejuruan, dan fasilitas KBM. |
| **Guru + Waka Humas / Hubin** (`teacher`) | `waka_humas` (+12 JP) | Kemitraan industri (DUDI), koordinasi program magang/PKL siswa SMK, bursa kerja khusus (BKK), dan publikasi sekolah. |
| **Guru + Kepala Unit** (`teacher`) | `kepala_lab` / `kepala_perpus` (+12 JP) | Pengelolaan sarana laboratorium sains/komputer atau perpustakaan dan literasi sekolah. |
| **Guru + Pembina OSIS** (`teacher`) | `pembina_osis` (+6 JP) | Pembinaan kepengurusan OSIS/MPK dan kegiatan ekstrakurikuler siswa. |
| **Guru Bimbingan Konseling** (`counselor`) | — | Layanan konseling siswa (pribadi/sosial/belajar/karir), tindak lanjut rujukan akumulasi poin pelanggaran, panggilan orang tua, dan pembinaan kedisiplinan. |
| **Bendahara Sekolah / Keuangan** (`treasurer`) | — | Pengelolaan pos tarif tagihan SPP/DSP, pencatatan transaksi kasir multi-metode, rekonsiliasi Virtual Account, dan pembukuan kas sekolah. |
| **Peserta Didik (Siswa)** (`student`) | — | Presensi mandiri swafoto selfie GPS, jadwal pelajaran KBM personal, kartu pelajar digital, riwayat absensi 30 hari, pengisian profil Dapodik, dan saldo e-wallet. |
| **Orang Tua / Wali Murid** (`parent`) | — | Pemantauan kehadiran anak secara real-time, penerimaan tagihan pembayaran SPP (VA & QRIS), dan riwayat kedisiplinan/prestasi anak. |

### 3.3 Akun Pengujian Sistem (Demo Accounts)

Telah disediakan akun dummy resmi pada seeder database (`DummyUsersSeeder`) untuk memfasilitasi pengujian alur bisnis sesuai hierarki wewenang masing-masing peran. 

> [!NOTE]
> **Pembaruan Antarmuka Login**: Seluruh panel daftar akun demo dan tombol jalan pintas (*1-Click fast track*) pada halaman login utama (`/login`) telah dihilangkan agar halaman login tampil bersih (*clean production interface*), profesional, dan aman. Pengujian dilakukan dengan memasukkan email/NISN dan kata sandi di bawah ini secara manual.

| No | Peran (Role RBAC) | Nama Lengkap / Entitas | Kredensial Login (Email / NISN) | Kata Sandi | Tugas Fungsional & Modul Tujuan |
|---|---|---|---|---|---|
| 1 | **Super Admin** (`super_admin`) | Administrator Pusat | `superadmin@sekolahku.id` | `password` | Tata kelola tenant multi-sekolah & enrollment global. |
| 2 | **Kepala Sekolah** (`principal`) | Dr. H. Mulyadi, M.Pd. | `kepsek@sekolahku.sch.id` | `password` | Supervisi KBM, evaluasi GTK & laporan eksekutif sekolah. |
| 3 | **Admin Sekolah** (`school_admin`) | Administrator Sekolah (TU) | `admin@sekolahku.sch.id` | `password` | Pengelolaan data master, direktori pengguna, impor masal, dan jendela edit profil. |
| 4 | **Waka Kurikulum** (`teacher` + `waka_kurikulum`) | Dra. Hj. Nurhayati, M.Pd. | `waka.kurikulum@sekolahku.sch.id` | `password` | Master kurikulum, kelompok mapel, alokasi JP, dan manajemen jadwal pelajaran KBM. |
| 5 | **Waka Kesiswaan** (`teacher` + `waka_kesiswaan`) | Drs. Agus Setiawan, M.Pd. | `waka.kesiswaan@sekolahku.sch.id` | `password` | Tata tertib, master pelanggaran, ambang batas sanksi SP, dan pemutihan poin siswa. |
| 6 | **Waka Sarpras** (`teacher` + `waka_sarpras`) | Ir. H. Budi Santoso, M.T. | `waka.sarpras@sekolahku.sch.id` | `password` | Inventaris sarana prasarana, master ruangan kelas, laboratorium & bengkel. |
| 7 | **Waka Humas / Hubin** (`teacher` + `waka_humas`) | Siti Rahmawati, S.Pd., M.M. | `waka.humas@sekolahku.sch.id` | `password` | Kemitraan industri DUDI, program magang PKL, dan publikasi kehumasan. |
| 8 | **Guru BK / Konselor** (`counselor`) | Dra. Hj. Endang Purwanti, M.Psi. | `bk@sekolahku.sch.id` | `password` | Buku kredit poin kedisiplinan, riwayat konseling, dan pembinaan kesiswaan. |
| 9 | **Guru / Pendidik** (`teacher`) | Drs. Bambang Sudarsono, M.Pd. | `guru@sekolahku.sch.id` | `password` | Jadwal mengajar personal, jurnal KBM harian, dan absensi pertemuan kelas. |
| 10 | **Staf TU / Tendik** (`staff`) | Rudi Hermawan, S.Kom. | `tu@sekolahku.sch.id` | `password` | Administrasi persuratan, kearsipan sekolah, dan pelayanan umum tata usaha. |
| 11 | **Bendahara / Keuangan** (`treasurer`) | Hj. Ratna Sari, S.E. | `bendahara@sekolahku.sch.id` | `password` | Tagihan SPP/DSP, pencatatan kasir multi-metode, rekap VA/QRIS, dan buku kas. |
| 12 | **Orang Tua / Wali** (`parent`) | H. Hendra Wijaya | `ortu@sekolahku.sch.id` | `password` | Portal presensi anak, notifikasi absensi real-time, dan informasi tagihan SPP. |
| 13 | **Peserta Didik (Siswa 1)** (`student`) | Ahmad Fauzi (X-MIPA-1) | `siswa@sekolahku.sch.id`<br>atau **NISN:** `0081234561` | `password` | Dashboard siswa, jadwal KBM, absensi swafoto selfie GPS, kartu pelajar, profil & poin tatib. |
| 14 | **Peserta Didik (Siswa 2)** (`student`) | Siti Aisyah (X-MIPA-1) | `siti.aisyah@sekolahku.sch.id`<br>atau **NISN:** `0081234562` | `password` | Dashboard siswa, presensi GPS, jadwal pelajaran, dan riwayat presensi mandiri. |

> **Catatan Kata Sandi Akun Baru dari Impor Excel**:  
> - Siswa baru hasil impor masal Excel: default password `Siswa123!` (jika kolom kata sandi tidak diisi).
> - GTK baru hasil impor masal Excel: default password `Guru123!` (jika kolom kata sandi tidak diisi).

---

## 4. Arsitektur Sistem

### 4.1 Arsitektur High-Level: Full-Stack Modular Monolith (Laravel 13)

Sistem **Sekolahku** mengadopsi arsitektur **Full-Stack Modular Monolith** berbasis **Laravel 13 (v13.38 Terbaru)**, di mana antarmuka web (Frontend) dan logika bisnis/database (Backend) disatukan dalam satu repositori terintegrasi. Pendekatan ini dipilih karena memberikan efisiensi pengembangan maksimal, kemudahan pemeliharaan (tidak memerlukan sinkronisasi multi-server yang rumit), serta performa yang sangat kencang.

Pada saat yang sama, Laravel tetap menyediakan lapisan **REST API bawaan (Built-in API)** sehingga aplikasi mobile (Android/iOS untuk Orang Tua dan Siswa) dapat terhubung secara mulus ke backend yang sama tanpa perlu membangun sistem terpisah.

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                              CLIENT LAYER                                   │
│                                                                             │
│  ┌─────────────────────────────────────┐   ┌─────────────────────────────┐  │
│  │     Web Browser (Admin & Guru)      │   │ Mobile App / PWA (Ortu &    │  │
│  │   Responsive Vuexy Blade Interface  │   │  Siswa: Android & iOS)      │  │
│  └──────────────────┬──────────────────┘   └──────────────┬──────────────┘  │
└─────────────────────┼─────────────────────────────────────┼─────────────────┘
                      │ (HTTP/HTTPS Web Requests)           │ (JSON API)
                      ▼                                     ▼
┌─────────────────────────────────────────────────────────────────────────────┐
│              SEKOLAHKU ENGINE (LARAVEL 13 FULL-STACK MONOLITH)              │
│                                                                             │
│  ┌─────────────────────────────────────┐   ┌─────────────────────────────┐  │
│  │         WEB PRESENTATION            │   │      BUILT-IN REST API      │  │
│  │  - Laravel Blade Templating Engine  │   │  - JSON API Endpoints       │  │
│  │  - Vuexy Modern UI & Vanilla CSS    │   │  - Sanctum Auth / Token     │  │
│  │  - ApexCharts & Leaflet.js Geofence │   │  - Mobile App Gateway       │  │
│  └──────────────────┬──────────────────┘   └──────────────┬──────────────┘  │
│                     │                                     │                 │
│                     └──────────────────┬──────────────────┘                 │
│                                        ▼                                    │
│  ┌───────────────────────────────────────────────────────────────────────┐  │
│  │               MODULAR APPLICATION & DOMAIN SERVICES                   │  │
│  │  - Manajemen Pengguna & RBAC        - Kurikulum, Jam & Jadwal KBM     │  │
│  │  - Data Master Dapodik Terpadu      - Keuangan, SPP & Virtual Account │  │
│  │  - Presensi GPS Geofence & Selfie   - Notifikasi (WhatsApp / Push)    │  │
│  └─────────────────────────────────────┬─────────────────────────────────┘  │
└────────────────────────────────────────┼────────────────────────────────────┘
                                         │
                                         ▼
┌─────────────────────────────────────────────────────────────────────────────┐
│                           INFRASTRUCTURE & DATA                             │
│                                                                             │
│  ┌────────────────────────┐  ┌───────────────────────┐  ┌────────────────┐  │
│  │ Database Relasional    │  │ Cache & Queue Session │  │ File Storage   │  │
│  │ MySQL 8 / PostgreSQL   │  │ Redis / Database      │  │ Local / S3     │  │
│  └────────────────────────┘  └───────────────────────┘  └────────────────┘  │
└─────────────────────────────────────────────────────────────────────────────┘
```

### 4.2 Struktur Direktori Proyek (Modular Monolith)

Seluruh komponen sistem dikelola dalam satu proyek Laravel yang bersih dan terstruktur rapi:

```
sekolahku/
├── app/
│   ├── Http/Controllers/      # Web Controllers & API Controllers
│   │   ├── AdminDashboardController.php
│   │   ├── AdminDataMasterController.php
│   │   ├── AdminBulkImportController.php
│   │   ├── AttendancePortalController.php
│   │   └── Api/               # API Endpoint siap pakai untuk Mobile
│   ├── Models/                # Eloquent ORM (Student, Teacher, Attendance, dll)
│   └── Services/              # Logika Bisnis (Geofencing, Billing, Notifikasi)
│
├── database/                  # Migrasi & Seeder Data Master Dapodik
│   ├── migrations/
│   └── seeders/
│
├── resources/
│   └── views/                 # Frontend Tampilan (Laravel Blade)
│       ├── layouts/
│       │   └── vuexy.blade.php      # Master Theme Vuexy (Sidebar, Topbar)
│       ├── admin/
│       │   ├── dashboard.blade.php  # Dashboard Analitik
│       │   └── data-master.blade.php# Data Master Terpadu Dapodik
│       └── attendance/
│           └── portal.blade.php     # Portal Presensi Kamera Selfie & GPS
│
├── routes/
│   ├── web.php                # Rute Halaman Web (Admin, Guru, Siswa)
│   └── api.php                # Rute JSON API (Mobile App & Integrasi)
│
├── public/                    # Web Root Assets (CSS, JS, Icon, Gambar, Build Assets)
├── storage/                   # File uploads, sessions, compiled views & logs
└── artisan                    # Laravel Command-Line Tool
```

### 4.3 Pola Multi-Tenant Terpadu

Sistem mendukung **Multi-Tenant SaaS** (banyak sekolah dalam satu sistem):
1. **Row-Level Tenancy (Direkomendasikan & Diterapkan)**:
   - Setiap tabel memiliki kolom relasi `school_id` / `tenant_id`.
   - Menggunakan Laravel Global Scope sehingga data antar sekolah terisolasi otomatis secara ketat dan aman tanpa duplikasi beban database.
2. **Fleksibilitas Database**:
   - Berjalan lancar di **MySQL / MariaDB** (Laragon lokal & cPanel) maupun **PostgreSQL** untuk deployment skala enterprise.

---

## 5. Modul & Fitur

### 5.1 Peta Modul Sistem

```
SEKOLAHKU PLATFORM
│
├── 🏠 DASHBOARD
│   ├── Statistik real-time (siswa, guru, pembayaran)
│   ├── Widget analytics
│   └── Activity feed
│
├── 🗄️ MANAJEMEN DATA MASTER (Sidebar Sub-Menu Terpadu)
│   ├── 1. Master Satuan Pendidikan (Multi-Sekolah & Profil Standar Dapodik)
│   ├── 2. Master Periode Akademik (Tahun Ajaran & Semester)
│   ├── 3. Master Kurikulum Sekolah & Pemetaan Multi-Jenjang (Fase A-F & K-13)
│   ├── 4. Master Spektrum Keahlian SMK (Bidang, Program, Konsentrasi & DUDI)
│   ├── 5. Master Waktu & Jam Pelajaran (Multi-Kategori: Normal, Ramadhan, Ujian)
│   ├── 6. Master Guru & Tenaga Kependidikan (GTK Standar Dapodik / Guru Kelas SD)
│   ├── 7. Master Peserta Didik (Siswa Standar Dapodik Lengkap)
│   │   ├── Identitas Angkatan (Cohort via Tahun Ajaran Masuk) & Filter per Angkatan
│   │   ├── Riwayat Rombel per Tahun Ajaran & Kenaikan Kelas Massal (Bulk Class Promotion)
│   │   ├── Pengaturan Jendela Rentang Tanggal Edit Profil Siswa (Profile Edit Window)
│   │   ├── Deteksi Koordinat Rumah Otomatis dari Perangkat Siswa (Auto GPS)
│   │   └── Unggah Pas Foto Terbaru Peserta Didik Ukuran 4x6
│   ├── 8. Master Orang Tua / Wali (Multi-Anak dalam 1 Akun)
│   ├── 9. Master Rombel Kelas & Peminatan / Konsentrasi (Dinamis SD/SMP/SMA/SMK)
│   └── 10. Master Mata Pelajaran & Alokasi JP (Beban Belajar Mingguan)
│
├── 👥 MANAJEMEN PENGGUNA & PROFIL SISWA MANDIRI
│   ├── Portal Mandiri Edit Biodata Siswa (`/student/profile`)
│   │   ├── Verifikasi Jendela Waktu Aktif (Read-Only jika Terkunci)
│   │   ├── Auto-Detect Koordinat GPS Rumah + Peta Interaktif Leaflet
│   │   ├── Upload Pas Foto 4x6 Resmi Peserta Didik
│   │   └── Data Riwayat Sekolah Asal (Previous School)
│   ├── Data Siswa (PSB, mutasi, kelulusan)
│   ├── Data Guru & Staf
│   ├── Data Orang Tua / Wali
│   └── Hak Akses & Peran (RBAC)
│
├── 📚 AKADEMIK & JADWAL KBM
│   ├── Kurikulum & Mata Pelajaran (Dilengkapi Warna Identitas Mapel / `color`)
│   ├── 🗓️ Manajemen Jadwal Pelajaran KBM Multi-Matriks (`/admin/schedules`)
│   │   ├── Tampilan Matriks berdasarkan Kelas, Guru, dan Ruang Kelas / Lab
│   │   ├── Auto-Merge Baris Tabel (`rowspan`) untuk Mapel > 1 Jam Pelajaran Berurutan
│   │   ├── Pewarnaan Sel Tabel (`<td>`) Sesuai Warna Mata Pelajaran & Rata Tengah Penuh
│   │   ├── Kategori Jadwal Dinamis Bermasa Berlaku (Persiapan Jadwal Semester Depan)
│   │   ├── Fitur Salin / Duplikasi Jadwal dari Semester Lalu (`copy-semester`)
│   │   ├── Edit Rentang Jam Pelajaran pada Slot Jadwal & Edit Waktu Sesi Jam
│   │   └── Master Ruangan Kelas, Laboratorium & Bengkel (`school_rooms`)
│   ├── Input & Rekap Nilai
│   ├── E-Rapor (Rapor Digital)
│   └── Kalender Akademik
│
├── 🛡️ KESISWAAN & BK (Menu Terpisah per Tim di Sidebar)
│   ├── 1. Tata Tertib, Pelanggaran & Kredit Poin (`/admin/kesiswaan/tatib`)
│   │   ├── Master Kategori Pelanggaran (Ringan/Sedang/Berat) & Item Pasal beserta Besar Poin
│   │   ├── Seting Ambang Batas Sanksi Dinamis oleh Waka Kesiswaan (Pembinaan, SP1, SP2, SP3)
│   │   ├── Seting Kebijakan & Program Pemutihan / Pengurangan Poin oleh Waka Kesiswaan
│   │   └── Buku Saldo Kredit Poin Siswa (Poin Pelanggaran dikurangi Kredit Pemutihan)
│   ├── 2. Prestasi & Talenta Siswa (`/admin/kesiswaan/prestasi` - Tahap Pengembangan)
│   ├── 3. Ekstrakurikuler & Organisasi Siswa OSIS/MPK (`/admin/kesiswaan/ekskul` - Tahap Pengembangan)
│   ├── 4. Bimbingan Konseling (BK), Panggilan Ortu & Home Visit (`/admin/kesiswaan/bk` - Tahap Pengembangan)
│   ├── 5. Piket, Dispensasi Keluar & Radar Rawan Presensi (`/admin/kesiswaan/dispensasi` - Tahap Pengembangan)
│   └── 6. Kesejahteraan (Beasiswa/PIP/KIP) & Mutasi Siswa (`/admin/kesiswaan/kesejahteraan` - Tahap Pengembangan)
│
├── 📅 PRESENSI
│   ├── Absensi Harian (QR Code / RFID)
│   ├── 🤳 Presensi Selfie + Verifikasi Lokasi GPS (Aplikasi Siswa)
│   │   ├── Kartu Identitas Siswa Digital (Pas Foto 4x6, NIS, NISN, Rombel, Angkatan)
│   │   ├── Indikator Status Realtime Hari Ini (Belum Absen, Hadir Tepat Waktu, Terlambat, Sudah Pulang)
│   │   ├── Tombol Pintar Aksi Masuk & Pulang (Pulse Ring Animation)
│   │   ├── Verifikasi Radius Geofencing Sekolah & Anti-Mock GPS
│   │   ├── Proteksi Impersonasi / Anti-Joki (Strict Auth Binding)
│   │   └── Tab Mandiri: Riwayat Kehadiran Siswa (30 Hari Terakhir & 5 KPI Ringkasan)
│   ├── Absensi Ekstrakurikuler
│   ├── Rekap & Monitoring Guru / Wali Kelas
│   ├── Peta Sebaran Lokasi Presensi (Admin Live Map Pins)
│   ├── Konfigurasi Geofencing Multi-Titik Kampus
│   └── Notifikasi Absen ke Orang Tua
│
├── 💰 KEUANGAN
│   ├── Tagihan SPP & Iuran
│   ├── Pembayaran Via Virtual Account
│   ├── Pembayaran QRIS
│   ├── Kartu Iuran Digital
│   ├── Laporan Keuangan
│   └── Rekonsiliasi Bank
│
├── 💳 SAKU DIGITAL (E-Wallet)
│   ├── Top-Up Saldo
│   ├── Transaksi Kantin/Minimarket
│   ├── Riwayat Transaksi
│   └── Monitoring Belanja oleh Orang Tua
│
├── 🛒 POS (Point of Sale)
│   ├── POS Kantin
│   ├── POS Minimarket / Koperasi
│   ├── Manajemen Produk & Stok
│   └── Laporan Penjualan
│
├── 👔 LAUNDRY (Khusus Asrama - Pending / Phase 3)
│   ├── Input Order Laundry
│   ├── Tracking Status Cucian
│   ├── Notifikasi Selesai
│   └── Laporan Laundry
│
├── 📖 TAHFIDZ / HAFALAN (Khusus Ponpes - Pending / Phase 3)
│   ├── Input Progress Hafalan
│   ├── Target & Pencapaian Hafalan
│   ├── Laporan Hafalan per Siswa
│   └── Monitoring oleh Orang Tua
│
├── 📢 PUBLIKASI & KOMUNIKASI
│   ├── Pengumuman Sekolah
│   ├── Berita & Artikel
│   ├── Chat Guru-Orang Tua
│   └── Notifikasi Push / WhatsApp
│
├── 🏠 MANAJEMEN ASRAMA (Pending / Phase 3)
│   ├── Data Kamar & Gedung
│   ├── Penempatan Siswa
│   ├── Perizinan Keluar/Masuk
│   └── Monitoring Siswa Asrama
│
└── 📊 LAPORAN & ANALITIK
    ├── Dashboard Eksekutif
    ├── Laporan Keuangan (PDF/Excel)
    ├── Laporan Akademik
    ├── Laporan Presensi
    └── Export Data
```

### 5.2 Detail Modul Presensi Selfie + Lokasi

#### 5.2.1 Metode Presensi yang Didukung

| Metode | Platform | Keterangan |
|--------|----------|------------|
| **QR Code** | Mobile + Web | Scan QR yang digenerate sistem per sesi |
| **RFID** | Hardware | Tap kartu RFID di reader sekolah |
| **🤳 Selfie + GPS** | Mobile | Foto wajah + koordinat lokasi real-time |
| **Manual (Guru)** | Web + Mobile | Input langsung oleh guru/wali kelas |

#### 5.2.2 Alur Presensi Selfie + Lokasi

```
ALUR PRESENSI SELFIE + VERIFIKASI LOKASI:

┌────────────┐
│   SISWA    │
│ Buka App   │
└─────┬──────┘
      │
      ▼
┌─────────────────────────────┐
│ 1. APP MINTA IZIN KAMERA    │
│    & LOKASI (GPS)           │
└─────────┬───────────────────┘
          │
      ┌───▼────────────────────────────────────┐
      │ 2. AMBIL FOTO SELFIE (Kamera Depan)    │
      │    • Deteksi wajah (face detection)    │
      │    • Liveness check (anti-foto/video)  │
      └───┬────────────────────────────────────┘
          │
      ┌───▼────────────────────────────────────┐
      │ 3. AMBIL KOORDINAT GPS                 │
      │    • Latitude & Longitude              │
      │    • Akurasi GPS (meter)               │
      │    • Altitude (opsional)               │
      └───┬────────────────────────────────────┘
          │
      ┌───▼────────────────────────────────────┐
      │ 4. KIRIM KE SERVER                     │
      │    • Foto selfie (compressed)          │
      │    • Koordinat GPS                     │
      │    • Timestamp device                  │
      │    • Device ID & User Agent            │
      └───┬────────────────────────────────────┘
          │
      ┌───▼────────────────────────────────────┐
      │ 5. VALIDASI SERVER                     │
      │    ┌─ Cek radius geofencing sekolah ─┐ │
      │    │  (default: 100 meter radius)    │ │
      │    └────────────────────────────────┘ │
      │    ┌─ Cek jam presensi valid ────────┐ │
      │    │  (sesuai jadwal kelas/sesi)     │ │
      │    └────────────────────────────────┘ │
      │    ┌─ Cek duplikasi presensi ────────┐ │
      │    │  (1 siswa = 1 presensi/sesi)    │ │
      │    └────────────────────────────────┘ │
      └───┬────────────────────────────────────┘
          │
    ┌─────┴──────┐
    │            │
    ▼ VALID      ▼ TIDAK VALID
┌───────────┐  ┌────────────────────────┐
│ PRESENSI  │  │ TAMPILKAN ERROR:       │
│ BERHASIL  │  │ • Di luar radius lokasi│
│ TERCATAT  │  │ • Di luar jam presensi │
│           │  │ • Foto tidak valid     │
│ → Notif   │  │ • Sudah presensi       │
│   ke Ortu │  └────────────────────────┘
└───────────┘
```

#### 5.2.3 Konfigurasi Geofencing per Sekolah

```
SETTING GEOFENCING (Konfigurasi Admin):
┌──────────────────────────────────────────────┐
│  Titik Pusat  : Koordinat Lokasi Sekolah     │
│  Radius       : 50 - 500 meter (adjustable)  │
│  Akurasi GPS  : Minimum 50 meter             │
│  Fallback     : Wifi SSID sekolah (opsional) │
│  Jam Buka     : 06:00 - 08:00 (masuk pagi)   │
│  Jam Tutup    : 11:00 - 14:00 (pulang)       │
│  Toleransi    : ± 15 menit dari jadwal       │
└──────────────────────────────────────────────┘
```

#### 5.2.4 Fitur Anti-Kecurangan (Anti-Spoofing)

| Fitur | Teknik | Tujuan |
|-------|--------|--------|
| **Liveness Detection** | Deteksi kedip mata / gerakan muka | Mencegah presensi pakai foto orang lain |
| **GPS Spoofing Detection** | Validasi mock location Android | Mencegah manipulasi koordinat GPS |
| **Timestamp Server** | Gunakan waktu server, bukan device | Mencegah manipulasi waktu di device |
| **Device Fingerprint** | Hash kombinasi device ID + IP | Mencegah satu device dipakai banyak akun |
| **Foto Duplikat** | Hash perceptual foto (pHash) | Mencegah kirim foto yang sama berulang |
| **Radius Dinamis** | Admin bisa set radius per event | Fleksibel untuk kegiatan luar sekolah |

#### 5.2.5 Modernisasi Desain Antarmuka & Implementasi Portal Siswa (`/attendance`)

Halaman portal presensi swafoto & GPS di `/attendance` telah diselaraskan 100% dengan standar UI/UX modern Sekolahku (Admindek / Vuexy Theme System):

1. **Penyelarasan Desain Visual (Design Tokens & Typography)**:
   - Menggunakan tipografi resmi: `'Inter'` (teks umum), `'Plus Jakarta Sans'` (judul & kartu hero), dan `'JetBrains Mono'` (koordinat, jam, NIS).
   - Penggunaan Boxicons (`bx bxs-...`, `bx bx-...`) menggantikan emoji lama untuk konsistensi seluruh aplikasi.
   - Variabel warna dinamis: `--bento-primary: #4680ff`, `--vx-success: #10b981`, `--vx-warning: #f59e0b`, `--vx-danger: #ef4444`, `--vx-info: #0284c7`, serta integrasi preset warna lokal `admindek_preset`.
   - Dukungan penuh Dark Mode (`html.dark`) dengan tombol toggle instan di navbar topbar.
2. **Viewfinder Kamera Modern & Efek Laser Scanner**:
   - Frame kamera dengan rasio 4:3, border gelap elegan, badge status real-time (*Kamera Aktif / Mode Simulasi*).
   - Animasi garis laser scan siber (*cyber scanning beam*) yang halus dan panduan oval wajah (*facial oval guide*) responsif yang berubah hijau berpendar saat GPS berada dalam radius geofence.
3. **Kartu Identitas Siswa Digital**:
   - Menampilkan pas foto resmi 4x6, nama lengkap, NIS, NISN, Rombel/Kelas, dan tahun angkatan dengan gradient signature `--bento-primary-grad`.
   - Dilengkapi tautan langsung ke halaman *Profil & Biodata Siswa* untuk melengkapi data Dapodik.
4. **Pencegahan Joki & Proteksi Impersonasi**:
   - Dropdown pengujian pemilihan siswa dihilangkan total untuk akun siswa.
   - ID siswa dikunci (*strictly bound*) ke entitas siswa pemilik akun, sehingga siswa tidak dapat melakukan presensi atas nama orang lain.
5. **Indikator Status Realtime Hari Ini**:
   - **Belum Presensi**: Menampilkan status *"Belum Presensi Hari Ini"*, mengarahkan siswa untuk menatap kamera dan memverifikasi radius GPS.
   - **Sudah Masuk**: Menampilkan jam masuk (mis: `06:55 WIB`), status tepat waktu/terlambat, jarak meter ke titik kampus, dan tombol pratinjau swafoto bukti masuk.
   - **Sudah Pulang**: Menampilkan banner lengkap *"Kehadiran Hari Ini Lengkap"* beserta jam kepulangan siswa.
6. **Smart Action Buttons (Animasi Pulse Ring)**:
   - Sebelum absen masuk: Tombol `Ambil Foto & Presensi Masuk` berdenyut aktif; tombol pulang dinonaktifkan.
   - Setelah absen masuk: Tombol masuk menjadi centang hijau; tombol `Presensi Pulang Sekarang` aktif berdenyut.
   - Setelah pulang: Seluruh tombol terkunci dengan keterangan selesai.
7. **Tab Mandiri: "Riwayat Kehadiran Saya"**:
   - **5 Kartu KPI Statistik**: Total Rekaman Hari, Hadir Tepat Waktu, Terlambat, Sakit/Izin, Alpa, dan Persentase Tingkat Kehadiran (%).
   - **Tabel Riwayat 30 Hari Terakhir**: Rekap tanggal, jam masuk, jam pulang, status badge, metode (Selfie GPS / QR / Manual), jarak GPS, thumbnail bukti foto selfie, dan catatan dispensasi.
8. **Mode GPS Riil (Real Sensor Auto-Detection & Anti-Manipulasi)**:
   - **Pintasan Simulasi Dihilangkan untuk Siswa**: Siswa tidak dapat lagi memilih lokasi simulasi (seperti "Di Sekolah ~25m" atau "Mock GPS"), opsi ini hanya tersedia untuk akun pengujian Guru/Admin.
   - **Auto-Acquire Geolocation**: Sistem otomatis meminta izin lokasi dan membaca koordinat sensor GPS asli perangkat siswa (`navigator.geolocation.getCurrentPosition` dengan opsi `enableHighAccuracy: true`).
   - **Tombol Kalibrasi / Refresh GPS**: Disediakan tombol kalibrasi ulang untuk siswa jika akurasi GPS sempat bergeser.
   - **Validasi Enforce Geofence**: Tombol presensi tidak dapat dikirim apabila sinyal GPS belum terkunci atau jika jarak siswa melebihi radius sekolah.
9. **Isolasi Hak Akses Penuh**:
   - Tab administrasi guru (*Rekap Guru*, *Peta Sebaran Live*, *Konfigurasi Geofencing*) dan modal manual presensi disembunyikan sepenuhnya dari pandangan siswa.

### 5.3 Detail Modul Keuangan

```
ALUR PEMBAYARAN SPP:
┌─────────┐    ┌──────────────┐    ┌──────────────┐    ┌──────────────┐
│  Admin  │───▶│ Buat Tagihan │───▶│  Notifikasi  │───▶│  Orang Tua  │
│ Sekolah │    │   per siswa  │    │  ke Ortu     │    │  Terima VA  │
└─────────┘    └──────────────┘    └──────────────┘    └──────┬───────┘
                                                              │
                                                   ┌──────────▼───────┐
                                                   │  Transfer ke VA  │
                                                   │  (Bank/QRIS)     │
                                                   └──────────┬───────┘
                                                              │
┌─────────┐    ┌──────────────┐    ┌──────────────┐          │
│  Admin  │◀───│  Rekonsili-  │◀───│   Callback   │◀─────────┘
│ Lihat   │    │   asi Auto   │    │  Payment GW  │
│ Lunas   │    └──────────────┘    └──────────────┘
└─────────┘
```

### 5.4 Detail Manajemen Data Master

#### 5.4.1 Master Satuan Pendidikan & Multi-Sekolah (Standar Resmi Dapodik Kemendikbudristek)
Sistem **Sekolahku** dirancang berarsitektur **Multi-Tenancy** native, di mana satu instalasi sistem dapat menampung dan mengelola banyak sekolah sekaligus (Multi-Sekolah) di bawah naungan yayasan atau dinas pendidikan:

1. **Menu Pengelolaan Satuan Pendidikan**:
   - Tersedia menu khusus di Data Master: **"Satuan Pendidikan"** untuk mendaftarkan dan memantau instansi sekolah baru (`schools`).
   - Dilengkapi **School Switcher Dropdown** di topbar navbar atas, memungkinkan Super Admin dan Operator berpindah konteks sekolah yang sedang aktif dalam satu klik tanpa logout.
2. **Data Sekolah Mengacu Standar Resmi Formulir Dapodik Kemendikbudristek**:
   - **Identitas Pokok**: Nama Resmi Sekolah, Nomor Pokok Sekolah Nasional (NPSN - 8 Digit Unik), Bentuk Pendidikan (`level`: SD, SMP, SMA, SMK), Status Sekolah (Negeri / Swasta), Akreditasi (A, B, C, Belum Terakreditasi).
   - **Legalitas & Perizinan**: Nomor SK Pendirian, Tanggal SK Pendirian, Nomor SK Izin Operasional, Tanggal SK Izin Operasional, dan NPWP Satuan Pendidikan.
   - **Wilayah & Titik Spasial (GPS)**: Alamat Jalan Lengkap, RT/RW, Dusun/Lingkungan, Desa/Kelurahan, Kecamatan, Kabupaten/Kota, Provinsi, Kode Pos, serta Titik Koordinat Spasial (Latitude Lintang & Longitude Bujur) untuk radius geofencing presensi.
   - **Pimpinan & Kontak**: Nama Lengkap Kepala Sekolah beserta Gelar, NIP Kepala Sekolah (18 Digit), Nomor Telepon Sekolah, Nomor Fax, Email Resmi, dan Website Sekolah.
3. **Penyesuaian Menu & Modul Otomatis Berdasarkan Jenjang Sekolah**:
   Sistem membaca atribut `level` dari sekolah aktif dan secara otomatis merestrukturisasi navigasi sidebar, form rombel, dan mata pelajaran:
   - **🏫 SD (Sekolah Dasar)**:
     - Rentang Tingkat: Kelas 1 s/d 6 (Fase A: Kelas 1-2, Fase B: Kelas 3-4, Fase C: Kelas 5-6).
     - Struktur Pendidik: Mengedepankan peran **Guru Kelas** (tematik terpadu) didampingi Guru Mapel khusus (Agama, PJOK).
     - Penyesuaian Menu: Modul penjurusan/spektrum kejuruan disembunyikan total; label rombel menyesuaikan "Rombel Kelas (Fase A-C)".
   - **🏫 SMP (Sekolah Menengah Pertama)**:
     - Rentang Tingkat: Kelas 7 s/d 9 (Fase D).
     - Struktur Pendidik: Sistem Guru Mata Pelajaran mandiri per bidang studi umum nasional.
     - Penyesuaian Menu: Menu kejuruan disembunyikan; label rombel menyesuaikan "Rombel Kelas (Fase D)".
   - **🏫 SMA (Sekolah Menengah Atas)**:
     - Rentang Tingkat: Kelas 10 s/d 12 (Fase E untuk Kelas 10, Fase F untuk Kelas 11-12).
     - Struktur Akademik: Menggunakan Rombel Peminatan Akademik (MIPA, IPS, Bahasa, atau Pilihan Lintas Minat Kurikulum Merdeka).
     - Penyesuaian Menu: Spektrum keahlian SMK disembunyikan; label menu rombel menampilkan "Peminatan & Rombel".
   - **🏫 SMK (Sekolah Menengah Kejuruan)**:
     - Rentang Tingkat: Kelas 10 s/d 12 (atau 13 untuk program SMK 4 Tahun).
     - Penyesuaian Menu: Menu & sub-tab **Spektrum Keahlian SMK** aktif penuh (Bidang Keahlian, Program Keahlian Fase E, Konsentrasi Keahlian Fase F), keterhubungan Rombel dengan Konsentrasi Keahlian, serta pengelolaan mitra DUDI dan PKL industri.

#### 5.4.2 Master Waktu Belajar & Jam Pelajaran (Multi-Kategori)
Sistem mendukung struktur waktu belajar 2 level untuk fleksibilitas operasional nyata di sekolah:
1. **Level 1: Kategori Waktu Belajar (`time_schedule_categories`)**
   - Mendukung lebih dari 1 kategori jadwal waktu yang dapat dipilih atau berganti sesuai kondisi:
     - **Jam Reguler / Normal**: Jadwal standar KBM sehari-hari (misal: 1 JP = 40–45 menit).
     - **Jam Khusus Ramadhan**: Durasi jam pelajaran dipersingkat (misal: 1 JP = 30–35 menit, istirahat ringkas, pulang lebih awal jam 12:30).
     - **Jam Pekan Ujian (PAS/PTS/PAT)**: Blok waktu berbasis sesi ujian (misal: Sesi 1 = 07:30–09:00, Sesi 2 = 09:30–11:00).
     - **Jam Kegiatan Khusus / MPLS / Class Meeting**: Blok waktu fleksibel untuk kegiatan non-KBM rutin.
   - Dilengkapi opsi **tanggal berlaku otomatis** (`effective_start_date` s/d `effective_end_date`) maupun sakelar aktif manual oleh admin sekolah.
2. **Level 2: Rincian Jam Pelajaran (`lesson_periods`)**
   - Setiap kategori memiliki slot jam per hari (Senin s/d Jumat/Sabtu), mengakomodasi perbedaan jam hari Jumat (sebelum Jumatan) atau hari Senin (upacara bendera).
   - Membedakan sesi KBM aktif (Jam Ke-1, Ke-2, ...) dan sesi non-KBM / istirahat / sholat (`is_break = true`).
   - Terintegrasi langsung dengan **Jadwal Pelajaran** dan **Presensi Guru per Jam Pelajaran**.

#### 5.4.2 Master Periode Akademik & Tahun Ajaran
- Tahun ajaran berstatus tunggal yang aktif (`is_active = true`), misal: `2026/2027`.
- Dibagi menjadi Semester Ganjil dan Semester Genap beserta rentang tanggal aktifnya.
- Menjadi jangkar utama untuk riwayat kenaikan kelas, arsip rapor lama, dan penagihan SPP bulanan.

#### 5.4.3 Master Orang Tua / Wali (Multi-Anak dalam 1 Akun & Standar Dapodik)
- Menggunakan entitas `parents` yang berelasi `many-to-many` melalui tabel pivot `parent_student`.
- 1 Akun Orang Tua (ayah/ibu) dapat memantau beberapa anak sekaligus (misal: anak pertama di kelas X, anak kedua di kelas VII) dalam satu aplikasi tanpa perlu ganti akun.
- **Standar Data Dapodik**: Menyimpan profil lengkap ayah, ibu, dan wali meliputi NIK, nama lengkap sesuai KTP/Akta, tahun lahir, jenjang pendidikan terakhir, pekerjaan, rentang penghasilan bulanan, dan nomor kontak aktif.

#### 5.4.4 Master Guru & Tenaga Kependidikan (GTK Standar Dapodik)
- Entitas `teachers` terpisah yang berelasi `1-to-1` dengan `users` untuk membedakan akun login dengan profil kepegawaian.
- **Standar Data Dapodik**:
  - Identitas Resmi: NIP (18 digit), NUPTK (16 digit), NIK (16 digit), nama lengkap beserta gelar depan dan belakang.
  - Data Pribadi: Tempat & tanggal lahir, jenis kelamin, agama, nama ibu kandung (kunci verifikasi Dapodik), alamat lengkap (RT/RW, desa/kelurahan, kecamatan, kota, provinsi, kode pos).
  - Kepegawaian & Penugasan: Jenis PTK (Guru Kelas, Guru Mapel, Guru BK, Tenaga Administrasi/TU, Laboran, Pustakawan, Kepala Sekolah), Status Kepegawaian (PNS, CPNS, PPPK, GTY, GTT, Guru Honor Sekolah), SK Pengangkatan, TMT Pengangkatan, Lembaga Pengangkat.
  - Kualifikasi & Sertifikasi: Status sertifikasi pendidik, nomor sertifikat pendidik, jenjang pendidikan terakhir (S1/S2/S3), dan program studi/jurusan.
- **Impor Masal GTK & Staf Berbasis Excel/CSV (`/admin/import/teachers`)**:
  - Disediakan tombol aksi unduh template dinamis `.xlsx` dan dialog modal unggah berkas (`.xlsx`, `.xls`, `.csv`) di halaman Direktori Pengguna (`/admin/users`) serta Data Master GTK (`/admin/data-master#tab-teachers`).
  - **Struktur 14 Kolom Standar Impor GTK**:
    1. `NIP` (Nomor Induk Pegawai, opsional)
    2. `NIK` (16 digit nomor kependudukan, opsional)
    3. `Nama Lengkap*` (Wajib, nama resmi tanpa gelar)
    4. `Gelar Depan` (Contoh: Drs., Dra., Ir., Dr.)
    5. `Gelar Belakang` (Contoh: S.Pd., M.Pd., M.Kom.)
    6. `Jenis Kelamin (L/P)*` (Wajib, format L/P/Laki-laki/Perempuan)
    7. `Tempat Lahir` (Kota/kabupaten lahir)
    8. `Tanggal Lahir (YYYY-MM-DD)*` (Format tanggal standar ISO atau date cell Excel)
    9. `Agama` (Islam, Kristen, Katolik, Hindu, Buddha, Konghucu)
    10. `Jenis PTK / Peran*` (Wajib: Guru Mapel, Guru BK, Waka Kurikulum, Waka Kesiswaan, Waka Sarpras, Waka Humas, Staf TU, Bendahara)
    11. `Status Kepegawaian` (PNS, PPPK, GTY, GTT, Honorer, Tetap Yayasan)
    12. `No HP / WhatsApp` (Nomor seluler aktif untuk notifikasi sistem)
    13. `Email (Akun Login)*` (Wajib, sebagai identitas login utama pendidik/staf)
    14. `Password (Opsional)` (Jika dikosongkan, default kata sandi aman: `Guru123!`)
  - **Pemetaan Otomatis Tupoksi & Peran**:
    - Akun `users` dibuat dengan role bersesuaian (`teacher`, `counselor`, `staff`, `treasurer`).
    - Bila peran merupakan Waka (Kurikulum, Kesiswaan, Sarpras, Humas/Hubin), sistem secara cerdas menetapkan peran dasar sebagai `teacher` sekaligus menyematkan tugas tambahan fungsional pada tabel `teachers` (`additional_role`) dan penugasan hak akses dashboard waka yang relevan.
  - **Opsi Duplikasi**: Admin dapat memilih opsi *Lewati duplikat* jika email/NIP sudah terdaftar, atau *Perbarui data yang ada* untuk sinkronisasi profil berkala.

#### 5.4.5 Master Siswa / Peserta Didik (Standar Dapodik & Manajemen Angkatan)
- **Identitas Pokok & Dapodik**:
  - Identitas Resmi: NIS (lokal sekolah), NISN (10 digit nasional), NIK (16 digit), No KK, No Registrasi Akta Kelahiran.
  - Biodata: Nama lengkap, jenis kelamin, tempat & tanggal lahir, agama, kebutuhan khusus (ABK).
  - Alamat & Domisili: Alamat jalan, RT/RW, kelurahan/desa, kecamatan, kab/kota, provinsi, kode pos, koordinat GPS lintang & bujur rumah siswa (`home_latitude`, `home_longitude`), jenis tempat tinggal, moda transportasi ke sekolah.
  - Riwayat Pendaftaran: Jenis pendaftaran (`new` / `transfer`), sekolah asal sebelumnya (`previous_school`), tingkat pertama masuk (`entry_grade_level`).
  - Kontak & Keluarga: Nomor HP siswa (`student_phone`), kontak ortu (`parent_phone`), nama ortu (`parent_name`).
  - Data Kesejahteraan: Status & nomor penerima KPS/PKH, status & nomor penerima KIP/PIP, nama tertera di KIP.
  - Data Fisik & Periodik: Tinggi badan (cm), berat badan (kg), lingkar kepala (cm), jarak tempat tinggal ke sekolah (km), waktu tempuh (menit), jumlah saudara kandung, anak keberapa, golongan darah.

- **1. Identitas Angkatan (Cohort via Tahun Ajaran Masuk)**:
  - Setiap siswa terikat pada kolom `entry_academic_year_id` (Angkatan), merujuk pada tahun ajaran pertama kali siswa resmi diterima di sekolah (contoh: *Angkatan 2026/2027*).
  - Dilengkapi **Filter Dropdown per Angkatan** pada tabel Master Siswa untuk memudahkan penelusuran satu leting/angkatan tanpa terpengaruh mutasi rombel berjalan.

- **2. Riwayat Rombel Kelas & Kenaikan Kelas Massal (Bulk Promotion)**:
  - Sistem tidak menimpa kelas lama, melainkan mencatat rekam jejak historis penempatan rombel siswa di tabel `student_class_histories` per tahun ajaran.
  - **Modal Kenaikan Kelas Massal**:
    - Admin memilih tahun ajaran asal, kelas asal, tahun ajaran target, dan kelas tujuan.
    - Menampilkan daftar kandidat siswa lengkap dengan checkbox seleksi.
    - Tombol aksi massal: *Naik Kelas* (`promoted`), *Tinggal Kelas* (`retained`), atau *Lulus* (`graduated`).
    - Riwayat kelas sebelumnya tersimpan rapi dan dapat ditinjau kapan saja via modal *Riwayat Kelas Siswa*.

- **3. Pengaturan Jendela Rentang Waktu Edit Biodata Siswa (Profile Edit Window)**:
  - Admin Sekolah memiliki wewenang mengontrol kapan siswa diizinkan memperbarui data mandiri melalui pengaturan `student_profile_edit_enabled`, `student_profile_edit_start`, dan `student_profile_edit_end` pada profil sekolah.
  - Jika jendela dibuka: Siswa dapat melengkapi profil Dapodik, titik koordinat rumah, dan mengunggah pas foto.
  - Jika jendela ditutup / di luar rentang tanggal: Halaman profil siswa otomatis terkunci dalam mode *read-only* dengan banner notifikasi resmi.

- **4. Portal Biodata Mandiri Siswa (`/student/profile`)**:
  - Halaman terdedikasi bagi siswa yang sedang login.
  - **Auto-Detect GPS Perangkat Siswa**: Tombol *Ambil Titik Koordinat GPS Otomatis* yang memanfaatkan HTML5 Geolocation API perangkat smartphone/laptop siswa, memetakan koordinat ke Leaflet.js, dan menghitung estimasi jarak ke kampus sekolah otomatis.
  - **Unggah Pas Foto 4x6**: Form upload pas foto resmi ukuran standar 4x6 (disimpan di `storage/app/public/student_photos`) dengan rasio 3:4 yang langsung ditampilkan pada Kartu Pelajar Digital dan kartu presensi.

- **5. Impor Masal Peserta Didik & Akun Berbasis Excel/CSV (`/admin/import/students`)**:
  - Fasilitas entri data masal peserta didik saat tahun ajaran baru atau migrasi dari Dapodik/Emis ke dalam sistem Sekolahku.
  - **Autentikasi Multi-Identitas Siswa (Login via NISN)**:
    - Siswa tidak lagi diwajibkan memiliki alamat email pribadi untuk mengakses sistem.
    - Siswa dapat langsung login menggunakan **NISN (10 digit)** dan kata sandi di portal login (`/login`).
    - Sistem secara transparan menghasilkan email sistem berformat `{nisn}@siswa.sekolahku.sch.id` untuk menjamin integritas integritas kolom unik `users.email` tanpa membingungkan siswa atau orang tua.
  - **Struktur 14 Kolom Standar Impor Siswa**:
    1. `NISN*` (10 digit nomor induk siswa nasional, wajib sebagai kunci unik dan akun login)
    2. `NIS` (Nomor induk lokal sekolah, opsional)
    3. `Nama Lengkap*` (Wajib)
    4. `Jenis Kelamin (L/P)*` (Wajib, format L/P)
    5. `Tempat Lahir` (Kota/kabupaten lahir)
    6. `Tanggal Lahir (YYYY-MM-DD)*` (Wajib, format standar ISO atau date cell Excel)
    7. `Agama` (Islam, Kristen, Katolik, Hindu, Buddha, Konghucu)
    8. `Nama Kelas*` (Wajib, nama rombel aktif di sekolah, contoh: *X-MIPA-1*, *VII-A*, dsb.)
    9. `Tahun Angkatan` (Tahun ajaran masuk, contoh: *2026/2027*, jika kosong otomatis terikat ke tahun ajaran aktif)
    10. `NIK` (16 digit nomor kependudukan, opsional)
    11. `Nama Wali` (Nama ayah/ibu/wali murid)
    12. `No HP/WA Wali` (Kontak wali murid untuk integrasi notifikasi presensi/SPP)
    13. `Email Siswa (Opsional)` (Opsional, jika ada akan digunakan sebagai email akun)
    14. `Password (Opsional)` (Opsional, default sistem: `Siswa123!`)
  - **Cerdas Pemetaan Rombel & Riwayat Kelas**:
    - Kolom *Nama Kelas* dipetakan secara cerdas dan toleran (case-insensitive & whitespace-trimming) ke master rombel sekolah (`classes`).
    - Sistem otomatis mencatat siswa ke tabel riwayat kelas `student_class_histories` dengan status `enrolled` pada tahun ajaran aktif.
  - **Generator Template Excel Dinamis**:
    - File template `.xlsx` yang diunduh dari tombol *Download Template Siswa* menyertakan 2 lembar kerja:
      - *Sheet 1 (Data Siswa)*: 14 kolom terformat rapi dengan baris contoh dummy.
      - *Sheet 2 (Petunjuk & Daftar Kelas)*: Panduan pengisian lengkap beserta daftar tabel rombel kelas yang terdaftar aktif di sekolah untuk mencegah kesalahan ketik (*typo*).

#### 5.4.6 Master Kurikulum Sekolah & Pemetaan Multi-Jenjang
Sistem mengakomodasi fleksibilitas transisi kurikulum nasional di mana **satu sekolah dapat menerapkan lebih dari satu kurikulum secara bersamaan** untuk jenjang yang berbeda:
1. **Dukungan Multi-Jenjang Realistis**:
   - **Tingkat 10 (Kelas X)**: Menggunakan **Kurikulum Merdeka (Fase E)** dengan pembelajaran intrakurikuler dan Projek Penguatan Profil Pelajar Pancasila (P5).
   - **Tingkat 11 (Kelas XI)**: Menggunakan **Kurikulum Merdeka (Fase F)** dengan pemilihan mata pelajaran peminatan.
   - **Tingkat 12 (Kelas XII)**: Masih dapat menggunakan **Kurikulum 2013 Revisi (K-13)** untuk rombel tingkat akhir masa transisi.
   - **Kurikulum Khusus / Terpadu**: Dukungan kurikulum keagamaan (MA/Pesantren) atau kurikulum muatan khusus lainnya.
2. **Penentuan Struktur Mata Pelajaran & Beban Belajar**:
   - Kurikulum menjadi jangkar penentu daftar mata pelajaran yang diajarkan pada siswa di jenjang bersangkutan.
   - **Master Kelompok Mata Pelajaran Dinamis (`curriculum_subject_groups`)**:
     - Kelompok mata pelajaran melekat pada masing-masing kurikulum secara dinamis (*Add / Edit / Delete*).
     - Mengakomodasi variasi nomenklatur regulasi:
       - **Kurikulum Merdeka**: Kelompok Mapel Umum, Kelompok Mapel Pilihan / Kejuruan, Projek Profil Pelajar Pancasila (P5), Muatan Lokal (Mulok).
       - **Kurikulum 2013 (K-13)**: Kelompok A (Muatan Nasional), Kelompok B (Muatan Kewilayahan), Kelompok C (Peminatan), Muatan Lokal.
       - **Kemenag / Madrasah**: Kelompok PAI & Bahasa Arab, Kelompok Umum, Kelompok Peminatan, P5RA, Mulok.
       - **Pesantren / Salafiyah**: Kelompok Diniyah / Tarbiyah, Kelompok Tahfidz, Kelompok Diknas Umum, Bahasa Arab & Asing, Mulok.
     - Setiap kelompok mapel dilengkapi: `code`, `name`, `order_no` (urutan resmi di lembar rapor & dokumen KOSP), dan `color` (badge visual).
     - Disediakan tombol **Muat Template Cepat** (*Presets*) untuk instan setup.
   - Setiap relasi kurikulum-mapel (`curriculum_subjects`) mengatur:
     - **Kelompok Mapel**: Terhubung ke `curriculum_subject_group_id` milik kurikulum bersangkutan.
     - **Beban Jam Belajar**: Alokasi Jam Pelajaran (JP/Minggu) per mapel (misal: MTK 4 JP/minggu, Fisika 5 JP/minggu, P5 3 JP/minggu).
     - **Standar Ketuntasan**: Kriteria Ketercapaian Tujuan Pembelajaran (KKTP) atau Kriteria Ketuntasan Minimal (KKM).
3. **Pewarisan Otomatis ke Rombongan Belajar (Rombel)**:
   - Saat kurikulum dipetakan ke suatu tingkat jenjang melalui `curriculum_grade_levels`, seluruh kelas di tingkat tersebut (`classes.curriculum_id`) otomatis tersinkronisasi sehingga jadwal KBM dan struktur rapor langsung mengacu pada kurikulum yang tepat.

#### 5.4.7 Master Spektrum Keahlian SMK (Khusus Sekolah Menengah Kejuruan)
Sesuai regulasi Kemendikbudristek (Kepmendikbudristek No. 56/M/2022 jo. No. 262/M/2022 dan Perdirjen Dikdasmen), satuan pendidikan kejuruan (SMK) memiliki kekhususan struktur akademik berjenjang 3-Tier yang berbeda dengan SMA/SMP/SD:

1. **Tingkat 1: Bidang Keahlian (`vocational_fields`)**:
   - Merupakan rumpun disiplin vokasi makro nasional (terdapat 10 Bidang Keahlian Nasional Kemendikbudristek):
     - *Teknologi Informasi*, *Teknologi Manufaktur dan Rekayasa*, *Bisnis dan Manajemen*, *Pariwisata*, *Seni dan Ekonomi Kreatif*, *Kesehatan dan Pekerjaan Sosial*, *Energi dan Pertambangan*, *Agribisnis dan Agriteknologi*, *Kemaritiman*, dan *Konstruksi dan Properti*.
2. **Tingkat 2: Program Keahlian (`vocational_programs`)**:
   - Sub-bidang keahlian yang menjadi materi belajar di **Tingkat 10 (Fase E)** melalui mata pelajaran *Dasar-Dasar Program Keahlian*.
   - Mendukung **durasi program belajar fleksibel**:
     - **SMK 3 Tahun**: Program reguler dengan PKL 6 bulan di kelas XII.
     - **SMK 4 Tahun**: Program unggulan / Fast Track D2 dengan PKL/magang industri hingga 10–12 bulan.
   - Contoh: *Pengembangan Perangkat Lunak dan Gim (PPLG)*, *Teknik Jaringan Komputer dan Telekomunikasi (TJKT)*, *Akuntansi dan Keuangan Lembaga (AKL)*, *Teknik Otomotif (TO)*.
3. **Tingkat 3: Konsentrasi Keahlian (`vocational_concentrations`)**:
   - Spesialisasi keahlian mendalam yang ditempuh siswa di **Tingkat 11 & 12 (Fase F)**.
   - Memiliki **Kode Dapodik Resmi** (6 digit Kemendikbud) untuk sinkronisasi Dapodik Kejuruan.
   - Dilengkapi data **Mitra Dunia Usaha & Dunia Industri (DUDI)** untuk tempat Praktik Kerja Lapangan (PKL), program guru tamu industri, dan sertifikasi BNSP/LSP.
   - Menyimpan ringkasan profil kompetensi lulusan berbasis Standar Kompetensi Kerja Nasional Indonesia (SKKNI).
   - Contoh: *Rekayasa Perangkat Lunak (RPL)*, *Teknik Komputer dan Jaringan (TKJ)*, *Teknik Kendaraan Ringan (TKR)*, *Akuntansi*.
4. **Pengondisian Otomatis Satuan Pendidikan SMK**:
   - Jika profil sekolah disetel sebagai SMK (`schools.level = 'SMK'`), sistem otomatis:
     - Mengaktifkan menu dan tab navigasi **Spektrum Keahlian SMK** di Data Master.
     - Mengarahkan penetapan rombel/kelas ke Program Keahlian (Kelas X) dan Konsentrasi Keahlian (Kelas XI-XII).
     - Menyesuaikan struktur kurikulum dengan mata pelajaran kejuruan (Dasar Program, Konsentrasi, Projek Kreatif dan Kewirausahaan / PKK, dan PKL Industri).

#### 5.4.8 Catatan Penyesuaian Modul Pesantren / Boarding School
- Sesuai keputusan pengembangan, modul khusus **Pondok Pesantren / Boarding School** (seperti manajemen asrama `dormitories`, `dormitory_rooms`, dan modul pencatatan `tahfidz`) **dipending** terlebih dahulu untuk fase ekspansi berikutnya, sehingga fokus awal 100% pada sekolah formal terpadu sesuai standar regulasi Dapodik.

### 5.5 Detail Modul Jadwal Pelajaran KBM Dinamis Multi-Tampilan (`/admin/schedules`)

Modul penjadwalan KBM dirancang khusus untuk memfasilitasi kerja **Waka Kurikulum** dalam menyusun jadwal tiap kelas tiap hari secara bebas bentrok serta menyajikannya dalam 3 perspektif utama:

1. **Tiga Perspektif Matriks Jadwal Terpadu**:
   - **Jadwal berdasarkan Kelas (`#view-class`)**: Matriks penyusunan jadwal harian per rombel lengkap dengan indikator kuota JP mingguan per mapel.
   - **Jadwal berdasarkan Guru (`#view-teacher`)**: Menampilkan sebaran jam mengajar guru lintas rombel beserta total beban tatap muka (JP/minggu).
   - **Jadwal berdasarkan Ruang Kelas / Lab (`#view-room`)**: Menampilkan okupansi ruangan teori, laboratorium, bengkel SMK, atau GOR (`school_rooms`) dan slot ruangan yang masih tersedia.
2. **Validasi Anti-Bentrok 3 Lapis (Triple Conflict Detection)**:
   - Mencegah bentrok secara *real-time* apabila pada hari dan sesi jam yang sama terdapat: **(a)** Kelas yang sudah memiliki jadwal lain, **(b)** Guru yang sedang mengajar di kelas lain, atau **(c)** Ruangan/Lab yang sedang dipakai rombel lain.
3. **Kategori Jadwal Dinamis Bermasa Berlaku & Persiapan Semester Depan**:
   - Setiap kategori jadwal (`time_schedule_categories`) memiliki masa berlaku (`effective_start_date` s/d `effective_end_date`) serta keterkaitan ke `academic_year_id` dan `academic_semester`, sehingga Waka Kurikulum dapat menyiapkan draf jadwal semester yang akan datang tanpa mengganggu jadwal aktif berjalan.
   - Dilengkapi fitur **Salin Jadwal dari Semester Lalu (`POST /api/v1/schedules/copy-semester`)** dengan pencocokan otomatis slot jam pelajaran dan pencegahan konflik.
4. **Auto-Merge Sel (`rowspan`), Pewarnaan Mata Pelajaran & Rata Tengah Penuh**:
   - Apabila suatu mata pelajaran berlangsung lebih dari 1 jam pelajaran berturut-turut (dengan guru dan ruang yang sama), baris tabel otomatis ter-*merge* (`rowspan`) sehingga nama mapel tidak diulang di setiap jam.
   - Setiap mata pelajaran memiliki opsi **Warna Identitas (`subjects.color`)** yang diterapkan langsung sebagai latar belakang dan aksen sel tabel (`<td>`) tempat mapel tersebut dijadwalkan.
   - Konten sel menampilkan secara ringkas **Nama Mapel + Kode**, **Nama Guru** (tanpa awalan teks *"Guru:"*), dan **Pill Ruangan** yang seluruhnya ditata **rata tengah kanan–kiri dan atas–bawah**, serta dilengkapi fitur **Edit Rentang Jam** pada modal edit jadwal maupun edit waktu sesi jam pelajaran.

### 5.6 Detail Modul Administrasi KBM & Jurnal Mengajar Guru (`/admin/kurikulum/jurnal`)

Modul Administrasi KBM dirancang sebagai "jantung" pencatatan operasional pembelajaran harian di ruang kelas. Menggantikan buku jurnal kertas konvensional dengan sistem digital interaktif yang terintegrasi langsung dengan jadwal pelajaran, presensi siswa per jam tatap muka, dan modul supervisi akademik:

1. **Alur Pembukaan Sesi KBM Terpadu (`openSession`)**:
   - Guru membuka portal KBM hari ini; sistem secara otomatis mendeteksi jadwal pelajaran aktif guru pada hari tersebut (Hari, Jam Ke-, Kelas, Mapel, Ruang).
   - Tombol **"Buka Sesi KBM Sekarang"** otomatis membuat rekaman jurnal pertemuan baru (`teaching_journals`) dengan penomoran pertemuan berurutan (`meeting_number`).
   - **Sinkronisasi Otomatis Baseline Presensi**: Sistem menarik seluruh siswa aktif pada kelas/kelompok mengajar tersebut dan langsung menyelaraskan baseline kehadiran awal dari presensi pagi/gerbang (GPS Selfie). Jika siswa pagi tercatat sakit/izin, status otomatis terisi Sakit/Izin; siswa lainnya default Hadir (H). Guru cukup menandai siswa yang tidak berada di ruangan (H/S/I/A/Dispensasi).
2. **Pencatatan Materi & Aktivitas Pembelajaran Standar Kurikulum**:
   - **Tujuan Pembelajaran (TP) / Capaian Pembelajaran (CP) / Pokok Bahasan**: Uraian materi yang diajarkan pada pertemuan tersebut.
   - **Uraian Aktivitas KBM**: Penjelasan metode pembelajaran (diskusi kelompok, ceramah, praktikum lab, presentasi).
   - **Hambatan KBM & Tindak Lanjut Guru**: Evaluasi kendala belajar siswa atau fasilitas kelas serta solusi penanganannya.
   - **Tagihan Formatif / Tugas / PR**: Catatan tugas yang diberikan kepada siswa.
   - **Dukungan Guru Pengganti (Guru Piket)**: Fleksibilitas memilih guru pengganti jika guru utama berhalangan hadir.
3. **Penyelesaian & Penguncian Sesi KBM**:
   - Guru menekan tombol **"Selesai & Kunci Jurnal KBM"** saat jam pelajaran usai.
   - Sistem mencatat timestamp kepulangan/penyelesaian KBM (`completed_at`), mengunci sesi KBM (`status = completed`), dan merekap total kehadiran secara otomatis dalam format JSON (`attendance_summary`).
4. **Supervisi Akademik Waka Kurikulum & Kepala Sekolah**:
   - Waka Kurikulum memiliki akses verifikasi dan pemberian catatan supervisi (*feedback*) pada setiap jurnal KBM guru (`verified_by_waka_at` & `waka_supervision_notes`).
   - Terdapat **Radar KBM Live Hari Ini** yang menampilkan statistik real-time: total sesi terjadwal, sesi yang sedang berlangsung, sesi yang sudah selesai, dan sesi yang belum dimulai.
5. **Format Laporan Cetak Resmi: Jurnal Mengajar Guru (Buku Kerja Guru 3)**:
   - Akses via `/admin/kurikulum/jurnal-laporan` dengan filter berdasarkan Guru, Mapel, Kelas, dan Semester.
   - Format cetak standar dokumen kurikulum (*Print-Ready A4/Legal Landscape*) yang memuat kop surat sekolah, identitas guru, tabel agenda pertemuan berurutan 1 s.d. selesai, rekap kehadiran siswa (H/S/I/A), catatan hambatan, dan **Lembar Pengesahan Tanda Tangan** (Guru Pengampu, Waka Kurikulum, dan Mengetahui Kepala Sekolah).

### 5.7 Detail Modul Kesiswaan & BK Terpadu (Waka Kesiswaan & Tim)

Modul Kesiswaan dirancang untuk kolaborasi **Waka Kesiswaan** bersama timnya (**Tim Tata Tertib/STP2K**, **Guru BK**, **Pembina Ekstrakurikuler**, **Pembina OSIS/MPK**, **Guru Piket**, dan **Wali Kelas**) melalui **6 menu mandiri terpisah di Sidebar (`Kesiswaan & BK`)**:

1. **Menu 1 — Tata Tertib, Master Pelanggaran & Kredit Poin (`/admin/kesiswaan/tatib`) *(Telah Diimplementasikan)***:
   - **Master Kategori Pelanggaran (`discipline_categories`)**: Pengelompokan tata tertib berdasarkan tingkat keparahan (`RINGAN`, `SEDANG`, `BERAT`) dan warna label (misal: *Kerajinan & Kehadiran*, *Kerapian & Atribut*, *Sikap & Etika*, *Pelanggaran Berat*).
   - **Master Item Pasal Pelanggaran & Besar Poin (`discipline_items`)**: Daftar rincian item pelanggaran siswa di dalam setiap kategori lengkap dengan bobot poin (`+5` s/d `+100` Poin) dan tindakan pembinaan awal.
   - **Seting Ambang Batas Pelanggaran Dinamis (`discipline_thresholds`)**: Dikonfigurasi langsung oleh Waka Kesiswaan untuk menentukan rentang poin pemicu sanksi otomatis (contoh: `25–49 Poin` = Pembinaan Wali Kelas; `50–74 Poin` = `SP1` + Panggilan Ortu + Wajib Konseling BK; `75–99 Poin` = `SP2` + Pembinaan Khusus; `>= 100 Poin` = `SP3` / Konferensi Kasus Kepala Sekolah).
   - **Seting Kebijakan & Program Pemutihan Poin (`discipline_policies` & `discipline_amnesty_rules`)**: Waka Kesiswaan dapat mengatur periode reset poin (per semester / tahun ajaran / akumulasi), batas maksimal poin pemutihan per semester, minimal hari berturut-turut bebas pelanggaran baru sebelum pemutihan berlaku, larangan pemutihan untuk pelanggaran kategori `BERAT`, serta daftar program pembinaan/prestasi pengurang poin.
   - **Buku Saldo Kredit Poin Siswa (`student_discipline_records`)**: Menghitung secara *real-time* `Poin Bersih = max(0, Total Poin Pelanggaran - Total Kredit Pemutihan)` beserta status ambang sanksi aktif tiap siswa.
2. **Menu 2 — Prestasi & Talenta Siswa (`/admin/kesiswaan/prestasi`)**:
   - Pencatatan kejuaraan akademik & non-akademik (tingkat Sekolah s/d Internasional), peringkat, penyelenggara, guru pembimbing, sertifikat, dan integrasi kredit apresiasi poin.
3. **Menu 3 — Ekstrakurikuler & Organisasi Siswa OSIS/MPK (`/admin/kesiswaan/ekskul`)**:
   - Manajemen ekskul wajib & pilihan, guru pembina, jadwal latihan, penilaian ekskul rapor, serta struktur kepengurusan dan program kerja OSIS/MPK.
4. **Menu 4 — Bimbingan Konseling / BK (`/admin/kesiswaan/bk`)**:
   - Layanan konseling individu/kelompok/karir, tindak lanjut rujukan siswa yang melewati ambang poin Tatib, panggilan orang tua, dan kunjungan rumah (*Home Visit* terhubung GPS rumah siswa).
5. **Menu 5 — Piket, Dispensasi & Radar Siswa Rawan (`/admin/kesiswaan/dispensasi`)**:
   - Surat izin keluar sekolah / pulang cepat / dispensasi lomba & OSIS, serta deteksi dini (*Early Warning*) siswa sering terlambat atau Alpha berturut-turut.
6. **Menu 6 — Beasiswa (PIP/KIP) & Mutasi Siswa (`/admin/kesiswaan/kesejahteraan`)**:
   - Monitoring penerima bantuan pendidikan (PIP/KIP/KPS/Beasiswa) serta administrasi mutasi masuk, pindah keluar, dan kelulusan/alumni.

### 5.8 Detail Modul Manajemen Pengguna, Hak Akses (RBAC) & Enrollment Satuan Pendidikan (`/admin/users`)

Modul Manajemen Pengguna dirancang untuk tata kelola identitas akun, delegasi tugas tambahan, dan otorisasi terpusat pada platform Sekolahku:

1. **Klasifikasi Peran Inti & Tugas Tambahan GTK (Role-Based Access Control)**:
   - **Pimpinan & Administrasi**:
     - `super_admin`: Administrator sistem pusat lintas sekolah dan konfigurasi multi-tenant global.
     - `principal`: Kepala Sekolah (pengawas supervisi eksekutif, evaluasi KBM & mutu satdik).
     - `school_admin`: Administrator Tata Usaha sekolah setempat (pengelola data master & perizinan).
     - `staff`: Staf Tenaga Kependidikan / Tendik (administrasi persuratan, arsip, perpustakaan, laboran).
   - **Pendidik & GTK**:
     - `teacher`: Guru / Tenaga Pendidik resmi (pelaksanaan KBM tatap muka, jurnal mengajar, dan presensi).
     - `counselor`: Guru Bimbingan Konseling / BK (layanan bimbingan, konseling psikologis, tindak lanjut pelanggaran tata tertib, dan pembinaan karakter siswa).
   - **Tugas Tambahan Fungsional GTK (`additional_role`)**:
     Selaras dengan regulasi Kemendikbudristek dan sistem Dapodik, Wakil Kepala Sekolah (Waka) hakikatnya adalah Guru (`teacher`) yang mengemban tugas tambahan formal, bukan entitas akun terpisah:
     - `waka_kurikulum` (+12 JP): Waka Kurikulum & Akademik (penyusunan kurikulum, alokasi jam & jadwal pelajaran).
     - `waka_kesiswaan` (+12 JP): Waka Kesiswaan & Kedisiplinan (tata tertib, poin pelanggaran, rombel, ekskul/OSIS).
     - `waka_sarpras` (+12 JP): Waka Sarana & Prasarana (fasilitas belajar, laboratorium, dan ketersediaan ruang KBM).
     - `waka_humas` (+12 JP): Waka Hubungan Industri & Humas (kemitraan DUDI, magang/PKL SMK, dan kehumasan).
     - `kepala_lab` (+12 JP): Kepala Laboratorium / Bengkel Kejuruan.
     - `kepala_perpus` (+12 JP): Kepala Perpustakaan Satuan Pendidikan.
     - `pembina_osis` (+6 JP): Pembina OSIS & Ekstrakurikuler.
   - **Keuangan & Bendahara**:
     - `treasurer`: Bendahara Sekolah / Keuangan (penerimaan SPP, transaksi kasir pembayaran, dan pembukuan kas).
   - **Komunitas Sekolah**:
     - `student`: Peserta Didik (Siswa aktif).
     - `parent`: Orang Tua / Wali Murid.

2. **Tata Kelola Akun & Penugasan Tugas Tambahan (CRUD)**:
   - **Tambah Pengguna Baru (`/admin/users/create`)**: Registrasi akun pengguna baru dengan input nama lengkap, email unik, nomor telepon, peran utama (`role`), penetapan satuan pendidikan, kata sandi, dan status aktif langsung. Apabila peran dipilih adalah `teacher`, formulir secara dinamis memunculkan seleksi Tugas Tambahan GTK resmi.
   - **Ubah Biodata Pengguna (`/admin/users/{id}/edit` & Modal Popup Cepat)**: Memperbarui identitas pengguna, penggantian peran, penugasan tugas tambahan Waka, pemindahan unit sekolah (Super Admin), dan opsi reset kata sandi.
   - **Antarmuka Bersih Bebas Distorsi Layout**: Aksi pada baris tabel disederhanakan menggunakan *icon button group* elegan (Edit via Popup Modal, Enrol via Popup Modal, Toggle Status Kunci/Buka, dan Hapus) tanpa tombol *dropdown menu* yang dapat merusak tinggi baris tabel (*anti-layout-shift*).
   - **Pencegahan Teks Redundan**: Tampilan peran pada tabel menyajikan badge bersih terstruktur (contoh: badge `Guru / Pendidik` + badge `Waka Kurikulum & Akademik`) tanpa pengulangan nama jabatan.
   - **Aktivasi & Penonaktifan Cepat (Enable/Disable)**: Toggle status `is_active` instan dengan proteksi sistem: pengguna dicegah menonaktifkan akun sendiri yang sedang aktif. Akun nonaktif dicegat secara mutlak saat login via middleware.
   - **Penghapusan Aman**: Konfirmasi keamanan ganda dan proteksi mutlak terhadap *self-deletion*.

3. **Matriks Penugasan Satuan Pendidikan Multi-Tenant (`/admin/users/enrollments`)**:
   - Menu tersendiri untuk mengelola penugasan (*enrollment*) seluruh pengguna ke satuan pendidikan resmi.
   - Dilengkapi kartu KPI jumlah akun ter-enrol resmi vs. akun belum terdaftar (*unassigned*).
   - *Super Admin* berwenang mendaftarkan atau melepaskan penugasan sekolah ke unit mana pun secara fleksibel.
   - *Admin Sekolah* memiliki proteksi *tenant-scoped* untuk mendaftarkan akun ke sekolahnya sendiri.

### 5.9 Detail Modul Profil & Biodata GTK Dapodik (`/gtk/profile`)

Modul ini mengadopsi standar instrumen resmi Data Pokok Pendidikan (Dapodik) Kemendikbudristek untuk seluruh Guru dan Tenaga Kependidikan:

1. **Pas Foto Resmi Formal 4x6**:
   - Tampilan frame foto formal dengan rasio presisi 4:6 (`140 x 210 px`).
   - Fitur unggah file foto resmi (`jpg`, `jpeg`, `png`, `webp`, maks 2MB) dengan *instant preview* langsung di peramban.
   - Disimpan pada *secure public disk* (`storage/app/public/teachers/photos`).

2. **Struktur 5 Tab Rincian Profil Dapodik**:
   - **Tab 1: Biodata Pribadi & Kontak**: Gelar depan/belakang, nama lengkap KTP/Akta, NIK (16 digit), jenis kelamin, tempat & tanggal lahir, nama ibu kandung (kunci validasi verifikasi Dapodik), agama, status perkawinan, nama & pekerjaan pasangan, alamat domisili lengkap (RT/RW, kelurahan, kecamatan, kota/kabupaten, provinsi, kode pos), nomor WhatsApp/HP, dan email resmi.
   - **Tab 2: Kepegawaian & Penugasan**: Status kepegawaian (PNS, CPNS, PPPK, GTY, GTT, Honor Sekolah), NIP (18 digit), NUPTK (16 digit), jenis PTK fungsional (Guru Mapel, Guru Kelas, Guru BK, TAS, Pustakawan, Laboran, Kepsek), lembaga pengangkat, nomor & tanggal SK pengangkatan, TMT pengangkatan, sumber gaji (APBN, APBD Provinsi, APBD Kab/Kota, Yayasan, BOS), serta status Satuan Pendidikan Induk (Satminkal).
   - **Tab 3: Pendidikan & Sertifikasi**: Jenjang pendidikan terakhir (D1 s/d S3), program studi/jurusan, nama perguruan tinggi, tahun kelulusan, status kepemilikan sertifikat pendidik, nomor sertifikat pendidik, Nomor Registrasi Guru (NRG), dan bidang studi sertifikasi.
   - **Tab 4: Tugas Tambahan & Beban Kerja (JJM)**: Jabatan tugas tambahan resmi (Waka Kurikulum, Waka Kesiswaan, Waka Sarpras, Waka Humas/Hubin, Kepala Lab, Kepala Perpus, Pembina Osis, Wali Kelas), total ekuivalensi jam tatap muka (JJM, min. 24 jam/minggu), serta integrasi matriks jadwal pelajaran KBM aktif yang diampu guru.
   - **Tab 5: Rekening Bank & NPWP**: NPWP resmi, nama bank penyalur gaji/honor/TPG (BNI, BRI, Mandiri, Bank BPD Daerah), nomor rekening buku tabungan, dan nama pemilik rekening.

3. **Pengaturan Jadwal Durasi Pemutakhiran Mandiri (Window Period)**:
   - Admin Sekolah memiliki kendali penuh (`/admin/gtk/profile/window`) untuk menentukan:
     - Toggle aktivasi pembukaan edit mandiri (`gtk_profile_edit_enabled`).
     - Tanggal mulai buka pemutakhiran (`gtk_profile_edit_start`).
     - Tanggal batas akhir penutupan (`gtk_profile_edit_end`).
   - Saat periode aktif (`now()` berada dalam rentang jadwal atau toggle ON), GTK dapat memperbarui profil dan pas foto mandiri.
   - Saat periode ditutup/kadaluarsa, formulir terkunci secara aman dalam mode baca (*read-only*) dengan notifikasi informasi bahwa pemutakhiran mandiri ditutup oleh Admin.
   - Administrator Sekolah tetap dapat melakukan penyesuaian data kapan saja (*admin override*).

---

## 6. Desain Database

### 6.1 Entity Relationship Diagram (ERD) - Konseptual

```
VOCATIONAL_FIELDS ──< VOCATIONAL_PROGRAMS ──< VOCATIONAL_CONCENTRATIONS
      ▲                        ▲                           ▲
      │                        │ (Fase E: Kelas 10)        │ (Fase F: Kelas 11-12)
      └────────────────────────┼───────────────────────────┤
                               │                           │
ACADEMIC_YEARS ──< ACADEMIC_SEMESTERS                      │
      │                                                    │
TIME_SCHEDULE_CATEGORIES ──< LESSON_PERIODS                │
      │                                                    │
CURRICULUMS ──< CURRICULUM_GRADE_LEVELS (Multi-Jenjang)    │
    │     ├──< CURRICULUM_SUBJECT_GROUPS (Kelompok Mapel)  │
    │     │             ▲                                  │
    │     └──< CURRICULUM_SUBJECTS >── SUBJECTS            │
    │                   ▲                                  │
    ▼                   │                                  │
SCHOOLS ──< DEPARTMENTS ──< CLASSES ───────────────────────┘
   │                           │           │
   │                           │           ├──< ATTENDANCES ──< ATTENDANCE_SELFIES
   │                           │           ├──< PAYMENTS / FEES
   │                           │           ├──< GRADES
   │                           │           ├──< WALLET_TRANSACTIONS
   │                           │           └──< TAHFIDZ_RECORDS
   │                           │
   ├──< TEACHERS ──────────────┼───────────> SCHEDULES (Jadwal Pelajaran KBM)
   │                           │                ▲
   │                           └────────────────┤
   │                                            │
   └──< SUBJECTS (Mata Pelajaran) ──────────────┘
```

### 6.2 Tabel Utama

#### Tabel `schools` (Master Tenant)
```sql
CREATE TABLE schools (
    id            UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    tenant_id     VARCHAR(50) UNIQUE NOT NULL,
    name          VARCHAR(255) NOT NULL,
    npsn          VARCHAR(20),           -- Nomor Pokok Sekolah Nasional
    address       TEXT,
    phone         VARCHAR(20),
    email         VARCHAR(100),
    logo_url      VARCHAR(500),
    level         ENUM('SD','SMP','SMA','SMK','PONPES'),
    subscription  ENUM('starter','professional','whitelabel'),
    is_active     BOOLEAN DEFAULT true,
    -- Jendela Waktu Edit Profil Siswa Mandiri
    student_profile_edit_enabled BOOLEAN DEFAULT true,
    student_profile_edit_start   DATE,
    student_profile_edit_end     DATE,
    -- Jendela Waktu Edit Profil Guru & Tenaga Kependidikan (GTK) Mandiri
    gtk_profile_edit_enabled     BOOLEAN DEFAULT true,
    gtk_profile_edit_start       DATE,
    gtk_profile_edit_end         DATE,
    created_at    TIMESTAMP DEFAULT NOW(),
    updated_at    TIMESTAMP DEFAULT NOW()
);
```

#### Tabel `users` (Master Akun Pengguna, RBAC & Multi-Tenant Enrollment)
```sql
CREATE TABLE users (
    id                  BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    school_id           BIGINT UNSIGNED REFERENCES schools(id) ON DELETE SET NULL, -- NULL jika Super Admin / Belum Terdaftar
    name                VARCHAR(255) NOT NULL,
    email               VARCHAR(255) UNIQUE NOT NULL,
    password            VARCHAR(255) NOT NULL,
    role                ENUM('super_admin', 'principal', 'school_admin', 'staff', 'teacher', 'counselor', 'treasurer', 'student', 'parent') NOT NULL DEFAULT 'student',
    additional_role     VARCHAR(50) NULL,             -- Tugas Tambahan GTK: waka_kurikulum, waka_kesiswaan, waka_sarpras, waka_humas, kepala_lab, kepala_perpus, pembina_osis
    phone               VARCHAR(30) NULL,
    avatar_url          VARCHAR(500) NULL,
    is_active           BOOLEAN DEFAULT true,         -- Status akses login sistem
    email_verified_at   TIMESTAMP NULL,
    remember_token      VARCHAR(100) NULL,
    created_at          TIMESTAMP DEFAULT NOW(),
    updated_at          TIMESTAMP DEFAULT NOW()
);
```

#### Tabel `academic_years` (Master Tahun Ajaran)
```sql
CREATE TABLE academic_years (
    id            UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    school_id     UUID REFERENCES schools(id) ON DELETE CASCADE,
    name          VARCHAR(20) NOT NULL,          -- Contoh: "2026/2027"
    start_date    DATE NOT NULL,
    end_date      DATE NOT NULL,
    is_active     BOOLEAN DEFAULT false,         -- Hanya 1 yang bernilai true per sekolah
    created_at    TIMESTAMP DEFAULT NOW(),
    updated_at    TIMESTAMP DEFAULT NOW()
);
```

#### Tabel `academic_semesters` (Master Semester)
```sql
CREATE TABLE academic_semesters (
    id                UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    academic_year_id  UUID REFERENCES academic_years(id) ON DELETE CASCADE,
    semester          ENUM('odd', 'even') NOT NULL,  -- Ganjil / Genap
    start_date        DATE NOT NULL,
    end_date          DATE NOT NULL,
    is_active         BOOLEAN DEFAULT false,
    created_at        TIMESTAMP DEFAULT NOW(),
    updated_at        TIMESTAMP DEFAULT NOW()
);
```

#### Tabel `time_schedule_categories` (Master Kategori Waktu Belajar)
```sql
CREATE TABLE time_schedule_categories (
    id                    UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    school_id             UUID REFERENCES schools(id) ON DELETE CASCADE,
    name                  VARCHAR(100) NOT NULL,        -- Mis: "Jam KBM Normal", "Jam Khusus Ramadhan", "Jam Pekan Ujian"
    code                  VARCHAR(30) NOT NULL,         -- 'NORMAL', 'RAMADHAN', 'EXAM'
    description           TEXT,                         -- Mis: "Durasi 1 JP = 35 menit selama bulan Ramadhan"
    is_default            BOOLEAN DEFAULT false,        -- Kategori harian default
    is_active             BOOLEAN DEFAULT true,         -- Status dapat dipilih
    effective_start_date  DATE,                         -- Tanggal mulai berlaku otomatis (opsional)
    effective_end_date    DATE,                         -- Tanggal akhir berlaku otomatis (opsional)
    created_at            TIMESTAMP DEFAULT NOW(),
    updated_at            TIMESTAMP DEFAULT NOW()
);
```

> **Fitur Manajemen Dinamis Kurikulum:**
> - Bagian Kurikulum dapat mengelola (*Add / Edit / Delete*) kategori jam belajar secara dinamis sesuai kebutuhan operasional (contoh: *Jam KBM Normal*, *Jam Khusus Ramadhan*, *Pekan Ujian PAS/PAT*, *Jam PTM Terbatas*, dll.).
> - **Proteksi Integritas:** Kategori yang sedang berstatus `is_default = true` (jadwal utama aktif) dilindungi dan tidak dapat dihapus. Pengguna harus mengaktifkan kategori pengganti terlebih dahulu sebelum menghapus kategori lama.
> - **Cascade Deletion:** Menghapus kategori non-aktif akan secara otomatis membersihkan seluruh sesi jam pelajaran (`lesson_periods`) terkait.

#### Tabel `lesson_periods` (Master Jam Pelajaran per Kategori)
```sql
CREATE TABLE lesson_periods (
    id              UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    category_id     UUID REFERENCES time_schedule_categories(id) ON DELETE CASCADE,
    school_id       UUID REFERENCES schools(id) ON DELETE CASCADE,
    day_of_week     SMALLINT NOT NULL,            -- 1: Senin, 2: Selasa, ..., 5: Jumat, 6: Sabtu
    name            VARCHAR(50) NOT NULL,         -- "Jam Ke-1", "Istirahat 1", "Upacara", "Sholat Dhuhur"
    period_number   INT,                          -- 1, 2, 3... (NULL jika waktu istirahat/ibadah)
    start_time      TIME NOT NULL,                -- 07:00:00
    end_time        TIME NOT NULL,                -- 07:45:00
    is_break        BOOLEAN DEFAULT false,        -- true jika istirahat/upacara/ibadah
    created_at      TIMESTAMP DEFAULT NOW(),
    updated_at      TIMESTAMP DEFAULT NOW()
);
```

#### Tabel `departments` (Master Jurusan / Peminatan)
```sql
CREATE TABLE departments (
    id            UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    school_id     UUID REFERENCES schools(id) ON DELETE CASCADE,
    name          VARCHAR(100) NOT NULL,        -- Misal: "Teknik Komputer & Jaringan", "MIPA", "IPS"
    code          VARCHAR(20) NOT NULL,         -- TKJ, MIPA, IPS
    is_active     BOOLEAN DEFAULT true,
    created_at    TIMESTAMP DEFAULT NOW()
);
```

#### Tabel `classes` (Master Kelas & Rombel)
```sql
CREATE TABLE classes (
    id                  UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    school_id           UUID REFERENCES schools(id) ON DELETE CASCADE,
    department_id       UUID REFERENCES departments(id) ON DELETE SET NULL,
    curriculum_id       BIGINT UNSIGNED REFERENCES curriculums(id) ON DELETE SET NULL, -- Kurikulum yang berlaku untuk rombel ini
    academic_year_id    UUID REFERENCES academic_years(id) ON DELETE CASCADE,
    name                VARCHAR(50) NOT NULL,         -- "VII-A", "X MIPA 1"
    grade_level         INT NOT NULL,                 -- 1-12
    homeroom_teacher_id UUID REFERENCES teachers(id) ON DELETE SET NULL,
    is_active           BOOLEAN DEFAULT true,
    created_at          TIMESTAMP DEFAULT NOW(),
    updated_at          TIMESTAMP DEFAULT NOW()
);
```

#### Tabel `curriculums` (Master Kurikulum Sekolah)
```sql
CREATE TABLE curriculums (
    id            BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    school_id     BIGINT UNSIGNED REFERENCES schools(id) ON DELETE CASCADE,
    code          VARCHAR(30) NOT NULL,          -- KUR-MERDEKA, K13-REV, KUR-PESANTREN
    name          VARCHAR(150) NOT NULL,         -- Kurikulum Merdeka (Nasional), Kurikulum 2013 Revisi
    target_level  VARCHAR(50) DEFAULT 'SMA/SMK', -- SD, SMP, SMA, SMK, MA, PONPES
    description   TEXT NULL,
    is_active     BOOLEAN DEFAULT true,
    created_at    TIMESTAMP DEFAULT NOW(),
    updated_at    TIMESTAMP DEFAULT NOW(),
    UNIQUE(school_id, code)
);
```

#### Tabel `curriculum_grade_levels` (Pemetaan Kurikulum per Jenjang / Tingkat Multi-Jenjang)
```sql
CREATE TABLE curriculum_grade_levels (
    id            BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    school_id     BIGINT UNSIGNED REFERENCES schools(id) ON DELETE CASCADE,
    curriculum_id BIGINT UNSIGNED REFERENCES curriculums(id) ON DELETE CASCADE,
    grade_level   INT NOT NULL,                  -- 1-12 (misal: 10, 11, 12)
    phase         VARCHAR(20) NULL,              -- Fase A, B, C, D, E, F (Kurikulum Merdeka)
    created_at    TIMESTAMP DEFAULT NOW(),
    updated_at    TIMESTAMP DEFAULT NOW(),
    UNIQUE(school_id, grade_level)
);
```

#### Tabel `curriculum_subject_groups` (Master Kelompok Mata Pelajaran Dinamis per Kurikulum)
```sql
CREATE TABLE curriculum_subject_groups (
    id            BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    school_id     BIGINT UNSIGNED REFERENCES schools(id) ON DELETE CASCADE,
    curriculum_id BIGINT UNSIGNED REFERENCES curriculums(id) ON DELETE CASCADE,
    code          VARCHAR(30) NOT NULL,          -- UMUM, PILIHAN, P5, MULOK, KEL_A, DINIYAH
    name          VARCHAR(150) NOT NULL,         -- Kelompok Mata Pelajaran Umum (Nasional)
    description   TEXT NULL,
    order_no      INT DEFAULT 1,                 -- Urutan resmi pada cetak buku rapor & KOSP
    color         VARCHAR(30) DEFAULT 'badge-primary', -- Warna visual identitas badge kelompok
    created_at    TIMESTAMP DEFAULT NOW(),
    updated_at    TIMESTAMP DEFAULT NOW(),
    UNIQUE(curriculum_id, code)
);
```

#### Tabel `curriculum_subjects` (Pemetaan Mata Pelajaran ke Kurikulum)
```sql
CREATE TABLE curriculum_subjects (
    id                          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    curriculum_id               BIGINT UNSIGNED REFERENCES curriculums(id) ON DELETE CASCADE,
    subject_id                  BIGINT UNSIGNED REFERENCES subjects(id) ON DELETE CASCADE,
    curriculum_subject_group_id BIGINT UNSIGNED REFERENCES curriculum_subject_groups(id) ON DELETE SET NULL,
    grade_level                 INT NULL,        -- NULL jika berlaku untuk seluruh jenjang kurikulum
    subject_group               VARCHAR(30) DEFAULT 'WAJIB_UMUM', -- Fallback kode kelompok
    hours_per_week              INT DEFAULT 2,   -- Alokasi JP (Jam Pelajaran) per minggu
    passing_grade               DECIMAL(5,2) DEFAULT 75.00, -- KKM / KKTP
    is_active                   BOOLEAN DEFAULT true,
    created_at                  TIMESTAMP DEFAULT NOW(),
    updated_at                  TIMESTAMP DEFAULT NOW(),
    INDEX(curriculum_id, subject_id)
);
```

#### Tabel `teachers` (Master Guru & Staf Pendidik)
```sql
CREATE TABLE teachers (
    id                    UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    school_id             UUID REFERENCES schools(id) ON DELETE CASCADE,
    user_id               UUID UNIQUE REFERENCES users(id) ON DELETE CASCADE,
    nip                   VARCHAR(30) UNIQUE,           -- 18 digit untuk PNS/PPPK
    nuptk                 VARCHAR(30) UNIQUE,           -- 16 digit Nomor Unik Pendidik & Tenaga Kependidikan
    nik                   VARCHAR(16) NOT NULL,         -- 16 digit NIK KTP
    full_name             VARCHAR(255) NOT NULL,
    title_prefix          VARCHAR(50),                  -- Dr., Drs., Prof.
    title_suffix          VARCHAR(50),                  -- S.Pd., M.Pd., M.Kom.
    gender                ENUM('L', 'P') NOT NULL,
    birth_place           VARCHAR(100) NOT NULL,
    birth_date            DATE NOT NULL,
    mother_maiden_name    VARCHAR(255) NOT NULL,        -- Nama ibu kandung (kunci validasi Dapodik)
    religion              ENUM('Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Khonghucu') NOT NULL,
    address               TEXT,
    rt                    VARCHAR(5),
    rw                    VARCHAR(5),
    sub_district          VARCHAR(100),                 -- Kelurahan / Desa
    district              VARCHAR(100),                 -- Kecamatan
    city                  VARCHAR(100),                 -- Kabupaten / Kota
    province              VARCHAR(100),                 -- Provinsi
    postal_code           VARCHAR(10),
    phone                 VARCHAR(30),
    email                 VARCHAR(100),
    family_card_number    VARCHAR(20),                  -- No Kartu Keluarga
    marital_status        ENUM('Belum Menikah', 'Menikah', 'Cerai Hidup', 'Cerai Mati') DEFAULT 'Menikah',
    spouse_name           VARCHAR(255),                 -- Nama Suami / Istri
    spouse_occupation     VARCHAR(100),                 -- Pekerjaan Pasangan
    employment_type       ENUM('GURU_KELAS', 'GURU_MAPEL', 'GURU_BK', 'TENAGA_ADMINISTRASI', 'LABORAN', 'PUSTAKAWAN', 'KEPALA_SEKOLAH') DEFAULT 'GURU_MAPEL',
    employment_status     ENUM('PNS', 'CPNS', 'PPPK', 'GTY', 'GTT', 'HONOR_SEKOLAH') DEFAULT 'GTT',
    sk_appointment_number VARCHAR(100),                 -- Nomor SK Pengangkatan
    sk_appointment_date   DATE,                         -- Tanggal SK / TMT Pengangkatan
    institution_assignment VARCHAR(150),                -- Lembaga Pengangkat (Pemerintah, Yayasan, Kepsek)
    source_of_salary      VARCHAR(100) DEFAULT 'APBD Kab/Kota', -- Sumber Gaji (APBN, APBD, Yayasan, BOS)
    is_satminkal          BOOLEAN DEFAULT true,         -- Satuan Administrasi Pangkal (Satminkal Induk)
    is_certified          BOOLEAN DEFAULT false,        -- Status Sertifikasi Pendidik
    certification_number  VARCHAR(50),                  -- Nomor Sertifikat Pendidik
    nrg                   VARCHAR(30),                  -- Nomor Registrasi Guru
    certification_subject VARCHAR(100),                 -- Bidang Studi Sertifikasi
    last_education        ENUM('D1', 'D2', 'D3', 'D4', 'S1', 'S2', 'S3') DEFAULT 'S1',
    major_study           VARCHAR(100),                 -- Jurusan / Program Studi
    university_name       VARCHAR(150),                 -- Perguruan Tinggi
    graduation_year       INTEGER,                      -- Tahun Kelulusan
    additional_duties     VARCHAR(150),                 -- Waka Kurikulum, Waka Kesiswaan, Waka Sarpras, Waka Humas, Kepala Lab/Perpus
    teaching_hours        INTEGER DEFAULT 24,           -- Ekuivalensi Beban Mengajar (JJM Tatap Muka)
    npwp                  VARCHAR(30),                  -- Nomor Pokok Wajib Pajak
    bank_name             VARCHAR(50),                  -- Bank Penyalur (BNI, BRI, Mandiri, BPD)
    bank_account_number   VARCHAR(50),                  -- Nomor Rekening
    bank_account_holder   VARCHAR(150),                 -- Nama Pemilik Rekening Sesuai Buku Tabungan
    photo_url             VARCHAR(500),                 -- Pas Foto Formal 4x6 (teachers/photos)
    is_active             BOOLEAN DEFAULT true,
    created_at            TIMESTAMP DEFAULT NOW(),
    updated_at            TIMESTAMP DEFAULT NOW()
);
```

#### Tabel `parents` (Master Orang Tua / Wali — Standar Dapodik)
```sql
CREATE TABLE parents (
    id                    UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    school_id             UUID REFERENCES schools(id) ON DELETE CASCADE,
    user_id               UUID UNIQUE REFERENCES users(id) ON DELETE CASCADE,
    no_kk                 VARCHAR(16),                  -- Nomor Kartu Keluarga
    -- Data Ayah Kandung
    father_nik            VARCHAR(16),
    father_name           VARCHAR(255),
    father_birth_year     INTEGER,
    father_education      ENUM('Tidak Sekolah', 'SD', 'SMP', 'SMA', 'D1', 'D2', 'D3', 'D4/S1', 'S2', 'S3'),
    father_occupation     VARCHAR(100),                 -- PNS, Karyawan Swasta, Wiraswasta, Petani, dll.
    father_income_range   ENUM('< 500.000', '500.000 - 999.999', '1.000.000 - 1.999.999', '2.000.000 - 4.999.999', '5.000.000 - 20.000.000', '> 20.000.000', 'Tidak Berpenghasilan'),
    father_phone          VARCHAR(30),
    -- Data Ibu Kandung
    mother_nik            VARCHAR(16),
    mother_name           VARCHAR(255),
    mother_birth_year     INTEGER,
    mother_education      ENUM('Tidak Sekolah', 'SD', 'SMP', 'SMA', 'D1', 'D2', 'D3', 'D4/S1', 'S2', 'S3'),
    mother_occupation     VARCHAR(100),
    mother_income_range   ENUM('< 500.000', '500.000 - 999.999', '1.000.000 - 1.999.999', '2.000.000 - 4.999.999', '5.000.000 - 20.000.000', '> 20.000.000', 'Tidak Berpenghasilan'),
    mother_phone          VARCHAR(30),
    -- Data Wali (Opsional)
    guardian_nik          VARCHAR(16),
    guardian_name         VARCHAR(255),
    guardian_birth_year   INTEGER,
    guardian_education    ENUM('Tidak Sekolah', 'SD', 'SMP', 'SMA', 'D1', 'D2', 'D3', 'D4/S1', 'S2', 'S3'),
    guardian_occupation   VARCHAR(100),
    guardian_income_range ENUM('< 500.000', '500.000 - 999.999', '1.000.000 - 1.999.999', '2.000.000 - 4.999.999', '5.000.000 - 20.000.000', '> 20.000.000', 'Tidak Berpenghasilan'),
    guardian_phone        VARCHAR(30),
    -- Alamat Domisili
    primary_phone         VARCHAR(30) NOT NULL,
    address               TEXT,
    rt                    VARCHAR(5),
    rw                    VARCHAR(5),
    sub_district          VARCHAR(100),
    district              VARCHAR(100),
    city                  VARCHAR(100),
    province              VARCHAR(100),
    postal_code           VARCHAR(10),
    created_at            TIMESTAMP DEFAULT NOW(),
    updated_at            TIMESTAMP DEFAULT NOW()
);
```

#### Tabel `parent_student` (Relasi Kartu Keluarga: 1 Orang Tua Multi-Anak)
```sql
CREATE TABLE parent_student (
    id            UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    parent_id     UUID REFERENCES parents(id) ON DELETE CASCADE,
    student_id    UUID REFERENCES students(id) ON DELETE CASCADE,
    relationship  ENUM('father', 'mother', 'guardian') DEFAULT 'father',
    created_at    TIMESTAMP DEFAULT NOW(),
    UNIQUE(parent_id, student_id)
);
```

#### Tabel `subjects` (Master Mata Pelajaran)
```sql
CREATE TABLE subjects (
    id            UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    school_id     UUID REFERENCES schools(id) ON DELETE CASCADE,
    code          VARCHAR(20) NOT NULL,         -- MTK, BIND, BING
    name          VARCHAR(150) NOT NULL,        -- Matematika, Bahasa Indonesia
    color         VARCHAR(25) DEFAULT '#4680ff',-- Warna identitas sel pada tabel jadwal pelajaran
    category      ENUM('A_MUATAN_NASIONAL', 'B_MUATAN_KEWILAYAHAN', 'C_KEJURUAN_PEMINATAN', 'MULOK') DEFAULT 'A_MUATAN_NASIONAL',
    passing_grade DECIMAL(5,2) DEFAULT 75.00,   -- KKM
    is_active     BOOLEAN DEFAULT true,
    created_at    TIMESTAMP DEFAULT NOW()
);
```

#### Tabel `school_rooms` (Master Ruang Kelas, Laboratorium & Bengkel)
```sql
CREATE TABLE school_rooms (
    id            BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    school_id     BIGINT UNSIGNED REFERENCES schools(id) ON DELETE CASCADE,
    code          VARCHAR(30) NOT NULL,         -- R-X1, LAB-KOM1, BKL-TKR
    name          VARCHAR(120) NOT NULL,        -- Ruang Kelas X-1, Lab Komputer 1
    type          VARCHAR(30) DEFAULT 'CLASSROOM', -- CLASSROOM, LABORATORY, WORKSHOP, SPORTS, HALL
    building      VARCHAR(80),
    floor         VARCHAR(30),
    capacity      INT DEFAULT 36,
    is_active     BOOLEAN DEFAULT true,
    UNIQUE(school_id, code)
);
```

#### Tabel `class_schedules` (Jadwal Pelajaran Kelas)
```sql
CREATE TABLE class_schedules (
    id                UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    school_id         UUID REFERENCES schools(id) ON DELETE CASCADE,
    category_id       UUID REFERENCES time_schedule_categories(id) ON DELETE SET NULL,
    class_id          UUID REFERENCES classes(id) ON DELETE CASCADE,
    subject_id        UUID REFERENCES subjects(id) ON DELETE CASCADE,
    teacher_id        UUID REFERENCES teachers(id) ON DELETE CASCADE,
    lesson_period_id  UUID REFERENCES lesson_periods(id) ON DELETE CASCADE,
    room_id           BIGINT UNSIGNED REFERENCES school_rooms(id) ON DELETE SET NULL,
    academic_year_id  UUID REFERENCES academic_years(id) ON DELETE CASCADE,
    academic_semester ENUM('odd', 'even') DEFAULT 'odd',
    room_name         VARCHAR(50),              -- Misal: "Lab Komputer 1", "R. Kelas X-1"
    notes             VARCHAR(255),
    created_at        TIMESTAMP DEFAULT NOW(),
    updated_at        TIMESTAMP DEFAULT NOW()
);
```

#### Tabel Modul Kesiswaan — Tata Tertib, Ambang Batas & Pemutihan Poin
```sql
-- 1. Master Kategori Pelanggaran Siswa
CREATE TABLE discipline_categories (
    id          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    school_id   BIGINT UNSIGNED NOT NULL,
    code        VARCHAR(30) NOT NULL,           -- KAT-A, KAT-B, KAT-C, KAT-D
    name        VARCHAR(150) NOT NULL,          -- Kerajinan & Kehadiran, Sikap & Etika, Pelanggaran Berat
    severity    VARCHAR(20) DEFAULT 'RINGAN',   -- RINGAN, SEDANG, BERAT
    color       VARCHAR(25) DEFAULT '#f59e0b',
    description TEXT,
    sort_order  SMALLINT UNSIGNED DEFAULT 1,
    is_active   BOOLEAN DEFAULT true,
    UNIQUE(school_id, code)
);

-- 2. Master Item Pasal Pelanggaran Siswa beserta Besar Poin
CREATE TABLE discipline_items (
    id               BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    school_id        BIGINT UNSIGNED NOT NULL,
    category_id      BIGINT UNSIGNED NOT NULL REFERENCES discipline_categories(id) ON DELETE CASCADE,
    code             VARCHAR(30) NOT NULL,      -- A-01, B-02, C-03, D-01
    name             VARCHAR(255) NOT NULL,     -- Uraian pelanggaran siswa
    points           INT UNSIGNED DEFAULT 5,    -- Besar poin pelanggaran (+5 s/d +100)
    default_sanction VARCHAR(255),
    is_active        BOOLEAN DEFAULT true,
    UNIQUE(school_id, code)
);

-- 3. Seting Ambang Batas Pelanggaran & Eskalasi Sanksi (Waka Kesiswaan)
CREATE TABLE discipline_thresholds (
    id                    BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    school_id             BIGINT UNSIGNED NOT NULL,
    level_code            VARCHAR(30) NOT NULL, -- PEMBINAAN, SP1, SP2, SP3
    name                  VARCHAR(150) NOT NULL,
    min_points            INT UNSIGNED NOT NULL,
    max_points            INT UNSIGNED NULL,
    sanction_action       TEXT,
    require_parent_call   BOOLEAN DEFAULT false,
    require_bk_counseling BOOLEAN DEFAULT false,
    color                 VARCHAR(25) DEFAULT '#f59e0b',
    sort_order            SMALLINT UNSIGNED DEFAULT 1,
    is_active             BOOLEAN DEFAULT true,
    UNIQUE(school_id, level_code)
);

-- 4. Seting Program & Kriteria Pemutihan / Pengurangan Poin (Waka Kesiswaan)
CREATE TABLE discipline_amnesty_rules (
    id                            BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    school_id                     BIGINT UNSIGNED NOT NULL,
    code                          VARCHAR(30) NOT NULL, -- PMT-01, PMT-02
    name                          VARCHAR(200) NOT NULL,
    credit_points                 INT UNSIGNED DEFAULT 10,
    min_violation_points_required INT UNSIGNED DEFAULT 0,
    clean_days_required           SMALLINT UNSIGNED DEFAULT 0,
    max_claims_per_semester       SMALLINT UNSIGNED NULL,
    description                   TEXT,
    is_active                     BOOLEAN DEFAULT true,
    UNIQUE(school_id, code)
);

-- 5. Kebijakan Umum Ambang Batas & Pemutihan Kesiswaan (Waka Kesiswaan)
CREATE TABLE discipline_policies (
    id                                 BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    school_id                          BIGINT UNSIGNED UNIQUE NOT NULL,
    max_violation_points               INT UNSIGNED DEFAULT 100,
    reset_period                       VARCHAR(30) DEFAULT 'ACADEMIC_YEAR', -- SEMESTER, ACADEMIC_YEAR, NEVER
    amnesty_enabled                    BOOLEAN DEFAULT true,
    max_amnesty_points_per_semester    INT UNSIGNED DEFAULT 50,
    min_clean_days_before_amnesty      SMALLINT UNSIGNED DEFAULT 7,
    auto_refer_bk_threshold            INT UNSIGNED DEFAULT 50,
    allow_amnesty_for_heavy_violations BOOLEAN DEFAULT false,
    updated_by                         BIGINT UNSIGNED NULL
);

-- 6. Buku Saldo & Riwayat Pelanggaran / Pemutihan Poin Siswa
CREATE TABLE student_discipline_records (
    id                 BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    school_id          BIGINT UNSIGNED NOT NULL,
    student_id         BIGINT UNSIGNED NOT NULL,
    academic_year_id   BIGINT UNSIGNED NULL,
    academic_semester  VARCHAR(10) DEFAULT 'odd',
    record_type        VARCHAR(20) DEFAULT 'VIOLATION', -- VIOLATION, AMNESTY
    discipline_item_id BIGINT UNSIGNED NULL,
    amnesty_rule_id    BIGINT UNSIGNED NULL,
    record_date        DATE NOT NULL,
    points             INT UNSIGNED NOT NULL,
    title              VARCHAR(255) NOT NULL,
    notes              TEXT NULL,
    location           VARCHAR(150) NULL,
    recorded_by        BIGINT UNSIGNED NULL,
    approved_by        BIGINT UNSIGNED NULL,
    status             VARCHAR(20) DEFAULT 'APPROVED'
);
```

#### Tabel `students` (Peserta Didik — Standar Dapodik)
```sql
CREATE TABLE students (
    id                    UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    school_id             UUID REFERENCES schools(id) ON DELETE CASCADE,
    user_id               UUID UNIQUE REFERENCES users(id) ON DELETE SET NULL,
    class_id              UUID REFERENCES classes(id) ON DELETE SET NULL,
    -- Angkatan & Riwayat Pendaftaran
    entry_academic_year_id UUID REFERENCES academic_years(id) ON DELETE SET NULL, -- Tahun Masuk / Angkatan
    entry_grade_level      INTEGER,                      -- Tingkat saat pertama masuk (mis: 1, 7, 10)
    registration_type      ENUM('new', 'transfer') DEFAULT 'new', -- Siswa Baru / Pindahan
    previous_school        VARCHAR(150),                 -- Nama sekolah asal sebelumnya
    -- Identitas Pokok Dapodik
    nis                   VARCHAR(30),                  -- Nomor Induk Siswa lokal
    nisn                  VARCHAR(20) UNIQUE,           -- 10 digit NISN
    nik                   VARCHAR(16) UNIQUE,           -- 16 digit NIK
    no_kk                 VARCHAR(16),                  -- Nomor Kartu Keluarga
    full_name             VARCHAR(255) NOT NULL,        -- Nama sesuai Akta Lahir
    gender                ENUM('L', 'P') NOT NULL,
    birth_place           VARCHAR(100) NOT NULL,
    birth_date            DATE NOT NULL,
    birth_certificate_no  VARCHAR(100),                 -- No Registrasi Akta Kelahiran
    religion              ENUM('Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Khonghucu') NOT NULL,
    special_needs         VARCHAR(100) DEFAULT 'Tidak Ada', -- Kebutuhan khusus (ABK)
    -- Alamat & Domisili Dapodik
    address               TEXT NOT NULL,
    rt                    VARCHAR(5),
    rw                    VARCHAR(5),
    sub_district          VARCHAR(100),                 -- Kelurahan / Desa
    district              VARCHAR(100),                 -- Kecamatan
    city                  VARCHAR(100),                 -- Kabupaten / Kota
    province              VARCHAR(100),                 -- Provinsi
    postal_code           VARCHAR(10),
    home_latitude         DECIMAL(10,8),                -- Koordinat lintang rumah siswa
    home_longitude        DECIMAL(11,8),                -- Koordinat bujur rumah siswa
    residence_type        ENUM('Bersama Orang Tua', 'Wali', 'Kos', 'Asrama', 'Panti Asuhan') DEFAULT 'Bersama Orang Tua',
    transportation_mode   ENUM('Jalan Kaki', 'Kendaraan Pribadi', 'Angkutan Umum', 'Jemputan Sekolah', 'Kereta/KRL', 'Ojek/Online') DEFAULT 'Kendaraan Pribadi',
    -- Data Keluarga & Kesejahteraan
    child_order           INTEGER DEFAULT 1,            -- Anak keberapa
    siblings_count        INTEGER DEFAULT 0,            -- Jumlah saudara kandung
    student_phone         VARCHAR(30),                  -- Nomor HP siswa
    parent_phone          VARCHAR(30),                  -- Nomor HP kontak ortu terpadu
    parent_name           VARCHAR(255),                 -- Nama gabungan orang tua
    kps_receiver          BOOLEAN DEFAULT false,        -- Penerima KPS / PKH
    kps_number            VARCHAR(50),
    kip_receiver          BOOLEAN DEFAULT false,        -- Penerima KIP / PIP
    kip_number            VARCHAR(50),
    kip_name              VARCHAR(255),                 -- Nama tertera di KIP
    -- Data Fisik & Periodik
    height_cm             INTEGER,                      -- Tinggi badan
    weight_kg             INTEGER,                      -- Berat badan
    head_circumference_cm INTEGER,                      -- Lingkar kepala
    distance_to_school_km DECIMAL(6,2),                 -- Jarak ke sekolah
    travel_time_minutes   INTEGER,                      -- Waktu tempuh
    blood_type            ENUM('A', 'B', 'AB', 'O', 'Tidak Tahu') DEFAULT 'Tidak Tahu',
    -- Status & Akademik
    photo_url             VARCHAR(500),                 -- Pas Foto Resmi Siswa 4x6
    status                ENUM('active', 'graduated', 'transferred', 'dropout', 'deceased') DEFAULT 'active',
    wallet_balance        DECIMAL(15,2) DEFAULT 0.00,
    enrollment_date       DATE,
    created_at            TIMESTAMP DEFAULT NOW(),
    updated_at            TIMESTAMP DEFAULT NOW()
);
```

#### Tabel `student_class_histories` (Riwayat Kenaikan & Penempatan Kelas per Tahun Ajaran)
```sql
CREATE TABLE student_class_histories (
    id               UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    school_id        UUID REFERENCES schools(id) ON DELETE CASCADE,
    student_id       UUID REFERENCES students(id) ON DELETE CASCADE,
    academic_year_id UUID REFERENCES academic_years(id) ON DELETE CASCADE,
    class_id         UUID REFERENCES classes(id) ON DELETE CASCADE,
    grade_level      INTEGER NOT NULL,             -- Tingkat rombel (mis: 10, 11, 12)
    status           ENUM('promoted', 'graduated', 'retained', 'transferred', 'dropped_out') DEFAULT 'promoted',
    notes            TEXT,
    promoted_at      TIMESTAMP DEFAULT NOW(),
    created_at       TIMESTAMP DEFAULT NOW(),
    updated_at       TIMESTAMP DEFAULT NOW(),
    UNIQUE(student_id, academic_year_id)
);
```

#### Tabel `fees` (Tagihan)
```sql
CREATE TABLE fees (
    id              UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    school_id       UUID REFERENCES schools(id),
    student_id      UUID REFERENCES students(id),
    fee_type_id     UUID REFERENCES fee_types(id),
    amount          DECIMAL(15,2) NOT NULL,
    due_date        DATE,
    period_month    INTEGER,              -- 1-12
    period_year     INTEGER,
    status          ENUM('unpaid','partial','paid','overdue','waived'),
    virtual_account VARCHAR(50),
    paid_at         TIMESTAMP,
    paid_amount     DECIMAL(15,2) DEFAULT 0,
    payment_method  VARCHAR(50),         -- VA, QRIS, CASH, TRANSFER
    created_at      TIMESTAMP DEFAULT NOW()
);
```

#### Tabel `wallet_transactions` (Saku Digital)
```sql
CREATE TABLE wallet_transactions (
    id              UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    school_id       UUID REFERENCES schools(id),
    student_id      UUID REFERENCES students(id),
    type            ENUM('topup','purchase','refund','transfer'),
    amount          DECIMAL(15,2) NOT NULL,
    balance_before  DECIMAL(15,2),
    balance_after   DECIMAL(15,2),
    description     VARCHAR(255),
    reference_id    VARCHAR(100),        -- ID transaksi POS
    pos_outlet_id   UUID REFERENCES pos_outlets(id),
    created_at      TIMESTAMP DEFAULT NOW()
);
```

#### Tabel `attendances` (Presensi)
```sql
CREATE TABLE attendances (
    id                UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    school_id         UUID REFERENCES schools(id),
    student_id        UUID REFERENCES students(id),
    schedule_id       UUID REFERENCES schedules(id),    -- Sesi/jadwal pelajaran
    date              DATE NOT NULL,
    check_in_at       TIMESTAMP,
    check_out_at      TIMESTAMP,
    status            VARCHAR(20) NOT NULL,              -- present, absent, late, excused, sick
    method            VARCHAR(20) NOT NULL,              -- qr_code, rfid, selfie, manual
    -- Kolom khusus presensi selfie
    selfie_url        VARCHAR(500),                      -- URL foto selfie (terenkripsi)
    latitude          DECIMAL(10, 8),                   -- Koordinat GPS latitude
    longitude         DECIMAL(11, 8),                   -- Koordinat GPS longitude
    gps_accuracy      DECIMAL(8, 2),                    -- Akurasi GPS dalam meter
    altitude          DECIMAL(8, 2),                    -- Ketinggian (opsional)
    is_within_radius  BOOLEAN DEFAULT false,             -- Hasil validasi geofencing
    distance_meters   DECIMAL(10, 2),                   -- Jarak dari titik sekolah
    device_id         VARCHAR(255),                     -- Fingerprint device
    ip_address        INET,                             -- IP saat presensi
    is_mock_location  BOOLEAN DEFAULT false,             -- Flag GPS palsu terdeteksi
    liveness_score    DECIMAL(5, 4),                    -- Skor liveness detection (0-1)
    verified_by       UUID REFERENCES users(id),        -- NULL jika otomatis, diisi jika manual
    notes             TEXT,
    created_at        TIMESTAMP DEFAULT NOW()
);
```

#### Tabel `school_geofences` (Konfigurasi Lokasi Sekolah)
```sql
CREATE TABLE school_geofences (
    id                UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    school_id         UUID REFERENCES schools(id),
    name              VARCHAR(100) NOT NULL,             -- Mis: "Gedung Utama", "Lapangan"
    latitude          DECIMAL(10, 8) NOT NULL,
    longitude         DECIMAL(11, 8) NOT NULL,
    radius_meters     INTEGER NOT NULL DEFAULT 100,      -- Radius geofencing
    is_primary        BOOLEAN DEFAULT false,             -- Lokasi utama sekolah
    wifi_ssid         VARCHAR(100),                     -- SSID WiFi sekolah (fallback)
    is_active         BOOLEAN DEFAULT true,
    created_at        TIMESTAMP DEFAULT NOW()
);
```

### 6.3 Indeks & Optimasi

```sql
-- Index untuk query yang sering digunakan
CREATE INDEX idx_students_school_class ON students(school_id, class_id);
CREATE INDEX idx_fees_student_status ON fees(student_id, status);
CREATE INDEX idx_fees_period ON fees(school_id, period_year, period_month);
CREATE INDEX idx_attendance_student_date ON attendances(student_id, date);
CREATE INDEX idx_wallet_student ON wallet_transactions(student_id, created_at DESC);
```

---

## 7. Desain API

### 7.1 Konvensi RESTful API

**Base URL:** `https://api.sekolahku.id/v1`

**Header Wajib:**
```http
Authorization: Bearer {jwt_token}
X-Tenant-ID: {tenant_id}
Content-Type: application/json
```

**Format Response:**
```json
{
  "success": true,
  "data": { ... },
  "meta": {
    "page": 1,
    "per_page": 20,
    "total": 150
  },
  "message": "OK"
}
```

### 7.2 Endpoint Utama

#### Auth
```
POST   /auth/login              → Login multi-identitas: Email atau NISN (Siswa) + kata sandi
POST   /auth/refresh            → Refresh token
POST   /auth/logout             → Logout
POST   /auth/forgot-password    → Request reset password
POST   /auth/reset-password     → Reset password
```

#### Data Master
```
-- Satuan Pendidikan & Jendela Edit Siswa
GET    /schools/:id/profile-edit-window → Cek status jendela rentang tanggal edit biodata siswa
POST   /schools/:id/profile-edit-window → Setel rentang tanggal buka/tutup edit biodata siswa

-- Periode Akademik
GET    /academic-years                 → List tahun ajaran
POST   /academic-years                 → Tambah tahun ajaran baru
PUT    /academic-years/:id             → Update tahun ajaran
POST   /academic-years/:id/activate    → Set tahun ajaran aktif
GET    /academic-years/:id/semesters   → List semester ganjil/genap

-- Master Kurikulum Sekolah & Pemetaan Multi-Jenjang
GET    /curriculums                    → List kurikulum terdaftar (+ struktur mapel & rombel)
POST   /curriculums                    → Tambah kurikulum baru
PUT    /curriculums/:id                → Update kurikulum
DELETE /curriculums/:id                → Hapus kurikulum
POST   /curriculums/assign-grade       → Tetapkan kurikulum per jenjang (Tingkat 10/11/12)
POST   /curriculums/:id/subjects       → Petakan mapel, alokasi JP/minggu, kelompok & KKTP
DELETE /curriculums/:id/subjects/:sid  → Keluarkan mapel dari kurikulum
GET    /curriculums/:id/subject-groups → List kelompok mapel kurikulum dinamis
POST   /curriculums/:id/subject-groups → Tambah kelompok mapel baru
PUT    /curriculums/:id/subject-groups/:gid → Update kelompok mapel (kode, nama, urutan, warna)
DELETE /curriculums/:id/subject-groups/:gid → Hapus kelompok mapel
POST   /curriculums/:id/subject-groups/template → Terapkan template kelompok (Merdeka, K13, Kemenag, Pesantren)

-- Spektrum Keahlian SMK (Bidang, Program & Konsentrasi)
GET    /vocational-spectrum            → List lengkap 3-Tier spektrum (Bidang, Program, Konsentrasi)
POST   /vocational-fields              → Tambah Bidang Keahlian baru
POST   /vocational-programs            → Tambah Program Keahlian (Fase E, 3/4 Tahun)
POST   /vocational-concentrations      → Daftarkan Konsentrasi Keahlian (Fase F, Kode Dapodik, DUDI)
PUT    /vocational-concentrations/:id  → Update profil kompetensi & mitra industri
DELETE /vocational-concentrations/:id  → Hapus konsentrasi keahlian
POST   /schools/switch-level           → Ganti tipe jenjang sekolah (SMA <-> SMK)

-- Master Waktu Belajar & Jam Pelajaran (Multi-Kategori)
GET    /time-categories                → List kategori waktu (Normal, Ramadhan, Ujian, dll.)
POST   /time-categories                → Tambah kategori waktu belajar
PUT    /time-categories/:id            → Update kategori waktu
POST   /time-categories/:id/activate   → Terapkan kategori waktu aktif
GET    /time-categories/:id/periods    → Rincian slot jam pelajaran per hari
POST   /time-categories/:id/periods    → Tambah / generate massal slot jam
PUT    /periods/:id                    → Update detail jam (jam mulai-selesai, istirahat)
DELETE /periods/:id                    → Hapus slot jam pelajaran

-- Master Jurusan & Kelas (Rombel)
GET    /departments                    → List jurusan / peminatan
POST   /departments                    → Tambah jurusan
GET    /classes                        → List kelas / rombel (filter: tingkat, tahun ajaran)
POST   /classes                        → Tambah rombel baru
PUT    /classes/:id                    → Update rombel & assign wali kelas

-- Master Guru & Staf
GET    /teachers                       → List guru & staf
POST   /teachers                       → Tambah guru baru (+ akun user)
GET    /teachers/:id                   → Detail guru & jadwal mengajar
PUT    /teachers/:id                   → Update profil guru

-- Master Orang Tua / Wali (Multi-Anak)
GET    /parents                        → List orang tua / wali
POST   /parents                        → Daftarkan orang tua (+ akun login)
GET    /parents/:id                    → Detail orang tua & daftar anak
POST   /parents/:id/students           → Hubungkan anak siswa (kartu keluarga)
DELETE /parents/:id/students/:student_id → Lepas hubungan anak siswa

-- Master Mata Pelajaran, Ruangan & Jadwal KBM Multi-Matriks
GET    /subjects                       → List mata pelajaran (termasuk atribut `color`)
POST   /subjects                       → Tambah mata pelajaran (+ warna tampilan jadwal)
PUT    /subjects/:id                   → Update mata pelajaran / warna sel jadwal
GET    /rooms                          → List master ruangan kelas, laboratorium & bengkel
POST   /rooms                          → Tambah master ruangan baru
PUT    /rooms/:id                      → Update ruangan
DELETE /rooms/:id                      → Hapus ruangan
GET    /schedules                      → Jadwal KBM (filter: kelas, hari, guru, ruang, kategori)
POST   /schedules                      → Buat jadwal KBM (mendukung blok rentang jam + cek anti-bentrok Kelas/Guru/Ruang)
PUT    /schedules/:id                  → Update jadwal KBM & ubah rentang jam pelajaran
DELETE /schedules/:id                  → Hapus slot atau blok ter-merge jadwal KBM
POST   /schedules/copy-semester        → Salin jadwal pelajaran dari semester sebelumnya ke semester tujuan
POST   /time-categories/:id/clone-periods → Duplikasi pola sesi jam antar kategori jadwal

-- Modul Kesiswaan: Master Pelanggaran, Ambang Batas, Pemutihan & Buku Kredit Poin
GET    /discipline                     → Ambil kebijakan, master kategori + item poin, ambang batas & aturan pemutihan
POST   /discipline/categories          → Tambah master kategori pelanggaran siswa
PUT    /discipline/categories/:id      → Update master kategori pelanggaran
DELETE /discipline/categories/:id      → Hapus master kategori pelanggaran
POST   /discipline/items               → Tambah item pasal pelanggaran beserta besar poin
PUT    /discipline/items/:id           → Update item pelanggaran & besar poin
DELETE /discipline/items/:id           → Hapus item pelanggaran
POST   /discipline/thresholds          → Tambah ambang batas sanksi pelanggaran (Waka Kesiswaan)
PUT    /discipline/thresholds/:id      → Seting rentang poin ambang batas sanksi (SP1/SP2/SP3)
DELETE /discipline/thresholds/:id      → Hapus ambang batas sanksi
PUT    /discipline/policy              → Seting kebijakan umum ambang maksimal & pemutihan poin (Waka Kesiswaan)
POST   /discipline/amnesty-rules       → Tambah program & syarat pemutihan/pengurangan poin
PUT    /discipline/amnesty-rules/:id   → Update program pemutihan poin
DELETE /discipline/amnesty-rules/:id   → Hapus program pemutihan poin
POST   /discipline/violations          → Catat insiden pelanggaran siswa ke Buku Kredit Poin
POST   /discipline/amnesties           → Terapkan pemutihan/pengurangan poin siswa (validasi syarat otomatis)
DELETE /discipline/records/:id         → Hapus riwayat catatan buku poin
```

#### Siswa, Kenaikan Kelas & Biodata Mandiri
```
GET    /students                        → List siswa (filter: kelas, angkatan, status, search)
POST   /students                        → Daftarkan siswa baru (lengkap data angkatan & dapodik)
GET    /students/:id                    → Detail siswa
PUT    /students/:id                    → Update data siswa
DELETE /students/:id                    → Hapus/nonaktifkan siswa
GET    /students/:id/class-histories    → Riwayat rombel penempatan kelas siswa per tahun ajaran
GET    /promotions/candidates           → Daftar kandidat siswa untuk kenaikan kelas massal
POST   /promotions                      → Eksekusi kenaikan kelas massal (promoted / graduated / retained)
GET    /students/:id/fees               → Tagihan siswa
GET    /students/:id/attendance         → Rekap presensi siswa
GET    /students/:id/grades             → Nilai siswa
GET    /students/:id/wallet             → Saldo & riwayat saku digital

-- Biodata Mandiri Siswa (Portal Siswa)
GET    /student/profile                 → [Web] Form profil siswa & pas foto 4x6 (Dicek jendela aktif)
POST   /student/profile                 → [Web] Simpan profil siswa, pas foto 4x6 & koordinat rumah

-- Manajemen Pengguna & Hak Akses (RBAC)
GET    /admin/users                     → Direktori master pengguna terdaftar (filter: kategori, status, sekolah)
GET    /admin/users/create              → Formulir pendaftaran akun pengguna baru (+ tugas tambahan Waka)
POST   /admin/users                     → Daftarkan pengguna baru (validasi role, additional_role, email)
GET    /admin/users/:id/edit            → Formulir ubah data & kredensial akun pengguna
PUT    /admin/users/:id                 → Perbarui identitas, hak akses, password & tugas tambahan
PATCH  /admin/users/:id/toggle-status   → Aktivasi / nonaktifkan akun pengguna instan
DELETE /admin/users/:id                 → Hapus akun pengguna (proteksi self-deletion)

-- Matriks Penugasan Satuan Pendidikan (Enrollment Multi-Tenant)
GET    /admin/users/enrollments         → Matriks daftar akun & status enrollment satuan pendidikan
POST   /admin/users/:id/enroll          → Enrol / pindahkan / lepaskan penugasan sekolah akun pengguna

-- Impor Masal Pengguna & Siswa (Bulk Import Excel / CSV)
GET    /admin/import/template/students  → Unduh template Excel impor siswa (.xlsx) 2 sheet (Formulir + Daftar Rombel)
GET    /admin/import/template/teachers  → Unduh template Excel impor GTK (.xlsx) 2 sheet (Formulir + Referensi Peran)
POST   /admin/import/students           → Eksekusi impor masal data siswa + auto create akun users (Login NISN)
POST   /admin/import/teachers           → Eksekusi impor masal data GTK + auto create akun users & mapping tupoksi

-- Profil & Biodata GTK Dapodik Terpadu (Portal Guru & Tendik)
GET    /gtk/profile                     → [Web] Formulir 5 tab rincian Dapodik GTK & pas foto resmi 4x6
PUT    /gtk/profile/:teacher            → [Web] Perbarui biodata Dapodik GTK & unggah pas foto 4x6
POST   /admin/gtk/profile/window        → Konfigurasi jendela jadwal durasi pemutakhiran mandiri GTK
```

#### Keuangan
```
GET    /fees                    → List semua tagihan
POST   /fees/generate           → Generate tagihan massal
GET    /fees/:id                → Detail tagihan
POST   /fees/:id/pay            → Proses pembayaran manual (kasir)
GET    /fees/report/summary     → Rekap keuangan
POST   /payments/virtual-account → Buat VA otomatis
POST   /payments/callback       → Callback dari payment gateway
```

#### Presensi Siswa & Geofence
```
GET    /attendances                  → Rekap absensi (terisolasi otomatis untuk akun role siswa)
POST   /attendances                  → Input absensi manual (khusus guru/admin)
POST   /attendances/qr-scan          → Scan QR Code absensi
POST   /attendances/selfie           → Presensi selfie masuk + GPS (otomatis mengunci student_id user login)
POST   /attendances/checkout         → Presensi pulang siswa (otomatis mengunci student_id user login)
GET    /attendances/selfie/:id/photo → Lihat foto selfie presensi
GET    /attendances/report           → Laporan absensi per periode
GET    /attendances/map              → Data presensi untuk tampilan peta live

-- Geofencing
GET    /geofences                    → List konfigurasi lokasi sekolah
POST   /geofences                    → Tambah titik lokasi geofencing
PUT    /geofences/:id                → Update radius/koordinat geofencing
DELETE /geofences/:id                → Hapus titik lokasi
```

**Contoh Request — Presensi Selfie:**
```http
POST /v1/attendances/selfie
Content-Type: multipart/form-data
Authorization: Bearer {token}

{
  "schedule_id": "uuid-jadwal",
  "selfie_image": <file: image/jpeg, max 2MB>,
  "latitude": -6.200000,
  "longitude": 106.816666,
  "gps_accuracy": 12.5,
  "altitude": 10.2,
  "device_id": "android-device-fingerprint-hash",
  "is_mock_location": false,
  "timestamp_device": "2026-10-20T07:15:00+07:00"
}
```

**Contoh Response — Presensi Selfie Berhasil:**
```json
{
  "success": true,
  "data": {
    "attendance_id": "uuid-presensi",
    "student_name": "Ahmad Fauzi",
    "status": "present",
    "method": "selfie",
    "check_in_at": "2026-10-20T07:15:03+07:00",
    "is_within_radius": true,
    "distance_meters": 45.2,
    "liveness_score": 0.9872,
    "selfie_thumbnail_url": "https://cdn.sekolahku.id/selfies/thumb/uuid.jpg"
  },
  "message": "Presensi berhasil dicatat"
}
```

**Contoh Response — Gagal (Di Luar Radius):**
```json
{
  "success": false,
  "error": {
    "code": "OUTSIDE_GEOFENCE",
    "message": "Anda berada di luar area sekolah",
    "data": {
      "your_location": { "lat": -6.215, "lng": 106.820 },
      "school_location": { "lat": -6.200, "lng": 106.816 },
      "distance_meters": 1834.5,
      "allowed_radius_meters": 100
    }
  }
}
```

#### POS
```
GET    /pos/products            → List produk kantin/minimarket
POST   /pos/transactions        → Transaksi baru (bayar saku digital)
GET    /pos/transactions        → Riwayat transaksi
GET    /pos/report              → Laporan penjualan
```

### 7.3 WebSocket Events (Real-time)

```javascript
// Koneksi WebSocket untuk notifikasi real-time
ws://api.sekolahku.id/ws

// Events yang disupport:
{
  "event": "payment.confirmed",    // Pembayaran terkonfirmasi
  "event": "attendance.recorded",  // Absensi dicatat
  "event": "announcement.new",     // Pengumuman baru
  "event": "wallet.transaction",   // Transaksi saku digital
  "event": "grade.published"       // Nilai diterbitkan
}
```

---

## 8. Tech Stack & Teknologi

### 8.1 Core Platform: Full-Stack Laravel Monolith

| Teknologi | Versi | Fungsi |
|-----------|-------|--------|
| **Laravel** | 13.x (Terbaru) | Framework Full-Stack (Routing, ORM, Auth, Controllers & Blade) |
| **PHP** | 8.3+ | Bahasa Pemrograman Utama |
| **Laravel Blade Engine** | Built-in | Server-Side Rendering untuk antarmuka web yang kencang & terintegrasi |
| **Clean Scandinavian & Visual Passbook UI** | Custom Pure CSS | Antarmuka bersih, ramah, dan bebas kesan template kaku (Header Kapsul Minimalis, Stat Pastel Nyaman di Mata, Dual View Mode: Tabel Rapi & Kartu Identitas Pas Foto 4x6, Spotlight Command Palette ⌘K) |
| **Vanilla JavaScript (ES6+)** | Modern ES6 | Interaktivitas modal, AJAX Fetch API, drawer sidebar, Switcher Mode Tabel/Kartu, Spotlight ⌘K, Dock Hover, tanpa overhead framework berat |
| **ApexCharts.js** | 3.x+ | Visualisasi analitik interaktif (Grafik tren presensi, donut komposisi siswa) |
| **Leaflet.js / OpenStreetMap** | Latest | Peta interaktif & pemetaan radius Geofencing presensi sekolah |
| **Boxicons & Font Awesome** | Latest | Ikon antarmuka profesional & modern |
| **Google Fonts (Plus Jakarta Sans)** | Webfont | Tipografi modern kelas dunia yang tajam, proporsional, dan sangat mudah dibaca |

### 8.2 Mobile Client (Aplikasi Pendamping Orang Tua & Siswa)

Aplikasi mobile mengonsumsi **REST API bawaan Laravel** (`/api/v1/...`) dari backend yang sama:

| Opsi Teknologi | Versi | Fungsi |
|----------------|-------|--------|
| **Flutter / React Native** | Latest | Pilihan klien mobile Android & iOS untuk Orang Tua & Siswa |
| **PWA (Progressive Web App)** | Standard | Web App yang dapat di-install langsung dari browser ke homescreen smartphone |
| **Kamera & Lokasi GPS** | Web API / Native | Pengambilan foto selfie mandiri dan verifikasi koordinat lokasi sekolah |

### 8.3 Database & Penyimpanan

| Komponen | Teknologi | Keterangan |
|----------|-----------|------------|
| **Database Utama** | MySQL 8.x / MariaDB & PostgreSQL | MySQL di Laragon/cPanel, PostgreSQL untuk skala enterprise |
| **ORM & Relasi** | Laravel Eloquent ORM | Model relasi kuat: Siswa, GTK, Wali Murid, Rombel, Presensi |
| **Session & Cache** | Redis / Database Cache | Mempercepat query statistik dashboard dan data master |
| **Antrian (Queue)** | Laravel Queue | Pengiriman notifikasi WhatsApp & email tagihan di latar belakang |
| **Media Storage** | Local Storage / MinIO / AWS S3 | Penyimpanan foto selfie presensi, dokumen rapor, dan berkas siswa |

### 8.4 Integrasi Pihak Ketiga

| Layanan | Fungsi |
|---------|--------|
| **Midtrans** | Payment gateway (VA, QRIS, dll) |
| **Xendit** | Alternatif payment gateway |
| **Fonnte / WA Cloud API** | Notifikasi WhatsApp |
| **Firebase FCM** | Push notification mobile |
| **Google Maps / Leaflet.js** | Peta lokasi presensi & geofencing |
| **face-api.js / MediaPipe** | Deteksi wajah & liveness check (client-side) |
| **AWS Rekognition / FaceAPI** | Verifikasi wajah server-side (opsional) |
| **Cloudflare** | CDN, WAF, DDoS protection |
| **MinIO / AWS S3** | Object storage (foto, dokumen) |
| **Sentry** | Error monitoring |
| **Grafana + Prometheus** | System monitoring |

### 8.5 DevOps & Infrastruktur

| Teknologi | Fungsi |
|-----------|--------|
| **Docker** | Containerization |
| **Docker Compose** | Orkestrasi local dev |
| **Kubernetes** | Orkestrasi production |
| **GitHub Actions** | CI/CD pipeline |
| **Nginx** | Reverse proxy & load balancer |
| **Let's Encrypt** | SSL certificate gratis |

---

## 9. Keamanan Sistem

### 9.1 Lapisan Keamanan

```
┌─────────────────────────────────────────────────────────────┐
│  LAYER 1: Network Security                                  │
│  • Cloudflare WAF & DDoS Protection                        │
│  • SSL/TLS Encryption (HTTPS only)                         │
│  • IP Whitelist untuk admin panel                          │
└─────────────────────────────────────────────────────────────┘
┌─────────────────────────────────────────────────────────────┐
│  LAYER 2: Application Security                              │
│  • JWT dengan expiry 15 menit + Refresh Token              │
│  • OAuth2 Social Login (Google)                            │
│  • Rate Limiting (100 req/min per IP)                      │
│  • Input validation & sanitization                         │
│  • SQL Injection prevention (ORM + prepared statements)    │
│  • XSS Protection (CSP Headers)                            │
│  • CSRF Protection                                         │
└─────────────────────────────────────────────────────────────┘
┌─────────────────────────────────────────────────────────────┐
│  LAYER 3: Data Security                                     │
│  • Enkripsi data sensitif at-rest (AES-256)               │
│  • Enkripsi komunikasi data (TLS 1.3)                      │
│  • Multi-tenant data isolation (Row Level Security)        │
│  • RBAC (Role-Based Access Control) granular              │
└─────────────────────────────────────────────────────────────┘
┌─────────────────────────────────────────────────────────────┐
│  LAYER 4: Audit & Compliance                               │
│  • Audit log semua aksi sensitif                           │
│  • Log akses dan perubahan data                            │
│  • Backup otomatis harian + enkripsi                       │
│  • Compliance PDPA (Perlindungan Data Pribadi)             │
└─────────────────────────────────────────────────────────────┘
```

### 9.2 Kebijakan Password

- Minimum 8 karakter
- Kombinasi huruf besar, huruf kecil, angka, simbol
- Tidak boleh menggunakan kata umum (dictionary attack prevention)
- Rotasi password wajib setiap 90 hari untuk admin

### 9.3 Two-Factor Authentication (2FA)

- 2FA wajib untuk role: Super Admin, Admin Sekolah, Kasir
- 2FA opsional untuk: Guru, Orang Tua
- 2FA via WhatsApp/SMS / Google Authenticator

### 9.4 Isolasi Multi-Tenant GTK & Batasan Satuan Pendidikan

Untuk mencegah kebocoran data antar-sekolah pada platform SaaS Sekolahku, diterapkan aturan isolasi tenant yang ketat:

1. **Aturan Hak Akses Sekolah**:
   - `super_admin`: **Satu-satunya** peran yang memiliki otoritas lintas sekolah (*cross-tenant*) dan dapat berganti unit pendidikan melalui menu *School Switcher* atau rute `/admin/switch-school/{id}`.
   - **GTK (Guru, Waka Kurikulum, Waka Kesiswaan, Waka Sarpras, Waka Humas, Staf TU/Admin Sekolah, Kasir), Siswa, dan Orang Tua**:
     - Terkunci mutlak (*strictly locked*) pada `school_id` resmi pengguna (`auth()->user()->school_id`).
     - Session `active_school_id` diselaraskan secara otomatis oleh middleware `EnsureTenantAccess`.
     - Injeksi parameter URL/Request seperti `?school_id=X` akan ditolak (pada endpoint API mengembalikan `403 Forbidden`) atau dipaksa kembali (*merged*) ke unit resmi pengguna pada Web View.
2. **Proteksi UI & Navbar Topbar**:
   - Menu dropdown *"Ganti Unit Pendidikan"* hanya dirender untuk `super_admin`.
   - Untuk GTK dan pengguna sekolah lainnya, navbar hanya menampilkan badge pill statis bertuliskan nama sekolah resmi dan label *"Unit Anda (Terkunci)"*.
3. **Penyelarasan Tugas Tambahan GTK**:
   - Tugas tambahan (seperti Waka, Wali Kelas, Guru BK, dll.) berjalan di dalam koridor sekolah tempat GTK bertugas tanpa harus terikat mutlak pada pergantian tahun ajaran, menjaga fleksibilitas SK penugasan di lapangan.

---

## 10. Infrastruktur & Deployment

### 10.1 Topologi Server

```
INTERNET
    │
    ▼
[Cloudflare CDN/WAF]
    │
    ▼
[Load Balancer - Nginx]
    │
    ├── [App Server 1 - Laravel]
    ├── [App Server 2 - Laravel]
    └── [App Server 3 - Laravel (Standby)]
          │
          ├── [PostgreSQL Primary]
          │        │
          │        └── [PostgreSQL Replica (Read)]
          │
          ├── [Redis Cluster]
          │
          ├── [Queue Workers]
          │
          └── [MinIO (File Storage)]
```

### 10.2 Spesifikasi Server

#### Development/Staging
| Komponen | Spesifikasi |
|----------|-------------|
| App Server | 2 vCPU, 4 GB RAM |
| Database | 2 vCPU, 4 GB RAM, 50 GB SSD |
| Redis | 1 vCPU, 2 GB RAM |

#### Production (s/d 10 Sekolah / 5.000 Siswa)
| Komponen | Spesifikasi |
|----------|-------------|
| App Server (x3) | 4 vCPU, 8 GB RAM masing-masing |
| DB Primary | 8 vCPU, 16 GB RAM, 500 GB NVMe |
| DB Replica | 4 vCPU, 8 GB RAM, 500 GB NVMe |
| Redis | 2 vCPU, 4 GB RAM |
| File Storage | 1 TB (scalable) |

### 10.3 CI/CD Pipeline

```yaml
# .github/workflows/deploy.yml (gambaran)
Pipeline:
  1. Push ke branch feature/*
     → Linting + Unit Test + Static Analysis
  
  2. Merge ke branch develop
     → Build Docker image
     → Deploy ke Staging
     → Integration Test
     → E2E Test (Playwright)
  
  3. Merge ke branch main (via PR)
     → Build Docker image production
     → Deploy ke Production (rolling update)
     → Health check
     → Notifikasi ke Slack/Discord
```

### 10.4 Backup & Disaster Recovery

| Item | Kebijakan |
|------|-----------|
| Database Backup | Setiap hari jam 02.00, retain 30 hari |
| File Backup | Incremental harian, retain 14 hari |
| RTO (Recovery Time Objective) | < 4 jam |
| RPO (Recovery Point Objective) | < 24 jam |
| Failover | Otomatis ke replica jika primary down |

---

## 11. Roadmap Pengembangan

### Phase 1 — MVP (Bulan 1-3)
- [x] **Core Infrastructure**: Setup cloud, CI/CD, database
- [x] **Auth & Multi-tenant**: Login, registrasi sekolah, manajemen tenant
- [x] **Manajemen Pengguna**: CRUD siswa, guru, orang tua, kelas
- [x] **Keuangan Dasar**: Tagihan SPP, Virtual Account, konfirmasi pembayaran
- [x] **Notifikasi**: Push notification & WhatsApp untuk tagihan
- [x] **Mobile App**: Versi orang tua (lihat data anak, tagihan, bayar)

### Phase 2 — Growth (Bulan 4-6)
- [x] **Presensi QR Code**: Absensi siswa via QR code scanner & generator
- [x] **Presensi Selfie + GPS**: Foto wajah + verifikasi lokasi geofencing multi-kampus, liveness & anti-mock location
- [x] **Portal Presensi Siswa Mandiri**: Kartu identitas siswa digital, status realtime hari ini, smart action buttons masuk/pulang, anti-spoofing / proteksi impersonasi, tab riwayat kehadiran 30 hari & KPI statistik
- [x] **Identitas Angkatan (Cohort) & Kenaikan Kelas Massal**: Penelusuran filter per angkatan, modal bulk class promotion, riwayat rombel penempatan kelas per tahun ajaran (`student_class_histories`)
- [x] **Biodata Siswa Mandiri Dapodik**: Portal `/student/profile`, auto-detect titik GPS rumah dari perangkat siswa, pas foto resmi 4x6, kontrol rentang tanggal jendela edit oleh admin (`profile edit window`)
- [x] **Jadwal Pelajaran KBM Dinamis Multi-Matriks & Kelas Gabungan (`/admin/schedules`)**: Penjadwalan per Kelas, **Tabel Khusus Kelas Gabungan & Kelompok Mapel Lintas Rombel** (`teaching_groups` & `class_schedule_classes`), **Filter Otomatis Biodata Agama Siswa** untuk mapel Pendidikan Agama (`students.religion`), pemilihan siswa manual lintas rombel untuk Mapel Pilihan Kurikulum Merdeka, tampilan per Guru & Ruang Kelas/Lab (`school_rooms`), kategori jadwal bermasa berlaku, salin jadwal dari semester lalu, auto-merge `rowspan` jam berurutan, dan pewarnaan sel tabel sesuai warna mata pelajaran
- [x] **Master Kelompok Mata Pelajaran Kurikulum Dinamis (`curriculum_subject_groups`)**: Fleksibilitas penuh Create, Edit, dan Delete kelompok mapel yang melekat spesifik pada setiap kurikulum (Kurikulum Merdeka, K13, Kemenag, Pesantren), pengaturan urutan tampil lembar rapor (`order_no`), pewarnaan badge visual, dan 1-click template presets standar nasional
- [x] **Modul Kesiswaan (Menu 1) — Tata Tertib & Kredit Poin (`/admin/kesiswaan/tatib`)**: Master kategori pelanggaran & item pasal beserta besar poin, seting ambang batas sanksi otomatis (`SP1`/`SP2`/`SP3`) dan kebijakan/program pemutihan poin oleh Waka Kesiswaan, serta Buku Saldo Kredit Poin Siswa
- [x] **Modul Portal & Dashboard Siswa Terpadu (`/student/dashboard`)**: Redirect otomatis saat siswa login ke beranda personal berisi Hero Identitas 4x6 & Status Presensi Hari Ini, 5 KPI utama (Kehadiran 30 Hari, Agenda KBM Live, Skor Kredit Disiplin, Kelengkapan Biodata Dapodik, Saldo Saku Digital), **Jadwal Pelajaran Personal (`/student/schedules`)** yang otomatis menggabungkan kelas reguler + kelas gabungan sesuai biodata agama, **Rekap Kehadiran (`/student/attendance`)**, **Tata Tertib & Buku Saku Poin (`/student/discipline`)**, serta **Kartu Pelajar Digital QR 4x6 (`/student/card`)**
- [ ] **Modul Kesiswaan Lanjutan (Menu 2–6)**: Prestasi & Talenta Siswa, Ekstrakurikuler & OSIS, Bimbingan Konseling (BK), Piket & Dispensasi, serta Beasiswa (PIP/KIP) & Mutasi Siswa
- [ ] **Akademik Penilaian**: Input nilai & e-rapor digital
- [ ] **Saku Digital**: E-wallet siswa
- [ ] **POS Kantin**: Point of sale terintegrasi saku digital
- [ ] **Publikasi**: Pengumuman, berita sekolah
- [x] **Dashboard Analitik**: Grafik dan laporan interaktif berstandar tema Vuexy

### Phase 3 — Expansion (Bulan 7-9)
- [ ] **Modul Tahfidz**: Monitoring hafalan (khusus ponpes)
- [ ] **Modul Laundry**: Manajemen laundry asrama
- [ ] **Manajemen Asrama**: Kamar, penempatan, perizinan
- [ ] **POS Minimarket**: Koperasi/minimarket sekolah
- [ ] **Chat Guru-Ortu**: Komunikasi langsung dalam platform

### Phase 4 — Enterprise (Bulan 10-12)
- [ ] **PSB Online**: Penerimaan siswa baru digital
- [ ] **E-Learning**: LMS ringan (materi, tugas)
- [ ] **API Publik**: Integrasi dengan Dapodik, EMIS
- [ ] **Whitelabel**: Custom branding per sekolah
- [ ] **Multi-Bahasa**: Indonesia + English
- [ ] **Offline Mode**: PWA dengan sync data

---

## 12. Estimasi Biaya & Paket Layanan

### 12.1 Paket Layanan SaaS

| Paket | Harga | Target |
|-------|-------|--------|
| **Starter** | Rp 10.000/siswa/bulan | Sekolah kecil (<200 siswa) |
| **Professional** | Rp 15.000/siswa/bulan | Sekolah menengah + ponpes |
| **Whitelabel** | Custom (hubungi sales) | Yayasan besar, jaringan sekolah |

### 12.2 Fitur per Paket

| Fitur | Starter | Professional | Whitelabel |
|-------|---------|-------------|-----------|
| Web & Mobile App | ✅ | ✅ | ✅ |
| Data Siswa/Guru/Ortu | ✅ | ✅ | ✅ |
| Tagihan & SPP | ✅ | ✅ | ✅ |
| Virtual Account Bank | ✅ | ✅ | ✅ |
| Presensi QR Code | ✅ | ✅ | ✅ |
| Presensi Selfie + GPS | ✅ | ✅ | ✅ |
| Publikasi & Pengumuman | ✅ | ✅ | ✅ |
| Monitoring Tahfidz | ✅ | ✅ | ✅ |
| POS Kantin | ❌ | ✅ | ✅ |
| POS Minimarket | ❌ | ✅ | ✅ |
| Saku Digital (E-Wallet) | ❌ | ✅ | ✅ |
| Monitoring Belanja | ❌ | ✅ | ✅ |
| Modul Laundry | ❌ | ✅ | ✅ |
| Domain Atas Nama Instansi | ❌ | ❌ | ✅ |
| Branding Custom (Logo/Nama App) | ❌ | ❌ | ✅ |
| Akun Playstore Sendiri | ❌ | ❌ | ✅ |
| Dedicated Support | ❌ | ❌ | ✅ |

### 12.3 Estimasi Biaya Pengembangan Awal

| Komponen | Estimasi Biaya |
|----------|---------------|
| Backend Developer (3 bulan) | Rp 15.000.000 - 25.000.000 |
| Frontend Developer (3 bulan) | Rp 12.000.000 - 20.000.000 |
| Mobile Developer (3 bulan) | Rp 12.000.000 - 20.000.000 |
| UI/UX Designer (2 bulan) | Rp 8.000.000 - 15.000.000 |
| QA Engineer (3 bulan) | Rp 8.000.000 - 12.000.000 |
| **Server & Infrastruktur/bulan** | Rp 2.000.000 - 5.000.000 |
| **Lisensi & Integrasi** | Rp 3.000.000 - 8.000.000 |
| **Total Estimasi MVP** | **Rp 60.000.000 - 105.000.000** |

---

## Lampiran A — Struktur Direktori Proyek (Laravel Full-Stack Monolith)

```
sekolahku/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── AdminDashboardController.php    # Handler Dashboard Analitik
│   │   │   ├── AdminDataMasterController.php   # Handler Data Master Terpadu (10 Kategori)
│   │   │   ├── AdminBulkImportController.php   # Handler Impor Masal Siswa & GTK via Excel/CSV
│   │   │   ├── AdminScheduleController.php     # Handler Matriks Jadwal KBM (Kelas, Guru, Ruang, Kategori & Auto-Rowspan)
│   │   │   ├── AdminDisciplineController.php   # Handler Modul Kesiswaan: Tata Tertib, Ambang Batas & Kredit Poin
│   │   │   ├── AdminTeachingJournalController.php # Handler KBM & Jurnal Mengajar Guru (KBM, Agenda, Verifikasi)
│   │   │   ├── AdminUserController.php         # Handler RBAC Pengguna, Multi-Peran & Enrollment Satdik
│   │   │   ├── TeacherProfileController.php    # Handler Profil GTK Dapodik (5 Tab, Foto 4x6, Jendela Jadwal)
│   │   │   ├── AttendancePortalController.php  # Handler Portal Presensi GPS & Selfie
│   │   │   ├── StudentProfileController.php    # Handler Biodata Mandiri Siswa (Dapodik & Pas Foto 4x6)
│   │   │   └── Api/
│   │   │       ├── ClassScheduleController.php     # CRUD Jadwal KBM, Anti-Bentrok 3 Lapis, Edit Jam & Salin Semester
│   │   │       ├── SchoolRoomController.php        # CRUD Master Ruang Kelas, Lab & Bengkel
│   │   │       ├── StudentDisciplineController.php # CRUD Master Kategori/Item Pelanggaran, Ambang Sanksi & Pemutihan
│   │   │       ├── StudentPromotionController.php  # Handler Kenaikan Kelas Massal & Riwayat Rombel
│   │   │       ├── CurriculumController.php        # CRUD Kurikulum, Jenjang, Kelompok Mapel Dinamis & Alokasi Mapel
│   │   │       ├── VocationalSpectrumController.php # CRUD Spektrum Keahlian SMK (Bidang, Program, Konsentrasi)
│   │   │       └── ...                             # Endpoint JSON API untuk Mobile Client
│   │   └── Middleware/
│   │       ├── RoleMiddleware.php              # RBAC Middleware (Role Passthrough & Waka Mapping)
│   │       └── MultiTenantMiddleware.php       # Enforce Tenant Isolation
│   ├── Models/                                 # 30+ Model Eloquent (User, Teacher, Curriculum, CurriculumSubjectGroup, Schedule, SchoolRoom, dll)
│   └── Services/                               # Logika Bisnis (DisciplineBootstrapService, Geofencing, Enrollment)
│
├── database/
│   ├── migrations/                             # Struktur Tabel Database Dapodik, Ruangan, Jadwal, User RBAC & GTK
│   └── seeders/                                # Data Awal SMAN/SMKN 1 JKT (Spektrum SMK, Kurmer, GTK, Siswa, Tatib)
│
├── resources/
│   └── views/                                  # Tampilan UI (Laravel Blade)
│   │       ├── layouts/
│   │       │   └── vuexy.blade.php                 # Master Template Vuexy (Sidebar Terpadu + Grup Kesiswaan & BK)
│   │       ├── admin/
│   │       │   ├── dashboard.blade.php             # Dashboard Analitik (Stat Card, ApexCharts)
│   │       │   ├── data-master.blade.php           # Data Master 10 Kategori (Termasuk Warna Mapel & Kenaikan Kelas)
│   │       │   ├── schedules.blade.php             # Matriks Jadwal KBM (Kelas/Guru/Ruang, Auto-Merge & Warna Sel Mapel)
│   │       │   ├── users/
│   │       │   │   ├── index.blade.php             # Direktori Master Pengguna & RBAC Filter
│   │       │   │   ├── create.blade.php            # Formulir Registrasi Pengguna Baru
│   │       │   │   ├── edit.blade.php              # Formulir Ubah Pengguna & Tugas Tambahan Waka
│   │       │   │   └── enrollments.blade.php       # Matriks Penugasan Satdik Multi-Tenant
│   │       │   └── kesiswaan/
│   │       │       └── tatib.blade.php             # Modul Waka Kesiswaan: Master Pelanggaran, Ambang Sanksi & Pemutihan
│   │       ├── teacher/
│   │       │   └── profile.blade.php               # Portal Profil GTK Dapodik 5 Tab, Foto 4x6 & Pengaturan Jadwal
│   │       ├── student/
│   │       │   └── profile.blade.php               # Portal Biodata Siswa Mandiri, Pas Foto 4x6 & Auto-GPS
│   │       └── attendance/
│   │           └── portal.blade.php                # Portal Kamera Selfie, Verifikasi GPS & Riwayat Siswa
│   │
│   ├── routes/
│   │   ├── web.php                                 # Rute Web (Dashboard, Data Master, Presensi, Profil GTK/Siswa, Users)
│   │   └── api.php                                 # Rute REST API (Mobile Ortu/Siswa & Kenaikan Kelas)
│   │
│   ├── config/                                     # Konfigurasi Database, Auth, Cache
│   ├── storage/                                    # Log & File Upload (Foto Selfie Siswa & Pas Foto 4x6 GTK/Siswa)
│   └── public/                                     # Web Root, Assets CSS/JS/Icons
│
├── rancangan sistem sekolahku.md                   # Dokumen Blueprint & Arsitektur Sistem
└── modul_kehadiran_siswa_sekolah.md                # Spesifikasi Detail Modul Presensi Geofence
```

---

## Lampiran B — Standar Kode & Konvensi

### API Response Codes

| Code | Arti | Situasi |
|------|------|---------|
| `200` | OK | Request berhasil |
| `201` | Created | Resource berhasil dibuat |
| `400` | Bad Request | Validasi input gagal |
| `401` | Unauthorized | Token tidak valid/expired |
| `403` | Forbidden | Tidak punya hak akses |
| `404` | Not Found | Resource tidak ditemukan |
| `422` | Unprocessable Entity | Business logic error |
| `429` | Too Many Requests | Rate limit exceeded |
| `500` | Internal Server Error | Error server |

### Naming Convention

| Item | Konvensi | Contoh |
|------|----------|--------|
| Database table | snake_case plural | `school_students` |
| Database column | snake_case | `full_name`, `created_at` |
| API endpoint | kebab-case | `/api/school-settings` |
| PHP Class | PascalCase | `StudentController` |
| PHP method | camelCase | `getStudentById()` |
| JS/TS variable | camelCase | `studentList` |
| JS/TS component | PascalCase | `StudentCard` |
| CSS class | kebab-case | `student-card__header` |

---

*Dokumen ini bersifat living document dan akan diperbarui seiring perkembangan proyek.*

**Dibuat oleh:** Tim Pengembang Sekolahku  
**Terakhir Diperbarui:** 2 November 2026 (Versi 2.6.0)
